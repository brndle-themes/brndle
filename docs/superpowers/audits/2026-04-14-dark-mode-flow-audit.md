# Brndle Dark Mode Flow — Deep Audit

Date: 2026-04-14
Scope: Enable/disable flow, toggle visibility, theme wiring
Verdict: Multiple real bugs. The floating-button duplication and
click-cancellation in header mode are the most user-visible.

---

## 1. End-to-end Flow

### Settings layer
- `app/Settings/Defaults.php:37-39` — defaults:
  - `dark_mode_default = 'light'`
  - `dark_mode_toggle = true`
  - `dark_mode_toggle_position = 'bottom-right'`
- `app/Settings/Settings.php` — stored in `wp_options['brndle_settings']` as JSON, merged with defaults on read (`Settings::all()`), in-memory cached per request.
- `admin/src/tabs/DarkMode.jsx` — React admin tab that writes those three keys via `onChange`.

### Composer layer
- `app/View/Composers/Theme.php:63-65` — exposes three Blade vars globally:
  - `$darkModeDefault`
  - `$showDarkModeToggle` (cast to bool)
  - `$darkModeTogglePosition`
- Per-request cached in `self::$cachedData[$cacheKey]`.

### CSS layer (two sources — this is important)
Source A — inline in `<head>` via `SettingsServiceProvider::outputCssVariables()` → `Settings::cssVariables()` (`Settings.php:174-249`):
- Always emits `:root { ... }` with light vars.
- Emits `[data-theme="dark"] { ... }` AND `@media (prefers-color-scheme:dark) { [data-theme="system"] { ... } }` **only** when `$toggle || $default !== 'light'` (`Settings.php:233-243`).
- Output is unlayered → wins over app.css via cascade.

Source B — compiled `resources/css/app.css:98-127`:
- Fallback dark vars inside `@layer theme { [data-theme="dark"] {...} }` AND `@media (prefers-color-scheme:dark) { [data-theme="system"] {...} }`.
- **This is always shipped**, regardless of settings — contradicts the "saving bandwidth" notice in the admin panel (`DarkMode.jsx:56-63`).

### Runtime layer
- Boot script, inline in `<head>` of each layout (`app.blade.php:6-10`, `landing.blade.php:6-10`, `template-canvas.blade.php:11-15`, `template-transparent.blade.php:11-15`), gated by
  `$hasDarkMode = $showDarkModeToggle || $darkModeDefault !== 'light'`:
  ```js
  var t = localStorage.getItem('brndle-theme');
  if (t==='dark'||t==='light') document.documentElement.setAttribute('data-theme', t);
  else document.documentElement.setAttribute('data-theme', matchMedia('(prefers-color-scheme:dark)').matches ? 'dark' : 'light');
  ```
- Toggle button + click handler in `resources/views/partials/components/dark-mode-toggle.blade.php`.

### Include points (where the toggle renders)
Every include is `@include('partials.components.dark-mode-toggle')`. Callers:

| File | Line(s) | Gate |
|------|---------|------|
| layouts/app.blade.php | 46 | `@if($hasDarkMode)` |
| layouts/landing.blade.php | 41 | `@if($hasDarkMode)` |
| template-canvas.blade.php | 35 | `@if($hasDarkMode)` |
| template-transparent.blade.php | 115 | `@if($hasDarkMode)` |
| sections/header.blade.php | 69, 134, 176, 231, 272, 342, 383, 434, 492, 562, 603, 672, 713, 778, 819 | `@if($toggleInHeader)` (= `showDarkModeToggle && position === 'header'`) |

Each header style includes the partial **inside the desktop nav AND inside the mobile menu** (two-up). That's intentional for UX but becomes a bug because of point 2 below.

---

## 2. Bugs Found

### BUG A — Duplicate DOM IDs + duplicate `<script>` when partial is included multiple times (critical)
The partial renders hard-coded IDs (`brndle-dark-toggle`, `brndle-icon-sun`, `brndle-icon-moon`, `brndle-theme-announce`) **and** an inline IIFE script. When two or more copies of the partial are on the page:
- HTML has duplicate IDs (invalid, fails a11y).
- Each IIFE runs; each one calls `document.getElementById(...)` which returns only the **first** match. All N listeners therefore attach to the **same** first button.
- Clicking the (first) button fires N listeners. They all read `data-theme` at the same instant and write the opposite value. Net result:
  - N=2 (e.g. desktop+mobile header toggle): state toggles twice → no visible change.
  - N=3 (header+mobile+body-end via Bug B): toggles three times → appears to work but fires stacked a11y announces.

