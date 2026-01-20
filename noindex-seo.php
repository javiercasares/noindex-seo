<?php
/**
 * Plugin Name: noindex SEO
 * Plugin URI: https://wordpress.org/plugins/noindex-seo/
 * Description: Allows adding a meta-tag for robots noindex in specific parts of your WordPress site.
 * Requires at least: 6.6
 * Requires PHP: 7.2
 * Version: 2.0.0
 * Author: Javier Casares
 * Author URI: https://www.javiercasares.com/
 * License: GPL-2.0-or-later
 * License URI: https://spdx.org/licenses/GPL-2.0-or-later.html
 * Text Domain: noindex-seo
 * Domain Path: /languages
 *
 * @package noindex-seo
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || die( 'Bye bye!' );

/**
 * Outputs a 'noindex' directive using the configured implementation method.
 *
 * This function adds a 'noindex' directive to instruct search engines not to index
 * the current page. It supports three implementation methods:
 *
 * - 'meta': HTML meta tags via wp_robots filter (default)
 * - 'header': HTTP X-Robots-Tag header
 * - 'both': Both HTML meta tags and HTTP headers
 *
 * The HTTP header method is more robust and works with non-HTML content (PDFs, images, feeds).
 * The meta tag method is more visible and easier for users to verify.
 *
 * @since 1.1.0
 * @since 2.0.0 Removed fallback for WordPress < 5.7 (now requires 6.6+).
 * @since 2.0.0 Added support for HTTP X-Robots-Tag headers and multiple implementation methods.
 *
 * @see https://developer.wordpress.org/reference/hooks/wp_robots/
 * @see https://developers.google.com/search/docs/crawling-indexing/robots-meta-tag
 *
 * @param string $method Implementation method: 'meta', 'header', or 'both'. Default 'meta'.
 * @return void
 */
