<div class="room-top-section"><?php
    $show_full_list = false; ?>
    <div class="cs-gallery gallery-mosaic">
        <div class="cs-gallery-wrap"><?php
            $image_size = apply_filters( 'loftocean_room_top_section_image_size', array( 800, 9999 ) );
            $limit = 5;
            if ( isset( $room_settings, $room_settings[ 'gallery' ] ) ) :
                $show_full_list = count( $room_settings[ 'gallery' ] ) > $limit;
                foreach( $room_settings[ 'gallery' ] as $index => $item ) : ?>
                    <div class="cs-gallery-item<?php if ( $index >= $limit ) : ?> hide<?php endif; ?>"><?php
                        if ( $show_full_list ) : ?><a href="<?php echo esc_url( wp_get_attachment_url( $item ) ); ?>" data-elementor-open-lightbox="no" aria-label="<?php esc_attr_e( 'Image', 'loftocean' ); ?>"><?php endif;
                        echo wp_get_attachment_image( $item, $image_size );
                        if ( $show_full_list ) : ?></a><?php endif; ?>
                    </div><?php
                endforeach;
            endif; ?>
        </div>
    </div><?php
    if ( $show_full_list ) : ?>
        <div class="cs-gallery-view-all"><a href="#" class="button cs-btn-rounded"><span class="cs-btn-text"><?php esc_html_e( 'View All Photos', 'loftocean' ); ?></span></a></div><?php
    endif; ?>
</div>
