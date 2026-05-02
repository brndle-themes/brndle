<?php

/**
 * Performance hooks.
 *
 * Cluster of small, theme-scope perf wins that play well with any caching
 * plugin or CDN. Each hook is a single concern with a public toggle so
 * client sites can disable individual ones without forking the theme.
 *
 * Currently shipped (Phase 1 of plans/2026-05-02-astro-level-perf.md):
 *   - Speculation Rules         — prefetch same-origin links on hover
 *   - LCP image preload         — emit <link rel="preload"> for the hero
 *
 * Not shipped yet (later phases): critical CSS, view transitions,
 * <picture> component, service worker. See the perf roadmap.
 */

namespace Brndle\Providers;

class PerformanceServiceProvider
{
    /**
     * Block names whose first occurrence on a page is a likely LCP candidate.
     * Order matters — earlier blocks beat later ones when both are present.
     *
     * @var string[]
     */
    private const LCP_BLOCKS = [
        'brndle/hero',
        'brndle/content-image-split',
        'brndle/video-embed',
    ];

    public function boot(): void
    {
        // Priority 4 sits after the design tokens (1) and font preload (2)
        // but before any third-party plugin output.
        add_action('wp_head', [$this, 'outputSpeculationRules'], 4);
        add_action('wp_head', [$this, 'outputLcpImagePreload'], 3);
    }

    /**
     * Emit <script type="speculationrules"> so Chrome/Edge prefetches
     * same-origin links the user is likely to visit next.
     *
     * Uses `moderate` eagerness — prefetch on intent (mousedown / hover for
     * a couple hundred ms) rather than `eager` (all visible links). Excludes
     * the WP admin so the user never accidentally pre-warms a logout link.
     *
     * Browsers without Speculation Rules ignore the script entirely; no
     * polyfill needed, no JS shipped.
     *
     * @link https://developer.mozilla.org/docs/Web/API/Speculation_Rules_API
     */
    public function outputSpeculationRules(): void
    {
        if (is_admin() || ! apply_filters('brndle/perf/speculation_rules', true)) {
            return;
        }

        $rules = [
            'prefetch' => [
                [
                    'where' => [
                        'and' => [
                            ['href_matches' => '/*'],
                            ['not' => ['href_matches' => '/wp-admin/*']],
                            ['not' => ['href_matches' => '/wp-login.php*']],
                            ['not' => ['selector_matches' => '[rel~="external"]']],
                            ['not' => ['selector_matches' => '[data-no-prefetch]']],
                        ],
                    ],
                    'eagerness' => 'moderate',
                ],
            ],
        ];

        echo '<script type="speculationrules">' . wp_json_encode($rules) . '</script>' . "\n";
    }

    /**
     * Walk the current post's blocks for the first LCP-likely image and
     * emit a <link rel="preload"> so the browser starts fetching it before
     * CSS finishes parsing. Saves 100–400 ms of LCP on hero pages.
     *
     * Skips when:
     *   - On admin / feed / non-singular views
     *   - The post has no parsed blocks (classic editor content)
     *   - No matching block has a non-empty image attribute
     */
    public function outputLcpImagePreload(): void
    {
        if (is_admin() || is_feed() || ! is_singular()) {
            return;
        }

        if (! apply_filters('brndle/perf/lcp_preload', true)) {
            return;
        }

        $post = get_post();
        if (! $post || ! has_blocks($post->post_content)) {
            return;
        }

        $imageUrl = $this->findLcpImage(parse_blocks($post->post_content));
        if (! $imageUrl) {
            return;
        }

        printf(
            '<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n",
            esc_url($imageUrl)
        );
    }

    /**
     * Walk parsed blocks (depth-first) and return the first block image
     * URL belonging to one of the LCP_BLOCKS types. Returns null when no
     * candidate is found.
     *
     * @param  array<int, array<string, mixed>>  $blocks
     */
    private function findLcpImage(array $blocks): ?string
    {
        foreach ($blocks as $block) {
            $name = $block['blockName'] ?? null;
            if ($name && in_array($name, self::LCP_BLOCKS, true)) {
                $url = $this->blockImageUrl($name, $block['attrs'] ?? []);
                if ($url) {
                    return $url;
                }
            }
            if (! empty($block['innerBlocks']) && is_array($block['innerBlocks'])) {
                $nested = $this->findLcpImage($block['innerBlocks']);
                if ($nested) {
                    return $nested;
                }
            }
        }

        return null;
    }

    /**
     * Resolve the LCP image URL for a given block by inspecting its
     * attributes. Different blocks use different attribute names; this
     * keeps the mapping in one place so future LCP-eligible blocks just
     * add a case.
     *
     * @param  array<string, mixed>  $attrs
     */
    private function blockImageUrl(string $name, array $attrs): ?string
    {
        $candidate = match ($name) {
            'brndle/hero', 'brndle/content-image-split' => $attrs['image'] ?? null,
            'brndle/video-embed' => $attrs['poster'] ?? null,
            default => null,
        };

        return is_string($candidate) && $candidate !== '' ? $candidate : null;
    }
}
