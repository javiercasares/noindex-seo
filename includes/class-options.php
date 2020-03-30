<?php
/**
 * Admin defaults
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
class NP_Admin {
	/**
	 * Construct of Class
	 */
	public function __construct() {
		// Create custom plugin settings menu.
		add_action( 'admin_menu', array( $this, 'plugin_create_menu' ) );
	}
	/**
	 * # Plugin Settings
	 * ---------------------------------------------------------------------------------------------------- */

	/**
	 * Create Menu option
	 *
	 * @return void
	 */
	public function plugin_create_menu() {

		add_submenu_page(
			'options-general.php',
			__( 'No Index Settings', 'noindex-seo' ),
			__( 'NoIndex', 'noindex-seo' ),
			'manage_options',
			'noindex_pages_settings',
			array(
				$this,
				'plugin_settings_page',
			),
		);

		// Call register settings function.
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Registers Settings for plugin
	 *
	 * @return void
	 */
	public function register_settings() {
		global $noindex_options;
		// Register our settings.
		register_setting( 'noindex_pages_settings', 'noindex_seo_type' );

		foreach ( $noindex_options as $key => $value ) {
			register_setting( 'noindex_pages_settings', 'noindex_' . $key );
		}
	}


	/**
	 * Options page for ERP Plugin
	 *
	 * @return void
	 */
	public function plugin_settings_page() {
		global $noindex_options;
		$noindex_seo_type = get_option( 'noindex_seo_type' );
		?>
		<div class="wrap">
		<h1><?php esc_html_e( 'No Index General Settings', 'noindex-seo' ); ?></h1>

		<form method="post" action="options.php">
			<?php settings_fields( 'noindex_pages_settings' ); ?>
			<?php do_settings_sections( 'noindex_pages_settings' ); ?>
			<h2><?php esc_html_e( 'Options', 'noindex-seo' ); ?></h2>

<?php
	$noindex_pages_error = 1;
	switch((int)get_option( 'noindex_pages_error' )) {
		case 0:
			$noindex_pages_error = 0;
			break;
		case 1:
		default: 
			$noindex_pages_error = 1;
			break;
	}
	$noindex_pages_archive = 1;
	switch((int)get_option( 'noindex_pages_archive' )) {
		case 0:
			$noindex_pages_archive = 0;
			break;
		case 1:
		default: 
			$noindex_pages_archive = 1;
			break;
	}
	$noindex_pages_attachment = 1;
	switch((int)get_option( 'noindex_pages_attachment' )) {
		case 0:
			$noindex_pages_attachment = 0;
			break;
		case 1:
		default: 
			$noindex_pages_attachment = 1;
			break;
	}
	$noindex_pages_author = 0;
	switch((int)get_option( 'noindex_pages_author' )) {
		case 0:
		default: 
			$noindex_pages_author = 0;
			break;
		case 1:
			$noindex_pages_author = 1;
			break;
	}
	$noindex_pages_category = 0;
	switch((int)get_option( 'noindex_pages_category' )) {
		case 0:
		default: 
			$noindex_pages_category = 0;
			break;
		case 1:
			$noindex_pages_category = 1;
			break;
	}
	$noindex_pages_comment_feed = 1;
	switch((int)get_option( 'noindex_pages_comment_feed' )) {
		case 0:
			$noindex_pages_comment_feed = 0;
			break;
		case 1:
		default:
			$noindex_pages_comment_feed = 1;
			break;
	}
	$noindex_pages_customize_preview = 1;
	switch((int)get_option( 'noindex_pages_customize_preview' )) {
		case 0:
			$noindex_pages_customize_preview = 0;
			break;
		case 1:
		default: 
			$noindex_pages_customize_preview = 1;
			break;
	}
	$noindex_pages_date = 1;
	switch((int)get_option( 'noindex_pages_date' )) {
		case 0:
			$noindex_pages_date = 0;
			break;
		case 1:
		default: 
			$noindex_pages_date = 1;
			break;
	}
	$noindex_pages_day = 1;
	switch((int)get_option( 'noindex_pages_day' )) {
		case 0:
			$noindex_pages_day = 0;
			break;
		case 1:
		default: 
			$noindex_pages_day = 1;
			break;
	}
	$noindex_pages_feed = 1;
	switch((int)get_option( 'noindex_pages_feed' )) {
		case 0:
			$noindex_pages_feed = 0;
			break;
		case 1:
		default: 
			$noindex_pages_feed = 1;
			break;
	}
	$noindex_pages_front_page = 0;
	switch((int)get_option( 'noindex_pages_front_page' )) {
		case 0:
		default: 
			$noindex_pages_front_page = 0;
			break;
		case 1:
			$noindex_pages_front_page = 1;
			break;
	}
	$noindex_pages_home = 0;
	switch((int)get_option( 'noindex_pages_home' )) {
		case 0:
		default: 
			$noindex_pages_home = 0;
			break;
		case 1:
			$noindex_pages_home = 1;
			break;
	}
	$noindex_pages_month = 1;
	switch((int)get_option( 'noindex_pages_month' )) {
		case 0:
			$noindex_pages_month = 0;
			break;
		case 1:
		default: 
			$noindex_pages_month = 1;
			break;
	}
	$noindex_pages_page = 0;
	switch((int)get_option( 'noindex_pages_page' )) {
		case 0:
		default: 
			$noindex_pages_page = 0;
			break;
		case 1:
			$noindex_pages_page = 1;
			break;
	}
	$noindex_pages_paged = 1;
	switch((int)get_option( 'noindex_pages_paged' )) {
		case 0:
			$noindex_pages_paged = 0;
			break;
		case 1:
		default: 
			$noindex_pages_paged = 1;
			break;
	}
	$noindex_pages_post_type_archive = 1;
	switch((int)get_option( 'noindex_pages_post_type_archive' )) {
		case 0:
			$noindex_pages_post_type_archive = 0;
			break;
		case 1:
		default: 
			$noindex_pages_post_type_archive = 1;
			break;
	}
	$noindex_pages_preview = 1;
	switch((int)get_option( 'noindex_pages_preview' )) {
		case 0:
			$noindex_pages_preview = 0;
			break;
		case 1:
		default: 
			$noindex_pages_preview = 1;
			break;
	}
	$noindex_pages_privacy_policy = 1;
	switch((int)get_option( 'noindex_pages_privacy_policy' )) {
		case 0:
			$noindex_pages_privacy_policy = 0;
			break;
		case 1:
		default: 
			$noindex_pages_privacy_policy = 1;
			break;
	}
	$noindex_pages_robots = 1;
	switch((int)get_option( 'noindex_pages_robots' )) {
		case 0:
			$noindex_pages_robots = 0;
			break;
		case 1:
		default: 
			$noindex_pages_robots = 1;
			break;
	}
	$noindex_pages_search = 1;
	switch((int)get_option( 'noindex_pages_search' )) {
		case 0:
			$noindex_pages_search = 0;
			break;
		case 1:
		default: 
			$noindex_pages_search = 1;
			break;
	}
	$noindex_pages_single = 0;
	switch((int)get_option( 'noindex_pages_single' )) {
		case 0:
		default: 
			$noindex_pages_single = 0;
			break;
		case 1:
			$noindex_pages_single = 1;
			break;
	}
	$noindex_pages_singular = 0;
	switch((int)get_option( 'noindex_pages_singular' )) {
		case 0:
		default: 
			$noindex_pages_singular = 0;
			break;
		case 1:
			$noindex_pages_singular = 1;
			break;
	}
	$noindex_pages_tag = 0;
	switch((int)get_option( 'noindex_pages_tag' )) {
		case 0:
		default: 
			$noindex_pages_tag = 0;
			break;
		case 1:
			$noindex_pages_tag = 1;
			break;
	}
	$noindex_pages_tax = 0;
	switch((int)get_option( 'noindex_pages_tax' )) {
		case 0:
		default: 
			$noindex_pages_tax = 0;
			break;
		case 1:
			$noindex_pages_tax = 1;
			break;
	}
	$noindex_pages_time = 1;
	switch((int)get_option( 'noindex_pages_time' )) {
		case 0:
			$noindex_pages_time = 0;
			break;
		case 1:
		default: 
			$noindex_pages_time = 1;
			break;
	}
	$noindex_pages_year = 0;
	switch((int)get_option( 'noindex_pages_year' )) {
		case 0:
			$noindex_pages_year = 0;
			break;
		case 1:
		default: 
			$noindex_pages_year = 1;
			break;
	}
?>
			<h2><?php esc_html_e( 'GLOBAL IMPORTANT PAGES', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tbody>
        <tr>
          <th scope="row"><label for="noindex_pages_front_page"><?php _e('Front Page', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_front_page" name="noindex_pages_front_page" value="1"<?php if($noindex_pages_front_page) echo 'checked'; ?>></fieldset></td>
          <td>Recommended: No.<br>The front page of the site.</td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_pages_home"><?php _e('Home', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_home" name="noindex_pages_home" value="1"<?php if($noindex_pages_home) echo 'checked'; ?>></fieldset></td>
          <td>Recommended: No.<br>The home of the site.</td>
        </tr>
      </table>
			<h2><?php esc_html_e( 'PAGES / POSTS', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_pages_page"><?php _e('PAge', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_page" name="noindex_pages_page" value="1"<?php if($noindex_pages_page) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: No.<br>xxxxxxxxxxx.</td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_pages_privacy_policy"><?php _e('Privacy Policy', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_privacy_policy" name="noindex_pages_privacy_policy" value="1"<?php if($noindex_pages_privacy_policy) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: Yes.<br>xxxxxxxxxxx.</td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_pages_single"><?php _e('Single', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_single" name="noindex_pages_single" value="1"<?php if($noindex_pages_single) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: No.<br>xxxxxxxxxxx.</td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_pages_singular"><?php _e('Singular', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_singular" name="noindex_pages_singular" value="1"<?php if($noindex_pages_singular) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: No.<br>xxxxxxxxxxx.</td>
        </tr>
      </table>
			<h2><?php esc_html_e( 'CATEGORIES / TAGS', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_pages_category"><?php _e('Category', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_category" name="noindex_pages_category" value="1"<?php if($noindex_pages_category) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: No.<br>xxxxxxxxxxx.</td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_pages_tag"><?php _e('TAG', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_tag" name="noindex_pages_tag" value="1"<?php if($noindex_pages_tag) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: No.<br>xxxxxxxxxxx.</td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_pages_tax"><?php _e('Taxonomy', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_tax" name="noindex_pages_tax" value="1"<?php if($noindex_pages_tax) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: No.<br>xxxxxxxxxxx.</td>
        </tr>
      </table>
			<h2><?php esc_html_e( 'DATES', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_pages_date"><?php _e('Date', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_date" name="noindex_pages_date" value="1"<?php if($noindex_pages_date) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: Yes.<br>xxxxxxxxxxx.</td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_pages_day"><?php _e('Day', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_day" name="noindex_pages_day" value="1"<?php if($noindex_pages_day) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: Yes.<br>xxxxxxxxxxx.</td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_pages_month"><?php _e('Month', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_month" name="noindex_pages_month" value="1"<?php if($noindex_pages_month) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: Yes.<br>xxxxxxxxxxx.</td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_pages_time"><?php _e('Time', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_time" name="noindex_pages_time" value="1"<?php if($noindex_pages_time) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: Yes.<br>xxxxxxxxxxx.</td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_pages_year"><?php _e('Year', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_year" name="noindex_pages_year" value="1"<?php if($noindex_pages_year) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: Yes.<br>xxxxxxxxxxx.</td>
        </tr>
      </table>
			<h2><?php esc_html_e( 'ARCHIVE', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_pages_archive"><?php _e('Archive', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_archive" name="noindex_pages_archive" value="1"<?php if($noindex_pages_archive) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: Yes.<br>xxxxxxxxxxx.</td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_pages_author"><?php _e('Author', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_author" name="noindex_pages_author" value="1"<?php if($noindex_pages_author) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: No.<br>xxxxxxxxxxx.</td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_pages_post_type_archive"><?php _e('Post Type Archive', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_post_type_archive" name="noindex_pages_post_type_archive" value="1"<?php if($noindex_pages_post_type_archive) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: Yes.<br>xxxxxxxxxxx.</td>
        </tr>
      </table>
			<h2><?php esc_html_e( 'PAGINATION', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_pages_paged"><?php _e('Pagination', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_paged" name="noindex_pages_paged" value="1"<?php if($noindex_pages_paged) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: Yes.<br>xxxxxxxxxxx.</td>
        </tr>
      </table>
			<h2><?php esc_html_e( 'SEARCH', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_pages_search"><?php _e('Search', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_search" name="noindex_pages_search" value="1"<?php if($noindex_pages_search) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: Yes.<br>xxxxxxxxxxx.</td>
        </tr>
      </table>
			<h2><?php esc_html_e( 'ATTACHMENT', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_pages_attachment"><?php _e('attachment', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_attachment" name="noindex_pages_attachment" value="1"<?php if($noindex_pages_attachment) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: Yes.<br>xxxxxxxxxxx.</td>
        </tr>
      </table>
			<h2><?php esc_html_e( 'PREVIEW', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_pages_customize_preview"><?php _e('Customize Preview', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_customize_preview" name="noindex_pages_customize_preview" value="1"<?php if($noindex_pages_customize_preview) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: Yes.<br>xxxxxxxxxxx.</td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_pages_preview"><?php _e('Preview', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_preview" name="noindex_pages_preview" value="1"<?php if($noindex_pages_preview) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: Yes.<br>xxxxxxxxxxx.</td>
        </tr>
      </table>
			<h2><?php esc_html_e( 'ERROR', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_pages_error"><?php _e('Error', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_error" name="noindex_pages_error" value="1"<?php if($noindex_pages_error) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: Yes.<br>xxxxxxxxxxx.</td>
        </tr>
      </table>
			<h2><?php esc_html_e( 'FEED', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_pages_comment_feed"><?php _e('Comment feed', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_comment_feed" name="noindex_pages_comment_feed" value="1"<?php if($noindex_pages_comment_feed) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: Yes.<br>xxxxxxxxxxx.</td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_pages_feed"><?php _e('Feed', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_feed" name="noindex_pages_feed" value="1"<?php if($noindex_pages_feed) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: Yes.<br>xxxxxxxxxxx.</td>
        </tr>
      </table>
			<h2><?php esc_html_e( 'ROBOTS', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_pages_robots"><?php _e('robots.txt', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_pages_robots" name="noindex_pages_robots" value="1"<?php if($noindex_pages_robots) echo ' checked'; ?>></fieldset></td>
          <td>Recommended: Yes.<br>xxxxxxxxxxx.</td>
        </tr>
      </table>
			<?php submit_button(); ?>
		</form>
		</div>
		<?php
    unset($noindex_pages_error, $noindex_pages_archive, $noindex_pages_attachment, $noindex_pages_author, $noindex_pages_category, $noindex_pages_comment_feed, $noindex_pages_customize_preview, $noindex_pages_date, $noindex_pages_day, $noindex_pages_feed, $noindex_pages_front_page, $noindex_pages_home, $noindex_pages_month, $noindex_pages_page, $noindex_pages_paged, $noindex_pages_post_type_archive, $noindex_pages_preview, $noindex_pages_privacy_policy, $noindex_pages_robots, $noindex_pages_search, $noindex_pages_single, $noindex_pages_singular, $noindex_pages_tag, $noindex_pages_tax, $noindex_pages_time, $noindex_pages_year);
	}
}

new NP_Admin();
