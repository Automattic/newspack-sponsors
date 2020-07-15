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

/**
 * Get sponsors associated with the given post ID.
 *
 * @param int $post_id ID for the post to look up.
 * @return array Array of sponsor IDs associated with the post.
 */
function get_sponsors_for_post( $post_id ) {
	$post = get_post( $post_id );

	if ( empty( $post ) ) {
		return [];
	}

	$sponsors        = [];
	$direct_sponsors = get_the_terms( $post_id, Core::NEWSPACK_SPONSORS_TAX );
	$categories      = get_the_category( $post_id );
	$tags            = get_the_tags( $post_id );
	$post_terms      = array_merge( is_array( $categories ) ? $categories : [], is_array( $tags ) ? $tags : [] );

	// Get sponsors directly assigned to the post. These take precedence over category/tag sponsors.
	if ( is_array( $direct_sponsors ) ) {
		foreach ( $direct_sponsors as $direct_sponsor ) {
			$sponsor_post = get_related_post( $direct_sponsor->slug );

			if ( ! empty( $sponsor_post ) ) {
				$sponsors[] = convert_post_to_sponsor( $sponsor_post );
			}
		}
	}

	// Get sponsors for the post's categories and tags, if any.
	foreach ( $post_terms as $term ) {
		if ( ! empty( $term->taxonomy ) ) {
			$term_sponsors = get_sponsor_posts_for_term( $term );

			if ( ! empty( $term_sponsors ) ) {
				foreach ( $term_sponsors as $term_sponsor ) {
					$sponsors[] = convert_post_to_sponsor( $term_sponsor, $term->taxonomy );
				}
			}
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
 * Get all sponsors who are associated with the given term.
 *
 * @param array $term Term object to look up.
 * @return array|bool Array of sponsor objects, if any, or false.
 */
function get_sponsor_posts_for_term( $term ) {
	if ( empty( $term ) || empty( $term->taxonomy ) || empty( $term->term_id ) ) {
		return false;
	}

	$sponsor_posts = new \WP_Query(
		[
			'post_type'      => Core::NEWSPACK_SPONSORS_CPT,
			'posts_per_page' => 100,
			'post_status'    => 'publish',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			'tax_query'      => [
				[
					'taxonomy' => $term->taxonomy,
					'field'    => 'term_id',
					'terms'    => $term->term_id,
				],
			],
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

	$sponsor_byline = get_post_meta( $post->ID, 'newspack_sponsor_byline_prefix', true );

	return [
		'sponsor_type'   => $type,
		'sponsor_id'     => $post->ID,
		'sponsor_name'   => $post->post_title,
		'sponsor_slug'   => $post->post_name,
		'sponsor_blurb'  => $post->post_content,
		'sponsor_url'    => get_post_meta( $post->ID, 'newspack_sponsor_url', true ),
		'sponsor_byline' => ! empty( $sponsor_byline ) ? $sponsor_byline : 'Sponsored by',
		'sponsor_logo'   => get_the_post_thumbnail( $post->ID, 'medium', [ 'class' => 'newspack-sponsor-logo' ] ),
	];
}
