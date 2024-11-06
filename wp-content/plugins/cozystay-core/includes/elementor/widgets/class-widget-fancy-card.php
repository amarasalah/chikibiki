<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Call to Action
 */
class Widget_Fancy_Card extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceanfancycard', array( 'id' => 'fancy-card' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Fancy Card', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-image-rollover';
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
		return [ 'fancy card' ];
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
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT
		) );
        $this->add_control( 'link', array(
			'type' => \Elementor\Controls_Manager::URL,
			'default' => array( 'url' => '#' ),
			'label' => esc_html__( 'Link', 'loftocean' ),
            'placeholder' => __( 'Enter the URL', 'loftocean' ),
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'image_content_section', array(
            'label' => __( 'Image', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT
        ) );
        $this->add_control( 'image', array(
			'label' => esc_html__( 'Choose Image', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::MEDIA,
			'default' => array( 'url' => \Elementor\Utils::get_placeholder_image_src() )
		) );
        $this->add_group_control( \Elementor\Group_Control_Image_Size::get_type(), array(
			'name' => 'image',
			'default' => 'full',
			'separator' => 'none'
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'content_section', array(
			'label' => __( 'Content', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT
		) );
        $this->add_control( 'subtitle', array(
            'label'   => esc_html__( 'Subtitle', 'loftocean' ),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => esc_html__( 'Fancy Card Subtitle', 'loftocean' )
        ) );
        $this->add_control( 'title', array(
            'label'   => esc_html__( 'Title', 'loftocean' ),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => esc_html__( 'Fancy Card Title', 'loftocean' ),
            'description' => esc_html__( 'Support HTML tag <br>, <em>,  <strong>, <small> and <mark> only.', 'loftocean' )
        ) );
        $this->add_control( 'text', array(
            'label'   => esc_html__( 'Text', 'loftocean' ),
			'default' => '<p>' . esc_html__( 'Aenean commodo ligula eget dolor.', 'loftocean' ) . '</p>',
            'type'    => \Elementor\Controls_Manager::WYSIWYG
        ) );
        $this->add_control( 'button_text', array(
            'label'   => esc_html__( 'Button Text', 'loftocean' ),
            'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'Discover More', 'loftocean' )
        ) );
		$this->add_control( 'button_link', array(
			'type' => \Elementor\Controls_Manager::URL,
			'default' => array( 'url' => '#' ),
			'label' => esc_html__( 'Link', 'loftocean' )
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'general_style_section', array(
            'label' => esc_html__( 'General', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
		$this->add_control( 'general_style', array(
            'label' => esc_html__( 'Style', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style-1',
            'options' => array(
                'style-1' => esc_html__( 'Style 1', 'loftocean' ),
                'style-2' => esc_html__( 'Style 2', 'loftocean' ),
            )
        ) );
		$this->add_control( 'image_position', array(
            'label' => esc_html__( 'Image Position', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'top',
            'condition' => array( 'general_style[value]' => 'style-1' ),
            'options' => array(
                'left' => esc_html__( 'Left', 'loftocean' ),
                'top' => esc_html__( 'Top', 'loftocean' ),
                'right' => esc_html__( 'Right', 'loftocean' ),
                'bottom' => esc_html__( 'Bottom', 'loftocean' )
            )
        ) );
		$this->add_control( 'content_position', array(
            'label'	=> esc_html__( 'Content Position', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '',
            'condition' => array( 'general_style[value]' => 'style-2' ),
            'options' => array(
                '' => esc_html__( 'Left', 'loftocean' ),
                'content-position-right' => esc_html__( 'Right', 'loftocean' ),
                'content-position-center' => esc_html__( 'Center', 'loftocean' ),
            ),
        ) );
		$this->add_responsive_control( 'text_alignment', array(
            'label'	=> esc_html__( 'Text Alignment', 'loftocean' ),
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
		$this->add_control( 'image_hover_effect', array(
            'label' => esc_html__( 'Image Hover Effect', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'cs-hover-img-zoom',
            'options' => array(
                'cs-hover-img-zoom' => esc_html__( 'Zoom In', 'loftocean' ),
                'cs-hover-img-zoom-out' => esc_html__( 'Zoom Out', 'loftocean' ),
                '' => esc_html__( 'None', 'loftocean' ),
            )
        ) );
		$this->start_controls_tabs( 'tabs_colors' );
		$this->start_controls_tab( 'tab_colors_normal', array(
			'label' => esc_html__( 'Normal', 'loftocean' ),
		) );
		$this->add_control( 'general_background_color', array(
            'label' => esc_html__( 'Background Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .cs-fancy-card' => '--content-bg: {{VALUE}};' ),
            'default' => ''
        ) );
        $this->add_control( 'general_text_color', array(
            'label' => esc_html__( 'Text Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .cs-fancy-card' => '--content-color: {{VALUE}};' ),
            'default' => ''
        ) );
		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_colors_hover', array(
			'label' => esc_html__( 'Hover', 'loftocean' ),
		) );
		$this->add_control( 'general_background_color_hover', array(
            'label' => esc_html__( 'Background Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .cs-fancy-card' => '--content-bg-hover: {{VALUE}};' ),
            'default' => ''
        ) );
        $this->add_control( 'general_text_color_hover', array(
            'label' => esc_html__( 'Text Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '{{WRAPPER}} .cs-fancy-card' => '--content-color-hover: {{VALUE}};' ),
            'default' => ''
        ) );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control( 'general_padding', array(
			'label' => esc_html__( 'Content Padding', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%', 'em' ),
			'selectors' => array(
				'{{WRAPPER}} .cs-fancy-card .cs-fc-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			)
		) );
        $this->end_controls_section();

		$this->start_controls_section( 'subtitle_style_section', array(
			'label' => __( 'Subtitle', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE
		) );
        $this->add_control( 'subtitle_style', array(
			'label' => esc_html__( 'Style', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => array(
				'' => esc_html__( 'Default', 'loftocean' ),
                'style-underline' => esc_html__( 'Underline', 'loftocean' ),
                'style-bordered' => esc_html__( 'Bordered', 'loftocean' )
			)
		) );
        $this->add_control( 'subtitle_preset_color', array(
			'label' => esc_html__( 'Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => array(
				'' => esc_html__( 'Default', 'loftocean' ),
				'color-primary' => esc_html__( 'Primary', 'loftocean' ),
                'color-secondary' => esc_html__( 'Secondary', 'loftocean' ),
				'color-white' => esc_html__( 'White', 'loftocean' ),
                'color-black' => esc_html__( 'Black', 'loftocean' ),
				'custom' => esc_html__( 'Custom', 'loftocean' )
			)
		) );
        $this->add_control( 'subtitle_custom_color', array(
			'label' => esc_html__( 'Custom Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'condition' => array(
				'subtitle_preset_color[value]' => 'custom',
			),
            'selectors' => array(
				'{{WRAPPER}} .cs-subtitle' => 'color: {{VALUE}};',
			)
		) );
        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'subtitle_typography',
				'label'    => esc_html__( 'Typography', 'loftocean' ),
				'selector' => '{{WRAPPER}} .cs-subtitle',
			)
		);
		$this->add_responsive_control( 'subtitle_margin', array(
			'label' => esc_html__( 'Margin', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em', '%', 'rem' ),
			'selectors' => array(
				'{{WRAPPER}} .cs-subtitle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
			)
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'title_style_section', array(
            'label' => __( 'Title', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'title_tag', array(
            'label' => esc_html__( 'HTML Tag', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'h4',
            'options' => array(
                'h1' => esc_html__( 'H1', 'loftocean' ),
                'h2' => esc_html__( 'H2', 'loftocean' ),
                'h3' => esc_html__( 'H3', 'loftocean' ),
                'h4' => esc_html__( 'H4', 'loftocean' ),
                'h5' => esc_html__( 'H5', 'loftocean' ),
                'h6' => esc_html__( 'H6', 'loftocean' ),
            )
        ) );
        $this->add_control( 'title_preset_color', array(
            'label' => esc_html__( 'Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '',
            'options' => array(
                '' => esc_html__( 'Default', 'loftocean' ),
                'color-primary' => esc_html__( 'Primary', 'loftocean' ),
                'color-secondary' => esc_html__( 'Secondary', 'loftocean' ),
                'color-white' => esc_html__( 'White', 'loftocean' ),
                'color-black' => esc_html__( 'Black', 'loftocean' ),
                'custom' => esc_html__( 'Custom', 'loftocean' )
            )
        ) );
        $this->add_control( 'title_custom_color', array(
            'label' => esc_html__( 'Custom Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'condition' => array(
                'title_preset_color[value]' => 'custom',
            ),
            'selectors' => array(
                '{{WRAPPER}} .cs-fc-title.cs-title' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .cs-fc-title.cs-title',
            )
        );
		$this->add_responsive_control( 'title_margin', array(
			'label' => esc_html__( 'Margin', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em', '%', 'rem' ),
			'selectors' => array(
				'{{WRAPPER}} .cs-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
			)
		) );
		$this->end_controls_section();

        $this->start_controls_section( 'text_style_section', array(
            'label' => __( 'Text', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'text_preset_color', array(
            'label' => esc_html__( 'Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '',
            'options' => array(
                '' => esc_html__( 'Default', 'loftocean' ),
                'color-primary' => esc_html__( 'Primary', 'loftocean' ),
                'color-secondary' => esc_html__( 'Secondary', 'loftocean' ),
                'color-white' => esc_html__( 'White', 'loftocean' ),
                'color-black' => esc_html__( 'Black', 'loftocean' ),
                'custom' => esc_html__( 'Custom', 'loftocean' )
            )
        ) );
        $this->add_control( 'text_custom_color', array(
            'label' => esc_html__( 'Custom Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'condition' => array(
                'text_preset_color[value]' => 'custom',
            ),
            'selectors' => array(
                '{{WRAPPER}} .cs-fc-text' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'text_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .cs-fc-text',
            )
        );
		$this->add_responsive_control( 'text_margin', array(
			'label' => esc_html__( 'Margin', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em', '%', 'rem' ),
			'selectors' => array(
				'{{WRAPPER}} .cs-fc-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
			)
		) );
		$this->end_controls_section();

        $this->start_controls_section( 'button_style_section', array(
            'label' => __( 'Button', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'button_style', array(
			'label' => esc_html__( 'Button Style', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'cs-btn-underline',
			'options' => array(
				'' => esc_html__( 'Solid', 'loftocean' ),
				'cs-btn-outline' => esc_html__( 'Outline', 'loftocean' ),
                'cs-btn-underline' => esc_html__( 'Underline', 'loftocean' )
			)
		) );
        $this->add_control( 'button_shape', array(
			'label' => esc_html__( 'Button Shape', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => array(
				'' => esc_html__( 'Default', 'loftocean' ),
				'cs-btn-square' => esc_html__( 'Square', 'loftocean' ),
                'cs-btn-rounded' => esc_html__( 'Rounded', 'loftocean' ),
                'cs-btn-pill' => esc_html__( 'Pill', 'loftocean' )
			),
            'condition' => array(
				'button_style[value]!' => 'cs-btn-underline',
			),
		) );
        $this->add_control( 'button_size', array(
			'label' => esc_html__( 'Button Size', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => array(
				'cs-btn-small' => esc_html__( 'Small', 'loftocean' ),
				'' => esc_html__( 'Medium', 'loftocean' ),
                'cs-btn-large' => esc_html__( 'Large', 'loftocean' ),
                'cs-btn-extra-large' => esc_html__( 'Extra Large', 'loftocean' )
			)
		) );
        $this->add_control( 'button_preset_color', array(
			'label' => esc_html__( 'Button Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => array(
				'' => esc_html__( 'Default', 'loftocean' ),
				'cs-btn-color-primary' => esc_html__( 'Primary', 'loftocean' ),
                'cs-btn-color-secondary' => esc_html__( 'Secondary', 'loftocean' ),
				'cs-btn-color-white' => esc_html__( 'White', 'loftocean' ),
                'cs-btn-color-black' => esc_html__( 'Black', 'loftocean' ),
				'custom' => esc_html__( 'Custom', 'loftocean' )
			)
		) );

        $this->start_controls_tabs( 'tabs_button_style' );
        $this->start_controls_tab( 'tab_button_normal', array(
        	'label' => esc_html__( 'Normal', 'loftocean' ),
            'condition' => array(
				'button_preset_color[value]' => 'custom',
			),
        ) );
        $this->add_control( 'button_custom_background_color', array(
			'label' => esc_html__( 'Button Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'condition' => array(
				'button_preset_color[value]' => 'custom',
			),
            'selectors' => array(
				'{{WRAPPER}} .cs-fc-btn .button' => '--btn-bg: {{VALUE}};',
			)
		) );
        $this->add_control( 'button_custom_text_color', array(
			'label' => esc_html__( 'Text Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'condition' => array(
				'button_preset_color[value]' => 'custom',
                'button_style[value]' => ''
			),
            'selectors' => array(
				'{{WRAPPER}} .cs-fc-btn .button' => '--btn-color: {{VALUE}};',
			)
		) );
        $this->add_control( 'underline_button_custom_text_color', array(
			'label' => esc_html__( 'Text Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'condition' => array(
				'button_preset_color[value]' => 'custom',
                'button_style[value]' => 'cs-btn-underline'
			),
            'selectors' => array(
				'{{WRAPPER}} .cs-fc-btn .button' => 'color: {{VALUE}};',
			)
		) );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_button_hover', array(
            'label' => esc_html__( 'Hover', 'loftocean' ),
            'condition' => array(
                'button_preset_color[value]' => 'custom',
            ),
        ) );

        $this->add_control( 'button_custom_hover_background_color', array(
            'label' => esc_html__( 'Button Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'condition' => array(
                'button_preset_color[value]' => 'custom',
            ),
            'selectors' => array(
                '{{WRAPPER}} .cs-fc-btn .button' => '--btn-bg-hover: {{VALUE}};',
            )
        ) );
        $this->add_control( 'button_custom_hover_text_color', array(
            'label' => esc_html__( 'Text Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'condition' => array(
                'button_preset_color[value]' => 'custom',
                'button_style[value]' => ''
            ),
            'selectors' => array(
                '{{WRAPPER}} .cs-fc-btn .button' => '--btn-color-hover: {{VALUE}};',
            )
        ) );
        $this->add_control( 'underline_button_custom_hover_text_color', array(
            'label' => esc_html__( 'Text Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'condition' => array(
                'button_preset_color[value]' => 'custom',
                'button_style[value]' => 'cs-btn-underline'
            ),
            'selectors' => array(
                '{{WRAPPER}} .cs-fc-btn .button:hover' => 'color: {{VALUE}};',
            )
        ) );
        $this->end_controls_tab();
    	$this->end_controls_tabs();

		$this->add_responsive_control( 'button_margin', array(
			'label' => esc_html__( 'Margin', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em', '%', 'rem' ),
			'selectors' => array(
				'{{WRAPPER}} .cs-fc-btn .button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
			)
		) );

        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'label'    => esc_html__( 'Typography', 'loftocean' ),
				'selector' => '{{WRAPPER}} .cs-fc-btn .button',
			)
		);
        $this->add_control( 'button_icon', array(
			'label' => esc_html__( 'Button Icon', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => array(
				'' => esc_html__( 'None', 'loftocean' ),
				'icon-line' => esc_html__( 'Line', 'loftocean' ),
                'icon-arrow' => esc_html__( 'Arrow 1', 'loftocean' ),
				'arrow-2' => esc_html__( 'Arrow 2', 'loftocean' ),
                'arrow-3' => esc_html__( 'Arrow 3', 'loftocean' ),
				'icon-plus' => esc_html__( 'Plus', 'loftocean' )
			)
		) );
        $this->end_controls_section();
	}
	/**
	* Written in PHP and used to generate the final HTML.
    * @access protected
	*/
	protected function render() {
        $settings = $this->get_settings_for_display();

		$this->add_render_attribute( array(
			'wrapper' => array( 'class' => array( 'cs-fancy-card', $settings[ 'general_style' ] ) ),
			'subtitle' => array( 'class' => array( 'cs-subtitle' ) ),
			'title' => array( 'class' => array( 'cs-fc-title', 'cs-title' ) ),
			'text' => array( 'class' => array( 'cs-fc-text' ) ),
			'link' => array( 'class' => array( 'cs-fc-link' ) ),
			'button' => array( 'class' => array( 'button' ), 'role' => 'button' )
		) );
		$this->add_inline_editing_attributes( 'subtitle', 'none' );
		$this->add_inline_editing_attributes( 'title', 'none' );
		$this->add_inline_editing_attributes( 'text', 'advanced' );

		if ( 'style-1' == $settings[ 'general_style' ] ) {
			in_array( $settings[ 'image_position' ], array( 'left', 'right' ) ) ? $this->add_render_attribute( 'wrapper', 'class', 'layout-row' ) : '';
			in_array( $settings[ 'image_position' ], array( 'bottom', 'right' ) ) ? $this->add_render_attribute( 'wrapper', 'class', 'order-reverse' ) : '';
		} else {
			if ( ! empty( $settings[ 'content_position' ] ) ) {
				$this->add_render_attribute( 'wrapper', 'class', $settings[ 'content_position' ] );
			}
		}

		$alignment = array( 'text_alignment' => '', 'text_alignment_mobile' => '-mobile', 'text_alignment_tablet' => '-tablet' );
		foreach( $alignment as $align => $after ) {
			if ( ! empty( $settings[ $align ] ) ) {
				$this->add_render_attribute( 'wrapper', 'class', $settings[ $align ] . $after );
			}
		}

		$color_settings = array( 'subtitle', 'title', 'text', 'button' );
		foreach( $color_settings as $element ) {
			$id = $element . '_preset_color';
			if ( ! empty( $settings[ $id ] ) && ( 'custom' != $settings[ $id ] ) ) {
				$this->add_render_attribute( $element, 'class', $settings[ $id ] );
			}
		}
		$not_empty_settings = array(
			'button' => array( 'button_style', 'button_size' ),
			'subtitle' => array( 'subtitle_style' ),
			'wrapper' => array( 'image_hover_effect' )
		);
		foreach( $not_empty_settings as $element => $attrs ) {
			foreach( $attrs as $id ) {
				if ( ! empty( $settings[ $id ] ) ) {
					$this->add_render_attribute( $element, 'class', $settings[ $id ] );
				}
			}
		}
		if ( ! empty( $settings[ 'button_shape' ] ) && ( 'cs-btn-underline' != $settings['button_style'] ) ) {
			$this->add_render_attribute( 'button', 'class', $settings['button_shape'] );
		} ?>

		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
            <div class="cs-fc-wrap"><?php
				if ( ! empty( $settings[ 'link' ][ 'url' ] ) ) :
					$this->add_link_attributes( 'link', $settings[ 'link' ] ); ?>
	                <a <?php $this->print_render_attribute_string( 'link' ); ?> aria-label="<?php esc_attr_e( 'Fancy Card Link', 'loftocean' ); ?>"></a><?php
				endif;
				if ( ! empty( $settings[ 'image' ][ 'url' ] ) ) : ?>
	               	<div class="cs-fc-img">
	                    <?php \Elementor\Group_Control_Image_Size::print_attachment_image_html( $settings, 'image', 'image' ); ?>
	                </div><?php
				endif; ?>

                <div class="cs-fc-content"><?php
					if ( ! empty( $settings[ 'subtitle' ] ) ) : ?>
						<div class="cs-subtitle-wrap"><span <?php $this->print_render_attribute_string( 'subtitle' ); ?>><?php $this->print_unescaped_setting( 'subtitle' ); ?></span></div><?php
					endif;
					if ( ! empty( $settings[ 'title' ] ) ) : ?>
			            <<?php echo esc_attr( $settings[ 'title_tag' ] ); ?> <?php $this->print_render_attribute_string( 'title' ); ?>>
			                <?php echo wp_kses( $settings[ 'title' ], array(
			                    'br' => array( 'class' => 1 ),
			                    'em' => array( 'class' => 1 ),
			                    'strong' => array( 'class' => 1 ),
			                    'small' => array( 'class' => 1 ),
			                    'mark' => array( 'class' => 1 )
			                ) ); ?>
			            </<?php echo esc_attr( $settings[ 'title_tag' ] ); ?>><?php
					endif;
					if ( ! empty( $settings[ 'text' ] ) ) : ?>
			            <div <?php $this->print_render_attribute_string( 'text' ); ?>>
			                <?php $this->print_text_editor( $settings[ 'text' ] ); ?>
			            </div><?php
					endif;
					if ( ! empty( $settings[ 'button_text' ] ) ) :
						if ( ! empty( $settings[ 'button_link' ] ) ) {
							$this->add_link_attributes( 'button', $settings[ 'button_link' ] );
						}
						$this->add_render_attribute( 'button_text', 'class', 'cs-btn-text' );
				        $this->add_inline_editing_attributes( 'button_text', 'none' );

						$has_icon = ! empty( $settings[ 'button_icon' ] );
						$has_icon ? $this->add_render_attribute( 'button', 'class', 'cs-btn-with-icon' ) : ''; ?>
		                <div class="cs-fc-btn">
		                    <a <?php $this->print_render_attribute_string( 'button' ); ?>>
		                        <span <?php $this->print_render_attribute_string( 'button_text' ); ?>><?php $this->print_unescaped_setting( 'button_text' ); ?></span><?php
								if ( $has_icon ) :
					            	$this->add_render_attribute( 'button_icon', 'class', array( 'cs-btn-icon', $settings[ 'button_icon' ] ) );
	            					in_array( $settings[ 'button_icon' ], array( 'arrow-2', 'arrow-3' ) ) ? $this->add_render_attribute( 'button_icon', 'class', 'icon-arrow' ) : ''; ?>
					            	<span <?php $this->print_render_attribute_string( 'button_icon' ); ?>></span><?php
					            endif; ?>
		                    </a>
		                </div><?php
					endif; ?>
				</div>
            </div>
        </div><?php
    }
    /**
	* Render button widget output in the editor.
	* Written as a Backbone JavaScript template and used to generate the live preview.
	* @access protected
	*/
	protected function content_template() { ?>
		<#
		view.addRenderAttribute( 'wrapper', 'class', [ 'cs-fancy-card', settings[ 'general_style' ] ] );
		view.addRenderAttribute( 'subtitle', 'class', 'cs-subtitle' );
		view.addRenderAttribute( 'title', 'class', [ 'cs-fc-title', 'cs-title' ] ),
		view.addRenderAttribute( 'text', 'class', 'cs-fc-text' );
		view.addRenderAttribute( 'link', 'class', 'cs-fc-link' );
		view.addRenderAttribute( 'button', 'class', 'button' );

		view.addInlineEditingAttributes( 'subtitle', 'none' );
		view.addInlineEditingAttributes( 'title', 'none' );
		view.addInlineEditingAttributes( 'text', 'advanced' );

		if ( 'style-1' == settings[ 'general_style' ] ) {
			[ 'left', 'right' ].includes( settings[ 'image_position' ] ) ? view.addRenderAttribute( 'wrapper', 'class', 'layout-row' ) : '';
			[ 'bottom', 'right' ].includes( settings[ 'image_position' ] ) ? view.addRenderAttribute( 'wrapper', 'class', 'order-reverse' ) : '';
		} else {
			if ( settings[ 'content_position' ] ) {
				view.addRenderAttribute( 'wrapper', 'class', settings[ 'content_position' ] );
			}
		}

		var alignment = { 'text_alignment': '', 'text_alignment_mobile': '-mobile', 'text_alignment_tablet': '-tablet' };
		jQuery.each( alignment, function( align, after ) {
			if ( settings[ align ] ) {
				view.addRenderAttribute( 'wrapper', 'class', settings[ align ] + after );
			}
		} );

		[ 'subtitle', 'title', 'text', 'button' ].forEach( function( element ) {
			var id = element + '_preset_color';
			if ( settings[ id ] && ( 'custom' != settings[ id ] ) ) {
				view.addRenderAttribute( element, 'class', settings[ id ] );
			}
		} );
		var notEmptySettings = {
			'button': [ 'button_style', 'button_size' ],
			'subtitle': [ 'subtitle_style' ],
			'wrapper': [ 'image_hover_effect' ]
		};
		jQuery.each( notEmptySettings, function( element, attrs ) {
			attrs.forEach( function( id ) {
				if ( settings[ id ] ) {
					view.addRenderAttribute( element, 'class', settings[ id ] );
				}
			} );
		} );
		if ( settings[ 'button_shape' ] && ( 'cs-btn-underline' != settings[ 'button_style' ] ) ) {
			view.addRenderAttribute( 'button', 'class', settings[ 'button_shape' ] );
		}
		var title = settings.title, $div = jQuery( '<div>' ), allowedTags = [ 'BR', 'EM', 'STRONG', 'SMALL', 'MARK' ];
        $div.html( title ).find( '*' ).each( function() {
            if ( jQuery( this ).get( 0 ).nodeName && ! allowedTags.includes( jQuery( this ).get( 0 ).nodeName ) ) {
                jQuery( this ).before( jQuery( this ).text() ).remove();
            }
        } );
        title = $div.html(); #>

		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
            <div class="cs-fc-wrap"><#
				if ( settings[ 'link' ][ 'url' ] ) { #>
	                <a {{{ view.getRenderAttributeString( 'link' ) }}} href="{{ settings.link.url }}"></a><#
				}
				if ( settings[ 'image' ][ 'url' ] ) {
					var image = {
						id: settings.image.id,
						url: settings.image.url,
						size: settings.image_size,
						dimension: settings.image_custom_dimension,
						model: view.getEditModel()
					};
					var imageURL = elementor.imagesManager.getImageUrl( image );
					if ( imageURL ) {  #>
						<div class="cs-fc-img">
							<img src="{{ imageURL }}">
						</div><#
					}
				} #>

                <div class="cs-fc-content"><#
					if ( settings[ 'subtitle' ] ) { #>
						<div class="cs-subtitle-wrap"><span {{{ view.getRenderAttributeString( 'subtitle' ) }}}>{{{ settings.subtitle }}}</span></div><#
					}
					if ( title ) { #>
			            <{{{ settings.title_tag }}} {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ title }}}</{{{ settings.title_tag }}}><#
					}
					if ( settings[ 'text' ] ) { #>
			            <div {{{ view.getRenderAttributeString( 'text' ) }}}>
			                {{{ settings.text }}}
			            </div><#
					}
					if ( settings[ 'button_text' ] ) {
						view.addRenderAttribute( 'button_text', 'class', 'cs-btn-text' );
						view.addInlineEditingAttributes( 'button_text', 'none' ); #>
		                <div class="cs-fc-btn"><#
							var hasIcon = settings[ 'button_icon' ];
							hasIcon ? view.addRenderAttribute( 'button', 'class', 'cs-btn-with-icon' ) : ''; #>
		                    <a href="{{ settings[ 'button_link' ][ 'url' ] }}" {{{ view.getRenderAttributeString( 'button' ) }}}>
								<span {{{ view.getRenderAttributeString( 'button_text' ) }}}>{{{ settings[ 'button_text' ] }}}</span><#
		                        if ( hasIcon ) {
		                        	view.addRenderAttribute( 'button_icon', 'class', [ 'cs-btn-icon', settings[ 'button_icon' ] ] );
		                        	[ 'arrow-2', 'arrow-3' ].includes( settings[ 'button_icon' ] ) ? view.addRenderAttribute( 'button_icon', 'class', 'icon-arrow' ) : ''; #>
	            					<span {{{ view.getRenderAttributeString( 'button_icon' ) }}}></span><#
		                    	} #>
							</a>
		                </div><#
					} #>
				</div>
            </div>
        </div><?php
	}
}
