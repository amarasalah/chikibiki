<?php
namespace LoftOcean\Custom_Post_Type;

if ( ! class_exists( '\LoftOcean\Custom_Post_Type\Manager' ) ) {
    class Manager {
        /**
         * Instance
         * @access private
         * @static
         */
        private static $_instance = null;
        /**
         * Ensures only one instance of the class is loaded or can be loaded.
         * @access public
         * @static
         */
        public static function _instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
        /**
        * Construct function
        */
        public function __construct() {
            $this->load_files();
            add_filter( 'loftocean_get_custom_post_type_list', array( $this, 'get_custom_post_type_list' ), 10, 2 );
			add_filter( 'template_include', array( $this, 'custom_post_type_template' ), 9999999 );
        }
        /**
        * Load files
        */
        protected function load_files() {
            $inc = LOFTOCEAN_DIR . 'includes/custom-post-types/';
            require_once $inc . 'class-custom-blocks.php';
            require_once $inc . 'class-custom-site-headers.php';
            require_once $inc . 'class-custom-rooms.php';
            require_once $inc . 'class-imported-bookings.php';
        }
        /**
        * Get custom post type list
        */
        public function get_custom_post_type_list( $list, $post_type = 'custom_blocks' ) {
            $ppp = 100; $paged = 0; $stop = false;
            $found = array( '0' => esc_html__( 'Choose from the list', 'loftocean' ) );
			while( ! $stop ) {
				$blocks = get_posts( array(
					'post_type' => $post_type,
					'offset' => ( $paged * $ppp ),
					'posts_per_page' => $ppp,
					'post_status' => 'any'
				) );
				foreach( $blocks as $b ) {
                    $found[ $b->ID ] = $b->post_title;
                }
                $stop = ( $ppp > count( $blocks ) );
                $paged ++;
			}
			return $found;
        }
		/*
		* Custom post type templates
		*/
		public function custom_post_type_template( $template ) {
			if ( is_singular( 'custom_site_headers' ) ) {
				$new_template = locate_template( array( 'single-custom_site_headers.php' ) );
				if ( '' != $new_template ) {
					return $new_template ;
				}
			} else if ( is_singular( 'custom_blocks' ) ) {
				$new_template = locate_template( array( 'single-custom_blocks.php' ) );
				if ( '' != $new_template ) {
					return $new_template ;
				}
			} else if ( is_singular( 'loftocean_room' ) ) {
                $new_template = LOFTOCEAN_DIR . 'template-parts/single-room.php';
                if ( file_exists( $new_template ) ) {
                    return $new_template ;
                }
            } else if ( isset( $_GET[ 'search_rooms' ] ) ) {
                $new_template = LOFTOCEAN_DIR . 'template-parts/search-rooms.php';
                if ( file_exists( $new_template ) ) {
                    return $new_template ;
                }
            }
			return $template;
		}
    }
    add_action( 'loftocean_load_core_modules', '\LoftOcean\Custom_Post_Type\Manager::_instance' );
}
