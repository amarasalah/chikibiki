<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget OpenTable.
 */
class Widget_Reservation_Filter extends \LoftOcean\Elementor_Widget_Base {
	/*
	* Room Max number
	*/ 
	protected $room_max_number = 50;
	/*
	* Adult max number
	*/
	protected $adult_max_number = 50;
	/*
	* Child Max number
	*/
	protected $child_max_number = 50;
	/*
	* Current search vars
	*/
	protected $search_vars = array();
	/*
	* Label prefix
	*/
	protected $label_prefix = '';
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceanreservationfilter', array( 'id' => 'reservation-filter' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Reservation Filter', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-form-horizontal';
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
		return [ 'reservation', 'filter', 'reservation filter' ];
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
		$this->start_controls_section( 'content_section', array(
			'label' => __( 'General', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		) );
		$this->add_control( 'items_title',array(
			'label' => esc_html__( 'Display Selected Items:', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::HEADING
		) );
        $this->add_control( 'item_check_in', array(
            'label' => esc_html__( 'Check In', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'on',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'item_check_out', array(
            'label' => esc_html__( 'Check Out', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'on',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'item_rooms', array(
            'label' => esc_html__( 'Rooms', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'on',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'item_guests', array(
            'label' => esc_html__( 'Guests', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'on',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on',
            'separator' => 'after'
        ) );
		$this->add_control( 'guests_items',array(
			'label' => esc_html__( 'Guests Sup items', 'loftocean' ),
			'default' => array( 'adults', 'children' ),
			'type' => \Elementor\Controls_Manager::SELECT2,
			'multiple' => true,
			'select2options' => array( 'allowClear' => true ),
			'condition' => array( 'item_guests' => 'on' ),
			'options' => array(
				'adults' => esc_html__( 'Adults', 'loftocean' ),
				'children' => esc_html__( 'Children', 'loftocean' ),
			)
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'label_section', array(
			'label' => __( 'Label', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		) );
		$this->add_control( 'custom_label_check_in', array(
			'label' => esc_html__( 'Check In', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'Check In', 'loftocean' )
		) );
		$this->add_control( 'custom_label_check_out', array(
			'label' => esc_html__( 'Check Out', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'Check Out', 'loftocean' )
		) );
		$this->add_control( 'custom_label_merged_dates', array(
			'label' => esc_html__( 'Dates', 'loftocean' ),
            'condition' => array( 'style' => 'style-block', 'item_check_in' => 'on', 'item_check_out' => 'on', 'block_style_merge_checkin_checkout' => 'on' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'Dates', 'loftocean' )
		) );
		$this->add_control( 'custom_label_rooms', array(
			'label' => esc_html__( 'Rooms', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'Rooms', 'loftocean' )
		) );
		$this->add_control( 'custom_label_guests', array(
			'label' => esc_html__( 'Guests', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'Guests', 'loftocean' )
		) );
		$this->add_control( 'custom_label_adults', array(
			'label' => esc_html__( 'Adults', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'Adults', 'loftocean' )
		) );
		$this->add_control( 'custom_label_children', array(
			'label' => esc_html__( 'Children', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'Children', 'loftocean' )
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'button_section', array(
			'label' => __( 'Button', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		) );
		$this->add_control( 'custom_button_text', array(
			'label' => esc_html__( 'Button Text', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'Check Availability', 'loftocean' )
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'general_style_section', array(
			'label' => __( 'General', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		) );
        $this->add_control( 'style', array(
			'label' => esc_html__( 'Style', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'style-banner',
			'options' => array(
				'style-block'  => esc_html__( 'Block', 'loftocean' ),
				'style-banner' => esc_html__( 'Banner 1', 'loftocean' ),
				'style-banner-2' => esc_html__( 'Banner 2', 'loftocean' ),
				'style-banner-3' => esc_html__( 'Banner 3', 'loftocean' ),
				'style-banner-4' => esc_html__( 'Banner 4', 'loftocean' )
            )
		) );
		$this->add_control( 'form_field_border_color', array(
			'label' => esc_html__( 'Form Field Border Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'selectors' => array(
				'{{WRAPPER}} .cs-reservation-form' => '--form-bd: {{VALUE}};',
			)
		) );
		$this->add_control( 'style_text_color', array(
			'label' => esc_html__( 'Text Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'selectors' => array(
				'{{WRAPPER}} .cs-reservation-form' => '--text-color: {{VALUE}};',
			)
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'label_style_section', array(
			'label' => __( 'Label', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'label_position', array(
			'label' => esc_html__( 'Label Position', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'condition' => array( 'style' => array( 'style-block', 'style-banner', 'style-banner-4' ) ),
			'default' => '',
			'options' => array(
				''  => esc_html__( 'Top', 'loftocean' ),
				'inline-label' => esc_html__( 'Inline', 'loftocean' ),
				'hide-label' => esc_html__( 'Hide', 'loftocean' )
            )
		) );
		$this->add_control( 'label_position_2', array(
			'label' => esc_html__( 'Label Position', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'condition' => array( 'style' => 'style-banner-2' ),
			'default' => '',
			'options' => array(
				''  => esc_html__( 'Top', 'loftocean' ),
				'hide-label' => esc_html__( 'Hide', 'loftocean' )
            )
		) );
		$this->add_control( 'label_position_3', array(
			'label' => esc_html__( 'Label Position', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'condition' => array( 'style' => 'style-banner-3' ),
			'default' => 'inline-label',
			'options' => array(
				'inline-label' => esc_html__( 'Inline', 'loftocean' ),
				'hide-label' => esc_html__( 'Hide', 'loftocean' )
            )
		) );
        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'label'    => esc_html__( 'Typography', 'loftocean' ),
				'selector' => '{{WRAPPER}} .cs-reservation-form .field-wrap > .cs-form-label',
			)
		);
        $this->add_control( 'label_text_color', array(
			'label' => esc_html__( 'Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'selectors' => array(
				'{{WRAPPER}} .cs-reservation-form .field-wrap > .cs-form-label' => 'color: {{VALUE}};',
			)
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'field_style_section', array(
			'label' => __( 'Field', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'form_field_style', array(
			'label' => esc_html__( 'Form Field Style', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'condition' => array( 'style' => array( 'style-block', 'style-banner', 'style-banner-4' ) ),
			'default' => 'cs-form-square',
			'options' => array(
				'cs-form-square' => esc_html__( 'Square', 'loftocean' ),
				'cs-form-rounded' => esc_html__( 'Rounded', 'loftocean' ),
				'cs-form-pill' => esc_html__( 'Pill', 'loftocean' ),
				'cs-form-underline' => esc_html__( 'Underline', 'loftocean' ),
            )
		) );
		$this->add_responsive_control( 'form_field_height', array(
			'label' => esc_html__( 'Field Height (px)', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => array(
				'px' => array( 'min' => 0, 'max' => 100, 'step' => 1 )
			),
			'render_type' => 'ui',
			'selectors' => array( '{{WRAPPER}} .cs-reservation-form' => '--field-height: {{SIZE}}px;' )
		) );
		$this->add_responsive_control( 'form_field_space', array(
			'label' => esc_html__( 'Field Space (px)', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => array(
				'px' => array( 'min' => 0, 'max' => 100, 'step' => 1 )
			),
			'render_type' => 'ui',
			'selectors' => array( '{{WRAPPER}} .cs-reservation-form' => '--field-space: {{SIZE}}px;' )
		) );
        $this->add_control( 'field_guests_separator', array(
            'label' => esc_html__( 'Separate Adults and Children fields for "Block" Style', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'condition' => array( 'style' => 'style-block' ),
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
		$this->add_control( 'block_style_merge_checkin_checkout', array(
			'label' => esc_html__( 'Merge "Check In" and "Check Out"', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
            'condition' => array( 'style' => 'style-block', 'item_check_in' => 'on', 'item_check_out' => 'on' ),
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'dropdown_style_section', array(
			'label' => __( 'Dropdown', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		) );
        $this->add_control( 'dropdown_background_color', array(
			'label' => esc_html__( 'Background Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'selectors' => array( '{{WRAPPER}} .cs-reservation-form' => '--dropdown-bg: {{VALUE}};' )
		) );
        $this->add_control( 'dropdown_text_color', array(
			'label' => esc_html__( 'Text Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'selectors' => array( '{{WRAPPER}} .cs-reservation-form' => '--dropdown-color: {{VALUE}};' )
		) );
        $this->add_control( 'dropdown_border_color', array(
			'label' => esc_html__( 'Border Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'selectors' => array( '{{WRAPPER}} .cs-reservation-form' => '--dropdown-border: {{VALUE}};' )
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'button_style_section', array(
			'label' => __( 'Button', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		) );
        $this->add_control( 'button_style', array(
			'label' => esc_html__( 'Button Style', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
            'condition' => array( 'style!' => 'style-banner-3' ),
			'default' => '',
			'options' => array(
				'' => esc_html__( 'Solid', 'loftocean' ),
				'cs-btn-outline' => esc_html__( 'Outline', 'loftocean' )
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
            'condition' => array( 'style!' => 'style-banner-3' )
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
				'button_preset_color' => 'custom',
			),
        ) );
        $this->add_control( 'button_custom_background_color', array(
			'label' => esc_html__( 'Button Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'condition' => array( 'button_preset_color' => 'custom' ),
            'selectors' => array( '{{WRAPPER}} .cs-reservation-form .button' => '--btn-bg: {{VALUE}};' )
		) );
        $this->add_control( 'button_custom_text_color', array(
			'label' => esc_html__( 'Text Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'condition' => array( 'button_preset_color' => 'custom' ),
            'selectors' => array( '{{WRAPPER}} .cs-reservation-form .button' => '--btn-color: {{VALUE}};' ),
			'separator' => 'after'
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
            'condition' => array( 'button_preset_color[value]' => 'custom' ),
            'selectors' => array( '{{WRAPPER}} .cs-reservation-form .button' => '--btn-bg-hover: {{VALUE}};' )
        ) );
        $this->add_control( 'button_custom_hover_text_color', array(
            'label' => esc_html__( 'Text Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'condition' => array( 'button_preset_color[value]' => 'custom' ),
            'selectors' => array( '{{WRAPPER}} .cs-reservation-form .button' => '--btn-color-hover: {{VALUE}};' ),
			'separator' => 'after'
        ) );
        $this->end_controls_tab();
    	$this->end_controls_tabs();

    	$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'label'    => esc_html__( 'Typography', 'loftocean' ),
				'selector' => '{{WRAPPER}} .cs-reservation-form .button',
			)
		);
    	$this->end_controls_section();

    	$this->start_controls_section( 'extra_style_section', array(
			'label' => __( 'Extra', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			'condition' => array( 'style' => 'style-banner-2' )
		) );
    	$this->add_control( 'extra_icon_color', array(
            'label' => esc_html__( 'Icon Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array( '{{WRAPPER}} .cs-reservation-form' => '--icon-color: {{VALUE}};' )
        ) );
    	$this->add_control( 'extra_divider_line_color', array(
            'label' => esc_html__( 'Divider Line Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array( '{{WRAPPER}} .cs-reservation-form' => '--divider-color: {{VALUE}};' )
        ) );
    	$this->end_controls_section();
	}
	/**
	* Written in PHP and used to generate the final HTML.
    * @access protected
	*/
	protected function render() {
		$this->label_prefix = 'room-reservation-' . $this->get_id() . rand( 1, 999 ) . '-';
		$label_prefix = $this->label_prefix;
		$this->room_max_number = apply_filters( 'loftocean_room_reservation_filter_max_room_number', $this->room_max_number );
		$this->adult_max_number = apply_filters( 'loftocean_room_reservation_filter_max_adult_number', $this->adult_max_number );
		$this->child_max_number = apply_filters( 'loftocean_room_reservation_filter_max_child_number', $this->child_max_number );

		$settings = $this->get_settings_for_display();
		$this->search_vars = apply_filters( 'loftocean_room_search_vars', array() );
		$search_vars = $this->search_vars;

		$search_url = apply_filters( 'loftocean_search_url', home_url( '/' ) );
		$display_date_format = apply_filters( 'loftocean_display_date_format', 'YYYY-MM-DD' );
		$date_format = 'YYYY-MM-DD';

		$style = $settings[ 'style' ];
		$is_style_banner2 = false;
		$this->add_render_attribute( array(
			'wrapper' => array( 'class' => array( 'cs-reservation-form', $style ) ),
			'form_wrapper' => array( 'class' => 'cs-form-wrap', 'data-display-date-format' => $display_date_format, 'data-date-format' => $date_format, 'action' => esc_url( $search_url ), 'method' => 'GET' ),
			'button' => array( 'class' => array( 'button' ), 'role' => 'button', 'type' => 'submit' ),
			'custom_button_text' => array( 'class' => array( 'btn-text' ) )
		) );
		$edit_attrs = array( 'custom_button_text' );
		foreach( $edit_attrs as $att ) {
			$this->add_inline_editing_attributes( $att, 'none' );
		}
		$button_class = array( 'button_style', 'button_shape' );
		if ( 'style-banner-3' !== $style ) {
			foreach( $button_class as $bs )	{
				empty( $settings[ $bs ] ) ? '' : $this->add_render_attribute( 'button', 'class', $settings[ $bs ] );
			}
		}
		$button_color = $settings[ 'button_preset_color' ];
		if ( ( ! empty( $button_color ) ) && ( 'custom' != $button_color ) ) {
			$this->add_render_attribute( 'button', 'class', $button_color );
		}

		$label_position = $settings[ 'label_position' ];
		$field_style = $settings[ 'form_field_style' ];
		switch( $style ) {
			case 'style-block':
			case 'style-banner':
				$this->add_render_attribute( 'wrapper', 'class', $field_style );
				break;
			case 'style-banner-2':
				$field_style = '';
				$is_style_banner2 = true;
				$label_position = $settings[ 'label_position_2' ];
				$this->add_render_attribute( 'wrapper', 'class', 'style-banner' );
				$this->add_render_attribute( 'wrapper', 'class', 'cs-form-square' );
				break;
			case 'style-banner-3':
				$field_style = '';
				$label_position = $settings[ 'label_position_3' ];
				$this->add_render_attribute( 'wrapper', 'class', 'style-banner' );
				$this->add_render_attribute( 'wrapper', 'class', 'cs-form-square' );
				break;
			case 'style-banner-4':
				$this->add_render_attribute( 'wrapper', 'class', 'style-banner' );
				$this->add_render_attribute( 'wrapper', 'class', $field_style );
		}
		$this->add_render_attribute( 'form_wrapper', 'data-elementor-widget-id', $this->get_id() );
		empty( $label_position ) ? '' : $this->add_render_attribute( 'wrapper', 'class', $label_position );
		$checkin_date = empty( $search_vars[ 'checkin' ] ) ? date( esc_html__( 'Y-m-d', 'loftocean' ) ) : $search_vars[ 'checkin' ];
		$checkout_date = empty( $search_vars[ 'checkout' ] ) ? date( esc_html__( 'Y-m-d', 'loftocean' ), strtotime( 'tomorrow' ) ) : $search_vars[ 'checkout' ]; ?>

		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<form <?php $this->print_render_attribute_string( 'form_wrapper' ); ?>><?php
				do_action( 'loftocean_room_search_form_fields_before' );
				if ( ( 'style-block' == $style ) && ( 'on' == $settings[ 'block_style_merge_checkin_checkout' ] ) ) : 
					if ( ( 'on' == $settings[ 'item_check_in' ] ) && ( 'on' == $settings[ 'item_check_in' ] ) ) : ?>
						<div class="cs-form-field-group date-group">
                            <label class="cs-form-label"><?php $this->print_unescaped_setting( 'custom_label_merged_dates' ); ?></label>
                            <input type="text" class="date-range-picker" value="<?php echo $checkin_date; ?> - <?php echo $checkout_date; ?>">
                            <div class="cs-form-field-group-inner">
                                <div class="cs-form-field cs-check-in">
                                    <div class="field-wrap">
                                        <div class="field-input-wrap checkin-date">
                                            <input type="hidden" value="" data-value="<?php echo $checkin_date; ?>" class="check-in-date" name="" readonly>
                                            <span class="input" role="textbox"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="date-separator"></div>
                                <div class="cs-form-field cs-check-out">
                                    <div class="field-wrap">
                                        <div class="field-input-wrap checkout-date">
                                            <input type="hidden" value="" data-value="<?php echo $checkout_date; ?>" class="check-out-date" name="" readonly>
                                            <span class="input" role="textbox"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><?php
					endif;
				else :
					if ( 'on' == $settings[ 'item_check_in' ] ) : ?>
						<div class="cs-form-field cs-check-in"><?php
							if ( $is_style_banner2 ) : ?>
								<div class="cs-form-icon">
									<svg xmlns="http://www.w3.org/2000/svg" width="30" viewBox="0 0 30 30">
										<path d="M7.11.06c-.22.1-.41.31-.49.52-.04.09-.06.52-.06.95v.78l-1.71.02c-1.68.02-1.73.02-2.17.18C1.45 2.95.6 3.79.17 5L0 5.47v21.42l.16.47c.43 1.21 1.27 2.05 2.5 2.48l.45.16h23.77l.47-.16c1.21-.43 2.05-1.27 2.48-2.5l.16-.45V5.47l-.16-.45c-.43-1.23-1.27-2.07-2.48-2.5-.46-.16-.48-.16-2.18-.18l-1.71-.02-.02-.87C23.42.5 23.39.41 23.02.14c-.23-.17-.78-.17-1.01 0-.38.28-.4.36-.42 1.32l-.02.87H8.44l-.02-.87C8.4.51 8.37.42 8.01.15c-.2-.15-.66-.19-.9-.08Zm-.55 4.78c.02.69.07.83.42 1.09.23.17.78.17 1.01 0 .35-.26.4-.39.42-1.09l.02-.64h13.12l.02.64c.02.69.07.83.42 1.09.23.17.78.17 1.01 0 .35-.26.4-.39.42-1.09l.02-.65 1.59.02 1.6.02.33.16c.42.21.76.55.97.97.16.33.16.34.18 1.7l.02 1.37H1.85V7.18c0-.93.02-1.33.08-1.54.18-.59.69-1.13 1.29-1.33.26-.09.52-.1 1.81-.11h1.5l.02.64Zm21.57 13.63-.02 8.18-.16.32c-.21.42-.57.78-.98.99l-.33.16H3.35l-.32-.16c-.42-.21-.78-.57-.99-.98l-.16-.33-.02-8.18V10.3h26.28v8.17Z"></path>
									</svg>
								</div><?php
							endif; ?>
							<div class="field-wrap">
								<label class="cs-form-label" for="<?php echo $label_prefix . 'checkin' ; ?>"><?php $this->print_unescaped_setting( 'custom_label_check_in' ); ?></label>

								<div class="field-input-wrap checkin-date">
									<input type="text" class="date-range-picker" value="<?php echo $checkin_date; ?> - <?php echo $checkout_date; ?>">
									<input type="text" value="" data-value="<?php echo $checkin_date; ?>" class="check-in-date" name="" id="<?php echo $label_prefix . 'checkin' ; ?>" readonly>
								</div>
							</div>
						</div><?php
					endif;
					if ( 'on' == $settings[ 'item_check_out' ] ) : ?>
						<div class="cs-form-field cs-check-out"><?php
							if ( $is_style_banner2 ) : ?>
								<div class="cs-form-icon">
	                                <svg xmlns="http://www.w3.org/2000/svg" width="30" viewBox="0 0 30 30">
	                                    <path d="M7.11.06c-.22.1-.41.31-.49.52-.04.09-.06.52-.06.95v.78l-1.71.02c-1.68.02-1.73.02-2.17.18C1.45 2.95.6 3.79.17 5L0 5.47v21.42l.16.47c.43 1.21 1.27 2.05 2.5 2.48l.45.16h23.77l.47-.16c1.21-.43 2.05-1.27 2.48-2.5l.16-.45V5.47l-.16-.45c-.43-1.23-1.27-2.07-2.48-2.5-.46-.16-.48-.16-2.18-.18l-1.71-.02-.02-.87C23.42.5 23.39.41 23.02.14c-.23-.17-.78-.17-1.01 0-.38.28-.4.36-.42 1.32l-.02.87H8.44l-.02-.87C8.4.51 8.37.42 8.01.15c-.2-.15-.66-.19-.9-.08Zm-.55 4.78c.02.69.07.83.42 1.09.23.17.78.17 1.01 0 .35-.26.4-.39.42-1.09l.02-.64h13.12l.02.64c.02.69.07.83.42 1.09.23.17.78.17 1.01 0 .35-.26.4-.39.42-1.09l.02-.65 1.59.02 1.6.02.33.16c.42.21.76.55.97.97.16.33.16.34.18 1.7l.02 1.37H1.85V7.18c0-.93.02-1.33.08-1.54.18-.59.69-1.13 1.29-1.33.26-.09.52-.1 1.81-.11h1.5l.02.64Zm21.57 13.63-.02 8.18-.16.32c-.21.42-.57.78-.98.99l-.33.16H3.35l-.32-.16c-.42-.21-.78-.57-.99-.98l-.16-.33-.02-8.18V10.3h26.28v8.17Z"></path>
	                                </svg>
	                            </div><?php
	                        endif; ?>
							<div class="field-wrap">
								<label class="cs-form-label" for="<?php echo $label_prefix . 'checkout' ; ?>"><?php $this->print_unescaped_setting( 'custom_label_check_out' ); ?></label>

								<div class="field-input-wrap checkout-date">
									<input type="text" value="" id="<?php echo $label_prefix . 'checkout' ; ?>" data-value="<?php echo $checkout_date; ?>" class="check-out-date" name="" readonly>
								</div>
							</div>
						</div><?php
					endif;
				endif; 
				$is_style_banner2 ? $this->show_fields_for_banner2( $settings ) : $this->show_fields( $settings );
				do_action( 'loftocean_room_search_form_fields_after' ); ?>

				<div class="cs-form-field cs-submit">
					<div class="field-wrap">
						<button <?php $this->print_render_attribute_string( 'button' ); ?>>
							<span <?php $this->print_render_attribute_string( 'custom_button_text' ); ?>><?php $this->print_unescaped_setting( 'custom_button_text' ); ?></span>
						</button>
					</div>
				</div>
				<input type="hidden" name="search_rooms" value="" />
				<?php do_action( 'loftocean_search_form' ); ?>
			</form>
			<?php do_action( 'loftocean_content_after_room_search_form' ); ?>
		</div><?php
	}
	/**
	* Show form fields for normal style
	*/
	protected function show_fields( $settings ) {
		$label_prefix = $this->label_prefix;
		$search_vars = $this->search_vars;
		if ( 'on' == $settings[ 'item_rooms' ] ) :
			$room_number = 1;
			$room_label = '';
			if ( isset( $search_vars[ 'room-quantity' ] ) && is_numeric( $search_vars[ 'room-quantity' ] ) ) {
				$room_number = $search_vars[ 'room-quantity' ];
			}
			if ( $room_number > 0 ) {
				$room_label = $room_number . ' ' . ( $room_number > 1 ? esc_html__( 'Rooms', 'loftocean' ) : esc_html__( 'Room', 'loftocean' ) );
			} ?>
			<div class="cs-form-field cs-rooms cs-has-dropdown">
				<div class="field-wrap">
					<label class="cs-form-label" for="<?php echo $label_prefix . 'rooms' ; ?>"><?php $this->print_unescaped_setting( 'custom_label_rooms' ); ?></label>

					<div class="field-input-wrap has-dropdown">
						<input type="text" name="" id="<?php echo $label_prefix . 'rooms' ; ?>" value="<?php echo esc_attr( $room_label ); ?>" readonly="">
					</div>

					<div class="csf-dropdown">
						<div class="csf-dropdown-item">
							<label class="cs-form-label"><?php $this->print_unescaped_setting( 'custom_label_rooms' ); ?></label>

							<div class="quantity cs-quantity" data-label="room">
								<label class="screen-reader-text"><?php esc_html_e( 'Rooms quantity', 'loftocean' ); ?></label>
								<button class="minus<?php if ( $room_number < 2 ) : ?> disabled<?php endif; ?>"></button>
								<input type="text" name="room-quantity" value="<?php echo esc_attr( $room_number ); ?>" class="input-text" autocomplete="off" readonly="" data-min="1" data-max="<?php echo $this->room_max_number; ?>">
								<button class="plus<?php if ( $room_number >= $this->room_max_number ) : ?> disabled<?php endif; ?>"></button>
							</div>
						</div>
					</div>
				</div>
			</div><?php
		endif;
		if ( ( 'on' == $settings[ 'item_guests' ] ) && \LoftOcean\is_valid_array( $settings[ 'guests_items' ] ) ) :
			$show_child_only = in_array( 'children', $settings[ 'guests_items' ] ) && ( ! in_array( 'adults', $settings[ 'guests_items' ] ) );
			$input_value = array();
			$show_adults = false;
			$show_children = false;
			$adult_number = 1;
			$child_number = $show_child_only ? 1 : 0;
			$child_min_number = $child_number;
			$adult_label = '';
			$child_label = '';
			if ( in_array( 'adults', $settings[ 'guests_items' ] ) ) {
				$show_adults = true;
				if ( isset( $search_vars[ 'adult-quantity' ] ) && is_numeric( $search_vars[ 'adult-quantity' ] ) ) {
					$adult_number = $search_vars[ 'adult-quantity' ];
				}
				if ( $adult_number > 0 ) {
					$adult_label = $adult_number . ' ' . ( $adult_number == 1 ? esc_html__( 'Adult', 'loftocean' ) : esc_html__( 'Adults', 'loftocean' ) );
				}
				empty( $adult_label ) ? '' : array_push( $input_value, $adult_label );
			}
			if ( in_array( 'children', $settings[ 'guests_items' ] ) ) {
				$show_children = true;
				if ( isset( $search_vars[ 'child-quantity' ] ) && is_numeric( $search_vars[ 'child-quantity' ] ) && ( $search_vars[ 'child-quantity' ] >= $child_min_number ) ) {
					$child_number = $search_vars[ 'child-quantity' ];
				}
				if ( $child_number >= $child_min_number ) {
					if ( apply_filters( 'loftocean_room_use_plural_if_children_number_is_zero', false ) ) {
						$child_label = $child_number . ' ' . ( $child_number == 1 ? esc_html__( 'Child', 'loftocean' ) : esc_html__( 'Children', 'loftocean' ) );
					} else {
						$child_label = $child_number . ' ' . ( $child_number < 2 ? esc_html__( 'Child', 'loftocean' ) : esc_html__( 'Children', 'loftocean' ) );
					}
				}

				empty( $child_label ) ? '' : array_push( $input_value, $child_label );
			}
			if ( ( 'style-block' == $settings[ 'style' ] ) && ( 'on' == $settings[ 'field_guests_separator' ] ) ) :
				$is_inline_label_position = ( 'inline-label' == $settings[ 'label_position' ] );
				$has_adult_age_description = apply_filters( 'loftocean_room_has_adult_age_description', false );
                $has_child_age_description = apply_filters( 'loftocean_room_has_child_age_description', false );
                $this->add_render_attribute( array(
                	'adult_field_wrap' => array( 'class' => array( 'cs-form-field', 'cs-adults', 'cs-has-dropdown' ) ),
                	'child_field_wrap' => array( 'class' => array( 'cs-form-field', 'cs-children', 'cs-has-dropdown' ) ) 
                ) );
                if ( $show_adults && $show_children ) {
                	$this->add_render_attribute( 'adult_field_wrap', 'class', 'form-field-col-1-2' );
                	$this->add_render_attribute( 'child_field_wrap', 'class', 'form-field-col-1-2' );
                }
               	$has_adult_age_description && $is_inline_label_position ? $this->add_render_attribute( 'adult_field_wrap', 'class', 'cs-has-age-text' ) : '';
                $has_child_age_description && $is_inline_label_position ? $this->add_render_attribute( 'child_field_wrap', 'class', 'cs-has-age-text' ) : '';

				if ( $show_adults ) : ?> 
					<div <?php $this->print_render_attribute_string( 'adult_field_wrap' ); ?>>
	                    <div class="field-wrap">
	                        <label class="cs-form-label" for="<?php echo $label_prefix . 'adults' ; ?>"><?php 
	                        	$this->print_unescaped_setting( 'custom_label_adults' );
	                        	do_action( 'loftocean_room_adult_age_description' ); ?>
	                        </label>

	                        <div class="field-input-wrap has-dropdown">
	                            <input type="text" name="" id="<?php echo $label_prefix . 'adults' ; ?>" value="<?php echo esc_attr( $adult_number ); ?>" readonly="" class="separated-guests">
	                        </div>

	                        <div class="csf-dropdown">
	                            <div class="csf-dropdown-item">
	                                <label class="cs-form-label"><?php $this->print_unescaped_setting( 'custom_label_adults' ); ?></label>

	                                <div class="quantity cs-quantity" data-label="adult">
	                                    <label class="screen-reader-text"><?php esc_html_e( 'Adults quantity', 'loftocean' ); ?></label>
	                                    <button class="minus<?php if ( $adult_number < 2 ) : ?> disabled<?php endif; ?>"></button>
	                                    <input type="text" name="adult-quantity" value="<?php echo esc_attr( $adult_number ); ?>" class="input-text" autocomplete="off" readonly="" data-min="1" data-max="<?php echo $this->adult_max_number; ?>">
	                                    <button class="plus<?php if ( $adult_number >= $this->adult_max_number ) : ?> disabled<?php endif; ?>"></button>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                </div><?php
	            endif;
	            if ( $show_children ) : ?>
	                <div <?php $this->print_render_attribute_string( 'child_field_wrap' ); ?>>
	                    <div class="field-wrap">
	                        <label class="cs-form-label" for="<?php echo $label_prefix . 'children' ; ?>"><?php 
	                        	$this->print_unescaped_setting( 'custom_label_children' );
	                        	do_action( 'loftocean_room_child_age_description' ); ?>
	                        </label>

	                        <div class="field-input-wrap has-dropdown">
	                            <input type="text" name="" id="<?php echo $label_prefix . 'children' ; ?>" value="<?php echo esc_attr( $child_number ); ?>" readonly="" class="separated-guests">
	                        </div>

	                        <div class="csf-dropdown">
	                            <div class="csf-dropdown-item">
	                                <label class="cs-form-label"><?php $this->print_unescaped_setting( 'custom_label_children' ); ?></label>

	                                <div class="quantity cs-quantity" data-label="child">
	                                    <label class="screen-reader-text"><?php esc_html_e( 'Children quantity', 'loftocean' ); ?></label>
	                                    <button class="minus<?php if ( $child_number <= $child_min_number ) : ?> disabled<?php endif; ?>"></button>
	                                    <input type="text" name="child-quantity" value="<?php echo esc_attr( $child_number ); ?>" class="input-text" autocomplete="off" readonly="" data-min="<?php echo $child_min_number; ?>" data-max="<?php echo $this->child_max_number; ?>">
	                                    <button class="plus<?php if ( $child_number >= $this->child_max_number ) : ?> disabled<?php endif; ?>"></button>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                </div><?php
	            endif;
			else : ?>
				<div class="cs-form-field cs-guests cs-has-dropdown">
					<div class="field-wrap">
						<label class="cs-form-label" for="<?php echo $label_prefix . 'guests' ; ?>"><?php $this->print_unescaped_setting( 'custom_label_guests' ); ?></label>

						<div class="field-input-wrap has-dropdown">
							<input type="text" name="" id="<?php echo $label_prefix . 'guests' ; ?>" value="<?php echo esc_attr( implode( ', ', $input_value ) ); ?>" readonly="">
						</div>

						<div class="csf-dropdown"><?php
							if ( $show_adults ) : ?>
								<div class="csf-dropdown-item">
									<label class="cs-form-label"><?php 
										$this->print_unescaped_setting( 'custom_label_adults' );
										do_action( 'loftocean_room_adult_age_description' ); ?>
									</label>

									<div class="quantity cs-quantity" data-label="adult">
										<label class="screen-reader-text"><?php esc_html_e( 'Adults quantity', 'loftocean' ); ?></label>
										<button class="minus<?php if ( $adult_number < 2 ) : ?> disabled<?php endif; ?>"></button>
										<input type="text" name="adult-quantity" value="<?php echo esc_attr( $adult_number ); ?>" class="input-text" autocomplete="off" readonly="" data-min="1" data-max="<?php echo $this->adult_max_number; ?>">
										<button class="plus<?php if ( $adult_number >= $this->adult_max_number ) : ?> disabled<?php endif; ?>"></button>
									</div>
								</div><?php
							endif;
							if ( $show_children ) : ?>
								<div class="csf-dropdown-item">
									<label class="cs-form-label"><?php 
										$this->print_unescaped_setting( 'custom_label_children' ); 
										do_action( 'loftocean_room_child_age_description' ); ?>
									</label>

									<div class="quantity cs-quantity" data-label="child">
										<label class="screen-reader-text"><?php esc_html_e( 'Children quantity', 'loftocean' ); ?></label>
										<button class="minus<?php if ( $child_number <= $child_min_number ) : ?> disabled<?php endif; ?>"></button>
										<input type="text" name="child-quantity" value="<?php echo esc_attr( $child_number ); ?>" class="input-text" autocomplete="off" readonly="" data-min="<?php echo $child_min_number; ?>" data-max="<?php echo $this->child_max_number; ?>">
										<button class="plus<?php if ( $child_number >= $this->child_max_number ) : ?> disabled<?php endif; ?>"></button>
									</div>
								</div><?php
							endif; ?>
						</div>
					</div>
				</div><?php
			endif;
		endif;
	}

	/**
	* Show form fields for banner 2 style
	*/
	protected function show_fields_for_banner2( $settings ) {
		$label_prefix = $this->label_prefix;
		$search_vars = $this->search_vars;
		$show_guests = ( 'on' == $settings[ 'item_guests' ] ) && \LoftOcean\is_valid_array( $settings[ 'guests_items' ] );
		$show_room = ( 'on' == $settings[ 'item_rooms' ] );
		if ( $show_room || $show_guests ) :
			$show_child_only = in_array( 'children', $settings[ 'guests_items' ] ) && ( ! in_array( 'adults', $settings[ 'guests_items' ] ) );
			$input_value = array();
			$show_adults = false;
			$show_children = false;
			$room_number = 1;
			$adult_number = 1;
			$child_number = $show_child_only ? 1 : 0;
			$child_min_number = $child_number;
			if ( $show_room ) {
				$room_label = '';
				if ( isset( $search_vars[ 'room-quantity' ] ) && is_numeric( $search_vars[ 'room-quantity' ] ) ) {
					$room_number = $search_vars[ 'room-quantity' ];
				}
				if ( $room_number > 0 ) {
					$room_label = $room_number . ' ' . ( $room_number > 1 ? esc_html__( 'Rooms', 'loftocean' ) : esc_html__( 'Room', 'loftocean' ) );
					array_push( $input_value, $room_label );
				}
			}
			if ( $show_guests ) {
				if ( in_array( 'adults', $settings[ 'guests_items' ] ) ) {
					$show_adults = true;
					if ( isset( $search_vars[ 'adult-quantity' ] ) && is_numeric( $search_vars[ 'adult-quantity' ] ) ) {
						$adult_number = $search_vars[ 'adult-quantity' ];
					}
					if ( $adult_number > 0 ) {
						$adult_label = $adult_number . ' ' . ( $adult_number == 1 ? esc_html__( 'Adult', 'loftocean' ) : esc_html__( 'Adults', 'loftocean' ) );
					}
					empty( $adult_label ) ? '' : array_push( $input_value, $adult_label );
				}
				if ( in_array( 'children', $settings[ 'guests_items' ] ) ) {
					$show_children = true;
					if ( isset( $search_vars[ 'child-quantity' ] ) && is_numeric( $search_vars[ 'child-quantity' ] ) && ( $search_vars[ 'child-quantity' ] >= $child_min_number ) ) {
						$child_number = $search_vars[ 'child-quantity' ];
					}
					if ( $child_number >= $child_min_number ) {
						if ( apply_filters( 'loftocean_room_use_plural_if_children_number_is_zero', false ) ) {
							$child_label = $child_number . ' ' . ( $child_number == 1 ? esc_html__( 'Child', 'loftocean' ) : esc_html__( 'Children', 'loftocean' ) );
						} else {
							$child_label = $child_number . ' ' . ( $child_number < 2 ? esc_html__( 'Child', 'loftocean' ) : esc_html__( 'Children', 'loftocean' ) );
						}
					}
					empty( $child_label ) ? '' : array_push( $input_value, $child_label );
				}
			} ?>
			<div class="cs-form-field cs-guests cs-has-dropdown">
				<div class="cs-form-icon">
					<svg xmlns="http://www.w3.org/2000/svg" width="30" viewBox="0 0 30 25">
						<path d="M10.39.25C8.13.72 6.23 2.44 5.54 4.64c-.62 1.95-.32 3.93.84 5.66.37.55 1.05 1.22 1.65 1.63.29.2.45.34.4.36s-.37.12-.73.24c-.81.26-2.12.91-2.81 1.37-2.28 1.54-3.84 3.65-4.52 6.09C.09 21.01-.02 22 0 23.39l.02 1.13.19.17c.26.23.63.24.86.01.08-.08.16-.21.18-.28.01-.07.04-.7.05-1.41.05-1.98.27-2.96.98-4.38.42-.84.91-1.49 1.75-2.34.92-.93 1.64-1.45 2.71-1.98 1.66-.81 3.13-1.15 5.02-1.14 1.45 0 2.41.17 3.76.64 2.71.94 4.99 3.04 5.98 5.53.47 1.18.63 2.1.63 3.65 0 .63.02 1.24.04 1.34.13.67 1.07.73 1.25.08.07-.27.08-1.51 0-2.46-.11-1.37-.39-2.41-.96-3.63-1.21-2.52-3.36-4.49-6.1-5.58-.32-.13-.7-.26-.84-.3-.6-.16-.6-.15-.18-.43 1.11-.74 2.06-1.95 2.51-3.19a6.47 6.47 0 0 0-1.37-6.62 6.69 6.69 0 0 0-3.6-1.98c-.65-.12-1.85-.1-2.5.04Zm2.62 1.31c1.99.55 3.43 2.09 3.83 4.07.12.6.09 1.77-.06 2.32-.12.45-.54 1.32-.81 1.7a5.287 5.287 0 0 1-3.01 2.03c-.7.18-1.83.18-2.5 0-2.5-.66-4.18-3.07-3.9-5.59.27-2.36 1.98-4.2 4.32-4.62.41-.08 1.75-.02 2.13.08Z"></path>
						<path d="M20.58 2.8c-.27.13-.4.45-.32.73.1.33.28.44.81.49.67.06.93.13 1.54.43.78.38 1.34.93 1.72 1.71.35.7.47 1.26.43 1.96a3.907 3.907 0 0 1-3.46 3.67c-.69.08-.9.16-1.01.44-.07.18-.08.26-.02.44.1.34.3.43 1.01.49 2.78.19 5.05 1.49 6.39 3.67.73 1.18 1.03 2.4 1.03 4.23 0 .67.01.77.12.93.27.39.9.33 1.08-.09.06-.15.09-.45.09-1.13 0-1.55-.22-2.63-.8-3.82-.72-1.51-1.78-2.71-3.22-3.63-.46-.3-1.42-.77-1.89-.91-.17-.06-.31-.13-.31-.16s.02-.05.05-.05c.11 0 1.01-.9 1.27-1.25a5.213 5.213 0 0 0 0-6.05c-.36-.5-1.11-1.18-1.63-1.48-.99-.56-2.4-.87-2.9-.62Z"></path>
					</svg>
				</div>
				<div class="field-wrap">
					<label class="cs-form-label" for="<?php echo $label_prefix . 'guests' ; ?>"><?php $this->print_unescaped_setting( 'custom_label_guests' ); ?></label>

					<div class="field-input-wrap has-dropdown">
						<input type="text" name="guests" id="<?php echo $label_prefix . 'guests' ; ?>" value="<?php echo esc_attr( implode( ', ', $input_value ) ); ?>" readonly="">
					</div>

					<div class="csf-dropdown"><?php
						if ( $show_room ) : ?>
							<div class="csf-dropdown-item">
					    		<label class="cs-form-label"><?php echo $this->print_unescaped_setting( 'custom_label_rooms' ); ?></label>

					    		<div class="quantity cs-quantity" data-label="room">
					        		<label class="screen-reader-text"><?php esc_html_e( 'Rooms quantity', 'loftocean' ); ?></label>
					        		<button class="minus<?php if ( $room_number < 2 ) : ?> disabled<?php endif; ?>"></button>
					        		<input type="text" name="room-quantity" value="<?php echo esc_attr( $room_number ); ?>" class="input-text" autocomplete="off" readonly="" data-min="1" data-max="<?php echo $this->room_max_number; ?>">
					        		<button class="plus<?php if ( $room_number >= $this->room_max_number ) : ?> disabled<?php endif; ?>"></button>
					    		</div>
							</div><?php
						endif;
						if ( $show_adults ) : ?>
							<div class="csf-dropdown-item">
					   			<label class="cs-form-label"><?php 
					   				$this->print_unescaped_setting( 'custom_label_adults' );
					        		do_action( 'loftocean_room_adult_age_description' ); ?>
					        	</label>

					    		<div class="quantity cs-quantity" data-label="adult">
					        		<label class="screen-reader-text"><?php esc_html_e( 'Adults quantity', 'loftocean' ); ?></label>
					       			<button class="minus<?php if ( $adult_number < 2 ) : ?> disabled<?php endif; ?>"></button>
					        		<input type="text" name="adult-quantity" value="<?php echo esc_attr( $adult_number ); ?>" class="input-text" autocomplete="off" readonly="" data-min="1" data-max="<?php echo $this->adult_max_number; ?>">
					        		<button class="plus<?php if ( $adult_number >= $this->adult_max_number ) : ?> disabled<?php endif; ?>"></button>
					    		</div>
							</div><?php
						endif;
						if ( $show_children ) : ?>
							<div class="csf-dropdown-item">
								<label class="cs-form-label"><?php 
									$this->print_unescaped_setting( 'custom_label_children' );
									do_action( 'loftocean_room_child_age_description' ); ?>
								</label>

								<div class="quantity cs-quantity" data-label="child">
									<label class="screen-reader-text"><?php esc_html_e( 'Children quantity', 'loftocean' ); ?></label>
									<button class="minus<?php if ( $child_number <= $child_min_number ) : ?> disabled<?php endif; ?>"></button>
									<input type="text" name="child-quantity" value="<?php echo esc_attr( $child_number ); ?>" class="input-text" autocomplete="off" readonly="" data-min="<?php echo $child_min_number; ?>" data-max="<?php echo $this->child_max_number; ?>">
									<button class="plus<?php if ( $child_number >= $this->child_max_number ) : ?> disabled<?php endif; ?>"></button>
								</div>
							</div><?php
						endif; ?>
					</div>
				</div>
			</div><?php
		endif;
	}
}
