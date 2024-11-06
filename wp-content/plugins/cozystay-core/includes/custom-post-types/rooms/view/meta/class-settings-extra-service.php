<?php
namespace LoftOcean\Room\Settings;

if ( ! class_exists( '\LoftOcean\Room\Settings\Extra_Services' ) ) {
    class Extra_Services {
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
            <li class="loftocean-room room-options tab-extra-services">
                <a href="#tab-extra-services"><span><?php esc_html_e( 'Extra Services', 'loftocean' ); ?></span></a>
            </li><?php
        }
        /**
        * Tab panel
        */
        public function the_room_setting_panel( $pid ) {
            $data = $this->get_room_data( $pid );
            $current_services = apply_filters( 'loftocean_get_room_extra_services', false ); ?>
            <div id="tab-extra-services-panel" class="panel loftocean-room-setting-panel hidden">
                <div class="options-group"><?php
                if ( \LoftOcean\is_valid_array( $current_services ) ) :
                    foreach ( $current_services as $cs ) :
                        $sid = $cs[ 'term_id' ];
                        $current_service_enabled = in_array( $sid, $data ); ?>
                        <p class="form-field facility-field">
                            <label><?php echo esc_html( $cs[ 'name' ] ); ?></label>
                            <input type="checkbox" name="loftocean_room[extra_services][<?php echo esc_attr( $sid ); ?>][enabled]" value="on"<?php if ( $current_service_enabled ) : ?> checked<?php endif; ?>>
                            <input type="hidden" name="loftocean_room[extra_services][<?php echo esc_attr( $sid ); ?>][term_id]" value="<?php echo esc_attr( $sid ); ?>" >
                        </p><?php
                    endforeach;
                else : ?>
                    <p class="error no-extra-service-found" style="padding: 20px;"><?php esc_html_e( 'No extra service found.', 'loftocean' ); ?></p><?php
                endif; ?>
                </div>
            </div><?php
        }
        /*
        * Get current extra services data
        */
        public function get_room_data( $room_id ) {
            return apply_filters( 'loftocean_get_room_extra_services_enabled', array(), $room_id );
        }
        /**
        * Save room settings
        */
        public function save_room_settings( $pid ) {
            if ( isset( $_REQUEST[ 'loftocean_room' ], $_REQUEST[ 'loftocean_room' ][ 'extra_services' ] ) ) {
                $data = wp_unslash( $_REQUEST[ 'loftocean_room' ][ 'extra_services' ] );
                $taxonomy = 'lo_room_extra_services';
                $services = array();
                if ( \LoftOcean\is_valid_array( $data ) ) {
                    foreach( $data as $val ) {
                        $term = get_term( absint( $val[ 'term_id' ] ), $taxonomy, ARRAY_A );
                        if ( ( ! is_wp_error( $term ) ) && \LoftOcean\is_valid_array( $term ) ) {
                            if ( isset( $val[ 'enabled' ] ) && ( 'on' == sanitize_text_field( $val[ 'enabled' ] ) ) ) {
                                $services[] = absint( $val[ 'term_id' ] );
                            }
                        }
                    }
                }
                wp_set_post_terms( $pid, $services, $taxonomy );
            }
        }
    }
    new Extra_Services();
}