This is almost certainly what the user sees as "buggy enable/disable".

**Repro**: Settings → Dark Mode → Position = Header. Click the header toggle. Theme flips and flips back in the same frame → user sees nothing.

### BUG B — Body-end floating toggle still renders when position = `header` (critical)
`layouts/app.blade.php:45-47` (and sibling layouts) gate the body-end include on `$hasDarkMode`, **not** on position:
```blade
@if($hasDarkMode)
  @include('partials.components.dark-mode-toggle')
@endif
```
The partial's own gate is `@if($showDarkModeToggle ?? false)` — also not position-aware. When `dark_mode_toggle_position = 'header'`, `$positionClasses = match($position) { ..., 'header' => '', default => '...' }` returns an empty string, so the button renders **inline at the end of `<body>`** with no fixed positioning — a random 40×40px circle at the bottom of the document flow, often hidden below the footer. Combined with Bug A, IDs also collide.

**Fix**: Each layout's body-end include must be `@if($hasDarkMode && $darkModeTogglePosition !== 'header')`.

### BUG C — No way to return to System mode from the toggle (UX)
`dark-mode-toggle.blade.php:46-53`:
```js
var next = isDark ? 'light' : 'dark';
localStorage.setItem('brndle-theme', next);
```
The click handler only flips between `dark` and `light`. Once localStorage is set, `matchMedia('change')` listener (`:54-59`) skips applying OS preference forever (it only acts when localStorage is empty). Users who set `darkModeDefault = system` and click the toggle once lose system-follow until they manually clear localStorage.

**Fix**: Either cycle `system → dark → light → system`, or expose a small "Use system" link in the button UI, or clear `localStorage` when the user selects the same value as `$darkModeDefault`.

### BUG D — `cssVariables()` static cache is not invalidated on `Settings::save()` (minor)
`Settings.php:174-179` uses a PHP-local `static $cached`. `Settings::save()` (`Settings.php:87-110`) updates `self::$cache` but does not clear the cssVariables local cache. If anything in the request lifecycle reads CSS vars, saves settings, then reads again, the second read is stale. In practice the REST save happens in a separate request so this rarely manifests — but the invariant is wrong.

**Fix**: In `Settings::save()` and `Settings::clearCache()`, also invalidate the cssVariables cache (convert the `static` into a class-level `?string` property that `save()`/`reset()`/`clearCache()` can null).

