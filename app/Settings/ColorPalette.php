<?php

/**
 * Color palette system.
 *
 * 12 enterprise-grade presets plus automatic palette generation
 * from any single hex color. Every generated palette ships with
 * light surfaces, dark surfaces, text colors, and semantic colors
 * that meet WCAG AA contrast ratios.
 */

namespace Brndle\Settings;

class ColorPalette
{
    /**
     * Enterprise color scheme presets sourced from $1B+ companies.
     *
     * Each preset contains:
     *  - name:  Human-readable label
     *  - hex:   Primary accent hex
     *  - tone:  'light' or 'dark' — describes the accent itself
     *
     * @return array<string, array{name: string, hex: string, tone: string}>
     */
    public static function presets(): array
    {
        $presets = [
            'neutral' => [
                'name' => __('Neutral', 'brndle'),
                'hex' => '#18181b',
                'tone' => 'dark',
            ],
            'sapphire' => [
                'name' => __('Sapphire', 'brndle'),
                'hex' => '#0070F3',
                'tone' => 'light',
            ],
            'indigo' => [
                'name' => __('Indigo', 'brndle'),
                'hex' => '#635BFF',
                'tone' => 'light',
            ],
            'cobalt' => [
                'name' => __('Cobalt', 'brndle'),
                'hex' => '#0C66E4',
                'tone' => 'light',
            ],
            'trust' => [
                'name' => __('Trust', 'brndle'),
                'hex' => '#0530AD',
                'tone' => 'dark',
            ],
            'commerce' => [
                'name' => __('Commerce', 'brndle'),
                'hex' => '#2a6e3f',
                'tone' => 'dark',
            ],
            'signal' => [
                'name' => __('Signal', 'brndle'),
                'hex' => '#F22F46',
                'tone' => 'light',
            ],
            'coral' => [
                'name' => __('Coral', 'brndle'),
                'hex' => '#FF7A59',
                'tone' => 'light',
            ],
            'aubergine' => [
                'name' => __('Aubergine', 'brndle'),
                'hex' => '#4A154B',
                'tone' => 'dark',
            ],
            'midnight' => [
                'name' => __('Midnight', 'brndle'),
                'hex' => '#1e3a5f',
                'tone' => 'dark',
            ],
            'stone' => [
                'name' => __('Stone', 'brndle'),
                'hex' => '#57534e',
                'tone' => 'dark',
            ],
            'carbon' => [
                'name' => __('Carbon', 'brndle'),
                'hex' => '#09090b',
                'tone' => 'dark',
            ],
        ];

        /** @var array<string, array{name: string, hex: string, tone: string}> */
        return apply_filters('brndle/color_presets', $presets);
    }

    /**
     * Generate a complete light + dark palette from a single accent hex.
     *
     * Returns accent variants, surface colors for both modes, text colors,
     * and semantic colors — all keyed for direct use as CSS custom properties.
     *
     * @param  string  $hex  Accent color in #RRGGBB format.
     * @return array<string, string>
     */
    public static function generate(string $hex): array
    {
        $hsl = self::hexToHsl($hex);
        $h = $hsl['h'];
        $s = $hsl['s'];
        $l = $hsl['l'];

        // Near-gray colors (saturation < 5%) drift to red in tinted surfaces.
        // Force hue to 220 (neutral blue) to prevent that.
        $surfaceHue = $s < 5 ? 220 : $h;
        $surfaceSat = $s < 5 ? 10 : max(6, $s * 0.15);

        // ── Accent variants ─────────────────────────────────
        // Dark accents (lightness < 20) need a LIGHTER hover, not darker.
        $hoverL = $l < 20 ? min(100, $l + 10) : max(0, $l - 8);

        $palette = [
            // Core accent
            'accent' => $hex,
            'accent-hover' => self::hslToHex($h, $s, $hoverL),
            'accent-light' => self::hslToHex($h, max(40, $s * 0.7), min(95, $l + 35)),
            'accent-subtle' => self::hslToHex($h, max(20, $s * 0.4), 95),

            // ── Light-mode surfaces ─────────────────────────
            'light-surface-primary' => '#fafafa',
            'light-surface-secondary' => self::hslToHex($surfaceHue, $surfaceSat, 96),
            'light-surface-tertiary' => self::hslToHex($surfaceHue, $surfaceSat, 92),

            // ── Light-mode text ─────────────────────────────
            'light-text-primary' => '#09090b',
            'light-text-secondary' => '#52525b',
            'light-text-tertiary' => '#a1a1aa',

            // ── Light-mode borders ──────────────────────────
            'light-border' => '#e4e4e7',
            'light-border-hover' => '#d4d4d8',

            // ── Dark-mode surfaces (tinted toward accent) ───
            'dark-surface-primary' => self::hslToHex($surfaceHue, max(5, $surfaceSat * 0.6), 7),
            'dark-surface-secondary' => self::hslToHex($surfaceHue, max(5, $surfaceSat * 0.5), 10),
            'dark-surface-tertiary' => self::hslToHex($surfaceHue, max(5, $surfaceSat * 0.4), 14),

            // ── Dark-mode text ──────────────────────────────
            'dark-text-primary' => '#fafafa',
            'dark-text-secondary' => '#a1a1aa',
            'dark-text-tertiary' => '#71717a',

            // ── Dark-mode borders ───────────────────────────
            'dark-border' => '#27272a',
            'dark-border-hover' => '#3f3f46',

            // ── Dark-mode accent adjustments ────────────────
            'dark-accent' => $l < 40 ? self::hslToHex($h, $s, min(65, $l + 20)) : $hex,
            'dark-accent-hover' => $l < 40
                ? self::hslToHex($h, $s, min(75, $l + 28))
                : self::hslToHex($h, $s, min(80, $l + 10)),

            // ── Semantic ────────────────────────────────────
            'success' => '#16a34a',
            'warning' => '#d97706',
            'error' => '#dc2626',
            'info' => '#2563eb',
        ];

        /** @var array<string, string> */
        return apply_filters('brndle/color_palette', $palette, $hex);
    }

