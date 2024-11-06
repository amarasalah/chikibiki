( function( $ ) {
    $( window ).on( 'elementor/init', function() {
        $( 'body' ).on( 'click', '.elementor-control-popup_box_preview .elementor-control-input-wrapper button', function( e ) {
            var widgets = elementor.selection.getElements();
            if ( 1 === widgets.length ) {
                var id = widgets[0].id, previewView = elementor.getPreviewView(), $button = previewView.$el.find( '.elementor-element-editable[data-id=' + id + '] > .elementor-widget-container > .elementor-button-link' );
                if ( $button.length ) {
                    var $popup = $button.data( 'popup-box' ) ? $button.data( 'popup-box' ) : $button.siblings( '.cs-button-popup' );
                    if ( $popup.length && $popup.parent().length && ( ! $popup.hasClass( 'show' ) ) ) {
                        var $body = $button.parents( 'body' ), $activedPopups = $body.children( '.cs-button-popup.show' );
                        $activedPopups.length ? $activedPopups.removeClass( 'show' ) : '';
                        $button.data( 'popup-box', $popup );
                        $popup.appendTo( $button.parents( 'body' ) ).addClass( 'show' ).removeClass( 'hide' );
                    }
                }
            }
        } ).on( 'click', '.media-widget-control .button.widget-choose-media, .media-widget-preview', function( e ) {
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
        } ).on( 'click', '.media-widget-control .button.widget-remove-media', function( e ) {
            e.preventDefault();
            var $this = $( this ), $wrap = $this.parent(), $preview = $wrap.siblings( '.media-widget-preview' );
            $this.removeClass( 'not-selected' ).addClass( 'selected' );
            $wrap.siblings( 'input[type=hidden]' ).val( '' ).first().trigger( 'change' );
            $preview.html( '' ).append( $( '<div>', { 'class': 'placeholder', 'text': $preview.attr( 'data-text-preview' ) } ) );
        } ).on( 'changed.loftocean.media', '.media-widget-control input.loftocean-widget-item[type=hidden]', function( e, media ) {
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
        elementor.hooks.addAction( 'panel/widgets/wp-widget-loftocean-widget-posts/controls/wp_widget/loaded', function( widgetView ) {
            var $category = widgetView.$el.find( '[data-loftocean-widget-item-id="category"]' ),
                $fitler = widgetView.$el.find( '[data-loftocean-widget-item-id="filter-by"]' );
            if ( $category.length && $fitler.length ) {
                $category = $category.parent();
                $fitler.on( 'change', function( e ) {
                    'category' == $( this ).val() ? $category.show() : $category.hide();
                } );
                'category' == $fitler.val() ? $category.show() : $category.hide();
            }
        } );
    } );
} ) ( jQuery );
