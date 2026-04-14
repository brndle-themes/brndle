# Brndle Dark Mode — Complete Refactor Plan

Date: 2026-04-14
Goal: Replace the current fragile dark-mode wiring with a single, idempotent,
production-grade system that works across every layout, every header style,
every page template, and every footer style. No patchwork — one clean flow.

Audit that motivates this plan:
`docs/superpowers/audits/2026-04-14-dark-mode-flow-audit.md`

---

## 1. Design Principles

1. **Single source of truth for runtime state** — `<html data-theme>` is the only place that stores theme. Code everywhere else reads/writes it via the same helper.
2. **Idempotent partial** — `dark-mode-toggle.blade.php` renders pure markup with no IDs and no inline `<script>`. Safe to include N times on a page.
3. **One global controller** — a single JS module attaches behaviour to every `[data-brndle-dark-toggle]` button on the page, runs exactly once, and uses a single live region.
4. **Three real states** — `light`, `dark`, `system`. Clicking cycles through all three. System mode clears `localStorage` so `prefers-color-scheme` takes over.
5. **Server-driven visibility** — Blade is the only place that decides whether the toggle renders. JS never creates or removes toggles.
6. **Server-driven CSS** — dark-mode CSS variables live inline (emitted by `Settings::cssVariables()`) and nowhere else. The admin "Dark mode is disabled" notice becomes truthful.
7. **No FOUC, no stale state** — the boot script runs synchronously in `<head>`, before body render. When dark mode is fully disabled, a one-shot cleanup clears stale `localStorage`.

---

## 2. Surface Covered (every place the toggle can appear)

### Layouts (body-end floating button)
- `resources/views/layouts/app.blade.php` (default layout — used by all non-template pages)
- `resources/views/layouts/landing.blade.php` (landing pages)
- `resources/views/template-canvas.blade.php` (Full Canvas template)
- `resources/views/template-transparent.blade.php` (Transparent Header template)

### Headers (inline in-nav toggle, 12 styles × 1–2 copies each)
`resources/views/sections/header.blade.php` — 16 include sites across these styles:
- `minimal` (1 include — overlay)
- `centered` (2 — desktop + mobile menu)
- `transparent` (2)
- `solid` (2)
- `sticky` (2 — default style)
- `split` (2)
- `banner` (2)
- `glass` (2)

### Footers
`resources/views/sections/footer.blade.php` — does **not** include the dark-mode-toggle. It uses `dark:hidden` / `dark:block` CSS classes for logo swaps at lines 35, 149–150, 228, 352–353, 429. No rendering change needed; must be verified visually in dark mode.

### Specialised templates / partials (read-only in this refactor, verified only)
- `resources/views/template-homepage.blade.php` — hard-codes `data-theme="light"` and has its own dark-canvas styling. **Out of scope**: intentional custom canvas page. Will not auto-host the toggle. Documented as such.
- `resources/views/partials/single/minimal-dark.blade.php` — forces `<div data-theme="dark">` on the article wrapper. Works alongside site-wide theme (local override). Will not change, but must be verified that the floating toggle still cycles the rest of the page correctly.

---

## 3. Target Architecture

### 3.1 The partial (`partials/components/dark-mode-toggle.blade.php`)
```blade
@php
  $position = $position ?? ($darkModeTogglePosition ?? 'bottom-right');
  $inline = ($position === 'header');
  $positionClasses = match ($position) {
      'bottom-left'  => 'fixed bottom-6 left-6 z-50',
      'bottom-right' => 'fixed bottom-6 right-6 z-50',
      default        => '', // inline in header
  };
@endphp

@if ($showDarkModeToggle ?? false)
  <button
    type="button"
    data-brndle-dark-toggle
    class="{{ $positionClasses }} w-10 h-10 rounded-full bg-surface-secondary border border-surface-tertiary shadow-md flex items-center justify-center cursor-pointer hover:border-accent transition-colors"
    aria-label="{{ __('Toggle color theme', 'brndle') }}"
  >
    <svg data-brndle-icon="sun"    class="w-5 h-5 text-text-secondary hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">...</svg>
    <svg data-brndle-icon="moon"   class="w-5 h-5 text-text-secondary hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">...</svg>
    <svg data-brndle-icon="system" class="w-5 h-5 text-text-secondary hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">...</svg>
  </button>
@endif
```
No IDs. No `<script>`. No live region. Safe to render any number of times. Icon visibility controlled by the global controller (adds/removes `.hidden`).

