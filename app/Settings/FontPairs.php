<?php

/**
 * Typography font pair system.
 *
 * 8 research-backed font pairings used by well-known products.
 * Each pair specifies heading + body families, optional Google Fonts
 * query, and the source product for attribution.
 */

namespace Brndle\Settings;

class FontPairs
{
    /**
     * Available font pairings.
     *
     * Each entry contains:
     *  - name:     Human-readable label
     *  - heading:  CSS font-family for headings
     *  - body:     CSS font-family for body text
     *  - fonts:    Array of local font file definitions for @font-face
     *  - source:   Product attribution
     *
     * @return array<string, array{name: string, heading: string, body: string, fonts: array, source: string}>
     */
    public static function pairs(): array
    {
        $pairs = [
            'system' => [
                'name' => __('System UI', 'brndle'),
                'heading' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif',
                'body' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif',
                'fonts' => [],
                'source' => 'GitHub',
            ],
            'inter' => [
                'name' => __('Inter', 'brndle'),
                'heading' => '"Inter", sans-serif',
                'body' => '"Inter", sans-serif',
                'fonts' => [
                    ['family' => 'Inter', 'file' => 'inter-latin-wght-normal.woff2', 'weight' => '100 900', 'style' => 'normal'],
                ],
                'source' => 'Linear, Notion, Figma',
            ],
            'geist' => [
                'name' => __('Geist Sans', 'brndle'),
                'heading' => '"Geist Sans", sans-serif',
                'body' => '"Geist Sans", sans-serif',
                'fonts' => [
                    ['family' => 'Geist Sans', 'file' => 'geist-sans-latin-400-normal.woff2', 'weight' => '400', 'style' => 'normal'],
                    ['family' => 'Geist Sans', 'file' => 'geist-sans-latin-500-normal.woff2', 'weight' => '500', 'style' => 'normal'],
                    ['family' => 'Geist Sans', 'file' => 'geist-sans-latin-600-normal.woff2', 'weight' => '600', 'style' => 'normal'],
                    ['family' => 'Geist Sans', 'file' => 'geist-sans-latin-700-normal.woff2', 'weight' => '700', 'style' => 'normal'],
                ],
                'source' => 'Vercel',
            ],
            'plex' => [
                'name' => __('IBM Plex Sans', 'brndle'),
                'heading' => '"IBM Plex Sans", sans-serif',
                'body' => '"IBM Plex Sans", sans-serif',
                'fonts' => [
                    ['family' => 'IBM Plex Sans', 'file' => 'ibm-plex-sans-latin-400-normal.woff2', 'weight' => '400', 'style' => 'normal'],
                    ['family' => 'IBM Plex Sans', 'file' => 'ibm-plex-sans-latin-500-normal.woff2', 'weight' => '500', 'style' => 'normal'],
                    ['family' => 'IBM Plex Sans', 'file' => 'ibm-plex-sans-latin-600-normal.woff2', 'weight' => '600', 'style' => 'normal'],
                    ['family' => 'IBM Plex Sans', 'file' => 'ibm-plex-sans-latin-700-normal.woff2', 'weight' => '700', 'style' => 'normal'],
                ],
                'source' => 'IBM',
            ],
            'dm-sans' => [
                'name' => __('DM Sans', 'brndle'),
                'heading' => '"DM Sans", sans-serif',
                'body' => '"DM Sans", sans-serif',
                'fonts' => [
                    ['family' => 'DM Sans', 'file' => 'dm-sans-latin-wght-normal.woff2', 'weight' => '100 1000', 'style' => 'normal'],
                ],
                'source' => 'Google Design',
            ],
            'editorial' => [
                'name' => __('Editorial', 'brndle'),
                'heading' => '"Playfair Display", serif',
                'body' => '"Source Serif 4", serif',
                'fonts' => [
                    ['family' => 'Playfair Display', 'file' => 'playfair-display-latin-wght-normal.woff2', 'weight' => '400 900', 'style' => 'normal'],
                    ['family' => 'Source Serif 4', 'file' => 'source-serif-4-latin-wght-normal.woff2', 'weight' => '200 900', 'style' => 'normal'],
                ],
                'source' => 'NYT, Intercom',
            ],
            'magazine' => [
                'name' => __('Magazine', 'brndle'),
                'heading' => '"Fraunces", serif',
                'body' => '"Libre Franklin", sans-serif',
                'fonts' => [
                    ['family' => 'Fraunces', 'file' => 'fraunces-latin-wght-normal.woff2', 'weight' => '100 900', 'style' => 'normal'],
                    ['family' => 'Libre Franklin', 'file' => 'libre-franklin-latin-wght-normal.woff2', 'weight' => '100 900', 'style' => 'normal'],
                ],
                'source' => 'Premium editorial',
            ],
            'humanist' => [
                'name' => __('Humanist', 'brndle'),
                'heading' => '"Merriweather", serif',
                'body' => '"Source Sans 3", sans-serif',
                'fonts' => [
                    ['family' => 'Merriweather', 'file' => 'merriweather-latin-400-normal.woff2', 'weight' => '400', 'style' => 'normal'],
                    ['family' => 'Merriweather', 'file' => 'merriweather-latin-700-normal.woff2', 'weight' => '700', 'style' => 'normal'],
                    ['family' => 'Source Sans 3', 'file' => 'source-sans-3-latin-wght-normal.woff2', 'weight' => '200 900', 'style' => 'normal'],
                ],
                'source' => 'Publishing',
            ],
        ];

        /** @var array<string, array{name: string, heading: string, body: string, fonts: array, source: string}> */
        return apply_filters('brndle/font_pairs', $pairs);
    }

    /**
     * Resolve a pair key to its heading + body family strings.
     *
     * Falls back to the 'inter' pair if the key is unknown.
     *
     * @param  string  $pairKey
     * @return array{heading: string, body: string}
     */
    public static function resolve(string $pairKey): array
    {
        $pairs = self::pairs();
        $pair = $pairs[$pairKey] ?? $pairs['inter'];

        return [
            'heading' => $pair['heading'],
            'body' => $pair['body'],
        ];
    }
}
