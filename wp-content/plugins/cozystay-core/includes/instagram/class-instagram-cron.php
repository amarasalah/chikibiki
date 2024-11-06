<?php
namespace LoftOcean\Instagram;

if ( ! class_exists( '\LoftOcean\Instagram\Auto_Download_Cron' ) ) {
	class Auto_Download_Cron {
		/**
		* String cron callback hook
		*/
		protected $cron_hook = 'loftocean_auto_download_instagram_images';
		/**
		* Contruct function to initialize the cron
		*/
		public function __construct() {
			add_action( 'loftocean_clear_schedule_hook', array( $this, 'clear_instagram_schedule_hook' ) );
			add_action( $this->cron_hook, array( $this, 'run_auto_download' ) );
			add_action( 'update_option_loftocean_auto_download_instagram_images_schedule', array( $this, 'update_auto_download_schedule' ), 10, 3 );
			add_filter( 'cron_schedules', array( $this, 'add_wp_cron_schedules' ) );
			if ( 'on' == get_option( 'loftocean_enable_auto_download_instagram_images', '' ) ) {
				if ( ! wp_next_scheduled ( $this->cron_hook ) ) {
					$schedule = get_option( 'loftocean_auto_download_instagram_images_schedule', 'weekly' );
					wp_schedule_event( time() + 2 , $schedule, $this->cron_hook );
				}
			} else {
				do_action( 'loftocean_clear_schedule_hook' );
			}
		}
		/**
		* Schedule hook callback to run the Instagram images auto download
		*/
		public function run_auto_download() {
			wp_remote_request( get_rest_url( null, 'loftocean/v1/auto-download-instagram-feeds' ), array( 'blocking' => false, 'timeout' => 0 ) );
		}
		/**
		 * Add more cron schedules.
		 * @param array $schedules List of WP scheduled cron jobs.
		 * @return array
		 */
		public function add_wp_cron_schedules( $schedules ) {
			if ( ! isset( $schedules['monthly'] ) ) {
				$schedules['monthly'] = array(
					'interval' => 2635200,
					'display' => __( 'Monthly', 'loftocean' ),
				);
			}
			if ( ! isset( $schedules['fifteendays'] ) ) {
				$schedules['fifteendays'] = array(
					'interval' => 1296000,
					'display'  => __( 'Twice Monthly', 'loftocean' ),
				);
			}
			$schedules['minute'] = array(
				'interval' => 60,
				'display'  => __( 'Every minute', 'loftocean' ),
			);
			$schedules['fminute'] = array(
				'interval' => 300,
				'display'  => __( 'Every 5 minutes', 'loftocean' ),
			);
			$schedules['tminute'] = array(
				'interval' => 120,
				'display'  => __( 'Every 2 minutes', 'loftocean' ),
			);
			$schedules['tenminute'] = array(
				'interval' => 600,
				'display'  => __( 'Every 10 minutes', 'loftocean' ),
			);
			return $schedules;
		}
		/**
		* Update option loftocean_auto_download_instagram_images_schedule
		*/
		public function update_auto_download_schedule( $old_value = '', $new_value = '', $name = '' ) {
			do_action( 'loftocean_clear_schedule_hook' );
		}
		/**
		* Clear Instagram images auto download schedule hook
		*/
		public function clear_instagram_schedule_hook() {
			wp_clear_scheduled_hook( $this->cron_hook );
		}
	}
	new Auto_Download_Cron();
}
