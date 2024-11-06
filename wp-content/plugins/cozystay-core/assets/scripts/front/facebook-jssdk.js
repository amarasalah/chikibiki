( function( d, s, id, $ ) {
	"use strict";
	// Adjust facebook widget width
	function loftocean_adjust_width() {
		var changed = false;
		$facebooks.each( function() {
			var fb_width = $( this ).attr( 'data-width' ),
				container_width = $( this ).parent().width();
			if ( fb_width != container_width ) {
				$( this ).attr( 'data-width', container_width ).removeAttr( 'fb-xfbml-state' ).removeAttr( 'fb-iframe-plugin-query' ).html( '' );
				changed = true;
			}
		} );
		return changed;
	}
	// Facebook XFBML render event
	function loftocean_fb_render() {
		FB.Event.subscribe( 'xfbml.render', function() {
			$( document ).trigger( 'rendered.loftocean.facebook' );
		} );
	}

	var js, fjs = d.getElementsByTagName( s )[0], loftocean_fb_timer = null,
		loftocean_fb_count = 20000,
		$facebooks = $( '.loftocean-fb-page' );
	if ( d.getElementById( id ) || ( ! $facebooks.length && ( ( ! window.parent ) || ( typeof window.parent.elementor == 'undefined' ) ) ) ) {
		return;
	}

	loftocean_adjust_width();

	js = d.createElement( s ); js.id = id; js.async = true;
	js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&version=v2.8";
	fjs.parentNode.insertBefore( js, fjs );

	$( window ).resize( function() {
		clearTimeout( $.data( this, 'resizeTimer' ) );
		$.data( this, 'resizeTimer', setTimeout( function() {
			( loftocean_adjust_width() && FB ) ? FB.XFBML.parse() : '';
		}, 500 ) );
	} );

	if ( typeof FB !== 'undefined' ) {
		loftocean_fb_render();
	} else {
		loftocean_fb_timer = setInterval( function() {
			var exists = ( typeof FB !== 'undefined' );
			loftocean_fb_count -= 500;
			if ( exists || ( loftocean_fb_count === 0 ) ) {
				clearInterval( loftocean_fb_timer );
			}
			if( exists ) {
				loftocean_fb_render();
			}
		}, 500 );
	}
} ) ( document, 'script', 'facebook-jssdk', jQuery );
