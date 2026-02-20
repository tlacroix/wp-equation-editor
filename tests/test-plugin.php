<?php
/**
 * Class PluginTest
 *
 * Tests for the main Plugin class.
 *
 * @package Equation_Editor
 */

use Equation_Editor\Plugin;
use Equation_Editor\Settings;
use Equation_Editor\TinyMCE;

class PluginTest extends WP_UnitTestCase {

	/**
	 * Clean up after each test.
	 */
	public function tear_down() {
		parent::tear_down();
		delete_option( 'mw_equation_editor' );
	}

	/**
	 * Test that plugin constants are defined.
	 */
	public function test_plugin_constants_defined() {
		$this->assertTrue( defined( 'EQUATION_EDITOR_VERSION' ) );
		$this->assertTrue( defined( 'EQUATION_EDITOR_FILE' ) );
		$this->assertTrue( defined( 'EQUATION_EDITOR_PATH' ) );
		$this->assertTrue( defined( 'EQUATION_EDITOR_URL' ) );
		$this->assertTrue( defined( 'EQUATION_EDITOR_BASENAME' ) );
	}

	/**
	 * Test plugin version constant value.
	 */
	public function test_plugin_version() {
		$this->assertEquals( '2.1.0', EQUATION_EDITOR_VERSION );
	}

	/**
	 * Test plugin path constant ends with slash.
	 */
	public function test_plugin_path_ends_with_slash() {
		$this->assertStringEndsWith( '/', EQUATION_EDITOR_PATH );
	}

	/**
	 * Test plugin URL constant ends with slash.
	 */
	public function test_plugin_url_ends_with_slash() {
		$this->assertStringEndsWith( '/', EQUATION_EDITOR_URL );
	}

	/**
	 * Test plugin file constant points to main file.
	 */
	public function test_plugin_file_is_main_file() {
		$this->assertStringEndsWith( 'equation-editor.php', EQUATION_EDITOR_FILE );
	}

	/**
	 * Test plugin basename format.
	 */
	public function test_plugin_basename_format() {
		$this->assertStringContainsString( 'equation-editor', EQUATION_EDITOR_BASENAME );
		$this->assertStringEndsWith( '.php', EQUATION_EDITOR_BASENAME );
	}

	/**
	 * Test Plugin class can be instantiated.
	 */
	public function test_plugin_can_be_instantiated() {
		$plugin = new Plugin();
		$this->assertInstanceOf( Plugin::class, $plugin );
	}

	/**
	 * Test Settings class can be instantiated.
	 */
	public function test_settings_can_be_instantiated() {
		$settings = new Settings();
		$this->assertInstanceOf( Settings::class, $settings );
	}

	/**
	 * Test TinyMCE class can be instantiated.
	 */
	public function test_tinymce_can_be_instantiated() {
		$tinymce = new TinyMCE( array() );
		$this->assertInstanceOf( TinyMCE::class, $tinymce );
	}

	/**
	 * Test init method registers mce_buttons filter.
	 */
	public function test_init_registers_mce_buttons_filter() {
		// The global plugin already called init, so just check the filter exists
		$this->assertGreaterThan( 0, has_filter( 'mce_buttons' ) );
	}

	/**
	 * Test init method registers mce_external_plugins filter.
	 */
	public function test_init_registers_mce_external_plugins_filter() {
		$this->assertGreaterThan( 0, has_filter( 'mce_external_plugins' ) );
	}

	/**
	 * Test init method registers admin_menu action.
	 */
	public function test_init_registers_admin_menu_action() {
		$this->assertGreaterThan( 0, has_action( 'admin_menu' ) );
	}

	/**
	 * Test init method registers admin_init action.
	 */
	public function test_init_registers_admin_init_action() {
		$this->assertGreaterThan( 0, has_action( 'admin_init' ) );
	}

	/**
	 * Test add_menu_page adds menu with correct capability.
	 */
	public function test_add_menu_page_requires_manage_options() {
		$admin_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_id );

		$plugin = new Plugin();
		$plugin->add_menu_page();

		global $menu;
		$found = false;
		foreach ( $menu as $item ) {
			if ( isset( $item[2] ) && $item[2] === 'mw_equation_editor' ) {
				$found = true;
				// Check capability
				$this->assertEquals( 'manage_options', $item[1] );
				break;
			}
		}
		$this->assertTrue( $found, 'Menu page should be added' );

