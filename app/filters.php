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

/**
 * Extend allowed HTML for wp_kses_post so wp:html blocks
 * can use style and class attributes on all elements.
 */
add_filter('wp_kses_allowed_html', function (array $tags, string $context): array {
    if ($context !== 'post') {
        return $tags;
    }

    $extra_attrs = [
        'class' => true,
        'style' => true,
        'id' => true,
        'aria-label' => true,
        'aria-hidden' => true,
        'role' => true,
        'loading' => true,
        'fetchpriority' => true,
        'target' => true,
        'rel' => true,
    ];

    foreach (['a', 'div', 'section', 'span', 'nav', 'p', 'img', 'svg', 'path', 'figure', 'main', 'footer', 'header', 'ul', 'li', 'time'] as $tag) {
        if (! isset($tags[$tag])) {
            $tags[$tag] = [];
        }
        $tags[$tag] = array_merge($tags[$tag], $extra_attrs);
    }

    // SVG support
    $tags['svg'] = array_merge($tags['svg'] ?? [], [
        'xmlns' => true, 'viewbox' => true, 'fill' => true,
        'width' => true, 'height' => true, 'class' => true, 'style' => true,
        'stroke' => true, 'stroke-width' => true,
    ]);
    $tags['path'] = array_merge($tags['path'] ?? [], [
        'd' => true, 'fill' => true, 'fill-rule' => true,
        'stroke-linecap' => true, 'stroke-linejoin' => true, 'stroke' => true, 'stroke-width' => true,
    ]);

    return $tags;
}, 10, 2);
