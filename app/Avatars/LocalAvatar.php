<?php

/**
 * Local avatar.
 *
 * Adds a "Profile photo" field on the user-edit screen so authors can
 * upload their avatar via the WP media library. Stores the attachment ID
 * as user meta `_brndle_avatar_id`. Filters `get_avatar` /
 * `pre_get_avatar_data` to return the local URL when set, falling back
 * to gravatar (or to the gravatar default mystery-person glyph if
 * `disable_gravatar_fallback` is on).
 *
 * Why this exists for Brndle: most Brndle sites are internal blogging
 * setups where authors are real team members, not random commenters.
 * Local avatars give:
 *   - No external request to secure.gravatar.com (perf + privacy)
 *   - No CLS from late-loading external image (when attachment has
 *     proper width/height in metadata)
 *   - GDPR-friendly (no IP shared with Automattic)
 *   - Works offline / on intranet deployments
 *
 * Pattern matches Simple Local Avatars / Basic User Avatars; we ship it
 * in-theme so users don't need an extra plugin.
 *
 * @see plans/avatar (no formal plan — this is a small feature, see
 *      v1.9.1 changelog for context)
 */

namespace Brndle\Avatars;

use Brndle\Settings\Settings;

class LocalAvatar
{
    /**
     * User meta key holding the attachment ID.
     */
    public const META_KEY = '_brndle_avatar_id';

    /**
     * Bootstrap all hooks.
     *
     * @return void
     */
    /**
     * Per-user social profile meta keys. Authors fill these out on their
     * profile edit screen; the author-box partial renders matching icons.
     */
    public const SOCIAL_KEYS = [
        '_brndle_role' => ['label' => 'Role / Title', 'type' => 'text', 'placeholder' => 'Senior WordPress Developer'],
        '_brndle_twitter' => ['label' => 'X / Twitter URL', 'type' => 'url', 'placeholder' => 'https://x.com/handle'],
        '_brndle_linkedin' => ['label' => 'LinkedIn URL', 'type' => 'url', 'placeholder' => 'https://linkedin.com/in/handle'],
        '_brndle_github' => ['label' => 'GitHub URL', 'type' => 'url', 'placeholder' => 'https://github.com/handle'],
        '_brndle_website' => ['label' => 'Website URL', 'type' => 'url', 'placeholder' => 'https://example.com'],
    ];

    public static function boot(): void
    {
        // Admin: render the field on user edit screens (own profile + others).
        add_action('show_user_profile', [self::class, 'renderField']);
        add_action('edit_user_profile', [self::class, 'renderField']);

        // Admin: save the field on profile update.
        add_action('personal_options_update', [self::class, 'save']);
        add_action('edit_user_profile_update', [self::class, 'save']);

        // Admin: enqueue media library + tiny picker JS on user-edit screens.
        add_action('admin_enqueue_scripts', [self::class, 'enqueueMediaPicker']);

        // Frontend: swap the avatar URL when a local one is set.
        add_filter('pre_get_avatar_data', [self::class, 'filterAvatarData'], 10, 2);
    }

    /**
     * Render the "Profile photo" upload field on the user-edit screen.
     * Uses WP's media library frame via inline JS — no extra dependencies.
     *
     * @param  \WP_User $user
     * @return void
     */
    public static function renderField(\WP_User $user): void
    {
        $attachmentId = (int) get_user_meta($user->ID, self::META_KEY, true);
        $previewUrl = $attachmentId > 0
            ? wp_get_attachment_image_url($attachmentId, 'thumbnail')
            : '';

        ?>
        <h2><?php esc_html_e('Brndle: Profile photo & author bio', 'brndle'); ?></h2>
        <table class="form-table" role="presentation">
            <tr>
                <th><label for="brndle-avatar-id"><?php esc_html_e('Avatar', 'brndle'); ?></label></th>
                <td>
                    <div class="brndle-avatar-field">
                        <img class="brndle-avatar-preview"
                             src="<?php echo esc_url($previewUrl); ?>"
                             alt=""
                             style="width:96px;height:96px;border-radius:8px;object-fit:cover;background:#f0f0f1;display:<?php echo $previewUrl ? 'block' : 'none'; ?>;margin-bottom:8px;">
                        <input type="hidden"
                               id="brndle-avatar-id"
                               name="<?php echo esc_attr(self::META_KEY); ?>"
                               value="<?php echo esc_attr((string) $attachmentId); ?>">
                        <button type="button" class="button brndle-avatar-upload">
                            <?php echo $attachmentId > 0
                                ? esc_html__('Replace', 'brndle')
                                : esc_html__('Upload avatar', 'brndle'); ?>
                        </button>
                        <button type="button" class="button-link brndle-avatar-remove" style="margin-left:8px;color:#a00;<?php echo $attachmentId > 0 ? '' : 'display:none;'; ?>">
                            <?php esc_html_e('Remove', 'brndle'); ?>
                        </button>
                        <p class="description">
                            <?php esc_html_e('Square images work best (512×512+). Falls back to Gravatar if not set.', 'brndle'); ?>
                        </p>
                    </div>
                </td>
            </tr>

            <?php foreach (self::SOCIAL_KEYS as $key => $config):
                $value = (string) get_user_meta($user->ID, $key, true);
            ?>
            <tr>
                <th><label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($config['label']); ?></label></th>
                <td>
                    <input type="<?php echo esc_attr($config['type']); ?>"
                           id="<?php echo esc_attr($key); ?>"
                           name="<?php echo esc_attr($key); ?>"
                           value="<?php echo esc_attr($value); ?>"
                           placeholder="<?php echo esc_attr($config['placeholder']); ?>"
                           class="regular-text">
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php
    }

