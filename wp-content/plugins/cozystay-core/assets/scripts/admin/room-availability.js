( function( $ ) {
    "use strict";
    document.addEventListener( 'DOMContentLoaded', function() {
        var calendarEl = document.getElementById( 'availability-calendar' ), $calendarEditor = $( '.availability-editor' ),
            priceByPerPerson = ( 'on' == loftoceanRoomAvailability.roomSettings.priceByPeople ), $doc = $( document ),
            i18nText = loftoceanRoomAvailability.i18nText, $checkIn, $checkOut, $basePrice, $adultChildPrice, $status,
            $basePriceInput, $adultPriceInput, $childPriceInput, $roomNumberInput, calendar, $loader;

        if ( ! $calendarEditor.length ) return false;

        var $datePicker = $calendarEditor.find( '.date-picker' );
        $datePicker.length ? $datePicker.datepicker( { 'dateFormat': 'yy-mm-dd' } ) : '';

        $checkIn = $calendarEditor.find( '#calendar_check_in' );
        $checkOut = $calendarEditor.find( '#calendar_check_out' );
        $basePrice = $calendarEditor.find( '.form-field.price-field' );
        $adultChildPrice = $calendarEditor.find( '.adult-child-price' );
        $basePriceInput = $basePrice.find( 'input' );
        $adultPriceInput = $adultChildPrice.find( '#calendar_adult_price' );
        $childPriceInput = $adultChildPrice.find( '#calendar_child_price' );
        $status = $calendarEditor.find( '#calendar_status' );
        $roomNumberInput = $calendarEditor.find( '#calendar_room_number' );
        $loader = $( '.calendar-loading' );

        if ( priceByPerPerson ) {
            $basePrice.hide();
            $adultChildPrice.show();
        } else {
            $basePrice.show();
            $adultChildPrice.hide();
        }

        function setEditorDate( args ) {
            $checkIn.val( args.start );
            $checkOut.val( args.end );
        }

        function addLeadingZero( num ) {
            return ( num > 9 ) ? num : '0' + num;
        }

        function updateCalendarEvents( attrs ) {
            if ( typeof attrs === 'object' && attrs.checkIn && attrs.checkOut ) {
                var startTime = ( new Date( attrs.checkIn ) ).getTime(), endTime = ( new Date( attrs.checkOut ) ).getTime(), dataToSend = [];
                if ( startTime < endTime ) {
                    dataToSend = {
                        'checkin': attrs.checkIn,
                        'checkout': attrs.checkOut,
                        'price': attrs.basePrice,
                        'number': attrs.number,
                        'adult_price': attrs.adultPrice,
                        'child_price': attrs.childPrice,
                        'status': attrs.status
                    };
                    $loader.show();
                    $.ajax( {
                        url: wpApiSettings.root + 'loftocean/v1/update_room_availability/' + ( new Date() ).getTime(),
                        data: { 'rid': loftoceanRoomAvailability[ 'roomID' ], 'data': btoa( JSON.stringify( dataToSend ) ) },
                        method: 'POST',
                        success: function ( response, status ) {
                            if ( ( typeof response == "object" ) && response.updated ) {
                                calendar.refetchEvents();
                                $calendarEditor.find( 'input[type=text]' ).val( '' );
                                $doc.trigger( 'refresh.loftocean.bookingCalendar' );
                            } else {
                                $loader.hide();
                            }
                        }, error: function ( e ) {
                            console.log( i18nText.errorMessage );
                            $loader.hide();
                        }
                    } );
                }
            }
        }

        calendar = new FullCalendar.Calendar( calendarEl, {
            locale: loftoceanRoomAvailability.locale,
			timeZone: loftoceanRoomAvailability.timezone,
            headerToolbar: {
                left: 'today',
                center: 'title',
                right: 'prev,next'
            },
			displayEventTime: true,
            selectable: true,
            fixedWeekCount: false,
			eventClick: function ( { event, el, jsEvent, view } ) {
                var attrs = event.extendedProps, startDate = new Date( event.start.getFullYear(), event.start.getMonth(), event.start.getDate() ), endDate = new Date( startDate );

                endDate.setDate( startDate.getDate() + 1 );
                setEditorDate( {
                    'start': startDate.getFullYear() + '-' + addLeadingZero( parseInt( startDate.getMonth(), 10 ) + 1 ) + '-' + addLeadingZero( startDate.getDate() ),
                    'end': endDate.getFullYear() + '-' + addLeadingZero( parseInt( endDate.getMonth(), 10 ) + 1 ) + '-' + addLeadingZero( endDate.getDate() )
                } );
                $basePriceInput.val( attrs.price );
                $adultPriceInput.val( attrs.adult_price );
                $childPriceInput.val( attrs.child_price );
                $roomNumberInput.val( attrs.number );
                $status.val( attrs.original_status );
			},
            events: function ( { start, end, startStr, endStr, timeZone }, successCallback, failureCallback ) {
                var startTime = start.getFullYear() + '-' + addLeadingZero( start.getMonth() + 1 ) + '-' + addLeadingZero( start.getDate() ),
                    endTime = end.getFullYear() + '-' + addLeadingZero( end.getMonth() + 1 ) + '-' + addLeadingZero( end.getDate() );
                $loader.show();
                $.ajax( {
                    url: wpApiSettings.root + 'loftocean/v1/get_room_availability/' + ( new Date() ).getTime(),
                    data: { 'rid': loftoceanRoomAvailability[ 'roomID' ], 'start': startTime, 'end': endTime },
                    type: 'POST',
                    success: function ( events ) {
                        if ( typeof events == "object" ) {
                            successCallback( events );
                        }
                        $loader.hide();
                    }, error: function ( e ) {
                        console.log( i18nText.errorMessage );
                        $loader.hide();
                    }
                } );
            },
			eventContent: function ( args ) {
				var $contentEl = $( '<div>', { 'class': 'fc-content' } ),
                    $priceEl = $( '<div>', { 'class': 'price' } ),
                    $startTimeEl = $( '<div>', { 'class': 'starttime' } );

				if ( args.event.extendedProps.status ) {
					var status = args.event.extendedProps.status;
					if ( args.event.extendedProps.original_status === 'unavailable' ) {
						$contentEl.addClass( 'unavailable' ).removeClass( 'available' );
						$contentEl.html( '<div class="not-available">' + i18nText.unavailable + '</div>' );
					} else {
						$contentEl.addClass( 'available' ).removeClass( 'unavailable' );
						if ( priceByPerPerson ) {
							if ( typeof args.event.extendedProps.adult_price != 'undefined' ) {
								var $adultPriceEl = $( '<div>', { 'class': 'price' } );
								$adultPriceEl.html( i18nText.adult + ': ' + args.event.extendedProps.adult_price );
								$contentEl.append( $adultPriceEl );
							}
							if ( typeof args.event.extendedProps.child_price != 'undefined' ) {
								var $childPriceEl = $( '<div>', { 'class': 'price' } );
								$childPriceEl.html( i18nText.child + ': ' + args.event.extendedProps.child_price );
								$contentEl.append( $childPriceEl );
							}
						} else {
							if ( typeof args.event.extendedProps.price != 'undefined' ) {
                                var $basePriceEl = $( '<div>', { 'class': 'price' } );
                                $basePriceEl.html( i18nText.base + ': ' + args.event.extendedProps.price );
                                $contentEl.append( $basePriceEl );
                            }
						}
						if ( typeof args.event.extendedProps.number != 'undefined' ) {
                            var $roomNumberEl = $( '<div>', { 'class': 'room-number' } );
                            $roomNumberEl.html( i18nText.roomNumber + ': ' + args.event.extendedProps.number );
                            $contentEl.append( $roomNumberEl );
                        }
                        if ( typeof args.event.extendedProps.available_number ) {
                            var $roomLeftNumberEl = $( '<div>', { 'class': 'room-number-left' } );
                            $roomLeftNumberEl.html( i18nText.leftNumber + ': ' + args.event.extendedProps.available_number );
                            $contentEl.append( $roomLeftNumberEl );
                        }
					}
				}
				return {
					domNodes: [ $contentEl.get(0) ]
				}
			},
            dayMaxEvents: true
        } );

        calendar.render();

        $( document ).on( 'render.loftocean.room.availability', function( e ) {
            calendar.trigger( '_resize' );
        } );
        $calendarEditor.on( 'click', 'input[type=submit]', function( e ) {
            e.preventDefault();
            var checkIn = $checkIn.val(), checkOut = $checkOut.val(), basePrice = $basePriceInput.val(),
                adultPrice = $adultPriceInput.val(), childPrice = $childPriceInput.val(),
                status = $status.val(), number = $roomNumberInput.val(),
                attrs = { checkIn, checkOut, basePrice, adultPrice, childPrice, status, number };
            if ( checkIn && checkOut ) {
                updateCalendarEvents( attrs );
            }
        } );
    } );
} ) ( jQuery );
