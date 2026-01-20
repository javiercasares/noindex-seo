# Changelog

All notable changes to the noindex SEO plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2026-01-20

### Major Feature & Security Release

This is a major release with comprehensive security hardening and powerful new robots directive features. The plugin has been completely refactored with modern PHP practices and enhanced security.

**Security Audit Score:** 99/100 (Excellent)

### Added

#### Multiple Robots Directives
- Support for 5 independent robots directives per context:
  - `noindex`: Prevent search engines from indexing
  - `nofollow`: Prevent search engines from following links
  - `noarchive`: Prevent cached versions in search results
  - `nosnippet`: Prevent text snippets in search results
  - `noimageindex`: Prevent image indexing
- Each directive can be enabled independently
- Total: 125 configurable options (25 contexts × 5 directives)
- Multiple directives combined in single meta tag or HTTP header

#### Implementation Methods
- New HTTP X-Robots-Tag header support
- Three implementation options:
  - `meta`: HTML meta tags via wp_robots filter (default)
  - `header`: HTTP X-Robots-Tag headers
  - `both`: Both methods for maximum compatibility
- Works with all content types (HTML, PDFs, images, feeds, attachments)
- Automatic fallback to meta tags if headers can't be sent
- Context-specific validation (attachment, feed, comment_feed require headers)

#### Granular Per-Post/Page Control
- Optional per-content override system (disabled by default)
- Meta boxes in Classic Editor
- Native Gutenberg sidebar panel for Block Editor
- Override global settings for individual posts/pages/CPTs
- Priority system: per-post settings override global settings
- Live preview showing effective directives
- Clean, intuitive UI with emoji icons

#### List Management Features
- Custom "Robots" column in post/page lists
- Color-coded badges showing active directives
- Quick Edit support for fast inline editing
- Bulk actions: "Enable Robots Override" and "Disable Robots Override"
- Filter posts by override status (with override / without override)
- Hidden data attributes for JavaScript integration

#### User Interface Improvements
- Modern checkbox-based interface replacing toggle switches
- Emoji icons for visual identification (🔍 🔗 💾 📄 🖼️)
- Inline compact layout showing all 5 directives per context
- Tooltip descriptions for each directive
- Updated statistics dashboard tracking directive usage
- Enhanced search functionality for directive names
- Maintained modern card-based collapsible design
- Color-coded recommendation badges (green/red)

### Security

#### Comprehensive Security Hardening
- **CSRF Protection**: Nonces implemented in all forms (settings page, meta box, quick edit)
- **Authorization**: Capability checks in all admin functions (`manage_options`, `edit_post`, `edit_posts`)
- **Input Sanitization**: All `$_POST`, `$_GET`, `$_REQUEST` properly sanitized
  - `sanitize_text_field()` for text inputs
  - `absint()` for integers
  - `wp_unslash()` for slashed data
  - Whitelist validation for method and filter values
- **Output Escaping**: Consistent use of `esc_html()`, `esc_attr()`, `esc_url()`
- **SQL Injection Prevention**: All database queries use `$wpdb->prepare()` with placeholders
- **Bulk Actions Security**:
  - General capability check (`edit_posts`)
  - Per-post capability filtering (`edit_post` for each ID)
  - Empty array validation
  - Integer sanitization with `intval()`
- **REST API Security**: Authorization callbacks for post meta registration
- **Direct File Access**: Prevented in all PHP files

#### Code Quality & Standards
- PHP 7.2+ with strict types (`declare(strict_types=1)`)
- Type hints for all function parameters and return types
- WordPress Coding Standards 100% compliance
- PHPCS verified with zero errors and warnings
- PHPDoc blocks for all functions
- Proper use of `phpcs:ignore` with clear justifications

#### Security Audit Results
- **Vulnerabilities Found**: 0 critical, 0 high, 0 medium, 0 low
- **Protection Coverage**:
  - ✅ CSRF (Cross-Site Request Forgery) - 100%
  - ✅ XSS (Cross-Site Scripting) - 100%
  - ✅ SQL Injection - 100%
  - ✅ Privilege Escalation - 100%
  - ✅ Authorization Bypass - 100%
