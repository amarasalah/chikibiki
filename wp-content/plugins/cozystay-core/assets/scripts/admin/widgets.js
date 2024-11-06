( function ( $ ) {
	"use strict";

	window.wp = window.wp || {};

	wp.loftOceanWidgets = ( function() {
		var editorParams		= false,
			widgetDependency 	= loftoceanWidgetJSON.dependency || {},
			widgetItems 		= loftoceanWidgetJSON.JSON || {},
			themeWidgets 		= loftoceanWidgetJSON.widgets || [],
			widgetsDone			= [];
		/**
		* Actual widget-added event handler function
		* 	1. Check if any tinymce editor exists
		* 	2. Check if any color picker exists
		*/
		function widgetAdded( e, widgetContainer ) {
			var widgetForm, idBase, widgetId, animatedCheckDelay = 50, renderWhenAnimationDone;
			// Note: '.form' appears in the customizer, whereas 'form' on the widgets admin screen.
			widgetForm = widgetContainer.find( '> .widget-inside > .form, > .widget-inside > form' );

			idBase = widgetForm.find( '> .id_base' ).val(); 
			if ( -1 === themeWidgets.indexOf( idBase ) ) {
				return;
			}
			// Prevent initializing already-added widgets.
			widgetId = widgetForm.find( '.widget-id' ).val();
			if ( -1 !== widgetsDone.indexOf( widgetId ) ) {
				return;
			}
			/*
			 * Render the widget once the widget parent's container finishes animating,
			 * as the widget-added event fires with a slideDown of the container.
			 * This ensures that the textarea is visible and enable the colorpicker, tinymce editor...
			 */
			renderWhenAnimationDone = function() {
				if ( ! widgetContainer.hasClass( 'open' ) ) {
					setTimeout( renderWhenAnimationDone, animatedCheckDelay );
				} else {
					widgetsDone.push( widgetId );
					widgetFromInit( widgetContainer, idBase, false );
				}
			};
			renderWhenAnimationDone();
		}
		/*
		* For widgets.php only
		*/
		function widgetUpdated( e, widgetContainer ) {
			if ( 'widgets' !== window.pagenow ) {
				return ;
			}

			var widgetForm, idBase;
			// Note: '.form' appears in the customizer, whereas 'form' on the widgets admin screen.
			widgetForm = widgetContainer.find( '> .widget-inside > .form, > .widget-inside > form' );

			idBase = widgetForm.find( '> .id_base' ).val();
			if ( -1 === themeWidgets.indexOf( idBase ) ) {
				return;
			}
			widgetFromInit( widgetContainer, idBase, true );
		}
		/**
		* Test if any special elements need to initialize, which including tinymce editor, colorpicker
		* @param jQuery object widget form
		*/
		function widgetFromInit( container, idBase, updated ) {
			var $colors = container.find( 'input.loftocean-color-picker' ),
				$editor = container.find( '.editor-widget-control.item-type-editor' ),
				$image 	= container.find( '.item-wrapper.item-type-image' ),
				$number = container.find( 'input[type=number]' ),
				$sortableSelection = container.find( '.sortable-selection-value' ),
				$slider = container.find( '.slider-widget-control' );
			if ( $number.length ) {
				initNumber( $number );
			}
			if ( $image.length ) {
				initImage( $image );
			}
			if ( $colors.length ) {
				initColorPicker( $colors );
			}
			if ( $editor.length && ! updated ) {
				$editor.each( function() {
					initTinyMCE( $(this).find( '.editor-textarea-wrap textarea' ).data( 'id' ), $( this ) );
				});
			}
			$slider.length ? initSlider( $slider ) : '';
			$sortableSelection.length ? initSortableSelection( $sortableSelection ) : '';

			if ( ( idBase in widgetDependency ) && ( idBase in widgetItems ) ) {
				var deps = widgetDependency[ idBase ], its = widgetItems[ idBase ];
				container.find( '.loftocean-widget-item' ).on( 'change', function() {
					var itemID = $( this ).data( 'loftocean-widget-item-id' );
					if ( itemID in deps ) {
						widgetFormChanged( container, deps[ itemID ], its );
					}
				} );
				$.each( deps, function( pid, items ) {
					widgetFormChanged( container, items, its );
				} );
			}
		}
		/**
		* Initialize input[type=number] element, add change event handler
		* @param jQuery object
		*/
		function initNumber( $number ) {
			$number.on( 'change', function( e ) {
				var val = parseInt( $( this).val(), 10 ),
					min = $( this ).attr( 'min' ),
					max = $( this ).attr( 'max' );
				if ( min && ( val < parseInt( min, 10 ) ) ) {
					$( this ).val( min );
				}
				if ( max && ( val > parseInt( max, 10 ) ) ) {
					$( this ).val( max );
				}
			} );
		}
		/**
		* Initialize element with type image, add events handler
		* @param jQuery object
		*/
		function initImage( $image ) {
			$image.each( function() {
				var $container = $( this );
				$container.on( 'click', '.button.widget-choose-media, .media-widget-preview', function( e ) {
					e.preventDefault();
					var $target = $( this ),
						mediaType = $target.hasClass( 'media' ) ? 'media' : 'image';

					if ( ( 'media' === mediaType ) && $target.hasClass( 'media-widget-preview' ) && $target.children( 'video' ).length ) {
						var video = $target.children( 'video' ).get(0);
						video.paused ? video.play() : video.pause();
					} else {
						var $input = $target.hasClass( 'media-widget-preview' ) ? $target.siblings( 'input[type=hidden]' ) : $target.parent().siblings( 'input[type=hidden]' );
						loftoceanMedia.open( $input.first(), mediaType );
					}
				} )
				.on( 'click', '.button.widget-remove-media', function( e ) {
					e.preventDefault();
					var $this = $( this ), $wrap = $this.parent(), $preview = $wrap.siblings( '.media-widget-preview' );
					$this.removeClass( 'not-selected' ).addClass( 'selected' );
					$wrap.siblings( 'input[type=hidden]' ).val( '' ).first().trigger( 'change' );
					$preview.html( '' ).append( $( '<div>', { 'class': 'placeholder', 'text': $preview.attr( 'data-text-preview' ) } ) );
				} )
				.on( 'changed.loftocean.media', 'input.loftocean-widget-item[type=hidden]', function( e, media ) {
					e.preventDefault();
					if ( media && ( -1 !== [ 'image', 'video' ].indexOf( media.type ) ) ) {
						var type = media.type,
							targetObject = ! media.sizes ? media : ( media.sizes.medium ? media.sizes.medium : ( media.sizes.thumbnail ? media.sizes.thumbnail : media ) ),
							$media = 'image' == type ? $( '<img>', { 'class': 'attachment-thumb', 'src': targetObject.url, 'width': targetObject.width || 0, 'height': targetObject.height || 0 } ) : $( '<video>', { 'class': 'attachment-thumb', 'src': targetObject.url } ),
							$input = $( this ),
							$preview = $input.siblings( '.media-widget-preview' ),
							$buttons = $input.siblings( '.media-widget-buttons' ),
							$type = $input.siblings( 'input[type=hidden]' );

						$preview.html( '' ).append( $media );
						$buttons.children( '.button.widget-remove-media' ).removeClass( 'selected' ).addClass( 'not-selected' );
						$type.length ? $type.val( type ) : '';
						$input.val( media.id ).trigger( 'change' );
					}
				} );
			} );
		}
		/**
		* Initialize element with type slider, add events handler
		* @param jQuery object
		*/
		function initSlider( $slider ) {
			$slider.each( function() {
				var $elem = $( this ).find( '.loader-ui-slider' ),
					$input = $( this ).find( 'input' ).first();
				$elem.slider( {
					'range': 	'min',
					'min': 		$elem.data( 'min' ),
					'max': 		$elem.data( 'max' ),
					'value': 	$elem.data( 'value' ),
					'step': 	$elem.data( 'step' ),
					'slide': 	function( event, ui ) {
						$input.val( ui.value ).trigger( 'change' );
					}
				} );
			} );
		}
		/**
		* Initialize element with type sortable selection, add events handler
		*/
		function initSortableSelection( $selection ) {
			$selection.each( function() {
				var $hiddenValue = $( this ), $list = $hiddenValue.siblings( '.sortable-selection-list' ),
					$container = $hiddenValue.siblings( '.sortable' ), value = $hiddenValue.val();
				if ( value ) {
					value = value.split( ',' );
					if ( Array.isArray( value ) ) {
						$.each( value, function( index, val ) {
							var $item = $list.find( '[value=' + val + ']' );
							if ( $item.length ) {
								$item.hide();
								$container.append( $( '<li>', { 'data-id': val } )
									.append( $( '<span>', { 'class': 'label' } ).append( $item.html() ) )
									.append( $( '<a>', { 'text': 'x', 'class': 'action-remove', 'href': '#' } ) )
								);
							}
						} );
					}
				}
				$container.on( 'value.update', function() {
					var newValue = [];
					$( this ).children().each( function() {
						$( this ).attr( 'data-id' ) ? newValue.push( $( this ).attr( 'data-id' ) ) : '';
					} );
					$hiddenValue.val( newValue.join( ',' ) ).trigger( 'change' );
				} )
				.sortable( {
					stop: function( event, ui ) {
						$( this ).trigger( 'value.update' );
					}
				} );
				$container.on( 'click', 'a', function( e ) {
					e.preventDefault();
					var $item = $( this ).parent();
					$list.find( '[value=' + $item.attr( 'data-id' ) + ']' ).show();
					$item.remove();
					$container.sortable( 'refresh' );
					$container.trigger( 'value.update' );
				} );
				$list.children().on( 'click', function( e ) {
					e.preventDefault();
					var $item = $( this ), val = $item.attr( 'value' ), currentHiddenValue = $hiddenValue.val();
					$container.append( $( '<li>', { 'data-id': val } )
						.append( $( '<span>', { 'class': 'label' } ).append( $item.html() ) )
						.append( $( '<a>', { 'text': 'x', 'class': 'action-remove', 'href': '#' } ) )
					);
					$item.hide();
					$container.sortable( 'refresh' );
					$hiddenValue.val( currentHiddenValue ? ( currentHiddenValue + ',' + val ) : val );
				} );
			} );
		}
		/**
		* Helper function is Object
		*/
		function isObject( obj ) {
			var type = typeof obj;
			return type === 'function' || type === 'object' && !!obj;
		}
		/**
		* Dependency process determination
		*/
		function processDependency( deps, container ) {
			if ( isObject( deps ) ) {
				var relation = 'AND', isComplex = false;
				if ( deps.relation ) {
					relation = deps.relation ? deps.relation.toUpperCase() : 'AND';
				}

				isComplex = deps['is_complex'] || false;
				return ( 'AND' == relation ) ? checkDependencyAND( deps, isComplex, container ) : checkDependencyOR( deps, isComplex, container );
			}
			return true;
		}
		/**
		* Helper function to check dependency AND
		*/
		function checkDependencyAND( deps, isComplex, container ) {
			if ( ! isObject( deps ) ) return true;
			var passed = true;
			$.each( deps, function( id, dep ) {
				if ( isObject( dep ) ) {
					var result = isComplex ? processDependency( dep, container ) : dependencyItemCheck( id, dep, container );
					if ( ! result ) {
						passed = false;
						return false;
					}
				}
			} );
			return passed;
		}
		/**
		* Helper function to check dependency OR
		*/
		function checkDependencyOR( deps, isComplex, container ) {
			if ( ! isObject( deps ) ) return true;
			var passed = false;
			$.each( deps, function( id, dep ) {
				if ( isObject( dep ) ) {
					var result = isComplex ? processDependency( dep, container ) : dependencyItemCheck( id, dep, container );
					if ( result ) {
						passed = true;
						return false;
					}
				}
			} );
			return passed;
		}
		/**
		* Dependency item check
		*/
		function dependencyItemCheck( pid, attr, container ) {
			if ( ! pid || ! attr || ! attr['value'] ) { // If not provide the test value list, return false
				return false;
			}
			var operator = attr.operator || 'in', $pitem = container.find( '[data-loftocean-widget-item-id=' + pid + ']' ),
				value = ( -1 !== [ 'radio', 'checkbox' ].indexOf( $pitem.first().attr( 'type' ) ) ) ? ( $pitem.filter( ':checked' ).length ? $pitem.filter( ':checked' ).val() : '' ) : $pitem.val() ;
			return ( operator === 'in' ) ? ( attr.value.indexOf( value ) !== -1 ) : ( attr.value.indexOf( value ) === -1 );
		}
		/**
		* Determine to show the widget elements if they have dependency set
		* @param jQuery object form
		* @param array list of element id related to current element changed
		* @param object items list with item id and its dependency settings
		*/
		function widgetFormChanged( container, dependency, items ) {
			$.each( dependency, function( i, v ) {
				var $item = container.find( '[data-loftocean-widget-item-id=' + v + ']' );
				if ( $item.length && items[ v ] ) {
					$item = $item.closest( '.item-wrapper' );
					processDependency( items[ v ], container ) ? $item.show() : $item.hide();
				}
			} );
		}
		/**
		* Initialize tinymce eidtor using the featured area custom content editor params
		* @param string attribute id of element <textarea>
		* @param jQuery object
		*/
		function initTinyMCE( id, $container ) {
			var control = $container, changeDebounceDelay = 1000, textarea, triggerChangeIfDirty, needsTextareaChangeTrigger = false, previousValue;
			textarea = control.find('.editor-textarea-wrap textarea');
			previousValue = textarea.val();
			triggerChangeIfDirty = function() {
				var updateWidgetBuffer = 300; // See wp.customize.Widgets.WidgetControl._setupUpdateUI() which uses 250ms for updateWidgetDebounced.
				if ( control.editor.isDirty() ) {
					if ( wp.customize && wp.customize.state ) {
						wp.customize.state( 'processing' ).set( wp.customize.state( 'processing' ).get() + 1 );
						_.delay(function(){
							wp.customize.state( 'processing' ).set(wp.customize.state( 'processing').get() - 1 );
						}, updateWidgetBuffer );
					}
					textarea.val( wp.editor.getContent( id ) );
				}
				// Trigger change on textarea when it has changed so the widget can enter a dirty state.
				if ( needsTextareaChangeTrigger && ( previousValue !== textarea.val() ) ) {
					textarea.trigger( 'change' );
					needsTextareaChangeTrigger = false;
					previousValue = textarea.val();
				}
			};
			function buildEditor() {
				var editor, onInit, mceSettings, qtSettings, tmpl,
					tmplEditorID 	= 'loftocean-widget-editor-id',
					in_mceInit 		= tinyMCEPreInit && tinyMCEPreInit.mceInit && ( tmplEditorID in tinyMCEPreInit.mceInit ) && window.tinymce,
					in_qtInit 		= tinyMCEPreInit && tinyMCEPreInit.qtInit && ( tmplEditorID in tinyMCEPreInit.qtInit ) && quicktags;
				if ( ! in_mceInit || ! in_qtInit ) {
					return;
				}

				// Abort building if the textarea is gone, likely due to the widget having been deleted entirely.
				if ( ! textarea.length ) {
					return;
				}

				// Destroy any existing editor so that it can be re-initialized after a widget-updated event.
				if ( tinymce.get( id ) ) {
					var mceInstance 	= window.tinymce.get( id ),
						qtInstance 		= window.QTags.getInstance( id ),
						$editor_wrap 	= $container.find( '#wp-' + id + '-wrap' );

					textarea.val( wp.editor.getContent( id ) );
					if ( mceInstance ) {
						mceInstance.remove();
					}
					if ( qtInstance ) {
						qtInstance.remove();
					}
					if ( $editor_wrap.length ) {
						$editor_wrap.remove();
					}
				}

				if ( ! editorParams ) {
					editorParams = $.extend( {}, { 'mce': tinyMCEPreInit.mceInit[tmplEditorID], 'qt': tinyMCEPreInit.qtInit[tmplEditorID] } );
				}
				// Start to initialize the editor settings and enable editors
				mceSettings = $.extend( {}, editorParams.mce, { 'selector': ( '#' + id ), 'body_class': editorParams.mce.body_class.replace( tmplEditorID, id ) } ),
				qtSettings 	= $.extend( {}, editorParams.qt, { 'id': id } );

				tmpl = $( '#tmpl-loftocean-widget-editor-field' ).html();
				tmpl = $( tmpl.replace( /\[\[loftocean-widget-editor-id\]\]/g, id ) );
				tmpl.find( 'textarea' ).attr( 'id', id).val(textarea.val() );
				$container.append( tmpl );
				window.tinymce.init( mceSettings );
				quicktags( qtSettings );
				//window.wpActiveEditor = id;

				editor = window.tinymce.get(id);
				if ( ! editor ) {
					return;
				}
				onInit = function() {
					// When a widget is moved in the DOM the dynamically-created TinyMCE iframe will be destroyed and has to be re-built.
					$( editor.getWin() ).on( 'unload', function() {
						_.defer( buildEditor );
					} );
				};

				if ( editor.initialized ) {
					onInit();
				}  else {
					editor.on( 'init', onInit );
				}

				control.editorFocused = false;
				tmpl.find( 'textarea').on( 'keyup change blur', function() {
					needsTextareaChangeTrigger = true;
					editor.setDirty( true ); // Because pasting doesn't currently set the dirty state.
					triggerChangeIfDirty();
				} );
				editor.on( 'focus', function() {
					control.editorFocused = true;
				} );
				editor.on( 'paste', function() {
					editor.setDirty( true ); // Because pasting doesn't currently set the dirty state.
					triggerChangeIfDirty();
				} );
				editor.on( 'NodeChange', function() {
					needsTextareaChangeTrigger = true;
				} );
				editor.on( 'NodeChange', _.debounce( triggerChangeIfDirty, changeDebounceDelay ) );
				editor.on( 'blur hide', function onEditorBlur() {
					control.editorFocused = false;
					triggerChangeIfDirty();
				} );
				control.editor = editor;
			}
			buildEditor();
		}
		/**
		* Initialize color picker
		* @param jQuery object need to enable color picker
		*/
		function initColorPicker( $picker ) {
			$picker.each( function() {
				var $color_picker = $( this );
				$color_picker.wpColorPicker( {
					change: function( event, ui ) {
						var color = ui.color ? ui.color.toString() : '';
						$color_picker.val(color).trigger( 'change' );
					},
					clear: function() {
						$color_picker.val( '' );
						$(this).trigger( 'change' );
					}
				} );
			} );
		}
		$( document )
			.on( 'widget-added', widgetAdded )
			.on( 'widget-synced widget-updated', widgetUpdated )
			.ready( function() {
				if ( 'widgets' !== window.pagenow ) {
					return;
				}

				var widgetContainers = $( '.widgets-holder-wrap:not(#available-widgets)' ).find( 'div.widget' );
				widgetContainers.one( 'click.toggle-widget-expanded', function() {
					widgetAdded( new jQuery.Event( 'widget-added' ), $( this ) );
				});
			} );
	} ) ();
} ) ( jQuery );
