<?php

namespace Brndle\Compatibility;

class PersonEnrichment
{
    /**
     * Return the user's social URLs from Brndle local-avatar meta.
     *
     * Returns absolute, https-prefixed URLs only — anything that fails
     * `esc_url_raw` is dropped so we don't pollute the SEO plugin's
     * Person entity with garbage.
     *
     * @return array<int, string>
     */
    public static function sameAs(int $userId): array
    {
        $keys = ['_brndle_twitter', '_brndle_linkedin', '_brndle_github', '_brndle_website'];
        $urls = [];

        foreach ($keys as $key) {
            $value = (string) get_user_meta($userId, $key, true);
            if ($value === '') {
                continue;
            }

            $clean = esc_url_raw($value);
            if ($clean !== '') {
                $urls[] = $clean;
            }
        }

        /**
         * Filter the sameAs URLs Brndle contributes to the Person entity.
         *
         * Use this to add network URLs that aren't part of the local-avatar
         * meta (Mastodon, Bluesky, custom profile pages, etc.).
         *
         * @param array<int, string> $urls
         * @param int                $userId
         */
        return (array) apply_filters('brndle/schema_person_sameas', $urls, $userId);
    }

    /**
     * Return the user's role/title from `_brndle_role` user meta.
     *
     * The SEO plugin only adopts this value when it has no `jobTitle` of
     * its own. Override that with the `brndle/schema_person_role_overrides`
     * filter to push Brndle's value over an existing plugin value.
     */
    public static function jobTitle(int $userId): string
    {
        $role = trim((string) get_user_meta($userId, '_brndle_role', true));

        /**
         * Filter the jobTitle Brndle contributes to the Person entity.
         *
         * @param string $role
         * @param int    $userId
         */
        return (string) apply_filters('brndle/schema_person_jobtitle', $role, $userId);
    }
}
