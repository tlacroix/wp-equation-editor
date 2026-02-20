<?php
/**
 * Class EquationEditorTest
 *
 * @package Equation_Editor
 */

class EquationEditorTest extends WP_UnitTestCase {

	/**
	 * Clean up after each test.
	 */
	public function tear_down() {
		parent::tear_down();
		delete_option( 'mw_equation_editor' );
	}

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
	 * Test that admin menu action is registered.
	 */
	public function test_admin_menu_action_registered() {
		$this->assertGreaterThan( 0, has_action( 'admin_menu' ) );
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
		$this->assertContains( 'tiny_mce_wiris_formulaEditorChemistry', $buttons );
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
		$this->assertStringContainsString( 'tiny_mce_wiris/editor_plugin.js', $plugins['tiny_mce_wiris'] );
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
		$this->assertStringContainsString( 'js/eq_editor.js', $plugins['equation'] );
	}

	/**
	 * Test equation_editor_register adds both plugins when both is selected.
	 */
	public function test_equation_editor_register_both() {
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'both',
		) );

		$editor = new mw_equation_editor();
		$plugins = $editor->equation_editor_register( array() );

		$this->assertArrayHasKey( 'equation', $plugins );
		$this->assertArrayHasKey( 'tiny_mce_wiris', $plugins );
	}

	/**
	 * Test that existing buttons are preserved when adding new ones.
	 */
	public function test_equation_add_button_preserves_existing() {
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'latex',
		) );

		$editor = new mw_equation_editor();
		$existing_buttons = array( 'bold', 'italic', 'underline' );
		$buttons = $editor->equation_add_button( $existing_buttons );

		$this->assertContains( 'bold', $buttons );
		$this->assertContains( 'italic', $buttons );
		$this->assertContains( 'underline', $buttons );
		$this->assertContains( 'equation', $buttons );
	}

	/**
	 * Test that existing plugins are preserved when registering new ones.
	 */
	public function test_equation_editor_register_preserves_existing() {
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'latex',
		) );

		$editor = new mw_equation_editor();
		$existing_plugins = array( 'some_plugin' => 'some_plugin.js' );
		$plugins = $editor->equation_editor_register( $existing_plugins );

		$this->assertArrayHasKey( 'some_plugin', $plugins );
		$this->assertArrayHasKey( 'equation', $plugins );
	}

	/**
	 * Test separator is added before buttons.
	 */
	public function test_equation_add_button_includes_separator() {
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'latex',
		) );

		$editor = new mw_equation_editor();
		$buttons = $editor->equation_add_button( array() );

		$this->assertContains( 'separator', $buttons );
	}

	/**
	 * Test behavior with empty/missing settings.
	 */
	public function test_equation_add_button_with_no_settings() {
		delete_option( 'mw_equation_editor' );

		$editor = new mw_equation_editor();
		$buttons = $editor->equation_add_button( array( 'bold' ) );

		// Should return null when settings don't exist
		$this->assertNull( $buttons );
	}

	/**
	 * Test behavior with invalid editor type.
	 */
	public function test_equation_add_button_with_invalid_editor_type() {
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'invalid_type',
		) );

		$editor = new mw_equation_editor();
		$existing_buttons = array( 'bold' );
		$buttons = $editor->equation_add_button( $existing_buttons );

		// Should return original buttons unchanged for invalid type
		$this->assertEquals( array( 'bold' ), $buttons );
	}

	/**
	 * Test behavior when editor is enabled but no type selected.
	 */
	public function test_equation_add_button_enabled_no_type() {
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '1',
		) );

		$editor = new mw_equation_editor();
		$existing_buttons = array( 'bold' );
		$buttons = $editor->equation_add_button( $existing_buttons );

		// Should return original buttons when type is missing
		$this->assertEquals( array( 'bold' ), $buttons );
	}

	/**
	 * Test that plugins are not registered when disabled.
	 */
	public function test_equation_editor_register_disabled() {
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '0',
			'select_eq_editor' => 'wiris',
		) );

		$editor = new mw_equation_editor();
		$plugins = $editor->equation_editor_register( array() );

		$this->assertArrayNotHasKey( 'tiny_mce_wiris', $plugins );
		$this->assertArrayNotHasKey( 'equation', $plugins );
	}

	/**
	 * Test plugin URLs contain expected paths.
	 */
	public function test_plugin_urls_are_valid() {
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'both',
		) );

		$editor = new mw_equation_editor();
		$plugins = $editor->equation_editor_register( array() );

		// URLs should be absolute (contain http)
		$this->assertMatchesRegularExpression( '/^https?:\/\//', $plugins['equation'] );
		$this->assertMatchesRegularExpression( '/^https?:\/\//', $plugins['tiny_mce_wiris'] );
	}

	/**
	 * Test activation sets default options when none exist.
	 */
	public function test_activation_sets_defaults() {
		delete_option( 'mw_equation_editor' );

		$editor = new mw_equation_editor();
		$editor->mw_equation_install();

		$settings = get_option( 'mw_equation_editor' );
		$this->assertIsArray( $settings );
		$this->assertEquals( '1', $settings['enable_eq_editor'] );
		$this->assertEquals( 'wiris', $settings['select_eq_editor'] );
	}

	/**
	 * Test activation does not overwrite existing settings.
	 */
	public function test_activation_preserves_existing_settings() {
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'latex',
		) );

		$editor = new mw_equation_editor();
		$editor->mw_equation_install();

		$settings = get_option( 'mw_equation_editor' );
		$this->assertEquals( 'latex', $settings['select_eq_editor'] );
	}
}
