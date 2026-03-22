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
        if ($this->view->name() !== 'partials.page-header') {
            return get_the_title();
        }

        if (is_home()) {
            if ($home = get_option('page_for_posts', true)) {
                return get_the_title($home);
            }

            return __('Latest Posts', 'brndle');
        }

        if (is_archive()) {
            return get_the_archive_title();
        }

        if (is_search()) {
            return sprintf(
                __('Search Results for %s', 'brndle'),
                get_search_query()
            );
        }

        if (is_404()) {
            return __('Not Found', 'brndle');
        }

        return get_the_title();
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
            _n('%d min read', '%d min read', $minutes, 'brndle'),
            $minutes
        );
    }
}
