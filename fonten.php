<?php
/**
 * Plugin Name: Fonten
 * Description: Customize the WordPress admin dashboard typography using Google Fonts.
 * Version: 0.1.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: fonten
 * Domain Path: /languages
 *
 * @package Fonten
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/bootstrap.php';

register_deactivation_hook( __FILE__, array( 'Fonten_Cleanup', 'deactivate' ) );
