<?php
/**
 * Remove Fonten data when the plugin is deactivated.
 *
 * @package Fonten
 */

defined( 'ABSPATH' ) || exit;

final class Fonten_Cleanup {

	/**
	 * Clean the current site, or all sites on network deactivation.
	 *
	 * @param bool $network_wide Whether the plugin is being network-deactivated.
	 * @return void
	 */
	public static function deactivate( $network_wide = false ) {
		if ( is_multisite() && $network_wide ) {
			$offset = 0;
			do {
				$sites = get_sites(
					array(
						'network_id' => get_current_network_id(),
						'fields'     => 'ids',
						'number'     => 100,
						'offset'     => $offset,
						'orderby'    => 'id',
						'order'      => 'ASC',
					)
				);
				foreach ( $sites as $site_id ) {
					switch_to_blog( $site_id );
					try {
						self::clean_site();
					} finally {
						restore_current_blog();
					}
				}
				$offset += 100;
			} while ( count( $sites ) === 100 );
			return;
		}

		self::clean_site();
	}

	/**
	 * Delete only Fonten's options, cached catalog, and generated files.
	 *
	 * @return void
	 */
	private static function clean_site() {
		delete_transient( 'fonten_google_fonts' );
		foreach ( array(
			'fonten_google_fonts_enabled',
			'fonten_settings',
			'fonten_google_fonts_backup',
			// Also remove database leftovers if an external object cache is now in use.
			'_transient_fonten_google_fonts',
			'_transient_timeout_fonten_google_fonts',
		) as $option ) {
			delete_option( $option );
		}

		$uploads = wp_upload_dir( null, false );
		if ( empty( $uploads['basedir'] ) ) {
			return;
		}
		$directory = trailingslashit( $uploads['basedir'] ) . 'fonten';
		if ( ! is_dir( $directory ) || is_link( $directory ) ) {
			return;
		}

		foreach ( glob( $directory . '/admin-*' ) ?: array() as $file ) {
			if ( preg_match( '/^admin-[a-f0-9]{64}\.css(?:\.[a-zA-Z0-9]{12}\.tmp)?$/D', basename( $file ) ) && ! is_dir( $file ) ) {
				wp_delete_file( $file );
			}
		}
		// Remove the dedicated directory only when empty; leave unrelated files alone.
		@rmdir( $directory );
	}
}
