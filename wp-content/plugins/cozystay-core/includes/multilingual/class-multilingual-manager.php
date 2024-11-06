<?php
namespace LoftOcean\Multilingual;
/**
* Multilingual Manager Class
*/
if ( ! class_exists( '\LoftOcean\Multilingual\Manager' ) ) {
	class Manager {
        /**
        * Make sure only one instance exists
        */
        public static $_instance = false;
        /**
        * Construction function
        */
		public function __construct() {
			add_filter( 'loftocean_multilingual_website_enabled', array( $this, 'is_multilingual_website' ) );
			add_action( 'after_setup_theme', array( $this, 'check_multilingual_plugin' ), 999 );
		}
		/**
		* Register hooks
		*/
		public function check_multilingual_plugin() {
            if ( function_exists( 'pll_current_language' ) ) {
                require_once LOFTOCEAN_DIR . 'includes/multilingual/class-polylang.php';
            } else if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
                require_once LOFTOCEAN_DIR . 'includes/multilingual/class-wpml.php';
            }
		}
		/**
		* If is multilingual website
		*/
		public function is_multilingual_website( $is ) {
			return function_exists( 'pll_current_language' ) || defined( 'ICL_LANGUAGE_CODE' );
		}
		/**
		* Instantiate class to make sure only once instance exists
		*/
		public static function _instance() {
			if ( false === self::$_instance ) {
				self::$_instance = new Manager();
			}
			return self::$_instance;
		}
	}
	// Add action to initialize Instagram
	add_action( 'loftocean_load_core_modules', array( 'LoftOcean\Multilingual\Manager', '_instance' ) );
}
