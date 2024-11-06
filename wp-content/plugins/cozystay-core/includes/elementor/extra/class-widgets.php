<?php
namespace LoftOcean\Elementor\Extra;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class Widgets {
    /**
    * Construct function
    */
    public function __construct() {
        add_action( 'elementor/frontend/widget/before_render', array( $this, 'before_render_content' ) );
    }
	/**
	* Action elementor/widget/before_render_content callback function
	*/
    public function before_render_content( $widget ) {
        if ( 'Elementor\Widget_WordPress' == get_class( $widget ) ) {
			if ( 'loftocean-widget-posts' == $widget->get_widget_instance()->id_base ) {
	            $settings = $widget->get_settings( 'wp' );
	            if ( isset( $settings[ 'show-list-number' ] ) && ( 'on' == $settings[ 'show-list-number' ] ) ) {
	                $widget->add_render_attribute( '_wrapper', 'class', 'with-post-number' );
	            }
			} else if ( 'loftocean-widget-instagram' == $widget->get_widget_instance()->id_base ) {
	            $settings = $widget->get_settings( 'wp' );
				$widget->add_render_attribute( '_wrapper', 'class', isset( $settings[ 'columns' ] ) ? $settings[ 'columns' ] : 'column-3' );
			}
        }
    }
}
new \LoftOcean\Elementor\Extra\Widgets();
