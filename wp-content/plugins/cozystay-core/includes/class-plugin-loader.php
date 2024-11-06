<?php
namespace LoftOcean;
/**
* Plugin Loader
*/
if ( ! class_exists( '\LoftOcean\Loader' ) ) {
	class Loader {
        /**
        * Loader class instance
        */
		protected static $instance = null;
        /**
        * Construct function
        */
		public function __construct() {
			$this->load_textdomain();
			$this->plugin_constants();
			$this->includes();
			$this->load_modules();
		}
		/**
		* Load text domain
		*/
		protected function load_textdomain() {
			load_plugin_textdomain( 'loftocean' );
		}
		/*
		* Define plugin constant
		*/
		protected function plugin_constants() {
			$this->define( 'LOFTOCEAN_DIR', plugin_dir_path( dirname( __FILE__ ) ) );
			$this->define( 'LOFTOCEAN_URI', plugins_url( '/', dirname( __FILE__ ) ) );
            $this->define( 'LOFTOCEAN_ASSETS_URI', LOFTOCEAN_URI . 'assets/' );
			$this->define( 'LOFTICEAN_SECONDS_IN_DAY', 86400 );
			$this->define( 'LOFTOCEAN_FLOAT_EPSILON', 0.0000001 );
		}
		/*
		* The actual function to test and define constant
		*/
		protected function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}
		/**
		* @description include required files
		*/
		private function includes() {
			$inc = LOFTOCEAN_DIR . 'includes/';

			// Load privacy class
			require_once $inc . 'class-privacy.php';

			// Import abstract classes
			require_once $inc . 'abstracts/abstract-widget.php';
			require_once $inc . 'abstracts/abstract-option-setting.php';

			// Import required functions
			require_once $inc . 'functions/sanitize-functions.php';
			require_once $inc . 'functions/functions.php';
			require_once $inc . 'functions/filter-functions.php';
			require_once $inc . 'utils/class-image-preloader.php';
			require_once $inc . 'utils/class-wp-core.php';
			require_once $inc . 'utils/class-metas.php';
			require_once $inc . 'utils/class-social-sharing.php';
			require_once $inc . 'utils/class-fullscreen-videos.php';
			require_once $inc . 'utils/class-yoast-seo.php';
			require_once $inc . 'utils/class-icons-manager.php';

			// Import modules
			require_once $inc . 'gutenberg/class-gutenberg-manager.php';
 			require_once $inc . 'widgets/class-widget-manager.php';
			require_once $inc . 'instagram/class-instagram-manager.php';

			require_once $inc . 'admin/class-admin-manager.php';
			require_once $inc . 'metas/class-taxonomy-metas.php';
			require_once $inc . 'metas/class-user-metas.php';
			require_once $inc . 'metas/class-post-metas.php';

			// multilingual related
			require_once $inc . 'multilingual/class-multilingual-manager.php';

			// Elementor extension
			require_once $inc . 'elementor/class-elementor-manager.php';

			// Custom post type
			require_once $inc . 'custom-post-types/class-manager.php';
		}
		/**
		* Load modules if they are enabled
		*/
		public function load_modules() {
			do_action( 'loftocean_load_core_modules' );
		}
		/**
		* @descirption initialize extenstion
		*/
		public static function init() {
			if ( null === self::$instance ) {
				self::$instance = new Loader();
			}
			return self::$instance;
		}
	}
}
