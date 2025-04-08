=== noindex SEO ===
Contributors: javiercasares
Tags: seo, noindex
Requires at least: 4.1
Tested up to: 6.8
Stable tag: 1.2.0
Requires PHP: 5.6
Version: 1.2.0
License: GPL-2.0-or-later
License URI: https://spdx.org/licenses/GPL-2.0-or-later.html

Allows to add a meta-tag for robots noindex in some parts of your WordPress site.

== Description ==

Allows to add a meta-tag for robots noindex in some parts of your WordPress site.

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

* No vulnerabilities have been published up to version 1.2.0.

Found a security vulnerability? Please report it to us privately at the [noindex SEO GitHub repository](https://github.com/javiercasares/noindex-seo/security/advisories/new).
