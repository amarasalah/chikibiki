( function( $ ) {
	"use strict";
	function loftocean_convert_number( num ) {
		num = parseInt( num, 10 );
		if ( num < 1 ) {
			return 0;
		} else if ( num >= 1000000 ) {
			num = Math.floor( num / 100000 ) / 10;
			return num + 'M';
		} else if( num >= 1000 ) {
			num = Math.floor( num / 100 ) / 10;
			return num + 'K';
		} else {
			return num;
		}
	}
	function loftocean_update_like( $el, plusOne ) {
		if ( $el && $el.length ) {
			var id = $el.attr( 'data-post-id' ), count = $el.data( 'like-count' ),
				$likes = $( '.post-like[data-post-id=' + id + ']' );
			if ( $likes && $likes.length ) {
				count = plusOne ? ( parseInt( count, 10 ) + 1 ) : ( count - 1 );
				$likes.data( 'like-count', Math.max( 0, count ) );
				$likes.find( '.count' ).text( loftocean_convert_number( count ) );
				if ( $likes.find( '.label' ).length ) {
					var $labels = $likes.find( '.label' ), $first = $labels.first().parent();
					$labels.text( count > 1 ? $first.data( 'plural-label' ) : $first.data( 'single-label' ) );
				}
			}
		}
	}

	var LoftOceanLocalStorage = {
		likes: false,
		itemName: 'loftocean/liked',
		init: function() {
			if ( $( '.post-like' ).length ) {
				var likes = LoftOceanLocalStorage.getItem( LoftOceanLocalStorage.itemName );
				if ( likes ) {
					LoftOceanLocalStorage.likes = JSON.parse( likes );
					if ( LoftOceanLocalStorage.hasLikes() ) {
						$( document ).ready( function() {
							LoftOceanLocalStorage.addLikedClass();
						} );
					}
				}
			}
			$( document ).on( 'loftcean/moreContent/loaded', function() {
				LoftOceanLocalStorage.addLikedClass();
			} );
		},
		addLikedClass: function() {
			if ( LoftOceanLocalStorage.hasLikes() ) {
				LoftOceanLocalStorage.likes.forEach( function( pid ) {
					var $likes = $( '.post-like[data-post-id=' + pid + ']' );
					if ( $likes.length ) {
						$likes.addClass( 'liked' );
					}
				} );
			}
		},
		set: function( name ) {
			if ( LoftOceanLocalStorage.hasLikes() ) {
				var index = LoftOceanLocalStorage.likes.indexOf( name );
				if ( -1 === index ) {
					LoftOceanLocalStorage.likes.push( name );
					LoftOceanLocalStorage.save();
				}
			} else {
				LoftOceanLocalStorage.likes = [ name ];
				LoftOceanLocalStorage.save();
			}
		},
		isLiked: function( name ) {
			if ( LoftOceanLocalStorage.hasLikes() ) {
				var index = LoftOceanLocalStorage.likes.indexOf( name );
				return ( -1 !== index );
			} else {
				return false;
			}
		},
		remove: function( name ) {
			if ( LoftOceanLocalStorage.hasLikes() ) {
				var index = LoftOceanLocalStorage.likes.indexOf( name );
				if ( -1 !== index ) {
					LoftOceanLocalStorage.likes.splice( index, 1 );
					LoftOceanLocalStorage.save();
				}
			}
		},
		save: function() {
			try {
				window.localStorage.setItem( LoftOceanLocalStorage.itemName, JSON.stringify( LoftOceanLocalStorage.likes ) );
			} catch( msg ) {}
		},
		hasLikes: function() {
			return LoftOceanLocalStorage.likes && Array.isArray( LoftOceanLocalStorage.likes );
		},
		getItem: function( name ) {
			try {
				return localStorage.getItem( name );
			} catch ( msg ) {
				return null;
			}
		}
	};
	LoftOceanLocalStorage.init();

	$( document ).ready( function() {
		if ( $( '.post-like' ).length ) {
			$( 'body' ).on( 'click', '.post-like', function( e ) {
				e.preventDefault();
				var $like = $( this );
				if ( ! $like.data( 'animating' ) ) {
					var pid = $like.attr( 'data-post-id' ), $likes = $( '.post-like[data-post-id=' + pid + ']' );
					$likes.data( 'animating', true );
					if ( $like.hasClass( 'liked' ) ) {
						var data = { 'action': loftoceanSocialAjax.like.action, 'post_id': pid, 'unliked': true };
						$.post( loftoceanSocialAjax.url, data ).done( function() {
							LoftOceanLocalStorage.remove( pid );
							if ( $like.data( 'like-count' ) > 0 ) {
								loftocean_update_like( $like, false );
								$likes.removeClass( 'liked' );
							}
							$likes.data( 'animating', false );
						} );
					} else {
						if ( pid && ! LoftOceanLocalStorage.isLiked( pid ) ) {
							var data = { 'action': loftoceanSocialAjax.like.action, 'post_id': pid };
							$likes.addClass( 'liked clicking' );
							$.post( loftoceanSocialAjax.url, data ).done( function() {
								LoftOceanLocalStorage.set( pid );
								loftocean_update_like( $like, true );
								$likes.data( 'animating', false );
								$likes.removeClass( 'clicking' );
							} );
						} else {
							$likes.data( 'animating', false );
						}
					}
				}
				return false;
			} );
		}
		if ( $( '.post-list-social-icon-list a.popup-window, .article-share a.popup-window, .tweet-it, .social-share-icons > a' ).length ) {
			$( 'body' ).on( 'click', '.post-list-social-icon-list a.popup-window, .article-share a.popup-window, .tweet-it, .social-share-icons > a', function( e ) {
				e.preventDefault();
				var self = $( this ),
					prop = self.attr( 'data-props' ) ? self.attr( 'data-props' ) : 'width=555,height=401';
				window.open( self.attr( 'href' ), self.attr( 'title' ), prop );
				if ( self.data( 'social-type' ) && self.data( 'post-id' ) ) {
					var data = {
						'action': loftoceanSocialAjax.social.action,
						'post_id': self.data( 'post-id' ),
						'social': self.data( 'social-type' )
					};
					$.post( loftoceanSocialAjax.url, data ).done( function() {
						var counter = ( self.data( 'raw-counter' ) || 0 ) + 1,
							target = '.loftocean-social-share-icon[data-social-type="' + self.data( 'social-type' ) + '"]';
						$( target ).data( 'raw-counter', counter )
							.find( 'span.counter' ).text( loftocean_convert_number( counter ) );
					} );
				}
				return false;
			} );
		}

		if ( wpApiSettings && wpApiSettings.root && loftoceanSocialAjax && loftoceanSocialAjax.loadPostMetasDynamically ) {
			var $metaViews = $( '.loftocean-view-meta' ), $metaLikes = $( '.loftocean-like-meta' ),
				$allMetas = $( '<div>', { 'class': 'temp-div' } ).add( $metaViews ).add( $metaLikes ).not( '.temp-div' );
			if ( $allMetas.length ) {
				var ids = [], currentPostID = loftoceanSocialAjax.currentPostID;
				$allMetas.each( function() {
					if ( $( this ).data( 'post-id' ) && ! ids.includes( $( this ).data( 'post-id' ) ) ) {
						ids.push( $( this ).data( 'post-id' ) );
					}
				} );
				currentPostID && ! ids.includes( parseInt( currentPostID, 10 ) ) ? ids.push( currentPostID ) : '';
				if ( ids.length ) {
					var url = wpApiSettings.root + 'loftocean/v1/get-post-metas/' + ids.join( ',' ) + '/' + ( currentPostID ? currentPostID : 0 );
					$.get( url ).done( function( data, text, status ) {
						if ( ( 200 == status.status ) && ( 200 == data.status ) ) {
							data = data.data;
							$metaViews.each( function() {
								var cPostID = $( this ).data( 'post-id' );
								if ( data[ cPostID ] && data[ cPostID ]['loftocean-view-count'] ) {
									var viewCount = data[ cPostID ]['loftocean-view-count'];
									$( this ).find( '.count' ).length ? $( this ).find( '.count' ).text( viewCount[ 'format' ] ) : '';
								}
							} );
							$metaLikes.each( function() {
								var cPostID = $( this ).data( 'post-id' );
								if ( data[ cPostID ] && data[ cPostID ]['loftocean-like-count'] ) {
									var likeCount = data[ cPostID ]['loftocean-like-count'];
									$( this ).data( 'like-count', Math.max( 0, likeCount[ 'raw' ] ) );
									$( this ).find( '.count' ).length ? $( this ).find( '.count' ).text( likeCount[ 'format' ] ) : '';
								}
							} );
						}
					} );
				}
			}
		}
	} );
} ) ( jQuery );
