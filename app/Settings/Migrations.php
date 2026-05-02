<?php

/**
 * Settings migration registry.
 *
 * Each entry is keyed by the *target* schema version and runs as a pure
 * function over the stored settings array. Settings::migrate() iterates
 * the keys in order, applying any whose target is greater than the
 * stored `_version`, then writes the result back to the `brndle_settings`
 * option exactly once.
 *
 * Conventions for adding a migration:
 *   - Bump Settings::VERSION to match the new highest key here.
 *   - Migrations must be idempotent: running them twice produces the
 *     same result as running them once.
 *   - Migrations must not call out to network / DB / WP options apart
 *     from inspecting and mutating the array passed in.
 *   - Removed keys: don't drop them — leave the migration that renames
 *     them in place forever so old installs keep upgrading cleanly.
 *
 * Example future migration (commented to show the pattern, not active):
 *
 *   2 => function (array $saved): array {
 *       // 1 → 2: rename `perf_remove_global_styles` to
 *       // `perf_inline_only` and invert the meaning.
 *       if (array_key_exists('perf_remove_global_styles', $saved)) {
 *           $saved['perf_inline_only'] = ! $saved['perf_remove_global_styles'];
 *           unset($saved['perf_remove_global_styles']);
 *       }
 *       return $saved;
 *   },
 */

namespace Brndle\Settings;

class Migrations
{
    /**
     * Return every registered migration, keyed by target version.
     *
     * @return array<int, callable(array<string, mixed>): array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            // 0 → 1: initial schema. Acts as a fixed-point so the
            // migrator has something to apply on a fresh install.
            1 => static fn (array $saved): array => $saved,
        ];
    }

    /**
     * The highest target version any registered migration produces.
     * `Settings::VERSION` should match this so the migrator stops at
     * the right point.
     */
    public static function latestVersion(): int
    {
        $keys = array_keys(self::all());
        return $keys === [] ? 0 : (int) max($keys);
    }
}
