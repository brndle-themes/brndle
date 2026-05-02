<?php

/**
 * Blade compile dry-run.
 *
 * Walks every `resources/views/**\/*.blade.php` template, compiles it
 * via Acorn's `BladeCompiler::compileString()`, then validates the
 * compiled PHP with `token_get_all($source, TOKEN_PARSE)` — which
 * throws `ParseError` on malformed output even when the Blade compiler
 * itself didn't object.
 *
 * Catches the class of bug that crashed `comparison-table.blade.php`
 * in 1.3.0 (`@php($expr)` containing `===` produced invalid PHP that
 * BladeCompiler emitted without complaining; only PHP itself rejected
 * it at include time, deep in a render path).
 *
 * Pure PHP — no shell exec, no booting WordPress. Runs after
 * `composer install --no-dev`.
 *
 * Usage:
 *   php bin/check-blade-compile.php
 *
 * Exits 0 on success, 1 with a list of (template, error) on failure.
 */

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;

$themeRoot = realpath(__DIR__ . '/..');
$viewsRoot = "{$themeRoot}/resources/views";
$cacheRoot = sys_get_temp_dir() . '/brndle-blade-check-' . getmypid();

if (! is_dir($viewsRoot)) {
    fwrite(STDERR, "ERROR: resources/views/ not found at {$viewsRoot}\n");
    exit(1);
}

if (! is_dir($cacheRoot)) {
    mkdir($cacheRoot, 0775, true);
}

$filesystem = new Filesystem;
$compiler = new BladeCompiler($filesystem, $cacheRoot);

$failures = [];
$skipped = [];
$count = 0;

$templates = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($viewsRoot, RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($templates as $template) {
    if (! $template->isFile()) {
        continue;
    }
    if (! str_ends_with($template->getFilename(), '.blade.php')) {
        continue;
    }

    $count++;
    $relative = substr($template->getPathname(), strlen($themeRoot) + 1);
    $source = $filesystem->get($template->getPathname());

    // Component tags (`<x-foo>`) need the full Acorn container's view
    // factory to compile — bootstrapping that here would mean booting
    // WordPress, which defeats the dry-run purpose. Components are
    // exercised by the E2E journey at runtime instead. Skip + report.
    if (preg_match('/<x-[a-z][a-z0-9-]*\b/', $source)) {
        $skipped[] = $relative;
        continue;
    }

    try {
        $compiled = $compiler->compileString($source);
    } catch (Throwable $e) {
        $failures[] = ['template' => $relative, 'where' => 'Blade compile', 'error' => $e->getMessage()];
        continue;
    }

    // token_get_all with TOKEN_PARSE throws ParseError on invalid PHP —
    // exactly the comparison-table bug shape (compiler emitted bad PHP
    // without throwing).
    try {
        token_get_all($compiled, TOKEN_PARSE);
    } catch (ParseError $e) {
        $failures[] = [
            'template' => $relative,
            'where' => 'compiled-PHP parse',
            'error' => $e->getMessage() . ' on line ' . $e->getLine(),
        ];
    }
}

@rmdir($cacheRoot);

if (! empty($failures)) {
    fwrite(STDERR, 'Blade compile dry-run failed for ' . count($failures) . " of {$count} templates:\n\n");
    foreach ($failures as $failure) {
        fwrite(STDERR, '  ' . $failure['template'] . "\n");
        fwrite(STDERR, '    [' . $failure['where'] . '] ' . preg_replace('/\s+/', ' ', $failure['error']) . "\n\n");
    }
    exit(1);
}

$checked = $count - count($skipped);
echo "All {$checked} Blade templates compile to valid PHP.\n";
if (! empty($skipped)) {
    echo "Skipped " . count($skipped) . " templates that use Acorn components (compile path needs the full container; covered by the E2E journey instead):\n";
    foreach ($skipped as $s) {
        echo "  {$s}\n";
    }
}
