<?php
/**
 * Plugin Name: Equation Editor
 * Plugin URI: https://wordpress.org/plugins/equation-editor/
 * Description: Adds equation editor to wordpress TinyMCE editor.
 * Author: NuageLab <wordpress-plugins@nuagelab.com>
 * Version: 1.9.0
 * Author URI: https://profiles.wordpress.org/nuagelab
 * Requires PHP: 8.0
 *
 * @package EquationEditor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include the Settings class.
require_once plugin_dir_path( __FILE__ ) . 'includes/class-settings.php';

if ( ! class_exists( 'mw_equation_editor' ) ) {
	/**
	 * Main plugin class.
	 *
	 * @since 1.0.0
	 */
	class mw_equation_editor {

		/**
		 * Option name in database.
		 *
		 * @var string
		 */
		private string $option;

		/**
		 * Plugin settings.
		 *
		 * @var array
		 */
		private array $settings;

		/**
		 * Settings handler instance.
		 *
		 * @var Equation_Editor_Settings
		 */
		private Equation_Editor_Settings $settings_handler;

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->settings_handler = new Equation_Editor_Settings();
			$this->option           = $this->settings_handler->get_option_name();
			$this->settings         = get_option( $this->option, array() );

			add_filter( 'mce_buttons', array( $this, 'equation_add_button' ), 0 );
			add_filter( 'mce_external_plugins', array( $this, 'equation_editor_register' ) );
			add_action( 'admin_menu', array( $this, 'mw_equation_menu_page' ) );
			add_action( 'admin_init', array( $this->settings_handler, 'register' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_settings_link' ) );

			register_activation_hook( __FILE__, array( $this, 'mw_equation_install' ) );
		}

		/**
		 * Plugin activation hook.
		 *
		 * @return void
		 */
		public function mw_equation_install(): void {
			$settings = array(
				'enable_eq_editor' => '1',
				'select_eq_editor' => 'wiris',
			);
			if ( empty( $this->settings['enable_eq_editor'] ) ) {
				update_option( $this->option, $settings );
			}
		}

		/**
		 * Add admin menu page.
		 *
		 * @return void
		 */
		public function mw_equation_menu_page(): void {
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
		 * Add TinyMCE buttons based on editor selection.
		 *
		 * @param array $buttons Existing TinyMCE buttons.
		 * @return array|null Modified buttons or null if disabled.
		 */
		public function equation_add_button( array $buttons ): ?array {
			if ( ! empty( $this->settings['enable_eq_editor'] ) && '1' === $this->settings['enable_eq_editor'] ) {
				$editor = $this->settings['select_eq_editor'] ?? '';
				if ( 'latex' === $editor ) {
					array_push( $buttons, 'separator', 'equation' );
				} elseif ( 'wiris' === $editor ) {
					array_push( $buttons, 'separator', 'tiny_mce_wiris_formulaEditor', 'tiny_mce_wiris_formulaEditorChemistry' );
				} elseif ( 'both' === $editor ) {
					array_push( $buttons, 'separator', 'equation' );
					array_push( $buttons, 'separator', 'tiny_mce_wiris_formulaEditor', 'tiny_mce_wiris_formulaEditorChemistry' );
				}
				return $buttons;
			}
			return null;
		}

		/**
		 * Register TinyMCE plugins based on editor selection.
		 *
		 * @param array $plugin_array Existing TinyMCE plugins.
		 * @return array Modified plugins array.
		 */
		public function equation_editor_register( array $plugin_array ): array {
			if ( ! empty( $this->settings['enable_eq_editor'] ) && '1' === $this->settings['enable_eq_editor'] ) {
				$editor = $this->settings['select_eq_editor'] ?? '';
				if ( 'latex' === $editor ) {
					$plugin_array['equation'] = plugins_url( 'js/eq_editor.js', __FILE__ );
				} elseif ( 'wiris' === $editor ) {
					$plugin_array['tiny_mce_wiris'] = plugins_url( 'tiny_mce_wiris/editor_plugin.js', __FILE__ );
				} elseif ( 'both' === $editor ) {
					$plugin_array['equation']       = plugins_url( 'js/eq_editor.js', __FILE__ );
					$plugin_array['tiny_mce_wiris'] = plugins_url( 'tiny_mce_wiris/editor_plugin.js', __FILE__ );
				}
			}
			return $plugin_array;
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

	new mw_equation_editor();
}
