<?php
namespace LoftOcean\Utils;
if ( ! class_exists( '\LoftOcean\Utils\Social_Sharing' ) ) {
	class Social_Sharing {
		/**
		* Object current class instance
		*/
		public static $_instance = false;
		/**
		* Construct function
		*/
		public function __construct() {
			add_filter( 'loftocean_has_social_sharing_icons', array( $this, 'has_social_icons' ), 99, 2 );
			add_action( 'loftocean_the_social_sharing_icons', array( $this, 'the_social_sharing_icons' ), 20, 2 );
        }
		/**
		* Output share like button
		*/
		protected function the_share_like_icon() {
			$like_number = apply_filters( 'loftocean_get_post_metas_like_count', 0 );
			$raw_number = get_post_meta( get_the_ID(), 'loftocean-like-count', true ); ?>
			<a
				target="_blank"
				title="<?php esc_attr_e( 'Like', 'loftocean' ); ?>"
				href="#"
				class="post-like sharing-like-icon loftocean-like-meta"
				data-post-id="<?php the_ID(); ?>"
				data-like-count="<?php echo esc_attr( intval( $raw_number ) ); ?>"
			>
				<i class="fas fa-heart"></i>
				<span><?php esc_html_e( 'Like', 'loftocean' ); ?></span>
				<span class="like-count count"><?php echo esc_html( $like_number ); ?></span>
			</a><?php
		}
		/**
		* Test if have any social sharing icons
		* @param boolean
		* @param array enabled social icons from user
		* @return boolean
		*/
		public function has_social_icons( $has, $enabled ) {
			$supported = array( 'like', 'facebook', 'twitter', 'pinterest', 'linkedin', 'whatsapp', 'reddit', 'email' );
			if ( is_array( $enabled ) ) {
				$intersection = array_intersect( $supported, $enabled );
				return is_array( $intersection ) && ( count( $intersection ) > 0 );
			}
			return $has;
		}
		/**
		* Display content sharing html
		*/
		public function the_social_sharing_icons( $enabled, $with_number = true ) {
			if ( ! apply_filters( 'loftocean_has_social_sharing_icons', false, $enabled ) ) {
				return '';
			}

			$enabled = (array) $enabled;
			$socials = $this->get_social_settings();
			$pid = get_the_ID();
			foreach ( $socials as $sid => $social ) :
				if ( in_array( $sid, $enabled ) ) :
					if ( 'like' == $sid ) :
						$this->the_share_like_icon();
					else : ?>
						<a
							class="popup-window loftocean-social-share-icon"
							target="_blank"
							title="<?php echo esc_attr( $social['title'] ); ?>"
							href="<?php echo esc_url( $social['url'] ); ?>"
							data-social-type="<?php echo esc_attr( $sid ); ?>"
							data-<?php echo esc_attr( $sid ); ?>-post-id="<?php echo esc_attr( $pid ); ?>"
							data-raw-counter="<?php echo esc_attr( $social['rawCounter'] ); ?>"
							<?php if ( ! empty( $social['attrs'] ) ) : ?> data-props="<?php echo esc_attr( $social['attrs'] ); ?>"<?php endif; ?>
						>
							<?php echo wp_kses_post( $social['icon'] ); ?>
							<span><?php echo esc_html( $social['title'] ); ?></span>
							<?php if ( $with_number ) : ?><span class="count"><?php echo esc_html( $social['formatCounter'] ); ?></span><?php endif; ?>
						</a> <?php
					endif;
				endif;
			endforeach;
		}
		/**
		* Helper function to get social icon settings
		*/
		protected function get_social_settings() {
			$pid = get_the_ID();
			$url = rawurlencode( get_permalink() );
			$title = get_the_title();
			$title = empty( $title ) ? '' : rawurlencode( html_entity_decode( strip_tags( $title ), ENT_QUOTES, 'UTF-8' ) );
			$excerpt = rawurlencode( get_the_excerpt() );
			$media = has_post_thumbnail() ? '&media=' . rawurlencode( wp_get_attachment_url( get_post_thumbnail_id() ) ) : '';
			$counters = $this->get_counter();
			return array(
				'like'	=> array(
					'attrs' => '',
					'icon'  => '<i class="fas fa-heart"></i>',
					'title'	=> esc_html__( 'Like', 'loftocean' ),
					'url' 	=> '#'
				),
				'facebook' => array(
					'attrs'	=> '',
					'icon' 	=> '<i class="fab fa-facebook"></i>',
					'title' => esc_html__( 'Facebook', 'loftocean' ),
					'url' 	=> 'https://www.facebook.com/sharer.php?u=' . $url . '&t=' . $title,
					'rawCounter' => $counters['facebook'],
					'formatCounter' => \LoftOcean\counter_format( $counters['facebook'] )
				),
				'twitter' => array(
					'attrs'	=> '',
					'icon' 	=> '<i class="fab fa-twitter"></i>',
					'title' => esc_html__( 'Twitter', 'loftocean' ),
					'url' 	=> 'https://twitter.com/share?text=' . $title . '&url=' . $url,
					'rawCounter' => $counters['twitter'],
					'formatCounter' => \LoftOcean\counter_format( $counters['twitter'] )
				),
				'pinterest' => array(
					'attrs'	=> 'width=757,height=728',
					'icon' 	=> '<i class="fab fa-pinterest"></i>',
					'title' => esc_html__( 'Pinterest', 'loftocean' ),
					'url' 	=> 'https://www.pinterest.com/pin/create/bookmarklet/?url=' .  $url . '&description=' . $title . $media,
					'rawCounter' => $counters['pinterest'],
					'formatCounter' => \LoftOcean\counter_format( $counters['pinterest'] )
				),
				'linkedin' => array(
					'attrs'	=> 'width=757,height=728',
					'icon' 	=> '<i class="fab fa-linkedin-in"></i>',
					'title' => esc_html__( 'LinkedIn', 'loftocean' ),
					'url' 	=> 'https://www.linkedin.com/shareArticle?mini=true&url=' . $url . '&title=' . $title,
					'rawCounter' => $counters['linkedin'],
					'formatCounter' => \LoftOcean\counter_format( $counters['linkedin'] )
				),
				'yummly' => array(
					'attrs'	=> '',
					'icon' 	=> '<img width="18" height="18" src="' . LOFTOCEAN_ASSETS_URI . 'images/yummly.png" alt="yummly">',
					'title' => esc_html__( 'Yummly', 'loftocean' ),
					'url' 	=> 'http://www.yummly.com/urb/verify?url=' . $url . '&title=' . $title . '&yumtype=button',
					'rawCounter' => $counters['yummly'],
					'formatCounter' => \LoftOcean\counter_format( $counters['yummly'] )
				),
				'whatsapp' => array(
					'attrs'	=> 'width=757,height=728',
					'icon' 	=> '<i class="fab fa-whatsapp"></i>',
					'title' => esc_html__( 'WhatsApp', 'loftocean' ),
					'url' 	=> 'https://api.whatsapp.com/send?text=' . $title . ' ' . $url,
					'rawCounter' => $counters['whatsapp'],
					'formatCounter' => \LoftOcean\counter_format( $counters['whatsapp'] )
				),
				'reddit' => array(
					'attrs'	=> '',
					'icon' 	=> '<i class="fab fa-reddit"></i>',
					'title' => esc_html__( 'Reddit', 'loftocean' ),
					'url' 	=> 'https://www.reddit.com/submit?url=' . $url . '&title=' . $title,
					'rawCounter' => $counters['reddit'],
					'formatCounter' => \LoftOcean\counter_format( $counters['reddit'] )
				),
				'email' => array(
					'attrs'	=> '',
					'icon' 	=> '<i class="fas fa-envelope"></i>',
					'title' => esc_html__( 'Email', 'loftocean' ),
					'url' 	=> 'mailto:?subject=I wanted you to see this page "' . $title . '"&amp;body=Check out this site ' . $url,
					'rawCounter' => $counters['email'],
					'formatCounter' => \LoftOcean\counter_format( $counters['email'] )
				)
			);
		}
		/**
		* Get social counter
		*/
		protected function get_counter() {
			$current = get_post_meta( get_the_ID(), 'loftocean_social_counters', true );
			$socials = array( 'facebook', 'twitter', 'pinterest', 'linkedin', 'yummly', 'whatsapp', 'reddit', 'email' );
			$counter = array_fill_keys( $socials, 0 );
			if ( is_array( $current ) && ( count( $current ) > 0 ) ) {
				foreach( $socials as $s ) {
					if ( ! empty( $current[ $s ] ) && absint( $current[ $s ] ) ) {
						$counter[ $s ] = absint( $current[ $s ] );
					}
				}
			}
			return $counter;
		}
		/**
		* Instantiate class to make sure only once instance exists
		*/
		public static function _instance() {
			if ( false === self::$_instance ) {
				self::$_instance = new Social_Sharing();
			}
			return self::$_instance;
		}
    }
    add_action( 'loftocean_load_core_modules', array( 'LoftOcean\Utils\Social_Sharing', '_instance' ) );
}
