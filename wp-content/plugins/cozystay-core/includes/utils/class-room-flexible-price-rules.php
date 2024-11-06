<?php
namespace LoftOcean\Utils\Room;
if ( ! class_exists( '\LoftOcean\Utils\Room\Flexible_Price_Rules' ) ) {
    class Flexible_Price_Rules {
        /**
        * Discount details
        */
        protected $discount_details = array();
        /**
        * Current total discount
        */
        protected $total_discount = 1;
        /**
        * String Post type
        */
        protected $post_type = 'loftocean_room';
        /**
        * Booking Rules Taxomony
        */
        protected $rule_taxonomy = 'lo_room_flexible_rules';
        /**
        * Construction function
        */
        public function __construct() {
            add_action( 'wp_ajax_get_room_discount', array( $this, 'get_room_discount' ) );
            add_action( 'wp_ajax_nopriv_get_room_discount', array( $this, 'get_room_discount' ) );
            add_action( 'loftocean_room_check_single_flexible_price_rules', array( $this, 'check_single_room_rules' ), 10, 1 );
            add_filter( 'loftocean_room_get_flexible_price_rate', array( $this, 'get_room_flexible_price_rate' ), 10, 2 );
            add_filter( 'loftocean_room_get_special_prices', array( $this, 'get_room_special_prices' ), 10, 2 );
        }
        /**
        * Ajax callback function for action
        */
        public function get_room_discount() {
            if ( isset( $_REQUEST[ 'action' ] ) && ( 'get_room_discount' == wp_unslash( $_REQUEST[ 'action' ] ) ) ) {
                $response = array( 'status' => 0, 'message' => '', 'discount' => false );
                if ( isset( $_REQUEST[ 'roomID' ], $_REQUEST[ 'checkin' ], $_REQUEST[ 'checkout' ] ) && ( $this->post_type == get_post_type( $_REQUEST[ 'roomID' ] ) ) ) {
                    do_action( 'loftocean_room_check_single_flexible_price_rules', array( 'room_id' => $_REQUEST[ 'roomID' ], 'checkin' => $_REQUEST[ 'checkin' ], 'checkout' => $_REQUEST[ 'checkout' ] ) );
                    if ( \LoftOcean\is_valid_array( $this->discount_details ) ) {
                        $response[ 'discount' ] = array( 'discount' => $this->discount_details, 'totleDiscount' => $this->total_discount );
                        $response[ 'status' ] = 1;
                        echo json_encode( $response );
                        wp_die();
                    }
                }
                echo json_encode( $response );
                wp_die();
            }
        }
        /**
        * Get single room price rate
        */
        public function get_room_flexible_price_rate( $discount, $data ) {
            if ( isset( $data[ 'room_id' ], $data[ 'checkin' ], $data[ 'checkout' ] ) && ( $this->post_type == get_post_type( $data[ 'room_id' ] ) ) ) {
                do_action( 'loftocean_room_check_single_flexible_price_rules', $data );
                if ( \LoftOcean\is_valid_array( $this->discount_details ) ) {
                    return array( 'discount' => $this->discount_details, 'totleDiscount' => $this->total_discount );
                }
            }
            return $discount;
        }
        /**
        * Get special price rules
        */
        public function get_room_special_prices( $prices, $room_id ) {
            $rates = array();
            if ( isset( $room_id ) && ( $this->post_type == get_post_type( $room_id ) ) ) {
                $rules = apply_filters( 'loftocean_get_room_current_flexible_rules', array(), $room_id );
                if ( \LoftOcean\is_valid_array( $rules ) ) {
                    foreach ( $rules as $rule_id ) {
                        $start_date = get_term_meta( $rule_id, 'start_date', true );
                        $end_date = get_term_meta( $rule_id, 'end_date', true );
                        $custom_time_range = ( 'custom' == get_term_meta( $rule_id, 'time_range', true ) );
                        if ( $custom_time_range && ( $start_date > $end_date ) ) continue;

                        $index = $custom_time_range ? $start_date . '-' . $end_date : 'all';
                        if ( ! isset( $rates[ $index ] ) ) {
                            $details = \LoftOcean\merge_array( array(
                                'special_price' => array( 'operator' => '-', 'amount' => '' )
                            ), get_term_meta( $rule_id, 'rule_details', true ) );
                            $rate = ( ( '-' == $details[ 'special_price' ][ 'operator' ] ) ? ( 100 - $details[ 'special_price' ][ 'amount' ] ) : ( 100 + $details[ 'special_price' ][ 'amount' ] ) ) / 100;
                            $rates[ $index ] = array( 'id' => $index, 'start' => $start_date, 'end' => $end_date, 'rate' => $rate );
                        }
                    }
                    $rates = array_values( $rates );
                }
            }
            return $rates;
        }
        /**
        * Check booking rules for single room page
        */
        public function check_single_room_rules( $data ) {
            $this->discount_details = array();
            $this->total_discount = 1;
            if ( \LoftOcean\is_valid_array( $data ) && isset( $data[ 'room_id' ], $data[ 'checkin' ], $data[ 'checkout' ] ) && ( $this->post_type == get_post_type( $data[ 'room_id' ] ) ) ) {
                $rules = apply_filters( 'loftocean_get_room_current_flexible_rules', array(), $data[ 'room_id' ] );
                if ( \LoftOcean\is_valid_array( $rules ) ) {
                    $default_rule_id = false;
                    $detailed_rule_id = false;
                    foreach ( $rules as $rule_id ) {
                        if ( '' == get_term_meta( $rule_id, 'time_range', true ) ) {
                            if ( false === $default_rule_id ) {
                                $default_rule_id = $rule_id;
                            }
                        } else {
                            $start_date = get_term_meta( $rule_id, 'start_date', true );
                            $end_date = get_term_meta( $rule_id, 'end_date', true );
                            if ( empty( $start_date ) || empty( $end_date ) ) continue;
                            if ( ( $start_date <= $data[ 'checkin' ] ) && ( $data[ 'checkin' ] <= $end_date ) ) {
                                $detailed_rule_id = $rule_id;
                                break;
                            }
                        }
                    }
                    $current_rule_id = ( false === $detailed_rule_id ) ? ( false === $default_rule_id ? false : $default_rule_id ) : $detailed_rule_id;
                    if ( false !== $current_rule_id ) {
                        $this->check_rule( $current_rule_id, array(
                            'checkin_stamp' => $data[ 'checkin' ],
                            'checkout_stamp' => $data[ 'checkout' ],
                            'days' => ( $data[ 'checkout' ] - $data[ 'checkin' ] ) / LOFTICEAN_SECONDS_IN_DAY,
                            'days_in_advance' => ( $data[ 'checkin' ] - strtotime( date( 'Y-m-d' ) ) ) / LOFTICEAN_SECONDS_IN_DAY
                        ) );
                    }
                }
            }
        }
        /**
        * Check rule
        */
        protected function check_rule( $rule_id, $data ) {
            $rule_details = \LoftOcean\merge_array( array(
                'special_price' => array( 'amount' => '' ),
                'long_stay_discount' => array( 'enable' => '' ),
                'custom_long_stay_discount' => array( 'enable' => '' ),
                'early_bird_discount' => array( 'enable' => '' ),
                'last_minute_discount' => array( 'enable' => '' )
            ), get_term_meta( $rule_id, 'rule_details', true ) );

            // Long stay discount
            if ( 'on' == $rule_details[ 'long_stay_discount' ][ 'enable' ] ) {
                $long_stay_discount = array();
                if ( ( ! empty( $rule_details[ 'long_stay_discount' ][ 'monthly' ] ) ) && is_numeric( $rule_details[ 'long_stay_discount' ][ 'monthly' ] ) ) {
                    $long_stay_discount[ 'days28' ] = array( 'days' => 28, 'off' => trim( $rule_details[ 'long_stay_discount' ][ 'monthly' ] ) );
                }
                if ( ( ! empty( $rule_details[ 'long_stay_discount' ][ 'weekly' ] ) ) && is_numeric( $rule_details[ 'long_stay_discount' ][ 'weekly' ] ) ) {
                    $long_stay_discount[ 'days7' ] = array( 'days' => 7, 'off' => trim( $rule_details[ 'long_stay_discount' ][ 'weekly' ] ) );
                }
                if ( ( 'on' == $rule_details[ 'custom_long_stay_discount' ][ 'enable' ] ) && \LoftOcean\is_valid_array( $rule_details[ 'custom_long_stay_discount' ][ 'items' ] ) ) {
                    foreach ( $rule_details[ 'custom_long_stay_discount' ][ 'items' ] as $clsd_item ) {
                        if ( ( ! empty( $clsd_item[ 'days' ] ) ) && ( ! empty( $clsd_item[ 'off' ] ) ) && is_numeric( $clsd_item[ 'days' ] ) && is_numeric( $clsd_item[ 'off' ] ) ) {
                            $long_stay_discount[ 'days' . $clsd_item[ 'days' ] ] = array( 'days' => trim( $clsd_item[ 'days' ] ), 'off' => trim( $clsd_item[ 'off' ] ) );
                        }
                    }
                }
                if ( \LoftOcean\is_valid_array( $long_stay_discount ) ) {
                    arsort( $long_stay_discount );
                    foreach ( $long_stay_discount as $lsdi ) {
                        if ( $data[ 'days' ] >= intval( $lsdi[ 'days' ] ) ) {
                            $this->discount_details[ 'long_stay_discount' ] = array( 'label' => esc_html__( 'Long Stay Discount', 'loftocean' ), 'discount' => $lsdi[ 'off' ] / 100 );
                            break;
                        }
                    }
                }
            }
            // Early bird discount
            if ( ( 'on' == $rule_details[ 'early_bird_discount' ][ 'enable' ] )
                && is_numeric( $rule_details[ 'early_bird_discount' ][ 'days_before'] )
                && ( $rule_details[ 'early_bird_discount' ][ 'days_before'] >= 0 )
                && ( $data[ 'days_in_advance' ] >= absint( $rule_details[ 'early_bird_discount' ][ 'days_before'] ) ) ) {

                $this->discount_details[ 'early_bird_discount' ] = array( 'label' => esc_html__( 'Early Bird Discount', 'loftocean' ), 'discount' => absint( $rule_details[ 'early_bird_discount' ][ 'discount' ] ) / 100 );
            }
            //Last minute discount
            if ( ( 'on' == $rule_details[ 'last_minute_discount' ][ 'enable' ] )
                && is_numeric( $rule_details[ 'last_minute_discount' ][ 'days_before'] )
                && ( $rule_details[ 'last_minute_discount' ][ 'days_before'] >= 0 )
                && ( $data[ 'days_in_advance' ] <= absint( $rule_details[ 'last_minute_discount' ][ 'days_before'] ) ) ) {

                $this->discount_details[ 'last_minute_discount' ] = array( 'label' => esc_html__( 'Last Minute Discount', 'loftocean' ), 'discount' => absint( $rule_details[ 'last_minute_discount' ][ 'discount' ] ) / 100 );
            }

            if ( \LoftOcean\is_valid_array( $this->discount_details ) ) {
                $base_percentage = empty( $rule_details[ 'special_price' ][ 'amount' ] ) ? 0 : $rule_details[ 'special_price' ][ 'operator' ] . absint( $rule_details[ 'special_price' ][ 'amount' ] ) / 100;
                $this->discount_details = array(
                    'base_percentage' => 1 + $base_percentage,
                    'details' => $this->discount_details
                );
                $total_discount = 0;
                foreach ( $this->discount_details[ 'details' ] as $discount ) {
                    $total_discount += $discount[ 'discount' ];
                }
                $this->total_discount = ( 1 - $total_discount );
            } 
            return true;
        }
    }
    new \LoftOcean\Utils\Room\Flexible_Price_Rules();
}
