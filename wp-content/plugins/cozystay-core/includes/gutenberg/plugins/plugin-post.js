( function( editor, components, i18n, element, hooks, $ ) {
	"use strict";
	const __ = i18n.__;
	const compose = wp.compose.compose;
	const MediaUpload = editor.MediaUpload;
	const registerPlugin = wp.plugins.registerPlugin;
	const {
		dispatch,
		withDispatch,
		withSelect
	} = wp.data;
	const el = element.createElement;
	const {
		Fragment,
		Component
	} = element;
	const {
		Button,
		SelectControl,
		CheckboxControl,
		ToggleControl,
		TextControl,
		TextareaControl,
		PanelBody
	} = components;
	const {
		PluginSidebar,
		PluginSidebarMoreMenuItem
	} = wp.editPost;

	const Icon = el( 'svg', { width: '29px', height: '29px', viewBox: '0 0 100 100' },
		el( 'circle', { cx: "50", cy: '50', r: '40', stroke: 'black', strokeWidth: '3', fill: 'red' } )
	);
	var currentAuthor = '', currentFormat = '';

	class LoftOceanPlugin extends Component {
		constructor() {
			super( ...arguments );
			var plugin = this;

			this.sidebars = {
				format: () => {
					const format = this.props.format;
					return ( 'gallery' == format ) && el( PanelBody, {
							className: 'loftocean-format-sidebar',
							title: __( 'Format Settings' ),
							initialOpen: true
						},
						el( MediaUpload, {
							onSelect: ( media ) => {
								if ( media ) {
									var ids = _.pluck(  media, 'id' ), urls = [],
										shortcode = '[gallery ids="' + ids.join() + '"][/gallery]';
									ids.forEach( function( v ) {
										var image = wp.media.attachment( v ), url;
										image.fetch();
										url = image.get( 'sizes' ) && image.get( 'sizes' ).thumbnail ? image.get( 'sizes' ).thumbnail.url : image.get( 'url' );
										urls.push( url );
									} );
									ids = ids.join( ',' );
									urls = urls.join( ',' );
									this.onSaveMeta( {
										loftocean_post_format_gallery_ids: ids,
										loftocean_post_format_gallery: shortcode,
										loftocean_post_format_gallery_urls: urls,
									} );
								}
							},
							type: 'image',
							multiple: true,
							gallery: true,
							value: this.props.meta.loftocean_post_format_gallery_ids ? this.props.meta.loftocean_post_format_gallery_ids.split( ',' ) : '',
							render: function( obj ) {
								var ids = plugin.props.meta.loftocean_post_format_gallery_ids || false,
								 	shortcode = plugin.props.meta.loftocean_post_format_gallery || false,
								 	urls = plugin.props.meta.loftocean_post_format_gallery_urls || '',
								 	isLocal = false;
								if ( ids && ( ids.length > 0 ) ) {
								 	var str = "ids=([\"']?)" + ids + "\\1",
								 		regex = new RegExp( str, 'g' ),
								 	 	match = regex.exec( shortcode );
								 	ids = ids.split( ',' );
								 	urls = urls.split( ',' );
								 	isLocal = ( match ? true : false );
								}
								return el( Fragment, {},
									el( 'p', { className: 'loftocean-group-gallery' }, el( 'label', {}, __( 'Set the cover image gallery shown in the post list (instead of featured image)' ) ) ),
									el(
										'div', { className: 'loftocean-format-gallery-preview loftocean-group-gallery' }, isLocal && urls.map( function( url ) {
											if ( url ) {
												return el( 'div', { style: { 'width': '50px', 'display': 'inline-block', 'margin-right': '2px' } },
													el( 'img', {
														src: url
													} )
												);
											}
										} )
									),
									el( components.Button, {
											className: 'components-button button button-large is-default loftocean-group-gallery',
											onClick: obj.open
										}, __( 'Open Media Library' )
									),
									el ( 'p' ),
									el( TextareaControl, {
										label: __( 'Or type manually'),
										className: 'loftocean-gallery-input loftocean-group-gallery',
										value: plugin.props.meta.loftocean_post_format_gallery,
										onChange: ( value ) => {
											plugin.onSaveMeta( { loftocean_post_format_gallery: value } );
										}
									} )
								);
							}
						} ),
						// ( 'video' == format ) && el( MediaUpload, {
						// 	onSelect: ( media ) => {
						// 		if ( media ) {
						// 			var width = media.width, height = media.height, url = media.url,
						// 				shortcode = '<video width="' + width + '" height="' + height + '" src="' + url + '"></video>';
						// 			this.onSaveMeta( {
						// 				loftocean_post_format_video_id: media.id,
						// 				loftocean_post_format_video_url: url,
						// 				loftocean_post_format_video_type: media.mime,
						// 				loftocean_post_format_video: shortcode
						// 			} );
						// 		}
						// 	},
						// 	allowedTypes: 'video',
						// 	value: this.props.meta.loftocean_post_format_video_id,
						// 	render: function( obj ) {
						// 		var id = plugin.props.meta.loftocean_post_format_video_id || '',
						// 			media = id ? ( plugin.props.meta.loftocean_post_format_video_url || false ) : false,
						// 			type = id ? ( plugin.props.meta.loftocean_post_format_video_type || '' ) : '',
						// 			shortcode = plugin.props.meta.loftocean_post_format_video || '',
						// 			isLocal = false;
				
						// 		if ( id && media ) {
						// 			var str = 'src="' + media + '"',
						// 				regex = new RegExp( str, 'g' ),
						// 				match = regex.exec( shortcode );
						// 			isLocal = ( null !== match );
						// 		}
						// 		return el( Fragment, {},
						// 			el( 'p', { className: 'loftocean-group-video' }, el( 'label', {}, __( 'For Video Format:' ) ) ),
						// 			el( 'div', { className: 'loftocean-format-video-preview loftocean-group-video' }, isLocal && el( 'video', {
						// 					style: { width: '100%', height: 'auto' },
						// 					mute: 'mute',
						// 					controls: 'controls',
						// 					src: media
						// 				} )
						// 			),
						// 			el( components.Button, {
						// 					className: 'components-button button button-large is-default loftocean-group-video',
						// 					onClick: obj.open
						// 				}, __( 'Open Media Library' )
						// 			),
						// 			el( TextareaControl, {
						// 				label: __( 'Or type manually' ),
						// 				className: 'loftocean-video-input loftocean-group-video',
						// 				value: plugin.props.meta.loftocean_post_format_video,
						// 				onChange: ( value ) => {
						// 					plugin.onSaveMeta( { loftocean_post_format_video: value } );
						// 				}
						// 			} ),
						// 			el( 'span', {
						// 					className: 'loftocean-group-video',
						// 					style: { 'font-size': '11px' }
						// 				},
						// 				el( 'b', {}, __( 'Note: ' ) ),
						// 				el( Fragment, {}, 'support ' ),
						// 				el( 'b', {}, __( 'Youtube/Vimeo Embed <iframe>' ) ),
						// 				el( Fragment, {}, ' or' ),
						// 				el( 'b', {}, __( ' HTML5 <video>' ) ),
						// 				el( Fragment, {}, ' only.' )
						// 			)
						// 		);
						// 	}
						// } ),
						// ( 'audio' == format ) && el( MediaUpload, {
						// 	onSelect: ( media ) => {
						// 		if ( media ) {
						// 			var url = media.url, shortcode = '[audio src="' + url + '"][/audio]';
						// 			this.onSaveMeta( {
						// 				loftocean_post_format_audio_id: media.id,
						// 				loftocean_post_format_audio_url: url,
						// 				loftocean_post_format_audio_type: media.mime,
						// 				loftocean_post_format_audio: shortcode
						// 			} );
						// 		}
						// 	},
						// 	allowedTypes: 'audio',
						// 	value: this.props.meta.loftocean_post_format_audio_id,
						// 	render: function( obj ) {
						// 		var id = plugin.props.meta.loftocean_post_format_audio_id || '',
						// 			media = id ? ( plugin.props.meta.loftocean_post_format_audio_url || false ) : false,
						// 			type = id ? ( plugin.props.meta.loftocean_post_format_audio_type || '' ) : '',
						// 			shortcode = plugin.props.meta.loftocean_post_format_audio || '',
						// 			isLocal = false;
				
						// 		if ( id && media ) {
						// 			var str = 'src="' + media + '"',
						// 				regex = new RegExp( str, 'g' ),
						// 				match = regex.exec( shortcode );
						// 			isLocal = ( null !== match );
						// 		}
						// 		return el( Fragment, {},
						// 			el( 'p', { className: 'loftocean-group-audio' }, el( 'label', {}, __( 'For Audio Format:' ) ) ),
						// 			el( 'div', { className: 'loftocean-format-audio-preview loftocean-group-audio' }, isLocal && el( 'audio', {
						// 						style: { width: '100%' },
						// 						controls: 'controls'
						// 					}, el( 'source', {
						// 						'src': media,
						// 						'type': type
						// 					} )
						// 				)
						// 			),
						// 			el( 'p' ), el( 'p' ),
						// 			el( components.Button, {
						// 					className: 'components-button button button-large is-default loftocean-group-audio',
						// 					onClick: obj.open
						// 				}, __( 'Open Media Library' )
						// 			),
						// 			plugin.props.meta.loftocean_post_format_audio && el( components.Button, {
						// 				className: 'loftocean-audio-clear',
						// 				onClick: () => {
						// 					plugin.onSaveMeta( {
						// 						loftocean_post_format_audio_id: 0,
						// 						loftocean_post_format_audio_url: '',
						// 						loftocean_post_format_audio_type: '',
						// 						loftocean_post_format_audio: ''
						// 					} );
						// 				}
						// 			}, __( 'clear Audio' ) ),
						// 			el( 'p' ), el( 'p' ),
						// 			el( TextareaControl, {
						// 				className: 'loftocean-audio-input loftocean-group-audio',
						// 				value: plugin.props.meta.loftocean_post_format_audio,
						// 				disabled: 'disabled',
						// 				readonly: 'readonly',
						// 				// onChange: ( value ) => {
						// 				// 	plugin.onSaveMeta( { loftocean_post_format_audio: value } );
						// 				// }
						// 			} )
						// 		);
						// 	}
						// } )
					)
				},
				counter: () => {
					return el( PanelBody, {
							title: __( 'Like Settings' ),
							initialOpen: false
						},
						el(
							TextControl, {
								type: 'number',
								label: __( 'Like Counts' ),
								value: this.props.meta[ 'loftocean-like-count' ],
								onChange: ( value ) => {
									this.onSaveMeta( { 'loftocean-like-count': Math.max( Math.abs( value ), 1 ) } );
								}
							},
						)
					);
				}
			};
			this.sidebars = wp.hooks.applyFilters( 'loftocean.post.metas.filter', this.sidebars, this );
		}
		onSaveMeta( newValue ) {
			this.props.onSave( newValue );
		}

		render() {
			let sidebars = Object.assign( {}, this.sidebars );
			if ( -1 === [ 'gallery', 'video', 'audio' ].indexOf( this.props.format ) ) {
				delete( sidebars['format'] );
			}

			return el( Fragment, {},
				el( PluginSidebarMoreMenuItem, { target: 'loftocean-theme-settings' }, __( 'Theme Settings' ) ),
				el( PluginSidebar, { name: 'loftocean-theme-settings', title: __( 'Theme Settings' ) },
					$.map( sidebars, function( sidebar, index ) {
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

		return wp.hooks.applyFilters( 'loftocean.post.withSelectReturn.filter', {
			meta: getEditedPostAttribute( 'meta' ),
			format: getEditedPostAttribute( 'format' ),
			currentAuthor: getEditedPostAttribute( 'author' )
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
} ) (
	window.wp.blockEditor || window.wp.editor,
	window.wp.components,
	window.wp.i18n,
	window.wp.element,
	window.wp.hooks,
	jQuery
);
