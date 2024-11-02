<?php
/**
 * Plugin Name: noindex SEO
 * Plugin URI: https://wordpress.org/plugins/noindex-seo/
 * Description: Allows adding a meta-tag for robots noindex in specific parts of your WordPress site.
 * Requires at least: 4.1
 * Requires PHP: 5.6
 * Version: 1.1.0
 * Author: Javier Casares
 * Author URI: https://www.javiercasares.com/
 * License: GPL-2.0-or-later
 * License URI: https://spdx.org/licenses/GPL-2.0-or-later.html
 * Text Domain: noindex-seo
 * Domain Path: /languages
 *
 * @package noindex-seo
 */

defined( 'ABSPATH' ) || die( 'Bye bye!' );

/**
 * Outputs a 'noindex' meta robots tag to the page.
 *
 * This function prints the 'noindex' meta robots tag, instructing search engines
 * not to index the content of the current page.
 *
 * @since 1.0.0
 *
 * @return void
 */
function noindex_seo_metarobots() {
	echo '<meta name="robots" content="noindex">' . "\n";
}

/**
 * Retrieves and sets the SEO 'noindex' values for various WordPress contexts.
 *
 * @global WP_Post $post The post global for the current post, if within The Loop.
 *
 * @return void
 *
 * @since 1.0.0
 */
function noindex_seo_show() {
	global $post;

	$noindex_seo_values = array(
		'front_page'        => is_front_page(),
		'home'              => is_home(),
		'page'              => is_page(),
		'privacy_policy'    => function_exists( 'is_privacy_policy' ) ? is_privacy_policy() : false,
		'single'            => is_single(),
		'singular'          => is_singular(),
		'category'          => is_category(),
		'tag'               => is_tag(),
		'date'              => is_date(),
		'day'               => is_day(),
		'month'             => is_month(),
		'time'              => is_time(),
		'year'              => is_year(),
		'archive'           => is_archive(),
		'author'            => is_author(),
		'post_type_archive' => is_post_type_archive(),
		'paged'             => is_paged(),
		'search'            => is_search(),
		'attachment'        => is_attachment(),
		'customize_preview' => is_customize_preview(),
		'preview'           => is_preview(),
		'error'             => is_404(),
	);

	foreach ( $noindex_seo_values as $key => $condition ) {
		if ( $condition && (bool) get_option( 'noindex_seo_' . $key ) ) {
			noindex_seo_metarobots();
			break; // Prevent multiple meta tags from being added.
		}
	}

	unset( $noindex_seo_values );
}

add_action( 'wp_head', 'noindex_seo_show' );
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'noindex_seo_settings_link' );
add_action( 'admin_init', 'noindex_seo_register' );
add_action( 'admin_menu', 'noindex_seo_menu' );

/**
 * Adds a 'Settings' link to the plugin action links on the plugins page.
 *
 * @since 1.0.0
 *
 * @param array $links An array of action links already present for the plugin.
 *
 * @return array Returns the updated array of action links including the 'Settings' link.
 */
function noindex_seo_settings_link( $links ) {
	$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=noindex_seo' ) ) . '">' . esc_html__( 'Settings', 'noindex-seo' ) . '</a>';
	$links[]       = $settings_link;
	return $links;
}

/**
 * Adds a 'noindex SEO' options page to the WordPress admin menu.
 *
 * @since 1.0.0
 *
 * @return void
 */
function noindex_seo_menu() {
	add_options_page(
		__( 'noindex SEO', 'noindex-seo' ),
		__( 'noindex SEO', 'noindex-seo' ),
		'manage_options',
		'noindex_seo',
		'noindex_seo_admin'
	);
}

/**
 * Registers settings for the 'noindex SEO' plugin.
 *
 * @since 1.0.0
 *
 * @return void
 */
