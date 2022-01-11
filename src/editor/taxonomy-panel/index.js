/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { select } from '@wordpress/data';
import { Fragment } from '@wordpress/element';

/**
 * Filters the PostTaxonomies component to add explanations unique to Newspack Sponsor posts.
 *
 * @param {Function} PostTaxonomies The original PostTaxonomies component to filter.
 *                                         https://github.com/WordPress/gutenberg/tree/master/packages/editor/src/components/post-taxonomies
 * @return {Function} The filtered component.
 */
export const TaxonomyPanel = PostTaxonomies => {
	return props => {
		const postType = select( 'core/editor' ).getCurrentPostType();

		if ( 'newspack_spnsrs_cpt' !== postType && 'post' !== postType ) {
			return <PostTaxonomies { ...props } />;
		}

		const { slug } = props;
		const hierarchical = 'category' === slug;
		const label =
			'category' === slug
				? __( 'categories', 'newspack-sponsors' )
				: __( 'tags', 'newspack-sponsors' );
		const message = sprintf(
			// Translators: explanation for applying sponsors to a taxonomy term.
			__(
				'%1$s one or more post %2$s to associate this sponsor with those %3$s.',
				'newspack-sponsors'
			),
			// Translators: "Select" terms if the taxonomy is hierarchical, or "Add" terms if not.
			hierarchical ? __( 'Select ', 'newspack-sponsors' ) : __( 'Add ', 'newspack-sponsors' ),
			label,
			label
		);

		return (
			<Fragment>
				{ 'newspack_spnsrs_cpt' === postType && ( slug === 'category' || slug === 'post_tag' ) && (
					<p>
						<em>{ message }</em>
					</p>
				) }
				<PostTaxonomies
					{ ...props }
					// Remove "Add new sponsors" link since sponsor terms are shadow terms of sponsor posts.
					hasCreateAction={ 'newspack_spnsrs_tax' !== slug }
				/>
			</Fragment>
		);
	};
};
