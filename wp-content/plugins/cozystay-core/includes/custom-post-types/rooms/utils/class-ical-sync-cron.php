<?php
namespace LoftOcean\iCal;

class Sync_Cron {
    /**
    * Default settings
    */
    protected $default_settings = array(
        'enable_auto_sync' => '',
        'auto_sync_interval' => 'loftocean_2hours',
        'auto_sync_interval_time' => '1',
        'auto_sync_interval_apm' => 'am',
        'auto_clear_log_interval' => '7days',
        'auto_clear_old_imported_bookings_interval' => 'never'
    );
    /**
    * Setting date pair
    */
    protected $setting_date_pair = array(
        '1day' => '-1 day',
        '3days' => '-3 days',
        '7days' => '-7 days',
        '14days' => '-14 days',
        '30days' => '-30 days',
        '60days' => '-60 days'
    );
    /**
    * Construction function
    */
    public function __construct() {
        add_filter( 'loftocean_ical_cron_get_default_settings', array( $this, 'get_default_settings' ) );
        add_filter( 'loftocean_ical_cron_get_settings', array( $this, 'get_settings' ) );
        add_filter( 'cron_schedules', array( $this, 'cron_schedules' ), 10, 1 );

        add_action( 'loftocean_ical_cron_update_settings', array( $this, 'update_settings' ), 10, 1 );
        add_action( 'loftocean_ical_cron_auto_sync', array( $this, 'auto_sync' ) );
        add_action( 'loftocean_ical_cron_clear_log', array( $this, 'clear_log' ) );
        add_action( 'loftocean_ical_cron_clear_old_imported_bookings', array( $this, 'clear_old_imported_bookings' ) ); $this->clear_log();

        $this->add_schedule_events();
    }
    /**
    * Get default settings
    */
    public function get_default_settings( $settings ) {
        return $this->default_settings;
    }
    /**
    * Get settings
    */
    public function get_settings( $settings ) {
        $default_settings =  apply_filters( 'loftocean_ical_cron_get_default_settings', array() );
        $settings = get_option( 'loftocean_ical_cron_settings',  $default_settings );
        return \LoftOcean\is_valid_array( $settings ) ? array_merge( $default_settings, $settings ) : $default_settings;
    }
    /**
    * Update settings
    */
    public function update_settings( $data ) {
        $default_settings = apply_filters( 'loftocean_ical_cron_get_default_settings', array() );
        $keys = array_keys( $default_settings );
        $settings = array();
        if ( \LoftOcean\is_valid_array( $data ) ) {
            foreach( $keys as $key ) {
                if ( isset( $data[ $key ] ) ) {
                    $settings[ $key ] = $data[ $key ];
                }
            }
        }
        update_option( 'loftocean_ical_cron_settings', array_merge( $default_settings, $settings ) );

        $cron_hooks = array( 'loftocean_ical_cron_auto_sync', 'loftocean_ical_cron_clear_log', 'loftocean_ical_cron_clear_old_imported_bookings' );
        foreach ( $cron_hooks as $hook ) {
            $this->clear_schedule_hook( $hook );
        }
    }
    /**
    * Add cron schedules
    */
    public function cron_schedules( $schedules ) {
        $prefix = 'loftocean_';
        $schedule_options = array(
            '15mins' => array(
                'display' => esc_html__( 'Every 15 Minutes', 'loftocean' ),
                'interval' => 900
            ),
            '30mins' => array(
                'display' => esc_html__( 'Every 30 Minutes', 'loftocean' ),
                'interval' => 1800
            ),
            '1hour' => array(
                'display' => esc_html__( 'Every Hour', 'loftocean' ),
                'interval' => 3600
            ),
            '2hours' => array(
                'display' => esc_html__( 'Every 2 Hours', 'loftocean' ),
                'interval' => 7200
            ),
            '3hours' => array(
                'display' => esc_html__( 'Every 3 Hours', 'loftocean' ),
                'interval' => 10800
            ),
            '6hours' => array(
                'display' => esc_html__( 'Every 6 Hours', 'loftocean' ),
                'interval' => 21600
            ),
            '12hours' => array(
                'display' => esc_html__( 'Every 12 Hour', 'loftocean' ),
                'interval' => 43200
            ),
            '24hours' => array(
                'display' => esc_html__( 'Every 24 Hours', 'loftocean' ),
                'interval' => 86400
            )
        );
        foreach( $schedule_options as $schedule_key => $schedule ) {
            $schedules[ $prefix . $schedule_key ] = array(
                'interval' => $schedule[ 'interval' ],
                'display' => $schedule[ 'display' ]
            );
        }
        return $schedules;
    }
    /**
    * Add schedule events
    */
    protected function add_schedule_events() {
        $initial_time = strtotime( 'midnight' ) - \LoftOcean\get_local_time_offset();
        $hook = 'loftocean_ical_cron_auto_sync';
        $cron_settings = apply_filters( 'loftocean_ical_cron_get_settings', array() );
        if ( 'on' == $cron_settings[ 'enable_auto_sync' ] ) {
            $timestamp = $initial_time;
            if ( 'loftocean_24hours' == $cron_settings[ 'auto_sync_interval' ] ) {
                $timestamp += ( $cron_settings[ 'auto_sync_interval_time' ] + ( 'am' == $cron_settings[ 'auto_sync_interval_apm' ] ? 0 : 12 ) ) * HOUR_IN_SECONDS;
            }
            $this->add_task( array( 'timestamp' => $timestamp, 'recurrence' => $cron_settings[ 'auto_sync_interval' ], 'hook' => $hook ) );
        } else {
            $this->clear_schedule_hook( $hook );
        }

        $events = array( 'auto_clear_log_interval' => 'loftocean_ical_cron_clear_log', 'auto_clear_old_imported_bookings_interval' => 'loftocean_ical_cron_clear_old_imported_bookings' );
        foreach ( $events as $key => $hook ) {
            ( 'never' == $cron_settings[ $key ] ) ? $this->clear_schedule_hook( $hook ) : $this->add_task( array( 'timestamp' => $initial_time, 'recurrence' => 'loftocean_24hours', 'hook' => $hook ) );
        }
    }
    /**
    * Add task
    */
    protected function add_task( $task ) {
        if ( ! wp_next_scheduled( $task[ 'hook' ] ) ) {
            wp_schedule_event( $task[ 'timestamp' ], $task[ 'recurrence' ], $task[ 'hook' ] );
        }
        return true;
    }
    /**
    * Clear schedule hook
    */
    protected function clear_schedule_hook( $hook ) {
        wp_clear_scheduled_hook( $hook );
    }
    /**
    * Cron function auto sync
    */
    public function auto_sync() {
        $sources = get_option( 'loftocean_room_ical_sync_sources', array() );
        if ( \LoftOcean\is_valid_array( $sources ) ) {
            $log_file_time = strtotime( 'now' );
            foreach( $sources as $roomID => $list ) {
                foreach( $list as $urlBase64 => $source ) {
                    if ( isset( $source[ 'url' ], $source[ 'roomID' ], $source[ 'title' ] ) && ( 'loftocean_room' == get_post_type( $source[ 'roomID' ] ) ) ) {
                        do_action( 'loftocean_ical_sync_import_by_url', $source[ 'url' ], $source[ 'roomID' ], $source[ 'title' ], $log_file_time );
                        $sources[ $roomID ][ $urlBase64 ][ 'lastSyncTime' ] = time();
                    }
                }
            }
            update_option( 'loftocean_room_ical_sync_sources', $sources );
        }
    }
    /**
    * Cron function clear log
    */
    public function clear_log() {
        $cron_settings = apply_filters( 'loftocean_ical_cron_get_settings', array() );
        $logs = apply_filters( 'loftocean_get_log_file_list', array() );
        if ( \LoftOcean\is_valid_array( $logs ) && isset( $this->setting_date_pair[ $cron_settings[ 'auto_clear_log_interval' ] ] ) ) {
            $check_time = strtotime( $this->setting_date_pair[ $cron_settings[ 'auto_clear_log_interval' ] ] );
            $remove_log_files = array();
            foreach ( $logs as $log ) {
                if ( $log[ 'created_time' ] <= $check_time ) {
                    array_push( $remove_log_files, $log[ 'name' ] );
                }
            }
            if ( \LoftOcean\is_valid_array( $remove_log_files ) ) {
                do_action( 'loftocean_remove_log_files', $remove_log_files );
            }
        }
    }
    /**
    * Cron function clear older imported bookings
    */
    public function clear_old_imported_bookings() {
        $cron_settings = apply_filters( 'loftocean_ical_cron_get_settings', array() );
        if ( isset( $this->setting_date_pair[ $cron_settings[ 'auto_clear_old_imported_bookings_interval' ] ] ) ) {
            $check_date = date( 'Y-m-d', strtotime( $this->setting_date_pair[ $cron_settings[ 'auto_clear_old_imported_bookings_interval' ] ] ) );
            $items = apply_filters(
                'loftocean_get_imported_bookings_by_args',
                array(),
                array( array( 'key' => 'checkout', 'value' => $check_date, 'compare' => '<=' ) )
            );
            if ( \LoftOcean\is_valid_array( $items ) ) {
                foreach( $items as $item ) {
                    do_action( 'loftocean_remove_imported_bookings', array( $item ), get_post_meta( $item, 'room_id', true ), get_post_meta( $item, 'source', true ) );
                }
            }
        }
    }
}
new \LoftOcean\iCal\Sync_Cron();