### BUG E — Landing template claims "zero JS" but ships the toggle IIFE (inconsistency)
`landing.blade.php:15-16` comments "No JS loaded for landing pages — zero JS", then includes the dark-mode-toggle partial at line 41, which injects an inline `<script>`. Either:
- Change the comment, or
- Exclude the toggle on landing pages (landing pages arguably shouldn't have a floating moon overlay over the hero).

### BUG F — app.css always ships dark-mode vars; admin notice promises "no dark mode CSS is loaded" (false claim)
`DarkMode.jsx:56-63` displays the notice:
> "No dark mode CSS is loaded — saving bandwidth."
but `resources/css/app.css:98-127` unconditionally contains `[data-theme="dark"]` + `[data-theme="system"]` rules inside `@layer theme`, plus more at `:133-168`, `:176-191`, `:238-272`, `:289`, `:490-495`. Only the inline `<style id="brndle-css-vars">` block respects the disabled flag. Bandwidth savings are negligible (a few hundred bytes) and the notice is misleading.

**Fix**: Either drop the claim from the notice, or move the dark-mode CSS into a separate stylesheet that is only enqueued when `$hasDarkMode` is true.

### BUG G — `data-theme="system"` is overwritten by JS before CSS can use it (minor)
When `$darkModeDefault = 'system'` and no localStorage preference exists, the server renders `<html data-theme="system">`. The CSS selector `@media (prefers-color-scheme:dark) { [data-theme="system"] { ... } }` would handle OS-follow natively. But the inline boot script immediately overwrites the attribute to explicit `dark` or `light` (`app.blade.php:8`). The `[data-theme="system"]` selector is therefore only ever matched when JS is disabled. Works correctly by accident, but the runtime flow never keeps `system`. Combined with Bug C: once a user clicks, they never see `system` again.

### BUG H — FOUC when dark mode is disabled but localStorage holds a stale preference (minor)
If a user once had dark mode enabled, toggled to dark, then the admin disables the toggle + sets default to light, `$hasDarkMode` becomes false, the boot script is not injected, and the page renders with `data-theme="light"`. But the user's localStorage still says `brndle-theme=dark`. Nothing clears it. Next time dark mode is re-enabled, the user unexpectedly starts in dark again.

**Fix**: When `$hasDarkMode` is false, still emit a tiny script that does `localStorage.removeItem('brndle-theme')` once.

---

## 3. Where the Toggle Is Visible (matrix)

Assume `dark_mode_toggle = true`. The table below shows current behavior; cells marked ⚠ are caused by bugs A and B.

| Layout / Template | Position = bottom-right | Position = bottom-left | Position = header |
|-------------------|-------------------------|------------------------|-------------------|
| layouts/app (default) | bottom-right floating (1 button) | bottom-left floating (1 button) | ⚠ In header + orphan inline at end of body (Bug B), duplicate IDs (Bug A) |
| layouts/landing | same | same | ⚠ same |
| template-canvas | same (no header rendered) | same | ⚠ orphan inline at end of body only (no header to host it) |
| template-transparent | same | same | ⚠ In transparent header region + orphan inline at end of body |

Header styles that include the partial twice per page (desktop + mobile menu) — clicks cancel out per Bug A:
`sticky`, `centered`, `transparent`, `solid`, `split`, `banner`, `glass`, plus `minimal` (single overlay-only include, safe).

---

## 4. Minimal Fix Plan (if the user wants one later)

1. **Make the partial idempotent**. Accept an `$id` prop or generate a unique ID per render (e.g. `uniqid('brndle-dark-toggle-')`). Move the IIFE out of the partial and into `resources/js/app.js` (or enqueue it once). Wire the click handler to *all* elements matching `[data-brndle-dark-toggle]`.
2. **Gate the body-end includes on position**:
   ```blade
   @if($hasDarkMode && ($darkModeTogglePosition ?? '') !== 'header')
     @include('partials.components.dark-mode-toggle')
   @endif
   ```
   In `app.blade.php:45`, `landing.blade.php:40`, `template-canvas.blade.php:34`, `template-transparent.blade.php:114`.
3. **Add system cycle**: cycle `system → dark → light → system` in the click handler; when returning to `system`, `localStorage.removeItem('brndle-theme')` so `matchMedia` takes over.
4. **Invalidate cssVariables cache on save** (`Settings::save()`, `Settings::clearCache()`, `Settings::reset()`).
5. **Clear stale localStorage when dark mode is disabled** — emit a one-liner cleanup script in that branch.
6. **Fix the admin notice copy** — remove the "no dark mode CSS is loaded" claim, or actually enqueue the dark-mode CSS conditionally.
7. **Drop the "zero JS" comment** from `landing.blade.php` or gate the toggle out of landing.

After any fix, clear compiled Blade cache:
```bash
rm -rf "/Users/varundubey/Local Sites/elementor/app/public/wp-content/cache/acorn/framework/views/"*
```
Then browser-verify with Playwright MCP at desktop (1440px) and mobile (390px) viewports for each of the three positions.

---

## 5. Files to Touch (when fixing)

- `resources/views/partials/components/dark-mode-toggle.blade.php` — rewrite markup + move JS out
- `resources/js/app.js` — new global handler (must run once, idempotent)
- `resources/views/layouts/app.blade.php`, `layouts/landing.blade.php`, `template-canvas.blade.php`, `template-transparent.blade.php` — gate the body-end include on position
- `app/Settings/Settings.php` — invalidate cssVariables cache on save
- `admin/src/tabs/DarkMode.jsx` — fix the misleading notice
- `resources/css/app.css` — optionally move dark layer into a separate file for conditional enqueue
