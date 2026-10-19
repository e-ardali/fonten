<?php
/**
 * Google Fonts catalog and generated admin stylesheets.
 *
 * @package Fonten
 */

defined( 'ABSPATH' ) || exit;

final class Fonten_Fonts {

	/**
	 * Only explicit, saved consent enables Google Fonts, including on upgrades.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		return '1' === get_option( 'fonten_google_fonts_enabled', '0' );
	}

	/**
	 * Fetch the public Google Fonts catalog, keeping a last successful copy.
	 *
	 * @param bool $request_consent Explicit consent for this catalog request only.
	 * @return array|WP_Error
	 */
	public function get_catalog( $request_consent = false ) {
		if ( ! self::is_enabled() && ! $request_consent ) {
			return new WP_Error( 'google_fonts_disabled', __( 'Enable Google Fonts and save before choosing a font.', 'fonten' ) );
		}

		$cached = get_transient( 'fonten_google_fonts' );
		if ( is_array( $cached ) && ! empty( $cached ) ) {
			return $cached;
		}

		$response = wp_remote_get(
			'https://fonts.google.com/metadata/fonts',
			array( 'timeout' => 15, 'limit_response_size' => 10 * MB_IN_BYTES )
		);
		$catalog  = array();

		if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
			$body = wp_remote_retrieve_body( $response );
			// Some Google metadata responses include an anti-XSSI prefix.
			$body = preg_replace( "/^\)\]\}'[^\n]*\n/", '', $body );
			$data = json_decode( $body, true );

			foreach ( (array) ( $data['familyMetadataList'] ?? array() ) as $font ) {
				$family = $font['family'] ?? '';
				if ( ! is_string( $family ) || ! preg_match( "/^[a-zA-Z0-9][a-zA-Z0-9 .'-]*$/D", $family ) ) {
					continue;
				}

				$weights = array();
				foreach ( array_keys( (array) ( $font['fonts'] ?? array() ) ) as $weight ) {
					if ( preg_match( '/^[1-9][0-9]{0,3}$/D', (string) $weight ) && (int) $weight <= 1000 ) {
						$weights[] = (int) $weight;
					}
				}

				$axis = 'wght';
				if ( ! $weights ) {
					// A few families are available exclusively in italic.
					$axis = 'ital,wght';
					foreach ( array_keys( (array) ( $font['fonts'] ?? array() ) ) as $weight ) {
						if ( preg_match( '/^([1-9][0-9]{0,3})i$/D', (string) $weight, $match ) && (int) $match[1] <= 1000 ) {
							$weights[] = (int) $match[1];
						}
					}
				}

				if ( $weights ) {
					sort( $weights, SORT_NUMERIC );
					if ( 'ital,wght' === $axis ) {
						$weights = array_map(
							static function ( $weight ) {
								return '1,' . $weight;
							},
							$weights
						);
					}
					$catalog[ $family ] = array( 'axis' => $axis, 'weights' => $weights );
				}
			}
		}

		if ( $catalog ) {
			ksort( $catalog, SORT_NATURAL | SORT_FLAG_CASE );
			set_transient( 'fonten_google_fonts', $catalog, DAY_IN_SECONDS );
			update_option( 'fonten_google_fonts_backup', $catalog, false );
			return $catalog;
		}

		$backup = get_option( 'fonten_google_fonts_backup', array() );
		if ( is_array( $backup ) && $backup ) {
			set_transient( 'fonten_google_fonts', $backup, 5 * MINUTE_IN_SECONDS );
			return $backup;
		}

		return new WP_Error( 'catalog_unavailable', __( 'Google Fonts could not be loaded. Please try again later. Your current font has not changed.', 'fonten' ) );
	}

	/**
	 * Write a versioned CSS file before committing the new selection.
	 *
	 * @param string $family Font family from the catalog.
	 * @return array|WP_Error Saved stylesheet metadata or an error.
	 */
	public function generate_stylesheet( $family ) {
		$catalog = $this->get_catalog();
		if ( is_wp_error( $catalog ) ) {
			return $catalog;
		}
		if ( ! isset( $catalog[ $family ] ) ) {
			return new WP_Error( 'invalid_font', __( 'Please select a font from Google Fonts.', 'fonten' ) );
		}

		$uploads = wp_upload_dir();
		if ( ! empty( $uploads['error'] ) ) {
			return new WP_Error( 'css_write_failed', __( 'The CSS file could not be created. Check that the uploads directory is writable.', 'fonten' ) );
		}

		$directory = trailingslashit( $uploads['basedir'] ) . 'fonten';
		$template  = file_get_contents( FONTEN_PATH . 'assets/css/admin.css' );
		if ( false === $template || ! wp_mkdir_p( $directory ) ) {
			return new WP_Error( 'css_write_failed', __( 'The CSS file could not be created. Check that the uploads directory is writable.', 'fonten' ) );
		}

		$font = $catalog[ $family ];
		$url = 'https://fonts.googleapis.com/css2?family=' . rawurlencode( $family ) . ':' . $font['axis'] . '@' . implode( ';', $font['weights'] ) . '&display=swap';
		$css = '@import url("' . $url . '");' . "\n\n"
			. ':root { --fonten-admin-font: "' . $family . '"; }' . "\n\n" . $template;

		// Content-based names prevent stale browser caches and preserve the old file on failure.
		$filename  = 'admin-' . hash( 'sha256', $css ) . '.css';
		$path      = trailingslashit( $directory ) . $filename;
		$temporary = $path . '.' . wp_generate_password( 12, false, false ) . '.tmp';
		if ( strlen( $css ) !== @file_put_contents( $temporary, $css, LOCK_EX ) || ! @rename( $temporary, $path ) ) {
			if ( is_file( $temporary ) ) {
				@unlink( $temporary );
			}
			return new WP_Error( 'css_write_failed', __( 'The CSS file could not be created. Your current font has not changed.', 'fonten' ) );
		}

		return array( 'family' => $family, 'file' => $filename );
	}
}
