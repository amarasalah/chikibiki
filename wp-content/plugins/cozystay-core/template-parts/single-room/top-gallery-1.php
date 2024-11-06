<div class="room-top-section">
    <div class="cs-gallery gallery-carousel gap-0 align-middle-v variable-width slider-dots-overlap">
        <div class="cs-gallery-wrap"><?php
            $image_size = apply_filters( 'loftocean_room_top_section_image_size', array( 800, 9999 ) );
            if ( isset( $room_settings, $room_settings[ 'gallery' ] ) ) :
                foreach( $room_settings[ 'gallery' ] as $index => $item ) : ?>
                    <div class="cs-gallery-item<?php if ( $index > 0 ) : ?> hide<?php endif; ?>"><?php echo wp_get_attachment_image( $item, $image_size ); ?></div><?php
                endforeach;
            endif; ?>
        </div>
    </div>

</div>
