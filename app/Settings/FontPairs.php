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
     *  - google:   Google Fonts query string (null if system/CDN)
     *  - source:   Product attribution
     *
     * @return array<string, array{name: string, heading: string, body: string, google: string|null, source: string}>
     */
    public static function pairs(): array
    {
        $pairs = [
            'system' => [
                'name' => __('System UI', 'brndle'),
                'heading' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif',
                'body' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif',
                'google' => null,
                'source' => 'GitHub',
            ],
            'inter' => [
                'name' => __('Inter', 'brndle'),
                'heading' => '"Inter", sans-serif',
                'body' => '"Inter", sans-serif',
                'google' => 'family=Inter:wght@400;500;600;700',
                'source' => 'Linear, Notion, Figma',
            ],
            'geist' => [
                'name' => __('Geist Sans', 'brndle'),
                'heading' => '"Geist Sans", sans-serif',
                'body' => '"Geist Sans", sans-serif',
                'google' => null, // Served via jsDelivr CDN
                'source' => 'Vercel',
            ],
            'plex' => [
                'name' => __('IBM Plex Sans', 'brndle'),
                'heading' => '"IBM Plex Sans", sans-serif',
                'body' => '"IBM Plex Sans", sans-serif',
                'google' => 'family=IBM+Plex+Sans:wght@400;500;600;700',
                'source' => 'IBM',
            ],
            'dm-sans' => [
                'name' => __('DM Sans', 'brndle'),
                'heading' => '"DM Sans", sans-serif',
                'body' => '"DM Sans", sans-serif',
                'google' => 'family=DM+Sans:wght@400;500;600;700',
                'source' => 'Google Design',
            ],
            'editorial' => [
                'name' => __('Editorial', 'brndle'),
                'heading' => '"Playfair Display", serif',
                'body' => '"Source Serif 4", serif',
                'google' => 'family=Playfair+Display:wght@400;600;700&family=Source+Serif+4:wght@400;600',
                'source' => 'NYT, Intercom',
            ],
            'magazine' => [
                'name' => __('Magazine', 'brndle'),
                'heading' => '"Fraunces", serif',
                'body' => '"Libre Franklin", sans-serif',
                'google' => 'family=Fraunces:wght@400;600;700&family=Libre+Franklin:wght@400;500;600',
                'source' => 'Premium editorial',
            ],
            'humanist' => [
                'name' => __('Humanist', 'brndle'),
                'heading' => '"Merriweather", serif',
                'body' => '"Source Sans 3", sans-serif',
                'google' => 'family=Merriweather:wght@400;700&family=Source+Sans+3:wght@400;600',
                'source' => 'Publishing',
            ],
        ];

        /** @var array<string, array{name: string, heading: string, body: string, google: string|null, source: string}> */
        return apply_filters('brndle/font_pairs', $pairs);
    }

    /**
     * Build a full Google Fonts URL for a given pair key.
     *
     * Returns null when the pair does not use Google Fonts
     * (e.g. system fonts or CDN-hosted fonts).
     *
     * @param  string  $pairKey  Key from pairs().
     * @return string|null       Google Fonts URL with display=swap, or null.
     */
    public static function googleFontsUrl(string $pairKey): ?string
    {
        $pairs = self::pairs();

        if (! isset($pairs[$pairKey]) || $pairs[$pairKey]['google'] === null) {
            return null;
        }

        return 'https://fonts.googleapis.com/css2?' . $pairs[$pairKey]['google'] . '&display=swap';
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
