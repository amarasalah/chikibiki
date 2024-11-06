<?php
namespace LoftOcean\Room;

if ( ! class_exists( '\LoftOcean\Room\iCal_Sync_Settings' ) ) {
    class iCal_Sync_Settings {
        /**
        * String Post type
        */
        protected $post_type = 'loftocean_room';
        /**
        * Ajax update sync source
        */
        protected $update_sync_source = 'loftocean_update_sync_source';
        /**
        * Ajax remove sync source
        */
        protected $remove_sync_source = 'loftocean_remove_sync_source';
        /**
        * Ajax sync
        */
        protected $action_sync = 'loftocean_sync';
        /**
        * Ajax get imported booking detail
        */
        protected $action_get_imported_booking_detail = 'loftocean_get_imported_booking_detail';
        /**
        * Ajax remove bookings without existing source
        */
        protected $action_remove_bookings_without_existing_source = 'loftocean_remove_bookings_without_existing_source';
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
            add_action( 'wp_ajax_' . $this->update_sync_source, array( $this, 'update_sync_source' ) );
            add_action( 'wp_ajax_' . $this->remove_sync_source, array( $this, 'remove_sync_source' ) );
            add_action( 'wp_ajax_' . $this->action_sync, array( $this, 'sync' ) );
            add_action( 'wp_ajax_' . $this->action_get_imported_booking_detail, array( $this, 'get_imported_booking_detail' ) );
            add_action( 'wp_ajax_' . $this->action_remove_bookings_without_existing_source, array( $this, 'remove_bookings_without_existing_source' ) );
        }
        /**
        * Add submenu page
        */
        public function add_admin_menu() {
            $label = esc_html__( 'iCal Sync', 'loftocean' );
            add_submenu_page( 'edit.php?post_type=' . $this->post_type, $label, $label, $this->capability, 'loftocean_room_ical_sync_settings', array( $this, 'room_ical_sync_settings_page' ) );
        }
        /**
        * Ajax action handler function to update sync source
        */
        public function update_sync_source() {
            if ( isset( $_REQUEST, $_REQUEST[ 'data' ], $_REQUEST[ 'data' ][ 'titleBase64' ], $_REQUEST[ 'data' ][ 'urlBase64' ], $_REQUEST[ 'data' ][ 'roomID' ], $_REQUEST[ 'data' ][ 'oldURLBase64' ] )
                && ( ! empty( $_REQUEST[ 'data' ][ 'titleBase64' ] ) ) && ( ! empty( $_REQUEST[ 'data' ][ 'urlBase64' ] ) ) && ( ! empty( $_REQUEST[ 'data' ][ 'roomID' ] ) ) ) {
                $title = sanitize_text_field( wp_unslash( $_REQUEST[ 'data' ][ 'titleBase64' ] ) );
                $url_base64 = sanitize_text_field( wp_unslash( $_REQUEST[ 'data' ][ 'urlBase64' ] ) );
                $roomID = sanitize_text_field( wp_unslash( $_REQUEST[ 'data' ][ 'roomID' ] ) );
                $source_index = 'room-' . $roomID;
                $url = base64_decode( $url_base64 );
                $item = array( 'title' => base64_decode( $title ), 'url' => $url, 'roomID' => $roomID );
                $old_url_base64 = empty( $_REQUEST[ 'data' ][ 'oldURLBase64' ] ) ? '' : sanitize_text_field( wp_unslash( $_REQUEST[ 'data' ][ 'oldURLBase64' ] ) );

                $sources = get_option( 'loftocean_room_ical_sync_sources', array() );
                $sources = \LoftOcean\is_valid_array( $sources ) ? $sources : array();
                if ( isset( $sources[ $source_index ], $sources[ $source_index ][ $url_base64 ] ) ) {
                    $item = array_merge( $sources[ $source_index ][ $url_base64 ], $item );
                }
                if ( ( ! empty( $old_url_base64 ) ) && ( $old_url_base64 != $url_base64 ) && isset( $sources[ $source_index ][ $old_url_base64 ] ) ) {
                    unset( $sources[ $source_index ][ $old_url_base64 ] );
                }
                if ( isset( $sources[ $source_index ] ) ) {
                    $sources[ $source_index ][ $url_base64 ] = $item;
                } else {
                    $sources[ $source_index ] = array( $url_base64 => $item );
                }
                update_option( 'loftocean_room_ical_sync_sources', $sources );
            }
            wp_send_json_success( array( 'status' => 'done', 'message' => esc_html__( 'Updated.', 'loftocean' ) ) );
        }
        /**
        * Ajax action handler function to remove sync source
        */
        public function remove_sync_source() {
            if ( isset( $_REQUEST, $_REQUEST[ 'data' ], $_REQUEST[ 'data' ][ 'urlBase64' ], $_REQUEST[ 'data' ][ 'roomID' ] )
                && ( ! empty( $_REQUEST[ 'data' ][ 'urlBase64' ] ) ) && ( ! empty( $_REQUEST[ 'data' ][ 'roomID' ] ) ) ) {

                $url_base64 = sanitize_text_field( wp_unslash( $_REQUEST[ 'data' ][ 'urlBase64' ] ) );
                $roomID = sanitize_text_field( wp_unslash( $_REQUEST[ 'data' ][ 'roomID' ] ) );
                $source_index = 'room-' . $roomID;
                $remove_data = empty( $_REQUEST[ 'data'][ 'removeData' ] ) || ( 'on' != $_REQUEST[ 'data' ][ 'removeData' ] ) ? false : true;

                $sources = get_option( 'loftocean_room_ical_sync_sources', array() );
                $sources = \LoftOcean\is_valid_array( $sources ) ? $sources : array();
                if ( isset( $sources[ $source_index ][ $url_base64 ] ) ) {
                    unset( $sources[ $source_index ][ $url_base64 ] );
                    if ( $remove_data ) {
                        $this->remove_imported_bookings( $roomID, $url_base64 );
                    }
                }
                $sources = array_filter( $sources );
                update_option( 'loftocean_room_ical_sync_sources', $sources );
            }
            wp_send_json_success( array( 'status' => 'done', 'message' => esc_html__( 'Removed.', 'loftocean' ) ) );
        }
        /**
        * Ajax action handler function sync
        */
        public function sync() {
            if ( isset( $_REQUEST, $_REQUEST[ 'data' ], $_REQUEST[ 'data' ][ 'source' ], $_REQUEST[ 'data' ][ 'roomID' ], $_REQUEST[ 'data' ][ 'time' ] )
                && ( ! empty( $_REQUEST[ 'data' ][ 'source' ] ) ) && ( ! empty( $_REQUEST[ 'data' ][ 'roomID' ] ) ) && ( ! empty( $_REQUEST[ 'data' ][ 'time' ] ) ) ) {

                $source = sanitize_text_field( wp_unslash( $_REQUEST[ 'data' ][ 'source' ] ) );
                $roomID = sanitize_text_field( wp_unslash( $_REQUEST[ 'data' ][ 'roomID' ] ) );
                $log_file_time = sanitize_text_field( wp_unslash( $_REQUEST[ 'data' ][ 'time' ] ) );
                $source_index = 'room-' . $roomID;
                if ( $this->post_type == get_post_type( $roomID ) ) {
                    $sources = get_option( 'loftocean_room_ical_sync_sources', array() );
                    if ( isset( $sources[ $source_index ], $sources[ $source_index ][ $source ] ) ) {
                        do_action( 'loftocean_ical_sync_import_by_url', $sources[ $source_index ][ $source ][ 'url' ], $roomID, $sources[ $source_index ][ $source ][ 'title' ], $log_file_time );
                        $sources[ $source_index ][ $source ][ 'lastSyncTime' ] = time();
                        update_option( 'loftocean_room_ical_sync_sources', $sources );
                    }
                }
            }
            wp_send_json_success( array( 'status' => 'done', 'message' => esc_html__( 'Imported.', 'loftocean' ) ) );
        }
        /**
        * Ajax action handler function get imported booking detail
        */
        public function get_imported_booking_detail() {
            $return_data = array( 'status' => 'done', 'message' => esc_html__( 'Imported.', 'loftocean' ), 'data' => array() );
            if ( isset( $_REQUEST, $_REQUEST[ 'data' ], $_REQUEST[ 'data' ][ 'order_id' ] ) && ( ! empty( $_REQUEST[ 'data' ][ 'order_id' ] ) ) ) {
                $order_id = sanitize_text_field( wp_unslash( $_REQUEST[ 'data' ][ 'order_id' ] ) );
                $return_data[ 'detail' ] = apply_filters( 'loftocean_get_imported_booking_detail', array(), $order_id );
            }
            wp_send_json_success( $return_data );
        }
        /**
        * Ajax action handler function remove imported bookings without exsiting source
        */
        public function remove_bookings_without_existing_source() {
            $option = get_option( 'loftocean_room_ical_sync_sources', array() );
            $remove_all = true;
            $items = array();

            if ( \LoftOcean\is_valid_array( $option ) ) {
                $sources = array();
                foreach( $option as $list ) {
                    foreach ( $list as $source => $item ) {
                        if ( ! empty( $item[ 'roomID' ] ) ) {
                            if ( isset( $sources[ $source ] ) ) {
                                array_push( $sources[ $source ], $item[ 'roomID' ] );
                            } else {
                                $sources[ $source ] = array( $item[ 'roomID' ] );
                            }
                        }
                    }
                }
                if ( \LoftOcean\is_valid_array( $sources ) ) {
                    $remove_all = false;
                    $items = apply_filters(
                        'loftocean_get_imported_bookings_by_args',
                        array(),
                        array( array( 'key' => 'source', 'value' => array_keys( $sources ), 'compare' => 'NOT IN' ) )
                    );
                    foreach( $sources as $source => $ids ) {
                        $roomIDs = apply_filters(
                            'loftocean_get_imported_bookings_by_args',
                            array(),
                            array(
                                array( 'key' => 'room_id', 'value' => $ids, 'compare' => 'NOT IN' ),
                                array( 'key' => 'source', 'value' => $source, 'compare' => '=' )
                            )
                        );
                        if ( \LoftOcean\is_valid_array( $roomIDs ) ) {
                            $items = array_merge( $items, $roomIDs );
                        }
                    }
                    $items = array_unique( $items );
                }
            }
            if ( $remove_all ) {
                $items = apply_filters( 'loftocean_get_all_imported_bookings', array() );
            }

            if ( \LoftOcean\is_valid_array( $items ) ) {
                foreach( $items as $item ) {
                    do_action( 'loftocean_remove_imported_bookings', array( $item ), get_post_meta( $item, 'room_id', true ), get_post_meta( $item, 'source', true ) );
                }
            }
            wp_send_json_success( array( 'status' => 'done', 'message' => esc_html__( 'Removed.', 'loftocean' ) ) );
        }
        /*
        * Room general setting page
        */
        public function room_ical_sync_settings_page() {
            $this->save_settings();
            $this->enqueue_assets();
            require_once LOFTOCEAN_DIR . 'includes/custom-post-types/rooms/view/settings/page-ical-sync-settings.php';
        }
        /**
        * Save settings
        */
        protected function save_settings() {
            if ( current_user_can( $this->capability ) && isset( $_REQUEST[ 'loftocean_ical_sync_settings_nonce' ] ) && wp_verify_nonce( $_REQUEST[ 'loftocean_ical_sync_settings_nonce' ], 'loftocean_ical_sync_nonce' ) ) {
                do_action( 'loftocean_ical_cron_update_settings', $_REQUEST );
                wp_safe_redirect( admin_url( 'edit.php?' . $_SERVER[ 'QUERY_STRING' ] . '&active_tab=settings&settings_updated=1' ) );
                exit();
            }
        }
        /**
        * Enqueue assets
        */
        public function enqueue_assets() {
            do_action( 'loftocean_load_admin_css' );
            wp_enqueue_script( 'moment', LOFTOCEAN_ASSETS_URI . 'libs/daterangepicker/moment.min.js', array(), '2.18.1', true );
    		wp_enqueue_script( 'loftocean-base64', LOFTOCEAN_ASSETS_URI . 'libs/base64/base64.min.js', array(), LOFTOCEAN_ASSETS_VERSION, true );
            wp_enqueue_script( 'loftocean-admin-ical-sync-settings', LOFTOCEAN_URI . 'assets/scripts/admin/room-ical-sync.min.js', array( 'jquery', 'wp-api-request', 'wp-util', 'loftocean-base64', 'moment' ), LOFTOCEAN_ASSETS_VERSION, true );
            wp_localize_script( 'loftocean-admin-ical-sync-settings', 'loftoceanRoomiCalSyncSettings', array(
                'url' => admin_url( 'admin-ajax.php' ),
                'actionUpdateSyncSource' => $this->update_sync_source,
                'actionRemoveSyncSource' => $this->remove_sync_source,
                'actionSync' => $this->action_sync,
                'actionGetImportedBookingDetail' => $this->action_get_imported_booking_detail,
                'actionRemoveBookingsWithoutExistingSource' => $this->action_remove_bookings_without_existing_source,
                'currentTab' => isset( $_GET[ 'active_tab' ] ) ? $_GET[ 'active_tab' ] : '',
                'feedBaseURL' => get_home_url() . '/?feed=loftocean.ics&room_id=',
                'syncSources' => $this->get_total_sources(),
                'i18nText' => array(
					'syncFailed' => esc_html__( 'Failed. Try again later', 'loftocean' ),
                    'lastSyncTimePassedPrefix' => esc_html__( 'Last synced', 'loftocean' ),
                    'lastSyncDatePrefix' => esc_html__( 'Last synced on', 'loftocean' )
                )
            ) );
        }
        /**
        * Remove imported bookings
        */
        protected function remove_imported_bookings( $roomID, $source ) {
            $bookings = apply_filters(
                'loftocean_get_imported_bookings_by_args',
                array(),
                array(
                    'relation' => 'AND',
                    array( 'key' => 'room_id', 'value' => $roomID ),
                    array( 'key' => 'source', 'value' => $source )
                )
            );
            do_action( 'loftocean_remove_imported_bookings', $bookings, $roomID, $source );
        }
        /**
        * Get calendar source list
        */
        protected function get_total_sources() {
            $sources = get_option( 'loftocean_room_ical_sync_sources', array() );
            if ( \LoftOcean\is_valid_array( $sources ) ) {
                $uuid_calendar_sources = array();
                foreach( $sources as $roomID => $list ) {
                    $rid = str_replace( 'room-', '', $roomID );
                    if ( $this->post_type == get_post_type( $rid ) ) {
                        $uuid = \LoftOcean\Room\Relationship_Tools::get_room_relationship( $rid );
                        if ( ! isset( $uuid_calendar_sources[ $uuid ] ) ) {
                            $uuid_calendar_sources[ $uuid ] = array();
                        }
                        foreach( $list as $url_base64 => $item ) {
                            $uuid_calendar_sources[ $uuid ][ $url_base64 ] = $item;
                        }
                    }
                }
                return $uuid_calendar_sources;
            }
            return array();
        }
    }
    new iCal_Sync_Settings();
}
