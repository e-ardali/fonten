<?php
/**
 * Local filesystem access for generated uploads.
 *
 * @package Fonten
 */

defined( 'ABSPATH' ) || exit;

final class Fonten_Filesystem {

	/**
	 * Use local paths without requesting FTP credentials or changing the global filesystem.
	 * Permission failures are returned by the WordPress filesystem methods.
	 *
	 * @return WP_Filesystem_Direct
	 */
	public static function local() {
		if ( ! class_exists( 'WP_Filesystem_Direct' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
		}

		return new WP_Filesystem_Direct( null );
	}
}
