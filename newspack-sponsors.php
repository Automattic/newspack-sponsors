<?php
/**
 * Plugin Name:     Newspack Sponsors (WRONG VERSION)
 * Plugin URI:      https://newspack.com
 * Description:     This plugin was downloaded from the legacy plugin repo. Please download the latest version from https://github.com/Automattic/newspack-workspace.
 * Author:          Automattic
 * Author URI:      https://newspack.com
 * Text Domain:     newspack-sponsors
 * Domain Path:     /languages
 * Version:         2.2.0
 *
 * @package         Newspack_Sponsors
 */

defined( 'ABSPATH' ) || exit;

// Define NEWSPACK_SPONSORS_PLUGIN_FILE.
if ( ! defined( 'NEWSPACK_SPONSORS_PLUGIN_FILE' ) ) {
	define( 'NEWSPACK_SPONSORS_PLUGIN_FILE', plugin_dir_path( __FILE__ ) );
	define( 'NEWSPACK_SPONSORS_URL', plugin_dir_url( __FILE__ ) );
}

// Include plugin resources.
require_once NEWSPACK_SPONSORS_PLUGIN_FILE . '/vendor/autoload.php';
require_once NEWSPACK_SPONSORS_PLUGIN_FILE . '/includes/class-core.php';
require_once NEWSPACK_SPONSORS_PLUGIN_FILE . '/includes/class-settings.php';
require_once NEWSPACK_SPONSORS_PLUGIN_FILE . '/includes/class-editor.php';
require_once NEWSPACK_SPONSORS_PLUGIN_FILE . '/includes/theme-helpers.php';
