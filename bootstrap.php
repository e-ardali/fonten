<?php
/**
 * Plugin bootstrap and admin menu registration.
 *
 * @package Fonten
 */

defined( 'ABSPATH' ) || exit;

define( 'FONTEN_VERSION', '0.1.0' );
define( 'FONTEN_PATH', plugin_dir_path( __FILE__ ) );
define( 'FONTEN_URL', plugin_dir_url( __FILE__ ) );


require_once FONTEN_PATH . 'includes/class-fonten-admin.php';

add_action(
	'admin_menu',
	function () {

		$admin = new Fonten_Admin();

		add_options_page(
			__( 'Fonten Settings', 'fonten' ),
			__( 'Fonten', 'fonten' ),
			'manage_options',
			'fonten',
			array( $admin, 'render_settings_page' )
		);
	}
);
