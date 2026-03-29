#!/usr/bin/env bash
#
# Brndle Theme Release Builder
#
# Usage:
#   ./bin/release.sh 1.0.0    # builds brndle-1.0.0.zip
#   ./bin/release.sh           # uses version from style.css
#
# What it does:
#   1. Installs production dependencies (Composer + npm)
#   2. Builds all assets (frontend, admin, blocks)
#   3. Generates .pot translation file
#   4. Bumps version in style.css
#   5. Creates a clean distribution zip
#
set -euo pipefail

THEME_DIR="$(cd "$(dirname "$0")/.." && pwd)"
THEME_SLUG="brndle"

# ── Version ──────────────────────────────────────────────────
VERSION="${1:-}"
if [ -z "$VERSION" ]; then
    VERSION=$(grep 'Version:' "$THEME_DIR/style.css" | head -1 | sed 's/.*Version:[[:space:]]*//' | tr -d '[:space:]')
fi
echo "==> Building ${THEME_SLUG} v${VERSION}"

# ── Pre-flight checks ────────────────────────────────────────
command -v node >/dev/null 2>&1 || { echo "ERROR: node not found"; exit 1; }
command -v composer >/dev/null 2>&1 || { echo "ERROR: composer not found"; exit 1; }
command -v zip >/dev/null 2>&1 || { echo "ERROR: zip not found"; exit 1; }

NODE_VER=$(node -v | sed 's/v//' | cut -d. -f1)
if [ "$NODE_VER" -lt 20 ]; then
    echo "ERROR: Node.js >= 20 required (found v${NODE_VER})"
    exit 1
fi

PHP_VER=$(php -r 'echo PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION;')
echo "==> Node $(node -v), PHP ${PHP_VER}, Composer $(composer --version --short 2>/dev/null || echo 'unknown')"

cd "$THEME_DIR"

# ── Step 1: Update version ───────────────────────────────────
if [ -n "${1:-}" ]; then
    sed -i.bak "s/^Version:.*/Version:            ${VERSION}/" "$THEME_DIR/style.css"
    rm -f "$THEME_DIR/style.css.bak"
    echo "==> Updated style.css version to ${VERSION}"
fi

# ── Step 2: Install dependencies ─────────────────────────────
echo "==> Installing Composer dependencies (production)..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

echo "==> Installing npm dependencies..."
npm ci --ignore-scripts 2>/dev/null || npm install

# ── Step 3: Build all assets ─────────────────────────────────
echo "==> Building Vite assets (frontend CSS/JS)..."
npm run build

echo "==> Building admin panel (React)..."
npm run admin:build

echo "==> Building editor blocks (React)..."
npm run blocks:build

# ── Step 4: Generate .pot file ───────────────────────────────
echo "==> Generating translation file..."
mkdir -p "$THEME_DIR/resources/lang"
if command -v wp >/dev/null 2>&1; then
    wp i18n make-pot "$THEME_DIR" "$THEME_DIR/resources/lang/brndle.pot" \
        --slug=brndle \
        --domain=brndle \
        --include="theme.json,blocks,app,resources" \
        --skip-audit \
        2>/dev/null && echo "==> .pot file generated" || echo "==> WARN: wp i18n failed, skipping .pot"
else
    echo "==> WARN: WP-CLI not found, skipping .pot generation"
    echo "   Install WP-CLI and run: wp i18n make-pot . resources/lang/brndle.pot"
fi

# ── Step 5: Remove dev artifacts ─────────────────────────────
rm -f "$THEME_DIR/public/hot"

# ── Step 6: Verify build artifacts ───────────────────────────
ERRORS=0

for required in \
    "public/build/manifest.json" \
    "public/build/assets/theme.json" \
    "admin/build/index.js" \
    "blocks/build/index.js" \
    "vendor/roots/acorn" \
    "style.css" \
    "functions.php" \
    "index.php" \
    "screenshot.png" \
    "LICENSE"; do
    if [ ! -e "$THEME_DIR/$required" ]; then
        echo "ERROR: ${required} missing"
        ERRORS=$((ERRORS + 1))
    fi
