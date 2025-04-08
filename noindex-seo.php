<?php
/**
 * Plugin Name: noindex SEO
 * Plugin URI: https://wordpress.org/plugins/noindex-seo/
 * Description: Allows adding a meta-tag for robots noindex in specific parts of your WordPress site.
 * Requires at least: 4.1
 * Requires PHP: 5.6
 * Version: 1.2.0
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
 * Outputs a 'noindex' directive in the meta robots tag.
 *
 * This function adds a 'noindex' directive to the robots meta tag to instruct search engines
 * not to index the current page. It uses the `wp_robots` filter if available (WordPress 5.7+),
 * or falls back to echoing a raw meta tag for older WordPress versions.
 *
 * Intended to be called only when certain conditions are met, such as in specific templates
 * or based on plugin configuration.
 *
 * @since 1.1.0
 *
 * @see https://developer.wordpress.org/reference/hooks/wp_robots/
 *
 * @return void
 */
function noindex_seo_metarobots() {

	if ( function_exists( 'wp_robots' ) ) {
		add_filter(
			'wp_robots',
			function ( $robots ) {
				$robots['noindex'] = true;
				return $robots;
			}
		);
	} else {
		echo '<meta name="robots" content="noindex">' . "\n";
	}
}

/**
 * Determines whether to output a 'noindex' meta tag based on page context and plugin settings.
 *
 * This function checks the current page context (e.g., single post, category archive, 404 page, etc.)
 * and evaluates plugin settings to determine if a 'noindex' directive should be added to the meta robots tag.
 *
 * It retrieves settings efficiently using a transient cache. If the cache is not set, it pulls values
 * from the WordPress options API and rebuilds the cache.
 *
 * The list of contexts and their associated option keys can be filtered via the {@see 'noindex_seo_contexts'} filter.
 * Once a matching context with 'noindex' enabled is found, it calls {@see noindex_seo_metarobots()} to apply the directive.
 *
 * @since 1.1.0
 *
 * @global WP_Post $post The global post object, if available.
 *
 * @return void
 */
function noindex_seo_show() {
	/**
	 * Filter the contexts and corresponding option keys used for noindex.
	 *
	 * @since 1.0.0.
	 *
	 * @param array $contexts Associative array of context => option_key.
	 */
	$contexts = apply_filters(
		'noindex_seo_contexts',
		array(
			'single'            => 'noindex_seo_single',
			'page'              => 'noindex_seo_page',
			'privacy_policy'    => 'noindex_seo_privacy_policy',
			'attachment'        => 'noindex_seo_attachment',
			'category'          => 'noindex_seo_category',
			'tag'               => 'noindex_seo_tag',
			'author'            => 'noindex_seo_author',
			'post_type_archive' => 'noindex_seo_post_type_archive',
			'date'              => 'noindex_seo_date',
			'day'               => 'noindex_seo_day',
			'month'             => 'noindex_seo_month',
			'year'              => 'noindex_seo_year',
			'archive'           => 'noindex_seo_archive',
			'search'            => 'noindex_seo_search',
			'error'             => 'noindex_seo_error',
			'front_page'        => 'noindex_seo_front_page',
			'home'              => 'noindex_seo_home',
			'singular'          => 'noindex_seo_singular',
			'paged'             => 'noindex_seo_paged',
			'preview'           => 'noindex_seo_preview',
			'customize_preview' => 'noindex_seo_customize_preview',
			'time'              => 'noindex_seo_time',
		)
	);

	// Try to get the options from the transient.
	$options = get_transient( 'noindex_seo_options' );

	if ( false === $options || empty( $options ) ) {
		// Transient not set, retrieve options from the database.
		$options = array();

		foreach ( $contexts as $context => $option_key ) {
			$options[ $option_key ] = get_option( $option_key, 0 );
		}

		// Set the transient for 1 hour to cache the options.
		set_transient( 'noindex_seo_options', $options, HOUR_IN_SECONDS );
	}

	// Define current conditions, ordered from most specific to most general.
	$current_conditions = array(
		'single'            => is_single(),
		'page'              => is_page(),
		'attachment'        => is_attachment(),
		'privacy_policy'    => function_exists( 'is_privacy_policy' ) ? is_privacy_policy() : false,
		'category'          => is_category(),
		'tag'               => is_tag(),
		'author'            => is_author(),
		'post_type_archive' => is_post_type_archive(),
		'day'               => is_day(),
		'month'             => is_month(),
		'year'              => is_year(),
		'time'              => is_time(),
		'date'              => is_date() && ! ( is_day() || is_month() || is_year() || is_time() ),
		'archive'           => is_archive() && ! ( is_category() || is_tag() || is_author() || is_post_type_archive() || is_date() ),
		'search'            => is_search(),
		'error'             => is_404(),
		'front_page'        => is_front_page() && ! is_paged() && ! is_home(),
		'home'              => is_home() && ! is_paged(),
		'singular'          => is_singular() && ! ( is_single() || is_page() || is_attachment() ),
		'paged'             => is_paged() && ! is_front_page() && ! is_home(),
		'preview'           => is_preview(),
		'customize_preview' => is_customize_preview(),
	);

	// Iterate through the contexts and apply 'noindex' if the condition and setting are true.
	foreach ( $contexts as $context => $option_key ) {

		if (
			isset( $current_conditions[ $context ] ) &&
			$current_conditions[ $context ] &&
			isset( $options[ $option_key ] ) &&
			(bool) $options[ $option_key ]
		) {
			noindex_seo_metarobots();

			break; // Prevent multiple meta tags from being added.
		}
	}

	unset( $contexts, $options, $current_conditions );
}

