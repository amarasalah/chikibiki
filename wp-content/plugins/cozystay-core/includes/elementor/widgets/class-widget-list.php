<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget List
 */
class Widget_List extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceanlist', array( 'id' => 'list' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'List', 'loftocean' );
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
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'list' ];
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
		$repeater->add_control( 'content',array(
			'label' => esc_html__( 'Content', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXTAREA,
			'label_block' => true,
			'default' => esc_html__( 'Lorem ipsum dolor sit amet', 'loftocean' )
		) );
		$repeater->add_control( 'link', array(
			'type' => \Elementor\Controls_Manager::URL,
			'default' => array( 'url' => '' ),
			'label' => esc_html__( 'Link', 'loftocean' ),
            'placeholder' => __( 'Enter the URL', 'loftocean' ),
		) );
		$this->add_control( 'list', array(
			'label' => esc_html__( 'List Items', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::REPEATER,
			'fields' => $repeater->get_controls(),
			'default' => array(
                array( 'content' => esc_html__( 'Lorem ipsum dolor sit amet', 'loftocean' ) ),
                array( 'content' => esc_html__( 'Lorem ipsum dolor sit amet', 'loftocean' ) ),
                array( 'content' => esc_html__( 'Lorem ipsum dolor sit amet', 'loftocean' ) )
            ),
            'title_field' => '{{{ content }}}',
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'general_style_section', array(
            'label' => __( 'General', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'style', array(
            'label'	=> esc_html__( 'List Style', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'cs-list-type-unordered',
            'options' => array(
				'cs-list-type-unordered' => esc_html__( 'Unordered', 'loftocean' ),
				'cs-list-type-ordered' => esc_html__( 'Ordered', 'loftocean' ),
				'cs-list-type-none' => esc_html__( 'None', 'loftocean' ),
			)
		) );
        $this->add_control( 'list_icon', array(
            'label' => esc_html__( 'Icon', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::ICONS,
            'default' => array(
                'value' => 'fas fa-check-circle',
                'library' => 'fa-solid'
            ),
            'fa4compatibility' => 'icon',
            'condition' => array( 'style[value]' => 'cs-list-type-unordered' )
        ) );
        $this->add_control( 'type', array(
            'label'	=> esc_html__( 'Type', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'type-decimal',
            'options' => array(
				'type-decimal' => esc_html__( 'Decimal', 'loftocean' ),
				'type-decimal-leading-zero' => esc_html__( 'Decimal Leading Zero', 'loftocean' ),
				'type-lower-roman' => esc_html__( 'Lower Roman', 'loftocean' ),
				'type-upper-roman' => esc_html__( 'Upper Roman', 'loftocean' )
			),
            'condition' => array( 'style[value]' => 'cs-list-type-ordered' )
		) );
        $this->add_control( 'border', array(
            'label'	=> esc_html__( 'Border', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'with-border',
            'options' => array(
				'with-border' => esc_html__( 'With Border', 'loftocean' ),
				'' => esc_html__( 'No Border', 'loftocean' )
			)
		) );
		$this->add_responsive_control( 'alignment', array(
			'label' => esc_html__( 'Alignment', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::CHOOSE,
			'options' => array(
				'left' => array(
					'title' => esc_html__( 'Left', 'loftocean' ),
					'icon' => 'eicon-h-align-left',
				),
				'center' => array(
					'title' => esc_html__( 'Center', 'loftocean' ),
					'icon' => 'eicon-h-align-center',
				),
				'right' => array(
					'title' => esc_html__( 'Right', 'loftocean' ),
					'icon' => 'eicon-h-align-right',
				),
			),
			'prefix_class' => 'elementor%s-align-'
		) );
		$this->add_responsive_control( 'space_between', array(
			'label' => esc_html__( 'Space Between', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => array( 'px', 'em', 'rem' ),
			'range' => array(
				'px' => array( 'max' => 50 )
			),
			'selectors' => array( '{{WRAPPER}} .cs-list' => '--list-space: {{SIZE}}{{UNIT}};' )
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'content_style_section', array(
            'label' => __( 'Content', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE
        ) );
        $this->add_control( 'content_preset_color', array(
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
        $this->add_control( 'content_custom_color', array(
            'label' => esc_html__( 'Custom Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'condition' => array( 'content_preset_color[value]' => 'custom' ),
            'selectors' => array(
                '{{WRAPPER}} .list-content' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'content_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .list-content',
            )
        );
        $this->end_controls_section();

        $this->start_controls_section( 'icon_style_section', array(
            'label' => __( 'Icon', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => array( 'style[value]!' => 'cs-list-type-none' )
        ) );
        $this->add_control( 'space', array(
			'label' => esc_html__( 'Space Between', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::NUMBER,
			'default' => '20',
			'selectors' => array(
                '{{WRAPPER}} .cs-list .list-icon' => 'margin-right: {{VALUE}}px;',
            )
		) );
        $this->add_control( 'icon_preset_color', array(
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
        $this->add_control( 'icon_custom_color', array(
            'label' => esc_html__( 'Custom Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'condition' => array( 'icon_preset_color[value]' => 'custom' ),
            'selectors' => array(
                '{{WRAPPER}} .list-icon' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'icon_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
				'condition' => array( 'style[value]!' => 'cs-list-type-unordered' ),
                'selector' => '{{WRAPPER}} .list-icon',
            )
        );
		$this->add_control( 'icon_size', array(
			'label' => esc_html__( 'Icon Size', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => array( 'px' => array( 'max' => 100, 'step' => 1, 'min' => 1 ) ),
			'render_type' => 'ui',
			'separator' => 'before',
			'selectors' => array( '{{WRAPPER}} .list-icon' => 'font-size: {{SIZE}}{{UNIT}};' ),
			'condition' => array( 'style[value]' => 'cs-list-type-unordered' )
		) );
        $this->add_control( 'icon_vertical_alignment', array(
			'label' => esc_html__( 'Vertical Align', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'selectors' => array( '{{WRAPPER}} .cs-list li' => 'align-items: {{VALUE}};' ),
			'condition' => array( 'style[value]' => 'cs-list-type-unordered' ),
			'default' => '',
			'options' => array(
				'flex-start' => esc_html__( 'Top', 'loftocean' ),
                '' => esc_html__( 'Middle', 'loftocean' ),
				'flex-end' => esc_html__( 'Bottom', 'loftocean' )
			)
		) );
		$this->add_responsive_control( 'icon_adjust_vertical_position', array(
			'label' => esc_html__( 'Adjust Vertical Position (px)', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'condition' => array( 'style[value]' => 'cs-list-type-unordered' ),
			'range' => array(
				'px' => array( 'min' => -15, 'max' => 15, 'step' => 1 )
			),
			'render_type' => 'ui',
			'selectors' => array( '{{WRAPPER}} .cs-list' => '--icon-v-offset: {{SIZE}}px;' )
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
            $list_style = $settings[ 'style' ];
            $has_icon = false;
            $this->add_render_attribute( array(
    			'wrapper' => array( 'class' => array( 'cs-list', $list_style ) ),
    			'icon' => array( 'class' => array( 'list-icon' ) ),
    			'content' => array( 'class' => array( 'list-content' ) )
            ) );
            if ( 'cs-list-type-none' != $list_style ) {
                $has_icon = true;
                if ( ! empty( $settings[ 'icon_preset_color' ] ) && ( 'custom' != $settings[ 'icon_preset_color' ] ) ) {
                    $this->add_render_attribute( 'icon', 'class', $settings[ 'icon_preset_color' ] );
                }
                if ( 'cs-list-type-ordered' == $list_style ) {
                    $this->add_render_attribute( 'wrapper', 'class', $settings[ 'type' ] );
                }
            }
            if ( ! empty( $settings[ 'border' ] ) ) {
                $this->add_render_attribute( 'wrapper', 'class', $settings[ 'border' ] );
            }
            if ( ! empty( $settings[ 'content_preset_color' ] ) && ( 'custom' != $settings[ 'content_preset_color' ] ) ) {
                $this->add_render_attribute( 'wrapper', 'class', $settings[ 'content_preset_color' ] );
            } ?>

            <ul <?php $this->print_render_attribute_string( 'wrapper' ); ?>><?php
            foreach( $settings[ 'list' ] as $index => $item ) :
				$content_key = 'list.' . $index . '.content';
				$this->add_render_attribute( $content_key, 'class', 'list-content' );
				$this->add_inline_editing_attributes( $content_key, 'none' ); ?>
                <li><?php
                    if ( $has_icon ) : ?>
                        <span <?php $this->print_render_attribute_string( 'icon' ); ?>>
                            <?php if ( 'cs-list-type-unordered' == $list_style ) {
                                \Elementor\Icons_Manager::render_icon( $settings[ 'list_icon' ], [ 'aria-hidden' => 'true' ] );
                            } ?>
                        </span><?php
                    endif; ?>
                    <span <?php $this->print_render_attribute_string( $content_key ); ?>><?php $this->print_unescaped_setting( 'content', 'list', $index ); ?></span><?php
                    if ( ! empty( $item[ 'link' ][ 'url' ] ) ) :
                        $this->add_render_attribute( 'link_' . $index, 'class', 'list-link' );
                        $this->add_link_attributes( 'link_' . $index, $item[ 'link' ] ); ?>
                        <a <?php $this->print_render_attribute_string( 'link_' . $index ); ?> aria-label="<?php esc_attr_e( 'List Item Link', 'loftocean' ); ?>"></a><?php
                    endif; ?>
                </li><?php
            endforeach; ?>
            </ul><?php
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
            var listStyle = settings[ 'style' ], hasIcon = false, iconHTML = false;
            view.addRenderAttribute( 'wrapper', 'class', [ 'cs-list', listStyle ] );
            view.addRenderAttribute( 'icon', 'class', 'list-icon' );
            view.addRenderAttribute( 'content', 'class', 'list-content' );
            if ( 'cs-list-type-none' != listStyle ) {
                hasIcon = true;
                if ( settings[ 'list_icon' ] && settings[ 'list_icon' ][ 'value' ] ) {
                    iconHTML = elementor.helpers.renderIcon( view, settings.list_icon, { 'aria-hidden': true }, 'i' , 'object' );
                }
                if ( 'cs-list-type-ordered' == listStyle ) {
                    view.addRenderAttribute( 'wrapper', 'class', settings[ 'type' ] );
                }
                if ( settings[ 'icon_preset_color' ] && ( 'custom' != settings[ 'icon_preset_color' ] ) ) {
                    view.addRenderAttribute( 'icon', 'class', settings[ 'icon_preset_color' ] );
                }
            }
            if ( settings[ 'border' ] ) {
                view.addRenderAttribute( 'wrapper', 'class', settings[ 'border' ] );
            }
            if ( settings[ 'content_preset_color' ] && ( 'custom' != settings[ 'content_preset_color' ] ) ) {
                view.addRenderAttribute( 'wrapper', 'class', settings[ 'content_preset_color' ] );
            } #>

            <ul {{{ view.getRenderAttributeString( 'wrapper' ) }}}><#
            settings[ 'list' ].forEach( function( item, index ) {
				var contentKey = 'list.' + index + '.content';
				view.addRenderAttribute( contentKey, 'class', 'list-content' );
				view.addInlineEditingAttributes( contentKey, 'none' ); #>
                <li><#
                    if ( hasIcon ) { #>
                        <span {{{ view.getRenderAttributeString( 'icon' ) }}}>
                            <# if ( 'cs-list-type-unordered' == listStyle && iconHTML ) { #>
                                {{{ iconHTML.value }}}
                            <# } #>
                        </span><# } #>
                    <span {{{ view.getRenderAttributeString( contentKey ) }}}>{{{ item[ 'content' ] }}}</span><#
                    if ( item[ 'link' ][ 'url' ] ) {
                        view.addRenderAttribute( 'link_' + index, 'class', 'list-link' ); #>
                        <a href="{{ item.link.url }}" {{{ view.getRenderAttributeString( 'link_' + index ) }}}></a><#
                    } #>
                </li><#
            } ); #>
        </ul><#
        } #><?php
	}
}
