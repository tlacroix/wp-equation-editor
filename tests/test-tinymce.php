<?php
/**
 * Class TinyMCETest
 *
 * Tests for the TinyMCE integration class.
 *
 * @package Equation_Editor
 */

use Equation_Editor\TinyMCE;

class TinyMCETest extends WP_UnitTestCase {

	/**
	 * Test TinyMCE constructor accepts settings array.
	 */
	public function test_constructor_accepts_settings() {
		$settings = array(
			'enable_eq_editor' => '1',
		);

		$tinymce = new TinyMCE( $settings );
		$this->assertInstanceOf( TinyMCE::class, $tinymce );
	}

	/**
	 * Test TinyMCE constructor with empty settings.
	 */
	public function test_constructor_with_empty_settings() {
		$tinymce = new TinyMCE( array() );
		$this->assertInstanceOf( TinyMCE::class, $tinymce );
	}

	/**
	 * Test add_buttons returns null when enable_eq_editor is missing.
	 */
	public function test_add_buttons_null_when_setting_missing() {
		$tinymce = new TinyMCE( array() );
		$this->assertNull( $tinymce->add_buttons( array() ) );
	}

	/**
	 * Test add_buttons returns null when enable_eq_editor is empty string.
	 */
	public function test_add_buttons_null_when_setting_empty() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '',
		) );
		$this->assertNull( $tinymce->add_buttons( array() ) );
	}

	/**
	 * Test add_buttons returns null when enable_eq_editor is false.
	 */
	public function test_add_buttons_null_when_setting_false() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => false,
		) );
		$this->assertNull( $tinymce->add_buttons( array() ) );
	}

	/**
	 * Test register_plugins returns empty array when disabled.
	 */
	public function test_register_plugins_empty_when_disabled() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '0',
		) );
		$this->assertEmpty( $tinymce->register_plugins( array() ) );
	}

	/**
	 * Test register_plugins preserves existing plugins when disabled.
	 */
	public function test_register_plugins_preserves_existing_when_disabled() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '0',
		) );
		$existing = array( 'other_plugin' => 'other.js' );
		$result = $tinymce->register_plugins( $existing );

		$this->assertEquals( $existing, $result );
	}

	/**
	 * Test equation plugin URL uses EQUATION_EDITOR_URL constant.
	 */
	public function test_equation_plugin_url_uses_constant() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '1',
		) );
		$plugins = $tinymce->register_plugins( array() );

		$this->assertStringStartsWith( EQUATION_EDITOR_URL, $plugins['equation'] );
	}

	/**
	 * Test equation button is added when enabled.
	 */
	public function test_equation_button_added_when_enabled() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '1',
		) );
		$buttons = $tinymce->add_buttons( array() );

		$this->assertContains( 'equation', $buttons );
	}

	/**
	 * Test separator is added before equation button.
	 */
	public function test_separator_before_equation() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '1',
		) );
		$buttons = $tinymce->add_buttons( array() );

		$separator_pos = array_search( 'separator', $buttons, true );
		$equation_pos = array_search( 'equation', $buttons, true );

		$this->assertLessThan( $equation_pos, $separator_pos );
	}

	/**
	 * Test buttons array is properly indexed.
	 */
	public function test_buttons_array_indexed() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '1',
		) );
		$buttons = $tinymce->add_buttons( array( 'bold', 'italic' ) );

		// Check all keys are sequential integers
		$this->assertEquals( array_values( $buttons ), $buttons );
	}

	/**
	 * Test plugins array is associative.
	 */
	public function test_plugins_array_associative() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '1',
		) );
		$plugins = $tinymce->register_plugins( array() );

		$this->assertArrayHasKey( 'equation', $plugins );
		$this->assertIsString( $plugins['equation'] );
	}

	/**
	 * Test existing buttons are preserved.
	 */
	public function test_existing_buttons_preserved() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '1',
		) );
		$existing = array( 'bold', 'italic' );
		$buttons = $tinymce->add_buttons( $existing );

		$this->assertContains( 'bold', $buttons );
		$this->assertContains( 'italic', $buttons );
		$this->assertContains( 'equation', $buttons );
	}

	/**
	 * Test existing plugins are preserved.
	 */
	public function test_existing_plugins_preserved() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '1',
		) );
		$existing = array( 'other_plugin' => 'other.js' );
		$plugins = $tinymce->register_plugins( $existing );

		$this->assertArrayHasKey( 'other_plugin', $plugins );
		$this->assertArrayHasKey( 'equation', $plugins );
	}
}
