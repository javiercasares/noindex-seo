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


// * Includes Libraries for Noindex
require_once dirname( __FILE__ ) . '/includes/class-options.php';

// * Includes Libraries for Frontend
require_once dirname( __FILE__ ) . '/includes/class-frontend.php';
