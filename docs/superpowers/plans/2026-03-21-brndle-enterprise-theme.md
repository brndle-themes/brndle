# Brndle Enterprise Theme — Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build enterprise-level backend settings panel, 12 color schemes, dark/light mode, 8 font pairs, 5 blog archive layouts, 8 single post layouts — all switchable from admin UI, zero coding required for end users.

**Architecture:** Settings stored in `wp_options` via REST API. React admin panel using `@wordpress/components`. CSS custom properties injected in `<head>` to override Tailwind defaults at runtime. Blade templates read settings via a `Settings` helper class and conditionally include the appropriate layout partial.

**Tech Stack:** PHP 8.2+, Roots Acorn 5, Laravel Blade, Tailwind CSS 4, Vite 7, React (`@wordpress/scripts`), WordPress REST API, `@wordpress/components`

---

## File Map

### New Files to Create

```
app/
├── Settings/
│   ├── Settings.php                    # Central settings accessor (singleton)
│   ├── Defaults.php                    # All default values in one place
│   ├── ColorPalette.php                # Color math: auto-generate palette from hex
│   └── FontPairs.php                   # Font pair definitions + Google Fonts URL builder
├── Providers/
│   └── SettingsServiceProvider.php      # Registers settings, REST routes, admin page
├── View/Composers/
│   └── Theme.php                       # Injects settings into all views

resources/views/
├── partials/
│   ├── archive/
│   │   ├── grid.blade.php              # 3-col card grid (existing content.blade.php refactored)
│   │   ├── list.blade.php              # Single-col large cards
│   │   ├── magazine.blade.php          # Hero featured + grid
│   │   ├── editorial.blade.php         # Text-only chronological
│   │   └── minimal.blade.php           # Clean typography, author-forward
│   ├── single/
│   │   ├── standard.blade.php          # Existing content-single.blade.php refactored
│   │   ├── hero-immersive.blade.php    # Full-viewport hero overlay
│   │   ├── sidebar.blade.php           # Sticky TOC + content
│   │   ├── editorial.blade.php         # Drop caps, pull quotes, wide images
│   │   ├── cinematic.blade.php         # 21:9 hero, full-bleed images
│   │   ├── presentation.blade.php      # Each h2 = visual section
│   │   ├── split.blade.php             # Fixed left, scroll right
│   │   └── minimal-dark.blade.php      # Dark bg, light text, monospace
│   └── components/
│       ├── reading-progress.blade.php   # Reading progress bar
│       ├── table-of-contents.blade.php  # Sticky TOC
│       ├── author-box.blade.php         # Author card
│       ├── related-posts.blade.php      # Related posts grid
│       ├── social-share.blade.php       # Share buttons
│       ├── post-navigation.blade.php    # Prev/next post links
│       └── dark-mode-toggle.blade.php   # Frontend theme toggle

admin/
├── src/
│   ├── index.js                        # Admin page entry point
│   ├── App.jsx                         # Main React app with tab router
│   ├── api.js                          # REST API helpers (save/load settings)
│   ├── components/
│   │   ├── TabNav.jsx                  # Tab navigation
│   │   ├── ColorSchemeSelector.jsx     # Visual scheme grid + custom picker
│   │   ├── FontPairSelector.jsx        # Font pair cards with preview
│   │   ├── LayoutSelector.jsx          # Visual layout thumbnails
│   │   └── ToggleRow.jsx               # Label + toggle + description
│   └── tabs/
│       ├── SiteIdentity.jsx            # Logo, name, favicon, socials
│       ├── Colors.jsx                  # 12 presets + custom hex
│       ├── DarkMode.jsx                # Default mode, toggle, position
│       ├── Typography.jsx              # Font pairs, size, scale
│       ├── Header.jsx                  # Style, CTA, mobile menu
│       ├── Footer.jsx                  # Columns, copyright, socials
│       ├── BlogArchive.jsx             # 5 layout thumbnails + options
│       ├── SinglePost.jsx              # 8 layout thumbnails + toggles
│       └── Performance.jsx             # Bloat removal toggles
├── admin.css                           # Admin panel custom styles
└── webpack.config.js                   # OR vite config for admin build
```

### Files to Modify

```
app/Providers/ThemeServiceProvider.php   # Register SettingsServiceProvider
app/View/Composers/App.php              # Add dark mode class, theme settings
app/setup.php                           # Font preloading, settings-based features
app/filters.php                         # Settings-aware excerpt filter
resources/css/app.css                   # Dark mode CSS variables layer
resources/views/layouts/app.blade.php   # data-theme attr, inline CSS vars, font preload
resources/views/layouts/landing.blade.php # Same dark mode support
resources/views/index.blade.php         # Dynamic archive layout include
resources/views/single.blade.php        # Dynamic single layout include
resources/views/sections/header.blade.php # Settings-driven header style
resources/views/sections/footer.blade.php # Settings-driven footer
blocks/hero/render.blade.php            # Use CSS vars instead of hardcoded colors
blocks/*/render.blade.php               # All blocks: use CSS vars for dark mode
composer.json                           # No new deps needed
package.json                            # Add @wordpress/scripts for admin build
```

---

## Task 1: Settings Foundation (PHP)

**Files:**
- Create: `app/Settings/Defaults.php`
- Create: `app/Settings/Settings.php`
- Create: `app/Settings/ColorPalette.php`
- Create: `app/Settings/FontPairs.php`

- [ ] **Step 1: Create Defaults.php — all theme defaults in one file**

```php
<?php
// app/Settings/Defaults.php
namespace Brndle\Settings;

class Defaults
{
    public static function all(): array
    {
        return [
            // Site Identity
            'site_logo_light' => '',
            'site_logo_dark' => '',
            'social_links' => [
                'twitter' => '',
                'linkedin' => '',
                'github' => '',
                'instagram' => '',
            ],

            // Colors
            'color_scheme' => 'sapphire',     // One of 12 preset keys
            'custom_accent' => '',             // Custom hex override (empty = use preset)

            // Dark Mode
            'dark_mode_default' => 'light',    // light | dark | system
            'dark_mode_toggle' => true,        // Show toggle on frontend
            'dark_mode_toggle_position' => 'bottom-right', // bottom-right | bottom-left | header

            // Typography
            'font_pair' => 'inter',            // One of 8 pair keys
            'font_size_base' => 16,            // px
            'heading_scale' => 1.25,           // ratio

            // Header
            'header_style' => 'sticky',        // transparent | solid | sticky
            'header_cta_text' => '',
            'header_cta_url' => '',
            'header_mobile_style' => 'slide',  // slide | fullscreen

            // Footer
            'footer_columns' => 3,             // 2 | 3 | 4
            'footer_copyright' => '',          // Empty = auto "{site_name}. All rights reserved."
            'footer_show_social' => true,
            'footer_style' => 'dark',          // dark | light

            // Blog Archive
            'archive_layout' => 'grid',        // grid | list | magazine | editorial | minimal
            'archive_posts_per_page' => 12,
            'archive_show_sidebar' => false,
            'archive_show_category_filter' => true,

            // Single Post
            'single_layout' => 'standard',     // standard | hero-immersive | sidebar | editorial | cinematic | presentation | split | minimal-dark
            'single_show_progress_bar' => true,
            'single_show_reading_time' => true,
            'single_show_author_box' => true,
            'single_show_social_share' => true,
            'single_show_related_posts' => true,
            'single_show_toc' => false,
            'single_show_post_nav' => true,

            // Performance
            'perf_remove_emoji' => true,
            'perf_remove_embed' => true,
            'perf_lazy_images' => true,
            'perf_preload_fonts' => true,
        ];
    }
}
```

