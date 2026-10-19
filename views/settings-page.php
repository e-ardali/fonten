<?php
/**
 * Settings page markup.
 *
 * @package Fonten
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<?php if ( isset( $messages[ $status ] ) ) : ?>
		<div class="notice <?php echo 'saved' === $status ? 'notice-success' : 'notice-error'; ?>"><p><?php echo esc_html( $messages[ $status ] ); ?></p></div>
	<?php endif; ?>
	<?php if ( is_wp_error( $fonts ) ) : ?>
		<div class="notice notice-error"><p><?php echo esc_html( $fonts->get_error_message() ); ?></p></div>
	<?php else : ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="fonten_save_font">
			<?php wp_nonce_field( 'fonten_save_font' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="fonten-family"><?php esc_html_e( 'Admin font', 'fonten' ); ?></label></th>
					<td>
						<select id="fonten-family" name="fonten_family" required aria-describedby="fonten-family-description">
							<?php if ( ! isset( $fonts[ $current_family ] ) ) : ?>
								<option value="" selected disabled><?php esc_html_e( 'Select a font', 'fonten' ); ?></option>
							<?php endif; ?>
							<?php foreach ( array_keys( $fonts ) as $family ) : ?>
								<option value="<?php echo esc_attr( $family ); ?>" <?php selected( $current_family, $family ); ?>><?php echo esc_html( $family ); ?></option>
							<?php endforeach; ?>
						</select>
						<p class="description" id="fonten-family-description"><?php esc_html_e( 'Choose a Google Font and save to apply it across the admin dashboard.', 'fonten' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button( __( 'Save font', 'fonten' ) ); ?>
		</form>
	<?php endif; ?>
</div>
