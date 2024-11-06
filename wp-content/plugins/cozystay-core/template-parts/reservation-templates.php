<script id="tmpl-loftocean-room-price-details" type="text/template"><?php 
    $base_price_breakdown_status = apply_filters( 'loftocean_room_base_price_breakdown_status', '' );
    $is_always_show = ( 'always-show' == $base_price_breakdown_status );
    $currency = \LoftOcean\get_current_currency(); ?>
    <ul>
        <li class="csf-pd-total-base<?php if ( $is_always_show ) : ?> always-show<?php endif; ?>">
            <div class="csf-pd-label">
                <?php esc_html_e( 'Total Base Price', 'loftocean' ); ?>
                <span class="info-indicator">i</span>
            </div>
            <div class="csf-pd-value"><?php echo $currency[ 'left' ]; ?>{{{ data.totalBasePrice }}}<?php echo $currency[ 'right' ]; ?></div>
            <div class="csf-base-price-breakdown<?php if ( $is_always_show ) : ?> show<?php endif; ?>">
                <div class="breakdown-title">
                    <div class="csf-pd-label"><?php esc_html_e( 'Base Price Breakdown', 'loftocean' ); ?></div>
                    <div class="csf-pd-value">{{{ data.nights }}} <# if ( data.nights > 1 ) { #><?php esc_html_e( 'Nights', 'loftocean' ); ?><# } else { #><?php esc_html_e( 'Night', 'loftocean' ); ?><# } #></div>
                </div>
                <div class="breakdown-main">
                    <ul><#
                    data.rooms.forEach( function( item ) { #>
                        <li>
                            <div class="csf-pd-label">{{{ item.date }}}</div><#
                            if ( item.price ) { #>
                                <div class="csf-pd-value"><#
                                    if ( Number( item.originalPrice ) > Number( item.price ) ) { #><del><?php echo $currency[ 'left' ]; ?>{{{ item.originalPrice }}}<?php echo $currency[ 'right' ]; ?></del> <# } #>
                                    <?php echo $currency[ 'left' ]; ?>{{{ item.price }}}<?php echo $currency[ 'right' ]; ?>
                                </div><#
                            } else { #>
                                <div class="csf-pd-value discounted"><?php esc_html_e( 'Unavailable', 'loftocean' ); ?></div><#
                            } #>
                        </li><#
                    } ); #>
                    </ul>
                </div>
                <div class="breakdown-footer">
                    <div class="csf-pd-label"><?php esc_html_e( 'Total Base Price', 'loftocean' ); ?></div>
                    <div class="csf-pd-value"><?php echo $currency[ 'left' ]; ?>{{{ data.totalBasePrice }}}<?php echo $currency[ 'right' ]; ?></div>
                </div>
            </div>
        </li><#
        if ( data.early_bird_discount ) { #>
            <li>
                <div class="csf-pd-label"><?php esc_html_e( 'Early Bird Discount', 'loftocean' ); ?></div>
                <div class="csf-pd-value discounted">{{{ data.early_bird_discount }}}</div>
            </li><#
        }
        if ( data.last_minute_discount ) { #>
            <li>
                <div class="csf-pd-label"><?php esc_html_e( 'Last Minute Discount', 'loftocean' ); ?></div>
                <div class="csf-pd-value discounted">{{{ data.last_minute_discount }}}</div>
            </li><#
        }
        if ( data.long_stay_discount ) { #>
            <li>
                <div class="csf-pd-label"><?php esc_html_e( 'Long Stay Discount', 'loftocean' ); ?></div>
                <div class="csf-pd-value discounted">{{{ data.long_stay_discount }}}</div>
            </li><#
        }
        if ( data.extraService ) { #>
            <li>
                <div class="csf-pd-label"><?php esc_html_e( 'Extra Services', 'loftocean' ); ?></div>
                <div class="csf-pd-value"><?php echo $currency[ 'left' ]; ?>{{{ data.extraService }}}<?php echo $currency[ 'right' ]; ?></div>
            </li><#
        } #><?php
        $tax_enabled = \LoftOcean\is_tax_enabled();
        $tax_included = ( 'yes' == get_option( 'woocommerce_prices_include_tax' ) );
        $tax_or_vat = $tax_enabled ? \WC()->countries->tax_or_vat() : '';
        $show_tax_detail = $tax_enabled && ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) );
        if ( $tax_enabled && ( ! $tax_included ) ) :
            if ( $show_tax_detail ) : ?><#
                if ( data.taxDetails ) {
                    data.taxDetails.forEach( function( item ) {  #>
                        <li>
                            <div class="csf-pd-label">{{{ item.label }}}</div>
                            <div class="csf-pd-value">{{{ item.tax }}}</div>
                        </li><#
                    } );
                } #><?php
            else : ?><#
                if ( data.tax ) { #>
                    <li>
                        <div class="csf-pd-label"><?php echo esc_html( $tax_or_vat ); ?></div>
                        <div class="csf-pd-value">{{{ data.tax }}}</div>
                    </li><#
                } #><?php
            endif;
        endif; ?>
        <li class="cs-form-price-details-total<?php if ( $tax_enabled ) : ?><# if ( data.tax || data.beforeTax ) { #> with-tax-info<# } #><?php endif; ?>">
            <div class="csf-pd-label"><?php esc_html_e( 'Total', 'loftocean' ); ?></div>
            <div class="csf-pd-value"><?php
                echo $currency[ 'left' ]; ?>{{{ data.totalPrice }}}<?php echo $currency[ 'right' ];
                if ( $tax_enabled ) :
                    if ( $tax_included ) :
                        if ( $show_tax_detail ) : ?><#
                            if ( data.taxDetails ) {
                                var taxText = [];
                                data.taxDetails.forEach( function( item ) {
                                    taxText.push( item.tax + ' ' + item.label );
                                } );
                                if ( taxText.length ) {
                                    taxText = taxText.join( ', ' ); #>
                                    <small class="includes_tax"><?php printf(
                                        // translators: tax details
                                        esc_html__( '(includes %1$s)', 'loftocean' ),
                                        '{{ taxText }}'
                                    ); ?></small><#
                                }
                            } #><?php
                        else : ?><#
                            if ( data.tax ) { #>
                                <small class="includes_tax"><?php printf(
                                    // translators: 1: tax amount 2: tax label
                                    esc_html__( '(includes %1$s %2$s)', 'loftocean' ),
                                    '{{ data.tax }}',
                                    $tax_or_vat
                                ); ?></small><#
                            } #><?php
                        endif;
                    else : ?><#
                        if ( data.beforeTax ) { #>
                            <small class="excludes_tax"><?php printf(
                                // translators: 1/3: currency symbol 2: tax amount
                                esc_html__( '(Total Before %1$s %2$s)', 'loftocean' ),
                                $tax_or_vat,
                                $currency[ 'left' ] . '{{ data.beforeTax }}' . $currency[ 'right' ]
                            ); ?>
                            </small><#
                        } #><?php
                    endif;
                endif; ?>
            </div>
        </li>
    </ul>
