<?php

namespace Brndle\Providers;

class BlockServiceProvider
{
    /**
     * Blocks to register from the blocks/ directory.
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

            if (file_exists("{$path}/block.json")) {
                register_block_type($path);
            }
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
