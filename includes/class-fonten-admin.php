<?php
/**
 * WordPress admin integration.
 *
 * @package Fonten
 */

defined( 'ABSPATH' ) || exit;

final class Fonten_Admin {

	/**
	 * Render the initial settings screen.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		require FONTEN_PATH . 'views/settings-page.php';
	}
}
