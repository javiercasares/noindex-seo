#!/usr/bin/env bash

#
# noindex SEO - Build Script
# Creates a distributable ZIP package of the plugin
#
# Usage: ./bin/build.sh VERSION
# Example: ./bin/build.sh 2.0.0
#

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Helper functions
error() {
	echo -e "${RED}Error: $1${NC}" >&2
	exit 1
}

success() {
	echo -e "${GREEN}✓ $1${NC}"
}

info() {
	echo -e "${BLUE}→ $1${NC}"
}

warning() {
	echo -e "${YELLOW}⚠ $1${NC}"
}

# Check if version parameter is provided
if [ -z "$1" ]; then
	error "Version parameter is required.\nUsage: ./bin/build.sh VERSION\nExample: ./bin/build.sh 2.0.0"
fi

VERSION="$1"

# Validate version format (semantic versioning)
if ! [[ "$VERSION" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
	error "Invalid version format. Expected: X.Y.Z (e.g., 2.0.0)"
fi

info "Building noindex SEO v${VERSION}..."

# Get the plugin directory (where this script is located)
PLUGIN_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PLUGIN_NAME="$(basename "$PLUGIN_DIR")"
PARENT_DIR="$(dirname "$PLUGIN_DIR")"

# Output file
OUTPUT_FILE="${PARENT_DIR}/${PLUGIN_NAME}-${VERSION}.zip"

# Check if output file already exists
if [ -f "$OUTPUT_FILE" ]; then
	warning "Output file already exists: ${OUTPUT_FILE}"
	read -p "Overwrite? (y/n) " -n 1 -r
	echo
	if [[ ! $REPLY =~ ^[Yy]$ ]]; then
		error "Build cancelled."
	fi
	rm -f "$OUTPUT_FILE"
fi

# Create temporary build directory
BUILD_DIR=$(mktemp -d)
BUILD_PLUGIN_DIR="${BUILD_DIR}/${PLUGIN_NAME}"

info "Creating temporary build directory..."
mkdir -p "$BUILD_PLUGIN_DIR"

# Copy plugin files to build directory
info "Copying plugin files..."
rsync -a \
	--exclude='.*' \
	--exclude='*.git*' \
	--exclude='node_modules' \
	--exclude='vendor' \
	--exclude='docs' \
	--exclude='bin' \
	--exclude='tests' \
	--exclude='.DS_Store' \
	--exclude='Thumbs.db' \
	--exclude='*.md' \
	--exclude='composer.json' \
	--exclude='composer.lock' \
	--exclude='package.json' \
	--exclude='package-lock.json' \
	--exclude='phpcs.xml' \
	--exclude='phpcs.xml.dist' \
	--exclude='.editorconfig' \
	--exclude='.phpcs.xml' \
	--exclude='*.log' \
	--exclude='*.bak' \
	--exclude='*~' \
	"$PLUGIN_DIR/" "$BUILD_PLUGIN_DIR/"

# Count files
FILE_COUNT=$(find "$BUILD_PLUGIN_DIR" -type f | wc -l)
success "Copied ${FILE_COUNT} files"

# List of files to keep (important documentation)
info "Included files:"
echo "  - noindex-seo.php (main plugin file)"
echo "  - uninstall.php"
echo "  - readme.txt"
echo "  - LICENSE.txt (if exists)"
echo "  - assets/ (CSS, JS, images)"
echo "  - languages/ (translations)"

# Verify main plugin file exists
if [ ! -f "$BUILD_PLUGIN_DIR/noindex-seo.php" ]; then
	rm -rf "$BUILD_DIR"
	error "Main plugin file not found: noindex-seo.php"
fi

# Verify readme.txt exists
if [ ! -f "$BUILD_PLUGIN_DIR/readme.txt" ]; then
	warning "readme.txt not found"
fi

# Create ZIP file
info "Creating ZIP archive..."
cd "$BUILD_DIR"
zip -r -q "$OUTPUT_FILE" "$PLUGIN_NAME"

# Clean up temporary directory
info "Cleaning up..."
rm -rf "$BUILD_DIR"

# Get file size
FILE_SIZE=$(du -h "$OUTPUT_FILE" | cut -f1)

# Final output
echo ""
echo "╔════════════════════════════════════════════════════════════╗"
echo "║              Build Completed Successfully!                 ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""
success "Package: $(basename "$OUTPUT_FILE")"
success "Version: ${VERSION}"
success "Size: ${FILE_SIZE}"
success "Location: ${OUTPUT_FILE}"
echo ""
info "You can now upload this file to WordPress.org or distribute it."
echo ""

exit 0
