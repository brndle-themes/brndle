<?php

/**
 * Block attribute migration registry.
 *
 * Brndle blocks are server-rendered (`save: () => null`), so attribute
 * shape changes don't trigger Gutenberg's "Invalid block" warning the
 * way client-rendered blocks do — but Blade templates would otherwise
 * accumulate `is_string($x) || is_array($x)` branches forever to handle
 * old saved data. This registry is the structural alternative.
 *
 * For each block, you register an ordered list of pure functions that
 * upgrade an old attribute shape to the current one. The
 * `BlockServiceProvider` render callback runs every applicable
 * migration before passing attributes to Blade — templates only ever
 * see the current shape.
 *
 * Conventions for adding a migration:
 *   - Migrations are pure: they take `$attrs` and return the new
 *     `$attrs`. No DB / network / option mutation.
 *   - Migrations must be idempotent: running them on already-current
 *     attributes is a no-op (use `array_key_exists()` guards).
 *   - Never delete an old migration — older saved posts may still
 *     hit it on first render.
 *
 * Worked example (commented to show the pattern, not active):
 *
 *   public static function all(): array
 *   {
 *       return [
 *           'brndle/logos' => [
 *               // 1.4.0: companies were a flat string array; promote
 *               // each string to a `{ name, url, id, alt }` object so
 *               // the template only handles the object form.
 *               static function (array $attrs): array {
 *                   if (! isset($attrs['companies']) || ! is_array($attrs['companies'])) {
 *                       return $attrs;
 *                   }
 *                   $attrs['companies'] = array_map(
 *                       fn ($c) => is_string($c)
 *                           ? ['name' => $c, 'url' => '', 'id' => 0, 'alt' => '']
 *                           : $c,
 *                       $attrs['companies']
 *                   );
 *                   return $attrs;
 *               },
 *           ],
 *       ];
 *   }
 *
 * After two majors, the template's old-shape branch can be deleted
 * with confidence: every saved post has been normalised on first
 * render.
 */

namespace Brndle\Blocks;

class AttributeMigrations
{
    /**
     * Map of block name → ordered array of migration functions.
     *
     * @return array<string, array<int, callable(array<string, mixed>): array<string, mixed>>>
     */
    public static function all(): array
    {
        return apply_filters('brndle/blocks/attribute_migrations', []);
    }

    /**
     * Run every registered migration for `$blockName` against `$attrs`,
     * in order. Returns the upgraded attribute array. No-op when no
     * migrations are registered for the block.
     *
     * @param  array<string, mixed>  $attrs
     * @return array<string, mixed>
     */
    public static function apply(string $blockName, array $attrs): array
    {
        $migrations = self::all()[$blockName] ?? [];
        foreach ($migrations as $migration) {
            $attrs = $migration($attrs);
        }
        return $attrs;
    }
}
