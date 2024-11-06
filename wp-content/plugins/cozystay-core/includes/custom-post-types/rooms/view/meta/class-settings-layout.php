<?php
namespace LoftOcean\Room\Settings;

if ( ! class_exists( '\LoftOcean\Room\Settings\Layout' ) ) {
	class Layout {
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
			<li class="loftocean-room room-options tab-layout"<?php if ( ! $this->show_current_tab( $pid ) ) : ?> style="display: none;"<?php endif; ?>>
				<a href="#tab-layout"><span><?php esc_html_e( 'Layout', 'loftocean' ); ?></span></a>
			</li><?php
		}
		/**
		* Tab panel
		*/
		public function the_room_setting_panel( $pid ) {
			$data = $this->get_room_data( $pid ); ?>
			<div id="tab-layout-panel" class="panel loftocean-room-setting-panel hidden">
				<div class="options-group">
					<p class="form-field select-field">
						<label for="room_top_section"><?php esc_html_e( 'Page Top Section', 'loftocean' ); ?></label>
						<select name="loftocean_room_top_section" id="room_top_section"><?php
							$options = array(
								'' => esc_html__( 'Default', 'loftocean' ),
								'top-gallery-1' => esc_html__( 'Top Gallery 1', 'loftocean' ),
								'top-gallery-2' => esc_html__( 'Top Gallery 2', 'loftocean' ),
								'top-image' => esc_html__( 'Top Image', 'loftocean' ),
								'hide' => esc_html__( 'Hide', 'loftocean' )
							);
							foreach ( $options as $id => $label ) : ?>
								<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $id, $data[ 'room_top_section' ] ); ?>><?php echo esc_html( $label ); ?></option><?php
							endforeach; ?>
						</select>
					</p>
					<p class="form-field select-field">
						<label for="room_booking_form"><?php esc_html_e( 'Room Booking Form', 'loftocean' ); ?></label>
						<select name="loftocean_room_booking_form" id="room_booking_form"><?php
							$options = array(
								'' => esc_html__( 'Default', 'loftocean' ),
								'right' => esc_html__( 'Right', 'loftocean' ),
								'left' => esc_html__( 'Left', 'loftocean' ),
								'hide' => esc_html__( 'Hide', 'loftocean' )
							);
							foreach ( $options as $id => $label ) : ?>
								<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $id, $data[ 'room_booking_form' ] ); ?>><?php echo esc_html( $label ); ?></option><?php
							endforeach; ?>
						</select>
					</p>
				</div>
			</div><?php
		}
		/**
		* Get room data
		*/
		protected function get_room_data( $pid ) {
			return array(
				'room_top_section' => get_post_meta( $pid, 'loftocean_room_top_section', true ),
				'room_booking_form' => get_post_meta( $pid, 'loftocean_room_booking_form', true ),
			);
		}
		/*
		* Condition function if show current tab
		*/
		protected function show_current_tab( $pid ) {
			$template = get_post_meta( $pid, '_wp_page_template', true );
			return empty( $template ) || ( 'default' == $template );
		}
		/**
		* Save room settings
		*/
		public function save_room_settings( $pid ) {
			$top_section = sanitize_text_field( wp_unslash( $_REQUEST[ 'loftocean_room_top_section' ] ) );
			$booking_form = sanitize_text_field( wp_unslash( $_REQUEST[ 'loftocean_room_booking_form' ] ) );

			update_post_meta( $pid, 'loftocean_room_top_section', $top_section );
			update_post_meta( $pid, 'loftocean_room_booking_form', $booking_form );
		}
	}
	new Layout();
}
