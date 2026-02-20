<?php
/**
 * Class EquationEditorTest
 *
 * @package Equation_Editor
 */

use Equation_Editor\Plugin;
use Equation_Editor\TinyMCE;

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
		$this->assertTrue( class_exists( 'Equation_Editor\Plugin' ) );
	}

	/**
	 * Test that TinyMCE class exists.
	 */
	public function test_tinymce_class_exists() {
		$this->assertTrue( class_exists( 'Equation_Editor\TinyMCE' ) );
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
	 * Test add_buttons adds wiris buttons when wiris is selected.
	 */
	public function test_add_buttons_wiris() {
		$settings = array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'wiris',
		);

		$tinymce = new TinyMCE( $settings );
		$buttons = $tinymce->add_buttons( array() );

		$this->assertContains( 'tiny_mce_wiris_formulaEditor', $buttons );
		$this->assertContains( 'tiny_mce_wiris_formulaEditorChemistry', $buttons );
	}

	/**
	 * Test add_buttons adds latex button when latex is selected.
	 */
	public function test_add_buttons_latex() {
		$settings = array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'latex',
		);

		$tinymce = new TinyMCE( $settings );
		$buttons = $tinymce->add_buttons( array() );

		$this->assertContains( 'equation', $buttons );
	}

	/**
	 * Test add_buttons adds both when both is selected.
	 */
	public function test_add_buttons_both() {
		$settings = array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'both',
		);

		$tinymce = new TinyMCE( $settings );
		$buttons = $tinymce->add_buttons( array() );

		$this->assertContains( 'equation', $buttons );
		$this->assertContains( 'tiny_mce_wiris_formulaEditor', $buttons );
		$this->assertContains( 'tiny_mce_wiris_formulaEditorChemistry', $buttons );
	}

	/**
	 * Test that buttons are not added when disabled.
	 */
	public function test_add_buttons_disabled() {
		$settings = array(
			'enable_eq_editor' => '0',
			'select_eq_editor' => 'wiris',
		);

		$tinymce = new TinyMCE( $settings );
		$buttons = $tinymce->add_buttons( array() );

		$this->assertNull( $buttons );
	}

	/**
	 * Test register_plugins adds wiris plugin when wiris is selected.
	 */
	public function test_register_plugins_wiris() {
		$settings = array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'wiris',
		);

		$tinymce = new TinyMCE( $settings );
		$plugins = $tinymce->register_plugins( array() );

		$this->assertArrayHasKey( 'tiny_mce_wiris', $plugins );
		$this->assertStringContainsString( 'tiny_mce_wiris/editor_plugin.js', $plugins['tiny_mce_wiris'] );
	}

	/**
	 * Test register_plugins adds latex plugin when latex is selected.
	 */
	public function test_register_plugins_latex() {
		$settings = array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'latex',
		);

		$tinymce = new TinyMCE( $settings );
		$plugins = $tinymce->register_plugins( array() );

		$this->assertArrayHasKey( 'equation', $plugins );
		$this->assertStringContainsString( 'assets/js/eq_editor.js', $plugins['equation'] );
	}

	/**
	 * Test register_plugins adds both plugins when both is selected.
	 */
	public function test_register_plugins_both() {
		$settings = array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'both',
		);

		$tinymce = new TinyMCE( $settings );
		$plugins = $tinymce->register_plugins( array() );

		$this->assertArrayHasKey( 'equation', $plugins );
		$this->assertArrayHasKey( 'tiny_mce_wiris', $plugins );
	}

	/**
	 * Test that existing buttons are preserved when adding new ones.
	 */
	public function test_add_buttons_preserves_existing() {
		$settings = array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'latex',
		);

		$tinymce = new TinyMCE( $settings );
		$existing_buttons = array( 'bold', 'italic', 'underline' );
		$buttons = $tinymce->add_buttons( $existing_buttons );

		$this->assertContains( 'bold', $buttons );
		$this->assertContains( 'italic', $buttons );
		$this->assertContains( 'underline', $buttons );
		$this->assertContains( 'equation', $buttons );
	}

	/**
	 * Test that existing plugins are preserved when registering new ones.
	 */
	public function test_register_plugins_preserves_existing() {
		$settings = array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'latex',
		);

		$tinymce = new TinyMCE( $settings );
		$existing_plugins = array( 'some_plugin' => 'some_plugin.js' );
		$plugins = $tinymce->register_plugins( $existing_plugins );

		$this->assertArrayHasKey( 'some_plugin', $plugins );
		$this->assertArrayHasKey( 'equation', $plugins );
	}

	/**
	 * Test separator is added before buttons.
	 */
	public function test_add_buttons_includes_separator() {
		$settings = array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'latex',
		);

		$tinymce = new TinyMCE( $settings );
		$buttons = $tinymce->add_buttons( array() );

		$this->assertContains( 'separator', $buttons );
	}

	/**
	 * Test behavior with empty/missing settings.
	 */
	public function test_add_buttons_with_no_settings() {
		$settings = array();

		$tinymce = new TinyMCE( $settings );
		$buttons = $tinymce->add_buttons( array( 'bold' ) );

		// Should return null when settings don't exist
		$this->assertNull( $buttons );
	}

	/**
	 * Test behavior with invalid editor type.
	 */
	public function test_add_buttons_with_invalid_editor_type() {
		$settings = array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'invalid_type',
		);

		$tinymce = new TinyMCE( $settings );
		$existing_buttons = array( 'bold' );
		$buttons = $tinymce->add_buttons( $existing_buttons );

		// Should return original buttons unchanged for invalid type
		$this->assertEquals( array( 'bold' ), $buttons );
	}

	/**
	 * Test behavior when editor is enabled but no type selected.
	 */
	public function test_add_buttons_enabled_no_type() {
		$settings = array(
			'enable_eq_editor' => '1',
		);

		$tinymce = new TinyMCE( $settings );
		$existing_buttons = array( 'bold' );
		$buttons = $tinymce->add_buttons( $existing_buttons );

		// Should return original buttons when type is missing
		$this->assertEquals( array( 'bold' ), $buttons );
	}

	/**
	 * Test that plugins are not registered when disabled.
	 */
	public function test_register_plugins_disabled() {
		$settings = array(
			'enable_eq_editor' => '0',
			'select_eq_editor' => 'wiris',
		);

		$tinymce = new TinyMCE( $settings );
		$plugins = $tinymce->register_plugins( array() );

		$this->assertArrayNotHasKey( 'tiny_mce_wiris', $plugins );
		$this->assertArrayNotHasKey( 'equation', $plugins );
	}

	/**
	 * Test plugin URLs contain expected paths.
	 */
	public function test_plugin_urls_are_valid() {
		$settings = array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'both',
		);

		$tinymce = new TinyMCE( $settings );
		$plugins = $tinymce->register_plugins( array() );

		// URLs should be absolute (contain http)
		$this->assertMatchesRegularExpression( '/^https?:\/\//', $plugins['equation'] );
		$this->assertMatchesRegularExpression( '/^https?:\/\//', $plugins['tiny_mce_wiris'] );
	}

	/**
	 * Test activation sets default options when none exist.
	 */
	public function test_activation_sets_defaults() {
		delete_option( 'mw_equation_editor' );

		$plugin = new Plugin();
		$plugin->activate();

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

		$plugin = new Plugin();
		$plugin->activate();

		$settings = get_option( 'mw_equation_editor' );
		$this->assertEquals( 'latex', $settings['select_eq_editor'] );
	}
}
