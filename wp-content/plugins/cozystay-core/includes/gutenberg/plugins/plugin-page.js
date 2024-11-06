( function( editor, components, i18n, element, hooks, $ ) {
	"use strict";

	const __ = i18n.__;
	const el = element.createElement;
	const Component = element.Component;
	const compose = wp.compose.compose;
	const registerPlugin = wp.plugins.registerPlugin;

	const {
		Fragment
	} = element;


	const {
		ToggleControl,
		SelectControl,
		PanelBody
	} = components;

	const {
		dispatch,
		withSelect,
		withDispatch
	} = wp.data;

	const {
		PluginSidebar,
		PluginSidebarMoreMenuItem
	} = wp.editPost;

	const Icon = el( 'svg', { width: '29px', height: '29px', viewBox: '0 0 100 100' },
		el( 'circle', { cx: "50", cy: '50', r: '40', stroke: 'black', strokeWidth: '3', fill: 'red' } )
	);

	class LoftOceanPlugin extends Component {
		constructor() {
			super( ...arguments );
			this.sidebars = hooks.applyFilters( 'loftocean.page.metas.filter', {}, this );
		}
		onSaveMeta( newValue ) {
			this.props.onSave( newValue );
		}
		render() {
			return el( Fragment, {},
				el( PluginSidebarMoreMenuItem, { target: 'loftocean-theme-settings' }, __( 'Theme Settings' ) ),
				el( PluginSidebar, { name: 'loftocean-theme-settings', title: __( 'Theme Settings' ) },
					$.map( this.sidebars, function( sidebar, index ) {
						return sidebar.call( this );
					} ),
					el( 'input', {
						type: 'hidden',
						name: 'loftocean_gutenberg_enabled',
						value: 'on'
					} )
				)
			);
		}
	}

	// Fetch the post meta.
	const applyWithSelect = withSelect( ( select, { forceIsSaving } ) => {
		const { getEditedPostAttribute } = select( 'core/editor' );

		return wp.hooks.applyFilters( 'loftocean.page.withSelectReturn.filter', {
			meta: getEditedPostAttribute( 'meta' )
		}, select );
	} );

	const applyWithDispatch = withDispatch( function( dispatch ) {
		return {
			onSave: function( newValue ) {
				dispatch( 'core/editor' ).editPost( { meta: { ...newValue } } );
			}
		};
	} );

	const render = compose( [
		applyWithSelect, applyWithDispatch
	] )( LoftOceanPlugin );

	registerPlugin( 'loftocean-theme-settings', {
		icon: 'menu',
		render
	} );
} )(
	window.wp.editor,
	window.wp.components,
	window.wp.i18n,
	window.wp.element,
	window.wp.hooks,
	jQuery
);
