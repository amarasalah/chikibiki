<?php
namespace LoftOcean\Instagram;

if ( ! class_exists( '\LoftOcean\Instagram\REST_API' ) ) {
	class REST_API {
		protected $downloader = false;
		// Register REST APIs
		public function __construct() {
			// Get instagram feeds
			register_rest_route( 'loftocean/v1', '/instagram/(?P<id>.+)/(?P<location>.+)/(?P<column>.+)/(?P<mobile>.+)', array(
				'methods' 	=> 'GET',
				'permission_callback' => '__return_true',
				'callback' 	=> array( $this, 'get_instagram_feeds' )
			) );

			// Remove instagram feed cache
			register_rest_route( 'loftocean/v1', '/clear-instagram-cache/', array(
				'methods' 	=> 'GET',
				'permission_callback' => '__return_true',
				'callback' 	=> array( $this, 'clear_instagram_cache' )
			) );

			// Regenerate instagram feed cache
			register_rest_route( 'loftocean/v1', '/get-latest-instagram/', array(
				'methods' 	=> 'GET',
				'permission_callback' => '__return_true',
				'callback' 	=> array( $this, 'get_latest_instagram_feeds' )
			) );

			// Download instagram feed
			register_rest_route( 'loftocean/v1', '/download-instagram-feed/(?P<ids>.+)', array(
				'methods' 	=> 'GET',
				'permission_callback' => '__return_true',
				'callback' 	=> array( $this, 'download_instagram_feeds' )
			) );

			// Clear storage on server
			register_rest_route( 'loftocean/v1', '/clear-instagram-feed-storage/', array(
				'methods' 	=> 'GET',
				'permission_callback' => '__return_true',
				'callback' 	=> array( $this, 'clear_instagram_feed_storage' )
			) );

			// Auto download Instagram images
			register_rest_route( 'loftocean/v1', '/auto-download-instagram-feeds/', array(
				'methods' 	=> 'GET',
				'permission_callback' => '__return_true',
				'callback' 	=> array( $this, 'auto_download_instagram_feeds' )
			) );
		}
		/**
		* REST API get instagram feeds for ajax request
		*/
		public function get_instagram_feeds( $data ) {
			$location = isset( $data['location'] ) ? $data['location'] : 'footer';
			$column = isset( $data['column'] ) ? $data['column'] : 4;
			$args = array( 'location' => $location, 'column' => $column );
			$has_srcs = false;
			if ( ! empty( $data['mobile'] ) ) {
				add_filter( 'loftocean_is_mobile', '__return_true', 999 );
			}
			$sizes = apply_filters( 'loftocean_get_image_sizes', array( 'full', 'full' ), array( 'module' => 'instagram', 'args' => $args ) );
			$has_srcs = is_array( $sizes ) && ( ! empty( $sizes ) );
			$feeds = apply_filters( 'loftocean_instagram_get_feed', false, $data['id'] );
			$has_srcs = array_fill( 0, count( $feeds ), $has_srcs );
			$sizes = array_fill( 0, count( $feeds ), $sizes );
			return array_map( function( $feed, $has, $size ) {
				if ( isset( $feed[ 'url' ] ) ) {
					$feed[ 'url' ] = str_replace( '&#038;', '&', $feed[ 'url' ] );
				}
				if ( $has && ( ! empty( $feed['attachment_id'] ) ) && ( false !== get_post_status( $feed['attachment_id'] ) ) ) {
					$feed['srcs'] = $this->get_srcs( $feed['attachment_id'], $size );
				}
				return $feed;
			}, $feeds, $has_srcs, $sizes );
		}
		/**
		* REST API clear instagram feeds cache
		*/
		public function clear_instagram_cache() {
			global $wpdb;
			return $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%loftocean_instagram-%'" );
		}
		/**
		* REST API get latest instagram feeds for customizer
		*/
		public function get_latest_instagram_feeds( $data ) {
			$this->clear_instagram_cache();
			$feeds = apply_filters( 'loftocean_instagram_get_feed', '' );
			if ( ! empty( $feeds ) ) {
				$data = array_map( function( $feed ) {
					return $feed['feed_id'];
				}, $feeds );
				return array( 'success' => 1, 'data' => $data );
			} else {
				return array( 'success' => 0 );
			}
		}
		/**
		* REST API download instagram feed to local server
		*/
		public function download_instagram_feeds( $data ) {
			if ( ! empty( $data['ids'] ) ) {
				if ( ! class_exists( 'Download_Instagram_Feeds' ) ) {
					require_once LOFTOCEAN_DIR . 'includes/instagram/class-download-instagram-feeds.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
					$this->downloader = new Download_Instagram_Feeds();
				}
				$this->downloader->init( $data['ids'] );
				return true;
			} else {
				return false;
			}
		}
		/**
		* REST API clear storage
		*/
		public function clear_instagram_feed_storage() {
			$limit = 20;
			$map = get_option( 'loftocean_instagram_feed_attachment_map', array() );
			if ( ( ! empty( $map ) ) && ( count( $map ) > $limit ) ) {
				$remove = array_splice( $map, $limit );
				if ( is_array( $remove ) && ( ! empty( $remove ) ) ) {
					foreach( $remove as $key => $id ) {
						if ( false !== get_post_status( $id ) ) {
							wp_delete_attachment( $id, true );
						}
					}
				}
				update_option( 'loftocean_instagram_feed_attachment_map', $map );
			}
		}
		/**
		* REST API auto download feeds
		*/
		public function auto_download_instagram_feeds() {
			$this->clear_instagram_cache();
			$feeds = apply_filters( 'loftocean_instagram_get_feed', '' );
			if ( ! empty( $feeds ) ) {
				$data = array_map( function( $feed ) {
					return $feed['feed_id'];
				}, $feeds );

				if ( ! class_exists( 'Download_Instagram_Feeds' ) ) {
					require_once LOFTOCEAN_DIR . 'includes/instagram/class-download-instagram-feeds.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
					$this->downloader = new Download_Instagram_Feeds();
				}

				$this->downloader->init( implode( ',', $data ) );
				$this->clear_instagram_feed_storage();
			}
		}
		/**
		* Get feed background image srcs
		*/
		protected function get_srcs( $id, $sizes ) {
			$srcs = array();
			foreach( $sizes as $size ) {
				array_push( $srcs, \LoftOcean\get_image_src( $id, $size ) );
			}
			return $srcs;
		}
	}
}
