=== noindex SEO ===
Contributors: javiercasares
Tags: seo, noindex, nofollow, noarchive, robots
Requires at least: 6.6
Tested up to: 6.9
Stable tag: 2.0.0
Requires PHP: 7.2
Version: 2.0.0
License: GPL-2.0-or-later
License URI: https://spdx.org/licenses/GPL-2.0-or-later.html

Control search engine behavior with 5 independent robots directives (noindex, nofollow, noarchive, nosnippet, noimageindex) using HTML meta tags or HTTP headers.

== Description ==

Fine-grained control over how search engines index and display your WordPress content. Apply 5 independent robots directives to 25 different page contexts with flexible implementation methods.

**5 Robots Directives:**

* **noindex**: Prevent search engines from indexing the page
* **nofollow**: Prevent search engines from following links on the page
* **noarchive**: Prevent search engines from showing cached versions
* **nosnippet**: Prevent search engines from showing text snippets in results
* **noimageindex**: Prevent search engines from indexing images on the page

**Implementation Methods:**

* HTML Meta Tags: Traditional method, easy to verify in page source (default)
* HTTP Headers: More robust, works with all content types including PDFs and images
* Both: Maximum compatibility for all scenarios

**Control Levels:**

* Global Settings: Apply directives to 25 different page contexts (posts, pages, archives, etc.)
* Granular Control (Optional): Override global settings for individual posts, pages, and custom post types via meta boxes in the editor

**Perfect for:**

* Blocking indexing of attachment pages while allowing link following
* Preventing duplicate content issues with flexible directive combinations
* Controlling archive page indexing with granular control
* Managing pagination SEO with independent settings
* Protecting private content from search engine caching
* Preventing snippet display while still indexing content

**Main pages**

* Front Page: Block the indexing of the site's front page.
* Home: Block the indexing of the site's home page.

**Pages and Posts**

* Page: Block the indexing of the site's pages.
* Privacy Policy: Block the indexing of the site's privacy policy page.
* Single: Block the indexing of a post on the site.
* Singular: Block the indexing of a post or a page of the site.

**Taxonomies**

* Category: Block the indexing of the site categories. The lists where the posts appear.
* Tag: Block the indexing of the site's tags. The lists where the posts appear.

**Dates**

* Date: Block the indexing when any date-based archive page (i.e. a monthly, yearly, daily or time-based archive) of the site. The lists where the posts appear.
* Day: Block the indexing when a daily archive of the site. The lists where the posts appear.
* Month: Block the indexing when a monthly archive of the site. The lists where the posts appear.
* Time: Block the indexing when an hourly, "minutely", or "secondly" archive of the site. The lists where the posts appear.
* Year: Block the indexing when a yearly archive of the site. The lists where the posts appear.

**Archives**

* Archive: Block the indexing of any type of Archive page. Category, Tag, Author and Date based pages are all types of Archives. The lists where the posts appear.
* Author: Block the indexing of the author's page, where the author's publications appear.
* Post Type Archive: Block the indexing of any post type page.

**Pagination**

* Pagination: Block the indexing of the pagination, i.e. all pages other than the main page of an archive.

**Search**

* Search: Block the indexing of the internal search result pages.

**Attachments**

* Attachment: Block the indexing of an attachment document to a post or page. An attachment is an image or other file uploaded through the post editor's upload utility. Attachments can be displayed on their own "page" or template. This will not cause the indexing of the image or file to be blocked.

**Previews**

* Customize Preview: Block the indexing when a content is being displayed in customize mode.
* Preview: Block the indexing when a single post is being displayed in draft mode.

**Error Page**

* Error 404: This will cause an error page to be blocked from being indexed. As it is an error page, it should not be indexed per se, but just in case.

Important note: if you have any doubt about any of the following items it is best not to activate the option as you could lose results in the search engines.

== Installation ==

= Automatic download =

Visit the plugin section in your WordPress, search for [noindex-seo]; download and install the plugin.

= Manual download =

Extract the contents of the ZIP and upload the contents to the `/wp-content/plugins/noindex-seo/` directory. Once uploaded, it will appear in your plugin list.

== Compatibility ==

* WordPress: 4.1 - 6.8
* PHP: 5.6 - 8.4

== Changelog ==

= 2.0.0 [2026-01-20] =

**Major Feature & Security Release**

This is a major release with comprehensive security hardening and powerful new robots directive features.

**🆕 New Features**

