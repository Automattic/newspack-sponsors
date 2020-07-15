/**
 * WordPress dependencies
 */
import { registerPlugin } from '@wordpress/plugins';

/**
 * Internal dependencies
 */
import { Sidebar } from './sidebar';

registerPlugin( 'newspack-sponsors-editor', {
	icon: 'money',
	render: Sidebar,
} );
