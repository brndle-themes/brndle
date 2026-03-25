<?php

namespace Brndle\Providers;

class PageMetaServiceProvider
{
    public function boot(): void
    {
        add_action('init', [$this, 'registerMeta']);
    }

    public function registerMeta(): void
    {
        $meta_fields = [
            '_brndle_header_style' => [
                'type' => 'string',
                'default' => '',
            ],
            '_brndle_footer_style' => [
                'type' => 'string',
                'default' => '',
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
            ],
            '_brndle_body_class' => [
                'type' => 'string',
                'default' => '',
            ],
        ];

        foreach ($meta_fields as $key => $args) {
            register_post_meta('page', $key, [
                'show_in_rest' => true,
                'single' => true,
                'type' => $args['type'],
                'default' => $args['default'],
                'auth_callback' => fn () => current_user_can('edit_posts'),
            ]);
        }
    }
}
