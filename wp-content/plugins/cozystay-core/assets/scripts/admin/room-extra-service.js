( function( $ ) {
    "use strict";
    var $roomExtraServices = $( '.loftocean-room-extra-service-wrapper' );
	if ( $roomExtraServices.length ) {
        var $addBtn = $roomExtraServices.find( '.loftocean-room-extra-service-add' ), tmpl = wp.template( 'loftocean-room-extra-service' ),
        	$removedID = $roomExtraServices.siblings( '[name=loftocean_room_extra_service_removed]' ), $submit = $( '.submit.loftocean-submit-button #submit' ),
            customTimeSlotTmpl = wp.template( 'loftocean-room-extra-service-custom-time-slot' );

		function addDefaultCustomFontItem() {
            var $newItem = tmpl( { 'index': $addBtn.data( 'current-index' ), 'list': [ {
                'name': '',
                'price': '',
                'method': 'fixed',
                'auto_method': '',
                'custom_price_appendix_text': 'Per Person',
                'custom_minimum_quantity': '',
                'custom_maximum_quantity': '',
                'custom_adult_price': '',
                'custom_child_price': '',
                'effective_time': '',
                'custom_effective_time_slots': [],
                'obligatory': '',
                'term_id': ''
            } ] } );
            if ( $newItem ) {
                $newItem = $( $newItem );
                $addBtn.before( $newItem );
                initControls( $newItem );
            }
        }
        function addNewCustomTimeSlot( $wrapper ) {
            var $newItem = customTimeSlotTmpl( { 'index': $wrapper.data( 'slot-count' ), 'namePrefix': $wrapper.data( 'name-prefix' ) } );
            if ( $newItem ) {
                $newItem = $( $newItem );
                $( $wrapper ).append( $newItem );
                initControls( $newItem );
            }
        }
        function initControls( $item ) {
            if ( $item && $item.length ) {
                var $datePicker = $item.find( '.date-picker' );
                $datePicker.length ? $datePicker.datepicker( { 'dateFormat': 'yy-mm-dd', 'minDate': 0 } ) : '';
            }
        }

        $roomExtraServices.on( 'click', '.loftocean-room-extra-service-item-remove', function( e ) {
            e.preventDefault();
            var $item = $( this ).closest( '.loftocean-room-extra-service-item' ),
            	$IDInput = $item.find( '.service-item-id-hidden' );
            if ( $IDInput.length && $IDInput.val() ) {
            	var val = $IDInput.val(), oldIDs = $removedID.val(),
            		newIDs = oldIDs ? oldIDs.split( ',' ) : [];
            	newIDs.push( val );
            	$removedID.val( newIDs.join( ',' ) );
            }
            $item.remove();
        } ).on( 'keyup', '.loftocean-room-extra-service-title', function( e ) {
            var $title = $( this ).closest( '.loftocean-room-extra-service-item' ).find( 'h3 .item-name' ),
                name = $( this ).val();
            if ( $title.length ) {
                name ? $title.html( ' - ' + name ) : $title.html( '' );
            }
        } ).on( 'change', '.loftocean-room-extra-service-method', function( e ) {
        	var $wrap = $( this ).closest( '.control-wrapper' ), $autoWrap = $wrap.siblings( '.control-auto-calculated-item' ),
        		$customWrap = $wrap.siblings( '.control-custom-item' ), $customPriceItems = $wrap.siblings( '.control-auto-calculated-price-item' );
        	switch ( $( this ).val() ) {
        		case 'fixed':
        			$autoWrap.hide();
        			$customWrap.hide();
        			break;
        		case 'auto':
        			$autoWrap.show();
        			$customWrap.hide();
        			break;
        		case 'custom':
                case 'auto_custom':
        			$autoWrap.hide();
        			$customWrap.show();
        			break;
        	}

            if ( $customPriceItems.length ) {
                ( 'auto' == $( this ).val() ) && [ 'person', 'night-person' ].includes( $autoWrap.find( '.loftocean-room-extra-service-auto-method' ).val() ) ? $customPriceItems.show() : $customPriceItems.hide();
            }
        } ).on( 'change', '.loftocean-room-extra-service-auto-method', function() {
            var $self = $( this ), $customPriceItems = $self.closest( '.control-wrapper' ).siblings( '.control-auto-calculated-price-item' );
            if ( $customPriceItems.length ) {
                [ 'person', 'night-person' ].includes( $self.val() ) ? $customPriceItems.show() : $customPriceItems.hide();
            }
        } ).on( 'change', '.effective-time', function() {
            var $customSlotWrapper = $( this ).closest( '.controls-row' ).find( '.custom-effective-time-slots-wrapper' );
            ( '' == $( this ).val() ) ? $customSlotWrapper.hide() : $customSlotWrapper.show();
        } ).on( 'click', '.custom-effective-time-slots-wrapper .add-custom-effective-time-slot', function( e ) {
            e.preventDefault();
            var $wrapper = $( this ).closest( '.custom-effective-time-slots-wrapper' );
            $wrapper.data( 'slot-count', $wrapper.data( 'slot-count' ) + 1 );
            addNewCustomTimeSlot( $wrapper );
            return false;
        } ).on( 'click', '.custom-effective-time-slots-wrapper .delete-custom-effective-time-slot', function( e ) {
            e.preventDefault();
            var $target = $( this ).parent(), $wrapper = $target.parent();
            $target.remove();
            if ( ! $wrapper.children().length ) {
                $wrapper.data( 'slot-count', $wrapper.data( 'slot-count' ) + 1 );
                addNewCustomTimeSlot( $wrapper );
            }
            return false;
        } );

        $addBtn.on( 'click', function( e ) {
            e.preventDefault();
            addDefaultCustomFontItem();
            $addBtn.data( 'current-index', ( 1 + $addBtn.data( 'current-index' ) ) );
        } );

        var currentExtraServices = loftoceanRoomExtraServices ? ( Array.isArray( loftoceanRoomExtraServices ) ? loftoceanRoomExtraServices : ( typeof loftoceanRoomExtraServices == 'object' ? Object.values( loftoceanRoomExtraServices ) : false ) ) : false;
        if ( currentExtraServices ) {
	       	var $newItems = tmpl( { 'index': 0, 'list': currentExtraServices } );
            if ( $newItems ) {
                $newItems = $( $newItems );
                $addBtn.before( $newItems );
                $addBtn.data( 'current-index', currentExtraServices.length );
                initControls( $newItems );
            }
    	}
    	$roomExtraServices.sortable( {
	    	'items': '> .loftocean-room-extra-service-item'
	    } ).on( 'dblclick', '.loftocean-room-extra-service-item > h3', function( e ) {
            var $itemDetails = $( this ).siblings( '.loftocean-room-extra-service-controls-wrapper' );
            $itemDetails.length ? $itemDetails.toggle() : '';
        } );
        $submit.length ? $submit.removeAttr( 'disabled' ).siblings( '.spinner' ).css( 'visibility', 'hidden' ) : '';
    }
} ) ( jQuery );
