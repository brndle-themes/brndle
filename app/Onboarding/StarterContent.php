<?php

namespace Brndle\Onboarding;

class StarterContent
{
    public static function boot(): void
    {
        add_action('after_setup_theme', function () {
            add_theme_support('starter-content', self::content());
        });
    }

    private static function content(): array
    {
        return [
            'posts' => [
                'home' => [
                    'post_type' => 'page',
                    'post_title' => _x('Home', 'Theme starter content', 'brndle'),
                    'template' => 'template-landing',
                    'post_content' => '<!-- wp:brndle/hero {"title":"Welcome to our website","subtitle":"Enterprise-grade performance meets beautiful design.","cta_primary":"Get Started","cta_primary_url":"#","variant":"dark"} /-->

<!-- wp:brndle/features {"eyebrow":"Features","title":"Built for speed","features":[{"title":"Lightning Fast","description":"Zero JavaScript bloat. Lighthouse 100."},{"title":"AI-Powered","description":"Create landing pages with AI in seconds."},{"title":"Enterprise Ready","description":"Built for 100+ sites with centralized settings."}]} /-->

<!-- wp:brndle/cta {"title":"Ready to get started?","subtitle":"Join thousands of teams building better websites.","cta_primary":"Start Free","cta_primary_url":"#"} /-->',
                ],
                'about' => [
                    'post_type' => 'page',
                    'post_title' => _x('About', 'Theme starter content', 'brndle'),
                ],
                'contact' => [
                    'post_type' => 'page',
                    'post_title' => _x('Contact', 'Theme starter content', 'brndle'),
                ],
                'blog' => [
                    'post_type' => 'page',
                    'post_title' => _x('Blog', 'Theme starter content', 'brndle'),
                ],
            ],
            'options' => [
                'show_on_front' => 'page',
                'page_on_front' => '{{home}}',
                'page_for_posts' => '{{blog}}',
            ],
            'nav_menus' => [
                'primary_navigation' => [
                    'name' => __('Primary Navigation', 'brndle'),
                    'items' => [
                        'page_home',
                        'page_about',
                        'page_blog',
                        'page_contact',
                    ],
                ],
                'footer_navigation' => [
                    'name' => __('Footer Navigation', 'brndle'),
                    'items' => [
                        'page_about',
                        'page_contact',
                    ],
                ],
            ],
        ];
    }
}
