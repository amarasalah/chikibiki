<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Flexible Price Rules', 'loftocean' ); ?></h1>
    <hr class="wp-header-end">
    <form id="loftocean-room-room-price-rules-form" action="<?php echo esc_url( admin_url( 'edit.php?post_type=loftocean_room&page=loftocean_room_flexible_price_rules' ) ); ?>" method="POST">
        <div class="loftocean-room-rules-wrapper">
            <a href="#" class="loftocean-room-rules-add" data-current-index="0" data-rule-type="flexible-price"><?php esc_html_e( 'Add New', 'loftocean' ); ?></a>
        </div>
        <p class="submit loftocean-submit-button">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'loftocean' ); ?>" disabled>
            <span class="spinner" style="visibility: visible; float: none;"></span>
        </p>
        <input type="hidden" name="loftocean_room_rules_removed" value="" />
        <input type="hidden" name="loftocean_room_rules_settings_nonce" value="<?php echo esc_attr( wp_create_nonce( 'loftocean_room_booking_rules' ) ); ?>" />
    </form>
</div><?php
$room_types = get_terms( array( 'taxonomy' => 'lo_room_type', 'hide_empty' => false ) );
$rooms = new WP_Query( array( 'post_type' => 'loftocean_room', 'posts_per_page' => '-1', 'offset' => 0 ) );
$has_room_types = ( ! is_wp_error( $room_types ) ) && ( count( $room_types ) > 0 );
$has_rooms = ( ! is_wp_error( $rooms ) ) && $rooms->have_posts(); ?>
<script id="tmpl-loftocean-room-rule-item" type="text/html">
<# data.list.forEach( function( item ) {
    var itemIndex = data.index ++,
        namePrefix = 'loftocean_room_flexible_price_rules[item' + itemIndex + ']',
        labelID = 'loftocean-room-flexible-price-rule-item' + itemIndex ;
        customLongStayDiscount = item[ 'custom_long_stay_discount' ]; #>
    <div class="loftocean-room-rules-item">
        <h3><?php esc_html_e( 'Price Rule', 'loftocean' ); ?><span class="item-name"><span class="item-name"><# if ( item.title ) { #> - {{{ item.title }}}<# } #></span></h3>
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
                <div class="control-wrapper">
                    <label><?php esc_html_e( 'Special Price', 'loftocean' ); ?><span class="required"> *</span></label>
                    <div class="multi-items-wrapper">
                        <select name="{{{ namePrefix }}}[special_price][operator]">
                            <option value="-"<# if ( '-' == item[ 'special_price' ][ 'operator' ] ) { #> selected<# } #>><?php esc_html_e( 'Decrease', 'loftocean' ); ?></option>
                            <option value="+"<# if ( '+' == item[ 'special_price' ][ 'operator' ] ) { #> selected<# } #>><?php esc_html_e( 'Increase', 'loftocean' ); ?></option>
                        </select>
                        <div class="loftocean-room-value-with-inline-unit">
                            <input name="{{{ namePrefix }}}[special_price][amount]" class="fullwidth" type="number" value="{{{ item[ 'special_price' ][ 'amount' ] }}}" min=0 max=100 required>
                            <span class="discount-unit"><?php esc_html_e( '%', 'loftocean' ); ?></span>
                        </div>
                    </div>
                    <div class="item-description"><?php printf(
                        // translators: 1/2: html strong open/close tag
                        esc_html__( '%1$sSpecial Price%2$s is based on the original price of the room and are scaled up or down by the percentage set.', 'loftocean' ),
                        '<strong>',
                        '</strong>'
                    ); ?></div>
                </div>
            </div>
            <hr>
            <div class="controls-row">
                <div class="control-wrapper">
                    <div class="option-title-with-toggle">
                        <input type="checkbox" id="{{{ labelID }}}-long-stay-discount-enable" name="{{{ namePrefix }}}[long_stay_discount][enable]"<# if ( 'on' == item[ 'long_stay_discount' ][ 'enable' ] ) { #> checked<# } #>>
                        <label for="{{{ labelID }}}-long-stay-discount-enable"><?php esc_html_e( 'Long-Stay Discounts', 'loftocean' ); ?></label>
                    </div>
                    <div class="option-content-after-toggle<# if ( 'on' != item[ 'long_stay_discount' ][ 'enable' ] ) { #> hide<# } #>">
                        <div class="item-description"><?php esc_html_e( 'When a guest checks in on any day within the specified time frame (regardless of when the guest checks out)', 'loftocean' ); ?></div>
                        <div class="multi-items-wrapper">
                            <div class="item-wrapper fullwidth">
                                <label class="label-text-small"><?php esc_html_e( 'Weekly Discount', 'loftocean' ); ?></label>
                                <input name="{{{ namePrefix }}}[long_stay_discount][weekly]" type="number" value="{{{ item[ 'long_stay_discount' ][ 'weekly' ] }}}" min=0 max=100>
                                <span class="discount-unit"><?php esc_html_e( '% off', 'loftocean' ); ?></span>
                                <div class="item-description"><?php esc_html_e( 'Discount for consecutive stays of 7 days or more.', 'loftocean' ); ?></div>
                            </div>
                            <div class="item-wrapper fullwidth">
                                <label class="label-text-small"><?php esc_html_e( 'Monthly Discount', 'loftocean' ); ?></label>
                                <input name="{{{ namePrefix }}}[long_stay_discount][monthly]" type="number" value="{{{ item[ 'long_stay_discount' ][ 'monthly' ] }}}" min=0 max=100>
                                <span class="discount-unit"><?php esc_html_e( '% off', 'loftocean' ); ?></span>
                                <div class="item-description"><?php esc_html_e( 'Discount for consecutive stays of 28 days or more.', 'loftocean' ); ?></div>
                            </div>
                        </div>
                        <div class="custom-long-stay">
                            <div class="option-title-with-toggle">
                                <input type="checkbox" id="{{{ namePrefix }}}[custom_long_stay_discount][enable]" name="{{{ namePrefix }}}[custom_long_stay_discount][enable]" <# if ( 'on' == customLongStayDiscount[ 'enable' ] ) { #> checked<# } #>> 
                                <label class="label-text-medium" for="{{{ namePrefix }}}[custom_long_stay_discount][enable]"><?php esc_html_e( 'Set Custom Long-Stay Discounts', 'loftocean' ); ?></label>
                            </div>
                            <div class="option-content-after-toggle<# if ( 'on' != customLongStayDiscount[ 'enable' ] ) { #> hide<# } #>" data-name-prefix="{{{ namePrefix }}}"><#
                                if ( Array.isArray( customLongStayDiscount[ 'items' ] ) && customLongStayDiscount[ 'items' ].length ) { 
                                    customLongStayDiscount[ 'items' ].forEach( function( clsdItem, clsdItemIndex ) { #>
                                        <div class="multi-items-wrapper">
                                            <div class="item-wrapper">
                                                <input name="{{{ namePrefix }}}[custom_long_stay_discount][items][][days]" type="number" value="{{{ clsdItem[ 'days' ] }}}">
                                                <span class="discount-unit"><?php esc_html_e( 'days or more', 'loftocean' ); ?></span>
                                            </div>

                                            <div class="item-wrapper">
                                                <input name="{{{ namePrefix }}}[custom_long_stay_discount][items][][off]" type="number" value="{{{ clsdItem[ 'off' ] }}}">
                                                <span class="discount-unit"><?php esc_html_e( '% off', 'loftocean' ); ?></span>
                                            </div>

                                            <div class="item-wrapper">
                                                <a href="#" class="add-custom-custom-long-stay"><?php esc_html_e( 'Add', 'loftocean' ); ?></a>
                                                <a href="#" class="delete-custom-custom-long-stay"><?php esc_html_e( 'Delete', 'loftocean' ); ?></a>
                                            </div>
                                        </div><#
                                    } );
                                } else { #>
                                    <div class="multi-items-wrapper">
                                        <div class="item-wrapper">
                                            <input name="{{{ namePrefix }}}[custom_long_stay_discount][items][][days]" type="number" value="">
                                            <span class="discount-unit"><?php esc_html_e( 'days or more', 'loftocean' ); ?></span>
                                        </div>

                                        <div class="item-wrapper">
                                            <input name="{{{ namePrefix }}}[custom_long_stay_discount][items][][off]" type="number" value="">
                                            <span class="discount-unit"><?php esc_html_e( '% off', 'loftocean' ); ?></span>
                                        </div>

                                        <div class="item-wrapper">
                                            <a href="#" class="add-custom-custom-long-stay"><?php esc_html_e( 'Add', 'loftocean' ); ?></a>
                                            <a href="#" class="delete-custom-custom-long-stay"><?php esc_html_e( 'Delete', 'loftocean' ); ?></a>
                                        </div>
                                    </div><#
                                } #>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="controls-row">
                <div class="control-wrapper">
                    <div class="option-title-with-toggle">
                        <input type="checkbox" name="{{{ namePrefix}}}[early_bird_discount][enable]" id="{{{ labelID }}}-enable-early-bird-discount"<# if ( 'on' == item[ 'early_bird_discount' ][ 'enable' ] ) { #> checked<# } #>>
                        <label for="{{{ labelID }}}-enable-early-bird-discount"><?php esc_html_e( 'Early Bird Discount', 'loftocean' ); ?></label>
                    </div>
                    <div class="option-content-after-toggle<# if ( 'on' != item[ 'early_bird_discount' ][ 'enable' ] ) { #> hide<# } #>">
                        <div class="item-description"><?php esc_html_e( 'Offer a discount for early bookings.', 'loftocean' ); ?></div>

                        <div class="multi-items-wrapper">
                            <div class="item-wrapper fullwidth">
                                <label class="label-text-small"><?php esc_html_e( 'Discount Ends', 'loftocean' ); ?></label>
                                <input name="{{{ namePrefix }}}[early_bird_discount][days_before]" type="number" value="{{{ item[ 'early_bird_discount' ][ 'days_before' ] }}}">
                                <span><?php esc_html_e( 'days before arrival', 'loftocean' ); ?></span>
                            </div>

                            <div class="item-wrapper">
                                <label class="label-text-small"><?php esc_html_e( 'Discount', 'loftocean' ); ?></label>
                                <div class="loftocean-room-value-with-inline-unit">
                                    <input name="{{{ namePrefix }}}[early_bird_discount][discount]" type="number" value="{{{ item[ 'early_bird_discount' ][ 'discount' ] }}}" min=0 max=100>
                                    <span class="discount-unit"><?php esc_html_e( '% off', 'loftocean' ); ?></span>
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
                        <input id="{{{ labelID }}}-enable-last-minute-discount" type="checkbox" name="{{{ namePrefix }}}[last_minute_discount][enable]"<# if ( 'on' == item[ 'last_minute_discount' ][ 'enable' ] ) { #> checked<# } #>>
                        <label for="{{{ labelID }}}-enable-last-minute-discount"><?php esc_html_e( 'Last-Minute Discount', 'loftocean' ); ?></label>
                    </div>
                    <div class="option-content-after-toggle<# if ( 'on' != item[ 'last_minute_discount' ][ 'enable' ] ) { #> hide<# } #>">
                        <div class="item-description"><?php esc_html_e( 'Offer a discount for bookings close to arrival.', 'loftocean' ); ?></div>
                        <div class="multi-items-wrapper">
                            <div class="item-wrapper fullwidth">
                                <label class="label-text-small"><?php esc_html_e( 'Discount Starts', 'loftocean' ); ?></label>
                                <input name="{{{ namePrefix }}}[last_minute_discount][days_before]" type="number" value="{{{ item[ 'last_minute_discount' ][ 'days_before' ] }}}">
                                <span><?php esc_html_e( 'days before arrival', 'loftocean' ); ?></span>
                            </div>

                            <div class="item-wrapper">
                                <label class="label-text-small"><?php esc_html_e( 'Discount', 'loftocean' ); ?></label>
                                <div class="loftocean-room-value-with-inline-unit">
                                    <input name="{{{ namePrefix }}}[last_minute_discount][discount]" type="number" value="{{{ item[ 'last_minute_discount' ][ 'discount' ] }}}" min=0 max=100>
                                    <span class="discount-unit"><?php esc_html_e( '% off', 'loftocean' ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="subsection-divider">
            <div class="controls-row">
                <div class="control-wrapper">
                    <label><?php esc_html_e( 'Apply this rule-set to', 'loftocean' ); ?></label>
                    <select name="{{{ namePrefix }}}[apply_to]" class="fullwidth apply-rule-to">
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
<script id="tmpl-loftocean-room-rule-custom-long-stay-item" type="text/html">
    <div class="multi-items-wrapper">
        <div class="item-wrapper">
            <input name="{{{ data.namePrefix }}}[custom_long_stay_discount][items][][days]" type="number" value="">
            <span class="discount-unit"><?php esc_html_e( 'days or more', 'loftocean' ); ?></span>
        </div>

        <div class="item-wrapper">
            <input name="{{{ data.namePrefix }}}[custom_long_stay_discount][items][][off]" type="number" value="">
            <span class="discount-unit"><?php esc_html_e( '% off', 'loftocean' ); ?></span>
        </div>

        <div class="item-wrapper">
            <a href="#" class="add-custom-custom-long-stay"><?php esc_html_e( 'Add', 'loftocean' ); ?></a>
            <a href="#" class="delete-custom-custom-long-stay"><?php esc_html_e( 'Delete', 'loftocean' ); ?></a>
        </div>
    </div>
</script>
