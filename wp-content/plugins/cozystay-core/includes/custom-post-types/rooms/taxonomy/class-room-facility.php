<?php
namespace LoftOcean\Room;

if ( ! class_exists( '\LoftOcean\Room\Facilities' ) ) {
    class Facilities {
        /**
        * String Post type
        */
        protected $default_facilities = array();
        /**
        * String Post type
        */
        protected $post_type = 'loftocean_room';
        /**
        * Taxomony ID
        */
        public $taxonomy = 'lo_room_facilities';
        /*
        * capability
        */
        protected $capability = 'manage_options';
        /**
        * Construct function
        */
        public function __construct() {
            $this->capability = \LoftOcean\get_room_section_capabilities();

            add_action( 'init', array( $this, 'register_room_taxonomy' ), 100 );
            add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

            add_filter( 'loftocean_get_default_room_facilities', array( $this, 'get_default_room_facilities' ) );
            add_filter( 'loftocean_get_room_facilities', array( $this, 'get_facilities' ) );
        }

        /**
        * Create room custom taxonomy
        */
        public function register_room_taxonomy() {
            register_taxonomy( $this->taxonomy ,array( $this->post_type ), array(
                    'hierarchical' => false,
                    'labels' => array(
                    'name' => esc_html__( 'Room Facility', 'loftocean' ),
                    'singular_name' => esc_html__( 'Room Facility', 'loftocean' )
                ),
                'show_ui' => false,
                'public' => false,
                'show_in_rest' => false,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => array( 'slug' => 'room-facility' ),
                'meta_box_cb' => false
            ) );
            $this->check_default_facilities();
        }

        /**
        * Add submenu page
        */
        public function add_admin_menu() {
            $label = esc_html__( 'Room Facility', 'loftocean' );
            add_submenu_page( 'edit.php?post_type=' . $this->post_type, $label, $label, $this->capability, 'loftocean_room_facility', array( $this, 'room_facility_settings_page' ) );
        }
        /**
        * Check default facilities
        */
        protected function check_default_facilities() {
            $terms = get_terms( array(
                'taxonomy' => $this->taxonomy,
                'hide_empty' => false,
                'orderby' => 'meta_value_num',
                'order' => 'ASC',
                'meta_key' => 'priority',
                'fields' => 'ids'
            ) );
            if ( ( ! is_wp_error( $terms ) ) && ( \LoftOcean\is_valid_array( $terms ) ) ) {
                $this->default_facilities = $terms;
            } else {
                $default_terms = array(
                    array( 'label' => esc_html__( 'Room Size', 'loftocean' ), 'title' => esc_html__( 'Room Size', 'loftocean' ), 'type' => 'room-footage', 'priority' => 0, 'icon' => 'maximize' ),
                    array( 'label' => esc_html__( 'Number of Guests', 'loftocean' ), 'title' => esc_html__( 'Guests', 'loftocean' ), 'type' => 'guests', 'priority' => 10, 'icon' => 'user-2' ),
                    array( 'label' => esc_html__( 'Number of Beds', 'loftocean' ), 'title' => esc_html__( 'Bed', 'loftocean' ), 'type' => 'beds', 'priority' => 20, 'icon' => 'bed-6' ),
                    array( 'label' => esc_html__( 'Number of Bathrooms', 'loftocean' ), 'title' => esc_html__( 'Bathroom', 'loftocean' ), 'type' => 'bathrooms', 'priority' => 30, 'icon' => 'bathing' ),
                    array( 'label' => esc_html__( 'Free WIFI', 'loftocean' ), 'title' => esc_html__( 'Free WIFI', 'loftocean' ), 'type' => 'free-wifi', 'priority' => 40, 'icon' => 'wifi-2' ),
                    array( 'label' => esc_html__( 'Air Conditioning', 'loftocean' ), 'title' => esc_html__( 'Air Conditioning', 'loftocean' ), 'type' => 'air-conditioning', 'priority' => 50, 'icon' => 'air-conditioner' )
                );
                foreach ( $default_terms as $t ) {
                    $new_term = wp_insert_term( $t[ 'title' ], $this->taxonomy, array( 'description' => $t[ 'label' ] ) );
                    if ( ! is_wp_error( $new_term ) ) {
                        $new_term_id = $new_term['term_id'];
                        update_term_meta( $new_term_id, 'priority', $t[ 'priority' ] );
                        update_term_meta( $new_term_id, 'icon', $t[ 'icon' ] );
                        update_term_meta( $new_term_id, 'facility_type', $t[ 'type' ] );
                        array_push( $this->default_facilities, $new_term_id );
                    }
                }
            }
        }
        /**
        * Get default room facilities
        */
        public function get_default_room_facilities( $facility ) {
            return $this->default_facilities;
        }
        /*
        * Room facility setting page
        */
        public function room_facility_settings_page() {
            $this->save_room_facilities();

            wp_enqueue_media();
            wp_enqueue_script( 'admin-room-facility', LOFTOCEAN_URI . 'assets/scripts/admin/room-facility.min.js', array( 'jquery', 'wp-util', 'jquery-ui-sortable' ), LOFTOCEAN_ASSETS_VERSION, true );
            wp_localize_script( 'admin-room-facility', 'loftoceanRoomFacility', $this->get_facilities() );
            wp_enqueue_style( 'admin-room-facility', LOFTOCEAN_URI . 'assets/styles/room-facility.min.css', array(), LOFTOCEAN_ASSETS_VERSION );

            do_action( 'loftocean_enqueue_icons' );

            require_once LOFTOCEAN_DIR . 'includes/custom-post-types/rooms/view/taxonomy/page-facility.php';
        }
        /**
        * Save room facilitie settings
        */
        protected function save_room_facilities() {
            if ( current_user_can( $this->capability ) && isset( $_REQUEST['loftocean_room_facilites_settings_nonce'] )
                && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['loftocean_room_facilites_settings_nonce'] ) ), 'loftocean_room_facilites_settings_nonce' ) ) {
                $room_facilities = wp_unslash( $_REQUEST[ 'loftocean_room_facility' ] );
                if ( \LoftOcean\is_valid_array( $room_facilities ) ) {
                    $room_facilities = array_filter( $room_facilities, function( $item ) {
                        return ( ! empty( $item[ 'name' ] ) ) || ( ! empty( $item[ 'description' ] ) );
                    } );
                    if ( \LoftOcean\is_valid_array( $room_facilities ) ) {
                        $priority = 0;
                        foreach ( $room_facilities as $rf ) {
                            $term_id = $rf[ 'id' ];
                            if ( empty( $term_id ) ) {
                                $new_term = wp_insert_term( $rf[ 'name' ], $this->taxonomy, array( 'description' => $rf[ 'description' ] ) );
                                if ( ! is_wp_error( $new_term ) && \LoftOcean\is_valid_array( $new_term ) ) {
                                    $new_term_id = $new_term[ 'term_id' ];
                                    update_term_meta( $new_term_id, 'priority', ( $priority * 10 ) );
                                    update_term_meta( $new_term_id, 'icon', $rf[ 'icon' ] );
                                    update_term_meta( $new_term_id, 'facility_type', 'custom-facility' );
                                }
                            } else {
                                wp_update_term( $term_id, $this->taxonomy, array( 'name' => $rf[ 'name' ], 'description' => $rf[ 'description' ] ) );
                                update_term_meta( $term_id, 'priority', ( $priority * 10 ) );
                                update_term_meta( $term_id, 'icon', $rf[ 'icon' ] );
                            }
                            $priority ++;
                        }
                    }
                }
                $removed_terms = empty( $_REQUEST[ 'loftocean_room_facility_removed' ] ) ? false : sanitize_text_field( wp_unslash( $_REQUEST[ 'loftocean_room_facility_removed' ] ) );
                if ( $removed_terms ) {
                    $removed_terms = explode( ',', $removed_terms );
                    foreach( $removed_terms as $term_id ) {
                        $term = get_term( $term_id, $this->taxonomy, ARRAY_A );
                        if ( ( ! is_wp_error( $term ) ) &&  \LoftOcean\is_valid_array( $term ) && ( get_term_meta( $term_id, 'facility_type', true ) == 'custom-facility' ) ) {
                            wp_delete_term( $term_id, $this->taxonomy );
                        }
                    }
                }
            }
        }
        /*
        * Get current facilities
        */
        public function get_facilities( $facilities = array() ) {
            $terms = get_terms( array(
                'taxonomy' => $this->taxonomy,
                'hide_empty' => false,
                'orderby' => 'meta_value_num',
                'order' => 'ASC',
                'meta_key' => 'priority',
                'fields' => 'ids'
            ) );
            if ( is_wp_error( $terms ) || ( ! \LoftOcean\is_valid_array( $terms ) ) ) {
                $terms = apply_filters( 'loftocean_get_default_room_facilities', false );
            }
            if ( \LoftOcean\is_valid_array( $terms ) ) {
                return array_map( function( $item_id ) {
                    $term = get_term( $item_id, 'lo_room_facilities', ARRAY_A );
                    return array_merge( $term, array(
                        'icon' => get_term_meta( $item_id, 'icon', true ),
                        'facility_type' => get_term_meta( $item_id, 'facility_type', true )
                    ) );
                }, $terms );
            }
            return array();
        }
    }
    new Facilities();
}