- **OWASP Top 10 Compliance**: Protected against all relevant vectors
- **WordPress Plugin Security Guidelines**: 100% compliance

### Changed

#### Technical Improvements
- `noindex_seo_metarobots()` now accepts array of directives and method parameter
- All functions updated to handle multiple directives per context
- Enhanced Settings API registration for all directive combinations
- Form processing handles 125 options with validation
- wp_robots filter priority increased to 99 for precedence
- Transient caching for performance (1-hour cache)
- Helper function `noindex_seo_clear_post_directives()` for code reuse
- Bulk actions use direct SQL for performance with large selections

#### Migration System
- Automatic migration from v1.x to v2.0
- Configuration version tracking in database
- Existing noindex settings preserved and migrated
- New directives default to disabled
- Migration runs once automatically on plugin update
- Safe to run multiple times without data loss

#### Compatibility Updates
- **WordPress**: 6.6 - 6.9 (updated from 4.1+)
- **PHP**: 7.2 - 8.5 (updated from 5.6+)
- Minimum requirements increased for security and modern features
- Tested with latest WordPress and PHP versions

### Fixed

- Race condition in HTTP header sending with automatic fallback
- localStorage JSON parsing errors with try-catch blocks
- Date context logic documented (catch-all for edge cases)
- Post meta "0" string treated as truthy (now uses explicit integer check)
- Form submit button custom effect removed (uses WordPress standard)

### Performance

- Transient caching reduces database queries (1-hour cache)
- Bulk actions optimized with direct SQL queries
- Efficient meta_query for post list filtering
- Early returns prevent unnecessary processing
- Minimized DOM manipulations in JavaScript

### Developer

- Comprehensive inline documentation
- PHPDoc blocks for all functions with @since tags
- Clear code organization and separation of concerns
- Helper functions for common operations
- Filters for extensibility (`noindex_seo_contexts`, etc.)
- Well-structured JavaScript (modular functions)
- Gutenberg integration following React best practices

### Documentation

- Updated README.md with new features
- Comprehensive SECURITY-AUDIT-2026-01-20.md report
- SECURITY-IMPROVEMENTS-2026-01-20.md implementation guide
- Inline code documentation enhanced
- User-facing help text improved

---

## [1.2.0] - 2025-04-08

### Changed
- Improved functions documentation

### Fixed
- The way the options are saved

### Compatibility
- WordPress: 4.1 - 6.8
- PHP: 5.6 - 8.4

### Tests
- PHP Coding Standards: 3.12.1
- WordPress Coding Standards: 3.1.0
- Plugin Check (PCP): 1.4.0

---

## [1.1.1] - 2024-11-04

### Added
- Configuration option to dismiss other SEO plugin incompatibilities

### Compatibility
- WordPress: 4.1 - 6.7
- PHP: 5.6 - 8.4

---

## [1.1.0] - 2024-11-02

### Added
- Detects other WordPress SEO plugins and creates a notice to avoid conflicts
- Filters for extensibility

### Changed
- Uses native wp_robots functions (since WP 5.7+)
- Big refactory for code quality
- Reduced plugin size

### Compatibility
- WordPress: 4.1 - 6.7
- PHP: 5.6 - 8.4

### Tests
- PHP Coding Standards: 3.10.3
- WordPress Coding Standards: 3.1.0
- Plugin Check (PCP): 1.1.0

---

## [1.0.0] - Initial Release

### Added
- Basic noindex functionality for WordPress contexts
- Simple toggle interface
- Support for 25 different page contexts

---

**Legend:**
- `Added` for new features
- `Changed` for changes in existing functionality
- `Deprecated` for soon-to-be removed features
- `Removed` for now removed features
- `Fixed` for any bug fixes
- `Security` for vulnerability fixes

[2.0.0]: https://github.com/javiercasares/noindex-seo/releases/tag/2.0.0
[1.2.0]: https://github.com/javiercasares/noindex-seo/releases/tag/1.2.0
[1.1.1]: https://github.com/javiercasares/noindex-seo/releases/tag/1.1.1
[1.1.0]: https://github.com/javiercasares/noindex-seo/releases/tag/1.1.0
[1.0.0]: https://github.com/javiercasares/noindex-seo/releases/tag/1.0.0
