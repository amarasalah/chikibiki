<?php
namespace LoftOcean\Room\Settings;

if ( ! class_exists( '\LoftOcean\Room\Settings\General' ) ) {
	class General {
		/**
		* Construct function
		*/
		public function __construct() {
			add_action( 'loftocean_room_the_settings_tabs', array( $this, 'get_room_setting_tabs' ) );
			add_action( 'loftocean_room_the_settings_panel', array( $this, 'the_room_setting_panel' ) );
			add_action( 'loftocean_save_room_settings', array( $this, 'save_room_settings' ) );
		}
		/**
		* Tab titles
		*/
		public function get_room_setting_tabs( $pid ) { ?>
			<li class="loftocean-room room-options tab-general active">
				<a href="#tab-general"><span><?php esc_html_e( 'General', 'loftocean' ); ?></span></a>
			</li><?php
		}
		/**
		* Tab panel
		*/
		public function the_room_setting_panel( $pid ) {
			$room_types = get_terms( array( 'taxonomy' => 'lo_room_type', 'hide_empty' => false ) );
			$data = $this->get_room_data( $pid );
			$current_types = wp_get_post_terms( $pid, 'lo_room_type', array( 'fields' => 'ids' ) ); ?>
			<div id="tab-general-panel" class="panel loftocean-room-setting-panel">
				<div class="options-group">
					<p class="form-field text-field">
						<label for="room_subtitle"><?php esc_html_e( 'Subtitle', 'loftocean' ); ?></label>
						<textarea name="loftocean_room_subtitle" id="room_subtitle"><?php echo $data[ 'room_subtitle' ]; ?></textarea>
					</p>
					<p class="form-field text-field">
						<label for="room_label"><?php esc_html_e( 'Label', 'loftocean' ); ?></label>
						<textarea name="loftocean_room_label" id="room_label"><?php echo $data[ 'room_label' ]; ?></textarea>
					</p><?php
					if ( ( ! is_wp_error( $room_types ) ) && ( count( $room_types ) > 0 ) ) : ?>
						<p class="form-field select2-field">
							<label for="room_type"><?php esc_html_e( 'Room Type', 'loftocean' ); ?></label>
							<select name="loftocean_room_types[]" id="room_type" multiple class="hidden"><?php
								foreach( $room_types as $room_type ) : ?>
									<option value="<?php echo esc_attr( $room_type->term_id ); ?>"<?php if ( in_array( $room_type->term_id, ( array )$current_types ) ) : ?> selected<?php endif; ?>><?php echo $room_type->name; ?></option><?php
								endforeach; ?>
 							</select>
						</p><?php
					endif; ?>
					<p class="form-field number-field">
						<label for="room_number"><?php esc_html_e( 'Number of Rooms', 'loftocean' ); ?></label>
						<input type="number" class="short" name="loftocean_room_number" id="room_number" placeholder="10" min="0" value="<?php echo esc_attr( $data[ 'room_number' ] ); ?>"><br>
						<span class="description"><?php esc_html_e( 'Number of available rooms for booking', 'loftocean' ); ?></span>
					</p>
				</div>
			</div><?php
		}
		/**
		* Get room data
		*/
		protected function get_room_data( $pid ) {
			return array(
				'room_subtitle' => get_post_meta( $pid, 'loftocean_room_subtitle', true ),
				'room_number' => get_post_meta( $pid, 'loftocean_room_number', true ),
				'room_label' => get_post_meta( $pid, 'loftocean_room_label', true )
			);
		}
		/**
		* Save room settings
		*/
		public function save_room_settings( $pid ) {
			if ( isset( $_REQUEST[ 'loftocean_room_types' ] ) && \LoftOcean\is_valid_array( $_REQUEST[ 'loftocean_room_types' ] ) ) {
				$types = array_map( 'absint', wp_unslash( $_REQUEST[ 'loftocean_room_types' ] ) );
				$types = array_filter( $types, function( $item ) {
					$term = get_term( $item, 'lo_room_type', ARRAY_A );
					return ( ! is_wp_error( $term ) ) && \LoftOcean\is_valid_array( $term );
				} );
				$types = \LoftOcean\is_valid_array( $types ) ? $types : array();
				wp_set_post_terms( $pid, $types, 'lo_room_type' );
			} else {
				wp_set_post_terms( $pid, array(), 'lo_room_type' );
			}

			$number = isset( $_REQUEST[ 'loftocean_room_number' ] ) ? absint( wp_unslash( $_REQUEST[ 'loftocean_room_number' ] ) ) : '';
			$label = isset( $_REQUEST[ 'loftocean_room_label' ] ) ? force_balance_tags( wp_unslash( $_REQUEST[ 'loftocean_room_label' ] ) ) : '';
			$subtitle = isset( $_REQUEST[ 'loftocean_room_subtitle' ] ) ? force_balance_tags( wp_unslash( $_REQUEST[ 'loftocean_room_subtitle' ] ) ) : '';

			update_post_meta( $pid, 'loftocean_room_subtitle', $subtitle );
			update_post_meta( $pid, 'loftocean_room_label', $label );
			update_post_meta( $pid, 'loftocean_room_number', $number );
		}
	}
	new General();
}
