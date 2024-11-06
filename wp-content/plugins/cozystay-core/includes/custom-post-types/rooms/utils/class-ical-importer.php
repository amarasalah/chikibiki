<?php
namespace LoftOcean\iCal;

use \LoftOcean\Libraries\iCalendar\ZCiCal;
use \LoftOcean\Libraries\iCalendar\ZCiCalNode;
use \loftOcean\Libraries\iCalendar\ZCiCalDataNode;
/**
* Import iCal file
*/
class Importer {
    /**
    * Sync manager instance
    */
    protected $sync_manager = null;
    /**
    * Looger manager instance
    */
    protected $logger = null;
    /**
    * Log file
    */
    protected $log_file = '';
    /**
    * Date format
    */
    protected $date_format = '';
    /**
    * String Post type
    */
    protected $imported_booking_post_type = 'loftocean_bookings';
    /**
    * Existing imported bookings found
    */
    protected $all_imported_bookings = null;
    /**
    * Booking data for each date
    */ 
    protected $booking_data = array();
    /**
    * Construction function
    */
	public function __construct( $sync ) {
        $this->sync_manager = $sync;
        $this->logger = $this->sync_manager->get_logger();

        $this->date_format = sprintf( '%1$s %2$s', get_option( 'date_format', 'Y-m-d' ), get_option( 'time_format', 'H:i:s' ) );
	}
    /**
    * Import events
    */
    public function import( $content, $roomID, $source_id, $source_title, $time ) {
        $this->log_file = $time;
        $this->all_imported_bookings = null;
        $this->booking_data = array();
        try {
            $ical = new \LoftOcean\iCal\iCal( $content );
            $events = $ical->getEventsData( $roomID );
            if ( count( $events ) > 0 ) {
                $today = date( 'Y-m-d' );
                $events_to_add = array();
                $list = $this->get_bookings_from_source( $source_id, $roomID );
                foreach ( $events as $event ) {
                    if ( isset( $event, $event[ 'uid' ], $event[ 'checkIn' ], $event[ 'checkOut' ] ) ) {
                        $uuid = $event[ 'uid' ];

                        if ( $this->is_event_imported( $event, $list ) ) {
                            $booking_id = $list[ $uuid ];
                            unset( $list[ $uuid ] );
                            $this->log( sprintf(
                                // translators: 1: booking ID.
                                esc_html__( 'Skipped, booking #%1$s alread exists.', 'loftocean' ),
                                $booking_id
                            ) );
                            update_post_meta( $booking_id, 'source_title', $source_title );
                            update_post_meta( $booking_id, 'booking_details', $event );

                            $this->update_booking_data( $event[ 'checkIn' ], $event[ 'checkOut' ] ); 
                        } else {
                            array_push( $events_to_add, $event );
                        }
                    } else {
                        $this->error( esc_html__( 'Not a valid event', 'loftocean' ) );
                    }
                }
                if ( \LoftOcean\is_valid_array( $list ) ) {
                    $this->remove_existing_bookings( $list, $roomID, $source_id );
                }
                if ( \LoftOcean\is_valid_array( $events_to_add ) ) {
                    $valid_events = array();
                    foreach ( $events_to_add as $event ) {
                        if ( apply_filters( 'loftocean_room_reservation_check_dates', false, $roomID, $event[ 'checkIn' ], $event[ 'checkOut' ], 1 ) ) {
                            array_push( $valid_events, $event );
                        } else {
                            $this->error( sprintf(
                                /* translators: 1/2: dates **/
                                esc_html__( 'Cannot import a new booking. Dates from %1$s to %2$s are blocked by other bookings', 'loftocean' ),
                                $event[ 'checkIn' ],
                                $event[ 'checkOut' ]
                            ) );
                        }
                    }
                    if ( \LoftOcean\is_valid_array( $valid_events ) ) {
                        $this->import_new_events( $valid_events, $roomID, $source_id, $source_title );
                    }
                }

                $this->update_booking_data_to_database( $roomID, $source_id );
            } else {
                $this->error( esc_html__( 'No events found', 'loftocean' ) );
                if ( apply_filters( 'loftocean_remove_existing_bookings_if_no_event_found', true ) ) {
                    $list = $this->get_bookings_from_source( $source_id, $roomID );
                    if ( \LoftOcean\is_valid_array( $list ) ) {
                        $this->remove_existing_bookings( $list, $roomID, $source_id );
                    }
                }
            }
        } catch ( \Exception $e ) {
			$this->error( $e->getMessage() );
		}

		return false;
    }
    /**
    * Imported new events
    */
    protected function import_new_events( $events, $roomID, $source, $source_title ) {
        if ( \LoftOcean\is_valid_array( $events ) ) {
            foreach( $events as $event ) {
                $booking_id = wp_insert_post( array(
                    'post_title' => $event[ 'summary' ],
                    'post_content' => '',
                    'post_status' => 'private',
                    'post_type' => $this->imported_booking_post_type
                ) );
                if ( ! is_wp_error( $booking_id ) ) {
                    update_post_meta( $booking_id, 'source_title', $source_title );
                    update_post_meta( $booking_id, 'source', $source );
                    update_post_meta( $booking_id, 'room_id', $roomID );
                    update_post_meta( $booking_id, 'checkin', $event[ 'checkIn' ] );
                    update_post_meta( $booking_id, 'checkout', $event[ 'checkOut' ] );
                    update_post_meta( $booking_id, 'booking_uuid', $event[ 'uid' ] );
                    update_post_meta( $booking_id, 'booking_details', $event );
                    update_post_meta( $booking_id, 'date_imported', strtotime( 'now' ) );

                    $this->update_booking_data( $event[ 'checkIn' ], $event[ 'checkOut' ] );

                    $this->log( sprintf(
                        // translators: booking ID.
                        esc_html__( 'New booking #%1$s has been added.', 'loftocean' ),
                        $booking_id
                    ) );
                } else {
                    $this->error( esc_html__( 'Failed to add new booking.', 'loftocean' ) );
                }
            }
        }
    }
    /**
    * Get all bookings from given source
    */
    protected function get_bookings_from_source( $source, $roomID ) {
        if ( is_null( $this->all_imported_bookings ) ) {
            $this->all_imported_bookings = array();
            $items = apply_filters(
                'loftocean_get_imported_bookings_by_args',
                array(),
                array(
                    'relation' => 'AND',
                    array( 'key' => 'room_id', 'value' => $roomID ),
                    array( 'key' => 'source', 'value' => $source )
                )
            );
            if ( \LoftOcean\is_valid_array( $items ) ) {
                foreach( $items as $item ) {
                    $this->all_imported_bookings[ get_post_meta( $item, 'booking_uuid', true ) ] = $item;
                }
            }
            return $this->all_imported_bookings;
        }
        return $this->all_imported_bookings;
    }
    /**
    * Check if current bookings exists
    */
    protected function is_event_imported( $event, $list ) {
        if ( isset( $list[ $event[ 'uid' ] ] ) ) {
            $booking_id = $list[ $event[ 'uid' ] ];
            $saved_checkin = get_post_meta( $booking_id, 'checkin', true );
            $saved_checkout = get_post_meta( $booking_id, 'checkout', true );
            return ( $saved_checkin == $event[ 'checkIn' ] ) && ( $saved_checkout == $event[ 'checkOut' ] );
        }
        return false;
    }
    /*
    * Remove existing bookings
    */
    protected function remove_existing_bookings( $bookings, $roomID, $source ) {
        if ( \LoftOcean\is_valid_array( $bookings ) ) {
            do_action( 'loftocean_remove_imported_bookings', $bookings, $roomID, $source );
            foreach( $bookings as $item ) {
                $this->log( sprintf(
                    // translators: booking ID.
                    esc_html__( 'Booking #%1$s has been removed.', 'loftocean' ),
                    $item
                ) );
            }
        }
    }
    /**
    * Update room booking data
    */ 
    protected function update_booking_data( $checkin, $checkout ) {
        $checkin = strtotime( $checkin );
        $checkout = strtotime( $checkout );
        for( $i = $checkin; $i < $checkout; $i += LOFTICEAN_SECONDS_IN_DAY ) {
            $index = 'date_' . $i;
            if ( isset( $this->booking_data[ $index ] ) ) {
                $this->booking_data[ $index ][ 'num' ] += 1;
            } else {
                $this->booking_data[ $index ] = array( 'num' => 1, 'checkin' => $i, 'checkout' => ( $i + LOFTICEAN_SECONDS_IN_DAY ) );
            }
        }
    }
    /**
    * Update booking data to database
    */ 
    protected function update_booking_data_to_database( $roomID, $source ) {
        do_action( 'loftocean_update_room_imported_orders', $this->booking_data, $roomID, $source );
    }
    /**
    * Log message
    */
    protected function log( $message ) {
        empty( $message ) ? '' : $this->logger->add_log( sprintf( '[ %1$s ] %2$s', date( $this->date_format ), $message ), $this->log_file );
    }
    /**
    * Log error message
    */
    protected function error( $message ) {
        $this->log( $message );
    }
}