add_action( 'template_redirect', 'noindex_seo_show' );
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'noindex_seo_settings_link' );
add_action( 'admin_init', 'noindex_seo_register' );
add_action( 'admin_menu', 'noindex_seo_menu' );

/**
 * Adds a "Settings" link to the plugin row actions on the Plugins admin screen.
 *
 * This function appends a direct link to the plugin's settings page within the list of action links
 * shown for the plugin on the Plugins page (`/wp-admin/plugins.php`). This improves user accessibility
 * by allowing quick access to the plugin's configuration page.
 *
 * Hooked to the {@see 'plugin_action_links_{plugin_basename}'} filter.
 *
 * @since 1.0.0
 *
 * @param string[] $links Array of existing action links for the plugin.
 * @return string[] Modified array including the "Settings" link.
 */
function noindex_seo_settings_link( $links ) {
	$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=noindex_seo' ) ) . '">' . esc_html__( 'Settings', 'noindex-seo' ) . '</a>';
	$links[]       = $settings_link;
	return $links;
}

/**
 * Registers the "noindex SEO" settings page in the WordPress admin menu.
 *
 * This function adds an entry under the "Settings" menu in the WordPress admin area,
 * which links to the plugin's main configuration page. The settings page is only accessible
 * to users with the 'manage_options' capability.
 *
 * Internally uses {@see add_options_page()} to register the page.
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
 * Registers all settings used by the 'noindex SEO' plugin.
 *
 * This function registers individual options for each context in which the plugin
 * may apply a 'noindex' directive (e.g., single posts, category pages, archives, etc.).
 * Each setting is stored as an integer (0 or 1), where 1 indicates that 'noindex' is enabled
 * for that context.
 *
 * All settings are grouped under the option group 'noindexseo' and will be handled by the
 * WordPress Settings API when the options form is submitted.
 *
 * Also registers the general configuration option 'noindex_seo_config_seoplugins'.
 * A transient cache is cleared upon update using the {@see 'update_option_noindexseo'} action.
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

	register_setting(
		'noindexseo',
		'noindex_seo_config_seoplugins',
		array(
			'type'    => 'integer',
			'default' => 0,
		)
	);

	// Hook to settings update to clear transient cache.
	add_action( 'update_option_noindexseo', 'noindex_seo_clear_transient', 10, 2 );
}

/**
 * Clears the cached plugin settings stored in the transient.
 *
 * This function deletes the 'noindex_seo_options' transient to ensure that updated
 * option values are fetched fresh from the database on the next request. It is typically
 * triggered after the plugin settings are updated to prevent stale data from being used.
 *
 * Hooked to the {@see 'update_option_noindexseo'} action.
 *
 * @since 1.0.0
 *
 * @return void
 */
function noindex_seo_clear_transient() {
	delete_transient( 'noindex_seo_options' );
}

/**
 * Detects potential conflicts with other SEO plugins and displays an admin notice.
 *
 * This function checks for the presence of known SEO plugins that may conflict with
 * the functionality of 'noindex SEO'. If a conflicting plugin is active and the user
 * has not opted to suppress warnings (via the 'noindex_seo_config_seoplugins' option),
 * a dismissible admin notice is displayed to alert the site administrator.
 *
 * The list of conflicting plugins includes popular SEO tools such as Yoast SEO, Rank Math,
 * SEOPress, and others. The check is performed using {@see is_plugin_active()}.
 *
 * Hooked to the {@see 'admin_init'} action.
 *
 * @since 1.1.0
 *
 * @return void
 */
