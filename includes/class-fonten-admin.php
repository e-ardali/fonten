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
	public function enqueue_assets( $hook_suffix = '' ) {
		if ( 'settings_page_fonten' === $hook_suffix ) {
			wp_enqueue_script( 'fonten-settings', FONTEN_URL . 'assets/js/settings.js', array(), (string) filemtime( FONTEN_PATH . 'assets/js/settings.js' ), true );
			wp_localize_script(
				'fonten-settings',
				'fontenSettings',
				array(
					'url'         => admin_url( 'admin-ajax.php' ),
					'nonce'       => wp_create_nonce( 'fonten_get_fonts' ),
					'loading'     => __( 'Loading Google Fonts…', 'fonten' ),
					'error'       => __( 'Google Fonts could not be loaded. Turn the option off and on to retry.', 'fonten' ),
					'placeholder' => __( 'Select a font', 'fonten' ),
				)
			);
		}

		if ( ! Fonten_Fonts::is_enabled() ) {
			return;
		}

		$settings = get_option( 'fonten_settings', array() );
		$filename = $settings['file'] ?? '';
		if ( is_string( $filename ) && preg_match( '/^admin-[a-f0-9]{64}\.css$/D', $filename ) ) {
			$uploads = wp_upload_dir( null, false );
			if ( empty( $uploads['error'] ) && is_file( trailingslashit( $uploads['basedir'] ) . 'fonten/' . $filename ) ) {
				wp_enqueue_style( 'fonten-admin', trailingslashit( $uploads['baseurl'] ) . 'fonten/' . $filename, array(), null );
				return;
			}
		}
	}

	/**
	 * Fetch the catalog after the administrator explicitly checks the option.
	 * This grants no persistent permission to load fonts on other pages.
	 *
	 * @return void
	 */
	public function get_fonts() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to load Google Fonts.', 'fonten' ) ), 403 );
			return;
		}
		check_ajax_referer( 'fonten_get_fonts', 'nonce' );
		if ( ! isset( $_POST['consent'] ) || '1' !== $_POST['consent'] ) {
			wp_send_json_error( array( 'message' => __( 'Enable Google Fonts before loading the catalog.', 'fonten' ) ), 400 );
			return;
		}

		$fonts = ( new Fonten_Fonts() )->get_catalog( true );
		if ( is_wp_error( $fonts ) ) {
			wp_send_json_error( array( 'message' => $fonts->get_error_message() ), 502 );
			return;
		}
		wp_send_json_success( array( 'fonts' => array_keys( $fonts ) ) );
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

		$enabled = isset( $_POST['fonten_google_fonts_enabled'] ) && '1' === $_POST['fonten_google_fonts_enabled'];
		$family = isset( $_POST['fonten_family'] ) && is_string( $_POST['fonten_family'] )
			? sanitize_text_field( wp_unslash( $_POST['fonten_family'] ) ) : '';
		if ( ! $enabled || ! Fonten_Fonts::is_enabled() ) {
			// Save persistent consent before generating a font; opting out never fetches.
			$value = $enabled ? '1' : '0';
			update_option( 'fonten_google_fonts_enabled', $value, false );
			$status = get_option( 'fonten_google_fonts_enabled' ) === $value
				? ( $enabled ? 'enabled' : 'disabled' ) : 'save_failed';
			if ( ! $enabled || 'save_failed' === $status || '' === $family ) {
				$this->redirect_to_settings( $status );
				return;
			}
		}

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

		$this->redirect_to_settings( $status );
	}

	/**
	 * Return to the settings page after saving.
	 *
	 * @param string $status Save result.
	 * @return void
	 */
	private function redirect_to_settings( $status ) {
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

		$enabled        = Fonten_Fonts::is_enabled();
		// A previously downloaded list can populate the disabled select without contacting Google.
		$cached_fonts   = get_transient( 'fonten_google_fonts' );
		$cached_fonts   = is_array( $cached_fonts ) && $cached_fonts ? $cached_fonts : get_option( 'fonten_google_fonts_backup', array() );
		$fonts          = $enabled ? ( new Fonten_Fonts() )->get_catalog() : ( is_array( $cached_fonts ) ? $cached_fonts : array() );
		$settings       = get_option( 'fonten_settings', array() );
		$current_family = $settings['family'] ?? '';
		$status         = isset( $_GET['fonten_status'] ) && is_string( $_GET['fonten_status'] )
			? sanitize_key( wp_unslash( $_GET['fonten_status'] ) ) : '';
		$messages       = array(
			'enabled'             => __( 'Google Fonts enabled. You can now select and save an admin font.', 'fonten' ),
			'disabled'            => __( 'Google Fonts disabled. The default WordPress font is restored.', 'fonten' ),
			'saved'               => __( 'Font saved and a new CSS file generated.', 'fonten' ),
			'invalid_font'        => __( 'Please select a font from Google Fonts.', 'fonten' ),
			'catalog_unavailable' => __( 'Google Fonts could not be loaded. Please try again later. Your current font has not changed.', 'fonten' ),
			'css_write_failed'    => __( 'The CSS file could not be created. Check that the uploads directory is writable. Your current font has not changed.', 'fonten' ),
			'save_failed'         => __( 'The font settings could not be saved. Please try again.', 'fonten' ),
		);

		require FONTEN_PATH . 'views/settings-page.php';
	}
}
