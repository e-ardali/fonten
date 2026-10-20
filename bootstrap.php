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


require_once FONTEN_PATH . 'includes/class-fonten-fonts.php';
require_once FONTEN_PATH . 'includes/class-fonten-cleanup.php';
require_once FONTEN_PATH . 'includes/class-fonten-admin.php';

add_action( 'admin_enqueue_scripts', array( new Fonten_Admin(), 'enqueue_assets' ) );
add_action( 'admin_post_fonten_save_font', array( new Fonten_Admin(), 'save_font' ) );
add_action( 'wp_ajax_fonten_get_fonts', array( new Fonten_Admin(), 'get_fonts' ) );

add_action(
	'admin_menu',
	function () {

		$admin = new Fonten_Admin();

		add_options_page(
			__( 'FontenDev Settings', 'fonten' ),
			__( 'FontenDev', 'fonten' ),
			'manage_options',
			'fonten',
			array( $admin, 'render_settings_page' )
		);
	}
);
