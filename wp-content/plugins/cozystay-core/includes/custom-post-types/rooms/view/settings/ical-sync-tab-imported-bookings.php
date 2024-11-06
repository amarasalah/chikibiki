<div class="cs-tab-content-wrapper" id="loftocean-room-ical-sync-tab-imported-bookings">
    <h2><?php esc_html_e( 'Imported Bookings', 'loftocean' ); ?></h2><?php
    $bookings = apply_filters( 'loftocean_get_all_imported_bookings', array() );
    $bookings_total_count = count( $bookings );
    $pages = ceil( $bookings_total_count / $loftocean_ical_settings_ppp );
    $text_size = ( $pages > 999 ? 4 : ( $pages > 99 ? 3 : ( $pages > 9 ? 2 : 1 ) ) );
    $booking_base_url = $loftocean_ical_settings_url_base . 'imported-bookings&current_page=';
    $current_page = isset( $_REQUEST[ 'current_page' ] ) && is_numeric( $_REQUEST[ 'current_page' ] ) ? absint( $_REQUEST[ 'current_page' ] ) : 1;
    $current_page = ( $current_page < 1 ) ? 1 : $current_page;
    $current_page = ( $current_page > $pages ) ? $pages : $current_page;

    ob_start(); ?>
        <div class="tablenav-pages">
            <span class="displaying-num"><?php echo $bookings_total_count . ( $bookings_total_count > 1 ? esc_html__( ' items', 'loftocean' ) : esc_html__( ' item', 'loftocean' ) ); ?></span><?php
            if ( $pages > 1 ) : ?>
                <span class="pagination-links"><?php
                    if ( $current_page > 1 ) : ?>
                        <a class="first-page button" href="<?php echo $booking_base_url . '1'; ?>">
                            <span class="screen-reader-text"><?php esc_html_e( 'First page', 'loftocean' ); ?></span>
                            <span aria-hidden="true">«</span>
                        </a>
                        <a class="prev-page button" href="<?php echo $booking_base_url . ( $current_page - 1 ); ?>">
                            <span class="screen-reader-text"><?php esc_html_e( 'Previous page', 'loftocean' ); ?></span>
                            <span aria-hidden="true">‹</span>
                        </a><?php
                    else : ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span><?php
                    endif; ?>
                    <span class="paging-input">
                        <label for="current-page-selector" class="screen-reader-text"><?php esc_html_e( 'Current Page', 'loftocean' ); ?></label>
                        <input class="current-page current-page-selector" type="text" name="paged" value="<?php echo $current_page; ?>" size="<?php echo $text_size; ?>" data-url-base="<?php echo $booking_base_url; ?>">
                        <span class="tablenav-paging-text"><?php esc_html_e( ' of ', 'loftocean' ); ?><span class="total-pages"><?php echo $pages; ?></span></span>
                    </span><?php
                    if ( $current_page < $pages ) : ?>
                        <a class="next-page button" href="<?php echo $booking_base_url . ( $current_page + 1 ); ?>">
                            <span class="screen-reader-text"><?php esc_html_e( 'Next page', 'loftocean' ); ?></span>
                            <span aria-hidden="true">›</span>
                        </a>
                        <a class="last-page button" href="<?php echo $booking_base_url . $pages; ?>">
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
    <div class="room-imported-bookings-list">
        <div class="tablenav top"><?php echo $page_navs; ?></div>
        <table class="wp-list-table widefat fixed striped table-view-list posts">
            <thead>
                <tr>
                    <th scope="col" id="order-id" class="manage-column column-order-id column-primary" abbr="<?php esc_html_e( 'Order ID', 'loftocean' ); ?>"><?php esc_html_e( 'ID', 'loftocean' ); ?></th>
                    <th scope="col" id="imported-date" class="manage-column column-imported-date"><?php esc_html_e( 'Imported Date', 'loftocean' ); ?></th>
                    <th scope="col" id="source" class="manage-column column-source"><?php esc_html_e( 'Source', 'loftocean' ); ?></th>
                </tr>
            </thead>

            <tbody id="the-list"><?php
                if ( \LoftOcean\is_valid_array( $bookings ) ) :
                    $date_format = get_option( 'date_format' );
                    $date_format = empty( $date_format ) ? 'M d, Y' : $date_format;
                    $bookings_start_index = ( $current_page - 1 ) * $loftocean_ical_settings_ppp;
                    $bookings_end_index = $current_page * $loftocean_ical_settings_ppp;
                    if ( $bookings_end_index > $bookings_total_count ) {
                        $bookings_end_index = $bookings_total_count;
                    }
                    for ( $i = $bookings_start_index; $i < $bookings_end_index; $i ++ ) :
                        $item_id = $bookings[ $i ];
                        $source_name = get_post_meta( $item_id, 'source_title', true );
                        $imported_date = get_post_meta( $item_id, 'date_imported', true );
                        $date_label = date( $date_format, $imported_date ); ?>
                        <tr>
                            <td class="order_number column-order-id column-primary" data-colname="<?php esc_html_e( 'Order ID', 'loftocean' ); ?>">
                                <a href="#" class="order-view" data-order-id="<?php echo esc_attr( $item_id ); ?>"><strong><?php echo esc_html( $item_id ); ?></strong></a>
                                <button type="button" class="toggle-row"><span class="screen-reader-text"><?php esc_html_e( 'Show more details', 'loftocean' ); ?></span></button>
                            </td>
                            <td class="imported-date column-imported-date" data-colname="<?php esc_html_e( 'Imported Date', 'loftocean' ); ?>">
                                <time datetime="<?php echo esc_attr( date( 'Y-m-dTG:i:s', $imported_date ) ); ?>" title="<?php echo esc_attr( date( 'F j, Y, g:i a', $imported_date ) ); ?>"><?php echo esc_html( $date_label ); ?></time>
                            </td>
                            <td class="column-source" data-colname="<?php esc_html_e( 'Source', 'loftocean' ); ?>"><?php echo esc_html( $source_name ); ?></td>
                        </tr><?php
                    endfor;
                else : ?>
                    <tr class="no-items"><td class="colspanchange" colspan="3"><?php esc_html_e( 'No items found.', 'loftocean' ); ?></td></tr><?php
                endif; ?>
            </tbody>
        </table>
        <div class="tablenav bottom"><?php echo $page_navs; ?></div>
    </div>
    <div class="loftocean-modal imported-order">
        <div class="loftocean-modal-bg-overlay"></div>
        <div class="loftocean-modal-main">
            <div class="loftocean-modal-header">
                <h2 class="booking-title"><?php esc_html_e( 'Order', 'loftocean' ); ?> #<span class="order-number"></span></h2>
                <button class="modal-close">
                    <span class="screen-reader-text"><?php esc_html_e( 'Close popup panel', 'loftocean' ); ?></span>
                </button>
            </div>
            <div class="loftocean-modal-content">
                <div class="cs-order-preview"></div>
            </div>
        </div>
    </div>
    <script id="tmpl-loftocean-room-order-details" type="text/html"><#
        if ( data.roomTitle ) { #>
            <h3 class="post-title">
                <a target="_blank" href="{{{ data.roomURL }}}">{{{ data.roomTitle }}}</a>
            </h3>
            <table>
                <tbody>
                    <tr>
                        <th><?php esc_html_e( 'ID', 'loftocean' ); ?></th>
                        <td>{{{ data.order_id }}}</td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Check-in Date', 'loftocean' ); ?></th>
                        <td>{{{ data.detail.checkIn }}}</td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Check-out Date', 'loftocean' ); ?></th>
                        <td>{{{ data.detail.checkOut }}}</td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'UID', 'loftocean' ); ?></th>
                        <td>{{{ data.detail.uid }}}</td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Summary', 'loftocean' ); ?></th>
                        <td>{{{ data.detail.summary }}}</td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Description', 'loftocean' ); ?></th>
                        <td>{{{ data.detail.description }}}</td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Source', 'loftocean' ); ?></th>
                        <td>{{{ data.detail.prodid }}}</td>
                    </tr>
                </tbody>
            </table><#
        } else { #>
            <p class="error-message"><?php esc_html_e( 'No imported booking found. Please reload the page to check again.', 'loftocean' ); ?></p><#
        } #>
    </script>
</div>
