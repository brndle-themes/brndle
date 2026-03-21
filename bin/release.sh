#!/usr/bin/env bash
#
# Brndle Theme Release Builder
#
# Usage:
#   ./bin/release.sh           # builds brndle-0.1.0.zip
#   ./bin/release.sh 1.2.3     # builds brndle-1.2.3.zip
#
# Prerequisites:
#   - Node.js >= 20, npm
#   - PHP >= 8.2, Composer
#   - zip command
#
set -euo pipefail

THEME_DIR="$(cd "$(dirname "$0")/.." && pwd)"
THEME_SLUG="brndle"

# ── Version ──────────────────────────────────────────────────
VERSION="${1:-}"
if [ -z "$VERSION" ]; then
    VERSION=$(grep -oP 'Version:\s*\K[^\s]+' "$THEME_DIR/style.css")
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

# ── Step 1: Install dependencies ─────────────────────────────
echo "==> Installing Composer dependencies (production)..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

echo "==> Installing npm dependencies..."
npm ci --ignore-scripts 2>/dev/null || npm install

# ── Step 2: Build frontend assets ────────────────────────────
echo "==> Building Vite assets (public/build/)..."
npm run build

echo "==> Building admin panel (admin/build/)..."
npm run admin:build

echo "==> Building blocks (blocks/build/)..."
npm run blocks:build

# ── Step 3: Remove Vite hot file if present ──────────────────
rm -f "$THEME_DIR/public/hot"

# ── Step 4: Verify build artifacts ───────────────────────────
ERRORS=0

if [ ! -f "$THEME_DIR/public/build/manifest.json" ]; then
    echo "ERROR: public/build/manifest.json missing"
    ERRORS=$((ERRORS + 1))
fi

if [ ! -f "$THEME_DIR/public/build/assets/theme.json" ]; then
    echo "ERROR: public/build/assets/theme.json missing"
    ERRORS=$((ERRORS + 1))
fi

if [ ! -f "$THEME_DIR/admin/build/index.js" ]; then
    echo "ERROR: admin/build/index.js missing"
    ERRORS=$((ERRORS + 1))
fi

if [ ! -f "$THEME_DIR/blocks/build/index.js" ]; then
    echo "ERROR: blocks/build/index.js missing"
    ERRORS=$((ERRORS + 1))
fi

if [ ! -d "$THEME_DIR/vendor/roots/acorn" ]; then
    echo "ERROR: vendor/roots/acorn missing"
    ERRORS=$((ERRORS + 1))
fi

# Check all 8 block.json files exist
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

# ── Step 5: Update version in style.css if specified ─────────
if [ -n "${1:-}" ]; then
    sed -i.bak "s/^Version:.*/Version:            ${VERSION}/" "$THEME_DIR/style.css"
    rm -f "$THEME_DIR/style.css.bak"
    echo "==> Updated style.css version to ${VERSION}"
fi

# ── Step 6: Create zip ───────────────────────────────────────
BUILD_DIR=$(mktemp -d)
DIST_DIR="${BUILD_DIR}/${THEME_SLUG}"
ZIP_NAME="${THEME_SLUG}-${VERSION}.zip"
ZIP_PATH="${THEME_DIR}/${ZIP_NAME}"

echo "==> Assembling release in ${DIST_DIR}..."
mkdir -p "$DIST_DIR"

# Copy everything except exclusions
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
    --exclude='*.md' \
    --exclude='docs/' \
    --exclude='tests/' \
    --exclude='bin/' \
    --exclude='vite.config.js' \
    --exclude='package.json' \
    --exclude='package-lock.json' \
    --exclude='composer.json' \
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
    "$THEME_DIR/" "$DIST_DIR/"

echo "==> Creating ${ZIP_NAME}..."
cd "$BUILD_DIR"
zip -rq "$ZIP_PATH" "$THEME_SLUG"

# ── Step 7: Cleanup ──────────────────────────────────────────
rm -rf "$BUILD_DIR"

# ── Step 8: Report ───────────────────────────────────────────
ZIP_SIZE=$(du -sh "$ZIP_PATH" | cut -f1)
echo ""
echo "============================================"
echo "  Release: ${ZIP_NAME}"
echo "  Size:    ${ZIP_SIZE}"
echo "  Path:    ${ZIP_PATH}"
echo "============================================"
echo ""
echo "Contents include:"
echo "  - vendor/ (Acorn + dependencies)"
echo "  - public/build/ (Vite CSS/JS/theme.json)"
echo "  - admin/build/ (Settings panel)"
echo "  - blocks/build/ (Editor blocks)"
echo "  - blocks/*/block.json (8 blocks)"
echo "  - resources/views/ (Blade templates)"
echo "  - resources/images/ (Logos)"
echo "  - app/ (PHP application code)"
echo "  - style.css, functions.php, index.php, theme.json"
echo "  - screenshot.png"
echo ""
echo "Excluded:"
echo "  - node_modules, source JS/CSS, config files"
echo "  - docs, tests, build tooling"
echo ""
echo "==> Done! Upload ${ZIP_NAME} to WordPress."
