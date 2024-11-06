<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Info Box.
 */
class Widget_Info_Box extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceaninfobox', array( 'id' => 'info-box' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Info Box', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-slides';
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
		return [ 'info box', 'gallery' ];
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

        $repeater = new \Elementor\Repeater();
		$repeater->add_control( 'media_type', array(
			'label' => esc_html__( 'Choose Media', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::CHOOSE,
			'default' => 'image',
            'options' => array(
				'image' => array(
					'title' => esc_html__( 'Image', 'loftocean' ),
					'icon' => 'eicon-image'
				),
				'icon' => array(
					'title' => esc_html__( 'Icon', 'loftocean' ),
					'icon' => 'eicon-icon-box',
				)
			)
		) );
        $repeater->add_control( 'image', array(
			'condition' => array( 'media_type' => 'image' ),
            'type' => \Elementor\Controls_Manager::MEDIA,
			'ai' => array( 'active' => false )
        ) );
		$repeater->add_control( 'selected_icon', array(
			'type' => \Elementor\Controls_Manager::ICONS,
			'condition' => array( 'media_type' => 'icon' ),
			'fa4compatibility' => 'icon',
			'ai' => array( 'active' => false )
		) );
        $repeater->add_group_control( \Elementor\Group_Control_Image_Size::get_type(), array(
			'name' => 'image',
			'default' => 'thumbnail',
			'condition' => array( 'media_type' => 'image' ),
			'separator' => 'none',
		) );
		$repeater->add_responsive_control( 'width', array(
			'label' => esc_html__( 'Width', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'condition' => array( 'media_type' => 'image' ),
			'default' => array( 'unit' => '%' ),
			'tablet_default' => array( 'unit' => '%' ),
			'mobile_default' => array( 'unit' => '%' ),
			'size_units' => array( '%', 'px', 'vw' ),
			'range' => array(
				'%' => array( 'min' => 1, 'max' => 100 ),
				'px' => array( 'min' => 1, 'max' => 1000 ),
				'vw' => array( 'min' => 1, 'max' => 100 )
			),
			'selectors' => array( '{{WRAPPER}} {{CURRENT_ITEM}} .cs-info-box-img img' => 'width: {{SIZE}}{{UNIT}};' )
		) );
		$repeater->add_control( 'info_title',array(
			'label' => esc_html__( 'Info Title', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'label_block' => true,
			'default' => esc_html__( 'Info Title', 'loftocean' )
		) );
		$repeater->add_control( 'info_text',array(
			'label' => esc_html__( 'Info Text', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXTAREA,
			'label_block' => true,
			'default' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 'loftocean' )
		) );
		$repeater->add_control( 'button_text',array(
			'label' => esc_html__( 'Button Text', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'label_block' => true,
			'default' => esc_html__( 'Discover More', 'loftocean' )
		) );
        $repeater->add_control( 'link', array(
			'type' => \Elementor\Controls_Manager::URL,
			'default' => array( 'url' => '#' ),
			'label' => esc_html__( 'Link', 'loftocean' ),
            'placeholder' => __( 'Enter the URL', 'loftocean' ),
		) );
		$this->add_control( 'list', array(
			'label' => esc_html__( 'Info Box Items', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::REPEATER,
			'fields' => $repeater->get_controls(),
			'default' => array(
                array(
    				'info_title' => esc_html__( 'Info Title', 'loftocean' ),
                    'info_text' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 'loftocean' )
    			), array(
    				'info_title' => esc_html__( 'Info Title', 'loftocean' ),
                    'info_text' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 'loftocean' )
    			), array(
    				'info_title' => esc_html__( 'Info Title', 'loftocean' ),
                    'info_text' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 'loftocean' )
    			)
            ),
            'title_field' => '{{{ info_title }}}',
		) );
		$this->add_control( 'apply_link_on', array(
			'label' => esc_html__( 'Apply Link On', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'button',
			'options' => array(
				'button' => esc_html__( 'Button', 'loftocean' ),
				'title' => esc_html__( 'Title', 'loftocean' ),
				'box' => esc_html__( 'Box', 'loftocean' ),
            )
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'layout_section', array(
            'label' => __( 'Layout', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
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
			)
		) );
        $this->add_control( 'vertical_alignment', array(
			'label' => esc_html__( 'Vertical Aligment', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'valign-top',
			'options' => array(
				'valign-top' => esc_html__( 'Top', 'loftocean' ),
				'valign-middle' => esc_html__( 'Middle', 'loftocean' )
			)
		) );
        $this->add_responsive_control( 'column', array(
			'label' => esc_html__( 'Column', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'column-3',
			'options' => array(
				'column-1' => esc_html__( '1', 'loftocean' ),
				'column-2' => esc_html__( '2', 'loftocean' ),
                'column-3' => esc_html__( '3', 'loftocean' ),
				'column-4' => esc_html__( '4', 'loftocean' ),
				'column-5' => esc_html__( '5', 'loftocean' ),
				'column-6' => esc_html__( '6', 'loftocean' )
			)
		) );
        $this->add_control( 'space', array(
			'label' => esc_html__( 'Space Between (px)', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => array( 'px' => array( 'min' => 0, 'max' => 100, 'step' => 1 ) ),
			'render_type' => 'ui',
			'separator' => 'before',
			'selectors' => array( '{{WRAPPER}} .cs-info-box' => '--box-gap: {{SIZE}}px;' )
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'box_style_section', array(
            'label' => esc_html__( 'Box Style', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'box_background_color', array(
            'label' => esc_html__( 'Background Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .cs-info-box .cs-item-container' => 'background-color: {{VALUE}};'
            )
        ) );
        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), array(
			'name' => 'box_border',
			'selector' => '{{WRAPPER}} .cs-info-box .cs-item-container'
		) );
		$this->add_responsive_control( 'box_border_radius', array(
			'label' => esc_html__( 'Border Radius', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%', 'em' ),
			'selectors' => array(
				'{{WRAPPER}} .cs-info-box .cs-item-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			)
		) );
		$this->add_responsive_control( 'box_padding', array(
			'label' => esc_html__( 'Padding', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%', 'em' ),
			'selectors' => array(
				'{{WRAPPER}} .cs-info-box .cs-item-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			)
		) );
		$this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), array(
			'name' => 'container_box_shadow',
			'selector' => '{{WRAPPER}} .cs-info-box .cs-item-container'
		) );
        $this->end_controls_section();

		$this->start_controls_section( 'media_style_section', array(
            'label' => esc_html__( 'Media', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
		$this->add_control( 'image_position', array(
            'label' => esc_html__( 'Media Position', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'img-left',
            'options' => array(
                'img-left' => esc_html__( 'Left', 'loftocean' ),
                'img-top' => esc_html__( 'Top', 'loftocean' ),
                'img-right' => esc_html__( 'Right', 'loftocean' ),
            )
        ) );
        $this->add_control( 'image_spacing', array(
			'label' => esc_html__( 'Media Spacing (px)', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => array( 'px' => array( 'min' => 0, 'max' => 400, 'step' => 1 ) ),
			'render_type' => 'ui',
			'separator' => 'before',
			'selectors' => array( '{{WRAPPER}} .cs-info-box' => '--icon-space: {{SIZE}}px;' )
		) );
		$this->add_control( 'icon_color', array(
            'label' => esc_html__( 'Icon Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
			'selectors' => array(
				'{{WRAPPER}} .cs-info-box-img i' => 'color: {{VALUE}};',
				'{{WRAPPER}} .cs-info-box-img svg' => 'fill: {{VALUE}};'
			),
            'default' => ''
        ) );
        $this->add_control( 'icon_size', array(
			'label' => esc_html__( 'Icon Size', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => array( 'px' => array( 'min' => 0, 'max' => 400, 'step' => 1 ) ),
			'render_type' => 'ui',
			'separator' => 'before',
			'selectors' => array(
				'{{WRAPPER}} .cs-info-box-img i' => 'font-size: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .cs-info-box-img svg' => 'width: {{SIZE}}{{UNIT}}; max-height: {{SIZE}}{{UNIT}};'
			)
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'title_style_section', array(
            'label' => esc_html__( 'Title', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'title_tag', array(
            'label' => esc_html__( 'HTML Tag', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'h5',
            'options' => array(
                'h1' => esc_html__( 'H1', 'loftocean' ),
                'h2' => esc_html__( 'H2', 'loftocean' ),
                'h3' => esc_html__( 'H3', 'loftocean' ),
                'h4' => esc_html__( 'H4', 'loftocean' ),
                'h5' => esc_html__( 'H5', 'loftocean' ),
                'h6' => esc_html__( 'H6', 'loftocean' ),
            )
        ) );
        $this->add_control( 'title_color', array(
            'label' => esc_html__( 'Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .cs-info-box-title' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .cs-info-box-title',
            )
        );
        $this->end_controls_section();

        $this->start_controls_section( 'text_style_section', array(
            'label' => esc_html__( 'Text', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'text_color', array(
            'label' => esc_html__( 'Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .cs-info-box-text' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'text_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .cs-info-box-text',
            )
        );
        $this->end_controls_section();

        $this->start_controls_section( 'button_style_section', array(
            'label' => esc_html__( 'Button', 'loftocean' ),
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
				'{{WRAPPER}} .cs-info-box-btn .button' => '--btn-bg: {{VALUE}};',
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
				'{{WRAPPER}} .cs-info-box-btn .button' => '--btn-color: {{VALUE}};',
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
				'{{WRAPPER}} .cs-info-box-btn .button' => 'color: {{VALUE}};',
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
                '{{WRAPPER}} .cs-info-box-btn .button' => '--btn-bg-hover: {{VALUE}};',
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
                '{{WRAPPER}} .cs-info-box-btn .button' => '--btn-color-hover: {{VALUE}};',
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
                '{{WRAPPER}} .cs-info-box-btn .button:hover' => 'color: {{VALUE}};',
            )
        ) );
        $this->end_controls_tab();
    	$this->end_controls_tabs();

    	$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'label'    => esc_html__( 'Typography', 'loftocean' ),
				'selector' => '{{WRAPPER}} .cs-info-box-btn .button',
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
        if ( \LoftOcean\is_valid_array( $settings[ 'list' ] ) ) :
			$this->add_render_attribute( 'wrapper', 'class', array( 'cs-info-box', $settings[ 'column' ], $settings[ 'vertical_alignment' ], $settings[ 'image_position' ] ) );
            $alignment = array( 'text_alignment' => '', 'text_alignment_mobile' => '-mobile', 'text_alignment_tablet' => '-tablet' );
			$apply_link_on_button = ( 'button' == $settings[ 'apply_link_on' ] );
			$apply_link_on_title = ( 'title' == $settings[ 'apply_link_on' ] );
			$apply_link_on_box = ( 'box' == $settings[ 'apply_link_on' ] );

			$columns_prefix = array( 'tablet', 'mobile' );
			foreach( $columns_prefix as $cp ) {
				if ( ! empty( $settings[ 'column_' . $cp ] ) ) {
					$this->add_render_attribute( 'wrapper', 'class', $cp . '-' . $settings[ 'column_' . $cp ] );
				}
			}

            foreach( $alignment as $align => $after ) {
                if ( ! empty( $settings[ $align ] ) ) {
                    $this->add_render_attribute( 'wrapper', 'class', $settings[ $align ] . $after );
                }
            }

			$this->add_render_attribute( 'button', 'class', 'button' );

			if ( ! empty( $settings[ 'button_style' ] ) ) {
				$this->add_render_attribute( 'button', 'class', $settings[ 'button_style' ] );
			}
			if ( ! empty( $settings[ 'button_shape' ] && ( 'cs-btn-underline' != $settings[ 'button_style' ] ) ) ) {
				$this->add_render_attribute( 'button', 'class', $settings[ 'button_shape' ] );
			}
			if ( ! empty( $settings[ 'button_size' ] ) ) {
				$this->add_render_attribute( 'button', 'class', $settings[ 'button_size' ] );
			}
			if ( ! empty($settings[ 'button_preset_color' ] ) && ( 'custom' != $settings[ 'button_preset_color' ] ) ) {
				$this->add_render_attribute( 'button', 'class', $settings[ 'button_preset_color' ] );
			}

			$icon_style = $settings[ 'button_icon' ];
			$has_icon = ! empty( $icon_style );
			if ( $has_icon ) {
				$this->add_render_attribute( 'button_icon', 'class', array( 'cs-btn-icon', $icon_style ) );
				in_array( $icon_style, array( 'arrow-2', 'arrow-3' ) ) ? $this->add_render_attribute( 'button_icon', 'class', 'icon-arrow' ) : '';
			} ?>

			<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
                <div class="cs-info-box-wrap"><?php
				foreach ( $settings[ 'list'] as $index => $item ) :
					$title_key = 'list.' . $index . '.info_title';
					$text_key = 'list.' . $index . '.info_text';
					$button_key = 'list.' . $index . '.button_text';
					$item_wrap_key = 'list.' . $index . '.item';

					$has_link = ! empty( $item[ 'link' ][ 'url' ] );
					$show_box_link = $has_link && $apply_link_on_box;
					$show_title_link = $has_link && $apply_link_on_title;
					$show_button_link = $has_link && $apply_link_on_button;
					if ( $has_link ) {
						$this->add_link_attributes( 'link-' . $index, $item[ 'link' ], true );
					}

					$this->add_render_attribute( $item_wrap_key, 'class', array( 'cs-info-box-item', 'elementor-repeater-item-' . esc_attr( $item[ '_id' ] ) ) );
					$this->add_render_attribute( 'title-' . $index, 'class', array( 'cs-title', 'cs-info-box-title' ) );
					$this->add_inline_editing_attributes( $title_key, 'none' );
					$this->add_render_attribute( $text_key, 'class', 'cs-info-box-text' );
					$this->add_inline_editing_attributes( $text_key, 'none' );
					$this->add_render_attribute( $button_key, 'class', 'cs-btn-text' );
					$this->add_inline_editing_attributes( $button_key, 'none' );

					$show_media = ( 'icon' == $item[ 'media_type' ] && ! empty( $item[ 'selected_icon' ][ 'value' ] ) ) || ( 'image' == $item[ 'media_type' ] && ! empty( $item[ 'image' ][ 'id' ] ) ); ?>
                    <div <?php $this->print_render_attribute_string( $item_wrap_key ); ?>>
                    	<div class="cs-item-container"><?php
							if ( $show_media ) : ?>
	                        <div class="cs-info-box-img"><?php
								'image' == $item[ 'media_type' ] ? \Elementor\Group_Control_Image_Size::print_attachment_image_html( $item, 'image', 'image' )
									: \Elementor\Icons_Manager::render_icon( $item[ 'selected_icon' ], [ 'aria-hidden' => 'true' ] ); ?>
	                        </div><?php
							endif; ?>
	                        <div class="cs-info-box-content">
	                            <<?php echo $settings[ 'title_tag' ]; ?> <?php $this->print_render_attribute_string( 'title-' . $index ); if ( ! $show_title_link ) { $this->print_render_attribute_string( $title_key ); } ?>><?php
									if ( $show_title_link ) : ?><a <?php $this->print_render_attribute_string( 'link-' . $index ); $this->print_render_attribute_string( $title_key ); ?>><?php endif;
									$this->print_unescaped_setting( 'info_title', 'list', $index );
									if ( $show_title_link ) : ?></a><?php endif; ?>
								</<?php echo $settings[ 'title_tag' ]; ?>><?php
	                            if ( ! empty( $item[ 'info_text' ] ) ) : ?>
	                            	<div <?php $this->print_render_attribute_string( $text_key ); ?>><p><?php $this->print_unescaped_setting( 'info_text', 'list', $index ); ?></p></div><?php
	                            endif;
								if ( ! empty( $item[ 'button_text' ] ) ) :
									$has_icon ? $this->add_render_attribute( 'button' . $index, 'class', 'cs-btn-with-icon' ) : ''; ?>
		                            <div class="cs-info-box-btn">
		                                <<?php if ( $show_button_link ) : ?>a <?php $this->print_render_attribute_string( 'link-' . $index ); else : ?>div<?php endif; ?> <?php $this->print_render_attribute_string( 'button' ); ?>>
		                                    <span <?php $this->print_render_attribute_string( $button_key ); ?>><?php $this->print_unescaped_setting( 'button_text', 'list', $index ); ?></span><?php
											if ( $has_icon ) : ?>
								            	<span <?php $this->print_render_attribute_string( 'button_icon' ); ?>></span><?php
								            endif; ?>
		                                </<?php if ( $show_button_link ) : ?>a<?php else : ?>div<?php endif; ?>>
		                            </div><?php
								endif; ?>
	                        </div><?php
							if ( $show_box_link ) : ?>
								<a class="cs-info-box-link" <?php $this->print_render_attribute_string( 'link-' . $index ); ?>  aria-label="<?php esc_attr_e( 'Info Box Link', 'loftocean' ); ?>"></a><?php
							endif;  ?>
	                    </div>
                    </div><?php
				endforeach; ?>
				</div>
			</div><?php
        endif;
	}
    /**
	* Render button widget output in the editor.
	* Written as a Backbone JavaScript template and used to generate the live preview.
	* @access protected
	*/
	protected function content_template() { ?>
        <#
        if ( settings[ 'list' ] ) {
            view.addRenderAttribute( 'wrapper', 'class', [ 'cs-info-box', settings[ 'vertical_alignment' ], settings[ 'column' ], settings[ 'image_position' ] ] );
            var alignment = { 'text_alignment': '', 'text_alignment_mobile': '-mobile', 'text_alignment_tablet': '-tablet' };
            jQuery.each( alignment, function( align, after ) {
                if ( settings[ align ] ) {
                    view.addRenderAttribute( 'wrapper', 'class', settings[ align ] + after );
                }
            } );

			[ 'tablet', 'mobile' ].forEach( function( cp ) {
				if ( settings[ 'column_' + cp ] ) {
					view.addRenderAttribute( 'wrapper', 'class', cp + '-' + settings[ 'column_' + cp ] );
				}
			} );

			view.addRenderAttribute( 'button', 'class', 'button' );

			if ( settings[ 'button_style' ] ) {
				view.addRenderAttribute( 'button', 'class', settings[ 'button_style' ] );
			}
			if ( settings[ 'button_shape' ] && ( 'cs-btn-underline' != settings[ 'button_style' ] ) ) {
				view.addRenderAttribute( 'button', 'class', settings[ 'button_shape' ] );
			}
			if ( settings[ 'button_size' ] ) {
				view.addRenderAttribute( 'button', 'class', settings[ 'button_size' ] );
			}
			if ( settings[ 'button_preset_color' ] && ( 'custom' != settings[ 'button_preset_color' ] ) ) {
				view.addRenderAttribute( 'button', 'class', settings[ 'button_preset_color' ] );
			}

			var iconStyle = settings[ 'button_icon' ], hasIcon = !! iconStyle,
				applyLinkOnButton = ( 'button' == settings[ 'apply_link_on' ] ),
				applyLinkOnTitle = ( 'title' == settings[ 'apply_link_on' ] ),
				applyLinkOnBox = ( 'box' == settings[ 'apply_link_on' ] );

			if ( hasIcon ) {
				view.addRenderAttribute( 'button_icon', 'class', [ 'cs-btn-icon', iconStyle ] );
				[ 'arrow-2', 'arrow-3' ].includes( iconStyle ) ? view.addRenderAttribute( 'button_icon', 'class', 'icon-arrow' ) : '';
			} #>
			<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
				<div class="cs-info-box-wrap"><#
				settings[ 'list'].forEach( function( item, index ) {
					var linkURL = item[ 'link' ][ 'url' ] || '#',
						titleKey = 'list.' + index + '.info_title',
						textKey = 'list.' + index + '.info_text',
						buttonKey = 'list.' + index + '.button_text',
						itemKey = 'list.' + index + '.item',
						showBoxLink = linkURL && applyLinkOnBox,
						showTitleLink = linkURL && applyLinkOnTitle,
						showButtonLink = linkURL && applyLinkOnButton,
						showMedia = ( 'icon' == item[ 'media_type' ] && item[ 'selected_icon' ][ 'value' ] ) || ( 'image' == item[ 'media_type' ] && item[ 'image' ][ 'id' ] );

					view.addRenderAttribute( itemKey, 'class', [ 'cs-info-box-item', 'elementor-repeater-item-' + item[ '_id' ] ] );
					view.addRenderAttribute( 'title-' + index, 'class', [ 'cs-title', 'cs-info-box-title' ] );
					view.addInlineEditingAttributes( titleKey, 'none' );
					view.addRenderAttribute( textKey, 'class', 'cs-info-box-text' );
					view.addInlineEditingAttributes( textKey, 'none' );
					view.addRenderAttribute( buttonKey, 'class', 'cs-btn-text' );
					view.addInlineEditingAttributes( buttonKey, 'none' ); #>
					<div {{{ view.getRenderAttributeString( itemKey ) }}}>
						<div class="cs-item-container"><#
							if ( showMedia ) { #>
								<div class="cs-info-box-img"><#
								if ( 'icon' == item[ 'media_type' ] ) {
									var iconHTML = elementor.helpers.renderIcon( view, item.selected_icon, { 'aria-hidden': true }, 'i' , 'object' ); #>
									{{{ iconHTML.value }}}<#
								} else {
									var imageSettings = {
			                            id: item.image.id,
			                            url: item.image.url,
			                            size: item.image_size,
			                            dimension: item.image_custom_dimension,
			                            model: view.getEditModel()
			                        };
			                        var imageURL = elementor.imagesManager.getImageUrl( imageSettings );
			                        if ( imageURL ) { #>
			                  			<img src="{{ imageURL }}"><#
			                        }
								} #>
								</div><#
							} #>
							<div class="cs-info-box-content">
								<{{{ settings[ 'title_tag' ] }}} {{{ view.getRenderAttributeString( 'title-' + index ) }}}<# if ( ! showTitleLink ) { #> {{{ view.getRenderAttributeString( titleKey ) }}}<# } #>><#
									if ( showTitleLink ) { #><a href="{{{ linkURL }}}" {{{ view.getRenderAttributeString( titleKey ) }}}><# } #>
										{{{ item[ 'info_title' ] }}}<#
									if ( showTitleLink ) { #></a><# } #>
								</{{{ settings[ 'title_tag' ] }}}><#
								if ( item[ 'info_text' ] ) { #>
									<div {{{ view.getRenderAttributeString( textKey ) }}}><p>{{{ item[ 'info_text' ] }}}</p></div><#
								}
								if ( item[ 'button_text' ] ) {
									hasIcon ? view.addRenderAttribute( 'button', 'class', 'cs-btn-with-icon' ) : ''; #>
									<div class="cs-info-box-btn">
										<<# if ( showButtonLink ) { #>a href="{{{ linkURL }}}"<# } else { #>div<# } #> {{{ view.getRenderAttributeString( 'button' ) }}}>
											<span {{{ view.getRenderAttributeString( buttonKey ) }}}>{{{ item[ 'button_text' ] }}}</span><#
					                        if ( hasIcon ) { #>
				            					<span {{{ view.getRenderAttributeString( 'button_icon' ) }}}></span><#
					                    	} #>
										</<# if ( showButtonLink ) { #>a<# } else { #>div<# } #>>
									</div><#
								} #>
							</div><#
							if ( showBoxLink ) { #>
								<a href="{{{ linkURL }}}" class="cs-info-box-link"></a><#
							} #>
						</div>
					</div><#
				} ); #>
				</div>
			</div><#
		} #><?php
	}
}
