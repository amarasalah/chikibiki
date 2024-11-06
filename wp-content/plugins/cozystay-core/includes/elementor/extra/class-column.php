<?php
namespace LoftOcean\Elementor\Extra;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class Column {
    /**
    * Construct function
    */
    public function __construct() {
        add_action( 'elementor/element/column/section_typo/after_section_end', array( $this, 'theme_controls' ), 10, 2 );
    }
	/**
	* Column controls
	*/
	public function theme_controls( $element, $args ) {
		$element->start_controls_section( 'theme_color_scheme_section', array(
			'label' => esc_html__( '[CozyStay] Color Scheme', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SECTION,
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$element->add_control( 'theme_color_scheme', array(
			'label'	=> esc_html__( 'Color Scheme', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '',
			'prefix_class' => '',
            'options' => array(
				'' => esc_html__( 'Inherit', 'loftocean' ),
				'light-color' => esc_html__( 'Light', 'loftocean' ),
				'dark-color' => esc_html__( 'Dark', 'loftocean' )
			)
		) );
		$element->end_controls_section();

		$element->start_controls_section( 'theme_order_section', array(
			'label' => esc_html__( '[CozyStay] Column Order', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SECTION,
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$element->add_responsive_control( 'column_order', array(
	        'label' => esc_html__( 'Column Order', 'loftocean' ),
	        'type' => \Elementor\Controls_Manager::SELECT,
	        'default' => '',
	        'options' => array(
	            ''  => esc_html__( '- Default -', 'loftocean' ),
	            '-1'  => esc_html__( 'First', 'loftocean' ),
	            '1'  => esc_html__( '1', 'loftocean' ),
	            '2'  => esc_html__( '2', 'loftocean' ),
	            '3'  => esc_html__( '3', 'loftocean' ),
	            '4'  => esc_html__( '4', 'loftocean' ),
	            '5'  => esc_html__( '5', 'loftocean' ),
	            '6'  => esc_html__( '6', 'loftocean' ),
	            '7'  => esc_html__( '7', 'loftocean' ),
	            '8'  => esc_html__( '8', 'loftocean' ),
	            '9'  => esc_html__( '9', 'loftocean' ),
	            '10'  => esc_html__( '10', 'loftocean' ),
	            '999'  => esc_html__( 'Last', 'loftocean' )
	        ),
	        'selectors' => array( '{{WRAPPER}}' => 'order: {{VALUE}};' )
	    ) );
		$element->end_controls_section();
	}
}
new \LoftOcean\Elementor\Extra\Column();
