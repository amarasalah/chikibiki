( function( $ ) {
	"use strict";
	if ( $( '#cs-dashboard-tabs-wrapper' ).length ) {
		var $contents = $( '.cs-tab-content-wrapper' ), $activeTabInput = $( '[name=loftocean_room_settings_activa_tab]' );
		$( '#cs-dashboard-tabs-wrapper' ).on( 'click', '.nav-tab', function( e ) {
			e.preventDefault();
			var $self = $( this ), targetID = $self.attr( 'href' ) + '-content';
			if ( $self.hasClass( 'nav-tab-active' ) ) return;
			
			if ( $contents.filter( targetID ).length ) {
				$self.siblings().removeClass( 'nav-tab-active' );
				$self.addClass( 'nav-tab-active' );
				$contents.filter( targetID ).removeClass( 'hidden' ).siblings( '.cs-tab-content-wrapper' ).addClass( 'hidden' );
				$activeTabInput.val( $self.attr( 'href' ).replace( '#tab-', '' ) );
			}
		} );
	}


	// Reset room facilities
	if ( $( '.loftocean-room-reset-facilities button' ).length ) {
		$( '.loftocean-room-reset-facilities button' ).on( 'click', function( e ) {
			e.preventDefault();
            e.stopPropagation();
            var $btn = $( this ), $message = $btn.siblings( '.message' );
            $btn.attr( 'disabled', 'disabled' ).addClass( 'loading' );
            $message.hide();
            $.post( loftoceanRoomSettings.url, { 'action': loftoceanRoomSettings.actionResetFacilities } ).done( function( response, status ) {
                    if ( ( ! response[ 'success' ] ) || ( typeof response[ 'data' ] == 'undefined' || response[ 'data' ][ 'status' ] == 'failed' ) ) {
                        $message.show();
                    }
                } ).error( function() {
					$message.show();
				} ).always( function() {
    				$btn.attr( 'disabled', false ).removeClass( 'loading' );
    			} );
		} );
	}

	// Sync order data
	if ( $( '.loftocean-room-regenerate-orders button' ).length ) {
		$( '.loftocean-room-regenerate-orders button' ).on( 'click', function( e ) {
			e.preventDefault();
            e.stopPropagation();
            var $btn = $( this ), $message = $btn.siblings( '.message' );
            $btn.attr( 'disabled', 'disabled' ).addClass( 'loading' );
            $message.hide();
            $.post( loftoceanRoomSettings.url, { 'action': loftoceanRoomSettings.actionSyncOrderData } ).done( function( response, status ) {
                    if ( ( ! response[ 'success' ] ) || ( typeof response[ 'data' ] == 'undefined' || response[ 'data' ][ 'status' ] == 'failed' ) ) {
                        $message.show();
                    }
                } ).error( function(){
					$message.show();
				} ).always( function() {
    				$btn.attr( 'disabled', false ).removeClass( 'loading' );
    			} );
		} );
	}
} ) ( jQuery );
