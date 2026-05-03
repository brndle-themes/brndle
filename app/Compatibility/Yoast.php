<?php

namespace Brndle\Compatibility;

class Yoast
{
    public static function boot(): void
    {
        add_action('after_setup_theme', function () {
            if (function_exists('yoast_breadcrumb') || function_exists('rank_math_the_breadcrumbs')) {
                add_theme_support('yoast-seo-breadcrumbs');
            }
        });

        add_filter('wpseo_schema_person', [self::class, 'enrichPerson'], 10, 2);
    }

    /**
     * Enrich Yoast's Person schema with Brndle local-avatar social URLs.
     *
     * Hook contract: Yoast passes a complete Person entity. We append our
     * social URLs to `sameAs` (de-duplicated) and fill `jobTitle` from
     * `_brndle_role` only when empty (filterable).
     */
    public static function enrichPerson(array $data, $context = null): array
    {
        $userId = self::resolveUserId($data, $context);

        if (! $userId) {
            return $data;
        }

        $sameAs = isset($data['sameAs']) && is_array($data['sameAs']) ? $data['sameAs'] : [];
        $sameAs = array_values(array_filter(array_unique(array_merge($sameAs, PersonEnrichment::sameAs($userId)))));

        if ($sameAs) {
            $data['sameAs'] = $sameAs;
        }

        $jobTitle = PersonEnrichment::jobTitle($userId);
        if ($jobTitle && empty($data['jobTitle'])) {
            $data['jobTitle'] = $jobTitle;
        }

        return $data;
    }

    /**
     * Identify the WP user this Person entity belongs to.
     *
     * Yoast usually passes a `$context` object with `->id` set to the
     * user ID — that's the authoritative path. When `$context` is empty
     * (older Yoast versions, or filter calls outside the schema graph),
     * fall back to matching the entity's `@id` / `url` to an
     * `/author/<slug>/` archive. We deliberately do NOT fall back to the
     * current post's author: a Yoast graph can carry a brand Person
     * entity at `/#person` whose @id has no author slug, and enriching
     * that node with the author's social URLs corrupts the brand entity.
     */
    private static function resolveUserId(array $data, $context): ?int
    {
        if (is_object($context) && isset($context->id) && is_numeric($context->id)) {
            return (int) $context->id;
        }

        foreach (['@id', 'url'] as $field) {
            if (empty($data[$field]) || ! is_string($data[$field])) {
                continue;
            }
            if (preg_match('#/author/([^/?#]+)/?#', $data[$field], $m)) {
                $user = get_user_by('slug', $m[1]);
                if ($user) {
                    return (int) $user->ID;
                }
            }
        }

        if (! empty($data['@id']) && is_string($data['@id']) && str_ends_with($data['@id'], '#author') && is_singular('post')) {
            $author = (int) get_post_field('post_author', get_the_ID());
            if ($author) {
                return $author;
            }
        }

        return null;
    }
}
