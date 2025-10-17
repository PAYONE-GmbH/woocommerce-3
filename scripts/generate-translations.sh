#!/bin/bash
# Generate JSON translation files for WooCommerce Blocks
# This script runs after webpack build to create language-specific JSON files

set -e

PLUGIN_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
LANG_DIR="$PLUGIN_DIR/lang"

# Script handle from PayoneBlocksSupport.php
SCRIPT_HANDLE="wc-payone-blocks-integration"

echo "🌍 Generating translation files..."
echo "  → Script handle: $SCRIPT_HANDLE"
echo ""

# Array of locales to process
LOCALES=("de_DE" "de_DE_formal" "de_CH" "de_CH_informal")

# Change to project root (where ddev can be executed)
cd "$PLUGIN_DIR/../../.."

# Check if we're in a DDEV environment
if command -v ddev &> /dev/null; then
    WP_CMD="ddev wp"
else
    WP_CMD="wp"
fi

# Clean up old JSON files (with MD5 hashes)
echo "  → Cleaning up old JSON files..."
find "$LANG_DIR" -name "payone-woocommerce-3-*-[a-f0-9][a-f0-9][a-f0-9][a-f0-9]*.json" -delete 2>/dev/null || true

# Generate JSON files for each locale
for locale in "${LOCALES[@]}"; do
    PO_FILE="wp-content/plugins/payone.woocommerce.plugin/lang/payone-woocommerce-3-${locale}.po"

    if [ -f "$PO_FILE" ]; then
        echo "  → Processing $locale..."

        # Generate JSON with wp i18n make-json
        $WP_CMD i18n make-json "$PO_FILE" wp-content/plugins/payone.woocommerce.plugin/lang --no-purge 2>&1 | grep -v "^Success: Created 0 files" || true

        # Find generated JSON file (with make-json hash)
        GENERATED_JSON=$(ls "$LANG_DIR"/payone-woocommerce-3-${locale}-*.json 2>/dev/null | grep -v "$SCRIPT_HANDLE" | head -1)

        if [ -n "$GENERATED_JSON" ] && [ -f "$GENERATED_JSON" ]; then
            # Create version with script handle (WordPress prefers this over MD5)
            TARGET_JSON="$LANG_DIR/payone-woocommerce-3-${locale}-${SCRIPT_HANDLE}.json"
            mv "$GENERATED_JSON" "$TARGET_JSON"
            echo "    ✓ Created: payone-woocommerce-3-${locale}-${SCRIPT_HANDLE}.json"
        fi
    else
        echo "  ⚠ Skipping $locale (PO file not found)"
    fi
done

echo ""
echo "✅ Translation generation complete!"
echo ""
echo "Generated JSON files with script handle ($SCRIPT_HANDLE):"
ls -lh "$LANG_DIR"/*-${SCRIPT_HANDLE}.json 2>/dev/null || echo "  (No JSON files found)"
