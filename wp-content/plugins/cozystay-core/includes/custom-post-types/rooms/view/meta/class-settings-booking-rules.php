<?php
namespace LoftOcean\Room\Settings;

if ( ! class_exists( '\LoftOcean\Room\Settings\Booking_Rules' ) ) {
    class Booking_Rules {
        /**
        * Booking rule taxonomy
        */
        protected $rule_taxonomy = 'lo_room_booking_rules';
        /**
        * Construct function
        */
        public function __construct() {
            add_action( 'loftocean_room_the_settings_tabs', array( $this, 'get_room_setting_tabs' ) );
            add_action( 'loftocean_room_the_settings_panel', array( $this, 'the_room_setting_panel' ) );
			add_action( 'loftocean_save_room_settings', array( $this, 'save_room_settings' ) );
        }
        /**
        * Tab titles
        */
        public function get_room_setting_tabs( $pid ) { ?>
            <li class="loftocean-room room-options tab-booking-rules">
                <a href="#tab-booking-rules"><span><?php esc_html_e( 'Booking Rules', 'loftocean' ); ?></span></a>
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

            <div id="tab-booking-rules-panel" class="panel loftocean-room-setting-panel loftocean-room-rules-panel hidden">
                <div class="options-group"><?php
                if ( \LoftOcean\is_valid_array( $current_rules ) ) : ?>
                    <p><?php esc_html_e( 'The booking rules applied to this room are as follows:', 'loftocean' ); ?></p><?php
                    foreach ( $current_rules as $br ) :
                        $details = \LoftOcean\merge_array( array(
                            'time_range' => '',
                            'stay_length' => array( 'general' => array( 'enable' => '' ), 'custom' => array( 'enable' => '' ) ),
                            'no_checkin_checkout_date' => array(
                                'enable' => '',
                                'checkin' => array( 'day0' => '', 'day1' => '', 'day2' => '', 'day3' => '', 'day4' => '', 'day5' => '', 'day6' => '' ),
                                'checkout' => array( 'day0' => '', 'day1' => '', 'day2' => '', 'day3' => '', 'day4' => '', 'day5' => '', 'day6' => '' )
                            ),
                            'in_advance' => array( 'enable' => '' ),
                        ), get_term_meta( $br, 'rule_details', true ) ); ?>

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
                                    if ( 'on' == $details[ 'stay_length' ][ 'general' ][ 'enable' ] ) :
                                        $content = array();
                                        if ( ! empty( $details[ 'stay_length' ][ 'general' ][ 'min' ] ) ) {
                                            array_push( $content, sprintf(
                                                // translators: day count
                                                esc_html__( 'Min Stay %s Nights', 'loftocean' ),
                                                $details[ 'stay_length' ][ 'general' ][ 'min' ]
                                            ) );
                                        }
                                        if ( ! empty( $details[ 'stay_length' ][ 'general' ][ 'max' ] ) ) {
                                            array_push( $content, sprintf(
                                                // translators: day count
                                                esc_html__( 'Max Stay %s Nights', 'loftocean' ),
                                                $details[ 'stay_length' ][ 'general' ][ 'max' ]
                                            ) );
                                        }
                                        if ( \LoftOcean\is_valid_array( $content ) ) : ?>
                                            <li class="rule-single-item">
                                                <div class="rule-single-item-title"><?php esc_html_e( 'Stay Length', 'loftocean' ); ?></div>
                                                <div class="rule-single-item-content"><?php echo implode( ', ', $content ); ?></div>
                                            </li><?php
                                        endif;
                                    endif;
                                    if ( 'on' == $details[ 'stay_length' ][ 'custom' ][ 'enable' ] ) : ?>
                                        <li class="rule-single-item">
                                            <div class="rule-single-item-title"><?php esc_html_e( 'Stay Length by Check-in Day', 'loftocean' ); ?></div>
                                            <div class="rule-single-item-content">
                                                <ul><?php
                                                foreach ( $weekdays as $index => $label ) :
                                                    $content = array();
                                                    if ( ! empty( $details[ 'stay_length' ][ 'custom' ][ $index ][ 'min' ] ) ) {
                                                        array_push( $content, sprintf(
                                                            // translators: day count
                                                            esc_html__( 'Min Stay %s Nights', 'loftocean' ),
                                                            $details[ 'stay_length' ][ 'custom' ][ $index ][ 'min' ]
                                                        ) );
                                                    }
                                                    if ( ! empty( $details[ 'stay_length' ][ 'custom' ][ $index ][ 'max' ] ) ) {
                                                        array_push( $content, sprintf(
                                                            // translators: day count
                                                            esc_html__( 'Max Stay %s Nights', 'loftocean' ),
                                                            $details[ 'stay_length' ][ 'custom' ][ $index ][ 'max' ]
                                                        ) );
                                                    }
                                                    if ( \LoftOcean\is_valid_array( $content ) ) : ?>
                                                        <li><?php echo $label; ?>: <?php echo implode( ', ', $content ); ?></li><?php
                                                    endif;
                                                endforeach; ?>
                                                </ul>
                                            </div>
                                        </li><?php
                                    endif;
                                    if ( 'on' == $details[ 'no_checkin_checkout_date' ][ 'enable' ] ) :
                                        $checkins = array();
                                        $checkouts = array();
                                        foreach ( $weekdays as $index => $label ) {
                                            ( 'on' == $details[ 'no_checkin_checkout_date' ][ 'checkin' ] [ $index ] ) ? array_push( $checkins, $label ) : '';
                                            ( 'on' == $details[ 'no_checkin_checkout_date' ][ 'checkout' ] [ $index ] ) ? array_push( $checkouts, $label ) : '';
                                        } ?>
                                        <li class="rule-single-item">
                                            <div class="rule-single-item-title"><?php esc_html_e( 'No Check-in Days', 'loftocean' ); ?></div>
                                            <div class="rule-single-item-content"><?php echo implode( ' ,', $checkins ); ?></div>
                                        </li>
                                        <li class="rule-single-item">
                                            <div class="rule-single-item-title"><?php esc_html_e( 'No Check-out Days', 'loftocean' ); ?></div>
                                            <div class="rule-single-item-content"><?php echo implode( ', ', $checkouts ); ?></div>
                                        </li><?php
                                    endif;
                                    if ( 'on' == $details[ 'in_advance' ][ 'enable' ] ) : ?>
                                        <li class="rule-single-item">
                                            <div class="rule-single-item-title"><?php esc_html_e( 'How far in advance can guests book?', 'loftocean' ); ?></div>
                                            <div class="rule-single-item-content">
                                                <ul><?php
                                                if ( ! empty( $details[ 'in_advance' ][ 'min' ] ) ) : ?>
                                                    <li><?php printf(
                                                        // translators: day count
                                                        esc_html__( 'Min Advance Reservation: %s days', 'loftocean' ),
                                                        absint( $details[ 'in_advance' ][ 'min' ] )
                                                    ); ?></li><?php
                                                endif;
                                                if ( ! empty( $details[ 'in_advance' ][ 'max' ] ) ) :  ?>
                                                    <li><?php printf(
                                                        // translators: day count
                                                        esc_html__( 'Max Advance Reservation: %s days', 'loftocean' ),
                                                        ( $details[ 'in_advance' ][ 'max' ] )
                                                    ); ?></li><?php
                                                endif; ?>
                                                </ul>
                                            </div>
                                        </li><?php
                                    endif; ?>
                                </ul>
                            </div>
                        </div><?php
                    endforeach;
                else : ?>
                    <p><?php esc_html_e( 'No booking rules found for this room yet.', 'loftocean' ); ?></p><?php
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
    new Booking_Rules();
}
