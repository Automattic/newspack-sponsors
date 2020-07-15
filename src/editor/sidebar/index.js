/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { PanelBody, TextControl } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { withDispatch, withSelect } from '@wordpress/data';
import { PluginSidebar, PluginSidebarMoreMenuItem } from '@wordpress/edit-post';
import { Fragment } from '@wordpress/element';

const SidebarComponent = props => {
	const { meta, updateByline, updateUrl } = props;
	const { newspack_sponsor_url, newspack_sponsor_byline_prefix } = meta;

	return (
		<Fragment>
			<PluginSidebarMoreMenuItem target="newspack-sponsors">
				Newspack Sponsors
			</PluginSidebarMoreMenuItem>
			<PluginSidebar name="newspack-sponsors">
				<PanelBody title={ __( 'Sponsor Info', 'newspack-sponsors' ) }>
					<TextControl
						label={ __( 'Sponsor URL', 'newspack-sponsors' ) }
						placeholder="URL to link to for this sponsor"
						type="url"
						value={ newspack_sponsor_url }
						onChange={ value => updateUrl( value ) }
					/>
					<TextControl
						label={ __( 'Sponsor Byline Prefix', 'newspack-sponsors' ) }
						placeholder="Default: “Sponsored by”"
						type="url"
						value={ newspack_sponsor_byline_prefix }
						onChange={ value => updateByline( value ) }
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
	};
};

export const Sidebar = compose( [
	withSelect( mapStateToProps ),
	withDispatch( mapDispatchToProps ),
] )( SidebarComponent );
