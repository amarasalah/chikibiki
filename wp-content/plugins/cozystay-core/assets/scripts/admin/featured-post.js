( function( $ ) {
	"use strict";
	$( document ).ready( function() {
		$( 'body' ).on( 'change', 'input[name=loftocean-featured-post-inline-edit]', function( e ) {
			var featuredAjax = loftoceanFeaturedPost ? loftoceanFeaturedPost : false;
			if ( $( this ).attr( 'data-id' ) && featuredAjax ) {
				var url = featuredAjax.url,
					data = {
						'action': featuredAjax.action,
						'post_id': $( this ).attr( 'data-id' ),
						'loftocean_featured_post': ( $( this ).is( ':checked' ) ? 'on' : 'off' ),
						'nonce': featuredAjax.nonce
					};
				$.post( url, data );
			}
		} );
	} );
} ) ( jQuery );
