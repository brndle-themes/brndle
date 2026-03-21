<?php

/**
 * Central settings accessor.
 *
 * Singleton entry point for reading, writing, and resolving all
 * Brndle theme settings. Handles option merging, caching, migration,
 * palette/font resolution, and CSS custom-property generation.
 */

namespace Brndle\Settings;

class Settings
{
    /**
     * WordPress option key where all settings are stored as JSON.
     */
    public const OPTION_KEY = 'brndle_settings';

    /**
     * Current settings schema version for migrations.
     */
    public const VERSION = 1;

    /**
     * In-memory cache so we hit the database at most once per request.
     *
     * @var array<string, mixed>|null
     */
    private static ?array $cache = null;

    /**
     * Retrieve all settings merged with defaults.
     *
     * Saved values take precedence over defaults. The result is cached
     * in a static property and reused for the remainder of the request.
     *
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $saved = get_option(self::OPTION_KEY, []);

        if (is_string($saved)) {
            $saved = json_decode($saved, true) ?: [];
        }

        if (! is_array($saved)) {
            $saved = [];
        }

        $saved = self::migrate($saved);

        self::$cache = array_merge(Defaults::all(), $saved);

        return self::$cache;
    }

    /**
     * Retrieve a single setting value.
     *
     * @param  string  $key      Setting key name.
     * @param  mixed   $default  Fallback when the key is not present.
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::all();

        return $all[$key] ?? $default;
    }

    /**
     * Save settings to the database.
     *
     * Incoming values are sanitized, merged with the current stored
     * values, and persisted. The in-memory cache is updated directly
     * so subsequent reads within the same request are consistent.
     *
     * @param  array<string, mixed>  $settings  Partial or full settings array.
     * @return bool  True on success.
     */
    public static function save(array $settings): bool
    {
        $sanitized = Sanitizer::sanitizeAll($settings);

        $saved = get_option(self::OPTION_KEY, []);

        if (is_string($saved)) {
            $saved = json_decode($saved, true) ?: [];
        }

        if (! is_array($saved)) {
            $saved = [];
        }

        $merged = array_merge($saved, $sanitized);
        $merged['_version'] = self::VERSION;

        $result = update_option(self::OPTION_KEY, $merged, false);

        // Update cache directly so reads within this request are fresh.
        self::$cache = array_merge(Defaults::all(), $merged);

        return $result;
    }

    /**
     * Reset all settings to defaults.
     *
     * Deletes the stored option and clears the in-memory cache.
     *
     * @return bool  True on success.
     */
    public static function reset(): bool
    {
        self::$cache = null;

        return delete_option(self::OPTION_KEY);
    }

    /**
     * Resolve the current color palette.
     *
     * Uses the custom accent hex if set, otherwise falls back to the
     * active preset's hex value.
     *
     * @return array<string, string>
     */
    public static function colorPalette(): array
    {
        $all = self::all();
        $customAccent = $all['custom_accent'] ?? '';

        if (! empty($customAccent)) {
            return ColorPalette::generate($customAccent);
        }

        $scheme = $all['color_scheme'] ?? 'sapphire';
        $presets = ColorPalette::presets();
        $preset = $presets[$scheme] ?? $presets['sapphire'];

        return ColorPalette::generate($preset['hex']);
    }

    /**
     * Resolve the current font pair configuration.
     *
     * @return array{heading: string, body: string}
     */
    public static function fontPair(): array
    {
        $pairKey = self::get('font_pair', 'inter');

        return FontPairs::resolve($pairKey);
    }

