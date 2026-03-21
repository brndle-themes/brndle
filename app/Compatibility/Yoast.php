<?php

namespace Brndle\Compatibility;

class Yoast
{
    public static function boot(): void
    {
        // Add breadcrumb support if Yoast or RankMath is active
        add_action('after_setup_theme', function () {
            if (function_exists('yoast_breadcrumb') || function_exists('rank_math_the_breadcrumbs')) {
                add_theme_support('yoast-seo-breadcrumbs');
            }
        });

        // Add JSON-LD Article schema if no SEO plugin is active
        add_action('wp_head', function () {
            if (
                ! defined('WPSEO_VERSION') &&
                ! class_exists('RankMath') &&
                is_singular('post')
            ) {
                self::outputArticleSchema();
            }
        }, 99);
    }

    private static function outputArticleSchema(): void
    {
        $post = get_post();
        if (! $post) {
            return;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title($post),
            'datePublished' => get_the_date('c', $post),
            'dateModified' => get_the_modified_date('c', $post),
            'author' => [
                '@type' => 'Person',
                'name' => get_the_author_meta('display_name', $post->post_author),
                'url' => get_author_posts_url($post->post_author),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => get_permalink($post),
            ],
        ];

        if (has_post_thumbnail($post)) {
            $schema['image'] = get_the_post_thumbnail_url($post, 'brndle-hero');
        }

        $description = get_the_excerpt($post);
        if ($description) {
            $schema['description'] = wp_strip_all_tags($description);
        }

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }
}
