( function( $ ) {
    "use strict";
    var $roomFacilities = $( '.loftocean-room-facility-wrapper' ), $iconLibrary = $( '.loftocean-flaticons-libaray' ),
        $searchBar = $iconLibrary.find( '.loftocean-lightbox-main-content-search input' ),
        $iconList = $iconLibrary.find( '.loftocean-lightbox-icon-item' ),
        $submit = $( '.submit.loftocean-submit-button #submit' );
	if ( $roomFacilities.length ) {
        var $addBtn = $roomFacilities.find( '.loftocean-room-facility-add' ), tmpl = wp.template( 'loftocean-room-facility' ),
            $removedID = $roomFacilities.siblings( '[name=loftocean_room_facility_removed]' );

		function addDefaultCustomFontItem() {
            $addBtn.before( tmpl( { 'index': $addBtn.data( 'current-index' ), 'list': [ { 'name': '', 'description': '', 'icon': '', 'facility_type': 'custom-facility', 'term_id': '' } ] } ) );
        }

        $iconLibrary.on( 'click', '.loftocean-lightbox-close-area', function( e ) {
            $iconLibrary.hide();
        } ).on( 'keyup', '.loftocean-lightbox-main-content-search input', function( e ) {
            var str = $( this ).val();
            if ( str ) {
                $iconList.hide().filter( '[title*="' + str.toLowerCase() + '"]' ).show();
            } else {
                $iconList.show();
            }
        } ).on( 'click', '.loftocean-lightbox-icon-item', function( e ) {
            $iconList.removeClass( 'selected' );
            $( this ).addClass( 'selected' );
        } ).on( 'click', '.loftocean-lightbox-insert-button', function( e ) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var $target = $( $iconLibrary.data( 'currentItem' ) ), $selected = $iconList.filter( '.selected' );
            if ( $target.length && $selected.length ) {
                var currentIcon = $selected.attr( 'filter' );
                $target.find( 'input' ).val( currentIcon );
                $target.find( '.icon-preview' ).html( '<i class="flaticon flaticon-' + currentIcon + '"></i>' );
            }
            $iconLibrary.hide();
        } );


        $roomFacilities.on( 'click', '.loftocean-room-facility-item-remove', function( e ) {
            e.preventDefault();
            var $item = $( this ).closest( '.loftocean-room-facility-item' ),
            	$IDInput = $item.find( '.facility-item-id-hidden' );
            if ( $IDInput.length && $IDInput.val() ) {
            	var val = $IDInput.val(), oldIDs = $removedID.val(),
            		newIDs = oldIDs ? oldIDs.split( ',' ) : [];
            	newIDs.push( val );
            	$removedID.val( newIDs.join( ',' ) );
            }
            $item.remove();
        } ).on( 'keyup', '.loftocean-room-facility-label', function( e ) {
            var $title = $( this ).closest( '.loftocean-room-facility-item' ).find( 'h3 .item-name' ),
                name = $( this ).val();
            if ( $title.length ) {
                name ? $title.html( ' - ' + name ) : $title.html( '' );
            }
        } ).on( 'click', '.loftocean-room-facility-choose-icon', function( e ) {
            e.preventDefault();
            $searchBar.val( '' );
            $iconList.show();
            $iconLibrary.data( 'currentItem', $( this ).parent() ).show();
        } ).on( 'click', '.loftocean-room-facility-remove-icon', function( e ) {
            e.preventDefault();
            $( this ).siblings( 'input' ).val( '' );
            $( this ).siblings( '.icon-preview' ).html( '' );
        } );
        $addBtn.on( 'click', function( e ) {
            e.preventDefault();
            addDefaultCustomFontItem();
            $addBtn.data( 'current-index', ( 1 + $addBtn.data( 'current-index' ) ) );
        } );

        var currentFacilities = loftoceanRoomFacility ? ( Array.isArray( loftoceanRoomFacility ) ? loftoceanRoomFacility : ( typeof loftoceanRoomFacility == 'object' ? Object.values( loftoceanRoomFacility ) : false ) ) : false;
        if ( currentFacilities ) {
	        $addBtn.before( tmpl( { 'index': 0, 'list': currentFacilities } ) );
    	    $addBtn.data( 'current-index', currentFacilities.length );
    	}
    	$roomFacilities.sortable( {
	    	'items': '> .loftocean-room-facility-item'
	    } ).on( 'dblclick', '.loftocean-room-facility-item > h3', function( e ) {
            var $itemDetails = $( this ).siblings( '.loftocean-room-facility-controls-wrapper' );
            $itemDetails.length ? $itemDetails.toggle() : '';
        } );
        $submit.length ? $submit.removeAttr( 'disabled' ).siblings( '.spinner' ).css( 'visibility', 'hidden' ) : '';
    }
} ) ( jQuery );
