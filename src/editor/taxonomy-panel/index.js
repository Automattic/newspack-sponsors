/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
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
		const { slug } = props;
		const message =
			'category' === slug
				? __(
						'Select one or more post categories to associate this sponsor with those categories.',
						'newspack-sponsors'
				  )
				: __(
						'Add one or more post tags to associate this sponsor with those tags.',
						'newspack-sponsors'
				  );

		return (
			<Fragment>
				<p>
					<em>{ message }</em>
				</p>
				<PostTaxonomies { ...props } />
			</Fragment>
		);
	};
};
