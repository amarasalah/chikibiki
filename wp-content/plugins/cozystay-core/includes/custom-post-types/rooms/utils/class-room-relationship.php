<?php
namespace LoftOcean\Room;

if ( ! class_exists( '\LoftOcean\Room\Room_Relationship' ) ) {
	class Room_Relationship {
		/**
		* Room post type
		*/
		protected $room_post_type = 'loftocean_room';
        /**
        * Room relationship table name
        */
        protected $room_relationship_table = 'loftocean_room_relationship';
        /**
        * Construction function
        */
        public function __construct() {
            add_filter( 'loftocean_room_custom_tables', array( $this, 'room_custom_tables' ) );
            add_action( 'loftocean_update_room_custom_tables', array( $this, 'update_relationship_table' ) );
			add_action( 'loftocean_room_regenerate_relationship_data', array( $this, 'update_existing_rooms_relationship' ) );
			add_action( 'save_post', array( $this, 'after_room_updated' ), 10, 3 );
			add_action( 'delete_post', array( $this, 'after_room_deleted' ), 10, 2 );
        }
        /**
        * Update room custom tables
        */
        public function room_custom_tables( $tables ) {
            $tables[] = $this->get_relationship_table_structure();
            return $tables;
        }
        /**
        * Update relationship table
        */
        public function update_relationship_table() {
            global $wpdb;
			$relationship_table_name = $wpdb->prefix . $this->room_relationship_table;
			$table_name_found = $wpdb->get_var( 'SHOW TABLES LIKE "' . $relationship_table_name . '"' );
			if ( $relationship_table_name != $table_name_found ) {
				$table_sql = $this->get_relationship_table_structure();
				$wpdb->query( $table_sql );
                add_action( 'init', array( $this, 'update_existing_rooms_relationship' ), 20 );
                do_action( 'loftocean_update_existing_room_relationships' );
			}
        }
        /**
        * Update existing room relationships
        */
        public function update_existing_rooms_relationship() {
            $rooms = $this->get_rooms();
            $relationships = array();
            foreach ( $rooms as $rid ) {
                $index_prefix = 'room_';
                if ( ! isset( $relationships[ $index_prefix . $rid ] ) ) {
                    $rIDs = \LoftOcean\get_room_translated_ids( $rid, true );
                    $unique_ID = \LoftOcean\generate_room_unique_relationship_id();
                    foreach( $rIDs as $trid ) {
                        $key = $index_prefix . $trid;
                        if ( ! isset( $relationships[ $key ] ) ) {
                            $relationships[ $key ] = array( 'room_id' => $trid, 'relationship_id' => $unique_ID );
                        }
                    }
                }
            }
            foreach( $relationships as $relationship ) {
                \LoftOcean\Room\Relationship_Tools::update_room_relationship( $relationship );
            }
        }
		/**
		* After room updated
		*/
		public function after_room_updated( $rid, $room, $update ) {
			if ( empty( $update ) || ( $room->post_type != $this->room_post_type ) ) return '';

			$room_ids = \LoftOcean\get_room_translated_ids( $rid, false );
			$current_relationship_id = \LoftOcean\Room\Relationship_Tools::get_room_relationship( $rid );
			$no_relationship_id = empty( $current_relationship_id );
			$update_args = array( 'room_id' => $rid, 'relationship_id' => '' );
			if ( \LoftOcean\is_valid_array( $room_ids ) ) {
				$other_room_relationship_id = false;
				foreach ( $room_ids as $room_id ) {
					$other_room_relationship_id = \LoftOcean\Room\Relationship_Tools::get_room_relationship( $room_id );
					if ( ! empty( $other_room_relationship_id ) ) {
						if ( $current_relationship_id != $other_room_relationship_id ) {
							$update_args[ 'relationship_id' ] = $other_room_relationship_id;
							\LoftOcean\Room\Relationship_Tools::update_room_relationship( $update_args );
						}
						break;
					}
				}
				if ( empty( $other_room_relationship_id ) && $no_relationship_id ) {
					$update_args[ 'relationship_id' ] = \LoftOcean\generate_room_unique_relationship_id();
					\LoftOcean\Room\Relationship_Tools::update_room_relationship( $update_args );
				}
			} else {
				if ( $no_relationship_id ) {
					$update_args[ 'relationship_id' ] = \LoftOcean\generate_room_unique_relationship_id();
					\LoftOcean\Room\Relationship_Tools::update_room_relationship( $update_args );
				} else if ( ! \LoftOcean\Room\Relationship_Tools::test_relationship_id( $current_relationship_id ) ) {
					$update_args[ 'relationship_id' ] = \LoftOcean\generate_room_unique_relationship_id();
					\LoftOcean\Room\Relationship_Tools::update_room_relationship( $update_args );
				}
			}
		}
		/**
		* After room deleted
		*/
		public function after_room_deleted( $rid, $room ) {
			if ( $this->room_post_type == $room->post_type ) {
				\LoftOcean\Room\Relationship_Tools::delete_room_relationship( $rid );
			}
		}
        /**
        * Room relationship table structure
        */
        protected function get_relationship_table_structure() {
			global $wpdb;
			$collate = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';
            return "CREATE TABLE {$wpdb->prefix}{$this->room_relationship_table} (
                room_id bigint(20) unsigned NOT NULL,
                room_unique_id text NOT NULL,
                PRIMARY KEY (room_id)
            ) $collate;";
        }
        /**
        * Get all rooms
        */
        protected function get_rooms() {
            $rooms = array();
            $ppp = 50;
            $query_args = array( 'post_type' => $this->room_post_type, 'post_status' => 'any', 'offset' => 0, 'posts_per_page' => $ppp, 'fields' => 'ids', 'lang' => '', 'suppress_filters' => true );
            do {
				$q = get_posts( $query_args );
                if ( \LoftOcean\is_valid_array( $q ) ) {
                    $rooms = array_merge( $rooms, $q );
                }
				$query_args['offset'] += $ppp;
			} while ( count( $q ) === $ppp );

            return array_unique( $rooms );
        }
    }
    new Room_Relationship();
}
