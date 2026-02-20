<?php
/**
 * Class AdminTest
 *
 * Tests for admin functionality.
 *
 * @package Equation_Editor
 */

use Equation_Editor\Plugin;
use Equation_Editor\Settings;

class AdminTest extends WP_UnitTestCase {

	/**
	 * Admin user ID.
	 *
	 * @var int
	 */
	private $admin_user_id;

	/**
	 * Subscriber user ID.
	 *
	 * @var int
	 */
	private $subscriber_user_id;

	/**
	 * Settings handler instance.
	 *
	 * @var Settings
	 */
	private $settings;

	/**
	 * Set up before each test.
	 */
	public function set_up() {
		parent::set_up();

		// Create test users
		$this->admin_user_id      = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$this->subscriber_user_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
		$this->settings           = new Settings();
	}

	/**
	 * Clean up after each test.
	 */
	public function tear_down() {
		parent::tear_down();
		delete_option( 'mw_equation_editor' );
		wp_set_current_user( 0 );
	}

	/**
	 * Test that admin menu page is added.
	 */
	public function test_admin_menu_page_added() {
		wp_set_current_user( $this->admin_user_id );

		$plugin = new Plugin();
		$plugin->add_menu_page();

		// Check menu page exists
		global $menu;
		$menu_slugs = array_column( $menu, 2 );
		$this->assertContains( 'mw_equation_editor', $menu_slugs );
	}

	/**
	 * Test settings sanitization with valid input.
	 */
	public function test_sanitize_valid_input() {
		$input = array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'latex',
		);

		$result = $this->settings->sanitize( $input );

		$this->assertEquals( '1', $result['enable_eq_editor'] );
		$this->assertEquals( 'latex', $result['select_eq_editor'] );
	}

	/**
	 * Test that invalid editor type defaults to wiris.
	 */
	public function test_sanitize_invalid_editor_type_defaults_to_wiris() {
		$input = array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'invalid_editor',
		);

		$result = $this->settings->sanitize( $input );

		$this->assertEquals( 'wiris', $result['select_eq_editor'] );
	}

	/**
	 * Test that checkbox unchecked results in '0' value.
	 */
	public function test_sanitize_unchecked_checkbox() {
		$input = array(
			'select_eq_editor' => 'wiris',
			// enable_eq_editor not set (unchecked checkbox)
		);

		$result = $this->settings->sanitize( $input );

		$this->assertEquals( '0', $result['enable_eq_editor'] );
	}

	/**
	 * Test that sanitization only returns expected keys.
	 */
	public function test_sanitize_only_returns_expected_keys() {
		$input = array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'latex',
			'malicious_key'    => 'should_not_be_saved',
			'another_extra'    => 'also_ignored',
		);

		$result = $this->settings->sanitize( $input );

		// Verify only expected keys
		$this->assertCount( 2, $result );
		$this->assertArrayHasKey( 'enable_eq_editor', $result );
		$this->assertArrayHasKey( 'select_eq_editor', $result );
		$this->assertArrayNotHasKey( 'malicious_key', $result );
		$this->assertArrayNotHasKey( 'another_extra', $result );
	}

	/**
	 * Test that all valid editor types are accepted.
	 */
	public function test_sanitize_all_valid_editor_types() {
		$valid_types = array( 'wiris', 'latex', 'both' );

		foreach ( $valid_types as $type ) {
			$input  = array(
				'enable_eq_editor' => '1',
				'select_eq_editor' => $type,
			);
			$result = $this->settings->sanitize( $input );
			$this->assertEquals( $type, $result['select_eq_editor'] );
		}
	}

	/**
	 * Test settings registration.
	 */
	public function test_settings_registration() {
		global $wp_registered_settings;

		$this->settings->register();

		$this->assertArrayHasKey( 'mw_equation_editor', $wp_registered_settings );
	}

	/**
	 * Test render_page requires manage_options capability.
	 */
	public function test_render_page_requires_capability() {
		wp_set_current_user( $this->subscriber_user_id );

		ob_start();
		$this->settings->render_page();
		$output = ob_get_clean();

		// Subscriber should not see admin content
		$this->assertEmpty( $output );
	}

	/**
	 * Test render_page shows content for administrators.
	 */
	public function test_render_page_shows_for_admin() {
		wp_set_current_user( $this->admin_user_id );

		ob_start();
		$this->settings->render_page();
		$output = ob_get_clean();

		// Admin should see the settings page
		$this->assertStringContainsString( 'Equation Editor', $output );
		$this->assertStringContainsString( 'options.php', $output );
	}

	/**
	 * Test enable field rendering.
	 */
	public function test_render_enable_field() {
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'wiris',
		) );

		ob_start();
		$this->settings->render_enable_field();
		$output = ob_get_clean();

		$this->assertStringContainsString( 'enable_eq_editor', $output );
		$this->assertStringContainsString( 'checked', $output );
	}

	/**
	 * Test editor type field rendering.
	 */
	public function test_render_editor_type_field() {
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'latex',
		) );

		ob_start();
		$this->settings->render_editor_type_field();
		$output = ob_get_clean();

		$this->assertStringContainsString( 'select_eq_editor', $output );
		// Latex should be selected
		$this->assertMatchesRegularExpression( '/<option[^>]+value="latex"[^>]+selected/', $output );
	}

	/**
	 * Test settings link is added to plugin actions.
	 */
	public function test_settings_link_added() {
		$plugin = new Plugin();

		$links = $plugin->add_settings_link( array() );

		$this->assertCount( 1, $links );
		$this->assertStringContainsString( 'Settings', $links[0] );
		$this->assertStringContainsString( 'mw_equation_editor', $links[0] );
	}

	/**
	 * Test getters return expected values.
	 */
	public function test_settings_getters() {
		$this->assertEquals( 'mw_equation_editor', $this->settings->get_option_name() );
		$this->assertEquals( 'mw_equation_editor', $this->settings->get_page_slug() );
	}
}
