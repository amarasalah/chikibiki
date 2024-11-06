<?php
namespace LoftOcean\Elementor\Extra;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class Commons {
    /**
    * Construct function
    */
    public function __construct() {
        add_action( 'elementor/element/before_section_end', array( $this, 'animation_controls' ), 10, 3 );
    }
	/**
	* Column controls
	*/
	public function animation_controls( $element, $section_id, $args ) {
        if ( in_array( $element->get_name(), array( 'wp-page', 'editor-preferences', 'kit' ) ) || ( 'section_effects' != $section_id  ) ) return;

        if ( in_array( $element->get_name(), array( 'section', 'column' ) ) ) {
            $element->add_control( 'theme_animation_offset', array(
                'label' => esc_html__( '[CozyStay] Animation Offset', 'loftocean' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => array(
                    ''  => esc_html__( 'Default', 'loftocean' ),
                    'small-offset'  => esc_html__( 'Small', 'loftocean' ),
                    'medium-offset'  => esc_html__( 'Medium', 'loftocean' ),
                    'large-offset'  => esc_html__( 'Large', 'loftocean' ),
                ),
            	'condition' => array( 'animation' => array( 'fadeInDown', 'fadeInLeft', 'fadeInRight', 'fadeInUp' ) ),
                'prefix_class' => 'cs-animation-'
            ) );
        } else {
            $element->add_control( 'theme_animation_offset', array(
                'label' => esc_html__( '[CozyStay] Animation Offset', 'loftocean' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => array(
                    ''  => esc_html__( 'Default', 'loftocean' ),
                    'small-offset'  => esc_html__( 'Small', 'loftocean' ),
                    'medium-offset'  => esc_html__( 'Medium', 'loftocean' ),
                    'large-offset'  => esc_html__( 'Large', 'loftocean' ),
                ),
            	'condition' => array( '_animation' => array( 'fadeInDown', 'fadeInLeft', 'fadeInRight', 'fadeInUp' ) ),
                'prefix_class' => 'cs-animation-'
            ) );
        }
    }
}
new \LoftOcean\Elementor\Extra\Commons();
