<?php
/**
 * Plugin Name:     Newspack Sponsors
 * Plugin URI:      https://newspack.pub
 * Description:     Add sponsors and sponsor info to posts. Allows special visual treatment for sponsored content.
 * Author:          Automattic
 * Author URI:      https://newspack.pub
 * Text Domain:     newspack-sponsors
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Newspack_Sponsors
 */

defined( 'ABSPATH' ) || exit;

// Define NEWSPACK_SPONSORS_PLUGIN_FILE.
if ( ! defined( 'NEWSPACK_SPONSORS_PLUGIN_FILE' ) ) {
	define( 'NEWSPACK_SPONSORS_PLUGIN_FILE', plugin_dir_path( __FILE__ ) );
}

// Include the main Newspack Sponsors class.
if ( ! class_exists( 'Newspack_Sponsors' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-newspack-sponsors.php';
}
