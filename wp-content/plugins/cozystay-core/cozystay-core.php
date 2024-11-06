<?php
/*
Plugin Name: CozyStay Core
Plugin URI: http://www.loftocean.com/
Description: CozyStay Theme function extension - Post like, post sharing, gallery slider, Instagram feed and more.
Version: 1.5.1
Author: Loft.Ocean
Author URI: http://www.loftocean.com/
Text Domain: loftocean
Domain Path: /languages
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'LOFTOCEAN_FOR_THEME_COZYSTAY' ) ) {
	define( 'LOFTOCEAN_FOR_THEME_COZYSTAY', true );
	function cozystay_core() {
		if ( defined( 'COZYSTAY_THEME_VERSION' ) ) {
			define( 'LOFTOCEAN_THEME_PLUGIN_VERSION_META_NAME', 'cozystay_core_version' );
			define( 'LOFTOCEAN_THEME_VERSION', COZYSTAY_THEME_VERSION );
			define( 'LOFTOCEAN_ASSETS_VERSION', '2024091901' );
			define( 'LOFTOCEAN_THEME_PREFIX', 'cozystay' );
			$plugin_dir = plugin_dir_path( __FILE__ );
			require_once $plugin_dir . 'class-upgrader.php';
			require_once $plugin_dir . 'includes/class-plugin-loader.php';
			LoftOcean\Loader::init();
		}
	}
	add_action( 'after_setup_theme', 'cozystay_core', 1 );
	/**
	* Clear schedule hooks
	*/
	function cozystay_core_deactivation() {
		$hooks = array( 'loftocean_ical_cron_auto_sync', 'loftocean_ical_cron_clear_log', 'loftocean_ical_cron_clear_old_imported_bookings' );
		foreach ( $hooks as $hook ) {
			wp_clear_scheduled_hook( $hook );
		}
		flush_rewrite_rules();
	}
	register_deactivation_hook( __FILE__, 'cozystay_core_deactivation' );
	/**
	* Activate plugin
	*/
	function cozystay_core_activation() {
		flush_rewrite_rules();
	}
	register_activation_hook( __FILE__, 'cozystay_core_activation' );
}
