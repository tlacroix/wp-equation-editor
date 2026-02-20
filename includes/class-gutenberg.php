<?php
/**
 * Gutenberg block integration class for Equation Editor.
 *
 * @package Equation_Editor
 * @since   2.1.0
 */

namespace Equation_Editor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Gutenberg block registration and assets.
 *
 * @since 2.1.0
 */
class Gutenberg {

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
	 * Check if the block editor is enabled.
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return ! empty( $this->settings['enable_eq_editor'] )
			&& '1' === $this->settings['enable_eq_editor'];
	}

	/**
	 * Register the equation block.
	 *
	 * @return void
	 */
	public function register_block(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}

		$block_json = EQUATION_EDITOR_PATH . 'build/blocks/equation/block.json';
		if ( ! file_exists( $block_json ) ) {
			return;
		}

		register_block_type(
			$block_json,
			array(
				'render_callback' => array( $this, 'render_block' ),
			)
		);
	}

	/**
	 * Render the equation block on the frontend.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content    Block content.
	 * @return string Rendered HTML.
	 */
	public function render_block( array $attributes, string $content ): string {
		$latex        = $attributes['latex'] ?? '';
		$display_mode = $attributes['displayMode'] ?? true;

		if ( empty( $latex ) ) {
			return '';
		}

		$this->enqueue_katex();

		$wrapper_class = 'wp-block-equation-editor-equation';
		if ( $display_mode ) {
			$wrapper_class .= ' is-display-mode';
		}

		return sprintf(
			'<div class="%s" data-latex="%s" data-display-mode="%s"></div>',
			esc_attr( $wrapper_class ),
			esc_attr( $latex ),
			$display_mode ? 'true' : 'false'
		);
	}

	/**
	 * Enqueue KaTeX library for frontend rendering.
	 *
	 * @return void
	 */
	public function enqueue_katex(): void {
		static $enqueued = false;

		if ( $enqueued ) {
			return;
		}

		wp_enqueue_style(
			'katex',
			EQUATION_EDITOR_URL . 'vendor/katex/katex.min.css',
			array(),
			'0.16.9'
		);

		wp_enqueue_script(
			'katex',
			EQUATION_EDITOR_URL . 'vendor/katex/katex.min.js',
			array(),
			'0.16.9',
			true
		);

		wp_enqueue_script(
			'equation-editor-frontend',
			EQUATION_EDITOR_URL . 'build/blocks/equation/frontend.js',
			array( 'katex' ),
			EQUATION_EDITOR_VERSION,
			true
		);

		$enqueued = true;
	}

	/**
	 * Enqueue block editor assets.
	 *
	 * @return void
	 */
	public function enqueue_editor_assets(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}

		wp_enqueue_style(
			'katex',
			EQUATION_EDITOR_URL . 'vendor/katex/katex.min.css',
			array(),
			'0.16.9'
		);

		wp_enqueue_script(
			'katex',
			EQUATION_EDITOR_URL . 'vendor/katex/katex.min.js',
			array(),
			'0.16.9',
			true
		);
	}
}
