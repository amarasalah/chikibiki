( function( $ ) {
	"use strict";
	$( document ).ready( function() {
		var $media = false, $format = $( '#formatdiv' ), $title = false,
			$doc = $( document ), $formatTmpl = $( '#loftocean-tmpl-format-meta-box' );
		function loftoceanSyncFormatMetaBox( format ) {
			var formats = [ 'gallery' ];
			$media.children( 'div.format').css('display', 'none' );
			( format && ( formats.indexOf( format ) !== -1 ) ) ?
				$media.children( 'div.format.' + format ).add( $title ).css( 'display', '' ) :
				$title.css( 'display', 'none' );

			if ( ! $media.find( '.audio-code' ).val() ) {
				$media.find( '.clear-audio' ).hide();
			}
		}
		$( 'body' ).on( 'change', 'select.loftocean-author-widget-choose-by', function( e ) {
			var val = $( this ).val(),
				$lists = $( this ).parent().siblings( '.author-list-choices' );
			$lists.css( 'display', 'none' );
			switch ( val ) {
				case 'name':
					$lists.filter( '.author-list-by-name' ).css( 'display', '' );
					break;
				case 'role':
					$lists.filter( '.author-list-by-role' ).css( 'display', '' );
					break;
			}
		} )
		.on( 'click', '.loftocean-post-counter-wrap a', function( e ) {
			e.preventDefault();
			var $a = $( this ), $input = $a.siblings( 'input' ).first(),
				$not_edit = $a.add( $a.siblings( 'a' ) ).not( '.edit' );
			if ( $a.hasClass( 'edit' ) ) {
				$a.css( 'display', 'none' ).siblings( 'a' ).css( 'display', '' );
				$input.removeAttr( 'readonly' ).attr( 'previous-value', $input.val() );
			} else {
				$not_edit.css( 'display', 'none' );
				$a.siblings( 'a.edit' ).css( 'display', '' );
				if ( $a.hasClass( 'cancel' ) ) {
					$input.val( $input.attr( 'previous-value' ) );
				}
				$input.attr( 'readonly', 'readonly' );
			}
		} )
		.on( 'change', '#post_author_override', function( e ) {
			var uid = $( this ).val(),
				$coAuthors = $( '#loftocean-single-post-co-authors' );
			$coAuthors.children().css( 'display', '' ).filter( '[value=' + uid + ']' ).css( 'display', 'none' ).removeAttr( 'selected' );
		} );

		if ( $( '#loftocean-tmpl-format-meta-box' ).length && $( '#formatdiv' ).length ) {
			$format.find( '.inside' ).append( $formatTmpl.html() );
			$media = $format.find( '#loftocean-format-media' );
			$title = $media.children( 'h4' );
			loftoceanSyncFormatMetaBox( ( $formatTmpl.attr( 'data-format' ) || '' ) );
			$( 'body' ).on( 'change', 'input[name=post_format]', function( e ) {
				loftoceanSyncFormatMetaBox( $( this ).val() );
			} )
			.on( 'click', '#loftocean-format-media .format-media', function( e ) {
				e.preventDefault();
				if ( $( this ).hasClass( 'gallery' ) ) {
					loftoceanFormatMedia.gallery.open();
				} else if ( $( this ).hasClass( 'video' ) ) {
					loftoceanFormatMedia.video.open();
				} else if ( $( this ).hasClass( 'audio' ) ) {
					loftoceanFormatMedia.audio.open();
				}
			} )
			.on( 'change', '#loftocean-format-media textarea', function( e ) {
				if ( $( this ).siblings( 'input[type=hidden]' ).length ) {
					var $hidden = $( this ).siblings( 'input[type=hidden]' );
					$hidden.val( '' );
				}
			} )
			.on( 'click', '#loftocean-format-media .clear-audio', function( e ) {
				e.preventDefault();
				var $audioWrap = $( this ).closest( '.format.audio' );
				$( this ).hide();
				$audioWrap.find( '.audio-code' ).val( '' );
				$audioWrap.find( '.audio-id' ).val( '' );
			} );
		}

		wp.loftocean = {};

		wp.loftoceanMediaTools = function() {
			wp.loftocean.roomThumbnail = new wp.media( {
				editing: true,
				library: {
					type : 'image'
				},
				multiple: false
			} );
			wp.loftocean.roomGallery = new wp.media( {
				frame: 'post',
				state: 'gallery-edit',
				title:	wp.media.view.l10n.editGalleryTitle,
				'media-sidebar': false,
				editing: false,
			} );
			wp.loftocean.gallery = new wp.media( {
				frame: 'post',
				state: 'gallery-edit',
				title:	wp.media.view.l10n.editGalleryTitle,
				'media-sidebar': false,
				editing: false,
			} );
			wp.loftocean.audio = new wp.media( {
				library: {
					type : 'audio'
				},
				title: wp.media.view.l10n.addMedia,
				multiple: false
			} );
			wp.loftocean.video = new wp.media( {
				library: {
					type : 'video'
				},
				title: wp.media.view.l10n.addMedia,
				multiple: false
			} );

			wp.loftocean.gallery.on( 'update', function( selection ) {
				var state = wp.loftocean.gallery.state();
				selection = selection || state.get( 'selection' );

				if ( ! selection ) {
					return ;
				}

				$media.find( '.gallery-id' ).val( wp.loftocean.gallery.states.get( 'gallery-edit' ).get( 'library' ).pluck( 'id' ).join( ',' ) );
				$media.find( '.gallery-code' ).val( wp.media.gallery.shortcode( selection ).string() );
			})
			.on( 'open', function() {
				var controller = wp.loftocean.gallery.states.get( 'gallery-edit' ),
					library	= controller.get( 'library' ),
					ids  = $media.find( '.gallery-id' ).val();
				if ( ids ) {
					ids = ids.split( ',' );
					ids.forEach( function( id ) {
						var attachment = wp.media.attachment( id );
						attachment.fetch();
						library.add( attachment ? [ attachment ] : [] );
					} );
				}
			} );

			wp.loftocean.video.on( 'select', function( selection ) {
				var state = wp.loftocean.video.state(), attrs = {};
				selection = selection || state.get( 'selection' ).first();
				attrs= selection.toJSON();
				$media.find( '.video-id' ).val( attrs.id );
				$media.find( '.video-code' ).val( '<video width="' + attrs.width + '" height="' + attrs.height + '" src="' + attrs.url + '"></video>' );
			} )
			.on( 'open', function() {
				var selection = wp.loftocean.video.state().get( 'selection' ),
					id = $media.find( '.video-id' ).val();
				if ( id ) {
					var attachment = wp.media.attachment( id );
					attachment.fetch();
					selection.add( attachment ? [ attachment ] : [] );
				}
			} );

			wp.loftocean.audio.on( 'select', function( selection ) {
				var state = wp.loftocean.audio.state(),
					audio = {},
					attrs = {};
				selection = selection || state.get( 'selection' ).first();
				attrs= selection.toJSON();
				audio['src'] = attrs.url;
				$media.find( '.clear-audio' ).show();
				$media.find( '.audio-id' ).val( attrs.id );
				$media.find( '.audio-code' ).val( wp.media.audio.shortcode( audio ).string() );
			} )
			.on( 'open', function() {
				var selection = wp.loftocean.audio.state().get( 'selection' ),
					id = $media.find( '.audio-id' ).val();
				if ( id ) {
					var attachment = wp.media.attachment( id );
					attachment.fetch();
					selection.add( attachment ? [ attachment ] : [] );
				}
			} );

			wp.loftocean.roomThumbnail.on( 'select', function( selection ) {
				var state = wp.loftocean.roomThumbnail.state(), attrs = {};
				selection = selection || state.get( 'selection' ).first();
				attrs = selection.toJSON();
				$media.find( '[name=loftocean_room_list_thumbnail_id]' ).val( attrs.id );
				$media.find( '.list-thumbnail-has-image-wrapper .set-list-thumbnail' ).html( '<img width="' + Math.min( attrs.width, 254 ) + '" src="' + attrs.url + '">' );
				$media.find( '.list-thumbnail-has-image-wrapper' ).css( 'display', '' )
					.end().find( '.list-thumbnail-no-image-wrapper' ).css( 'display', 'none' );

			} ).on( 'open', function() {
				var selection = wp.loftocean.roomThumbnail.state().get( 'selection' ),
					id = $media.find( '[name=loftocean_room_list_thumbnail_id]' ).val();
				if ( id ) {
					var attachment = wp.media.attachment( id );
					attachment.fetch();
					selection.add( attachment ? [ attachment ] : [] );
				}
			} );
			wp.loftocean.roomGallery.on( 'update', function( selection ) {
				var state = wp.loftocean.roomGallery.state();
				selection = selection || state.get( 'selection' );

				if ( ! selection ) {
					return ;
				}

				var gallery = wp.loftocean.roomGallery.states.get( 'gallery-edit' ).get( 'library' ),
					$ul = $( '<ul>' );
				gallery.pluck( 'url' ).forEach( function( url ) {
					$ul.append( $( '<li>' ).append( $( '<a>', { 'href': '#', 'class': 'set-gallery' } ).append( $( '<img>', { 'src': url } ) ) ) );
				} );
				$media.find( '[name=loftocean_room_gallery_ids]' ).val( gallery.pluck( 'id' ).join( ',' ) );
				$media.find( '.gallery-no-image-wrapper' ).css( 'display', 'none' )
					.end().find( '.gallery-preview-list' ).html( '' ).append( $ul.children() )
					.end().find( '.gallery-has-image-wrapper' ).css( 'display', '' );

			} ).on( 'open', function() {
				var controller = wp.loftocean.roomGallery.states.get( 'gallery-edit' ),
					library	= controller.get( 'library' ),
					ids  = $media.find( '[name=loftocean_room_gallery_ids]' ).val();
				if ( ids ) {
					ids = ids.split( ',' );
					ids.forEach( function( id ) {
						var attachment = wp.media.attachment( id );
						attachment.fetch();
						library.add( attachment ? [ attachment ] : [] );
					} );
				} else {
					library.reset();
				}
			} );

			return wp.loftocean;
		};

		var loftoceanFormatMedia = new wp.loftoceanMediaTools(),
			roomMedia = new wp.loftoceanMediaTools();

		if ( $( '.page-video-meta-box' ).length ) {
			$media = $( '.page-video-meta-box' );
			$( 'body' ).on( 'click', '.loftocean-page-upload-video', function( e ) {
				e.preventDefault();
				loftoceanFormatMedia.video.open();
			} )
			.on( 'change', '.page-video-meta-box textarea', function( e ) {
				if ( $( this ).siblings( 'input[type=hidden]' ).length ) {
					var $hidden = $( this ).siblings( 'input[type=hidden]' );
					$hidden.val( '' );
				}
			} );
		}

		var $listThumbnailBox = $( '#loftocean-room-list-thumbnail.postbox ' );
		if ( $listThumbnailBox.length ) {
			var $galleryBox = $( '#loftocean-room-list-gallery.postbox' );

			$listThumbnailBox.on( 'click', '.set-list-thumbnail', function( e ) {
				e.preventDefault();
				$media = $listThumbnailBox;
				roomMedia.roomThumbnail.open();
			} ).on( 'click', '.remove-list-thumbnail', function( e ) {
				e.preventDefault();
				$listThumbnailBox.find( '[name=loftocean_room_list_thumbnail_id]' ).val( '-1' );
				$listThumbnailBox.find( '.list-thumbnail-has-image-wrapper' ).css( 'display', 'none' )
					.end().find( '.list-thumbnail-no-image-wrapper' ).css( 'display', '' );
			} );

			$galleryBox.on( 'click', '.set-gallery', function( e ) {
				e.preventDefault();
				$media = $galleryBox;
				roomMedia.roomGallery.open();
			} ).on( 'click', '.remove-gallery', function( e ) {
				e.preventDefault();
				$galleryBox.find( '[name=loftocean_room_gallery_ids]' ).val( '' );
				$galleryBox.find( '.gallery-has-image-wrapper' ).css( 'display', 'none' )
					.end().find( '.gallery-no-image-wrapper' ).css( 'display', '' )
					.end().find( '.gallery-preview-list' ).html( '' );
			} );
		}

		var $roomSettings = $( '.panel-wrap.room-data-settings' );
		if ( $roomSettings.length ) {
			var $select2 = $roomSettings.find( '.select2-field select' ),
				$pageTemplate = $( 'select[name=page_template]' ),
				$nightlyWeekendPrice = $roomSettings.find( '.weekend-prices.nightly-weekend-price' ),
				$perPersonWeekendPrice = $roomSettings.find( '.weekend-prices.per-person-weekend-price' ),
				$nightlyVariablePrices = $roomSettings.find( '.variable-price-by-room.loftocean-variable-price' ),
				$perPersonVariablePrices = $roomSettings.find( '.variable-price-by-guest.loftocean-variable-price' ),
				variablePriceItemTmpl = wp.template( 'loftocean-room-variable-prices' );

			$select2.length ? $select2.select2( { 'width': '95%' } ).removeClass( 'hidden' ) : '';
			$pageTemplate.on( 'change', function( e ) {
				var $layoutTab = $roomSettings.find( '.loftocean-room.room-options.tab-layout' );
				if ( $layoutTab.length ) {
					if ( 'default' == $( this ).val() ) {
						$layoutTab.css( 'display', '' );
					} else {
						$layoutTab.css( 'display', 'none' );
						$layoutTab.hasClass( 'active' ) ? $layoutTab.siblings( '.tab-general' ).find( 'a' ).trigger( 'click' ) : '';
					}
				}
			} );
			$roomSettings.on( 'change', '.option-title-with-toggle.checkbox-field [type=checkbox]', function( e ) {
				var $content = $( this ).parent().siblings( '.option-content-after-toggle' );
				if ( $content.length ) {
					this.checked ? $content.slideDown() : $content.slideUp();
					if ( 'room_price_by_people' == $( this ).attr( 'id' ) ) {
						if ( this.checked ) {
							$nightlyWeekendPrice.addClass( 'hide' );
							$perPersonWeekendPrice.removeClass( 'hide' );
							$nightlyVariablePrices.addClass( 'hide' );
							$perPersonVariablePrices.removeClass( 'hide' );
						} else {
							$nightlyWeekendPrice.removeClass( 'hide' );
							$perPersonWeekendPrice.addClass( 'hide' );
							$nightlyVariablePrices.removeClass( 'hide' );
							$perPersonVariablePrices.addClass( 'hide' );
						}
					}
				}
			} ).on( 'click', '.loftocean-room-settings-tabs > li > a', function( e ) {
				e.preventDefault();
				var $li = $( this ).parent(), tabID = $( this ).attr( 'href' );
				if ( ! $li.hasClass( 'active' ) ) {
					$li.siblings().removeClass( 'active' ).end().addClass( 'active' );
					$roomSettings.children( '.panel.loftocean-room-setting-panel' ).addClass( 'hidden' );
					$roomSettings.children( tabID + '-panel, ' + tabID ).removeClass( 'hidden' );

					if ( '#tab-availability' == tabID ) {
						$doc.trigger( 'render.loftocean.room.availability' );
					}
					if ( '#tab-booking-calendar' == tabID ) {
						$doc.trigger( 'render.loftocean.bookingCalendar' );
					}
				}
			} ).on( 'click', '.loftocean-room-rules-panel .loftocean-room-rules-item .item-title', function( e ) {
				$( this ).toggleClass( 'toggled-on' );
			} ).on( 'change', '[name="loftocean_room_enable_variable_guest_group"]', function( e ) {
				this.checked 
					? $nightlyVariablePrices.add( $perPersonVariablePrices ).addClass( 'has-guest-group' )
						: $nightlyVariablePrices.add( $perPersonVariablePrices ).removeClass( 'has-guest-group' );
			} ).on( 'change', '[name="loftocean_room_enable_variable_weekend_prices"]', function( e ) {
				this.checked 
					? $nightlyVariablePrices.add( $perPersonVariablePrices ).addClass( 'has-weekend-price' )
						: $nightlyVariablePrices.add( $perPersonVariablePrices ).removeClass( 'has-weekend-price' );
			} ).on( 'click', '.form-field.checkbox-field .trigger-label', function( e ) {
				var $label = $( this ).siblings( 'label' );
				$label.length ? $label.trigger( 'click' ) : '';
			} ).on( 'click', '.loftocean-variable-price .variable-actions .button', function( e ) {
				e.preventDefault();
				e.stopPropagation();
				var $item = $( this ).closest( 'tr' );
				$item.siblings( 'tr' ).length ? $item.remove() : $item.find( 'input' ).val( '' );

			} ).on( 'click', '.loftocean-variable-price tfoot .button', function( e ) {
				e.preventDefault();
				e.stopPropagation();

				var $btn = $( this ), currentIndex = $btn.data( 'current-index' ), type =$btn.data( 'current-type' ), $newItem;
				currentIndex = isNaN( currentIndex ) ? 0 : currentIndex;
				type = [ 'regular', 'per-person' ].includes( type ) ? type : 'regular';

				$newItem = variablePriceItemTmpl( { 'index': currentIndex, 'type': type } );
                if ( $newItem ) {
                    $btn.data( 'current-index', ++ currentIndex );
                    $btn.closest( 'table' ).find( 'tbody' ).append( $newItem );
                }
			} );
		}
	} );
} ) ( jQuery );
