( function( $ ) {
	"use strict";
    var $reservationForm, $pageContainer, $extraServices, $totalPrice, $roomMessage, $errorMessage, $successMessage, $loading, $priceDetails, $basePrice, 
    	$roomNumber, $roomBtnsPlus, $roomBtnsMinus, $adultNumber, $adultBtnsPlus, $adultBtnsMinus, $childNumber, $childBtnsPlus, $childBtnsMinus,
    	roomID, i18nText, priceList, extraServiceTotalPrice, roomTotalPrice, adultTotalPrice, childTotalPrice, originalTotalPrice, discountBasePrice, finalRoomTotalPrice,
    	$checkinDate, $checkoutDate, checkinDate, checkoutDate, groupCheckinCheckoutFields = false, $checkinCheckoutGroup, $checkinSpan, $checkoutSpan,
    	roomNumber, adultNumber, childNumber, checkinTimestamp, checkoutTimestamp, dayTime = 86400,  
		discounts = false, hasFlexibilePriceRules = false, priceDetailsTmpl, extraServiceListTmpl, 
		$availabilityCalendar, todayTimestamp, disabledStartDates = [], disabledEndDates = [], dateFormat = 'YYYY-MM-DD',
		displayDateFormat, hasExtraServices, hasCustomExtraServices, $totalPriceSection, hasAvailabilityCalendar, messageTimer = false, showGuestsField = false,
		enabledGuestNumber, hasMinGuestSet, hasMaxGuestSet, minGuest, maxGuest, currentMinGuestAllowed = 1, childFieldOnly, adultFieldOnly,
		defaultMaxNumber = parseInt( Number.MAX_SAFE_INTEGER * 2 / 3 , 10 ), currentMaxGuestAllowed = defaultMaxNumber, currentMaxAdultAllowed = defaultMaxNumber, 
		currentMaxChildAllowed = defaultMaxNumber, hasMaxAdultSet = false, hasMaxChildSet = false, maxAdultNumber = -1, maxChildNumber = -1, 
		variablePriceEnabled = false, variablePrices = {}, variableWeekendPriceEnabled = false, groupGuests = false;

	function updateLowestPrice() {
		var lowest = false, originalLowest = false;
		for ( var i = checkinTimestamp; i < checkoutTimestamp; i += dayTime ) {
			if ( priceList[ i ] && ( 'available' == priceList[ i ][ 'status' ] ) ) {
				var currentActualPrice, currentPrices = getCurrentRoomPrices( i ),
					currentOriginalPrice = loftoceanRoomReservation.pricePerPerson
						? add( multiplication( currentPrices[ 'adult_price' ], adultNumber ), multiplication( childNumber, currentPrices[ 'child_price' ] ) )
							: currentPrices[ 'price' ];

				currentActualPrice = priceList[ i ][ 'special_price_rate' ] ? multiplication( currentOriginalPrice, priceList[ i ][ 'special_price_rate' ] ) : currentOriginalPrice;

				if ( ( false === lowest ) || ( Number( currentActualPrice ) < Number( lowest ) ) ) {
					lowest = currentActualPrice;
				}
				if ( ( false === originalLowest ) || ( Number( currentOriginalPrice ) < Number( originalLowest ) ) ) {
					originalLowest = currentOriginalPrice;
				}
			}
		}
		if ( false !== lowest ) {
			var currentBasePrice = '';
			if ( originalLowest > lowest ) {
				currentBasePrice = '<del>' + checkOutputNumberFormat( originalLowest ) + '</del> <span class="sale">' + checkOutputNumberFormat( lowest ) + '</span>';
			} else {
				currentBasePrice = checkOutputNumberFormat( lowest );
			}
			$basePrice.html( currentBasePrice );
		}
	}
	function updateRoomMessage() {
		if ( ! $roomMessage.length ) return;

		var lowest = false, failed = false;
		clearTimeout( messageTimer );
		$roomMessage.removeClass( 'show' );
		$roomNumber.removeData( 'max' );
		for ( var i = checkinTimestamp; i < checkoutTimestamp; i += dayTime ) {
			if ( priceList[ i ] && ( 'available' == priceList[ i ][ 'status' ] ) && ( ! ! priceList[ i ][ 'available_number' ] ) ) {
				if ( ( false === lowest ) || ( Number( priceList[ i ][ 'available_number' ] ) < lowest ) ) {
					lowest = Number( priceList[ i ][ 'available_number' ] );
				}
			} else {
				failed = true;
				break;
			}
		}
		if ( ( ! failed ) && ( false !== lowest ) && ( lowest > 0 ) ) {
			$roomNumber.data( 'max', lowest );
			if ( roomNumber > lowest ) {
				$roomMessage.find( '.room-error-limit-number' ).text( lowest );
				$roomNumber.val( lowest - 1 ).siblings( '.plus' ).trigger( 'click' );
				$roomMessage.addClass( 'show' );
				messageTimer = setTimeout( function() { $roomMessage.removeClass( 'show' ); }, 3000 );
			}
		}
		checkRoomNumberField();
	}
	function checkRoomNumberField() {
		var roomNumberMin = ( 'undefined' != typeof $roomNumber.data( 'min' ) ) && ( ! isNaN( $roomNumber.data( 'min' ) ) ) ? $roomNumber.data( 'min' ) : 1,
			roomNumberMax = ( 'undefined' != typeof $roomNumber.data( 'min' ) ) && ( ! isNaN( $roomNumber.data( 'min' ) ) ) ? $roomNumber.data( 'max' ) : defaultMaxNumber;
		
		( roomNumber > roomNumberMin ) ? $roomBtnsMinus.removeClass( 'disabled' ).data( 'status', '' ) : $roomBtnsMinus.addClass( 'disabled' ).data( 'status', 'disabled' );
		( roomNumber < roomNumberMax ) ? $roomBtnsPlus.removeClass( 'disabled' ).data( 'status', '' ) : $roomBtnsPlus.addClass( 'disabled' ).data( 'status', 'disabled' );
	}
	function updatePriceDetails() {
		var roomList = [], totalPrice = add( finalRoomTotalPrice, extraServiceTotalPrice ),
			data = {
				'totalBasePrice': checkOutputNumberFormat( originalTotalPrice, true ),
				'nights': ( checkoutTimestamp - checkinTimestamp ) / dayTime,
				'totalPrice': checkOutputNumberFormat( totalPrice, true ),
				'totalOriginalPrice': totalPrice
			};
		if ( extraServiceTotalPrice ) {
			data.extraService = checkOutputNumberFormat( extraServiceTotalPrice, true );
		}
		if ( ( false !== discounts ) && discounts[ 'discount' ][ 'base_percentage' ] ) {
			Object.keys( discounts[ 'discount' ][ 'details' ] ).forEach( function( key ) {
				var discountItem = discounts[ 'discount' ][ 'details' ][ key ];
				data[ key ] = ( '-' + checkOutputNumberFormat( multiplication( originalTotalPrice, discountItem[ 'discount' ] ) ) );
			} );
		}

		for ( var i = checkinTimestamp; i < checkoutTimestamp; i += dayTime ) {
			if ( priceList[ i ] ) {
				if ( 'available' == priceList[ i ][ 'status' ] ) {
					var rate = priceList[ i ][ 'special_price_rate' ] ? priceList[ i ][ 'special_price_rate' ] : 1, currentPrices = getCurrentRoomPrices( i ),
						originalPrice = loftoceanRoomReservation.pricePerPerson
							? add( multiplication( currentPrices[ 'adult_price' ], adultNumber ), multiplication( currentPrices[ 'child_price' ], childNumber ) )
								: multiplication( currentPrices[ 'price' ], roomNumber );
					roomList.push( {
						'date': moment( priceList[ i ][ 'start' ] ).format( displayDateFormat ),
						'originalPrice': checkOutputNumberFormat( originalPrice, true ),
						'price': checkOutputNumberFormat( multiplication( originalPrice, rate ), true )
					} );
				} else {
					roomList.push( {
						'date': moment( priceList[ i ][ 'start' ] ).format( displayDateFormat ),
						'price': false
					} );
				}
			}
		}
		data.rooms = roomList;
		checkTaxes( totalPrice, data );
		$priceDetails.html( '' ).append( priceDetailsTmpl( data ) );
        $totalPrice.html( data.totalPrice );
	}
	function showDefaultPriceDetail( currentBasePrice ) {
		var roomList = [], data = {
				'totalBasePrice': checkOutputNumberFormat( currentBasePrice, true ),
				'nights': ( checkoutTimestamp - checkinTimestamp ) / dayTime,
				'totalPrice': checkOutputNumberFormat( currentBasePrice, true ),
				'totalOriginalPrice': currentBasePrice
			};

		if ( ( false !== discounts ) && discounts[ 'discount' ][ 'base_percentage' ] ) {
			Object.keys( discounts[ 'discount' ][ 'details' ] ).forEach( function( key ) {
				var discountItem = discounts[ 'discount' ][ 'details' ][ key ];
				data[ key ] = ( '-' + checkOutputNumberFormat( multiplication( currentBasePrice, discountItem[ 'discount' ] ) ) );
			} );
			data[ 'totalOriginalPrice' ] = multiplication( currentBasePrice, discounts.totleDiscount );
			data[ 'totalPrice' ] = checkOutputNumberFormat( data[ 'totalOriginalPrice' ], true );
		}

		for ( var i = checkinTimestamp; i < checkoutTimestamp; i += dayTime ) {
			if ( priceList[ i ] ) {
				if ( 'available' == priceList[ i ][ 'status' ] ) {
					var rate = priceList[ i ][ 'special_price_rate' ] ? priceList[ i ][ 'special_price_rate' ] : 1, currentPrices = getCurrentRoomPrices( i ),
						originalPrice = loftoceanRoomReservation.pricePerPerson
							? add( multiplication( currentPrices[ 'adult_price' ], adultNumber ), multiplication( currentPrices[ 'child_price' ], childNumber ) )
								: multiplication( currentPrices[ 'price' ], roomNumber );
					roomList.push( {
						'date': priceList[ i ][ 'start' ],
						'originalPrice': checkOutputNumberFormat( originalPrice, true ),
						'price': checkOutputNumberFormat( multiplication( originalPrice, rate ), true )
					} );
				} else {
					roomList.push( {
						'date': priceList[ i ][ 'start' ],
						'price': false
					} );
				}
			}
		}
		data.rooms = roomList;
		checkTaxes( currentBasePrice, data );
		$priceDetails.html( '' ).append( priceDetailsTmpl( data ) );
	}
	function afterCheckinCheckoutChanged() {
		checkinDate = $checkinDate.data( 'value' );
		checkoutDate = $checkoutDate.data( 'value' );
		calculateRoomTotalPrice();
		calculateExtraServiceTotalPrice();
		updateRoomMessage();
		updateLowestPrice();
		updateTotalPrice();
	}
	function checkFlexiblePriceRules() {
		if ( hasFlexibilePriceRules ) {
			var data = { 'roomID': loftoceanRoomReservation.roomID, 'checkin': checkinTimestamp, 'checkout': checkoutTimestamp, 'action': loftoceanRoomReservation.getFlexiblePriceRuleAjaxAction };
			$errorMessage.html( '' );
			$successMessage.html( '' );
			$loading.addClass( 'loading' );
			$.ajax( loftoceanRoomReservation.ajaxURL, { 'method': 'GET', 'data': data } ).done( function( data, status ) {
				data = data ? JSON.parse( data ) : {};
				discounts = ( data && data.status && ( 1 == data.status ) ) ? data.discount : false;
			} ).fail( function() {
				discounts = false;
			} ).always( function() {
				afterCheckinCheckoutChanged();
				$loading.removeClass( 'loading' );
			} );
		} else {
			afterCheckinCheckoutChanged();
		}
	}

    function calculateExtraServiceTotalPrice() {
        var $services = $reservationForm.find( '.cs-form-group.cs-extra-service-group .extra-service-switcher:checked' ), servicePriceSum = 0;
        if ( $services.length ) {
            $services.each( function() {
                var $parent = $( this ).parent(), serviceID = 'extra_service_' + $( this ).val(),
                    servicePrice = $parent.find( 'input[name="extra_service_price[' + serviceID + ']"]' ).val(),
                    serviceCalculatingMethod = $parent.find( 'input[name="extra_service_calculating_method[' + serviceID + ']"]' ).val();

                switch ( serviceCalculatingMethod ) {
                    case 'custom':
                        var customQuantity = $parent.parent().find( 'input[name="extra_service_quantity[' + serviceID + ']"]' ).val();
                        servicePriceSum = add( servicePriceSum, multiplication( servicePrice, customQuantity ) );
                        break;
					case 'auto_custom':
						var customQuantity = $parent.parent().find( 'input[name="extra_service_quantity[' + serviceID + ']"]' ).val();
						servicePriceSum = add( servicePriceSum, multiplication( multiplication( servicePrice, customQuantity ), ( checkoutTimestamp - checkinTimestamp ) / dayTime ) );
						break;
                    case 'auto':
                        var autoCalculatingUnit = $parent.find( 'input[name="extra_service_auto_calculating_unit[' + serviceID + ']"]' ).val();
						if ( [ 'night-room' ].includes( autoCalculatingUnit ) ) {
                            servicePrice = multiplication( servicePrice, parseInt( roomNumber, 10 ) );
                        }
                        if ( [ 'person', 'night-person' ].includes( autoCalculatingUnit ) ) {
							var $customAdultPrice = $parent.find( 'input[name="extra_service_auto_calculating_custom_adult_price[' + serviceID + ']"]' ),
								$customChildPrice = $parent.find( 'input[name="extra_service_auto_calculating_custom_child_price[' + serviceID + ']"]' );
							if ( $customAdultPrice.length && $customChildPrice.length ) {
								var customAdultPrice = $customAdultPrice.val() ? $customAdultPrice.val() : 0,
									customChildPrice = $customChildPrice.val() ? $customChildPrice.val() : 0;
								servicePrice = add( multiplication( customAdultPrice, parseInt( adultNumber, 10 ) ), multiplication( customChildPrice, parseInt( childNumber , 10 ) ) );
							} else {
                            	servicePrice = multiplication( servicePrice, ( parseInt( adultNumber, 10 ) + parseInt( childNumber , 10 ) ) );
							}
                        }
                        if ( [ 'night', 'night-person', 'night-room' ].includes( autoCalculatingUnit ) ) {
                            servicePrice = multiplication( servicePrice, ( checkoutTimestamp - checkinTimestamp ) / dayTime );
                        }

                        servicePriceSum = add( servicePriceSum, servicePrice );
                        break;
                    default:
                        servicePriceSum = add( servicePriceSum, servicePrice );
                }
            } );
        }
        extraServiceTotalPrice = servicePriceSum;
    }
    function calculateRoomTotalPrice() {
        var startTime = new Date( checkinDate ), endTime = new Date( checkoutDate ),
			startTimestamp = getTimeStamp( startTime ), endTimestamp = getTimeStamp( endTime ),
            priceSum = 0, adultPriceSum = 0, childPriceSum = 0;
		if ( typeof priceList[ endTimestamp - dayTime ] == 'undefined'  ) {
			startTime.setDate( startTime.getDate() - 15 );
			endTime.setDate( endTime.getDate() + 15 );
			getRemotePriceList( getDateValue( startTime ), getDateValue( endTime ) );
		} else {
	        for ( var i = startTimestamp; i < endTimestamp; i += dayTime ) {
	            if ( priceList[ i ] && ( 'available' == priceList[ i ][ 'status' ] ) ) {
					var rate = priceList[ i ][ 'special_price_rate' ] ? priceList[ i ][ 'special_price_rate' ] : 1,
						currentPrices = getCurrentRoomPrices( i );

	                priceSum = add( priceSum, multiplication( currentPrices[ 'price' ], rate ) );
					adultPriceSum = add( adultPriceSum, multiplication( currentPrices[ 'adult_price' ], rate ) );
					childPriceSum = add( childPriceSum, multiplication( currentPrices[ 'child_price' ], rate ) );
	            }
	        }
	        roomTotalPrice = priceSum;
			adultTotalPrice = adultPriceSum;
			childTotalPrice = childPriceSum;
		}
    }
    function getCurrentRoomPrices( indexTime ) {
    	var returnPrices = { 
    		'price': priceList[ indexTime ][ 'price' ], 
    		'adult_price': priceList[ indexTime ][ 'adult_price' ], 
    		'child_price': priceList[ indexTime ][ 'child_price' ] 
    	};

    	if ( variablePriceEnabled ) {
    		var guestNumber = getNumber( adultNumber ) + getNumber( childNumber ), 
				propertyNames = groupGuests ? [ 'adult' + adultNumber + '-child' + childNumber, 'adult' + adultNumber + '-child', 'adult-child' + childNumber, 'adult-child' ] : [ 'guests' + guestNumber, 'guests' ];
    		propertyNames.every( function( propertyName ) {
    			if ( variablePrices.hasOwnProperty( propertyName ) ) {
    				var prices = variablePrices[ propertyName ]; 
    				if ( 'per_person' == loftoceanRoomReservation.variablePrices.mode ) {
	    				if ( groupGuests ) {
	    					[ 'adult_price', 'child_price' ].forEach( function( priceProperty ) {
	    						var weekendPriceProperty = 'weekend_' + priceProperty;
	    						if ( ( 'yes' == priceList[ indexTime ][ 'is_weekend' ] ) && ( ! isNaN( prices[ weekendPriceProperty ] ) ) && ( prices[ weekendPriceProperty ] > -1 ) ) {
	    							returnPrices[ priceProperty ] = prices[ weekendPriceProperty ];
	    						} else if ( ( ! isNaN( prices[ priceProperty ] ) ) && ( prices[ priceProperty ] > -1 ) ) {
	    							returnPrices[ priceProperty ] = prices[ priceProperty ];
	    						}
	    					} );
	    				} else {
	    					var priceProperty = 'price', weekendPriceProperty = 'weekend_price';
	    					if ( ( 'yes' == priceList[ indexTime ][ 'is_weekend' ] ) && ( ! isNaN( prices[ weekendPriceProperty ] ) ) && ( prices[ weekendPriceProperty ] > -1 ) ) {
								returnPrices[ 'adult_price' ] = prices[ weekendPriceProperty ];
								returnPrices[ 'child_price' ] = prices[ weekendPriceProperty ];
							} else if ( ( ! isNaN( prices[ priceProperty ] ) ) && ( prices[ priceProperty ] > -1 ) ) {
								returnPrices[ 'adult_price' ] = prices[ priceProperty ];
								returnPrices[ 'child_price' ] = prices[ priceProperty ];
							}
	    				}
	    			} else {
	    				var priceProperty = 'price', weekendPriceProperty = 'weekend_price';
    					if ( ( 'yes' == priceList[ indexTime ][ 'is_weekend' ] ) && ( ! isNaN( prices[ weekendPriceProperty ] ) ) && ( prices[ weekendPriceProperty ] > -1 ) ) {
							returnPrices[ 'price' ] = prices[ weekendPriceProperty ];
						} else if ( ( ! isNaN( prices[ priceProperty ] ) ) && ( prices[ priceProperty ] > -1 ) ) {
							returnPrices[ 'price' ] = prices[ priceProperty ];
						}
	    			}
    				return false;
    			}
    			return true;
    		} ); 
    	}
    	return returnPrices;
    }
    function redirectToCartPage() {
        window.location.href = loftoceanRoomReservation.cartPage;
    }
    function getTimeStamp( date ) {
        if ( typeof date != 'undefined' && ! date.getTime ) {
            date = new Date();
        }
        return Math.floor( date.getTime() / dayTime / 1000 ) * dayTime;
    }
    function updateTotalPrice() {
		var roomPriceSum = loftoceanRoomReservation.pricePerPerson
			? add( multiplication( adultTotalPrice, adultNumber ), multiplication( childTotalPrice, childNumber ) )
				: multiplication( roomTotalPrice, roomNumber );
		if ( ( false !== discounts ) && discounts.totleDiscount ) {
			originalTotalPrice = roomPriceSum;
			roomPriceSum = multiplication( roomPriceSum, discounts.totleDiscount );
		} else {
			originalTotalPrice = roomPriceSum;
		}
		finalRoomTotalPrice = roomPriceSum;

		updatePriceDetails();
    }
    function getRemotePriceList( startTime, endTime ) {
		if ( roomID && startTime && endTime ) {
			$.ajax( {
				url: wpApiSettings.root + 'loftocean/v1/get_room_availability/' + ( new Date() ).getTime(),
				data: { 'rid': roomID, 'start': startTime, 'end': endTime },
				type: 'POST',
				success: function ( data, status ) {
					if ( typeof data == "object" ) {
						data.forEach( function( item ) {
							priceList[ item[ 'id' ] ] = item;
						} );
			            calculateRoomTotalPrice();
			            calculateExtraServiceTotalPrice();
			            updateTotalPrice();
					}
				}, error: function ( e ) {
					console.log( i18nText.getRemotePriceListErrorMessage );
				}
			} );
		}
    }
    function checkOutputNumberFormat( num, ignoreSymbal ) {
		var m = 0, tmpNum = 0, numStr = '', thousand = 1000;
        num = ( 'undefined' == typeof num ) ? 0 : ( isNumber( num ) ? num : 0 );
		try { m = ( '' + num ).split( '.' )[1].length; } catch( e ) { m = 0; }
        num = Number( num ).toFixed( m ? Math.max( 0, loftoceanRoomReservation.currencySettings.precision ) : 0 );
		num = ( '' + num ).split( '.' );

		numStr = Number( num[ 0 ] );
		if ( loftoceanRoomReservation.currencySettings.thousandSeparator ) {
			tmpNum = Number( num[ 0 ] );
			if ( tmpNum > thousand ) {
				numStr = ( tmpNum + '' ).substr( -3 )
				tmpNum = Math.floor( tmpNum / thousand );
				while ( tmpNum > thousand ) {
					numStr = ( tmpNum + '' ).substr( -3 ) + loftoceanRoomReservation.currencySettings.thousandSeparator + numStr;
					tmpNum = Math.floor( tmpNum / thousand );
				}
				if ( tmpNum > 0 ) {
					numStr = tmpNum + loftoceanRoomReservation.currencySettings.thousandSeparator + numStr;
				}
			}
		}
		if ( ( num.length > 1 ) && ( Number( num[ 1 ] ) > 0 ) && loftoceanRoomReservation.currencySettings.precision && loftoceanRoomReservation.currencySettings.decimalSeparator ) {
			numStr += loftoceanRoomReservation.currencySettings.decimalSeparator + num[ 1 ];
		}

		return ignoreSymbal ? numStr : loftoceanRoomReservation.currency[ 'left' ] + numStr + loftoceanRoomReservation.currency[ 'right' ];
    }
    function addLeadingZero( num ) {
        return num > 9 ? num : '0' + num;
    }
	function getDateValue( date ) {
		return ( 'Invalid Date' === date.toString() ) ? '' : date.getFullYear() + '-' + addLeadingZero( date.getMonth() + 1 ) + '-' + addLeadingZero( date.getDate() );
	}

	function isNumber( value ) {
		return ( ! isNaN( value ) ) && isFinite( value );
	}

	function add( arg1, arg2 ) {
		var m1, m2, m, sum = 0;
		try { m1 = ( '' + arg1 ).split( '.' )[1].length; } catch( e ) { m1 = 0; }
		try { m2 = ( '' + arg2 ).split( '.' )[1].length; } catch( e ) { m2 = 0; }
		m = Math.max( m1, m2 );
		sum = ( arg1 * Math.pow( 10, m ) + arg2 * Math.pow( 10, m ) ) / Math.pow( 10, m );
		return sum.toFixed( m ) ;
	}
	function subtraction( arg1, arg2 ) {
		return add( arg1, ( - arg2 ) );
	}
	function multiplication ( arg1, arg2 ) {
		var m1, m2, result = 0;
		try { m1 = ( '' + arg1 ).split( '.' )[1].length; } catch( e ) { m1 = 0; }
		try { m2 = ( '' + arg2 ).split( '.' )[1].length; } catch( e ) { m2 = 0; }
		result = ( arg1 * Math.pow( 10, m1 ) ) * ( arg2 * Math.pow( 10, m2 ) ) / Math.pow( 10, ( m1 + m2 ) );
		return result.toFixed( m1 + m2 );
	}

	function checkDateAvailability( date, drp ) {
		date = date.format( dateFormat );
		if ( ( typeof drp === 'undefined' || null === drp.startDate || null !== drp.endDate ) && disabledStartDates.includes( date ) ) {
			return [ false, '', '' ];
		} else {
			var notSetEndDateYet = ( typeof drp !== 'undefined' && null !== drp.startDate && null === drp.endDate );
			if ( notSetEndDateYet ) {
				if ( moment( date ).isBefore( drp.startDate ) ) return [ false, '', '' ];

				if( disabledEndDates.length ) {
					if ( disabledEndDates.includes( date ) ) return [ false, '', '' ];

					var currentVerifyDate = drp.startDate.clone(), validEndDate = moment( date );
					currentVerifyDate.add( '1', 'day' );
					while ( currentVerifyDate.isBefore( validEndDate ) ) {
						if ( disabledStartDates.includes( currentVerifyDate.format( dateFormat ) ) ) {
							return [ false, '', '' ];
						}
						currentVerifyDate.add( '1', 'day' );
					}
				}
			}

			var d = new Date( date ), dayOfWeek = 'day' + d.getDay(), currentTimstamp = getTimeStamp( d ), classes = [], messages = [];
			if ( loftoceanRoomReservation.unavailableDates ) {
				if ( loftoceanRoomReservation.unavailableDates[ 'in_advance' ] && loftoceanRoomReservation.unavailableDates[ 'in_advance' ][ 'length' ] ) {
					for ( let i = 0; i < loftoceanRoomReservation.unavailableDates[ 'in_advance' ].length; i ++ ) {
						let inAdvanceItem = loftoceanRoomReservation.unavailableDates[ 'in_advance' ][ i ];
						if ( ( 'all' == inAdvanceItem.id ) || ( ( inAdvanceItem.start <= currentTimstamp ) && ( currentTimstamp <= inAdvanceItem.end ) ) ) {
							if ( inAdvanceItem.min && ( ( ( currentTimstamp - todayTimestamp ) / dayTime ) < inAdvanceItem.min ) ) {
								classes.push( 'disabled', 'checkin-unavailable' );
							}
							if ( inAdvanceItem.max && ( ( ( currentTimstamp - todayTimestamp ) / dayTime ) > inAdvanceItem.max ) ) {
								classes.push( 'checkin-unavailable' );
								if ( ( 'undefined' === typeof drp ) || ( null === drp.startDate || null !== drp.endDate ) ) {
									classes.push( ' disabled' );
								}
							}
							break;
						}
					}
				}
				if ( loftoceanRoomReservation.unavailableDates.checkin && loftoceanRoomReservation.unavailableDates.checkin.length ) {
					for ( let i = 0; i < loftoceanRoomReservation.unavailableDates.checkin.length; i++ ) {
						let disabledCheckItem = loftoceanRoomReservation.unavailableDates.checkin[ i ];
						if ( ( 'all' == disabledCheckItem.id ) || ( ( disabledCheckItem.start <= currentTimstamp ) && ( currentTimstamp <= disabledCheckItem.end ) ) ) {
							if ( disabledCheckItem.days.includes( dayOfWeek ) ) {
								classes.push( 'no-checkin', 'checkin-unavailable' );
								messages.push( i18nText.noCheckin );
							}
							break;
						}
					}
				}
				if ( loftoceanRoomReservation.unavailableDates.checkout && loftoceanRoomReservation.unavailableDates.checkout.length ) {
					for ( let i = 0; i < loftoceanRoomReservation.unavailableDates.checkout.length; i ++ ) {
						let disabledCheckItem = loftoceanRoomReservation.unavailableDates.checkout[ i ];
						if ( ( 'all' == disabledCheckItem.id ) || ( ( disabledCheckItem.end >= currentTimstamp ) && ( currentTimstamp >= disabledCheckItem.start ) ) ) {
							if ( disabledCheckItem.days.includes( dayOfWeek ) ) {
								classes.push( 'no-checkout', 'checkout-unavailable' );
								messages.push( i18nText.noCheckout );
							}
							break;
						}
					}
				}
				if ( notSetEndDateYet ) {
					if ( loftoceanRoomReservation.unavailableDates[ 'stay_length' ] && loftoceanRoomReservation.unavailableDates[ 'stay_length' ][ 'length' ] ) {
						var startDateTimestamp = getTimeStamp( new Date( drp.startDate.format( dateFormat ) ) ),
							startDayOfWeek = 'day' + drp.startDate.day();
						if ( currentTimstamp > startDateTimestamp ) {
							for ( let i = 0; i < loftoceanRoomReservation.unavailableDates[ 'stay_length' ][ 'length' ]; i ++ ) {
								let stayLengthItem = loftoceanRoomReservation.unavailableDates[ 'stay_length' ][ i ];
								if ( ( 'all' == stayLengthItem.id ) || ( ( stayLengthItem.end >= startDateTimestamp ) && ( startDateTimestamp >= stayLengthItem.start ) ) ) {
									var daysAfterStart = ( currentTimstamp - startDateTimestamp ) / dayTime;
									if ( stayLengthItem.rules[ startDayOfWeek ] ) {
										if ( stayLengthItem.rules[ startDayOfWeek ][ 'min' ] && ( daysAfterStart < stayLengthItem.rules[ startDayOfWeek ][ 'min' ] ) ) {
											classes.push( 'minimal-stay-unavailable', 'checkout-unavailable' );
											messages.push( stayLengthItem.rules[ startDayOfWeek ][ 'min' ] + i18nText.minimum );
										}
										if ( stayLengthItem.rules[ startDayOfWeek ][ 'max' ] && ( daysAfterStart > stayLengthItem.rules[ startDayOfWeek ][ 'max' ] ) ) {
											classes.push( 'off', 'disabled', 'maximal-stay-unavailable', 'checkout-unavailable' );
											messages.push( stayLengthItem.rules[ startDayOfWeek ][ 'max' ] + i18nText.maximum );
										}
									}
									break;
								}
							}
						}
					}
				}
			}
			return [ true, classes.length ? classes.join( ' ' ) : '', messages.length ? messages.join( ', ' ) : '' ];
		}
		return [ true, '', '' ];
	}

	function updateBookingDates( startDate, endDate ) {
		$checkinDate.val( moment( startDate ).format( displayDateFormat ) ).data( 'value', startDate );
		$checkoutDate.val( moment( endDate ).format( displayDateFormat ) ).data( 'value', endDate );
		if ( groupCheckinCheckoutFields ) {
			$checkinSpan.text( moment( startDate ).format( displayDateFormat ) );
			$checkoutSpan.text( moment( endDate ).format( displayDateFormat ) ).css( 'opacity', '' );
		}

		checkinDate = startDate;
        checkinTimestamp = getTimeStamp( new Date( startDate ) );
        checkoutDate = endDate;
        checkoutTimestamp = getTimeStamp( new Date(  endDate ) );

		checkExtraServiceList( checkinTimestamp, checkoutTimestamp );
		checkFlexiblePriceRules();
	}
	function getDefaultAvailableDates( checkin, checkout ) {
		var i = 0, j = 0, max = 70, currentStartDate = checkin.clone(), currentEndDate = null;
		while ( i ++ < max ) {
			var startDateStatus = checkDateAvailability( currentStartDate );
			if ( ( ! startDateStatus[0] ) || ( startDateStatus[1] && startDateStatus[1].split( ' ' ).includes( 'checkin-unavailable' ) ) ) {
				currentStartDate.add( '1', 'day' );
				continue;
			}

			j = 0; currentEndDate = currentStartDate.clone().add( '1', 'day' );
			var checkoutValidationArgs = { 'startDate': currentStartDate, 'endDate': null };
			while ( j ++ < max ) {
				var endDateStatus = checkDateAvailability( currentEndDate, checkoutValidationArgs );
				if ( ( ! endDateStatus[0] ) || ( endDateStatus[1] && endDateStatus[ 1 ].split( ' ' ).includes( 'checkout-unavailable' ) ) ) {
					currentEndDate.add( '1', 'day' );
					continue;
				}
				return { 'checkin': currentStartDate.format( dateFormat ), 'checkout': currentEndDate.format( dateFormat ) };
			}
			currentStartDate.add( '1', 'day' );
		}
		return { 'checkin': checkin.format( dateFormat ), 'checkout': checkout.format( dateFormat ) };
	}
	function checkDefaultSettings() {
		if ( loftoceanRoomReservation.passParamsFromSearchResultPage ) {
			var startDate = loftoceanRoomReservation.searchResultParams.checkin,
				endDate = loftoceanRoomReservation.searchResultParams.checkout,
				start = moment( startDate ), end = moment( endDate ),
				startDateStatus = checkDateAvailability( start );
			if ( startDateStatus[0] && ! ( startDateStatus[1] && startDateStatus[1].split( ' ' ).includes( 'checkin-unavailable' ) ) ) {
				var checkoutValidationArgs = { 'startDate': start, 'endDate': null },
					endDateStatus = checkDateAvailability( end, checkoutValidationArgs );
				if ( endDateStatus[0] && ! ( endDateStatus[1] && endDateStatus[ 1 ].split( ' ' ).includes( 'checkout-unavailable' ) ) ) {
					return { 'checkin': startDate, 'checkout': endDate };
				}
			}
		}

		return getDefaultAvailableDates( moment(), moment().add( '1', 'day' ) );
	}
	function checkSelectedDates( checkin, checkout ) {
		var startDateStatus = checkDateAvailability( checkin );
		if ( startDateStatus[0] && ! ( startDateStatus[1] && startDateStatus[1].split( ' ' ).includes( 'checkin-unavailable' ) ) ) {
			var checkoutValidationArgs = { 'startDate': checkin, 'endDate': null },
				endDateStatus = checkDateAvailability( checkout, checkoutValidationArgs );
			if ( endDateStatus[0] && ! ( endDateStatus[1] && endDateStatus[ 1 ].split( ' ' ).includes( 'checkout-unavailable' ) ) ) {
				return { 'checkin': checkin.format( dateFormat ), 'checkout': checkout.format( dateFormat ) };
			}
		}

		return getDefaultAvailableDates( checkin, checkout );
	}
	function checkExtraServiceList( checkin, checkout ) {
		if ( hasExtraServices && hasCustomExtraServices ) {
			var currentList = [];
			hasCustomExtraServices = false;
			loftoceanRoomReservation.extraServices.forEach( function( item ) {
				if ( ( '' !== item.effective_time ) && item.custom_effective_time_slots.length ) {
					hasCustomExtraServices = true;
					var passDeactivated = true, isActivated = ( 'activated' == item.effective_time );
					for ( let i = 0; i < item.custom_effective_time_slots.length; i ++ ) {
						var cets = item.custom_effective_time_slots[ i ];
						if ( ( ( ! cets.start_timestamp ) || ( cets.start_timestamp <= checkin ) )
							&& ( ( ! cets.end_timstamp ) || ( cets.end_timstamp >= checkout ) ) ) {
							if ( isActivated ) {
								currentList.push( $.extend( {}, item ) );
							} else {
								passDeactivated = false;
							}
							break;
						}
					}
					if ( ( ! isActivated ) && passDeactivated ) {
						currentList.push( $.extend( {}, item ) );
					}
				} else {
					currentList.push( $.extend( {}, item ) );
				}
			} );
			var $extraServiceList = $( '#secondary .cs-form-group.cs-extra-service-group' );
			$extraServiceList.length ? $extraServiceList.remove() : '';
			if ( currentList.length ) {
				$totalPriceSection.before( extraServiceListTmpl( { 'currency': loftoceanRoomReservation.currency, 'services': currentList } ) );
			}
		}
	}
	function checkTaxes( price, data ) {
		if ( loftoceanRoomReservation.isTaxEnabled ) {
			if ( loftoceanRoomReservation.taxIncluded ) {
				var taxes = calculateIncludedTax( price );
				data.tax = checkOutputNumberFormat( taxes.totalTax );
				data.taxDetails = taxes.taxDetails;
			} else {
				var taxes = calculateExcludeTax( price );
				data.tax = checkOutputNumberFormat( taxes.totalTax );
				data.taxDetails = taxes.taxDetails;
				data.beforeTax = data.totalPrice;
				data.totalPrice = checkOutputNumberFormat( add( data.totalOriginalPrice, taxes.totalTax ), true );
			}
		}
	}
	function calculateIncludedTax( price ) {
		var precision = add( loftoceanRoomReservation.currencySettings.precision, 2 ),
			taxes = loftoceanRoomReservation.taxRate, taxDetails = [],
			priceBeforeTax = price, currentPrice = 0;
		if ( taxes[ 'reversed_compound_rates' ] && taxes[ 'reversed_compound_rates' ].length ) {
			for ( var i = 0; i < taxes[ 'reversed_compound_rates' ].length; i ++ ) {
				currentPrice = priceBeforeTax;
				priceBeforeTax = multiplication( priceBeforeTax, ( 100 / ( 100 + taxes[ 'reversed_compound_rates' ][ i ][ 'rate' ] ) ) );
				priceBeforeTax = Number( priceBeforeTax ).toFixed( precision );
				taxDetails.unshift( { 'tax': checkOutputNumberFormat( subtraction( currentPrice, priceBeforeTax ) ), 'label': taxes[ 'reversed_compound_rates' ][ i ][ 'label' ] } );
			}
		}
		if ( taxes[ 'regular_rates' ] && taxes[ 'regular_rates' ].length ) {
			var rateSum = 100;
			for ( var i = 0; i < taxes[ 'regular_rates' ].length; i ++ ) {
				rateSum = add( rateSum, taxes[ 'regular_rates' ][ i ][ 'rate' ] );
			}
			priceBeforeTax = multiplication( priceBeforeTax, 100 / rateSum );
			priceBeforeTax = Number( priceBeforeTax ).toFixed( precision );

			for ( i -= 1; i >= 0; i -- ) {
				taxDetails.push( { 'tax': checkOutputNumberFormat( multiplication( priceBeforeTax, taxes[ 'regular_rates' ][ i ][ 'rate' ] / 100 ) ), 'label': taxes[ 'regular_rates' ][ i ][ 'label' ] } );
			}
		}
		return { 'totalTax': subtraction( price, priceBeforeTax ), 'taxDetails': taxDetails };
	}
	function calculateExcludeTax( price ) {
		var taxes = loftoceanRoomReservation.taxRate,
			priceForCompound = price, totalTax = 0,
			taxDetails = [];
		if ( taxes[ 'regular_rates' ] && taxes[ 'regular_rates' ].length ) {
			var currentTax = 0
			for ( var i = 0; i < taxes[ 'regular_rates' ].length; i ++ ) {
				currentTax = multiplication( price, ( taxes[ 'regular_rates' ][ i ][ 'rate' ] / 100 ) );
				taxDetails.push( { 'tax': checkOutputNumberFormat( currentTax ), 'label': taxes[ 'regular_rates' ][ i ][ 'label' ] } );
				totalTax = add( totalTax, currentTax );
			}
			priceForCompound = add( price, totalTax );
		}
		if ( taxes[ 'compound_rates' ] && taxes[ 'compound_rates' ].length ) {
			var compoundTax = 0;
			for ( var i = 0; i < taxes[ 'compound_rates' ].length; i ++ ) {
				compoundTax = multiplication( priceForCompound, ( taxes[ 'compound_rates' ][ i ][ 'rate' ] / 100 ) );
				totalTax = add( totalTax, compoundTax );
				priceForCompound = add( priceForCompound, compoundTax );
				taxDetails.push( { 'tax': checkOutputNumberFormat( compoundTax ), 'label': taxes[ 'compound_rates' ][ i ][ 'label' ] } );
			}
		}
		return { 'totalTax': totalTax, 'taxDetails': taxDetails };
	}
	function checkChildOnlyField() {
		$adultNumber.data( 'min', 0 ).val( 1 ).siblings( '.minus' ).trigger( 'click', true );
		$childNumber.data( 'min', 1 ).val( 0 ).siblings( '.plus' ).trigger( 'click', true );
	}
	function reCalculateGuestNumber() {
		if ( enabledGuestNumber ) {
			childFieldOnly ? recalculateChildOnlyNumber() : ( adultFieldOnly ? recalculateAdultOnlyNumber() : recalculateNormalGuestNumber() );
		}
	}
	function recalculateChildOnlyNumber() {
		var currentChildNumber = childNumber, updatedChildNumber = false; 

		if ( hasMinGuestSet ) {
			currentMinGuestAllowed = minGuest * roomNumber; 
			if ( currentChildNumber < currentMinGuestAllowed ) {
				currentChildNumber = currentMinGuestAllowed;
				updatedChildNumber = true;
			}
		}
		if ( hasMaxGuestSet ) {
			currentMaxGuestAllowed = maxGuest * roomNumber; 
			if ( currentChildNumber > currentMaxGuestAllowed ) {
				currentChildNumber = currentMaxGuestAllowed;
				updatedChildNumber = true;
			}
		}
		if ( hasMaxChildSet ) {
			currentMaxChildAllowed = maxChildNumber * roomNumber;
			if ( currentChildNumber > currentMaxChildAllowed ) {
				currentChildNumber = currentMaxChildAllowed;
				updatedChildNumber = true;
			}
		}
		if ( updatedChildNumber ) {
			$childNumber.val( currentChildNumber - 1 ).siblings( '.plus' ).trigger( 'click', true );
		}
	}
	function recalculateAdultOnlyNumber() {
		var currentAdultNumber = adultNumber, updatedAdultNumber = false; 

		if ( hasMinGuestSet ) {
			currentMinGuestAllowed = minGuest * roomNumber; 
			if ( currentAdultNumber < currentMinGuestAllowed ) {
				currentAdultNumber = currentMinGuestAllowed;
				updatedAdultNumber = true;
			}
		}
		if ( hasMaxGuestSet ) {
			currentMaxGuestAllowed = maxGuest * roomNumber; 
			if ( currentAdultNumber > currentMaxGuestAllowed ) {
				currentAdultNumber = currentMaxGuestAllowed;
				updatedAdultNumber = true;
			}
		}
		if ( hasMaxChildSet ) {
			currentMaxAdultAllowed = maxChildNumber * roomNumber;
			if ( currentAdultNumber > currentMaxAdultAllowed ) {
				currentAdultNumber = currentMaxAdultAllowed;
				updatedAdultNumber = true;
			}
		}
		if ( updatedAdultNumber ) {
			$adultNumber.val( currentAdultNumber - 1 ).siblings( '.plus' ).trigger( 'click', true );
		}
	}
	function recalculateNormalGuestNumber() {
		var currentAdultNumber = adultNumber, currentChildNumber = childNumber, updatedAdultNumber = false, 
			updatedChildNumber = false, currentTotalGuest = currentAdultNumber + currentChildNumber;

		currentMaxAdultAllowed = hasMaxAdultSet ? maxAdultNumber * roomNumber : currentMaxAdultAllowed;
		currentMaxChildAllowed = hasMaxChildSet ? maxChildNumber * roomNumber : currentMaxChildAllowed; 

		if ( hasMinGuestSet ) {
			currentMinGuestAllowed = minGuest * roomNumber;
			if ( currentTotalGuest < currentMinGuestAllowed ) {
				currentAdultNumber -= currentTotalGuest - currentMinGuestAllowed;
				currentTotalGuest = parseInt( currentAdultNumber, 10 ) + parseInt( currentChildNumber, 10 );
				updatedAdultNumber = true;
			}
		}  
		if ( hasMaxGuestSet ) {
			currentMaxGuestAllowed = maxGuest * roomNumber;
			if ( currentTotalGuest > currentMaxGuestAllowed ) {
				currentChildNumber -= currentTotalGuest - currentMaxGuestAllowed;
				updatedChildNumber = true;
				if ( currentChildNumber < 0 ) {
					currentAdultNumber += currentChildNumber;
					currentChildNumber = 0;
					updatedChildNumber = [ '0', 0 ].includes( childNumber ) ? false : true;
					currentAdultNumber = Math.max( 1, currentAdultNumber );
					updatedAdultNumber = true;
				}
			}
		}
		if ( hasMaxAdultSet && ( currentAdultNumber > currentMaxAdultAllowed ) ) {
			currentChildNumber -= currentMaxAdultAllowed - currentAdultNumber;
			currentAdultNumber = currentMaxAdultAllowed;
			updatedAdultNumber = true;
			updatedChildNumber = true;
		}
		if ( hasMaxChildSet && ( currentChildNumber > currentMaxChildAllowed ) ) {
			currentChildNumber = currentMaxChildAllowed;
			updatedChildNumber = true;
		}
		if ( updatedAdultNumber ) {
			childNumber = currentChildNumber;
			adultNumber = currentAdultNumber - 1;
			$adultNumber.val( adultNumber ).siblings( '.plus' ).trigger( 'click', true );
		}
		if ( updatedChildNumber ) {
			if ( currentChildNumber > 1 ) { 
				childNumber = currentChildNumber - 1;
				$childNumber.val( childNumber ).siblings( '.plus' ).trigger( 'click', true );
			} else {
				childNumber = currentChildNumber + 1;
				$childNumber.val( childNumber ).siblings( '.minus' ).removeClass( 'disabled' ).removeAttr( 'disabled' ).trigger( 'click', true );
			}
		}
	}
	function checkMinusBtnClicked( isCheckingAdult ) {
		if ( ! showGuestsField ) return true;

		var isChild = ( 'undefined' === typeof isCheckingAdult ) || ( ! isCheckingAdult ),
			currentFieldMinNumberAllowed = isChild ? 0 : 1,
			checkedFieldMaxNumberAllowed = isChild ? currentMaxAdultAllowed : currentMaxChildAllowed,
			currentFieldNumber = isChild ? childNumber : adultNumber,
			checkedFiledNumber = isChild ? adultNumber : childNumber,
			currentTotalNumber = parseInt( adultNumber, 10 ) + parseInt( childNumber, 10 );

		if ( currentTotalNumber === currentMinGuestAllowed ) { 
			if ( ( currentFieldNumber > currentFieldMinNumberAllowed ) && ( checkedFiledNumber < checkedFieldMaxNumberAllowed ) ) {
				if ( isChild ) {
					$childNumber.siblings( '.minus' ).trigger( 'click', true );
					$adultNumber.siblings( '.plus' ).trigger( 'click', true );
				} else {
					$adultNumber.siblings( '.minus' ).trigger( 'click', true );
					$childNumber.siblings( '.plus' ).trigger( 'click', true );
				}

				return false;
			}
		}
		return true;
	}
	function validateTotalGuestNumber( guestType, isPlus ) {
		var currentTotalNumber = parseInt( adultNumber, 10 ) + parseInt( childNumber, 10 );
		if ( isPlus ) { 
			return ( currentTotalNumber < currentMaxGuestAllowed ) && validateMaxAdultChildNumber( guestType );
		} else { 
			return ( currentTotalNumber > currentMinGuestAllowed );
		}
	}
	function getNumber( val ) {
		return isNaN( val ) ? 0 : parseInt( val, 10 );
	}
	function validateMaxAdultChildNumber( guestType ) {
		if ( hasMaxAdultSet && ( 'adult' == guestType ) ) {
			var currentAdultNumber = parseInt( adultNumber, 10 );
			return ( currentAdultNumber < currentMaxAdultAllowed );
		}
		if ( hasMaxChildSet && ( 'child' == guestType ) ) {
			var currentChildNumber = parseInt( childNumber, 10 );
			return ( currentChildNumber < currentMaxChildAllowed );
		}

		return true;
	}
	function checkAdultChildFields() {
		var currentAdultNumber = parseInt( adultNumber, 10 ), currentChildNumber = parseInt( childNumber, 10 ),
			currentTotalNumber = parseInt( adultNumber, 10 ) + parseInt( childNumber, 10 );

		if ( currentTotalNumber < currentMaxGuestAllowed ) {
			( hasMaxAdultSet && ( currentAdultNumber >= currentMaxAdultAllowed ) ) ? $adultBtnsPlus.addClass( 'disabled' ).data( 'status', 'disabled' )
				.children().first().attr( 'data-title', i18nText.adult.plus.general.replace( '[number]', currentMaxAdultAllowed ) ).removeClass( 'hide' )
					: $adultBtnsPlus.removeClass( 'disabled' ).data( 'status', '' ).children().addClass( 'hide' );
			
			( hasMaxChildSet && ( currentChildNumber >= currentMaxChildAllowed ) ) ? $childBtnsPlus.addClass( 'disabled' ).data( 'status', 'disabled' )
				.children().first().attr( 'data-title', i18nText.child.plus.general.replace( '[number]', currentMaxChildAllowed ) ).removeClass( 'hide' )
					: $childBtnsPlus.removeClass( 'disabled' ).data( 'status', '' ).children().addClass( 'hide' );
		} else {
			$adultBtnsPlus.addClass( 'disabled' ).data( 'status', 'disabled' )
				.children().first().attr( 'data-title', i18nText.adult.plus.maximum.replace( '[number]', currentMaxGuestAllowed ) ).removeClass( 'hide' );
			$childBtnsPlus.addClass( 'disabled' ).data( 'status', 'disabled' )
				.children().first().attr( 'data-title', i18nText.child.plus.maximum.replace( '[number]', currentMaxGuestAllowed ) ).removeClass( 'hide' );
		}

		$adultBtnsMinus.removeClass( 'cs-hint' );
		$childBtnsMinus.removeClass( 'cs-hint' );
		if ( currentTotalNumber > currentMinGuestAllowed ) {
			var adultFieldMinimumNumber = ( childFieldOnly ? 0 : 1 ),
				childFieldMinimumNumber = ( childFieldOnly ? 1 : 0 );

			( adultNumber > adultFieldMinimumNumber ) ? $adultBtnsMinus.removeClass( 'disabled' ).data( 'status', '' ).children().addClass( 'hide' )
				: $adultBtnsMinus.addClass( 'disabled' ).data( 'status', 'disabled' )
					.children().first().attr( 'data-title', i18nText.adult.minus.general.replace( '[number]', adultFieldMinimumNumber ) ).addClass( 'hide' );

			( childNumber > childFieldMinimumNumber ) ? $childBtnsMinus.removeClass( 'disabled' ).data( 'status', '' ).children().addClass( 'hide' )
				: $childBtnsMinus.addClass( 'disabled' ).data( 'status', 'disabled' )
					.children().first().attr( 'data-title', i18nText.child.minus.general.replace( '[number]', childFieldMinimumNumber ) ).addClass( 'hide' );
		} else {
			$adultBtnsMinus.addClass( 'disabled' ).data( 'status', 'disabled' );
			( adultNumber < 2 ) ? $adultBtnsMinus.children().first().addClass( 'hide' )
				: $adultBtnsMinus.children().first().attr( 'data-title', i18nText.adult.minus.minimum.replace( '[number]', currentMinGuestAllowed ) ).removeClass( 'hide' );

			$childBtnsMinus.addClass( 'disabled' ).data( 'status', 'disabled' );
			( childNumber < ( childFieldOnly ? 2 : 1 ) ) ? $childBtnsMinus.children().first().addClass( 'hide' )
				: $childBtnsMinus.children().first().attr( 'data-title', i18nText.child.minus.minimum.replace( '[number]', currentMinGuestAllowed ) ).removeClass( 'hide' );

			if ( ( currentTotalNumber === currentMinGuestAllowed ) && showGuestsField ) {
				var $targetElems = { 'child': $childBtnsMinus, 'adult': $adultBtnsMinus };
				[ 'child', 'adult' ].forEach( function( field ) {
					var isChild = ( 'child' == field ),
						currentFieldMinNumberAllowed = isChild ? 0 : 1,
						checkedFieldMaxNumberAllowed = isChild ? currentMaxAdultAllowed : currentMaxChildAllowed,
						currentFieldNumber = isChild ? childNumber : adultNumber,
						checkedFiledNumber = isChild ? adultNumber : childNumber;

					if ( ( currentFieldNumber > currentFieldMinNumberAllowed ) && ( checkedFiledNumber < checkedFieldMaxNumberAllowed ) ) {
						$targetElems[ field ].removeClass( 'disabled' ).data( 'status', '' ).addClass( 'cs-hint' )
							.children().first().attr( 'data-title', i18nText[ field ].minus.minimum.replace( '[number]', currentMinGuestAllowed ) ).removeClass( 'hide' );
					}
				} );
			}
		}
	}

	document.addEventListener( 'DOMContentLoaded', function() {
		if ( 'undefined' == loftoceanRoomReservation ) return false;

		var $checkinField, $checkoutField, checkoutFieldOuterHeight = 0;

		$pageContainer = $( 'body' );

		$reservationForm = $( '#secondary .cs-reservation-form' );
		displayDateFormat = loftoceanRoomReservation.displayDateFormat;
		showGuestsField = !! $reservationForm.data( 'guests-field' );

		priceDetailsTmpl = wp.template( 'loftocean-room-price-details' );
		extraServiceListTmpl = wp.template( 'loftocean-room-extra-services' );
		hasExtraServices = loftoceanRoomReservation.extraServices && loftoceanRoomReservation.extraServices.length;
		hasCustomExtraServices = true;

		$basePrice = $( '#secondary .base-price' );
		$loading = $( '#secondary .cs-room-booking' );
		$priceDetails = $( '#secondary .cs-form-price-details' );

		$totalPriceSection = $reservationForm.find( '.cs-form-total-price' );
        $totalPrice = $totalPriceSection.find( '.total-price-number' );
        priceList = loftoceanRoomReservation.priceList;
		roomID = loftoceanRoomReservation.roomID;
		i18nText = loftoceanRoomReservation.i18nText;
		hasFlexibilePriceRules = !! loftoceanRoomReservation.hasFlexibilePriceRules;

        $checkinDate = $reservationForm.find( '.cs-check-in input[name=checkin]' );
    	$checkoutDate = $reservationForm.find( '.cs-check-out input[name=checkout]' );
        $roomNumber = $reservationForm.find( '.cs-rooms input[name=room-quantity]' );
        $roomBtnsPlus = $roomNumber.siblings( '.plus' );
        $roomBtnsMinus = $roomNumber.siblings( '.minus' );
        $adultNumber = $reservationForm.find( '.cs-adults input[name=adult-quantity]' );
        $adultBtnsPlus = $adultNumber.siblings( '.plus' );
        $adultBtnsMinus = $adultNumber.siblings( '.minus' );
        $childNumber = $reservationForm.find( '.cs-children input[name=child-quantity]' );
        $childBtnsPlus = $childNumber.siblings( '.plus' );
        $childBtnsMinus = $childNumber.siblings( '.minus' );
        $checkinField = $checkinDate.closest( '.cs-form-field.cs-check-in' );
        $checkoutField = $checkoutDate.closest( '.cs-form-field.cs-check-out' );
        checkoutFieldOuterHeight = $checkoutField.length ? $checkoutField.outerHeight( true ) : 0;

		$roomMessage = $reservationForm.find( '.cs-form-field.cs-rooms > .cs-form-notice' );
		$errorMessage = $reservationForm.children( '.cs-form-error-message' );
		$successMessage = $reservationForm.children( '.cs-form-success-message' );
		$availabilityCalendar = $( '.room-availability-calendar-wrapper .hidden-calendar' );
		hasAvailabilityCalendar = $availabilityCalendar.length;

		minGuest = parseInt( loftoceanRoomReservation.guestLimitation.min, 10 );
		maxGuest = parseInt( loftoceanRoomReservation.guestLimitation.max, 10 );
		hasMinGuestSet = ( ! isNaN( minGuest ) ) && ( minGuest > 0 );
		hasMaxGuestSet = ( ! isNaN( maxGuest ) ) && ( maxGuest > 0 );
		childFieldOnly = ( ! showGuestsField ) && ( ! $reservationForm.find( '.cs-form-field.cs-children' ).hasClass( 'hide' ) );
		adultFieldOnly = ( ! showGuestsField ) && ( ! $reservationForm.find( '.cs-form-field.cs-adults' ).hasClass( 'hide' ) );

		maxAdultNumber = parseInt( loftoceanRoomReservation.guestLimitation.maxAdult, 10 );
		maxChildNumber = parseInt( loftoceanRoomReservation.guestLimitation.maxChild, 10 );
		hasMaxAdultSet = ( ! isNaN( maxAdultNumber ) ) && ( maxAdultNumber > 0 );
		hasMaxChildSet = ( ! isNaN( maxChildNumber ) ) && ( maxChildNumber > 0 );

		enabledGuestNumber = hasMinGuestSet || hasMaxGuestSet || hasMaxChildSet || hasMaxAdultSet;

		todayTimestamp = getTimeStamp( '' );
		$.each( loftoceanRoomReservation.priceList, function( i, item ) {
			if ( ( 'unavailable' == item.status ) || ( item.available_number < 1 ) ) {
				disabledStartDates.push( item.start );
				disabledEndDates.push( item.end );
			}
		} );

		var defaultsDates = checkDefaultSettings(),
			defaultStartDate = defaultsDates.checkin,
			defaultEndDate = defaultsDates.checkout;

		$checkinDate.val( moment( defaultStartDate ).format( displayDateFormat ) ).data( 'value', defaultStartDate );
		$checkoutDate.val( moment( defaultEndDate ).format( displayDateFormat ) ).data( 'value', defaultEndDate );
        checkinDate = defaultStartDate;
        checkinTimestamp = getTimeStamp( new Date( checkinDate ) );
        checkoutDate = defaultEndDate;
        checkoutTimestamp = getTimeStamp( new Date( checkoutDate ) );
        adultNumber = getNumber( $adultNumber.val() );
        childNumber = getNumber( $childNumber.val() );
        roomNumber = getNumber( $roomNumber.val() );
        roomTotalPrice = 0;
		adultTotalPrice = 0;
		childTotalPrice = 0;
        extraServiceTotalPrice = 0;

        $checkinCheckoutGroup = $reservationForm.find( '.cs-form-field-group.date-group' );
    	groupCheckinCheckoutFields = $checkinCheckoutGroup.length;
    	if ( groupCheckinCheckoutFields ) {
    		$checkinSpan = $checkinDate.siblings( 'span.input' );
    		$checkoutSpan = $checkoutDate.siblings( 'span.input' );
    		$checkinSpan.text( moment( defaultStartDate ).format( displayDateFormat ) );
    		$checkoutSpan.text( moment( defaultEndDate ).format( displayDateFormat ) );
    	}

        if ( loftoceanRoomReservation.variablePrices.enable && ( '[object Object]' == Object.prototype.toString.call( loftoceanRoomReservation.variablePrices.prices ) ) ) {
        	if ( ( loftoceanRoomReservation.pricePerPerson && ( 'per_person' == loftoceanRoomReservation.variablePrices.mode ) )
        		|| ( ( ! loftoceanRoomReservation.pricePerPerson ) && ( 'per_person' != loftoceanRoomReservation.variablePrices.mode ) ) ) {
        		variablePriceEnabled = true;
        		groupGuests = ( 'undefined' !== typeof loftoceanRoomReservation.variablePrices.guestMode ) && ( 'group' == loftoceanRoomReservation.variablePrices.guestMode );
        		variableWeekendPriceEnabled = ( 'undefined' !== typeof loftoceanRoomReservation.variablePrices.enableWeekendPrice ) && loftoceanRoomReservation.variablePrices.enableWeekendPrice;
        		variablePrices = loftoceanRoomReservation.variablePrices.prices;
        	}
        }

		var $dateRangePicker = $reservationForm.find( '.date-range-picker' );

		if ( hasAvailabilityCalendar ) {
			$availabilityCalendar.daterangepicker( {
				parentEl: '.room-availability-calendar-wrapper',
				minDate: moment().format( dateFormat ),
				maxDate: moment().add( loftoceanRoomReservation.maximalMonthsAllowedForBooking, 'M' ).format( dateFormat ),
				startDate: defaultStartDate,
				endDate: defaultEndDate,
				alwaysShowCalendars: true,
				buttonClasses: $reservationForm.length ? 'btn btn-sm' : 'btn btn-sm hide',
				disableButtons: ! $reservationForm.length,
				locale: {
					format: dateFormat,
					applyLabel: loftoceanRoomReservation.availabilityCalendarText.apply,
					cancelLabel: loftoceanRoomReservation.availabilityCalendarText.cancel
				},
				beforeShowDay: function( date, drp ) {
					return checkDateAvailability( date, drp );
				}
			} ).trigger( 'click' ).on( 'apply.daterangepicker', function( e, drp ) {
				drp.show();
				var startDate = drp.startDate.format( dateFormat ), endDate = drp.endDate.format( dateFormat );

				if ( $reservationForm.length ) {
					$dateRangePicker.val( startDate + ' - ' + endDate );
					updateBookingDates( startDate, endDate );

					$( 'html, body' ).animate( { scrollTop: $checkinDate.offset().top - window.innerHeight / 2 }, 200 );
				}
			} ).on( 'cancel.daterangepicker', function( e, drp ) {
				drp.show();
				drp.setStartDate( defaultStartDate );
				drp.setEndDate( defaultEndDate );
				drp.updateView();
			} ).on( 'outsideClick.daterangepicker', function( e, drp ) { drp.show(); } );
		}

		if ( $reservationForm.length ) {
			$( '#content.site-content.with-sidebar-right' ).length ? $dateRangePicker.addClass( 'pull-right' ) : '';
			$dateRangePicker.daterangepicker( {
				minDate: moment().format( dateFormat ),
				maxDate: moment().add( loftoceanRoomReservation.maximalMonthsAllowedForBooking, 'M' ).format( dateFormat ),
				startDate: defaultStartDate,
				endDate: defaultEndDate,
				locale: { format: dateFormat },
				autoApply: true,
				beforeShowDay: function( date, drp ) {
					return checkDateAvailability( date, drp );
				}
			} ).on( 'apply.daterangepicker', function( e, drp ) {
				var startDate = drp.startDate.format( dateFormat ), endDate = drp.endDate.format( dateFormat );
				$( this ).val( startDate + ' - ' + endDate );
				updateBookingDates( startDate, endDate );

				if ( hasAvailabilityCalendar ) {
					var dateRangePicker = $availabilityCalendar.data( 'daterangepicker' );
					dateRangePicker.setStartDate( startDate );
					dateRangePicker.setEndDate( endDate );
					dateRangePicker.updateView();
				}
				if ( groupCheckinCheckoutFields ) {
					$checkinCheckoutGroup.removeClass( 'loftocean-highlighted' );
				} else {
					$checkinField.removeClass( 'loftocean-highlighted' );
	                $checkoutField.removeClass( 'loftocean-highlighted' );
	                drp.container.css( { 'transform': '', 'transition': '' } );
	            }
			} ).on( 'show.daterangepicker', function( e, drp ) {
				drp.popupSingle = false;
                drp.container.removeClass( 'single' ).find( '.drp-calendar.right' ).show();
                if ( drp.container.outerWidth( true ) < 558 ) { 
                    drp.popupSingle = true;
                    drp.container.addClass( 'single' ).find( '.drp-calendar.right' ).hide();
                } else {
                    drp.popupSingle = false;
                    drp.container.removeClass( 'single' ).find( '.drp-calendar.right' ).show();
                }
                drp.renderCalendar( 'left' );

				if ( groupCheckinCheckoutFields ) {
					$checkinCheckoutGroup.addClass( 'loftocean-highlighted' );
				} else {
	                $checkinField.addClass( 'loftocean-highlighted' );
	                $checkoutField.removeClass( 'loftocean-highlighted' );
	            }
            } ).on( 'setStartDate.daterangepicker', function( e, drp ) {
                if ( groupCheckinCheckoutFields ) {
                	$checkinSpan.text( drp.startDate.format( displayDateFormat ) ); 
                	$checkoutSpan.css( 'opacity', 0 );
                } else {
	                $checkinDate.val( drp.startDate.format( displayDateFormat ) );
	                $checkoutDate.val( '' );
	                $checkinField.removeClass( 'loftocean-highlighted' );
	                $checkoutField.addClass( 'loftocean-highlighted' );
                	drp.container.css( { 'transform': 'translateY(' + checkoutFieldOuterHeight + 'px)', 'transition': '0.15s' } );
                }
            } ).on( 'outsideClick.daterangepicker', function( e, drp ) {
                if ( drp.oldStartDate ) {
                    if ( groupCheckinCheckoutFields ) {
                    	$checkinSpan.text( drp.oldStartDate.format( displayDateFormat ) ); 
                		$checkoutSpan.text( drp.oldEndDate.format( displayDateFormat ) ).css( 'opacity', '' );
                    } else {
	                    $checkinDate.val( drp.oldStartDate.format( displayDateFormat ) );
	                    $checkoutDate.val( drp.oldEndDate.format( displayDateFormat ) );
                    }
                }
                if ( groupCheckinCheckoutFields ) {
                	$checkinCheckoutGroup.removeClass( 'loftocean-highlighted' );
                } else {
	                $checkinField.removeClass( 'loftocean-highlighted' );
	                $checkoutField.removeClass( 'loftocean-highlighted' );
	                drp.container.css( { 'transform': '', 'transition': '' } );
	            }
            } );

			$reservationForm.find( '.field-input-wrap.checkin-date, .field-input-wrap.checkout-date, .cs-form-field-group.date-group .cs-form-field-group-inner' ).on( 'click', function( e ) {
				var dateRangePicker = $dateRangePicker.data( 'daterangepicker' ),
					tmpCurrentCheckin = moment( $checkinDate.data( 'value' ) ? $checkinDate.data( 'value' ) : '' ),
					tmpCurrentCheckout = moment( $checkoutDate.data( 'value' ) ? $checkoutDate.data( 'value' ) : '' ),
					currentDates, currentCheckinDate, currentCheckoutDate;

				tmpCurrentCheckin = tmpCurrentCheckin.isValid() ? tmpCurrentCheckin : moment();
				tmpCurrentCheckout = tmpCurrentCheckout.isAfter( tmpCurrentCheckin ) ? tmpCurrentCheckout : tmpCurrentCheckin.clone().add( '1', 'day' );
				currentDates = checkSelectedDates( tmpCurrentCheckin, tmpCurrentCheckout );
				currentCheckinDate = currentDates.checkin;
				currentCheckoutDate = currentDates.checkout;

				dateRangePicker.setStartDate( currentCheckinDate );
				dateRangePicker.setEndDate( currentCheckoutDate );
				dateRangePicker.show();
			} );
		}

		if ( $priceDetails.length ) {
			$totalPriceSection.on( 'click', function( e ) {
				var $self = $( this );
				if ( $priceDetails.hasClass( 'hide' ) ) {
					$self.addClass( 'toggled-on' );
					$priceDetails.removeClass( 'hide' );
				} else {
					$self.removeClass( 'toggled-on' );
					$priceDetails.addClass( 'hide' );
				}
			} );
			$totalPriceSection.hasClass( 'default-hide' ) ? '' : $totalPriceSection.trigger( 'click' );
			$totalPriceSection.hasClass( 'always-show' ) ? $totalPriceSection.off( 'click' ) : '';
		}
        $reservationForm.on( 'change', '.cs-form-group.cs-extra-service-group .label-checkbox .extra-service-switcher', function() {
            calculateExtraServiceTotalPrice();
            updateTotalPrice();
        } ).on( 'click', '.cs-form-group.cs-extra-service-group .extra-service-custom-quantity button', function( e ) {
            setTimeout( function() {
                calculateExtraServiceTotalPrice();
                updateTotalPrice();
            }, 50 );
        } );
        $roomNumber.on( 'loftocean.number.changed', function( e, isPlus, $btn ) {
        	if ( $btn.data( 'status' ) && ( 'disabled' == $btn.data( 'status' ) ) ) return;

        	roomNumber = getNumber( $roomNumber.val() ); 
            reCalculateGuestNumber();
            updateTotalPrice(); 

            checkRoomNumberField();
            checkAdultChildFields(); 
        } ).parent().on( 'click', 'button', function( e ) { 
			$roomMessage.removeClass( 'show' );
			clearTimeout( messageTimer );
        } );
        $adultNumber.on( 'loftocean.number.changed', function( e, isPlus, $btn ) {
        	e.preventDefault();
        	if ( $btn.data( 'status' ) && ( 'disabled' == $btn.data( 'status' ) ) ) return;

        	adultNumber = getNumber( $adultNumber.val() );
			calculateRoomTotalPrice();
            calculateExtraServiceTotalPrice();
            updateTotalPrice();
			if ( loftoceanRoomReservation.pricePerPerson || variablePriceEnabled ) {
				updateLowestPrice();
			}

			checkAdultChildFields(); 
        } ).parent().on( 'click', 'button', function( e, ignoreValidation ) {
        	if ( 'undefined' == typeof ignoreValidation ) {
	        	var isPlus = $( this ).hasClass( 'plus' ),
	        		validatePassed = validateTotalGuestNumber( 'adult', isPlus ),
	        		minusBtnValidatePassed = isPlus ? true : checkMinusBtnClicked( true );

	        	if ( ( ! minusBtnValidatePassed ) || ( ! validatePassed ) ) {
	        		e.preventDefault();
	        		e.stopImmediatePropagation();
	        		return false;
	        	}
	        } else if ( ignoreValidation ) {
	        	$adultBtnsPlus.removeClass( 'disabled' ).data( 'status', '' );
	        	$adultBtnsMinus.removeClass( 'disabled' ).data( 'status', '' );
	        	$childBtnsPlus.removeClass( 'disabled' ).data( 'status', '' );
	        	$childBtnsMinus.removeClass( 'disabled' ).data( 'status', '' );
	        }
        } );
        $childNumber.on( 'loftocean.number.changed', function( e, isPlus, $btn ) {
        	e.preventDefault();
        	if ( $btn.data( 'status' ) && ( 'disabled' == $btn.data( 'status' ) ) ) return;

            childNumber = getNumber( $childNumber.val() );
			calculateRoomTotalPrice();
            calculateExtraServiceTotalPrice();
            updateTotalPrice();
			if ( loftoceanRoomReservation.pricePerPerson || variablePriceEnabled ) {
				updateLowestPrice();
			}
			checkAdultChildFields(); 
        } ).parent().on( 'click', 'button', function( e, ignoreValidation ) {
        	if ( 'undefined' == typeof ignoreValidation ) {
	        	var isPlus = $( this ).hasClass( 'plus' ),
	        		validatePassed = validateTotalGuestNumber( 'child', isPlus ),
	        		minusBtnValidatePassed = isPlus ? true : checkMinusBtnClicked( false );

	        	if ( ( ! minusBtnValidatePassed ) || ( ! validatePassed ) ) {
	        		e.preventDefault();
	        		e.stopImmediatePropagation();
	        		return false;
	        	}
	        } else if ( ignoreValidation ) {
	        	$adultBtnsPlus.removeClass( 'disabled' ).data( 'status', '' );
	        	$adultBtnsMinus.removeClass( 'disabled' ).data( 'status', '' );
	        	$childBtnsPlus.removeClass( 'disabled' ).data( 'status', '' );
	        	$childBtnsMinus.removeClass( 'disabled' ).data( 'status', '' );
	        }
        } );
		$( 'body' ).on( 'click', function( e ) {
			var $target = $( e.target ), $priceBreakdown = $( '.csf-base-price-breakdown' );
			if ( $priceBreakdown.length && ( ! $target.hasClass( 'csf-base-price-breakdown' ) ) && ( ! $target.parents( '.csf-base-price-breakdown' ).length ) && ( ! $priceBreakdown.parent().hasClass( 'always-show' ) ) ) {
				$( '.csf-base-price-breakdown' ).removeClass( 'show' );
			}
		} ).on( 'mouseenter', '.daterangepicker-has-tooltip', function() {
			var $toolTip = $( this ).find( '.day-tooltip' );
			$toolTip.length ? $toolTip.removeClass( 'hide' ) : '';
		} ).on( 'mouseleave', '.daterangepicker-has-tooltip', function() {
			var $toolTip = $( this ).find( '.day-tooltip' );
			$toolTip.length ? $toolTip.addClass( 'hide' ) : '';
		} );
		$reservationForm.on( 'click', '.csf-pd-total-base .csf-pd-label', function( e ) {
			e.stopImmediatePropagation();
			e.stopPropagation();

			if ( $( this ).parent().hasClass( 'always-show' ) ) return; 

			var $priceBreakdown = $( this ).siblings( '.csf-base-price-breakdown' );
			$priceBreakdown.hasClass( 'show' ) ? '' : $priceBreakdown.addClass( 'show' );
		} ).on( 'click', '.cs-submit button', function( e ) {
			e.preventDefault();
			var data = { 'roomID': loftoceanRoomReservation.roomID, 'action': loftoceanRoomReservation.addRoomToCartAjaxAction },
				options = $reservationForm.find( 'input,select,cheeckbox' ).serializeArray();
			options.forEach( function( option ) {
				data[ option[ 'name' ] ] = option[ 'value' ];
			} );
			data[ 'checkin' ] = $checkinDate.data( 'value' );
			data[ 'checkout' ] = $checkoutDate.data( 'value' );

			$errorMessage.html( '' );
			$successMessage.html( '' );
			$loading.addClass( 'loading' );
			$.ajax( loftoceanRoomReservation.ajaxURL, { 'method': 'POST', 'data': data } ).done( function( data, status ) {
				var processed = false;
				if ( 'success' == status ) {
					data = data ? JSON.parse( data ) : {};
					if ( 'object' == typeof data ) {
						if ( data.message ) {
							processed = true;
							$errorMessage.html( '<p>' + data.message + '</p>' );
						} else if ( data.redirect ) {
							processed = true;
							$successMessage.html( '<p>' + i18nText.bookingSuccess + '</p>' );
							setTimeout( function() {
								redirectToCartPage();
							}, 500 );
						}
					}
				}
				processed ? '' : $errorMessage.html( '<p>' + i18nText.bookingError + '</p>' );
			} ).fail( function() {
				$errorMessage.html( '<p>' + i18nText.bookingError + '</p>' );
			} ).always( function() {
				$loading.removeClass( 'loading' );
			} );
			return false;
		} );

		if ( $reservationForm.length ) {
			childFieldOnly ? checkChildOnlyField() : checkAdultChildFields();
			reCalculateGuestNumber();
			checkExtraServiceList( checkinTimestamp, checkoutTimestamp );
			checkFlexiblePriceRules();
			$loading.removeClass( 'loading' );
		}
	} );
} ) ( jQuery );
