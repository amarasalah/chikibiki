<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Count Down
 */
class Widget_Count_Down extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceancountdown', array( 'id' => 'count-down' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Count Down', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-countdown';
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
		return [ 'count down', 'countdown' ];
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
	 * Register widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 * @access protected
	 */
	protected function register_controls() {
        $this->start_controls_section( 'general_content_section', array(
			'label' => __( 'General', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		) );
        $this->add_control( 'date', array(
			'label'   => esc_html__( 'Pick a Date', 'loftocean' ),
			'type'    => \Elementor\Controls_Manager::DATE_TIME,
			'default' => date( 'Y-m-d', strtotime( ' +2 days' ) ),
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'general_style_section', array(
            'label' => __( 'General', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_responsive_control( 'alignment', array(
            'label'	=> esc_html__( 'Alignment', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => array(
                'text-left' => array(
                    'title' => esc_html__( 'Left', 'loftocean' ),
                    'icon' => 'eicon-text-align-left'
                ),
                'text-center' => array(
                    'title' => esc_html__( 'Center', 'loftocean' ),
                    'icon' => 'eicon-text-align-center',
                ),
                'text-right' => array(
                    'title' => esc_html__( 'Right', 'loftocean' ),
                    'icon' => 'eicon-text-align-right',
                )
            ),
            'default' => 'text-center',
        ) );
		$this->add_responsive_control( 'item_min_width', array(
            'label'	=> esc_html__( 'Each Item Min Width', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range' => array( 'px' => array( 'max' => 500, 'min' => 0 ) ),
			'render_type' => 'ui',
			'separator' => 'before',
			'selectors' => array( '{{WRAPPER}} .cs-countdown .countdown-item ' => 'min-width: {{SIZE}}px;' )
        ) );
		$this->add_responsive_control( 'item_space_between', array(
            'label'	=> esc_html__( 'Space Between', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range' => array( 'px' => array( 'max' => 500, 'min' => 0 ) ),
			'render_type' => 'ui',
			'separator' => 'before',
			'selectors' => array( '{{WRAPPER}} .cs-countdown .countdown-item ' => 'margin: 0 {{SIZE}}px;' )
        ) );
        $this->end_controls_section();

        $this->start_controls_section( 'amount_style_section', array(
            'label' => __( 'Amount', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE
        ) );
        $this->add_control( 'amount_preset_color', array(
			'label' => esc_html__( 'Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => array(
				'' => esc_html__( 'Default', 'loftocean' ),
				'amount-color-primary' => esc_html__( 'Primary', 'loftocean' ),
                'amount-color-secondary' => esc_html__( 'Secondary', 'loftocean' ),
				'amount-color-white' => esc_html__( 'White', 'loftocean' ),
                'amount-color-black' => esc_html__( 'Black', 'loftocean' ),
				'custom' => esc_html__( 'Custom', 'loftocean' )
			)
		) );
        $this->add_control( 'amount_custom_color', array(
            'label' => esc_html__( 'Custom Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'condition' => array( 'amount_preset_color[value]' => 'custom' ),
            'selectors' => array(
                '{{WRAPPER}} .countdown-amount' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'amount_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .countdown-amount',
            )
        );
        $this->end_controls_section();

        $this->start_controls_section( 'period_style_section', array(
            'label' => __( 'Period', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE
        ) );
        $this->add_control( 'period_preset_color', array(
			'label' => esc_html__( 'Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => array(
				'' => esc_html__( 'Default', 'loftocean' ),
				'period-color-primary' => esc_html__( 'Primary', 'loftocean' ),
                'period-color-secondary' => esc_html__( 'Secondary', 'loftocean' ),
				'period-color-white' => esc_html__( 'White', 'loftocean' ),
                'period-color-black' => esc_html__( 'Black', 'loftocean' ),
				'custom' => esc_html__( 'Custom', 'loftocean' )
			)
		) );
        $this->add_control( 'period_custom_color', array(
            'label' => esc_html__( 'Custom Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'condition' => array( 'period_preset_color[value]' => 'custom' ),
            'selectors' => array(
                '{{WRAPPER}} .countdown-period' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'period_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .countdown-period',
            )
        );
        $this->end_controls_section();
	}
	/**
	* Written in PHP and used to generate the final HTML.
    * @access protected
	*/
	protected function render() {
        $settings = $this->get_settings_for_display();
        $timezone = get_option( 'timezone_string', 'UTC' );
        $timezone = wp_timezone(); // empty( $timezone ) ? 'GMT' : $timezone;
        $this->add_render_attribute( array(
            'wrapper' => array( 'class' => array( 'cs-countdown' ) ),
            'timer' => array( 'class' => 'cs-countdown-wrap', 'data-timezone' => $timezone, 'data-end-date' => get_gmt_from_date( $settings[ 'date' ], 'Y-m-d H:i:s' ) )
        ) );
        $alignment = array( 'alignment' => '', 'alignment_mobile' => '-mobile', 'alignment_tablet' => '-tablet' );
        foreach( $alignment as $align => $after ) {
            if ( ! empty( $settings[ $align ] ) ) {
                $this->add_render_attribute( 'wrapper', 'class', $settings[ $align ] . $after );
            }
        }
        $color_settings = array( 'amount', 'period' );
        foreach ( $color_settings as $element ) {
            $id = $element . '_preset_color';
            if ( ! empty( $settings[ $id ] ) && ( 'custom' != $settings[ $id ] ) ) {
                $this->add_render_attribute( 'wrapper', 'class', $settings[ $id ] );
            }
        } ?>
        <div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
            <div <?php $this->print_render_attribute_string( 'timer' ); ?>></div>
        </div><?php
	}
}
