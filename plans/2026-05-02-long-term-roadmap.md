# Brndle Long-Term Roadmap

_Written 2026-05-02 after the 1.3.0 / 1.3.1 quality pass shipped. This is a
forward-looking plan — not a list of bugs to fix this week, but the
structural improvements that pay down compounding fragility as brndle scales
to 100+ client sites._

## Operating principle

Prefer **structural fixes that prevent a class of bugs** over per-instance
patches. Per-instance patches accumulate as silent fragility; the kind of
bug that bites when someone adds a new template, child theme, or block.

When the choice is between a workaround and an upstream-aligned pattern,
pick upstream. When introducing a CSS variant or PHP convention, design it
so child themes that drop the convention still work (the "no-attribute"
adoptive case).

Examples of this thinking already shipped:
- `@custom-variant dark (...)` — one declaration replaces 21 lines of
  per-utility `[data-theme="dark"]` workarounds and future-proofs every
  new `dark:foo` utility.
- `editor.css @import "./app.css"` — one path for editor + frontend
  styling, instead of two parallel enqueue mechanisms.
- `html { font-size: var(--font-size-base) }` — the rem cascade scales
  globally; we don't have to override per-element.

## Themes for the next two releases

### 1. Settings: make the schema self-describing

The current setup has three sources of truth that have to be kept in
sync by hand:

  - `app/Settings/Defaults.php` — keys + defaults + type-key lists
  - `admin/src/tabs/*.jsx` — hand-coded React forms
  - `app/View/Composers/Theme.php` — Blade variable bridges

The 1.3.1 audit caught two settings that were saved + emitted but never
read by any stylesheet (`font_size_base`, `heading_scale`). That hole
exists because the three sources can drift. The long-term move is to
make Defaults the single source of truth and generate the rest:

  - Add field metadata directly to Defaults (label, control type, range,
    section) — one entry per setting describes everything needed to
    render the form, sanitize, expose to Blade.
  - Replace the hand-coded admin tabs with a generic React form that
    iterates the metadata.
  - Have `Theme.php` composer iterate Defaults to inject every setting
    into Blade (with a per-key `expose` flag for ones that should/should
    not reach views).

The migration is incremental — keep the existing tabs/composers, add the
metadata, gradually swap each tab for the generated form once the
metadata covers it.

### 2. Settings: real migration system

`Settings::migrate()` currently has a single empty 0→1 stub. As we add
new fields (e.g. `image_id`, `image_alt`, `photo_id`) the schema
silently drifts without bumping `Settings::VERSION`. Long-term:

  - One named migration per non-trivial schema change.
  - `Settings::VERSION` bumped in the same commit as the fields it
    introduces.
  - Migrations run on theme activation/upgrade, not lazily.
  - A test harness that exercises the upgrade path from each historical
    version to current.

### 3. CI: turn the audits into guardrails

The 1.3.1 audit (43-key cross-reference, dead-setting hunt, dark-mode
variant verification) was manual. None of it would catch a regression in
1.4.0 unless someone runs the audit again. Long-term:

  - **Settings consistency check** — _**shipped**_ as
    `bin/check-settings-consistency.mjs`. Asserts every `Defaults::all()`
    key has matching entries in `Defaults::schema()`, in `admin/src/
    tabs/*.jsx`, and in PHP / Blade consumers. Wired into
    `.github/workflows/main.yml` in 1.3.2.
  - **Blade compile dry-run** — _**shipped**_ as `bin/check-blade-
    compile.php`. Compiles every template through Acorn's BladeCompiler,
    then `token_get_all($source, TOKEN_PARSE)` on the output. Caught
    six latent bugs on first run (5 `@media` / `@keyframes` escapes plus
    the `<html @php(language_attributes())>` regex collision). The
    workflow file install template lives in `bin/github-actions/`
    pending the maintainer's manual `cp` (security hook still blocks
    direct workflow writes).
  - **Tailwind variant assertion** — _**deferred, not next.**_
    The original concern is "did Tailwind generate the dark: rule
    under the right selector?" 1.3.2's `@custom-variant dark` change
    fixed the only known failure mode and the rule is asserted at
    runtime by the dark-mode portion of the E2E journey. A
    build-time check would also need to know which utilities the
    project considers "must compile under the custom variant" —
    that's a shape-of-data question we don't have an answer for yet.
    Revisit when a second `dark:`-style custom variant gets added.
  - **Visual regression** — _**deferred, not next.**_ Playwright
    screenshots per block + per layout, diffed against a baseline.
    Real value but real cost: needs a stable rendering host (theme
    test fixtures + seeded content), pixel-tolerance tuning per
    block, baseline storage / Git LFS, and a flake-rate budget.
    The 1.3.4 E2E journey covers the high-value paths
    behaviourally; visual-regression can layer on once the test
    fixtures + seeded content exist as a separate plan item.

