<?php
/*
Plugin Name: Equation Editor
Plugin URI: https://wordpress.org/plugins/equation-editor/
Description: Adds equation editor to wordpress TinyMCE editor.
Author: NuageLab <wordpress-plugins@nuagelab.com>
Version: 1.7
Author URI: https://profiles.wordpress.org/nuagelab
Requires PHP: 8.0
*/
if(!class_exists('mw_equation_editor')) {
	class mw_equation_editor {
		private string $option;
		private array $settings;

	    public function __construct() {
              add_filter('mce_buttons', array($this, 'equation_add_button'), 0);
              add_filter('mce_external_plugins', array($this, 'equation_editor_register'));
              add_action('admin_menu', array($this, 'mw_equation_menu_page'));
              $this->option = 'mw_equation_editor';
              $this->settings = get_option($this->option, array());
              register_activation_hook(__FILE__, array($this, 'mw_equation_install'));
        }

        public function mw_equation_install(): void {
              $settings = array(
                  'enable_eq_editor' => '1',
                  'select_eq_editor' => 'wiris',
              );
              if (empty($this->settings['enable_eq_editor'])) {
                  update_option($this->option, $settings);
              }
        }
        public function mw_equation_menu_page(): void {
            add_menu_page(
                __('Equation Editor', 'equation-editor'),
                __('Equation Editor', 'equation-editor'),
                'manage_options',
                'mw_equation_editor',
                array($this, 'mw_equation_editor_method'),
                'dashicons-editor-underline'
            );
        }

        public function equation_add_button(array $buttons): ?array {
            if (!empty($this->settings['enable_eq_editor']) && $this->settings['enable_eq_editor'] === '1') {
                $editor = $this->settings['select_eq_editor'] ?? '';
                if ($editor === 'latex') {
                    array_push($buttons, 'separator', 'equation');
                } elseif ($editor === 'wiris') {
                    array_push($buttons, 'separator', 'tiny_mce_wiris_formulaEditor', 'tiny_mce_wiris_formulaEditorChemistry');
                } elseif ($editor === 'both') {
                    array_push($buttons, 'separator', 'equation');
                    array_push($buttons, 'separator', 'tiny_mce_wiris_formulaEditor', 'tiny_mce_wiris_formulaEditorChemistry');
                }
                return $buttons;
            }
            return null;
        }

        public function equation_editor_register(array $plugin_array): array {
            if (!empty($this->settings['enable_eq_editor']) && $this->settings['enable_eq_editor'] === '1') {
                $editor = $this->settings['select_eq_editor'] ?? '';
                if ($editor === 'latex') {
                    $plugin_array['equation'] = plugins_url('js/eq_editor.js', __FILE__);
                } elseif ($editor === 'wiris') {
                    $plugin_array['tiny_mce_wiris'] = plugins_url('tiny_mce_wiris/editor_plugin.js', __FILE__);
                } elseif ($editor === 'both') {
                    $plugin_array['equation'] = plugins_url('js/eq_editor.js', __FILE__);
                    $plugin_array['tiny_mce_wiris'] = plugins_url('tiny_mce_wiris/editor_plugin.js', __FILE__);
                }
            }
            return $plugin_array;
        }

        public function mw_equation_editor_method(): void {
            if (is_admin() && current_user_can('manage_options')) {
                require_once 'admin/mw_equation_editor.php';
            }
        }

        public function save(): void {
            $nonce = isset($_POST['mw_equation_editor_nonce']) ? sanitize_text_field(wp_unslash($_POST['mw_equation_editor_nonce'])) : '';
            if (isset($_POST['submit']) && wp_verify_nonce($nonce, 'mw_equation_editor_action')) {
                // Sanitize and validate settings.
                $valid_editors = array( 'wiris', 'latex', 'both' );
                $editor_type   = isset($_POST['select_eq_editor']) ? sanitize_text_field(wp_unslash($_POST['select_eq_editor'])) : 'wiris';

                $settings = array(
                    'enable_eq_editor' => isset($_POST['enable_eq_editor']) ? '1' : '0',
                    'select_eq_editor' => in_array($editor_type, $valid_editors, true) ? $editor_type : 'wiris',
                );

                $set = update_option($this->option, $settings);
                $url = $set ? 'admin.php?page=mw_equation_editor&s=y' : 'admin.php?page=mw_equation_editor&s=n';

                if ( ! headers_sent() ) {
                    wp_safe_redirect( admin_url( $url ) );
                    exit;
                }
            }
        }
    }

    new mw_equation_editor();
}