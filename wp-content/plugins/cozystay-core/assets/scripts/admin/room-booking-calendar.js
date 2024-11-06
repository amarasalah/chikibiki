( function( $ ) {
    "use strict";
    document.addEventListener( 'DOMContentLoaded', function() {
        var i18nText = loftoceanBookingCalendar.i18nText,
            $popup = $( '#tab-booking-calendar-panel .booking-calendar-modal' ),
            $popupFooter = $popup.find( '.booking-calendar-modal-footer .button' ),
            $popupMainContent = $popup.find( '.booking-calendar-modal-content .cs-order-preview' ),
            detailTemplate = wp.template( 'loftocean-room-order-details' ),
            calendarEl = document.getElementById( 'booking-calendar' ), calendar,
            $loader = $( '#tab-booking-calendar-panel .calendar-loading' );

        function addLeadingZero( num ) {
            return ( num > 9 ) ? num : '0' + num;
        }
        function showOrderDetails( attrs ) {
            if ( ( 'undefined' != typeof attrs.extendedProps.is_booking ) && attrs.extendedProps.is_booking ) {
                var orderDetails = attrs.extendedProps;
                $popup.find( '.booking-title .order-number' ).html( orderDetails.order_id );
                parseOrder( orderDetails );
                if ( 'woocommerce' == orderDetails.source ) {
                    $popupFooter.attr( 'href', orderDetails.link ).show();
                } else {
                    $popupFooter.attr( 'href', '#' ).hide();
                }
                $popup.addClass( 'show' );
            }
        }
        function parseOrder( attrs ) {
            var $detail = detailTemplate( attrs );
            if ( $detail && $detail.length ) {
                $popupMainContent.html( $detail );
            }
        }

        calendar = new FullCalendar.Calendar( calendarEl, {
            locale: loftoceanBookingCalendar.locale,
			timeZone: loftoceanBookingCalendar.timezone,
            headerToolbar: {
                left: 'today',
                center: 'title',
                right: 'prev,next'
            },
			displayEventTime: true,
            selectable: true,
            fixedWeekCount: false,
			eventClick: function ( { event, el, jsEvent, view } ) {
                showOrderDetails( event );
			},
            events: function ( { start, end, startStr, endStr, timeZone }, successCallback, failureCallback ) {
                var startTime = start.getFullYear() + '-' + addLeadingZero( start.getMonth() + 1 ) + '-' + addLeadingZero( start.getDate() ),
                    endTime = end.getFullYear() + '-' + addLeadingZero( end.getMonth() + 1 ) + '-' + addLeadingZero( end.getDate() );
                $loader.show();
                $.ajax( {
                    url: wpApiSettings.root + 'loftocean/v1/get_bookings/' + ( new Date() ).getTime(),
                    data: { 'rid': loftoceanRoomAvailability[ 'roomID' ], 'start': startTime, 'end': endTime },
                    type: 'POST',
                    success: function ( results ) {
                        if ( typeof results == "object" ) {
                            successCallback( results );
                        }
                        $loader.hide();
                    },
                    error: function ( e ) {
                        console.log( i18nText.errorMessage );
                        $loader.hide();
                    }
                } );
            },
			eventContent: function ( args ) {
                var $contentEl = $( '<div>', { 'class': 'fc-content' } );
                if ( args.event.extendedProps.is_booking ) {
    				$contentEl.html( '<div class="order-summary">' + args.event.title + '</div>' );
                }
				return {
					domNodes: [ $contentEl.get(0) ]
				}
			},
            dayMaxEvents: true
        } );

        calendar.render();

        $popup.on( 'click', '.modal-close', function( e ) {
            e.preventDefault();
            $popup.removeClass( 'show' );
        } ).on( 'click', '.booking-calendar-modal-bg-overlay', function( e ) {
            e.preventDefault();
            $popup.removeClass( 'show' );
        } );
        $( document ).on( 'render.loftocean.bookingCalendar', function( e ) {
            calendar.trigger( '_resize' );
        } ).on( 'refresh.loftocean.bookingCalendar', function( e ) {
            calendar.refetchEvents();
        } );
    } );
} ) ( jQuery );
