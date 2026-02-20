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
	 * Test add_buttons adds equation button when enabled.
	 */
	public function test_add_buttons_equation() {
		$settings = array(
			'enable_eq_editor' => '1',
		);

		$tinymce = new TinyMCE( $settings );
		$buttons = $tinymce->add_buttons( array() );

		$this->assertContains( 'equation', $buttons );
	}

	/**
	 * Test that buttons are not added when disabled.
	 */
	public function test_add_buttons_disabled() {
		$settings = array(
			'enable_eq_editor' => '0',
		);

		$tinymce = new TinyMCE( $settings );
		$buttons = $tinymce->add_buttons( array() );

		$this->assertNull( $buttons );
	}

	/**
	 * Test register_plugins adds equation plugin when enabled.
	 */
	public function test_register_plugins_equation() {
		$settings = array(
			'enable_eq_editor' => '1',
		);

		$tinymce = new TinyMCE( $settings );
		$plugins = $tinymce->register_plugins( array() );

		$this->assertArrayHasKey( 'equation', $plugins );
		$this->assertStringContainsString( 'assets/js/eq_editor.js', $plugins['equation'] );
	}

	/**
	 * Test that existing buttons are preserved when adding new ones.
	 */
	public function test_add_buttons_preserves_existing() {
		$settings = array(
			'enable_eq_editor' => '1',
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
	 * Test that plugins are not registered when disabled.
	 */
	public function test_register_plugins_disabled() {
		$settings = array(
			'enable_eq_editor' => '0',
		);

		$tinymce = new TinyMCE( $settings );
		$plugins = $tinymce->register_plugins( array() );

		$this->assertArrayNotHasKey( 'equation', $plugins );
	}

	/**
	 * Test plugin URLs contain expected paths.
	 */
	public function test_plugin_urls_are_valid() {
		$settings = array(
			'enable_eq_editor' => '1',
		);

		$tinymce = new TinyMCE( $settings );
		$plugins = $tinymce->register_plugins( array() );

		// URLs should be absolute (contain http)
		$this->assertMatchesRegularExpression( '/^https?:\/\//', $plugins['equation'] );
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
	}

	/**
	 * Test activation does not overwrite existing settings.
	 */
	public function test_activation_preserves_existing_settings() {
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '1',
		) );

		$plugin = new Plugin();
		$plugin->activate();

		$settings = get_option( 'mw_equation_editor' );
		$this->assertEquals( '1', $settings['enable_eq_editor'] );
	}
}
