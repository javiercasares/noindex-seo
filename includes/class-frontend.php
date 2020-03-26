<?php
/**
 * Frontend Settings
 *
 * Has functions customize the WordPress Admins
 *
 * @author   closemarketing
 * @category Functions
 * @package  Admin
 */

/**
 * Class for admin fields
 */
class NP_Frontend {

	/**
	 * Construct of Class
	 */
	public function __construct() {
		// Create custom plugin settings menu.
		add_action( 'wp_head', array( $this, 'noindex_pages_frontend' ) );
	}

	public function noindex_pages_frontend() {
		if ( is_author() || is_date() || is_year() || is_month() || is_day() || is_time() || is_archive() || is_search() || is_paged() || is_attachment() || is_preview() ) {
		  	echo '<meta name="robots" content="noindex">';
		}
	} 

}

new NP_Frontend();