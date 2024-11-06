<?php
namespace LoftOcean\Room;

if ( ! class_exists( '\LoftOcean\Room\General_Settings' ) ) {
    class General_Settings {
        /**
        * String Post type
        */
        protected $post_type = 'loftocean_room';
        /**
        * Ajax reset facility action
        */
        protected $reset_facility_action = 'loftocean_reset_room_facilities';
        /**
        * Ajax sync order data action
        */
        protected $sync_order_data_action = 'loftocean_sync_room_data';
        /*
        * capability
        */
        protected $capability = 'manage_options';
        /**
        * Construct function
        */
        public function __construct() {
            $this->capability = \LoftOcean\get_room_section_capabilities();

            add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
            // add_action( 'wp_ajax_nopriv_' . $this->reset_facility_action, array( $this, 'reset_room_facilities' ) );
            add_action( 'wp_ajax_' . $this->reset_facility_action, array( $this, 'reset_room_facilities' ) );
            // add_action( 'wp_ajax_nopriv_' . $this->sync_order_data_action, array( $this, 'sync_order_data' ) );
            add_action( 'wp_ajax_' . $this->sync_order_data_action, array( $this, 'sync_order_data' ) );
            add_action( 'loftocean_sync_room_data', array( $this, 'sync_order_data' ) );

            $guest_use_plural_label_when_zero = get_option( 'loftocean_guest_use_plural_label_when_zero', 'on' );
            if ( 'on' == $guest_use_plural_label_when_zero ) {
                add_filter( 'loftocean_room_use_plural_if_children_number_is_zero', '__return_true', 99999 );
                add_filter( 'loftocean_room_use_plural_if_adults_number_is_zero', '__return_true', 99999 );
            }
        }
        /**
        * Add submenu page
        */
        public function add_admin_menu() {
            $label = esc_html__( 'Settings', 'loftocean' );
            add_submenu_page( 'edit.php?post_type=' . $this->post_type, $label, $label, $this->capability, 'loftocean_room_general_settings', array( $this, 'room_general_settings_page' ) );
        }
        /**
        * Ajax action handler function to reset room facilities
        */
        public function reset_room_facilities() {
            global $sitepress;
            $taxonomy = 'lo_room_facilities';
            if ( $sitepress ) {
                remove_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter' ) );
                remove_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ) );
                remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
                $facilities = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
            } else {
                $facilities = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false, 'lang' => '' ) );
            }
            if ( ( ! is_wp_error( $facilities ) ) && \LoftOcean\is_valid_array( $facilities ) ) {
                foreach( $facilities as $facility ) {
                    wp_delete_term( $facility->term_id, $taxonomy );
                }
            }
            wp_send_json_success( array( 'status' => 'done', 'message' => esc_html__( 'Updated.', 'loftocean' ) ) );
        }
        /**
        * Ajax action handler function to sync order data
        */
        public function sync_order_data() {
            \LoftOcean\Room\Relationship_Tools::reset_relationship_table();
            do_action( 'loftocean_room_regenerate_relationship_data' );
            do_action( 'loftocean_room_regenerate_order_data' );
            do_action( 'loftocean_regenerate_imported_booking_data' );
            wp_send_json_success( array( 'status' => 'done', 'message' => esc_html__( 'Updated.', 'loftocean' ) ) );
        }
        /*
        * Room general setting page
        */
        public function room_general_settings_page() {
            $this->save_settings();
            $this->enqueue_assets();

            require_once LOFTOCEAN_DIR . 'includes/custom-post-types/rooms/view/settings/page-general-settings.php';
        }
        /**
        * Save settings
        */
        protected function save_settings() {
            if ( current_user_can( $this->capability ) && isset( $_REQUEST[ 'loftocean_room_advanced_setting_nonce' ] ) && wp_verify_nonce( $_REQUEST[ 'loftocean_room_advanced_setting_nonce' ], 'loftocean_room_advanced_settings' ) ) {
                $options = array();
                if ( isset( $_REQUEST[ 'loftocean_weekend_days_setting' ] ) ) {
                    $options = array_map( 'sanitize_text_field', wp_unslash( $_REQUEST[ 'loftocean_weekend_days_setting' ] ) );
                    $options = array_filter( (array)$options, function( $item ) {
                        return in_array( $item, array( 'day1', 'day2', 'day3', 'day4', 'day5', 'day6', 'day7' ) );
                    } );
                }
                update_option( 'loftocean_room_weekend_days_setting', $options );

                $adult_age_description = isset( $_REQUEST[ 'loftocean_adult_age_description' ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ 'loftocean_adult_age_description' ] ) ) : '';
                $child_age_description = isset( $_REQUEST[ 'loftocean_child_age_description' ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ 'loftocean_child_age_description' ] ) ) : '';
                $guest_use_plural_label_when_zero = isset( $_REQUEST[ 'loftocean_guest_use_plural_label_when_zero' ] ) ? 'on' : ''; 
                update_option( 'loftocean_adult_age_description', $adult_age_description );
                update_option( 'loftocean_child_age_description', $child_age_description ); 
                update_option( 'loftocean_guest_use_plural_label_when_zero', $guest_use_plural_label_when_zero );
             }
        }
        /**
        * Enqueue assets
        */
        public function enqueue_assets() {
            do_action( 'loftocean_load_admin_css' );
            wp_enqueue_script( 'loftocean-admin-room-settings', LOFTOCEAN_URI . 'assets/scripts/admin/room-settings.min.js', array( 'jquery' ), LOFTOCEAN_ASSETS_VERSION, true );
            wp_localize_script( 'loftocean-admin-room-settings', 'loftoceanRoomSettings', array(
                'url' => admin_url( 'admin-ajax.php' ),
                'actionResetFacilities' => $this->reset_facility_action,
                'actionSyncOrderData' => $this->sync_order_data_action
            ) );
        }
    }
    new General_Settings();
}
