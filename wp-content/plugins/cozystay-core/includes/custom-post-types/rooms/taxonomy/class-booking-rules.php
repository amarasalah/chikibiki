<?php
namespace LoftOcean\Room;

if ( ! class_exists( '\LoftOcean\Room\Booking_Rules' ) ) {
    class Booking_Rules {
        /**
        * String Post type
        */
        protected $post_type = 'loftocean_room';
        /**
        * Taxomony type
        */
        public $taxonomy = 'lo_room_booking_rules';
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

            add_filter( 'loftocean_get_room_booking_rules', array( $this, 'get_rules' ) );
            add_filter( 'loftocean_get_room_current_booking_rules', array( $this, 'get_room_current_rules' ), 10, 2 );
        }

        /**
        * Create rooms custom taxonomy
        */
        public function register_room_taxonomy() {
            register_taxonomy( $this->taxonomy ,array( $this->post_type ), array(
                    'hierarchical' => false,
                    'labels' => array(
                    'name' => esc_html__( 'Booking Rules', 'loftocean' ),
                    'singular_name' => esc_html__( 'Booking Rule', 'loftocean' )
                ),
                'show_ui' => false,
                'public' => false,
                'show_in_rest' => false,
                'show_admin_column' => false,
                'query_var' => true,
                'rewrite' => array( 'slug' => 'room-booking-rule' ),
                'meta_box_cb' => false
            ) );
        }

        /**
        * Add submenu page
        */
        public function add_admin_menu() {
            $label = esc_html__( 'Booking Rules', 'loftocean' );
            add_submenu_page( 'edit.php?post_type=' . $this->post_type, $label, $label, $this->capability, 'loftocean_room_booking_rules', array( $this, 'room_rules_settings_page' ) );
        }
        /*
        * Room rules setting page
        */
        public function room_rules_settings_page() {
            $this->save_room_rules();

            wp_enqueue_script( 'admin-room-rules', LOFTOCEAN_URI . 'assets/scripts/admin/room-rules.min.js', array( 'jquery', 'wp-util', 'jquery-ui-sortable', 'jquery-ui-datepicker', 'wp-api-request', 'loftocean-select2' ), LOFTOCEAN_ASSETS_VERSION, true );
            wp_localize_script( 'admin-room-rules', 'loftoceanRoomRules', $this->get_rules() );
            wp_enqueue_script( 'loftocean-select2', LOFTOCEAN_URI . 'assets/libs/select2/js/select2.full.min.js', array( 'jquery' ), '4.0.13', true );
            wp_enqueue_style( 'loftocean-select2', LOFTOCEAN_URI . 'assets/libs/select2/css/select2.min.css', array(), '4.0.13' );
            wp_enqueue_style( 'admin-room-rules', LOFTOCEAN_URI . 'assets/styles/room-rules.min.css', array(), LOFTOCEAN_ASSETS_VERSION );
            wp_enqueue_style( 'jquery-ui', '//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css', array(), '1.13.1' );

            require_once LOFTOCEAN_DIR . 'includes/custom-post-types/rooms/view/taxonomy/page-booking-rules.php';
        }
        /**
        * Save room rules
        */
        protected function save_room_rules() {
            if ( current_user_can( $this->capability ) && isset( $_REQUEST[ 'loftocean_room_rules_settings_nonce' ] ) && wp_verify_nonce( $_REQUEST[ 'loftocean_room_rules_settings_nonce' ], 'loftocean_room_booking_rules' ) ) {
                $rules = isset( $_REQUEST[ 'loftocean_room_booking_rules' ] ) ? wp_unslash( $_REQUEST[ 'loftocean_room_booking_rules' ] ) : false;
                if ( \LoftOcean\is_valid_array( $rules ) ) {
                    $rules = array_filter( $rules, function( $item ) {
                        return ! empty( $item[ 'title' ] );
                    } );
                    if ( \LoftOcean\is_valid_array( $rules ) ) {
                        $priority = 0;
                        foreach ( $rules as $rule ) {
                            $term_id = $rule[ 'id' ];
                            $rule = \LoftOcean\merge_array( array(
                                'time_range' => '',
                                'stay_length' => array( 'general' => array( 'enable' => '' ), 'custom' => array( 'enable' => '' ) ),
                                'no_checkin_checkout_date' => array(
                                    'enable' => '',
                                    'checkin' => array( 'day0' => '', 'day1' => '', 'day2' => '', 'day3' => '', 'day4' => '', 'day5' => '', 'day6' => '' ),
                                    'checkout' => array( 'day0' => '', 'day1' => '', 'day2' => '', 'day3' => '', 'day4' => '', 'day5' => '', 'day6' => '' )
                                ),
                                'in_advance' => array( 'enable' => '' )
                            ), $rule );

                            if ( empty( $term_id ) ) {
                                $new_term = wp_insert_term( $rule[ 'title' ], $this->taxonomy );
                                if ( ! is_wp_error( $new_term ) && \LoftOcean\is_valid_array( $new_term ) ) {
                                    $new_term_id = $new_term[ 'term_id' ];
                                    $rule[ 'term_id' ] = $new_term_id;
                                    update_term_meta( $new_term_id, 'priority', ( $priority * 10 ) );
                                    update_term_meta( $new_term_id, 'time_range', $rule[ 'time_range' ] );
                                    update_term_meta( $new_term_id, 'start_date', empty( $rule[ 'start_date' ] ) ? '' : strtotime( $rule[ 'start_date' ] ) );
                                    update_term_meta( $new_term_id, 'end_date', empty( $rule[ 'end_date' ] ) ? '' : strtotime( $rule[ 'end_date' ] ) );
                                    update_term_meta( $new_term_id, 'apply_to', sanitize_text_field( $rule[ 'apply_to' ] ) );
                                    update_term_meta( $new_term_id, 'apply_to_room_types', isset( $rule[ 'apply_to_room_types' ] ) && \LoftOcean\is_valid_array( $rule[ 'apply_to_room_types' ] ) ? $rule[ 'apply_to_room_types' ] : array() );
                                    update_term_meta( $new_term_id, 'apply_to_rooms', isset( $rule[ 'apply_to_rooms' ] ) && \LoftOcean\is_valid_array( $rule[ 'apply_to_rooms' ] ) ? $rule[ 'apply_to_rooms' ] : array() );
                                    update_term_meta( $new_term_id, 'rule_details', $rule );
                                }
                            } else {
                                $rule[ 'term_id' ] = $term_id;
                                wp_update_term( $term_id, $this->taxonomy, array( 'name' => $rule[ 'title' ] ) );
                                update_term_meta( $term_id, 'priority', ( $priority * 10 ) );
                                update_term_meta( $term_id, 'time_range', $rule[ 'time_range' ] );
                                update_term_meta( $term_id, 'start_date', empty( $rule[ 'start_date' ] ) ? '' : strtotime( $rule[ 'start_date' ] ) );
                                update_term_meta( $term_id, 'end_date', empty( $rule[ 'end_date' ] ) ? '' : strtotime( $rule[ 'end_date' ] ) );
                                update_term_meta( $term_id, 'apply_to', sanitize_text_field( $rule[ 'apply_to' ] ) );
                                update_term_meta( $term_id, 'apply_to_room_types', isset( $rule[ 'apply_to_room_types' ] ) && \LoftOcean\is_valid_array( $rule[ 'apply_to_room_types' ] ) ? $rule[ 'apply_to_room_types' ] : array() );
                                update_term_meta( $term_id, 'apply_to_rooms', isset( $rule[ 'apply_to_rooms' ] ) && \LoftOcean\is_valid_array( $rule[ 'apply_to_rooms' ] ) ? $rule[ 'apply_to_rooms' ] : array() );
                                update_term_meta( $term_id, 'rule_details', $rule );
                            }
                            $priority ++;
                        }
                    }
                }

                $removed_terms = empty( $_REQUEST[ 'loftocean_room_rules_removed' ] ) ? false : sanitize_text_field( wp_unslash( $_REQUEST[ 'loftocean_room_rules_removed' ] ) );
                if ( $removed_terms ) {
                    $removed_terms = explode( ',', $removed_terms );
                    foreach( $removed_terms as $term_id ) {
                        $term = get_term( $term_id, $this->taxonomy, ARRAY_A );
                        if ( ( ! is_wp_error( $term ) ) &&  \LoftOcean\is_valid_array( $term ) ) {
                            wp_delete_term( $term_id, $this->taxonomy );
                        }
                    }
                }
                $this->update_single_room_rules(); ?>
                <div id="message" class="notice notice-success"><p><strong><?php esc_html_e( 'Booking rules updated.', 'loftocean' ); ?></strong></p></div><?php
            }
        }
        /*
        * Get current rules
        */
        public function get_rules( $rules = array() ) {
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
                    $details = \LoftOcean\merge_array( array(
                        'time_range' => '',
                        'stay_length' => array( 'general' => array( 'enable' => '' ), 'custom' => array( 'enable' => '' ) ),
                        'no_checkin_checkout_date' => array(
                            'enable' => '',
                            'checkin' => array( 'day0' => '', 'day1' => '', 'day2' => '', 'day3' => '', 'day4' => '', 'day5' => '', 'day6' => '' ),
                            'checkout' => array( 'day0' => '', 'day1' => '', 'day2' => '', 'day3' => '', 'day4' => '', 'day5' => '', 'day6' => '' )
                        ),
                        'in_advance' => array( 'enable' => '' ),
                    ), get_term_meta( $item_id, 'rule_details', true ) );

                    $details[ 'term_id' ] = $item_id;
                    $details[ 'apply_to_rooms' ] = isset( $details[ 'apply_to_rooms' ] ) && \LoftOcean\is_valid_array( $details[ 'apply_to_rooms' ] ) ? $details[ 'apply_to_rooms' ] : array();
                    $details[ 'apply_to_room_types' ] = isset( $details[ 'apply_to_room_types' ] ) && \LoftOcean\is_valid_array( $details[ 'apply_to_room_types' ] ) ? $details[ 'apply_to_room_types' ] : array();
                    return $details;
                }, $terms );
            }
            return array();
        }
        /**
        * Get room current enabled rules
        */
        public function get_room_current_rules( $rules, $room_id ) {
            $terms = wp_get_post_terms( $room_id, $this->taxonomy, array( 'fields' => 'ids' ) );
            if ( \LoftOcean\is_valid_array( $terms ) ) {
                return get_terms( array(
                    'taxonomy' => $this->taxonomy,
                    'include' => $terms,
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
        * Update rules for each single room
        */
        public function update_single_room_rules() {
            $rooms = $this->get_rooms_need_to_update();
            if ( ! \LoftOcean\is_valid_array( $rooms ) ) return;

            $results = array();
            $room_types = array();
            foreach ( $rooms as $rid ) {
                $results[ 'room_' . $rid ] = array();
            }

            $terms = get_terms( array( 'taxonomy' => $this->taxonomy, 'hide_empty' => false, 'orderby' => 'meta_value_num', 'order' => 'ASC', 'meta_key' => 'priority', 'fields' => 'ids' ) );
            if ( ( ! is_wp_error( $terms ) ) && \LoftOcean\is_valid_array( $terms ) ) {
                foreach ( $terms as $tid ) {
                    $apply_to = get_term_meta( $tid, 'apply_to', true );
                    switch ( $apply_to ) {
                        case 'all':
                            $results = $this->update_list( $results, $rooms, $tid );
                            break;
                        case 'room_types':
                            $selected_types = get_term_meta( $tid, 'apply_to_room_types', true );
                            if ( \LoftOcean\is_valid_array( $selected_types ) ) {
                                foreach ( $selected_types as $srt ) {
                                    $index = 'room_types_' . $srt;
                                    if ( ! isset( $room_types[ $index ] ) ) {
                                        $room_types[ $index ] = $this->get_rooms_need_to_update( array( 'tax_query' => array(
                                            array( 'taxonomy' => 'lo_room_type', 'field' => 'term_id', 'terms' => absint( $srt ) )
                                        ) ) );
                                    }
                                    if ( \LoftOcean\is_valid_array( $room_types[ $index ] ) ) {
                                        $results = $this->update_list( $results, $room_types[ $index ], $tid );
                                    }
                                }
                            }
                            break;
                        case 'rooms':
                            $selected_rooms = get_term_meta( $tid, 'apply_to_rooms', true );
                            if ( \LoftOcean\is_valid_array( $selected_rooms ) ) {
                                $results = $this->update_list( $results, $selected_rooms, $tid );
                            }
                            break;
                    }
                }
            }
            if ( \LoftOcean\is_valid_array( $results ) ) {
                foreach ( $results as $room => $ruleIDs ) {
                    wp_set_post_terms( str_replace( 'room_', '', $room ), $ruleIDs, $this->taxonomy );
                }
            }
        }
        /**
        * Get rooms
        */
        public function get_rooms_need_to_update( $extra_args = array() ) {
            $results = array();
            $posts_per_page = 100;
            $args = array_merge( array(
                'post_type' => 'loftocean_room',
                'posts_per_page' => $posts_per_page,
                'offset' => 0,
                'fields' => 'ids',
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key' => 'update_booking_rules_manully',
                        'compare' => 'NOT EXISTS',
                    ),
                    array(
                        'key' => 'update_booking_rules_manully',
                        'value' => 'on',
                        'compare' => '!='
                    ),
                )
            ), $extra_args );
            do {
				$q = get_posts( $args );
				if ( \LoftOcean\is_valid_array( $q ) ) {
                    $results = array_merge( $results, $q );
                    $args[ 'offset' ] += $posts_per_page;
                }
			} while ( count( $q ) === $posts_per_page );

            return $results;
        }
        /**
        * update list
        */
        protected function update_list( $list, $rooms, $rule_id ) {
            foreach ( $rooms as $rid ) {
                $index = 'room_' . $rid;
                if ( isset( $list[ $index ] ) && \LoftOcean\is_valid_array( $list[ $index ] ) ) {
                    in_array( $rule_id, $list[ $index ] ) ? '' : array_push( $list[ $index ], $rule_id );
                } else {
                    $list[ $index ] = array( $rule_id );
                }
            }
            return $list;
        }
    }
    new Booking_Rules();
}
