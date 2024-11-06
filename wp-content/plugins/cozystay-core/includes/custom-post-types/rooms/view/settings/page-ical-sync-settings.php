<div class="wrap"><?php
    $loftocean_ical_settings_ppp = apply_filters( 'loftocean_ical_settings_posts_per_page', 10 );
    $loftocean_ical_settings_url_base = admin_url( 'edit.php?post_type=loftocean_room&page=loftocean_room_ical_sync_settings&active_tab=' );
    $loftocean_ical_settings_tabs = array(
        'calendars' => esc_html__( 'Sync Calendars', 'loftocean' ),
        'imported-bookings' => esc_html__( 'Imported Bookings', 'loftocean' ),
        'settings' => esc_html__( 'Settings', 'loftocean' ),
        'logs' => esc_html__( 'Logs', 'loftocean' )
    );
    $loftocean_ical_settings_active_tab = isset( $_REQUEST[ 'active_tab' ] ) && ( ! empty( $_REQUEST[ 'active_tab' ] ) ) ? wp_unslash( $_REQUEST[ 'active_tab' ] ) : 'calendars';
    if ( ! in_array( $loftocean_ical_settings_active_tab, array_keys( $loftocean_ical_settings_tabs ) ) ) {
        $loftocean_ical_settings_active_tab = 'calendars';
    } ?>
    <h1 class="wp-heading-inline"><?php esc_html_e( 'iCal Sync', 'loftocean' ); ?></h1>
    <hr class="wp-header-end">
    <div id="cs-dashboard-tabs-wrapper" class="nav-tab-wrapper cs-nav-tab-wrapper"><?php
        foreach( $loftocean_ical_settings_tabs as $id => $label ) :
            $current_actived = ( $loftocean_ical_settings_active_tab == $id ); ?>
            <a class="nav-tab<?php if ( $current_actived ) : ?> nav-tab-active<?php endif; ?>" href="<?php echo $loftocean_ical_settings_url_base . $id; ?>"><?php echo $label; ?></a><?php
        endforeach; ?>
    </div><?php
    $dir = LOFTOCEAN_DIR . 'includes/custom-post-types/rooms/view/settings/';
    switch ( $loftocean_ical_settings_active_tab ) {
        case 'imported-bookings':
            require_once $dir . 'ical-sync-tab-imported-bookings.php';
            break;
        case 'settings':
            require_once $dir . 'ical-sync-tab-settings.php';
            break;
        case 'logs':
            require_once $dir . 'ical-sync-tab-logs.php';
            break;
        default:
            require_once $dir . 'ical-sync-tab-sync.php';
    } ?>
</div>
