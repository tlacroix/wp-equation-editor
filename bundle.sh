#!/bin/bash
#
# Bundle script for WordPress.org plugin submission
#
# Creates a clean copy of the plugin with only the files needed
# for distribution, excluding development files.
#
# Usage: ./bundle.sh
#

set -e

# Configuration
PLUGIN_SLUG="wp-equation-editor"
BUNDLE_DIR="wordpress-submission"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Building WordPress submission bundle...${NC}"

# Change to script directory
cd "$SCRIPT_DIR"

# Clean previous bundle
if [ -d "$BUNDLE_DIR" ]; then
    echo "Cleaning previous bundle..."
    rm -rf "$BUNDLE_DIR"
fi

# Create bundle directory
mkdir -p "$BUNDLE_DIR/$PLUGIN_SLUG"

echo "Copying plugin files..."

# Main plugin file
cp wp-equation-editor.php "$BUNDLE_DIR/$PLUGIN_SLUG/"

# Readme and uninstall
cp readme.txt "$BUNDLE_DIR/$PLUGIN_SLUG/"
cp uninstall.php "$BUNDLE_DIR/$PLUGIN_SLUG/"

# PHP includes
cp -r includes "$BUNDLE_DIR/$PLUGIN_SLUG/"

# Assets (JS/CSS source files)
cp -r assets "$BUNDLE_DIR/$PLUGIN_SLUG/"

# Build directory (compiled block assets)
cp -r build "$BUNDLE_DIR/$PLUGIN_SLUG/"

# Frontend script (plain JS, not compiled by webpack)
cp assets/blocks/equation/frontend.js "$BUNDLE_DIR/$PLUGIN_SLUG/build/blocks/equation/"

# KaTeX library (MIT License - see vendor/katex/LICENSE)
mkdir -p "$BUNDLE_DIR/$PLUGIN_SLUG/vendor/katex/fonts"
cp vendor/katex/katex.min.css "$BUNDLE_DIR/$PLUGIN_SLUG/vendor/katex/"
cp vendor/katex/katex.min.js "$BUNDLE_DIR/$PLUGIN_SLUG/vendor/katex/"
cp vendor/katex/LICENSE "$BUNDLE_DIR/$PLUGIN_SLUG/vendor/katex/"
cp vendor/katex/fonts/* "$BUNDLE_DIR/$PLUGIN_SLUG/vendor/katex/fonts/"

# Third-party Wiris editor
cp -r tiny_mce_wiris "$BUNDLE_DIR/$PLUGIN_SLUG/"

# Clean up unwanted files from the bundle
echo "Cleaning up development files..."

# Remove any .DS_Store files
find "$BUNDLE_DIR" -name ".DS_Store" -delete 2>/dev/null || true

# Remove any .gitkeep files
find "$BUNDLE_DIR" -name ".gitkeep" -delete 2>/dev/null || true

# Remove all hidden files (not allowed by WordPress)
find "$BUNDLE_DIR" -name ".*" -type f -delete 2>/dev/null || true
find "$BUNDLE_DIR" -name "*.swp" -delete 2>/dev/null || true
find "$BUNDLE_DIR" -name "*.dist" -delete 2>/dev/null || true

# Clear Wiris cache directory entirely
if [ -d "$BUNDLE_DIR/$PLUGIN_SLUG/tiny_mce_wiris/cache" ]; then
    rm -rf "$BUNDLE_DIR/$PLUGIN_SLUG/tiny_mce_wiris/cache"
fi

# Create zip file
echo "Creating zip archive..."
cd "$BUNDLE_DIR"
zip -r "$PLUGIN_SLUG.zip" "$PLUGIN_SLUG" -x "*.DS_Store" -x "*__MACOSX*"
cd "$SCRIPT_DIR"

# Calculate file sizes
FOLDER_SIZE=$(du -sh "$BUNDLE_DIR/$PLUGIN_SLUG" | cut -f1)
ZIP_SIZE=$(du -sh "$BUNDLE_DIR/$PLUGIN_SLUG.zip" | cut -f1)

echo ""
echo -e "${GREEN}Bundle created successfully!${NC}"
echo ""
echo "Bundle contents:"
echo "  Folder: $BUNDLE_DIR/$PLUGIN_SLUG/ ($FOLDER_SIZE)"
echo "  Zip:    $BUNDLE_DIR/$PLUGIN_SLUG.zip ($ZIP_SIZE)"
echo ""
echo "Files included:"
find "$BUNDLE_DIR/$PLUGIN_SLUG" -type f | wc -l | xargs echo "  Total files:"
echo ""
echo "Directory structure:"
find "$BUNDLE_DIR/$PLUGIN_SLUG" -type d | sed 's|'"$BUNDLE_DIR/$PLUGIN_SLUG"'|  .|'
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo "  1. Review the bundle contents in $BUNDLE_DIR/$PLUGIN_SLUG/"
echo "  2. Test the plugin from the bundle directory"
echo "  3. Upload $BUNDLE_DIR/$PLUGIN_SLUG.zip to WordPress.org"