function noindex_seo_detect_conflicts() {

	$option_config_seoplugins = get_option( 'noindex_seo_config_seoplugins', 0 );

	if ( ! absint( $option_config_seoplugins ) ) {

		// Include the plugin.php file if the function is not available.
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Define an associative array of conflicting plugins: slug/file => real plugin name.
		$conflicting_plugins = array(
			'all-in-one-seo-pack/all_in_one_seo_pack.php' => 'All in One SEO',
			'premium-seo-pack/index.php'                  => 'Premium SEO Pack',
			'seo-by-rank-math/rank-math.php'              => 'Rank Math SEO',
			'wp-seopress/seopress.php'                    => 'SEOPress',
			'slim-seo/slim-seo.php'                       => 'Slim SEO',
			'squirrly-seo/squirrly.php'                   => 'Squirrly SEO',
			'autodescription/autodescription.php'         => 'The SEO Framework',
			'wordpress-seo/wp-seo.php'                    => 'Yoast SEO',
		);

		// Iterate through the conflicting plugins to check if any are active.
		foreach ( $conflicting_plugins as $plugin_path => $plugin_name ) {
			if ( is_plugin_active( $plugin_path ) ) {
				// Add an admin notice if a conflicting plugin is active.
				add_action(
					'admin_notices',
					function () use ( $plugin_name ) {
						echo '<div class="notice notice-warning is-dismissible"><p>';
						// translators: plugin name.
						printf( esc_html__( 'noindex SEO has detected that %s is active. This may cause conflicts. Please configure the options accordingly.', 'noindex-seo' ), esc_html( $plugin_name ) );
						echo '</p></div>';
					}
				);
				break; // Stop checking after finding the first conflict.
			}
		}
	}
}
add_action( 'admin_init', 'noindex_seo_detect_conflicts' );

/**
 * Processes the form submission for the 'noindex SEO' plugin settings.
 *
 * This function handles the saving of plugin options submitted from the custom admin form.
 * It first verifies the current user's capability and nonce for security. Then it resets all
 * registered context options to `0`, and selectively updates those submitted as checked in the form.
 *
 * Additionally, it updates the general configuration setting `noindex_seo_config_seoplugins`,
 * clears the plugin's transient cache, and redirects back to the settings page.
 *
 * Hooked to the {@see 'admin_post_update_noindex_seo'} action.
 *
 * @since 1.2.0
 *
 * @return void
 */
function noindex_seo_process_form() {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'update_noindex_seo_nonce' ) ) {
		wp_die( esc_html__( 'Permission denied or invalid nonce.', 'noindex-seo' ) );
	}

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

	// Reset all options to 0.
	foreach ( $settings as $setting ) {
		update_option( 'noindex_seo_' . $setting, 0 );
	}

	// Save only active options (checked checkboxes).
	foreach ( $settings as $setting ) {
		if ( isset( $_POST[ 'noindex_seo_' . $setting ] ) ) {
			update_option( 'noindex_seo_' . $setting, 1 );
		}
	}

	// Save general configuration option.
	update_option(
		'noindex_seo_config_seoplugins',
		isset( $_POST['noindex_seo_config_seoplugins'] ) ? 1 : 0
	);

	// Clear cache.
	delete_transient( 'noindex_seo_options' );

	wp_safe_redirect( admin_url( 'options-general.php?page=noindex_seo&updated=true' ) );
	exit;
}
add_action( 'admin_post_update_noindex_seo', 'noindex_seo_process_form' );

