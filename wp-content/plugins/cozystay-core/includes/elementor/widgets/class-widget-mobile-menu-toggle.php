<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Mobile Menu Toggle
 */
class Widget_Mobile_Menu_Toggle extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceanmobilemenutoggle', array( 'id' => 'mobile-menu-toggle' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Mobile Menu Toggle', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-text';
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
		return array( 'mobile menu toggle', 'mobile menu', 'toggle' );
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
		$this->start_controls_section( 'general_style_section', array(
			'label' => __( 'General', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE
		) );
		$this->add_control( 'line_width', array(
			'label' => esc_html__( 'Icon Line Width', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => array(
				'px' => array( 'max' => 500, 'step' => 1 )
			),
			'render_type' => 'ui',
			'separator' => 'before',
			'selectors' => array( '{{WRAPPER}} .menu-toggle' => '--line-width: {{SIZE}}px;' )
		) );
		$this->add_control( 'line_height', array(
			'label' => esc_html__( 'Icon Line Height', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => array(
				'px' => array( 'max' => 10, 'step' => 1 )
			),
			'render_type' => 'ui',
			'separator' => 'before',
			'selectors' => array( '{{WRAPPER}} .menu-toggle' => '--line-height: {{SIZE}}px;' )
		) );

		$this->start_controls_tabs( 'tabs_color_style' );
        $this->start_controls_tab( 'tab_color_normal', array(
        	'label' => esc_html__( 'Normal', 'loftocean' )
        ) );
		$this->add_control( 'color', array(
            'label' => esc_html__( 'Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .menu-toggle' => 'color: {{VALUE}};' ),
			'separator' => 'after',
            'default' => ''
        ) ); 
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_color_hover', array(
            'label' => esc_html__( 'Hover', 'loftocean' )
        ) );
		$this->add_control( 'hover_color', array(
            'label' => esc_html__( 'Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .menu-toggle:hover' => 'color: {{VALUE}};' ),
			'separator' => 'after',
            'default' => ''
        ) ); 
        $this->end_controls_tab();
    	$this->end_controls_tabs();

		$this->add_control( 'button_text', array(
			'label'	=> esc_html__( 'Button Text', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'placeholder' => esc_html__( 'Menu', 'loftocean' ),
            'default' => ''
		) );
		$this->add_control( 'work_as_mobile_menu_close_button', array(
			'label' => esc_html__( 'Work as Mobile Menu Close button', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'default' => '',
			'label_on' => 'on',
			'label_off' => 'off',
			'return_value' => 'on'
		) );
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_text',
				'label'    => esc_html__( 'Typography', 'loftocean' ),
				'selector' => '{{WRAPPER}} .menu-toggle-text',
				'condition' => array( 'button_text[value]!' => '' )
			)
		);
		$this->add_control( 'order', array(
			'label' => esc_html__( 'Reverse Icon and Text', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'condition' => array( 'button_text[value]!' => '' ),
			'default' => 'off',
			'label_on' => 'on',
			'label_off' => 'off',
			'return_value' => 'on'
		) );
		$this->end_controls_section();
	}
	/**
	* Written in PHP and used to generate the final HTML.
    * @access protected
	*/
	protected function render() {
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute( 'wrapper', 'class', array( 'menu-toggle', 'elementor-widget-menu-toggle' ) );
		if ( 'on' == $settings[ 'work_as_mobile_menu_close_button' ] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'close-button' );
		}
		if ( 'on' == $settings[ 'order' ] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'text-icon-reverse' );
		} ?>
        <button <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'loftocean' ); ?></span>
			<span class="menu-toggle-icon"></span>
			<?php if ( ! empty( $settings[ 'button_text' ] ) ) : ?>
				<span class="menu-toggle-text"><?php echo esc_html( $settings[ 'button_text' ] ); ?></span>
			<?php endif; ?>
		</button><?php
    }
    /**
	* Render button widget output in the editor.
	* Written as a Backbone JavaScript template and used to generate the live preview.
	* @access protected
	*/
	protected function content_template() { ?>
		<#
		view.addRenderAttribute( 'wrapper', 'class', [ 'menu-toggle', 'elementor-widget-menu-toggle' ] );
		var buttonText = settings[ 'button_text' ] ? settings[ 'button_text' ] : "";
		if ( 'on' == settings[ 'work_as_mobile_menu_close_button' ] ) {
			view.addRenderAttribute( 'wrapper', 'class', 'close-button' );
		}
		if ( 'on' == settings[ 'order' ] ) {
			view.addRenderAttribute( 'wrapper', 'class', 'text-icon-reverse' );
		} #>
        <button {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'loftocean' ); ?></span>
			<span class="menu-toggle-icon"></span>
			<# if ( settings[ 'button_text' ] ) { #><span class="menu-toggle-text">{{{ settings[ 'button_text' ] }}}</span><# } #>
		</button><?php
	}
}
