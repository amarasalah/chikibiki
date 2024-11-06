<?php
namespace LoftOcean\Instagram;
/**
* Custom WP Customize Control class for widget instagram
*/

if ( ! class_exists( '\LoftOcean\Instagram\Customize_Control' ) && class_exists( '\WP_Customize_Control' ) ) {
	class Customize_Control extends \WP_Customize_Control {
		/**
		* Boolean to the label go before input
		*/
		public $label_first = false;
		/**
		* Override parent function render_content
		*/
		public function render_content() {
			switch ( $this->type ) {
				case 'button':
					if ( ! empty( $this->label ) ) : ?>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span> <?php
					endif;
					if ( ! empty( $this->description ) ) : ?>
						<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span> <?php
					endif; ?>
					<input type="button" <?php $this->link(); ?> <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" class="button button-primary" />
					<div class="customize-control-notifications-container"></div> <?php
					break;
				case 'checkbox':
					if ( $this->label_first ) { ?>
						<label class="title-first-checkbox"> <?php
							if ( ! empty( $this->label ) ) : ?>
								<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span> <?php
							endif; ?>
							<input type="checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> <?php checked( 'on', $this->value() ); ?>> <?php
							if ( ! empty( $this->description ) ) : ?>
								<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span> <?php
							endif; ?>
						</label>
						<div class="customize-control-notifications-container"></div> <?php
					} else {
						parent::render_content();
					}
					break;
				default:
					parent::render_content();
			}
		}
	}
}
