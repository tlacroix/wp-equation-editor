<?php
/**
 * Settings class for Equation Editor plugin.
 *
 * Handles WordPress Settings API registration and rendering.
 *
 * @package Equation_Editor
 * @since   1.9.0
 */

namespace Equation_Editor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Equation Editor Settings class.
 *
 * @since 1.9.0
 */
class Settings {

	/**
	 * Option name in the database.
	 *
	 * @var string
	 */
	private string $option_name = 'mw_equation_editor';

	/**
	 * Settings page slug.
	 *
	 * @var string
	 */
	private string $page_slug = 'mw_equation_editor';

	/**
	 * Settings group name.
	 *
	 * @var string
	 */
	private string $option_group = 'equation_editor_settings';

	/**
	 * Valid editor types.
	 *
	 * @var array
	 */
	private array $valid_editors = array( 'wiris', 'latex', 'both' );

	/**
	 * Register settings with WordPress Settings API.
	 *
	 * @return void
	 */
	public function register(): void {
		register_setting(
			$this->option_group,
			$this->option_name,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => array(
					'enable_eq_editor' => '1',
					'select_eq_editor' => 'wiris',
				),
			)
		);

		add_settings_section(
			'equation_editor_main',
			'',
			'__return_false',
			$this->page_slug
		);

		add_settings_field(
			'enable_eq_editor',
			__( 'Enable Equation Editor', 'equation-editor' ),
			array( $this, 'render_enable_field' ),
			$this->page_slug,
			'equation_editor_main',
			array( 'label_for' => 'enable_eq_editor' )
		);

		add_settings_field(
			'select_eq_editor',
			__( 'Select Editor Type', 'equation-editor' ),
			array( $this, 'render_editor_type_field' ),
			$this->page_slug,
			'equation_editor_main',
			array( 'label_for' => 'select_eq_editor' )
		);
	}

	/**
	 * Sanitize settings input.
	 *
	 * @param array $input Raw input from form.
	 * @return array Sanitized settings.
	 */
	public function sanitize( array $input ): array {
		$editor_type = isset( $input['select_eq_editor'] )
			? sanitize_text_field( $input['select_eq_editor'] )
			: 'wiris';

		return array(
			'enable_eq_editor' => isset( $input['enable_eq_editor'] ) ? '1' : '0',
			'select_eq_editor' => in_array( $editor_type, $this->valid_editors, true ) ? $editor_type : 'wiris',
		);
	}

	/**
	 * Render the settings page.
	 *
	 * @return void
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Show settings saved message.
		if ( isset( $_GET['settings-updated'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			add_settings_error(
				$this->option_name,
				'settings_updated',
				__( 'Settings saved.', 'equation-editor' ),
				'updated'
			);
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Equation Editor', 'equation-editor' ); ?></h1>
			<?php settings_errors( $this->option_name ); ?>
			<form action="options.php" method="post">
				<?php
				settings_fields( $this->option_group );
				do_settings_sections( $this->page_slug );
				submit_button( __( 'Save Changes', 'equation-editor' ) );
				?>
			</form>
			<p>
				<a href="https://wordpress.org/support/plugin/equation-editor/reviews/?filter=5" target="_blank"><?php esc_html_e( 'Rate this plugin', 'equation-editor' ); ?></a> |
				<a href="https://wordpress.org/support/plugin/equation-editor/" target="_blank"><?php esc_html_e( 'Support', 'equation-editor' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Render the enable checkbox field.
	 *
	 * @return void
	 */
	public function render_enable_field(): void {
		$settings = get_option( $this->option_name, array() );
		$enabled  = $settings['enable_eq_editor'] ?? '0';
		?>
		<input type="checkbox" name="<?php echo esc_attr( $this->option_name ); ?>[enable_eq_editor]"
			id="enable_eq_editor" value="1" <?php checked( $enabled, '1' ); ?>>
		<?php esc_html_e( 'Check to enable Equation Editor', 'equation-editor' ); ?>
		<?php
	}

	/**
	 * Render the editor type select field.
	 *
	 * @return void
	 */
	public function render_editor_type_field(): void {
		$settings    = get_option( $this->option_name, array() );
		$editor_type = $settings['select_eq_editor'] ?? 'wiris';
		?>
		<select name="<?php echo esc_attr( $this->option_name ); ?>[select_eq_editor]" id="select_eq_editor">
			<option value="wiris" <?php selected( $editor_type, 'wiris' ); ?>><?php esc_html_e( 'Wiris Editor', 'equation-editor' ); ?></option>
			<option value="latex" <?php selected( $editor_type, 'latex' ); ?>><?php esc_html_e( 'Latex Editor', 'equation-editor' ); ?></option>
			<option value="both" <?php selected( $editor_type, 'both' ); ?>><?php esc_html_e( 'Both', 'equation-editor' ); ?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Default: Wiris Editor', 'equation-editor' ); ?></p>
		<?php
	}

	/**
	 * Get the option name.
	 *
	 * @return string
	 */
	public function get_option_name(): string {
		return $this->option_name;
	}

	/**
	 * Get the page slug.
	 *
	 * @return string
	 */
	public function get_page_slug(): string {
		return $this->page_slug;
	}
}
