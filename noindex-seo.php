<?php
/*
Plugin Name: noindex SEO
Plugin URI: https://wordpress.org/plugins/noindex-seo/
Description: Allows to add a meta-tag for robots noindex in some parts of your WordPress site.
Version: 1.1.0
Author: Javier Casares
Author URI: https://www.javiercasares.com/
License: EUPL 1.2
License URI: https://eupl.eu/1.2/en/
Text Domain: noindex-seo
*/
defined('ABSPATH') or die('Bye bye!');

function noindex_seo_show()
{
  global $post;
  
  $noindex_seo_values = array(
    'error' => (boolean) get_option( 'noindex_seo_error' ), 
    'archive' => (boolean) get_option( 'noindex_seo_archive' ),
    'attachment' => (boolean) get_option( 'noindex_seo_attachment' ),
    'author' => (boolean) get_option( 'noindex_seo_author' ),
    'category' => (boolean) get_option( 'noindex_seo_category' ),
    'comment_feed' => (boolean) get_option( 'noindex_seo_comment_feed' ),
    'customize_preview' => (boolean) get_option( 'noindex_seo_customize_preview' ),
    'date' => (boolean) get_option( 'noindex_seo_date' ),
    'day' => (boolean) get_option( 'noindex_seo_day' ),
    'feed' => (boolean) get_option( 'noindex_seo_feed' ),
    'front_page' => (boolean) get_option( 'noindex_seo_front_page' ),
    'home' => (boolean) get_option( 'noindex_seo_home' ),
    'month' => (boolean) get_option( 'noindex_seo_month' ),
    'page' => (boolean) get_option( 'noindex_seo_page' ),
    'paged' => (boolean) get_option( 'noindex_seo_paged' ),
    'post_type_archive' => (boolean) get_option( 'noindex_seo_post_type_archive' ),
    'preview' => (boolean) get_option( 'noindex_seo_preview' ),
    'privacy_policy' => (boolean) get_option( 'noindex_seo_privacy_policy' ),
    'robots' => (boolean) get_option( 'noindex_seo_robots' ),
    'search' => (boolean) get_option( 'noindex_seo_search' ),
    'single' => (boolean) get_option( 'noindex_seo_single' ),
    'singular' => (boolean) get_option( 'noindex_seo_singular' ),
    'tag' => (boolean) get_option( 'noindex_seo_tag' ),
    'time' => (boolean) get_option( 'noindex_seo_time' ),
    'year' => (boolean) get_option( 'noindex_seo_year' )
  );
  $enter = true;

  /*
    GLOBAL IMPORTANT PAGES
  */
  if($enter && $noindex_seo_values['front_page'] && is_front_page())
  {
    noindex_seo_metarobots();
    $enter = false;
  }
  if($enter && $noindex_seo_values['home'] && is_home())
  {
    noindex_seo_metarobots();
    $enter = false;
  }

  /*
    PAGES / POSTS
  */
  if($enter && $noindex_seo_values['page'] && is_page())
  {
    noindex_seo_metarobots();
    $enter = false;
  }
  if($enter && $noindex_seo_values['privacy_policy'] && is_privacy_policy())
  {
    noindex_seo_metarobots();
    $enter = false;
  }
  if($enter && $noindex_seo_values['single'] && is_single())
  {
    noindex_seo_metarobots();
    $enter = false;
  }
  if($enter && $noindex_seo_values['singular'] && is_singular())
  {
    noindex_seo_metarobots();
    $enter = false;
  }

  /*
    CATEGORIES / TAGS
  */
  if($enter && $noindex_seo_values['category'] && is_category())
  {
    noindex_seo_metarobots();
    $enter = false;
  }
  if($enter && $noindex_seo_values['tag'] && is_tag())
  {
    noindex_seo_metarobots();
    $enter = false;
  }

  /*
    DATES
  */
  if($enter && $noindex_seo_values['date'] && is_date())
  {
    noindex_seo_metarobots();
    $enter = false;
  }
  if($enter && $noindex_seo_values['day'] && is_day())
  {
    noindex_seo_metarobots();
    $enter = false;
  }
  if($enter && $noindex_seo_values['month'] && is_month())
  {
    noindex_seo_metarobots();
    $enter = false;
  }
  if($enter && $noindex_seo_values['time'] && is_time())
  {
    noindex_seo_metarobots();
    $enter = false;
  }
  if($enter && $noindex_seo_values['year'] && is_year())
  {
    noindex_seo_metarobots();
    $enter = false;
  }    

  /*
    ARCHIVE
  */
  if($enter && $noindex_seo_values['archive'] && is_archive())
  {
    noindex_seo_metarobots();
    $enter = false;
  }
  if($enter && $noindex_seo_values['author'] && is_author())
  {
    noindex_seo_metarobots();
    $enter = false;
  }
  if($enter && $noindex_seo_values['post_type_archive'] && is_post_type_archive())
  {
    noindex_seo_metarobots();
    $enter = false;
  }

  /*
    PAGINATION
  */
  if($enter && $noindex_seo_values['paged'] && is_paged())
  {
    noindex_seo_metarobots();
    $enter = false;
  }

  /*
    SEARCH
  */
  if($enter && $noindex_seo_values['search'] && is_search())
  {
    noindex_seo_metarobots();
    $enter = false;
  }

  /*
    ATTACHMENT
  */
  if($enter && $noindex_seo_values['attachment'] && is_attachment())
  {
    noindex_seo_metarobots();
    $enter = false;
  }

  /*
    PREVIEW
  */
  if($enter && $noindex_seo_values['customize_preview'] && is_customize_preview())
  {
    noindex_seo_metarobots();
    $enter = false;
  }
  if($enter && $noindex_seo_values['preview'] && is_preview())
  {
    noindex_seo_metarobots();
    $enter = false;
  }

  /*
    ERROR
  */
  if($enter && $noindex_seo_values['error'] && is_404())
  {
    noindex_seo_metarobots();
    $enter = false;
  }

  /*
    FEED
  */
  /*
  if($enter && $noindex_seo_values['comment_feed'] && is_comment_feed())
  {
    add_action( 'wp_headers', 'noindex_seo_xrobots' );
    $enter = false;
  }
  */
  /*
  if($enter && $noindex_seo_values['feed'] && is_feed())
  {
    add_action( 'wp_headers', 'noindex_seo_xrobots' );
    $enter = false;
  }
  */

  /*
    ROBOTS
  */
  /*
  if($enter && $noindex_seo_values['robots'] && is_robots()) {
    add_action( 'do_robotstxt', 'noindex_seo_xrobots' );
    $enter = false;
  }
  */

  unset($enter, $noindex_seo_values);
}
function noindex_seo_metarobots()
{
  echo '<meta name="robots" content="noindex">'."\n";
}  
/*
function noindex_seo_xrobots()
{

  header( 'X-Robots-Tag: noindex' );
  echo 'PARADO';
  exit;
  
  $headers['X-Robots-Tag'] = 'noindex';
  return $headers;
}
*/

