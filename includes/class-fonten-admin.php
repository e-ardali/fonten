<?php
/**
 * WordPress admin integration.
 *
 * @package Fonten
 */

defined( 'ABSPATH' ) || exit;

final class Fonten_Admin {

	/**
	 * Load the saved font across the admin dashboard.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		$settings = get_option( 'fonten_settings', array() );
		$filename = $settings['file'] ?? '';
		if ( is_string( $filename ) && preg_match( '/^admin-[a-f0-9]{64}\.css$/D', $filename ) ) {
			$uploads = wp_upload_dir( null, false );
			if ( empty( $uploads['error'] ) && is_file( trailingslashit( $uploads['basedir'] ) . 'fonten/' . $filename ) ) {
				wp_enqueue_style( 'fonten-admin', trailingslashit( $uploads['baseurl'] ) . 'fonten/' . $filename, array(), null );
				return;
			}
		}

		// Preserve the existing test font until the first successful save.
		wp_enqueue_style(
			'fonten-estedad',
			'https://fonts.googleapis.com/css2?family=Estedad:wght@100..900&display=swap',
			array(),
			null
		);

		wp_enqueue_style(
			'fonten-admin',
			FONTEN_URL . 'assets/css/admin.css',
			array( 'fonten-estedad' ),
			FONTEN_VERSION
		);
	}

	/**
	 * Validate and save the selected font after generating its stylesheet.
	 *
	 * @return void
	 */
	public function save_font() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to change the admin font.', 'fonten' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'fonten_save_font' );

		$family = isset( $_POST['fonten_family'] ) && is_string( $_POST['fonten_family'] )
			? sanitize_text_field( wp_unslash( $_POST['fonten_family'] ) ) : '';
		$result = ( new Fonten_Fonts() )->generate_stylesheet( $family );
		$status = 'saved';
		if ( is_wp_error( $result ) ) {
			$status = $result->get_error_code();
		} else {
			update_option( 'fonten_settings', $result, false );
			if ( get_option( 'fonten_settings' ) !== $result ) {
				$status = 'save_failed';
			}
		}

		wp_safe_redirect( add_query_arg( array( 'page' => 'fonten', 'fonten_status' => $status ), admin_url( 'options-general.php' ) ) );
		exit;
	}

	/**
	 * Render the initial settings screen.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$fonts          = ( new Fonten_Fonts() )->get_catalog();
		$settings       = get_option( 'fonten_settings', array() );
		$current_family = $settings['family'] ?? 'Estedad';
		$status         = isset( $_GET['fonten_status'] ) && is_string( $_GET['fonten_status'] )
			? sanitize_key( wp_unslash( $_GET['fonten_status'] ) ) : '';
		$messages       = array(
			'saved'               => __( 'Font saved and a new CSS file generated.', 'fonten' ),
			'invalid_font'        => __( 'Please select a font from Google Fonts.', 'fonten' ),
			'catalog_unavailable' => __( 'Google Fonts could not be loaded. Please try again later. Your current font has not changed.', 'fonten' ),
			'css_write_failed'    => __( 'The CSS file could not be created. Check that the uploads directory is writable. Your current font has not changed.', 'fonten' ),
			'save_failed'         => __( 'The font settings could not be saved. Please try again.', 'fonten' ),
		);

		require FONTEN_PATH . 'views/settings-page.php';
	}
}
