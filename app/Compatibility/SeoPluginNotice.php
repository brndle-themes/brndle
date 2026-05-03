<?php

namespace Brndle\Compatibility;

class SeoPluginNotice
{
    private const DISMISS_KEY = 'brndle_seo_notice_dismissed';

    public static function boot(): void
    {
        if (defined('WPSEO_VERSION') || class_exists('RankMath')) {
            return;
        }

        add_action('admin_notices', [self::class, 'render']);
        add_action('wp_ajax_brndle_dismiss_seo_notice', [self::class, 'dismiss']);
        add_action('admin_footer', [self::class, 'dismissScript']);
    }

    public static function render(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        if (get_user_meta(get_current_user_id(), self::DISMISS_KEY, true)) {
            return;
        }

        $message = sprintf(
            /* translators: 1: Yoast SEO plugin name, 2: Rank Math plugin name */
            esc_html__('Brndle expects %1$s or %2$s to be active. Without an SEO plugin, post pages publish without Article / Person / Breadcrumb structured data — the theme deliberately does not emit competing schema.', 'brndle'),
            '<strong>Yoast SEO</strong>',
            '<strong>Rank Math</strong>'
        );

        $nonce = wp_create_nonce('brndle_seo_notice');

        echo '<div class="notice notice-warning is-dismissible" data-brndle-seo-notice data-nonce="' . esc_attr($nonce) . '">';
        echo '<p>' . wp_kses($message, ['strong' => []]) . '</p>';
        echo '</div>';
    }

    public static function dismiss(): void
    {
        check_ajax_referer('brndle_seo_notice');

        if (! current_user_can('manage_options')) {
            wp_send_json_error('forbidden', 403);
        }

        update_user_meta(get_current_user_id(), self::DISMISS_KEY, 1);
        wp_send_json_success();
    }

    public static function dismissScript(): void
    {
        ?>
        <script>
        (function () {
            var notice = document.querySelector('[data-brndle-seo-notice]');
            if (!notice) { return; }
            notice.addEventListener('click', function (event) {
                if (!event.target.classList.contains('notice-dismiss')) { return; }
                var formData = new FormData();
                formData.append('action', 'brndle_dismiss_seo_notice');
                formData.append('_wpnonce', notice.dataset.nonce);
                fetch(ajaxurl, { method: 'POST', credentials: 'same-origin', body: formData });
            });
        })();
        </script>
        <?php
    }
}