add_action( 'wp_head', 'noindex_seo_show' );
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'noindex_seo_settings_link' );
add_action( 'admin_init', 'noindex_seo_register' );
add_action( 'admin_menu', 'noindex_seo_menu');

function noindex_seo_settings_link( $links )
{
  $links[] = '<a href="' . get_admin_url( null, 'options-general.php?page=noindex_seo' ) . '">' . _('Settings') . '</a>';
  return $links;
}

function noindex_seo_menu()
{
  add_options_page(__('noindex SEO', 'noindex-seo'), __('noindex SEO', 'noindex-seo'), 'manage_options', 'noindex_seo', 'noindex_seo_admin');	
}

function noindex_seo_register()
{
  register_setting( 'noindexseo', 'noindex_seo_error', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_archive', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_attachment', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_author', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_category', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_comment_feed', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_customize_preview', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_date', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_day', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_feed', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_front_page', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_home', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_month', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_page', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_paged', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_post_type_archive', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_preview', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_privacy_policy', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_robots', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_search', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_single', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_singular', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_tag', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_time', array('type' => 'integer', 'default' => 0) );
  register_setting( 'noindexseo', 'noindex_seo_year', array('type' => 'integer', 'default' => 0) );
}
  
function noindex_seo_admin() {
?>
		<div class="wrap">
		<h1><?php esc_html_e( 'noindex SEO Settings', 'noindex-seo' ); ?></h1>

		<form method="post" action="options.php">
  <?php
  settings_fields( 'noindexseo' );
  ?>
    <p><?php esc_html_e( 'Important note: if you have any doubt about any of the following items it is best not to activate the option as you could lose results in the search engines.', 'noindex-seo' ); ?></p>
<?php
	$noindex_seo_error = 0;
	if( (boolean) get_option( 'noindex_seo_error' ) ) $noindex_seo_error = 1;

	$noindex_seo_archive = 0;
	if( (boolean) get_option( 'noindex_seo_archive' ) ) $noindex_seo_archive = 1;

	$noindex_seo_attachment = 0;
	if( (boolean) get_option( 'noindex_seo_attachment' ) ) $noindex_seo_attachment = 1;

	$noindex_seo_author = 0;
	if( (boolean) get_option( 'noindex_seo_author' ) ) $noindex_seo_author = 1;

	$noindex_seo_category = 0;
	if( (boolean) get_option( 'noindex_seo_category' ) ) $noindex_seo_category = 1;

	$noindex_seo_comment_feed = 0;
	if( (boolean) get_option( 'noindex_seo_comment_feed' ) ) $noindex_seo_comment_feed = 1;

	$noindex_seo_customize_preview = 0;
	if( (boolean) get_option( 'noindex_seo_customize_preview' ) ) $noindex_seo_customize_preview = 1;

	$noindex_seo_date = 0;
	if( (boolean) get_option( 'noindex_seo_date' ) ) $noindex_seo_date = 1;

	$noindex_seo_day = 0;
	if( (boolean) get_option( 'noindex_seo_day' ) ) $noindex_seo_day = 1;

	$noindex_seo_feed = 0;
	if( (boolean) get_option( 'noindex_seo_feed' ) ) $noindex_seo_feed = 1;

	$noindex_seo_front_page = 0;
	if( (boolean) get_option( 'noindex_seo_front_page' ) ) $noindex_seo_front_page = 1;

	$noindex_seo_home = 0;
	if( (boolean) get_option( 'noindex_seo_home' ) ) $noindex_seo_home = 1;

	$noindex_seo_month = 0;
	if( (boolean) get_option( 'noindex_seo_month' ) ) $noindex_seo_month = 1;

	$noindex_seo_page = 0;
	if( (boolean) get_option( 'noindex_seo_page' ) ) $noindex_seo_page = 1;

	$noindex_seo_paged = 0;
	if( (boolean) get_option( 'noindex_seo_paged' ) ) $noindex_seo_paged = 1;

	$noindex_seo_post_type_archive = 0;
	if( (boolean) get_option( 'noindex_seo_post_type_archive' ) ) $noindex_seo_post_type_archive = 1;

	$noindex_seo_preview = 0;
	if( (boolean) get_option( 'noindex_seo_preview' ) ) $noindex_seo_preview = 1;

	$noindex_seo_privacy_policy = 0;
	if( (boolean) get_option( 'noindex_seo_privacy_policy' ) ) $noindex_seo_privacy_policy = 1;

	$noindex_seo_robots = 0;
	if( (boolean) get_option( 'noindex_seo_robots' ) ) $noindex_seo_robots = 1;

	$noindex_seo_search = 0;
	if( (boolean) get_option( 'noindex_seo_search' ) ) $noindex_seo_search = 1;

	$noindex_seo_single = 0;
	if( (boolean) get_option( 'noindex_seo_single' ) ) $noindex_seo_single = 1;

	$noindex_seo_singular = 0;
	if( (boolean) get_option( 'noindex_seo_singular' ) ) $noindex_seo_singular = 1;

	$noindex_seo_tag = 0;
	if( (boolean) get_option( 'noindex_seo_tag' ) ) $noindex_seo_tag = 1;

	$noindex_seo_time = 0;
	if( (boolean) get_option( 'noindex_seo_time' ) ) $noindex_seo_time = 1;

	$noindex_seo_year = 0;
	if( (boolean) get_option( 'noindex_seo_year' ) ) $noindex_seo_year = 1;

?>
			<h2><?php esc_html_e( 'Main pages', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_seo_front_page"><?php esc_html_e( 'Front Page', 'noindex-seo' ); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_front_page" name="noindex_seo_front_page" value="1"<?php if($noindex_seo_front_page) echo 'checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-no" title="No"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing of the site\'s front page.', 'noindex-seo' ); ?> <a href="<?php echo get_site_url(); ?>" target="_blank"><?php esc_html_e( 'View', 'noindex-seo' ); ?></a></span></fieldset></td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_seo_home"><?php _e('Home', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_home" name="noindex_seo_home" value="1"<?php if($noindex_seo_home) echo 'checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-no" title="No"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing of the site\'s home page.', 'noindex-seo' ); ?> <a href="<?php echo get_home_url(); ?>" target="_blank"><?php esc_html_e( 'View', 'noindex-seo' ); ?></a></span></fieldset></td>
        </tr>
      </table>
	
      <h2><?php esc_html_e( 'Pages and Posts', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_seo_page"><?php _e('Page', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_page" name="noindex_seo_page" value="1"<?php if($noindex_seo_page) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-no" title="No"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing of the site\'s pages.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_seo_privacy_policy"><?php _e('Privacy Policy', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_privacy_policy" name="noindex_seo_privacy_policy" value="1"<?php if($noindex_seo_privacy_policy) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-yes" title="Yes"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing of the site\'s privacy policy page.', 'noindex-seo' ); ?> <a href="<?php echo get_privacy_policy_url(); ?>" target="_blank"><?php esc_html_e( 'View', 'noindex-seo' ); ?></a></span></fieldset></td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_seo_single"><?php _e('Single', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_single" name="noindex_seo_single" value="1"<?php if($noindex_seo_single) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-no" title="No"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing of a post on the site.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_seo_singular"><?php _e('Singular', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_singular" name="noindex_seo_singular" value="1"<?php if($noindex_seo_singular) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-no" title="No"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing of a post or a page of the site.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
      </table>

			<h2><?php esc_html_e( 'Taxonomies', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_seo_category"><?php _e('Category', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_category" name="noindex_seo_category" value="1"<?php if($noindex_seo_category) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-no" title="No"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing of the site categories. The lists where the posts appear.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_seo_tag"><?php _e('Tag', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_tag" name="noindex_seo_tag" value="1"<?php if($noindex_seo_tag) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-no" title="No"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing of the site\'s tags. The lists where the posts appear.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
      </table>

			<h2><?php esc_html_e( 'Dates', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_seo_date"><?php _e('Date', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_date" name="noindex_seo_date" value="1"<?php if($noindex_seo_date) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-yes" title="Yes"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing when any date-based archive page (i.e. a monthly, yearly, daily or time-based archive) of the site. The lists where the posts appear.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_seo_day"><?php _e('Day', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_day" name="noindex_seo_day" value="1"<?php if($noindex_seo_day) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-yes" title="Yes"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing when a daily archive of the site. The lists where the posts appear.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_seo_month"><?php _e('Month', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_month" name="noindex_seo_month" value="1"<?php if($noindex_seo_month) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-yes" title="Yes"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing when a monthly archive of the site. The lists where the posts appear.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_seo_time"><?php _e('Time', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_time" name="noindex_seo_time" value="1"<?php if($noindex_seo_time) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-yes" title="Yes"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing when an hourly, "minutely", or "secondly" archive of the site. The lists where the posts appear.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_seo_year"><?php _e('Year', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_year" name="noindex_seo_year" value="1"<?php if($noindex_seo_year) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-yes" title="Yes"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing when a yearly archive of the site. The lists where the posts appear.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
      </table>

			<h2><?php esc_html_e( 'Archives', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_seo_archive"><?php _e('Archive', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_archive" name="noindex_seo_archive" value="1"<?php if($noindex_seo_archive) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-no" title="No"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing of any type of Archive page. Category, Tag, Author and Date based pages are all types of Archives. The lists where the posts appear.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_seo_author"><?php _e('Author', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_author" name="noindex_seo_author" value="1"<?php if($noindex_seo_author) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-no" title="No"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing of the author\'s page, where the author\'s publications appear.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_seo_post_type_archive"><?php _e('Post Type Archive', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_post_type_archive" name="noindex_seo_post_type_archive" value="1"<?php if($noindex_seo_post_type_archive) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-no" title="No"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing of any post type page.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
      </table>

			<h2><?php esc_html_e( 'Pagination', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_seo_paged"><?php _e('Pagination', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_paged" name="noindex_seo_paged" value="1"<?php if($noindex_seo_paged) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-yes" title="Yes"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing of the pagination, i.e. all pages other than the main page of an archive.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
      </table>

			<h2><?php esc_html_e( 'Search', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_seo_search"><?php _e('Search', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_search" name="noindex_seo_search" value="1"<?php if($noindex_seo_search) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-yes" title="Yes"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing of the internal search result pages.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
      </table>

			<h2><?php esc_html_e( 'Attachments', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_seo_attachment"><?php _e('Attachment', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_attachment" name="noindex_seo_attachment" value="1"<?php if($noindex_seo_attachment) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-yes" title="Yes"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing of an attachment document to a post or page. An attachment is an image or other file uploaded through the post editor\'s upload utility. Attachments can be displayed on their own "page" or template. This will not cause the indexing of the image or file to be blocked.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
      </table>

			<h2><?php esc_html_e( 'Previews', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_seo_customize_preview"><?php _e('Customize Preview', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_customize_preview" name="noindex_seo_customize_preview" value="1"<?php if($noindex_seo_customize_preview) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-yes" title="Yes"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing when a content is being displayed in customize mode.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_seo_preview"><?php _e('Preview', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_preview" name="noindex_seo_preview" value="1"<?php if($noindex_seo_preview) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-yes" title="Yes"></span>. <span class="description"><?php esc_html_e( 'This will block the indexing when a single post is being displayed in draft mode.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
      </table>

			<h2><?php esc_html_e( 'Error Page', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_seo_error"><?php _e('Error 404', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_error" name="noindex_seo_error" value="1"<?php if($noindex_seo_error) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-yes" title="Yes"></span>. <span class="description"><?php esc_html_e( 'This will cause an error page to be blocked from being indexed. As it is an error page, it should not be indexed per se, but just in case.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
      </table>
      
      <!--
			<h2><?php esc_html_e( 'Feeds (RSS, Atom)', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_seo_comment_feed"><?php _e('Comment feed', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_comment_feed" name="noindex_seo_comment_feed" value="1"<?php if($noindex_seo_comment_feed) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-yes" title="Yes"></span>. <span class="description"><?php esc_html_e( 'Comment feeds should not be indexed if they are properly configured, as they are not HTML pages but XML pages. Even so, in case they could somehow be in the results, it will not show them. This will not prevent them from being read by crawlers, only that it does not show them in the results.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
        <tr>
          <th scope="row"><label for="noindex_seo_feed"><?php _e('Feed', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_feed" name="noindex_seo_feed" value="1"<?php if($noindex_seo_feed) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-yes" title="Yes"></span>. <span class="description"><?php esc_html_e( 'Feeds should not be indexed if they are properly configured, as they are not HTML pages but XML pages. Even so, in case they could somehow be in the results, it will not show them. This will not prevent them from being read by crawlers, only that it does not show them in the results.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
      </table>
      -->

      <!--
			<h2><?php esc_html_e( 'robots.txt', 'noindex-seo' ); ?></h2>
      <table class="form-table">
        <tr>
          <th scope="row"><label for="noindex_seo_robots"><?php _e('robots.txt', 'noindex-seo'); ?></label></th>
          <td><fieldset><input type="checkbox" id="noindex_seo_robots" name="noindex_seo_robots" value="1"<?php if($noindex_seo_robots) echo ' checked'; ?>> <?php esc_html_e( 'Recommended', 'noindex-seo' ); ?>: <span class="dashicons dashicons-yes" title="Yes"></span>. <span class="description"><?php esc_html_e( 'The robots.txt file should not appear in the search results, but there may be the odd case where it does. With this option you can force it not to, although the crawlers will still read it and process it, only they won\'t show up in the results.', 'noindex-seo' ); ?></span></fieldset></td>
        </tr>
      </table>
      -->

			<?php submit_button(); ?>
		</form>
		</div>
		<?php
    unset($noindex_seo_error, $noindex_seo_archive, $noindex_seo_attachment, $noindex_seo_author, $noindex_seo_category, $noindex_seo_comment_feed, $noindex_seo_customize_preview, $noindex_seo_date, $noindex_seo_day, $noindex_seo_feed, $noindex_seo_front_page, $noindex_seo_home, $noindex_seo_month, $noindex_seo_page, $noindex_seo_paged, $noindex_seo_post_type_archive, $noindex_seo_preview, $noindex_seo_privacy_policy, $noindex_seo_robots, $noindex_seo_search, $noindex_seo_single, $noindex_seo_singular, $noindex_seo_tag, $noindex_seo_time, $noindex_seo_year);
}

/*
Future integrations
-------------------
admin
login
password
network?
*/
