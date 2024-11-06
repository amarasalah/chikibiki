<?php
/**
* Template for singular room
*/
get_header();

$current_room_id = get_queried_object_id();

$top_section_type = apply_filters( 'loftocean_room_top_section', 'top-gallery-1' );
if ( ! empty( $top_section_type ) ) {
    $room_settings = apply_filters( 'loftocean_get_room_details', array(), $current_room_id );
    $top_section_template = '';
    if ( in_array( $top_section_type, array( 'top-gallery-1', 'top-gallery-2' ) ) && ( ! empty( $room_settings[ 'gallery' ] ) ) ) {
        $top_section_template = LOFTOCEAN_DIR . 'template-parts/single-room/' . $top_section_type . '.php';
    }
    if ( ( 'hide' != $top_section_type ) && ( ( 'top-image' == $top_section_type ) || empty( $top_section_template ) ) && ( ! empty( $room_settings[ 'featuredImage' ] ) ) ) {
        $top_section_template = LOFTOCEAN_DIR . 'template-parts/single-room/top-image.php';
    }
    if ( file_exists( $top_section_template ) ) {
        require $top_section_template;
    }
} ?>

<div class="main">
    <div class="container">
        <div id="primary" class="primary content-area"><?php
        while( have_posts() ) :
            the_post();
            $roomDetails = apply_filters( 'loftocean_get_room_details', '', get_the_ID() ); ?>
            <article <?php post_class(); ?>>
                <header class="post-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1><?php
                    if ( isset( $roomDetails, $roomDetails[ 'roomSettings' ], $roomDetails[ 'roomSettings' ][ 'roomSubtitle' ] ) ) : ?>
                        <div class="item-subtitle"><?php echo do_shortcode( $roomDetails[ 'roomSettings' ][ 'roomSubtitle' ] ); ?></div><?php
                    endif; ?>
                    <?php do_action( 'loftocean_the_room_facilities', get_the_ID(), '', 'normal' ); ?>
                </header>
                <div class="entry-content"><?php the_content(); ?></div><?php
                if ( apply_filters( 'loftocean_room_enable_availibility_calendar', false ) ) : ?>
                    <div class="room-availability">
                        <div class="container">
                            <h4 class="room-availability-title"><?php echo apply_filters( 'loftocean_room_availability_calendar_section_title', esc_html__( 'Availability', 'loftocean' ) ); ?></h4>
                            <div class="room-availability-calendar-wrapper">
                                <input class="hidden-calendar" />
                            </div>
                        </div>
                    </div><?php
                endif; ?>
            </article><?php
        endwhile;
        wp_reset_postdata(); ?>
        </div><?php
        $roomDetails = apply_filters( 'loftocean_get_room_details', array(), get_queried_object_id() );
        if ( isset( $roomDetails, $roomDetails[ 'roomSettings' ], $roomDetails[ 'roomSettings' ][ 'bookingForm' ] ) ) :
            if ( in_array( $roomDetails[ 'roomSettings' ][ 'bookingForm' ], array( 'left', 'right', '' ) ) ) : ?>
                <aside id="secondary" class="sidebar">
                    <div class="sidebar-container"><?php require LOFTOCEAN_DIR . 'template-parts/single-room-reservation.php'; ?></div>
                </aside><?php
            endif;
        endif; ?>
    </div>
</div><?php
if ( apply_filters( 'loftocean_room_show_similar_rooms_section', true ) ) :
    $similar_section_title = apply_filters( 'loftocean_single_room_simialr_section_title', esc_html__( 'Similar Rooms', 'loftocean' ) );
    query_posts( apply_filters( 'loftocean_room_similar_section_args', array(
        'posts_per_page' => 3,
        'post_type' => 'loftocean_room',
        'offset' => 0,
        'post__not_in' => array( $current_room_id ),
        'orderby' => 'rand',
        'tax_query' => array( array(
            'taxonomy' => 'lo_room_type',
            'field'    => 'term_id',
            'terms'    => wp_get_post_terms( $current_room_id, 'lo_room_type', array( 'fields' => 'ids' ) )
        ) )
    ), $current_room_id ) );
    if ( have_posts() ) : ?>
        <div class="similar-rooms">
            <div class="container">
                <h4 class="similar-rooms-title"><?php echo esc_html( $similar_section_title ); ?></h4><?php
                do_action( 'loftocean_rooms_widget_the_list_content', array(
                    'args' => array( 'layout' => 'overlay', 'columns' => '3', 'metas' => array( 'excerpt','read_more_btn', 'subtitle', 'label' ), 'page_layout' => '' ),
                    'wrap_class' => array( 'posts', 'cs-rooms', 'column-3', 'layout-grid', 'layout-overlay', 'with-hover-effect', 'img-ratio-1-1' ),
                    'pagination' => ''
                ), true ); ?>
            </div>
        </div><?php
    endif;
endif;
wp_reset_query();

get_footer();
