<?php
namespace LoftOcean\Utils;
/*
 *************************************************************************************
 * Initial verison
 *		1. Remove core attributes for w3c html validation
 *		2. $image_size tell the image size for featured background image
 *		3. Add background image preloader to improve user experience
 *************************************************************************************
 */

if ( ! class_exists( '\LoftOcean\Utils\Core' ) ) {
	class Core {
		/**
		* Object current class instance
		*/
		public static $_instance = false;
		/**
		* Boolean is mobile device
		*/
		protected $is_mobile = null;
		/**
		* Construct function
		*/
		public function __construct() {
			add_filter( 'safe_style_css', array( $this, 'safe_style_css' ) );
			add_filter( 'get_custom_logo', array( $this, 'custom_logo' ), 10, 2 );
			add_filter( 'wp_get_attachment_image', array( $this, 'get_attachment_image' ), 99999, 5 );
			add_filter( 'loftocean_posts_args', array( $this, 'list_post_args' ), 10, 2 );
			add_filter( 'loftocean_is_mobile', array( $this, 'test_mobile_device' ) );
			add_filter( 'upload_mimes', array( $this, 'add_custom_upload_mimes' ) );
			add_filter( 'pre_get_document_title', array( $this, 'get_document_title' ), 9999, 1 );
			remove_filter( 'get_the_excerpt', 'wpautop' );

			add_action( 'loftocean_woocommerce_the_short_description', array( $this, 'the_short_description' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'change_datepicker_week_start_on' ), 99 );
			add_shortcode( 'loftocean_year', array( $this, 'current_year' ) );
		}
		/**
		* Add support for display when using wp_kses to sanitize content
		*/
		public function safe_style_css( $styles ) {
		   $styles[] = 'display';
		   return $styles;
		}
		// Remove the itemprop attribute
		public function custom_logo( $html, $blog_id ) {
			$html = str_replace(array( ' itemprop="url"', ' itemprop="logo"' ), '', $html);
			return $html;
		}
		/**
		* Parse WP_Query arguments for post list
		* @param array
		* @param string filter
		* @return array
		*/
		public function list_post_args( $args, $filter ) {
			if ( ! empty( $filter ) ) {
				switch ( $filter ) {
					case 'featured':
						$args = array_merge( $args, array(
							'ignore_sticky_posts' 	=> true,
							'meta_key' 				=> 'loftocean-featured-post',
							'meta_value'			=> 'on'
						) );
						break;
					case 'views':
						$args = array_merge( $args, array(
							'ignore_sticky_posts' 	=> true,
							'orderby'  				=> 'meta_value_num',
							'meta_key'  			=> 'loftocean-view-count',
							'order' 				=> 'DESC'
						) );
						break;
					case 'likes':
						$args = array_merge($args, array(
							'ignore_sticky_posts' 	=> true,
							'orderby'   			=> 'meta_value_num',
							'meta_key'  			=> 'loftocean-like-count',
							'order' 				=> 'DESC'
						) );
						break;
				}
			}
			return $args;
		}
		/**
		* Test if is mobile device
		*/
		public function test_mobile_device( $is_mobile ) {
			if ( null === $this->is_mobile ) {
				if ( isset( $_SERVER, $_SERVER[ 'HTTP_USER_AGENT' ] ) && ( ! is_null( $_SERVER[ 'HTTP_USER_AGENT' ] ) ) ) {
					if ( ! class_exists( '\LoftOcean\Libraries\Mobile_Detect' ) ) {
						require_once LOFTOCEAN_DIR . 'includes/libraries/mobile-detect/Mobile_Detect.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
					}
					$device_detect = new \LoftOcean\Libraries\Mobile_Detect();
					$this->is_mobile = $device_detect->isMobile();
				} else {
					return false;
				}
			}
			return $this->is_mobile;
		}
		/**
		* Fix image output html for svg
		*/
		public function get_attachment_image( $html, $attachment_id, $size, $icon, $attr = array() ) {
			$mime_type = get_post_mime_type( $attachment_id );
			if ( ! empty( $mime_type ) && ( false !== strpos( $mime_type, 'svg' ) ) ) {
				$size_class = is_array( $size ) ? implode( 'x', $size ) : $size;
				$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
				$default_attr = array(
					'class' => "attachment-$size_class size-$size_class",
					'alt'   => empty( $alt ) ? '' : trim( strip_tags( $alt ) )
				);
				$attr = wp_parse_args( $attr, $default_attr );
				unset( $attr['sizes'], $attr['srcset'] );

				if ( is_array ( $size ) ) {
					$actual_width = $size[0];
				} else {
					$image_sizes = $this->get_image_size( $size );
					$actual_width = empty( $image_size ) ? 200 : $image_size['width'];
				}
				$actual_height = $this->get_actual_image_height( get_attached_file( $attachment_id ), $actual_width );

				$html = sprintf( '<img width="%1$s" height="%2$s"', $actual_width, $actual_height );
				foreach ( $attr as $name => $value ) {
		            $html .= " $name=" . '"' . $value . '"';
		        }
				$html .= ' />';
			}
			return $html;
		}
		/**
		* Get image size details by image size name
		*/
		protected function get_image_size( $size ) {
			if ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
				return array(
					'width' => get_option( $size . '_size_w' ),
					'height' => get_option( $size . '_size_h' )
				);
			}
			global $_wp_additional_image_sizes;
			if ( isset( $_wp_additional_image_sizes[ $size ] ) ) {
			   return array(
				   'width' => $_wp_additional_image_sizes[ $size ]['width'],
				   'height' => $_wp_additional_image_sizes[ $size ]['height']
			   );
		   }
		   return false;
		}
		/**
		* Get actual image height
		*/
		protected function get_actual_image_height( $file, $width ) {
			if ( file_exists( $file ) ) {
				if ( function_exists( '\simplexml_load_file' ) ) {
					$xml = \simplexml_load_file( $file );
				} else {
					require_once LOFTOCEAN_DIR . 'includes/libraries/simple-html-dom/simple_html_dom.php';
					$xml = \LoftOcean\Libraries\simplexml_load_file( $file );
				}

				if ( $xml !== false ) {
		            $attr = $xml->attributes();
		            $viewbox = explode( ' ', $attr->viewBox );
		            $raw_width = isset( $attr->width ) && preg_match( '/\d+/', $attr->width, $value ) ? (int) $value[0] : ( isset( $viewbox[2] ) ? (int) $viewbox[2] : null );
		            $raw_height = isset( $attr->height ) && preg_match( '/\d+/', $attr->height, $value) ? (int) $value[0] : ( isset( $viewbox[3] ) ? (int) $viewbox[3] : null );
					return empty( $raw_height ) || empty( $raw_width ) ? $width : intval( $raw_height / $raw_width * $width, 10 );
				}
			}
			return $width;
		}
		/**
		* Add font files allowed for media library
		*/
		public function add_custom_upload_mimes( $mimes ) {
			$mimes['woff'] = 'application/x-font-woff';
			$mimes['woff2'] = 'application/font-woff2';
			return $mimes;
		}
		/**
		* Show short description
		*/
		public function the_short_description( $extra_class = '' ) {
			ob_start();
			add_filter( 'woocommerce_short_description', array( $this, 'product_list_short_description' ), 0 );
			woocommerce_template_single_excerpt();
			remove_filter( 'woocommerce_short_description', array( $this, 'product_list_short_description' ), 0 );
			$excerpt = ob_get_clean();
			if ( ! empty( $extra_class ) ) {
				$excerpt = str_replace( 'class="woocommerce-product-details__short-description', sprintf( 'class="woocommerce-product-details__short-description %s', $extra_class ), $excerpt );
			}
			echo $excerpt;
		}
		/**
		* Change WooCommerce product list short description length
		*/
		public function product_list_short_description( $excerpt ) {
			$length = apply_filters( 'loftocean_woocommerce_short_description_length', 15 );
			$length = absint( $length );
			$length = empty( $length ) ? 15 : $length;

			$excerpt = strip_shortcodes( $excerpt );
			$excerpt = excerpt_remove_blocks( $excerpt );
			$excerpt = str_replace( ']]>', ']]&gt;', $excerpt );
			return wp_trim_words( $excerpt, $length, '...' );
		}
		/**
		* Get document title
		*/
		public function get_document_title( $title ) {
			if ( isset( $_GET, $_GET[ 'search_rooms' ] ) ) {
				$sep = apply_filters( 'document_title_separator', '-' );
				$parts = array( 'title' => esc_html__( 'Room Search Results', 'loftocean' ), 'site' => get_bloginfo( 'name', 'display' ) );
				$parts = apply_filters( 'document_title_parts', $parts );
				return implode( ' ' . $sep . ' ', array_filter( $parts ) );
			}
			return $title;
		}
		/**
		* Change datepicker week start on 
		*/
		public function change_datepicker_week_start_on() {
			wp_localize_script( 'jquery-daterangepicker', 'datepickerSettings', array( 'weekStartsOn' => get_option( 'start_of_week', 0 ) ) );
		}
		/**
		* Shortcode current year 
		*/
		public function current_year() {
			return date_i18n( apply_filters( 'loftocean_year_format', 'Y' ) );
		}
		/**
		* Instantiate class to make sure only once instance exists
		*/
		public static function _instance() {
			if ( false === self::$_instance ) {
				self::$_instance = new Core();
			}
			return self::$_instance;
		}
	}
	// Add action to initialize Instagram
	add_action( 'loftocean_load_core_modules', array( 'LoftOcean\Utils\Core', '_instance' ) );
}
