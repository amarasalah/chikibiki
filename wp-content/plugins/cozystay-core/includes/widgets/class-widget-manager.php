<?php
namespace LoftOcean\Widget;
/**
* Site wide widget manager class
*/

if ( ! class_exists( '\LoftOcean\Widget\Manager' ) ) {
	class Manager {
		/**
		* Object current class instance
		*/
		public static $_instance = false;
		/**
		* Array with widget class name as id and filename as value
		*/
		private $widgets;
		/**
		* Construct function
		*/
		public function __construct () {
			$this->setup_env();

			add_action( 'admin_print_scripts-widgets.php', array( $this, 'enqueue_widget_assets' ) );
			add_action( 'admin_print_footer_scripts-widgets.php', array( $this, 'enqueue_widget_scripts' ) );
			add_action( 'loftocean_elementor_editor_enqueue_assets', array( $this, 'enqueue_widget_scripts' ) );
			add_action( 'widgets_init', array( $this, 'register_widgets' ) );
			add_filter( 'loftocean_get_registered_widgets', array( $this, 'get_widgets' ), 1, 1 );
		}
		/**
		* Setup environment needed for homepage widgets registration
		*/
		private function setup_env() {
			$inc = LOFTOCEAN_DIR . 'includes/widgets/';
			$this->widgets = array(
				'LoftOcean\Widget\Facebook' => $inc . 'class-widget-facebook.php',
				'LoftOcean\Widget\Posts' 	=> $inc . 'class-widget-posts.php',
				'LoftOcean\Widget\Category' => $inc . 'class-widget-category.php',
				'LoftOcean\Widget\Profile' 	=> $inc . 'class-widget-profile.php',
				'LoftOcean\Widget\Social' 	=> $inc . 'class-widget-social.php'
			);
		}
		/**
		* The default homepage widgets settings
		* @param array
		* @return array
		*/
		public function get_widgets( $widgets ) {
			if ( is_array( $widgets ) && ! empty( $widgets ) ) {
				$widgets = array_merge( $widgets, $this->widgets );
			} else {
				$widgets = $this->widgets;
			}
			return $widgets;
		}
		/**
		* Register widgets
		*/
		public function register_widgets() {
			$widgets = apply_filters( 'loftocean_get_registered_widgets', array() );
			if ( is_array( $widgets ) && is_array( $widgets ) ) {
				foreach ( $widgets as $wn => $wfn ) {
					if ( file_exists( $wfn ) ) {
						require_once $wfn;
						register_widget( $wn );
					}
				}
			}
		}
		/**
		* Enqueue widget related style assets
		*/
		public function enqueue_widget_assets() {
			wp_enqueue_style( 'loftocean-widgets', LOFTOCEAN_URI . 'assets/styles/widgets.min.css', array(), LOFTOCEAN_ASSETS_VERSION );
		}
		/**
		* Enqueue required scripts
		*/
		public function enqueue_widget_scripts() {
			$asset_version 		= LOFTOCEAN_ASSETS_VERSION;
			$asset_uri 			= LOFTOCEAN_URI . 'assets/';
			$widget_dependency 	= array( 'jquery', 'editor', 'wp-util', 'wp-color-picker', 'loftocean-widget-lib', 'jquery-ui-core', 'jquery-ui-sortable' );

			wp_register_script( 'loftocean-widget-lib', $asset_uri . 'scripts/admin/widget-lib.min.js', array(), $asset_version, true );
			wp_enqueue_script( 'loftocean-widgets', $asset_uri . 'scripts/admin/widgets.min.js', $widget_dependency, $asset_version, true );
			wp_localize_script( 'loftocean-widgets', 'loftoceanWidgetJSON', apply_filters( 'loftocean_get_widget_json', array() ) );
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
	// Add action to initialize widgets
	add_action( 'loftocean_load_core_modules', array( 'LoftOcean\Widget\Manager', '_instance' ) );
}
