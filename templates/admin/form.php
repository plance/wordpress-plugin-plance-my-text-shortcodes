<?php
/**
 * Page.
 *
 * @package Plance\Plugin\My_Text_Shortcodes
 */

use const Plance\Plugin\My_Text_Shortcodes\SECURITY;

defined( 'ABSPATH' ) || exit;

$shortcode = $args['shortcode'];
?>

<div class="wrap">
	<h2><?php echo esc_attr( $args['form_title'] ); ?></h2>
	<form method="post" action="<?php echo esc_url( $args['form_action'] ); ?>">
		<?php wp_nonce_field( SECURITY ); ?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Title', 'my-text-shortcodes' ); ?></th>
				<td>
					<input type="text" name="shortcode[sh_title]" value="<?php echo esc_attr( $shortcode['sh_title'] ); ?>" style="width: 40%;">
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Code', 'my-text-shortcodes' ); ?></th>
				<td>
					<input type="text" name="shortcode[sh_code]" value="<?php echo esc_attr( $shortcode['sh_code'] ); ?>" style="width: 40%;">
					<p>
						<small>
							<?php esc_html_e( 'Only Latin letters (a-z), digits (0-9), and hyphens (-) are allowed; the code must start and end with a letter or digit.', 'my-text-shortcodes' ); ?>
						</small>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'State', 'my-text-shortcodes' ); ?></th>
				<td>
					<select name="shortcode[sh_is_lock]">
						<option value="0"<?php selected( 0, $shortcode['sh_is_lock'] ); ?>><?php esc_html_e( 'Show', 'my-text-shortcodes' ); ?></option>
						<option value="1"<?php selected( 1, $shortcode['sh_is_lock'] ); ?>><?php esc_html_e( 'Hide', 'my-text-shortcodes' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Description', 'my-text-shortcodes' ); ?></th>
				<td>
					<textarea name="shortcode[sh_description]"style="width: 80%; height: 250px;" ><?php echo esc_textarea( $shortcode['sh_description'] ); ?></textarea>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
