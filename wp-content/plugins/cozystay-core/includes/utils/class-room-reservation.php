<?php
namespace LoftOcean\Utils;
if ( ! class_exists( '\LoftOcean\Utils\Room_Reservation' ) ) {
    class Room_Reservation {
        /**
        * String message
        */
        protected static $message = '';
        /**
        * String room post type
        */
        protected $room_post_type = 'loftocean_room';
        /**
        * Room Reservation data
        */
        protected $room_reservation_data = array();
        /**
        * Construction function
        */
        public function __construct() {
            add_action( 'wp_ajax_add_room_to_cart', array( $this, 'ajax_add_room_to_cart' ) );
            add_action( 'wp_ajax_nopriv_add_room_to_cart', array( $this, 'ajax_add_room_to_cart' ) );

            add_action( 'woocommerce_order_status_changed', array( $this, 'order_status_check' ), 9999, 4 );
            add_action( 'woocommerce_after_order_object_save', array( $this, 'save_order_metas' ), 99, 2 );

            add_filter( 'loftocean_room_reservation_check_dates', array( $this, 'check_reservation_dates' ), 10, 5 );
        }
        /**
        * Ajax callback function for action
        */
        public function ajax_add_room_to_cart() {
            if ( isset( $_REQUEST[ 'action' ] ) && ( 'add_room_to_cart' == wp_unslash( $_REQUEST[ 'action' ] ) ) ) {
                $response = array( 'status' => 0, 'message' => '', 'redirect' => '' );
                if ( $this->do_add_to_cart() ) {
                    $response[ 'redirect' ] = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : WC()->cart->get_cart_url();
                    $response[ 'status' ] = 1;
                    echo json_encode( $response );
                    wp_die();
                } else {
                    $response[ 'message' ] = \LoftOcean\Utils\Room_Reservation::$message;
                    echo json_encode( $response );
                    wp_die();
                }
            }
        }
        /**
        * Check order status
        */
        public function order_status_check( $order_id, $from, $to, $order ) {
            $is_room_order = false;
            $items = $order->get_items();
            foreach ( $items as $item ) {
                if ( get_post_meta( $item->get_product_id(), '_loftocean_booking_id', true ) ) {
                    $is_room_order = true;
                    break;
                }
            }
            if ( $is_room_order ) {
                $room_order_status = get_post_meta( $order_id, '_loftocean_room_order_updated', true );
				$booked_status = \loftOcean\get_room_booked_status();
                $cancelled_status = \LoftOcean\get_room_available_status();
                if ( in_array( $to, $booked_status ) && ( 'yes' != $room_order_status ) ) {
                    update_post_meta( $order_id, '_loftocean_room_order_updated', 'yes' );
                    foreach ( $items as $item ) {
                        $room_id = get_post_meta( $item->get_product_id(), '_loftocean_booking_id', true );
                        if ( ( ! empty( $room_id ) ) && ( $this->room_post_type == get_post_type( $room_id ) ) ) {
                            $data = get_post_meta( $item->get_variation_id(), 'data', true );
                            for ( $i = $data[ 'check_in' ]; $i < $data[ 'check_out' ]; $i = strtotime( '+1 day', $i ) ) {
                                do_action( 'loftocean_update_room_order', array( 'room_id' => $data[ 'room_id' ], 'check_in' => $i, 'number' => $data[ 'room_num_search' ] ), 'paid' );
                            }
                        }
                    }
                }
                if ( in_array( $from, $booked_status ) && in_array( $to, $cancelled_status ) && ( 'yes' == $room_order_status ) ) {
                    update_post_meta( $order_id, '_loftocean_room_order_updated', 'no' );
                    foreach ( $items as $item ) {
                        $room_id = get_post_meta( $item->get_product_id(), '_loftocean_booking_id', true );
                        if ( ( ! empty( $room_id ) ) && ( $this->room_post_type == get_post_type( $room_id ) ) ) {
                            $data = get_post_meta( $item->get_variation_id(), 'data', true );
                            for ( $i = $data[ 'check_in' ]; $i < $data[ 'check_out' ]; $i = strtotime( '+1 day', $i ) ) {
                                do_action( 'loftocean_update_room_order', array( 'room_id' => $data[ 'room_id' ], 'check_in' => $i, 'number' => $data[ 'room_num_search' ] ), 'unpaid' );
                            }
                        }
                    }
                }
            }
        }
        /**
        * Add to cart function
        */
        protected function do_add_to_cart() {
            $pass_validate = true;
            $hide_fields = apply_filters( 'loftocean_room_reservation_form_hide_fields', array() );
            $hide_adult = ( ! empty( $hide_fields[ 'adult' ] ) );
            $hide_child = ( ! empty( $hide_fields[ 'child' ] ) );
            $hide_guests = $hide_child && $hide_adult;

            $room_id = $this->get_request( 'roomID', false );
            if ( empty( $room_id ) || ( $this->room_post_type != get_post_type( $room_id ) ) ) {
                \LoftOcean\Utils\Room_Reservation::set_message( esc_html__( 'This room is not available.', 'loftocean' ) );
                $pass_validate = false;
                return false;
            }
            $check_in = $this->get_request('checkin', false );
            if ( empty($check_in ) ) {
                \LoftOcean\Utils\Room_Reservation::set_message( esc_html__( 'Check-in date is invalid', 'loftocean' ) );
                $pass_validate = false;
                return false;
            }
            $check_out = $this->get_request( 'checkout', false );
            if ( empty( $check_out ) ) {
                \LoftOcean\Utils\Room_Reservation::set_message( esc_html__( 'Check-out date is invalid', 'loftocean' ) );
                $pass_validate = false;
                return false;
            }
            $today_timestamp = strtotime( date( 'Y-m-d' ) );
            $checkin_timestamp = strtotime( $check_in );
            $checkout_timestamp = strtotime( $check_out );
            if ( $today_timestamp > $checkin_timestamp ) {
                \LoftOcean\Utils\Room_Reservation::set_message( esc_html__( 'You can not set check-in date in the past', 'loftocean' ) );
                $pass_validate = false;
                return false;
            }
            if ( $checkout_timestamp - $checkin_timestamp <= 0 ) {
                \LoftOcean\Utils\Room_Reservation::set_message( esc_html__( 'The check-out is ealier than the check-in.', 'loftocean' ) );
                $pass_validate = false;
                return false;
            }

            $room_num_search = $this->get_request( 'room-quantity', '' );
            if ( $room_num_search <= 0 ) {
                $room_num_search = 1;
            }
            $adult_number = intval( $this->get_request( 'adult-quantity', 1 ) );
            if ( $adult_number <= 1 ) {
                $adult_number = $hide_adult ? 0 : 1;
            }
            $child_number = intval( $this->get_request( 'child-quantity', '' ) );
            if ( $child_number <= 0 ) {
                $child_number = ( $hide_adult && ( ! $hide_child ) ) ? 1 : 0;
            }
            $total_person_count = $adult_number + $child_number;

            $this->room_reservation_data = apply_filters( 'loftocean_get_room_reservation_data', array(), $room_id, $check_in, $check_out );
            if ( ( ! \LoftOcean\is_valid_array( $this->room_reservation_data ) ) || ( $unavailable_days = $this->check_day_cant_order( $room_id, $check_in, $check_out, $room_num_search ) ) ) {
                \LoftOcean\Utils\Room_Reservation::set_message( sprintf(
                    // translators: %s: unavailable date information
                    esc_html__( 'This room is not available on the following date(s): %s.', 'loftocean' ),
                    $unavailable_days
                ) );
                $pass_validate = false;
                return false;
            }

            if ( ! $hide_guests ) {
                $room_details = apply_filters( 'loftocean_get_room_details', array(), $room_id );
                if ( false !== $room_details && \LoftOcean\is_valid_array( $room_details ) ) {
                    $room_details = $room_details[ 'roomSettings' ];
                    $room_min_person = intval( $room_details[ 'minPeople' ] );
                    $room_max_person = intval( $room_details[ 'maxPeople' ] );
                    $room_max_adult = ( 'on' == $room_details[ 'enableMaxAdultNumber' ] ) && is_numeric( $room_details[ 'maxAdultNumber' ] ) ? $room_details[ 'maxAdultNumber' ] : -1;
                    $room_max_child = ( 'on' == $room_details[ 'enableMaxChildNumber' ] ) && is_numeric( $room_details[ 'maxChildNumber' ] ) ? $room_details[ 'maxChildNumber' ] : -1;

                    if ( ( $room_max_person > 0 ) && ( $room_num_search * $room_max_person < $total_person_count ) ) {
                        \LoftOcean\Utils\Room_Reservation::set_message( sprintf(
                            // translators: 1: person count, 2: room number searched
                            esc_html__( 'Upto %1$d people per %2$d room.', 'loftocean' ),
                            $room_max_person * $room_num_search,
                            $room_num_search
                        ) );
                        $pass_validate = false;
                        return false;
                    }
                    if ( ( $room_min_person > 0 ) && ( $room_num_search * $room_min_person > $total_person_count ) ) {
                        \LoftOcean\Utils\Room_Reservation::set_message( sprintf(
                            // translators: 1: person count, 2: room numer searched
                            esc_html__( 'A minimum of %1$d people is required to book %2$d rooms.', 'loftocean' ),
                            $room_min_person * $room_num_search,
                            $room_num_search
                        ) );
                        $pass_validate = false;
                        return false;
                    }
                    if ( ( ! $hide_adult ) && ( $room_max_adult > -1 ) && ( $room_num_search * $room_max_adult < $adult_number ) ) {
                        \LoftOcean\Utils\Room_Reservation::set_message( sprintf(
                            // translators: 1: adult count, 2: room number searched
                            esc_html__( 'Upto %1$d adult(s) per %2$d room(s).', 'loftocean' ),
                            $room_max_adult * $room_num_search,
                            $room_num_search
                        ) );
                        $pass_validate = false;
                        return false;
                    }
                    if ( ( ! $hide_child ) && ( $room_max_child > -1 ) && ( $room_num_search * $room_max_child < $child_number ) ) {
                        \LoftOcean\Utils\Room_Reservation::set_message( sprintf(
                            // translators: 1: child count, 2: room number searched
                            esc_html__( 'Upto %1$d child(ren) per %2$d room(s).', 'loftocean' ),
                            $room_max_child * $room_num_search,
                            $room_num_search
                        ) );
                        $pass_validate = false;
                        return false;
                    }
                }
            }


            // Check extra services
            $pass_validate = $this->check_extra_service_custom_quantity( $room_id );
            if ( ! $pass_validate ) {
                return false;
            }

            $pass_validate = apply_filters( 'loftocean_room_single_booking_rules', $pass_validate, array( 'room_id' => $room_id, 'checkin' => $checkin_timestamp, 'checkout' => $checkout_timestamp ) );
            if ( ! $pass_validate ) {
                return false;
            }

            $day_count = ( $checkout_timestamp - $checkin_timestamp ) / LOFTICEAN_SECONDS_IN_DAY;
            $total_room_price = $this->get_total_room_price( $room_id, $checkin_timestamp, $checkout_timestamp, $room_num_search, $adult_number, $child_number );
            $total_extra_price = $this->get_total_extra_price( $room_id, $day_count, $room_num_search, $adult_number, $child_number );

            $original_room_price = $total_room_price;
            $discount_details = apply_filters( 'loftocean_room_get_flexible_price_rate', false, array( 'room_id' => $room_id, 'checkin' => $checkin_timestamp, 'checkout' => $checkout_timestamp ) );
            if ( \LoftOcean\is_valid_array( $discount_details ) && isset( $discount_details[ 'totleDiscount' ], $discount_details[ 'discount' ] ) ) {
                $total_room_price *= $discount_details[ 'totleDiscount' ];
            }

            $data = array(
                'room_id' => $room_id,
                'uuid4' => \wp_generate_uuid4(),
                'original_room_price' => $original_room_price,
                'room_price' => $total_room_price,
                'check_in' => $checkin_timestamp,
                'check_out' => $checkout_timestamp,
                'room_num_search' => $room_num_search,
                'adult_number' => $adult_number,
                'child_number' => $child_number,
                'extra_services' => array(
                    'services' => $this->get_request( 'extra_service_id', array() ),
                    'titles' => $this->get_request( 'extra_service_title', array() ),
                    'prices' => $this->get_request( 'extra_service_price', array() ),
                    'customAdultPrice' => $this->get_request( 'extra_service_auto_calculating_custom_adult_price', array() ),
                    'customChildPrice' => $this->get_request( 'extra_service_auto_calculating_custom_child_price', array() ),
                    'method' => $this->get_request( 'extra_service_calculating_method', array() ),
                    'unit' => $this->get_request( 'extra_service_auto_calculating_unit', array() ),
                    'label' => $this->get_request( 'extra_service_price_label', array() ),
                    'quantity' => $this->get_request( 'extra_service_quantity', array() )
                ),
                'extra_price' => $total_extra_price,
                'extra_type' => 'services',
                'discount_details' => $discount_details,
                'total_price' => $total_room_price + $total_extra_price
            );
            $pass_validate = apply_filters( 'loftocean_room_add_cart_validate', $pass_validate, $data );
            if ( $pass_validate ) {
                $this->add_cart( $room_id, $room_num_search, $total_room_price + $total_extra_price, $data );
            }
            return $pass_validate;
        }
        /**
        * Get total room price
        */
        protected function get_total_room_price( $room_id = '', $check_in = '', $check_out = '', $number_room = 1, $adult_number = '', $child_number = '' ) {
            $number_room = empty( $number_room ) ? 1 : $number_room;
            $room_id = intval( $room_id );
            $total_price = 0;

            $rooms = array_combine( array_column( $this->room_reservation_data, 'id' ), $this->room_reservation_data );
            $default_adult_price = get_post_meta( $room_id, 'loftocean_room_price_per_adult', true );
            $default_child_price = get_post_meta( $room_id, 'loftocean_room_price_per_child', true );
            $default_regular_price = get_post_meta( $room_id, 'loftocean_room_regular_price', true );
            $price_by_per_person = ( 'on' == get_post_meta( $room_id, 'loftocean_room_price_by_people', true ) );

            $current_room_variable_price_settings = \LoftOcean\get_room_variable_prices( $room_id );
            $has_variable_price = $current_room_variable_price_settings[ 'enable' ] && \LoftOcean\is_valid_array( $current_room_variable_price_settings[ 'prices' ] );

            for ( $i = $check_in; $i < $check_out; $i = strtotime( '+1 day', $i ) ) {
                if ( isset( $rooms[ $i ] ) ) {
                    $item = $rooms[ $i ];
                    if ( $has_variable_price ) {
                        $item = apply_filters( 'loftocean_get_room_current_prices', $item, $current_room_variable_price_settings, $adult_number, $child_number );
                    }

                    $current_rate = isset( $item[ 'special_price_rate' ] ) ? $item[ 'special_price_rate' ] : 1;

                    if ( $price_by_per_person ) {
                        $adult_price = empty( $item[ 'adult_price' ] ) ? $default_adult_price : $item[ 'adult_price' ];
                        $child_price = empty( $item[ 'child_price' ] ) ? $default_child_price : $item[ 'child_price' ];
                        $total_price += is_numeric( $adult_price ) ? $adult_number * $adult_price * $current_rate : 0;
                        $total_price += is_numeric( $child_price ) ? $child_number * $child_price * $current_rate : 0;
                    } else {
                        $price = empty( $item[ 'price' ] ) ? $default_regular_price : $item[ 'price' ];
                        $total_price += is_numeric( $price ) ? $number_room * $price * $current_rate : 0;
                    }
                }
            }
            return $total_price;
        }
        /**
        */
        protected function get_total_extra_price( $room_id, $day_count, $room_num_search, $adult_number, $child_number ) {
            $extra_service_ids = $this->get_request( 'extra_service_id', array() );
            $enabled_extra_services = apply_filters( 'loftocean_get_room_extra_services_enabled', array(), $room_id );
            $total_price = 0;
            if ( \LoftOcean\is_valid_array( $extra_service_ids ) && \LoftOcean\is_valid_array( $enabled_extra_services ) ) {
                $custom_quantity = $this->get_request( 'extra_service_quantity', array() );
                foreach ( $extra_service_ids as $esi ) {
                    if ( in_array( $esi, $enabled_extra_services ) ) {
                        $price = get_term_meta( $esi, 'price', true );
                        $method = get_term_meta( $esi, 'method', true );
                        $auto_method = get_term_meta( $esi, 'auto_method', true );
                        $index = 'extra_service_' . $esi;
                        if ( ! empty( $price ) ) {
                            switch ( $method ) {
                                case 'custom':
                                    if ( ! empty( $custom_quantity[ $index ] ) ) {
                                        $total_price += $price * $custom_quantity[ $index ];
                                    }
                                    break;
                                case 'auto_custom':
                                    if ( ! empty( $custom_quantity[ $index ] ) ) {
                                        $total_price += $price * $custom_quantity[ $index ] * $day_count;
                                    }
                                    break;
                                case 'auto':
                                    if ( ! empty( $auto_method ) ) {
                                        if ( in_array( $auto_method, array( 'night-room' ) ) ) {
                                            $price *= $room_num_search;
                                        }
                                        if ( in_array( $auto_method, array( 'person', 'night-person' ) ) ) {
                                            $custom_adult_price = get_term_meta( $esi, 'custom_adult_price', true );
                                            $custom_child_price = get_term_meta( $esi, 'custom_child_price', true );
                                            if ( ( '' !== $custom_adult_price ) || ( '' !== $custom_child_price ) ) {
                                                $custom_adult_price = empty( $custom_adult_price ) ? 0 : $custom_adult_price;
                                                $custom_child_price = empty( $custom_child_price ) ? 0 : $custom_child_price;
                                                $price = $custom_adult_price * $adult_number + $custom_child_price * $child_number;
                                            } else {
                                                $price *= ( $adult_number + $child_number );
                                            }
                                        }
                                        if ( in_array( $auto_method, array( 'night', 'night-person', 'night-room' ) ) ) {
                                            $price *= $day_count;
                                        }
                                        $total_price += $price;
                                    }
                                    break;
                                default:
                                    $total_price += $price;
                            }
                        }
                    }
                }
            }
            return $total_price;
        }
        /**
        * Helper function
        */
        protected function check_day_cant_order( $room_id, $check_in, $check_out, $number_room ) {
            global $wpdb;
            $room = intval( get_post_meta( $room_id, 'loftocean_room_number', true ) );
            $results = array_combine( array_column( $this->room_reservation_data, 'id' ), $this->room_reservation_data );
            $check_in = strtotime( $check_in );
            $check_out = strtotime( $check_out );
            $is_price_by_person = ( get_post_meta( $room_id, 'loftocean_room_price_by_people', true ) == 'on' );
            $unavailable = array();
            for ( $i = $check_in; $i < $check_out; $i = strtotime( '+1 day', $i ) ) {
                if ( isset( $results[ $i ] ) ) {
                    $item = $results[ $i ];
                    if ( $is_price_by_person ) {
                        if ( empty( $item[ 'adult_price' ] ) && empty( $item[ 'child_price' ] ) ) {
                            array_push( $unavailable, date( 'Y-m-d', $i ) );
                        }
                    } else {
                        if ( empty( $item[ 'price' ] ) ) {
                            array_push( $unavailable, date( 'Y-m-d', $i ) );
                        }
                    }
		            if ( 'unavailable' == $item[ 'status' ] || $item[ 'available_number' ] < $number_room ) {
                        array_push( $unavailable, date( 'Y-m-d', $i ) );
		            }
                } else {
                    array_push( $unavailable, date( 'Y-m-d', $i ) );
                }
            }
            return \LoftOcean\is_valid_array( $unavailable ) ? implode( ', ', $unavailable ) : false;
        }
        /**
        * Add to woocommerce cart
        */
        protected function add_cart( $item_id, $number = 1, $price = false, $data = [] ) {
            $data[ 'loftocean_booking_id' ] = $item_id;
            $number = intval( $number );
            $cart_data = array(
                'number' => $number,
                'price'  => $price,
                'data'   => $data,
                'title'  => get_the_title( $item_id )
            );
            $post_id = intval( $cart_data[ 'data' ][ 'room_id' ] );
            $product_id = $this->_create_new_product( $post_id, $cart_data );
            if ( $product_id ) {
                $this->_add_product_to_cart( $product_id, $cart_data[ 'data' ] );
            }
            $cart_data[ 'data' ][ 'user_id' ] = get_current_user_id();
            $this->destroy_cart();
            $data_cart = array( $item_id => $cart_data );
            if ( is_user_logged_in() ) {
                $userID = get_current_user_id();
                update_user_meta( $userID, '_save_cart_data_' . $userID, $data_cart );
            }
            $this->set_cart( 'loftocean_cart', $data_cart );
        }
        /**
        * Create new product
        */
        protected function _create_new_product( $item_id, $cart_item ) {
            $default = array( 'title' => '', 'price' => 0, 'number' => 1, 'data' => '' );
            $cart_item = wp_parse_args( $cart_item, $default );
            $total_cart_item_price = 0;
            $cart_item[ 'number' ] = empty( $cart_item[ 'number' ] ) ? 1 : $cart_item[ 'number' ];
            $total_cart_item_price = apply_filters( 'loftocean_room_item_total', $cart_item[ 'price' ], $item_id, $cart_item );
            $thumbnail_id = get_post_thumbnail_id( $item_id );
            $product_id = 0;
            $args = array( 'post_type' => 'product', 'meta_key' => '_loftocean_booking_id', 'meta_value' => $item_id, 'offset' => 0, 'posts_per_page' => 1, 'post_status' => 'publish' );
            query_posts( $args );
            if ( have_posts() ) {
                while ( have_posts() ) {
                    the_post();
                    $product_id = get_the_ID();
                    wp_update_post( array( 'ID' => $product_id, 'post_title' => $cart_item[ 'title' ] ) );
                    empty( $thumbnail_id ) ? '' : update_post_meta( $product_id, '_thumbnail_id', $thumbnail_id );
                }
                wp_reset_postdata();
            } else {
                $product_id = wp_insert_post( array(
                    'post_content'   => '',
                    'post_status'    => 'publish',
                    'post_title'     => $cart_item[ 'title' ],
                    'post_parent'    => '',
                    'post_type'      => 'product',
                    'comment_status' => 'closed'
                ) );
                if ( is_wp_error( $product_id ) ) {
                    \LoftOcean\Utils\Room_Reservation::set_message( esc_html__( 'Sorry! Can not create product', 'loftocean' ) );
                    return false;
                }
                // Product Type simple
                wp_set_object_terms( $product_id, 'variable', 'product_type' );
                update_post_meta( $product_id, '_manage_stock', 'no' );
                update_post_meta( $product_id, '_stock_status', 'instock' );
                update_post_meta( $product_id, '_sold_individually', 'yes' );
                update_post_meta( $product_id, '_sku', sanitize_title( $cart_item[ 'title' ] ) );
                update_post_meta( $product_id, '_loftocean_booking_id', $item_id );
                update_post_meta( $product_id, '_downloadable', 'no' );
                update_post_meta( $product_id, '_virtual', 'yes' );
                update_post_meta( $product_id, 'attribute_types', '' );
                empty( $thumbnail_id ) ? '' : update_post_meta( $product_id, '_thumbnail_id', $thumbnail_id );

                $wc_product = wc_get_product( $product_id );
                $wc_product->set_catalog_visibility( 'hidden' );
                $wc_product->save();
            }

            $variation = array(
                'post_content'  => '',
                'post_status'   => 'publish',
                'post_title'    => sprintf(
                    // translators: 1: cart item title, 2: current date info
                    esc_html__( '%1$s in %2$s', 'loftocean' ),
                    $cart_item[ 'title' ],
                    date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) )
                ),
                'post_parent'    => $product_id,
                'post_type'      => 'product_variation',
                'comment_status' => 'closed'
            );
            $variation_id = wp_insert_post( $variation );
            if ( is_wp_error( $variation_id ) ) {
                \LoftOcean\Utils\Room_Reservation::set_message( esc_html__( 'Sorry! Can not create variation product', 'loftocean' ) );
                return false;
            }

            update_post_meta( $variation_id, '_regular_price', $total_cart_item_price );
            update_post_meta( $variation_id, 'data', $cart_item[ 'data' ] );
            update_post_meta( $variation_id, '_virtual', 'yes' );

            ob_start();
            $room_order_item_data = $cart_item[ 'data' ];
            require LOFTOCEAN_DIR . 'includes/custom-post-types/rooms/order/room-item-details.php';
            $order_details = ob_get_clean();

            $wc_product = wc_get_product( $variation_id ); 
            $wc_product->set_description( $order_details );
            $wc_product->save();

            return array(
                'product_id'   => $product_id,
                'variation_id' => $variation_id
            );
        }
        /**
        * Add product to cart by product id
        */
        protected function _add_product_to_cart( $product_id, $cart_data = array() ) {
            if ( is_array( $product_id ) && isset( $product_id[ 'product_id' ], $product_id[ 'variation_id' ] ) ) {
                $cart = WC()->cart->add_to_cart( $product_id[ 'product_id' ], 1, $product_id[ 'variation_id' ], array(), array( 'loftocean_booking_data' => $cart_data ) );
            }
        }
        /**
        * Set cart cookie
        */
        protected function set_cart( $cart_name, $data ) {
            $data_compress = base64_encode( gzcompress( addslashes(serialize( $data ) ), 9 ) );
            $this->setcookie( $cart_name, $data_compress, time() + ( LOFTICEAN_SECONDS_IN_DAY * 30 ) );
        }
        /**
        * Delete cart
        */
        protected function destroy_cart() {
            $expire = time() - 3600;
            do_action( 'loftocean_before_destroy_cart' );
            $this->setcookie( 'loftocean_cart', '', $expire );
            $this->setcookie( 'loftocean_cart_coupon', '', $expire );
            do_action( 'loftocean_after_destroy_cart' );
        }
        /**
        * Set cookie value
        */
        protected function setcookie( $name, $value, $expire = 0, $secure = false ) {
            setcookie( $name, $value, $expire, '/', null, null );
        }
        /**
        * Helper function get request paramater
        */
        protected function get_request( $param, $default_value = false ) {
            return isset( $_REQUEST[ $param ] ) ? wp_unslash( $_REQUEST[ $param ] ) : $default_value;
        }
        /**
        * Save metas for woocommerce order
        */
        public function save_order_metas( $order, $data ) {
            $order_id = $order->get_id();
            if ( 'yes' != $order->get_meta( '_loftocean_rooms_updated' ) ) {
                $items = $order->get_items();
                $roomIDs = array();
                foreach ( $items as $item ) {
                    $room_id = get_post_meta( $item->get_product_id(), '_loftocean_booking_id', true );
                    if ( ! ( empty( $room_id ) || in_array( $room_id, $roomIDs ) ) ) {
                        $order->add_meta_data( '_loftocean_order_room_ID', $room_id, false );
                        array_push( $roomIDs, $room_id );
                    }
                }
                $order->update_meta_data( '_loftocean_rooms_updated', 'yes' );
            }
        }
        /**
        * Check room reservation dates
        */
        public function check_reservation_dates( $status, $room_id, $checkin, $checkout, $num ) {
            $this->room_reservation_data = apply_filters( 'loftocean_get_room_reservation_data', array(), $room_id, $checkin, $checkout, true );
            if ( \LoftOcean\is_valid_array( $this->room_reservation_data ) ) {
                $unavailable_days = $this->check_day_cant_order( $room_id, $checkin, $checkout, $num );
                return false === $unavailable_days;
            } else {
                return false;
            }
        }
        /**
        * Set error message
        */
        public static function set_message( $error ) {
            \LoftOcean\Utils\Room_Reservation::$message = $error;
        }
        /**
        * Check extra service custom quantity
        */
        protected function check_extra_service_custom_quantity( $room_id ) {
            $extra_service_ids = $this->get_request( 'extra_service_id', array() );
            $enabled_extra_services = apply_filters( 'loftocean_get_room_extra_services_enabled', array(), $room_id );
            if ( \LoftOcean\is_valid_array( $extra_service_ids ) && \LoftOcean\is_valid_array( $enabled_extra_services ) ) {
                $custom_quantities = $this->get_request( 'extra_service_quantity', array() );
                $extra_service_titles = $this->get_request( 'extra_service_title', array() );
                $quantity_check = array( 'minimum', 'maximum' );
                $message = array(
                    'minimum' => esc_html__(
                        // translators: 1: service name 2: custom minimum quantity
                        'The minimum quantity for service "%1$s" is %2$s.', 'loftocean'
                    ),
                    'maximum' => esc_html__(
                        // translators: 1: service name 2: custom maximum quantity
                        'The maximum quantity for service "%1$s" is %2$s.', 'loftocean' )
                );
                $messages = array();
                foreach ( $extra_service_ids as $esi ) {
                    $index = 'extra_service_' . $esi;
                    $method = get_term_meta( $esi, 'method', true );
                    if ( in_array( $esi, $enabled_extra_services ) && in_array( $method, array( 'custom', 'auto_custom' ) ) && ( ! empty( $custom_quantities[ $index ] ) ) ) {
                        $custom_quantity = intval( $custom_quantities[ $index ] );
                        foreach( $quantity_check as $qc ) {
                            $meta_val = get_term_meta( $esi, 'custom_' . $qc . '_quantity', true );
                            if ( ( $meta_val != '' ) && ( intval( $meta_val ) > 0 ) ) {
                                $meta_val = intval( $meta_val );
                                $failed = ( $qc == 'minimum' ) ? ( $custom_quantity < $meta_val ) : ( $custom_quantity > $meta_val );
                                if ( $failed ) {
                                    array_push( $messages, sprintf(
                                        /** // translators: %1$s: extra service title, %2$s: limitation **/
                                        $message[ $qc ],
                                        $extra_service_titles[ $index ],
                                        $meta_val
                                    ) );
                                }
                            }
                        }
                    }
                }
                if ( \LoftOcean\is_valid_array( $messages ) ) {
                    \LoftOcean\Utils\Room_Reservation::set_message( sprintf(
                        // translators: 1: line break, 2: messages
                        esc_html__( 'The custom quantity for the following extra services are not correct:%1$s%2$s', 'loftocean' ),
                        '<br>',
                        implode( '<br>', $messages )
                    ) );
                    return false;
                }
            }
            return true;
        }
    }
    new Room_Reservation();
}
