<?php
/**
 * Plugin Name: Noindex SEO
 * Plugin URI: https://wordpress.org/plugins/noindex-seo/
 * Description: .
 * Author: closemarketing
 * Author URI: https://www.closemarketing.es/
 * Version: 0.1
 * Text Domain: noindex-seo
 * Domain Path: /languages
 * License: GNU General Public License version 3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WordPress
 */


// * Loads translation
load_plugin_textdomain( 'noindex-seo', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

$noindex_options = array(
	'is_author'     => esc_html__( 'Author archives', 'noindex-seo' ),
	'is_attachment' => esc_html__( 'Attachment archives', 'noindex-seo' ),
	'is_date'       => esc_html__( 'Date', 'noindex-seo' ),
	'is_year'       => esc_html__( 'Year', 'noindex-seo' ),
	'is_month'      => esc_html__( 'Month archives', 'noindex-seo' ),
	'is_day'        => esc_html__( 'Day archives', 'noindex-seo' ),
	'is_time'       => esc_html__( 'Time archives', 'noindex-seo' ),
	'is_archive'    => esc_html__( 'All archives', 'noindex-seo' ),
	'is_search'     => esc_html__( 'Search pages', 'noindex-seo' ),
	'is_paged'      => esc_html__( 'Pagination pages', 'noindex-seo' ),
	'is_attachment' => esc_html__( 'Attachment pages', 'noindex-seo' ),
	'is_preview'    => esc_html__( 'Preview pages', 'noindex-seo' ),
);

// * Includes Libraries for Noindex
require_once dirname( __FILE__ ) . '/includes/class-options.php';

// * Includes Libraries for Frontend
require_once dirname( __FILE__ ) . '/includes/class-frontend.php';
