<div class="cs-tab-content-wrapper" id="loftocean-room-ical-sync-tab-logs">
    <h2><?php esc_html_e( 'Logs', 'loftocean' ); ?></h2><?php
    $logs = apply_filters( 'loftocean_get_log_file_list', array() );
    $logs = \LoftOcean\is_valid_array( $logs ) ? array_values( $logs ) : array();
    $log_total_count = count( $logs );
    $pages = ceil( $log_total_count / $loftocean_ical_settings_ppp );
    $text_size = ( $pages > 999 ? 4 : ( $pages > 99 ? 3 : ( $pages > 9 ? 2 : 1 ) ) );
    $log_base_url = $loftocean_ical_settings_url_base . 'logs&current_page=';
    $current_page = isset( $_REQUEST[ 'current_page' ] ) && is_numeric( $_REQUEST[ 'current_page' ] ) ? absint( $_REQUEST[ 'current_page' ] ) : 1;
    $current_page = ( $current_page < 1 ) ? 1 : $current_page;
    $current_page = ( $current_page > $pages ) ? $pages : $current_page;

    ob_start(); ?>
        <div class="tablenav-pages">
            <span class="displaying-num"><?php echo $log_total_count . ( $log_total_count > 1 ? esc_html__( ' items', 'loftocean' ) : esc_html__( ' item', 'loftocean' ) ); ?></span><?php
            if ( $pages > 1 ) : ?>
                <span class="pagination-links"><?php
                    if ( $current_page > 1 ) : ?>
                        <a class="first-page button" href="<?php echo $log_base_url . '1'; ?>">
                            <span class="screen-reader-text"><?php esc_html_e( 'First page', 'loftocean' ); ?></span>
                            <span aria-hidden="true">«</span>
                        </a>
                        <a class="prev-page button" href="<?php echo $log_base_url . ( $current_page - 1 ); ?>">
                            <span class="screen-reader-text"><?php esc_html_e( 'Previous page', 'loftocean' ); ?></span>
                            <span aria-hidden="true">‹</span>
                        </a><?php
                    else : ?>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                        <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span><?php
                    endif; ?>
                    <span class="paging-input">
                        <label for="current-page-selector" class="screen-reader-text"><?php esc_html_e( 'Current Page', 'loftocean' ); ?></label>
                        <input class="current-page current-page-selector" type="text" name="paged" value="<?php echo $current_page; ?>" size="<?php echo $text_size; ?>" data-url-base="<?php echo $log_base_url; ?>">
                        <span class="tablenav-paging-text"><?php esc_html_e( ' of ', 'loftocean' ); ?><span class="total-pages"><?php echo $pages; ?></span></span>
                    </span><?php
                    if ( $current_page < $pages ) : ?>
                        <a class="next-page button" href="<?php echo $log_base_url . ( $current_page + 1 ); ?>">
                            <span class="screen-reader-text"><?php esc_html_e( 'Next page', 'loftocean' ); ?></span>
                            <span aria-hidden="true">›</span>
                        </a>
                        <a class="last-page button" href="<?php echo $log_base_url . $pages; ?>">
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

    <div class="room-sync-log-list">
        <div class="tablenav top"><?php echo $page_navs; ?></div>
        <table class="wp-list-table widefat fixed striped table-view-list posts">
            <thead>
                <tr>
                    <th scope="col" id="title" class="manage-column column-title column-primary" abbr="<?php esc_html_e( 'Title', 'loftocean' ); ?>"><?php esc_html_e( 'File Name', 'loftocean' ); ?></th>
                    <th scope="col" id="created-date" class="manage-column column-created-date"><?php esc_html_e( 'Created Date', 'loftocean' ); ?></th>
                    <th scope="col" id="file-size" class="manage-column column-file-size"><?php esc_html_e( 'Size', 'loftocean' ); ?></th>
                </tr>
            </thead>

            <tbody id="the-list"><?php
                if ( \LoftOcean\is_valid_array( $logs ) ) :
                    $date_format = get_option( 'date_format', 'M d, Y' );
                    $date_format = empty( $date_format ) ? 'M d, Y' : $date_format;
                    $log_start_index = ( $current_page - 1 ) * $loftocean_ical_settings_ppp;
                    $log_end_index = $current_page * $loftocean_ical_settings_ppp;
                    if ( $log_end_index > $log_total_count ) {
                        $log_end_index = $log_total_count;
                    }
                    for ( $i = $log_start_index; $i < $log_end_index; $i ++ ) :
                        $log = $logs[ $i ]; ?>
                        <tr>
                            <td class="order_number column-order_number column-title column-primary page-title title" data-colname="<?php esc_html_e( 'Name', 'loftocean' ); ?>">
                                <a href="<?php echo esc_url( $log[ 'link' ] ); ?>" target="_blank"><strong><?php echo esc_html( $log[ 'name' ] ); ?></strong></a>
                                <button type="button" class="toggle-row"><span class="screen-reader-text"><?php esc_html_e( 'Show more details', 'loftocean' ); ?></span></button>
                            </td>
                            <td class="column-created-date" data-colname="<?php esc_html_e( 'Created Date', 'loftocean' ); ?>">
                                <time datetime="<?php echo esc_attr( $log[ 'created_date' ] ); ?>" title="<?php echo esc_attr( $log[ 'created_date' ] ); ?>"><?php echo esc_html( $log[ 'created_date' ] ); ?></time>
                            </td>
                            <td class="column-size" data-colname="<?php esc_html_e( 'Size', 'loftocean' ); ?>"><?php echo esc_html( $log[ 'size' ] ); ?></td>
                        </tr><?php
                    endfor;
                else : ?>
                    <tr class="no-items"><td class="colspanchange" colspan="3"><?php esc_html_e( 'No items found.', 'loftocean' ); ?></td></tr><?php
                endif; ?>
            </tbody>
        </table>
        <div class="tablenav bottom"><?php echo $page_navs; ?></div>
    </div>
</div>