- [ ] **Step 2: Create ColorPalette.php — auto-generate palette from one hex**

```php
<?php
// app/Settings/ColorPalette.php
namespace Brndle\Settings;

class ColorPalette
{
    /**
     * 12 enterprise presets sourced from $1B+ companies.
     */
    public static function presets(): array
    {
        return [
            'neutral'   => ['accent' => '#18181b', 'name' => 'Neutral',   'base' => 'zinc'],
            'sapphire'  => ['accent' => '#0070F3', 'name' => 'Sapphire',  'base' => 'cool'],
            'indigo'    => ['accent' => '#635BFF', 'name' => 'Indigo',    'base' => 'cool'],
            'cobalt'    => ['accent' => '#0C66E4', 'name' => 'Cobalt',    'base' => 'neutral'],
            'trust'     => ['accent' => '#0530AD', 'name' => 'Trust',     'base' => 'cool'],
            'commerce'  => ['accent' => '#2a6e3f', 'name' => 'Commerce',  'base' => 'warm'],
            'signal'    => ['accent' => '#F22F46', 'name' => 'Signal',    'base' => 'neutral'],
            'coral'     => ['accent' => '#FF7A59', 'name' => 'Coral',     'base' => 'warm'],
            'aubergine' => ['accent' => '#4A154B', 'name' => 'Aubergine', 'base' => 'cool'],
            'midnight'  => ['accent' => '#1e3a5f', 'name' => 'Midnight',  'base' => 'cool'],
            'stone'     => ['accent' => '#57534e', 'name' => 'Stone',     'base' => 'warm'],
            'carbon'    => ['accent' => '#09090b', 'name' => 'Carbon',    'base' => 'zinc'],
        ];
    }

    /**
     * Generate full light + dark palette from a single hex.
     */
    public static function generate(string $hex): array
    {
        $hsl = self::hexToHsl($hex);

        return [
            // Accent variants
            'accent'        => $hex,
            'accent-hover'  => self::hslToHex($hsl[0], $hsl[1], max(0, $hsl[2] - 12)),
            'accent-light'  => self::hslToHex($hsl[0], min(100, $hsl[1] + 10), min(100, $hsl[2] + 20)),
            'accent-subtle' => self::hslToHex($hsl[0], max(0, $hsl[1] - 40), 96),
            'accent-muted'  => $hex . '1a', // 10% opacity

            // Light mode surfaces (tinted 2-3% toward accent hue)
            'light' => [
                'surface-primary'   => '#fafafa',
                'surface-secondary' => self::hslToHex($hsl[0], 8, 98),
                'surface-tertiary'  => self::hslToHex($hsl[0], 6, 96),
                'surface-inverse'   => self::hslToHex($hsl[0], 20, 7),
                'text-primary'      => '#0f172a',
                'text-secondary'    => '#475569',
                'text-tertiary'     => '#94a3b8',
            ],

            // Dark mode surfaces (tinted toward accent hue)
            'dark' => [
                'surface-primary'   => self::hslToHex($hsl[0], 25, 5),
                'surface-secondary' => self::hslToHex($hsl[0], 20, 8),
                'surface-tertiary'  => self::hslToHex($hsl[0], 18, 12),
                'surface-inverse'   => '#f8fafc',
                'text-primary'      => '#f1f5f9',
                'text-secondary'    => '#94a3b8',
                'text-tertiary'     => '#64748b',
            ],

            // Semantic (fixed across all schemes)
            'success' => '#16a34a',
            'warning' => '#d97706',
            'error'   => '#dc2626',
            'info'    => '#2563eb',
        ];
    }

    /**
     * Convert hex to HSL array [h, s, l].
     */
    public static function hexToHsl(string $hex): array
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;

        if ($max === $min) {
            return [0, 0, round($l * 100)];
        }

        $d = $max - $min;
        $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

        $h = match ($max) {
            $r => (($g - $b) / $d + ($g < $b ? 6 : 0)) / 6,
            $g => (($b - $r) / $d + 2) / 6,
            $b => (($r - $g) / $d + 4) / 6,
        };

        return [round($h * 360), round($s * 100), round($l * 100)];
    }

    /**
     * Convert HSL to hex.
     */
    public static function hslToHex(float $h, float $s, float $l): string
    {
        $h /= 360;
        $s /= 100;
        $l /= 100;

        if ($s === 0.0) {
            $r = $g = $b = $l;
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;
            $r = self::hueToRgb($p, $q, $h + 1/3);
            $g = self::hueToRgb($p, $q, $h);
            $b = self::hueToRgb($p, $q, $h - 1/3);
        }

        return sprintf('#%02x%02x%02x', round($r * 255), round($g * 255), round($b * 255));
    }

    private static function hueToRgb(float $p, float $q, float $t): float
    {
        if ($t < 0) $t += 1;
        if ($t > 1) $t -= 1;
        if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
        if ($t < 1/2) return $q;
        if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
        return $p;
    }
}
```

- [ ] **Step 3: Create FontPairs.php — 8 pairs sourced from real enterprise sites**

```php
<?php
// app/Settings/FontPairs.php
namespace Brndle\Settings;

class FontPairs
{
    public static function pairs(): array
    {
        return [
            'system' => [
                'name'    => 'System',
                'heading' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                'body'    => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                'google'  => null, // No Google Fonts needed
                'source'  => 'GitHub',
            ],
            'inter' => [
                'name'    => 'Inter',
                'heading' => "'Inter', sans-serif",
                'body'    => "'Inter', sans-serif",
                'google'  => 'Inter:wght@400;500;600;700;800',
                'source'  => 'Linear, Notion, Figma',
            ],
            'geist' => [
                'name'    => 'Geist',
                'heading' => "'Geist Sans', sans-serif",
                'body'    => "'Geist Sans', sans-serif",
                'google'  => null, // Self-hosted from Vercel CDN
                'cdn'     => 'https://cdn.jsdelivr.net/npm/geist@1/dist/fonts/geist-sans/style.css',
                'source'  => 'Vercel',
            ],
            'plex' => [
                'name'    => 'IBM Plex',
                'heading' => "'IBM Plex Sans', sans-serif",
                'body'    => "'IBM Plex Sans', sans-serif",
                'google'  => 'IBM+Plex+Sans:wght@400;500;600;700',
                'source'  => 'IBM',
            ],
            'dm-sans' => [
                'name'    => 'DM Sans',
                'heading' => "'DM Sans', sans-serif",
                'body'    => "'DM Sans', sans-serif",
                'google'  => 'DM+Sans:wght@400;500;600;700',
                'source'  => 'Google Design',
            ],
            'editorial' => [
                'name'    => 'Editorial',
                'heading' => "'Playfair Display', serif",
                'body'    => "'Source Serif 4', serif",
                'google'  => 'Playfair+Display:wght@400;600;700;800&family=Source+Serif+4:wght@400;500;600',
                'source'  => 'NYT / Intercom pattern',
            ],
            'magazine' => [
                'name'    => 'Magazine',
                'heading' => "'Fraunces', serif",
                'body'    => "'Libre Franklin', sans-serif",
                'google'  => 'Fraunces:wght@400;600;700;800&family=Libre+Franklin:wght@400;500;600',
                'source'  => 'Premium editorial',
            ],
            'humanist' => [
                'name'    => 'Humanist',
                'heading' => "'Merriweather', serif",
                'body'    => "'Source Sans 3', sans-serif",
                'google'  => 'Merriweather:wght@400;700;900&family=Source+Sans+3:wght@400;500;600',
                'source'  => 'Publishing, accessible',
            ],
        ];
    }

    public static function googleFontsUrl(string $pairKey): ?string
    {
        $pair = self::pairs()[$pairKey] ?? null;
        if (! $pair || ! ($pair['google'] ?? null)) {
            return null;
        }
        return 'https://fonts.googleapis.com/css2?family=' . $pair['google'] . '&display=swap';
    }
}
```

