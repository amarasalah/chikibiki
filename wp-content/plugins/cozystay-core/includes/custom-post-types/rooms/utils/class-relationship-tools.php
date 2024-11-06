<?php
namespace LoftOcean\Room;

if ( ! class_exists( '\LoftOcean\Room\Relationship_Tools' ) ) {
	class Relationship_Tools {
		/**
		* Room post type
		*/
		public static $room_post_type = 'loftocean_room';
        /**
        * Room relationship table name
        */
        public static $room_relationship_table = 'loftocean_room_relationship';
        /**
        * Room relationship list
        */
        public static $room_relationships = null;
		/**
		* Room IDs
		*/
		public static $room_IDs_from_relationship = null;
        /**
        * Update room relationship
        */
        public static function update_room_relationship( $data ) {
            if ( isset( $data[ 'room_id' ], $data[ 'relationship_id' ] ) ) {
                global $wpdb;
                $relationship_table = $wpdb->prefix . self::$room_relationship_table;
                $row = $wpdb->get_row( $wpdb->prepare( "SELECT room_id FROM {$relationship_table} WHERE room_id = %d;", $data[ 'room_id' ] ), ARRAY_A );
                if ( \LoftOcean\is_valid_array( $row ) ) {
                    $wpdb->delete( $relationship_table, array( 'room_id' => $data[ 'room_id' ] ), array( '%d' ) );
                }
                $wpdb->insert( $relationship_table, array( 'room_id' => $data[ 'room_id' ], 'room_unique_id' => $data[ 'relationship_id' ] ), array( '%d', '%s' ) );
            }
        }
        /**
        * Get room relationship ID
        */
        public static function get_room_relationship( $room ) {
            $index_key = 'room_' . $room;
            if ( is_null( self::$room_relationships ) ) {
                global $wpdb;
                self::$room_relationships = array();
                $relationship_table = $wpdb->prefix . self::$room_relationship_table;
    			$results = $wpdb->get_results( "SELECT * FROM {$relationship_table};", ARRAY_A );
                if ( \LoftOcean\is_valid_array( $results ) ) {
                    foreach ( $results as $result ) {
                        self::$room_relationships[ 'room_' . $result[ 'room_id' ] ] = $result[ 'room_unique_id' ];
                    }
                }
            }
            return isset( self::$room_relationships[ $index_key ] ) ? self::$room_relationships[ $index_key ] : false;
        }
        /**
        * Delete room relationship
        */
        public static function delete_room_relationship( $room ) {
            if ( isset( $room ) ) {
                global $wpdb;
                $relationship_table = $wpdb->prefix . self::$room_relationship_table;
                $wpdb->delete( $relationship_table, array( 'room_id' => $room ), array( '%d' ) );
            }
        }
		/**
		* Reset relationship table
		*/
		public static function reset_relationship_table() {
			global $wpdb;
            $relationship_table = $wpdb->prefix . self::$room_relationship_table;
            $wpdb->query( "TRUNCATE TABLE {$relationship_table};" );
		}
        /**
        * Get room relationship ID
        */
        public static function get_room_ids_from_relationship_id( $relationship_id ) {
            if ( is_null( self::$room_IDs_from_relationship ) ) {
                global $wpdb;
                self::$room_IDs_from_relationship = array();
                $relationship_table = $wpdb->prefix . self::$room_relationship_table;
    			$results = $wpdb->get_results( "SELECT * FROM {$relationship_table};", ARRAY_A );
                if ( \LoftOcean\is_valid_array( $results ) ) {
                    foreach ( $results as $result ) {
						if ( isset( self::$room_IDs_from_relationship[ $result[ 'room_unique_id' ] ] ) ) {
							array_push( self::$room_IDs_from_relationship[ $result[ 'room_unique_id' ] ], $result[ 'room_id' ] );
						} else {
							self::$room_IDs_from_relationship[ $result[ 'room_unique_id' ] ] = array( $result[ 'room_id' ] );
						}
                    }
                }
            }
            return isset( self::$room_IDs_from_relationship[ $relationship_id ] ) ? self::$room_IDs_from_relationship[ $relationship_id ] : false;
        }
		/**
		* Test if relationship ID used
		*/
		public static function test_relationship_id( $relationship_id ) {
			global $wpdb;
			self::$room_relationships = array();
			$relationship_table = $wpdb->prefix . self::$room_relationship_table;
			$results = $wpdb->get_results( $wpdb->prepare( "SELECT room_id FROM {$relationship_table} WHERE room_unique_id = %s;", $relationship_id ), ARRAY_A );
			if ( \LoftOcean\is_valid_array( $results ) && ( count( $results ) > 1 ) ) {
				return false;
			}
			return true;
		}
    }
}
