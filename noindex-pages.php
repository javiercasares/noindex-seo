<?php
/**
 * Plugin Name: Noindex Pages
 * Plugin URI: https://wordpress.org/plugins/noindex-pages/
 * Description: .
 * Author: closemarketing
 * Author URI: https://www.closemarketing.es/
 * Version: 0.1
 * Text Domain: noindex-pages
 * Domain Path: /languages
 * License: GNU General Public License version 3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WordPress
 */


// * Loads translation
load_plugin_textdomain( 'noindex-pages', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


// * Includes Libraries for Noindex
require_once dirname( __FILE__ ) . '/includes/class-options.php';
