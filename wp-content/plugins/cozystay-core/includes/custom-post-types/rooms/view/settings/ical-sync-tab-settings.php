<div class="cs-tab-content-wrapper" id="loftocean-room-ical-sync-tab-settings">
    <h2><?php esc_html_e( 'iCal Auto Sync Schedules', 'loftocean' ); ?></h2>
    <form id="room-ical-form" method="post" action="<?php echo esc_url( admin_url( 'edit.php?post_type=loftocean_room&page=loftocean_room_ical_sync_settings' ) ); ?>"><?php
        $settings = apply_filters( 'loftocean_ical_cron_get_settings', array() ); ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th><?php esc_html_e( 'Enable auto sync', 'loftocean' ); ?></th>
                    <td>
                        <label><input type="checkbox" name="enable_auto_sync" value="on" <?php checked( 'on', $settings[ 'enable_auto_sync' ] ); ?>><?php esc_html_e( 'Enable automatic synchronization of external calendars', 'loftocean' ); ?></label>
                    </td>
                </tr>
                <tr class="loftocean-auto-sync-interval-wrapper"<?php if ( empty( $settings[ 'enable_auto_sync' ] ) ) : ?> style="display: none;"<?php endif; ?>>
                    <th><?php esc_html_e( 'Interval', 'loftocean' ); ?></th>
                    <td>
                        <select id="loftocean-auto-update-interval" class="loftocean-auto-update-interval" name="auto_sync_interval"><?php
                            $update_interval_options = array(
                                'loftocean_15mins' => esc_html__( 'Every 15 minutes', 'loftocean' ),
                                'loftocean_30mins' => esc_html__( 'Every 30 minutes', 'loftocean' ),
                                'loftocean_1hour' => esc_html__( 'Every hour', 'loftocean' ),
                                'loftocean_2hours' => esc_html__( 'Every 2 hours', 'loftocean' ),
                                'loftocean_3hours' => esc_html__( 'Every 3 hours', 'loftocean' ),
                                'loftocean_6hours' => esc_html__( 'Every 6 hours', 'loftocean' ),
                                'loftocean_12hours' => esc_html__( 'Every 12 hours', 'loftocean' ),
                                'loftocean_24hours' => esc_html__( 'Every 24 hours', 'loftocean' )
                            );
                            foreach( $update_interval_options as $value => $label ) : ?>
                                <option value="<?php echo $value; ?>" <?php selected( $value, $settings[ 'auto_sync_interval' ] ); ?>><?php echo $label; ?></option><?php
                            endforeach; ?>
                        </select>
                        <span class="auto-sync-interval-option-wrapper"<?php if ( 'loftocean_24hours' != $settings[ 'auto_sync_interval' ] ) : ?> style="display: none;"<?php endif; ?>>
                            <select id="loftocean-auto-update-time" class="loftocean-auto-update-time" name="auto_sync_interval_time"><?php
                                $auto_sync_time_options = array(
                                    '1' => '1:00',
                                    '2' => '2:00',
                                    '3' => '3:00',
                                    '4' => '4:00',
                                    '5' => '5:00',
                                    '6' => '6:00',
                                    '7' => '7:00',
                                    '8' => '8:00',
                                    '9' => '9:00',
                                    '10' => '10:00',
                                    '11' => '11:00',
                                    '0' => '12:00'
                                );
                                foreach( $auto_sync_time_options as $value => $label ) : ?>
                                    <option value="<?php echo $value; ?>" <?php selected( $value, $settings[ 'auto_sync_interval_time' ] ); ?>><?php echo $label; ?></option><?php
                                endforeach; ?>
                            </select>
                            <select id="loftocean-auto-update-apm" class="loftocean-auto-update-apm" name="auto_sync_interval_apm">
                                <option value="am" <?php selected( 'am', $settings[ 'auto_sync_interval_apm' ] ); ?>>AM</option>
                                <option value="pm" <?php selected( 'pm', $settings[ 'auto_sync_interval_apm' ] ); ?>>PM</option>
                            </select>
                            <span class="description"><?php esc_html_e( 'Based on the timezone set by this WordPress site.', 'loftocean' ); ?></span>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        <h2><?php esc_html_e( 'Data Clearing', 'loftocean' ); ?></h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th><?php esc_html_e( 'Automatically clear logs', 'loftocean' ); ?></th>
                    <td>
                        <select id="loftocean-auto-clear-log-interval" class="loftocean-auto-clear-log-interval" name="auto_clear_log_interval"><?php
                            $auto_clear_log_interval_options = array(
                                '1day' => esc_html__( 'Older than 1 day', 'loftocean' ),
                                '3days' => esc_html__( 'Older than 3 days', 'loftocean' ),
                                '7days' => esc_html__( 'Older than 7 days', 'loftocean' ),
                                '14days' => esc_html__( 'Older than 14 days', 'loftocean' ),
                                '30days' => esc_html__( 'Older than 30 days', 'loftocean' ),
                                '60days' => esc_html__( 'Older than 60 days', 'loftocean' ),
                                'never' => esc_html__( 'Never', 'loftocean' )
                            );
                            foreach( $auto_clear_log_interval_options as $value => $label ) : ?>
                                <option value="<?php echo $value; ?>" <?php selected( $value, $settings[ 'auto_clear_log_interval' ] ); ?>><?php echo $label; ?></option><?php
                            endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Automatically clear old imported bookings', 'loftocean' ); ?></th>
                    <td>
                        <select id="loftocean-auto-clear-interval" class="loftocean-auto-clear-interval" name="auto_clear_old_imported_bookings_interval"><?php
                            $auto_clear_bookings_interval_options = array(
                                '3days' => esc_html__( 'With check-out dates older than 3 days', 'loftocean' ),
                                '7days' => esc_html__( 'With check-out dates older than 7 days', 'loftocean' ),
                                '14days' => esc_html__( 'With check-out dates older than 14 days', 'loftocean' ),
                                '30days' => esc_html__( 'With check-out dates older than 30 days', 'loftocean' ),
                                '60days' => esc_html__( 'With check-out dates older than 60 days', 'loftocean' ),
                                'never' => esc_html__( 'Never', 'loftocean' )
                            );
                            foreach( $auto_clear_bookings_interval_options as $value => $label ) : ?>
                                <option value="<?php echo $value; ?>" <?php selected( $value, $settings[ 'auto_clear_old_imported_bookings_interval' ] ); ?>><?php echo $label; ?></option><?php
                            endforeach; ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th><?php esc_html_e( 'For external calendar links that no longer exist', 'loftocean' ); ?></th>
                    <td>
                        <button class="button loftocean-clear-booking-records cs-spinner-button"><?php esc_html_e( 'Delete Booking Records', 'loftocean' ); ?></button>
                        <p class="description"><?php esc_html_e( 'Have you deleted some of the external calendars you added?', 'loftocean' ); ?>
                            <br> <?php esc_html_e( 'If you need to delete all old booking records imported through links that no longer exist, click the button above.', 'loftocean' ); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <button name="" class="button-primary" type="submit" value="<?php esc_attr_e( 'Save changes', 'loftocean' ); ?>"><?php esc_html_e( 'Save changes', 'loftocean' ); ?></button>
            <input type="hidden" name="page" value="loftocean_room_ical_sync_settings" />
            <input type="hidden" name="post_type" value="loftocean_room" />
            <input type="hidden" name="active_tab" value="settings" />
            <input type="hidden" name="loftocean_ical_sync_settings_nonce" value="<?php echo wp_create_nonce( 'loftocean_ical_sync_nonce' ); ?>" />
        </p>
    </form>
</div>
