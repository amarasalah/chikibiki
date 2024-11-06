<?php
namespace LoftOcean\Room\WooCommerce;

if ( class_exists( 'WooCommerce' ) && ( ! class_exists( '\LoftOcean\Room\WooCommerce\Cart_Item_Check' ) ) ) {
    class Cart_Item_Check {
        /**
        * String Post type
        */
        protected $post_type = 'loftocean_room'; 
        /**
        * Construct function
        */
        public function __construct() {
            add_action( 'woocommerce_check_cart_items', array( $this, 'shortcode_check_cart_items' ), 0 );
            add_action( 'woocommerce_store_api_cart_errors', array( $this, 'block_check_cart_items' ), 99, 2 );
            // add_action( 'woocommerce_store_api_validate_cart_item', array( $this, 'validate_cart_item' ), 99, 2 );
        }
        /*
        * Validate block version cart item
        */
        public function validate_cart_item( $product, $cart_item ) { 
            if ( ( ! isset( $cart_item[ 'loftocean_booking_data' ] ) ) || ( ! \LoftOcean\is_valid_array( $cart_item[ 'loftocean_booking_data' ] ) ) ) return; 

            throw new \Automattic\WooCommerce\StoreApi\Exceptions\RouteException (
                'woocommerce_room_validation_error',
                $product->get_name(),
                400
            );
        }
        /*
        * Block version cart item checking
        */
        public function block_check_cart_items( $cart_error, $cart ) {
            $error_message = $this->check_cart_items();
            if ( \LoftOcean\is_valid_array( $error_message ) ) {
                $message = '<ul class="room-error-message">';
                foreach( $error_message as $em ) {
                    $message .= '<li>' . $em . '</li>';
                }
                $message .= '</ul>';
                $cart_error->add( 'woocommerce_cart_room_validation_error', $message );
            }
        }
        /**
        * Shortcode version cart items checking
        */
        public function shortcode_check_cart_items() {
            $error_message = $this->check_cart_items();
            if ( \LoftOcean\is_valid_array( $error_message ) ) {
                foreach( $error_message as $em ) {
                    wc_add_notice( $em, 'error' );
                }
            }
        }
        /**
        * Looks through the cart to check each item is in stock. If not, add an error.
        *
        * @return bool|WP_Error
        */
        public function check_cart_items() {
            $room_dates = array();
            $error_message = array();
            $cart = $this->get_cart();
            $today_timestamp = strtotime( 'today' );
            $date_format = get_option( 'date_format', 'Y-m-d' );

            if ( ! \LoftOcean\is_valid_array( $cart ) ) return false;

            foreach ( $cart as $values ) {
                if ( ( ! isset( $values[ 'loftocean_booking_data' ] ) ) || ( ! \LoftOcean\is_valid_array( $values[ 'loftocean_booking_data' ] ) ) ) continue; 

                $product = $values[ 'data' ];
                $product_name = $product->get_name();
                $room_details = $values[ 'loftocean_booking_data' ];

                if ( isset( $room_details[ 'check_in' ], $room_details[ 'check_out' ] ) ) {
                    if ( $room_details[ 'check_in' ] < $today_timestamp ) {
                        $error_message[] = sprintf( 
                            /* translators: 1: date range 2: product name */
                            __( 'Your selected dates (%1$s) for the "%2$s" in your reservation have expired.', 'loftocean' ), 
                            date_i18n( $date_format, $room_details[ 'check_in' ] ). ' - ' . date_i18n( $date_format, $room_details[ 'check_out' ] ), 
                            '<strong>' . $product_name . '</strong>' 
                        );
                    }
                    $room_unique_id = \LoftOcean\get_room_unique_id( $room_details[ 'room_id' ] );
                    if ( ! isset( $room_dates[ $room_unique_id ] ) ) {
                        $room_dates[ $room_unique_id ] = array( 'name' => $product_name, 'rid' => $room_details[ 'room_id' ], 'list' => array() );
                    }
                    for ( $i = $room_details[ 'check_in' ]; $i < $room_details[ 'check_out' ]; $i += LOFTICEAN_SECONDS_IN_DAY ) {
                        $room_number = isset( $room_details[ 'room_num_search' ] ) && is_numeric( $room_details[ 'room_num_search' ] ) && ( $room_details[ 'room_num_search' ] > 0 ) ? $room_details[ 'room_num_search' ] : 1;
                        if ( isset( $room_dates[ $room_unique_id ][ 'list' ][ $i ] ) ) {
                            $room_dates[ $room_unique_id ][ 'list' ][ $i ][ 'number' ] += $room_number;
                        } else {
                            $room_dates[ $room_unique_id ][ 'list' ][ $i ] = array( 'number' => $room_number, 'timestamp' => $i, 'date' => date_i18n( $date_format, $i ) );
                        }
                    }
                }
            }
            if ( \LoftOcean\is_valid_array( $room_dates ) ) {
                foreach( $room_dates as $ruid => $data ) {
                    $list = $data[ 'list' ];
                    ksort( $list );

                    $details = apply_filters( 'loftocean_get_room_reservation_data', array(), $data[ 'rid' ], date( 'Y-m-d', reset( $list )[ 'timestamp' ] ), date( 'Y-m-d', end( $list )[ 'timestamp' ] ) );
                    $details = \LoftOcean\is_valid_array( $details ) ? array_combine( array_column( $details, 'id' ), $details ) : array();
                    $result = $this->check_availability( $list, $details );
                    if ( \LoftOcean\is_valid_array( $result ) ) {
                        /* translators: 1: product name 2: date details */
                        $error_message[] = sprintf( __( 'Sorry, we do not have enough "%1$s" rooms to fulfill your reservation: %2$s', 'loftocean' ), '<strong>' . $data[ 'name' ] . '</strong>', '<ul class="unavailable-dates">' . implode( '', $result ) . '</ul>' );
                        
                    }
                }
                return $error_message;
            }

            return false;
        }
        /**
         * Returns the contents of the cart in an array.
         *
         * @return array contents of the cart
         */
        public function get_cart() {
            if ( ! did_action( 'wp_loaded' ) ) {
                wc_doing_it_wrong( __FUNCTION__, __( 'Get cart should not be called before the wp_loaded action.', 'loftocean' ), '2.3' );
            }
            $cart = \WC()->cart;
            return isset( $cart ) ? $cart->cart_contents : array();
        }
        /*
        * Check room availability
        */
        protected function check_availability( $list, $current_details ) { // return array();
            $return_value = array();
            if ( \LoftOcean\is_valid_array( $list ) && \LoftOcean\is_valid_array( $current_details ) ) {
                foreach ( $list as $timestamp => $data ) {
                    if ( isset( $current_details[ $timestamp ], $current_details[ $timestamp ][ 'available_number' ] ) && is_numeric( $current_details[ $timestamp ][ 'available_number' ] ) && ( $current_details[ $timestamp ][ 'available_number' ] < $data[ 'number' ] ) ) {
                        // translators: 1/5: html tag span 2: date 3: available number 4: request number
                        $return_value[] = '<li class="unavailable-date-item">' . sprintf( 
                            __( '%1$s: %2$s available, but %3$s requested', 'loftocean' ), 
                            '<strong>' . $data[ 'date' ] . '</strong>', 
                            $current_details[ $timestamp ][ 'available_number' ], 
                            $data[ 'number' ]
                        ) . '</li>';
                    }
                }
            }
            return $return_value;
        }
    }
    new Cart_Item_Check();
}