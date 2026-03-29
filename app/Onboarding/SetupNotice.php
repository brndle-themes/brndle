<?php

namespace Brndle\Onboarding;

class SetupNotice
{
    public static function boot(): void
    {
        add_action('admin_notices', [self::class, 'render']);
        add_action('wp_ajax_brndle_dismiss_notice', [self::class, 'dismiss']);
    }

    public static function render(): void
    {
        if (get_user_meta(get_current_user_id(), 'brndle_notice_dismissed', true)) {
            return;
        }

        if (! current_user_can('manage_options')) {
            return;
        }

        $settings_url = admin_url('themes.php?page=brndle-settings');
        ?>
        <div class="notice notice-info is-dismissible" id="brndle-setup-notice" style="padding: 16px; border-left-color: #6366f1;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #6366f1, #a855f7, #06b6d4); display: flex; align-items: center; justify-content: center; color: white; font-weight: 900; font-size: 16px; flex-shrink: 0;">B</div>
                <div>
                    <p style="margin: 0 0 4px; font-size: 15px; font-weight: 600;">
                        <?php esc_html_e('Welcome to Brndle!', 'brndle'); ?>
                    </p>
                    <p style="margin: 0; color: #555;">
                        <?php
                        printf(
                            /* translators: %s is the settings page link */
                            esc_html__('Configure your brand colors, typography, and layouts in %s to get started.', 'brndle'),
                            '<a href="' . esc_url($settings_url) . '" style="font-weight: 500;">' . esc_html__('Brndle Settings', 'brndle') . '</a>'
                        );
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <script>
        jQuery(function($) {
            $('#brndle-setup-notice').on('click', '.notice-dismiss', function() {
                $.post(ajaxurl, { action: 'brndle_dismiss_notice', _wpnonce: '<?php echo wp_create_nonce('brndle_dismiss'); ?>' });
            });
        });
        </script>
        <?php
    }

    public static function dismiss(): void
    {
        check_ajax_referer('brndle_dismiss', '_wpnonce');

        if (! current_user_can('manage_options')) {
            wp_die(-1, 403);
        }

        update_user_meta(get_current_user_id(), 'brndle_notice_dismissed', true);
        wp_die();
    }
}
