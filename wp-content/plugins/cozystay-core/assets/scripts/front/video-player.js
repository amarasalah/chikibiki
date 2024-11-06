( function( $ ) {
	"use strict";
	var NativeVideoPlayer, YouTubeVideoPlayer, VimeoVideoPlayer, AutoPlayVideo, FullscreenVideo, youtubeStack = [],
		youtubeCBLoaded = false, vimeoStack = [], vimeoCBLoaded = false, textarea = false, tagA = false;

	function loftoceanGetURLQuery( url ) {
		if ( ! url || ! url.includes( '?' ) ) return {};
		textarea = textarea ? textarea : document.createElement( 'textarea' );
		tagA = tagA ? tagA : document.createElement( 'a' );
		textarea.innerHTML = url;
		tagA.href = textarea.childNodes[0].nodeValue;
		url = tagA.search ? tagA.search.substr( 1 ) : '';
		return url.split( '&' ).reduce( ( params, param ) => {
			let [ key, value ] = param.split( '=' );
			params[ key ] = value ? decodeURIComponent( value.replace( /\+/g, ' ' ) ) : '';
			return params;
		}, {} );
	}

	FullscreenVideo = function( html, container, wrapID, cbTarget ) {
		this.container = container;
		this.wrapID = wrapID;
		var deferred = this.init( html, container );
		if ( deferred ) {
			var self = this;
			deferred.done( function( player ) {
				self.showVideo( player );
				cbTarget ? cbTarget.trigger( 'shown.loftocean.video', player ) : '';
			} ).always( function() {
				cbTarget ? cbTarget.trigger( 'done.loftocean.video' ) : '';
			} );
		} else if ( cbTarget ) {
			cbTarget.trigger( 'done.loftocean.video' );
		}
	}

	FullscreenVideo.prototype = {
		init: function( html, container ) {
			var i = 0, self = this, currentPlayer = false, player = '', players = [ YouTubeVideoPlayer, VimeoVideoPlayer, NativeVideoPlayer ];
			for ( i = 0; i < 3; i++ ) {
				player = new players[ i ]();
				if ( 'test' in player && player.test( html ) ) {
					return player.initialize.call( player, html, container, self );
				} else {
					player = '';
				}
			}
			return false;
		},
		insertVideo: function( node ) {
			this.container.append( $( '<div>', { 'class': 'loftocean-video-wrap', 'id': this.wrapID } ).append( node ) );
		},
		showVideo: function( player ) {
			this.container.addClass( 'hide' );
			this.container.find( '.loftocean-video-wrap' ).addClass( 'hide' );
			this.container.find( '#' + this.wrapID ).removeClass( 'hide' );
			this.container.removeClass( 'hide' ).addClass( 'show' );
			$( 'body' ).css( 'overflow', 'hidden' );
			$( '#loftocean-fullscreen-media-wrapper .close-button' ).data( 'player', player );
			player.resizeVideo();
			player.playVideo();
		}
	};

	AutoPlayVideo = function( html, container, args ) {
		this.container = container;
		this.args = args;
		var deferred = this.init( html, container );
		if ( deferred ) {
			var self = this;
			deferred.done( function( player ) {
				self.showVideo( player );
			} );
		}
	}

	AutoPlayVideo.prototype = {
		init: function( html, container ) {
			var i = 0, self = this, player = '', players = [ YouTubeVideoPlayer, VimeoVideoPlayer, NativeVideoPlayer ];
			for ( i = 0; i < 3; i++ ) {
				player = new players[ i ]();
				if ( 'test' in player && player.test( html ) ) {
					return player.initialize.call( player, html, container, self );
				}
			}
			return false;
		},
		insertVideo: function( node ) {
			this.container.append( $( node ).addClass( 'loftocean-autoplay-video hide' ) );
		},
		showVideo: function( player ) {
			var video = this.container.find( '.loftocean-autoplay-video' );
			player.resizeVideo();
			player.playVideo();
			if ( this.args && this.args.className ) {
				video.find( 'iframe' ).length ? video.find( 'iframe' ).addClass( this.args.className ) : video.addClass( this.args.className );
			}
			video.removeClass( 'hide' );
		}
	};

	function VideoPlayerBase(){ }
	VideoPlayerBase.prototype = {
		deferred: false,
		initialize: function( html, $container, manager ) {
			this.html = html;
			this.deferred = $.Deferred();
			this.$container = $container;
			this.container = this.$container.get( 0 );
			this.manager = manager;
			this.ready();
			return this.deferred.promise();
		},
		ready: function() {},
		test: function( html ) {
			return false;
		},
		getDimensions: function() {
			var width = this.$container.width() || 0, ratio = this.container.ratio || (9 / 16);
			return { 'width': width, 'height': width * ratio };
		},
		resizeVideo: function() {
			var video = this.video, dimension = this.getDimensions();
			video.width = dimension.width;
			video.height = dimension.height;
		},
		playVideo: function() {},
		pauseVideo: function() {},
		setMuted: function() {},
	};

	VideoPlayerBase.extend = function( protoProps ) {
		var prop;
		function CustomHandler() {
			var result = VideoPlayerBase.apply( this, arguments );
			return result;
		}

		CustomHandler.prototype = Object.create( VideoPlayerBase.prototype );
		CustomHandler.prototype.constructor = CustomHandler;
		for ( prop in protoProps ) {
			CustomHandler.prototype[ prop ] = protoProps[ prop ];
		}
		return CustomHandler;
	};

	NativeVideoPlayer = VideoPlayerBase.extend( {
		test: function( html ) {
			var regex = /\/\/.+\/.+\.(mp4|webm|ogg|mov)/;
			if ( regex.exec( html ) ) {
				var video = document.createElement( 'video' );
				video.src = html.match( regex )[0];
				this.video = video;
				return true;
			}
			return false;
		},
		ready: function() {
			var handler = this, video = this.video;

			video.autoplay = false;
			video.loop = 'loop';
			video.controls = 'controls';

			this.video = video;
			this.manager.insertVideo( video );
			$( handler.video ).on( 'loadedmetadata', function() {
				handler.container.ratio = ( this.videoHeight || 9 ) / ( this.videoWidth || 16 );
				handler.deferred.resolve( handler );
			} );
			$( window ).on( 'resize', function() {
				if ( handler.container.ratio ) {
					handler.resizeVideo.call( handler );
				}
			} );
		},
		playVideo: function() {
			this.video.play();
		},
		pauseVideo: function() {
			this.video.pause();
		},
		setMuted: function() {
			this.video.muted = true;
		}
	} );

	YouTubeVideoPlayer = VideoPlayerBase.extend( {
		regex: /([^"'])*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?'"]*)([^"'])*/,
		test: function( html ) {
			return this.regex.exec( html );
		},
		ready: function() {
			if ( ( 'YT' in window ) && YT.Player ) {
				this.loadVideo();
			} else {
				var self = this;
				youtubeStack.push( self );
				if ( ! youtubeCBLoaded ) {
				 	youtubeCBLoaded = true;
					var tag = document.createElement( 'script' );
					tag.src = 'https://www.youtube.com/iframe_api';
					tag.onload 	= function() {
						YT.ready( function( e ) {
							var item = '';
							while ( youtubeStack.length ) {
								item = youtubeStack.pop();
								item.loadVideo.call( item );
							}
						} );
					};
					document.getElementsByTagName( 'head' )[0].appendChild( tag );
				}
			}
		},
		loadVideo: function() {
			var handler = this,
				video = $( '<div>', { 'class': 'youtube-video' } ).get( 0 ), matches = this.html.match( this.regex ),
				vid = matches[2], urlQuery = loftoceanGetURLQuery( matches[0] ),
				videoArgs = Object.assign( {}, {
					autoplay: 		0,
					controls: 		1,
					disablekb: 		1,
					fs: 			0,
					iv_load_policy: 3,
					loop: 			1,
					modestbranding: 1,
					playsinline: 	1,
					rel: 			0,
					showinfo: 		0
				}, urlQuery );

			$.getJSON( 'https://noembed.com/embed', { format: 'json', url: ( 'https://www.youtube.com/watch?v=' + vid ) }, function( data ) {
				var dimensions;
				handler.container.ratio = ( data.height || 9 ) / ( data.width || 16 );
				dimensions = handler.getDimensions.call( handler );

				handler.manager.insertVideo( video );
				handler.player = new YT.Player( video, {
					videoId: vid,
					width: dimensions.width,
					height: dimensions.height,
					events: {
						onReady: function( e ) {
							handler.video = handler.$container.find( '.youtube-video' ).get( 0 );
							handler.deferred.resolve( handler );
						},
						onStateChange: function( e ) {
							if ( YT.PlayerState.ENDED === e.data ) {
								if ( videoArgs.start ) {
									e.target.seekTo( videoArgs.start );
								}
								e.target.playVideo();
							}
						}
					},
					playerVars: videoArgs
				} );
				$( window ).on( 'resize', function() {
					if ( handler.container.ratio && handler.video ) {
						handler.resizeVideo.call( handler );
					}
				} );
			} );
		},
		playVideo: function() {
			this.player.playVideo();
		},
		pauseVideo: function() {
			this.player.pauseVideo();
		},
		setMuted: function() {
			this.player.mute();
		}
	} );

	VimeoVideoPlayer = VideoPlayerBase.extend( {
		regex: /https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)/,
	 	test: function( html ) {
			return this.regex.exec( html );
		},
		ready: function() {
			if ( ( 'Vimeo' in window ) && Vimeo.Player ) {
				this.loadVideo();
			} else {
				var self = this;
				vimeoStack.push( self );
				if ( ! vimeoCBLoaded ) {
					vimeoCBLoaded = true;
					var tag = document.createElement( 'script' );
					tag.src = 'https://player.vimeo.com/api/player.js';
					tag.onload 	= function() {
						var item = '';
						while ( vimeoStack.length ) {
							item = vimeoStack.pop();
							item.loadVideo.call( item );
						}
					};
					document.getElementsByTagName( 'head' )[0].appendChild( tag );
				}
			}
		},
		loadVideo: function() {
			var handler = this, vid = this.html.match( this.regex )[3], elementID = this.generateID(),
				video = $( '<div>', { 'id': 'vimeo-video-' + elementID } ).get( 0 );

			if ( Vimeo && Vimeo.Player ) {
				var options = { 'id': vid, 'loop': true, 'autoplay': false };
				handler.manager.insertVideo( video );
				handler.player = new Vimeo.Player( 'vimeo-video-' + elementID, options );
				Promise.all( [ handler.player.getVideoWidth(), handler.player.getVideoHeight() ] ).then( function( dimensions ) {
					handler.container.ratio = dimensions[1] / dimensions[0];
					handler.video = handler.$container.find( 'iframe' ).get( 0 );
					handler.deferred.resolve( handler );
					$( window ).on( 'resize', function() {
						if ( handler.container.ratio ) {
							handler.resizeVideo.call( handler );
						}
					} );
				} );
			}
		},
		playVideo: function() {
			this.player.play();
		},
		pauseVideo: function() {
			this.player.pause();
		},
		setMuted: function() {
			this.player.setVolume( 0 );
		},
		generateID: function() {
			var newID = '', loop = 10, characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', charactersLength = characters.length;
			do {
				newID = '';
				loop = 10;
				while ( loop -- ) {
					newID += characters.charAt( Math.floor( Math.random() * charactersLength ) );
				}
			} while( $( '#' + newID ).length );
			return newID;
		}
	} );

	function string2Hash( str, seed = 0 ) {
	    var h1 = 0xdeadbeef ^ seed, h2 = 0x41c6ce57 ^ seed;
	    for ( var i = 0, ch; i < str.length; i++ ) {
	        ch = str.charCodeAt(i);
	        h1 = Math.imul(h1 ^ ch, 2654435761);
	        h2 = Math.imul(h2 ^ ch, 1597334677);
	    }
	    h1 = Math.imul(h1 ^ (h1>>>16), 2246822507) ^ Math.imul(h2 ^ (h2>>>13), 3266489909);
	    h2 = Math.imul(h2 ^ (h2>>>16), 2246822507) ^ Math.imul(h1 ^ (h1>>>13), 3266489909);
	    return 4294967296 * (2097151 & h2) + (h1>>>0);
	}

	document.addEventListener( 'DOMContentLoaded', function() {
		$( 'body' ).on( 'click', '#loftocean-fullscreen-media-wrapper .close-button', function ( e ) {
			e.preventDefault();
			var $close = $( this );
			if ( $close.data( 'player' ) ) {
				$close.data( 'player' ).pauseVideo();
				$close.data( 'player', false );
			}
			$( '#loftocean-fullscreen-media-wrapper' ).removeClass( 'show' ).addClass( 'hide' );
			$('body').css( 'overflow', '' );
		} );
		$( document ).on( 'video.play', function( e, el, video ) {
			if ( ( typeof el != 'undefined' ) && $( el ).length && ( typeof video != 'undefined' ) ) {
				var $btn = $( el ), clearData = true, videoID = string2Hash( video );
				// Return if still process previous clicking
				if ( $btn.data( 'clicking' ) ) {
					return false;
				}
				$btn.data( 'clicking', true );
				if ( $btn.data( 'videoManager' ) && $btn.data( 'player' ) ) {
					$btn.data( 'videoManager' ).showVideo( $btn.data( 'player' ) );
					$btn.data( 'clicking', false );
				} else {
					var $wrap = $( '#loftocean-fullscreen-media-wrapper' );
					if ( ! $wrap.length ) {
						$wrap = $( '<div>', { 'id': 'loftocean-fullscreen-media-wrapper', 'class': loftoceanFullscreenVideos.wrapClass + ' hide' } ).append(
							$( '<div>', { 'class': 'close-button', 'text': 'close' } )
						);
						$( 'body' ).append( $wrap );
					}
					$btn.data( 'videoManager', new FullscreenVideo( video, $wrap, videoID, $btn ) );
				}
			}
		} );

		if ( loftoceanFullscreenVideos && loftoceanFullscreenVideos.videos && Array.isArray( loftoceanFullscreenVideos.videos ) ) {
			$( '.video-block .video-play-btn, .featured-video-play-btn' ).on( 'click', function( e ) {
				var $btn = $( this ), videoID = 'loftocean-fullscreen-'  + $btn.data( 'loftocean-video-id' );
				// Return if still process previous clicking
				if ( $btn.data( 'clicking' ) ) {
					return false;
 				}
 				$btn.data( 'clicking', true );
				if ( $btn.data( 'videoManager' ) && $btn.data( 'player' ) ) {
					$btn.data( 'videoManager' ).showVideo( $btn.data( 'player' ) );
					$btn.data( 'clicking', false );
				} else {
					if ( $btn.data( 'loftocean-video-id' ) ) {
						var vid = $btn.data( 'loftocean-video-id' ).replace( 'video-id-', '' );
						if ( loftoceanFullscreenVideos.videos[ vid ] ) {
							var $wrap = $( '#loftocean-fullscreen-media-wrapper' );
							if ( ! $wrap.length ) {
								$wrap = $( '<div>', { 'id': 'loftocean-fullscreen-media-wrapper', 'class': loftoceanFullscreenVideos.wrapClass + ' hide' } ).append(
									$( '<div>', { 'class': 'close-button', 'text': 'close' } )
								);
								$( 'body' ).append( $wrap );
							}
							$btn.data( 'videoManager', new FullscreenVideo( loftoceanFullscreenVideos.videos[ vid ], $wrap, videoID, $btn ) );
						}
					}
				}
			} ).on( 'shown.loftocean.video', function( e, player ) {
				e.preventDefault();
				$( this ).data( 'player', player ).data( 'clicking', false );
			} ).on( 'done.loftocean.video', function( e ) {
				e.preventDefault();
				$( this ).data( 'clicking', false );
			} );

			$( document ).on( 'autoplay.loftocean.video', function( e, args ) {
				if ( args && args[ 'video' ] && args[ 'container' ] && $( args[ 'container' ] ).length ) {
					new AutoPlayVideo( args[ 'video' ], $( args['container'] ), args['args'] || false );
				}
			} );
		}
	} );
} )( jQuery );
