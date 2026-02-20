<?php
/**
 * Class EquationEditorTest
 *
 * @package Equation_Editor
 */

class EquationEditorTest extends WP_UnitTestCase {

	/**
	 * Test that the plugin class exists.
	 */
	public function test_plugin_class_exists() {
		$this->assertTrue( class_exists( 'mw_equation_editor' ) );
	}

	/**
	 * Test that TinyMCE button filter is registered.
	 */
	public function test_mce_buttons_filter_registered() {
		$this->assertGreaterThan( 0, has_filter( 'mce_buttons' ) );
	}

	/**
	 * Test that TinyMCE external plugins filter is registered.
	 */
	public function test_mce_external_plugins_filter_registered() {
		$this->assertGreaterThan( 0, has_filter( 'mce_external_plugins' ) );
	}

	/**
	 * Test equation_add_button adds wiris buttons when wiris is selected.
	 */
	public function test_equation_add_button_wiris() {
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'wiris',
		) );

		$editor = new mw_equation_editor();
		$buttons = $editor->equation_add_button( array() );

		$this->assertContains( 'tiny_mce_wiris_formulaEditor', $buttons );
		$this->assertContains( 'tiny_mce_wiris_formulaEditorChemistry', $buttons );
	}

	/**
	 * Test equation_add_button adds latex button when latex is selected.
	 */
	public function test_equation_add_button_latex() {
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'latex',
		) );

		$editor = new mw_equation_editor();
		$buttons = $editor->equation_add_button( array() );

		$this->assertContains( 'equation', $buttons );
	}

	/**
	 * Test equation_add_button adds both when both is selected.
	 */
	public function test_equation_add_button_both() {
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'both',
		) );

		$editor = new mw_equation_editor();
		$buttons = $editor->equation_add_button( array() );

		$this->assertContains( 'equation', $buttons );
		$this->assertContains( 'tiny_mce_wiris_formulaEditor', $buttons );
	}

	/**
	 * Test that buttons are not added when disabled.
	 */
	public function test_equation_add_button_disabled() {
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '0',
			'select_eq_editor' => 'wiris',
		) );

		$editor = new mw_equation_editor();
		$buttons = $editor->equation_add_button( array() );

		$this->assertNull( $buttons );
	}

	/**
	 * Test equation_editor_register adds wiris plugin when wiris is selected.
	 */
	public function test_equation_editor_register_wiris() {
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'wiris',
		) );

		$editor = new mw_equation_editor();
		$plugins = $editor->equation_editor_register( array() );

		$this->assertArrayHasKey( 'tiny_mce_wiris', $plugins );
	}

	/**
	 * Test equation_editor_register adds latex plugin when latex is selected.
	 */
	public function test_equation_editor_register_latex() {
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'latex',
		) );

		$editor = new mw_equation_editor();
		$plugins = $editor->equation_editor_register( array() );

		$this->assertArrayHasKey( 'equation', $plugins );
	}
}
