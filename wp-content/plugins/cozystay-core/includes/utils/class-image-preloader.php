<?php
namespace LoftOcean\Utils;

if ( ! class_exists( '\LoftOcean\Utils\Image_Preloader' ) ) {
	class Image_Preloader {
		/**
		* Object current class instance
		*/
		public static $_instance = false;
		/**
		* Placeholder image size
		*/
		public $placeholder_size = 'medium';
		/**
		* Construct function
		*/
		public function __construct() {
			add_filter( 'wp_lazy_loading_enabled', '__return_false', 99999999, 3 );
			add_filter( 'loftocean_media_get_background_image', array( $this, 'get_preload_bg' ), 10, 4 );
			add_filter( 'loftocean_media_get_background_image_attrs', array( $this, 'get_preload_bg_attrs' ), 10, 4 );
			add_filter( 'loftocean_media_get_responsive_image', array( $this, 'get_responsive_image' ), 10, 4 );
			add_filter( 'wp_get_attachment_image_attributes', array( $this, 'lazy_load_image_attrs' ), 99999999, 3 );

			add_action( 'loftocean_pre_post_list', array( $this, 'pre_post_list' ) );
			add_action( 'loftocean_reset_post_list', array( $this, 'reset_post_list' ) );
			add_action( 'loftocean_media_the_background_image', array( $this, 'the_preload_bg' ), 10, 4 );
			add_action( 'loftocean_media_the_background_image_attrs', array( $this, 'the_preload_bg_attrs' ), 10, 4 );
			add_action( 'loftocean_media_the_responsive_image', array( $this, 'the_responsive_image' ), 10, 3 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			add_action( 'init', array( $this, 'set_settings' ) );
		}
		/**
		* Set preloader image size
		*/
		public function set_settings() {
			$this->placeholder_size = apply_filters( 'loftocean_placeholder_size', 'medium' );
		}
		/**
		* Get preload background html
		* @param string previous attribute string
		* @param int image id
		* @param array image sizes ['normal-size', 'retina-size']
		* @return string changed attribute string
		*/
		public function get_preload_bg_attrs( $attrs, $id, $sizes = array( 'full', 'full' ), $skip_lazy_load = false ) {
			if ( ! \LoftOcean\media_exists( $id ) || ( ! is_array( $sizes ) ) ) {
				return $attrs;
			}

			if ( ( count( $sizes ) === 1 ) || apply_filters( 'loftocean_disable_image_loading_optization', true ) ) {
				$attrs = sprintf( 'background-image: url(%s);', esc_url( $this->get_image_src( $id, $sizes[0] ) ) );
			} else {
				$srcs = $this->get_image_srcs( $id, $sizes );
				if ( ! empty( $srcs ) ) {
					$attrs = '';
					if ( apply_filters( 'loftocean_disable_media_preload', false ) ) {
						$attrs = sprintf(
							'%1$s" data-loftocean-image="1" data-loftocean-normal-image="%2$s" data-loftocean-retina-image="%3$s',
							apply_filters( 'loftocean_enable_media_lazy_load', false ) ? '' : sprintf( 'background-image: url(%s);', esc_url( $srcs['normal_image_src'] ) ),
							esc_url( $srcs['normal_image_src'] ),
							esc_url( $srcs['retina_image_src'] )
						);
					} else if ( ( $srcs['normal_image_src'] == $srcs['preload_image_src'] ) && ( $srcs['retina_image_src'] == $srcs['preload_image_src'] ) ) {
						$attrs = sprintf( 'background-image: url(%s);', esc_url( $srcs['preload_image_src'] ) );
					} else if ( $skip_lazy_load ) {
						$attrs = sprintf(
							'background-image: url(%1$s); filter: blur(5px);" data-loftocean-image="1" data-loftocean-normal-image="%2$s" data-loftocean-retina-image="%3$s',
							esc_url( $srcs['normal_image_src'] ),
							esc_url( $srcs['normal_image_src'] ),
							esc_url( $srcs['retina_image_src'] )
						);
					} else {
						$attrs = sprintf(
							'background-image: url(%1$s); filter: blur(5px);" data-loftocean-image="1" data-loftocean-normal-image="%2$s" data-loftocean-retina-image="%3$s',
							esc_url( $srcs['preload_image_src'] ),
							esc_url( $srcs['normal_image_src'] ),
							esc_url( $srcs['retina_image_src'] )
						);
					}
				}
			}
			return $attrs;
		}
		/**
		* Get preload background html
		* @param int image id
		* @param string image size
		* @param array options
		* @return string html
		*/
		public function get_preload_bg( $html, $id, $sizes = array( 'full', 'full' ), $args = array() ) {
			$skip_lazy_load = ( ! empty( $args['attrs'] ) ) && ( ! empty( $args['attrs']['data-no-lazy'] ) );
			$preload_attrs = $this->get_preload_bg_attrs( '', $id, $sizes, $skip_lazy_load );
			if ( ! empty( $preload_attrs ) ) {
				$attrs = empty( $args['attrs'] ) ? array() : $args['attrs'];
				if ( ! empty( $args['class'] ) ) {
					$attrs['class'] = $args['class'];
				}
				$styles = '';
				if ( ! empty( $attrs['style'] ) ) {
					$styles = sprintf( '%s %s', $attrs['style'], $preload_attrs );
					unset( $attrs['style'] );
				} else {
					$styles = $preload_attrs;
				}
				return sprintf(
					'<%1$s style="%2$s" %3$s>%4$s</%1$s>',
					empty( $args['tag'] ) ? 'div' : $args['tag'],
					$styles,
					$this->get_attributes( $attrs ),
					empty( $args['html'] ) ? '' : $args['html']
				);
			}
			return '';
		}
		/**
		* Output the preload background html attributes
		* @param int image id
		* @param array image sizes ['normal-size', 'retina-size']
		* @return string changed attribute string
		*/
		public function the_preload_bg_attrs( $id, $sizes = array( 'full', 'full' ), $style = '', $skip_lazy_load = false ) {
			if ( ! \LoftOcean\media_exists( $id ) || ! is_array( $sizes ) ) {
				return false;
			}

			if ( ( count( $sizes ) === 1 ) || apply_filters( 'loftocean_disable_image_loading_optization', true ) ) {
				printf( ' style="%1$sbackground-image: url(%2$s);"', esc_attr( $style ), esc_attr( $this->get_image_src( $id, $sizes[0] ) ) );
			} else {
				$srcs = $this->get_image_srcs( $id, $sizes );
				if ( ! empty( $srcs ) ) {
					$attrs = '';
					if ( apply_filters( 'loftocean_disable_media_preload', false ) ) {
						$style = empty( $style ) ? array() : array( $style );
						apply_filters( 'loftocean_enable_media_lazy_load', false ) ? '' : array_push( $style, sprintf( 'background-image: url(%s);', esc_url( $srcs['normal_image_src'] ) ) );
						printf(
							'%1$s data-loftocean-image="1" data-loftocean-normal-image="%2$s" data-loftocean-retina-image="%3$s" ',
							count( $style ) > 0 ? sprintf( ' style="%s"', esc_attr( implode( ' ', $style ) ) ) : '',
							esc_attr( $srcs['normal_image_src'] ),
							esc_attr( $srcs['retina_image_src'] )
						);
					} else if ( ( $srcs['normal_image_src'] === $srcs['preload_image_src'] ) && ( $srcs['retina_image_src'] === $srcs['preload_image_src'] ) ) {
						printf( ' style="%1$sbackground-image: url(%2$s);"', esc_attr( $style ), esc_attr( $srcs['preload_image_src'] ) );
					} else if ( $skip_lazy_load ) {
						printf(
							' style="%1$sbackground-image: url(%2$s); filter: blur(5px);" data-loftocean-image="1" data-loftocean-normal-image="%3$s" data-loftocean-retina-image="%4$s" ',
							esc_attr( $style ),
							esc_url( $srcs['normal_image_src'] ),
							esc_attr( $srcs['normal_image_src'] ),
							esc_attr( $srcs['retina_image_src'] )
						);
					} else {
						printf(
							' style="%1$sbackground-image: url(%2$s); filter: blur(5px);" data-loftocean-image="1" data-loftocean-normal-image="%3$s" data-loftocean-retina-image="%4$s" ',
							esc_attr( $style ),
							esc_url( $srcs['preload_image_src'] ),
							esc_attr( $srcs['normal_image_src'] ),
							esc_attr( $srcs['retina_image_src'] )
						);
					}
				}
			}
		}
		/**
		* Output the preload background html
		* @param int image id
		* @param string image size
		* @param array options
		*/
		public function the_preload_bg( $id, $sizes = array( 'full', 'full' ), $args = array() ) {
			if ( \LoftOcean\media_exists( $id ) ) {
				$attrs = empty( $args['attrs'] ) ? array() : $args['attrs'];
				if ( ! empty( $args['class'] ) ) {
					$attrs['class'] = $args['class'];
				}
				$style = '';
				if ( ! empty( $attrs['style'] ) ) {
					$style = $attrs['style'];
					unset( $attrs['style'] );
				}
				$tag = empty( $args['tag'] ) ? 'div' : $args['tag'];
				$skip_lazy_load = ! empty( $attrs['data-no-lazy'] );
				if ( empty( $args['html'] ) ) : ?>
					<<?php echo esc_attr( $tag ); $this->the_attributes( $attrs ); $this->the_preload_bg_attrs( $id, $sizes, $style, $skip_lazy_load ); ?>></<?php echo esc_attr( $tag ); ?>> <?php
				else : ?>
					<<?php echo esc_attr( $tag ); $this->the_attributes( $attrs ); $this->the_preload_bg_attrs( $id, $sizes, $style, $skip_lazy_load ); ?>><?php echo wp_kses_post( $args['html'] ); ?></<?php echo esc_attr( $tag ); ?>> <?php
				endif;
			}
		}
		/**
		* Get preload image html
		* @param int image id
		* @param string image size
		* @param array options
		* @return string html
		*/
		public function get_responsive_image( $html, $id, $size = 'full', $args = array() ) {
			$image_src = $this->get_image_src( $id, $size );
			if ( ! empty( $image_src ) ) {
				$attrs = empty( $args['attrs'] ) ? array() : $args['attrs'];
				$attrs['alt'] 	= isset( $attrs['alt'] ) ? $attrs['alt'] : $this->get_image_alt( $id );
				$attrs['data-no-lazy'] = '1';
				$attrs['class'] = empty( $attrs['class'] ) ? 'skip-lazy' : 'skip-lazy ' . $attrs['class'];

				return wp_get_attachment_image( $id, $size, '', $attrs );
			}
			return '';
		}
		/**
		* Output the preload image html
		* @param int image id
		* @param string image size
		* @param array options
		* @return string html
		*/
		public function the_responsive_image( $id, $size = 'full', $args = array() ) {
			$image_src = $this->get_image_src( $id, $size );
			if ( ! empty( $image_src ) ) {
				$attrs = empty( $args['attrs'] ) ? array() : $args['attrs'];
				$attrs['alt'] 	= isset( $attrs['alt'] ) ? $attrs['alt'] : $this->get_image_alt( $id );
				$attrs['class'] = empty( $attrs['class'] ) ? 'skip-lazy' : 'skip-lazy ' . $attrs['class'];
				echo wp_get_attachment_image( $id, $size, '', $attrs );
			}
		}
		/**
		* Helpre function to get the image srcs
		* @param int image id
		* @param array image sizes ['normal-size', 'retina-size']
		* @return mix array if exists, otherwise boolean false
		*/
		private function get_image_srcs( $id, $sizes = array( 'full', 'full' ) ) {
			return \LoftOcean\media_exists( $id ) ? array(
				'preload_image_src' => $this->get_image_src( $id, $this->placeholder_size ),
				'normal_image_src' 	=> $this->get_image_src( $id, $sizes[0] ),
				'retina_image_src'	=> $this->get_image_src( $id, $sizes[1] )
			) : false;
		}
		/**
		* Helper function to get the html attributes
		* @param array attributes
		* @return string html attributes
		*/
		private function get_attributes( $attrs ) {
			if ( ! empty( $attrs ) && is_array( $attrs ) ) {
				$items = array();
				foreach ( $attrs as $key => $item ) {
					if ( ! empty( $key ) ) {
						$item = ( 'style' == $key ) ? $item : esc_attr( $item );
						$items[] = sprintf( '%1$s="%2$s"', esc_attr( $key ), esc_attr( $item ) );
					}
				}
				return implode( ' ', $items );
			}
			return is_string( $attrs ) ? $attrs : '';
		}
		/**
		* Helper function to output the html attributes
		* @param array attributes
		*/
		private function the_attributes( $attrs ) {
			if ( ! empty( $attrs ) && is_array( $attrs ) ) {
				foreach ( $attrs as $key => $item ) {
					if ( ! empty( $key ) ) {
						$item = ( 'style' === $key ) ? $item : esc_attr( $item );
						printf( ' %1$s="%2$s"', esc_attr( $key ),  esc_attr( $item ) );
					}
				}
			}
		}
		/**
		* Helpre function to get the image src
		* @param int image id
		* @param array image size
		* @return mix string if exists, otherwise boolean false
		*/
		private function get_image_src( $id, $size = 'full' ) {
			return \LoftOcean\get_image_src( $id, $size );
		}
		/**
		* Get image alt text
		* @param int image id
		* @return string image alt text
		*/
		public function get_image_alt( $image_id ) {
			return \LoftOcean\get_image_alt( $image_id );
		}
		// Enqueue script for background image preloader
		public function enqueue_scripts() {
			wp_enqueue_style( 'jquery-daterangepicker', LOFTOCEAN_ASSETS_URI . 'libs/daterangepicker/daterangepicker.min.css', array(), '3.1.1' );
			wp_enqueue_script( 'moment', LOFTOCEAN_ASSETS_URI . 'libs/daterangepicker/moment.min.js', array(), '2.18.1', true );
			wp_enqueue_script( 'jquery-daterangepicker', LOFTOCEAN_ASSETS_URI . 'libs/daterangepicker/daterangepicker.min.js', array( 'jquery', 'moment' ), LOFTOCEAN_ASSETS_VERSION, true );

			wp_enqueue_script(
				'loftocean-front-media',
				LOFTOCEAN_URI . 'assets/scripts/front/front-media.min.js',
				array( 'jquery-daterangepicker' ),
				LOFTOCEAN_ASSETS_VERSION,
				true
			);
			wp_localize_script( 'loftocean-front-media', 'loftoceanImageLoad', array(
				'lazyLoadEnabled' => apply_filters( 'loftocean_enable_media_lazy_load', false ),
				'reservation' => array(
					'room' => array( 'single' => esc_html__( 'Room', 'loftocean' ), 'plural' => esc_html__( 'Rooms', 'loftocean' ), 'usePluralIfZero' => apply_filters( 'loftocean_room_use_plural_if_rooms_number_is_zero', false ) ),
					'adult' => array( 'single' => esc_html__( 'Adult', 'loftocean' ), 'plural' => esc_html__( 'Adults', 'loftocean' ), 'usePluralIfZero' => apply_filters( 'loftocean_room_use_plural_if_adults_number_is_zero', false ) ),
					'child' => array( 'single' => esc_html__( 'Child', 'loftocean' ), 'plural' => esc_html__( 'Children', 'loftocean' ), 'usePluralIfZero' => apply_filters( 'loftocean_room_use_plural_if_children_number_is_zero', false ) )
				)
			) );
		}
		/**
		* Action fired before rendering post list
		*/
		public function pre_post_list() {
			add_filter( 'wp_get_attachment_image_attributes', array( $this, 'image_attrs' ), 999999, 3 );
		}
		/**
		* Action fired after rendering post list
		*/
		public function reset_post_list() {
			remove_filter( 'wp_get_attachment_image_attributes', array( $this, 'image_attrs' ), 999999 );
		}
		/**
		* Modify attachment image attributes
		*/
		public function image_attrs( $attrs, $attachment = '', $size = '' ) {
			$newAttrs = apply_filters( 'loftocean_modify_image_attributes', false, $attrs );
			if ( ( ! empty( $newAttrs ) ) && is_array( $newAttrs ) ) {
				$attrs = array_merge( $attrs, (array) $newAttrs );
			}
			return $attrs;
		}
		/**
		* Lazy load attributes for responsive image
		*/
		public function lazy_load_image_attrs( $attrs, $attachment = '', $size = '' ) {
			if ( ( ! is_admin() ) || wp_doing_ajax() ) {
				$progressive_enabled = false;
				$lazy_enabled = false;
				$has_src = ! empty( $attrs['src'] );
				$has_srcset = ! empty( $attrs['srcset'] );
				$has_size = ! empty( $attrs['sizes'] );
				$is_svg = ( false !== strpos( $attrs['src'], 'svg' ) );
				$enable_lazy_load = empty( $attrs['data-no-lazy'] ) && ( empty( $attrs['class'] ) || ( false === strpos( $attrs['class'], 'skip-lazy' ) ) );
				$progress_enabled_gloablly = ! apply_filters( 'loftocean_disable_media_preload', false );
				// Image progressive loading
				if ( $progress_enabled_gloablly ) {
					if ( $has_size && $has_srcset ) {
						$progressive_enabled = true;
						$attrs['data-loftocean-lazy-load-sizes'] = $attrs['sizes'];
						$attrs['sizes'] = '255px';
					} else {
						unset( $attrs['sizes'], $attrs['srcset'] );
					}
				}
				// Image lazy loading
				if ( apply_filters( 'loftocean_enable_media_lazy_load', false ) && $enable_lazy_load && ( $has_src || $has_srcset ) && ( ! $progress_enabled_gloablly ) ) {
					$lazy_enabled = true;
					$attrs['style'] = empty( $attrs['style'] ) ? 'opacity: 0;' : $attrs['style'] . ' opacity: 0;';
					if ( $has_src ) {
						$attrs['data-src'] = $attrs['src'];
					}
					if ( $has_srcset ) {
						$attrs['data-srcset'] = $attrs['srcset'];
					}
					if ( $has_size ) {
						$attrs['data-loftocean-lazy-load-sizes'] = $attrs['sizes'];
					}
					unset( $attrs['src'], $attrs['sizes'], $attrs['srcset'] );
				}
				// Check if need to enable JavaScript to load the image
				if ( $is_svg ) {
					unset( $attrs['sizes'], $attrs['srcset'], $attrs['data-srcset'], $attrs['data-loftocean-lazy-load-sizes'] );
				}
				if ( $lazy_enabled || ( ( ! $is_svg ) && $progressive_enabled ) ) {
					$attrs['data-loftocean-loading-image' ] = 'on';
				}
			}
			return $attrs;
		}
		/**
		* Instantiate class to make sure only once instance exists
		*/
		public static function _instance() {
			if ( false === self::$_instance ) {
				self::$_instance = new Image_Preloader();
			}
			return self::$_instance;
		}
	}
	// Add action to initialize Instagram
	add_action( 'loftocean_load_core_modules', array( 'LoftOcean\Utils\Image_Preloader', '_instance' ) );
}
