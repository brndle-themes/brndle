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
        return apply_filters('brndle/blocks/attribute_migrations', self::builtIn());
    }

    /**
     * Migrations shipped with the theme. Filterable through all().
     *
     * @return array<string, array<int, callable(array<string, mixed>): array<string, mixed>>>
     */
    private static function builtIn(): array
    {
        return [
            'brndle/comparison-table' => [
                // The editor saves columns as { label, sublabel } and
                // rows as { feature, values }, but the docs described other
                // shapes, and content written from them rendered with empty
                // column headings and row labels. Accept every shape seen:
                // columns as plain strings or { name, price }, a `headers`
                // list (first entry labels the feature column), rows keyed
                // `label`.
                [self::class, 'comparisonTableShapes'],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $attrs
     * @return array<string, mixed>
     */
    public static function comparisonTableShapes(array $attrs): array
    {
        if (empty($attrs['columns']) && ! empty($attrs['headers']) && is_array($attrs['headers'])) {
            $attrs['columns'] = array_slice(array_values($attrs['headers']), 1);
        }
        // The same `headers` convention written into `columns`: a blank first
        // entry (the corner above the row labels) and one more column than
        // each row has values. Drop the corner; the highlight index counted
        // it, so it moves down one.
        $columns = isset($attrs['columns']) && is_array($attrs['columns']) ? array_values($attrs['columns']) : [];
        $valueCount = 0;
        foreach ((array) ($attrs['rows'] ?? []) as $row) {
            $valueCount = max($valueCount, is_array($row) ? count((array) ($row['values'] ?? [])) : 0);
        }
        if ($columns && $columns[0] === '' && count($columns) === $valueCount + 1) {
            $attrs['columns'] = array_slice($columns, 1);
            $highlight = (int) ($attrs['highlight_column'] ?? -1);
            if ($highlight > 0) {
                $attrs['highlight_column'] = $highlight - 1;
            } elseif ($highlight === 0) {
                $attrs['highlight_column'] = -1;
            }
        }
        if (isset($attrs['columns']) && is_array($attrs['columns'])) {
            $attrs['columns'] = array_map(static function ($col) {
                if (is_string($col) || is_numeric($col)) {
                    return ['label' => (string) $col, 'sublabel' => ''];
                }
                if (is_array($col) && ! isset($col['label']) && isset($col['name'])) {
                    $col['label'] = (string) $col['name'];
                    $col['sublabel'] = (string) ($col['sublabel'] ?? $col['price'] ?? '');
                }

                return $col;
            }, array_values($attrs['columns']));
        }
        if (isset($attrs['rows']) && is_array($attrs['rows'])) {
            $attrs['rows'] = array_map(static function ($row) {
                if (is_array($row) && ! isset($row['feature']) && isset($row['label'])) {
                    $row['feature'] = (string) $row['label'];
                }

                return $row;
            }, array_values($attrs['rows']));
        }

        return $attrs;
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
