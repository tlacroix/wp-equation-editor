<?php
/**
 * TinyMCE integration class for Equation Editor.
 *
 * @package Equation_Editor
 * @since   2.0.0
 */

namespace Equation_Editor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles TinyMCE editor integration.
 *
 * @since 2.0.0
 */
class TinyMCE {

	/**
	 * Plugin settings.
	 *
	 * @var array
	 */
	private array $settings;

	/**
	 * Constructor.
	 *
	 * @param array $settings Plugin settings.
	 */
	public function __construct( array $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Check if the editor is enabled.
	 *
	 * @return bool
	 */
	private function is_enabled(): bool {
		return ! empty( $this->settings['enable_eq_editor'] ) && '1' === $this->settings['enable_eq_editor'];
	}

	/**
	 * Get the selected editor type.
	 *
	 * @return string
	 */
	private function get_editor_type(): string {
		return $this->settings['select_eq_editor'] ?? '';
	}

	/**
	 * Add TinyMCE buttons based on editor selection.
	 *
	 * @param array $buttons Existing TinyMCE buttons.
	 * @return array|null Modified buttons or null if disabled.
	 */
	public function add_buttons( array $buttons ): ?array {
		if ( ! $this->is_enabled() ) {
			return null;
		}

		$editor = $this->get_editor_type();

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

	/**
	 * Register TinyMCE plugins based on editor selection.
	 *
	 * @param array $plugin_array Existing TinyMCE plugins.
	 * @return array Modified plugins array.
	 */
	public function register_plugins( array $plugin_array ): array {
		if ( ! $this->is_enabled() ) {
			return $plugin_array;
		}

		$editor = $this->get_editor_type();

		if ( 'latex' === $editor ) {
			$plugin_array['equation'] = EQUATION_EDITOR_URL . 'assets/js/eq_editor.js';
		} elseif ( 'wiris' === $editor ) {
			$plugin_array['tiny_mce_wiris'] = EQUATION_EDITOR_URL . 'tiny_mce_wiris/editor_plugin.js';
		} elseif ( 'both' === $editor ) {
			$plugin_array['equation']       = EQUATION_EDITOR_URL . 'assets/js/eq_editor.js';
			$plugin_array['tiny_mce_wiris'] = EQUATION_EDITOR_URL . 'tiny_mce_wiris/editor_plugin.js';
		}

		return $plugin_array;
	}
}
