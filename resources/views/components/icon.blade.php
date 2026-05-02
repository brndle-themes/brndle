{{--
  <x-icon> — Lucide SVG renderer.

  Reads `resources/icons/{name}.svg` (populated at build time by
  `npm run icons:copy` from the curated list in
  `bin/copy-lucide-icons.mjs`), strips the literal `width` / `height`
  attributes Lucide ships with so callers can size via CSS, and applies
  any class / aria-* the caller passes.

  Convention reminder (CLAUDE.md → Icons): use this for frontend Blade
  templates; use `@wordpress/icons` from JSX in editor / admin code.
  Don't paste raw `<svg>` markup and don't use emoji as an affordance.

  Props:
    - name      (string, required) — kebab-case Lucide icon name. Must
                                     exist in resources/icons/.
    - class     (string)            — extra classes on the <svg>
    - size      (int|string)        — explicit pixel size; otherwise CSS
                                     drives via 1em / w-* utilities.
    - aria      (string)            — accessible label; if set, role=img
                                     + aria-label are added. If absent
                                     (decorative), aria-hidden=true is
                                     emitted.
--}}
@php
    $name  = $name ?? '';
    $class = $class ?? '';
    $size  = $size ?? null;
    $aria  = $aria ?? null;

    $svg = '';
    $iconPath = get_theme_file_path("resources/icons/{$name}.svg");
    if ($name !== '' && preg_match('/^[a-z0-9-]+$/', $name) && file_exists($iconPath)) {
        $svg = (string) file_get_contents($iconPath);
    }

    if ($svg !== '') {
        // Strip Lucide's own width/height so callers control via CSS.
        $svg = preg_replace('/\s(width|height)="[^"]*"/', '', $svg);

        $extraAttrs = '';
        if ($class !== '') {
            $extraAttrs .= ' class="' . esc_attr($class) . '"';
        }
        if ($size !== null && $size !== '') {
            $sizeAttr = is_int($size) ? (string) $size : esc_attr((string) $size);
            $extraAttrs .= ' width="' . $sizeAttr . '" height="' . $sizeAttr . '"';
        }
        if ($aria !== null && $aria !== '') {
            $extraAttrs .= ' role="img" aria-label="' . esc_attr($aria) . '"';
        } else {
            $extraAttrs .= ' aria-hidden="true" focusable="false"';
        }

        // Inject the extra attrs into the opening <svg> tag.
        $svg = preg_replace('/<svg\b/', '<svg' . $extraAttrs, $svg, 1);
    }
@endphp

{!! $svg !!}
