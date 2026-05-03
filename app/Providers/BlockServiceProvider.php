<?php

namespace Brndle\Providers;

use function Roots\view;

class BlockServiceProvider
{
    /**
     * Blocks to register from the blocks/ directory.
     * Each block has a block.json for metadata and a Blade view in resources/views/blocks/ for rendering.
     */
    protected array $blocks = [
        'hero',
        'logos',
        'stats',
        'features',
        'testimonials',
        'pricing',
        'cta',
        'faq',
        'content-image-split',
        'how-it-works',
        'lead-form',
        'comparison-table',
        'team',
        'video-embed',
        // v2.1 editorial blocks
        'code',
        'pull-quote',
        'timeline',
        'tabs-accordion',
    ];

    public function boot(): void
    {
        add_action('init', [$this, 'registerBlocks']);
        add_filter('block_categories_all', [$this, 'registerCategory']);
        add_action('wp_enqueue_scripts', [$this, 'registerFrontendScripts']);
    }

    /**
     * Register block view scripts. Enqueueing happens lazily inside the
     * render callback so the asset only loads on pages that use the block.
     */
    public function registerFrontendScripts(): void
    {
        $viewScripts = [
            'lead-form-view' => 'brndle-lead-form-view',
            'code-view' => 'brndle-code-view',
            'timeline-view' => 'brndle-timeline-view',
            'tabs-accordion-view' => 'brndle-tabs-accordion-view',
        ];

        foreach ($viewScripts as $entry => $handle) {
            $asset_file = get_theme_file_path("blocks/build/{$entry}.asset.php");
            if (! file_exists($asset_file)) {
                continue;
            }
            $asset = require $asset_file;
            wp_register_script(
                $handle,
                get_theme_file_uri("blocks/build/{$entry}.js"),
                $asset['dependencies'] ?? [],
                $asset['version'] ?? false,
                true
            );
        }
    }

    public function registerBlocks(): void
    {
        // Enqueue editor script for all blocks
        $asset_file = get_theme_file_path('blocks/build/index.asset.php');
        if (file_exists($asset_file)) {
            $asset = require $asset_file;
            wp_register_script(
                'brndle-blocks-editor',
                get_theme_file_uri('blocks/build/index.js'),
                $asset['dependencies'] ?? [],
                $asset['version'] ?? false,
                true
            );

            // Load JS translations from resources/lang for the editor script.
            wp_set_script_translations(
                'brndle-blocks-editor',
                'brndle',
                get_theme_file_path('resources/lang')
            );

            $style_path = get_theme_file_path('blocks/build/index.css');
            if (file_exists($style_path)) {
                wp_register_style(
                    'brndle-blocks-editor-style',
                    get_theme_file_uri('blocks/build/index.css'),
                    [],
                    $asset['version'] ?? false
                );
            }
        }

        foreach ($this->blocks as $block) {
            $path = get_theme_file_path("blocks/{$block}");

            if (! file_exists("{$path}/block.json")) {
                continue;
            }

            $viewName = "blocks.{$block}";
            $blockSlug = $block;
            $blockName = "brndle/{$block}";

            register_block_type($path, [
                'editor_script' => 'brndle-blocks-editor',
                'editor_style' => 'brndle-blocks-editor-style',
                'render_callback' => function (array $attributes, string $content) use ($viewName, $blockSlug, $blockName) {
                    // Run any registered attribute migrations so the Blade
                    // template only ever sees the current shape (no
                    // `is_string($x) || is_array($x)` branches forever).
                    $attributes = \Brndle\Blocks\AttributeMigrations::apply($blockName, $attributes);

                    // Lazily enqueue per-block view scripts only when the block actually renders.
                    if ($blockSlug === 'lead-form' && empty($attributes['form_action'] ?? '')) {
                        wp_enqueue_script('brndle-lead-form-view');
                    }
                    if ($blockSlug === 'code' && ! empty($attributes['code'] ?? '')) {
                        wp_enqueue_script('brndle-code-view');
                    }
                    if ($blockSlug === 'timeline' && ! empty($attributes['items'] ?? [])) {
                        wp_enqueue_script('brndle-timeline-view');
                    }
                    if ($blockSlug === 'tabs-accordion' && ! empty($attributes['items'] ?? [])) {
                        wp_enqueue_script('brndle-tabs-accordion-view');
                    }

                    // Render through Acorn's Blade engine
                    if (function_exists('Roots\\view')) {
                        return view($viewName, [
                            'attributes' => $attributes,
                            'content' => $content,
                        ])->render();
                    }

                    // Fallback: if Acorn view isn't available
                    return $content;
                },
            ]);
        }
    }

    public function registerCategory(array $categories): array
    {
        array_unshift($categories, [
            'slug' => 'brndle-sections',
            'title' => __('Brndle Sections', 'brndle'),
            'icon' => 'layout',
        ]);

        return $categories;
    }
}
