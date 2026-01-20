<?php
/**
 * Uninstall the noindex SEO plugin.
 *
 * This file is called by WordPress when the plugin is deleted through the admin interface.
 * It removes all plugin options and transients from the database to ensure a clean uninstall.
 *
 * @package noindex-seo
 * @since 1.0.0
 * @since 2.0.0 Added cleanup for new implementation method option and transients.
 */

declare(strict_types=1);

// Exit if uninstall not called from WordPress.
if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete all noindex context options.
$context_options = array(
	'noindex_seo_archive',
	'noindex_seo_attachment',
	'noindex_seo_author',
	'noindex_seo_category',
	'noindex_seo_comment_feed',
	'noindex_seo_customize_preview',
	'noindex_seo_date',
	'noindex_seo_day',
	'noindex_seo_error',
	'noindex_seo_feed',
	'noindex_seo_front_page',
	'noindex_seo_home',
	'noindex_seo_month',
	'noindex_seo_page',
	'noindex_seo_paged',
	'noindex_seo_post_type_archive',
	'noindex_seo_preview',
	'noindex_seo_privacy_policy',
	'noindex_seo_robots',
	'noindex_seo_search',
	'noindex_seo_single',
	'noindex_seo_singular',
	'noindex_seo_tag',
	'noindex_seo_time',
	'noindex_seo_year',
);

foreach ( $context_options as $option ) {
	delete_option( $option );
}

// Delete configuration options.
delete_option( 'noindex_seo_config_seoplugins' );
delete_option( 'noindex_seo_config_method' );

// Delete transient cache.
delete_transient( 'noindex_seo_options' );

// Clean up any leftover options (in case of partial uninstall).
global $wpdb;

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
// Direct database queries are necessary here for complete cleanup during uninstall.
// This is a DELETE operation (not SELECT), so caching is not applicable.
// Using wildcards with delete_option() is not possible, requiring direct SQL.
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'noindex_seo_%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_noindex_seo_%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_noindex_seo_%'" );
// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