### 3.2 The boot script (inline in `<head>` of each layout)
One 3-line synchronous script. Present only when `$hasDarkMode` is true.
```html
<script>(function(){var s=localStorage.getItem('brndle-theme');var d=document.documentElement;if(s==='dark'||s==='light'){d.setAttribute('data-theme',s)}else{d.setAttribute('data-theme','system')}}());</script>
```
Notes:
- Applies `system` literally when no preference is stored, so CSS `@media (prefers-color-scheme: dark) { [data-theme="system"] {...} }` drives the OS follow without JS overriding to explicit `dark`/`light`.
- No FOUC because it runs synchronously before `<body>`.

When `$hasDarkMode` is **false**, emit a cleanup script instead:
```html
<script>try{localStorage.removeItem('brndle-theme')}catch(e){}</script>
```

### 3.3 The global controller (`resources/js/dark-mode.js`)
Imported from `resources/js/app.js`. Responsibilities:
- `setTheme(value)` — writes `<html data-theme>`, writes or clears `localStorage`, updates every `[data-brndle-dark-toggle]` button's icon and `aria-label`, announces change via a single body-end live region it lazily creates once.
- On DOMContentLoaded: query `[data-brndle-dark-toggle]`, attach click handler. Click cycles `system → dark → light → system`.
- `matchMedia('(prefers-color-scheme: dark)')` change listener — only re-paints when current state is `system`.
- Runs exactly once: guarded by `window.__brndleDarkModeBooted`.

### 3.4 The CSS (`resources/css/app.css`)
- **Remove** the `@layer theme { [data-theme="dark"] {...} }` and `[data-theme="system"] {...}` **variable fallback** at `app.css:98-127`. These duplicate what `Settings::cssVariables()` emits inline and make the admin "bandwidth" notice a lie.
- **Keep** all non-variable dark rules (prose, logo swaps, wp-block-table, `.brndle-section-dark`, scrollbar) — they cost nothing when `[data-theme]` never becomes `dark`/`system`.
- Now when the admin disables dark mode fully, only `:root { ... }` ships inline, the `data-theme` attribute stays `light`, and zero dark-mode rules apply. The notice becomes truthful.

### 3.5 The settings layer (`app/Settings/Settings.php`)
- Convert `cssVariables()`'s function-local `static $cached` into a class-level `private static ?string $cssVarsCache = null;`.
- In `save()`, `reset()`, and `clearCache()`, also null `$cssVarsCache`.

### 3.6 The admin UI (`admin/src/tabs/DarkMode.jsx`)
Rewrite the disabled-mode notice so it's accurate:
> "Dark mode is fully disabled. The site will always display in light mode and no theme-switching JS runs."

Drop the "saving bandwidth" claim entirely.

---

## 4. Files Modified / Created

| Action | File | Purpose |
|--------|------|---------|
| Create | `resources/js/dark-mode.js` | Global controller (cycle states, update all toggles, matchMedia listener, live region) |
| Edit   | `resources/js/app.js` | Import `dark-mode.js` |
| Rewrite | `resources/views/partials/components/dark-mode-toggle.blade.php` | Dumb partial: data-attrs, no IDs, no script, 3 icons |
| Edit   | `resources/views/layouts/app.blade.php` | Single boot script; body-end include gated on `$hasDarkMode && position !== 'header'`; cleanup-when-disabled branch |
| Edit   | `resources/views/layouts/landing.blade.php` | Same changes; drop misleading "zero JS" comment |
| Edit   | `resources/views/template-canvas.blade.php` | Same changes |
| Edit   | `resources/views/template-transparent.blade.php` | Same changes |
| Edit   | `resources/views/sections/header.blade.php` | No structural change — all 16 include sites already gate on `$toggleInHeader`, now safe because the partial is idempotent. Verify each |
| Edit   | `resources/css/app.css` | Delete the `@layer theme { [data-theme="dark"] {...} }` + system-system variable blocks at lines 98-127 |
| Edit   | `app/Settings/Settings.php` | Class-level `$cssVarsCache`; invalidate on save/reset/clearCache |
| Edit   | `admin/src/tabs/DarkMode.jsx` | Honest disabled-mode notice |
| — | `resources/views/sections/footer.blade.php` | **No changes** — it doesn't render the toggle, only uses `dark:hidden` / `dark:block` classes that already work via existing CSS rules |
| — | `resources/views/template-homepage.blade.php` | **No changes** — intentional dark canvas, out of scope |
| — | `resources/views/partials/single/minimal-dark.blade.php` | **No changes** — intentional local override, compatible |

