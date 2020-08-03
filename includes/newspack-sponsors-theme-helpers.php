<?php
/**
 * Newspack Sponsors theme helpers.
 *
 * Functions that can be called from themes to get sponsor info.
 *
 * @package Newspack_Sponsors
 */

namespace Newspack_Sponsors;

use \Newspack_Sponsors\Newspack_Sponsors_Core as Core;
use \Newspack_Sponsors\Newspack_Sponsors_Settings as Settings;
use \WP_Error as WP_Error;

/**
 * Get sponsors associated with the given post ID.
 *
 * @param int $post_id ID for the post to look up (optional).
 * @return array|bool|WP_Error Array of sponsor objects associated with the
 *                             post, false if we can't find a post with given
 *                             $post_id, or WP_Error if no $post_id given and
 *                             we're not on a post page.
 */
function get_sponsors_for_post( $post_id = null ) {
	if ( null === $post_id ) {
		if ( ! is_singular( 'post' ) ) {
			return new WP_Error(
				'newspack-sponsors__is_not_post',
				__( 'Please provide a $post_id if not invoking within a single post.' )
			);
		}

		$post_id = get_the_ID();
	}

	$post = get_post( $post_id );

	// Return false if there's no post for this $post_id.
	if ( empty( $post ) ) {
		return false;
	}

	$sponsors        = [];
	$direct_sponsors = get_the_terms( $post_id, Core::NEWSPACK_SPONSORS_TAX );
	$categories      = get_the_category( $post_id );
	$tags            = get_the_tags( $post_id );

	// Get sponsors directly assigned to the post.
	if ( is_array( $direct_sponsors ) ) {
		foreach ( $direct_sponsors as $direct_sponsor ) {
			$sponsor_post = get_related_post( $direct_sponsor->slug );

			if ( ! empty( $sponsor_post ) ) {
				$sponsors[] = convert_post_to_sponsor( $sponsor_post );
			}
		}
	}

	// Get sponsors for the post's categories, if any.
	$category_sponsors = get_sponsor_posts_for_terms( $categories );

	if ( is_array( $category_sponsors ) ) {
		foreach ( $category_sponsors as $category_sponsor ) {
			$hide_term_sponsor = get_post_meta( $category_sponsor->ID, 'newspack_sponsor_only_direct', true );

			// Don't add if sponsor is set to show only as direct.
			if ( empty( $hide_term_sponsor ) ) {
				$sponsors[] = convert_post_to_sponsor( $category_sponsor, 'category' );
			}
		}
	}

	// Get sponsors for the post's tags, if any.
	$tag_sponsors = get_sponsor_posts_for_terms( $tags );

	if ( is_array( $tag_sponsors ) ) {
		foreach ( $tag_sponsors as $tag_sponsor ) {
			$hide_term_sponsor = get_post_meta( $tag_sponsor->ID, 'newspack_sponsor_only_direct', true );

			// Don't add if sponsor is set to show only as direct.
			if ( empty( $hide_term_sponsor ) ) {
				$sponsors[] = convert_post_to_sponsor( $tag_sponsor, 'tag' );
			}
		}
	}

	return $sponsors;
}

/**
 * Get sponsors associated with the given term ID.
 *
 * @param int $term_id ID for the post to look up (optional).
 * @return array|bool|WP_Error Array of sponsor objects associated with the
 *                             term, false if we can't find a term with given
 *                             $term_id, or WP_Error if no $term_id given and
 *                             we're not on a term archive page.
 */
function get_sponsors_for_archive( $term_id = null ) {
	if ( null === $term_id ) {
		if ( ! is_archive() ) {
			return new WP_Error(
				'newspack-sponsors__is_not_archive',
				__( 'Please provide a $term_id if not invoking within a term archive page.' )
			);
		}

		$term = get_queried_object();
	} else {
		$term = get_term( $term_id, 'category' );

		if ( empty( $term ) ) {
			$term - get_term( $term_id, 'post_tag' );
		}
	}

	// Return false if there's no term for this $term_id.
	if ( empty( $term ) ) {
		return false;
	}

	$sponsors      = [];
	$type          = 'category' === $term->taxonomy ? 'category' : 'tag';
	$term_sponsors = get_sponsor_posts_for_terms( [ $term ] );

	if ( is_array( $term_sponsors ) ) {
		foreach ( $term_sponsors as $term_sponsor ) {
			$sponsors[] = convert_post_to_sponsor( $term_sponsor, $type );
		}
	}

	return $sponsors;
}

