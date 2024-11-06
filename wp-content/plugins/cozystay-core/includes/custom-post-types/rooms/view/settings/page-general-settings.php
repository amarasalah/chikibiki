<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Room & Booking Settings', 'loftocean' ); ?></h1>
    <hr class="wp-header-end">
    <div class="setting-content-container"><?php
        $adult_age_description = get_option( 'loftocean_adult_age_description', '' );
        $child_age_description = get_option( 'loftocean_child_age_description', '' );
        $guest_use_plural_label_when_zero = get_option( 'loftocean_guest_use_plural_label_when_zero', 'on' );

        $titles = array( 'guests' => esc_html__( 'Guests', 'loftocean' ), 'advanced' => esc_html__( 'Advanced', 'loftocean' ) ); 
        $current_active_tab = 'guests';
        if ( isset( $_REQUEST[ 'loftocean_room_settings_activa_tab' ] ) && in_array( $_REQUEST[ 'loftocean_room_settings_activa_tab' ], array_keys( $titles ) ) ) { 
            $current_active_tab = $_REQUEST[ 'loftocean_room_settings_activa_tab' ];
        } ?>
        <div id="cs-dashboard-tabs-wrapper" class="nav-tab-wrapper cs-nav-tab-wrapper"><?php
            foreach ( $titles as $id => $label ) :
                $title_classes = array( 'nav-tab' );
                ( $current_active_tab == $id ) ? array_push( $title_classes, 'nav-tab-active' ) : '';
                $id = 'tab-' . $id; ?>
                <a id="cs-dashboard-<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( implode( ' ', $title_classes ) ); ?>" href="#<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></a><?php
            endforeach; ?>
        </div>
        <form id="room-advanced-settings" method="POST" action="<?php echo esc_url( admin_url( 'edit.php?post_type=loftocean_room&page=loftocean_room_general_settings' ) ); ?>">
            <div class="cs-tab-content-wrapper<?php if ( 'guests' != $current_active_tab ) : ?> hidden<?php endif; ?>" id="tab-guests-content">
                <h2><?php esc_html_e( 'Guest Age Policy', 'loftocean' ); ?></h2>

                <div id="cs-setting-description">
                    <p><?php printf( 
                        // translators: 1/2 html tag
                        esc_html__( 'Here you can fill in %1$soptional%2$s descriptions for each age groups of guests. If filled in, it will be displayed in the dropdown of the room search/booking form.', 'loftocean' ),
                        '<strong>',
                        '</strong>'
                    ); ?></p>
                </div>

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th><?php esc_html_e( 'Adults Age Description', 'loftocean' ); ?></th>
                            <td>
                                <input type="text" name="loftocean_adult_age_description" value="<?php echo esc_attr( $adult_age_description ); ?>">
                                <span class="description"><?php esc_html_e( 'e.g. Age 18+', 'loftocean' ); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Children Age Description', 'loftocean' ); ?></th>
                            <td>
                                <input type="text" name="loftocean_child_age_description" value="<?php echo esc_attr( $child_age_description ); ?>">
                                <span class="description"><?php esc_html_e( 'e.g. Ages 0-17', 'loftocean' ); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Use Plural Label when Guest Number is 0', 'loftocean' ); ?></th>
                            <td>
                                <input type="checkbox" id="loftocean_guest_use_plural_label_when_zero" name="loftocean_guest_use_plural_label_when_zero" value="on" <?php checked( 'on', $guest_use_plural_label_when_zero ); ?>>
                                <label for="loftocean_guest_use_plural_label_when_zero"><?php esc_html_e( 'When the number of guests (adults/children) is 0, the noun after 0 is displayed as plural', 'loftocean' ); ?></label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="cs-tab-content-wrapper<?php if ( 'advanced' != $current_active_tab ) : ?> hidden<?php endif; ?>" id="tab-advanced-content">
                <table class="form-table">
                    <tbody>
                        <tr class="loftocean-room-regenerate-orders">
                            <th><?php esc_html_e( 'Sync Room Order Data', 'loftocean' ); ?></th>
                            <td>
                                <div class="multi-items-wrapper">
                                    <button class="button cs-spinner-button"><?php esc_html_e( 'Sync Room Order Data', 'loftocean' ); ?></button>
                                    <p class="message" style="display: none;"><?php esc_html_e( 'Failed. Please try again late.', 'loftocean' ); ?></p>
                                </div>

                                <p class="description"><?php esc_html_e( 'The number of remaining rooms in different language versions is automatically synchronized. You can also manually sync it by clicking on the sync button.', 'loftocean' ); ?></p>
                            </td>
                        </tr><?php
                        if ( apply_filters( 'loftocean_multilingual_website_enabled', false ) ) : ?>
                        <tr class="loftocean-room-reset-facilities">
                            <th><?php esc_html_e( 'Reset Room Facilities', 'loftocean' ); ?></th>
                            <td>
                                <div class="multi-items-wrapper">
                                    <button class="button cs-spinner-button"><?php esc_html_e( 'Reset Room Facilities', 'loftocean' ); ?></button>
                                    <p class="message" style="display: none;"><?php esc_html_e( 'Failed. Please try again late.', 'loftocean' ); ?></p>
                                </div>
                                <p class="description"><?php esc_html_e( 'Repeatedly changing settings of multilingual plugins may result in room facilities being set incorrectly. Click this button to reset room facilities.', 'loftocean' ); ?></p>
                            </td>
                        </tr><?php
                        endif;
                        $current_weekend_days = get_option( 'loftocean_room_weekend_days_setting', array( 'day5', 'day6' ) );
                        $options = array(
                            'day1' => esc_html__( 'Mondays', 'loftocean' ),
                            'day2' => esc_html__( 'Tuesdays', 'loftocean' ),
                            'day3' => esc_html__( 'Wednesdays', 'loftocean' ),
                            'day4' => esc_html__( 'Thursdays', 'loftocean' ),
                            'day5' => esc_html__( 'Fridays', 'loftocean' ),
                            'day6' => esc_html__( 'Saturdays', 'loftocean' ),
                            'day7' => esc_html__( 'Sundays', 'loftocean' )
                        ); ?>
                        <tr>
                            <th><?php esc_html_e( 'Set Weekend Days', 'loftocean' ); ?></th>
                            <td>
                                <div class="multi-checkboxes"><?php
                                    if ( ! \LoftOcean\is_valid_array( $current_weekend_days ) ) {
                                        $current_weekend_days = array();
                                    }
                                    foreach( $options as $day => $label ) : ?>                              
                                        <div class="checkbox-item">
                                            <input id="weekend-<?php echo esc_attr( $day ); ?>" name="loftocean_weekend_days_setting[]" type="checkbox" value="<?php echo esc_attr( $day ); ?>"<?php if ( in_array( $day, $current_weekend_days ) ) : ?> checked<?php endif; ?>>
                                            <label for="weekend-<?php echo esc_attr( $day ); ?>"><?php echo esc_html( $label ); ?></label>
                                        </div><?php
                                    endforeach; ?>
                                </div>
                                <p class="description"><?php printf( 
                                    // translators: 1/2 html tag
                                    esc_html__( 'This determines which nights your %1$sWeekend Prices%2$s will be in effect.', 'loftocean' ),
                                    '<strong>', 
                                    '</strong>'
                                    ); ?>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="submit">
                <?php wp_nonce_field( 'loftocean_room_advanced_settings', 'loftocean_room_advanced_setting_nonce' ); ?>
                <button name="save" class="button-primary" type="submit" value="Save changes"><?php esc_html_e( 'Save changes', 'loftocean' ); ?></button>
                <input type="hidden" name="loftocean_room_settings_activa_tab" value="<?php echo esc_attr( $current_active_tab ); ?>">
            </p>
        </form>
    </div>
</div>
