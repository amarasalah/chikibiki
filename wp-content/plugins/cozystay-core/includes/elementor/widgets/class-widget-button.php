<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Button.
 */
class Widget_Button extends \LoftOcean\Elementor_Widget_Base {
	/**
	* Popups
	*/
	public static $popups = array();
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceanbutton', array( 'id' => 'button' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Button', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-button';
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
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'button' ];
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
		$this->add_control( 'text', array(
			'type'		=> \Elementor\Controls_Manager::TEXT,
			'label'		=> esc_html__( 'Text', 'loftocean' ),
			'default'	=> esc_html__( 'Discover More', 'loftocean' )
		) );
		$this->add_control( 'link', array(
			'type' => \Elementor\Controls_Manager::URL,
			'default' => array( 'url' => '#' ),
			'label' => esc_html__( 'Link', 'loftocean' ),
            'placeholder' => __( 'Enter the URL', 'loftocean' ),
		) );
		$this->add_responsive_control( 'alignment', array(
            'label'	=> esc_html__( 'Alignment', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => array(
				'left' => array(
					'title' => esc_html__( 'Left', 'loftocean' ),
					'icon' => 'eicon-text-align-left'
				),
				'center' => array(
					'title' => esc_html__( 'Center', 'loftocean' ),
					'icon' => 'eicon-text-align-center',
				),
				'right' => array(
					'title' => esc_html__( 'Right', 'loftocean' ),
					'icon' => 'eicon-text-align-right',
				),
                'justify' => array(
					'title' => esc_html__( 'Justified', 'loftocean' ),
					'icon' => 'eicon-text-align-justify',
				)
			),
			'prefix_class' => 'elementor%s-align-',
			'default' => '',
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'popup_content_section', array(
			'label' => __( 'Popup Box', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		) );
		$this->add_control( 'enable_popup', array(
			'label' => esc_html__( 'Enable Popup', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'off',
			'label_on' => 'on',
			'label_off' => 'off',
			'return_value' => 'on'
		) );
		$this->add_control( 'popup_box_custom_block', array(
			'label'	=> esc_html__( 'Content Inside The Popup Box', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'condition' => array( 'enable_popup[value]' => 'on' ),
			'default' => '0',
			'options' => $this->get_custom_block()
		) );
        $this->add_control( 'popup_box_box_size', array(
			'label' => esc_html__( 'Box Size', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'condition' => array( 'enable_popup[value]' => 'on' ),
			'default' => 'fullscreen',
			'options' => array(
				'fullscreen' => esc_html__( 'Fullscreen', 'loftocean' ),
				'custom' => esc_html__( 'Custom', 'loftocean' )
			)
		) );
		$this->add_responsive_control( 'popup_box_custom_width', array(
			'label' => esc_html__( 'Custom Width', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::NUMBER,
			'condition' => array(
				'enable_popup[value]' => 'on',
				'popup_box_box_size[value]' => 'custom'
			),
			'default' => '600',
			'selectors' => array( '.cs-button-popup-{{ID}}' => '--popup-width: {{VALUE}}px;' )
		) );
		$this->add_control( 'popup_box_background_color', array(
            'label' => esc_html__( 'Background Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
			'selectors' => array( '.cs-button-popup-{{ID}}' => 'background-color: {{VALUE}};' ),
            'default' => '',
            'condition' => array( 'enable_popup[value]' => 'on' )
        ) );
		$this->add_control( 'popup_box_background_image', array(
			'label' => esc_html__( 'Background Image', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::MEDIA,
            'condition' => array( 'enable_popup[value]' => 'on' )
		) );
		$this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), array(
			'name' => 'popup_box_shadow',
			'selector' => '.cs-button-popup-{{ID}}',
			'condition' => array( 'enable_popup[value]' => 'on' )
		) );
		$this->add_control( 'close_manually', array(
			'label' => esc_html__( 'Close by clicking "X" button', 'loftocean' ),
            'condition' => array( 'enable_popup[value]' => 'on' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'off',
			'label_on' => 'on',
			'label_off' => 'off',
			'return_value' => 'on'
		) );
		$this->add_control( 'popup_box_preview', array(
			'text' => esc_html__( 'Preview Popup Box', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::BUTTON,
            'condition' => array( 'enable_popup[value]' => 'on' )
		) );
		$this->end_controls_section();

        $this->start_controls_section( 'general_style_section', array(
			'label' => __( 'General', 'loftocean' ),
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
				'{{WRAPPER}} .button' => '--btn-bg: {{VALUE}};',
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
				'{{WRAPPER}} .button' => '--btn-color: {{VALUE}};',
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
				'{{WRAPPER}} .button' => 'color: {{VALUE}};',
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
                '{{WRAPPER}} .button' => '--btn-bg-hover: {{VALUE}};',
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
                '{{WRAPPER}} .button' => '--btn-color-hover: {{VALUE}};',
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
                '{{WRAPPER}} .button:hover' => 'color: {{VALUE}};',
            )
        ) );
        $this->end_controls_tab();
    	$this->end_controls_tabs();

        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'label'    => esc_html__( 'Typography', 'loftocean' ),
				'selector' => '{{WRAPPER}} .button',
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

		$this->start_controls_section( 'close_button_style_section', array(
			'label' => __( 'Close Button Styles', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			'condition' => array( 'enable_popup[value]' => 'on' )
		) );
		$this->add_control( 'close_btn_background_color', array(
			'label' => esc_html__( 'Background Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
			'condition' => array( 'enable_popup[value]' => 'on' ),
			'selectors' => array(
				'.cs-button-popup-{{ID}} .close-button' => 'background: {{VALUE}};',
			)
		) );
		$this->add_control( 'close_btn_text_color', array(
            'label' => esc_html__( 'Button Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'condition' => array( 'enable_popup[value]' => 'on' ),
            'selectors' => array(
                '.cs-button-popup-{{ID}} .close-button' => 'color: {{VALUE}};',
            )
        ) );
		$this->add_control( 'close_btn_border_radius', array(
			'label' => esc_html__( 'Border Radius', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%', 'em' ),
			'selectors' => array( '.cs-button-popup-{{ID}} .close-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			'condition' => array( 'enable_popup[value]' => 'on' )
		) );
		$this->end_controls_section();
	}
    /**
    * Helper function to print the custom block content
    */
    protected static function print_custom_block( $block ) {
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
		$show_error_message =  false;
		$has_popup_enabled = false;
		$current_popup_hash = false;

		if ( empty( $settings [ 'text' ] ) ) {
			return ;
		}

		if ( 'on' == $settings[ 'enable_popup' ] ) {
			if ( ( ! empty( $settings[ 'popup_box_custom_block' ] ) ) && ( false !== get_post_status( $settings[ 'popup_box_custom_block' ] ) ) ) :
				$has_popup_enabled = true;
				if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) :
					$this->add_render_attribute( 'popup_wrapper', 'class', array(  'cs-button-popup', 'cs-popup', 'hide', 'cs-popup-box', 'cs-button-popup-' . $this->get_id() ) );
					$styles = array();
					if ( 'fullscreen' == $settings[ 'popup_box_box_size' ] ) {
						$this->add_render_attribute( 'popup_wrapper', 'class', 'cs-popup-fullsize' );
					} ?>

					<div <?php $this->print_render_attribute_string( 'popup_wrapper' ); ?>>
						<?php if ( ! empty( $settings[ 'popup_box_background_image' ][ 'url' ] ) ) : ?>
							<div class="screen-bg" style="background-image: url(<?php echo esc_url( $settings[ 'popup_box_background_image' ][ 'url' ] ); ?>);"></div><?php
						endif; ?>
						<span class="close-button"><?php esc_html_e( 'Close', 'loftocean' ); ?></span>
						<div class="container"><?php self::print_custom_block( $settings[ 'popup_box_custom_block' ] ); ?></div>
					</div><?php
				else :
					$current_popup_hash = $this->get_popup_box_index( $settings );
					if ( ! in_array( $current_popup_hash, self::$popups ) ) {
						array_push( self::$popups, $current_popup_hash );
						$this->add_render_attribute( 'popup_wrapper', 'class', array( 'cs-button-popup', 'cs-popup', 'hide', 'cs-popup-box', 'cs-button-popup-' . $this->get_id() ) );
						if ( isset( $settings[ 'close_manually' ] ) && ( 'on' == $settings[ 'close_manually' ] ) ) {
							$this->add_render_attribute( 'popup_wrapper', 'class', 'close-manually' );
						}
						$this->add_render_attribute( 'popup_wrapper', 'data-popup-hash', $current_popup_hash );
						if ( 'fullscreen' == $settings[ 'popup_box_box_size' ] ) {
							$this->add_render_attribute( 'popup_wrapper', 'class', 'cs-popup-fullsize' );
						} ?>

						<div <?php $this->print_render_attribute_string( 'popup_wrapper' ); ?>>
							<?php if ( ! empty( $settings[ 'popup_box_background_image' ][ 'url' ] ) ) : ?>
								<div class="screen-bg" style="background-image: url(<?php echo esc_url( $settings[ 'popup_box_background_image' ][ 'url' ] ); ?>);"></div><?php
							endif; ?>
							<span class="close-button"><?php esc_html_e( 'Close', 'loftocean' ); ?></span>
							<div class="container"><?php self::print_custom_block( $settings[ 'popup_box_custom_block' ] ); ?></div>
						</div><?php
					}
				endif;
			else :
				$show_error_message = true;
			endif;
		}

        if ( ! empty( $settings[ 'link' ][ 'url' ] ) ) {
            $this->add_link_attributes( 'button', $settings['link'] );
            $this->add_render_attribute( 'button', 'class', 'elementor-button-link' );
        }

        $this->add_render_attribute( 'button', 'class', 'button' );
        $this->add_render_attribute( 'button', 'role', 'button' );

		if ( false !== $current_popup_hash ) {
			$this->add_render_attribute( 'button', 'data-popup-hash', $current_popup_hash );
		}
        if ( ! empty( $settings[ 'button_style' ] ) ) {
            $this->add_render_attribute( 'button', 'class', $settings['button_style'] );
        }
        if ( ! empty( $settings[ 'button_shape' ] ) && ( 'cs-btn-underline' != $settings[ 'button_style' ] ) ) {
            $this->add_render_attribute( 'button', 'class', $settings['button_shape'] );
        }
        if ( ! empty( $settings[ 'button_size' ] ) ) {
            $this->add_render_attribute( 'button', 'class', $settings[ 'button_size' ] );
        }
        if ( ! empty($settings[ 'button_preset_color' ] ) && ( 'custom' != $settings[ 'button_preset_color' ] ) ) {
            $this->add_render_attribute( 'button', 'class', $settings[ 'button_preset_color' ] );
        }
		if ( $has_popup_enabled ) {
			$this->add_render_attribute( 'button', 'class', 'popup-box-enabled' );
		}

		$has_icon = ! empty( $settings[ 'button_icon' ] );
		$has_icon ? $this->add_render_attribute( 'button', 'class', 'cs-btn-with-icon' ) : '';

        $this->add_render_attribute( 'text', 'class', 'cs-btn-text' );
        $this->add_inline_editing_attributes( 'text', 'none' ); ?>
        <a <?php $this->print_render_attribute_string( 'button' ); ?>>
            <span <?php $this->print_render_attribute_string( 'text' ); ?>><?php $this->print_unescaped_setting( 'text' ); ?></span><?php
            if ( $has_icon ) :
            	$this->add_render_attribute( 'button_icon', 'class', array( 'cs-btn-icon', $settings[ 'button_icon' ] ) );
            	in_array( $settings[ 'button_icon' ], array( 'arrow-2', 'arrow-3' ) ) ? $this->add_render_attribute( 'button_icon', 'class', 'icon-arrow' ) : ''; ?>
            	<span <?php $this->print_render_attribute_string( 'button_icon' ); ?>></span><?php
            endif; ?>
        </a><?php

		if ( $show_error_message && \Elementor\Plugin::$instance->editor->is_edit_mode() ) : ?>
			<div class="cs-notice"><?php esc_html_e( 'Please select a value for "Content Inside The Popup Box" in the widget setting panel.', 'loftocean' ); ?></div><?php
		endif;
	}
	/**
	* Get popup box index
	*/
	protected function get_popup_box_index( $settings ) {
		$bg_settings = array( 'popup_box_custom_block', 'popup_box_background_color', 'popup_box_shadow_box_shadow_type', 'popup_box_shadow_box_shadow_position' );
		$bg_str = 'popupbox';
		foreach( $bg_settings as $setting ) {
			$bg_str .= empty( $settings[ $setting ] ) ? $setting : strtolower( $settings[ $setting ] );
		}
		$bg_str .= isset( $settings[ 'close_manually' ] ) && ( 'on' == $settings[ 'close_manually' ] ) ? 'on' : '';
		$bg_str .= ( 'custom' == $settings[ 'popup_box_box_size' ] ) ? 'custom-width-' . $settings[ 'popup_box_custom_width' ] : 'fullwidth';
		$bg_str .= isset( $settings[ 'popup_box_background_image' ][ 'url' ] ) ?  $settings[ 'popup_box_background_image' ][ 'url' ] : ''; 
		if ( isset( $settings[ 'popup_box_shadow_box_shadow' ] ) ) {
			$box_shadow = $settings[ 'popup_box_shadow_box_shadow' ];
			$box_shadow_settings = array( 'horizontal', 'vertical', 'blur', 'spread', 'color' );
			foreach( $box_shadow_settings as $bss ) {
				if ( isset( $box_shadow[ $bss ] ) ) {
					$bg_str .= strtolower( $box_shadow[ $bss ] );
				}
			}
		}
		return hash( 'md5', $bg_str );
	}
}
