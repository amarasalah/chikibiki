<?php
namespace LoftOcean\Room;

if ( ! class_exists( '\LoftOcean\Room\Availability' ) ) {
	class Availability {
		/**
		* Room post type
		*/
		protected $room_post_type = 'loftocean_room';
		/*
		* Room custom data table name
		*/
		protected $room_custom_data_table = 'loftocean_room_custom_data';
		/*
		* Order table name
		*/
		protected $order_table = 'loftocean_room_order';
		/**
		* Current room data
		*/
		protected $current_room_data = array();
		/*
		* Construction function
		*/
		public function __construct() {
			$this->check_tables();

			add_action( 'rest_api_init', array( $this, 'register_rest_api' ) );
			add_action( 'loftocean_update_room_order', array( $this, 'update_room_order' ), 10, 2 );
			add_action( 'loftocean_update_room_imported_orders', array( $this, 'update_imported_room_order' ), 10, 3 );
			add_action( 'loftocean_cancel_room_imported_orders', array( $this, 'cancel_imported_room_order' ), 10, 3 );

			add_filter( 'loftocean_get_unavailable_rooms', array( $this, 'get_unavailable_rooms' ), 99, 2 );
			add_filter( 'loftocean_get_room_reservation_data', array( $this, 'get_room_reservation_data' ), 10, 5 );
		}
		/*
		* Register REST APIs
		*/
		public function register_rest_api() {
			// Get room availability
			register_rest_route( 'loftocean/v1', '/get_room_availability/(?P<nonce>.+)', array(
				'methods' 	=> 'POST',
				'permission_callback' => '__return_true',
				'callback' 	=> array( $this, 'get_room_availability' )
			) );
			register_rest_route( 'loftocean/v1', '/update_room_availability/(?P<nonce>.+)', array(
				'methods' 	=> 'POST',
				'permission_callback' => '__return_true',
				'callback' 	=> array( $this, 'update_room_availability' )
			) );
		}
        /**
        * Update room order
        */
        public function update_room_order( $data, $status ) {
            if ( $data[ 'room_id' ] && ( $this->room_post_type == get_post_type( $data[ 'room_id' ] ) ) ) {
                global $wpdb;
				$order_table = $wpdb->prefix . $this->order_table;
				$room_unique_id = $this->get_room_unique_id( $data[ 'room_id' ] );
                $row = $wpdb->get_row( $wpdb->prepare( "SELECT id, booked, sync_from_other_platform FROM {$order_table} WHERE room_unique_id = %s AND checkin = %d;", $room_unique_id, $data[ 'check_in' ] ), ARRAY_A );
				$number = $this->get_current_room_data( $data[ 'room_id' ], $data );
                if ( \LoftOcean\is_valid_array( $row ) ) {
					$room_left = $this->get_room_number_left( $number, $row );
                    $is_valid = ( 'paid' == $status ? ( $room_left >= $data[ 'number' ] ) : ( $row[ 'booked' ] >= $data[ 'number' ] ) );
                    if ( $is_valid ) {
                        $wpdb->update(
							$order_table,
							array( 'booked' => ( 'paid' == $status ? ( $row[ 'booked' ] + $data[ 'number' ] ) : ( $row[ 'booked' ] - $data[ 'number' ] ) ) ),
							array( 'id' => $row[ 'id' ] ),
							array( '%d' ),
							array( '%d' )
						);
                    }
                } else if ( 'paid' == $status ) {
                    if ( is_numeric( $number ) && ( $number >= $data[ 'number' ] ) ) {
                        $wpdb->insert(
                            $order_table,
                            array(
                                'room_unique_id' => $room_unique_id,
                                'checkin' => $data[ 'check_in' ],
                                'checkout' => strtotime( '+1 day', $data[ 'check_in' ] ),
                                'booked' => $data[ 'number' ]
                            ),
                            array( '%s', '%d', '%d', '%d' ),
                        );
                    }
                }
            }
        }
		/**
		* Get room reservation data
		*/
		public function get_room_reservation_data( $data, $rid, $start, $end, $force_start_date = false ) {
			return $this->get_room_availability( array( 'rid' => $rid, 'start' => $start, 'end' => $end ), $force_start_date );
		}
		/**
		* REST API get room availability for ajax request
		*/
		public function get_room_availability( $data, $force_start_date = false ) {
			$rid = isset( $data[ 'rid' ] ) ? $data[ 'rid' ] : false;
			$start = isset( $data[ 'start' ] ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $data[ 'start' ] ) ? strtotime( $data[ 'start' ] ) : false;
			$end = isset( $data[ 'end' ] ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $data[ 'end' ] ) ? strtotime( $data[ 'end' ] ) : false;
			if ( ( ! $rid ) || ( $this->room_post_type != get_post_type( $rid ) ) || ( ! $start ) || ( ! $end ) ) return false;

			$room_unique_id = $this->get_room_unique_id( $rid );
			$room_details = apply_filters( 'loftocean_get_room_details', array(), $rid );
			$has_details = \LoftOcean\is_valid_array( $room_details ) && \LoftOcean\is_valid_array( $room_details[ 'roomSettings' ] );

			$prices = \LoftOcean\get_room_prices( $rid );
			$has_weekend_night_price = ! empty( $prices[ 'weekend' ][ 'night' ] );
			$has_weekend_adult_price = ! empty( $prices[ 'weekend' ][ 'adult' ] );
			$has_weekend_child_price = ! empty( $prices[ 'weekend' ][ 'child' ] );
			$default_room_number = $room_details[ 'roomSettings' ][ 'roomNumber' ];

			$records = $this->get_availability_monthly_data( $rid, $start, $end );
			$records = array_combine( array_column( $records, 'checkin' ), $records );
			$json_return = array();

			$special_prices = apply_filters( 'loftocean_room_get_special_prices', false, $rid );
			$has_special_prices = \LoftOcean\is_valid_array( $special_prices );

			$start = $force_start_date ? $start : max( $start, strtotime( date( 'Y-m-d' ) ) );
			for ( $i = $start; $i <= $end; $i ) {
				$is_weekend = \LoftOcean\is_weekend( $i );
				$default_rate = $has_weekend_night_price && $is_weekend ? $prices[ 'weekend' ][ 'night' ] : $prices[ 'regular' ][ 'night' ];
				$default_adult_rate = $has_weekend_adult_price && $is_weekend ? $prices[ 'weekend' ][ 'adult' ] : $prices[ 'regular' ][ 'adult' ];
				$default_child_rate = $has_weekend_child_price && $is_weekend ? $prices[ 'weekend' ][ 'child' ] : $prices[ 'regular' ][ 'child' ];
				if ( isset( $records[ $i ] ) ) {
					$custom_number = is_null( $records[ $i ][ 'number' ] ) ? $default_room_number : $records[ $i ][ 'number' ];
					$custom_rate = is_null( $records[ $i ][ 'price' ] ) ? $default_rate : $records[ $i ][ 'price' ];
					$available_number = $this->check_room_available_number( $room_unique_id, $i, $custom_number );
					$json_return[] = array(
						'id' => $i,
						'allDay' => true,
						'title' => 'Price ' . $custom_rate,
						'price' => $custom_rate,
						'start' => date( 'Y-m-d', $records[ $i ][ 'checkin' ] ),
						'end' => date( 'Y-m-d', $records[ $i ][ 'checkout' ] ),
						'status' => ( $available_number > 0 ) ? $records[ $i ][ 'status' ] : 'unavailable',
						'original_status' => $records[ $i ][ 'status' ],
						'adult_price' => is_null( $records[ $i ][ 'adult_price' ] ) ? $default_adult_rate : $records[ $i ][ 'adult_price' ],
						'child_price' => is_null( $records[ $i ][ 'child_price' ] ) ? $default_child_rate : $records[ $i ][ 'child_price' ],
						'number' => is_null( $records[ $i ][ 'number' ] ) ? $default_room_number : $records[ $i ][ 'number' ],
						'available_number' => $available_number,
						'is_weekend' => $is_weekend ? 'yes' : 'no',
						'special_price_rate' => $has_special_prices ? $this->get_special_price_rate( $special_prices, $i ) : 1
					);
				} else if ( $has_details ) {
					$available_number = $this->check_room_available_number( $room_unique_id, $i, $default_room_number );
					$json_return[] = array(
						'id' => $i,
						'allDay' => true,
						'title' => 'Price ' . $default_rate,
						'price' => $default_rate,
						'start' => date( 'Y-m-d', $i ),
						'end' => date( 'Y-m-d', strtotime('+1 day', $i ) ),
						'status' => ( $available_number > 0 ) ? 'available' : 'unavailable',
						'original_status' => 'available',
						'adult_price' => $default_adult_rate,
						'child_price' => $default_child_rate,
						'number' => $default_room_number,
						'available_number' => $available_number,
						'is_weekend' => $is_weekend ? 'yes' : 'no',
						'special_price_rate' => $has_special_prices ? $this->get_special_price_rate( $special_prices, $i ) : 1
					);
				}
				$i += LOFTICEAN_SECONDS_IN_DAY;
			}
			return $json_return;
		}
		/**
		* Get special price rate
		*/
		protected function get_special_price_rate( $list, $date ) {
			return \LoftOcean\get_special_price_rate( $list, $date );
		}
		/**
		* REST API update room availability data
		*/
		public function update_room_availability( $data ) {
			$rid = wp_unslash( $data[ 'rid' ] );
			$d = wp_unslash( $data[ 'data' ] );
			if ( ( ! empty( $rid ) ) && ( ! empty( $d ) ) ) {
				$rids = apply_filters( 'loftocean_availability_data_get_room_ids', array( $rid ), $rid );
				if ( \LoftOcean\is_valid_array( $rids ) ) {
					$d = base64_decode( $d );
					$d = json_decode( $d, true );
					if ( \LoftOcean\is_valid_array( $d ) ) {
						$start = strtotime( $d[ 'checkin' ] );
						$end = strtotime( $d[ 'checkout' ] );
						$default_data = $this->get_default_room_data( $rid );
						for( $i = $start; $i < $end; $i ) {
							$d[ 'checkin' ] = $i;
							$i += LOFTICEAN_SECONDS_IN_DAY;
							$d[ 'checkout' ] = $i;
							$updated_data = $this->get_updated_custom_data( $rid, $default_data, $d );
							foreach ( $rids as $rid ) {
								if ( false === $updated_data ) {
									global $wpdb;
									$wpdb->delete( $wpdb->prefix . $this->room_custom_data_table, array( 'room_id' => $rid, 'checkin' => $d[ 'checkin' ] ), array( '%d', '%d' ) );
								} else {
									$this->update_availability_data( $rid, $updated_data );
								}
							}
						}
					}
				}
			}
			return array( 'updated' => true );
		}
		/**
		* Update availability database
		*/
		public function update_availability_data( $rid, $data ) {
			global $wpdb;
			$id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}{$this->room_custom_data_table} WHERE room_id = %d AND checkin = %d;", $rid, $data[ 'checkin' ] ) );
			if ( $id ) {
				$wpdb->update(
					$wpdb->prefix . $this->room_custom_data_table,
					array(
						'price' => $data[ 'price' ],
                        'number' => $data[ 'number' ],
                        'adult_price' => $data[ 'adult_price' ],
                        'child_price' => $data[ 'child_price' ],
                        'status' => $data[ 'status' ]
					),
					array( 'id' => $id ),
					array( '%f', '%d', '%f', '%f', '%s' ),
					array( '%d' )
				);
			} else {
				$wpdb->insert(
					$wpdb->prefix . $this->room_custom_data_table,
					array(
						'room_id' => $rid,
						'checkin' => $data[ 'checkin' ],
						'checkout' => $data[ 'checkout' ],
						'price' => $data[ 'price' ],
                        'number' => $data[ 'number' ],
                        'adult_price' => $data[ 'adult_price' ],
                        'child_price' => $data[ 'child_price' ],
                        'status' => $data[ 'status' ],
						'discount' => 100,
						'number_booked' => 0
					),
					array( '%d', '%d', '%d', '%f', '%d', '%f', '%f', '%s', '%d', '%d' ),
				);
			}
		}
		/*
		* Get room availability monthly
		* @param int room id
		* @param int month
		* @param int year
		*/
		public function get_availability_monthly_data( $rid, $start = '', $end = '' ) {
			if ( ! empty( $rid ) ) {
				global $wpdb;
 				$data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}{$this->room_custom_data_table} WHERE room_id = %d AND checkin >= %d AND checkin < %d;", $rid, $start, $end ), ARRAY_A );
				return \LoftOcean\is_valid_array( $data ) ? $data : array();
			}
			return array();
		}
		/**
		* Get unavailable rooms
		*/
		public function get_unavailable_rooms( $rooms, $data ) {
			global $wpdb;
			$checkin = $data[ 'checkin' ];
			$room_number_check = empty( $data[ 'room-quantity' ] ) || ( ! is_numeric( $data[ 'room-quantity' ] ) ) || ( $data[ 'room-quantity' ] < 1 ) ? 1 : $data[ 'room-quantity' ];
			$where = "( status = 'unavailable' OR ( number IS NOT NULL AND number < " . $room_number_check . " ) )";
			$where .= sprintf( ' AND checkin >= %d', $checkin );
			if ( isset( $data[ 'checkout' ] ) ) {
				$where .= sprintf( ' AND checkin < %d', $data[ 'checkout' ] );
			}
			$results = $wpdb->get_results( "SELECT id, room_id FROM {$wpdb->prefix}{$this->room_custom_data_table} WHERE {$where} GROUP BY room_id;", ARRAY_A );
			$excluded_ids = array_map( function( $item ) {
				return $item[ 'room_id' ];
			}, $results );

			if ( ! empty( $data[ 'room-quantity' ] ) ) {
				$query_args = array( 'posts_per_page' => -1, 'offset' => 0, 'post_type' => 'loftocean_room', 'fields' => 'ids', 'post_status' => ( is_user_logged_in() ? array( 'publish', 'private' ) : 'publish' ) );
                if ( \LoftOcean\is_valid_array( $excluded_ids ) ) {
                	$query_args[ 'post__not_in' ] = $excluded_ids;
                }

               $query_args[ 'meta_query' ] = array( array(
                    'key' => 'loftocean_room_number',
                    'value' => $data[ 'room-quantity' ],
                    'compare' => '<',
                    'type' => 'NUMERIC'
                ) );

				$results = new \WP_Query( $query_args );
				if ( \LoftOcean\is_valid_array( $results->posts ) ) {
					foreach ( $results->posts as $pid ) {
						array_push( $excluded_ids, $pid );
					}
				}
			}

			$where = sprintf( 'checkin >= %d', $checkin );
			if ( isset( $data[ 'checkout' ] ) ) {
				$where .= sprintf( ' AND checkin < %d', $data[ 'checkout' ] );
			}
			$results = $wpdb->get_results( "SELECT room_unique_id, checkin, booked, sync_from_other_platform FROM {$wpdb->prefix}{$this->order_table} WHERE {$where};", ARRAY_A );
			if ( \LoftOcean\is_valid_array( $results ) ) {
				$checked_room_ids = array();
				foreach ( $results as $item ) {
					$room_IDs = \LoftOcean\Room\Relationship_Tools::get_room_ids_from_relationship_id( $item[ 'room_unique_id' ] );
					if ( \LoftOcean\is_valid_array( $room_IDs ) ) {
						foreach( $room_IDs as $room_id ) {
							if ( in_array( $room_id, $checked_room_ids ) || in_array( $room_id, $excluded_ids ) ) continue;

							array_push( $checked_room_ids, $room_id );
							$item[ 'check_in' ] = $item[ 'checkin' ];
							$room_number = $this->get_current_room_data( $room_id, $item );
							$room_number_left = $this->get_room_number_left( $room_number, $item );
							if ( $room_number_left < $room_number_check ) {
								array_push( $excluded_ids, $room_id );
							}
						}
					}
				}
			}
			return array_unique( $excluded_ids );
		}
		/**
		* Check tables
		*/
		protected function check_tables() {
			global $wpdb;
			$test_table = $wpdb->prefix . $this->order_table;
			$table_name_found = $wpdb->get_var( 'SHOW TABLES LIKE "' . $test_table . '"' );
			if ( $wpdb->prefix . $this->order_table != $table_name_found ) {
				$tables = $this->get_tables();
				foreach( $tables as $table ) {
					$wpdb->query( $table );
				}
			} else if ( 'yes' != get_option( 'loftocean_room_tables_updated' ) ) {
				$this->update_existing_tables();
				update_option( 'loftocean_room_tables_updated', 'yes' );
			}
		}
		/**
		* update current tables
		*/
		protected function update_existing_tables() {
			global $wpdb;
			$availability_table_name = $wpdb->prefix . 'loftocean_room_availability';
			$table_name_found = $wpdb->get_var( 'SHOW TABLES LIKE "' . $availability_table_name . '"' );
			if ( $availability_table_name == $table_name_found ) {
				$custom_data_table = $wpdb->prefix . $this->room_custom_data_table;
				$wpdb->query( "ALTER TABLE {$availability_table_name} MODIFY COLUMN price float unsigned, MODIFY COLUMN adult_price float unsigned, MODIFY COLUMN child_price float unsigned, MODIFY COLUMN number int(10) unsigned DEFAULT NULL;" );
				$wpdb->query( "RENAME TABLE {$availability_table_name} TO {$custom_data_table};" );
			}
			do_action( 'loftocean_update_room_custom_tables' );
		}
		/*
		* Get tables structures
		*/
		protected function get_tables() {
			global $wpdb;
			$collate = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';
			return apply_filters( 'loftocean_room_custom_tables', array(
					"CREATE TABLE {$wpdb->prefix}{$this->room_custom_data_table} (
						id bigint(20) unsigned NOT NULL auto_increment,
						room_id bigint(20) unsigned NOT NULL,
						checkin bigint(20) unsigned NOT NULL,
						checkout bigint(20) unsigned NOT NULL,
						number int(10) unsigned NULL,
						price float unsigned NULL,
						status char(20) NOT NULL,
						number_booked int(10) unsigned NOT NULL,
						allow_full_day char(5) NOT NULL,
						discount int(3) unsigned NOT NULL DEFAULT 100,
						adult_number int unsigned,
						adult_price float unsigned NULL,
						child_number int unsigned,
						child_price float unsigned NULL,
						PRIMARY KEY (id)
					) $collate;"
			) );
		}
		/**
		* Get current room data
		*/
		protected function get_current_room_data( $rid, $data ) {
			if ( empty( $rid ) || ( $this->room_post_type != get_post_type( $rid ) ) ) return false;

			$key = 'room-' . $rid;
			if ( empty( $this->current_room_data[ $key ] ) ) {
				global $wpdb;
				$custom_data_table = $wpdb->prefix . $this->room_custom_data_table;
				$custom_number = $wpdb->get_var( $wpdb->prepare( "SELECT number FROM {$custom_data_table} WHERE room_id = %d AND checkin = %d;", $rid, $data[ 'check_in' ] ) );
				$this->current_room_data[ $key ] = is_null( $custom_number ) ? get_post_meta( $rid, 'loftocean_room_number', true ) : $custom_number;
			}
			return $this->current_room_data[ $key ];
		}
		/**
		* Get room number available
		*/
		protected function get_room_number_left( $number, $data ) {
			if ( ! is_numeric( $number ) ) return 0;

			if ( ! empty( $data[ 'booked' ] ) ) {
				$number -= $data[ 'booked' ];
			}
			if ( ( ! is_null( $data[ 'sync_from_other_platform' ] ) ) && ( ! empty( $data[ 'sync_from_other_platform' ] ) ) ) {
				$imported_orders = $this->decode( $data[ 'sync_from_other_platform' ], array() );
				if ( \LoftOcean\is_valid_array( $imported_orders ) ) {
					foreach( $imported_orders as $platform => $num ) {
						if ( is_numeric( $num ) ) {
							$number -= $num;
						}
					}
				}
			}
			return $number;
		}
		/**
		* Get default room data
		*/
		protected function get_default_room_data( $rid ) {
			$data = \LoftOcean\get_room_prices( $rid );
			$data[ 'number' ] = get_post_meta( $rid, 'loftocean_room_number', true );
			$data[ 'enabled_weekend_prices' ] = ( 'on' == get_post_meta( $rid, 'loftocean_room_enable_weekend_prices', true ) ) ? 'yes' : 'no';
			return $data;
		}
		/**
		* Get updated custom data
		*/
		protected function get_updated_custom_data( $rid, $default, $data ) {
			if ( empty( $rid ) || ( $this->room_post_type != get_post_type( $rid ) ) ) return false;
			if ( ! \LoftOcean\is_valid_array( $data ) ) return false;

			$current_data = array( 'status' => 'available', 'number' => $default[ 'number' ] );
			if ( ( 'yes' == $default[ 'enabled_weekend_prices' ] ) && \LoftOcean\is_weekend( $data[ 'checkin' ] ) ) {
				$current_data[ 'price' ] = $default[ 'weekend' ][ 'night' ];
				$current_data[ 'adult_price' ] = $default[ 'weekend' ][ 'adult' ];
				$current_data[ 'child_price' ] = $default[ 'weekend' ][ 'child' ];
			} else {
				$current_data[ 'price' ] = $default[ 'regular' ][ 'night' ];
				$current_data[ 'adult_price' ] = $default[ 'regular' ][ 'adult' ];
				$current_data[ 'child_price' ] = $default[ 'regular' ][ 'child' ];
			}

			$passed = false;
			$test_keys = array_keys( $current_data );
			foreach ( $data as $key => $val ) {
				if ( in_array( $key, $test_keys ) ) {
					$is_number_compare = is_numeric( $val ) && is_numeric( $current_data[ $key ] );
					if ( ( ( ! $is_number_compare ) && ( $val === $current_data[ $key ] ) ) || ( $is_number_compare && ( abs( $val - $current_data[ $key ] ) < LOFTOCEAN_FLOAT_EPSILON ) ) ) {
						$data[ $key ] = ( 'status' == $key ) ? 'available' : null;
					} else {
						$passed = true;
					}
				}
			}
			return $passed ? $data : false;
		}
		/**
		* Check room current available number
		*/
		protected function check_room_available_number( $ruid, $checkin, $default ) {
			global $wpdb;
			$order_table = $wpdb->prefix . $this->order_table;
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT id, booked, sync_from_other_platform FROM {$order_table} WHERE room_unique_id = %s AND checkin = %d;", $ruid, $checkin ), ARRAY_A );

			if ( \LoftOcean\is_valid_array( $row ) ) {
				return $this->get_room_number_left( $default, $row );
			} else {
				return $default;
			}
		}
		/**
		* Get room unique ID
		*/
		protected function get_room_unique_id( $rid ) {
			return \LoftOcean\get_room_unique_id( $rid );
		}
		/**
		* Update imported room order
		*/
		public function update_imported_room_order( $items, $roomID, $source ) {
			if ( ( $this->room_post_type != get_post_type( $roomID ) ) || empty( $source ) ) return;

			global $wpdb;
			$order_table = $wpdb->prefix . $this->order_table;
			$room_unique_id = $this->get_room_unique_id( $roomID );
			$results = $wpdb->get_results( $wpdb->prepare( "SELECT id, booked, sync_from_other_platform FROM {$order_table} WHERE room_unique_id = %s;", $room_unique_id ), ARRAY_A );
			if ( \LoftOcean\is_valid_array( $results ) ) {
				foreach( $results as $item ) {
					$imported_orders = $this->decode( $item[ 'sync_from_other_platform' ], array() );
					if ( \LoftOcean\is_valid_array( $imported_orders ) && isset( $imported_orders[ $source ] ) ) {
						unset( $imported_orders[ $source ] );
						$imported_orders = $this->encode( $imported_orders );
						$wpdb->update(
							$order_table,
							array( 'sync_from_other_platform' => $imported_orders ),
							array( 'id' => $item[ 'id' ] ),
							array( '%s' ),
							array( '%d' )
						);
					}
				}
			}

			foreach( $items as $item ) {
				for( $i = $item[ 'checkin' ]; $i < $item[ 'checkout' ]; $i = strtotime( '+1 day', $i ) ) {
					$row = $wpdb->get_row( $wpdb->prepare( "SELECT id, booked, sync_from_other_platform FROM {$order_table} WHERE room_unique_id = %s AND checkin = %d;", $room_unique_id, $i ), ARRAY_A );
					if ( \LoftOcean\is_valid_array( $row ) ) {
						$imported_orders = $this->decode( $row[ 'sync_from_other_platform' ], array() );
						if ( isset( $imported_orders[ $source ] ) ) {
							$imported_orders[ $source ] += $item[ 'num' ];
						} else {
							$imported_orders[ $source ] = $item[ 'num' ];
						}
						$wpdb->update(
							$order_table,
							array( 'sync_from_other_platform' => $this->encode( $imported_orders ) ),
							array( 'id' => $row[ 'id' ] ),
							array( '%s' ),
							array( '%d' )
						);
					} else {
	                    $wpdb->insert(
	                        $order_table,
	                        array(
	                            'room_unique_id' => $room_unique_id,
	                            'checkin' => $i,
	                            'checkout' => strtotime( '+1 day', $i ),
	                            'booked' => 0,
								'sync_from_other_platform' => $this->encode( array( $source => $item[ 'num' ] ) )
	                        ),
	                        array( '%s', '%d', '%d', '%d', '%s' ),
	                    );
					}
				}
			}
		}
		/**
		* Remove booking number
		*/
		public function cancel_imported_room_order( $items, $roomID, $source ) {
			if ( ( $this->room_post_type != get_post_type( $roomID ) ) || empty( $source ) ) return;

			global $wpdb;
			$order_table = $wpdb->prefix . $this->order_table;
			$room_unique_id = $this->get_room_unique_id( $roomID );
			foreach( $items as $item ) {
				for( $i = $item[ 'checkin' ]; $i < $item[ 'checkout' ]; $i = strtotime( '+1 day', $i ) ) {
					$row = $wpdb->get_row( $wpdb->prepare( "SELECT id, booked, sync_from_other_platform FROM {$order_table} WHERE room_unique_id = %s AND checkin = %d;", $room_unique_id, $i ), ARRAY_A );
					if ( \LoftOcean\is_valid_array( $row ) ) {
						$imported_orders = $this->decode( $row[ 'sync_from_other_platform' ], array() );
						if ( isset( $imported_orders[ $source ] ) ) {
							if ( $imported_orders[ $source ] > $item[ 'num' ] ) {
								$imported_orders[ $source ] -= $item[ 'num' ];
							} else {
								unset( $imported_orders[ $source ] );
							}
						}
						$imported_orders = array_filter( $imported_orders );
						$wpdb->update(
							$order_table,
							array( 'sync_from_other_platform' => \LoftOcean\is_valid_array( $imported_orders ) ? $this->encode( $imported_orders ) : '' ),
							array( 'id' => $row[ 'id' ] ),
							array( '%s' ),
							array( '%d' )
						);
					}
				}
			}
		}
		/**
		* Decode a variable
		*/
		protected function decode( $var, $default ) {
			return empty( $var ) ? $default : maybe_unserialize( $var );
		}
		/**
		* Encode a variable
		*/
		protected function encode( $var ) {
			return empty( $var ) || ( ! \LoftOcean\is_valid_array( $var ) ) ? '' : maybe_serialize( $var );
		}
	}
	new Availability();
}
