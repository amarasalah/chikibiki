<?php
namespace LoftOcean\Room\Settings;

if ( ! class_exists( '\LoftOcean\Room\Settings\Flexbile_Price_Rules' ) ) {
    class Flexbile_Price_Rules {
        /**
        * Booking rule taxonomy
        */
        protected $rule_taxonomy = 'lo_room_flexible_rules';
        /**
        * Construct function
        */
        public function __construct() {
            add_action( 'loftocean_room_the_settings_tabs', array( $this, 'get_room_setting_tabs' ) );
            add_action( 'loftocean_room_the_settings_panel', array( $this, 'the_room_setting_panel' ) );
			add_action( 'loftocean_save_room_settings', array( $this, 'save_room_settings' ), 999 );
        }
        /**
        * Tab titles
        */
        public function get_room_setting_tabs( $pid ) { ?>
            <li class="loftocean-room room-options tab-booking-rules">
                <a href="#tab-flexible-price-rules"><span><?php esc_html_e( 'Flexible Price Rules', 'loftocean' ); ?></span></a>
            </li><?php
        }
        /**
        * Tab panel
        */
        public function the_room_setting_panel( $pid ) {
            $weekdays = array(
                'day1' => esc_html__( 'Mondays', 'loftocean' ),
                'day2' => esc_html__( 'Tuesdays', 'loftocean' ),
                'day3' => esc_html__( 'Wednesdays', 'loftocean' ),
                'day4' => esc_html__( 'Thursdays', 'loftocean' ),
                'day5' => esc_html__( 'Fridays', 'loftocean' ),
                'day6' => esc_html__( 'Saturdays', 'loftocean' ),
                'day0' => esc_html__( 'Sundays', 'loftocean' )
            );
            $current_rules = $this->get_current_room_rules( $pid ); ?>

            <div id="tab-flexible-price-rules-panel" class="panel loftocean-room-setting-panel loftocean-room-rules-panel hidden">
                <div class="options-group"><?php
                if ( \LoftOcean\is_valid_array( $current_rules ) ) : ?>
                    <p><?php esc_html_e( 'The flexible price rules applied to this room are as follows:', 'loftocean' ); ?></p><?php
                    foreach ( $current_rules as $fpr ) :
                        $details = \LoftOcean\merge_array( array(
                            'time_range' => '', 
                            'long_stay_discount' => array( 'enable' => '' ), 
                            'custom_long_stay_discount'=> array( 'enable' => '', 'items' => array() ),
                            'early_bird_discount' => array( 'enable' => '' ), 
                            'last_minute_discount' => array( 'enable' => '' )
                        ), get_term_meta( $fpr, 'rule_details', true ) ); ?>

                        <div class="loftocean-room-rules-item">
                            <div class="item-title"><?php
                                echo esc_html( $details[ 'title' ] );
                                if ( 'custom' == $details[ 'time_range' ] ) {
                                    printf(
                                        // translators: 1/2: date string
                                        esc_html__( ' (%1$s to %2$s)', 'loftocean' ),
                                        $details[ 'start_date' ],
                                        $details[ 'end_date' ]
                                    );
                                } ?>
                            </div>
                            <div class="item-details">
                                <ul><?php
                                    if ( ! empty( $details[ 'special_price' ][ 'amount' ] ) ) : ?>
                                        <li class="rule-single-item">
                                            <div class="rule-single-item-title"><?php esc_html_e( 'Sepcial Price', 'loftocean' ); ?></div>
                                            <div class="rule-single-item-content"><?php ( '-' == $details[ 'special_price' ][ 'operator' ] ) ? printf(
                                                // translators: 1: percentage 2: percentage symbol
                                                esc_html__( 'Decrease by %1$s%2$s', 'loftocean' ),
                                                $details[ 'special_price' ][ 'amount' ],
                                                esc_html__( '%', 'loftocean' )
                                            ) : printf(
                                                // translators: 1: percentage 2: percentage symbol
                                                esc_html__( 'Increase by %1$s%2$s', 'loftocean' ),
                                                $details[ 'special_price' ][ 'amount' ],
                                                esc_html__( '%', 'loftocean' )
                                            ); ?></div>
                                        </li><?php
                                    endif;
                                    if ( 'on' == $details[ 'long_stay_discount' ][ 'enable' ] ) : ?>
                                        <li class="rule-single-item">
                                            <div class="rule-single-item-title"><?php esc_html_e( 'Long-Stay Discounts', 'loftocean' ); ?></div>
                                            <div class="rule-single-item-content">
                                                <ul><?php
                                                $long_stay_unit = array( 'weekly' => esc_html__( 'Weekly Discount', 'loftocean' ), 'monthly' => esc_html__( 'Monthly Discount', 'loftocean' ) );
                                                foreach ( $long_stay_unit as $lsu => $label ) :
                                                    if ( ! empty( $details[ 'long_stay_discount' ][ $lsu ] ) ) : ?>
                                                        <li><?php echo $label; ?>: <?php printf(
                                                            // translators: 1: percentage 2: percentage symbol
                                                            esc_html( '%1$s%2$s off', 'loftocean' ),
                                                            $details[ 'long_stay_discount' ][ $lsu ],
                                                            esc_html__( '%', 'loftocean' )
                                                        ); ?></li><?php
                                                    endif;
                                                endforeach;
                                                if ( isset( $details[ 'custom_long_stay_discount' ] ) && \LoftOcean\is_valid_array( $details[ 'custom_long_stay_discount' ][ 'items' ] ) && ( 'on' == $details[ 'custom_long_stay_discount' ][ 'enable' ] ) ) :
                                                    foreach ( $details[ 'custom_long_stay_discount' ][ 'items' ] as $clsd ) :
                                                        if ( ( ! empty( $clsd[ 'days' ] ) ) && ( ! empty( $clsd[ 'off' ] ) ) ) : ?>
                                                            <li><?php printf(
                                                                // translators: 1: percentage 2: percentage symbol
                                                                esc_html( '%1$s days or more: %2$s off', 'loftocean' ),
                                                                $clsd[ 'days' ],
                                                                $clsd[ 'off' ] . '%'
                                                            ); ?></li><?php
                                                        endif;
                                                    endforeach;
                                                endif; ?>
                                                </ul>
                                            </div>
                                        </li><?php
                                    endif;
                                    if ( 'on' == $details[ 'early_bird_discount' ][ 'enable' ] ) : ?>
                                        <li class="rule-single-item">
                                            <div class="rule-single-item-title"><?php esc_html_e( 'Early Bird Discount', 'loftocean' ); ?></div>
                                            <div class="rule-single-item-content"><?php printf(
                                                // translators: 1: percentage 2: percentage symbol
                                                esc_html__( '%1$s days before arrival: %2$s%3$s off', 'loftocean' ),
                                                $details[ 'early_bird_discount' ][ 'days_before' ],
                                                $details[ 'early_bird_discount' ][ 'discount' ],
                                                esc_html__( '%', 'loftocean' )
                                            ); ?></div>
                                        </li><?php
                                    endif;
                                    if ( 'on' == $details[ 'last_minute_discount' ][ 'enable' ] ) : ?>
                                        <li class="rule-single-item">
                                            <div class="rule-single-item-title"><?php esc_html_e( 'Last-Minute Discount', 'loftocean' ); ?></div>
                                            <div class="rule-single-item-content"><?php printf(
                                                // translators: 1: percentage 2: percentage symbol
                                                esc_html__( '%1$s days before arrival: %2$s%3$s off', 'loftocean' ),
                                                $details[ 'last_minute_discount' ][ 'days_before' ],
                                                $details[ 'last_minute_discount' ][ 'discount' ],
                                                esc_html__( '%', 'loftocean' )
                                            ); ?></div>
                                        </li><?php
                                    endif; ?>
                                </ul>
                            </div>
                        </div><?php
                    endforeach;
                else : ?>
                    <p><?php esc_html_e( 'No flexible price rules found for this room yet.', 'loftocean' ); ?></p><?php
                endif; ?>
                </div>
            </div><?php
        }
		/**
		* Save room settings
		*/
		public function save_room_settings( $room_id ) {
            $rules = $this->get_current_room_rules( $room_id );
            if ( \LoftOcean\is_valid_array( $rules ) ) {
                wp_set_post_terms( $room_id, $rules, $this->rule_taxonomy );
            } else {
                wp_set_post_terms( $room_id, array(), $this->rule_taxonomy );
            }
        }
        /**
        * Get booking rules for current room
        */
        protected function get_current_room_rules( $room_id ) {
            $rules = array();
            $all_rules = get_terms( array(  'taxonomy' => $this->rule_taxonomy, 'hide_empty' => true, 'fields' => 'ids', 'orderby' => 'meta_value_num', 'order' => 'ASC', 'meta_key' => 'priority' ) );
            foreach ( $all_rules as $ari ) {
                $apply_to = get_term_meta( $ari, 'apply_to', true );
                switch ( $apply_to ) {
                    case 'all':
                        array_push( $rules, $ari );
                        break;
                    case 'room_types':
                        $selected_types = get_term_meta( $ari, 'apply_to_room_types', true );
                        if ( \LoftOcean\is_valid_array( $selected_types ) ) {
                            $current_room_types = wp_get_post_terms( $room_id, 'lo_room_type', array( 'fields' => 'ids' ) );
                            if ( \LoftOcean\is_valid_array( $current_room_types ) ) {
                                foreach ( $selected_types as $st ) {
                                    if ( in_array( $st, $current_room_types ) ) {
                                        array_push( $rules, $ari );
                                    }
                                }
                            }
                        }
                        break;
                    case 'rooms':
                        $selected_rooms = get_term_meta( $ari, 'apply_to_rooms', true );
                        if ( \LoftOcean\is_valid_array( $selected_rooms ) && in_array( $room_id, $selected_rooms ) ) {
                            array_push( $rules, $ari );
                        }
                        break;
                }
            }
            return $rules;
        }
    }
    new Flexbile_Price_Rules();
}
