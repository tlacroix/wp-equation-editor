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
			'select_eq_editor' => 'wiris',
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
		$tinymce = new TinyMCE( array( 'select_eq_editor' => 'wiris' ) );
		$this->assertNull( $tinymce->add_buttons( array() ) );
	}

	/**
	 * Test add_buttons returns null when enable_eq_editor is empty string.
	 */
	public function test_add_buttons_null_when_setting_empty() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '',
			'select_eq_editor' => 'wiris',
		) );
		$this->assertNull( $tinymce->add_buttons( array() ) );
	}

	/**
	 * Test add_buttons returns null when enable_eq_editor is false.
	 */
	public function test_add_buttons_null_when_setting_false() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => false,
			'select_eq_editor' => 'wiris',
		) );
		$this->assertNull( $tinymce->add_buttons( array() ) );
	}

	/**
	 * Test register_plugins returns empty array when disabled.
	 */
	public function test_register_plugins_empty_when_disabled() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '0',
			'select_eq_editor' => 'wiris',
		) );
		$this->assertEmpty( $tinymce->register_plugins( array() ) );
	}

	/**
	 * Test register_plugins preserves existing plugins when disabled.
	 */
	public function test_register_plugins_preserves_existing_when_disabled() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '0',
			'select_eq_editor' => 'wiris',
		) );
		$existing = array( 'other_plugin' => 'other.js' );
		$result = $tinymce->register_plugins( $existing );

		$this->assertEquals( $existing, $result );
	}

	/**
	 * Test latex plugin URL uses EQUATION_EDITOR_URL constant.
	 */
	public function test_latex_plugin_url_uses_constant() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'latex',
		) );
		$plugins = $tinymce->register_plugins( array() );

		$this->assertStringStartsWith( EQUATION_EDITOR_URL, $plugins['equation'] );
	}

	/**
	 * Test wiris plugin URL uses EQUATION_EDITOR_URL constant.
	 */
	public function test_wiris_plugin_url_uses_constant() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'wiris',
		) );
		$plugins = $tinymce->register_plugins( array() );

		$this->assertStringStartsWith( EQUATION_EDITOR_URL, $plugins['tiny_mce_wiris'] );
	}

	/**
	 * Test wiris buttons include formula editor.
	 */
	public function test_wiris_includes_formula_editor_button() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'wiris',
		) );
		$buttons = $tinymce->add_buttons( array() );

		$this->assertContains( 'tiny_mce_wiris_formulaEditor', $buttons );
	}

	/**
	 * Test wiris buttons include chemistry editor.
	 */
	public function test_wiris_includes_chemistry_editor_button() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'wiris',
		) );
		$buttons = $tinymce->add_buttons( array() );

		$this->assertContains( 'tiny_mce_wiris_formulaEditorChemistry', $buttons );
	}

	/**
	 * Test latex button name is 'equation'.
	 */
	public function test_latex_button_name() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'latex',
		) );
		$buttons = $tinymce->add_buttons( array() );

		$this->assertContains( 'equation', $buttons );
	}

	/**
	 * Test 'both' option adds 3 buttons (plus separators).
	 */
	public function test_both_adds_all_buttons() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'both',
		) );
		$buttons = $tinymce->add_buttons( array() );

		$this->assertContains( 'equation', $buttons );
		$this->assertContains( 'tiny_mce_wiris_formulaEditor', $buttons );
		$this->assertContains( 'tiny_mce_wiris_formulaEditorChemistry', $buttons );
	}

	/**
	 * Test 'both' option adds both plugins.
	 */
	public function test_both_adds_all_plugins() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'both',
		) );
		$plugins = $tinymce->register_plugins( array() );

		$this->assertArrayHasKey( 'equation', $plugins );
		$this->assertArrayHasKey( 'tiny_mce_wiris', $plugins );
	}

	/**
	 * Test separator is added before latex button.
	 */
	public function test_separator_before_latex() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'latex',
		) );
		$buttons = $tinymce->add_buttons( array() );

		$separator_pos = array_search( 'separator', $buttons, true );
		$equation_pos = array_search( 'equation', $buttons, true );

		$this->assertLessThan( $equation_pos, $separator_pos );
	}

	/**
	 * Test separator is added before wiris buttons.
	 */
	public function test_separator_before_wiris() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'wiris',
		) );
		$buttons = $tinymce->add_buttons( array() );

		$separator_pos = array_search( 'separator', $buttons, true );
		$wiris_pos = array_search( 'tiny_mce_wiris_formulaEditor', $buttons, true );

		$this->assertLessThan( $wiris_pos, $separator_pos );
	}

	/**
	 * Test buttons array is properly indexed.
	 */
	public function test_buttons_array_indexed() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'latex',
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
			'select_eq_editor' => 'both',
		) );
		$plugins = $tinymce->register_plugins( array() );

		$this->assertArrayHasKey( 'equation', $plugins );
		$this->assertIsString( $plugins['equation'] );
	}

	/**
	 * Test unknown editor type leaves buttons unchanged.
	 */
	public function test_unknown_editor_type_unchanged() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'unknown',
		) );
		$existing = array( 'bold', 'italic' );
		$buttons = $tinymce->add_buttons( $existing );

		$this->assertEquals( $existing, $buttons );
	}

	/**
	 * Test unknown editor type adds no plugins.
	 */
	public function test_unknown_editor_type_no_plugins() {
		$tinymce = new TinyMCE( array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'unknown',
		) );
		$plugins = $tinymce->register_plugins( array() );

		$this->assertEmpty( $plugins );
	}
}
