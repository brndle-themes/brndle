#!/usr/bin/env bash
#
# Compare brndle's key framework deps against the upstream roots/sage repo.
#
# Usage:
#   ./bin/check-upstream.sh
#
# Shows a side-by-side of acorn / vite / laravel-vite-plugin / @roots/vite-plugin /
# PHP version requirements, plus the most recent sage release tag and the last
# 5 commits to roots/sage main. Use this before starting framework upgrades.
#
# Requires: gh (authenticated for the API to avoid rate limits) and jq.
#
set -euo pipefail

THEME_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$THEME_DIR"

if ! command -v gh >/dev/null 2>&1; then
    echo "ERROR: 'gh' CLI required. brew install gh"
    exit 1
fi

echo "==> Brndle vs roots/sage upstream"
echo

# Pull the upstream package.json + composer.json fresh.
SAGE_PKG=$(gh api 'repos/roots/sage/contents/package.json' --jq '.content' 2>/dev/null | base64 -d 2>/dev/null || echo '{}')
SAGE_COMPOSER=$(gh api 'repos/roots/sage/contents/composer.json' --jq '.content' 2>/dev/null | base64 -d 2>/dev/null || echo '{}')

# Read local versions via jq (portable, avoids BSD/GNU sed differences).
local_acorn=$(jq -r '.require["roots/acorn"] // "unknown"' composer.json)
local_php=$(jq -r '.require.php // "unknown"' composer.json)
local_vite=$(jq -r '.devDependencies.vite // "unknown"' package.json)
local_lvp=$(jq -r '.devDependencies["laravel-vite-plugin"] // "unknown"' package.json)
local_rvp=$(jq -r '.devDependencies["@roots/vite-plugin"] // "unknown"' package.json)

upstream_acorn=$(echo "$SAGE_COMPOSER" | jq -r '.require["roots/acorn"] // "unknown"')
upstream_php=$(echo "$SAGE_COMPOSER" | jq -r '.require.php // "unknown"')
upstream_vite=$(echo "$SAGE_PKG" | jq -r '.devDependencies.vite // "unknown"')
upstream_lvp=$(echo "$SAGE_PKG" | jq -r '.devDependencies["laravel-vite-plugin"] // "unknown"')
upstream_rvp=$(echo "$SAGE_PKG" | jq -r '.devDependencies["@roots/vite-plugin"] // "unknown"')

printf '%-26s %-22s %-22s\n' 'dep' 'brndle' 'sage main'
printf '%-26s %-22s %-22s\n' '---' '------' '---------'
printf '%-26s %-22s %-22s\n' 'php'                "$local_php"   "$upstream_php"
printf '%-26s %-22s %-22s\n' 'roots/acorn'        "$local_acorn" "$upstream_acorn"
printf '%-26s %-22s %-22s\n' 'vite'               "$local_vite"  "$upstream_vite"
printf '%-26s %-22s %-22s\n' 'laravel-vite-plugin' "$local_lvp"  "$upstream_lvp"
printf '%-26s %-22s %-22s\n' '@roots/vite-plugin' "$local_rvp"   "$upstream_rvp"

echo
echo "==> Latest sage release"
gh release view --repo roots/sage --json tagName,publishedAt,name --jq '"\(.tagName) (\(.publishedAt[:10])) — \(.name)"' 2>/dev/null || echo '(could not fetch release)'

echo
echo "==> Last 5 commits to roots/sage main"
gh api 'repos/roots/sage/commits?per_page=5' --jq '.[] | "\(.commit.author.date[:10])  \(.commit.message | split("\n")[0])"' 2>/dev/null || echo '(could not fetch commits)'

echo
echo "Done. See CLAUDE.md → Upstream tracking for upgrade rationale."
