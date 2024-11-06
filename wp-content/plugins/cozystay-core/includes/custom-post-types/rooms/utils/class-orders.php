<?php
namespace LoftOcean\Room;

if ( ! class_exists( '\LoftOcean\Room\Orders' ) ) {
	class Orders {
		/**
		* Room post type
		*/
		protected $room_post_type = 'loftocean_room';
        /**
        * Room order table name
        */
        protected $order_table = 'loftocean_room_order';
        /**
        * Construction function
        */
        public function __construct() {
			add_action( 'rest_api_init', array( $this, 'register_rest_api' ) );

            add_action( 'loftocean_update_room_custom_tables', array( $this, 'update_order_table' ), 20 );
            add_action( 'loftocean_room_regenerate_order_data', array( $this, 'regenerate_orders' ) );
			add_filter( 'loftocean_room_custom_tables', array( $this, 'room_custom_tables' ) );
		}
		/*
		* Register REST APIs
		*/
		public function register_rest_api() {
			// Get room availability
			register_rest_route( 'loftocean/v1', '/get_bookings/(?P<nonce>.+)', array(
				'methods' 	=> 'POST',
				'permission_callback' => '__return_true',
				'callback' 	=> array( $this, 'get_bookings' )
			) );
		}
		/**
		* Get bookings
		*/
		public function get_bookings( $data ) {
			$rid = isset( $data[ 'rid' ] ) ? $data[ 'rid' ] : false;
			$start = isset( $data[ 'start' ] ) ? strtotime( $data[ 'start' ] ) : false;
			$end = isset( $data[ 'end' ] ) ? strtotime( $data[ 'end' ] ) : false;
			if ( ( ! $rid ) || ( $this->room_post_type != get_post_type( $rid ) ) || ( ! $start ) || ( ! $end ) ) return false;

			$rids = \LoftOcean\get_room_translated_ids( $rid, true );

			$status = $this->get_status( $rid, $data[ 'start' ], $data[ 'end' ] );
			$orders = $this->get_woocommerce_orders( $rids, $start, $end );
			$imported_orders = $this->get_imported_orders( $rids, $data[ 'start' ], $data[ 'end' ] );

			$events = array_merge( $orders, $imported_orders, $status );
			return array_filter( $events );
		}
        /**
        * Update room custom tables
        */
        public function room_custom_tables( $tables ) {
            $tables[] = $this->get_order_table_structure();
            return $tables;
        }
        /**
        * Update order table
        */
        public function update_order_table() {
            global $wpdb;
			$order_table_name = $wpdb->prefix . $this->order_table;
			$table_name_found = $wpdb->get_var( 'SHOW TABLES LIKE "' . $order_table_name . '"' );
			if ( $order_table_name == $table_name_found ) {
                $wpdb->query( "DROP TABLE {$order_table_name};" );
				$table_sql = $this->get_order_table_structure();
				$wpdb->query( $table_sql );
                add_action( 'init', array( $this, 'update_existing_room_orders' ), 30 );
                do_action( 'loftocean_update_existing_room_orders' );
			}
        }
        /**
        * Regenerate orders
        */
        public function regenerate_orders() {
            global $wpdb;
            $order_table = $wpdb->prefix . $this->order_table;
            $wpdb->query( "TRUNCATE TABLE {$order_table};" );
            $this->update_existing_room_orders();
        }
        /**
        * Update existing room orders
        */
        public function update_existing_room_orders() {
            if ( class_exists( '\WooCommerce' ) ) {
				$orders = $this->get_all_woocommerce_orders(); 
				$check_status = \loftOcean\get_room_booked_status();
				foreach ( $orders as $order_id ) { 
					$roomIDs = array();
					$order = \wc_get_order( $order_id );
					if ( ( ! is_object( $order ) ) || ( ! method_exists( $order, 'get_formatted_billing_full_name' ) ) )  continue;

					$order_status = $order->get_status(); 
					if ( in_array( $order_status, $check_status ) ) {
						$items = $order->get_items();
			            foreach ( $items as $item ) {
							$room_id = get_post_meta( $item->get_product_id(), '_loftocean_booking_id', true );
							if ( ( ! empty( $room_id ) ) && ( $this->room_post_type == get_post_type( $room_id ) ) ) {
								in_array( $room_id, $roomIDs ) ? '' : array_push( $roomIDs, $room_id );
								$data = get_post_meta( $item->get_variation_id(), 'data', true );
								for ( $i = $data[ 'check_in' ]; $i < $data[ 'check_out' ]; $i = strtotime( '+1 day', $i ) ) {
									do_action( 'loftocean_update_room_order', array( 'room_id' => $data[ 'room_id' ], 'check_in' => $i, 'number' => $data[ 'room_num_search' ] ), 'paid' );
								}
			                }
			            }
					}
					if ( 'yes' != $order->get_meta( '_loftocean_rooms_updated' ) ) {
		                foreach ( $roomIDs as $rid ) {
							$order->add_meta_data( '_loftocean_order_room_ID', $rid, false );
		                }
						$order->update_meta_data( '_loftocean_rooms_updated', 'yes' );
						$order->save();
					}
				}
			}
        }
        /*
        * Get all woocommerce orders
        */
        protected function get_all_woocommerce_orders() {
        	$ppp = 30;
			$return_orders = array();
			$query_args = array( 'limit' => $ppp, 'return' => 'ids', 'offset' => 0 );
			while ( 1 ) {
				$orders = \wc_get_orders( $query_args );
				if ( \LoftOcean\is_valid_array( $orders ) ) {
					$return_orders = array_merge( $return_orders, $orders );
					$query_args[ 'offset' ] += $ppp;
				} else {
					break;
				}
			}
			return $return_orders;
        }
		/**
		* Get room orders from WooCommerce
		* @param int room id
		* @param int start timestamp
		* @param int end timestamp
		*/
		public function get_woocommerce_orders( $rids, $start, $end ) {
			if ( $start >= $end ) {
				return array();
			}

			$events = array();
			$orders = \LoftOcean\get_room_orders( $rids );
			$check_status = \loftOcean\get_room_booked_status();
			if ( \LoftOcean\is_valid_array( $orders ) ) {
				foreach( $orders as $order_id ) {
					$order = \wc_get_order( $order_id );
					if ( ( ! is_object( $order ) ) || ( ! method_exists( $order, 'get_formatted_billing_full_name' ) ) )  continue;

					$order_status = $order->get_status();
					if ( in_array( $order_status, $check_status ) ) {
						$title = sprintf( '#%d %s', $order_id, $order->get_formatted_billing_full_name() );
						$billing_information = $this->get_woocommerce_billing_information( $order );
						$items = $order->get_items();
						foreach ( $items as $item ) {
							$room_id = get_post_meta( $item->get_product_id(), '_loftocean_booking_id', true );
							if ( ( ! empty( $room_id ) ) && in_array( $room_id, $rids ) ) {
								$data = get_post_meta( $item->get_variation_id(), 'data', true );
								$checkin = $data[ 'check_in' ];
								$checkout = $data[ 'check_out' ];
								if ( ( ( $start <= $checkin ) && ( $checkin <= $end ) ) || ( ( $start <= $checkout ) && ( $checkout <= $end ) ) || ( ( $checkin < $start ) && ( $checkout > $end ) ) ) {
									$checkin = date( 'Y-m-d', $checkin );
									$checkout = date( 'Y-m-d', $checkout );
									$room_booking_detail = get_post_meta( $item->get_variation_id(), 'data', true );
									array_push( $events, array(
										'is_booking' => true,
										'order_id' => $order_id,
										'room_id' => $room_id,
										'room_title' => get_post_field( 'post_title', $room_id ),
										'room_link' => get_permalink( $room_id ),
										'title' => $title,
										'start' => $checkin,
										'end' => $checkout,
										'backgroundColor' => '#3788d8',
										'borderColor' => '#3788d8',
										'source' => 'woocommerce',
										'link' => admin_url( 'admin.php?page=wc-orders&action=edit&id=' . $order_id ),
										'checkin' => $checkin,
										'checkout' => $checkout,
										'details' => $this->get_woocommerce_order_detail( $room_booking_detail ),
										'extra_services' => $this->get_woocommerce_booking_extra_service( $room_booking_detail ),
										'billing' => $billing_information
									) );
								}
							}
						}
					}
				}
			}
			return $events;
		}
		/**
		* Get status events
		*/
		protected function get_status( $rid, $start, $end ) {
			$status = apply_filters( 'loftocean_get_room_reservation_data', array(), $rid, $start, $end );
			if ( \LoftOcean\is_valid_array( $status ) ) {
				$status = array_filter( $status, function( $item ) {
					return ( $item[ 'status' ] == 'unavailable' ) || ( $item[ 'available_number' ] < 1 );
				} );
			}
			if ( \LoftOcean\is_valid_array( $status ) ) {
				$status = array_map( function( $item ) {
					$item[ 'className' ] = ( $item[ 'available_number' ] < 1 ) ? 'full-booked' : 'blocked';
					$item[ 'display' ] = 'background';
					// $item[ 'backgroundColor' ] = '#b8b7b7';
					$item[ 'is_booking' ] = false;
					return $item;
				}, $status );
			}
			return array_values( $status );
		}
		/**
		* Get imported orders
		*/
		protected function get_imported_orders( $rids, $start, $end ) {
			return apply_filters( 'loftocean_get_imported_booking', array(), $rids, $start, $end );
		}
		/**
		* Get woocommerce booking detail
		*/
		protected function get_woocommerce_order_detail( $data ) {
			$detail = array();
	        array_push( $detail, esc_html__( 'Rooms: ', 'loftocean' ) . $data[ 'room_num_search' ] );
	        array_push( $detail, esc_html__( 'Adults: ', 'loftocean' ) . $data[ 'adult_number' ] );
	        if ( $data[ 'child_number' ] > 0 ) {
				array_push( $detail, esc_html__( 'Children: ', 'loftocean' ) . $data[ 'child_number' ] );
			}
			return implode( ', ', $detail );
		}
		/**
		* Get woocommerce booking extra services
		*/
		protected function get_woocommerce_booking_extra_service( $data ) {
			$services = array();
			$room_order_extra_services = $data[ 'extra_services' ];
            $titles = $room_order_extra_services[ 'titles' ];
            $prices = $room_order_extra_services[ 'prices' ];
            $method = $room_order_extra_services[ 'method' ];
            $label = $room_order_extra_services[ 'label' ];
            $unit = $room_order_extra_services[ 'unit' ];
            $quantity = $room_order_extra_services[ 'quantity' ];
            foreach ( $room_order_extra_services[ 'services' ] as $index => $service_id ) {
				$service = $titles[ $index ] . ' (';
				$service .= $label[ $index ];
				$service .= in_array( $method[ $index ], array( 'custom', 'auto_custom' ) ) ? ' x ' . $quantity[ $index ] : '';
				$service .= ')';
                array_push( $services, $service );
            }
			return implode( ', ', $services );
		}
		/**
		* Get WooCommerce billing information
		*/
		protected function get_woocommerce_billing_information( $order ) {
			return array(
				'full_name' => $order->get_formatted_billing_full_name(),
 				'address' => $order->get_formatted_billing_address(),
				'email' => $order->get_billing_email(),
				'phone' => $order->get_billing_phone()
			);
		}
        /**
        * Order table structure
        */
        protected function get_order_table_structure() {
			global $wpdb;
			$collate = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';
            return "CREATE TABLE {$wpdb->prefix}{$this->order_table} (
                id bigint(20) unsigned NOT NULL auto_increment,
                room_unique_id text NOT NULL,
                checkin bigint(20) unsigned NOT NULL,
                checkout bigint(20) unsigned NOT NULL,
                booked int(10) unsigned NULL,
                sync_from_other_platform text NULL,
                PRIMARY KEY (id)
            ) $collate;";
        }
    }
    new Orders();
}