    /**
     * Save handler — fires on profile-update.
     *
     * @param  int $userId
     * @return void
     */
    public static function save(int $userId): void
    {
        if (! current_user_can('edit_user', $userId)) {
            return;
        }

        // Avatar attachment ID.
        $value = isset($_POST[self::META_KEY]) ? absint($_POST[self::META_KEY]) : 0;
        if ($value > 0) {
            update_user_meta($userId, self::META_KEY, $value);
        } else {
            delete_user_meta($userId, self::META_KEY);
        }

        // Social meta keys + role.
        foreach (self::SOCIAL_KEYS as $key => $config) {
            if (! isset($_POST[$key])) continue;
            $raw = (string) wp_unslash($_POST[$key]);
            $clean = $config['type'] === 'url'
                ? esc_url_raw($raw)
                : sanitize_text_field($raw);
            if ($clean === '') {
                delete_user_meta($userId, $key);
            } else {
                update_user_meta($userId, $key, $clean);
            }
        }
    }

    /**
     * Enqueue WP media library + the small picker script on user-edit
     * screens only.
     *
     * @param  string $hook
     * @return void
     */
    public static function enqueueMediaPicker(string $hook): void
    {
        if (! in_array($hook, ['user-edit.php', 'profile.php'], true)) {
            return;
        }

        wp_enqueue_media();

        // Inline JS — small enough to not warrant a separate file. Reads /
        // writes the hidden input + preview img above. Idempotent: bound
        // once per page even if the field is re-rendered.
        wp_add_inline_script('jquery', <<<'JS'
            (function ($) {
                $(document).on('click', '.brndle-avatar-upload', function (e) {
                    e.preventDefault();
                    var $btn = $(this);
                    var $field = $btn.closest('.brndle-avatar-field');
                    var frame = wp.media({
                        title: 'Select profile photo',
                        button: { text: 'Use this photo' },
                        library: { type: 'image' },
                        multiple: false,
                    });
                    frame.on('select', function () {
                        var attachment = frame.state().get('selection').first().toJSON();
                        var thumb = (attachment.sizes && attachment.sizes.thumbnail)
                            ? attachment.sizes.thumbnail.url
                            : attachment.url;
                        $field.find('input[type=hidden]').val(attachment.id);
                        $field.find('.brndle-avatar-preview').attr('src', thumb).show();
                        $field.find('.brndle-avatar-remove').show();
                        $btn.text('Replace');
                    });
                    frame.open();
                });

                $(document).on('click', '.brndle-avatar-remove', function (e) {
                    e.preventDefault();
                    var $btn = $(this);
                    var $field = $btn.closest('.brndle-avatar-field');
                    $field.find('input[type=hidden]').val('');
                    $field.find('.brndle-avatar-preview').attr('src', '').hide();
                    $btn.hide();
                    $field.find('.brndle-avatar-upload').text('Upload avatar');
                });
            })(jQuery);
        JS);
    }

    /**
     * Filter `get_avatar` / `pre_get_avatar_data` to return the local URL
     * when one is set on the user. Falls back to gravatar (or to the
     * gravatar default if `disable_gravatar_fallback` is on) when no
     * local avatar exists.
     *
     * The `$id_or_email` param can be a user ID, WP_User, WP_Post,
     * WP_Comment, or email string. Handle each case to find the user.
     *
     * @param  array $args
     * @param  mixed $idOrEmail
     * @return array
     */
    public static function filterAvatarData(array $args, $idOrEmail): array
    {
        $userId = self::resolveUserId($idOrEmail);

        if ($userId > 0) {
            $attachmentId = (int) get_user_meta($userId, self::META_KEY, true);
            if ($attachmentId > 0) {
                $size = (int) ($args['size'] ?? 96);
                // Pick the closest registered image size that's at least as
                // large as the requested render size.
                $imageSize = $size <= 96 ? 'thumbnail' : ($size <= 300 ? 'medium' : 'large');
                $url = wp_get_attachment_image_url($attachmentId, $imageSize);
                if ($url) {
                    $args['url'] = $url;
                    $args['found_avatar'] = true;
                    return $args;
                }
            }
        }

        // No local avatar — apply the gravatar-fallback setting.
        $disableGravatar = (bool) Settings::get('disable_gravatar_fallback', false);
        if ($disableGravatar) {
            // Replace with a 1x1 transparent gif so the slot stays the
            // same shape but no external request fires.
            $args['url'] = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
            $args['found_avatar'] = true;
        }

        return $args;
    }

    /**
     * Resolve the various shapes WP passes to avatar filters into a user ID.
     *
     * @param  mixed $idOrEmail
     * @return int
     */
    private static function resolveUserId($idOrEmail): int
    {
        if (is_numeric($idOrEmail)) {
            return (int) $idOrEmail;
        }
        if ($idOrEmail instanceof \WP_User) {
            return (int) $idOrEmail->ID;
        }
        if ($idOrEmail instanceof \WP_Post) {
            return (int) $idOrEmail->post_author;
        }
        if ($idOrEmail instanceof \WP_Comment) {
            return (int) $idOrEmail->user_id;
        }
        if (is_string($idOrEmail) && is_email($idOrEmail)) {
            $user = get_user_by('email', $idOrEmail);
            return $user ? (int) $user->ID : 0;
        }
        return 0;
    }
}
