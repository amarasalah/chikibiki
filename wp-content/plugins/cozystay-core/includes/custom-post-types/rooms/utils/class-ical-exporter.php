<?php
namespace LoftOcean\iCal;

use \LoftOcean\Libraries\iCalendar\ZCiCal;
use \LoftOcean\Libraries\iCalendar\ZCiCalNode;
use \loftOcean\Libraries\iCalendar\ZCiCalDataNode;
/**
* Export iCal file
*/
class Exporter {
    /**
    * Construction function
    */
	public function __construct() {}
    /**
    * Actual exporter function
    */
    public function export( $roomID ) {
		$roomIDs =\LoftOcean\get_room_translated_ids( $roomID, true );
        $orders = \LoftOcean\get_room_orders( $roomIDs );
        $current_domain = \LoftOcean\get_current_domain();

        // Time when calendar was created. Format: "Ymd\THis\Z"
        $datestamp = ZCiCal::fromUnixDateTime() . 'Z';

        // Create calendar
        $calendar = new iCal();
        $calendar->removeMethodProperty(); // Remove property METHOD

        // Change default PRODID
        $prodid = '-//' . $current_domain . '//Room Booking ' . LOFTOCEAN_THEME_VERSION;
        $calendar->setProdid( $prodid );

        // Fill the calendar with events
        $this->add_bookings( $calendar, $datestamp, $orders, $roomIDs );

        // %domain%-%name%-%date%.ics
        $filename = $current_domain . '-' . get_post_field( 'post_name', $roomID, 'raw' ) . '-' . date( 'Ymd' ) . '.ics';

        header( 'Content-type: text/calendar; charset=utf-8' );
        header( 'Content-Disposition: inline; filename=' . $filename );
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $calendar->export();
    }
    /**
    * Add bookings
    */
    protected function add_bookings( $calendar, $datestamp, $orders, $roomIDs ) {
        $check_status = \LoftOcean\get_room_booked_status(); 
        foreach( $orders as $order_id ) { 
            $order = \wc_get_order( $order_id );
            if ( ( ! is_object( $order ) ) || ( ! method_exists( $order, 'get_formatted_billing_full_name' ) ) )  continue;

            $order_status = $order->get_status();
            if ( in_array( $order_status, $check_status ) ) {
                $summary = $this->create_summary( $order );
                $items = $order->get_items();
                $today = strtotime( 'today' );
                foreach ( $items as $item ) {
                    $room_id = get_post_meta( $item->get_product_id(), '_loftocean_booking_id', true );
                    if ( in_array( $room_id, $roomIDs ) ) {
                        $item_data = get_post_meta( $item->get_variation_id(), 'data', true );
                        if ( ( $today <= $item_data[ 'check_out' ] ) && ( $item_data[ 'room_num_search' ] > 0 ) ) {
                            $description = $this->create_description( $item_data, $room_id );
							for ( $i = 0; $i < $item_data[ 'room_num_search' ]; $i ++ ) {
	                            $event = new ZCiCalNode( 'VEVENT', $calendar->curnode );

	                            $event->addNode( new ZCiCalDataNode( 'UID:' . $i . '-' . $this->get_uuid( $item_data, $item ) ) );
	                            $event->addNode( new ZCiCalDataNode( 'DTSTART;VALUE=DATE:' . ZCiCal::fromSqlDateTime( date( 'Y-m-d', $item_data[ 'check_in' ] ) ) ) );
	                            $event->addNode( new ZCiCalDataNode( 'DTEND;VALUE=DATE:' . ZCiCal::fromSqlDateTime( date( 'Y-m-d', $item_data[ 'check_out' ] ) ) ) );
	                            $event->addNode( new ZCiCalDataNode( 'DTSTAMP:' . $datestamp ) );
	                            $event->addNode( new ZCiCalDataNode( 'SUMMARY:' . $summary ) );

	                            // ZCiCal library can limit DESCRIPTION by 80 characters, so
	                            // some of the content can be pushed on the next line
	                            $event->addNode( new ZCiCalDataNode( 'DESCRIPTION:' . $description ) );
							}
                        }
                    }
                }
            }
        } // For each booking
    }
    /**
     * Create booking summary
     */
    protected function create_summary( $order ) {
        return trim( sprintf( '%s %s (%d)', $order->get_billing_first_name(), $order->get_billing_last_name(), $order->get_id() ) );
    }
    /**
    * Create booking description
    */
    protected function create_description( $booking, $roomID ) {
        $check_in = date( 'Y-m-d', $booking[ 'check_in' ] );
        $check_out = date( 'Y-m-d', $booking[ 'check_out' ] );
        $nights = ( $booking[ 'check_out' ] - $booking[ 'check_in' ] ) / LOFTICEAN_SECONDS_IN_DAY;

        $description = sprintf( 'CHECKIN: %s\nCHECKOUT: %s\nNIGHTS: %d\n', $check_in, $check_out, $nights );

        $propertyName = get_the_title( $roomID );
        if ( ! empty( $propertyName ) ) {
            $description .= sprintf( 'PROPERTY: %s\n', trim( $propertyName ) );
        }

        $extra_services = $this->get_extra_services( $booking );
        if ( false !== $extra_services ) {
            $description .= sprintf( 'EXTRA SERVICES: %s\n', trim( $extra_services ) );
        }

        return $description;
    }
    /**
    * Get room uuid
    */
    protected function get_uuid( $data, $item ) {
        if ( empty( $data[ 'uuid4' ] ) ) {
            $data[ 'uuid4' ] = wp_generate_uuid4();
            update_post_meta( $item->get_variation_id(), 'data', $data );
        }
        return trim( sprintf( '%s@%s', $data[ 'uuid4' ], \LoftOcean\get_current_domain() ) );
    }
    /**
    * Get extra services
    */
    protected function get_extra_services( $booking ) {
        $extra_services = array();
        if ( isset( $booking[ 'extra_services' ], $booking[ 'extra_services' ][ 'services' ] ) ) {
            $room_order_extra_services = $booking[ 'extra_services' ];
            $titles = $room_order_extra_services[ 'titles' ];
            $prices = $room_order_extra_services[ 'prices' ];
            $method = $room_order_extra_services[ 'method' ];
            $label = $room_order_extra_services[ 'label' ];
            $unit = $room_order_extra_services[ 'unit' ];
            $quantity = $room_order_extra_services[ 'quantity' ];
            foreach ( $room_order_extra_services[ 'services' ] as $index => $service_id ) {
                array_push(
                    $extra_services,
                    sprintf(
                        '%s (%s%s)',
                        $titles[ $index ],
                        $label[ $index ],
                        in_array( $method[ $index ], array( 'custom', 'auto_custom' ) ) ? ' x ' . $quantity[ $index ] : ''
                    )
                );
            }
        }
        return \LoftOcean\is_valid_array( $extra_services ) ? implode( ', ', $extra_services ) : false;
    }
}
