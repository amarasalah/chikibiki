( function( $ ) {
	"use strict";
	var is_retina = ( 'devicePixelRatio' in window ) && ( parseInt( window.devicePixelRatio, 10 ) >= 2 ), $body = $( 'body' ),
		imageDataName = is_retina ? 'data-loftocean-retina-image' : 'data-loftocean-normal-image', isRTL = $body.hasClass( 'rtl' ),
		$backgroundImages = false, $responsiveImgs = false, $head = $( 'head' ), previousTop = 0, lazyLoadDelta = 100;

	// Replace images if needed
	$.fn.loftoceanImageLoading = function() {
		var $bgImages = $( this ).add( $( this ).find( '[data-loftocean-image=1]' ) ).filter( '[data-loftocean-image=1]' ),
			$imgs = $( this ).add( $( this ).find( 'img[data-loftocean-loading-image="on"]' ) ).filter( 'img[data-loftocean-loading-image="on"]' );
		if ( loftoceanImageLoad.lazyLoadEnabled ) {
			if ( $bgImages.length ) {
				$backgroundImages = $backgroundImages && $backgroundImages.length ? $backgroundImages.add( $bgImages ) : $bgImages;
			}
			if ( $imgs.length ) {
				$responsiveImgs = $responsiveImgs && $responsiveImgs.length ? $responsiveImgs.add( $imgs ) : $imgs;
			}
			$( window ).trigger( 'startLazyLoad.loftocean' );
		} else {
			if ( $bgImages.length ) {
				$bgImages.each( function() {
					var self = $( this );
					if ( self.attr( 'data-loftocean-image' ) ) {
						var name = self.prop( 'tagName' ), image = self.attr( imageDataName );
						$( new Image() ).on( 'load', function() {
							self.css( 'transition', 'none' );
							( 'IMG' == name ) ? self.attr( 'src', image ).removeAttr( 'style' ) : self.css( { 'background-image': 'url(' + image + ')', 'filter': '' } );
							self.css( 'transition', '' );
							self.removeAttr( 'data-loftocean-retina-image' ).removeAttr( 'data-loftocean-normal-image' ).removeAttr( 'data-loftocean-image' );
						} ).attr( 'src', image );
					}
				} );
			}

			if ( $imgs.length ) {
				$imgs.each( function() {
					if ( $( this ).attr( 'data-loftocean-loading-image' ) ) {
					   $( this ).data( 'srcset' ) ? $( this ).attr( 'srcset', $( this ).data( 'srcset' ) ).removeAttr( 'data-srcset' ) : '';
					   $( this ).data( 'loftocean-lazy-load-sizes' ) ? $( this ).attr( 'sizes', $( this ).data( 'loftocean-lazy-load-sizes' ) ).removeAttr( 'data-loftocean-lazy-load-sizes' ) : '';
   					   $( this ).data( 'src' ) ? $( this ).attr( 'src', $( this ).data( 'src' ) ).removeAttr( 'data-src' ) : '';
					   $( this ).removeAttr( 'data-loftocean-loading-image' ).css( { 'filter': '', 'opacity': '' } );
				   }
				} );
			}
		}
		return this;
	};

	if ( loftoceanImageLoad.lazyLoadEnabled ) {
		$( window ).on( 'startLazyLoad.loftocean', function( e) {
			var scrollBottom = $( window ).scrollTop() + $( window ).height(), $done = $();
			if ( $backgroundImages && $backgroundImages.length ) {
				$backgroundImages.each( function() {
					var self = $( this ), image = self.attr( imageDataName );
					if ( image && ( parseInt( self.offset().top - scrollBottom, 10 ) < lazyLoadDelta ) ) {
						$( new Image() ).on( 'load', function() {
							self.css( 'transition', 'none' );
							self.css( { 'background-image': 'url(' + image + ')', 'filter': '' } );
							self.css( 'transition', '' );
							self.removeAttr( 'data-loftocean-retina-image' ).removeAttr( 'data-loftocean-normal-image' ).removeAttr( 'data-loftocean-image' );
						} ).attr( 'src', image );
						$done = $done.add( self );
					}
				} );
				if ( $done.length ) {
					$backgroundImages = $backgroundImages.not( $done );
				}
			}
			if ( $responsiveImgs && $responsiveImgs.length ) {
				$done = $();
				$responsiveImgs.each( function() {
					if ( $( this ).attr( 'data-loftocean-loading-image' ) && ( parseInt( $( this ).offset().top - scrollBottom, 10 ) < lazyLoadDelta ) ) {
						$( this ).data( 'srcset' ) ? $( this ).attr( 'srcset', $( this ).data( 'srcset' ) ).removeAttr( 'data-srcset' ) : '';
						$( this ).data( 'loftocean-lazy-load-sizes' ) ? $( this ).attr( 'sizes', $( this ).data( 'loftocean-lazy-load-sizes' ) ).removeAttr( 'data-loftocean-lazy-load-sizes' ) : '';
						$( this ).data( 'src' ) ? $( this ).attr( 'src', $( this ).data( 'src' ) ).removeAttr( 'data-src' ) : '';
						$( this ).removeAttr( 'data-loftocean-loading-image' ).css( { 'filter': '', 'opacity': '' } );
						$done = $done.add( $( this ) );
					}
				} );
				if ( $done.length ) {
					$responsiveImgs = $responsiveImgs.not( $done );
				}
			}
		} )
		.on( 'scroll', function( e ) {
			var scrollTop = $( this ).scrollTop();
			previousTop < scrollTop ? $( this ).trigger( 'startLazyLoad.loftocean' ) : '';
			previousTop = scrollTop;
		} ).on( 'load', function( e ) {
			$( this ).trigger( 'startLazyLoad.loftocean' );
		} );
		$( 'body *' ).on( 'scroll', function() {
			$( window ).trigger( 'startLazyLoad.loftocean' );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function() {
		$( 'body' ).loftoceanImageLoading();
		$( 'body' ).on( 'click', '#page .loftocean-gallery-zoom', function( e ) {
			e.preventDefault();
			var $body 	= $( 'body' ),
				$wrap 	= $( this ).parent(),
				$slick 	= $wrap.children( '.image-gallery' ).first();
			if ( $body.hasClass( 'gallery-zoom' ) ) {
				$body.removeClass( 'gallery-zoom' );
				$wrap.removeClass( 'fullscreen' );
			} else {
				$body.addClass( 'gallery-zoom' );
				$wrap.addClass( 'fullscreen' );
			}
			$slick.slick( 'slickSetOption', 'speed', 500, true );
		} )
		.on( 'click', '.post-content-gallery.justified-gallery-initialized .gallery-item, .portfolio-gallery.gallery-justified .gallery-item', function( e ) {
			e.preventDefault();
			var gallery_id = $( this ).closest( '.justified-gallery-initialized' ).data( 'gallery-id' );
			if ( gallery_id && $( '.loftocean-popup-sliders .' + gallery_id ).length ) {
				var $body = $( 'body' ), index = $( this ).index(),
					$wrap = $( '.loftocean-popup-sliders .' + gallery_id ),
					$slick = $wrap.children( '.image-gallery' ).first();
				if ( ! $body.hasClass( 'gallery-zoom' ) ) {
					$body.addClass( 'gallery-zoom' );
					$wrap.addClass( 'fullscreen' ).removeClass( 'hide' );
					$slick.slick( 'slickGoTo', index ).slick( 'slickSetOption', 'speed', 500, true );
				}
			}
		} )
		.on( 'click', '.loftocean-popup-sliders .loftocean-popup-gallery-close', function( e ) {
			e.preventDefault();
			var $body = $( 'body' ), $wrap = $( this ).parent();
			if ( $body.hasClass( 'gallery-zoom' ) ) {
				$body.removeClass( 'gallery-zoom' );
				$wrap.removeClass( 'fullscreen' ).addClass( 'hide' );
			}
		} )
		.on( 'click', '#secondary .cs-form-wrap .has-dropdown', function( e ) {
			e.preventDefault();
			e.stopImmediatePropagation();
			var $dropdown = $( this ).siblings( '.csf-dropdown' );
			if ( $dropdown.length ) {
				if ( $dropdown.hasClass( 'is-open' ) ) {
					$dropdown.removeClass( 'is-open' );
					$dropdown.closest( '.cs-form-field' ).length ? $dropdown.closest( '.cs-form-field' ).removeClass( 'loftocean-highlighted' ) : '';
				} else {
					$( '.csf-dropdown' ).removeClass( 'is-open' );
					$( '.csf-dropdown' ).closest( '.cs-form-field' ).length ? $( '.csf-dropdown' ).closest( '.cs-form-field' ).removeClass( 'loftocean-highlighted' ) : '';
					$dropdown.addClass( 'is-open' );
					$dropdown.closest( '.cs-form-field' ).length ? $dropdown.closest( '.cs-form-field' ).addClass( 'loftocean-highlighted' ) : '';
				}
			}
		} )
		.on( 'click', '#secondary .cs-form-wrap .minus', function( e ) {
            e.preventDefault();

            if ( ( 'on' == $( this ).data( 'disabled' ) ) || $( this ).hasClass( 'disabled' ) ) return '';

            var $self = $( this ), $buttonWrapper = $self.parent(), label = $buttonWrapper.data( 'label' ),
                $outerInput = $self.parents( '.field-wrap' ).first().find( '.field-input-wrap input' ), hasLabel = loftoceanImageLoad[ 'reservation' ][ label ],
                $innerInput = $self.siblings( 'input' ).first(), currentValue = parseInt( $innerInput.val(), 10 ), minValue = $innerInput.data( 'min' ),
                regexString = hasLabel ? ( new RegExp( '\\d+ (' + loftoceanImageLoad[ 'reservation' ][ label ][ 'plural' ] + '|' + loftoceanImageLoad[ 'reservation' ][ label ]['single'] + ')', 'ig' ) ) : false;

            if ( ( ! $innerInput.length ) || ( ! $outerInput.length ) ) return '';

            var outerInputValue = $outerInput.val() || '';

			minValue = ( 'undefined' == typeof minValue ) || isNaN( minValue ) || ( minValue < 1 ) ? 0 : minValue;

            currentValue = isNaN( currentValue ) ? 1 : currentValue;
            currentValue = Math.max( ( currentValue < 1 ? 0 : ( currentValue - 1 ) ), minValue );
            $innerInput.val( currentValue );

            if ( $outerInput.hasClass( 'separated-guests' ) ) {
                outerInputValue = currentValue;
            } else { 
                var usePluralIfZero = hasLabel && ( 'undefined' != typeof loftoceanImageLoad[ 'reservation' ][ label ][ 'usePluralIfZero' ] ) && loftoceanImageLoad[ 'reservation' ][ label ][ 'usePluralIfZero' ];
                if ( hasLabel && regexString.test( outerInputValue ) ) {
                    if ( currentValue === 0 ) {
                        outerInputValue = outerInputValue.replace( regexString, ( currentValue + ' ' + loftoceanImageLoad[ 'reservation' ][ label ][ usePluralIfZero ? 'plural' : 'single' ] ) );
                    } else {
                    	outerInputValue = outerInputValue.replace( regexString, ( currentValue + ' ' + loftoceanImageLoad[ 'reservation' ][ label ][ ( currentValue < 2 ) ? 'single' : 'plural' ] ) );
                    }
                } else {
                    var extraValue = currentValue;
					if ( hasLabel ) {
	                    if ( currentValue === 0 ) {
	                        extraValue += ' ' + loftoceanImageLoad[ 'reservation' ][ label ][ usePluralIfZero ? 'plural' : 'single' ];
	                    } else {
	                        extraValue += ' ' + loftoceanImageLoad[ 'reservation' ][ label ][ ( currentValue < 2 ) ? 'single' : 'plural' ];
	                    }
	                    outerInputValue = outerInputValue ? ( ( 'adult' == label ) ? extraValue + ', ' + outerInputValue : outerInputValue + ', ' + extraValue ) : extraValue;

					} else {
						outerInputValue = extraValue;
					}
                }
            }
            $outerInput.val( outerInputValue );
            $self.siblings( '.plus' ).removeClass( 'disabled' ).data( 'disabled', '' ).removeAttr( 'disabled' );
            minValue === currentValue ? $self.data( 'disabled', 'on' ).addClass( 'disabled' ).attr( 'disabled', 'disabled' ) : '';

            $self.siblings( 'input' ).trigger( 'loftocean.number.changed', [ false, $self ] );
        } )
		.on( 'click', '#secondary .cs-form-wrap .plus', function( e ) {
            e.preventDefault();

            if ( ( 'on' == $( this ).data( 'disabled' ) ) || $( this ).hasClass( 'disabled' ) ) return '';

            var $self = $( this ), $buttonWrapper = $self.parent(), label = $buttonWrapper.data( 'label' ),
                $outerInput = $self.parents( '.field-wrap' ).first().find( '.field-input-wrap input' ), hasLabel = loftoceanImageLoad[ 'reservation' ][ label ],
                $innerInput = $self.siblings( 'input' ).first(), currentValue = parseInt( $innerInput.val(), 10 ), maxValue = $innerInput.data( 'max' ) || Number.MAX_SAFE_INTEGER,
                regexString = hasLabel ? ( new RegExp( '\\d+ (' + loftoceanImageLoad[ 'reservation' ][ label ][ 'plural' ] + '|' + loftoceanImageLoad[ 'reservation' ][ label ][ 'single' ] + ')', 'ig' ) ) : false;

            if ( ( ! $innerInput.length ) || ( ! $outerInput.length ) ) return '';

            var outerInputValue = $outerInput.val() || '';

            currentValue = isNaN( currentValue ) ? 1 : currentValue;
            currentValue = currentValue < 1 ? 1 : ( currentValue + 1 );
			if ( ( 'undefined' != typeof maxValue ) && ( ! isNaN( maxValue ) ) ) {
				currentValue = Math.min( maxValue, currentValue );
			}
            $innerInput.val( currentValue );
            if ( $outerInput.hasClass( 'separated-guests' ) ) {
                outerInputValue = currentValue;
            } else {
                if ( hasLabel && regexString.test( outerInputValue ) ) {
                    outerInputValue = outerInputValue.replace( regexString, currentValue + ' ' + loftoceanImageLoad[ 'reservation' ][ label ][ ( currentValue < 2 ) ? 'single' : 'plural' ] )
                } else {
                    var extraValue = currentValue;
					if ( hasLabel ) {
						extraValue += ' ' + loftoceanImageLoad[ 'reservation' ][ label ][ ( currentValue < 2 ) ? 'single' : 'plural' ];
	                    outerInputValue = outerInputValue ? ( ( 'adult' == label ) ? extraValue + ', ' + outerInputValue : outerInputValue + ', ' + extraValue ) : extraValue;
					} else {
						outerInputValue = extraValue;
					}
                }
            }
            $outerInput.val( outerInputValue );
            $self.siblings( '.minus' ).removeClass( 'disabled' ).removeAttr( 'disabled' ).data( 'disabled', '' );

            $self.siblings( 'input' ).trigger( 'loftocean.number.changed', [ true, $self ] );
        } )
		.on( 'click', function( e ) {
            var $target = $( e.target ), $openedDropdown = $( '.csf-dropdown.is-open' );
            if ( $openedDropdown.length && ( ! $target.is( '.cs-has-dropdown, .has-dropdown' ) ) && ( ! $target.parents( '.cs-has-dropdown, .has-dropdown' ).length ) ) {
                $openedDropdown.removeClass( 'is-open' );
                $openedDropdown.closest( '.cs-form-field' ).length ? $openedDropdown.closest( '.cs-form-field' ).removeClass( 'loftocean-highlighted' ) : '';
            }
		} );

		var $roomSearchForm = $( 'body.rooms-search-results #secondary .cs-form-wrap' );
		if ( $roomSearchForm.length ) {
			var dateFormat = $roomSearchForm.data( 'date-format' ) ? $roomSearchForm.data( 'date-format' ) : 'YYYY-MM-DD',
				displayDateFormat = $roomSearchForm.data( 'display-date-format' ) ? $roomSearchForm.data( 'display-date-format' ) : 'YYYY-MM-DD',
				$checkinDate = $roomSearchForm.find( '.field-input-wrap.checkin-date input.check-in-date' ), $checkinField = $checkinDate.closest( '.cs-form-field.cs-check-in' ),
				$checkoutDate = $roomSearchForm.find( '.field-input-wrap.checkout-date input' ), $checkoutField = $checkoutDate.closest( '.cs-form-field.cs-check-out' ),
				$checkinCheckoutGroup = $roomSearchForm.find( '.cs-form-field-group.date-group' ), groupCheckinCheckout = $checkinCheckoutGroup.length,
				$checkinSpan = groupCheckinCheckout ? $checkinDate.siblings( 'span.input' ) : '', $checkoutSpan = groupCheckinCheckout ? $checkoutDate.siblings( 'span.input' ) : '',
				$dateRangePicker = $roomSearchForm.find( '.date-range-picker' ), checkoutFieldOuterHeight = $checkoutField.length ? $checkoutField.outerHeight( true ) : 0;
			$checkinDate.val( moment( $checkinDate.data( 'value' ) ).format( displayDateFormat ) );
			$checkoutDate.val( moment( $checkoutDate.data( 'value' ) ).format( displayDateFormat ) );
			if ( groupCheckinCheckout ) {
				$checkinSpan.text( moment( $checkinDate.data( 'value' ) ).format( displayDateFormat ) );
				$checkoutSpan.text( moment( $checkoutDate.data( 'value' ) ).format( displayDateFormat ) );
			}
			$dateRangePicker.daterangepicker( {
				minDate: moment().format( dateFormat ),
				startDate: $checkinDate.data( 'value' ),
				endDate: $checkoutDate.data( 'value' ),
				locale: { format: dateFormat },
				autoApply: true
			} ).on( 'apply.daterangepicker', function( e, drp ) {
				var startDate = drp.startDate.format( dateFormat ), endDate = drp.endDate.format( dateFormat );
				$( this ).val( startDate + ' - ' + endDate );
				$checkinDate.val( drp.startDate.format( displayDateFormat ) ).data( 'value', startDate );
				$checkoutDate.val( drp.endDate.format( displayDateFormat ) ).data( 'value', endDate );

                if ( groupCheckinCheckout ) {
					$checkinSpan.text( drp.startDate.format( displayDateFormat ) );
					$checkoutSpan.text( drp.endDate.format( displayDateFormat ) ).css( 'opacity', '' );
					$checkinCheckoutGroup.removeClass( 'loftocean-highlighted' );
				} else {
					$checkinField.removeClass( 'loftocean-highlighted' );
	                $checkoutField.removeClass( 'loftocean-highlighted' );
	                drp.container.css( { 'transform': '', 'transition': '' } );
				}
            } ).on( 'show.daterangepicker', function( e, drp ) {
                drp.popupSingle = false;
                drp.container.removeClass( 'single' ).find( '.drp-calendar.right' ).show();
                if ( drp.container.outerWidth( true ) < 558 ) { 
                    drp.popupSingle = true;
                    drp.container.addClass( 'single' ).find( '.drp-calendar.right' ).hide();
                } else {
                    drp.popupSingle = false;
                    drp.container.removeClass( 'single' ).find( '.drp-calendar.right' ).show();
                }
                drp.renderCalendar( 'left' );

            	if ( groupCheckinCheckout ) {
            		$checkinCheckoutGroup.addClass( 'loftocean-highlighted' );
            	} else {
	                $checkinField.addClass( 'loftocean-highlighted' );
    	            $checkoutField.removeClass( 'loftocean-highlighted' );
    	        }
            } ).on( 'setStartDate.daterangepicker', function( e, drp ) {
                if ( groupCheckinCheckout ) { 
                	$checkinSpan.text( drp.startDate.format( displayDateFormat ) );
                	$checkoutSpan.css( 'opacity', 0 );
                } else { 
	                $checkinDate.val( drp.startDate.format( displayDateFormat ) );
	                $checkoutDate.val( '' );
	                $checkinField.removeClass( 'loftocean-highlighted' );
	                $checkoutField.addClass( 'loftocean-highlighted' );
                	drp.container.css( { 'transform': 'translateY(' + checkoutFieldOuterHeight + 'px)', 'transition': '0.15s' } );
                }
            } ).on( 'outsideClick.daterangepicker', function( e, drp ) {
                if ( drp.oldStartDate ) {
                    if ( groupCheckinCheckout ) { 
	                	$checkinSpan.text( drp.oldStartDate.format( displayDateFormat ) );
	                	$checkoutSpan.text( drp.oldEndDate.format( displayDateFormat ) ).css( 'opacity', '' );
	                } else {
	                    $checkinDate.val( drp.oldStartDate.format( displayDateFormat ) );
	                    $checkoutDate.val( drp.oldEndDate.format( displayDateFormat ) );
	                }
                }
                if ( groupCheckinCheckout ) { 
	            	$checkinCheckoutGroup.removeClass( 'loftocean-highlighted' );
	            } else {
	                $checkinField.removeClass( 'loftocean-highlighted' );
	                $checkoutField.removeClass( 'loftocean-highlighted' );
	                drp.container.css( { 'transform': '', 'transition': '' } );
	            }
            } );


			$roomSearchForm.find( '.checkin-date, .checkout-date, .cs-form-field-group.date-group .cs-form-field-group-inner' ).on( 'click', function( e ) {
				var dateRangePicker = $dateRangePicker.data( 'daterangepicker' );
				dateRangePicker.setStartDate( $checkinDate.data( 'value' ) );
				dateRangePicker.setEndDate( $checkoutDate.data( 'value' ) );
				dateRangePicker.show();
			} );
		}

		var $roomSearchForm = $( 'body.rooms-search-results #secondary .cs-reservation-form .cs-form-wrap' );
		if ( $roomSearchForm.length ) {
			$roomSearchForm.submit( function( e ) {
				var dates = [ 'checkin-date', 'checkout-date' ];
	            dates.forEach( function( name ) {
	                if ( $roomSearchForm.find( '.field-input-wrap.' + name + ' input' ).length ) {
	                    var hiddenInputName = name.split( '-' )[0],
	                        $originalItem = $roomSearchForm.find( '.field-input-wrap.' + name + ' input' ).last(),
	                        $itemInput = $roomSearchForm.children( 'input[type="hidden"][name="' + hiddenInputName + '"]' ).length
	                            ? $roomSearchForm.children( 'input[type="hidden"][name="' + hiddenInputName + '"]' )
	                                : $( '<input>', { 'type': 'hidden', 'name': hiddenInputName } ).appendTo( $roomSearchForm );
	                    $itemInput.val( $originalItem.data( 'value' ) );
	                }
	            } );
				var nonceName = 'roomSearchNonce', fieldValue = $roomSearchForm.serializeArray(),
	                $dataInput = $roomSearchForm.children( 'input[type="hidden"][name="' + nonceName + '"]' ).length
	                ? $roomSearchForm.children( 'input[type="hidden"][name="' + nonceName + '"]' )
	                    : $( '<input>', { 'type': 'hidden', 'name': nonceName } ).appendTo( $roomSearchForm );
	            $dataInput.val( btoa( JSON.stringify( fieldValue ) ) );
			} );
		}

		var $carousels = $( '.posts.layout-carousel .posts-wrapper' );
		if ( $carousels.length ) {
			var responsiveSettings = [
				{
					'breakpoint': 1200,
					'settings': {
						'slidesToShow': 3
					}
				},
				{
					'breakpoint': 800,
					'settings': {
						'slidesToShow': 2
					}
				},
				{
					'breakpoint': 480,
					'settings': {
						'slidesToShow': 1
					}
				}
			];
			$carousels.each( function() {
				var $wrap = $( this ).parent(), cols = $wrap.find( '.post' ).length;
				cols = Math.min( Math.max( parseInt( cols, 10 ), 1 ), 4 );
				$( this ).on( 'init', function( e ) {
					$.fn.loftoceanImageLoading ? $( this ).loftoceanImageLoading() : '';
				} ).slick( {
					'dots': false,
					'arrows': true,
					'infinite': true,
					'fade': false,
					'speed': 700,
					'autoplay': true,
					'autoplaySpeed': 5000,
					'pauseOnHover': true,
					'rtl': isRTL,
					'slidesToShow': cols,
					'slidesToScroll': 1,
					'swipeToSlide': true,
					'responsive': responsiveSettings.slice( -cols )
				} );
			} );
		}

		var $roomCarousel = $( '.room-top-section .cs-gallery.gallery-carousel.variable-width .cs-gallery-wrap' );
		if ( $roomCarousel.length ) {
			$roomCarousel.each( function() {
				$( this ).on( 'init', function( e ) {
                    $( this ).find( '.hide' ).removeClass( 'hide' );
				} ).slick( {
		            dots: true,
		            arrows: true,
		            rtl: isRTL,
		            slidesToShow: 1,
		            infinite: true,
		            speed: 500,
		            centerMode: true,
		            variableWidth: true
		        } );
			} );
		}

		var $roomTopGallery = $( '.room-top-section .cs-gallery.gallery-mosaic .cs-gallery-item > a' );
		if ( $roomTopGallery.length ) {
			new SimpleLightbox( '.room-top-section .cs-gallery.gallery-mosaic .cs-gallery-item > a', {} );
			$( '.room-top-section .cs-gallery-view-all' ).on( 'click', function( e ) {
				e.preventDefault();
				$roomTopGallery.eq( 0 ).find( 'img' ).trigger( 'click' );
			} );
		}



		$( window ).on( 'resize', function( e ) { 
			var $datePickers = $( 'input.date-range-picker' );
			if ( $datePickers.length ) {
				$datePickers.each( function() {
					var drp = $( this ).data( 'daterangepicker' );
					if ( ( 'undefined' != typeof drp ) && ( 'none' != drp.container.css( 'display' ) ) ) {
						$( this ).trigger( 'show.daterangepicker', drp );
					}
				} );
			}
		} );
	} );
} ) ( jQuery );