- [ ] **Step 4: Create Settings.php — the central accessor**

```php
<?php
// app/Settings/Settings.php
namespace Brndle\Settings;

class Settings
{
    private static ?array $cache = null;
    public const OPTION_KEY = 'brndle_settings';

    public static function all(): array
    {
        if (self::$cache === null) {
            $saved = get_option(self::OPTION_KEY, []);
            self::$cache = array_merge(Defaults::all(), is_array($saved) ? $saved : []);
        }
        return self::$cache;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::all();
        return $all[$key] ?? $default ?? Defaults::all()[$key] ?? null;
    }

    public static function save(array $settings): bool
    {
        // Merge with existing, only save non-default values
        $current = get_option(self::OPTION_KEY, []);
        $merged = array_merge(is_array($current) ? $current : [], $settings);
        self::$cache = null; // Clear cache
        return update_option(self::OPTION_KEY, $merged);
    }

    public static function reset(): bool
    {
        self::$cache = null;
        return delete_option(self::OPTION_KEY);
    }

    /**
     * Get the resolved color palette (preset or custom).
     */
    public static function colorPalette(): array
    {
        $customAccent = self::get('custom_accent');
        if ($customAccent && preg_match('/^#[0-9a-fA-F]{6}$/', $customAccent)) {
            return ColorPalette::generate($customAccent);
        }

        $scheme = self::get('color_scheme', 'sapphire');
        $presets = ColorPalette::presets();
        $accent = $presets[$scheme]['accent'] ?? '#0070F3';

        return ColorPalette::generate($accent);
    }

    /**
     * Get the resolved font pair.
     */
    public static function fontPair(): array
    {
        $key = self::get('font_pair', 'inter');
        return FontPairs::pairs()[$key] ?? FontPairs::pairs()['inter'];
    }

    /**
     * Generate inline CSS custom properties for <head>.
     */
    public static function cssVariables(): string
    {
        $palette = self::colorPalette();
        $fonts = self::fontPair();
        $mode = self::get('dark_mode_default', 'light');

        $lightVars = $palette['light'];
        $darkVars = $palette['dark'];

        $css = ":root {\n";
        $css .= "  --color-accent: {$palette['accent']};\n";
        $css .= "  --color-accent-hover: {$palette['accent-hover']};\n";
        $css .= "  --color-accent-light: {$palette['accent-light']};\n";
        $css .= "  --color-accent-subtle: {$palette['accent-subtle']};\n";

        // Light mode surfaces (default)
        foreach ($lightVars as $key => $value) {
            $css .= "  --color-{$key}: {$value};\n";
        }

        // Typography
        $css .= "  --font-family-heading: {$fonts['heading']};\n";
        $css .= "  --font-family-body: {$fonts['body']};\n";
        $css .= "  --font-size-base: " . self::get('font_size_base', 16) . "px;\n";

        // Semantic colors
        $css .= "  --color-success: {$palette['success']};\n";
        $css .= "  --color-warning: {$palette['warning']};\n";
        $css .= "  --color-error: {$palette['error']};\n";
        $css .= "  --color-info: {$palette['info']};\n";
        $css .= "}\n\n";

        // Dark mode overrides
        $css .= "[data-theme=\"dark\"] {\n";
        $css .= "  --color-accent: {$palette['accent-light']};\n";
        foreach ($darkVars as $key => $value) {
            $css .= "  --color-{$key}: {$value};\n";
        }
        $css .= "}\n\n";

        // System preference fallback
        $css .= "@media (prefers-color-scheme: dark) {\n";
        $css .= "  [data-theme=\"system\"] {\n";
        foreach ($darkVars as $key => $value) {
            $css .= "    --color-{$key}: {$value};\n";
        }
        $css .= "    --color-accent: {$palette['accent-light']};\n";
        $css .= "  }\n";
        $css .= "}\n";

        return $css;
    }
}
```

- [ ] **Step 5: Commit**

```bash
git add app/Settings/
git commit -m "feat: add Settings foundation — Defaults, ColorPalette, FontPairs, Settings accessor"
```

---

## Task 2: Settings Service Provider + REST API

**Files:**
- Create: `app/Providers/SettingsServiceProvider.php`
- Modify: `app/Providers/ThemeServiceProvider.php`

- [ ] **Step 1: Create SettingsServiceProvider.php**

Registers:
- Admin menu page under Appearance
- REST API endpoint `brndle/v1/settings` (GET/POST)
- Inline CSS output in `wp_head`
- Google Fonts preload link
- Admin scripts enqueue

```php
<?php
// app/Providers/SettingsServiceProvider.php
namespace Brndle\Providers;

use Brndle\Settings\FontPairs;
use Brndle\Settings\Settings;

class SettingsServiceProvider
{
    public function boot(): void
    {
        add_action('admin_menu', [$this, 'registerAdminPage']);
        add_action('rest_api_init', [$this, 'registerRestRoutes']);
        add_action('wp_head', [$this, 'outputCssVariables'], 1);
        add_action('wp_head', [$this, 'outputFontPreload'], 2);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
    }

    public function registerAdminPage(): void
    {
        add_theme_page(
            __('Brndle Settings', 'brndle'),
            __('Brndle', 'brndle'),
            'manage_options',
            'brndle-settings',
            [$this, 'renderAdminPage']
        );
    }

    public function renderAdminPage(): void
    {
        echo '<div id="brndle-settings-root"></div>';
    }

    public function registerRestRoutes(): void
    {
        register_rest_route('brndle/v1', '/settings', [
            [
                'methods'  => 'GET',
                'callback' => function () {
                    return rest_ensure_response(Settings::all());
                },
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
            ],
            [
                'methods'  => 'POST',
                'callback' => function (\WP_REST_Request $request) {
                    $settings = $request->get_json_params();
                    Settings::save($settings);
                    return rest_ensure_response(['success' => true, 'settings' => Settings::all()]);
                },
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
            ],
        ]);
    }

    public function outputCssVariables(): void
    {
        echo '<style id="brndle-css-vars">' . Settings::cssVariables() . '</style>';
    }

    public function outputFontPreload(): void
    {
        $pair = Settings::get('font_pair', 'inter');
        $url = FontPairs::googleFontsUrl($pair);
        if ($url) {
            echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
            echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
            echo '<link rel="stylesheet" href="' . esc_url($url) . '">';
        }

        // CDN fonts (like Geist)
        $pairData = FontPairs::pairs()[$pair] ?? null;
        if ($pairData && isset($pairData['cdn'])) {
            echo '<link rel="stylesheet" href="' . esc_url($pairData['cdn']) . '">';
        }
    }

    public function enqueueAdminAssets(string $hook): void
    {
        if ($hook !== 'appearance_page_brndle-settings') {
            return;
        }

        $asset = get_theme_file_path('admin/build/index.asset.php');
        if (! file_exists($asset)) {
            return;
        }

        $meta = require $asset;

        wp_enqueue_script(
            'brndle-admin',
            get_theme_file_uri('admin/build/index.js'),
            $meta['dependencies'] ?? [],
            $meta['version'] ?? false,
            true
        );

        wp_enqueue_style(
            'brndle-admin',
            get_theme_file_uri('admin/build/index.css'),
            ['wp-components'],
            $meta['version'] ?? false
        );

        wp_localize_script('brndle-admin', 'brndleAdmin', [
            'restUrl' => rest_url('brndle/v1/settings'),
            'nonce'   => wp_create_nonce('wp_rest'),
        ]);
    }
}
```

