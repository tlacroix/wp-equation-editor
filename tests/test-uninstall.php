<?php
/**
 * Class UninstallTest
 *
 * Tests for the uninstall functionality.
 *
 * @package Equation_Editor
 */

class UninstallTest extends WP_UnitTestCase {

	/**
	 * Test that uninstall.php file exists.
	 */
	public function test_uninstall_file_exists() {
		$this->assertFileExists( EQUATION_EDITOR_PATH . 'uninstall.php' );
	}

	/**
	 * Test that uninstall removes plugin options.
	 */
	public function test_uninstall_removes_options() {
		// Set up test option.
		update_option( 'mw_equation_editor', array(
			'enable_eq_editor' => '1',
			'select_eq_editor' => 'wiris',
		) );

		// Verify option exists.
		$this->assertNotFalse( get_option( 'mw_equation_editor' ) );

		// Simulate uninstall by defining constant and including file.
		if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			define( 'WP_UNINSTALL_PLUGIN', true );
		}
		require EQUATION_EDITOR_PATH . 'uninstall.php';

		// Verify option is deleted.
		$this->assertFalse( get_option( 'mw_equation_editor' ) );
	}

	/**
	 * Test uninstall with no existing options.
	 */
	public function test_uninstall_with_no_options() {
		// Ensure option doesn't exist.
		delete_option( 'mw_equation_editor' );

		// This should not throw any errors.
		// The constant is already defined from previous test.
		require EQUATION_EDITOR_PATH . 'uninstall.php';

		// Verify option still doesn't exist.
		$this->assertFalse( get_option( 'mw_equation_editor' ) );
	}
}
