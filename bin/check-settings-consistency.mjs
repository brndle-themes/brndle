#!/usr/bin/env node
/**
 * Settings consistency guardrail.
 *
 * Brndle has three places that must agree on the list of settings:
 *
 *   1. `app/Settings/Defaults.php`        — canonical keys + defaults + type lists
 *   2. `admin/src/tabs/*.jsx`             — admin form fields
 *   3. `app/**\/*.php` + `resources/**\/*.{blade.php,php}` — runtime consumers
 *
 * When the three drift, an admin can move a slider that does nothing
 * (1.3.1 caught two of these: `font_size_base`, `heading_scale`).
 *
 * This script asserts:
 *   - every Defaults key has at least one admin-tab field reference
 *   - every Defaults key has at least one PHP / Blade consumer
 *
 * Exits non-zero with a precise list of offenders so the PR check
 * surfaces the gap before merge. Implementation is purposely
 * dependency-free: regex on plain text. False positives are accepted
 * over heavyweight AST parsing — false negatives (a key consumed only
 * via dynamic property access) are flagged in the README so reviewers
 * know to override consciously.
 */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const ROOT = path.resolve(__dirname, '..');

// Keys defined here may legitimately have no consumer (for example, a
// setting only used by an external plugin). Edit consciously.
const ALLOW_NO_CONSUMER = new Set([
    // (none today — every key currently has a consumer)
]);

// Keys defined here may legitimately have no admin field (for example,
// hidden internal flags). Edit consciously.
const ALLOW_NO_ADMIN = new Set([
    // (none today)
]);

function readFile(file) {
    return fs.readFileSync(file, 'utf8');
}

function* walk(dir, predicate) {
    if (!fs.existsSync(dir)) return;
    for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
        const full = path.join(dir, entry.name);
        if (entry.isDirectory()) {
            if (entry.name === 'node_modules' || entry.name === 'vendor' ||
                entry.name === 'build' || entry.name.startsWith('.')) continue;
            yield* walk(full, predicate);
        } else if (predicate(entry.name)) {
            yield full;
        }
    }
}

