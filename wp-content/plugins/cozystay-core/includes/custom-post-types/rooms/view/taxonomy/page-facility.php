<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Room Facility', 'loftocean' ); ?></h1>
    <hr class="wp-header-end">
    <form id="loftocean-room-facility-form" action="<?php echo esc_url( admin_url( 'edit.php?post_type=loftocean_room&page=loftocean_room_facility' ) ); ?>" method="POST">
        <div class="loftocean-room-facility-wrapper">
            <a href="#" class="loftocean-room-facility-add" data-current-index="0"><?php esc_html_e( 'Add New', 'loftocean' ); ?></a>
        </div>
        <p class="submit loftocean-submit-button">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'loftocean' ); ?>" disabled>
            <span class="spinner" style="visibility: visible; float: none;"></span>
        </p>
        <input type="hidden" name="loftocean_room_facility_removed" value="" />
        <input type="hidden" name="loftocean_room_facilites_settings_nonce" value="<?php echo esc_attr( wp_create_nonce( 'loftocean_room_facilites_settings_nonce' ) ); ?>" />
    </form>
</div>
<script id="tmpl-loftocean-room-facility" type="text/html">
<# data.list.forEach( function( item ) {
    var namePrefix = 'loftocean_room_facility[item' + ( data.index ++ ) + ']',
        isCustomItem = item[ 'facility_type' ] && ( 'custom-facility' == item[ 'facility_type' ] ); #>
    <div class="loftocean-room-facility-item">
        <h3><?php esc_html_e( 'Room Facility', 'loftocean' ); ?><span class="item-name"><# if ( item[ 'name' ] ) { #> - {{{ item.name }}}<# } #></span></h3>
        <# if ( isCustomItem ) { #><a href="#" class="loftocean-room-facility-item-remove"><?php esc_html_e( 'Remove', 'loftocean' ); ?></a><# } #>
        <div class="loftocean-room-facility-controls-wrapper">
            <div class="controls-row">
                <div class="control-wrapper">
                    <label><?php esc_html_e( 'Label:', 'loftocean' ); ?></label>
                    <input name="{{{ namePrefix }}}[description]" class="loftocean-room-facility-label" type="text" value="{{{ item.description }}}">
                </div>
                <div class="control-wrapper">
                    <label><?php esc_html_e( 'Title:', 'loftocean' ); ?></label>
                    <input name="{{{ namePrefix }}}[name]" class="loftocean-room-facility-title" type="text" value="{{{ item.name }}}">
                </div>
            </div>
            <div class="controls-row">
                <div class="control-wrapper">
                    <label><?php esc_html_e( 'Icon:', 'loftocean' ); ?></label>
                    <div class="icon-preview"><# if ( item.icon ) { #><i class="flaticon flaticon-{{{ item.icon }}}"></i><# } #></div>
                    <input name="{{{ namePrefix }}}[icon]" type="hidden" value="{{{ item.icon }}}">
                    <button class="loftocean-room-facility-choose-icon"><?php esc_html_e( 'Choose an Icon', 'loftocean' ); ?></button>
                    <button class="loftocean-room-facility-remove-icon"><?php esc_html_e( 'Remove', 'loftocean' ); ?></button>
                </div>
            </div>
            <input type="hidden" name="{{{ namePrefix }}}[type]" value="{{{ item.facility_type }}}" readonly />
            <input type="hidden" class="facility-item-id-hidden" name="{{{ namePrefix }}}[id]" value="{{{ item.term_id }}}" readonly />
        </div>
    </div><#
} ); #>
</script>