---

## 5. Verification Matrix

Build first:
```bash
cd "/Users/varundubey/Local Sites/elementor/app/public/wp-content/themes/brndle"
npm run build
npm run admin:build
rm -rf "/Users/varundubey/Local Sites/elementor/app/public/wp-content/cache/acorn/framework/views/"*
```

Then use Playwright MCP to verify (autologin `?autologin=1`):

**Page matrix (5 pages):**
- `/` — home with `layouts/app` + active `header_style`
- `/?page=landing` or any page using `template-landing`
- Any page using `template-canvas`
- Any page using `template-transparent`
- A single post with `single_layout = minimal-dark`

**Setting matrix (3 × 3 × 3 = must cover these 9 combos):**
- `dark_mode_toggle` ∈ {true, false}
- `dark_mode_toggle_position` ∈ {bottom-right, bottom-left, header}
- `dark_mode_default` ∈ {light, dark, system}

For each meaningful combination:
1. Load page, screenshot desktop (1440px) and mobile (390px).
2. Confirm exactly **one** clickable toggle button is visible (or zero, when disabled).
3. Click it — theme should cycle `system → dark → light → system`.
4. Verify `localStorage` is set/cleared correctly at each step.
5. Verify CSS vars update (check e.g. `<html>` computed background).

**Header-style matrix (8 styles):**
Iterate `header_style` ∈ {minimal, centered, transparent, solid, sticky, split, banner, glass} with `dark_mode_toggle_position = header`. Each must show the toggle in the nav (desktop) and in the mobile menu where applicable, and clicking must work — each copy responds simultaneously because all share state via `data-theme`.

**Footer-style matrix (6 styles):**
Iterate `footer_style` ∈ {dark, light, columns, minimal, big, stacked} in both light and dark mode. Logos and colours must remain legible.

Automated command pattern (repeatable per combination):
```
mcp__mcp-local-wp__mysql_write( UPDATE wp_options SET option_value=... WHERE option_name='brndle_settings' )
mcp__plugin_playwright_playwright__browser_navigate /?autologin=1
mcp__plugin_playwright_playwright__browser_take_screenshot
```

**Success criteria:**
- No duplicate DOM IDs reported by browser console.
- No JS errors on any page.
- Exactly one toggle visible (or zero when disabled) per page.
- Clicking toggle cycles 3 states, updates all copies, no dead clicks.
- When fully disabled, `localStorage['brndle-theme']` is removed on next visit.
- `<style id="brndle-css-vars">` contains dark blocks only when dark mode is enabled.
- No FOUC on desktop throttled to Fast 3G.

---

## 6. Implementation Order

1. `app/Settings/Settings.php` — class-level cache invalidation (behaviour-preserving refactor, safe to land first).
2. `resources/css/app.css` — remove variable duplicates.
3. `resources/js/dark-mode.js` — new global controller.
4. `resources/js/app.js` — import the controller.
5. `resources/views/partials/components/dark-mode-toggle.blade.php` — rewrite dumb partial.
6. `resources/views/layouts/app.blade.php` — gate include + boot/cleanup script.
7. `resources/views/layouts/landing.blade.php` — same.
8. `resources/views/template-canvas.blade.php` — same.
9. `resources/views/template-transparent.blade.php` — same.
10. `admin/src/tabs/DarkMode.jsx` — fix notice copy.
11. `npm run build`, `npm run admin:build`, clear Acorn cache.
12. Browser verification pass per §5.

No commits until the verification pass is clean.
