( function( $ ) {
	"use strict";
	var media = wp.media;

	// Wrap the render() function to append controls.
	media.view.Settings.Gallery = media.view.Settings.Gallery.extend( {
		render: function() {
			var $el = this.$el;

			media.view.Settings.prototype.render.apply( this, arguments );

			// Append the type template and update the settings.
			$el.append( media.template( 'loftocean-gallery-settings' ) );
			media.gallery.defaults['type'] 						= 'default';
			media.gallery.defaults['slider_ratio']				= 'ratio-3-2';
			media.gallery.defaults['justified_row_height'] 		= 120;
			media.gallery.defaults['justified_last_row_style'] 	= 'nojustify';
			media.gallery.defaults['justified_margin'] 			= 1;
			this.update( 'type' );
			this.update( 'slider_ratio' );
			this.update( 'justified_row_height' );
			this.update( 'justified_last_row_style' );
			this.update( 'justified_margin' );

			// Hide the Columns setting for all types except Default
			$el.find( 'select[name=type]' ).on( 'change', function() {
				var val 				= $( this ).val(),
					columnSetting 		= $el.find( 'select[name=columns]' ).closest( 'label.setting' ),
					linkSetting 		= $el.find( 'select.link-to' ).closest( 'label.setting' ),
					sizeSetting 		= $el.find( 'select[name=size]' ).closest( 'label.setting' ),
					sliderSettings 		= $el.find( '.gallery-type-slider' ),
					justifiedSettings  	= $el.find( '.gallery-type-justified' );

				sliderSettings.add( justifiedSettings ).hide();
				if ( 'default' === val ) {
					columnSetting.show();
					linkSetting.show();
					sizeSetting.show();
				} else {
					columnSetting.hide();
					linkSetting.hide();
					sizeSetting.hide();
					'slider' == val ? sliderSettings.show() : justifiedSettings.show();
				}
			} ).change();

			return this;
		},
		update: function( key ) {
			media.view.Settings.prototype.update.apply( this, arguments );

			var value 		= this.model.get( key ) || media.gallery.defaults[ key ],
				$setting 	= this.$( '[data-setting="' + key + '"]' ),
				$buttons, $value;

			// Bail if we didn't find a matching setting.
			if ( ! $setting.length ) {
				return;
			}

			if ( $setting.is( 'input[type="number"]' ) && ! $setting.is( ':focus' ) ) {
				$setting.val(value);
			}
		}
	} );

	media.view.Settings.AttachmentDisplay = media.view.Settings.AttachmentDisplay.extend( {
		render: function() {
			var attachment = this.options.attachment, $el = this.$el;
			media.view.settings.defaultProps['image_container'] = 'original';
			if ( attachment ) {
				_.extend( this.options, {
					sizes: 				attachment.get( 'sizes' ),
					type:  				attachment.get( 'type' ),
					image_container: 	attachment.get( 'image_container' ),
				} );
			}
			/**
			 * call 'render' directly on the parent class
			 */
			media.view.Settings.prototype.render.call( this );

			$el.find( '.setting.align' ).before( media.template( 'loftocean-display-settings' ) );
			this.update.apply( this, ['image_container'] );
			this.updateLinkTo();
			return this;
		}
	} );

	wp.media.editor.send.attachment = function( props, attachment ) {
		var caption = attachment.caption,
			options, html;

		// If captions are disabled, clear the caption.
		if ( ! wp.media.view.settings.captions ) {
			delete attachment.caption;
		}

		props = wp.media.string.props( props, attachment );

		options = {
			id:           attachment.id,
			post_content: attachment.description,
			post_excerpt: caption
		};

		if ( props.linkUrl ) {
			options.url = props.linkUrl;
		}

		if ( 'image' === attachment.type ) {
			html = wp.media.string.image( props );

			_.each( {
				align: 				'align',
				size: 				'image-size',
				alt: 				'image_alt',
				image_container: 	'image_container'

			}, function( option, prop ) {
				if ( props[prop] ) {
					options[ option ] = props[ prop ];
				}
			} );
		} else if ( 'video' === attachment.type ) {
			html = wp.media.string.video( props, attachment );
		} else if ( 'audio' === attachment.type ) {
			html = wp.media.string.audio( props, attachment );
		} else {
			html = wp.media.string.link( props );
			options.post_title = props.title;
		}

		return wp.media.post( 'send-attachment-to-editor', {
			nonce:      wp.media.view.settings.nonce.sendToEditor,
			attachment: options,
			html:       html,
			post_id:    wp.media.view.settings.post.id
		} );
	};
} ) ( jQuery );
