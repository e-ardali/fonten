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
		<div class="notice <?php echo in_array( $status, array( 'saved', 'enabled', 'disabled' ), true ) ? 'notice-success' : 'notice-error'; ?>"><p><?php echo esc_html( $messages[ $status ] ); ?></p></div>
	<?php endif; ?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="fonten_save_font">
		<?php wp_nonce_field( 'fonten_save_font' ); ?>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php esc_html_e( 'Google Fonts', 'fonten' ); ?></th>
				<td>
					<label for="fonten-google-fonts-enabled">
						<input type="checkbox" id="fonten-google-fonts-enabled" name="fonten_google_fonts_enabled" value="1" <?php checked( $enabled ); ?> aria-describedby="fonten-google-fonts-description">
						<?php esc_html_e( 'Enable Google Fonts', 'fonten' ); ?>
					</label>
					<p class="description" id="fonten-google-fonts-description"><?php esc_html_e( 'Checking this option immediately connects your server to Google to retrieve the font catalog. Saving enables Google Fonts across the dashboard and lets your browser connect to Google to load the selected font. Leave it disabled to use the default WordPress font without Google requests from FontenDev.', 'fonten' ); ?></p>
					<p class="description">
						<a href="https://developers.google.com/fonts/terms" rel="noreferrer"><?php esc_html_e( 'Google Fonts Terms of Service', 'fonten' ); ?></a>
						&middot;
						<a href="https://policies.google.com/privacy" rel="noreferrer"><?php esc_html_e( 'Google Privacy Policy', 'fonten' ); ?></a>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="fonten-family"><?php esc_html_e( 'Admin font', 'fonten' ); ?></label></th>
				<td>
					<select id="fonten-family" name="fonten_family" <?php disabled( ! $enabled ); ?> aria-describedby="fonten-family-description">
						<option value="" <?php selected( is_wp_error( $fonts ) || ! isset( $fonts[ $current_family ] ) ); ?>><?php esc_html_e( 'Select a font', 'fonten' ); ?></option>
						<?php if ( ! is_wp_error( $fonts ) ) : ?>
							<?php foreach ( array_keys( $fonts ) as $fonten_family ) : ?>
								<option value="<?php echo esc_attr( $fonten_family ); ?>" <?php selected( $current_family, $fonten_family ); ?>><?php echo esc_html( $fonten_family ); ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
					<p class="description" id="fonten-family-description">
						<?php if ( is_wp_error( $fonts ) ) : ?>
							<?php echo esc_html( $fonts->get_error_message() ); ?>
						<?php elseif ( ! $enabled ) : ?>
							<?php esc_html_e( 'Enable Google Fonts to load the font list immediately, then choose a font and save. Turning it off disables font selection; save to stop Google Fonts across the dashboard.', 'fonten' ); ?>
						<?php else : ?>
							<?php esc_html_e( 'Choose a Google Font and save to apply it across the admin dashboard.', 'fonten' ); ?>
						<?php endif; ?>
					</p>
					<p class="description" id="fonten-catalog-status" role="status" aria-live="polite"></p>
				</td>
			</tr>
		</table>
		<?php submit_button( __( 'Save settings', 'fonten' ) ); ?>
	</form>
</div>
