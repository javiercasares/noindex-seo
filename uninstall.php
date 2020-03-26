<?php
if ( defined( 'ABSPATH' ) && defined( 'WP_UNINSTALL_PLUGIN' ) ) {
  delete_option( 'noindex_pages_error' );
  delete_option( 'noindex_pages_archive' );
  delete_option( 'noindex_pages_attachment' );
  delete_option( 'noindex_pages_author' );
  delete_option( 'noindex_pages_category' );
  delete_option( 'noindex_pages_comment_feed' );
  delete_option( 'noindex_pages_customize_preview' );
  delete_option( 'noindex_pages_date' );
  delete_option( 'noindex_pages_day' );
  delete_option( 'noindex_pages_feed' );
  delete_option( 'noindex_pages_front_page' );
  delete_option( 'noindex_pages_home' );
  delete_option( 'noindex_pages_month' );
  delete_option( 'noindex_pages_page' );
  delete_option( 'noindex_pages_paged' );
  delete_option( 'noindex_pages_post_type_archive' );
  delete_option( 'noindex_pages_preview' );
  delete_option( 'noindex_pages_privacy_policy' );
  delete_option( 'noindex_pages_robots' );
  delete_option( 'noindex_pages_search' );
  delete_option( 'noindex_pages_single' );
  delete_option( 'noindex_pages_singular' );
  delete_option( 'noindex_pages_tag' );
  delete_option( 'noindex_pages_tax' );
  delete_option( 'noindex_pages_time' );
  delete_option( 'noindex_pages_year' );
}

