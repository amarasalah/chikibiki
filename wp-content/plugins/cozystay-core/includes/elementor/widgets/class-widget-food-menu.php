<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Food Menu
 */
class Widget_Food_Menu extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceanfoodmenu', array( 'id' => 'food-menu' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Food Menu', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-bullet-list';
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
		return [ 'food', 'food menu' ];
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
		$repeater->add_control( 'title',array(
			'label' => esc_html__( 'Title', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXTAREA,
			'label_block' => true,
			'placeholder' => esc_html__( 'Menu Item Title', 'loftocean' ),
			'default' => esc_html__( 'Purple Corn Tostada', 'loftocean' )
		) );
		$repeater->add_control( 'price',array(
			'label' => esc_html__( 'Price', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'label_block' => true,
			'placeholder' => esc_html__( '$36', 'loftocean' ),
			'default' => esc_html__( '$36', 'loftocean' )
		) );
		$repeater->add_control( 'price2',array(
			'label' => esc_html__( 'Price (optional)', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'label_block' => true,
			'placeholder' => esc_html__( '', 'loftocean' ),
			'default' => ''
		) );
		$repeater->add_control( 'details',array(
			'label' => esc_html__( 'Details', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXTAREA,
			'label_block' => true,
			'default' => esc_html__( 'Ricotta, goat cheese, beetroot and datterini.', 'loftocean' )
		) );
        $repeater->add_control( 'image', array(
            'label' => esc_html__( 'Image', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::MEDIA
        ) );
		$repeater->add_control( 'image_link_to', array(
			'label' => esc_html__( 'Image Link', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'none',
			'options' => array(
				'none' => esc_html__( 'None', 'loftocean' ),
				'file' => esc_html__( 'Media File', 'loftocean' ),
				'custom' => esc_html__( 'Custom URL', 'loftocean' )
			)
		) );
		$repeater->add_control( 'image_link', array(
			'label' => esc_html__( 'Link', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::URL,
			'placeholder' => esc_html__( 'https://your-link.com', 'loftocean' ),
			'condition' => array( 'image_link_to' => 'custom' ),
			'show_label' => false
		) );
		$repeater->add_control( 'image_open_lightbox', array(
			'label' => esc_html__( 'Lightbox', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'default',
			'options' => array(
				'default' => esc_html__( 'Default', 'loftocean' ),
				'yes' => esc_html__( 'Yes','loftocean' ),
				'no' => esc_html__( 'No', 'loftocean' )
			),
			'condition' => array( 'image_link_to' => 'file' )
		) );
        $repeater->add_group_control( \Elementor\Group_Control_Image_Size::get_type(), array(
			'name' => 'image',
			'default' => 'thumbnail',
			'separator' => 'none',
		) );
        $repeater->add_control( 'label',array(
			'label' => esc_html__( 'Label (Optional)', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'label_block' => true,
			'default' => ''
		) );
		$repeater->add_control( 'label_colors',array(
			'label' => esc_html__( 'Label Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '',
            'options' => array(
				'' => esc_html__( 'Inherit', 'loftocean' ),
				'custom' => esc_html__( 'Custom', 'loftocean' ),
			)
		) );
		$repeater->add_control( 'label_background_color', array(
            'label' => esc_html__( 'Label Background Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'condition' => array( 'label_colors[value]' => 'custom' ),
            'default' => ''
        ) );
		$repeater->add_control( 'label_text_color', array(
            'label' => esc_html__( 'Label Text Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'condition' => array( 'label_colors[value]' => 'custom' ),
            'default' => ''
        ) );
		$repeater->add_control( 'link', array(
			'label' => esc_html__( 'Link', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::URL,
			'placeholder' => esc_html__( 'https://your-link.com', 'loftocean' ),
		) );
		$this->add_control( 'menu_list', array(
			'label' => esc_html__( 'Menu Items', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::REPEATER,
			'fields' => $repeater->get_controls(),
			'default' => array(
                array(
    				'title' => esc_html__( 'Purple Corn Tostada', 'loftocean' ),
                    'price' => '$36',
    				'details' => esc_html__( 'Ricotta, goat cheese, beetroot and datterini.', 'loftocean' )
    			), array(
    				'title' => esc_html__( 'Purple Corn Tostada', 'loftocean' ),
                    'price' => '$36',
    				'details' => esc_html__( 'Ricotta, goat cheese, beetroot and datterini.', 'loftocean' )
                )
            ),
            'title_field' => '{{{ title }}}',
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'style_section', array(
            'label' => __( 'Style', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'style', array(
            'label'	=> esc_html__( 'Style', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'food-menu-style-1',
            'options' => array(
				'food-menu-style-1' => esc_html__( 'Style 1', 'loftocean' ),
				'food-menu-style-2' => esc_html__( 'Style 2', 'loftocean' ),
				'food-menu-style-3' => esc_html__( 'Style 3', 'loftocean' ),
				'food-menu-style-4' => esc_html__( 'Style 4', 'loftocean' ),
				'food-menu-style-5' => esc_html__( 'Style 5', 'loftocean' ),
				'food-menu-style-6' => esc_html__( 'Style 6', 'loftocean' )
			)
		) );
        $this->add_control( 'show_line_on_mobile', array(
            'label' => esc_html__( 'Still Show Lines for Mobile Devices', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'condition' => array( 'style[value]' => array( 'food-menu-style-2', 'food-menu-style-3', 'food-menu-style-4' ) ),
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'columns', array(
            'label'	=> esc_html__( 'Column', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'column-3',
            'options' => array(
				'column-2' => esc_html__( '2 Columns', 'loftocean' ),
				'column-3' => esc_html__( '3 Columns', 'loftocean' ),
				'column-4' => esc_html__( '4 Columns', 'loftocean' ),
			),
            'condition' => array( 'style[value]' => 'food-menu-style-5' )
		) );
        $this->add_control( 'gap', array(
            'label'	=> esc_html__( 'Gap', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'gap-20',
            'options' => array(
				'gap-10' => esc_html__( 'Small', 'loftocean' ),
				'gap-20' => esc_html__( 'Medium', 'loftocean' ),
				'gap-32' => esc_html__( 'Large', 'loftocean' ),
			),
            'condition' => array( 'style[value]' => 'food-menu-style-5' )
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'label_section', array(
            'label' => __( 'Multi-Price Label', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => array( 'style[value]!' => 'food-menu-style-5' )
        ) );
        $this->add_control( 'price_label1', array(
			'type'		=> \Elementor\Controls_Manager::TEXT,
			'label'		=> esc_html__( 'Label 1', 'loftocean' ),
			'default'	=> ''
		) );
        $this->add_control( 'price_label2', array(
			'type'		=> \Elementor\Controls_Manager::TEXT,
			'label'		=> esc_html__( 'Label 2', 'loftocean' ),
			'default'	=> ''
		) );
		$this->add_control( 'multi_price_label_background_color', array(
			'label' => esc_html__( 'Background Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
			'selectors' => array( '{{WRAPPER}} .cs-food-menu-group span' => 'background: {{VALUE}};' )
		) );
		$this->add_control( 'multi_price_label_text_color', array(
			'label' => esc_html__( 'Text Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
			'selectors' => array( '{{WRAPPER}} .cs-food-menu-group span' => 'color: {{VALUE}};' )
		) );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
			'name'     => 'multi_price_label_typography',
			'label'    => esc_html__( 'Typography', 'loftocean' ),
			'selector' => '{{WRAPPER}} .cs-food-menu-group span',
		) );
		$this->add_control( 'multi_price_label_border_type', array(
            'label'	=> esc_html__( 'Border Type', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'solid',
			'selectors' => array( '{{WRAPPER}} .cs-food-menu-group span' => 'border-style: {{VALUE}};' ),
            'options' => array(
				'none' => esc_html__( 'None', 'loftocean' ),
				'solid' => esc_html__( 'Solid', 'loftocean' ),
				'double' => esc_html__( 'Double', 'loftocean' ),
				'dotted' => esc_html__( 'Dotted', 'loftocean' ),
				'dashed' => esc_html__( 'Dashed', 'loftocean' ),
				'groove' => esc_html__( 'Groove', 'loftocean' )
			)
		) );
		$this->add_responsive_control( 'multi_price_label_border_width', array(
			'label' => esc_html__( 'Border Width', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px' ),
			'selectors' => array( '{{WRAPPER}} .cs-food-menu-group span' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			'condition' => array( 'multi_price_label_border_type[value]!' => 'none' )
		) );
		$this->add_control( 'multi_price_label_border_color', array(
            'label' => esc_html__( 'Border Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'condition' => array( 'multi_price_label_border_type[value]!' => 'none' ),
            'selectors' => array( '{{WRAPPER}} .cs-food-menu-group span' => 'border-color: {{VALUE}};' )
        ) );
        $this->end_controls_section();

        $this->start_controls_section( 'title_style_section', array(
            'label' => __( 'Title', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'title_color', array(
            'label' => esc_html__( 'Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .cs-food-menu-title' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .cs-food-menu-title',
            )
        );
        $this->end_controls_section();

        $this->start_controls_section( 'details_style_section', array(
            'label' => __( 'Details', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'details_color', array(
            'label' => esc_html__( 'Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .cs-food-menu-details' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'details_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .cs-food-menu-details',
            )
        );
        $this->end_controls_section();

        $this->start_controls_section( 'price_style_section', array(
            'label' => __( 'Price', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'price_color', array(
            'label' => esc_html__( 'Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .cs-food-menu-price' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'price_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .cs-food-menu-price',
            )
        );
        $this->end_controls_section();

        $this->start_controls_section( 'label_style_section', array(
            'label' => __( 'Label', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'label_background_color', array(
            'label' => esc_html__( 'Background Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .menu-label' => 'background-color: {{VALUE}};',
            )
        ) );
        $this->add_control( 'label_color', array(
            'label' => esc_html__( 'Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .menu-label' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'label_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .menu-label',
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
        $has_price_label = false;

        $this->add_render_attribute( array(
            'wrapper' => array( 'class' => array( 'cs-food-menu', $settings[ 'style' ] ) )
        ) );
        if ( 'food-menu-style-5' == $settings[ 'style' ] ) {
            $this->add_render_attribute( 'wrapper', 'class', array( $settings[ 'columns' ], $settings[ 'gap' ] ) );
        } else {
			if ( in_array( $settings[ 'style' ], array( 'food-menu-style-2', 'food-menu-style-3', 'food-menu-style-4' ) ) ) {
				'on' == $settings[ 'show_line_on_mobile' ] ? $this->add_render_attribute( 'wrapper', 'class', 'lines-on-mobile' ) : '';
			}
			$has_price_label = ! empty( $settings[ 'price_label1' ] ) || ! empty( $settings[ 'price_label2' ] );
        }

        if ( \LoftOcean\is_valid_array( $settings['menu_list' ] ) ) : ?>
            <div <?php $this->print_render_attribute_string( 'wrapper' ); ?>><?php
            if ( $has_price_label ) : ?>
                <div class="cs-food-menu-group"><?php
				$labels = array( 'price_label1', 'price_label2' );
				foreach ( $labels as $label ) :
					$this->add_inline_editing_attributes( $label, 'none' ); ?>
					<span <?php $this->print_render_attribute_string( $label ); ?>><?php $this->print_unescaped_setting( $label ); ?></span><?php
				endforeach; ?>
				</div><?php
            endif;
            foreach ( $settings[ 'menu_list' ] as $index => $menu ) :
				$title_key = 'menu_list.' . $index . '.title';
				$label_key = 'menu_list.' . $index . '.label';
				$p1_key = 'menu_list.' . $index . '.price';
				$p2_key = 'menu_list.' . $index . '.price2';
				$detail_key = 'menu_list.' . $index . '.details'; ?>
                <div class="cs-food-menu-item"><?php
                    if ( ! empty( $menu[ 'image' ][ 'id' ] ) ) :
						$image_link_key = 'menu_list.' . $index . '.link';
						$image_link = $this->get_link_url( $menu );

						if ( $image_link ) {
							$this->add_link_attributes( $image_link_key, $image_link );

							if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
								$this->add_render_attribute( $image_link_key, array( 'class' => 'elementor-clickable' ) );
							}

							if ( 'custom' !== $menu['image_link_to'] ) {
								$this->add_lightbox_data_attributes( $image_link_key, $menu['image']['id'], $menu['image_open_lightbox'] );
							}
						} ?>
                        <div class="cs-food-menu-img">
							<?php if ( $image_link ) : ?><a <?php $this->print_render_attribute_string( $image_link_key ); ?> aria-label="<?php esc_attr_e( 'Food Menu Image', 'loftocean' ); ?>"><?php endif; ?>
                            <?php \Elementor\Group_Control_Image_Size::print_attachment_image_html( $menu, 'image', 'image' ); ?>
							<?php if ( $image_link ) : ?></a><?php endif; ?>
                        </div><?php
                    endif; ?>
                    <div class="cs-food-menu-main">
                        <div class="cs-food-menu-header"><?php
							$has_link = false;
							$this->add_render_attribute( $title_key, 'class', 'title-wrap' );
							$this->add_inline_editing_attributes( $title_key, 'none' );
							$this->add_render_attribute( $label_key, 'class', 'menu-label' );
							$this->add_inline_editing_attributes( $label_key, 'none' ); ?>
                            <h6 class="cs-food-menu-title"><?php
                                if ( ! empty( $menu[ 'link' ][ 'url' ] ) ) {
									$has_link = true;
									$linkKey = 'link_' . $index;
                                    $this->add_link_attributes( $linkKey, $menu[ 'link' ] ); ?>
                                    <a <?php $this->print_render_attribute_string( $linkKey ); ?>><?php
                                } ?>
                                <span <?php $this->print_render_attribute_string( $title_key ); ?>><?php
									echo wp_kses( $menu[ 'title' ], array(
					                    'br' => array( 'class' => 1 ),
										'span' => array( 'style' => 1, 'class' => 1 ),
					                    'em' => array( 'class' => 1 ),
					                    'strong' => array( 'class' => 1 ),
					                    'small' => array( 'class' => 1 ),
					                    'mark' => array( 'class' => 1 )
					                ) ); ?>
								</span><?php
                                if ( $has_link ) : ?></a><?php endif;
                                if ( ! empty( $menu[ 'label' ] ) ) :
									if ( 'custom' == $menu[ 'label_colors' ] ) {
										empty( $menu[ 'label_background_color' ] ) ? '' : $this->add_render_attribute( $label_key, 'style', 'background: ' . $menu[ 'label_background_color' ] . ';' );
										empty( $menu[ 'label_text_color' ] ) ? '' : $this->add_render_attribute( $label_key, 'style', 'color: ' . $menu[ 'label_text_color' ] . ';' );
									} ?>
									<span <?php $this->print_render_attribute_string( $label_key ); ?>><?php $this->print_unescaped_setting( 'label', 'menu_list', $index ); ?></span><?php
								endif; ?>
                            </h6>
                            <div class="cs-food-menu-lines"></div><?php
							$this->add_render_attribute( $p1_key, 'class', 'cs-food-menu-price' );
							$this->add_inline_editing_attributes( $p1_key, 'none' );
							$this->add_render_attribute( $p2_key, 'class', 'cs-food-menu-price' );
							$this->add_inline_editing_attributes( $p2_key, 'none' );
                            if ( ! empty( $menu[ 'price' ] ) ) : ?><span <?php $this->print_render_attribute_string( $p1_key ); ?>><?php $this->print_unescaped_setting( 'price', 'menu_list', $index ); ?></span><?php endif;
                            if ( ! empty( $menu[ 'price2' ] ) ) : ?><span <?php $this->print_render_attribute_string( $p2_key ); ?>><?php $this->print_unescaped_setting( 'price2', 'menu_list', $index ); ?></span><?php endif; ?>
                        </div><?php
                        if ( ! empty( $menu[ 'details' ] ) ) :
							$this->add_inline_editing_attributes( $detail_key, 'none' );
							$this->add_render_attribute( $detail_key, 'class', 'cs-food-menu-details' ); ?>
                            <p <?php $this->print_render_attribute_string( $detail_key ); ?>><?php $this->print_unescaped_setting( 'details', 'menu_list', $index ); ?></p><?php
                        endif; ?>
                    </div>
                </div><?php
            endforeach; ?>
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
        view.addRenderAttribute( 'wrapper', 'class', 'cs-food-menu ' + settings[ 'style' ] );
        var hasPriceLabel = false, $sanitizeWrap = jQuery( '<div>' ), allowedTags = [ 'BR', 'EM', 'STRONG', 'SMALL', 'MARK', 'SPAN' ];
        if ( 'food-menu-style-5' == settings[ 'style' ] ) {
            view.addRenderAttribute( 'wrapper', 'class', [ settings[ 'columns' ], settings[ 'gap' ] ] );
        } else {
			if ( [ 'food-menu-style-2', 'food-menu-style-3', 'food-menu-style-4' ].includes( settings[ 'style' ] ) ) {
				'on' == settings[ 'show_line_on_mobile' ] ? view.addRenderAttribute( 'wrapper', 'class', 'lines-on-mobile' ) : '';
			}
			hasPriceLabel = settings[ 'price_label1' ] || settings[ 'price_label2' ];
        }

        if ( settings[ 'menu_list' ] ) { #>
            <div {{{ view.getRenderAttributeString( 'wrapper' ) }}}><#
            if ( hasPriceLabel ) { #>
                <div class="cs-food-menu-group"><#
				[ 'price_label1', 'price_label2' ].forEach( function( label, index ) {
					view.addInlineEditingAttributes( label, 'none' ); #>
					<span {{{ view.getRenderAttributeString( label ) }}}>{{{ settings[ label ] }}}</span><#
				} ); #>
				</div><#
            }
            settings[ 'menu_list' ].forEach( function( menu, index ) {
				var titleKey = 'menu_list.' + index + '.title',
					labelKey = 'menu_list.' + index + '.label',
					p1Key = 'menu_list.' + index + '.price',
					p2Key = 'menu_list.' + index + '.price2',
					detailKey = 'menu_list.' + index + '.details',
					menuTitle = menu.title;

				$sanitizeWrap.html( menuTitle ).find( '*' ).each( function() {
		            if ( jQuery( this ).get( 0 ).nodeName && ! allowedTags.includes( jQuery( this ).get( 0 ).nodeName ) ) {
		                jQuery( this ).before( jQuery( this ).text() ).remove();
		            }
		        } );
		        menuTitle = $sanitizeWrap.html(); #>
                <div class="cs-food-menu-item"><#
                    if ( menu.image.url ) {
                        var image = {
            				id: menu.image.id,
            				url: menu.image.url,
            				size: menu.image_size,
            				dimension: menu.image_custom_dimension,
            				model: view.getEditModel()
            			};
            			var imageURL = elementor.imagesManager.getImageUrl( image );
                        if ( imageURL ) {
							var image_link_url = false;

							if ( 'custom' === menu.image_link_to ) {
								image_link_url = menu.image_link.url;
							}

							if ( 'file' === menu.image_link_to ) {
								image_link_url = menu.image.url;
							} #>
                            <div class="cs-food-menu-img">
								<# if ( image_link_url ) { #><a class="elementor-clickable" data-elementor-open-lightbox="{{ menu.image_open_lightbox }}" href="{{ image_link_url }}"><# } #>
                                <img src="{{ imageURL }}">
								<# if ( image_link_url ) { #></a><# } #>
                            </div><#
                        }
                    } #>
                    <div class="cs-food-menu-main">
                        <div class="cs-food-menu-header"><#
							var hasLink = false;
							view.addRenderAttribute( titleKey, 'class', 'title-wrap' );
							view.addInlineEditingAttributes( titleKey, 'none' );
							view.addRenderAttribute( labelKey, 'class', 'menu-label' );
							view.addInlineEditingAttributes( labelKey, 'none' ); #>
                            <h6 class="cs-food-menu-title"><#
                                if ( menu[ 'link' ][ 'url' ] ) {
									hasLink = true; #>
                                    <a href="{{ menu[ 'link' ][ 'url' ] }}"><#
                                } #>
                                <span {{{ view.getRenderAttributeString( titleKey ) }}}>{{{ menuTitle }}}</span><#
                                if ( hasLink ) { #></a><# }
                                if ( menu[ 'label' ] ) {
									if ( 'custom' == menu[ 'label_colors' ] ) {
										menu[ 'label_background_color' ] ? view.addRenderAttribute( labelKey, 'style', 'background: ' + menu[ 'label_background_color' ] + ';' ) : '';
										menu[ 'label_text_color' ] ? view.addRenderAttribute( labelKey, 'style', 'color: ' + menu[ 'label_text_color' ] + ';' ) : '';
									} #>
									<span {{{ view.getRenderAttributeString( labelKey ) }}}>{{{ menu.label }}}</span><#
								} #>
                            </h6>
                            <div class="cs-food-menu-lines"></div><#
							view.addRenderAttribute( p1Key, 'class', 'cs-food-menu-price' );
							view.addInlineEditingAttributes( p1Key, 'none' );
							view.addRenderAttribute( p2Key, 'class', 'cs-food-menu-price' );
							view.addInlineEditingAttributes( p2Key, 'none' );
                            if ( menu[ 'price' ] ) { #><span {{{ view.getRenderAttributeString( p1Key ) }}}>{{{ menu.price }}}</span><# }
                            if ( menu[ 'price2' ] ) { #><span {{{ view.getRenderAttributeString( p2Key ) }}}>{{{ menu.price2 }}}</span><# } #>
                        </div><#
                        if ( menu[ 'details' ] ) {
							view.addInlineEditingAttributes( detailKey, 'none' );
							view.addRenderAttribute( detailKey, 'class', 'cs-food-menu-details' ); #>
                            <p {{{ view.getRenderAttributeString( detailKey ) }}}>{{{ menu.details }}}</p><#
                        } #>
                    </div>
                </div><#
            } ); #>
            </div><#
        } #><?php
	}
	/**
	* Retrieve image widget link URL.
	* @return array|string|false An array/string containing the link URL, or false if no link.
	*/
	private function get_link_url( $settings ) {
		if ( 'none' === $settings['image_link_to'] ) {
			return false;
		}

		if ( 'custom' === $settings['image_link_to'] ) {
			if ( empty( $settings['image_link']['url'] ) ) {
				return false;
			}

			return $settings['image_link'];
		}

		return array(
			'url' => $settings['image']['url']
		);
	}
}