- [ ] **Step 2: Register in ThemeServiceProvider**

```php
// app/Providers/ThemeServiceProvider.php — modify boot()
public function boot(): void
{
    parent::boot();
    $this->app->make(BlockServiceProvider::class)->boot();
    $this->app->make(SettingsServiceProvider::class)->boot();
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Providers/SettingsServiceProvider.php app/Providers/ThemeServiceProvider.php
git commit -m "feat: add SettingsServiceProvider with REST API, admin page, CSS var output"
```

---

## Task 3: Theme View Composer + Layout Integration

**Files:**
- Create: `app/View/Composers/Theme.php`
- Modify: `app/View/Composers/App.php`
- Modify: `resources/views/layouts/app.blade.php`
- Modify: `resources/views/layouts/landing.blade.php`

- [ ] **Step 1: Create Theme.php composer — inject settings into all views**

```php
<?php
// app/View/Composers/Theme.php
namespace Brndle\View\Composers;

use Brndle\Settings\Settings;
use Roots\Acorn\View\Composer;

class Theme extends Composer
{
    protected static $views = ['*'];

    public function darkModeDefault(): string
    {
        return Settings::get('dark_mode_default', 'light');
    }

    public function showDarkModeToggle(): bool
    {
        return (bool) Settings::get('dark_mode_toggle', true);
    }

    public function darkModeTogglePosition(): string
    {
        return Settings::get('dark_mode_toggle_position', 'bottom-right');
    }

    public function headerStyle(): string
    {
        return Settings::get('header_style', 'sticky');
    }

    public function headerCtaText(): string
    {
        return Settings::get('header_cta_text', '');
    }

    public function headerCtaUrl(): string
    {
        return Settings::get('header_cta_url', '');
    }

    public function footerStyle(): string
    {
        return Settings::get('footer_style', 'dark');
    }

    public function footerColumns(): int
    {
        return (int) Settings::get('footer_columns', 3);
    }

    public function footerCopyright(): string
    {
        $custom = Settings::get('footer_copyright', '');
        return $custom ?: sprintf('%s %s. %s',
            '©' . date('Y'),
            get_bloginfo('name'),
            __('All rights reserved.', 'brndle')
        );
    }

    public function archiveLayout(): string
    {
        return Settings::get('archive_layout', 'grid');
    }

    public function singleLayout(): string
    {
        return Settings::get('single_layout', 'standard');
    }

    public function singleShowProgressBar(): bool
    {
        return (bool) Settings::get('single_show_progress_bar', true);
    }

    public function singleShowReadingTime(): bool
    {
        return (bool) Settings::get('single_show_reading_time', true);
    }

    public function singleShowAuthorBox(): bool
    {
        return (bool) Settings::get('single_show_author_box', true);
    }

    public function singleShowSocialShare(): bool
    {
        return (bool) Settings::get('single_show_social_share', true);
    }

    public function singleShowRelatedPosts(): bool
    {
        return (bool) Settings::get('single_show_related_posts', true);
    }

    public function singleShowToc(): bool
    {
        return (bool) Settings::get('single_show_toc', false);
    }

    public function singleShowPostNav(): bool
    {
        return (bool) Settings::get('single_show_post_nav', true);
    }

    public function socialLinks(): array
    {
        return Settings::get('social_links', []);
    }
}
```

- [ ] **Step 2: Update App.php — add dark logo support**

Add `siteLogoDark()` method for dark mode logo variant.

- [ ] **Step 3: Update layouts/app.blade.php — add data-theme, font family, inline vars**

Key changes:
- Add `data-theme="{{ $darkModeDefault }}"` to `<html>`
- Add dark mode toggle script (tiny inline, localStorage-based)
- Use `font-family: var(--font-family-body)` on body
- Include dark mode toggle component if enabled

- [ ] **Step 4: Update layouts/landing.blade.php — same dark mode support**

- [ ] **Step 5: Commit**

```bash
git add app/View/Composers/Theme.php app/View/Composers/App.php resources/views/layouts/
git commit -m "feat: add Theme composer, dark mode data-theme, font system to layouts"
```

---

## Task 4: CSS Dark Mode Layer

**Files:**
- Modify: `resources/css/app.css`

- [ ] **Step 1: Replace hardcoded @theme with CSS variable references**

The `@theme` block in app.css sets Tailwind defaults. The `<style id="brndle-css-vars">` in `<head>` overrides them at runtime. Add dark mode CSS layer:

```css
/* Dark mode transitions */
html { transition: background-color 0.3s ease, color 0.3s ease; }

/* Prose dark mode */
[data-theme="dark"] .prose { --tw-prose-body: var(--color-text-secondary); }
[data-theme="dark"] .prose { --tw-prose-headings: var(--color-text-primary); }
[data-theme="dark"] .prose a { color: var(--color-accent); }
```

- [ ] **Step 2: Commit**

```bash
git add resources/css/app.css
git commit -m "feat: add dark mode CSS layer with prose overrides"
```

---

## Task 5: Blog Archive Layouts (5)

**Files:**
- Create: `resources/views/partials/archive/grid.blade.php`
- Create: `resources/views/partials/archive/list.blade.php`
- Create: `resources/views/partials/archive/magazine.blade.php`
- Create: `resources/views/partials/archive/editorial.blade.php`
- Create: `resources/views/partials/archive/minimal.blade.php`
- Modify: `resources/views/index.blade.php`

- [ ] **Step 1: Refactor existing content.blade.php into archive/grid.blade.php**

Move existing card grid markup into `archive/grid.blade.php`.

- [ ] **Step 2: Create list.blade.php — single column, image left + text right**

Based on Notion/Intercom pattern: large horizontal card, thumbnail left (aspect-[16/9] w-72), title + excerpt + meta right.

- [ ] **Step 3: Create magazine.blade.php — hero featured post + grid below**

Based on Linear/Apple: first post gets full-width hero treatment with large image, remaining posts in 2-3 col grid below. Uses `$loop->first` to detect featured.

- [ ] **Step 4: Create editorial.blade.php — text-only chronological**

Based on Vercel blog: no images, clean list with title, date, category tag, brief description. Dividers between entries.

- [ ] **Step 5: Create minimal.blade.php — clean typography, author-forward**

Based on Medium: author avatar + name prominent, clean serif-like rendering, generous line spacing, subtle dividers.

- [ ] **Step 6: Update index.blade.php — dynamic layout include**

```blade
@extends('layouts.app')
@section('content')
  <div class="max-w-7xl mx-auto px-6 py-16">
    @include('partials.page-header')

    @if(! have_posts())
      <div class="py-12 text-center">
        <p class="text-lg text-text-secondary">{{ __('No posts found.', 'brndle') }}</p>
      </div>
    @else
      @include('partials.archive.' . $archiveLayout)
    @endif
  </div>
@endsection
```

- [ ] **Step 7: Commit**

```bash
git add resources/views/partials/archive/ resources/views/index.blade.php
git commit -m "feat: add 5 blog archive layouts — grid, list, magazine, editorial, minimal"
```

---