/**
 * Get a post for the given shadow taxonomy term by term slug.
 *
 * @param string $slug Term slug to use for looking up post.
 * @return array|bool Post object for the related post, or false.
 */
function get_related_post( $slug ) {
	$related_post = new \WP_Query(
		[
			'post_type'      => Core::NEWSPACK_SPONSORS_CPT,
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			'name'           => $slug,
			'no_found_rows'  => true,
		]
	);

	if ( empty( $related_post->posts ) || is_wp_error( $related_post ) ) {
		return false;
	}

	return $related_post->posts[0];
}

/**
 * Get all sponsors who are associated with the given terms.
 *
 * @param array $terms Array of term objects to look up.
 * @return array|bool Array of sponsor post objects, if any, or false.
 */
function get_sponsor_posts_for_terms( $terms ) {
	if ( empty( $terms ) ) {
		return false;
	}

	$tax_query_args = [];

	foreach ( $terms as $term ) {
		if ( ! empty( $term->taxonomy ) && ! empty( $term->term_id ) ) {
			$tax_query_args[] = [
				'taxonomy' => $term->taxonomy,
				'field'    => 'term_id',
				'terms'    => $term->term_id,
			];
		}
	}

	// No need to run query if there are no valid terms to query.
	if ( empty( $tax_query_args ) ) {
		return false;
	}

	$tax_query_args['relation'] = 'OR';

	$sponsor_posts = new \WP_Query(
		[
			'post_type'      => Core::NEWSPACK_SPONSORS_CPT,
			'posts_per_page' => 100,
			'post_status'    => 'publish',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			'tax_query'      => $tax_query_args,
		]
	);

	if ( empty( $sponsor_posts->posts ) || is_wp_error( $sponsor_posts ) ) {
		return false;
	}

	return $sponsor_posts->posts;
}

/**
 * Formats a post object into a sponsor object, for ease of theme developer use.
 *
 * @param array  $post Post object to convert.
 * @param string $type Type of sponsorship: direct, category, or tag?. Default: 'direct'.
 * @return array|bool Sponsor object, or false.
 */
function convert_post_to_sponsor( $post, $type = 'direct' ) {
	if ( empty( $post ) ) {
		return false;
	}

	$sponsor_sitewide_settings = Settings::get_settings();

	$sponsor_byline     = get_post_meta( $post->ID, 'newspack_sponsor_byline_prefix', true );
	$sponsor_url        = get_post_meta( $post->ID, 'newspack_sponsor_url', true );
	$sponsor_flag       = get_post_meta( $post->ID, 'newspack_sponsor_flag_override', true );
	$sponsor_scope      = get_post_meta( $post->ID, 'newspack_sponsor_sponsorship_scope', true );
	$sponsor_disclaimer = get_post_meta( $post->ID, 'newspack_sponsor_disclaimer_override', true );

	// Check for single-sponsor overrides, default to site-wide options.
	if ( empty( $sponsor_byline ) ) {
		$sponsor_byline = $sponsor_sitewide_settings['byline'];
	}
	if ( empty( $sponsor_flag ) ) {
		$sponsor_flag = $sponsor_sitewide_settings['flag'];
	}
	if ( empty( $sponsor_disclaimer ) ) {
		$sponsor_disclaimer = str_replace( '[sponsor name]', $post->post_title, $sponsor_sitewide_settings['disclaimer'] );
	}

	return [
		'sponsor_type'       => $type,
		'sponsor_id'         => $post->ID,
		'sponsor_name'       => $post->post_title,
		'sponsor_slug'       => $post->post_name,
		'sponsor_blurb'      => $post->post_content,
		'sponsor_url'        => $sponsor_url,
		'sponsor_byline'     => $sponsor_byline,
		'sponsor_logo'       => get_the_post_thumbnail( $post->ID, 'medium', [ 'class' => 'newspack-sponsor-logo' ] ),
		'sponsor_flag'       => $sponsor_flag,
		'sponsor_scope'      => ! empty( $sponsor_scope ) ? $sponsor_scope : 'native', // Default: native, not underwritten.
		'sponsor_disclaimer' => $sponsor_disclaimer,
	];
}
