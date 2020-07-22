/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { withDispatch, withSelect } from '@wordpress/data';
import { PluginSidebar, PluginSidebarMoreMenuItem } from '@wordpress/edit-post';
import { Fragment } from '@wordpress/element';

/**
 * Internal dependencies
 */
import './style.scss';

const SidebarComponent = props => {
	const { meta, updateByline, updateUrl, updateIsDirect } = props;
	const {
		newspack_sponsor_url,
		newspack_sponsor_byline_prefix,
		newspack_sponsor_only_direct,
	} = meta;

	return (
		<Fragment>
			<PluginSidebarMoreMenuItem target="newspack-sponsors">
				Newspack Sponsors
			</PluginSidebarMoreMenuItem>
			<PluginSidebar name="newspack-sponsors">
				<PanelBody
					className="newspack-sponsors"
					title={ __( 'Sponsor Info', 'newspack-sponsors' ) }
				>
					<TextControl
						className="newspack-sponsors__text-control"
						label={ __( 'Sponsor URL', 'newspack-sponsors' ) }
						placeholder="URL to link to for this sponsor"
						type="url"
						value={ newspack_sponsor_url }
						onChange={ value => updateUrl( value ) }
					/>
					<TextControl
						className="newspack-sponsors__text-control"
						label={ __( 'Sponsor Byline Prefix', 'newspack-sponsors' ) }
						placeholder="Default: “Sponsored by”"
						type="url"
						value={ newspack_sponsor_byline_prefix }
						onChange={ value => updateByline( value ) }
					/>
					<ToggleControl
						className="newspack-sponsors__toggle-control"
						label={ __( 'Show on posts only if a direct sponsor?', 'newspack-newsletters' ) }
						help={ __(
							'If this option is enabled, this sponsor will only be shown on single posts if assigned as a direct sponsor. It will still appear on category and tag archive pages, if applicable.'
						) }
						checked={ newspack_sponsor_only_direct }
						onChange={ value => updateIsDirect( value ) }
					/>
				</PanelBody>
			</PluginSidebar>
		</Fragment>
	);
};

const mapStateToProps = select => {
	const { getEditedPostAttribute } = select( 'core/editor' );

	return {
		meta: getEditedPostAttribute( 'meta' ),
	};
};

const mapDispatchToProps = dispatch => {
	const { editPost } = dispatch( 'core/editor' );

	return {
		updateUrl: value => editPost( { meta: { newspack_sponsor_url: value } } ),
		updateByline: value => editPost( { meta: { newspack_sponsor_byline_prefix: value } } ),
		updateIsDirect: value => editPost( { meta: { newspack_sponsor_only_direct: value } } ),
	};
};

export const Sidebar = compose( [
	withSelect( mapStateToProps ),
	withDispatch( mapDispatchToProps ),
] )( SidebarComponent );
