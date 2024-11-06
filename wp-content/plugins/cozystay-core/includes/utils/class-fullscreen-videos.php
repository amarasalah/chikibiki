<?php
namespace LoftOcean\Utils;
if ( ! class_exists( '\LoftOcean\Utils\Fullscreen_Videos' ) ) {
	class Fullscreen_Videos {
		/**
		* Object current class instance
		*/
		public static $_instance = false;
        /**
        * Array videos need to be fullscreen
        */
		public static $videos = array();
        /**
        * Boolean if there is any video from vimeo
        */
		public static $has_vimeo = false;
        /**
        * Boolean if there is any video from youtube
        */
		public static $has_youtube = false;
        /**
        * Regex used to test if the video is from vimeo
        */
		public $vimeo_regex = '/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)/';
		/**
		* Regex used to test if the video is from youtube
		*/
		public $youtube_regex = '/^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?\'"]*).*/';
		/**
		* Regex used to test if the video is reghular html5 video
		*/
		public $html5_video_regex = '/<video[^>]*>.*<\/video>/';
		/**
		* Construct function
		*/
		public function __construct() {
            add_filter( 'loftocean_get_current_video_id', array( $this, 'get_current_video_id' ), 10, 2 );
			add_filter( 'loftocean_has_video', array( $this, 'has_video' ) );
			add_filter( 'loftocean_get_videos', array( $this, 'get_videos' ) );
    		add_action( 'wp_footer', array( $this, 'enqueue_scripts' ), 0 );
    		add_action( 'rest_api_init', array( $this, 'request_post_videos' ) );
		}
        /**
        * Get current video id
        */
        public function get_current_video_id( $id, $video ) {
			if ( empty( $video ) ) {
				return $id;
			}

            $id = count( self::$videos );
            array_push( self::$videos, $video );
			if ( preg_match( $this->youtube_regex, $video ) ) {
                self::$has_youtube = true;
            } else if ( preg_match( $this->vimeo_regex, $video ) ) {
                self::$has_vimeo = true;
            }
            return $id;
        }
		/**
        * Enqueue script for background image preloader
        */
		public function enqueue_scripts() {
			$has_videos = is_array( self::$videos ) && ( count( self::$videos ) > 0 );
			$has_dynamica_videos = apply_filters( 'loftocean_has_dynamic_videos', true );
            if ( $has_videos || $has_dynamica_videos ) {
				wp_enqueue_script( 'loftocean-video-player', LOFTOCEAN_URI . 'assets/scripts/front/video-player.min.js', array( 'jquery' ), LOFTOCEAN_ASSETS_VERSION, true );
				wp_localize_script( 'loftocean-video-player', 'loftoceanFullscreenVideos', array(
                    'videos' => self::$videos,
                    'wrapClass' => apply_filters( 'loftocean_fullscreen_video_wrap_class', 'loftocean-media-wrapper loftocean-media-fullscreen-playing' )
                ) );
			}
		}
		/**
		* Get current videos
		*/
		public function get_videos( $videos ) {
			return self::$videos;
		}
		/**
		* If current have video
		*/
		public function has_video( $has ) {
			return is_array( self::$videos ) && ( count( self::$videos ) > 0 );
		}
		/**
		* Register REST request API route for post videos
		*/
		public function request_post_videos() {
			register_rest_route( 'loftocean/v1', '/post-vidoes/(?P<pids>.+)', array(
				'permission_callback' => '__return_true',
				'methods' => \WP_REST_Server::READABLE,
				'callback' => array( $this, 'post_videos_cb' )
			) );
		}
		/**
		* REST API request handler for post videos
		*/
		public function post_videos_cb( $data ) {
			$pids = isset( $data[ 'pids' ] ) ? explode( ',', $data[ 'pids' ] ) : false;
			if ( $pids ) {
				$pids = array_unique( array_filter( $pids ) );
				if ( count( $pids ) > 0 ) {
					$videos = array();
					foreach( $pids as $pid ) {
						if ( ( false !== get_post_status( $pid ) ) && ( 'video' == get_post_format( $pid ) ) ) {
							$video = get_post_meta( $pid, 'loftocean_post_format_video', true );
							if ( ! empty( $video ) ) {
								if ( has_shortcode( $video, 'video' ) ) {
									$videos[ 'post-' . $pid ] = do_shortcode( $video );
								} else if ( preg_match( $this->html5_video_regex, $video ) || preg_match( $this->youtube_regex, $video ) || preg_match( $this->vimeo_regex, $video ) ) {
									$videos[ 'post-' . $pid ] = $video;
								}
							}
						}
					}
					if ( count( $videos ) > 0 ) return array( 'status' => 'success', 'data' => $videos );
				}
			}
			return array( 'status' => 'fail', 'data' => false );
		}
 		/**
		* Instantiate class to make sure only once instance exists
		*/
		public static function _instance() {
			if ( false === self::$_instance ) {
				self::$_instance = new Fullscreen_Videos();
			}
			return self::$_instance;
		}
	}
	// Add action to initialize Instagram
	add_action( 'loftocean_load_core_modules', array( 'LoftOcean\Utils\Fullscreen_Videos', '_instance' ) );
}
