<?php

namespace Brndle\View\Composers;

use Roots\Acorn\View\Composer;

class Post extends Composer
{
    protected static $views = [
        'partials.page-header',
        'partials.content',
        'partials.content-*',
        'partials.single.*',
        'partials.archive.*',
    ];

    public function override(): array
    {
        return [
            'title' => $this->resolveTitle(),
            'readingTime' => $this->resolveReadingTime(),
            'pagination' => wp_link_pages([
                'echo' => 0,
                'before' => '<p>' . __('Pages:', 'brndle'),
                'after' => '</p>',
            ]),
        ];
    }

    private function resolveTitle(): string
    {
        $allowedHtml = ['br' => [], 'em' => [], 'strong' => [], 'span' => ['class' => []]];
        $decode = fn (string $s): string => html_entity_decode($s, ENT_QUOTES, 'UTF-8');

        if ($this->view->name() !== 'partials.page-header') {
            return $decode(wp_kses(get_the_title(), $allowedHtml));
        }

        if (is_home()) {
            if ($home = get_option('page_for_posts', true)) {
                return $decode(wp_kses(get_the_title($home), $allowedHtml));
            }

            return __('Latest Posts', 'brndle');
        }

        if (is_archive()) {
            return $decode(wp_kses(get_the_archive_title(), $allowedHtml));
        }

        if (is_search()) {
            return sprintf(
                /* translators: %s: search query string */
                __('Search Results for %s', 'brndle'),
                esc_html(get_search_query())
            );
        }

        if (is_404()) {
            return __('Not Found', 'brndle');
        }

        return $decode(wp_kses(get_the_title(), $allowedHtml));
    }

    private function resolveReadingTime(): string
    {
        $post = get_post();
        if (! $post) {
            return '';
        }

        // Use raw content — no filters, no block rendering
        $word_count = str_word_count(strip_tags($post->post_content));
        $minutes = max(1, ceil($word_count / 250));

        return sprintf(
            /* translators: %d: estimated reading time in minutes */
            _n('%d min read', '%d min read', $minutes, 'brndle'),
            $minutes
        );
    }
}
