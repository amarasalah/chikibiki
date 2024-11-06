( function( $ ) {
	"use strict";
	/**
	* Parse Instagram feed to html
	* @param jQuery object instagram widget
	* @param array feed list
	* @param boolean whether open in new tab
	* @param int how many feed need to show
	*/
	var isRetina = ( 'devicePixelRatio' in window ) && ( parseInt( window.devicePixelRatio, 10 ) >= 2 );
	function parseInstagramFeeds( widget, feeds, newTab, limit ) {
		if ( validateInstagramFeed( feeds ) ) {
			var $list = $( widget ).find( 'ul' ).length ? $( widget ).find( 'ul' ) : $( '<ul>', { 'style': 'display: none;' } ).appendTo( widget ),
				aAttrs = { 'href': '' }, divAttrs = { 'class': 'feed-bg', 'style': '' }, images = feeds.slice( 0, limit ), imageURL = '';
			if ( newTab ) {
				aAttrs[ 'target' ] = '_blank';
				aAttrs[ 'rel' ] = 'noopenner noreferrer';
			}
			images.forEach( function( val, index ) {
				aAttrs[ 'href' ] = val[ 'link' ];
				if ( val.srcs && val.srcs.length ) {
					imageURL = ( val.srcs.length > 1 ) && isRetina ? val.srcs[1] : val.srcs[0];
				} else {
					imageURL = val['url'];
				}
				divAttrs[ 'style' ] = 'background-image: url(' + imageURL + ');';
				$list.append( $( '<li>' ).append( $( '<a>', aAttrs ).append( $( '<div>', divAttrs ) ) ) );
			} );
			$list.fadeIn( 'slow' );
		}
	}
	/**
	* Check the feeds is valid
	* @param array feed list
	*/
	function validateInstagramFeed( feeds ) {
		return $.isArray( feeds ) && feeds.length;
	}

	$( document ).ready( function(){
		if ( loftoceanInstagram ) {
			var instagrams = $( '.widget.' + loftoceanInstagram[ 'class' ] );
			if ( instagrams.length ) {
				var cache = {}, url = loftoceanInstagram.apiRoot + 'loftocean/v1/instagram/';
				instagrams.each( function() {
					var widget = $( this ), feedID = widget.data( 'feed-id' ),
						cols = widget.data( 'column' ) || 6, limit = widget.data( 'limit' ),
						newTab = widget.data( 'new-tab' ), location = widget.data( 'location' ),
						cacheKey = feedID + location;

					if ( limit && ( parseInt( limit, 10 ) > 0 ) ) {
						if ( cacheKey in cache ) {
							parseInstagramFeeds( widget, cache[ cacheKey ], newTab, limit );
						} else {
							$.get( url + feedID + '/' + location + '/' + cols + '/' + ( loftoceanInstagram.isMobile ? 1 : 0 ) )
								.done( function( data, text, status ) {
									if ( 200 == status.status ) {
										cache[ cacheKey ] = data;
										parseInstagramFeeds( widget, data, newTab, limit );
									}
								} );
						}
					}
				} );
			}
		}
	} );
} ) ( jQuery );