The existing `.github/workflows/main.yml` covers PHP lint + Pint + Blade
single-line @php + settings consistency. The two checks above are the
deliberately-deferred layer.

### 4. Block attribute deprecations

Brndle blocks are SSR (`save: () => null`) so attribute changes don't
trigger "Invalid block" warnings. The trade-off is that Blade templates
carry legacy branches forever — `logos.blade.php` accepts both
`is_string($logo)` (pre-1.3.0) and `is_array($logo) && isset($logo['url
'])` (1.3.0+). Two years of additions and the templates become
unreadable.

Long-term: a formal block-attribute deprecation system.

  - Each block declares `attribute_versions` in `block.json`.
  - On render, a normaliser upgrades old attribute shapes to current.
  - Blade templates only handle the current shape.
  - After a major release, drop the normaliser for shapes more than two
    versions old, log a one-time admin notice for posts still using
    them.

### 5. Upstream sync automation

`bin/check-upstream.sh` exists but is manual. The Acorn 5 → 6 upgrade
would have surfaced earlier with weekly tracking. Schedule a recurring
job (GitHub Action on a cron, or a hosted /loop agent) to:

  - Run the script
  - Open a tracking issue when a major upstream version lands
  - Auto-PR for safe minor bumps (vite plugin, etc.)
  - Surface notable Sage commit messages in the issue body

### 6. Type scale integration

1.3.1 wires `--font-size-base` to `html { font-size }` and exposes
`--text-h1` … `--text-h6` for opt-in usage. Currently nothing
auto-applies the heading ramp because block markup uses explicit
Tailwind sizes (`text-[clamp(3rem,7vw,5rem)]`). Long-term:

  - Apply the ramp inside `.prose` so blog posts pick up the user's
    heading-scale automatically.
  - Audit each block: if the heading is intentionally over-sized for
    visual impact (hero), keep the explicit clamp; if it's just
    "default heading" (faq title, features title), switch to the ramp.
  - Document "use the ramp by default; override only with intent."

### 7. Releases as a workflow

Releases today rely on a developer running `bin/release.sh` locally + a
manual `gh release create`. Sage v11.2 added a `release.yml` GitHub
Action that bumps style.css and tags from `workflow_dispatch`.
Long-term:

  - Adapt that workflow for brndle.
  - Build the release zip in CI (signed, reproducible) and attach.
  - Auto-generate the changelog from squashed PR titles since the last
    tag.

## Out of scope (for now)

These came up during the audits but aren't priorities yet:

  - Editor canvas dark-mode preview — useful but a feature, not a fix.
    Authors edit in light by convention.
  - WCAG AA color-contrast audit on every dark surface — needs axe-core
    or Lighthouse tooling pass; tackle when adopting visual regression.
  - Per-block "follow theme" option — would let blocks inherit the
    user's dark/light choice instead of being explicitly dark/light.
    Real feature work.

## How to use this document

When starting a session, read `CLAUDE.md` → "Recent Changes" first, then
this roadmap. If the work falls under a roadmap theme, take the
structural path even if the immediate fix is smaller. If it doesn't,
note in CLAUDE.md whether the change creates new technical debt that
should be added here.

When closing a roadmap item, move it to `CLAUDE.md` → "Recent Changes"
and remove the section here.
