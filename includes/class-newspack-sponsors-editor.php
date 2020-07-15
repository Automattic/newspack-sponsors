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
		add_filter( 'wpseo_primary_term_taxonomies', [ __CLASS__, 'disable_yoast_category_picker' ] );
		add_action( 'enqueue_block_editor_assets', [ __CLASS__, 'enqueue_block_editor_assets' ] );
	}

	/**
	 * Remove all editor enqueued assets besides this plugins' and disable some editor features.
	 * This is to prevent theme styles being loaded in the editor.
	 * Remove editor color palette theme supports - the MJML parser uses a static list of default editor colors.
	 */
	public static function strip_editor_modifications() {
		if ( ! self::is_editing_sponsor() ) {
			return;
		}

		$enqueue_block_editor_assets_filters = $GLOBALS['wp_filter']['enqueue_block_editor_assets']->callbacks;
		foreach ( $enqueue_block_editor_assets_filters as $index => $filter ) {
			$action_handlers = array_keys( $filter );
			foreach ( $action_handlers as $handler ) {
				if ( __CLASS__ . '::enqueue_block_editor_assets' != $handler ) {
					remove_action( 'enqueue_block_editor_assets', $handler, $index );
				}
			}
		}

		remove_editor_styles();
	}

	/**
	 * Disable the Yoast primary category picker for Sponsor posts.
	 *
	 * @param array $categories Array of categories.
	 */
	public static function disable_yoast_category_picker( $categories ) {
		if ( Core::NEWSPACK_SPONSORS_CPT === get_post_type() ) {
			return [];
		}

		return $categories;
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
		if ( ! self::is_editing_sponsor() ) {
			return;
		}

		wp_enqueue_script(
			'newspack-sponsors-editor',
			NEWSPACK_SPONSORS_URL . 'dist/editor.js',
			[],
			filemtime( NEWSPACK_SPONSORS_PLUGIN_FILE . 'dist/editor.js' ),
			true
		);
	}
}

Newspack_Sponsors_Editor::instance();
