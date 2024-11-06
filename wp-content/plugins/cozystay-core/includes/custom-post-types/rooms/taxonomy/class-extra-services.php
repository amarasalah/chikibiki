<?php
namespace LoftOcean\Room;

if ( ! class_exists( '\LoftOcean\Room\Extra_Services' ) ) {
    class Extra_Services {
        /**
        * String Post type
        */
        protected $post_type = 'loftocean_room';
        /**
        * Taxomony type
        */
        public $taxonomy = 'lo_room_extra_services';
        /*
        * capability
        */
        protected $capability = 'manage_options';
        /**
        * Construct function
        */
        public function __construct() {
            $this->capability = \LoftOcean\get_room_section_capabilities();

            add_action( 'init', array( $this, 'register_room_taxonomy' ) );
            add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

            add_filter( 'loftocean_get_room_extra_services', array( $this, 'get_extra_services' ) );
            add_filter( 'loftocean_get_room_extra_services_enabled', array( $this, 'get_room_enabled_extra_services' ), 10, 2 );
            add_filter( 'loftocean_get_room_detailed_extra_services', array( $this, 'get_room_enabled_detailed_services' ), 10, 2 );
        }

        /**
        * Create room custom taxonomy
        */
        public function register_room_taxonomy() {
            register_taxonomy( $this->taxonomy ,array( $this->post_type ), array(
                    'hierarchical' => false,
                    'labels' => array(
                    'name' => esc_html__( 'Extra Services', 'loftocean' ),
                    'singular_name' => esc_html__( 'Extra Service', 'loftocean' )
                ),
                'show_ui' => false,
                'public' => false,
                'show_in_rest' => false,
                'show_admin_column' => false,
                'query_var' => true,
                'rewrite' => array( 'slug' => 'room-extra-service' ),
                'meta_box_cb' => false
            ) );
        }

        /**
        * Add submenu page
        */
        public function add_admin_menu() {
            $label = esc_html__( 'Extra Services', 'loftocean' );
            add_submenu_page( 'edit.php?post_type=' . $this->post_type, $label, $label, $this->capability, 'loftocean_room_extra_services', array( $this, 'room_extra_services_settings_page' ) );
        }
        /*
        * Room extra services setting page
        */
        public function room_extra_services_settings_page() {
            $this->save_room_extra_services();

            wp_enqueue_script( 'admin-room-extra-service', LOFTOCEAN_URI . 'assets/scripts/admin/room-extra-service.min.js', array( 'jquery', 'wp-util', 'jquery-ui-sortable', 'jquery-ui-datepicker' ), LOFTOCEAN_ASSETS_VERSION, true );
            wp_localize_script( 'admin-room-extra-service', 'loftoceanRoomExtraServices', $this->get_extra_services() );
            wp_enqueue_style( 'admin-room-extra-service', LOFTOCEAN_URI . 'assets/styles/room-extra-service.min.css', array(), LOFTOCEAN_ASSETS_VERSION );
            wp_enqueue_style( 'jquery-ui', '//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css', array(), '1.13.1' );

            require_once LOFTOCEAN_DIR . 'includes/custom-post-types/rooms/view/taxonomy/page-extra-service.php';
        }
        /**
        * Save room facilitie settings
        */
        protected function save_room_extra_services() {
            if ( current_user_can( $this->capability ) && isset( $_REQUEST['loftocean_room_extra_services_settings_nonce'] )
                && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['loftocean_room_extra_services_settings_nonce'] ) ), 'loftocean_room_extra_services_settings_nonce' ) ) {
                $extra_services = isset( $_REQUEST[ 'loftocean_room_extra_service' ] ) ? wp_unslash( $_REQUEST[ 'loftocean_room_extra_service' ] ) : false;
                if ( \LoftOcean\is_valid_array( $extra_services ) ) {
                    $extra_services = array_filter( $extra_services, function( $item ) {
                        return ! empty( $item[ 'title' ] );
                    } );
                    if ( \LoftOcean\is_valid_array( $extra_services ) ) {
                        $priority = 0;
                        foreach ( $extra_services as $es ) {
                            $term_id = $es[ 'id' ];
                            $custom_effective_time_slots = array();
                            if ( isset( $es[ 'custom_effective_time_slot' ] ) && \LoftOcean\is_valid_array( $es[ 'custom_effective_time_slot' ] ) ) {
                                foreach ( $es[ 'custom_effective_time_slot' ] as $slot ) {
                                    $has_start = ! empty( $slot[ 'start' ] );
                                    $has_end = ! empty( $slot[ 'end' ] );
                                    if ( $has_start || $has_end ) {
                                        array_push( $custom_effective_time_slots, array(
                                            'start' => $slot[ 'start' ],
                                            'end' => $slot[ 'end' ],
                                            'start_timestamp' => $has_start ? strtotime( $slot[ 'start' ] ) : '',
                                            'end_timstamp' => $has_end ? strtotime( $slot[ 'end' ] ) + LOFTICEAN_SECONDS_IN_DAY : ''
                                        ) );
                                    }
                                }
                            }
                            if ( empty( $term_id ) ) {
                                $new_term = wp_insert_term( $es[ 'title' ], $this->taxonomy );
                                if ( ! is_wp_error( $new_term ) && \LoftOcean\is_valid_array( $new_term ) ) {
                                    $new_term_id = $new_term[ 'term_id' ];
                                    update_term_meta( $new_term_id, 'priority', ( $priority * 10 ) );
									update_term_meta( $new_term_id, 'price', sanitize_text_field( $es[ 'price' ] ) );
                        			update_term_meta( $new_term_id, 'method', sanitize_text_field( $es[ 'method' ] ) );
                        			update_term_meta( $new_term_id, 'auto_method', sanitize_text_field( $es[ 'auto_method' ] ) );
                       				update_term_meta( $new_term_id, 'custom_price_appendix_text', sanitize_text_field( $es[ 'custom_price_appendix_text' ] ) );
                       				update_term_meta( $new_term_id, 'custom_minimum_quantity', sanitize_text_field( $es[ 'custom_minimum_quantity' ] ) );
                       				update_term_meta( $new_term_id, 'custom_maximum_quantity', sanitize_text_field( $es[ 'custom_maximum_quantity' ] ) );
                                    update_term_meta( $new_term_id, 'custom_adult_price', sanitize_text_field( $es[ 'custom_adult_price' ] ) );
                                    update_term_meta( $new_term_id, 'custom_child_price', sanitize_text_field( $es[ 'custom_child_price' ] ) );
                                    update_term_meta( $new_term_id, 'effective_time', sanitize_text_field( $es[ 'effective_time' ] ) );
                                    update_term_meta( $new_term_id, 'custom_effective_time_slots', $custom_effective_time_slots );
                                    update_term_meta( $new_term_id, 'obligatory', sanitize_text_field( $es[ 'obligatory' ] ) );
                                }
                            } else {
                                wp_update_term( $term_id, $this->taxonomy, array( 'name' => $es[ 'title' ] ) );
                                update_term_meta( $term_id, 'priority', ( $priority * 10 ) );
                                update_term_meta( $term_id, 'price', sanitize_text_field( $es[ 'price' ] ) );
                                update_term_meta( $term_id, 'method', sanitize_text_field( $es[ 'method' ] ) );
                                update_term_meta( $term_id, 'auto_method', sanitize_text_field( $es[ 'auto_method' ] ) );
                                update_term_meta( $term_id, 'custom_price_appendix_text', sanitize_text_field( $es[ 'custom_price_appendix_text' ] ) );
                                update_term_meta( $term_id, 'custom_minimum_quantity', sanitize_text_field( $es[ 'custom_minimum_quantity' ] ) );
                                update_term_meta( $term_id, 'custom_maximum_quantity', sanitize_text_field( $es[ 'custom_maximum_quantity' ] ) );
                                update_term_meta( $term_id, 'custom_adult_price', sanitize_text_field( $es[ 'custom_adult_price' ] ) );
                                update_term_meta( $term_id, 'custom_child_price', sanitize_text_field( $es[ 'custom_child_price' ] ) );
                                update_term_meta( $term_id, 'effective_time', sanitize_text_field( $es[ 'effective_time' ] ) );
                                update_term_meta( $term_id, 'custom_effective_time_slots', $custom_effective_time_slots );
                                update_term_meta( $term_id, 'obligatory', sanitize_text_field( $es[ 'obligatory' ] ) );
                            }
                            $priority ++;
                        }
                    }
                }

                $removed_terms = empty( $_REQUEST[ 'loftocean_room_extra_service_removed' ] ) ? false : sanitize_text_field( wp_unslash( $_REQUEST[ 'loftocean_room_extra_service_removed' ] ) );
                if ( $removed_terms ) {
                    $removed_terms = explode( ',', $removed_terms );
                    foreach( $removed_terms as $term_id ) {
                        $term = get_term( $term_id, $this->taxonomy, ARRAY_A );
                        if ( ( ! is_wp_error( $term ) ) &&  \LoftOcean\is_valid_array( $term ) ) {
                            wp_delete_term( $term_id, $this->taxonomy );
                        }
                    }
                }
            }
        }
        /*
        * Get current facilities
        */
        public function get_extra_services( $extra = array() ) {
            $terms = get_terms( array(
                'taxonomy' => $this->taxonomy,
                'hide_empty' => false,
                'orderby' => 'meta_value_num',
                'order' => 'ASC',
                'meta_key' => 'priority',
                'fields' => 'ids'
            ) );
            if ( ( ! is_wp_error( $terms ) ) && \LoftOcean\is_valid_array( $terms ) ) {
                return array_map( function( $item_id ) {
                    $term = get_term( $item_id, $this->taxonomy, ARRAY_A );
                    $custom_time_slots = get_term_meta( $item_id, 'custom_effective_time_slots', true );
                    return array_merge( $term, array(
                        'price' => get_term_meta( $item_id, 'price', true ),
                        'method' => get_term_meta( $item_id, 'method', true ),
                        'auto_method' => get_term_meta( $item_id, 'auto_method', true ),
                        'custom_adult_price' => get_term_meta( $item_id, 'custom_adult_price', true ),
                        'custom_child_price' => get_term_meta( $item_id, 'custom_child_price', true ),
                        'custom_price_appendix_text' => get_term_meta( $item_id, 'custom_price_appendix_text', true ),
                        'custom_minimum_quantity' => get_term_meta( $item_id, 'custom_minimum_quantity', true ),
                        'custom_maximum_quantity' => get_term_meta( $item_id, 'custom_maximum_quantity', true ),
                        'effective_time' => get_term_meta( $item_id, 'effective_time', true ),
                        'custom_effective_time_slots' => \LoftOcean\is_valid_array( $custom_time_slots ) ? $custom_time_slots : array(),
                        'obligatory' => get_term_meta( $item_id, 'obligatory', true )
                    ) );
                }, $terms );
            }
            return array();
        }
        /**
        * Get enabled extra services of a given room ID
        */
        public function get_room_enabled_extra_services( $services, $room_id ) {
            $services = wp_get_post_terms( $room_id, $this->taxonomy, array( 'fields' => 'ids' ) );
            if ( \LoftOcean\is_valid_array( $services ) ) {
                return get_terms( array(
                    'taxonomy' => $this->taxonomy,
                    'include' => $services,
                    'hide_empty' => false,
                    'orderby' => 'meta_value_num',
                    'order' => 'ASC',
                    'meta_key' => 'priority',
                    'fields' => 'ids'
                ) );
            }
            return array();
        }
        /**
        * Get detailed enabled extra service for given room
        */
        public function get_room_enabled_detailed_services( $services, $room_id ) {
            $services = wp_get_post_terms( $room_id, $this->taxonomy, array( 'fields' => 'ids' ) );
            if ( \LoftOcean\is_valid_array( $services ) ) {
                $terms = get_terms( array(
                    'taxonomy' => $this->taxonomy,
                    'include' => $services,
                    'hide_empty' => false,
                    'orderby' => 'meta_value_num',
                    'order' => 'ASC',
                    'meta_key' => 'priority',
                    'fields' => 'ids'
                ) );
                if ( ( ! is_wp_error( $terms ) ) && \LoftOcean\is_valid_array( $terms ) ) {
                    return array_map( function( $item_id ) {
                        $term = get_term( $item_id, $this->taxonomy, ARRAY_A );
                        $custom_time_slots = get_term_meta( $item_id, 'custom_effective_time_slots', true );
                        $term = array_merge( $term, array(
                            'price' => get_term_meta( $item_id, 'price', true ),
                            'method' => get_term_meta( $item_id, 'method', true ),
                            'auto_method' => get_term_meta( $item_id, 'auto_method', true ),
                            'custom_adult_price' => get_term_meta( $item_id, 'custom_adult_price', true ),
                            'custom_child_price' => get_term_meta( $item_id, 'custom_child_price', true ),
                            'custom_price_appendix_text' => get_term_meta( $item_id, 'custom_price_appendix_text', true ),
                            'custom_minimum_quantity' => get_term_meta( $item_id, 'custom_minimum_quantity', true ),
                            'custom_maximum_quantity' => get_term_meta( $item_id, 'custom_maximum_quantity', true ),
                            'effective_time' => get_term_meta( $item_id, 'effective_time', true ),
                            'custom_effective_time_slots' => \LoftOcean\is_valid_array( $custom_time_slots ) ? $custom_time_slots : array(),
                            'obligatory' => get_term_meta( $item_id, 'obligatory', true )
                        ) );
                        $display_prices = array( 'price', 'custom_adult_price', 'custom_child_price' );
                        $currency_settings = \LoftOcean\get_current_currency_settings();
                        foreach( $display_prices as $dp ) {
                            $term[ 'display_' . $dp ] = empty( $term[ $dp ] ) ? $term[ $dp ] : \LoftOcean\get_formatted_price( $term[ $dp ], $currency_settings );
                        }
                        return $term;
                    }, $terms );
                }
            }
            return array();
        }
    }
    new Extra_Services();
}
