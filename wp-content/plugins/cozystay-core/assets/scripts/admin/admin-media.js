/**
* For WP admin
*   1. Custom media uploader
*	2. Media clear after ajax updated
*/
( function( $ ) {
	"use strict";
	$( document ).ready( function() {
		var loftoceanMedia = {
			input: '',
			frames: {},
			videoFrame: function() {
				if ( ! this.frames.video ) {
					this.frames.video = wp.media( {
						id: 'loftocean-video-media-library',
						editing: true,
						library: { type : 'video' },
						multiple: false  // Set this to true to allow multiple files to be selected
					} )
					.on( 'select', function() {
						var video = loftoceanMedia.frames.video.state().get( 'selection' ).first().toJSON();

						loftoceanMedia.input.val( video.url );
						// reset input
						loftoceanMedia.input = '';
					} )
					.on( 'open', function() {
						var selection = loftoceanMedia.frames.video.state().get( 'selection' );
						selection.reset();
					} );
				}
				return this.frames.video;
			},
			imageFrame: function() {
				if ( ! this.frames.image ) {
					this.frames.image = wp.media( {
						id: 'loftocean-image-media-library',
						editing: true,
						library: {
							type : 'image'
						},
						multiple: false  // Set this to true to allow multiple files to be selected
					} )
					.on( 'select', function() {
						var image = loftoceanMedia.frames.image.state().get( 'selection' ).first().toJSON(),
							url = ( image.sizes && image.sizes.thumbnail ) ? image.sizes.thumbnail.url : image.url;

						loftoceanMedia.input.val( image.id ).trigger( 'changed.loftocean.media', [ url ] );
						// reset input
						loftoceanMedia.input = '';
					} )
					.on( 'open', function() {
						var selection = loftoceanMedia.frames.image.state().get( 'selection' ),
							image_id  = loftoceanMedia.input.val();
						selection.reset();
						if ( image_id && ( image_id !== '' ) ) {
							var attachment = wp.media.attachment( image_id );
							attachment.fetch();
							selection.add( attachment ? [ attachment ] : [] );
						}
					} );
				}
				return this.frames.image;
			},
			imageWithSizeFrame: function() {
				if ( ! this.frames.imageWithSize ) {
					this.frames.imageWithSize = new wp.media( {
						id: 'loftocean-image-with-size-media-library',
						editing: true,
						library: {
							type : 'image'
						},
						multiple: false,  // Set this to true to allow multiple files to be selected
						frame: 'post',
						state: 'image'
					} )
					.on( 'select', function() {
						var image = loftoceanMedia.frames.image.state().get( 'selection' ).first().toJSON(),
							url = ( image.sizes && image.sizes.thumbnail ) ? image.sizes.thumbnail.url : image.url;

						loftoceanMedia.input.val( image.id ).trigger( 'changed.loftocean.media', [ url ] );
						// reset input
						loftoceanMedia.input = '';
					} )
					.on( 'open', function() {
						var selection = loftoceanMedia.frames.imageWithSize.state().get( 'selection' ),
							image_id  = loftoceanMedia.input.val();
						selection.reset();
						if ( image_id && ( image_id !== '' ) ) {
							var attachment = wp.media.attachment( image_id );
							attachment.fetch();
							selection.add( attachment ? [ attachment ] : [] );
						}
					} );
				}
				return this.frames.imageWithSize;
			},
			galleryFrame: function() {
				if ( ! this.frames.gallery ) {
					this.frames.gallery = new wp.media( {
						id: 'loftocean-gallery-media-library',
						editing: true,
						library: {
							type : 'image'
						},
						multiple: true,  // Set this to true to allow multiple files to be selected
						frame: 'post',
						state: 'gallery-edit',
						title:	wp.media.view.l10n.editGalleryTitle,
						'media-sidebar': true
					} )
					.on( 'update', function( selection ) {
						var state = loftoceanMedia.frames.gallery.state(),
							images = loftoceanMedia.frames.gallery.states.get( 'gallery-edit' ).get( 'library' ),
							ids = images.pluck( 'id' ).join( ',' ),
							urls = images.map( function ( image ) {
								image = image.toJSON();
								return image.sizes && image.sizes.thumbnail ? image.sizes.thumbnail.url : image.url
							} );
						selection = selection || state.get( 'selection' );
						if ( ! selection ) {
							return ;
						}
						loftoceanMedia.input.val( ids ).trigger( 'changed.loftocean.media', [ urls ] );
					} )
					.on( 'open', function() {
						var controller = loftoceanMedia.frames.gallery.states.get( 'gallery-edit' ),
							library	= controller.get( 'library' ),
							ids  = loftoceanMedia.input.val();
						library.reset();
						if ( ids ) {
							ids = ids.split( ',' );
							ids.forEach( function( id ) {
								var attachment = wp.media.attachment( id );
								attachment.fetch();
								library.add( attachment ? [ attachment ] : [] );
							} );
						}
					} );
				}
				return this.frames.gallery;
			},
			open: function( $input, type ) {
				this.input = $input;
				if ( type ) {
					switch ( type ) {
						case 'gallery':
							this.galleryFrame().open();
							break;
						case 'video':
							this.videoFrame().open();
							break;
						default:
							this.imageFrame().open();
					}
				} else {
					this.imageFrame().open();
				}
			}
		};

		$( 'body' ).on( 'click', '.loftocean-upload-image', function( e ) {
			e.preventDefault();
			loftoceanMedia.open( $( this ).siblings( 'input[type=hidden]' ).first() );
		} )
		.on( 'click', '.loftocean-remove-image', function( e ) {
			e.preventDefault();
			var $upload = $( this ).siblings( '.loftocean-upload-image' ).first();
			$( this ).siblings( 'input[type=hidden]' ).first().val( 0 ).trigger( 'change' );
			$( this ).css( 'display', 'none' );
			$upload.text( $upload.attr( 'data-upload' ) );
		} )
		.on( 'changed.loftocean.media', 'input.loftocean-image-hidden', function( e, url ) {
			var styleWidth = $( this ).data( 'width' ) ? $( this ).data( 'width' ) : '50%';
			$( this ).siblings( '.loftocean-upload-image' ).html( $( '<img>', { 'src': url, 'style': 'max-width: ' + styleWidth } ) )
				.siblings( '.loftocean-remove-image' ).css( 'display', 'block' );
		} )
		.on( 'click', '.button.choose-media, .media-preview', function( e ) {
			e.preventDefault();
			var $target = $( this );
			var $input = $target.hasClass( 'media-preview' ) ? $target.siblings( 'input[type=hidden]' ) : $target.parent().siblings( 'input[type=hidden]' );
			loftoceanMedia.open( $input.first(), $target.parents( '.gallery-wrap' ).length ? 'gallery' : 'image' );
		} )
		.on( 'click', '.button.remove-media', function( e ) {
			e.preventDefault();
			var $this = $( this ), $wrap = $this.parent(), $preview = $wrap.siblings( '.media-preview' );
			$this.removeClass( 'not-selected' ).addClass( 'selected' );
			$wrap.siblings( 'input[type=hidden]' ).val( '' ).first().trigger( 'change' );
			$preview.html( '' ).append( $( '<div>', { 'class': 'placeholder', 'text': $preview.attr( 'data-text-preview' ) } ) );
		} )
		.on( 'changed.loftocean.media', 'input.loftocean-gallery-hidden', function( e, gallery ) {
			e.preventDefault();

			if ( gallery && $.isArray( gallery ) && gallery.length ) {
				var $input = $( this ),
					$gallery = gallery.map( function( url ) { return $( '<img>', { 'src': url, 'style': 'height: 100px; width: auto;' } ); } ),
					$preview = $input.siblings( '.media-preview' ),
					$buttons = $input.siblings( '.media-buttons' );

				$preview.html( '' ).append( $gallery );
				$buttons.children( '.button.remove-media' ).removeClass( 'selected' ).addClass( 'not-selected' );

				var $urls = $input.siblings( '.loftocean-gallery-urls' );
				if ( $urls.length ) {
					$urls.val( JSON.stringify( gallery ) );
				}
			}
		} )
		.on( 'click', '.loftocean-shortcode-generator-body .loftocean-media', function( e ) {
			e.preventDefault();
			var type = $( this ).hasClass( 'video' ) ? 'video' : 'image';
			loftoceanMedia.open( $( this ).parent().siblings( 'input.video-media-input' ).first(), type );
		} )
		.on( 'changed.loftocean.media', '.loftocean-shortcode-generator-body .video-media-input', function( e, url ) {
			$( this ).siblings( 'p' ).first().before( $( '<p>' ).append( $( '<img>', { 'src': url } ) ) );
		} )
		.on( 'changed.loftocean.media', 'input.loftocean-new-image-hidden', function( e, url ) {
			if ( url ) {
				var $input = $( this ),
					$image = $( '<img>', { 'src': url, 'style': 'height: 100px; width: auto;' } ),
					$preview = $input.siblings( '.media-preview' ),
					$buttons = $input.siblings( '.media-buttons' );

				$preview.html( '' ).append( $image );
				$buttons.children( '.button.remove-media' ).removeClass( 'selected' ).addClass( 'not-selected' );
			}
		} );

		if ( $( 'input[value=add-tag]' ).length && $( '.term-img-wrap' ).length ) {
			$( document ).ajaxComplete( function( event, request, options ) {
				if ( request && ( 4 === request.readyState ) && ( 200 === request.status ) && request.responseXML ) {
					var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );
					if ( ! res || res.errors || ! $( '.term-img-wrap' ).length || ! $( '.term-img-wrap' ).find( '.loftocean-remove-image' ).length ) {
						return;
					}
					$( '.term-img-wrap' ).find( '.loftocean-remove-image' ).trigger( 'click' );
				}
			} );
		}
	} );
} ) ( jQuery );
