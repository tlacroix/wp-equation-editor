<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$this->save();
?>
<div class="wrap">
<h1><?php esc_html_e( 'Equation Editor', 'equation-editor' ); ?></h1>
<form action="" method="post" id="ffm_manager">
<?php wp_nonce_field( 'mw_equation_editor_action', 'mw_equation_editor_nonce' ); ?>
<table class="form-table">
<tbody>
<tr>
<th scope="row"><label for="enable_eq_editor"><?php esc_html_e( 'Enable Equation Editor', 'equation-editor' ); ?></label></th>
<td>
<input name="enable_eq_editor" id="enable_eq_editor" value="1" type="checkbox" <?php checked( $this->settings['enable_eq_editor'] ?? '', '1' ); ?>>
<?php esc_html_e( 'Check to enable Equation Editor', 'equation-editor' ); ?>
</td>
</tr>
<tr>
<th scope="row"><label for="select_eq_editor"><?php esc_html_e( 'Select Editor Type', 'equation-editor' ); ?></label></th>
<td>
<select name="select_eq_editor" id="select_eq_editor">
	<option value="wiris" <?php selected( $this->settings['select_eq_editor'] ?? '', 'wiris' ); ?>><?php esc_html_e( 'Wiris Editor', 'equation-editor' ); ?></option>
	<option value="latex" <?php selected( $this->settings['select_eq_editor'] ?? '', 'latex' ); ?>><?php esc_html_e( 'Latex Editor', 'equation-editor' ); ?></option>
	<option value="both" <?php selected( $this->settings['select_eq_editor'] ?? '', 'both' ); ?>><?php esc_html_e( 'Both', 'equation-editor' ); ?></option>
</select>
<p class="description"><?php esc_html_e( 'Default: Wiris Editor', 'equation-editor' ); ?></p>
</td>
</tr>
</tbody>
</table>
<p class="submit"><input name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'equation-editor' ); ?>" type="submit"></p>
</form>
<p>
	<a href="https://wordpress.org/support/plugin/equation-editor/reviews/?filter=5" target="_blank"><?php esc_html_e( 'Rate this plugin', 'equation-editor' ); ?></a> |
	<a href="https://wordpress.org/support/plugin/equation-editor/" target="_blank"><?php esc_html_e( 'Support', 'equation-editor' ); ?></a>
</p>
</div>