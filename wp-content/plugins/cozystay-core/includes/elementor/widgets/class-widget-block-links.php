<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Block Links.
 */
class Widget_Block_Links extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceanblocklinks', array( 'id' => 'block-links' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Block Links', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-t-letter';
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
		return [ 'block links' ];
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
		$this->start_controls_section( 'block1_content_section', array(
			'label' => __( 'Block 1', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT
		) );
		$this->add_control( 'block1_link', array(
			'type' => \Elementor\Controls_Manager::URL,
			'default' => array( 'url' => '#' ),
			'label' => esc_html__( 'Block Link', 'loftocean' ),
            'placeholder' => __( 'Enter the URL', 'loftocean' ),
		) );
        $this->add_control( 'block1_subtitle', array(
            'label'   => esc_html__( 'Subtitle', 'loftocean' ),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => esc_html__( 'Section Subtitle', 'loftocean' )
        ) );
        $this->add_control( 'block1_title', array(
            'label'   => esc_html__( 'Title', 'loftocean' ),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => esc_html__( 'Section Title', 'loftocean' ),
            'description' => esc_html__( 'Support HTML tag <br>, <em>,  <strong>, <small> and <mark> only.', 'loftocean' )
        ) );
        $this->add_control( 'block1_text', array(
            'label'   => esc_html__( 'Text', 'loftocean' ),
            'type'    => \Elementor\Controls_Manager::WYSIWYG,
            'default' => '<p>' . esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 'loftocean' ) . '</p>'
        ) );
        $this->add_control( 'block1_button_text', array(
            'label'   => esc_html__( 'Button Text', 'loftocean' ),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__( 'Discover More', 'loftocean' )
        ) );
        $this->end_controls_section();

        $this->start_controls_section( 'block2_content_section', array(
            'label' => __( 'Block 2', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT
        ) );
        $this->add_control( 'block2_link', array(
            'type' => \Elementor\Controls_Manager::URL,
            'default' => array( 'url' => '#' ),
            'label' => esc_html__( 'Block Link', 'loftocean' ),
            'placeholder' => __( 'Enter the URL', 'loftocean' ),
        ) );
        $this->add_control( 'block2_subtitle', array(
            'label'   => esc_html__( 'Subtitle', 'loftocean' ),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => esc_html__( 'Section Subtitle', 'loftocean' )
        ) );
        $this->add_control( 'block2_title', array(
            'label'   => esc_html__( 'Title', 'loftocean' ),
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => esc_html__( 'Section Title', 'loftocean' ),
            'description' => esc_html__( 'Support HTML tag <br>, <em>,  <strong>, <small> and <mark> only.', 'loftocean' )
        ) );
        $this->add_control( 'block2_text', array(
            'label'   => esc_html__( 'Text', 'loftocean' ),
            'type'    => \Elementor\Controls_Manager::WYSIWYG,
            'default' => '<p>' . esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 'loftocean' ) . '</p>'
        ) );
        $this->add_control( 'block2_button_text', array(
            'label'   => esc_html__( 'Button Text', 'loftocean' ),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__( 'Discover More', 'loftocean' )
        ) );
        $this->end_controls_section();

        $this->start_controls_section( 'general_style_section', array(
            'label' => esc_html__( 'General', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'height', array(
			'label' => esc_html__( 'Height', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => array(
				'' => esc_html__( 'Default', 'loftocean' ),
				'height-full' => esc_html__( 'Fit To Screen', 'loftocean' )
			)
		) );
        $this->add_control( 'white_text_on_hover', array(
            'label' => esc_html__( 'White text on hover ', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'on',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
		$this->end_controls_section();

        $this->start_controls_section( 'block1_style_section', array(
			'label' => __( 'Block 1', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE
		) );
        $this->add_control( 'block1_background_color', array(
            'label' => esc_html__( 'Background Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .cs-bl-item.first' => 'background-color: {{VALUE}};',
            )
        ) );
        $this->add_control( 'block1_background_image', array(
			'label' => esc_html__( 'Background Image', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::MEDIA
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'block2_style_section', array(
			'label' => __( 'Block 2', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE
		) );
        $this->add_control( 'block2_background_color', array(
            'label' => esc_html__( 'Background Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .cs-bl-item.last' => 'background-color: {{VALUE}};',
            )
        ) );
        $this->add_control( 'block2_background_image', array(
			'label' => esc_html__( 'Background Image', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::MEDIA
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
                '{{WRAPPER}} .cs-title' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .cs-title',
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
                '{{WRAPPER}} .cs-text' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'text_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .cs-text',
            )
        );
		$this->add_responsive_control( 'text_margin', array(
			'label' => esc_html__( 'Margin', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em', '%', 'rem' ),
			'selectors' => array(
				'{{WRAPPER}} .cs-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
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
			'default' => '',
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

        $this->add_control( 'button_custom_background_color', array(
			'label' => esc_html__( 'Button Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'condition' => array(
				'button_preset_color[value]' => 'custom',
			),
            'selectors' => array(
				'{{WRAPPER}} .cs-bl-item-inner .button' => '--btn-bg: {{VALUE}};',
			)
		) );
        $this->add_control( 'button_custom_text_color', array(
			'label' => esc_html__( 'Button Text Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'condition' => array(
				'button_preset_color[value]' => 'custom',
                'button_style[value]' => ''
			),
            'selectors' => array(
				'{{WRAPPER}} .cs-bl-item-inner .button' => '--btn-color: {{VALUE}};',
			)
		) );
        $this->add_control( 'underline_button_custom_text_color', array(
			'label' => esc_html__( 'Button Text Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'condition' => array(
				'button_preset_color[value]' => 'custom',
                'button_style[value]' => 'cs-btn-underline'
			),
            'selectors' => array(
				'{{WRAPPER}} .cs-bl-item-inner .button' => 'color: {{VALUE}};',
			)
		) );

		$this->add_responsive_control( 'button_margin', array(
			'label' => esc_html__( 'Margin', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em', '%', 'rem' ),
			'selectors' => array(
				'{{WRAPPER}} .cs-bl-item-inner .button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
			)
		) );

        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'label'    => esc_html__( 'Typography', 'loftocean' ),
				'selector' => '{{WRAPPER}} .cs-bl-item-inner .button',
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

        $this->add_render_attribute( 'wrapper', 'class', array( 'cs-block-links' ) );
		$this->add_render_attribute( 'button', 'class', array( 'button' ) );

        $color_settings = array( 'subtitle', 'title', 'text', 'button' );
        foreach( $color_settings as $element ) {
            $id = $element . '_preset_color';
            if ( ! empty( $settings[ $id ] ) && ( 'custom' != $settings[ $id ] ) ) {
                $this->add_render_attribute( $element, 'class', $settings[ $id ] );
            }
        }
        $not_empty_settings = array(
            'button' => array( 'button_style', 'button_size' ),
			'wrapper' => array( 'height' )
        );
        foreach( $not_empty_settings as $element => $attrs ) {
            foreach( $attrs as $id ) {
                if ( ! empty( $settings[ $id ] ) ) {
                    $this->add_render_attribute( $element, 'class', $settings[ $id ] );
                }
            }
        }
		if ( 'on' == $settings[ 'white_text_on_hover' ] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'hover-text-white' );
		}
        if ( ! empty( $settings[ 'button_shape' ] ) && ( 'cs-btn-underline' != $settings[ 'button_style' ] ) ) {
            $this->add_render_attribute( 'button', 'class', $settings['button_shape'] );
        } ?>

        <div <?php $this->print_render_attribute_string( 'wrapper' ); ?>><?php
		$blocks = array( 'block1', 'block2' );
		foreach( $blocks as $block ) :
			if ( ! empty( $settings[ $block . '_subtitle' ] )
				|| ! empty( $settings[ $block . '_title' ] )
				|| ! empty( $settings[ $block . '_text' ] )
				|| ! empty( $settings[ $block . '_button_text' ] )
				|| ! empty( $settings[ $block . '_background_image' ][ 'url' ] ) ) :

				$subtitle_key = $block . '_subtitle';
				$text_key = $block . '_text';
				$title_key = $block . '_title';
				$button_key = $block . '_button_text';
				$this->add_render_attribute( $subtitle_key, 'class', array( 'cs-subtitle' ) );
				if ( ! empty( $settings[ 'subtitle_style' ] ) ) {
					$this->add_render_attribute( $subtitle_key, 'class', $settings[ 'subtitle_style' ] );
				}
				$this->add_inline_editing_attributes( $subtitle_key, 'none' );
				$this->add_render_attribute( $title_key, 'class', array( 'cs-title' ) );
				$this->add_inline_editing_attributes( $title_key, 'none' );
				$this->add_render_attribute( $text_key, 'class', array( 'cs-text' ) );
				$this->add_inline_editing_attributes( $text_key, 'advanced' );
				$this->add_render_attribute( $button_key, 'class', array( 'cs-btn-text' ) );
				$this->add_inline_editing_attributes( $button_key, 'none' );

				$colors = array(
					$subtitle_key => 'subtitle_preset_color',
					$text_key => 'text_preset_color',
					$title_key => 'title_preset_color',
					$button_key => 'button_preset_color'
				);
				foreach( $colors as $key => $id ) {
					if ( ! empty( $settings[ $id ] ) && ( 'custom' != $settings[ $id ] ) ) {
			            $this->add_render_attribute( $key, 'class', $settings[ $id ] );
			        }
				} ?>

				<div class="cs-bl-item <?php if ( 'block1' == $block ) : ?>first<?php else : ?>last<?php endif; ?>"><?php
				if ( ! empty( $settings[ $block . '_background_image' ][ 'url' ] ) ) : ?>
                    <div class="cs-bl-item-bg">
                        <div class="cs-bl-item-bg-container" style="background-image:url(<?php echo esc_url( $settings[ $block . '_background_image' ][ 'url' ] ); ?>);"></div>
                    </div><?php
				endif;
				if ( ! empty( $settings[ $subtitle_key ] ) || ! empty( $settings[ $title_key ] ) || ! empty( $settings[ $text_key ] ) || ! empty( $settings[ $button_key ] ) ) : ?>
                    <div class="cs-bl-item-inner"><?php
					if ( ! empty( $settings[ $subtitle_key ] ) ) : ?>
						<div class="cs-subtitle-wrap"><span <?php $this->print_render_attribute_string( $subtitle_key ); ?>><?php $this->print_unescaped_setting( $subtitle_key ); ?></span></div><?php
					endif;
					if ( ! empty( $settings[ $title_key ] ) ) : ?>
						<<?php echo esc_attr( $settings[ 'title_tag' ] ); ?> <?php $this->print_render_attribute_string( $title_key ); ?>>
			                <?php echo wp_kses( $settings[ $title_key ], array(
			                    'br' => array( 'class' => 1 ),
			                    'em' => array( 'class' => 1 ),
			                    'strong' => array( 'class' => 1 ),
			                    'small' => array( 'class' => 1 ),
			                    'mark' => array( 'class' => 1 )
			                ) ); ?>
			            </<?php echo esc_attr( $settings[ 'title_tag' ] ); ?>><?php
					endif;
					if ( ! empty( $settings[ $text_key ] ) ) : ?>
                        <div <?php $this->print_render_attribute_string( $text_key ); ?>><?php $this->print_text_editor( $settings[ $text_key ] ); ?></div><?php
					endif;
					if( ! empty( $settings[ $button_key ] ) ) :
						$has_icon = ! empty( $settings[ 'button_icon' ] );
						$has_icon ? $this->add_render_attribute( 'button', 'class', 'cs-btn-with-icon' ) : ''; ?>
                        <div <?php  $this->print_render_attribute_string( 'button' ); ?>>
                            <span <?php $this->print_render_attribute_string( $button_key ); ?>><?php echo $settings[ $button_key ]; ?></span><?php
							if ( $has_icon ) :
				            	$this->add_render_attribute( 'button_icon', 'class', array( 'cs-btn-icon', $settings[ 'button_icon' ] ) );
            					in_array( $settings[ 'button_icon' ], array( 'arrow-2', 'arrow-3' ) ) ? $this->add_render_attribute( 'button_icon', 'class', 'icon-arrow' ) : ''; ?>
				            	<span <?php $this->print_render_attribute_string( 'button_icon' ); ?>></span><?php
				            endif; ?>
                        </div><?php
					endif; ?>
					</div><?php
					if ( ! empty( $settings[ $block . '_link' ][ 'url' ] ) ) :
			            $this->add_link_attributes( $block . '_link', $settings[ $block . '_link' ] );
			            $this->add_render_attribute( $block . '_link', 'class', 'cs-bl-link' ); ?>
	                    <a <?php $this->print_render_attribute_string( $block . '_link' ); ?> aria-label="<?php esc_attr_e( 'Block Link', 'loftocean' ); ?>"></a><?php
					endif;
				endif; ?>
                </div><?php
			endif;
		endforeach; ?>
		</div><?php
	}
    /**
	* Render button widget output in the editor.
	* Written as a Backbone JavaScript template and used to generate the live preview.
	* @access protected
	*/
	protected function content_template() { ?>
		<#
		view.addRenderAttribute( 'wrapper', 'class', 'cs-block-links' );
	   	view.addRenderAttribute( 'button', 'class', 'button' );

	    [ 'subtitle', 'title', 'text', 'button' ].forEach( function( element ) {
	        var id = element + '_preset_color';
	        if ( settings[ id ] && ( 'custom' != settings[ id ] ) ) {
	            view.addRenderAttribute( element, 'class', settings[ id ] );
	        }
	    } );
	    var notEmptySettings = {
	        'button': [ 'button_style', 'button_size' ],
			'wrapper': [ 'height' ]
	    };
	    jQuery.each( notEmptySettings, function( element, attrs ) {
	        attrs.forEach( function( id ) {
	            if ( settings[ id ] ) {
	                view.addRenderAttribute( element, 'class', settings[ id ] );
	            }
	        } );
	    } );
		if ( 'on' == settings[ 'white_text_on_hover' ] ) {
			view.addRenderAttribute( 'wrapper', 'class', 'hover-text-white' );
		}
	    if ( settings[ 'button_shape' ] && ( 'cs-btn-underline' != settings[ 'button_style' ] ) ) {
	        view.addRenderAttribute( 'button', 'class', settings['button_shape'] );
	    } #>

	    <div {{{ view.getRenderAttributeString( 'wrapper' ) }}}><#
		[ 'block1', 'block2' ].forEach( function( block ) {
			if ( settings[ block + '_subtitle' ] || settings[ block + '_title' ] || settings[ block + '_text' ] || settings[ block + '_button_text' ] || settings[ block + '_background_image' ][ 'url' ] ) {
				var subtitleKey = block + '_subtitle', textKey = block + '_text',
					titleKey = block + '_title', buttonKey = block + '_button_text';

				view.addRenderAttribute( subtitleKey, 'class', 'cs-subtitle' );
				if ( settings[ 'subtitle_style' ] ) {
					view.addRenderAttribute( subtitleKey, 'class', settings[ 'subtitle_style' ] );
				}
				view.addInlineEditingAttributes( subtitleKey, 'none' );
				view.addRenderAttribute( titleKey, 'class', 'cs-title' );
				view.addInlineEditingAttributes( titleKey, 'none' );
				view.addRenderAttribute( textKey, 'class', 'cs-text' );
				view.addInlineEditingAttributes( textKey, 'advanced' );
				view.addRenderAttribute( buttonKey, 'class', 'cs-btn-text' );
				view.addInlineEditingAttributes( buttonKey, 'none' );

				var colors = {};
				colors[ subtitleKey ] = 'subtitle_preset_color';
				colors[ textKey ] = 'text_preset_color';
				colors[ titleKey ] = 'title_preset_color';
				colors[ buttonKey ] = 'button_preset_color';
				jQuery.each( colors, function( element, sid ) {
					if ( settings[ sid ] && ( 'custom' != settings[ sid ] ) ) {
			            view.addRenderAttribute( element, 'class', settings[ sid ] );
			        }
				} ); #>
				<div class="cs-bl-item <# if ( 'block1' == block ) { #>first<# } else { #>last<# } #>"><#
				if ( settings[ block + '_background_image' ][ 'url' ] ) { #>
	                <div class="cs-bl-item-bg">
	                    <div class="cs-bl-item-bg-container" style="background-image:url({{ settings[ block + '_background_image' ][ 'url' ] }});"></div>
	                </div><#
				}
				if ( settings[ subtitleKey ] || settings[ titleKey ] || settings[ textKey ] || settings[ buttonKey ] ) { #>
	                <div class="cs-bl-item-inner"><#
					if ( settings[ subtitleKey ] ) { #>
						<div class="cs-subtitle-wrap"><span {{{ view.getRenderAttributeString( subtitleKey ) }}}>{{{ settings[ subtitleKey ] }}}</span></div><#
					}
					if ( settings[ titleKey ] ) {
						var title = settings[ titleKey ], $div = jQuery( '<div>' ), allowedTags = [ 'BR', 'EM', 'STRONG', 'SMALL', 'MARK' ];
						$div.html( title ).find( '*' ).each( function() {
							if ( jQuery( this ).get( 0 ).nodeName && ! allowedTags.includes( jQuery( this ).get( 0 ).nodeName ) ) {
								jQuery( this ).before( jQuery( this ).text() ).remove();
							}
						} );
						title = $div.html(); #>
						<{{{ settings[ 'title_tag' ] }}} {{{ view.getRenderAttributeString( titleKey ) }}}>{{{ title }}}</{{{ settings[ 'title_tag' ] }}}><#
					}
					if ( settings[ textKey ] ) { #>
	                    <div {{{ view.getRenderAttributeString( textKey ) }}}>{{{ settings[ textKey ] }}}</div><#
					}
					if( settings[ buttonKey ] ) {
						var hasIcon = settings[ 'button_icon' ];
						hasIcon ? view.addRenderAttribute( 'button', 'class', 'cs-btn-with-icon' ) : ''; #>
	                    <div {{{ view.getRenderAttributeString( 'button' ) }}}>
	                        <span {{{ view.getRenderAttributeString( buttonKey ) }}}>{{{ settings[ buttonKey ] }}}</span><#
	                        if ( hasIcon ) {
	                        	view.addRenderAttribute( 'button_icon', 'class', [ 'cs-btn-icon', settings[ 'button_icon' ] ] );
	                        	[ 'arrow-2', 'arrow-3' ].includes( settings[ 'button_icon' ] ) ? view.addRenderAttribute( 'button_icon', 'class', 'icon-arrow' ) : ''; #>
            					<span {{{ view.getRenderAttributeString( 'button_icon' ) }}}></span><#
	                    	} #>
	                    </div><#
					} #>
					</div><#
					if ( settings[ block + '_link' ][ 'url' ] ) {
			            view.addRenderAttribute( block + '_link', 'class', 'cs-bl-link' ); #>
	                    <a href="{{ settings[ block + '_link' ][ 'url' ] }}" {{{ view.getRenderAttributeString( block + '_link' ) }}}></a><#
					}
				} #>
	            </div><#
			}
		} ); #>
		</div><?php
	}
}
