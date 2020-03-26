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
			__( 'Software Connections', 'noindex-seo' ),
			__( 'Connections', 'noindex-seo' ),
			'manage_options',
			'noindex_pages_settings',
			array(
				$this,
				'erp_plugin_settings_page'
			),
		);

		// Call register settings function.
		add_action( 'admin_init', array( $this, 'register_erp_settings' ) );
	}

	/**
	 * Registers Settings for plugin
	 *
	 * @return void
	 */
	public function register_erp_settings() {
		// Register our settings.
		register_setting( 'noindex_pages_settings', 'botcamp_erp' );
		register_setting( 'noindex_pages_settings', 'botcamp_erp_apipass' );
		register_setting( 'noindex_pages_settings', 'botcamp_project' );
		register_setting( 'noindex_pages_settings', 'botcamp_project_apipass' );
	}

	/**
	 * Options page for ERP Plugin
	 *
	 * @return void
	 */
	public function erp_plugin_settings_page() {
		$erp = get_option( 'botcamp_erp' );
		// Language.
		$select_erp = '<option value=""';
		if ( ! $erp ) {
			$select_erp .= ' selected';
		}
		$select_erp   .= '></option>';
		$erp_selection = array(
			'holded' => __( 'Holded', 'noindex-seo' ),
		);
		foreach ( $erp_selection as $key => $value ) {
			$select_erp .= '<option value="' . $key . '"';
			if ( ( $erp === $key ) || ( ! $erp && 1 === $key ) ) {
				$select_erp .= ' selected';
			}
			$select_erp .= '>' . $value . '</option>';
		}
		$project = get_option( 'botcamp_project' );
		// Language.
		$select_project = '<option value=""';
		if ( ! $project ) {
			$select_project .= ' selected';
		}
		$select_project   .= '></option>';
		$project_selection = array(
			'asana' => __( 'Asana', 'noindex-seo' ),
		);
		foreach ( $project_selection as $key => $value ) {
			$select_project .= '<option value="' . $key . '"';
			if ( ( $project === $key ) || ( ! $project && 1 === $key ) ) {
				$select_project .= ' selected';
			}
			$select_project .= '>' . $value . '</option>';
		}
		?>
		<div class="wrap">
		<h1><?php esc_html_e( 'Software Connections', 'noindex-seo' ); ?></h1>

		<form method="post" action="options.php">
			<?php settings_fields( 'noindex_pages_settings' ); ?>
			<?php do_settings_sections( 'noindex_pages_settings' ); ?>
			<h2><?php esc_html_e( 'ERP Selections', 'noindex-seo' ); ?></h2>
			<hr/>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Select ERP', 'import-inmovilla-properties' ); ?></th>
					<td><select name="botcamp_erp"><?php echo $select_erp; ?></select></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'API Password', 'noindex-seo' ); ?></th>
					<td><input type="text" name="botcamp_erp_apipass" value="<?php echo esc_attr( get_option( 'botcamp_erp_apipass' ) ); ?>" style="min-width: 300px" /></td>
				</tr>
			</table>
			<h2><?php esc_html_e( 'Projects Selections', 'noindex-seo' ); ?></h2>
			<hr/>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Select Project', 'import-inmovilla-properties' ); ?></th>
					<td><select name="botcamp_project"><?php echo $select_project; ?></select></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'API Password', 'noindex-seo' ); ?></th>
					<td><input type="text" name="botcamp_project_apipass" value="<?php echo esc_attr( get_option( 'botcamp_project_apipass' ) ); ?>" style="min-width: 300px" /></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
		</div>
		<?php
	}
}


new NP_Admin();
