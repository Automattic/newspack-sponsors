<?php
/**
 * Plugin Name:     Newspack Sponsors (final version, please migrate)
 * Plugin URI:      https://newspack.com
 * Description:     Final version released from the legacy plugin repository. This copy will not receive further updates. Download the current version at https://newspack.com/download-center
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

/**
 * Warn administrators that this build came from the legacy plugin repository.
 */
function newspack_sponsors_legacy_repo_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="notice notice-error">
		<p><strong><?php esc_html_e( 'You are running an outdated version of the Newspack Sponsors plugin.', 'newspack-sponsors' ); ?></strong></p>
		<p>
			<?php
			printf(
				wp_kses(
					/* translators: 1: URL of the announcement post. 2: URL of the download center. */
					__( 'This is the final version released from the legacy plugin repository, and it will not receive further updates. <a href="%1$s">Read the announcement</a>, then download the current version from the <a href="%2$s">Newspack download center</a>.', 'newspack-sponsors' ),
					[
						'a' => [
							'href' => [],
						],
					]
				),
				esc_url( 'https://newspack.com/newspack-plugins-and-themes-have-a-new-home/' ),
				esc_url( 'https://newspack.com/download-center' )
			);
			?>
		</p>
	</div>
	<?php
}

/*
 * Newspack wizard screens call remove_all_actions() on the notice hooks at priority -9999,
 * so this notice runs ahead of that to stay visible on every admin screen.
 */
add_action( 'all_admin_notices', 'newspack_sponsors_legacy_repo_notice', -99999 );
