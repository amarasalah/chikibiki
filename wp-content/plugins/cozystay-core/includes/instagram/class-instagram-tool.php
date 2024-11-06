<?php
namespace LoftOcean\Instagram;
/**
* Instagram tool class
*/
if ( ! class_exists( '\LoftOcean\Instagram\Feed_Tool' ) ) {
	class Feed_Tool {
		public function __construct() {
			add_action( 'wp', array( $this, 'wp' ) );
			add_action( 'loftocean_instagram_actions', array( $this, 'wp' ) );
			add_action( 'delete_attachment', array( $this, 'check_feed_attachment_map' ) );
			add_action( 'rest_api_init', array( $this, 'init_rest' ) );
			add_filter( 'loftocean_instagram_get_feed', array( $this, 'get_feeds' ), 10, 3 );
			add_filter( 'loftocean_instagram_get_feed_list', array( $this, 'get_feed_list' ) );
		}
		/**
		* Filters for frontend calling
		*/
		public function wp() {
			add_filter( 'loftocean_instagram_has_feed', array( $this, 'has_feed' ), 10, 2 );
			add_filter( 'loftocean_instagram_get_html', array( $this, 'get_html' ), 10, 5 );
			add_action( 'loftocean_instagram_the_html', array( $this, 'the_html' ), 10, 4 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts') );
		}
		/**
		* @description get instagram feed from transient or from instagram site
		* @param string feed ID
		* @param int number of feeds to get
		* @return mix if feeds exists, return array of feeds otherwise return boolean false
		*/
		public function get_feeds( $instagram, $feed = '', $limit = 20 ) {
			$has_token = function_exists( '\sbi_get_database_settings' );

			$db_user = 'feed' . $feed;

			if ( ! class_exists( '\LoftOcean\Instagram\Feeds' ) ) {
				require_once LOFTOCEAN_DIR . 'includes/instagram/class-instagram-feeds.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
				new Feeds( $limit );
			}
			$feeds = $has_token ? apply_filters( 'loftocean_get_instagram_feed', '', $feed ) : false;

			if ( empty( $feeds ) ) {
				$feeds = get_option( 'loftocean_instagram-' . $db_user, array() );
				return empty( $feeds ) ? array() : maybe_unserialize( $feeds );
			} else {
				update_option( 'loftocean_instagram-' . $db_user, maybe_serialize( $feeds ) );
				return $feeds;
			}
		}
		/**
		* Test if have instagram feed
		* @param boolean
		* @param string
		* @return boolean
		*/
		public function has_feed( $has, $feed ) {
			$feeds = apply_filters( 'loftocean_instagram_get_feed', false, $feed );
			if ( ! empty( $feeds ) && is_array( $feeds ) ) {
				return true;
			}
			return false;
		}
		/**
		* @description callback function of filter loftocean_instagram_html, to get instagram feeds html
		* @param string default html string
		* @param string feed ID
		* @param int number of feeds to return
		* @return string feed list html
		*/
		public function get_html( $html, $feed, $limit = 0, $new_tab = false, $args = array() ) {
			$feeds = apply_filters( 'loftocean_instagram_get_feed', false, $feed );
			if ( ! empty( $feeds ) && is_array( $feeds ) ) {
				if ( is_array( $feeds ) && ( count( $feeds ) > 0 ) ) {
					if ( $limit > 0 ) {
						$feeds = array_slice( $feeds, 0, $limit );
					}
					$target = $new_tab ? ' target="_blank" rel="noopenner noreferrer"' : '';
					$html = '<ul>';
					foreach ( $feeds as $ins ) {
						$html .= '<li>';
						$html .= sprintf( '<a href="%s"%s>', esc_url( $ins['link'] ), $target );
						if ( ( ! empty( $ins['attachment_id'] ) ) && ( false !== get_post_status( $ins['attachment_id'] ) ) ) {
							$html .= apply_filters(
								'loftocean_media_get_background_image',
								'',
								$ins['attachment_id'],
								apply_filters( 'loftocean_get_image_sizes', array( 'full', 'full' ), array( 'module' => 'instagram', 'args' => $args ) ),
								array( 'class' => 'feed-bg' )
							);
						} else {
							$html .= '<div class="feed-bg" style="background-image: url(' . esc_url( $ins['url'] ) . ');"></div>';
						}
						$html .= '</a>';
						$html .= '</li>';
					}
					$html .= '</ul>';
					return $html;
				}
			}
			return false;
		}
		/**
		* Output the instagram feeds html
		* @param string feed ID
		* @param int number of feeds to return
		* @param boolean open link in new tab
		*/
		public function the_html( $feed, $limit = 0, $new_tab = false, $args = array() ) {
			$feeds = apply_filters( 'loftocean_instagram_get_feed', false, $feed );
			if ( ! empty( $feeds ) && is_array( $feeds ) ) {
				if ( is_array( $feeds ) && ( count( $feeds ) > 0 ) ) :
					$show_ul = empty( $args['no_ul'] );
					if ( $limit > 0 ) {
						$feeds = array_slice( $feeds, 0, $limit );
					}
					if ( $show_ul ) : ?><ul><?php endif;
					foreach ( $feeds as $ins ) : ?>
						<li>
							<a href="<?php echo esc_url( $ins['link'] ); ?>"<?php if ( $new_tab ) : ?> target="_blank" rel="noopenner noreferrer"<?php endif; ?>><?php
							if ( ( ! empty( $ins['attachment_id'] ) ) && ( false !== get_post_status( $ins['attachment_id'] ) ) ):
								do_action(
									'loftocean_media_the_background_image',
									$ins['attachment_id'],
									apply_filters( 'loftocean_get_image_sizes', array( 'full', 'full' ), array( 'module' => 'instagram', 'args' => $args ) ),
									array( 'class' => 'feed-bg' )
								);
							else : ?>
								<div class="feed-bg" style="background-image: url(<?php echo esc_url( $ins['url'] ); ?>);"></div><?php
							endif; ?>
							</a>
						</li> <?php
					endforeach;
					if ( $show_ul ) : ?></ul><?php endif;
				endif;
			}
		}
		/**
		* Enqueue instagram related scripts
		*/
		public function enqueue_scripts() {
			if ( 'ajax' == apply_filters( 'loftocean_instagram_render_method', '' ) ) {
				wp_register_script( 'loftocean-instagram', LOFTOCEAN_URI . 'assets/scripts/front/instagram.min.js', array( 'wp-api' ), LOFTOCEAN_ASSETS_VERSION, true );
				wp_localize_script( 'loftocean-instagram', 'loftoceanInstagram', array(
					'class' => apply_filters( 'loftocean_instagram_widget_class', 'loftocean-widget_instagram' ),
					'isMobile' => apply_filters( 'loftocean_is_mobile', false ),
					'apiRoot' => esc_url_raw( get_rest_url() )
				) );
				wp_enqueue_script( 'loftocean-instagram' );
			}
		}
		/**
		* Register REST endpoint for instagram
		*/
		public function init_rest() {
			if ( ! class_exists( 'REST_API' ) ) {
				require_once LOFTOCEAN_DIR . 'includes/instagram/class-instagram-rest-api.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
				new REST_API();
			}
		}
		/**
		* Check feed attachment map when delete attachment
		*/
		public function check_feed_attachment_map( $post_id ) {
			$map = get_option( 'loftocean_instagram_feed_attachment_map', false );
			if ( ! empty( $map ) && is_array( $map ) ) {
				foreach( $map as $key => $pid ) {
					if ( $post_id == $pid ) {
						unset( $map[ $key] );
						update_option( 'loftocean_instagram_feed_attachment_map', $map );
						break;
					}
				}
			}
		}
		/**
		* Get feed list created by Smash Balloon Instagram Feed
		*/
		public function get_feed_list( $list ) {
			return \LoftOcean\get_instagram_feeds();
		}
	}
	new Feed_Tool();
}
