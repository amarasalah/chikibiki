<?php
namespace LoftOcean\Room\Settings;

if ( ! class_exists( '\LoftOcean\Room\Settings\Availability' ) ) {
	class Availability {
		/**
		* Construct function
		*/
		public function __construct() {
			add_action( 'loftocean_room_the_settings_tabs', array( $this, 'get_room_setting_tabs' ) );
			add_action( 'loftocean_room_the_settings_panel', array( $this, 'the_room_setting_panel' ) );
		}
		/**
		* Tab titles
		*/
		public function get_room_setting_tabs( $pid ) { ?>
			<li class="loftocean-room room-options tab-availability">
				<a href="#tab-availability"><span><?php esc_html_e( 'Availability', 'loftocean' ); ?></span></a>
			</li><?php
		}
		/**
		* Tab panel
		*/
		public function the_room_setting_panel( $pid ) {
			$currency = \LoftOcean\get_current_currency();
			$currency = empty( $currency[ 'left' ] ) ? $currency[ 'right' ] : $currency[ 'left' ]; ?>
			<div id="tab-availability-panel" class="panel loftocean-room-setting-panel hidden">
				<div class="availability-editor">
					<div class="calendar-form">
			            <div class="form-field">
			                <label for="calendar_check_in"><strong><?php esc_html_e( 'Check In', 'loftocean' ); ?></strong></label>
			                <input readonly="readonly" type="text" class="date-picker" name="calendar_check_in" id="calendar_check_in" placeholder="<?php esc_attr_e( 'Check In', 'loftocean' ); ?>">
			            </div>
			            <div class="form-field">
			                <label for="calendar_check_out"><strong><?php esc_html_e( 'Check Out', 'loftocean' ); ?></strong></label>
			                <input readonly="readonly" type="text" class="date-picker" name="calendar_check_out" id="calendar_check_out" placeholder="<?php esc_attr_e( 'Check Out', 'loftocean' ); ?>">
			            </div>
			            <div class="form-field price-field">
			                <label for="calendar_price"><strong><?php esc_html_e( 'Price', 'loftocean' ); echo ' (' . $currency . ')'; ?></strong></label>
			                <input type="text" name="calendar_price" id="calendar_price" placeholder="<?php esc_attr_e( 'Price', 'loftocean' ); ?>">
			            </div>
		                <div class="form-field adult-child-price" style="display: none;">
		                    <label for="calendar_adult_price"><strong><?php esc_html_e( 'Adult Price', 'loftocean' ); echo ' (' . $currency . ')'; ?></strong></label>
		                    <input type="text" name="calendar_adult_price" id="calendar_adult_price" class="form-control" placeholder="<?php esc_attr_e( 'Adult Price', 'loftocean' ) ;?>">
		                </div>
		                <div class="form-field adult-child-price" style="display: none;">
		                    <label for="calendar_child_price"><strong><?php esc_html_e( 'Child Price', 'loftocean' ); echo ' (' . $currency . ')'; ?></strong></label>
		                    <input type="text" name="calendar_child_price" id="calendar_child_price" class="form-control" placeholder="<?php esc_attr_e( 'Child Price', 'loftocean' ); ?>">
		                </div>
			            <div class="form-field">
			                <label for="calendar_room_number"><?php esc_html_e( 'Number of Rooms', 'loftocean' ); ?></label>
			                <input type="text" name="calendar_room_number" id="calendar_room_number" class="form-control" placeholder="<?php esc_attr_e( 'Number of Rooms', 'loftocean' ); ?>">
			            </div>
			            <div class="form-field">
			                <label for="calendar_status"><?php esc_html_e( 'Status', 'loftocean' ); ?></label>
			                <select name="calendar_status" id="calendar_status">
			                    <option value="available"><?php esc_html_e( 'Available', 'loftocean' ); ?></option>
			                    <option value="unavailable"><?php esc_html_e( 'Unavailable', 'loftocean' ); ?></option>
			                </select>
			            </div>
			            <div class="form-field">
			                <div class="form-message">
			                    <p></p>
			                </div>
			            </div>
			            <div class="form-field">
			                <input type="submit" id="calendar_submit" class="button button-primary" name="calendar_submit" value="<?php esc_attr_e( 'Update', 'loftocean' ); ?>">
			            </div>

			        </div>
				</div>
                <div id="availability-calendar" class="availability-calendar"></div>
				<div class="calendar-loading" style="display: none;"></div>
			</div><?php
			$room_details = apply_filters( 'loftocean_get_room_details', array(), $pid );
			$locale = get_locale();

			wp_enqueue_style( 'jquery-ui', '//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css', array(), '1.13.1' );
            wp_enqueue_script( 'fullcalendar-locales', LOFTOCEAN_URI . 'assets/libs/fullcalendar-6.1.6/locales-all.global.min.js', array( 'fullcalendar' ), '6.1.6', true );
            wp_enqueue_script( 'fullcalendar', LOFTOCEAN_URI . 'assets/libs/fullcalendar-6.1.6/index.global.min.js', array( 'jquery' ), '6.1.6', true );
            wp_enqueue_script( 'loftocean-admin-room-availability', LOFTOCEAN_URI . 'assets/scripts/admin/room-availability.min.js', array( 'fullcalendar', 'jquery-ui-datepicker', 'wp-api-request' ), LOFTOCEAN_ASSETS_VERSION, true );
			wp_localize_script( 'loftocean-admin-room-availability', 'loftoceanRoomAvailability', array_merge( $room_details, array(
				'roomID' => get_the_ID(),
				'currentDate' => date( 'Y-m-d' ),
				'timezone' => get_option( 'timezone_string', 'local' ),
				'locale' => substr( $locale, 0, 2 ) ? strtolower( substr( $locale, 0, 2 ) ) : $locale,
				'i18nText' => array(
					'errorMessage' => esc_html__( 'Can not get the availability slot. Lost connect with your sever', 'loftocean' ),
					'available' => esc_html__( 'Available', 'loftocean' ),
					'unavailable' => esc_html__( 'Unavailable', 'loftocean' ),
					'adult' => esc_html__( 'Adult', 'loftocean' ),
					'child' => esc_html__( 'Child', 'loftocean' ),
					'base' => esc_html__( 'Price', 'loftocean' ),
					'roomNumber' => esc_html__( 'Number', 'loftocean' ),
					'leftNumber' => esc_html__( 'Left', 'loftocean' )
				)
			) ) );
		}
	}
	new Availability();
}
