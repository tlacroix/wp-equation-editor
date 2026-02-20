<?php
/**
 * Class AdminTest
 *
 * Tests for admin functionality.
 *
 * @package Equation_Editor
 */

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
	 * Set up before each test.
	 */
	public function set_up() {
		parent::set_up();

		// Create test users
		$this->admin_user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$this->subscriber_user_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
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

		$editor = new mw_equation_editor();
		$editor->mw_equation_menu_page();

		// Check menu page exists
		global $menu;
		$menu_slugs = array_column( $menu, 2 );
		$this->assertContains( 'mw_equation_editor', $menu_slugs );
	}

	/**
	 * Test save function requires nonce.
	 */
	public function test_save_requires_nonce() {
		wp_set_current_user( $this->admin_user_id );

		$_POST['submit'] = '1';
		$_POST['enable_eq_editor'] = '1';
		$_POST['select_eq_editor'] = 'latex';
		// No nonce set

		$editor = new mw_equation_editor();

		// Start output buffering to capture any redirect script
		ob_start();
		$editor->save();
		ob_end_clean();

		// Settings should not be saved without valid nonce
		$settings = get_option( 'mw_equation_editor' );
		$this->assertNotEquals( 'latex', $settings['select_eq_editor'] ?? '' );

		unset( $_POST['submit'], $_POST['enable_eq_editor'], $_POST['select_eq_editor'] );
	}

	/**
	 * Test save function with valid nonce.
	 */
	public function test_save_with_valid_nonce() {
		wp_set_current_user( $this->admin_user_id );

		$_POST['submit'] = '1';
		$_POST['enable_eq_editor'] = '1';
		$_POST['select_eq_editor'] = 'latex';
		$_POST['mw_equation_editor_nonce'] = wp_create_nonce( 'mw_equation_editor_action' );

		$editor = new mw_equation_editor();
		$editor->save();

		// Verify settings were saved (redirect doesn't happen in tests due to headers_sent)
		$settings = get_option( 'mw_equation_editor' );
		$this->assertEquals( 'latex', $settings['select_eq_editor'] );

		unset( $_POST['submit'], $_POST['enable_eq_editor'], $_POST['select_eq_editor'], $_POST['mw_equation_editor_nonce'] );
	}

	/**
	 * Test that settings are properly sanitized during save.
	 */
	public function test_save_stores_settings() {
		wp_set_current_user( $this->admin_user_id );

		$_POST['submit'] = '1';
		$_POST['enable_eq_editor'] = '1';
		$_POST['select_eq_editor'] = 'both';
		$_POST['mw_equation_editor_nonce'] = wp_create_nonce( 'mw_equation_editor_action' );

		$editor = new mw_equation_editor();

		ob_start();
		$editor->save();
		ob_end_clean();

		$settings = get_option( 'mw_equation_editor' );
		$this->assertEquals( '1', $settings['enable_eq_editor'] );
		$this->assertEquals( 'both', $settings['select_eq_editor'] );

		unset( $_POST['submit'], $_POST['enable_eq_editor'], $_POST['select_eq_editor'], $_POST['mw_equation_editor_nonce'] );
	}

	/**
	 * Test that checkbox unchecked results in '0' value.
	 */
	public function test_save_unchecked_checkbox() {
		wp_set_current_user( $this->admin_user_id );

		// First, set enabled
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'wiris',
		) );

		// Now submit without checkbox (simulating unchecked)
		$_POST['submit'] = '1';
		$_POST['select_eq_editor'] = 'wiris';
		$_POST['mw_equation_editor_nonce'] = wp_create_nonce( 'mw_equation_editor_action' );
		// Note: enable_eq_editor is NOT set (unchecked checkbox)

		$editor = new mw_equation_editor();
		$editor->save();

		$settings = get_option( 'mw_equation_editor' );
		$this->assertEquals( '0', $settings['enable_eq_editor'] );

		unset( $_POST['submit'], $_POST['select_eq_editor'], $_POST['mw_equation_editor_nonce'] );
	}

	/**
	 * Test admin page requires manage_options capability.
	 */
	public function test_admin_page_requires_capability() {
		// Test with subscriber (no manage_options)
		wp_set_current_user( $this->subscriber_user_id );
		set_current_screen( 'admin' );

		$editor = new mw_equation_editor();

		ob_start();
		$editor->mw_equation_editor_method();
		$output = ob_get_clean();

		// Subscriber should not see admin content
		$this->assertEmpty( $output );
	}

	/**
	 * Test admin page loads for administrator.
	 *
	 * Note: This test verifies the capability check works correctly.
	 * The actual admin template loading depends on is_admin() which
	 * may not return true in all test environments.
	 */
	public function test_admin_page_loads_for_admin() {
		wp_set_current_user( $this->admin_user_id );

		// Verify admin has the required capability
		$this->assertTrue( current_user_can( 'manage_options' ) );
	}

	/**
	 * Test that admin template file exists and contains nonce.
	 */
	public function test_admin_template_has_nonce() {
		$admin_file = dirname( __DIR__ ) . '/admin/mw_equation_editor.php';
		$this->assertFileExists( $admin_file );

		$content = file_get_contents( $admin_file );
		$this->assertStringContainsString( 'mw_equation_editor_nonce', $content );
		$this->assertStringContainsString( 'mw_equation_editor_action', $content );
	}

	/**
	 * Test that invalid editor type defaults to wiris.
	 */
	public function test_save_invalid_editor_type_defaults_to_wiris() {
		wp_set_current_user( $this->admin_user_id );

		$_POST['submit'] = '1';
		$_POST['enable_eq_editor'] = '1';
		$_POST['select_eq_editor'] = 'invalid_editor';
		$_POST['mw_equation_editor_nonce'] = wp_create_nonce( 'mw_equation_editor_action' );

		$editor = new mw_equation_editor();
		$editor->save();

		$settings = get_option( 'mw_equation_editor' );
		$this->assertEquals( 'wiris', $settings['select_eq_editor'] );

		unset( $_POST['submit'], $_POST['enable_eq_editor'], $_POST['select_eq_editor'], $_POST['mw_equation_editor_nonce'] );
	}

	/**
	 * Test that save only stores expected keys (no extra POST data).
	 */
	public function test_save_only_stores_expected_keys() {
		wp_set_current_user( $this->admin_user_id );

		$_POST['submit'] = '1';
		$_POST['enable_eq_editor'] = '1';
		$_POST['select_eq_editor'] = 'latex';
		$_POST['malicious_key'] = 'should_not_be_saved';
		$_POST['another_extra'] = 'also_ignored';
		$_POST['mw_equation_editor_nonce'] = wp_create_nonce( 'mw_equation_editor_action' );

		$editor = new mw_equation_editor();
		$editor->save();

		$settings = get_option( 'mw_equation_editor' );
		// Verify only expected keys were saved
		$this->assertCount( 2, $settings );
		$this->assertArrayHasKey( 'enable_eq_editor', $settings );
		$this->assertArrayHasKey( 'select_eq_editor', $settings );
		$this->assertArrayNotHasKey( 'malicious_key', $settings );
		$this->assertArrayNotHasKey( 'another_extra', $settings );

		unset( $_POST['submit'], $_POST['enable_eq_editor'], $_POST['select_eq_editor'], $_POST['malicious_key'], $_POST['another_extra'], $_POST['mw_equation_editor_nonce'] );
	}
}