function noindex_seo_register() {
	$settings = array(
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

	foreach ( $settings as $setting ) {
		register_setting(
			'noindexseo',
			'noindex_seo_' . $setting,
			array(
				'type'    => 'integer',
				'default' => 0,
			)
		);
	}
}

/**
 * Displays the administration settings page for the 'noindex SEO' plugin.
 *
 * @since 1.0.0
 *
 * @return void
 */
function noindex_seo_admin() {
	// Define sections and their respective settings.
	$sections = array(
		'main_pages'  => array(
			'title'  => __( 'Main Pages', 'noindex-seo' ),
			'fields' => array(
				'front_page' => array(
					'label'       => __( 'Front Page', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing of the site\'s front page.', 'noindex-seo' ),
					'view_url'    => get_site_url(),
				),
				'home'       => array(
					'label'       => __( 'Home', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing of the site\'s home page.', 'noindex-seo' ),
					'view_url'    => get_home_url(),
				),
			),
		),
		'pages_posts' => array(
			'title'  => __( 'Pages and Posts', 'noindex-seo' ),
			'fields' => array(
				'page'           => array(
					'label'       => __( 'Page', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing of the site\'s pages.', 'noindex-seo' ),
				),
				'privacy_policy' => array(
					'label'       => __( 'Privacy Policy', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing of the site\'s privacy policy page.', 'noindex-seo' ),
					'view_url'    => get_privacy_policy_url(),
					'conditional' => version_compare( $GLOBALS['wp_version'], '5.2', '>=' ),
				),
				'single'         => array(
					'label'       => __( 'Single', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing of a post on the site.', 'noindex-seo' ),
				),
				'singular'       => array(
					'label'       => __( 'Singular', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing of a post or a page of the site.', 'noindex-seo' ),
				),
			),
		),
		'taxonomies'  => array(
			'title'  => __( 'Taxonomies', 'noindex-seo' ),
			'fields' => array(
				'category' => array(
					'label'       => __( 'Category', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing of the site categories. The lists where the posts appear.', 'noindex-seo' ),
				),
				'tag'      => array(
					'label'       => __( 'Tag', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing of the site\'s tags. The lists where the posts appear.', 'noindex-seo' ),
				),
			),
		),
		'dates'       => array(
			'title'  => __( 'Dates', 'noindex-seo' ),
			'fields' => array(
				'date'  => array(
					'label'       => __( 'Date', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing of any date-based archive page (i.e., monthly, yearly, daily, or time-based archive) of the site. The lists where the posts appear.', 'noindex-seo' ),
				),
				'day'   => array(
					'label'       => __( 'Day', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing of a daily archive of the site. The lists where the posts appear.', 'noindex-seo' ),
				),
				'month' => array(
					'label'       => __( 'Month', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing of a monthly archive of the site. The lists where the posts appear.', 'noindex-seo' ),
				),
				'time'  => array(
					'label'       => __( 'Time', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing of an hourly, "minutely", or "secondly" archive of the site. The lists where the posts appear.', 'noindex-seo' ),
				),
				'year'  => array(
					'label'       => __( 'Year', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing of a yearly archive of the site. The lists where the posts appear.', 'noindex-seo' ),
				),
			),
		),
		'archives'    => array(
			'title'  => __( 'Archives', 'noindex-seo' ),
			'fields' => array(
				'archive'           => array(
					'label'       => __( 'Archive', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing of any type of Archive page. Category, Tag, Author, and Date-based pages are all types of Archives. The lists where the posts appear.', 'noindex-seo' ),
				),
				'author'            => array(
					'label'       => __( 'Author', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing of the author\'s page, where the author\'s publications appear.', 'noindex-seo' ),
				),
				'post_type_archive' => array(
					'label'       => __( 'Post Type Archive', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing of any post type page.', 'noindex-seo' ),
				),
			),
		),
		'pagination'  => array(
			'title'  => __( 'Pagination', 'noindex-seo' ),
			'fields' => array(
				'paged' => array(
					'label'       => __( 'Pagination', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing of the pagination, i.e., all pages other than the main page of an archive.', 'noindex-seo' ),
				),
			),
		),
		'search'      => array(
			'title'  => __( 'Search', 'noindex-seo' ),
			'fields' => array(
				'search' => array(
					'label'       => __( 'Search', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing of the internal search result pages.', 'noindex-seo' ),
				),
			),
		),
		'attachments' => array(
			'title'  => __( 'Attachments', 'noindex-seo' ),
			'fields' => array(
				'attachment' => array(
					'label'       => __( 'Attachment', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing of an attachment document to a post or page. An attachment is an image or other file uploaded through the post editor\'s upload utility. Attachments can be displayed on their own "page" or template. This will not cause the indexing of the image or file to be blocked.', 'noindex-seo' ),
				),
			),
		),
		'previews'    => array(
			'title'  => __( 'Previews', 'noindex-seo' ),
			'fields' => array(
				'customize_preview' => array(
					'label'       => __( 'Customize Preview', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing when content is being displayed in customize mode.', 'noindex-seo' ),
				),
				'preview'           => array(
					'label'       => __( 'Preview', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will block the indexing when a single post is being displayed in draft mode.', 'noindex-seo' ),
				),
			),
		),
		'error_page'  => array(
			'title'  => __( 'Error Page', 'noindex-seo' ),
			'fields' => array(
				'error' => array(
					'label'       => __( 'Error 404', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'description' => __( 'This will cause an error page to be blocked from being indexed. As it is an error page, it should not be indexed per se, but just in case.', 'noindex-seo' ),
				),
			),
		),
	);

	?>
	<div class="wrap">
		<h1><?php echo esc_html( __( 'noindex SEO Settings', 'noindex-seo' ) ); ?></h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'noindexseo' );
			do_settings_sections( 'noindexseo' ); // In case you have sections added later.
			?>
			<p><?php echo esc_html( __( 'Important note: if you have any doubt about any of the following items it is best not to activate the option as you could lose results in the search engines.', 'noindex-seo' ) ); ?></p>
			<?php
			foreach ( $sections as $section_id => $section ) {
				echo '<h2>' . esc_html( $section['title'] ) . '</h2>';
				echo '<table class="form-table">';
				foreach ( $section['fields'] as $field_id => $field ) {
					// Check for conditional display.
					if ( isset( $field['conditional'] ) && ! $field['conditional'] ) {
						continue;
					}

					// Get current option value.
					$option = get_option( 'noindex_seo_' . $field_id, 0 );

					echo '<tr>';
					echo '<th scope="row"><label for="noindex_seo_' . esc_attr( $field_id ) . '">' . esc_html( $field['label'] ) . '</label></th>';
					echo '<td><fieldset>';
					echo '<input type="checkbox" id="noindex_seo_' . esc_attr( $field_id ) . '" name="noindex_seo_' . esc_attr( $field_id ) . '" value="1" ' . checked( 1, $option, false ) . '> ';
					echo esc_html( $field['recommended'] ) . ': <span class="dashicons ' . ( $option ? 'dashicons-yes' : 'dashicons-no' ) . '" title="' . ( $option ? 'Yes' : 'No' ) . '"></span>. ';
					echo '<span class="description">' . esc_html( $field['description'] ) . '</span>';

					if ( isset( $field['view_url'] ) ) {
						echo ' <a href="' . esc_url( $field['view_url'] ) . '" target="_blank">' . esc_html__( 'View', 'noindex-seo' ) . '</a>';
					}

					echo '</fieldset></td>';
					echo '</tr>';
				}
				echo '</table>';
			}
			?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
