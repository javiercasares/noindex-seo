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
    
		$noindex_pages_values = array(
      'error' => (int)get_option( 'noindex_pages_error' ), 
      'archive' => (int)get_option( 'noindex_pages_archive' ),
      'attachment' => (int)get_option( 'noindex_pages_attachment' ),
      'author' => (int)get_option( 'noindex_pages_author' ),
      'category' => (int)get_option( 'noindex_pages_category' ),
      'comment_feed' => (int)get_option( 'noindex_pages_comment_feed' ),
      'customize_preview' => (int)get_option( 'noindex_pages_customize_preview' ),
      'date' => (int)get_option( 'noindex_pages_date' ),
      'day' => (int)get_option( 'noindex_pages_day' ),
      'feed' => (int)get_option( 'noindex_pages_feed' ),
      'front_page' => (int)get_option( 'noindex_pages_front_page' ),
      'home' => (int)get_option( 'noindex_pages_home' ),
      'month' => (int)get_option( 'noindex_pages_month' ),
      'page' => (int)get_option( 'noindex_pages_page' ),
      'paged' => (int)get_option( 'noindex_pages_paged' ),
      'post_type_archive' => (int)get_option( 'noindex_pages_post_type_archive' ),
      'preview' => (int)get_option( 'noindex_pages_preview' ),
      'privacy_policy' => (int)get_option( 'noindex_pages_privacy_policy' ),
      'robots' => (int)get_option( 'noindex_pages_robots' ),
      'search' => (int)get_option( 'noindex_pages_search' ),
      'single' => (int)get_option( 'noindex_pages_single' ),
      'singular' => (int)get_option( 'noindex_pages_singular' ),
      'tag' => (int)get_option( 'noindex_pages_tag' ),
      'tax' => (int)get_option( 'noindex_pages_tax' ),
      'time' => (int)get_option( 'noindex_pages_time' ),
      'year' => (int)get_option( 'noindex_pages_year' )
    );
    $enter = true;

    /*
      GLOBAL IMPORTANT PAGES
    */
    if($enter && $noindex_pages_values['front_page'] && is_front_page()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }
    if($enter && $noindex_pages_values['home'] && is_home()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }

    /*
      PAGES / POSTS
    */
    if($enter && $noindex_pages_values['page'] && is_page()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }
    if($enter && $noindex_pages_values['privacy_policy'] && is_privacy_policy()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }
    if($enter && $noindex_pages_values['single'] && is_single()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }
    if($enter && $noindex_pages_values['singular'] && is_singular()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }

    /*
      CATEGORIES / TAGS
    */
    if($enter && $noindex_pages_values['category'] && is_category()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }
    if($enter && $noindex_pages_values['tag'] && is_tag()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }
    if($enter && $noindex_pages_values['tax'] && is_tax()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }

    /*
      DATES
    */
    if($enter && $noindex_pages_values['date'] && is_date()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }
    if($enter && $noindex_pages_values['day'] && is_day()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }
    if($enter && $noindex_pages_values['month'] && is_month()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }
    if($enter && $noindex_pages_values['time'] && is_time()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }
    if($enter && $noindex_pages_values['year'] && is_year()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }    

    /*
      ARCHIVE
    */
    if($enter && $noindex_pages_values['archive'] && is_archive()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }
    if($enter && $noindex_pages_values['author'] && is_author()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }
    if($enter && $noindex_pages_values['post_type_archive'] && is_post_type_archive()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }

    /*
      PAGINATION
    */
    if($enter && $noindex_pages_values['paged'] && is_paged()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }

    /*
      SEARCH
    */
    if($enter && $noindex_pages_values['search'] && is_search()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }

    /*
      ATTACHMENT
    */
    if($enter && $noindex_pages_values['attachment'] && is_attachment()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }

    /*
      PREVIEW
    */
    if($enter && $noindex_pages_values['customize_preview'] && is_customize_preview()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }
    if($enter && $noindex_pages_values['preview'] && is_preview()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }

    /*
      ERROR
    */
    if($enter && $noindex_pages_values['error'] && is_404()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }

    /*
      FEED
    */
    if($enter && $noindex_pages_values['comment_feed'] && is_comment_feed()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }
    if($enter && $noindex_pages_values['feed'] && is_feed()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }

    /*
      ROBOTS
    */
    if($enter && $noindex_pages_values['robots'] && is_robots()) {
      echo '<meta name="robots" content="noindex">'."\n";
      $enter = false;
    }
    
    unset($enter, $noindex_pages_values);
	} 

}

new NP_Frontend();
