<?php

/**
 * Theme filters.
 */

namespace Brndle;

/**
 * Add "… Continued" to the excerpt.
 */
add_filter('excerpt_more', function () {
    return sprintf(
        ' &hellip; <a href="%s" class="text-accent hover:text-accent-dark font-medium transition-colors">%s</a>',
        get_permalink(),
        __('Read more', 'brndle')
    );
});

/**
 * Custom excerpt length.
 */
add_filter('excerpt_length', function () {
    return 24;
});

/**
 * Add loading="lazy" and decoding="async" to content images.
 */
add_filter('wp_get_attachment_image_attributes', function ($attr) {
    if (! isset($attr['loading'])) {
        $attr['loading'] = 'lazy';
    }

    $attr['decoding'] = 'async';

    return $attr;
});
