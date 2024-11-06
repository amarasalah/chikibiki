<?php
if ( class_exists( 'WooCommerce', false ) ) :
    $merge_checkin_checkout = apply_filters( 'loftocean_room_merge_checkin_checkout', false );
    $show_adult_child = true;
    $hide_fields = apply_filters( 'loftocean_room_reservation_form_hide_fields', array_fill_keys( array( 'room', 'adult', 'child' ), false ) );
    $fields_classes = array( 'room' => array( 'cs-form-field', 'cs-rooms' ), 'adult' => array( 'cs-form-field', 'cs-adults' ), 'child' => array( 'cs-form-field', 'cs-children' ) );
    foreach( $hide_fields as $field => $hide ) {
        if ( $hide ) {
            array_push( $fields_classes[ $field ], 'hide' );
            if ( in_array( $field, array( 'adult', 'child' ) ) ) {
                $show_adult_child = false;
            }
        }
    }
    if ( $show_adult_child ) {
        array_push( $fields_classes[ 'adult' ], 'form-field-col-1-2' );
        array_push( $fields_classes[ 'child' ], 'form-field-col-1-2' );
    }
    apply_filters( 'loftocean_room_has_adult_age_description', false ) ? array_push( $fields_classes[ 'adult' ], 'cs-has-age-text' ) : '';
    apply_filters( 'loftocean_room_has_child_age_description', false ) ? array_push( $fields_classes[ 'child' ], 'cs-has-age-text' ) : '';


    $current_room_id = get_queried_object_id();
    $current_currency = \LoftOcean\get_current_currency();

    $adult_number = 1;
    $child_number = 0;
    $room_number = 1;
    $room_number_label = esc_attr__( '1 Room', 'loftocean' );
    $bookingFormData = apply_filters( 'loftocean_room_booking_url_param', array() );
    if ( apply_filters( 'loftocean_pass_params_from_search_result', false ) && \LoftOcean\is_valid_array( $bookingFormData ) && isset( $bookingFormData[ 'adult' ], $bookingFormData[ 'child' ], $bookingFormData[ 'room' ] ) ) {
        $adult_number = $bookingFormData[ 'adult' ];
        $child_number = $bookingFormData[ 'child' ];
        $room_number = $bookingFormData[ 'room' ];
        $room_number_label = $room_number . ' ' . ( $room_number > 1 ? esc_attr__( 'Rooms', 'loftocean' ) : esc_attr__( 'Room', 'loftocean' ) );
    } ?>

    <div class="cs-room-booking loading">
        <div class="cs-room-booking-wrap">
            <div class="room-booking-title">
                <h4><?php esc_html_e( 'Reserve:', 'loftocean' ); ?></h4>
                <span><?php printf(
                    // translators: html tag
                    esc_html__( 'From %s/night', 'loftocean' ),
                    '<span class="base-price"></span>'
                ); ?></span>
            </div>

            <div class="room-booking-form">
                <div class="cs-reservation-form style-block cs-form-square inline-label"<?php if ( $show_adult_child ) : ?> data-guests-field="on"<?php endif; ?>>
                    <div class="cs-form-wrap"><?php
                        if ( $merge_checkin_checkout ) : ?>
                            <div class="cs-form-field-group date-group">
                                <label class="cs-form-label"><?php esc_html_e( 'Dates', 'loftocean' ); ?></label>
                                <input type="text" class="date-range-picker" value="">
                                <div class="cs-form-field-group-inner">
                                    <div class="cs-form-field cs-check-in">
                                        <div class="field-wrap">
                                            <div class="field-input-wrap">
                                                <input type="hidden" value="" name="checkin" readonly>
                                                <span class="input" role="textbox"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="date-separator"></div>
                                    <div class="cs-form-field cs-check-out">
                                        <div class="field-wrap">
                                            <div class="field-input-wrap">
                                                <input type="hidden" value="" name="checkout" readonly>
                                                <span class="input" role="textbox"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><?php
                        else : ?>
                            <div class="cs-form-field cs-check-in">
                                <div class="field-wrap">
                                    <label class="cs-form-label"><?php esc_html_e( 'Check In', 'loftocean' ); ?></label>
                                    <div class="field-input-wrap checkin-date">
                                        <input type="text" class="date-range-picker" value="">
                                        <input type="text" value="" name="checkin" class="check-in-date" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="cs-form-field cs-check-out">
                                <div class="field-wrap">
                                    <label class="cs-form-label"><?php esc_html_e( 'Check Out', 'loftocean' ); ?></label>
                                    <div class="field-input-wrap checkout-date">
                                        <input type="text" value="" name="checkout" readonly>
                                    </div>
                                </div>
                            </div><?php
                        endif; ?>

                        <div class="<?php echo esc_attr( implode( ' ', $fields_classes[ 'room' ] ) ); ?>">
                            <div class="field-wrap">
                                <label class="cs-form-label"><?php esc_html_e( 'Rooms', 'loftocean' ); ?></label>
                                <div class="field-input-wrap has-dropdown">
                                    <input type="text" name="rooms" value="<?php echo esc_attr( $room_number_label ); ?>" readonly="">
                                </div>

                                <div class="csf-dropdown">
                                    <div class="csf-dropdown-item has-dropdown">
                                        <label class="cs-form-label"><?php esc_html_e( 'Rooms', 'loftocean' ); ?></label>
                                        <div class="quantity cs-quantity" data-label="room">
                                            <label class="screen-reader-text"><?php esc_html_e( 'Rooms quantity', 'loftocean' ); ?></label>
                                            <button class="minus"></button>
                                            <input type="text" name="room-quantity" value="<?php echo esc_attr( $room_number ); ?>" class="input-text" autocomplete="off" readonly="" data-min="1">
                                            <button class="plus"></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cs-form-notice">
                                <p><?php printf(
                                    // translators: %s room available number
                                    esc_html__( 'Only %s Left', 'loftocean' ),
                                    '<span class="room-error-limit-number"></span>'
                                ); ?></p>
                            </div>
                        </div>

                        <div class="<?php echo esc_attr( implode( ' ', $fields_classes[ 'adult' ] ) ); ?>">
                            <div class="field-wrap">
                                <label class="cs-form-label">
                                    <?php esc_html_e( 'Adults', 'loftocean' ); ?>
                                    <?php do_action( 'loftocean_room_adult_age_description' ); ?>        
                                </label>
                                <div class="field-input-wrap has-dropdown">
                                    <input type="text" name="adults" value="<?php echo esc_attr( $adult_number ); ?>" readonly="">
                                </div>
                                <div class="csf-dropdown">
                                    <div class="csf-dropdown-item has-dropdown">
                                        <label class="cs-form-label"><?php esc_html_e( 'Adults', 'loftocean' ); ?></label>
                                        <div class="quantity cs-quantity">
                                            <label class="screen-reader-text"><?php esc_html_e( 'Adults quantity', 'loftocean' ); ?></label>
                                            <button class="minus"><span class="cs-btn-tooltip hide" data-title=""></button>
                                            <input type="text" name="adult-quantity" value="<?php echo esc_attr( $adult_number ); ?>" class="input-text" autocomplete="off" readonly="" data-min="1">
                                            <button class="plus"><span class="cs-btn-tooltip hide" data-title=""></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="<?php echo esc_attr( implode( ' ', $fields_classes[ 'child' ] ) ); ?>">
                            <div class="field-wrap">
                                <label class="cs-form-label">
                                    <?php esc_html_e( 'Children', 'loftocean' ); ?>
                                    <?php do_action( 'loftocean_room_child_age_description' ); ?>
                                </label>
                                <div class="field-input-wrap has-dropdown">
                                    <input type="text" name="children" value="<?php echo esc_attr( $child_number ); ?>" readonly="">
                                </div>
                                <div class="csf-dropdown">
                                    <div class="csf-dropdown-item has-dropdown">
                                        <label class="cs-form-label"><?php esc_html_e( 'Children', 'loftocean' ); ?></label>
                                        <div class="quantity cs-quantity">
                                            <label class="screen-reader-text"><?php esc_html_e( 'Children quantity', 'loftocean' ); ?></label>
                                            <button class="minus"><span class="cs-btn-tooltip hide" data-title=""></span></button>
                                            <input type="text" name="child-quantity" value="<?php echo esc_attr( $child_number ); ?>" class="input-text" autocomplete="off" readonly="" data-min="0">
                                            <button class="plus"><span class="cs-btn-tooltip hide" data-title=""></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><?php
                        do_action( 'loftocean_room_booking_form_extra_text' ); 
                        $total_cost_default_status = apply_filters( 'loftocean_room_total_cost_default_status', 'default-hide' ); ?>
                        <div class="cs-form-total-price <?php echo $total_cost_default_status; ?>">
                            <h5 class="csf-title"> 
                                <?php esc_html_e( 'Total Cost', 'loftocean' ); ?>
                                <span class="price-details<?php if ( 'always-show' == $total_cost_default_status ) : ?> hide<?php endif; ?>">
                                    <span class="screen-reader-text"><?php esc_html_e( 'View Details', 'loftocean' ); ?></span>
                                </span>
                            </h5>
                            <div class="total-price"><?php echo $current_currency[ 'left' ]; ?><span class="total-price-number"></span><?php echo $current_currency[ 'right' ]; ?></div>
                        </div>
                        <div class="cs-form-price-details hide"></div>
                        <div class="cs-form-field cs-submit">
                            <div class="field-wrap">
                                <button type="submit" class="button cs-btn-color-black cs-btn-rounded"><span class="btn-text"><?php esc_html_e( 'Book Your Stay Now', 'loftocean' ); ?></span></button>
                            </div>
                        </div>
                    </div>
                    <div class="cs-form-error-message"></div>
                    <div class="cs-form-success-message"></div>
                </div>
            </div>
        </div>
    </div><?php
endif;
