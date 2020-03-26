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

$noindex_options = array(
	'is_author'     => esc_html__( 'Author archives', 'noindex-seo' ),
	'is_attachment' => esc_html__( 'Attachment archives', 'noindex-seo' ),
	'is_date'       => esc_html__( '', 'noindex-seo' ),
	'is_year'       => esc_html__( '', 'noindex-seo' ),
	'is_month'      => esc_html__( '', 'noindex-seo' ),
	'is_day'        => esc_html__( '', 'noindex-seo' ),
	'is_time'       => esc_html__( '', 'noindex-seo' ),
	'is_archive'    => esc_html__( '', 'noindex-seo' ),
	'is_search'     => esc_html__( '', 'noindex-seo' ),
	'is_paged'      => esc_html__( '', 'noindex-seo' ),
	'is_attachment' => esc_html__( '', 'noindex-seo' ),
	'is_preview'    => esc_html__( '', 'noindex-seo' ),
);
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
		// Register our settings.
		register_setting( 'noindex_pages_settings', 'noindex_seo_type' );
	}

	private function select_table_html( $variable, $options ) {
		$variable_value = get_option( $variable );
		// Construct HTML.
		$html = '<select name="' . $variable . '"><option value=""';
		if ( ! $variable_value ) {
			$html .= ' selected';
		}
		$html .= '></option>';
		foreach ( $options as $key => $value ) {
			$html .= '<option value="' . $key . '"';
			if ( ( $variable_value === $key ) || ( ! $variable_value && 1 === $key ) ) {
				$html .= ' selected';
			}
			$html .= '>' . $value . '</option>';
		}
		$html .= '</select>';

		echo $html;
	}

	/**
	 * Options page for ERP Plugin
	 *
	 * @return void
	 */
	public function plugin_settings_page() {
		$noindex_seo_type = get_option( 'noindex_seo_type' );
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
					<th scope="row"><?php esc_html_e( 'Type of NoIndex', 'noindex-seo' ); ?></th>
					<td>
						<?php
						$this->select_table_html(
							'noindex_seo_type',
							array(
								'total'   => __( 'All options recommended', 'noindex-seo' ),
								'partial' => __( 'Partial selection', 'noindex-seo' ),
							)
						);
						?>
					</td>
				</tr>
			</table>
			<?php
			if ( 'partial' === $noindex_seo_type ) {
				?>
				<h2><?php esc_html_e( 'Partial Settings for No indexation', 'noindex-seo' ); ?></h2>
				<hr/>
				<table class="form-table">
					<?php
					foreach ( $noindex_options as $key => $value ) {
						echo '<tr valign="top"><th scope="row">';
						esc_html_e( 'Type of NoIndex', 'noindex-seo' );
						echo '</th><td>';
						$this->select_table_html(
							'noindex_' . $key,
							array(
								'total'   => __( 'All options recommended', 'noindex-seo' ),
								'partial' => __( 'Partial selection', 'noindex-seo' ),
							)
						);
						echo '</td></tr>';

					}
					?>
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Select Project', 'noindex-seo' ); ?></th>
						<td><select name="botcamp_project"><?php echo $select_project; ?></select></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'API Password', 'noindex-seo' ); ?></th>
						<td><input type="text" name="botcamp_project_apipass" value="<?php echo esc_attr( get_option( 'botcamp_project_apipass' ) ); ?>" style="min-width: 300px" /></td>
					</tr>
				</table>
				<?php
			}
			?> 
			<?php submit_button(); ?>
		</form>
		</div>
		<?php
	}
}


new NP_Admin();
