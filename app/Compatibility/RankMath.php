<?php

namespace Brndle\Compatibility;

class RankMath
{
    public static function boot(): void
    {
        add_filter('rank_math/snippet/rich_snippet_person_entity', [self::class, 'enrichPerson'], 10, 1);
        add_filter('rank_math/json_ld', [self::class, 'enrichGraph'], 20, 2);
    }

    /**
     * Direct hook for the Person entity. RankMath calls this whenever it
     * builds a standalone Person snippet (author archives, schema-generator
     * Person entries).
     */
    public static function enrichPerson(array $entity): array
    {
        $userId = self::resolveUserIdFromEntity($entity);
        if (! $userId) {
            return $entity;
        }

        return self::merge($entity, $userId);
    }

    /**
     * Walk the assembled JSON-LD graph and enrich every Person node that
     * we can confidently tie to a WP user.
     *
     * Important: this walker MUST NOT fall back to the post author when a
     * Person node carries no author identifier. Sites configured with a
     * "Person" knowledge graph (e.g. RankMath's `Titles & Meta → Author`
     * Person setup, or a brand entity at `/#person`) emit a Person node
     * for the site itself — enriching that node with the post author's
     * sameAs URLs pollutes the brand entity. Only enrich when the node's
     * `@id` or `url` clearly points at an author archive.
     *
     * @param array<string, mixed> $data
     */
    public static function enrichGraph(array $data, $jsonld = null): array
    {
        if (empty($data) || ! is_array($data)) {
            return $data;
        }

        foreach ($data as $key => $node) {
            if (! is_array($node) || ! self::isPersonNode($node)) {
                continue;
            }

            $userId = self::resolveAuthorUserIdFromEntity($node);
            if ($userId) {
                $data[$key] = self::merge($node, $userId);
            }
        }

        return $data;
    }

    private static function isPersonNode(array $node): bool
    {
        if (! isset($node['@type'])) {
            return false;
        }

        $type = $node['@type'];

        if (is_string($type)) {
            return $type === 'Person';
        }

        if (is_array($type)) {
            return in_array('Person', $type, true);
        }

        return false;
    }

    private static function merge(array $entity, int $userId): array
    {
        $existing = isset($entity['sameAs']) && is_array($entity['sameAs']) ? $entity['sameAs'] : [];
        $merged = array_values(array_filter(array_unique(array_merge($existing, PersonEnrichment::sameAs($userId)))));

        if ($merged) {
            $entity['sameAs'] = $merged;
        }

        $jobTitle = PersonEnrichment::jobTitle($userId);
        if ($jobTitle && empty($entity['jobTitle'])) {
            $entity['jobTitle'] = $jobTitle;
        }

        return $entity;
    }

    /**
     * Used by the dedicated `rank_math/snippet/rich_snippet_person_entity`
     * filter, which fires only when RankMath is rendering a standalone
     * Person snippet. In that context falling back to the singular post's
     * author or the queried author archive is correct — the snippet is
     * already scoped to a Person and the @id may be missing.
     */
    private static function resolveUserIdFromEntity(array $entity): ?int
    {
        $byUrl = self::resolveAuthorUserIdFromEntity($entity);
        if ($byUrl) {
            return $byUrl;
        }

        if (is_singular('post')) {
            $author = (int) get_post_field('post_author', get_the_ID());
            if ($author) {
                return $author;
            }
        }

        if (is_author()) {
            $obj = get_queried_object();
            if (is_object($obj) && isset($obj->ID)) {
                return (int) $obj->ID;
            }
        }

        return null;
    }

    /**
     * Strict resolver: only matches when the entity is unambiguously the
     * post author or an author archive. Used by the graph walker so the
     * site brand Person (e.g. `<home>/#person`) is left alone.
     *
     * Two patterns count:
     *   - `@id` / `url` contains `/author/<slug>/` → look up by slug.
     *   - `@id` ends in `#author` AND we're on a singular post →
     *     post author (this is RankMath's + Yoast's conventional shape
     *     for the per-post author Person node, e.g.
     *     `https://example.com/post-slug/#author`).
     */
    private static function resolveAuthorUserIdFromEntity(array $entity): ?int
    {
        foreach (['@id', 'url'] as $field) {
            if (empty($entity[$field]) || ! is_string($entity[$field])) {
                continue;
            }
            if (preg_match('#/author/([^/?#]+)/?#', $entity[$field], $m)) {
                $user = get_user_by('slug', $m[1]);
                if ($user) {
                    return (int) $user->ID;
                }
            }
        }

        if (! empty($entity['@id']) && is_string($entity['@id']) && str_ends_with($entity['@id'], '#author') && is_singular('post')) {
            $author = (int) get_post_field('post_author', get_the_ID());
            if ($author) {
                return $author;
            }
        }

        return null;
    }
}
