<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Slider
 */
class Widget_Slider extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceanslider', array( 'id' => 'slider' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Slider', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-slider-push';
	}
	/**
	 * Get widget categories.
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'loftocean-theme-category' );
	}
	/**
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'sliders', 'slider' ];
	}
	/**
	* Get JavaScript dependency to render this widget
	* @return array of script handler
	*/
	public function get_script_depends() {
		return array();
	}
	/**
	* Get style dependency to render this widget
	* @return array of style handler
	*/
	public function get_style_depends() {
		return array();
	}
    /**
    * Helper function get custom block
    */
    protected function get_custom_block() {
        return apply_filters( 'loftocean_get_custom_post_type_list', array(), 'custom_blocks' );
    }
	/**
	 * Register widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 * @access protected
	 */
	protected function register_controls() {
        $this->start_controls_section( 'general_content_section', array(
			'label' => __( 'General', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		) );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control( 'content_type', array(
            'label'	=> esc_html__( 'Content Type', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'editor',
            'options' => array(
				'editor' => esc_html__( 'Text', 'loftocean' ),
				'custom' => esc_html__( 'Custom Block', 'loftocean' ),
			)
		) );
        $repeater->add_control( 'text', array(
            'label'   => esc_html__( 'Text', 'loftocean' ),
            'type'    => \Elementor\Controls_Manager::WYSIWYG,
            'condition' => array( 'content_type[value]' => 'editor' ),
            'default' => '<p>' . esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 'loftocean' ) . '</p>'
        ) );
        $repeater->add_control( 'custom_block', array(
            'label'	=> esc_html__( 'Custom Block', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '0',
			'condition' => array( 'content_type[value]' => 'custom' ),
            'options' => $this->get_custom_block()
		) );
		$this->add_control( 'sliders', array(
			'label' => esc_html__( 'Slider Items', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::REPEATER,
			'fields' => $repeater->get_controls(),
            'default' => array(
                array( 'text' => '<p>' . esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 'loftocean' ) . '</p>' ),
                array( 'text' => '<p>' . esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 'loftocean' ) . '</p>' )
            )
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'slider_section', array(
            'label' => __( 'Slider', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE
        ) );
        $this->add_control( 'autoplay', array(
            'label' => esc_html__( 'Autoplay', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'autoplay_speed', array(
			'label' => esc_html__( 'Autoplay Speed', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::NUMBER,
			'condition' => array( 'autoplay[value]' => 'on' ),
			'default' => '5000'
		) );
        $this->add_control( 'show_arrows', array(
            'label' => esc_html__( 'Show Arrows', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'on',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'slider_arrows_color', array(
			'label' => esc_html__( 'Slider Arrow Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'condition' => array( 'show_arrows[value]' => 'on' ),
            'selectors' => array(
				'{{WRAPPER}} .slick-arrow' => 'color: {{VALUE}};',
			)
		) );
        $this->add_control( 'show_dots', array(
            'label' => esc_html__( 'Show Dots', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'on',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'slider_dots_color', array(
			'label' => esc_html__( 'Slider Dots Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'condition' => array( 'show_dots[value]' => 'on' ),
            'selectors' => array(
				'{{WRAPPER}} .slick-dots li' => 'color: {{VALUE}};',
			)
		) );
        $this->end_controls_section();
	}
    /**
    * Helper function to print the custom block content
    */
    protected function print_custom_block( $block ) {
        if ( ! empty( $block ) ) {
            do_action( 'loftocean_the_custom_blocks_content', $block );
        }
    }
	/**
	* Written in PHP and used to generate the final HTML.
    * @access protected
	*/
	protected function render() {
        $settings = $this->get_settings_for_display();
        if ( \LoftOcean\is_valid_array( $settings[ 'sliders' ] ) ) :
            $this->add_render_attribute( 'wrapper', 'class', array( 'cs-slider' ) );
            $this->add_render_attribute( 'wrapper', 'data-autoplay', $settings[ 'autoplay' ] );
            $this->add_render_attribute( 'wrapper', 'data-autoplay-speed', ( 'on' == $settings[ 'autoplay' ] ? $settings[ 'autoplay_speed' ] : 5000 ) );
            $this->add_render_attribute( 'wrapper', 'data-show-arrows', $settings[ 'show_arrows' ] );
            $this->add_render_attribute( 'wrapper', 'data-show-dots', $settings[ 'show_dots' ] ); ?>

            <div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
                <div class="cs-slider-wrap"><?php
                foreach( $settings[ 'sliders' ] as $index => $slider ) :
                    $element = 'sliders.' . $index . '.text';
					$is_source_editor = false;
					if ( 'editor' == $slider[ 'content_type' ] ) {
						$is_source_editor = true;
						$this->add_inline_editing_attributes( $element, 'advanced' );
					}
                    $this->add_render_attribute( $element, 'class', 'cs-slider-item' );
                    ( $index > 0 ) ? $this->add_render_attribute( $element, 'class', 'hide' ) : ''; ?>
                    <div <?php $this->print_render_attribute_string( $element ); ?>><?php
                        if ( $is_source_editor ) {
                            $this->print_text_editor( $slider[ 'text' ] );
                        } else {
							$this->print_custom_block( $slider[ 'custom_block' ] );
                        } ?>
                    </div><?php
                endforeach; ?>
                </div>
            </div><?php
        endif;
	}
}
