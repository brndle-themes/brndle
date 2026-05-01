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
    ];

    public function boot(): void
    {
        add_action('init', [$this, 'registerBlocks']);
        add_filter('block_categories_all', [$this, 'registerCategory']);
        add_action('wp_enqueue_scripts', [$this, 'registerFrontendScripts']);
        // Load the compiled frontend stylesheet inside the block editor iframe
        // so Tailwind utilities and .brndle-section-* rules resolve there too.
        add_action('enqueue_block_assets', [$this, 'enqueueEditorIframeStyles']);
    }

    /**
     * Resolve a hashed Vite asset filename (e.g. assets/app-Ch4xGcKa.css)
     * from public/build/manifest.json.
     */
    protected function viteAsset(string $entry): ?string
    {
        static $manifest = null;
        if ($manifest === null) {
            $manifestPath = get_theme_file_path('public/build/manifest.json');
            $manifest = file_exists($manifestPath)
                ? (json_decode((string) file_get_contents($manifestPath), true) ?: [])
                : [];
        }
        return $manifest[$entry]['file'] ?? null;
    }

    /**
     * Enqueue the compiled frontend stylesheet inside the editor iframe so
     * blocks render with the correct theme tokens, Tailwind utilities and
     * brndle-section-* layout rules. The hook runs for both frontend and
     * editor block contexts; the is_admin() guard limits it to the editor.
     */
    public function enqueueEditorIframeStyles(): void
    {
        if (! is_admin()) {
            return;
        }

        $appCss = $this->viteAsset('resources/css/app.css');
        if ($appCss) {
            wp_enqueue_style(
                'brndle-blocks-iframe',
                get_theme_file_uri("public/build/{$appCss}"),
                [],
                null
            );
        }

        // Editor-only overrides — keep blocks workable inside the canvas:
        // cap min-h-screen so blocks aren't a full viewport tall, and make
        // sure headings inside dark sections inherit white from the parent.
        $overrides = <<<'CSS'
        .brndle-section-dark, .brndle-section-gradient { min-height: 0 !important; }
        .brndle-section-dark .min-h-screen,
        .brndle-section-gradient .min-h-screen,
        section.min-h-screen { min-height: 0 !important; }
        .brndle-section-dark h1, .brndle-section-dark h2, .brndle-section-dark h3,
        .brndle-section-gradient h1, .brndle-section-gradient h2, .brndle-section-gradient h3 {
            color: inherit;
        }
        /* Disable the eyebrow ping pulse and hover lifts inside the editor canvas. */
        .editor-styles-wrapper .animate-ping,
        .block-editor-iframe__container .animate-ping { animation: none !important; }
        CSS;
        wp_add_inline_style('brndle-blocks-iframe', $overrides);
    }

    /**
     * Register block view scripts. Enqueueing happens lazily inside the
     * render callback so the asset only loads on pages that use the block.
     */
    public function registerFrontendScripts(): void
    {
        $asset_file = get_theme_file_path('blocks/build/lead-form-view.asset.php');
        if (! file_exists($asset_file)) {
            return;
        }
        $asset = require $asset_file;
        wp_register_script(
            'brndle-lead-form-view',
            get_theme_file_uri('blocks/build/lead-form-view.js'),
            $asset['dependencies'] ?? [],
            $asset['version'] ?? false,
            true
        );
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

            register_block_type($path, [
                'editor_script' => 'brndle-blocks-editor',
                'editor_style' => 'brndle-blocks-editor-style',
                'render_callback' => function (array $attributes, string $content) use ($viewName, $blockSlug) {
                    // Lazily enqueue the lead-form view script only when the block actually renders.
                    if ($blockSlug === 'lead-form' && empty($attributes['form_action'] ?? '')) {
                        wp_enqueue_script('brndle-lead-form-view');
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
