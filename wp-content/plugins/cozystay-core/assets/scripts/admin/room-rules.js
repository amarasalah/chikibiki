( function( $ ) {
    "use strict";
    var $rulesSection = $( '.loftocean-room-rules-wrapper' );
	if ( $rulesSection.length ) {
        var $addBtn = $rulesSection.find( '.loftocean-room-rules-add' ), tmpl = wp.template( 'loftocean-room-rule-item' ),
            priceRuleCustomLongStayDiscountTmpl = wp.template( 'loftocean-room-rule-custom-long-stay-item' ),
        	$removedID = $rulesSection.siblings( '[name=loftocean_room_rules_removed]' ), $submit = $( '.submit.loftocean-submit-button #submit' ),
            defaultSettings = {
                'booking': {
                    'term_id': '',
                    'title': '',
                    'time_range': '',
                    'start_date': '',
                    'end_date': '',
                    'stay_length': {
                        'general': { 'enable': '', 'min': '', 'max': '' },
                        'custom': { 'enable': '', 'day0': { 'min': '', 'max': '' }, 'day1': { 'min': '', 'max': '' }, 'day2': { 'min': '', 'max': '' }, 'day3': { 'min': '', 'max': '' }, 'day4': { 'min': '', 'max': '' }, 'day5': { 'min': '', 'max': '' }, 'day6': { 'min': '', 'max': '' } }
                    },
                    'no_checkin_checkout_date': { 'enable': '', 'checkin': { 'day0': '', 'day1': '', 'day2': '', 'day3': '', 'day4': '', 'day5': '', 'day6': '' }, 'checkout': { 'day0': '', 'day1': '', 'day2': '', 'day3': '', 'day4': '', 'day5': '', 'day6': '' } },
                    'in_advance': { 'enable': '', 'min': '', 'max': '' },
                    'apply_to': '',
                    'apply_to_room_types': [],
                    'apply_to_rooms': []
                },
                'flexible-price': {
                    'term_id': '',
                    'title': '',
                    'time_range': '',
                    'start_date': '',
                    'end_date': '',
                    'special_price': {
                        'operator': '-',
                        'amount': '0'
                    },
                    'long_stay_discount': { 'enable': '', 'weekly': 10, 'monthly': 15, },
                    'custom_long_stay_discount': { 'enable': '', 'items': [ { 'days': '', 'off': '' } ] },
                    'early_bird_discount': { 'enable': '', 'days_before': 90, 'discount': '' },
                    'last_minute_discount': { 'enable': '', 'days_before': '7', 'discount': '' },
                    'apply_to': '',
                    'apply_to_room_types': [],
                    'apply_to_rooms': []
                }
            };

		function addNewItem( type ) {
            if ( ( 'undefined' != typeof type ) && ( 'undefined' != defaultSettings[ type ] ) ) {
                var $newItem = tmpl( { 'index': $addBtn.data( 'current-index' ), 'list': [ defaultSettings[ type ] ] } );
                if ( $newItem ) {
                    $newItem = $( $newItem );
                    $addBtn.before( $newItem );
                    initControls( $newItem );
                }
            }
        }

        function initControls( $item ) {
            if ( $item && $item.length ) {
                var $datePicker = $item.find( '.date-picker' ),
                    $multipleChoices = $item.find( 'select.multiple-choices' );
                $datePicker.length ? $datePicker.datepicker( { 'dateFormat': 'yy-mm-dd', 'minDate': 0 } ) : '';
                $multipleChoices.length ? $multipleChoices.select2( { 'width': '100%' } ) : '';
            }
        }

        $rulesSection.on( 'click', '.loftocean-room-rules-item-remove', function( e ) {
            e.preventDefault();
            var $item = $( this ).closest( '.loftocean-room-rules-item' ),
            	$IDInput = $item.find( '.room-rules-item-id-hidden' );
            if ( $IDInput.length && $IDInput.val() ) {
            	var val = $IDInput.val(), oldIDs = $removedID.val(),
            		newIDs = oldIDs ? oldIDs.split( ',' ) : [];
            	newIDs.push( val );
            	$removedID.val( newIDs.join( ',' ) );
            }
            $item.remove();
        } ).on( 'keyup', '.loftocean-room-rules-title', function( e ) {
            var $title = $( this ).closest( '.loftocean-room-rules-item' ).find( 'h3 .item-name' ),
                name = $( this ).val();
            if ( $title.length ) {
                name ? $title.html( ' - ' + name ) : $title.html( '' );
            }
        } ).on( 'change', '.option-title-with-toggle input[type=checkbox]', function( e ) {
            var $optionDetails = $( this ).parent().siblings( '.option-content-after-toggle' );
            if ( $optionDetails.length ) {
                $( this ).is( ':checked' ) ? $optionDetails.removeClass( 'hide' ) : $optionDetails.addClass( 'hide' );
            }
        } ).on( 'change', '.apply-rule-to', function( e ) {
            var $self = $( this ), val = $self.val(), $subOptions = $self.siblings( '.sub-options' );
            if ( $subOptions.length ) {
                $subOptions.addClass( 'hide' );
                val && $self.siblings( '.loftocean-' + val ).length ? $self.siblings( '.loftocean-' + val ).removeClass( 'hide' ) : '';
            }
        } ).on( 'change', '.loftocean-time-range-select', function( e ) {
            var $customDates = $( this ).closest( '.control-wrapper' ).siblings( '.control-wrapper.loftocean-custom-date-range' );
            if ( $customDates.length ) {
                if ( $( this ).val() ) {
                    $customDates.removeClass( 'hide' );
                    $customDates.find( 'label' ).append( '<span class="required"> *</span>' );
                    $customDates.find( 'input' ).attr( 'required', 'required' );
                } else {
                    $customDates.addClass( 'hide' );
                    $customDates.find( '.required' ).remove();
                    $customDates.find( 'input' ).removeAttr( 'required' );
                }
            }
        } ).on( 'click', '.add-custom-custom-long-stay', function( e ) {
            e.preventDefault();
            var $itemWrapper = $( this ).closest( '.option-content-after-toggle' ),
                $newItem = priceRuleCustomLongStayDiscountTmpl( { 'namePrefix': $itemWrapper.data( 'name-prefix' ) } );
            if ( $newItem ) {
                $itemWrapper.append( $newItem );
            }
        } ).on( 'click', '.delete-custom-custom-long-stay', function( e ) {
            e.preventDefault();
            var $item = $( this ).closest( '.multi-items-wrapper' );
            if ( $item.siblings().length ) {
                $item.remove();
            } else {
                $item.find( 'input' ).val( '' );
            }
        } );
        $addBtn.on( 'click', function( e ) {
            e.preventDefault();
            addNewItem( $addBtn.data( 'rule-type' ) );
            $addBtn.data( 'current-index', ( 1 + $addBtn.data( 'current-index' ) ) );
        } );

        var currentRules = loftoceanRoomRules
            ? ( Array.isArray( loftoceanRoomRules ) ? loftoceanRoomRules : ( typeof loftoceanRoomRules == 'object' ? Object.values( loftoceanRoomRules ) : false ) )
                : false;
        if ( currentRules && currentRules.length ) {
            var $newItems = tmpl( { 'index': 0, 'list': currentRules } );
            if ( $newItems ) {
                $newItems = $( $newItems );
                $addBtn.before( $newItems );
                $addBtn.data( 'current-index', $newItems.length );
                initControls( $newItems );
            }
    	}
    	$rulesSection.sortable( {
	    	'items': '> .loftocean-room-rules-item'
	    } ).on( 'dblclick', '.loftocean-room-rules-item > h3', function( e ) {
            var $itemDetails = $( this ).siblings( '.loftocean-room-rules-controls-wrapper' );
            $itemDetails.length ? $itemDetails.toggle() : '';
        } );
        $submit.length ? $submit.removeAttr( 'disabled' ).siblings( '.spinner' ).css( 'visibility', 'hidden' ) : '';
    }
} ) ( jQuery );