// 1. Pull canonical keys from Defaults.php — first-level array keys only.
//    `which` selects which top-level array we extract from: $defaults
//    inside all() vs $schema inside schema(). Both share the depth-aware
//    parse + trailing-line flush.
function extractKeysFromArray(varName, fnName) {
    const text = readFile(path.join(ROOT, 'app/Settings/Defaults.php'));
    const re = new RegExp(
        `public static function ${fnName}\\(\\): array\\s*\\{[\\s\\S]*?\\$${varName} = \\[([\\s\\S]*?)\\n\\s*\\];`
    );
    const match = text.match(re);
    if (!match) {
        throw new Error(`Could not locate $${varName} array in Defaults::${fnName}().`);
    }
    const body = match[1];
    const keys = new Set();
    let depth = 0;
    let buffer = '';
    const flush = () => {
        const line = buffer.trim();
        const m = line.match(/^['"]([a-z_][a-z0-9_]*)['"]\s*=>/);
        if (m) keys.add(m[1]);
        buffer = '';
    };
    for (const ch of body) {
        if (ch === '[') depth++;
        else if (ch === ']') depth--;
        if (depth === 0 && ch === '\n') {
            flush();
        } else {
            buffer += ch;
        }
    }
    // Trailing line (no terminating \n captured by the outer regex).
    flush();
    return keys;
}

function extractDefaultsKeys() {
    return extractKeysFromArray('defaults', 'all');
}

function extractSchemaKeys() {
    return extractKeysFromArray('schema', 'schema');
}

// 2. Scan admin/src/tabs for `settings.<key>` and `settings['<key>']` references.
function extractAdminKeys() {
    const keys = new Set();
    for (const file of walk(path.join(ROOT, 'admin/src/tabs'), (n) => n.endsWith('.jsx') || n.endsWith('.js'))) {
        const text = readFile(file);
        for (const m of text.matchAll(/settings\.([a-z_][a-z0-9_]*)/g)) keys.add(m[1]);
        for (const m of text.matchAll(/settings\[['"]([a-z_][a-z0-9_]*)['"]\]/g)) keys.add(m[1]);
        for (const m of text.matchAll(/onChange\(\s*['"]([a-z_][a-z0-9_]*)['"]/g)) keys.add(m[1]);
    }
    return keys;
}

// 3. Scan PHP + Blade for any key reference: Settings::get('foo'),
//    $all['foo'], $settings['foo'], or just the bare 'foo' string in
//    Sanitizer/Settings (broader net to avoid false negatives).
function extractConsumerKeys(canonical) {
    const seen = new Set();
    const haystack = [];

    for (const file of walk(path.join(ROOT, 'app'), (n) => n.endsWith('.php'))) {
        // Don't count Defaults.php / Sanitizer.php as consumers — they're
        // the schema layer, not the runtime layer.
        const base = path.basename(file);
        if (base === 'Defaults.php' || base === 'Sanitizer.php') continue;
        haystack.push(readFile(file));
    }
    for (const file of walk(path.join(ROOT, 'resources'), (n) => n.endsWith('.blade.php') || n.endsWith('.php'))) {
        haystack.push(readFile(file));
    }

    const blob = haystack.join('\n');
    for (const key of canonical) {
        const re = new RegExp(`['"]${key}['"]`);
        if (re.test(blob)) seen.add(key);
    }
    return seen;
}

function main() {
    const defaults = extractDefaultsKeys();
    const schema = extractSchemaKeys();
    const adminFields = extractAdminKeys();
    const consumers = extractConsumerKeys(defaults);

    const orphansFromAdmin = [...defaults].filter(
        (k) => !adminFields.has(k) && !ALLOW_NO_ADMIN.has(k)
    );
    const orphansFromConsumers = [...defaults].filter(
        (k) => !consumers.has(k) && !ALLOW_NO_CONSUMER.has(k)
    );
    const orphansFromSchema = [...defaults].filter((k) => !schema.has(k));
    const ghostsInAdmin = [...adminFields].filter((k) => !defaults.has(k));
    const ghostsInSchema = [...schema].filter((k) => !defaults.has(k));

    let failed = false;

    console.log(`Defaults: ${defaults.size} keys`);
    console.log(`Schema metadata: ${schema.size} keys`);
    console.log(`Admin tab fields: ${adminFields.size} unique keys`);
    console.log(`Code consumers found: ${consumers.size} keys`);
    console.log('');

    if (orphansFromAdmin.length) {
        failed = true;
        console.log('❌ Defaults keys with no admin tab field:');
        for (const k of orphansFromAdmin) console.log(`   - ${k}`);
        console.log('   (Add the field to admin/src/tabs/*.jsx or list in ALLOW_NO_ADMIN.)');
        console.log('');
    }

    if (orphansFromConsumers.length) {
        failed = true;
        console.log('❌ Defaults keys with no PHP/Blade consumer:');
        for (const k of orphansFromConsumers) console.log(`   - ${k}`);
        console.log('   (Add a Settings::get/$all["..."] reference, or list in ALLOW_NO_CONSUMER.)');
        console.log('');
    }

    if (ghostsInAdmin.length) {
        failed = true;
        console.log('Admin tab fields not declared in Defaults.php:');
        for (const k of ghostsInAdmin) console.log(`   - ${k}`);
        console.log('   (Add the key + default + type list to Defaults.php.)');
        console.log('');
    }

    if (orphansFromSchema.length) {
        failed = true;
        console.log('Defaults keys with no schema() metadata entry:');
        for (const k of orphansFromSchema) console.log(`   - ${k}`);
        console.log('   (Add a metadata entry to Defaults::schema() — section, label, control, etc.)');
        console.log('');
    }

    if (ghostsInSchema.length) {
        failed = true;
        console.log('schema() entries not declared in all():');
        for (const k of ghostsInSchema) console.log(`   - ${k}`);
        console.log('   (Add the key + default to Defaults::all().)');
        console.log('');
    }

    if (!failed) {
        console.log('All settings consistent across Defaults (all + schema) / admin tabs / consumers.');
        process.exit(0);
    }

    process.exit(1);
}

main();