/**
 * Renders the settings page for the 'noindex SEO' plugin in the WordPress admin.
 *
 * This function outputs the full HTML for the plugin's settings interface, including:
 * - General configuration (e.g., disabling conflict notices)
 * - A structured list of SEO-related options grouped by context (main pages, archives, taxonomies, etc.)
 *
 * Each context is represented as a checkbox that allows the administrator to enable or disable
 * the `noindex` meta directive for that specific section of the site.
 *
 * The form is submitted via `admin-post.php` and processed by {@see noindex_seo_process_form()}.
 * Security is enforced with a nonce field. Options are retrieved using `get_option()` for each field.
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
					'suggestion'  => false,
					'description' => __( 'This will block the indexing of the site\'s front page.', 'noindex-seo' ),
					'view_url'    => get_site_url(),
				),
				'home'       => array(
					'label'       => __( 'Home', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => false,
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
					'suggestion'  => false,
					'description' => __( 'This will block the indexing of the site\'s pages.', 'noindex-seo' ),
				),
				'privacy_policy' => array(
					'label'       => __( 'Privacy Policy', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => true,
					'description' => __( 'This will block the indexing of the site\'s privacy policy page.', 'noindex-seo' ),
					'view_url'    => get_privacy_policy_url(),
					'conditional' => version_compare( $GLOBALS['wp_version'], '5.2', '>=' ),
				),
				'single'         => array(
					'label'       => __( 'Single', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => false,
					'description' => __( 'This will block the indexing of a post on the site.', 'noindex-seo' ),
				),
				'singular'       => array(
					'label'       => __( 'Singular', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => false,
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
					'suggestion'  => false,
					'description' => __( 'This will block the indexing of the site categories. The lists where the posts appear.', 'noindex-seo' ),
				),
				'tag'      => array(
					'label'       => __( 'Tag', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => false,
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
					'suggestion'  => true,
					'description' => __( 'This will block the indexing of any date-based archive page (i.e., monthly, yearly, daily, or time-based archive) of the site. The lists where the posts appear.', 'noindex-seo' ),
				),
				'day'   => array(
					'label'       => __( 'Day', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => true,
					'description' => __( 'This will block the indexing of a daily archive of the site. The lists where the posts appear.', 'noindex-seo' ),
				),
				'month' => array(
					'label'       => __( 'Month', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => true,
					'description' => __( 'This will block the indexing of a monthly archive of the site. The lists where the posts appear.', 'noindex-seo' ),
				),
				'time'  => array(
					'label'       => __( 'Time', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => true,
					'description' => __( 'This will block the indexing of an hourly, "minutely", or "secondly" archive of the site. The lists where the posts appear.', 'noindex-seo' ),
				),
				'year'  => array(
					'label'       => __( 'Year', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => true,
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
					'suggestion'  => false,
					'description' => __( 'This will block the indexing of any type of Archive page. Category, Tag, Author, and Date-based pages are all types of Archives. The lists where the posts appear.', 'noindex-seo' ),
				),
				'author'            => array(
					'label'       => __( 'Author', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => false,
					'description' => __( 'This will block the indexing of the author\'s page, where the author\'s publications appear.', 'noindex-seo' ),
				),
				'post_type_archive' => array(
					'label'       => __( 'Post Type Archive', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => false,
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
					'suggestion'  => true,
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
					'suggestion'  => true,
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
					'suggestion'  => true,
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
					'suggestion'  => true,
					'description' => __( 'This will block the indexing when content is being displayed in customize mode.', 'noindex-seo' ),
				),
				'preview'           => array(
					'label'       => __( 'Preview', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => true,
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
					'suggestion'  => true,
					'description' => __( 'This will cause an error page to be blocked from being indexed. As it is an error page, it should not be indexed per se, but just in case.', 'noindex-seo' ),
				),
			),
		),
	);

	?>
	<div class="wrap">
		<h1><?php echo esc_html( __( 'noindex SEO Settings', 'noindex-seo' ) ); ?></h1>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="update_noindex_seo">
			<?php
			wp_nonce_field( 'update_noindex_seo_nonce' );

			echo '<h2>' . esc_html( __( 'General Configuration', 'noindex-seo' ) ) . '</h2>';
			echo '<table class="form-table">';
			// Get current configuration value.
			$option_config_seoplugins = get_option( 'noindex_seo_config_seoplugins', 0 );
			echo '<tr>';
			echo '<th scope="row"><label for="noindex_seo_config_seoplugins">' . esc_html( __( 'Plugin compatibility', 'noindex-seo' ) ) . '</label></th>';
			echo '<td><fieldset>';
			echo '<input type="checkbox" id="noindex_seo_config_seoplugins" name="noindex_seo_config_seoplugins" value="1" ' . checked( 1, $option_config_seoplugins, false ) . '> ';
			echo '<span class="description">' . esc_html( __( 'Do not display the message of possible incompatibilities with other plugins.', 'noindex-seo' ) ) . '</span>';
			echo '</fieldset></td>';
			echo '</tr>';
			echo '</table>';

			echo '<h2>' . esc_html( __( 'SEO Configuration', 'noindex-seo' ) ) . '</h2>';
			?>
			<p><?php echo esc_html( __( 'Important note: if you have any doubt about any of the following items, it is best not to activate the option as you could lose results in the search engines.', 'noindex-seo' ) ); ?></p>
			<?php
			foreach ( $sections as $section_id => $section ) {
				echo '<h3>' . esc_html( $section['title'] ) . '</h3>';
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
					echo esc_html( $field['recommended'] ) . ': <span class="dashicons ' . ( $field['suggestion'] ? 'dashicons-yes' : 'dashicons-no' ) . '" title="' . ( $field['suggestion'] ? 'Yes' : 'No' ) . '"></span>. ';

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
