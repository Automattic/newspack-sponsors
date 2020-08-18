<?php
/**
 * Newspack Sponsors editor.
 *
 * Editor resources for Newspack Sponsors CPT.
 *
 * @package Newspack_Sponsors
 */

namespace Newspack_Sponsors;

use \Newspack_Sponsors\Newspack_Sponsors_Core as Core;
use \Newspack_Sponsors\Newspack_Sponsors_Settings as Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Editor class.
 * Editor resources needed for the Sponsors CPT.
 */
final class Newspack_Sponsors_Editor {

	/**
	 * The single instance of the class.
	 *
	 * @var Editor
	 */
	protected static $instance = null;

	/**
	 * Main Editor Instance.
	 * Ensures only one instance of Editor is loaded or can be loaded.
	 *
	 * @return Editor - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'the_post', [ __CLASS__, 'strip_editor_modifications' ] );
		add_filter( 'wpseo_primary_term_taxonomies', [ __CLASS__, 'disable_yoast_primary_category_picker' ], 10, 2 );
		add_action( 'enqueue_block_editor_assets', [ __CLASS__, 'enqueue_block_editor_assets' ] );
	}

	/**
	 * Remove certain editor enqueued assets which might not be compatible with this post type.
	 */
	public static function strip_editor_modifications() {
		if ( ! self::is_editing_sponsor() ) {
			return;
		}

		$enqueue_block_editor_assets_filters = $GLOBALS['wp_filter']['enqueue_block_editor_assets']->callbacks;
		$disallowed_assets                   = [
			'Newspack_Popups::enqueue_block_editor_assets',
			'Newspack_Newsletters_Editor::enqueue_block_editor_assets',
			'Newspack_Ads_Blocks::enqueue_block_editor_assets',
			'newspack_ads_enqueue_suppress_ad_assets',
		];

		foreach ( $enqueue_block_editor_assets_filters as $index => $filter ) {
			$action_handlers = array_keys( $filter );
			foreach ( $action_handlers as $handler ) {
				if ( in_array( $handler, $disallowed_assets ) ) {
					remove_action( 'enqueue_block_editor_assets', $handler, $index );
				}
			}
		}
	}

	/**
	 * Disable the Yoast primary category picker for Sponsor posts and terms.
	 *
	 * @param array  $taxonomies Array of taxonomies.
	 * @param string $post_type Post type of the current post.
	 */
	public static function disable_yoast_primary_category_picker( $taxonomies, $post_type ) {
		// Disable for all taxonomies on Sponsor posts.
		if ( Core::NEWSPACK_SPONSORS_CPT === $post_type ) {
			return [];
		}

		// Disable for sponsor tax terms everywhere.
		if ( isset( $taxonomies[ Core::NEWSPACK_SPONSORS_TAX ] ) ) {
			unset( $taxonomies[ Core::NEWSPACK_SPONSORS_TAX ] );
		}

		return $taxonomies;
	}

	/**
	 * Is editing a sponsor?
	 */
	public static function is_editing_sponsor() {
		$post_type = get_post()->post_type;
		return Core::NEWSPACK_SPONSORS_CPT === $post_type;
	}

	/**
	 * Load up JS/CSS for editor.
	 */
	public static function enqueue_block_editor_assets() {
		if ( ! self::is_editing_sponsor() && 'post' !== get_post_type() ) {
			return;
		}

		wp_enqueue_script(
			'newspack-sponsors-editor',
			NEWSPACK_SPONSORS_URL . 'dist/editor.js',
			[],
			filemtime( NEWSPACK_SPONSORS_PLUGIN_FILE . 'dist/editor.js' ),
			true
		);

		wp_localize_script(
			'newspack-sponsors-editor',
			'newspack_sponsors_data',
			[
				'settings' => Settings::get_settings(),
				'defaults' => Settings::get_default_settings(),
			]
		);

		wp_register_style(
			'newspack-sponsors-editor',
			plugins_url( '../dist/editor.css', __FILE__ ),
			[],
			filemtime( NEWSPACK_SPONSORS_PLUGIN_FILE . 'dist/editor.css' )
		);
		wp_style_add_data( 'newspack-sponsors-editor', 'rtl', 'replace' );
		wp_enqueue_style( 'newspack-sponsors-editor' );
	}
}

Newspack_Sponsors_Editor::instance();
