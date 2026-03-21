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
    ];

    public function boot(): void
    {
        add_action('init', [$this, 'registerBlocks']);
        add_filter('block_categories_all', [$this, 'registerCategory']);
    }

    public function registerBlocks(): void
    {
        foreach ($this->blocks as $block) {
            $path = get_theme_file_path("blocks/{$block}");

            if (! file_exists("{$path}/block.json")) {
                continue;
            }

            $viewName = "blocks.{$block}";

            register_block_type($path, [
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