done

# Check all 8 block.json files
for block in hero logos stats features testimonials pricing cta faq; do
    if [ ! -f "$THEME_DIR/blocks/${block}/block.json" ]; then
        echo "ERROR: blocks/${block}/block.json missing"
        ERRORS=$((ERRORS + 1))
    fi
done

if [ "$ERRORS" -gt 0 ]; then
    echo "==> ${ERRORS} error(s) found. Aborting."
    exit 1
fi
echo "==> All build artifacts verified."

# ── Step 7: Create distribution zip ──────────────────────────
BUILD_DIR=$(mktemp -d)
DIST_DIR="${BUILD_DIR}/${THEME_SLUG}"
ZIP_NAME="${THEME_SLUG}-${VERSION}.zip"
ZIP_PATH="${THEME_DIR}/${ZIP_NAME}"

echo "==> Assembling release in ${DIST_DIR}..."
mkdir -p "$DIST_DIR"

rsync -a \
    --exclude='node_modules' \
    --exclude='.git' \
    --exclude='.github' \
    --exclude='.env' \
    --exclude='.gitignore' \
    --exclude='.editorconfig' \
    --exclude='.eslintrc*' \
    --exclude='.prettierrc*' \
    --exclude='.phpunit*' \
    --exclude='phpunit.xml*' \
    --exclude='pint.json' \
    --exclude='CLAUDE.md' \
    --exclude='.claude/' \
    --exclude='docs/' \
    --exclude='tests/' \
    --exclude='bin/' \
    --exclude='vite.config.js' \
    --exclude='package.json' \
    --exclude='package-lock.json' \
    --exclude='composer.lock' \
    --exclude='admin/src/' \
    --exclude='admin/webpack.config.cjs' \
    --exclude='blocks/src/' \
    --exclude='blocks/webpack.config.cjs' \
    --exclude='resources/js/' \
    --exclude='resources/css/' \
    --exclude='public/hot' \
    --exclude='*.map' \
    --exclude='*.bak' \
    --exclude="${ZIP_NAME}" \
    --exclude='brndle-*.zip' \
    "$THEME_DIR/" "$DIST_DIR/"

echo "==> Creating ${ZIP_NAME}..."
cd "$BUILD_DIR"
zip -rq "$ZIP_PATH" "$THEME_SLUG"

# ── Step 8: Cleanup ──────────────────────────────────────────
rm -rf "$BUILD_DIR"

# ── Step 9: Report ───────────────────────────────────────────
ZIP_SIZE=$(du -sh "$ZIP_PATH" | cut -f1)
FILE_COUNT=$(zipinfo -t "$ZIP_PATH" 2>/dev/null | grep -o '[0-9]* files' || echo "unknown")
echo ""
echo "============================================"
echo "  Brndle v${VERSION}"
echo "  File: ${ZIP_NAME}"
echo "  Size: ${ZIP_SIZE} (${FILE_COUNT})"
echo "  Path: ${ZIP_PATH}"
echo "============================================"
echo ""
echo "  Included:"
echo "    app/            PHP application code"
echo "    admin/build/    Settings panel (compiled)"
echo "    blocks/         Block definitions + compiled JS"
echo "    public/build/   Frontend CSS/JS (compiled)"
echo "    resources/      Blade templates, images, translations"
echo "    vendor/         Acorn + PHP dependencies"
echo "    style.css       Theme header (v${VERSION})"
echo "    LICENSE          GPL-2.0"
echo "    README.md        Documentation"
echo ""
echo "  Excluded:"
echo "    Source JS/CSS, node_modules, config files,"
echo "    build tooling, tests, CLAUDE.md"
echo ""
echo "==> Upload ${ZIP_NAME} to any WordPress site."
