<?php

/**
 * Settings sanitizer.
 *
 * Type-aware input sanitization for every Brndle setting.
 * Each field type maps to the appropriate WordPress sanitization
 * function so values are safe before hitting the database.
 */

namespace Brndle\Settings;

class Sanitizer
{
    /**
     * Sanitize a single setting value based on its key.
     *
     * Uses the Defaults class type-key lists to determine the correct
     * sanitization strategy, then falls back to sanitize_text_field().
     *
     * @param  string  $key    Setting key name.
     * @param  mixed   $value  Raw input value.
     * @return mixed           Sanitized value.
     */
    public static function sanitize(string $key, mixed $value): mixed
    {
        if ($key === 'homepage_sections') {
            return self::sanitizeHomepageSections($value);
        }

        return match (true) {
            in_array($key, Defaults::colorKeys(), true) => self::sanitizeColor($value),
            in_array($key, Defaults::urlKeys(), true) => self::sanitizeUrl($value),
            in_array($key, Defaults::emailKeys(), true) => self::sanitizeEmail($value),
            in_array($key, Defaults::htmlKeys(), true) => self::sanitizeHtml($value),
            in_array($key, Defaults::boolKeys(), true) => self::sanitizeBool($value),
            in_array($key, Defaults::intKeys(), true) => self::sanitizeInt($value),
            in_array($key, Defaults::floatKeys(), true) => self::sanitizeFloat($value),
            in_array($key, Defaults::arrayKeys(), true) => self::sanitizeArray($value),
            default => self::sanitizeString($value),
        };
    }

    /**
     * Sanitize an entire settings array.
     *
     * @param  array<string, mixed>  $settings  Raw settings.
     * @return array<string, mixed>             Sanitized settings.
     */
    public static function sanitizeAll(array $settings): array
    {
        $sanitized = [];

        foreach ($settings as $key => $value) {
            $sanitized[$key] = self::sanitize($key, $value);
        }

        return $sanitized;
    }

    /**
     * Sanitize a hex color value.
     *
     * @param  mixed  $value
     * @return string
     */
    private static function sanitizeColor(mixed $value): string
    {
        if (! is_string($value) || $value === '') {
            return '';
        }

        $sanitized = sanitize_hex_color($value);

        return $sanitized ?? '';
    }

    /**
     * Sanitize a URL value.
     *
     * @param  mixed  $value
     * @return string
     */
    private static function sanitizeUrl(mixed $value): string
    {
        if (! is_string($value)) {
            return '';
        }

        return esc_url_raw($value);
    }

    /**
     * Sanitize an email value.
     *
     * @param  mixed  $value
     * @return string
     */
    private static function sanitizeEmail(mixed $value): string
    {
        if (! is_string($value)) {
            return '';
        }

        return sanitize_email($value);
    }

    /**
     * Sanitize an HTML content value (allows safe tags).
     *
     * @param  mixed  $value
     * @return string
     */
    private static function sanitizeHtml(mixed $value): string
    {
        if (! is_string($value)) {
            return '';
        }

        return wp_kses_post($value);
    }

    /**
     * Sanitize a boolean value.
     *
     * @param  mixed  $value
     * @return bool
     */
    private static function sanitizeBool(mixed $value): bool
    {
        return (bool) $value;
    }

    /**
     * Sanitize an integer value.
     *
     * @param  mixed  $value
     * @return int
     */
    private static function sanitizeInt(mixed $value): int
    {
        return absint($value);
    }

    /**
     * Sanitize a float value.
     *
     * @param  mixed  $value
     * @return float
     */
    private static function sanitizeFloat(mixed $value): float
    {
        return (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * Recursively sanitize an array value.
     *
     * Nested arrays are handled recursively. Scalar values within
     * the array are sanitized as text fields or URLs (for social links).
     *
     * @param  mixed  $value
     * @return array<string, mixed>
     */
    private static function sanitizeArray(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $sanitized = [];

        foreach ($value as $k => $v) {
            $k = sanitize_text_field((string) $k);

            if (is_array($v)) {
                $sanitized[$k] = self::sanitizeArray($v);
            } elseif (filter_var($v, FILTER_VALIDATE_URL)) {
                $sanitized[$k] = esc_url_raw((string) $v);
            } else {
                $sanitized[$k] = sanitize_text_field((string) $v);
            }
        }

        return $sanitized;
    }

    /**
     * Sanitize a plain text string value.
     *
     * @param  mixed  $value
     * @return string
     */
    private static function sanitizeString(mixed $value): string
    {
        if (! is_string($value)) {
            return (string) $value;
        }

        return sanitize_text_field($value);
    }

    /**
     * Sanitize the homepage_sections array.
     *
     * Each section is `{category_id:int, style:string, count:int,
     * show_title:bool, show_view_all:bool}`. Unknown styles fall back
     * to grid-3col. Counts are clamped 1..10. Order is preserved (the
     * sections render top-to-bottom).
     *
     * @param  mixed  $value
     * @return array<int, array{category_id:int,style:string,count:int,show_title:bool,show_view_all:bool}>
     */
    private static function sanitizeHomepageSections(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $allowedStyles = Defaults::homepageSectionStyles();
        $sanitized = [];

        // Hard cap at 12 sections. Each section issues its own SELECT
        // on the homepage; without this guard a misconfigured list could
        // explode into 100+ queries per pageview on a high-traffic blog.
        // Twelve is plenty for a magazine homepage and matches the
        // render-time guard in resources/views/partials/archive/sections.blade.php.
        $rows = array_slice(array_values($value), 0, 12);

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $style = (string) ($row['style'] ?? 'grid-3col');
            if (! in_array($style, $allowedStyles, true)) {
                $style = 'grid-3col';
            }

            $sanitized[] = [
                'category_id'   => absint($row['category_id'] ?? 0),
                'style'         => $style,
                'count'         => max(1, min(10, absint($row['count'] ?? 4))),
                'show_title'    => ! empty($row['show_title']),
                'show_view_all' => ! empty($row['show_view_all']),
            ];
        }

        return $sanitized;
    }
}
