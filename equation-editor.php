<?php
/**
 * Plugin Name: Equation Editor
 * Plugin URI: https://wordpress.org/plugins/equation-editor/
 * Description: Add mathematical equations using LaTeX. Supports Block Editor and Classic Editor.
 * Author: NuageLab <wordpress-plugins@nuagelab.com>
 * Version: 2.1.0
 * Author URI: https://profiles.wordpress.org/nuagelab
 * Requires PHP: 8.0
 *
 * @package Equation_Editor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin constants.
define( 'EQUATION_EDITOR_VERSION', '2.1.0' );
define( 'EQUATION_EDITOR_FILE', __FILE__ );
define( 'EQUATION_EDITOR_PATH', plugin_dir_path( __FILE__ ) );
define( 'EQUATION_EDITOR_URL', plugin_dir_url( __FILE__ ) );
define( 'EQUATION_EDITOR_BASENAME', plugin_basename( __FILE__ ) );

// Autoloader.
require_once EQUATION_EDITOR_PATH . 'vendor/autoload.php';

// Initialize the plugin.
$equation_editor = new Equation_Editor\Plugin();
$equation_editor->init();