    /**
     * Generate a complete CSS custom-properties string for injection into <head>.
     *
     * Produces three blocks:
     *  1. :root { } — accent colors, light surfaces, text, fonts, semantics
     *  2. [data-theme="dark"] { } — dark surfaces + adjusted accent
     *  3. @media (prefers-color-scheme: dark) { [data-theme="system"] { } }
     *
     * Output is minified (no unnecessary whitespace).
     *
     * @return string  Minified CSS string.
     */
    public static function cssVariables(): string
    {
        $palette = self::colorPalette();
        $fonts = self::fontPair();
        $all = self::all();

        $baseFontSize = (int) ($all['font_size_base'] ?? 16);
        $headingScale = (float) ($all['heading_scale'] ?? 1.25);

        // ── Light-mode variables (default) ──────────────────
        $root = [];
        $root['--color-accent'] = $palette['accent'];
        $root['--color-accent-hover'] = $palette['accent-hover'];
        $root['--color-accent-light'] = $palette['accent-light'];
        $root['--color-accent-subtle'] = $palette['accent-subtle'];
        $root['--color-surface-primary'] = $palette['light-surface-primary'];
        $root['--color-surface-secondary'] = $palette['light-surface-secondary'];
        $root['--color-surface-tertiary'] = $palette['light-surface-tertiary'];
        $root['--color-text-primary'] = $palette['light-text-primary'];
        $root['--color-text-secondary'] = $palette['light-text-secondary'];
        $root['--color-text-tertiary'] = $palette['light-text-tertiary'];
        $root['--color-border'] = $palette['light-border'];
        $root['--color-border-hover'] = $palette['light-border-hover'];
        $root['--color-success'] = $palette['success'];
        $root['--color-warning'] = $palette['warning'];
        $root['--color-error'] = $palette['error'];
        $root['--color-info'] = $palette['info'];
        $root['--font-family-heading'] = $fonts['heading'];
        $root['--font-family-body'] = $fonts['body'];
        $root['--font-family-sans'] = $fonts['body'];
        $root['--font-size-base'] = $baseFontSize . 'px';
        $root['--heading-scale'] = (string) $headingScale;
        $root['--shadow-glow'] = '0 0 20px ' . self::hexToRgba($palette['accent'], 0.15);

        // ── Dark-mode variables ─────────────────────────────
        $dark = [];
        $dark['--color-accent'] = $palette['dark-accent'];
        $dark['--color-accent-hover'] = $palette['dark-accent-hover'];
        $dark['--color-surface-primary'] = $palette['dark-surface-primary'];
        $dark['--color-surface-secondary'] = $palette['dark-surface-secondary'];
        $dark['--color-surface-tertiary'] = $palette['dark-surface-tertiary'];
        $dark['--color-text-primary'] = $palette['dark-text-primary'];
        $dark['--color-text-secondary'] = $palette['dark-text-secondary'];
        $dark['--color-text-tertiary'] = $palette['dark-text-tertiary'];
        $dark['--color-border'] = $palette['dark-border'];
        $dark['--color-border-hover'] = $palette['dark-border-hover'];
        $dark['--shadow-glow'] = '0 0 20px ' . self::hexToRgba($palette['dark-accent'], 0.2);

        // Build minified CSS.
        $rootStr = self::buildBlock(':root', $root);
        $darkStr = self::buildBlock('[data-theme="dark"]', $dark);
        $systemStr = '@media(prefers-color-scheme:dark){' . self::buildBlock('[data-theme="system"]', $dark) . '}';

        $css = $rootStr . $darkStr . $systemStr;

        /** @var string */
        return apply_filters('brndle/css_variables', $css);
    }

    /**
     * Run version-based migrations on saved settings.
     *
     * Each migration bumps the `_version` key. Migrations run
     * sequentially from the saved version to the current VERSION.
     *
     * @param  array<string, mixed>  $saved  Raw saved settings.
     * @return array<string, mixed>          Migrated settings.
     */
    public static function migrate(array $saved): array
    {
        $version = (int) ($saved['_version'] ?? 0);

        if ($version >= self::VERSION) {
            return $saved;
        }

        // ── Migration 0 → 1: Initial schema ────────────────
        if ($version < 1) {
            $saved['_version'] = 1;

            // Future migration logic goes here.
            // Example: rename deprecated keys, convert types, etc.
        }

        // Persist migration result so it does not run again.
        if ($version !== ($saved['_version'] ?? 0)) {
            update_option(self::OPTION_KEY, $saved, false);
        }

        return $saved;
    }

    /**
     * Clear the in-memory cache.
     *
     * Useful in tests or after external option changes.
     *
     * @return void
     */
    public static function clearCache(): void
    {
        self::$cache = null;
    }

    /**
     * Build a minified CSS block from property => value pairs.
     *
     * @param  string                $selector    CSS selector.
     * @param  array<string, string> $properties  Property map.
     * @return string
     */
    private static function buildBlock(string $selector, array $properties): string
    {
        $declarations = '';

        foreach ($properties as $prop => $value) {
            $declarations .= $prop . ':' . $value . ';';
        }

        return $selector . '{' . $declarations . '}';
    }

    /**
     * Convert a hex color to an rgba() string.
     *
     * @param  string  $hex    #RRGGBB hex color.
     * @param  float   $alpha  Opacity (0–1).
     * @return string          rgba(r,g,b,a) string.
     */
    private static function hexToRgba(string $hex, float $alpha): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "rgba({$r},{$g},{$b},{$alpha})";
    }
}
