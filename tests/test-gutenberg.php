<?php
/**
 * Class GutenbergTest
 *
 * Tests for the Gutenberg block integration class.
 *
 * @package Equation_Editor
 */

use Equation_Editor\Gutenberg;

class GutenbergTest extends WP_UnitTestCase {

	/**
	 * Test Gutenberg class can be instantiated.
	 */
	public function test_constructor_accepts_settings() {
		$gutenberg = new Gutenberg( array(
			'enable_eq_editor' => '1',
		) );
		$this->assertInstanceOf( Gutenberg::class, $gutenberg );
	}

	/**
	 * Test is_enabled returns true when setting is '1'.
	 */
	public function test_is_enabled_true_when_enabled() {
		$gutenberg = new Gutenberg( array(
			'enable_eq_editor' => '1',
		) );
		$this->assertTrue( $gutenberg->is_enabled() );
	}

	/**
	 * Test is_enabled returns false when setting is '0'.
	 */
	public function test_is_enabled_false_when_disabled() {
		$gutenberg = new Gutenberg( array(
			'enable_eq_editor' => '0',
		) );
		$this->assertFalse( $gutenberg->is_enabled() );
	}

	/**
	 * Test is_enabled returns false when setting is missing.
	 */
	public function test_is_enabled_false_when_missing() {
		$gutenberg = new Gutenberg( array() );
		$this->assertFalse( $gutenberg->is_enabled() );
	}

	/**
	 * Test render_block returns empty string for empty latex.
	 */
	public function test_render_block_empty_for_empty_latex() {
		$gutenberg = new Gutenberg( array( 'enable_eq_editor' => '1' ) );
		$result    = $gutenberg->render_block( array( 'latex' => '' ), '' );
		$this->assertEmpty( $result );
	}

	/**
	 * Test render_block returns empty string for missing latex.
	 */
	public function test_render_block_empty_for_missing_latex() {
		$gutenberg = new Gutenberg( array( 'enable_eq_editor' => '1' ) );
		$result    = $gutenberg->render_block( array(), '' );
		$this->assertEmpty( $result );
	}

	/**
	 * Test render_block output contains latex data attribute.
	 */
	public function test_render_block_contains_latex_attribute() {
		$gutenberg = new Gutenberg( array( 'enable_eq_editor' => '1' ) );
		$result    = $gutenberg->render_block(
			array(
				'latex'       => 'E=mc^2',
				'displayMode' => true,
			),
			''
		);

		$this->assertStringContainsString( 'data-latex="E=mc^2"', $result );
	}

	/**
	 * Test render_block escapes HTML in latex.
	 */
	public function test_render_block_escapes_html() {
		$gutenberg = new Gutenberg( array( 'enable_eq_editor' => '1' ) );
		$result    = $gutenberg->render_block(
			array(
				'latex'       => '<script>alert(1)</script>',
				'displayMode' => true,
			),
			''
		);

		$this->assertStringNotContainsString( '<script>', $result );
		$this->assertStringContainsString( '&lt;script&gt;', $result );
	}

	/**
	 * Test render_block includes display mode class.
	 */
	public function test_render_block_display_mode_class() {
		$gutenberg = new Gutenberg( array( 'enable_eq_editor' => '1' ) );

		$display_result = $gutenberg->render_block(
			array(
				'latex'       => 'x^2',
				'displayMode' => true,
			),
			''
		);
		$this->assertStringContainsString( 'is-display-mode', $display_result );

		$inline_result = $gutenberg->render_block(
			array(
				'latex'       => 'x^2',
				'displayMode' => false,
			),
			''
		);
		$this->assertStringNotContainsString( 'is-display-mode', $inline_result );
	}

	/**
	 * Test render_block contains correct data-display-mode attribute.
	 */
	public function test_render_block_display_mode_attribute() {
		$gutenberg = new Gutenberg( array( 'enable_eq_editor' => '1' ) );

		$display_result = $gutenberg->render_block(
			array(
				'latex'       => 'x^2',
				'displayMode' => true,
			),
			''
		);
		$this->assertStringContainsString( 'data-display-mode="true"', $display_result );

		$inline_result = $gutenberg->render_block(
			array(
				'latex'       => 'x^2',
				'displayMode' => false,
			),
			''
		);
		$this->assertStringContainsString( 'data-display-mode="false"', $inline_result );
	}

	/**
	 * Test render_block contains wrapper class.
	 */
	public function test_render_block_contains_wrapper_class() {
		$gutenberg = new Gutenberg( array( 'enable_eq_editor' => '1' ) );
		$result    = $gutenberg->render_block(
			array(
				'latex'       => 'x^2',
				'displayMode' => true,
			),
			''
		);

		$this->assertStringContainsString( 'wp-block-equation-editor-equation', $result );
	}

	/**
	 * Test render_block handles special characters in latex.
	 */
	public function test_render_block_handles_special_characters() {
		$gutenberg = new Gutenberg( array( 'enable_eq_editor' => '1' ) );
		$result    = $gutenberg->render_block(
			array(
				'latex'       => '\frac{a}{b} & "quoted"',
				'displayMode' => true,
			),
			''
		);

		$this->assertStringContainsString( 'data-latex=', $result );
		$this->assertStringContainsString( '&amp;', $result );
		$this->assertStringContainsString( '&quot;', $result );
	}
}