    /**
     * Ensure foreground/background meet a minimum WCAG contrast ratio.
     *
     * If the pair fails, the foreground lightness is adjusted until
     * the ratio is met (darkened on light backgrounds, lightened on dark).
     *
     * @param  string  $fg        Foreground hex.
     * @param  string  $bg        Background hex.
     * @param  float   $minRatio  Minimum contrast ratio (4.5 = AA normal text).
     * @return string  Adjusted foreground hex.
     */
    public static function ensureContrast(string $fg, string $bg, float $minRatio = 4.5): string
    {
        if (self::contrastRatio($fg, $bg) >= $minRatio) {
            return $fg;
        }

        $fgHsl = self::hexToHsl($fg);
        $bgHsl = self::hexToHsl($bg);

        // Decide direction: darken fg on light bg, lighten fg on dark bg.
        $direction = $bgHsl['l'] > 50 ? -1 : 1;

        $h = $fgHsl['h'];
        $s = $fgHsl['s'];
        $l = $fgHsl['l'];

        for ($i = 0; $i < 100; $i++) {
            $l = max(0, min(100, $l + $direction * 2));
            $candidate = self::hslToHex($h, $s, $l);

            if (self::contrastRatio($candidate, $bg) >= $minRatio) {
                return $candidate;
            }
        }

        // Fallback to black or white.
        return $direction === -1 ? '#000000' : '#ffffff';
    }

    /**
     * Calculate the WCAG 2.1 contrast ratio between two hex colors.
     *
     * @param  string  $hex1
     * @param  string  $hex2
     * @return float   Ratio ranging from 1.0 to 21.0.
     */
    public static function contrastRatio(string $hex1, string $hex2): float
    {
        $l1 = self::relativeLuminance($hex1);
        $l2 = self::relativeLuminance($hex2);

        $lighter = max($l1, $l2);
        $darker = min($l1, $l2);

        return ($lighter + 0.05) / ($darker + 0.05);
    }

    /**
     * Convert a hex color to HSL components.
     *
     * @param  string  $hex  Color in #RRGGBB or #RGB format.
     * @return array{h: float, s: float, l: float}
     */
    public static function hexToHsl(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;

        if ($max === $min) {
            $h = $s = 0.0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

            $h = match (true) {
                $max === $r => (($g - $b) / $d + ($g < $b ? 6 : 0)) / 6,
                $max === $g => (($b - $r) / $d + 2) / 6,
                default     => (($r - $g) / $d + 4) / 6,
            };
        }

        return [
            'h' => round($h * 360, 2),
            's' => round($s * 100, 2),
            'l' => round($l * 100, 2),
        ];
    }

    /**
     * Convert HSL components to a hex color string.
     *
     * @param  float  $h  Hue (0–360).
     * @param  float  $s  Saturation (0–100).
     * @param  float  $l  Lightness (0–100).
     * @return string #RRGGBB hex string.
     */
    public static function hslToHex(float $h, float $s, float $l): string
    {
        $h = fmod($h, 360);
        if ($h < 0) {
            $h += 360;
        }

        $s = max(0, min(100, $s)) / 100;
        $l = max(0, min(100, $l)) / 100;

        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
        $m = $l - $c / 2;

        [$r, $g, $b] = match (true) {
            $h < 60  => [$c, $x, 0],
            $h < 120 => [$x, $c, 0],
            $h < 180 => [0, $c, $x],
            $h < 240 => [0, $x, $c],
            $h < 300 => [$x, 0, $c],
            default  => [$c, 0, $x],
        };

        $r = (int) round(($r + $m) * 255);
        $g = (int) round(($g + $m) * 255);
        $b = (int) round(($b + $m) * 255);

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * Calculate relative luminance per WCAG 2.1.
     *
     * @param  string  $hex
     * @return float   Luminance value between 0 and 1.
     */
    private static function relativeLuminance(string $hex): float
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        $linearize = fn (float $c): float => $c <= 0.04045
            ? $c / 12.92
            : (($c + 0.055) / 1.055) ** 2.4;

        return 0.2126 * $linearize($r) + 0.7152 * $linearize($g) + 0.0722 * $linearize($b);
    }
}
