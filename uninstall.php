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
 * @since 2.0.0 Added cleanup for multiple directives (noindex, nofollow, noarchive, nosnippet, noimageindex).
 */

declare(strict_types=1);

// Exit if uninstall not called from WordPress.
if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Define contexts and directives.
$noindex_seo_contexts   = array(
	'error',
	'archive',
	'attachment',
	'author',
	'category',
	'comment_feed',
	'customize_preview',
	'date',
	'day',
	'feed',
	'front_page',
	'home',
	'month',
	'page',
	'paged',
	'post_type_archive',
	'preview',
	'privacy_policy',
	'robots',
	'search',
	'single',
	'singular',
	'tag',
	'time',
	'year',
);
$noindex_seo_directives = array( 'noindex', 'nofollow', 'noarchive', 'nosnippet', 'noimageindex' );

// Delete all directive options for each context.
foreach ( $noindex_seo_contexts as $noindex_seo_context ) {
	foreach ( $noindex_seo_directives as $noindex_seo_directive ) {
		delete_option( $noindex_seo_directive . '_seo_' . $noindex_seo_context );
	}
}

// Delete configuration options.
delete_option( 'noindex_seo_config_seoplugins' );
delete_option( 'noindex_seo_config_method' );
delete_option( 'noindex_seo_config_granular' );
delete_option( 'noindex_seo_config_version' );

// Delete transient cache.
delete_transient( 'noindex_seo_options' );

// Clean up any leftover options (in case of partial uninstall).
global $wpdb;

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
// Direct database queries are necessary here for complete cleanup during uninstall.
// This is a DELETE operation (not SELECT), so caching is not applicable.
// Using wildcards with delete_option() is not possible, requiring direct SQL.
// Clean up all directive-related options (noindex, nofollow, noarchive, nosnippet, noimageindex).
foreach ( $noindex_seo_directives as $noindex_seo_directive ) {
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $noindex_seo_directive . '_seo_%' ) );
}

// Clean up transients.
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
		'_transient_noindex_seo_%'
	)
);
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
		'_transient_timeout_noindex_seo_%'
	)
);

// Clean up post meta (granular control).
$noindex_seo_meta_keys = array(
	'_noindex_seo_override',
	'_noindex_seo_noindex',
	'_noindex_seo_nofollow',
	'_noindex_seo_noarchive',
	'_noindex_seo_nosnippet',
	'_noindex_seo_noimageindex',
);

foreach ( $noindex_seo_meta_keys as $noindex_seo_meta_key ) {
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s",
			$noindex_seo_meta_key
		)
	);
}
// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
