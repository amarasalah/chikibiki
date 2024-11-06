<div class="cs-room-order-wrapper">
    <div class="cs-room-order-date">
        <strong><?php esc_html_e( 'Date: ', 'loftocean' ); ?></strong><?php
        $date_format = get_option( 'date_format', 'Y-m-d' );
        echo esc_html( date_i18n( $date_format, $room_order_item_data[ 'check_in' ] ) ); ?> - <?php echo esc_html( date_i18n( $date_format, $room_order_item_data[ 'check_out' ] ) ); ?>
    </div><?php
    $hide_fields = apply_filters( 'loftocean_room_reservation_form_hide_fields', array_fill_keys( array( 'room', 'adult', 'child' ), false ) );
    $hide_child = $hide_fields[ 'child' ] || ( $room_order_item_data[ 'child_number' ] < 1 );
    $hide_adult_child = $hide_fields[ 'adult' ] && $hide_child;
    $hide_wrap = $hide_fields[ 'room' ] && $hide_adult_child;
    if ( ! $hide_wrap ) : ?>
        <div class="cs-room-order-details">
            <strong><?php esc_html_e( 'Details: ', 'loftocean' ); ?></strong><?php
            if ( ! $hide_fields[ 'room' ] ) {
                esc_html_e( 'Rooms: ', 'loftocean' );
                echo esc_html( $room_order_item_data[ 'room_num_search' ] );
                echo $hide_adult_child ? '' : ', ';
            }
            if ( ! $hide_fields[ 'adult' ] ) {
                esc_html_e( 'Adults: ', 'loftocean' );
                echo esc_html( $room_order_item_data[ 'adult_number' ] );
                echo $hide_child ? '' : ', ';
            }
            if ( ! $hide_child ) {
                esc_html_e( 'Children: ', 'loftocean' );
                echo esc_html( $room_order_item_data[ 'child_number' ] );
            } ?>
        </div><?php
    endif;
    if ( isset( $room_order_item_data[ 'extra_services' ], $room_order_item_data[ 'extra_services' ][ 'services' ] ) && \LoftOcean\is_valid_array( $room_order_item_data[ 'extra_services' ][ 'services' ] ) ) : ?>
        <div class="cs-room-order-extra">
            <strong><?php esc_html_e( 'Extra Services: ', 'loftocean' ); ?></strong><?php
            $room_order_extra_services = $room_order_item_data[ 'extra_services' ];
            $titles = $room_order_extra_services[ 'titles' ];
            $prices = $room_order_extra_services[ 'prices' ];
            $method = $room_order_extra_services[ 'method' ];
            $label = $room_order_extra_services[ 'label' ];
            $unit = $room_order_extra_services[ 'unit' ];
            $quantity = $room_order_extra_services[ 'quantity' ];
            $loop_index = 0;
            foreach ( $room_order_extra_services[ 'services' ] as $index => $service_id ) {
                echo ( $loop_index > 0 ) ? ', ' : '';
                echo $titles[ $index ] . ' (';
                echo $label[ $index ];
                echo in_array( $method[ $index ], array( 'custom', 'auto_custom' ) ) ? ' x ' . $quantity[ $index ] : '';
                echo ')';
                $loop_index ++;
            } ?>
        </div><?php
    endif; ?>
</div>
