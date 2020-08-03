<?php
/**
 * Newspack Sponsors Settings Page
 *
 * @package Newspack
 */

namespace Newspack_Sponsors;

use \Newspack_Sponsors\Newspack_Sponsors_Core as Core;

defined( 'ABSPATH' ) || exit;

/**
 * Manages Settings page.
 */
final class Newspack_Sponsors_Settings {
	/**
	 * Set up hooks.
	 */
	public static function init() {
		add_action( 'admin_menu', [ __CLASS__, 'add_plugin_page' ] );
		add_action( 'admin_init', [ __CLASS__, 'page_init' ] );
	}

	/**
	 * Retreives list of settings.
	 *
	 * @return array Settings list.
	 */
	public static function get_settings_list() {
		return array(
			array(
				'label'       => __( 'Default Sponsor Byline Prefix', 'newspack-sponsors' ),
				'placeholder' => __( 'Default: “Sponsored by”', 'newspack-sponsors' ),
				'key'         => 'newspack_sponsors_default_byline',
			),
			array(
				'label'       => __( 'Default Sponsored Flag Label', 'newspack-sponsors' ),
				'placeholder' => __( 'Default: “Sponsored”', 'newspack-sponsors' ),
				'key'         => 'newspack_sponsors_default_flag',
			),
			array(
				'label'       => __( 'Default Sponsorship Explanation', 'newspack-sponsors' ),
				'placeholder' => __( 'Default: none', 'newspack-sponsors' ),
				'key'         => 'newspack_sponsors_explanation',
				'type'        => 'textarea',
			),
		);
	}

	/**
	 * Add options page
	 */
	public static function add_plugin_page() {
		add_submenu_page(
			'edit.php?post_type=' . Core::NEWSPACK_SPONSORS_CPT,
			__( 'Newspack Sponsors: Site-wide Settings', 'newspack-sponsors' ),
			__( 'Settings', 'newspack-sponsors' ),
			'manage_options',
			'newspack-sponsors-settings-admin',
			[ __CLASS__, 'create_admin_page' ]
		);
	}

	/**
	 * Options page callback
	 */
	public static function create_admin_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Newspack Sponsors: Site-wide Settings', 'newspack-sponsors' ); ?></h1>
			<form method="post" action="options.php">
			<?php
				settings_fields( 'newspack_sponsors_options_group' );
				do_settings_sections( 'newspack-sponsors-settings-admin' );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public static function page_init() {
		add_settings_section(
			'newspack_sponsors_options_group',
			null,
			null,
			'newspack-sponsors-settings-admin'
		);
		foreach ( self::get_settings_list() as $setting ) {
			register_setting(
				'newspack_sponsors_options_group',
				$setting['key']
			);
			add_settings_field(
				$setting['key'],
				$setting['label'],
				[ __CLASS__, 'newspack_sponsors_settings_callback' ],
				'newspack-sponsors-settings-admin',
				'newspack_sponsors_options_group',
				$setting
			);
		};
	}

	/**
	 * Render settings fields.
	 *
	 * @param array $setting Settings array.
	 */
	public static function newspack_sponsors_settings_callback( $setting ) {
		$key         = $setting['key'];
		$type        = $setting['type'];
		$placeholder = $setting['placeholder'];
		$value       = get_option( $key, false );

		if ( 'textarea' === $type ) {
			printf(
				'<textarea id="%s" name="%s" class="widefat" rows="4" placeholder="%s">%s</textarea>',
				esc_attr( $key ),
				esc_attr( $key ),
				esc_attr( $placeholder ),
				esc_attr( $value )
			);
		} else {
			printf(
				'<input type="text" id="%s" name="%s" placeholder="%s" value="%s" class="widefat" />',
				esc_attr( $key ),
				esc_attr( $key ),
				esc_attr( $placeholder ),
				esc_attr( $value )
			);
		}
	}
}

if ( is_admin() ) {
	Newspack_Sponsors_Settings::init();
}