		wp_set_current_user( 0 );
	}

	/**
	 * Test add_settings_link returns array with Settings link.
	 */
	public function test_add_settings_link_returns_array() {
		$plugin = new Plugin();
		$result = $plugin->add_settings_link( array() );

		$this->assertIsArray( $result );
		$this->assertCount( 1, $result );
	}

	/**
	 * Test add_settings_link prepends to existing links.
	 */
	public function test_add_settings_link_prepends() {
		$plugin = new Plugin();
		$existing = array( '<a href="#">Existing Link</a>' );
		$result = $plugin->add_settings_link( $existing );

		$this->assertCount( 2, $result );
		$this->assertStringContainsString( 'Settings', $result[0] );
		$this->assertStringContainsString( 'Existing Link', $result[1] );
	}

	/**
	 * Test add_settings_link contains proper URL structure.
	 */
	public function test_add_settings_link_url_structure() {
		$plugin = new Plugin();
		$result = $plugin->add_settings_link( array() );

		$this->assertStringContainsString( 'admin.php', $result[0] );
		$this->assertStringContainsString( 'page=mw_equation_editor', $result[0] );
	}

	/**
	 * Test activate sets defaults when no settings exist.
	 */
	public function test_activate_creates_defaults() {
		delete_option( 'mw_equation_editor' );

		$plugin = new Plugin();
		$plugin->activate();

		$settings = get_option( 'mw_equation_editor' );
		$this->assertIsArray( $settings );
		$this->assertArrayHasKey( 'enable_eq_editor', $settings );
	}

	/**
	 * Test activate default enables editor.
	 */
	public function test_activate_default_enables_editor() {
		delete_option( 'mw_equation_editor' );

		$plugin = new Plugin();
		$plugin->activate();

		$settings = get_option( 'mw_equation_editor' );
		$this->assertEquals( '1', $settings['enable_eq_editor'] );
	}

	/**
	 * Test activate preserves existing enable setting.
	 */
	public function test_activate_preserves_enable_setting() {
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '1',
		) );

		$plugin = new Plugin();
		$plugin->activate();

		$settings = get_option( 'mw_equation_editor' );
		$this->assertEquals( '1', $settings['enable_eq_editor'] );
	}

	/**
	 * Test deactivate clears transients.
	 */
	public function test_deactivate_clears_transients() {
		// Set a transient that would be created by the plugin.
		set_transient( 'equation_editor_cache', 'test_value', 3600 );
		$this->assertEquals( 'test_value', get_transient( 'equation_editor_cache' ) );

		$plugin = new Plugin();
		$plugin->deactivate();

		// Transient should be deleted.
		$this->assertFalse( get_transient( 'equation_editor_cache' ) );
	}

	/**
	 * Test deactivate clears scheduled hooks.
	 */
	public function test_deactivate_clears_scheduled_hooks() {
		// Schedule a hook that would be created by the plugin.
		wp_schedule_event( time() + 3600, 'hourly', 'equation_editor_cleanup' );
		$this->assertNotFalse( wp_next_scheduled( 'equation_editor_cleanup' ) );

		$plugin = new Plugin();
		$plugin->deactivate();

		// Scheduled hook should be cleared.
		$this->assertFalse( wp_next_scheduled( 'equation_editor_cleanup' ) );
	}

	/**
	 * Test deactivate preserves settings.
	 */
	public function test_deactivate_preserves_settings() {
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '1',
		) );

		$plugin = new Plugin();
		$plugin->deactivate();

		// Settings should still exist after deactivation.
		$settings = get_option( 'mw_equation_editor' );
		$this->assertIsArray( $settings );
		$this->assertEquals( '1', $settings['enable_eq_editor'] );
	}

	/**
	 * Test deactivate does not throw errors when no transients exist.
	 */
	public function test_deactivate_handles_missing_transients() {
		// Ensure no transient exists.
		delete_transient( 'equation_editor_cache' );

		$plugin = new Plugin();

		// This should not throw any errors.
		$plugin->deactivate();

		$this->assertFalse( get_transient( 'equation_editor_cache' ) );
	}
}
