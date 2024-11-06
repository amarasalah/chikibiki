( function( $ ) {
	if ( 'undefined' != typeof elementor && 'undefined' !== elementorCommon ) {
		elementor.on( 'preview:loaded', function() {
			var $modal;
			var $buttons = $( '#tmpl-elementor-add-section' ), text = $buttons.text().replace(
    				'<div class="elementor-add-section-drag-title',
    				'<div class="elementor-add-section-area-button loftocean-library-modal-btn" title="CozyStay Templates">CozyStay Templates</div><div class="elementor-add-section-drag-title'
    			);

			$buttons.text( text );
			// Call modal.
			$( elementor.$previewContents[0].body ).on( 'click', '.loftocean-library-modal-btn', function() {
				if ( $modal ) {
					$modal.show();
					return;
				}
				$modal = elementorCommon.dialogsManager.createWidget( 'lightbox', {
					id           : 'loftocean-library-modal',
					headerMessage: $( '#tmpl-elementor-loftocean-library-modal-header' ).html(),
					message      : $( '#tmpl-elementor-loftocean-library-modal' ).html(),
					className    : 'elementor-templates-modal',
					closeButton  : true,
					draggable    : false,
					hide         : { onOutsideClick: true, onEscKeyPress : true },
					position     : { my: 'center', at: 'center' }
				} );
				$modal.show();
				loadTemplateLibrary();
			} );

			// Load items.
			function loadTemplateLibrary() {
				showLoader();
				$.ajax( {
					url     : loftoceanElementorLibrary.demoAjaxUrl,
					method  : 'GET',
					dataType: 'json',
					success : function( response ) {
						if ( response && response.status && ( 'success' == response.status ) && response.defaultTab ) {
							var itemTemplate = wp.template( 'elementor-loftocean-library-modal-item' ),
								itemTabTitleTemplate = wp.template( 'elementor-loftocean-library-tab-title-item' ),
								itemTabContentTemplate = wp.template( 'elementor-loftocean-library-tab-content' ),
								itemOrderTemplate = wp.template( 'elementor-loftocean-library-modal-order' );

							$( itemTabTitleTemplate( response.tabs ) ).appendTo( $( '#loftocean-library-modal #elementor-template-library-header-menu' ) );
							$( itemTabContentTemplate( response.tabs ) ).appendTo( $( '#loftocean-library-modal #elementor-template-library-templates' ) );

							_.each( response.library, function( data, tab ) {
								data.elements = data.elements.reverse();
								$( itemTemplate( data ) ).appendTo( $( '#loftocean-library-modal .elementor-template-library-tab-content.elementor-template-library-' + tab + ' .elementor-template-library-templates-container' ) );
								$( itemOrderTemplate( data ) ).appendTo( $( '#loftocean-library-modal .elementor-template-library-tab-content.elementor-template-library-' + tab + ' .elementor-template-library-filter-toolbar-remote' ) );
							} );

							initElemenetsPosition( $( '#loftocean-library-modal #elementor-template-library-templates' ).children().first() );
							$( window ).resize( function() {
								var $tabs = $( '#loftocean-library-modal #elementor-template-library-templates' ).children(), $activeTab = $tabs.filter( '.show' ),
									$list = $activeTab.length ? $activeTab : $tabs.first();
								initElemenetsPosition( $list.find( '.elementor-template-library-templates-container' ).children() );
							} );
							$( '#loftocean-library-modal #elementor-template-library-templates img' ).on( 'load', function() {
								var $container = $( this ).closest( '.elementor-template-library-templates-container' ), cols = 3,
									$list = $container.children(), $current = $( this ).closest( '.elementor-template-library-template' ),
									length = $list.length, currentIndex = $list.index( $current ), start = Math.floor( currentIndex / 3 ) * 3;
								if ( $container.parent().hasClass( 'active-tab' ) ) {
									for ( var i = Math.max( start, 3 ); i < length; i++ ) {
										$list.eq( i ).css( 'margin-top', '' );
										setElementPosition( $list.eq( i ), $list.eq( i - cols ) );
									}
								}
							} );

							importTemplate();
							activeDefaultTab( response.defaultTab );
							hideLoader();
						} else {
							$( '<div>', { 'class': 'cs-notice cs-error', 'text': 'The library can\'t be loaded from the server.' } )
                                .prependTo( $( '#loftocean-library-modal #elementor-template-library-templates' ) );
							hideLoader();
						}
					},
					error: function() {
						$( '<div>', { 'class': 'cs-notice cs-error', 'text': 'The library can\'t be loaded from the server.' } )
                            .prependTo( $( '#loftocean-library-modal #elementor-template-library-templates' ) );
						hideLoader();
					}
				} );
			}

			// Change current elementor's position
			function setElementPosition( $elementCurrent, $elementAbove ) {
				if ( 'none' != $elementCurrent.css( 'display' ) ) {
					var marginTop = $elementAbove.offset().top + $elementAbove.outerHeight() + 30;
					$elementCurrent.css( 'margin-top', marginTop - $elementCurrent.offset().top );
				}
			}

			// Init elements
			function initElemenetsPosition( $list ) {
				$list = $list.css( 'margin-top', '' ).not( '.invisible-item' );
				var cols = 3, length = $list.length;
				$list.css( 'margin-top', '' );
				for ( var i = cols; i < length; i ++ ) {
					setElementPosition( $list.eq( i ), $list.eq( i - cols ) );
				}
			}

			// Loader
			function showLoader() {
				$( '#loftocean-library-modal #elementor-template-library-templates' ).hide();
				$( '#loftocean-library-modal .elementor-loader-wrapper' ).show();
			}

			function hideLoader() {
				$( '#loftocean-library-modal #elementor-template-library-templates' ).show();
				$( '#loftocean-library-modal .elementor-loader-wrapper' ).hide();
			}

			function activateUpdateButton() {
				$( '#elementor-panel-saver-button-publish' ).removeClass( 'elementor-disabled' );
				$( '#elementor-panel-saver-button-save-options' ).removeClass( 'elementor-disabled' );
			}

			// Import.
			function importTemplate() {
				$( '#loftocean-library-modal' ).on( 'click', '.elementor-template-library-template-insert', function() {
					showLoader();
                    var $message = $( '#loftocean-library-modal #elementor-template-library-templates .cs-notice' );
                    if ( $message.length ) {
                        $message.remove();
                    }
					return elementorCommon.ajax.addRequest( 'get_template_data', {
						data: {
							source: 'loftocean',
							edit_mode: true,
							display : true,
							template_id: $( this ).data( 'id' ),
							with_page_settings: false
						},
						success: function success( data ) {
							if ( data && data.content ) {
								elementor.getPreviewView().addChildModel( data.content );
								$modal.hide();
								setTimeout( function() {
									hideLoader();
								}, 2000 );
								activateUpdateButton();
							} else {
								$( '<div>', { 'class': 'cs-notice cs-error', 'text': 'The element can\'t be loaded from the server.' } )
                                    .prependTo( $( '#loftocean-library-modal #elementor-template-library-templates' ) );
								hideLoader();
							}
						},
						error: function() {
							$( '<div>', { 'class': 'cs-notice cs-error', 'text': 'The element can\'t be loaded from the server.' } )
                                .prependTo( $( '#loftocean-library-modal #elementor-template-library-templates' ) );
							hideLoader();
						}
					} );
				} ).on( 'click', '.elementor-templates-modal__header__close', function() {
					$modal.hide();
					hideLoader();
				} ).on( 'click', '.elementor-template-library-menu-item', function() {
					if ( ! $( this ).hasClass( 'elementor-active' ) ) {
						var $elem = $( '#loftocean-library-modal .elementor-template-library-tab-content' ).filter( '.' + $( this ).data( 'tab' ) );
						$( this ).siblings().removeClass( 'elementor-active' );
						$( this ).addClass( 'elementor-active' );
						$( '#loftocean-library-modal .elementor-template-library-tab-content' ).hide().removeClass( 'active-tab' )
							.filter( '.' + $( this ).data( 'tab' ) ).show().addClass( 'active-tab' );
						initElemenetsPosition( $elem.find( '.elementor-template-library-templates-container' ).children() );
					}
				} );

				// Search.
				$( '#loftocean-library-modal .elementor-template-library-filter-text-wrapper input' ).on( 'keyup', function() {
					var val = $( this ).val().toLowerCase(),
						$container = $( this ).parents( '.elementor-template-library-tab-content' ).first();
					$container.find( '.elementor-template-library-template-block' ).each( function() {
						var $this = $( this ), title = $this.data( 'title' ).toLowerCase(), slug = $this.data( 'slug' ).toLowerCase();
						( title.indexOf( val ) > -1 || slug.indexOf( val ) > -1 ) ? $this.show().removeClass( 'invisible-item' ) : $this.hide().addClass( 'invisible-item' );
					} );
					initElemenetsPosition( $container.find( '.elementor-template-library-template-block' ).not( '.invisible-item' ) );
				} );

				// Filters.
				$( '#loftocean-library-modal .elementor-template-library-filter-select' ).on( 'change', function() {
					var val = $( this ).val(),
						$container = $( this ).parents( '.elementor-template-library-tab-content' ).first();
					$container.find( '.elementor-template-library-template-block' ).each( function() {
						var $this = $( this ), tag = $this.data( 'tag' ).toLowerCase();
						( 'all' === val || tag.indexOf( val ) > -1 ) ? $this.show().removeClass( 'invisible-item' ) : $this.hide().addClass( 'invisible-item' );
					} );
					initElemenetsPosition( $container.find( '.elementor-template-library-template-block' ).not( '.invisible-item' ) );
				} );
			}
			function activeDefaultTab( tabID ) {
				var $tabs = $( '#loftocean-library-modal .elementor-template-library-menu-item' );
				if ( $tabs.length ) {
					var $defaultTab = $tabs.filter( '[data-tab=elementor-template-library-' + tabID + ']' );
					$defaultTab.length ? $defaultTab.trigger( 'click' ) : $tabs.first().trigger( 'click' );
				}
			}
		} );
	}
} ) ( jQuery );
