( function( $ ) {
    "use strict";
    var $tabs = $( '.cs-tab-content-wrapper' ), i18nText = loftoceanRoomiCalSyncSettings.i18nText, uuidList = {},
        millisecondsInDay = 86400000, counting = 1000, syncDateFormat = 'YYYY-MM-DD H:mm', $importColumns, syncSources;

    function base64Encode( str ) {
        return str ? Base64.encode( str ) : '';
    }
    function base64Decode( str ) {
        return str ? Base64.decode( str ) : '';
    }
    function getTotalSources() {
        if ( Object.keys( syncSources ).length ) {
            var total = { 'count': 0, 'list': [] }, timeStamp = Math.floor( Date.now() / 1000 );
            Object.keys( syncSources ).forEach( function( uuid ) {
                total[ 'count' ] += parseInt( Object.keys( syncSources[ uuid ] ).length, 10 );
                Object.keys( syncSources[ uuid ] ).forEach( function( urlBase64 ) {
                    total.list.push( { 'room': syncSources[ uuid ][ urlBase64 ][ 'roomID' ], 'source': urlBase64, 'time': timeStamp, 'itemIndex': syncSources[ uuid ][ urlBase64 ][ 'itemIndex' ] } );
                } )
            } );
            return total;
        }
        return 0;
    }
    function getSourceRoomID( uuid, urlBase64, defaultRoomID ) {
        if ( ( 'undefined' != typeof syncSources[ uuid ] ) && ( 'undefined' != typeof syncSources[ uuid ][ urlBase64 ] ) && syncSources[ uuid ][ urlBase64 ][ 'roomID' ] ) {
            return syncSources[ uuid ][ urlBase64 ][ 'roomID' ];
        }
        return defaultRoomID ? defaultRoomID : '';
    }
    function updateSyncSources( uuid, source, add, data ) {
        if ( add ) {
            var source = data[ 'urlBase64' ];
            if ( 'undefined' == typeof syncSources[ uuid ] ) {
                syncSources[ uuid ] = {};
                syncSources[ uuid ][ source ] = data;
            } else {
                data[ 'oldURLBase64' ] && ( data[ 'oldURLBase64' ] != source ) && ( 'undefined' != typeof syncSources[ uuid ][ data[ 'oldURLBase64' ] ] ) ? ( delete syncSources[ uuid ][ data[ 'oldURLBase64' ] ] ) : '';
                syncSources[ uuid ][ source ] = data;
            }
        } else if ( ( 'undefined' != typeof syncSources[ uuid ] ) && ( 'undefined' != syncSources[ uuid ][ source ] ) ) {
            delete syncSources[ uuid ][ source ];
            Object.keys( syncSources[ uuid ] ).length ? '' : ( delete syncSources[ uuid ] );
        }
    }
    function getLastSyncText( lastUpdatedMoment, currentMoment ) {
        return currentMoment.diff( lastUpdatedMoment ) >  millisecondsInDay
            ? i18nText.lastSyncDatePrefix + ' ' + lastUpdatedMoment.format( syncDateFormat )
                : i18nText.lastSyncTimePassedPrefix + ' ' + lastUpdatedMoment.fromNow();
    }
    function updateSyncDate( $item, lastUpdatedMoment, currentMoment ) {
        if ( $item.length ) {
            $item.data( 'last-sync-time', parseInt( lastUpdatedMoment.unix(), 10 ) * 1000 );
            $item.html( getLastSyncText( lastUpdatedMoment, currentMoment ) );
        }
    }
    function sync( roomID, source, $status ) {
        $status.trigger( 'change.status.loftocean', [ 'status-syncing' ] );
        var syncedTime = moment();
        $.post( loftoceanRoomiCalSyncSettings.url, { 'action': loftoceanRoomiCalSyncSettings.actionSync, 'data': { 'roomID': roomID, 'source': source, 'time': Math.floor( Date.now() / 1000 ) } } )
            .done( function( response ) {
                if ( ( 'undefined' != typeof response ) && ( 'undefined' != response[ 'success' ] ) ) {
                    $status.trigger( 'change.status.loftocean', [ 'status-synced' ] );
                    updateSyncDate( $status.closest( '.added-calendar-item' ).find( '.added-calendar-name .added-calendar-info' ), syncedTime, moment() );
                } else {
                    $status.trigger( 'change.status.loftocean', [ 'status-failed' ] );
                }
            } ).error( function() {
                $status.trigger( 'change.status.loftocean', [ 'status-failed' ] );
            } );
    }
    function multipleSync( data ) {
        var current = data.list.shift(), lastSyncTime = moment(), leftListLength = data.list.length;
        $.post( loftoceanRoomiCalSyncSettings.url, { 'action': loftoceanRoomiCalSyncSettings.actionSync, 'data': { 'roomID': current[ 'room' ], 'source': current[ 'source' ], 'time': current[ 'time' ] } } ).always( function() {
            if ( $importColumns && $importColumns.length && $importColumns.find( '.added-calendar-item[data-index=' + current.itemIndex + ']' ).length ) {
                updateSyncDate( $importColumns.find( '.added-calendar-item[data-index=' + current.itemIndex + '] .added-calendar-info' ), lastSyncTime, lastSyncTime );
            }
            data.current ++;
            var percentage = ( data.current === data.total ) ? 100 : Math.floor( data.current / data.total * 100 );
            data.bar.css( 'transform', 'scaleX(' + percentage / 100 + ')' );
            data.counter.animate(
    			{ percentage: percentage },
    			{ duration: 700, step: function( now ) {
                    now = Math.floor( now );
                    var offsetX = Math.max( 0, ( data.counter.parent().width() * now / 100 - 30 ) );
    				data.bar.css( 'transform', 'scaleX(' + now / 100 + ')' );
    				data.counter.text( now + '%' ).css( 'transform', 'translate(' + offsetX + 'px, 100%)' );
    			}, complete: function() {
                    if ( ! leftListLength ) {
                        data.processingMessage.hide();
                        data.processedMessage.show();
                    }
                } }
            );
            leftListLength ? multipleSync( data ) : '';
        } );
    }
    function showSyncError() {
        alert( 'No external Calendar found.' );
    }

    var $calendars = $tabs.filter( '#loftocean-room-ical-sync-tab-calendars' );
    if ( $calendars.length ) {
        var $bulkAction = $calendars.find( 'input.button.action.doaction' ),
            $model = $calendars.find( '.loftocean-modal' ),
            $syncModel = $model.filter( '#loftocean-sync-calendars' ),
            $syncModelLoadBar = $syncModel.find( '.loading-bar-wrapper .load' ),
            $syncModelProcessing = $syncModel.find( '.processing' ),
            $syncModelProcessed = $syncModel.find( '.processed' ),
            $syncModelLoadCount = $syncModel.find( '.loading-bar-wrapper .load-count' ),
            $import = $model.filter( '#loftocean-ical-import-calendar' ),
            $importCalendarTitle = $import.find( '.loftocean-import-calendar-name' ),
            $importCalendarURL = $import.find( '.loftocean-import-calendar-url' ),
            $importCalendarRoomID = $import.find( '.loftocean-import-calendar-room-id' ),
            $importCalendarIndex = $import.find( '.loftocean-import-calendar-index' ),
            $importCalendarErrorMessageWrap = $import.find( '.error-message-wrapper' ),
            $importCalendarFieldRequiredErrorMessage = $importCalendarErrorMessageWrap.find( '.field-required' ),
            $importCalendarSyncSourceExistingErrorMessage = $importCalendarErrorMessageWrap.find( '.sync-source-existing' ),
            $importCalendarSyncServerErrorMessage = $importCalendarErrorMessageWrap.find( '.sync-server-error' ),
            $export = $model.filter( '#loftocean-ical-export-calendar' ),
            $exportMessage = $export.find( '.copy-link-msg' ),
            sourceTemplate = wp.template( 'loftocean-import-calendar-item' ),
            $removeModel = $model.filter( '#loftocean-delete-sync-source-item' ),
            $removeRoomURLBase64 = $removeModel.find( '.loftocean-remove-calendar-url-base64' ),
            $removeRoomID = $removeModel.find( '.loftocean-remove-calendar-room-id' ),
            $removeRoomIndex = $removeModel.find( '.loftocean-remove-calendar-index' ),
            $removeImportedBooking = $removeModel.find( '.loftocean-remove-imported-booking' );

        $importColumns = $calendars.find( '#the-list .column-import' );
        syncSources = ( 'object' == typeof loftoceanRoomiCalSyncSettings.syncSources ) && Object.keys( loftoceanRoomiCalSyncSettings.syncSources ).length ? loftoceanRoomiCalSyncSettings.syncSources : {};

        $calendars.on( 'click', 'button.loftocean-feed-url-export', function( e ) {
            e.preventDefault();
            $export.find( '.loftocean-room-ical-feed-url' ).val( loftoceanRoomiCalSyncSettings.feedBaseURL + $( this ).data( 'room-id' ) );
            $exportMessage.hide();
            $export.addClass( 'show' );
        } )
        .on( 'click', '.button.action.loftocean-sync-all', function( e ) {
            e.preventDefault();
            var totalSources = getTotalSources(), totalSourceCount = totalSources[ 'count' ];
            if ( totalSourceCount ) {
                $syncModelProcessing.show();
                $syncModelProcessed.hide();
                $syncModelLoadBar.css( 'transform', 'scaleX(0)' );
                $syncModelLoadCount.css( 'transform', 'translate( 0px, 100%)' ).text( '0%' ).prop( 'percentage', 0 );
                $syncModel.addClass( 'show' );

                multipleSync( {
                    'bar': $syncModelLoadBar,
                    'counter': $syncModelLoadCount,
                    'current': 0,
                    'total': totalSourceCount,
                    'list': totalSources[ 'list' ],
                    'processingMessage': $syncModelProcessing,
                    'processedMessage': $syncModelProcessed
                } );
            } else {
                showSyncError();
            }
        } );

        $model.on( 'click', '.modal-close', function( e ) {
            e.preventDefault();
            $model.removeClass( 'show' );
        } )
        .on( 'click', '.loftocean-modal-bg-overlay', function( e ) {
            $model.removeClass( 'show' );
        } );
        $removeModel.on( 'click', '#submit', function( e ) {
            e.preventDefault();
            var urlBase64 = $removeRoomURLBase64.val(), roomID = $removeRoomID.val(), itemIndex = $removeRoomIndex.val(), removeData = $removeImportedBooking.is( ':checked' ),
                sourceIndex = 'room-' + roomID, $btn = $( this ), uuid = uuidList[ sourceIndex ], $listColumn = $importColumns.filter( '[data-uuid="' + uuid + '"]' ),
                data = { 'urlBase64': urlBase64, 'roomID': getSourceRoomID( uuid, urlBase64, roomID ), 'removeData': ( removeData ? 'on' : 'off' ) };

            $btn.attr( 'disabled', 'disabled' ).attr( 'value', $btn.data( 'update-text' ) );
            $.post( loftoceanRoomiCalSyncSettings.url, { 'action': loftoceanRoomiCalSyncSettings.actionRemoveSyncSource, 'data': data } )
                .done( function( response, status ) {
                    uuidList[ sourceIndex ] ? updateSyncSources( uuidList[ sourceIndex ], urlBase64, false, {} ) : '';
                    if ( $listColumn.length && $listColumn.find( '.added-calendar-item[data-index=' + itemIndex + ']' ).length ) {
                        var $items = $listColumn.find( '.added-calendar-item[data-index=' + itemIndex + ']' );
                        $items.each( function() {
                            var $item = $( this );
                            $item.siblings().length ? '' : $item.parent().addClass( 'hidden' );
                            $item.remove();
                        } );
                    }

                    $removeModel.removeClass( 'show' );
                    $removeImportedBooking.removeAttr( 'checked' );
                    $removeRoomURLBase64.val( '' );
                    $removeRoomIndex.val( '' );
                    $removeRoomID.val( '' );
                } )
                .always( function() {
                    $btn.attr( 'disabled', false ).attr( 'value', $btn.data( 'label' ) );
                } );
        } );
        $export.on( 'click', '.copy-link-button .button', function( e ) {
            e.preventDefault();
            $export.find( '.loftocean-room-ical-feed-url' ).focus().select();
            document.execCommand( 'copy' );
            $exportMessage.removeClass( 'hidden' ).show();
        } );
        $import.on( 'click', '#submit', function( e ) {
            e.preventDefault();
            var name = $importCalendarTitle.val(), url = $importCalendarURL.val(), roomID = $importCalendarRoomID.val(),
                itemIndex = $importCalendarIndex.val(), urlBase64 = base64Encode( url ), sourceIndex = 'room-' + roomID,
                $btn = $( this ), uuid = uuidList[ sourceIndex ], $listColumn = $importColumns.filter( '[data-uuid="' + uuid + '"]' );
            if ( ( ! name ) || ( ! url ) ) {
                $importCalendarFieldRequiredErrorMessage.show();
                return false;
            }
            if ( ( ! itemIndex ) && ( 'undefined' != typeof syncSources[ uuid ] ) && ( 'undefined' != typeof syncSources[ uuid ][ urlBase64 ] ) ) {
                $importCalendarSyncSourceExistingErrorMessage.show();
                return false;
            }

            var itemExisting = itemIndex && $listColumn.find( '.added-calendar-item[data-index="' + itemIndex + '"]' ).length,
                oldURLBase64 = itemExisting ? $listColumn.find( '.added-calendar-item[data-index="' + itemIndex + '"]' ).data( 'sync-url-base64' ) : '',
                data = {
                    'title': name,
                    'roomID': itemExisting ? getSourceRoomID( uuid, oldURLBase64, roomID ) : roomID,
                    'urlBase64': urlBase64,
                    'titleBase64': base64Encode( name ),
                    'oldURLBase64': oldURLBase64
                };
            $btn.attr( 'disabled', 'disabled' ).attr( 'value', $btn.data( 'update-text' ) );
            $.post( loftoceanRoomiCalSyncSettings.url, { 'action': loftoceanRoomiCalSyncSettings.actionUpdateSyncSource, 'data': data } )
                .done( function( response, status ) {
                    var dataItemIndex = itemExisting ? itemIndex : counting;
                    data[ 'itemIndex' ] = dataItemIndex;
                    updateSyncSources( uuid, urlBase64, true, data );

                    if ( $listColumn.length ) {
                        if ( itemIndex && $listColumn.find( '.added-calendar-item[data-index="' + itemIndex + '"]' ).length ) {
                            var $items = $listColumn.find( '.added-calendar-item[data-index="' + itemIndex + '"]' );
                            $items.data( 'sync-url-base64', urlBase64 ).find( '.edit-item' ).html( name );
                            if ( oldURLBase64 != urlBase64 ) {
                                $items.find( '.added-calendar-info' ).data( 'last-sync-time', '' ).html( '' );
                            }
                        } else {
                            var $newItem = sourceTemplate( { 'index': counting++, 'list': [ data ] } );
                            $listColumn.children( '.added-calendars' ).append( $newItem ).removeClass( 'hidden' );
                        }
                        $import.removeClass( 'show' );
                        $importCalendarURL.val( '' );
                        $importCalendarTitle.val( '' );
                        $importCalendarIndex.val( '' );
                        $importCalendarRoomID.val( '' );
                    }
                } )
                .error( function() {
                    $importCalendarSyncServerErrorMessage.show();
                } )
                .always( function() {
    				$btn.attr( 'disabled', false ).attr( 'value', $btn.data( 'label' ) );
    			} );
        } )
        .on( 'focus', 'input[type="text"]', function() {
            $importCalendarErrorMessageWrap.children().hide();
        } );
        $importColumns.on( 'click', 'button.loftocean-ical-calendar-import', function( e ) {
            e.preventDefault();
            $importCalendarURL.val( '' );
            $importCalendarTitle.val( '' );
            $importCalendarIndex.val( '' );
            $importCalendarRoomID.val( $( this ).parent().data( 'room-id' ) );
            $importCalendarErrorMessageWrap.children().hide();

            $import.addClass( 'show' );
            $importCalendarURL.focus();
        } )
        .on( 'click', '.edit-item', function( e ) {
            e.preventDefault();
            var $btn = $( this ), $wrap = $btn.parent().parent();
            $wrap.parent().siblings( '.loftocean-ical-calendar-import' ).trigger( 'click' );
            $importCalendarURL.val( base64Decode( $wrap.data( 'sync-url-base64' ) ) );
            $importCalendarTitle.val( $btn.html() );
            $importCalendarIndex.val( $wrap.data( 'index' ) );
        } )
        .on( 'click', '.button.delete-button', function( e ) {
            e.preventDefault();
            var $item = $( this ), $wrap = $item.closest( '.added-calendar-item' );
            $removeImportedBooking.removeAttr( 'checked' );
            $removeRoomID.val( $item.closest( '.column-import' ).data( 'room-id' ) );
            $removeRoomIndex.val( $wrap.data( 'index' ) );
            $removeRoomURLBase64.val( $wrap.data( 'sync-url-base64' ) );
            $removeModel.addClass( 'show' );
        } )
        .on( 'click', '.button.sync-button', function( e ) {
            e.preventDefault();
            var $btn = $( this ), uuid = $btn.closest( '.column-import' ).data( 'uuid' ),
                $item = $btn.closest( '.added-calendar-item' ), urlBase64 = $item.data( 'sync-url-base64' ), roomID = getSourceRoomID( uuid, urlBase64 ),
                $status = $importColumns.filter( '[data-uuid="' + uuid + '"]' ).find( '.added-calendar-item[data-index="' + $item.data( 'index' ) + '"] .added-calendar-sync-status' );

            sync( roomID, urlBase64, $status );
        } )
        .on( 'change.status.loftocean', '.added-calendar-sync-status', function( e, classname ) {
            if ( 'undefined' != typeof classname ) {
                var $status = $( this );
                $status.removeClass( 'status-syncing status-synced status-failed' );
                'status-failed' == classname ? $( '<div>', { 'class': 'sync-status-text', 'text': i18nText.syncFailed } ).appendTo( $status ) : $status.find( '.sync-status-text' ).remove();
                classname ? $status.addClass( classname ) : '';
            }
        } );
        $bulkAction.on( 'click', function( e ) {
            e.preventDefault();
            if ( 'sync' == $( this ).siblings( '.bulk-action-selector' ).val() ) {
                var $checked = $calendars.find( '.check-column input:checked' );
                if ( $checked.length ) {
                    var totalSources = [], totalSourceCount = 0, timeStamp = Math.floor( Date.now() / 1000 ), addedList = [];
                    $checked.each( function() {
                        var $wrap = $( this ).closest( 'tr' ).find( '.column-import' ), uuid = $wrap.data( 'uuid' );
                        $wrap.find( '.added-calendar-item' ).each( function() {
                            var checkedSource = $( this ).data( 'sync-url-base64' );
                            if ( ( ! addedList.includes( uuid + checkedSource ) ) && ( 'undefined' != typeof syncSources[ uuid ] ) && ( 'undefined' != typeof syncSources[ uuid ][ checkedSource ] ) ) {
                                addedList.push( uuid + checkedSource );
                                totalSources.push( { 'room': syncSources[ uuid ][ checkedSource ][ 'roomID' ], 'source': checkedSource, 'time': timeStamp, 'itemIndex': $( this ).data( 'index' ) } );
                            }
                        } );
                    } );
                    totalSourceCount = totalSources.length;
                    if ( totalSourceCount ) {
                        $syncModelProcessing.show();
                        $syncModelProcessed.hide();
                        $syncModelLoadBar.css( 'transform', 'scaleX(0)' );
                        $syncModelLoadCount.css( 'transform', 'translate( 0px, 100%)' ).text( '0%' ).prop( 'percentage', 0 );
                        $syncModel.addClass( 'show' );

                        multipleSync( {
                            'bar': $syncModelLoadBar,
                            'counter': $syncModelLoadCount,
                            'current': 0,
                            'total': totalSourceCount,
                            'list': totalSources,
                            'processingMessage': $syncModelProcessing,
                            'processedMessage': $syncModelProcessed
                        } );
                    } else {
                        showSyncError();
                    }
                }
            }
            return false;
        } )

        if ( $importColumns.length ) {
            $importColumns.each( function() {
                var $item = $( this );
                if ( $item.data( 'room-id' ) && $item.data( 'uuid' ) ) {
                    uuidList[ 'room-' + $item.data( 'room-id' ) ] = $item.data( 'uuid' );
                }
            } );

            if ( Object.keys( syncSources ).length ) {
                var itemIndex = counting, current = moment();
                Object.keys( syncSources ).forEach( function( uuid ) {
                    var $sourceList = $importColumns.filter( '[data-uuid="' + uuid + '"]' );
                    if ( $sourceList.length && Object.keys( syncSources[ uuid ] ).length ) {
                        Object.keys( syncSources[ uuid ] ).forEach( function( urlBase64 ) {
                            if ( syncSources[ uuid ][ urlBase64 ][ 'lastSyncTime' ] ) {
                                var m = moment( parseInt( syncSources[ uuid ][ urlBase64 ][ 'lastSyncTime' ], 10 ) * 1000 );
                                if ( m.isValid() ) {
                                    syncSources[ uuid ][ urlBase64 ][ 'lastSyncTimePassed' ] = getLastSyncText( m, current );
                                    syncSources[ uuid ][ urlBase64 ][ 'lastSyncTime' ] = parseInt( syncSources[ uuid ][ urlBase64 ][ 'lastSyncTime' ], 10 ) * 1000;
                                } else {
                                    syncSources[ uuid ][ urlBase64 ][ 'lastSyncTime' ] = '';
                                }
                            }
                            syncSources[ uuid ][ urlBase64 ][ 'itemIndex' ] = itemIndex ++;
                            syncSources[ uuid ][ urlBase64 ][ 'urlBase64' ] = urlBase64;
                        } );
                        var $newItems = sourceTemplate( { 'index': counting, 'list': syncSources[ uuid ] } );
                        $sourceList.children( '.added-calendars' ).append( $newItems ).removeClass( 'hidden' );
                        counting += parseInt( Object.keys( syncSources[ uuid ] ).length, 10 );
                    }
                } );
            }
        }
        setTimeout( function autoUpdateSyncDate() {
            if ( $calendars.find( '.added-calendar-info' ).length ) {
                var currentMoment = moment();
                $calendars.find( '.added-calendar-info' ).each( function() {
                    var $item = $( this );
                    if ( $item.data( 'last-sync-time' ) ) {
                        $item.html( getLastSyncText( moment( parseInt( $item.data( 'last-sync-time' ), 10 ) ), currentMoment ) );
                    }
                } );
                setTimeout( function() { autoUpdateSyncDate(); }, 300000 );
            }
        }, 300000 );
    }
    var $importedBookings = $tabs.filter( '#loftocean-room-ical-sync-tab-imported-bookings' );
    if ( $importedBookings.length ) {
        var $bookingDetailModel = $importedBookings.find( '.loftocean-modal.imported-order' ), $preview = $bookingDetailModel.find( '.cs-order-preview' ),
            $orderNumber = $bookingDetailModel.find( '.booking-title .order-number' ), detailTemplate = wp.template( 'loftocean-room-order-details' );
        $importedBookings.on( 'click', '#the-list .order-view', function( e ) {
            e.preventDefault();

            var orderID = $( this ).data( 'order-id' );
            $preview.html( '' ).addClass( 'loading' );
            $orderNumber.text( orderID );
            $bookingDetailModel.addClass( 'show' );

            $.post( loftoceanRoomiCalSyncSettings.url, { 'action': loftoceanRoomiCalSyncSettings.actionGetImportedBookingDetail, 'data': { 'order_id': orderID } } )
                .done( function( response, status ) {
                    var $newDetail = detailTemplate( response.data.detail );
                    $preview.removeClass( 'loading' ).append( $newDetail );
                } )
                .error( function() {
                    var $newDetail = detailTemplate( {} );
                    $preview.removeClass( 'loading' ).append( $newDetail );
                } );
        } );
        $bookingDetailModel.on( 'click', '.modal-close', function( e ) {
            e.preventDefault();
            $bookingDetailModel.removeClass( 'show' );
        } )
        .on( 'click', '.loftocean-modal-bg-overlay', function( e ) {
            e.preventDefault();
            $bookingDetailModel.removeClass( 'show' );
        } );;
    }
    var $settings = $tabs.filter( '#loftocean-room-ical-sync-tab-settings' );
    if ( $settings.length ) {
        var $autoSyncIntervalWrapp = $settings.find( '.loftocean-auto-sync-interval-wrapper' ),
            $autoSyncIntervalOptions = $settings.find( '.auto-sync-interval-option-wrapper' );
        $settings.on( 'change', 'input[name=enable_auto_sync]', function( e ) {
            $( this ).is( ':checked' ) ? $autoSyncIntervalWrapp.show() : $autoSyncIntervalWrapp.hide();
        } ).on( 'change', 'select[name=auto_sync_interval]', function( e ) {
            'loftocean_24hours' == $( this ).val() ? $autoSyncIntervalOptions.show() : $autoSyncIntervalOptions.hide();
        } ).on( 'click', '.loftocean-clear-booking-records', function( e ) {
            e.preventDefault();
            var $btn = $( this );
            $btn.attr( 'disabled', 'disabled' ).addClass( 'loading' );
            $.post( loftoceanRoomiCalSyncSettings.url, { 'action': loftoceanRoomiCalSyncSettings.actionRemoveBookingsWithoutExistingSource } ).always( function() {
                $btn.removeAttr( 'disabled' ).removeClass( 'loading' );
            } );
        } );
    }

    $( 'body' ).on( 'keypress', '.current-page-selector', function( e ) {
        var $input = $( this );
        if ( $input.data( 'url-base' ) && $input.val() ) {
        	var keycode = ( e.keyCode ? e.keyCode : e.which );
        	if ( keycode == '13' ) {
        		window.location.href = $input.data( 'url-base' ) + $input.val();
        	}
        }
    } );
} ) ( jQuery );
