<?php
/**
 * Uninstall the plugin.
 *
 * @package noindex-seo
 */

if ( defined( 'ABSPATH' ) && defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	delete_option( 'noindex_seo_single' );
	delete_option( 'noindex_seo_page' );
	delete_option( 'noindex_seo_privacy_policy' );
	delete_option( 'noindex_seo_attachment' );
	delete_option( 'noindex_seo_category' );
	delete_option( 'noindex_seo_tag' );
	delete_option( 'noindex_seo_author' );
	delete_option( 'noindex_seo_post_type_archive' );
	delete_option( 'noindex_seo_date' );
	delete_option( 'noindex_seo_day' );
	delete_option( 'noindex_seo_month' );
	delete_option( 'noindex_seo_year' );
	delete_option( 'noindex_seo_archive' );
	delete_option( 'noindex_seo_search' );
	delete_option( 'noindex_seo_error' );
	delete_option( 'noindex_seo_front_page' );
	delete_option( 'noindex_seo_home' );
	delete_option( 'noindex_seo_singular' );
	delete_option( 'noindex_seo_paged' );
	delete_option( 'noindex_seo_preview' );
	delete_option( 'noindex_seo_customize_preview' );
	delete_option( 'noindex_seo_time' );
	delete_option( 'noindex_seo_config_seoplugins' );
}
