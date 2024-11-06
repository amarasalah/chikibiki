<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Booking Rules', 'loftocean' ); ?></h1>
    <hr class="wp-header-end">
    <form id="loftocean-room-booking-rules-form" action="<?php echo esc_url( admin_url( 'edit.php?post_type=loftocean_room&page=loftocean_room_booking_rules' ) ); ?>" method="POST">
        <div class="loftocean-room-rules-wrapper">
            <a href="#" class="loftocean-room-rules-add" data-current-index="0" data-rule-type="booking"><?php esc_html_e( 'Add New', 'loftocean' ); ?></a>
        </div>
        <p class="submit loftocean-submit-button">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'loftocean' ); ?>" disabled>
            <span class="spinner" style="visibility: visible; float: none;"></span>
        </p>
        <input type="hidden" name="loftocean_room_rules_removed" value="" />
        <input type="hidden" name="loftocean_room_rules_settings_nonce" value="<?php echo esc_attr( wp_create_nonce( 'loftocean_room_booking_rules' ) ); ?>" />
    </form>
</div><?php
$weekdays = array(
    '1' => esc_html__( 'Mondays', 'loftocean' ),
    '2' => esc_html__( 'Tuesdays', 'loftocean' ),
    '3' => esc_html__( 'Wednesdays', 'loftocean' ),
    '4' => esc_html__( 'Thursdays', 'loftocean' ),
    '5' => esc_html__( 'Fridays', 'loftocean' ),
    '6' => esc_html__( 'Saturdays', 'loftocean' ),
    '0' => esc_html__( 'Sundays', 'loftocean' )
);
$room_types = get_terms( array( 'taxonomy' => 'lo_room_type', 'hide_empty' => false ) );
$rooms = new WP_Query( array( 'post_type' => 'loftocean_room', 'posts_per_page' => '-1', 'offset' => 0 ) );
$has_room_types = ( ! is_wp_error( $room_types ) ) && ( count( $room_types ) > 0 );
$has_rooms = ( ! is_wp_error( $rooms ) ) && $rooms->have_posts(); ?>
<script id="tmpl-loftocean-room-rule-item" type="text/html">
<# data.list.forEach( function( item ) {
    var itemIndex = data.index ++,
        namePrefix = 'loftocean_room_booking_rules[item' + itemIndex + ']',
        labelID = 'loftocean-room-booking-rule-item' + itemIndex ; #>
    <div class="loftocean-room-rules-item">
        <h3><?php esc_html_e( 'Booking Rule', 'loftocean' ); ?><span class="item-name"><span class="item-name"><# if ( item.title ) { #> - {{{ item.title }}}<# } #></span></h3>
        <a href="#" class="loftocean-room-rules-item-remove"><?php esc_html_e( 'Remove', 'loftocean' ); ?></a>
        <div class="loftocean-room-rules-controls-wrapper">
            <div class="controls-row">
                <div class="control-wrapper">
                    <label for="{{{ labelID }}}-title"><?php esc_html_e( 'Title', 'loftocean' ); ?><span class="required"> *</span></label>
                    <input name="{{{ namePrefix }}}[title]" id="{{{ labelID }}}-title" class="loftocean-room-rules-title fullwidth" type="text" value="{{{ item.title }}}" required>
                </div>
                <div class="control-wrapper">
                    <label><?php esc_html_e( 'Time Range', 'loftocean' ); ?></label>
                    <div class="multi-items-wrapper">
                        <select name="{{{ namePrefix }}}[time_range]" class="loftocean-time-range-select">
                            <option value=""<# if ( '' == item[ 'time_range' ] ) { #> selected<# } #>><?php esc_html_e( 'No restriction', 'loftocean' ); ?></option>
                            <option value="custom"<# if ( 'custom' == item[ 'time_range' ] ) { #> selected<# } #>><?php esc_html_e( 'Set custom time range', 'loftocean' ); ?></option>
                        </select>
                    </div>
                </div>
                <div class="control-wrapper loftocean-custom-date-range<# if ( '' == item[ 'time_range' ] ) { #> hide<# } #>">
                    <label><?php esc_html_e( 'Dates', 'loftocean' ); ?><# if ( 'custom' == item[ 'time_range' ] ) { #><span class="required"> *</span><# } #></label>
                    <div class="multi-items-wrapper">
                        <input name="{{{ namePrefix }}}[start_date]" class="fullwidth date-picker" type="text" value="{{{ item[ 'start_date' ] }}}"<# if ( 'custom' == item[ 'time_range' ] ) { #> required<# } #> autocomplete="off">
                        <span class="field-text">-</span>
                        <input name="{{{ namePrefix }}}[end_date]" class="fullwidth date-picker" type="text" value="{{{ item[ 'end_date' ] }}}"<# if ( 'custom' == item[ 'time_range' ] ) { #> required<# } #> autocomplete="off">
                    </div>
                </div>
            </div>
            <hr>
            <div class="controls-row">
                <div class="control-wrapper">
                    <div class="option-title-with-toggle">
                        <input type="checkbox" name="{{{ namePrefix }}}[stay_length][general][enable]" id="{{{ labelID }}}-general-enable-stay-length" value="on"<# if ( item[ 'stay_length' ][ 'general' ][ 'enable' ] == 'on' ) { #> checked<# } #>>
                        <label for="{{{ labelID }}}-general-enable-stay-length"><?php esc_html_e( 'Stay Length', 'loftocean' ); ?></label>
                    </div>
                    <div class="option-content-after-toggle<# if ( item[ 'stay_length' ][ 'general' ][ 'enable'] != 'on' ) { #> hide<# } #>">
                        <div class="multi-items-wrapper">
                            <div class="item-wrapper fullwidth">
                                <label class="label-text-small" for="{{{ labelID }}}-general-min-stay-length"><?php esc_html_e( 'Minimum Stay', 'loftocean' ); ?></label>
                                <div class="loftocean-room-value-with-inline-unit">
                                    <input name="{{{ namePrefix }}}[stay_length][general][min]" id="{{{ labelID }}}-genral-min-stay-length" type="number" value="{{{ item[ 'stay_length' ][ 'general' ][ 'min' ] }}}">
                                    <span class="discount-unit"><?php esc_html_e( 'Nights', 'loftocean' ); ?></span>
                                </div>
                            </div>
                            <div class="item-wrapper fullwidth">
                                <label class="label-text-small" for="{{{ labelID }}}-general-max-stay-length"><?php esc_html_e( 'Maximum Stay', 'loftocean' ); ?></label>
                                <div class="loftocean-room-value-with-inline-unit">
                                    <input name="{{{ namePrefix }}}[stay_length][general][max]" id="{{{ labelID }}}-general-max-stay-length" type="number" value="{{{ item[ 'stay_length' ][ 'general' ][ 'max' ] }}}">
                                    <span class="discount-unit"><?php esc_html_e( 'Nights', 'loftocean' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="controls-row">
                <div class="control-wrapper">
                    <div class="option-title-with-toggle">
                        <input type="checkbox" name="{{{ namePrefix }}}[stay_length][custom][enable]" id="{{{ labelID }}}-custom-enable-stay-length" value="on"<# if ( 'on' == item[ 'stay_length' ][ 'custom' ][ 'enable'] ) { #> checked<# } #>>
                        <label for="{{{ labelID }}}-custom-enable-stay-length"><?php esc_html_e( 'Set Stay Length by Check-in Day', 'loftocean' ); ?></label>
                    </div>
                    <div class="option-content-after-toggle stay-length-by-checkin<# if ( 'on' != item[ 'stay_length' ][ 'custom' ][ 'enable'] ) { #> hide<# } #>">
                        <div class="multi-items-wrapper">
                            <div class="item-wrapper">
                                <label class="label-text-medium"><?php esc_html_e( 'Check-in Day', 'loftocean' ); ?></label>
                            </div>
                            <div class="item-wrapper">
                                <label class="label-text-medium"><?php esc_html_e( 'Minimum Stay', 'loftocean' ); ?></label>
                            </div>
                            <div class="item-wrapper">
                                <label class="label-text-medium"><?php esc_html_e( 'Maximum Stay', 'loftocean' ); ?></label>
                            </div>
                        </div><?php
                        foreach ( $weekdays as $index => $label ) : ?>
                            <div class="multi-items-wrapper">
                                <div class="item-wrapper"><?php echo esc_html( $label ); ?></div>
                                <div class="item-wrapper">
                                    <div class="loftocean-room-value-with-inline-unit">
                                        <input name="{{{ namePrefix }}}[stay_length][custom][day<?php echo $index; ?>][min]" type="number" value="{{{ item[ 'stay_length' ][ 'custom' ][ 'day<?php echo $index; ?>' ][ 'min' ] }}}">
                                        <span class="discount-unit"><?php esc_html_e( 'Nights', 'loftocean' ); ?></span>
                                    </div>
                                </div>
                                <div class="item-wrapper">
                                    <div class="loftocean-room-value-with-inline-unit">
                                        <input name="{{{ namePrefix }}}[stay_length][custom][day<?php echo $index; ?>][max]" type="number" value="{{{ item[ 'stay_length' ][ 'custom' ][ 'day<?php echo $index; ?>' ][ 'max' ] }}}">
                                        <span class="discount-unit"><?php esc_html_e( 'Nights', 'loftocean' ); ?></span>
                                    </div>
                                </div>
                            </div><?php
                        endforeach; ?>
                    </div>
                </div>
            </div>
            <hr>
            <div class="controls-row">
                <div class="control-wrapper">
                    <div class="option-title-with-toggle">
                        <input type="checkbox" name="{{{ namePrefix }}}[no_checkin_checkout_date][enable]" id="{{{ labelID }}}-enable-no-checkin-checkout-date" value="on"<# if ( 'on' == item[ 'no_checkin_checkout_date' ][ 'enable' ] ) { #> checked <# } #>>
                        <label for="{{{ labelID }}}-enable-no-checkin-checkout-date"><?php esc_html_e( 'No Check-in and No Check-out Days Settings', 'loftocean' ); ?></label>
                    </div>
                    <div class="option-content-after-toggle<# if ( 'on' != item[ 'no_checkin_checkout_date' ][ 'enable' ] ) { #> hide<# } #>">
                        <div class="multi-items-wrapper">
                            <div class="item-wrapper fullwidth">
                                <label class="label-text-medium"><?php esc_html_e( 'Can\'t Check In On', 'loftocean' ); ?></label>
                                <div class="multi-checkboxes"><?php
                                foreach ( $weekdays as $index => $label ) : ?>
                                    <div class="checkbox-item">
                                        <input id="{{{ labelID }}}-no-checkin-day<?php echo $index; ?>" name="{{{ namePrefix }}}[no_checkin_checkout_date][checkin][day<?php echo $index; ?>]" type="checkbox" value="on" <# if ( 'on' == item[ 'no_checkin_checkout_date' ][ 'checkin' ][ 'day<?php echo $index; ?>' ] ) { #> checked<# } #>>
                                        <label for="{{{ labelID }}}-no-checkin-day<?php echo $index; ?>"><?php echo esc_html( $label ); ?></label>
                                    </div><?php
                                endforeach; ?>
                                </div>
                            </div>

                            <div class="item-wrapper fullwidth">
                                <label class="label-text-medium"><?php esc_html_e( 'Can\'t Check Out On', 'loftocean' ); ?></label>
                                <div class="multi-checkboxes"><?php
                                foreach ( $weekdays as $index => $label ) : ?>
                                    <div class="checkbox-item">
                                        <input id="{{{ labelID }}}-no-checkout-day<?php echo $index; ?>" name="{{{ namePrefix }}}[no_checkin_checkout_date][checkout][day<?php echo $index; ?>]" type="checkbox" value="on" <# if ( 'on' == item[ 'no_checkin_checkout_date' ][ 'checkout' ][ 'day<?php echo $index; ?>' ] ) { #> checked<# } #>>
                                        <label for="{{{ labelID }}}-no-checkout-day<?php echo $index; ?>"><?php echo esc_html( $label ); ?></label>
                                    </div><?php
                                endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="controls-row">
                <div class="control-wrapper">
                    <div class="option-title-with-toggle">
                        <input type="checkbox" name="{{{ namePrefix }}}[in_advance][enable]" id="{{{ labelID }}}-enable-in-advance" value="on"<# if ( 'on' == item[ 'in_advance' ][ 'enable' ] ) { #> checked<# } #>>
                        <label for="{{{ labelID }}}-enable-in-advance"><?php esc_html_e( 'How far in advance can guests book?', 'loftocean' ); ?></label>
                    </div>
                    <div class="option-content-after-toggle<# if ( 'on' != item[ 'in_advance' ][ 'enable' ] ) { #> hide<# } #>">
                        <div class="multi-items-wrapper">
                            <div class="item-wrapper fullwidth">
                                <label class="label-text-small"><?php esc_html_e( 'Minimum Advance Reservation', 'loftocean' ); ?></label>
                                <div class="loftocean-room-value-with-inline-unit">
                                    <input name="{{{ namePrefix }}}[in_advance][min]" type="number" value="{{{ item[ 'in_advance' ][ 'min' ] }}}">
                                    <span class="discount-unit"><?php esc_html_e( 'Days', 'loftocean' ); ?></span>
                                </div>
                            </div>
                            <div class="item-wrapper fullwidth">
                                <label class="label-text-small"><?php esc_html_e( 'Maximum Advance Reservation', 'loftocean' ); ?></label>
                                <div class="loftocean-room-value-with-inline-unit">
                                    <input name="{{{ namePrefix }}}[in_advance][max]" type="number" value="{{{ item[ 'in_advance' ][ 'max' ] }}}">
                                    <span class="discount-unit"><?php esc_html_e( 'Days', 'loftocean' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="subsection-divider">
            <div class="controls-row">
                <div class="control-wrapper">
                    <label for="{{{ labelID }}}-apply-to"><?php esc_html_e( 'Apply this rule-set to', 'loftocean' ); ?></label>
                    <select name="{{{ namePrefix }}}[apply_to]" id="{{{ labelID }}}-apply-to" class="fullwidth apply-rule-to">
                        <option value=""><?php esc_html_e( 'Choose an option', 'loftocean' ); ?></option>
                        <option value="all"<# if ( 'all' == item[ 'apply_to' ] ) { #> selected<# } #>><?php esc_html_e( 'All Rooms', 'loftocean' ); ?></option>
                        <?php if ( $has_room_types ) : ?><option value="room_types"<# if ( 'room_types' == item[ 'apply_to' ] ) { #> selected<# } #>><?php esc_html_e( 'Selected Room Types', 'loftocean' ); ?></option><?php endif; ?>
                        <?php if( $has_rooms ) : ?><option value="rooms"<# if ( 'rooms' == item[ 'apply_to' ] ) { #> selected<# } #>><?php esc_html_e( 'Selected Rooms', 'loftocean' ); ?></option><?php endif; ?>
                    </select><?php
                    if ( $has_room_types ) : ?>
                        <div class="loftocean-room_types sub-options<# if ( 'room_types' != item[ 'apply_to' ] ) { #> hide <# } #>">
                            <select name="{{{ namePrefix }}}[apply_to_room_types][]" multiple class="multiple-choices"><?php
                                foreach( $room_types as $room_type ) : ?>
                                    <option value="<?php echo esc_attr( $room_type->term_id ); ?>"<# if ( item[ 'apply_to_room_types' ].includes( '<?php echo $room_type->term_id; ?>' ) ) { #> selected<# } #>><?php echo $room_type->name; ?></option><?php
                                endforeach; ?>
                            </select>
                        </div><?php
                    endif;
                    if ( $has_rooms ) : ?>
                        <div class="loftocean-rooms sub-options<# if ( 'rooms' != item[ 'apply_to' ] ) { #> hide <# } #>">
                            <select name="{{{ namePrefix }}}[apply_to_rooms][]" multiple class="multiple-choices"><?php
                                while( $rooms->have_posts() ) :
                                    $rooms->the_post(); ?>
                                    <option value="<?php the_ID(); ?>"<# if ( item[ 'apply_to_rooms' ].includes( '<?php the_ID(); ?>' ) ) { #> selected<# } #>><?php the_title(); ?></option><?php
                                endwhile;
                                $rooms->wp_reset_postdata(); ?>
                            </select>
                        </div><?php
                    endif; ?>
                </div>
            </div>
            <input type="hidden" class="room-rules-item-id-hidden" name="{{{ namePrefix }}}[id]" value="{{{ item.term_id }}}" readonly="">
        </div>
    </div><#
} ); #>
</script>
