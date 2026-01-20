# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Plugin Overview

This is a WordPress plugin that adds `noindex` meta tags to specific parts of a WordPress site based on user configuration. The plugin allows site administrators to prevent search engines from indexing various types of content such as archives, pagination, search results, attachment pages, and more.

**Key Details:**
- Plugin supports WordPress 6.6 - 6.9 and PHP 7.2 - 8.5
- Uses native WordPress `wp_robots` filter (available since WP 5.7)
- Single main file architecture (`noindex-seo.php`)
- Options stored in WordPress options table with transient caching
- Text domain: `noindex-seo`

## Development Commands

### Code Quality & Standards

Run PHP_CodeSniffer with WordPress Coding Standards:
```bash
composer require-dev
vendor/bin/phpcs --standard=WordPress noindex-seo.php
```

Check PHP compatibility:
```bash
vendor/bin/phpcs -p noindex-seo.php --standard=PHPCompatibility --runtime-set testVersion 7.2-
```

Check WordPress coding standards with specific ruleset:
```bash
vendor/bin/phpcs -p noindex-seo.php --standard=WordPress-Extra
```

### Dependencies

Install Composer dependencies (for development):
```bash
composer install
```

Production dependencies include:
- `symfony/http-client`
- `nyholm/psr7`
- `auth0/wordpress`

Development dependencies include:
- `squizlabs/php_codesniffer`
- `wp-coding-standards/wpcs`
- `phpcompatibility/php-compatibility`
- `phpcompatibility/phpcompatibility-wp`
- `eduardovillao/wp-since`

### Localization

Generate POT file for translations:
```bash
wp i18n make-pot . languages/noindex-seo.pot
```

The plugin is translation-ready with text domain `noindex-seo` and domain path `/languages`.

### Build & Release

Create a distributable ZIP package:
```bash
./bin/build.sh VERSION
```

Example:
```bash
./bin/build.sh 2.0.0
```

This creates `noindex-seo-VERSION.zip` in the parent directory with only necessary files:
- Excludes: `.git/`, `vendor/`, `docs/`, `bin/`, `*.md`, development configs
- Includes: plugin files, `assets/`, `languages/`, `readme.txt`

See `bin/README.md` for detailed documentation.

## Architecture

### Main Components

**Core Functions:**

1. `noindex_seo_show()` - Main controller that evaluates page context and applies noindex based on settings
   - Runs on `template_redirect` hook
   - Uses transient caching (`noindex_seo_options`) for performance
   - Supports filter hook `noindex_seo_contexts` for customization

2. `noindex_seo_metarobots()` - Outputs the noindex directive
   - Uses `wp_robots` filter (WP 5.7+) or falls back to raw meta tag
   - Called when conditions match

3. `noindex_seo_admin()` - Renders the settings page
   - Located at Settings → noindex SEO
   - Organizes options by sections (Main Pages, Archives, Taxonomies, etc.)

4. `noindex_seo_process_form()` - Handles form submission
   - Hooked to `admin_post_update_noindex_seo`
   - Validates nonce and user capabilities
   - Clears transient cache on save

5. `noindex_seo_detect_conflicts()` - Checks for conflicting SEO plugins
   - Displays admin notice if plugins like Yoast SEO, Rank Math, etc. are active
   - Can be suppressed via `noindex_seo_config_seoplugins` option

### Settings Storage

All settings are stored as individual WordPress options with prefix `noindex_seo_`:
- Format: `noindex_seo_{context}` (e.g., `noindex_seo_archive`, `noindex_seo_search`)
- Values: integer (0 or 1)
- Cached in transient `noindex_seo_options` for 1 hour

### Supported Contexts

The plugin supports noindex for these WordPress conditional tags:
- `is_single()`, `is_page()`, `is_attachment()`, `is_privacy_policy()`
- `is_category()`, `is_tag()`, `is_author()`
- `is_archive()`, `is_post_type_archive()`
- `is_date()`, `is_day()`, `is_month()`, `is_year()`, `is_time()`
- `is_search()`, `is_404()`, `is_paged()`
- `is_front_page()`, `is_home()`, `is_singular()`
- `is_preview()`, `is_customize_preview()`

## WordPress Integration

### Hooks Used

**Actions:**
- `template_redirect` - Main entry point for checking and applying noindex
- `admin_init` - Registers settings and detects conflicts
- `admin_menu` - Adds settings page
- `admin_post_update_noindex_seo` - Handles form submission
- `update_option_noindexseo` - Clears transient cache
- `plugin_action_links_{basename}` - Adds Settings link to plugins page

**Filters:**
- `wp_robots` - Modern way to add robots directives (WP 5.7+)
- `noindex_seo_contexts` - Allows filtering available contexts

### Uninstallation

The plugin includes `uninstall.php` which removes all options when the plugin is deleted through WordPress admin.

## Code Style Guidelines

- Follow WordPress Coding Standards (WPCS)
- Maintain compatibility with PHP 7.2+ and WordPress 6.6+
- Use WordPress core functions (e.g., `esc_html()`, `esc_url()`, `wp_nonce_field()`)
- All user-facing strings must be translatable with `__()` or `esc_html__()`
- Security: Always sanitize output and validate input
- Use `ABSPATH` check at the top of PHP files

## Security Notes

- Nonce verification on form submission
- Capability checks (`manage_options`)
- Output escaping (`esc_html()`, `esc_url()`, `esc_attr()`)
- Input sanitization (`absint()`, `checked()`)
- Direct file access prevention (`defined( 'ABSPATH' ) || die()`)

## Testing References

According to readme.txt, the plugin is tested with:
- PHP Coding Standards: 3.12.1
- WordPress Coding Standards: 3.1.0
- Plugin Check (PCP): 1.4.0

## Extensibility

Developers can extend the plugin using:
- `noindex_seo_contexts` filter - Modify or add contexts and their option keys
- `wp_robots` filter - Intercept and modify robot directives
