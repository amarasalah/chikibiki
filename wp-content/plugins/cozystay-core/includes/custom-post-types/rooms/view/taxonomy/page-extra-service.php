<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Room Extra Service', 'loftocean' ); ?></h1>
    <hr class="wp-header-end">
    <form id="loftocean-room-extra-service-form" action="<?php echo esc_url( admin_url( 'edit.php?post_type=loftocean_room&page=loftocean_room_extra_services' ) ); ?>" method="POST">
        <div class="loftocean-room-extra-service-wrapper">
            <a href="#" class="loftocean-room-extra-service-add" data-current-index="0"><?php esc_html_e( 'Add New', 'loftocean' ); ?></a>
        </div>
        <p class="submit loftocean-submit-button">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'loftocean' ); ?>" disabled>
            <span class="spinner" style="visibility: visible; float: none;"></span>
        </p>
        <input type="hidden" name="loftocean_room_extra_service_removed" value="" />
        <input type="hidden" name="loftocean_room_extra_services_settings_nonce" value="<?php echo esc_attr( wp_create_nonce( 'loftocean_room_extra_services_settings_nonce' ) ); ?>" />
    </form>
</div>
<script id="tmpl-loftocean-room-extra-service" type="text/html">
<# data.list.forEach( function( item ) {
    var namePrefix = 'loftocean_room_extra_service[item' + ( data.index ++ ) + ']'; #>
    <div class="loftocean-room-extra-service-item">
        <h3><?php esc_html_e( 'Room Extra Service', 'loftocean' ); ?><span class="item-name"><# if ( item[ 'name' ] ) { #> - {{{ item.name }}}<# } #></span></h3>
        <a href="#" class="loftocean-room-extra-service-item-remove"><?php esc_html_e( 'Remove', 'loftocean' ); ?></a>
        <div class="loftocean-room-extra-service-controls-wrapper">
            <div class="controls-row">
                <div class="control-wrapper">
                    <label><?php esc_html_e( 'Title:', 'loftocean' ); ?></label>
                    <input name="{{{ namePrefix }}}[title]" class="loftocean-room-extra-service-title" type="text" value="{{{ item.name }}}">
                </div>
                <div class="control-wrapper">
                    <label><?php esc_html_e( 'Price:', 'loftocean' ); ?></label>
                    <input name="{{{ namePrefix }}}[price]" class="loftocean-room-extra-servie-price" type="number" value="{{{ item.price }}}" step="0.01">
                </div>
            </div>
            <div class="controls-row">
                <div class="control-wrapper">
                    <label><?php esc_html_e( 'How it is calculated:', 'loftocean' ); ?></label>
                    <select class="loftocean-room-extra-service-method" name="{{{ namePrefix }}}[method]">
                        <option value="fixed"<# if ( 'fixed' == item.method ) { #> selected<# } #>><?php esc_html_e( 'Fixed Fee', 'loftocean' ); ?></option>
                        <option value="auto"<# if ( 'auto' == item.method ) { #> selected<# } #>><?php esc_html_e( 'Automatic Calculation', 'loftocean' ); ?></option>
                        <option value="custom"<# if ( 'custom' == item.method ) { #> selected<# } #>><?php esc_html_e( 'Item Price * User Set Quantity', 'loftocean' ); ?></option>
                        <option value="auto_custom"<# if ( 'auto_custom' == item.method ) { #> selected<# } #>><?php esc_html_e( 'Item Price * User Set Quantity * Nights', 'loftocean' ); ?></option>
                    </select>
                </div>
                <div class="control-wrapper control-auto-calculated-item"<# if ( 'auto' != item.method ) { #> style="display: none;"<# } #>>
                    <label></label>
                    <select class="loftocean-room-extra-service-auto-method" name="{{{ namePrefix }}}[auto_method]">
                        <option value="night"<# if ( 'night' == item['auto_method'] ) { #> selected<# } #>><?php esc_html_e( 'Item Price * Nights', 'loftocean' ); ?></option>
                        <option value="person"<# if ( 'person' == item['auto_method'] ) { #> selected<# } #>><?php esc_html_e( 'Item Price * Guests', 'loftocean' ); ?></option>
                        <option value="night-room"<# if ( 'night-room' == item['auto_method'] ) { #> selected<# } #>><?php esc_html_e( 'Item Price * Nights * Rooms', 'loftocean' ); ?></option>
                        <option value="night-person"<# if ( 'night-person' == item['auto_method'] ) { #> selected<# } #>><?php esc_html_e( 'Item Price * Nights * Guests', 'loftocean' ); ?></option>
                    </select>
                </div>
                <div class="control-wrapper control-auto-calculated-price-item"<# if ( ( 'auto' != item.method ) || ( ! [ 'night-person', 'person' ].includes( item['auto_method'] ) ) ) { #> style="display: none;"<# } #>>
                    <label><?php esc_html_e( 'Adult Price / Child Price', 'loftocean' ); ?></label>
                    <div class="multi-items-wrapper">
                        <div class="item-wrapper fullwidth">
                            <div class="loftocean-room-value-with-inline-unit">
                                <input name="{{{ namePrefix }}}[custom_adult_price]" type="number" value="{{{ item[ 'custom_adult_price' ] }}}" step="0.01">
                                <span class="price-unit"><?php esc_html_e( 'Per Adult', 'loftocean' ); ?></span>
                            </div>
                        </div>

                        <div class="item-wrapper fullwidth">
                            <div class="loftocean-room-value-with-inline-unit">
                                <input name="{{{ namePrefix }}}[custom_child_price]" type="number" value="{{{ item[ 'custom_child_price' ] }}}" step="0.01">
                                <span class="price-unit"><?php esc_html_e( 'Per Child', 'loftocean' ); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="item-description"><?php esc_html_e( 'This item is optional. If you set an adult price and a child price, it will override the "Price" setting above.', 'loftocean' ); ?></div>
                </div>
                <div class="control-wrapper control-custom-item"<# if ( ! [ 'custom', 'auto_custom' ].includes( item[ 'method' ] ) ) { #> style="display: none;"<# } #>>
                    <label><?php esc_html_e( 'Extra Text after Item Price:', 'loftocean' ); ?></label>
                    <input name="{{{ namePrefix }}}[custom_price_appendix_text]" class="loftocean-room-extra-service-custom-appendix-text" type="text" value="{{{ item.custom_price_appendix_text }}}">
                </div>
                <div class="control-wrapper control-custom-item"<# if ( ! [ 'custom', 'auto_custom' ].includes( item[ 'method' ] ) ) { #> style="display: none;"<# } #>>
                    <label><?php esc_html_e( 'Quantity Range:', 'loftocean' ); ?></label>
                    <div class="multi-items-wrapper">
                        <input name="{{{ namePrefix }}}[custom_minimum_quantity]" class="fullwidth loftocean-room-extra-service-custom-minimum-quantity" type="number" value="{{{ item.custom_minimum_quantity }}}" min=1 placeholder="<?php esc_attr_e( 'Min Value', 'loftocean' ); ?>">
                        <span class="field-text">-</span>
                        <input name="{{{ namePrefix }}}[custom_maximum_quantity]" class="fullwidth loftocean-room-extra-service-custom-maximum-quantity" type="number" value="{{{ item.custom_maximum_quantity }}}" min=1 placeholder="<?php esc_attr_e( 'Max Value', 'loftocean' ); ?>">
                    </div>
                    <div class="item-description"><?php esc_html_e( 'This item is optional. You can set the range of numbers that visitors can enter. Leave blank for no restrictions.', 'loftocean' ); ?></div>
                </div>
            </div>
            <hr>
            <div class="controls-row">
                <div class="control-wrapper">
                    <label><?php esc_html_e( 'Effective Time:', 'loftocean' ); ?></label>
                    <select class="effective-time" name="{{{ namePrefix }}}[effective_time]">
                        <option value=""<# if ( '' == item['effective_time'] ) { #> selected<# } #>><?php esc_html_e( 'Always', 'loftocean' ); ?></option>
                        <option value="activated"<# if ( 'activated' == item['effective_time'] ) { #> selected<# } #>><?php esc_html_e( 'Activated during', 'loftocean' ); ?></option>
                        <option value="deactivated"<# if ( 'deactivated' == item['effective_time'] ) { #> selected<# } #>><?php esc_html_e( 'Deactivated during', 'loftocean' ); ?></option>
                    </select>
                </div>

                <div class="control-wrapper custom-effective-time-slots-wrapper" data-slot-count="{{{ item.custom_effective_time_slots.length }}}" data-name-prefix="{{{ namePrefix }}}"<# if ( '' == item['effective_time'] ) { #> style="display: none;"<# } #>><#
                    if ( item.custom_effective_time_slots.length ) {
                        item.custom_effective_time_slots.forEach( function( cets, cets_index ) { #>
                            <div class="multi-items-wrapper">
                                <input name="{{{ namePrefix }}}[custom_effective_time_slot][{{{ cets_index }}}][start]" class="fullwidth date-picker" type="text" value="{{{ cets['start'] }}}" autocomplete="off">
                                <span class="field-text">-</span>
                                <input name="{{{ namePrefix }}}[custom_effective_time_slot][{{{ cets_index }}}][end]" class="fullwidth date-picker" type="text" value="{{{ cets['end'] }}}" autocomplete="off">
                                <a href="#" class="add-custom-effective-time-slot"><?php esc_html_e( 'Add', 'loftocean' ); ?></a>
                                <a href="#" class="delete-custom-effective-time-slot"><?php esc_html_e( 'Delete', 'loftocean' ); ?></a>
                            </div><#
                        } );
                    } else { #>
                        <div class="multi-items-wrapper">
                            <input name="{{{ namePrefix }}}[custom_effective_time_slot][0][start]" class="fullwidth date-picker" type="text" value="" autocomplete="off">
                            <span class="field-text">-</span>
                            <input name="{{{ namePrefix }}}[custom_effective_time_slot][0][end]" class="fullwidth date-picker" type="text" value="" autocomplete="off">
                            <a href="#" class="add-custom-effective-time-slot"><?php esc_html_e( 'Add', 'loftocean' ); ?></a>
                            <a href="#" class="delete-custom-effective-time-slot"><?php esc_html_e( 'Delete', 'loftocean' ); ?></a>
                        </div><#
                    } #>
                </div>
            </div>
            <hr>
            <div class="controls-row">
                <div class="control-wrapper">
                    <label><?php esc_html_e( 'Set as obligatory?', 'loftocean' ); ?></label>
                    <select class="obligatory-service" name="{{{ namePrefix }}}[obligatory]">
                        <option value="yes"<# if ( 'yes' == item[ 'obligatory' ] ) { #> selected<# } #>><?php esc_html_e( 'Yes', 'loftocean' ); ?></option>
                        <option value=""<# if ( '' == item[ 'obligatory' ] ) { #> selected<# } #>><?php esc_html_e( 'No', 'loftocean' ); ?></option>
                    </select>
                </div>
            </div>
            <input type="hidden" class="service-item-id-hidden" name="{{{ namePrefix }}}[id]" value="{{{ item.term_id }}}" readonly />
        </div>
    </div><#
} ); #>
</script>

<script id="tmpl-loftocean-room-extra-service-custom-time-slot" type="text/template">
    <div class="multi-items-wrapper">
        <input name="{{{ data.namePrefix }}}[custom_effective_time_slot][{{{ data.index }}}][start]" class="fullwidth date-picker" type="text" value="" autocomplete="off">
        <span class="field-text">-</span>
        <input name="{{{ data.namePrefix }}}[custom_effective_time_slot][{{{ data.index }}}][end]" class="fullwidth date-picker" type="text" value="" autocomplete="off">
        <a href="#" class="add-custom-effective-time-slot"><?php esc_html_e( 'Add', 'loftocean' ); ?></a>
        <a href="#" class="delete-custom-effective-time-slot"><?php esc_html_e( 'Delete', 'loftocean' ); ?></a>
    </div>
</script