## Task 6: Single Post Layouts (8)

**Files:**
- Create: `resources/views/partials/single/standard.blade.php`
- Create: `resources/views/partials/single/hero-immersive.blade.php`
- Create: `resources/views/partials/single/sidebar.blade.php`
- Create: `resources/views/partials/single/editorial.blade.php`
- Create: `resources/views/partials/single/cinematic.blade.php`
- Create: `resources/views/partials/single/presentation.blade.php`
- Create: `resources/views/partials/single/split.blade.php`
- Create: `resources/views/partials/single/minimal-dark.blade.php`
- Modify: `resources/views/single.blade.php`

- [ ] **Step 1: Refactor content-single.blade.php into single/standard.blade.php**

Move existing markup. All single layouts use `max-w-[700px]` content column (research-backed: Medium, Stripe, Linear, Vercel all use 700px).

- [ ] **Step 2: Create hero-immersive.blade.php**

Based on Apple Newsroom: full-viewport featured image, gradient overlay bottom→top, title/meta overlaid at bottom, `text-wrap: balance` on h1. Content scrolls up over the hero. `position: sticky` hero with content having `position: relative; z-index: 1; bg-surface-primary`.

- [ ] **Step 3: Create sidebar.blade.php**

Based on Intercom: 2-column grid on desktop. Left: sticky TOC (auto-generated from h2/h3 headings via JS). Right: content column. Reading progress bar at top. Falls back to single column + collapsible TOC on mobile.

- [ ] **Step 4: Create editorial.blade.php**

Based on NYT long reads: first letter `::first-letter` styled as drop cap (3-line float). Pull quotes styled with large serif font + left border accent. Images break out of 700px column to 1000px width with `@screen lg { margin-left: -150px; margin-right: -150px; }`. Serif heading font from font pair.

- [ ] **Step 5: Create cinematic.blade.php**

Based on Figma blog: 21:9 aspect-ratio hero (`aspect-[21/9]`). Full-bleed images between text sections (viewport width). Alternating subtle bg colors per section (surface-primary / surface-secondary). Generous `py-section` spacing.

- [ ] **Step 6: Create presentation.blade.php**

Based on Linear: JS-free approach using CSS `scroll-snap-type: y mandatory` on parent, each `<section>` is `min-h-screen scroll-snap-align: start`. Each h2 heading starts a new visual section with its own background treatment. Numbered section indicators on the side.

- [ ] **Step 7: Create split.blade.php**

Based on Stripe: `grid grid-cols-2` on desktop. Left column: `position: sticky; top: 0; height: 100vh` containing title, meta, category, author. Right column: scrollable content. `@media (max-width: 1024px)` falls back to standard single-column. Light accent color bar between columns.

- [ ] **Step 8: Create minimal-dark.blade.php**

Based on Mercury/Linear: forces `data-theme="dark"` on the article wrapper. Light text on dark surface-primary. Monospace font accents for dates/meta. Zero decorative elements. Extra generous whitespace. `max-w-[640px]` content column (slightly narrower). Code blocks with subtle border instead of bg contrast.

- [ ] **Step 9: Update single.blade.php — dynamic layout include**

```blade
@extends('layouts.app')
@section('content')
  @while(have_posts()) @php(the_post())
    @include('partials.single.' . $singleLayout)
  @endwhile
@endsection
```

- [ ] **Step 10: Commit**

```bash
git add resources/views/partials/single/ resources/views/single.blade.php
git commit -m "feat: add 8 single post layouts — standard through minimal-dark"
```

---

## Task 7: Shared Components

**Files:**
- Create: `resources/views/partials/components/reading-progress.blade.php`
- Create: `resources/views/partials/components/table-of-contents.blade.php`
- Create: `resources/views/partials/components/author-box.blade.php`
- Create: `resources/views/partials/components/related-posts.blade.php`
- Create: `resources/views/partials/components/social-share.blade.php`
- Create: `resources/views/partials/components/post-navigation.blade.php`
- Create: `resources/views/partials/components/dark-mode-toggle.blade.php`

- [ ] **Step 1: reading-progress.blade.php** — thin bar at top of viewport, CSS `scroll()` timeline if supported, tiny inline JS fallback. Accent-colored.

- [ ] **Step 2: table-of-contents.blade.php** — scans content for h2/h3 headings. Renders as sticky nav list with active state highlighting. CSS-only scroll-spy using `IntersectionObserver` in a small inline script.

- [ ] **Step 3: author-box.blade.php** — refactor from content-single. Avatar, name, bio, link. Reusable across layouts.

- [ ] **Step 4: related-posts.blade.php** — query 3 posts from same category, excluding current. Grid of small cards.

- [ ] **Step 5: social-share.blade.php** — share links for X/Twitter, LinkedIn, Facebook. Native share URLs (no JS SDK). Copy link button with clipboard API.

- [ ] **Step 6: post-navigation.blade.php** — prev/next post links with title preview and arrow.

- [ ] **Step 7: dark-mode-toggle.blade.php** — floating button at configured position. Sun/moon icon. Toggles `data-theme` on `<html>`. Saves to `localStorage`. Tiny inline script (~15 lines).

- [ ] **Step 8: Commit**

```bash
git add resources/views/partials/components/
git commit -m "feat: add shared components — progress bar, TOC, author, related, share, nav, dark toggle"
```

---

## Task 8: Header + Footer Settings Integration

**Files:**
- Modify: `resources/views/sections/header.blade.php`
- Modify: `resources/views/sections/footer.blade.php`

- [ ] **Step 1: Update header.blade.php** — Settings-driven header style (transparent/solid/sticky), CTA button, dark mode toggle in header option, dark/light logo switching based on `data-theme`.

- [ ] **Step 2: Update footer.blade.php** — Settings-driven column count (2/3/4), copyright from settings, social icon links, dark/light style.

- [ ] **Step 3: Commit**

```bash
git add resources/views/sections/
git commit -m "feat: settings-driven header (3 styles + CTA) and footer (columns + socials)"
```

---

## Task 9: Update All Block Render Templates for Dark Mode

**Files:**
- Modify: `blocks/hero/render.blade.php`
- Modify: `blocks/features/render.blade.php`
- Modify: `blocks/pricing/render.blade.php`
- Modify: `blocks/testimonials/render.blade.php`
- Modify: `blocks/cta/render.blade.php`
- Modify: `blocks/faq/render.blade.php`
- Modify: `blocks/logos/render.blade.php`
- Modify: `blocks/stats/render.blade.php`

- [ ] **Step 1: Replace all hardcoded color values with CSS variable references**

Current: `bg-[#080B16]`, `text-slate-400`, `bg-white`, `border-slate-200`
Replace with: `bg-surface-inverse`, `text-text-secondary`, `bg-surface-primary`, `border-surface-tertiary`

Blocks should NOT contain any hardcoded hex values. All colors through CSS custom properties so dark mode and color schemes cascade automatically.

- [ ] **Step 2: Commit**

```bash
git add blocks/
git commit -m "refactor: replace hardcoded colors in all blocks with CSS variable references"
```

---

## Task 10: React Admin Panel

