<?php

namespace Brndle\Providers;

class PageMetaServiceProvider
{
    public function boot(): void
    {
        add_action('init', [$this, 'registerMeta']);
        add_filter('body_class', [$this, 'addBodyClass']);
    }

    public function addBodyClass(array $classes): array
    {
        if (is_singular('page')) {
            $extra = get_post_meta(get_the_ID(), '_brndle_body_class', true);
            if ($extra) {
                $classes = array_merge($classes, array_filter(explode(' ', $extra)));
            }
        }

        return $classes;
    }

    public function registerMeta(): void
    {
        $headerStyles = ['', 'sticky', 'solid', 'transparent', 'centered', 'minimal', 'split', 'banner', 'glass'];
        $footerStyles = ['', 'dark', 'light', 'columns', 'minimal', 'big', 'stacked'];
        $colorSchemes = ['', 'sapphire', 'indigo', 'cobalt', 'trust', 'commerce', 'signal', 'coral', 'aubergine', 'midnight', 'stone', 'carbon', 'neutral'];

        $meta_fields = [
            '_brndle_header_style' => [
                'type' => 'string',
                'default' => '',
                'sanitize_callback' => fn ($v) => in_array($v, $headerStyles, true) ? $v : '',
            ],
            '_brndle_footer_style' => [
                'type' => 'string',
                'default' => '',
                'sanitize_callback' => fn ($v) => in_array($v, $footerStyles, true) ? $v : '',
            ],
            '_brndle_hide_header' => [
                'type' => 'boolean',
                'default' => false,
            ],
            '_brndle_hide_footer' => [
                'type' => 'boolean',
                'default' => false,
            ],
            '_brndle_color_scheme' => [
                'type' => 'string',
                'default' => '',
                'sanitize_callback' => fn ($v) => in_array($v, $colorSchemes, true) ? $v : '',
            ],
            '_brndle_body_class' => [
                'type' => 'string',
                'default' => '',
                'sanitize_callback' => fn ($v) => implode(' ', array_map('sanitize_html_class', explode(' ', $v))),
            ],
        ];

        foreach ($meta_fields as $key => $args) {
            $registration = [
                'show_in_rest' => true,
                'single' => true,
                'type' => $args['type'],
                'default' => $args['default'],
                'auth_callback' => fn () => current_user_can('edit_posts'),
            ];

            if (isset($args['sanitize_callback'])) {
                $registration['sanitize_callback'] = $args['sanitize_callback'];
            }

            register_post_meta('page', $key, $registration);
        }
    }
}