* **5 Independent Robots Directives**: noindex, nofollow, noarchive, nosnippet, noimageindex
* Each directive can be enabled independently for any page context
* 125 total configurable options (25 contexts × 5 directives)
* Checkbox-based interface with emoji icons for easy visualization
* Tooltip descriptions for each directive
* Multiple directives combined in single meta tag or HTTP header
* **Granular Per-Post/Page Control (Optional)**: Enable per-content overrides in settings to control directives for individual posts, pages, and custom post types via meta boxes

**🔒 Security Improvements**

* Added explicit capability checks in admin functions (fixes CWE-862)
* Enhanced input sanitization with proper validation (addresses CWE-20)
* Improved HTML attribute escaping (prevents CWE-79)
* Added validation for filtered contexts to prevent option key injection
* Strengthened transient cache clearing with admin context verification
* Modernized code for PHP 7.2+ with strict types and type declarations

**🎨 UI/UX Improvements**

* Replaced single toggle switches with 5 directive checkboxes per context
* Compact inline layout with visual directive indicators
* Updated statistics dashboard to track directive usage
* Enhanced search functionality for directive names
* Maintained modern card-based collapsible design

**⚙️ Technical Changes**

* `noindex_seo_metarobots()` now accepts array of directives
* All functions updated to handle multiple directives per context
* Enhanced Settings API registration for all directive combinations
* Form processing handles 125 options with validation
* Uninstall script cleans up all directive options
* HTTP headers support multiple directives (e.g., `X-Robots-Tag: noindex, nofollow, noarchive`)

**🔧 Compatibility & Migration**

* WordPress: 6.6 - 6.9 (updated from 4.1+)
* PHP: 7.2 - 8.5 (updated from 5.6+)
* **Automatic Migration**: Existing noindex settings preserved and migrated automatically
* **No Manual Action Required**: Plugin detects version and migrates configuration on first load
* **Configuration Version Tracking**: Uses version number to ensure migration runs only once
* New directives (nofollow, noarchive, nosnippet, noimageindex) default to disabled
* Migration is safe and can run multiple times without data loss

**Upgrade Notice**

Major feature release with 5 independent robots directives. **Fully backward compatible** - your existing noindex settings will be automatically migrated on upgrade. No manual action required. Recommended for all users.

= 1.2.0 [2025-04-08] =

**Changes**

* Improved functions documentation.

**Fixes**

* The way the options are saved.

**Compatibility**

* WordPress: 4.1 - 6.8
* PHP: 5.6 - 8.4

**Tests**

* PHP Coding Standards: 3.12.1
* WordPress Coding Standards: 3.1.0
* Plugin Check (PCP): 1.4.0

= 1.1.1 [2024-11-04] =

**Added**

* Configuration option to dismiss other SEO plugin incompatibilities.

**Compatibility**

* WordPress: 4.1 - 6.7
* PHP: 5.6 - 8.4

= 1.1.0 [2024-11-02] =

**Added**

* Detects other WordPress SEO plugins, and creates a notice about it, to avoid conflicts.
* Has filters, so other plugins can hack.

**Changed**

* Uses native wp_robots functions (since WP 5.7+)
* Big refactory.
* Less size, improved code quality.

**Compatibility**

* WordPress: 4.1 - 6.7
* PHP: 5.6 - 8.4

**Tests**

* PHP Coding Standards: 3.10.3
* WordPress Coding Standards: 3.1.0
* Plugin Check (PCP): 1.1.0

== Security ==

This plugin adheres to the following security measures and review protocols for each version:

* [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
* [WordPress Plugin Security](https://developer.wordpress.org/plugins/wordpress-org/plugin-security/)
* [WordPress APIs Security](https://developer.wordpress.org/apis/security/)
* [WordPress Coding Standards](https://github.com/WordPress/WordPress-Coding-Standards)
* [Plugin Check (PCP)](https://wordpress.org/plugins/plugin-check/)

== Privacy ==

* This plugin does not collect any information about your site, your identity, the plugins, themes or content the site has.

== Vulnerabilities ==

* No vulnerabilities have been published up to version 2.0.0.
* Version 2.0.0 includes proactive security hardening based on comprehensive security audit (see docs/SECURITY-2026-01-20.md).

Found a security vulnerability? Please report it to us privately at the [noindex SEO GitHub repository](https://github.com/javiercasares/noindex-seo/security/advisories/new).
