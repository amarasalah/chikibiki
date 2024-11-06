<?php
namespace LoftOcean\Instagram;

if ( ! class_exists( '\LoftOcean\Instagram\Feeds' ) ) {
	class Feeds {
		protected $feed_attachment_map = false;
		protected $limit = 20;
		/**
		* Construct function
		*/
		public function __construct( $limit = 20 ) {
			$this->limit = $limit;
			add_filter( 'loftocean_get_instagram_feed', array( $this, 'get_feeds' ), 10, 2 );
		}
		/**
		* Get feeds by token
		*/
		public function get_feeds( $feeds, $feed = '' ) {
			if ( function_exists( 'sbi_get_database_settings' ) ) {
				$feeds = \LoftOcean\get_instagram_feeds();
				$pro_version_enabled = class_exists( '\SB_Instagram_Settings_Pro' );
				$atts = array( 'num' => $this->limit );
				if ( \LoftOcean\is_valid_array( $feeds ) ) {
					// $feed = get_option( 'loftocean_instagram_token_feed', 'legacy' );
					$feed = empty( $feed ) ? 'legacy' : $feed;
					$atts[ 'feed' ] = empty( $feed ) ? 'legacy' : $feed;
				}
				$new_plugin = method_exists( \SB_Instagram_Feed::class, 'set_cache' );
				$database_settings = sbi_get_database_settings();
				if ( ! $new_plugin && empty( $database_settings['connected_accounts'] ) ) {
					return false;
				}

				$instagram_feed_settings = $pro_version_enabled ? ( new \SB_Instagram_Settings_Pro( $atts, $database_settings, false ) )
									: ( new \SB_Instagram_Settings( $atts, $database_settings, false ) );
				$instagram_feed_settings->set_feed_type_and_terms();
				$instagram_feed_settings->set_transient_name();
				$transient_name = $instagram_feed_settings->get_transient_name();
				$settings = $instagram_feed_settings->get_settings();
				$feed_type_and_terms = $instagram_feed_settings->get_feed_type_and_terms();
				$instagram_feed = $pro_version_enabled ? ( new \SB_Instagram_Feed_Pro( $transient_name ) ) : ( new \SB_Instagram_Feed( $transient_name ) );
				if ( $new_plugin ) {
					$instagram_feed->set_cache( $instagram_feed_settings->get_cache_time_in_seconds(), $settings );
				}

				if ( $instagram_feed->regular_cache_exists() ) {
				    $instagram_feed->add_report( 'page load caching used and regular cache exists' );
				    $instagram_feed->set_post_data_from_cache();

				    if ( $instagram_feed->need_posts( $settings['num'] ) && $instagram_feed->can_get_more_posts() ) {
				        while ( $instagram_feed->need_posts( $settings['num'] ) && $instagram_feed->can_get_more_posts() ) {
				            $instagram_feed->add_remote_posts( $settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
				        }
				        $instagram_feed->cache_feed_data( $instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled'] );
				    }

				} else {
				    $instagram_feed->add_report( 'no feed cache found' );

				    while ( $instagram_feed->need_posts( $settings['num'] ) && $instagram_feed->can_get_more_posts() ) {
				        $instagram_feed->add_remote_posts( $settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
				    }

				    if ( ! $instagram_feed->should_use_backup() ) {
				        $instagram_feed->cache_feed_data( $instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled'] );
				    }

				}

				if ( $instagram_feed->should_use_backup() ) {
				    $instagram_feed->add_report( 'trying to use backup' );
				    $instagram_feed->maybe_set_post_data_from_backup();
				    $instagram_feed->maybe_set_header_data_from_backup();
				}

				$post_data = $instagram_feed->get_post_data();
				$post_data = array_slice( $post_data, 0, $settings['num'] );
				$feeds = array();
				if ( \LoftOcean\is_valid_array( $post_data ) ) {
					global $sb_instagram_posts_manager;
					if ( $sb_instagram_posts_manager->image_resizing_disabled() ) {
						foreach ( $post_data as $data ) {
							$type = $this->get_type( $data['media_url'] );
							array_push( $feeds, array(
								'description'   => empty( $data['caption'] ) ? esc_attr__( 'Instagram Image', 'loftocean') : $data['caption'],
								'link'		  	=> esc_url_raw( $data['permalink'] ),
								'attachment_id'	=> $this->get_attachment_id( $data['id'] ),
								'url'			=> empty( $data['thumbnail_url'] ) ? $data['media_url'] : $data['thumbnail_url'],
								'feed_id'		=> $data['id'],
								'type'		  	=> $type
							) );
						}
					} else {
						$image_ids = array();
						foreach ( $post_data as $post ) {
							$image_ids[] = \SB_Instagram_Parse::get_post_id( $post );
						}
						$resized_images = \SB_Instagram_Feed::get_resized_images_source_set( $image_ids, 0, $transient_name );
						foreach ( $post_data as $data ) {
							$type = $this->get_type( $data['media_url'] );
							$url = empty( $data['thumbnail_url'] ) ? $data['media_url'] : $data['thumbnail_url'];
							if ( \LoftOcean\is_valid_array( $resized_images ) && isset( $resized_images[ $data['id'] ] ) ) {
								$sizes = $resized_images[ $data['id'] ]['sizes'];
								if ( \LoftOcean\is_valid_array( $sizes ) ) {
									$upload = wp_upload_dir();
									$file_dir = trailingslashit( $upload[ 'basedir' ] ) . trailingslashit( SBI_UPLOADS_NAME ) . $resized_images[ $data['id'] ]['id'];
									$suffix = file_exists( $file_dir . 'full.jpg' ) ? 'full'
										: ( file_exists( $file_dir . 'low.jpg' ) ? 'low' : ( file_exists( $file_dir . 'thumb.jpg' ) ? 'thumb' : '' ) );
									$url = empty( $suffix ) ? $url : ( \sbi_get_resized_uploads_url() . $resized_images[ $data['id'] ]['id'] . $suffix . '.jpg' );
								}
							}
							array_push( $feeds, array(
								'description'   => empty( $data['caption'] ) ? esc_attr__( 'Instagram Image', 'loftocean') : $data['caption'],
								'link'		  	=> esc_url_raw( $data['permalink'] ),
								'attachment_id'	=> $this->get_attachment_id( $data['id'] ),
								'url'			=> esc_url( $url ),
								'feed_id'		=> $data['id'],
								'type'		  	=> $type
							) );
						}
					}
				}
				return $feeds;
			}
			return false;
		}
		/**
		* Get instagram post type
		*/
		protected function get_type( $url ) {
			if ( ! empty( $url ) ) {
				if ( preg_match( '/\.(mp4|mov)($|\?)/i', $url ) ) {
					return 'video';
				} else if ( preg_match( '/\.(jpeg|jpg|png|bmp|gif)($|\?)/i', $url ) ) {
					return 'image';
				}
			}
			return false;
		}
		/**
		* Get attachment id by instagram feed id
		*/
		protected function get_attachment_id( $id ) {
			if ( false === $this->feed_attachment_map ) {
				$this->feed_attachment_map = get_option( 'loftocean_instagram_feed_attachment_map', array() );
			}
			return isset( $this->feed_attachment_map, $this->feed_attachment_map[ 'item-' . $id ] ) ? $this->feed_attachment_map[ 'item-' . $id ] : '';
		}
	}
}
