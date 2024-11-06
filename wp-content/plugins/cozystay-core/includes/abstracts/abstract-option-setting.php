<?php
namespace LoftOcean;
/**
* Abstract option setting class
*/

if ( ! class_exists( 'Option_Setting' ) ) {
	class Option_Setting {
		/**
		* String setting id
		*/
		protected $id = '';
		/**
		* Array setting attributes
		*/
		protected $setting = array();
		/**
		* Construction function
		*/
		public function __construct() {
			add_filter( 'admin_init', array( $this, 'init_setting' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
		public function init_setting() {
			$this->register_settings();
			if ( ! empty( $this->id ) && ! empty( $this->setting['type'] ) ) {
				add_settings_field(
					$this->id,
					$this->setting['label'],
					array( $this, 'render' ),
					$this->setting['section']
				);
			 	register_setting(
			 		$this->setting['section'],
			 		$this->id,
			 		array( 'sanitize_callback' => array( $this, 'sanitize' ) )
			 	);
			}
		}
		/**
		* Enqueue admin scripts
		*/
		public function enqueue_scripts() {
			// Do nothing here
		}
		/**
		* Register setting attributes, store it in property 'setting'
		*/
		protected function register_settings() {
			$this->setting = array();
		}
		/**
		* Test if the form element is disabled
		*/
		protected function is_disabled() {
			return false;
		}
		/**
		* Render the option for setting page
		*/
		public function render() {
			$setting = array_merge( array_fill_keys( array( 'default', 'description' ), '' ), $this->setting );
			switch ( $this->setting['type'] ) {
				case 'select':
					if ( ! empty( $this->setting['options'] ) && is_array( $this->setting['options'] ) ) {
						$value = get_option( $this->id, $this->setting['default'] ); ?>
						<select name="<?php echo esc_attr( $this->id ); ?>" id="<?php echo esc_attr( $this->id ); ?>"<?php if ( $this->is_disabled() ) : ?> disabled<?php endif; ?>><?php
						foreach ( $this->setting['options'] as $val => $lbl ) : ?>
							<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $value, $val ); ?>><?php echo esc_html( $lbl ); ?></option><?php
						endforeach; ?>
						</select>
						<?php if ( ! empty( $setting['description'] ) ) : ?>
							<p class="description"><?php echo wp_kses_post( $setting['description'] ); ?></p>
						<?php endif;
					}
					break;
			}
		}
		/**
		* Sanitize the
		*/
		public function sanitize( $value ) {
			switch ( $this->setting['type'] ) {
				case 'select':
					if ( ! empty( $this->setting['options'] ) && is_array( $this->setting['options'] ) ) {
						$options = array_keys( $this->setting['options'] );
						return in_array( $value, $options ) ? $value : $this->setting['default'];
					}
					break;
				default:
					return $value;
			}
		}
	}
}
