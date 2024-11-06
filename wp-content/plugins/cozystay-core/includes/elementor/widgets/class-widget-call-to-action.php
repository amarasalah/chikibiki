<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Call to Action
 */
class Widget_Call_To_Action extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceancalltoaction', array( 'id' => 'call-to-action' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Call to Action', 'loftocean' );
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
		return [ 'call to action', 'call', 'action' ];
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
			'default' => esc_html__( 'Call To Action Subtitle', 'loftocean' )
        ) );
        $this->add_control( 'title', array(
            'label'   => esc_html__( 'Title', 'loftocean' ),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => esc_html__( 'Call To Action Title', 'loftocean' ),
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

        $this->start_controls_section( 'label_content_section', array(
            'label' => esc_html__( 'Label', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ) );
        $this->add_control( 'label_text', array(
            'label'   => esc_html__( 'Label', 'loftocean' ),
            'type'    => \Elementor\Controls_Manager::TEXT
        ) );
        $this->end_controls_section();

        $this->start_controls_section( 'general_style_section', array(
            'label' => esc_html__( 'General', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
		$this->add_control( 'general_style', array(
            'label' => esc_html__( 'Style', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'cta-layout-text-overlap',
            'options' => array(
                'cta-layout-text-overlap' => esc_html__( 'Overlap', 'loftocean' ),
                'cta-layout-text-normal' => esc_html__( 'Normal', 'loftocean' ),
            )
        ) );
		$this->add_control( 'vertical_alignment', array(
            'label' => esc_html__( 'Content Vertical Position', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'content-middle',
            'condition' => array( 'general_style[value]' => 'cta-layout-text-overlap' ),
            'options' => array(
                'content-top' => esc_html__( 'Top', 'loftocean' ),
                'content-middle' => esc_html__( 'Middle', 'loftocean' ),
                'content-bottom' => esc_html__( 'Bottom', 'loftocean' ),
            )
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
            'default' => 'cta-hover-img-zoom',
            'options' => array(
                'cta-hover-img-zoom' => esc_html__( 'Zoom In', 'loftocean' ),
                'cta-hover-img-zoom-out' => esc_html__( 'Zoom Out', 'loftocean' ),
                '' => esc_html__( 'None', 'loftocean' ),
            )
        ) );
        $this->add_control( 'image_offset_border', array(
            'label' => esc_html__( 'Image Offset Border', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'condition' => array( 'general_style[value]' => 'cta-layout-text-normal' ),
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
		$this->add_control( 'color_scheme', array(
            'label' => esc_html__( 'Color Scheme', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '',
            'condition' => array( 'general_style[value]' => 'cta-layout-text-overlap' ),
            'options' => array(
                '' => esc_html__( 'Inherited', 'loftocean' ),
                'light-color' => esc_html__( 'Light', 'loftocean' ),
                'dark-color' => esc_html__( 'Dark', 'loftocean' ),
            )
        ) );
		$this->add_control( 'text_visibility', array(
            'label' => esc_html__( 'Text Visibility', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '',
            'condition' => array( 'general_style[value]' => 'cta-layout-text-overlap' ),
            'options' => array(
                '' => esc_html__( 'Always Display', 'loftocean' ),
                'text-hover-to-show' => esc_html__( 'Hover to Display', 'loftocean' ),
                'text-hover-to-hide' => esc_html__( 'Hover to Hide', 'loftocean' ),
                'button-hover-to-show' => esc_html__( 'Hover to show button', 'loftocean' )
            )
        ) );
        $this->add_control( 'inner_border', array(
            'label' => esc_html__( 'Inner Border', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'condition' => array( 'general_style[value]' => 'cta-layout-text-overlap' ),
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'border_color', array(
        	'label' => esc_html__( 'Border Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',

            'selectors' => array(
				'{{WRAPPER}} .cs-cta' => '--cta-border-color: {{VALUE}};'
			),
        	'conditions' => array(
				'relation' => 'or',
				'terms' => array(
					array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => 'general_style',
								'operator' => '==',
								'value' => 'cta-layout-text-normal'
							),
							array(
								'name' => 'image_offset_border',
								'operator' => '==',
								'value' => 'on'
							)
						)
					),
					array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => 'general_style',
								'operator' => '==',
								'value' => 'cta-layout-text-overlap'
							),
							array(
								'name' => 'inner_border',
								'operator' => '==',
								'value' => 'on'
							)
						)
					)
				)
			)
        ) );
		$this->add_responsive_control( 'general_content_padding', array(
			'label' => esc_html__( 'Content Area Padding', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em', '%', 'rem' ),
			'selectors' => array(
				'{{WRAPPER}} .cs-cta-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
			)
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'overlay_style_section', array(
            'label' => esc_html__( 'Overlay', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => array( 'general_style[value]' => 'cta-layout-text-overlap' )
        ) );
		$this->start_controls_tabs( 'tabs_background' );
		$this->start_controls_tab( 'tab_background_normal', array(
			'label' => esc_html__( 'Normal', 'loftocean' ),
		) );
		$this->add_group_control( \Elementor\Group_Control_Background::get_type(), array(
			'name' => 'overlay',
			'selector' => '{{WRAPPER}} .cs-cta-overlay',
			'fields_options' => array(
				'background' => array( 'frontend_available' => true )
			)
		) );
		$this->add_control( 'overlay_opacity', array(
			'label' => esc_html__( 'Opacity', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'default' => array( 'size' => 0.3 ),
			'range' => array(
				'px' => array( 'max' => 1, 'step' => 0.1 )
			),
			'render_type' => 'ui',
			'separator' => 'before',
			'selectors' => array( '{{WRAPPER}} .cs-cta-overlay' => 'opacity: {{SIZE}};' )
		) );
		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_background_hover', array(
			'label' => esc_html__( 'Hover', 'loftocean' ),
		) );
		$this->add_group_control( \Elementor\Group_Control_Background::get_type(), array(
			'name' => 'overlay_hover',
			'selector' => '{{WRAPPER}} .cs-cta:hover .cs-cta-overlay',
		) );
		$this->add_control( 'overylay_hover_opacity', array(
			'label' => esc_html__( 'Opacity', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'default' => array( 'size' => 0.3 ),
			'range' => array(
				'px' => array( 'max' => 1, 'step' => 0.1 )
			),
			'render_type' => 'ui',
			'separator' => 'before',
			'selectors' => array( '{{WRAPPER}} .cs-cta:hover .cs-cta-overlay' => 'opacity: {{SIZE}};' )
		) );
		$this->end_controls_tab();
		$this->end_controls_tabs();
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
                '{{WRAPPER}} .cs-cta-title.cs-title' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .cs-cta-title.cs-title',
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
                '{{WRAPPER}} .cs-cta-text' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'text_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .cs-cta-text',
            )
        );
		$this->add_responsive_control( 'text_margin', array(
			'label' => esc_html__( 'Margin', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em', '%', 'rem' ),
			'selectors' => array(
				'{{WRAPPER}} .cs-cta-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
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
				'{{WRAPPER}} .cs-cta-btn .button' => '--btn-bg: {{VALUE}};',
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
				'{{WRAPPER}} .cs-cta-btn .button' => '--btn-color: {{VALUE}};',
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
				'{{WRAPPER}} .cs-cta-btn .button' => 'color: {{VALUE}};',
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
                '{{WRAPPER}} .cs-cta-btn .button' => '--btn-bg-hover: {{VALUE}};',
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
                '{{WRAPPER}} .cs-cta-btn .button' => '--btn-color-hover: {{VALUE}};',
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
                '{{WRAPPER}} .cs-cta-btn .button:hover' => 'color: {{VALUE}};',
            )
        ) );
        $this->end_controls_tab();
    	$this->end_controls_tabs();

		$this->add_responsive_control( 'button_margin', array(
			'label' => esc_html__( 'Margin', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em', '%', 'rem' ),
			'selectors' => array(
				'{{WRAPPER}} .cs-cta-btn .button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
			)
		) );

        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'label'    => esc_html__( 'Typography', 'loftocean' ),
				'selector' => '{{WRAPPER}} .cs-cta-btn .button',
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

        $this->start_controls_section( 'label_section', array(
            'label' => __( 'Label', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'label_position', array(
			'label' => esc_html__( 'Label Position', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'label-top-left',
			'options' => array(
				'label-top-left' => esc_html__( 'Top Left', 'loftocean' ),
                'label-top-right' => esc_html__( 'Top Right', 'loftocean' ),
				'label-bottom-right' => esc_html__( 'Bottom Right', 'loftocean' ),
                'label-bottom-left' => esc_html__( 'Bottom Left', 'loftocean' ),
				'label-centered' => esc_html__( 'Center', 'loftocean' )
			)
		) );
		$this->add_control( 'label_background_color', array(
            'label' => esc_html__( 'Background Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => array(
                '{{WRAPPER}} .cs-cta .cs-cta-label' => 'background-color: {{VALUE}};',
            )
        ) );
        $this->add_control( 'label_color', array(
            'label' => esc_html__( 'Text Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .cs-cta .cs-cta-label' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'label'    => esc_html__( 'Typography', 'loftocean' ),
				'selector' => '{{WRAPPER}} .cs-cta .cs-cta-label',
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

		$this->add_render_attribute( array(
			'wrapper' => array( 'class' => array( 'cs-cta', $settings[ 'general_style' ] ) ),
			'subtitle' => array( 'class' => array( 'cs-subtitle' ) ),
			'title' => array( 'class' => array( 'cs-cta-title', 'cs-title' ) ),
			'text' => array( 'class' => array( 'cs-cta-text' ) ),
			'link' => array( 'class' => array( 'cs-cta-link' ) ),
			'button_wrapper' => array( 'class' => array( 'cs-cta-btn' ) ),
			'button' => array( 'class' => array( 'button' ), 'role' => 'button' )
		) );
		$this->add_inline_editing_attributes( 'subtitle', 'none' );
		$this->add_inline_editing_attributes( 'title', 'none' );
		$this->add_inline_editing_attributes( 'text', 'advanced' );

		if ( 'cta-layout-text-overlap' == $settings[ 'general_style' ] ) {
			$this->add_render_attribute( 'wrapper', 'class', $settings[ 'vertical_alignment' ] );
			if ( ! empty( $settings[ 'text_visibility' ] ) ) {
				'button-hover-to-show' == $settings[ 'text_visibility' ] ? $this->add_render_attribute( 'button_wrapper', 'class', 'hover-slide-up' )
					: $this->add_render_attribute( 'wrapper', 'class', $settings[ 'text_visibility' ] );
			}
			if ( 'on' == $settings[ 'inner_border' ] ) {
				$this->add_render_attribute( 'wrapper', 'class', 'with-inner-border' );
			}
			if ( ! empty( $settings[ 'color_scheme' ] ) ) {
				$this->add_render_attribute( 'wrapper', 'class', $settings[ 'color_scheme' ] );
			}
		} else {
			if ( 'on' == $settings[ 'image_offset_border' ] ) {
				$this->add_render_attribute( 'wrapper', 'class', 'img-offset-border' );
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
            <div class="cs-cta-wrap"><?php
				if ( ! empty( $settings[ 'image' ][ 'url' ] ) ) :
					if ( 'cta-layout-text-normal' == $settings[ 'general_style' ] ) : ?>
		                <div class="cs-cta-img-wrap">
					        <div class="cs-cta-img">
					            <?php \Elementor\Group_Control_Image_Size::print_attachment_image_html( $settings, 'image', 'image' ); ?>
					        </div>
					        <div class="cs-offset-border"></div>
						</div><?php
		            else : ?>
		               	<div class="cs-cta-img">
		                    <?php \Elementor\Group_Control_Image_Size::print_attachment_image_html( $settings, 'image', 'image' ); ?>
		                </div><?php
		            endif;
				endif; ?>
                <div class="cs-cta-overlay"></div><?php
                if ( ! empty( $settings[ 'label_text' ] ) ) :
                	$this->add_render_attribute( 'label', 'class', array( 'cs-cta-label', $settings[ 'label_position' ] ) ); ?>
                	<div <?php $this->print_render_attribute_string( 'label') ?>>
					   <div class="cs-cta-label-text"><?php $this->print_unescaped_setting( 'label_text' ); ?></div>
					</div><?php
                endif; ?>

                <div class="cs-cta-content"><?php
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
	                <div <?php $this->print_render_attribute_string( 'button_wrapper' ); ?>>
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
			</div><?php
			if ( ! empty( $settings[ 'link' ][ 'url' ] ) ) :
				$this->add_link_attributes( 'link', $settings[ 'link' ] ); ?>
                <a <?php $this->print_render_attribute_string( 'link' ); ?> aria-label="<?php esc_attr_e( 'Call to Action Link', 'loftocean' ); ?>"></a><?php
			endif; ?>
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
		view.addRenderAttribute( 'wrapper', 'class', [ 'cs-cta', settings[ 'general_style' ] ] );
		view.addRenderAttribute( 'subtitle', 'class', 'cs-subtitle' );
		view.addRenderAttribute( 'title', 'class', [ 'cs-cta-title', 'cs-title' ] ),
		view.addRenderAttribute( 'text', 'class', 'cs-cta-text' );
		view.addRenderAttribute( 'link', 'class', 'cs-cta-link' );
		view.addRenderAttribute( 'button', 'class', 'button' );

		view.addInlineEditingAttributes( 'subtitle', 'none' );
		view.addInlineEditingAttributes( 'title', 'none' );
		view.addInlineEditingAttributes( 'text', 'advanced' );

		if ( 'cta-layout-text-overlap' == settings[ 'general_style' ] ) {
			view.addRenderAttribute( 'wrapper', 'class', settings[ 'vertical_alignment' ] );
			if ( settings[ 'text_visibility' ] ) {
				'button-hover-to-show' == settings[ 'text_visibility' ] ? view.addRenderAttribute( 'button', 'class', 'hover-slide-up' )
					: view.addRenderAttribute( 'wrapper', 'class', settings[ 'text_visibility' ] );
			}
			if ( 'on' == settings[ 'inner_border' ] ) {
				view.addRenderAttribute( 'wrapper', 'class', 'with-inner-border' );
			}
			if ( settings[ 'color_scheme' ] ) {
				view.addRenderAttribute( 'wrapper', 'class', settings[ 'color_scheme' ] );
			}
		} else {
			if ( 'on' == settings[ 'image_offset_border' ] ) {
				view.addRenderAttribute( 'wrapper', 'class', 'img-offset-border' );
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
            <div class="cs-cta-wrap"><#
				if ( settings[ 'image' ][ 'url' ] ) {
					var image = {
						id: settings.image.id,
						url: settings.image.url,
						size: settings.image_size,
						dimension: settings.image_custom_dimension,
						model: view.getEditModel()
					};
					var imageURL = elementor.imagesManager.getImageUrl( image );
					if ( imageURL ) {
						if ( 'cta-layout-text-normal' == settings[ 'general_style' ] ) { #>
							<div class="cs-cta-img-wrap">
								<div class="cs-cta-img">
									<img src="{{ imageURL }}">
								</div>
								<div class="cs-offset-border"></div>
							</div><#
						} else { #>
							<div class="cs-cta-img">
								<img src="{{ imageURL }}">
							</div><#
						}
					}
				} #>
                <div class="cs-cta-overlay"></div><#
                if ( settings[ 'label_text' ] ) {
               		view.addRenderAttribute( 'label', 'class', [ 'cs-cta-label', settings[ 'label_position'] ] ); #>
                	<div {{{ view.getRenderAttributeString( 'label' ) }}}>
					   <div class="cs-cta-label-text">{{{ settings.label_text }}}</div>
					</div><#
				} #>

                <div class="cs-cta-content"><#
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
	                <div class="cs-cta-btn"><#
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
			</div><#
			if ( settings[ 'link' ][ 'url' ] ) { #>
                <a {{{ view.getRenderAttributeString( 'link' ) }}} href="{{ settings.link.url }}"></a><#
			} #>
            </div>
        </div><?php
	}
}