**Files:**
- Create: `admin/src/index.js`
- Create: `admin/src/App.jsx`
- Create: `admin/src/api.js`
- Create: `admin/src/components/TabNav.jsx`
- Create: `admin/src/components/ColorSchemeSelector.jsx`
- Create: `admin/src/components/FontPairSelector.jsx`
- Create: `admin/src/components/LayoutSelector.jsx`
- Create: `admin/src/components/ToggleRow.jsx`
- Create: `admin/src/tabs/SiteIdentity.jsx`
- Create: `admin/src/tabs/Colors.jsx`
- Create: `admin/src/tabs/DarkMode.jsx`
- Create: `admin/src/tabs/Typography.jsx`
- Create: `admin/src/tabs/Header.jsx`
- Create: `admin/src/tabs/Footer.jsx`
- Create: `admin/src/tabs/BlogArchive.jsx`
- Create: `admin/src/tabs/SinglePost.jsx`
- Create: `admin/src/tabs/Performance.jsx`
- Create: `admin/admin.css`
- Modify: `package.json` (add @wordpress/scripts)

- [ ] **Step 1: Set up build tooling**

Add to package.json:
```json
"scripts": {
  "admin:build": "wp-scripts build --webpack-src-dir=admin/src --output-path=admin/build",
  "admin:start": "wp-scripts start --webpack-src-dir=admin/src --output-path=admin/build"
}
```

devDependencies: `@wordpress/scripts`

- [ ] **Step 2: Create api.js — REST helper**

```js
const { restUrl, nonce } = window.brndleAdmin;

export async function fetchSettings() {
  const res = await fetch(restUrl, { headers: { 'X-WP-Nonce': nonce } });
  return res.json();
}

export async function saveSettings(settings) {
  const res = await fetch(restUrl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
    body: JSON.stringify(settings),
  });
  return res.json();
}
```

- [ ] **Step 3: Create App.jsx — main shell with 9 tabs**

Uses `@wordpress/components` TabPanel. Loads settings on mount. Save button with Spinner feedback. Toast notification on save.

- [ ] **Step 4: Create ColorSchemeSelector.jsx**

12 preset mini-mockup cards in a 3×4 grid. Each shows a tiny page preview (header bar + content block + CTA) in that scheme's colors. Selected state with accent border. "Use custom color" checkbox reveals ColorPicker from `@wordpress/components`. Live preview swatch bar showing generated palette.

- [ ] **Step 5: Create FontPairSelector.jsx**

8 cards showing heading + body text sample in each font pair. Pair name and source attribution (e.g., "Used by Linear, Notion"). Selected state with accent border. Live preview of current selection with sample heading + paragraph.

- [ ] **Step 6: Create LayoutSelector.jsx**

Reusable component that accepts layout options array with SVG thumbnails. Shows visual grid of layout cards. Used by both BlogArchive and SinglePost tabs. Selected state with accent border + checkmark.

- [ ] **Step 7: Create all 9 tab components**

Each tab: form fields using `@wordpress/components` (TextControl, ToggleControl, SelectControl, RangeControl, ColorPicker, MediaUpload). ToggleRow as reusable wrapper: label + toggle + description text.

- [ ] **Step 8: Create admin.css — panel styling**

Minimal custom CSS for the settings page. Most styling comes from `@wordpress/components`. Custom grid layouts for scheme/font/layout selectors.

- [ ] **Step 9: Build and test**

```bash
npm run admin:build
```

- [ ] **Step 10: Commit**

```bash
git add admin/ package.json
git commit -m "feat: add React admin panel with 9 tabs — colors, typography, layouts, dark mode"
```

---

## Task 11: Integration Testing + Final Polish

**Files:**
- Various modifications for edge cases

- [ ] **Step 1: Test color scheme switching** — change scheme in admin, verify CSS variables update on frontend across all page types (archive, single, landing, 404).

- [ ] **Step 2: Test dark mode** — verify toggle works, persists across page loads, respects system preference, all layouts render correctly in both modes.

- [ ] **Step 3: Test font pair switching** — verify Google Fonts loads correctly, heading and body fonts apply, editor styles match frontend.

- [ ] **Step 4: Test all 5 archive layouts** — switch between each, verify rendering, responsive behavior, dark mode compatibility.

- [ ] **Step 5: Test all 8 single post layouts** — switch between each, verify reading progress, TOC, author box, related posts, social share toggles.

- [ ] **Step 6: Test block renders** — verify all 8 landing page blocks render correctly with CSS variables (no hardcoded colors remaining).

- [ ] **Step 7: Lighthouse audit** — run on landing page template, verify 95+ on all 4 categories.

- [ ] **Step 8: Commit**

```bash
git add -A
git commit -m "test: integration verification across all layouts, modes, and color schemes"
```

---

## Audit Fixes (Critical — Must Apply)

These fixes come from architecture audit + developer perspective review.

### Fix 1: REST API Sanitization (Security)
**In Task 2, SettingsServiceProvider POST callback:**
- Allowlist keys against `Defaults::all()` keys
- Apply type-specific sanitization: `sanitize_hex_color()` for colors, `absint()` for ints, `esc_url_raw()` for URLs, `sanitize_text_field()` for strings, `wp_kses_post()` for HTML fields (footer_copyright)
- Reject unknown keys silently

### Fix 2: Dark Mode Flash Prevention (UX)
**In Task 3, layouts/app.blade.php and landing.blade.php:**
- Add blocking inline `<script>` BEFORE any CSS/stylesheet in `<head>`:
```html
<script>(function(){var t=localStorage.getItem('brndle-theme');if(t==='dark'||t==='light'){document.documentElement.setAttribute('data-theme',t)}else{document.documentElement.setAttribute('data-theme',window.matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light')}})()</script>
```
- This MUST be the first thing in `<head>` after `<meta charset>`

### Fix 3: Font Variable Connection to Tailwind (Critical)
**In Task 1, Settings::cssVariables():**
- Must output `--font-family-sans: {body font}` to override Tailwind's `font-sans`
- Add `--font-family-heading: {heading font}` for heading utility class
- In `app.css` @theme block, reference: `--font-family-sans: var(--font-family-sans);`

### Fix 4: Layout Allowlist Validation (Security)
**In Task 5 and 6, index.blade.php and single.blade.php:**
```blade
@php
  $allowedArchive = ['grid', 'list', 'magazine', 'editorial', 'minimal'];
  $layout = in_array($archiveLayout, $allowedArchive) ? $archiveLayout : 'grid';
@endphp
@include('partials.archive.' . $layout)
```
Same pattern for single layouts.

### Fix 5: Color Contrast Enforcement (Accessibility)
**In Task 1, ColorPalette.php:**
- Add `ensureContrast(fg, bg, minRatio = 4.5)` method
- After generating accent color for light mode, check contrast against `#fafafa`
- If below 4.5:1, darken accent until it passes
- After generating accent-light for dark mode, check against dark surface-primary
- If below 4.5:1, lighten until it passes

### Fix 6: Dark Accent Hover Fix (Color Edge Case)
**In Task 1, ColorPalette::generate():**
```php
'accent-hover' => $hsl[2] < 20
    ? self::hslToHex($hsl[0], $hsl[1], min(100, $hsl[2] + 15))
    : self::hslToHex($hsl[0], $hsl[1], max(0, $hsl[2] - 12)),
```
And for near-gray colors (saturation < 5), force hue to 220 for surface tinting.

### Fix 7: Pagination in Archive Layouts
**In Task 5, index.blade.php — add AFTER the layout include:**
```blade
<div class="mt-16">
  {!! get_the_posts_pagination(['mid_size' => 2, 'prev_text' => __('Previous', 'brndle'), 'next_text' => __('Next', 'brndle')]) !!}
</div>
```

### Fix 8: Posts Per Page Hook
**In Task 2, SettingsServiceProvider::boot():**
```php
add_action('pre_get_posts', function ($query) {
    if (!is_admin() && $query->is_main_query() && ($query->is_home() || $query->is_archive())) {
        $query->set('posts_per_page', Settings::get('archive_posts_per_page', 12));
    }
});
```

