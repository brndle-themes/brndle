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
    ];

    public function boot(): void
    {
        add_action('init', [$this, 'registerBlocks']);
        add_filter('block_categories_all', [$this, 'registerCategory']);
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

            register_block_type($path, [
                'editor_script' => 'brndle-blocks-editor',
                'editor_style' => 'brndle-blocks-editor-style',
                'render_callback' => function (array $attributes, string $content) use ($viewName) {
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
