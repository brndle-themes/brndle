#!/usr/bin/env node
/**
 * Copy a curated list of Lucide SVGs from `node_modules/lucide-static/`
 * into `resources/icons/` so the `<x-icon>` Blade component can render
 * them at runtime without depending on `node_modules` (which the release
 * zip excludes).
 *
 * The curated list is the single source of truth for what icons brndle
 * exposes via `<x-icon>`. Adding an icon: append to ICONS, run
 * `npm run icons:copy`, commit the new SVG. Removing one: drop from
 * ICONS, delete the file.
 */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const ROOT = path.resolve(path.dirname(__filename), '..');
const SRC = path.join(ROOT, 'node_modules/lucide-static/icons');
const DEST = path.join(ROOT, 'resources/icons');

// Curated list. Keep alphabetised. Each entry is the kebab-case Lucide
// icon name. Browse https://lucide.dev/icons for the full catalogue.
const ICONS = [
    'alert-triangle',
    'arrow-right',
    'arrow-up',
    'check',
    'chevron-down',
    'chevron-right',
    'chevron-up',
    'circle',
    'clock',
    'code',
    'copy',
    'external-link',
    'home',
    'info',
    'plus',
    'quote',
    'search',
    'star',
    'x',
];

if (!fs.existsSync(SRC)) {
    console.error('ERROR: lucide-static is not installed. Run `npm install`.');
    process.exit(1);
}

fs.mkdirSync(DEST, { recursive: true });

let copied = 0;
let missing = 0;
for (const name of ICONS) {
    const sourceFile = path.join(SRC, `${name}.svg`);
    const destFile = path.join(DEST, `${name}.svg`);
    if (!fs.existsSync(sourceFile)) {
        console.error(`WARN: ${name} is not in lucide-static (rename or upstream removal?)`);
        missing++;
        continue;
    }
    fs.copyFileSync(sourceFile, destFile);
    copied++;
}

console.log(`Copied ${copied}/${ICONS.length} icons to ${path.relative(ROOT, DEST)}/`);
if (missing > 0) {
    process.exit(1);
}