### Fix 9: API Error Handling
**In Task 10, api.js:**
```js
export async function saveSettings(settings) {
  const res = await fetch(restUrl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
    body: JSON.stringify(settings),
  });
  if (!res.ok) {
    if (res.status === 403) throw new Error('Session expired. Please refresh the page.');
    throw new Error('Save failed: ' + res.statusText);
  }
  return res.json();
}
```
Add dirty-state tracking + `beforeunload` warning in App.jsx.

### Fix 10: Featured Image Fallbacks
**In Task 5, create shared component:**
```blade
{{-- partials/components/post-thumbnail.blade.php --}}
@if(has_post_thumbnail())
  {!! get_the_post_thumbnail(get_the_ID(), $size ?? 'brndle-card', [
    'class' => $class ?? 'w-full h-full object-cover',
    'loading' => $loading ?? 'lazy',
    'decoding' => 'async',
  ]) !!}
@else
  <div class="w-full h-full bg-gradient-to-br from-surface-tertiary to-surface-secondary flex items-center justify-center">
    @if($category = get_the_category())
      <span class="text-lg font-bold text-accent/30">{{ $category[0]->name }}</span>
    @endif
  </div>
@endif
```
All archive layouts must use this component instead of inline `@if(has_post_thumbnail())`.

### Fix 11: Block Editor CSS Variables
**In Task 2, SettingsServiceProvider::boot():**
```php
add_filter('block_editor_settings_all', function ($settings) {
    $settings['styles'][] = ['css' => Settings::cssVariables()];
    return $settings;
}, 20);
```
Editor will see the same colors as the frontend.

### Fix 12: Settings Save Cache Fix
**In Task 1, Settings::save():**
```php
public static function save(array $settings): bool
{
    $current = get_option(self::OPTION_KEY, []);
    $merged = array_merge(is_array($current) ? $current : [], $settings);
    $result = update_option(self::OPTION_KEY, $merged);
    self::$cache = array_merge(Defaults::all(), $merged); // Set directly, don't null
    return $result;
}
```

### Fix 13: `prefers-reduced-motion` (Accessibility)
**In Task 4, app.css:**
```css
@media (prefers-reduced-motion: reduce) {
  .reveal { opacity: 1; transform: none; animation: none; }
  html { transition: none !important; }
  * { scroll-behavior: auto !important; }
}
```

### Fix 14: Dark Mode Toggle ARIA (Accessibility)
**In Task 7, dark-mode-toggle.blade.php:**
```html
<button aria-label="{{ __('Toggle dark mode', 'brndle') }}" aria-pressed="false" role="switch">
```
Inline JS must update `aria-pressed` on toggle.

---

## Task 12: Developer Experience Layer

**Files:**
- Create: `app/Settings/Hooks.php`
- Create: `app/Settings/Sanitizer.php`
- Create: `.github/workflows/main.yml`
- Create: `pint.json`
- Modify: `app/Settings/Settings.php` (add filter hooks)
- Modify: `app/Settings/ColorPalette.php` (add filter hooks)
- Modify: `app/Settings/FontPairs.php` (add filter hooks)

This task adds everything a developer extending Brndle expects.

- [ ] **Step 1: Add filter hooks throughout the settings system**

Every major data point must be filterable so child themes and plugins can extend:

```php
// In Settings::all()
$settings = apply_filters('brndle/settings', $settings);

// In ColorPalette::presets()
return apply_filters('brndle/color_presets', $presets);

// In ColorPalette::generate()
return apply_filters('brndle/color_palette', $palette, $hex);

// In FontPairs::pairs()
return apply_filters('brndle/font_pairs', $pairs);

// In Settings::cssVariables()
$css = apply_filters('brndle/css_variables', $css);

// In Theme composer — layout resolution
$layout = apply_filters('brndle/archive_layout', $layout);
$layout = apply_filters('brndle/single_layout', $layout, get_the_ID());
```

This lets developers:
- Add custom color presets: `add_filter('brndle/color_presets', fn($p) => array_merge($p, ['brand' => [...]]))`
- Add custom font pairs: `add_filter('brndle/font_pairs', fn($p) => array_merge($p, ['custom' => [...]]))`
- Override layout per-post: `add_filter('brndle/single_layout', fn($l, $id) => get_post_meta($id, '_brndle_layout', true) ?: $l, 10, 2)`
- Inject custom CSS variables: `add_filter('brndle/css_variables', fn($css) => $css . ':root { --custom: red; }')`

- [ ] **Step 2: Create Sanitizer.php — centralized input sanitization**

```php
<?php
namespace Brndle\Settings;

class Sanitizer
{
    public static function sanitize(string $key, mixed $value): mixed
    {
        return match (true) {
            str_contains($key, 'color') || str_contains($key, 'accent')
                => sanitize_hex_color($value) ?: '',
            str_contains($key, 'url')
                => esc_url_raw($value),
            str_contains($key, 'copyright')
                => wp_kses_post($value),
            is_bool(Defaults::all()[$key] ?? null)
                => (bool) $value,
            is_int(Defaults::all()[$key] ?? null)
                => absint($value),
            is_array(Defaults::all()[$key] ?? null)
                => self::sanitizeArray($key, $value),
            default
                => sanitize_text_field($value),
        };
    }

    private static function sanitizeArray(string $key, mixed $value): array
    {
        if (!is_array($value)) return [];

        if ($key === 'social_links') {
            return array_map('esc_url_raw', $value);
        }

        return array_map('sanitize_text_field', $value);
    }
}
```

- [ ] **Step 3: Create CI workflow (.github/workflows/main.yml)**

```yaml
name: CI
on: [push, pull_request]
jobs:
  php:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with: { php-version: '8.2' }
      - run: composer install --no-dev
      - run: vendor/bin/pint --test

  node:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with: { node-version: '20' }
      - run: npm ci
      - run: npm run build
```

- [ ] **Step 4: Create pint.json for code style enforcement**

```json
{
    "preset": "laravel",
    "rules": {
        "ordered_imports": { "sort_algorithm": "alpha" }
    }
}
```

- [ ] **Step 5: Add settings version + migration system**

```php
// In Settings.php
private static function migrate(array $saved): array
{
    $version = $saved['_version'] ?? 0;

    // Future migrations go here:
    // if ($version < 2) { ... $saved['_version'] = 2; }

    if ($version < 1) {
        $saved['_version'] = 1;
        update_option(self::OPTION_KEY, $saved);
    }

    return $saved;
}
```

Call in `all()` before merging with defaults.

- [ ] **Step 6: Add REST export/import endpoints**

```php
// In SettingsServiceProvider
register_rest_route('brndle/v1', '/settings/export', [
    'methods'  => 'GET',
    'callback' => fn() => rest_ensure_response(Settings::all()),
    'permission_callback' => fn() => current_user_can('manage_options'),
]);

register_rest_route('brndle/v1', '/settings/import', [
    'methods'  => 'POST',
    'callback' => function ($request) {
        $data = $request->get_json_params();
        Settings::save($data);
        return rest_ensure_response(['success' => true]);
    },
    'permission_callback' => fn() => current_user_can('manage_options'),
]);

register_rest_route('brndle/v1', '/settings', [
    'methods'  => 'DELETE',
    'callback' => function () {
        Settings::reset();
        return rest_ensure_response(['success' => true, 'settings' => Settings::all()]);
    },
    'permission_callback' => fn() => current_user_can('manage_options'),
]);
```

