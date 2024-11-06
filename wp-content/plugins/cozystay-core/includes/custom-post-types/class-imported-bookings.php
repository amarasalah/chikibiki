<?php
namespace LoftOcean\Custom_Post_Type;

class Imported_Booking {
    /**
    * String Post type
    */
    protected $post_type = 'loftocean_bookings';
    /**
    * Construction function
    */
    public function __construct() {
        add_action( 'init', array( $this, 'register_custom_post_type' ) );
        add_filter( 'loftocean_get_imported_booking', array( $this, 'get_imported_order' ), 99, 4 );
        add_filter( 'loftocean_get_all_imported_bookings', array( $this, 'get_all_bookings' ) );
        add_filter( 'loftocean_get_imported_bookings_by_args', array( $this, 'get_bookings_by_args' ), 10, 2 );
        add_filter( 'loftocean_get_imported_booking_detail', array( $this, 'get_booking_detail' ), 10, 2 );

        add_action( 'loftocean_remove_imported_bookings', array( $this, 'remove_imported_bookings' ), 10, 3 );
        add_action( 'loftocean_regenerate_imported_booking_data', array( $this, 'regenerate_data' ) );
    }
    /**
    * Custom post type
    */
    public function register_custom_post_type() {
        register_post_type( $this->post_type, array(
            'labels' => array(
                'name' => esc_html__( 'Imported Booking', 'loftocean' ),
                'all_items' => __( 'All Imported Bookings', 'loftocean' ),
                'singular_name' => esc_html__( 'Imported Booking', 'loftocean' ),
                'add_new' => esc_html__( 'Add Imported Booking', 'loftocean' ),
                'add_new_item' => esc_html__( 'Add Imported Booking', 'loftocean' )
            ),
            'public' => false,
            'has_archive' => false,
            'show_in_rest' => false,
            'capability_type' => 'post',
            'publicly_queryable' => false,
            'supports' => array( 'title' )
        ) );
    }
    /**
    * Get booking detail
    */
    public function get_booking_detail( $detail, $booking_id ) {
        if ( ( ! empty( $booking_id ) ) && ( $this->post_type == get_post_type( $booking_id ) ) ) {
            $roomID = get_post_meta( $booking_id, 'room_id', true );
            if ( ( ! empty( $roomID ) ) && ( false !== get_post_status( $roomID ) ) ) {
                $detail = array(
                    'roomURL' => admin_url( 'post.php?post=' . $roomID . '&action=edit' ),
                    'roomTitle' => get_post_field( 'post_title', $roomID ),
                    'order_id' => $booking_id,
                    'title' => sprintf( '#%1$s %2$s', $booking_id, get_post_field( 'post_title', $booking_id ) ),
                    'start' => get_post_meta( $booking_id, 'checkin', true ),
                    'end' => get_post_meta( $booking_id, 'checkout', true ),
                    'source' => get_post_meta( $booking_id, 'source_title', true ),
                    'detail' => get_post_meta( $booking_id, 'booking_details', true )
                );
            }
        }
        return $detail;
    }
    /**
    * Get imported bookings
    */
    public function get_imported_order( $orders, $roomIDs, $start, $end ) {
        $roomIDs = is_array( $roomIDs ) ? $roomIDs : array( $roomIDs );
        $bookings = array();
        $items = apply_filters(
            'loftocean_get_imported_bookings_by_args',
            array(),
            array(
                'relation' => 'AND',
                array( 'key' => 'room_id', 'value' => $roomIDs, 'compare' => 'IN' ),
                array(
                    'relation' => 'OR',
                    array(
                        'relation' => 'AND',
                        array( 'key' => 'checkin', 'value' => $start, 'compare' => '>=' ),
                        array( 'key' => 'checkin', 'value' => $end, 'compare' => '<=' )
                    ),
                    array(
                        'relation' => 'AND',
                        array( 'key' => 'checkout', 'value' => $start, 'compare' => '>=' ),
                        array( 'key' => 'checkout', 'value' => $end, 'compare' => '<=' )
                    ),
                    array(
                        'relation' => 'AND',
                        array( 'key' => 'checkin', 'value' => $start, 'compare' => '<' ),
                        array( 'key' => 'checkout', 'value' => $end, 'compare' => '>' )
                    )
                )
            )
        );
        if ( \LoftOcean\is_valid_array( $items ) ) {
            foreach ( $items as $item ) {
                $room_id = get_post_meta( $item, 'room_id', true );
                array_push( $bookings, array(
                    'is_booking' => true,
                    'order_id' => $item,
                    'title' => sprintf( '#%1$s %2$s', $item, get_post_field( 'post_title', $item ) ),
                    'room_id' => $room_id,
                    'room_title' => get_post_field( 'post_title', $room_id ),
                    'room_link' => get_permalink( $room_id ),
                    'start' => get_post_meta( $item, 'checkin', true ),
                    'end' => get_post_meta( $item, 'checkout', true ),
                    'backgroundColor' => '#88b153',
                    'borderColor' => '#88b153',
                    'detail' => get_post_meta( $item, 'booking_details', true ),
                    'source' => 'imported'
                ) );
            }
        }
        return $bookings;
    }
    /**
    * Get all bookings
    */
    public function get_all_bookings( $bookings = array() ) {
        return apply_filters( 'loftocean_get_imported_bookings_by_args', array(), array() );
    }
    /**
    * Regenerate imported booking data
    */
    public function regenerate_data() {
        $bookings = $this->get_all_bookings();
        $booked_num = array();
        if ( \LoftOcean\is_valid_array( $bookings ) ) {
            foreach ( $bookings as $bid ) {
                $roomID = get_post_meta( $bid, 'room_id', true );
                $checkin = get_post_meta( $bid, 'checkin', true );
                $checkout = get_post_meta( $bid, 'checkout', true );
                $source = get_post_meta( $bid, 'source', true );
                $room_index = 'room' . $roomID;

                if ( ! isset( $booked_num[ $room_index ] ) ) {
                    $booked_num[ $room_index ] = array( 'roomID' => $roomID, 'list' => array() );
                }
                if ( ! isset( $booked_num[ $room_index ][ 'list' ][ $source ] ) ) {
                    $booked_num[ $room_index ][ 'list' ][ $source ] = array();
                }
                $checkin = strtotime( $checkin );
                $checkout = strtotime( $checkout );
                for( $i = $checkin; $i < $checkout; $i += LOFTICEAN_SECONDS_IN_DAY ) {
                    $date_index = 'date_' . $i;
                    if ( isset( $booked_num[ $room_index ][ 'list' ][ $source ][ $date_index ] ) ) {
                        $booked_num[ $room_index ][ 'list' ][ $source ][ $date_index ][ 'num' ] += 1;
                    } else {
                         $booked_num[ $room_index ][ 'list' ][ $source ][ $date_index ] = array( 'num' => 1, 'checkin' => $i, 'checkout' => ( $i + LOFTICEAN_SECONDS_IN_DAY ) );
                    }
                }
            }
            foreach ( $booked_num as $bn ) {
                foreach ( $bn[ 'list' ] as $bns => $bnd ) {
                    do_action( 'loftocean_update_room_imported_orders', $bnd, $bn[ 'roomID' ], $bns );
                }
            }
        }
    }
    /**
    * Get bookings by args
    */
    public function get_bookings_by_args( $bookings, $meta_args = array() ) {
        $bookings = array();
        $ppp = 30;
        $args = array(
            'offset' => 0,
            'posts_per_page' => $ppp,
            'fields' => 'ids',
            'post_status' => 'any',
            'post_type' => $this->post_type
        );
        if ( \LoftOcean\is_valid_array( $meta_args ) ) {
            $args[ 'meta_query' ] = $meta_args;
        }
        do {
            $items = \get_posts( $args );
            foreach ( $items as $item ) {
                array_push( $bookings, $item );
            }
            $args[ 'offset' ] += $ppp;
        } while( count( $items ) === $ppp );
        return $bookings;
    }
    /**
    * Remove imported bookings
    */
    public function remove_imported_bookings( $items, $roomID, $source ) {
        if ( \LoftOcean\is_valid_array( $items ) ) {
            foreach ( $items as $item ) {
                $bookings = array();
                $checkin = strtotime( get_post_meta( $item, 'checkin', true ) );
                $checkout = strtotime( get_post_meta( $item, 'checkout', true ) );
                for ( $i = $checkin; $i < $checkout; $i += LOFTICEAN_SECONDS_IN_DAY ) {
                    $index = 'date_' . $i;
                    if ( isset( $bookings[ $index ] ) ) {
                        $bookings[ $index ][ 'num' ] += 1;
                    } else {
                        $bookings[ $index ] = array( 'num' => 1, 'checkin' => $i, 'checkout' => ( $i + LOFTICEAN_SECONDS_IN_DAY ) );
                    }
                }
                wp_delete_post( $item, true );
                do_action( 'loftocean_cancel_room_imported_orders', $bookings, $roomID, $source );
            }
        }
    }
}
new Imported_Booking();