</script>

<script id="tmpl-loftocean-room-extra-services" type="text/template">
    <div class="cs-form-group cs-extra-service-group">
        <h5 class="csf-title"><?php esc_html_e( 'Extra Services', 'loftocean' ); ?></h5><#
        var autoMethodLabel = {
            'night': "<?php esc_html_e( ' / Night', 'loftocean' ); ?>",
            'person': "<?php esc_html_e( ' / Person', 'loftocean' ); ?>",
            'night-person': "<?php esc_html_e( ' / Night / Person', 'loftocean' ); ?>",
            'night-room': "<?php esc_html_e( ' / Night / Room', 'loftocean' ); ?>",
            'custom-person-adult': "<?php esc_html_e( ' / Adult', 'loftocean' ); ?>",
            'custom-person-child': "<?php esc_html_e( ' / Child', 'loftocean' ); ?>",
            'custom-night-person-adult': "<?php esc_html_e( ' / Night / Adult', 'loftocean' ); ?>",
            'custom-night-person-child': "<?php esc_html_e( ' / Night / Child', 'loftocean' ); ?>"
        };
        data.services.forEach( function( item ) {
            var price = item.price, method = item.method, autoMethod = item.auto_method, briefText = '',
                serviceID = 'extra_service_' + item.term_id, labelFor = 'extra-service-id-' + item.term_id,
                priceText = data.currency.left + item.display_price + data.currency.right + ( 'fixed' == method ? '' : ( 'auto_custom' == method ? autoMethodLabel[ 'night' ] : '' ) + ' ' + item.custom_price_appendix_text ),
                hasCustomPrice = false, hasAllCustomPrices = false, customAdultPrice = 0, customChildPrice = 0;
            if ( 'auto' == method ) {
                if ( [ 'person', 'night-person' ].includes( autoMethod ) && ( item.custom_adult_price || item.custom_child_price ) ) {
                    var priceArray = [];
                    hasCustomPrice = true;
                    if ( '' !== item.custom_adult_price ) {
                        customAdultPrice = item.custom_adult_price;
                        priceArray.push( data.currency.left + item.display_custom_adult_price + data.currency.right + autoMethodLabel[ 'custom-' + autoMethod + '-adult' ] );
                    }
                    if ( '' !== item.custom_child_price ) {
                        customChildPrice = item.custom_child_price;
                        priceArray.push( data.currency.left + item.display_custom_child_price + data.currency.right + autoMethodLabel[ 'custom-' + autoMethod + '-child' ] );
                    }
                    if ( priceArray.length > 1 ) {
                        hasAllCustomPrices = ( 'night-person' == autoMethod );
                        briefText = priceArray[ 0 ];
                    }
                    priceText = priceArray.join( ', ' );
                } else {
                    priceText = data.currency.left + item.display_price + data.currency.right + autoMethodLabel[ autoMethod ];
                }
            } #>

            <div class="cs-form-field cs-extra-service">
                <div class="field-wrap">
                    <div class="label-checkbox<# if ( item.obligatory ) { #> obligatory<# } #>">
                        <input class="hidden-check extra-service-switcher" type="checkbox" name="extra_service_id[{{{ serviceID }}}]" id="{{{ labelFor }}}" value="{{{ item.term_id }}}"<# if ( item.obligatory ) { #> checked<# } #>>
                        <div class="cs-styled-checkbox"></div>
                        <div class="cs-form-label checkbox-label" ><label for="{{{ labelFor }}}">{{{ item.name }}}</label></div>
                        <div class="hidden-fiedls">
                            <input type="hidden" name="extra_service_price[{{{ serviceID }}}]" value="{{{ price }}}" />
                            <input type="hidden" name="extra_service_calculating_method[{{{ serviceID }}}]" value="{{{ method }}}" />
                            <input type="hidden" name="extra_service_title[{{{ serviceID }}}]" value="{{{ item.name }}}" />
                            <input type="hidden" name="extra_service_price_label[{{{ serviceID }}}]" value="{{{ priceText }}}" /><#
                            if ( 'auto' == method ) { #>
                                <input type="hidden" name="extra_service_auto_calculating_unit[{{{ serviceID }}}]" value="{{{ autoMethod }}}" /><#
                                if ( hasCustomPrice ) { #>
                                    <input type="hidden" name="extra_service_auto_calculating_custom_adult_price[{{{ serviceID }}}]" value="{{{ customAdultPrice }}}" />
                                    <input type="hidden" name="extra_service_auto_calculating_custom_child_price[{{{ serviceID }}}]" value="{{{ customChildPrice }}}" /><#
                                }
                            } #>
                        </div>
                    </div><#
                    if ( [ 'custom', 'auto_custom' ].includes( method ) ) {
                        var minimumQuantity = ( 'undefined' !== typeof item.custom_minimum_quantity ) && ( '' !== item.custom_minimum_quantity ) ? parseInt( item.custom_minimum_quantity ) : '',
                            maximumQuantity = ( 'undefined' !== typeof item.custom_maximum_quantity ) && ( '' !== item.custom_maximum_quantity ) ? parseInt( item.custom_maximum_quantity ) : '',
                            defaultQuantity = 1, dataMin = ' data-min="1"', dataMax = '', attrMin = '', attrMax = '';
                        if ( isNaN( minimumQuantity ) || ( minimumQuantity < 1 ) ) {
                            minimumQuantity = '';
                        } else {
                            defaultQuantity = minimumQuantity;
                            dataMin = ' data-min="' + minimumQuantity + '"';
                            attrMin = ' min="' + minimumQuantity + '"';
                        }
                        if ( isNaN( maximumQuantity ) || ( maximumQuantity < 1 ) ) {
                            maximumQuantity = '';
                        } else {
                            dataMax = ' data-max="' + maximumQuantity + '"';
                            attrMax = ' max="' + maximumQuantity + '"';
                        } #>
                        <div class="price-quantity extra-service-custom-quantity">
                            <div class="cs-form-price">{{{ priceText }}}</div>

                            <div class="cs-form-quantity">
                                <div class="field-wrap">
                                    <div class="field-input-wrap has-dropdown">
                                        <input type="text" name="extra_service_quantity[{{{ serviceID }}}]" value="{{{ defaultQuantity }}}" readonly=""{{{ attrMin }}}{{{ attrMax }}}>
                                    </div>
                                    <div class="csf-dropdown">
                                        <div class="csf-dropdown-item has-dropdown">
                                            <label class="cs-form-label"><?php esc_attr_e( 'Quantity', 'loftocean' ); ?></label>
                                            <div class="quantity cs-quantity">
                                                <label class="screen-reader-text"><?php esc_attr_e( 'Quantity', 'loftocean' ); ?></label>
                                                <button class="minus"></button>
                                                <input type="text" name="extra_service[{{{ serviceID }}}]" value="{{{ defaultQuantity }}}" class="input-text" autocomplete="off" readonly=""{{{ dataMin }}}{{{ dataMax }}}>
                                                <button class="plus"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><#
                    } else if ( 'auto' == method ) { #>
                        <div class="price-quantity"><#
                            if ( hasAllCustomPrices ) { #>
                                <div class="cs-form-price cs-form-price-long">
                                    <div class="cs-form-price-brief">{{{ briefText }}}</div>
                                    <div class="cs-form-price-all">{{{ priceText }}}</div>
                                </div><#
                            } else { #>
                                <div class="cs-form-price">{{{ priceText }}}</div><#
                            } #>
                        </div><#
                    } else { #>
                        <div class="price-quantity">
                            <div class="cs-form-price">{{{ priceText }}}</div>
                        </div><#
                    } #>
                </div>
            </div><#
        } ); #>
    </div>
</script>