- [ ] **Step 7: Add category filter component for archive**

Create `resources/views/partials/components/category-filter.blade.php`:
```blade
@if($archiveShowCategoryFilter ?? false)
  @php($categories = get_categories(['hide_empty' => true]))
  @if($categories)
    <nav class="flex flex-wrap gap-2 mb-8" aria-label="{{ __('Filter by category', 'brndle') }}">
      <a href="{{ get_permalink(get_option('page_for_posts')) ?: home_url('/') }}"
         class="px-4 py-1.5 text-sm font-medium rounded-full transition-colors
           {{ !is_category() ? 'bg-accent text-white' : 'bg-surface-secondary text-text-secondary hover:bg-surface-tertiary' }}">
        {{ __('All', 'brndle') }}
      </a>
      @foreach($categories as $cat)
        <a href="{{ get_category_link($cat) }}"
           class="px-4 py-1.5 text-sm font-medium rounded-full transition-colors
             {{ is_category($cat->term_id) ? 'bg-accent text-white' : 'bg-surface-secondary text-text-secondary hover:bg-surface-tertiary' }}">
          {{ $cat->name }}
        </a>
      @endforeach
    </nav>
  @endif
@endif
```

- [ ] **Step 8: Add search.blade.php layout alignment**

Update `search.blade.php` to use `list` layout (best for search results) instead of hardcoded grid.

- [ ] **Step 9: Commit**

```bash
git add app/Settings/ .github/ pint.json resources/views/partials/components/category-filter.blade.php
git commit -m "feat: add developer hooks, sanitization, CI, migrations, export/import, category filter"
```

---

## Task 13: Plugin Compatibility Layer

**Files:**
- Create: `app/Compatibility/Yoast.php`
- Create: `app/Compatibility/WooCommerce.php`
- Create: `app/Compatibility/WPML.php`

Developers expect enterprise themes to work with major plugins out of the box.

- [ ] **Step 1: Yoast/RankMath SEO compatibility**

- Add breadcrumb support via `yoast_breadcrumb()` / `rank_math_the_breadcrumbs()` in single layouts
- Add JSON-LD structured data output for blog posts (Article schema) if no SEO plugin detected
- Ensure `<title>` and meta tags don't conflict

- [ ] **Step 2: WooCommerce basic support**

- `add_theme_support('woocommerce')` in setup.php
- Add `resources/views/woocommerce.blade.php` template wrapper
- WooCommerce pages use the `app` layout, not `landing`

- [ ] **Step 3: WPML/Polylang compatibility**

- Ensure all `__()` strings use 'brndle' textdomain consistently
- Settings are per-site (correct for multilingual — each language site has its own options)
- Add `hreflang` output helper if WPML detected

- [ ] **Step 4: Commit**

```bash
git add app/Compatibility/
git commit -m "feat: add compatibility layer for Yoast, WooCommerce, WPML"
```

---

## Task 14: Starter Content + Admin Onboarding

**Files:**
- Create: `app/Onboarding/SetupNotice.php`
- Create: `app/Onboarding/StarterContent.php`

- [ ] **Step 1: Admin setup notice**

Show a dismissible notice when theme is activated:
"Welcome to Brndle! → Configure your brand settings → Import demo content"
Links to the Brndle settings page. Dismisses permanently per-user via `update_user_meta`.

- [ ] **Step 2: Starter content definition**

Register WordPress starter content (`add_theme_support('starter-content', [...])`) with:
- Homepage using Landing Page template with hero + features + CTA blocks
- Blog page
- About page
- Contact page
- Primary navigation menu
- Sample blog posts (3)

This gives new installs a non-empty starting point.

- [ ] **Step 3: Commit**

```bash
git add app/Onboarding/
git commit -m "feat: add onboarding notice and starter content for new installs"
```

---

## Developer Expectations Checklist

What developers expect from an enterprise theme that this plan now covers:

| Expectation | Status | Where |
|-------------|--------|-------|
| Filter hooks for all settings | ✅ | Task 12 Step 1 |
| Filter hooks for colors/fonts/layouts | ✅ | Task 12 Step 1 |
| Per-post layout override via filter | ✅ | `brndle/single_layout` filter |
| Input sanitization on all REST endpoints | ✅ | Task 12 Step 2 (Sanitizer.php) |
| Settings export/import | ✅ | Task 12 Step 6 |
| Settings reset to defaults | ✅ | Task 12 Step 6 (DELETE endpoint) |
| Settings versioning + migration | ✅ | Task 12 Step 5 |
| CI/CD pipeline (lint + build) | ✅ | Task 12 Step 3 |
| Code style enforcement (Pint) | ✅ | Task 12 Step 4 |
| Child theme view overrides | ✅ | Sage/Acorn ViewFinder handles this |
| WooCommerce support | ✅ | Task 13 Step 2 |
| SEO plugin compatibility | ✅ | Task 13 Step 1 |
| WPML/Polylang i18n | ✅ | Task 13 Step 3 |
| Breadcrumbs | ✅ | Task 13 Step 1 |
| JSON-LD structured data | ✅ | Task 13 Step 1 |
| Onboarding admin notice | ✅ | Task 14 Step 1 |
| Starter content | ✅ | Task 14 Step 2 |
| Category filter on archives | ✅ | Task 12 Step 7 |
| Shared post thumbnail fallback | ✅ | Audit Fix 10 |
| Reduced motion support | ✅ | Audit Fix 13 |
| ARIA labels on toggles | ✅ | Audit Fix 14 |
| WCAG AA color contrast | ✅ | Audit Fix 5 |
| `register_setting()` for migration tools | ✅ | Task 2 |
| Minified inline CSS output | ✅ | Audit note in Task 2 |

### What's Deferred to Phase 2

| Feature | Reason |
|---------|--------|
| Block editor.js (React edit UI for each block) | Blocks work server-side. Editor preview is Phase 2. |
| Multisite network-wide defaults | Requires network admin page. Phase 2. |
| Custom block creation guide/CLI | Documentation, not code. Phase 2. |
| Performance: self-hosted Google Fonts | Build step integration. Phase 2. |
| Performance: font metric overrides for CLS | Requires per-font measurement. Phase 2. |
| Admin panel mobile optimization | WP admin is desktop-primary. Phase 2. |
| Block patterns (pre-built page section combos) | Content, not code. Phase 2. |

---

## Execution Order Summary

| # | Task | Dependencies | Estimate |
|---|------|-------------|----------|
| 1 | Settings Foundation (PHP) | None | Core |
| 2 | Settings Service Provider + REST API | Task 1 | Core |
| 3 | Theme View Composer + Layout Integration | Task 1, 2 | Core |
| 4 | CSS Dark Mode Layer | Task 1 | Core |
| 5 | Blog Archive Layouts (5) | Task 3 | Feature |
| 6 | Single Post Layouts (8) | Task 3 | Feature |
| 7 | Shared Components | Task 3 | Feature |
| 8 | Header + Footer Settings | Task 3 | Feature |
| 9 | Block Render Dark Mode Update | Task 4 | Refactor |
| 10 | React Admin Panel | Task 2 | Feature |
| 11 | Integration Testing | All above | QA |
| 12 | Developer Experience Layer | Task 1, 2 | DX |
| 13 | Plugin Compatibility | Task 3 | Compat |
| 14 | Starter Content + Onboarding | Task 2 | UX |

Tasks 5-9 can run in parallel. Task 10 can start after Task 2. Tasks 12-14 can run in parallel after core tasks.
