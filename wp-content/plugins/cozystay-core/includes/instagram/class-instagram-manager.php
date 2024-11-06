<?php
namespace LoftOcean\Instagram;
/**
* Instagram manager class
*/

if ( ! class_exists( '\LoftOcean\Instagram\Manager' ) ) {
	class Manager {
		/**
		* Object current class instance
		*/
		public static $_instance = false;
		/**
		* String current path
		*/
		private $root_dir = '';
		/**
		* Construct function
		*/
		public function __construct() {
			$this->setup_env();
			$this->load_customize();

			add_action( 'widgets_init', array( $this, 'register_widget' ) );
			add_filter( 'loftocean_instagram_render_method', array( $this, 'get_render_method' ) );
			add_filter( 'loftocean_instagram_has_token', array( $this, 'has_token' ) );
		}
		/**
		* Setup environment
		*/
		private function setup_env() {
			$this->root_dir = LOFTOCEAN_DIR . 'includes/instagram/';
			require_once $this->root_dir . 'class-instagram-tool.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			// require_once $this->root_dir . 'class-instagram-cron.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		}
		/**
		* Load customize settings if needed
		*/
		private function load_customize() {
			// Only load the customize settings needed
			if ( \LoftOcean\is_customize() ) {
				add_action( 'customize_register', array( $this, 'register_customize_settings' ) );
				add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_customize_scripts' ) );
			}
		}
		/**
		* Register Instagram widget
		*/
		public function register_widget() {
			require_once $this->root_dir . 'class-widget-instagram.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			register_widget( 'LoftOcean\Instagram\Widget' );
		}
		/**
		* Get the way how instagram feed rendered in frontend
		* @param string
		* @return string
		*/
		public function get_render_method( $type ) {
			$enabled = get_option( 'loftocean_instagram_enable_ajax', '' );
			return ( 'on' == $enabled ) ? 'ajax' : '';
		}
		/**
		* Enqueue homepage widgets script for theme customize
		*/
		public function enqueue_customize_scripts() {
			wp_enqueue_script(
				'loftocean-customize-instagram',
				LOFTOCEAN_URI . 'assets/scripts/admin/customize/customize-instagram.min.js',
				array( 'jquery', 'customize-controls' ),
				LOFTOCEAN_ASSETS_VERSION,
				true
			);
			wp_localize_script( 'loftocean-customize-instagram', 'loftoceanInstagram', array(
				'i18nMessage' => array(
					'clear' => array(
						'process' => esc_attr__( 'Request Sending ...', 'loftocean' ),
						'done' => esc_attr__( 'Clear Cache', 'loftocean' ),
						'fail' => esc_attr__( 'Failed, Please try again late.', 'loftocean' )
					),
					'download' => array(
						'process' => esc_attr__( 'Downloading Images ...', 'loftocean' ),
						'done' => esc_attr__( 'Manually Download Images', 'loftocean' ),
						'fail' => esc_attr__( 'Failed, Please try again late.', 'loftocean' ),
						'noFeedFound' => esc_attr__( 'Seems no image found, Please check your Instagram.', 'loftocean' )
					)
				)
			) );
		}
		/**
		* Register Instagram related customize settings
		* @param object wp_customize_manager
		*/
		public function register_customize_settings( $wp_customize ) {
			require_once $this->root_dir . 'class-customize-control.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			$wp_customize->add_section( 'loftocean_section_instagram', array(
				'title'		=> esc_html__( 'Instagram', 'loftocean' ),
				'priority' 	=> 30
			) );

			$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, 'loftocean_instagram_clear_cache', array(
				'default'			=> esc_html__( 'Clear Cache', 'loftocean' ),
				'transport'			=> 'postMessage',
				'type' 				=> 'option',
				'sanitize_callback' => '\LoftOcean\sanitize_empty'
			) ) );
			// $wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, 'loftocean_instagram_download_images', array(
			// 	'default'			=> esc_html__( 'Manually Download Images', 'loftocean' ),
			// 	'transport'			=> 'postMessage',
			// 	'type' 				=> 'option',
			// 	'sanitize_callback' => '\LoftOcean\sanitize_empty'
			// ) ) );
			// $wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, 'loftocean_enable_auto_download_instagram_images', array(
			// 	'default'			=> '',
			// 	'transport'			=> 'postMessage',
			// 	'type' 				=> 'option',
			// 	'sanitize_callback' => '\LoftOcean\sanitize_checkbox'
			// ) ) );
			// $wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, 'loftocean_auto_download_instagram_images_schedule', array(
			// 	'default'			=> 'weekly',
			// 	'transport'			=> 'postMessage',
			// 	'type' 				=> 'option',
			// 	'sanitize_callback' => '\LoftOcean\sanitize_choice'
			// ) ) );
			$wp_customize->add_setting( new \WP_Customize_Setting( $wp_customize, 'loftocean_instagram_enable_ajax', array(
				'default'			=> '',
				'transport'			=> 'postMessage',
				'type' 				=> 'option',
				'sanitize_callback' => '\LoftOcean\sanitize_checkbox'
			) ) );

			$wp_customize->add_control( new Customize_Control( $wp_customize, 'loftocean_instagram_clear_cache', array(
				'type' 			=> 'button',
				'label'			=> esc_html__( 'Clear Instagram Cache', 'loftocean' ),
				'description'	=> esc_html__( 'By default, the Instagram cache will exist for up to 2 hours. To manually clear the cache, please click the button below.', 'loftocean' ),
				'section'		=> 'loftocean_section_instagram',
				'settings' 		=> 'loftocean_instagram_clear_cache'
			) ) );
			// $wp_customize->add_control( new Customize_Control( $wp_customize, 'loftocean_instagram_download_images', array(
			// 	'type' 			=> 'button',
			// 	'label'			=> esc_html__( 'Download images from Instagram to my website server.', 'loftocean' ),
			// 	'section'		=> 'loftocean_section_instagram',
			// 	'settings' 		=> 'loftocean_instagram_download_images'
			// ) ) );
			// $wp_customize->add_control( new Customize_Control( $wp_customize, 'loftocean_enable_auto_download_instagram_images', array(
			// 	'type' 			=> 'checkbox',
			// 	'label_first'	=> true,
			// 	'label'			=> esc_html__( 'Enable automatic download of Instagram images', 'loftocean' ),
			// 	'section'		=> 'loftocean_section_instagram',
			// 	'settings' 		=> 'loftocean_enable_auto_download_instagram_images'
			// ) ) );
			// $wp_customize->add_control( new Customize_Control( $wp_customize, 'loftocean_auto_download_instagram_images_schedule', array(
			// 	'type' 				=> 'select',
			// 	'label'				=> esc_html__( 'Automatic download interval', 'loftocean' ),
			// 	'section'			=> 'loftocean_section_instagram',
			// 	'settings' 			=> 'loftocean_auto_download_instagram_images_schedule',
			// 	'active_callback' 	=> array( $this, 'auto_feed_download_active_callback' ),
			// 	'choices'			=> array(
			// 		'daily' => esc_html( 'Once a day', 'loftocean' ),
			// 		'weekly' => esc_html( 'Once a week', 'loftocean' ),
			// 		'fifteendays' => esc_html( 'Every 15 days', 'loftocean' ),
			// 		'monthly' => esc_html__( 'Once a month', 'loftocean' )
			// 	)
			// ) ) );
			$wp_customize->add_control( new Customize_Control( $wp_customize, 'loftocean_instagram_enable_ajax', array(
				'type' 			=> 'checkbox',
				'label_first'	=> true,
				'label'			=> esc_html__( 'Load Instagram pictures dynamically with AJAX', 'loftocean' ),
				'description' 	=> esc_html__( 'Recommend enabling this option if any caching plugins are used on your site.', 'loftocean' ),
				'section'		=> 'loftocean_section_instagram',
				'settings' 		=> 'loftocean_instagram_enable_ajax',
			) ) );
		}
		/**
		* Detect if token set
		*/
		public function has_token( $has ) {
			return function_exists( 'sbi_get_database_settings' );
		}
		/**
		* Active callback for auto download instagram feed schedule
		*/
		public function auto_feed_download_active_callback() {
			return ( 'on' == get_option( 'loftocean_enable_auto_download_instagram_images', '' ) );
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
	add_action( 'loftocean_load_core_modules', array( '\LoftOcean\Instagram\Manager', '_instance' ) );
}
