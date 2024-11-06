<?php
    $room_id = get_the_ID();
    $has_featured_image_section = false;
    $has_settings = \LoftOcean\is_valid_array( $sets );

    $layout = $has_settings && isset( $sets[ 'args' ], $sets[ 'args' ][ 'layout' ] ) ? $sets[ 'args' ][ 'layout' ] : 'standard';
    $column = $has_settings && isset( $sets[ 'args' ], $sets[ 'args' ][ 'columns' ] ) ? $sets[ 'args' ][ 'columns' ] : '';
    $room_details = apply_filters( 'loftocean_get_room_details', '', $room_id );
    $item_class = array( 'post', 'cs-room-item' );
    $show_gallery = in_array( $layout, array( 'standard', 'list', 'zigzag', 'grid' ) );

    $has_details = \LoftOcean\is_valid_array( $room_details );
    $has_featured_image = $has_details && ( ! empty( $room_details[ 'featuredImage' ] ) );
    $has_list_thumbnail = $has_details && ( ! empty( $room_details[ 'listImage' ] ) );
    $has_gallery = $has_details && ( ! empty( $room_details[ 'gallery' ] ) );
    $metas = $has_settings && isset( $sets[ 'args' ], $sets[ 'args' ][ 'metas' ] ) ? $sets[ 'args' ][ 'metas' ] : array();
    $room_settings = $has_details && isset( $room_details[ 'roomSettings' ] ) ? $room_details[ 'roomSettings' ] : array( 'roomSubtitle' => '', 'roomLabel' => '' );
    $show_subtitle = in_array( 'subtitle', $metas ) && ( ! empty( $room_settings[ 'roomSubtitle' ] ) );
    $subtitle_before_title = $has_settings && isset( $sets[ 'args' ], $sets[ 'args' ][ 'subtitle_position' ] ) && ( 'before_title' == $sets[ 'args' ][ 'subtitle_position' ] );

    if ( $has_featured_image || $has_list_thumbnail || ( $show_gallery && $has_gallery ) ) {
        $has_featured_image_section = true;
        array_push( $item_class, 'has-post-thumbnail' );
        $show_gallery && $has_gallery ? array_push( $item_class, 'format-gallery' ) : '';
    }

    if ( in_array( $layout, array( 'carousel', 'coverlay', 'carousels', 'coverlays' ) ) && isset( $sets, $sets[ 'current_index' ] ) && ( ! empty( $column ) ) && ( $sets[ 'current_index' ] > $column ) ) {
        array_push( $item_class, 'hide' );
    }

    $item_class = apply_filters( 'loftocean_room_item_class', array_unique( $item_class ) ); ?>

    <div class="<?php echo esc_attr( implode( ' ', $item_class ) ); ?>"><?php
        if ( $has_featured_image_section ) :
            $image_size = apply_filters( 'loftocean_room_featured_image_size', array( 800, 9999 ), $layout, $column ); ?>
            <div class="featured-img">
                <a href="<?php the_permalink(); ?>" aria-label="<?php esc_attr_e( 'Room Featured Image', 'loftocean' ); ?>"><?php
                if ( $show_gallery && $has_gallery ) : ?>
                    <ul class="thumbnail-gallery"><?php
                    foreach( $room_details[ 'gallery' ] as $item ) : ?>
                        <li><?php echo wp_get_attachment_image( $item, $image_size ); ?></li><?php
                    endforeach; ?>
                    </ul><?php
                else :
                    $image_id = $has_list_thumbnail ? $room_details[ 'listImage' ] : $room_details[ 'featuredImage' ];
                    echo wp_get_attachment_image( $image_id, $image_size );
                endif; ?>
                </a><?php
                if ( $has_gallery && $show_gallery ) : ?>
                    <div class="slider-arrows"></div>
                    <div class="slider-dots"></div><?php
                endif;
                if ( in_array( 'label', $metas ) && ( ! empty( $room_settings[ 'roomLabel' ] ) ) ) : ?>
                    <div class="overlay-label">
                        <div class="overlay-label-text"><?php echo $room_settings[ 'roomLabel' ]; ?></div>
                    </div><?php
                endif; ?>
            </div><?php
        endif; ?>

        <div class="post-content cs-room-content">
            <header class="post-header item-header"><?php
                if ( $show_subtitle && $subtitle_before_title ) : ?>
                    <div class="item-subtitle"><?php echo $room_settings[ 'roomSubtitle' ]; ?></div><?php
                endif; ?>
                <h2 class="post-title item-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2><?php
                if ( $show_subtitle && ( ! $subtitle_before_title ) ) : ?>
                    <div class="item-subtitle"><?php echo $room_settings[ 'roomSubtitle' ]; ?></div><?php
                endif;
                if ( in_array( 'facilities', $metas ) ) {
                    $facility_limit = $has_settings && isset( $sets[ 'args' ], $sets[ 'args' ][ 'facility_limit' ] ) && is_numeric( $sets[ 'args' ][ 'facility_limit' ] ) ? $sets[ 'args' ][ 'facility_limit' ] : ''; 
                    do_action( 'loftocean_the_room_facilities', $room_id, $column, 'normal', $facility_limit );
                } ?>
            </header><?php
            if ( in_array( 'excerpt', $metas ) ) : ?>
                <div class="post-excerpt item-excerpt"><?php the_excerpt(); ?></div><?php
            endif;
            if ( in_array( 'read_more_btn', $metas ) ) : ?>
                <footer class="post-footer item-footer">
                    <div class="more-btn">
                        <a class="read-more-btn button cs-btn-underline" href="<?php the_permalink(); ?>">
                            <span><?php echo apply_filters( 'loftocean_room_readmore_button_text', '' ); ?></span>
                        </a>
                    </div>
                </footer><?php
            endif; ?>
        </div>
    </div>
