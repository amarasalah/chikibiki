<?php
namespace LoftOcean\Room;

if ( ! class_exists( '\LoftOcean\Room\Price' ) ) {
    class Price {
        /**
        * Room post type
        */
        protected $room_post_type = 'loftocean_room';
        /*
        * Construction function 
        */
        public function __construct() {
            add_filter( 'loftocean_get_room_prices', array( $this, 'get_room_prices' ), 10, 2 );
            add_filter( 'loftocean_get_room_variable_prices', array( $this, 'get_room_variable_prices' ), 10, 2 );
            add_filter( 'loftocean_get_room_current_prices', array( $this, 'check_room_current_prices' ), 10, 4 );
        }


        /*
        * Get prices by given room ID
        */
        public function get_room_prices( $prices, $roomID ) {
            
            return $prices;
        }
        /*
        * Get variable prices by given room ID
        */
        public function get_room_variable_prices( $prices, $roomID ) {
            $is_variable_prices_enabled = get_post_meta( $roomID, 'loftocean_room_enable_variable_prices', true ); 
            if ( 'on' == $is_variable_prices_enabled ) {
                $is_price_per_person_mode = ( 'on' == get_post_meta( $roomID, 'loftocean_room_price_by_people', true ) );
                $is_group_guest_mode = ( 'on' == get_post_meta( $roomID, 'loftocean_room_enable_variable_guest_group', true ) );
                $is_weekend_price_mode = ( 'on' == get_post_meta( $roomID, 'loftocean_room_enable_variable_weekend_prices', true ) );
                $final_variable_prices = array();
                
                $prices[ 'enable' ] = true; 
                $prices[ 'mode' ] = $is_price_per_person_mode ? 'per_person' : 'nightly';
                $prices[ 'guestMode' ] = $is_group_guest_mode ? 'group' : 'simple';
                $prices[ 'enableWeekendPrice' ] = $is_weekend_price_mode;

                if ( $is_price_per_person_mode ) {
                    $variable_prices = get_post_meta( $roomID, 'loftocean_room_variable_per_person_prices', true );
                    if ( \LoftOcean\is_valid_array( $variable_prices ) ) {
                        foreach ( $variable_prices as $vp ) {
                            if ( $is_group_guest_mode ) {
                                $current_variable_prices = array();
                                $current_index = 'adult' . $vp[ 'adult_number' ] . '-child' . $vp[ 'child_number' ];
                                if ( is_numeric( $vp[ 'adult_price' ] ) || is_numeric( $vp[ 'child_price' ] ) ) {
                                    $current_variable_prices[ 'adult_price' ] = is_numeric( $vp[ 'adult_price' ] ) ? $vp[ 'adult_price' ] : -1;
                                    $current_variable_prices[ 'child_price' ] = is_numeric( $vp[ 'child_price' ] ) ? $vp[ 'child_price' ] : -1;
                                }
                                if ( $is_weekend_price_mode && ( is_numeric( $vp[ 'weekend_adult_price' ] ) || is_numeric( $vp[ 'weekend_child_price' ] ) ) ) {
                                    $current_variable_prices[ 'weekend_adult_price' ] = is_numeric( $vp[ 'weekend_adult_price' ] ) ? $vp[ 'weekend_adult_price' ] : -1;
                                    $current_variable_prices[ 'weekend_child_price' ] = is_numeric( $vp[ 'weekend_child_price' ] ) ? $vp[ 'weekend_child_price' ] : -1;
                                }
                                if ( \LoftOcean\is_valid_array( $current_variable_prices ) && ( ! isset( $final_variable_prices[ $current_index ] ) ) ) {
                                    $final_variable_prices[ $current_index ] = array_merge(
                                        array( 'adult_price' => -1, 'child_price' => -1, 'weekend_adult_price' => -1, 'weekend_child_price' => -1 ),
                                        $current_variable_prices
                                    );
                                }
                            } else {
                                $current_variable_prices = array();
                                $current_index = 'guests' . $vp[ 'guest_number' ];
                                if ( is_numeric( $vp[ 'price' ] ) ) {
                                    $current_variable_prices[ 'price' ] = $vp[ 'price' ];
                                }
                                if ( $is_weekend_price_mode && ( is_numeric( $vp[ 'weekend_price' ] ) ) ) {
                                    $current_variable_prices[ 'weekend_price' ] = $vp[ 'weekend_price' ];
                                }
                                if ( \LoftOcean\is_valid_array( $current_variable_prices ) && ( ! isset( $final_variable_prices[ $current_index ] ) ) ) {
                                    $final_variable_prices[ $current_index ] = array_merge( array( 'price' => -1, 'weekend_price' => -1 ), $current_variable_prices );
                                }
                            }
                        }
                    }
                } else {
                    $variable_prices = get_post_meta( $roomID, 'loftocean_room_variable_nightly_prices', true );
                    if ( \LoftOcean\is_valid_array( $variable_prices ) ) {
                        foreach ( $variable_prices as $vp ) {
                            $current_variable_prices = array();
                            $current_index = $is_group_guest_mode ? 'adult' . $vp[ 'adult_number' ] . '-child' . $vp[ 'child_number' ] : 'guests' . $vp[ 'guest_number' ];
                            if ( is_numeric( $vp[ 'price' ] ) ) {
                                $current_variable_prices[ 'price' ] = $vp[ 'price' ];
                            }
                            if ( $is_weekend_price_mode && is_numeric( $vp[ 'weekend_price' ] ) ) {
                                $current_variable_prices[ 'weekend_price' ] = $vp[ 'weekend_price' ];
                            }

                            if ( \LoftOcean\is_valid_array( $current_variable_prices ) && ( ! isset( $final_variable_prices[ $current_index ] ) ) ) {
                                $final_variable_prices[ $current_index ] = array_merge(
                                    array( 'price' => -1, 'weekend_price' => -1 ),
                                    $current_variable_prices
                                );
                            }
                        }
                    }
                }

                $prices[ 'prices' ] = $final_variable_prices;
            }
            return $prices;
        }
        /**
        * Get room current prices if variable prices enabled
        */
        public function check_room_current_prices( $item, $variable_price_settings, $adult_number, $child_number ) {
            $vaiable_prices = $variable_price_settings[ 'prices' ];
            $is_group_guest_mode = ( 'group' == $variable_price_settings[ 'guestMode' ] );
            $property_names =  $is_group_guest_mode
                ? array( sprintf( 'adult%1$s-child%2$s', $adult_number, $child_number ), sprintf( 'adult%s-child', $adult_number ), sprintf( 'adult-child%s', $child_number ), 'adult-child' )
                    : array( sprintf( 'guests%s', ( $adult_number + $child_number ) ), 'guests' );

            foreach( $property_names as $property_name ) {
                if ( isset( $vaiable_prices[ $property_name ] ) ) {
                    $prices = $vaiable_prices[ $property_name ]; 
                    if ( 'per_person' == $variable_price_settings[ 'mode' ] ) {
                        if ( $is_group_guest_mode ) {
                            foreach( array( 'adult_price', 'child_price' ) as $price_property ) {
                                $weekend_price_property = 'weekend_' . $price_property;
                                if ( ( 'yes' == $item[ 'is_weekend' ] ) && is_numeric( $prices[ $weekend_price_property ] ) && ( $prices[ $weekend_price_property ] > -1 ) ) {
                                    $item[ $price_property ] = $prices[ $weekend_price_property ];
                                } else if ( is_numeric( $prices[ $price_property ] ) && ( $prices[ $price_property ] > -1 ) ) {
                                    $item[ $price_property ] = $prices[ $price_property ];
                                }
                            };
                        } else {
                            $price_property = 'price'; 
                            $weekend_price_property = 'weekend_price';
                            if ( ( 'yes' == $item[ 'is_weekend' ] ) && is_numeric( $prices[ $weekend_price_property ] ) && ( $prices[ $weekend_price_property ] > -1 ) ) {
                                $item[ 'adult_price' ] = $prices[ $weekend_price_property ];
                                $item[ 'child_price' ] = $prices[ $weekend_price_property ];
                            } else if ( is_numeric( $prices[ $price_property ] ) && ( $prices[ $price_property ] > -1 ) ) {
                                $item[ 'adult_price' ] = $prices[ $price_property ];
                                $item[ 'child_price' ] = $prices[ $price_property ];
                            }
                        }
                    } else {
                        $price_property = 'price'; 
                        $weekend_price_property = 'weekend_price';
                        if ( ( 'yes' == $item[ 'is_weekend' ] ) && is_numeric( $prices[ $weekend_price_property ] ) && ( $prices[ $weekend_price_property ] > -1 ) ) {
                            $item[ 'price' ] = $prices[ $weekend_price_property ];
                        } else if ( is_numeric( $prices[ $price_property ] ) && ( $prices[ $price_property ] > -1 ) ) {
                            $item[ 'price' ] = $prices[ $price_property ];
                        }
                    }
                    break;
                }
            }; 

            return $item;
        }
    }
    new Price();
}