function noindex_seo_metarobots( string $method = 'meta' ): void {
	// Sanitize method.
	$valid_methods = array( 'meta', 'header', 'both' );
	$method        = in_array( $method, $valid_methods, true ) ? $method : 'meta';

	// Send HTTP header if requested.
	if ( in_array( $method, array( 'header', 'both' ), true ) && ! headers_sent() ) {
		header( 'X-Robots-Tag: noindex', false );
	}

	// Add HTML meta tag if requested.
	if ( in_array( $method, array( 'meta', 'both' ), true ) ) {
		add_filter(
			'wp_robots',
			function ( array $robots ): array {
				$robots['noindex'] = true;
				return $robots;
			}
		);
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
function noindex_seo_show(): void {
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

	// Validate filtered contexts to prevent injection of invalid option names.
	if ( is_array( $contexts ) ) {
		foreach ( $contexts as $context => $option_key ) {
			// Ensure option_key follows expected pattern.
			if ( ! is_string( $option_key ) || 0 !== strpos( $option_key, 'noindex_seo_' ) ) {
				unset( $contexts[ $context ] );
			}
		}
	} else {
		// If contexts is not an array after filtering, reset to defaults.
		$contexts = array();
	}

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
		'privacy_policy'    => is_privacy_policy(),
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

	// Get implementation method configuration.
	$implementation_method = get_option( 'noindex_seo_config_method', 'meta' );

	// Iterate through the contexts and apply 'noindex' if the condition and setting are true.
	foreach ( $contexts as $context => $option_key ) {

		if (
			isset( $current_conditions[ $context ] ) &&
			$current_conditions[ $context ] &&
			isset( $options[ $option_key ] ) &&
			(bool) $options[ $option_key ]
		) {
			noindex_seo_metarobots( $implementation_method );

			break; // Prevent multiple meta tags from being added.
		}
	}

	unset( $contexts, $options, $current_conditions );
}

add_action( 'template_redirect', 'noindex_seo_show' );
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'noindex_seo_settings_link' );
add_action( 'admin_init', 'noindex_seo_register' );
add_action( 'admin_menu', 'noindex_seo_menu' );
add_action( 'admin_enqueue_scripts', 'noindex_seo_enqueue_admin_assets' );

/**
 * Enqueues admin CSS and JavaScript assets.
 *
 * Loads the modern admin panel styles and interactive JavaScript only on the
 * noindex SEO settings page for better performance.
 *
 * @since 2.0.0
 *
 * @param string $hook The current admin page hook.
 * @return void
 */
function noindex_seo_enqueue_admin_assets( string $hook ): void {
	// Only load on our settings page..
	if ( 'settings_page_noindex_seo' !== $hook ) {
		return;
	}

	// Enqueue admin CSS..
	wp_enqueue_style(
		'noindex-seo-admin',
		plugins_url( 'assets/css/admin.css', __FILE__ ),
		array(),
		'2.0.0',
		'all'
	);

	// Enqueue admin JavaScript..
	wp_enqueue_script(
		'noindex-seo-admin',
		plugins_url( 'assets/js/admin.js', __FILE__ ),
		array( 'jquery' ),
		'2.0.0',
		true
	);

	// Localize script with translations..
	wp_localize_script(
		'noindex-seo-admin',
		'noindexSeoAdmin',
		array(
			'successMessage' => __( 'Settings saved successfully!', 'noindex-seo' ),
			'expandAll'      => __( 'Expand All', 'noindex-seo' ),
			'collapseAll'    => __( 'Collapse All', 'noindex-seo' ),
		)
	);
}

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
 * @param array $links Array of existing action links for the plugin.
 * @return array Modified array including the "Settings" link.
 */
function noindex_seo_settings_link( array $links ): array {
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
function noindex_seo_menu(): void {
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
function noindex_seo_register(): void {
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

	register_setting(
		'noindexseo',
		'noindex_seo_config_method',
		array(
			'type'              => 'string',
			'default'           => 'meta',
			'sanitize_callback' => function ( $value ): string {
				return in_array( $value, array( 'meta', 'header', 'both' ), true ) ? $value : 'meta';
			},
		)
	);

	// Hook to settings update to clear transient cache..
	// Note: Hook receives $old_value and $value parameters but we don't need them.
	add_action( 'update_option_noindexseo', 'noindex_seo_clear_transient', 10, 0 );
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
 * @since 2.0.0 Added admin context verification for security.
 *
 * @return void
 */
function noindex_seo_clear_transient(): void {
	// Verify we're in a valid admin context..
	if ( ! is_admin() && ! wp_doing_ajax() ) {
		return;
	}

	// Delete the transient cache..
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
function noindex_seo_detect_conflicts(): void {

	$option_config_seoplugins = get_option( 'noindex_seo_config_seoplugins', 0 );

	if ( ! absint( $option_config_seoplugins ) ) {

		// Include the plugin.php file if the function is not available..
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Define an associative array of conflicting plugins: slug/file => real plugin name..
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

		// Iterate through the conflicting plugins to check if any are active..
		foreach ( $conflicting_plugins as $plugin_path => $plugin_name ) {
			if ( is_plugin_active( $plugin_path ) ) {
				// Add an admin notice if a conflicting plugin is active..
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
function noindex_seo_process_form(): void {
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

	// Get the implementation method to validate field compatibility.
	$method_value = isset( $_POST['noindex_seo_config_method'] )
		? sanitize_text_field( wp_unslash( $_POST['noindex_seo_config_method'] ) )
		: 'meta';

	// Validate method.
	$method_value = in_array( $method_value, array( 'meta', 'header', 'both' ), true ) ? $method_value : 'meta';

	// Fields that only work with HTTP headers.
	$header_only_fields = array( 'attachment', 'feed', 'comment_feed' );
	$is_header_enabled  = in_array( $method_value, array( 'header', 'both' ), true );

	// Reset all options to 0..
	foreach ( $settings as $setting ) {
		update_option( 'noindex_seo_' . $setting, 0 );
	}

	// Save only active options (checked checkboxes)..
	foreach ( $settings as $setting ) {
		$option_key   = 'noindex_seo_' . $setting;
		$option_value = isset( $_POST[ $option_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $option_key ] ) ) : '';

		// Validate: header-only fields require header implementation method.
		if ( in_array( $setting, $header_only_fields, true ) && ! $is_header_enabled ) {
			// Force to 0 - this field doesn't work with current method.
			update_option( $option_key, 0 );
			continue;
		}

		// Only set to 1 if the checkbox was actually checked (value should be "1").
		if ( '1' === $option_value ) {
			update_option( $option_key, 1 );
		}
	}

	// Save general configuration option..
	$config_value = isset( $_POST['noindex_seo_config_seoplugins'] )
		? absint( $_POST['noindex_seo_config_seoplugins'] )
		: 0;

	// Ensure value is either 0 or 1..
	$config_value = ( 1 === $config_value ) ? 1 : 0;

	update_option( 'noindex_seo_config_seoplugins', $config_value );

	// Save implementation method configuration (already validated above).
	update_option( 'noindex_seo_config_method', $method_value );

	// Clear cache..
	delete_transient( 'noindex_seo_options' );

	wp_safe_redirect( admin_url( 'options-general.php?page=noindex_seo&updated=true' ) );
	exit;
}
add_action( 'admin_post_update_noindex_seo', 'noindex_seo_process_form' );

/**
 * Renders the modern, visual settings page for the 'noindex SEO' plugin.
 *
 * This function outputs a completely redesigned admin interface with:
 * - Modern card-based layout
 * - Toggle switches instead of checkboxes
 * - Tabbed navigation for better organization
 * - Visual indicators and badges
 * - Collapsible sections
 * - Search/filter functionality
 * - Statistics dashboard
 *
 * @since 1.0.0
 * @since 2.0.0 Completely redesigned with modern UI/UX.
 *
 * @return void
 */
function noindex_seo_admin(): void {
	// Verify user capabilities for defense in depth..
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die(
			esc_html__( 'You do not have sufficient permissions to access this page.', 'noindex-seo' ),
			esc_html__( 'Permission Denied', 'noindex-seo' ),
			array( 'response' => 403 )
		);
	}

	// Define section icons (using Dashicons)..
	$section_icons = array(
		'main_pages'  => 'dashicons-admin-home',
		'pages_posts' => 'dashicons-admin-page',
		'taxonomies'  => 'dashicons-category',
		'dates'       => 'dashicons-calendar-alt',
		'archives'    => 'dashicons-archive',
		'pagination'  => 'dashicons-ellipsis',
		'search'      => 'dashicons-search',
		'attachments' => 'dashicons-paperclip',
		'previews'    => 'dashicons-visibility',
		'error_page'  => 'dashicons-warning',
	);

	// Define sections and their respective settings..
	$sections = array(
		'main_pages'  => array(
			'title'  => __( 'Main Pages', 'noindex-seo' ),
			'fields' => array(
				'front_page' => array(
					'label'       => __( 'Front Page', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => false,
					'description' => __( 'Block the indexing of the site\'s front page.', 'noindex-seo' ),
					'view_url'    => get_site_url(),
				),
				'home'       => array(
					'label'       => __( 'Home', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => false,
					'description' => __( 'Block the indexing of the site\'s home page.', 'noindex-seo' ),
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
					'description' => __( 'Block the indexing of site pages.', 'noindex-seo' ),
				),
				'privacy_policy' => array(
					'label'       => __( 'Privacy Policy', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => true,
					'description' => __( 'Block the indexing of the privacy policy page.', 'noindex-seo' ),
					'view_url'    => get_privacy_policy_url(),
				),
				'single'         => array(
					'label'       => __( 'Single Post', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => false,
					'description' => __( 'Block the indexing of individual posts.', 'noindex-seo' ),
				),
				'singular'       => array(
					'label'       => __( 'Singular', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => false,
					'description' => __( 'Block the indexing of any singular content (post or page).', 'noindex-seo' ),
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
					'description' => __( 'Block the indexing of category archive pages.', 'noindex-seo' ),
				),
				'tag'      => array(
					'label'       => __( 'Tag', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => false,
					'description' => __( 'Block the indexing of tag archive pages.', 'noindex-seo' ),
				),
			),
		),
		'dates'       => array(
			'title'  => __( 'Date Archives', 'noindex-seo' ),
			'fields' => array(
				'date'  => array(
					'label'       => __( 'Date', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => true,
					'description' => __( 'Block the indexing of any date-based archive page.', 'noindex-seo' ),
				),
				'day'   => array(
					'label'       => __( 'Day', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => true,
					'description' => __( 'Block the indexing of daily archive pages.', 'noindex-seo' ),
				),
				'month' => array(
					'label'       => __( 'Month', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => true,
					'description' => __( 'Block the indexing of monthly archive pages.', 'noindex-seo' ),
				),
				'time'  => array(
					'label'       => __( 'Time', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => true,
					'description' => __( 'Block the indexing of time-based archive pages.', 'noindex-seo' ),
				),
				'year'  => array(
					'label'       => __( 'Year', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => true,
					'description' => __( 'Block the indexing of yearly archive pages.', 'noindex-seo' ),
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
					'description' => __( 'Block the indexing of any type of archive page.', 'noindex-seo' ),
				),
				'author'            => array(
					'label'       => __( 'Author', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => false,
					'description' => __( 'Block the indexing of author archive pages.', 'noindex-seo' ),
				),
				'post_type_archive' => array(
					'label'       => __( 'Post Type Archive', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => false,
					'description' => __( 'Block the indexing of post type archive pages.', 'noindex-seo' ),
				),
			),
		),
		'pagination'  => array(
			'title'  => __( 'Pagination', 'noindex-seo' ),
			'fields' => array(
				'paged' => array(
					'label'       => __( 'Paginated Pages', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => true,
					'description' => __( 'Block the indexing of pagination pages (page 2, 3, etc.).', 'noindex-seo' ),
				),
			),
		),
		'search'      => array(
			'title'  => __( 'Search', 'noindex-seo' ),
			'fields' => array(
				'search' => array(
					'label'       => __( 'Search Results', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => true,
					'description' => __( 'Block the indexing of search result pages.', 'noindex-seo' ),
				),
			),
		),
		'attachments' => array(
			'title'  => __( 'Attachments', 'noindex-seo' ),
			'fields' => array(
				'attachment' => array(
					'label'       => __( 'Attachment Pages', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => true,
					'description' => __( 'Block the indexing of attachment pages (does not affect the file itself).', 'noindex-seo' ),
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
					'description' => __( 'Block the indexing when content is in customize preview mode.', 'noindex-seo' ),
				),
				'preview'           => array(
					'label'       => __( 'Post Preview', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => true,
					'description' => __( 'Block the indexing when viewing post previews.', 'noindex-seo' ),
				),
			),
		),
		'error_page'  => array(
			'title'  => __( 'Error Pages', 'noindex-seo' ),
			'fields' => array(
				'error' => array(
					'label'       => __( 'Error 404', 'noindex-seo' ),
					'recommended' => __( 'Recommended', 'noindex-seo' ),
					'suggestion'  => true,
					'description' => __( 'Block the indexing of 404 error pages.', 'noindex-seo' ),
				),
			),
		),
	);

	// Get config options.
	$option_config_seoplugins = get_option( 'noindex_seo_config_seoplugins', 0 );
	$option_config_method     = get_option( 'noindex_seo_config_method', 'meta' );

	// Define fields that only work with HTTP headers (non-HTML content).
	$header_only_fields = array( 'attachment', 'feed', 'comment_feed' );
	$is_header_enabled  = in_array( $option_config_method, array( 'header', 'both' ), true );
	?>

	<div class="wrap noindex-seo-admin-wrap">
		<h1><?php esc_html_e( 'noindex SEO Settings', 'noindex-seo' ); ?></h1>
		<p><?php esc_html_e( 'Control which pages search engines can index on your WordPress site.', 'noindex-seo' ); ?></p>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="update_noindex_seo">
			<?php wp_nonce_field( 'update_noindex_seo_nonce' ); ?>

			<!-- Statistics Dashboard -->
			<div class="noindex-seo-stats">
				<div class="noindex-seo-stat-card">
					<div class="noindex-seo-stat-number" id="noindex-seo-stat-total">0</div>
					<div class="noindex-seo-stat-label"><?php esc_html_e( 'Total Options', 'noindex-seo' ); ?></div>
				</div>
				<div class="noindex-seo-stat-card">
					<div class="noindex-seo-stat-number" id="noindex-seo-stat-enabled">0</div>
					<div class="noindex-seo-stat-label"><?php esc_html_e( 'Enabled', 'noindex-seo' ); ?></div>
				</div>
				<div class="noindex-seo-stat-card">
					<div class="noindex-seo-stat-number" id="noindex-seo-stat-recommended">0</div>
					<div class="noindex-seo-stat-label"><?php esc_html_e( 'Recommended to Enable', 'noindex-seo' ); ?></div>
				</div>
			</div>

			<!-- General Configuration -->
			<div class="noindex-seo-general-config">
				<h2>
					<span class="dashicons dashicons-admin-settings"></span>
					<?php esc_html_e( 'General Configuration', 'noindex-seo' ); ?>
				</h2>
				<div class="noindex-seo-config-option">
					<label class="noindex-seo-switch">
						<input
							type="checkbox"
							id="noindex_seo_config_seoplugins"
							name="noindex_seo_config_seoplugins"
							value="1"
							<?php checked( 1, $option_config_seoplugins ); ?>
						>
						<span class="noindex-seo-slider"></span>
					</label>
					<label for="noindex_seo_config_seoplugins">
						<?php esc_html_e( 'Disable compatibility warnings with other SEO plugins', 'noindex-seo' ); ?>
					</label>
				</div>

				<div class="noindex-seo-config-option" style="margin-top: 20px;">
					<div style="flex: 1;">
						<label for="noindex_seo_config_method" style="display: block; font-weight: 600; margin-bottom: 8px; color: #92400e;">
							<?php esc_html_e( 'Implementation Method', 'noindex-seo' ); ?>
						</label>
						<select
							id="noindex_seo_config_method"
							name="noindex_seo_config_method"
							style="width: 100%; max-width: 400px; padding: 8px; border: 1px solid #fcd34d; border-radius: 4px; background: #fff; color: #78350f;"
						>
							<option value="meta" <?php selected( $option_config_method, 'meta' ); ?>>
								<?php esc_html_e( 'HTML Meta Tags (default, easier to verify)', 'noindex-seo' ); ?>
							</option>
							<option value="header" <?php selected( $option_config_method, 'header' ); ?>>
								<?php esc_html_e( 'HTTP Headers (more robust, works with PDFs/images)', 'noindex-seo' ); ?>
							</option>
							<option value="both" <?php selected( $option_config_method, 'both' ); ?>>
								<?php esc_html_e( 'Both (maximum compatibility)', 'noindex-seo' ); ?>
							</option>
						</select>
						<p style="margin: 8px 0 0 0; font-size: 13px; color: #92400e; line-height: 1.5;">
							<?php esc_html_e( 'Choose how noindex directives are sent to search engines. Meta tags work for HTML pages. HTTP headers work for all content types including PDFs, images, and feeds.', 'noindex-seo' ); ?>
						</p>
					</div>
				</div>
			</div>

			<!-- Alert -->
			<div class="noindex-seo-alert">
				<span class="dashicons dashicons-warning"></span>
				<p><?php esc_html_e( 'Important: Enabling noindex on the wrong pages can harm your search engine rankings. Only enable options you fully understand.', 'noindex-seo' ); ?></p>
			</div>

			<!-- Search Box -->
			<div class="noindex-seo-search">
				<input
					type="search"
					placeholder="<?php esc_attr_e( 'Search options...', 'noindex-seo' ); ?>"
					aria-label="<?php esc_attr_e( 'Search options', 'noindex-seo' ); ?>"
				>
			</div>

			<!-- Sections as Cards -->
			<?php foreach ( $sections as $section_id => $section ) : ?>
				<?php
				$icon = isset( $section_icons[ $section_id ] ) ? $section_icons[ $section_id ] : 'dashicons-admin-generic';
				?>
				<div class="noindex-seo-card" id="noindex-seo-card-<?php echo esc_attr( $section_id ); ?>">
					<div class="noindex-seo-card-header">
						<h3>
							<span class="dashicons <?php echo esc_attr( $icon ); ?>"></span>
							<?php echo esc_html( $section['title'] ); ?>
						</h3>
						<span class="dashicons dashicons-arrow-down-alt2 noindex-seo-card-toggle"></span>
					</div>
					<div class="noindex-seo-card-body">
						<?php foreach ( $section['fields'] as $field_id => $field ) : ?>
							<?php
							// Check for conditional display..
							if ( isset( $field['conditional'] ) && ! $field['conditional'] ) {
								continue;
							}

							// Get current option value..
							$option = get_option( 'noindex_seo_' . $field_id, 0 );

							// Prepare badge class..
							$badge_class = $field['suggestion'] ? 'recommended' : 'not-recommended';

							// Check if field should be disabled (header-only fields with meta method).
							$should_disable = in_array( $field_id, $header_only_fields, true ) && ! $is_header_enabled;
							?>
							<div class="noindex-seo-option<?php echo $should_disable ? ' disabled' : ''; ?>"<?php echo $should_disable ? ' title="' . esc_attr__( 'This option only works with HTTP Headers implementation method', 'noindex-seo' ) . '"' : ''; ?>>
								<div class="noindex-seo-option-toggle">
									<label class="noindex-seo-switch">
										<input
											type="checkbox"
											id="noindex_seo_<?php echo esc_attr( $field_id ); ?>"
											name="noindex_seo_<?php echo esc_attr( $field_id ); ?>"
											value="1"
											<?php checked( 1, $option ); ?>
											<?php disabled( $should_disable ); ?>
										>
										<span class="noindex-seo-slider"></span>
									</label>
								</div>
								<div class="noindex-seo-option-content">
									<div class="noindex-seo-option-label">
										<label for="noindex_seo_<?php echo esc_attr( $field_id ); ?>">
											<?php echo esc_html( $field['label'] ); ?>
										</label>
										<span class="noindex-seo-badge <?php echo esc_attr( $badge_class ); ?>">
											<span class="dashicons <?php echo $field['suggestion'] ? 'dashicons-yes' : 'dashicons-no'; ?>"></span>
											<?php echo $field['suggestion'] ? esc_html__( 'Recommended', 'noindex-seo' ) : esc_html__( 'Not Recommended', 'noindex-seo' ); ?>
										</span>
									</div>
									<p class="noindex-seo-option-description">
										<?php echo esc_html( $field['description'] ); ?>
									</p>
									<?php if ( isset( $field['view_url'] ) && ! empty( $field['view_url'] ) ) : ?>
										<div class="noindex-seo-option-meta">
											<a href="<?php echo esc_url( $field['view_url'] ); ?>" target="_blank" class="noindex-seo-view-link">
												<span class="dashicons dashicons-external"></span>
												<?php esc_html_e( 'View Page', 'noindex-seo' ); ?>
											</a>
										</div>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>

			<?php submit_button(); ?>
		</form>
	</div>

	<?php
}
