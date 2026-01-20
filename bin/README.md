# Build Scripts

This directory contains scripts for building and releasing the noindex SEO plugin.

## build.sh

Creates a distributable ZIP package of the plugin ready for upload to WordPress.org or manual distribution.

### Usage

```bash
./bin/build.sh VERSION
```

### Example

```bash
./bin/build.sh 2.0.0
```

This will create `noindex-seo-2.0.0.zip` in the parent directory (`wp-content/plugins/`).

### What Gets Included

The build script includes only the files necessary for distribution:

✅ **Included:**
- `noindex-seo.php` - Main plugin file
- `uninstall.php` - Uninstall cleanup script
- `readme.txt` - WordPress.org plugin readme
- `changelog.txt` - Version history
- `LICENSE.txt` - Plugin license (if exists)
- `assets/` - CSS, JavaScript, and images
- `languages/` - Translation files (.pot, .po, .mo)

❌ **Excluded:**
- `.git/`, `.github/` - Git files and directories
- `vendor/` - Composer dependencies (development only)
- `docs/` - Documentation files
- `bin/` - Build scripts
- `tests/` - Test files
- `node_modules/` - NPM dependencies
- `composer.json`, `composer.lock` - Composer files
- `package.json`, `package-lock.json` - NPM files
- `phpcs.xml`, `.editorconfig` - Development configuration
- `*.md` - Markdown documentation files
- Hidden files (`.gitignore`, `.DS_Store`, etc.)
- Backup files (`*.bak`, `*~`, `*.log`)

### Features

- **Version Validation**: Ensures version follows semantic versioning (X.Y.Z)
- **Overwrite Protection**: Asks for confirmation if ZIP already exists
- **File Counting**: Reports number of files included
- **Size Reporting**: Shows final ZIP file size
- **Color Output**: Clear, colored status messages
- **Error Handling**: Stops on errors with clear messages

### Output

The script creates a ZIP file in the parent directory with the format:

```
noindex-seo-VERSION.zip
```

For example: `noindex-seo-2.0.0.zip`

### Requirements

- `bash` (v4.0+)
- `rsync`
- `zip`
- Unix-like environment (Linux, macOS, WSL)

### Before Building

Make sure to:

1. Update version in `noindex-seo.php` header
2. Update version in `readme.txt`
3. Update `CHANGELOG.md` and `changelog.txt`
4. Commit all changes
5. Tag the release in Git

### After Building

The generated ZIP file can be:

1. **Uploaded to WordPress.org** via SVN repository
2. **Distributed manually** to users
3. **Tested** in a clean WordPress installation
4. **Archived** for version control

### Example Workflow

```bash
# 1. Update version numbers
vim noindex-seo.php readme.txt

# 2. Update changelog
vim CHANGELOG.md changelog.txt

# 3. Commit changes
git add -A
git commit -m "Release version 2.0.0"

# 4. Tag release
git tag -a v2.0.0 -m "Version 2.0.0"

# 5. Build distributable package
./bin/build.sh 2.0.0

# 6. Upload to WordPress.org or distribute
```

### Troubleshooting

**"Invalid version format"**
- Ensure version follows X.Y.Z format (e.g., 2.0.0, 1.2.3)
- Don't include 'v' prefix

**"Main plugin file not found"**
- Ensure `noindex-seo.php` exists in plugin root
- Check file permissions

**"Permission denied"**
- Make script executable: `chmod +x bin/build.sh`

**"Command not found: rsync"**
- Install rsync: `apt-get install rsync` or `brew install rsync`

### Notes

- The script uses `rsync` for efficient file copying
- A temporary directory is created and cleaned up automatically
- The original plugin directory remains untouched
- Hidden files and development files are automatically excluded
