<div class="cs-tab-content-wrapper" id="loftocean-room-ical-sync-tab-calendars">
    <h2><?php esc_html_e( 'Sync Calendars', 'loftocean' ); ?></h2><?php
    $rooms = apply_filters( 'loftocean_get_all_rooms', array() );
    $rooms_total_count = count( $rooms );
    $pages = ceil( $rooms_total_count / $loftocean_ical_settings_ppp );
    $text_size = ( $pages > 999 ? 4 : ( $pages > 99 ? 3 : ( $pages > 9 ? 2 : 1 ) ) );
    $rooms_base_url = $loftocean_ical_settings_url_base . 'calendars&current_page=';
    $current_page = isset( $_REQUEST[ 'current_page' ] ) && is_numeric( $_REQUEST[ 'current_page' ] ) ? absint( $_REQUEST[ 'current_page' ] ) : 1;
    $current_page = ( $current_page < 1 ) ? 1 : $current_page;
    $current_page = ( $current_page > $pages ) ? $pages : $current_page;

    ob_start(); ?>
        <div class="alignleft actions bulkactions">
            <label class="screen-reader-text"><?php esc_html_e( 'Select bulk action', 'loftocean' ); ?></label>
            <select name="action" class="bulk-action-selector">
                <option value=""><?php esc_html_e( 'Bulk actions', 'loftocean' ); ?></option>
                <option value="sync" class="hide-if-no-js"><?php esc_html_e( 'Sync Calendars', 'loftocean' ); ?></option>
            </select>
            <input type="submit" class="button action doaction" value="<?php esc_attr_e( 'Apply', 'loftocean' ); ?>">
        </div>

        <div class="alignleft actions">
            <a href="#" class="button action loftocean-sync-all"><?php esc_html_e( 'Sync All External Calendars', 'loftocean' ); ?></a>
        </div>
        <div class="tablenav-pages">
            <span class="displaying-num"><?php echo $rooms_total_count . ( $rooms_total_count > 1 ? esc_html__( ' items', 'loftocean' ) : esc_html__( ' item', 'loftocean' ) ); ?></span><?php
            if ( $pages > 1 ) : ?>
                <span class="pagination-links"><?php
                    if ( $current_page > 1 ) : ?>
                        <a class="first-page button" href="<?php echo $rooms_base_url . '1'; ?>">
                            <span class="screen-reader-text"><?php esc_html_e( 'First page', 'loftocean' ); ?></span>
                            <span aria-hidden="true">«</span>
                        </a>
                        <a class="prev-page button" href="<?php echo $rooms_base_url . ( $current_page - 1 ); ?>">
                            <span class="screen-reader-text"><?php esc_html_e( 'Previous page', 'loftocean' ); ?></span>
                            <span aria-hidden="true">‹</span>
                        </a><?php
                    else : ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span><?php
                    endif; ?>
                    <span class="paging-input">
                        <label class="screen-reader-text"><?php esc_html_e( 'Current Page', 'loftocean' ); ?></label>
                        <input class="current-page current-page-selector" type="text" name="paged" value="<?php echo $current_page; ?>" size="<?php echo $text_size; ?>" data-url-base="<?php echo $rooms_base_url; ?>">
                        <span class="tablenav-paging-text"><?php esc_html_e( ' of ', 'loftocean' ); ?><span class="total-pages"><?php echo $pages; ?></span></span>
                    </span><?php
                    if ( $current_page < $pages ) : ?>
                        <a class="next-page button" href="<?php echo $rooms_base_url . ( $current_page + 1 ); ?>">
                            <span class="screen-reader-text"><?php esc_html_e( 'Next page', 'loftocean' ); ?></span>
                            <span aria-hidden="true">›</span>
                        </a>
                        <a class="last-page button" href="<?php echo $rooms_base_url . $pages; ?>">
                            <span class="screen-reader-text"><?php esc_html_e( 'Last page', 'loftocean' ); ?></span>
                            <span aria-hidden="true">»</span>
                        </a><?php
                    else : ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span><?php
                    endif; ?>
                </span><?php
            endif; ?>
        </div><?php
    $page_navs = ob_get_clean(); ?>
    <div class="room-calendar-list">
        <div class="tablenav top"><?php echo $page_navs; ?></div>
        <table class="wp-list-table widefat fixed striped table-view-list posts">
            <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column">
                        <input id="cb-select-all-1" type="checkbox">
                        <label for="cb-select-all-1"><span class="screen-reader-text"><?php esc_html_e( 'Select All', 'loftocean' ); ?></span></label>
                    </td>
                    <th scope="col" id="title" class="manage-column column-title column-primary" abbr="<?php esc_html_e( 'Title', 'loftocean' ); ?>">Title</th>
                    <th scope="col" id="" class="manage-column"><?php esc_html_e( 'Import', 'loftocean' ); ?></th>
                    <th scope="col" id="" class="manage-column"><?php esc_html_e( 'Export', 'loftocean' ); ?></th>
                </tr>
            </thead>

            <tbody id="the-list"><?php
                if ( \LoftOcean\is_valid_array( $rooms ) ) :
                    $rooms_start_index = ( $current_page - 1 ) * $loftocean_ical_settings_ppp;
                    $rooms_end_index = $current_page * $loftocean_ical_settings_ppp;
                    if ( $rooms_end_index > $rooms_total_count ) {
                        $rooms_end_index = $rooms_total_count;
                    }
                    for ( $i = $rooms_start_index; $i < $rooms_end_index; $i ++ ) :
                        $room_id = $rooms[ $i ];
                        $uuid = \LoftOcean\Room\Relationship_Tools::get_room_relationship( $room_id );
                        $room_title = get_post_field( 'post_title', $room_id ); ?>
                        <tr>
                            <th scope="row" class="check-column">
                                <input id="cb-select-<?php echo esc_attr( $room_id ); ?>" type="checkbox" name="post[]" value="<?php echo esc_attr( $room_id ); ?>">
                                <label for="cb-select-<?php echo esc_attr( $room_id ); ?>">
                                    <span class="screen-reader-text"><?php echo esc_html( $room_title ); ?></span>
                                </label>
                            </th>
                            <td class="column-title column-primary page-title title" data-colname="<?php esc_html_e( 'Title', 'loftocean' ); ?>">
                                <strong>
                                    <a class="row-title" href="<?php echo admin_url( 'post.php?post=' . $room_id . '&amp;action=edit' ); ?>" target="_blank"><?php echo esc_html( $room_title ); ?></a>
                                </strong>
                                <button type="button" class="toggle-row"><span class="screen-reader-text"><?php esc_html_e( 'Show more details', 'loftocean' ); ?></span></button>
                            </td>
                            <td class="column-import room-<?php echo esc_attr( $room_id ); ?>" data-room-id="<?php echo esc_attr( $room_id ); ?>" data-uuid="<?php echo $uuid; ?>">
                                <div class="added-calendars hidden"></div>
                                <button class="button loftocean-ical-calendar-import"><?php esc_html_e( 'Import Calendar', 'loftocean' ); ?></button>
                            </td>
                            <td class="column-export">
                                <button class="button loftocean-feed-url-export" data-room-id="<?php echo esc_attr( $room_id ); ?>"><?php esc_html_e( 'Export Calendar', 'loftocean' ); ?></button>
                            </td>
                        </tr><?php
                    endfor;
                else : ?>
                    <tr class="no-items"><td class="colspanchange" colspan="3"><?php esc_html_e( 'No items found.', 'loftocean' ); ?></td></tr><?php
                endif; ?>
            </tbody>
        </table>
        <div class="tablenav bottom"><?php echo $page_navs; ?></div>
    </div>
    <div class="loftocean-modal" id="loftocean-ical-export-calendar">
        <div class="loftocean-modal-bg-overlay"></div>
        <div class="loftocean-modal-main">
            <div class="loftocean-modal-header">
                <h2><?php esc_html_e( 'Export Calendar', 'loftocean' ); ?></h2>
                <button class="modal-close">
                    <span class="screen-reader-text"><?php esc_html_e( 'Close popup panel', 'loftocean' ); ?></span>
                </button>
            </div>
            <div class="loftocean-modal-content">
                <p><?php esc_html_e( 'Copy and paste the link into other iCal applications.', 'loftocean' ); ?></p>
                <p>
                    <input name="" class="loftocean-room-ical-feed-url" type="text" value="">
                </p>
                <p class="copy-link-button">
                    <a class="button button-primary" href="#"><?php esc_html_e( 'Click to Copy Link', 'loftocean' ); ?></a>
                    <span class="copy-link-msg" style="display: none;"><?php esc_html_e( 'Link copied to clipboard.', 'loftocean' ); ?></span>
                </p>
            </div>

        </div>
    </div>
    <div class="loftocean-modal" id="loftocean-ical-import-calendar">
        <div class="loftocean-modal-bg-overlay"></div>
        <div class="loftocean-modal-main">
            <div class="loftocean-modal-header">
                <h2><?php esc_html_e( 'Import a New Calendar', 'loftocean' ); ?></h2>
                <button class="modal-close">
                    <span class="screen-reader-text"><?php esc_html_e( 'Close popup panel', 'loftocean' ); ?></span>
                </button>
            </div>
            <div class="loftocean-modal-content">
                <p><?php esc_html_e( 'Import other calendars that use the iCal format, such as Google Calendar, booking.com, Airbnb, and etc.', 'loftocean' ); ?></p>
                <p>
                    <label><?php esc_html_e( 'Paste your calendar link (URL) below', 'loftocean' ); ?> *</label>
                    <input class="loftocean-import-calendar-url" type="text" value="" placeholder="<?php esc_attr_e( 'Your iCal Link', 'loftocean' ); ?>">
                </p>
                <p>
                    <label><?php esc_html_e( 'Name the calendar', 'loftocean' ); ?> *</label>
                    <input class="loftocean-import-calendar-name" type="text" value="" placeholder="<?php esc_attr_e( 'Custom Calendar Name', 'loftocean' ); ?>">
                </p>
                <p>
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Add Calendar', 'loftocean' ); ?>" data-label="<?php esc_attr_e( 'Add Calendar', 'loftocean' ); ?>" data-update-text="<?php esc_attr_e( 'Adding ...', 'loftocean' ); ?>">
                    <input type="hidden" class="loftocean-import-calendar-room-id" value="" />
                    <input type="hidden" class="loftocean-import-calendar-index" value="" />
                </p>
                <div class="error-message-wrapper">
                    <p class="field-required" style="display: none;"><?php esc_html_e( 'All fields are required.', 'loftocean' ); ?></p>
                    <p class="sync-source-existing" style="display: none;"><?php esc_html_e( 'The sync source already existing.', 'loftocean' ); ?></p>
                    <p class="sync-server-error" style="display: none;"><?php esc_html_e( 'Please try again late.', 'loftocean' ); ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="loftocean-modal" id="loftocean-delete-sync-source-item">
        <div class="loftocean-modal-bg-overlay"></div>
        <div class="loftocean-modal-main">
            <div class="loftocean-modal-header">
                <h2><?php esc_html_e( 'Delete Calendar', 'loftocean' ); ?></h2>
                <button class="modal-close">
                    <span class="screen-reader-text"><?php esc_html_e( 'Close popup panel', 'loftocean' ); ?></span>
                </button>
            </div>
            <div class="loftocean-modal-content">
                <p><?php esc_html_e( 'Delete this external calendar and stop syncing new data from the source.', 'loftocean' ); ?></p>
                <p>
                    <label><input name="" class="loftocean-remove-imported-booking" type="checkbox" value="on"><?php esc_html_e( 'Also delete all booking records imported via this link', 'loftocean' ); ?></label>
                </p>
                <p>
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e( 'Delete Calendar', 'loftocean' ); ?>" data-label="<?php esc_html_e( 'Delete Calendar', 'loftocean' ); ?>" data-update-text="<?php esc_html_e( 'Deleting ...', 'loftocean' ); ?>">
                    <input type="hidden" class="loftocean-remove-calendar-room-id" value="" />
                    <input type="hidden" class="loftocean-remove-calendar-index" value="" />
                    <input type="hidden" class="loftocean-remove-calendar-url-base64" value="" />
                </p>
            </div>

        </div>
    </div>
    <div class="loftocean-modal" id="loftocean-sync-calendars">
        <div class="loftocean-modal-bg-overlay"></div>
        <div class="loftocean-modal-main">
            <div class="loftocean-modal-header">
                <h2><?php esc_html_e( 'Syncing External Calendars', 'loftocean' ); ?></h2>
                <button class="modal-close">
                    <span class="screen-reader-text"><?php esc_html_e( 'Close popup panel', 'loftocean' ); ?></span>
                </button>
            </div>

            <div class="loftocean-modal-content">
                <p class="processing"><?php esc_html_e( 'Synchronizing data for external calendars, please wait and do not close this window until synchronization is complete.', 'loftocean' ); ?></p>
                <p class="processed" style="display: none;"><?php printf(
                    // translators: 1/2 html tag
                    esc_html__( 'Synchronization is complete. You can close this window. To view the logs please visit the %1$sLogs%2$s page.', 'loftocean' ),
                    '<a href="' . $loftocean_ical_settings_url_base . 'logs" class="visit-log-tab">',
                    '</a>'
                ); ?></p>
                <div class="loftocean-modal-loading-bar">
                    <span class="loading-bar-wrapper">
                        <span class="bar"><span class="load"></span></span>
                        <span class="load-count"></span>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <script id="tmpl-loftocean-import-calendar-item" type="text/html"><#
        if ( ( 'object' == typeof( data.list ) ) && Object.keys( data.list ).length ) {
            var itemIndex = data.index;
            Object.keys( data.list ).forEach( function( index ) {
                var item = data.list[ index ];
                if ( item.urlBase64 && item.title ) { #>
                    <div class="added-calendar-item" data-sync-url-base64="{{{ item.urlBase64 }}}" data-index="{{{ itemIndex ++ }}}">
                        <div class="added-calendar-name" >
                            <a href="#" class="edit-item">{{{ item.title }}}</a><#
                            if ( item.lastSyncTime ) { #>
                                <span class="added-calendar-info" data-last-sync-time="{{{ item.lastSyncTime }}}">{{{ item.lastSyncTimePassed }}}</span><#
                            } else { #>
                                <span class="added-calendar-info" data-last-sync-time=""></span><#
                            } #>
                        </div>
                        <div class="added-calendar-actions">
                            <div class="added-calendar-sync-status">
                                <div class="sync-status-icon"></div>
                            </div>
                            <button class="button button-primary sync-button"><?php esc_html_e( 'Sync', 'loftocean' ); ?></button>
                            <button class="button delete-button"><?php esc_html_e( 'Delete', 'loftocean' ); ?></button>
                        </div>
                    </div><#
                }
            } );
        } #>
    </script>
</div>
