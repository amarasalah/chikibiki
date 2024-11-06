<?php
namespace LoftOcean\Room\Settings;

if ( ! class_exists( '\LoftOcean\Room\Settings\Booking_Calendar' ) ) {
	class Booking_Calendar {
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
			<li class="loftocean-room room-options tab-booking-calendar">
				<a href="#tab-booking-calendar"><span><?php esc_html_e( 'Booking Calendar', 'loftocean' ); ?></span></a>
			</li><?php
		}
		/**
		* Tab panel
		*/
		public function the_room_setting_panel( $pid ) {
			$currency = \LoftOcean\get_current_currency();
			$currency = empty( $currency[ 'left' ] ) ? $currency[ 'right' ] : $currency[ 'left' ]; ?>
			<div id="tab-booking-calendar-panel" class="panel loftocean-room-setting-panel hidden">
                <div class="booking-calendar-colors">
                    <div class="calendar-color-item booked">
                        <span class="color-label"></span>
                        <span class="color-text"><?php esc_html_e( 'Booked', 'loftocean' ); ?></span>
                    </div>
                    <div class="calendar-color-item imported">
                        <span class="color-label"></span>
                        <span class="color-text"><?php esc_html_e( 'Imported', 'loftocean' ); ?></span>
                    </div>
                    <div class="calendar-color-item blocked">
                        <span class="color-label"></span>
                        <span class="color-text"><?php esc_html_e( 'Blocked', 'loftocean' ); ?></span>
                    </div>
                    <div class="calendar-color-item full-booked">
                        <span class="color-label"></span>
                        <span class="color-text"><?php esc_html_e( 'Full Booked', 'loftocean' ); ?></span>
                    </div>
                </div>

                <div id="booking-calendar" class="booking-calendar-wrapper"></div>
				<div class="calendar-loading" style="display: none;"></div>

                <div class="booking-calendar-modal">
                    <div class="booking-calendar-modal-bg-overlay"></div>
                    <div class="booking-calendar-modal-main">
                        <div class="booking-calendar-modal-header">
                            <h2 class="booking-title"><?php esc_html_e( 'Order', 'loftocean' ); ?> #<span class="order-number"></span></h2>
                            <button class="modal-close">
                                <span class="screen-reader-text"><?php esc_html_e( 'Close popup panel', 'loftocean' ); ?></span>
                            </button>
                        </div>

                        <div class="booking-calendar-modal-content">
                            <div class="cs-order-preview"></div>
                        </div>

                        <div class="booking-calendar-modal-footer">
                            <div class="inner">
                                <a class="button button-primary button-large" aria-label="<?php esc_attr_e( 'View this order', 'loftocean' ); ?>" target="_blank" href="#"><?php esc_html_e( 'View Order Details', 'loftocean' ); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
			<script id="tmpl-loftocean-room-order-details" type="text/html">
				<h3><a target="_blank" href="{{{ data.room_link }}}">{{{ data.room_title }}}</a></h3><#
				if ( 'woocommerce' == data.source ) { #>
					<table>
						<tbody>
							<tr>
								<th><?php esc_html_e( 'ID', 'loftocean' ); ?></th>
								<td>{{{ data.order_id }}}</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Check-in Date', 'loftocean' ); ?></th>
								<td>{{{ data.checkin }}}</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Check-out Date', 'loftocean' ); ?></th>
								<td>{{{ data.checkout }}}</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Details', 'loftocean' ); ?></th>
								<td>{{{ data.details }}}</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Extra Services', 'loftocean' ); ?></th>
								<td>{{{ data.extra_services }}}</td>
							</tr>
						</tbody>
					</table>

					<div class="cs-order-preview-address">
						<h3><?php esc_html_e( 'Billing details', 'loftocean' ); ?></h3>
						<p>{{{ data.billing.address }}}</p>

						<p><strong><?php esc_html_e( 'Email', 'loftocean' ); ?></strong><br>
						<a href="mailto:{{{ data.billing.email }}}">{{{ data.billing.email }}}</a>
						</p>

						<p><strong>Phone</strong><br>
						<a href="tel:{{{ data.billing.phone }}}">{{{ data.billing.phone }}}</a></p>

					</div><#
				} else if ( 'imported' == data.source ) { #>
					<table>
						<tbody>
							<tr>
								<th><?php esc_html_e( 'ID', 'loftocean' ); ?></th>
								<td>{{{ data.order_id }}}</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Check-in Date', 'loftocean' ); ?></th>
								<td>{{{ data.detail.checkIn }}}</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Check-out Date', 'loftocean' ); ?></th>
								<td>{{{ data.detail.checkOut }}}</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'UID', 'loftocean' ); ?></th>
								<td>{{{ data.detail.uid }}}</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Summary', 'loftocean' ); ?></th>
								<td>{{{ data.detail.summary }}}</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Description', 'loftocean' ); ?></th>
								<td>{{{ data.detail.description }}}</td>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Source', 'loftocean' ); ?></th>
								<td>{{{ data.detail.prodid }}}</td>
							</tr>
						</tbody>
					</table><#
				} #>
			</script><?php
			$room_details = apply_filters( 'loftocean_get_room_details', array(), $pid );
			$locale = get_locale();
            wp_enqueue_script( 'fullcalendar-locales', LOFTOCEAN_URI . 'assets/libs/fullcalendar-6.1.6/locales-all.global.min.js', array( 'fullcalendar' ), '6.1.6', true );
            wp_enqueue_script( 'fullcalendar', LOFTOCEAN_URI . 'assets/libs/fullcalendar-6.1.6/index.global.min.js', array( 'jquery' ), '6.1.6', true );
            wp_enqueue_script( 'loftocean-admin-booking-calendar', LOFTOCEAN_URI . 'assets/scripts/admin/room-booking-calendar.min.js', array( 'fullcalendar', 'wp-api-request', 'wp-util' ), LOFTOCEAN_ASSETS_VERSION, true );
			wp_localize_script( 'loftocean-admin-booking-calendar', 'loftoceanBookingCalendar', array(
				'roomID' => get_the_ID(),
				'currentDate' => date( 'Y-m-d' ),
				'timezone' => get_option( 'timezone_string', 'local' ),
				'locale' => substr( $locale, 0, 2 ) ? strtolower( substr( $locale, 0, 2 ) ) : $locale,
				'i18nText' => array(
					'errorMessage' => esc_html__( 'Can not get the booking information. Lost connect with your sever', 'loftocean' ),
					'blocked' => esc_html__( 'Blocked', 'loftocean' )
				)
			) );
		}
	}
	new Booking_Calendar();
}
