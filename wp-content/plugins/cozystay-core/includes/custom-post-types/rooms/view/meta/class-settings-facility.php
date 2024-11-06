<?php
namespace LoftOcean\Room\Settings;

if ( ! class_exists( '\LoftOcean\Room\Settings\Facility' ) ) {
	class Facility {
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
			<li class="loftocean-room room-options tab-facility">
				<a href="#tab-facility"><span><?php esc_html_e( 'Room Facility', 'loftocean' ); ?></span></a>
			</li><?php
		}
		/**
		* Tab panel
		*/
		public function the_room_setting_panel( $pid ) {
			$current_facilities = apply_filters( 'loftocean_get_room_facilities', false ); ?>
			<div id="tab-facility-panel" class="panel loftocean-room-setting-panel hidden">
				<div class="options-group"><?php
				foreach ( $current_facilities as $cf ) : ?>
					<p class="form-field facility-field">
						<label><?php echo esc_html( $cf['description'] ); ?></label><?php
						$callback_func = 'the_' . str_replace( '-', '_', $cf[ 'facility_type' ] );
						method_exists( $this, $callback_func ) ? call_user_func( array( $this, $callback_func ), $cf, $pid ) : ''; ?>
					</p><?php
				endforeach; ?>
				</div>
			</div><?php
		}
		/**
		* Output the html of room footage
		*/
		public function the_room_footage( $facility, $pid ) {
			$number = get_post_meta( $pid, 'loftocean_room_facility_room_footage_number', true );
			// $number = ( '' === $number ) ? 10 : $number;
			$unit = get_post_meta( $pid, 'loftocean_room_facility_room_footage_unit', true );
			$unit = empty( $unit ) ? 'sm' : $unit; ?>

			<input type="hidden" name="loftocean_room_facility[room_footage][term_id]" value="<?php echo esc_attr( $facility['term_id'] ); ?>" >
			<input type="text" name="loftocean_room_facility[room_footage][number]" value="<?php echo esc_attr( $number ); ?>" class="short" min=0 >
			<span class="room-footage-unit-wrapper">
				<input type="radio" name="loftocean_room_facility[room_footage][unit]" value="sm" <?php checked( 'sm', $unit ); ?>> <?php esc_html_e( 'Square Meters', 'loftocean' ); ?>
				<input type="radio" name="loftocean_room_facility[room_footage][unit]" value="sf" <?php checked( 'sf', $unit ); ?>> <?php esc_html_e( 'Square Feet', 'loftocean' ); ?>
			</span><?php
		}
		/*
		* Output the html of guests
		*/
		public function the_guests( $facility, $pid ) {
			$number = get_post_meta( $pid, 'loftocean_room_facility_guests_number', true );
			// $number = ( '' === $number ) ? 2 : $number;
			$label= get_post_meta( $pid, 'loftocean_room_facility_guests_label', true );
			$label = empty( $label ) ? $facility[ 'name' ] : $label; ?>

			<input type="text" name="loftocean_room_facility[guests][number]" value="<?php echo esc_attr( $number ); ?>" class="short" min=0 >
			<input type="text" name="loftocean_room_facility[guests][label]" value="<?php echo esc_attr( $label ); ?>" class="medium" >
			<input type="hidden" name="loftocean_room_facility[guests][term_id]" value="<?php echo esc_attr( $facility['term_id'] ); ?>" ><?php
		}
		/*
		* Output the html of beds
		*/
		public function the_beds( $facility, $pid ) {
			$number = get_post_meta( $pid, 'loftocean_room_facility_beds_number', true );
			// $number = ( '' === $number ) ? 1 : $number;
			$label= get_post_meta( $pid, 'loftocean_room_facility_beds_label', true );
			$label = empty( $label ) ? $facility[ 'name' ] : $label; ?>

			<input type="text" name="loftocean_room_facility[beds][number]" value="<?php echo esc_attr( $number ); ?>" class="short" min=0 >
			<input type="text" name="loftocean_room_facility[beds][label]" value="<?php echo esc_attr( $label ); ?>" class="medium" >
			<input type="hidden" name="loftocean_room_facility[beds][term_id]" value="<?php echo esc_attr( $facility['term_id'] ); ?>" ><?php
		}
		/*
		* Output the html of bathrooms
		*/
		public function the_bathrooms( $facility, $pid ) {
			$number = get_post_meta( $pid, 'loftocean_room_facility_bathrooms_number', true );
			// $number = ! is_numeric( $number ) ? 1 : $number;
			$label= get_post_meta( $pid, 'loftocean_room_facility_bathrooms_label', true );
			$label = empty( $label ) ? $facility[ 'name' ] : $label; ?>

			<input type="text" name="loftocean_room_facility[bathrooms][number]" value="<?php echo esc_attr( $number ); ?>" class="short" min=0 >
			<input type="text" name="loftocean_room_facility[bathrooms][label]" value="<?php echo esc_attr( $label ); ?>" class="medium" >
			<input type="hidden" name="loftocean_room_facility[bathrooms][term_id]" value="<?php echo esc_attr( $facility['term_id'] ); ?>" ><?php
		}
		/*
		* Output the html of free wifi
		*/
		public function the_free_wifi( $facility, $pid ) {
			$facility_id = $facility[ 'term_id' ];
			$enabled = get_post_meta( $pid, 'loftocean_room_facility_enable_' . $facility_id, true );
			$custom_label= get_post_meta( $pid, 'loftocean_room_facility_free_wifi_label', true ); ?>
			<input type="checkbox" name="loftocean_room_facility[free_wifi][enabled]" value="on" <?php checked( 'on', $enabled ); ?>>
			<input type="text" name="loftocean_room_facility[free_wifi][custom_label]" value="<?php echo esc_attr( $custom_label ); ?>" class="medium" >
			<input type="hidden" name="loftocean_room_facility[free_wifi][term_id]" value="<?php echo esc_attr( $facility['term_id'] ); ?>" ><?php
		}
		/*
		* Output the html of air conditioning
		*/
		public function the_air_conditioning( $facility, $pid ) {
			$facility_id = $facility[ 'term_id' ];
			$enabled = get_post_meta( $pid, 'loftocean_room_facility_enable_' . $facility_id, true );
			$custom_label= get_post_meta( $pid, 'loftocean_room_facility_air_conditioning_label', true ); ?>
			<input type="checkbox" name="loftocean_room_facility[air_conditioning][enabled]" value="on" <?php checked( 'on', $enabled ); ?>>
			<input type="text" name="loftocean_room_facility[air_conditioning][custom_label]" value="<?php echo esc_attr( $custom_label ); ?>" class="medium" >
			<input type="hidden" name="loftocean_room_facility[air_conditioning][term_id]" value="<?php echo esc_attr( $facility_id ); ?>" ><?php
		}
		/*
		* Output the html of custom facility
		*/
		public function the_custom_facility( $facility, $pid ) {
			$facility_id = $facility[ 'term_id' ];
			$enabled = get_post_meta( $pid, 'loftocean_room_facility_enable_' . $facility_id, true );
			$custom_label= get_post_meta( $pid, 'loftocean_room_facility_custom_label_' . $facility_id, true ); ?>
			<input type="checkbox" name="loftocean_room_facility[custom][<?php echo esc_attr( $facility_id ); ?>][enabled]" value="on" <?php checked( 'on', $enabled ); ?>>
			<input type="text" name="loftocean_room_facility[custom][<?php echo esc_attr( $facility_id ); ?>][custom_label]" value="<?php echo esc_attr( $custom_label ); ?>" class="medium" >
			<input type="hidden" name="loftocean_room_facility[custom][<?php echo esc_attr( $facility_id ); ?>][term_id]" value="<?php echo esc_attr( $facility_id ); ?>" ><?php
		}
		/**
		* Save room settings
		*/
		public function save_room_settings( $pid ) {
			$name_prefix = 'loftocean_room_facility';
			if ( isset( $_REQUEST[ $name_prefix ] ) ) {
				$taxonomy = 'lo_room_facilities';
				$data = wp_unslash( $_REQUEST[ $name_prefix ] );
				$facilities = array();
				// Room Footage
				if ( isset( $data[ 'room_footage' ] ) ) {
					$term = get_term( absint( $data[ 'room_footage' ][ 'term_id' ] ), $taxonomy, ARRAY_A );
					if ( ( ! is_wp_error( $term ) ) && \LoftOcean\is_valid_array( $term ) ) {
						update_post_meta( $pid, $name_prefix . '_room_footage_number', sanitize_text_field( $data[ 'room_footage' ][ 'number' ] ) );
						update_post_meta( $pid, $name_prefix . '_room_footage_unit', sanitize_text_field( $data[ 'room_footage' ][ 'unit' ] ) );
						if ( ! empty( $data[ 'room_footage' ][ 'number' ] ) ) {
							$facilities[] = absint( $data[ 'room_footage' ][ 'term_id' ] );
						}
					}
				}

				// Guests/Beds/Bathrooms
				$numbers = array( 'guests', 'beds', 'bathrooms' );
				foreach( $numbers as $fn ) {
					if ( isset( $data[ $fn ] ) ) {
						$term = get_term( absint( $data[ $fn ][ 'term_id' ] ), $taxonomy, ARRAY_A );
						if ( ( ! is_wp_error( $term ) ) && \LoftOcean\is_valid_array( $term ) ) {
							update_post_meta( $pid, $name_prefix . '_' . $fn . '_number', sanitize_text_field( $data[ $fn ][ 'number' ] ) );
							update_post_meta( $pid, $name_prefix . '_' . $fn . '_label', sanitize_text_field( $data[ $fn ][ 'label' ] ) );
							if ( ! empty( $data[ $fn ][ 'number' ] ) ) {
								$facilities[] = absint( $data[ $fn ][ 'term_id' ] );
							}
						}
					}
				}
				// Free WIFI/Air Conditioning
				$enabled = array( 'air_conditioning', 'free_wifi' );
				foreach( $enabled as $fn ) {
					if ( isset( $data[ $fn ] ) ) {
						$term = get_term( absint( $data[ $fn ][ 'term_id' ] ), $taxonomy, ARRAY_A );
						if ( ( ! is_wp_error( $term ) ) && \LoftOcean\is_valid_array( $term ) ) {
							$enabled = isset( $data[ $fn ][ 'enabled' ] ) && ( 'on' == sanitize_text_field( $data[ $fn ][ 'enabled' ] ) ) ? 'on' : '';
							update_post_meta( $pid, $name_prefix . '_enable_' . $data[ $fn ][ 'term_id' ], $enabled );
							update_post_meta( $pid, $name_prefix . '_' . $fn . '_label', sanitize_text_field( $data[ $fn ][ 'custom_label' ] ) );
							if ( $enabled ) {
								$facilities[] = absint( $data[ $fn ][ 'term_id' ] );
							}
						}
					}
				}
				// Custom facilities
				if ( isset( $data[ 'custom' ] ) ) {
					foreach( $data[ 'custom' ] as $val ) {
						$term = get_term( absint( $val[ 'term_id' ] ), $taxonomy, ARRAY_A );
						if ( ( ! is_wp_error( $term ) ) && \LoftOcean\is_valid_array( $term ) ) {
							$enabled = isset( $val[ 'enabled' ] ) && ( 'on' == sanitize_text_field( $val[ 'enabled' ] ) ) ? 'on' : '';
							update_post_meta( $pid, $name_prefix . '_enable_' . $val[ 'term_id' ], $enabled );
							update_post_meta( $pid, $name_prefix . '_custom_label_' . $val[ 'term_id' ], sanitize_text_field( $val[ 'custom_label' ] ) );
							if ( 'on' == $enabled ) {
								$facilities[] = absint( $val[ 'term_id' ] );
							}
						}
					}
				}
				wp_set_post_terms( $pid, $facilities, $taxonomy );
			}
		}
	}
	new Facility();
}
