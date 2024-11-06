( function( api, $ ) {
	"use strict";
	/**
	* Add new control constructor for button type control
	*/
	var feeds = false, itemsProcessing = 1, feedCount = false;
	function loftoceanInstagramDownloadFeed( url, offset, $btn, $clearBtn, message ) {
		var lastRequest = ( offset + itemsProcessing ) >= feedCount,
			nextOffset = offset + itemsProcessing, items = feeds.slice( offset, nextOffset );
		$.get( url + items.join( ',' ) ).done( function( response ) {
			if ( lastRequest ) {
				$.get( wpApiSettings.root + 'loftocean/v1/clear-instagram-feed-storage/' ).always( function() {
					$clearBtn.removeAttr( 'disabled' );
					$btn.val( message['done'] ).removeAttr( 'disabled' );
					feeds = false;
					feedCount = false;
				} );
			} else {
				loftoceanInstagramDownloadFeed( url, nextOffset, $btn, $clearBtn, message );
			}
		} ).fail( function() {
			$clearBtn.removeAttr( 'disabled' );
			$btn.val( message['fail'] );
			setTimeout( function() { $btn.val( message['done'] ).removeAttr( 'disabled' ); }, 5000 );
			feeds = false;
			feedCount = false;
		} );
	}
	api.bind( 'ready', function( e ) {
		var $clearBtn = $( '#customize-control-loftocean_instagram_clear_cache input[type=button]' );
		$( 'body' ).on( 'click', '#customize-control-loftocean_instagram_clear_cache input[type=button]', function( e ){
			e.preventDefault();
			if ( wpApiSettings && wpApiSettings.root ) {
				var $self = $( this ), message = loftoceanInstagram.i18nMessage.clear;
				$self.val( message['process'] ).attr( 'disabled', 'disabled' );
				var cache = {}, url = wpApiSettings.root + 'loftocean/v1/clear-instagram-cache/';
				$.get( url ).done( function() {
					$self.val( message['done'] ).removeAttr( 'disabled' );
				} ).fail( function() {
					$self.val( message['fail'] );
					setTimeout( function() { $self.val( message['done'] ).removeAttr( 'disabled' ); }, 5000 );
				} );
			}
		} ).on( 'click', '#customize-control-loftocean_instagram_download_images input[type=button]', function( e ){
			e.preventDefault();
			if ( wpApiSettings && wpApiSettings.root ) {
				var $self = $( this ), message = loftoceanInstagram.i18nMessage.download;
				$self.val( message['process'] ).attr( 'disabled', 'disabled' );
				$clearBtn.attr( 'disabled', 'disabled' );
				var latestFeedURL = wpApiSettings.root + 'loftocean/v1/get-latest-instagram/',
					downloadImageURL = wpApiSettings.root + 'loftocean/v1/download-instagram-feed/';
				$.get( latestFeedURL ).done( function( response ) {
					if ( response.success ) {
						feeds = response.data;
						feedCount = feeds.length;
						loftoceanInstagramDownloadFeed( downloadImageURL, 0, $self, $clearBtn, message );
					} else {
						$self.val( message.noFeedFound );
						setTimeout( function() { $self.val( message['done'] ).removeAttr( 'disabled' ); }, 5000 );
					}
				} ).fail( function() {
					$clearBtn.removeAttr( 'disabled' );
					$self.val( message['fail'] );
					setTimeout( function() { $self.val( message['done'] ).removeAttr( 'disabled' ); }, 5000 );
				} );
			}
		} );

		var controlSchedule = api.control( 'loftocean_auto_download_instagram_images_schedule' );
		if ( controlSchedule ) {
			api( 'loftocean_enable_auto_download_instagram_images', function( value ) {
				value.bind( function( to ) {
					to ? controlSchedule.container.show() : controlSchedule.container.hide();
				} );
			} );
		}
	} );
} ) ( wp.customize, jQuery );
