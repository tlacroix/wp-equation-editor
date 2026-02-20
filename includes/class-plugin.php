<?php
/**
 * Main plugin class for Equation Editor.
 *
 * @package Equation_Editor
 * @since   2.0.0
 */

namespace Equation_Editor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Equation Editor plugin class.
 *
 * @since 2.0.0
 */
class Plugin {

	/**
	 * Plugin settings.
	 *
	 * @var array
	 */
	private array $settings;

	/**
	 * Settings handler instance.
	 *
	 * @var Settings
	 */
	private Settings $settings_handler;

	/**
	 * TinyMCE handler instance.
	 *
	 * @var TinyMCE
	 */
	private TinyMCE $tinymce;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->settings_handler = new Settings();
		$this->settings         = get_option( $this->settings_handler->get_option_name(), array() );
		$this->tinymce          = new TinyMCE( $this->settings );
	}

	/**
	 * Initialize the plugin.
	 *
	 * @return void
	 */
	public function init(): void {
		// TinyMCE hooks.
		add_filter( 'mce_buttons', array( $this->tinymce, 'add_buttons' ), 0 );
		add_filter( 'mce_external_plugins', array( $this->tinymce, 'register_plugins' ) );

		// Admin hooks.
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_init', array( $this->settings_handler, 'register' ) );
		add_filter( 'plugin_action_links_' . EQUATION_EDITOR_BASENAME, array( $this, 'add_settings_link' ) );

		// Activation hook.
		register_activation_hook( EQUATION_EDITOR_FILE, array( $this, 'activate' ) );
	}

	/**
	 * Plugin activation hook.
	 *
	 * @return void
	 */
	public function activate(): void {
		$defaults = array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'wiris',
		);
		if ( empty( $this->settings['enable_eq_editor'] ) ) {
			update_option( $this->settings_handler->get_option_name(), $defaults );
		}
	}

	/**
	 * Add admin menu page.
	 *
	 * @return void
	 */
	public function add_menu_page(): void {
		add_menu_page(
			__( 'Equation Editor', 'equation-editor' ),
			__( 'Equation Editor', 'equation-editor' ),
			'manage_options',
			$this->settings_handler->get_page_slug(),
			array( $this->settings_handler, 'render_page' ),
			'dashicons-editor-underline'
		);
	}

	/**
	 * Add settings link to plugin actions.
	 *
	 * @param array $links Plugin action links.
	 * @return array Modified action links.
	 */
	public function add_settings_link( array $links ): array {
		$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=' . $this->settings_handler->get_page_slug() ) ) . '">'
			. esc_html__( 'Settings', 'equation-editor' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}
}
