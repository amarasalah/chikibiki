<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Testimonials
 */
class Widget_Testimonials extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceantestimonials', array( 'id' => 'testimonials' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Testimonials', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-testimonial-carousel';
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
		return [ 'testimonials', 'testimonial' ];
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
			'default' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 'loftocean' )
		) );
        $repeater->add_control( 'image', array(
            'label' => esc_html__( 'Choose Image', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::MEDIA
        ) );
        $repeater->add_group_control( \Elementor\Group_Control_Image_Size::get_type(), array(
			'name' => 'image',
			'default' => 'thumbnail',
			'separator' => 'none',
		) );
		$repeater->add_control( 'name',array(
			'label' => esc_html__( 'Name', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'label_block' => true,
			'default' => esc_html__( 'Alice Wayne', 'loftocean' )
		) );
		$repeater->add_control( 'title',array(
			'label' => esc_html__( 'Title', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'label_block' => true,
			'default' => esc_html__( 'Blogger', 'loftocean' )
		) );
		$this->add_control( 'list', array(
			'label' => esc_html__( 'Testimonial Item', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::REPEATER,
			'fields' => $repeater->get_controls(),
			'default' => array(
                array(
    				'name' => esc_html__( 'Alice Wayne', 'loftocean' ),
                    'title' => esc_html__( 'Blogger', 'loftocean' ),
                    'content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 'loftocean' )
    			), array(
    				'name' => esc_html__( 'Alice Wayne', 'loftocean' ),
                    'title' => esc_html__( 'Blogger', 'loftocean' ),
    				'content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 'loftocean' )
                ), array(
    				'name' => esc_html__( 'Alice Wayne', 'loftocean' ),
                    'title' => esc_html__( 'Blogger', 'loftocean' ),
    				'content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 'loftocean' )
                )
            ),
            'title_field' => '{{{ name }}}',
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'layout_section', array(
            'label' => __( 'Layout', 'loftocean' ),
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
			)
		) );
        $this->add_control( 'layout', array(
            'label'	=> esc_html__( 'Layout', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'slider',
            'options' => array(
				'grid-col-2' => esc_html__( 'Grid 2 Columns', 'loftocean' ),
				'grid-col-3' => esc_html__( 'Grid 3 Columns', 'loftocean' ),
				'slider' => esc_html__( 'Slider', 'loftocean' ),
			)
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'slider_section', array(
            'label' => __( 'Slider', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => array( 'layout[value]' => 'slider' )
        ) );
        $this->add_control( 'slider_column', array(
            'label' => esc_html__( 'Column', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '1',
            'options' => array(
                '1' => esc_html__( '1 Column', 'loftocean' ),
                '2' => esc_html__( '2 Columns', 'loftocean' ),
                '3' => esc_html__( '3 Columns', 'loftocean' ),
                '4' => esc_html__( '4 Columns', 'loftocean' )
            )
        ) );
        $this->add_control( 'autoplay', array(
            'label' => esc_html__( 'Autoplay', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'autoplay_speed', array(
			'label' => esc_html__( 'Autoplay Speed', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::NUMBER,
			'condition' => array( 'autoplay[value]' => 'on' ),
			'default' => '5000'
		) );
        $this->add_control( 'show_arrows', array(
            'label' => esc_html__( 'Show Arrows', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'on',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'slider_arrows_background_color', array(
			'label' => esc_html__( 'Slider Arrow Background Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'condition' => array(
				'layout[value]' => 'slider',
				'show_arrows[value]' => 'on'
			),
            'selectors' => array(
				'{{WRAPPER}} .slick-arrow' => 'background-color: {{VALUE}};',
			)
		) );
        $this->add_control( 'slider_arrows_icon_color', array(
			'label' => esc_html__( 'Slider Arrow Icon Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'condition' => array(
				'layout[value]' => 'slider',
				'show_arrows[value]' => 'on'
			),
            'selectors' => array(
				'{{WRAPPER}} .slick-arrow' => 'color: {{VALUE}};',
			)
		) );
        $this->add_control( 'show_dots', array(
            'label' => esc_html__( 'Show Dots', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'on',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
		$this->add_control( 'dots_alignment', array(
            'label'	=> esc_html__( 'Dots Alignment', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::CHOOSE,
			'default' => '',
            'options' => array(
				'slider-dots-left' => array(
					'title' => esc_html__( 'Left', 'loftocean' ),
					'icon' => 'eicon-text-align-left'
				),
				'' => array(
					'title' => esc_html__( 'Center', 'loftocean' ),
					'icon' => 'eicon-text-align-center',
				),
				'slider-dots-right' => array(
					'title' => esc_html__( 'Right', 'loftocean' ),
					'icon' => 'eicon-text-align-right',
				)
			),
            'condition' => array( 'show_dots[value]' => 'on' )
		) );
        $this->add_control( 'slider_dots_color', array(
			'label' => esc_html__( 'Slider Dots Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'condition' => array(
				'layout[value]' => 'slider',
				'show_dots[value]' => 'on'
			),
            'selectors' => array(
				'{{WRAPPER}} .slick-dots li' => 'color: {{VALUE}};',
			)
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'content_style_section', array(
            'label' => esc_html__( 'Content', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'content_color', array(
            'label' => esc_html__( 'Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .cs-testimonial-content' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'content_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .cs-testimonial-content',
            )
        );
        $this->end_controls_section();

        $this->start_controls_section( 'image_style_section', array(
            'label' => esc_html__( 'Image', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE
        ) );
        $this->add_control( 'image_position', array(
            'label'	=> esc_html__( 'Image Position', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '',
            'options' => array(
				'' => esc_html__( 'Top', 'loftocean' ),
				'elementor-testimonial-image-position-aside' => esc_html__( 'Aside', 'loftocean' ),
			)
		) );
		$this->add_responsive_control( 'image_width', array(
			'label' => esc_html__( 'Image Width', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'default' => array( 'unit' => '%' ),
			'tablet_default' => array( 'unit' => '%' ),
			'mobile_default' => array( 'unit' => '%' ),
			'size_units' => array( '%', 'px', 'vw' ),
			'range' => array(
				'%' => array( 'min' => 1, 'max' => 100 ),
				'px' => array( 'min' => 1, 'max' => 1000 ),
				'vw' => array( 'min' => 1, 'max' => 100 )
			),
			'selectors' => array( '{{WRAPPER}} .elementor-testimonial-image img' => 'width: {{SIZE}}{{UNIT}};' )
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'title_style_section', array(
            'label' => esc_html__( 'Title', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'title_color', array(
            'label' => esc_html__( 'Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .elementor-testimonial-job' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .elementor-testimonial-job',
            )
        );
        $this->end_controls_section();

        $this->start_controls_section( 'name_style_section', array(
            'label' => esc_html__( 'Name', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'name_color', array(
            'label' => esc_html__( 'Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .elementor-testimonial-name' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'name_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .elementor-testimonial-name',
            )
        );
        $this->end_controls_section();

		$this->start_controls_section( 'rating_style_section', array(
			'label' => esc_html__( 'Star Rating', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'enable_rating', array(
			'label' => esc_html__( 'Display Star Rating', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'off',
			'label_on' => 'on',
			'label_off' => 'off',
			'return_value' => 'on'
		) );
        $this->add_control( 'rating_color', array(
            'label' => esc_html__( 'Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
			'condition' => array( 'enable_rating[value]' => 'on' ),
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .cs-testimonial-stars:before' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_control( 'rating_position', array(
            'label'	=> esc_html__( 'Position', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'condition' => array( 'enable_rating[value]' => 'on' ),
            'default' => '',
            'options' => array(
				'above' => esc_html__( 'Above Content', 'loftocean' ),
				'' => esc_html__( 'Below Content', 'loftocean' ),
			)
		) );
		$this->add_responsive_control( 'icon_size', array(
            'label'	=> esc_html__( 'Icon Size', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'condition' => array( 'enable_rating[value]' => 'on' ),
			'range' => array( 'px' => array( 'max' => 50, 'min' => 1 ) ),
			'render_type' => 'ui',
			'separator' => 'before',
			'selectors' => array( '{{WRAPPER}} .cs-testimonial-stars:before' => 'font-size: {{SIZE}}px;' )
        ) );
		$this->add_responsive_control( 'item_space_between', array(
            'label'	=> esc_html__( 'Space Between Stars', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'condition' => array( 'enable_rating[value]' => 'on' ),
			'range' => array( 'px' => array( 'max' => 200, 'min' => 0 ) ),
			'render_type' => 'ui',
			'separator' => 'before',
			'selectors' => array( '{{WRAPPER}} .cs-testimonial-stars:before' => 'letter-spacing: {{SIZE}}px;' )
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
            $this->add_render_attribute( 'wrapper', 'class', array( 'cs-testimonials', 'testimonial-style-1' ) );

            $is_slider = 'slider' == $settings[ 'layout' ];
            $alignment = array( 'alignment' => '', 'alignment_mobile' => '-mobile', 'alignment_tablet' => '-tablet' );
            foreach( $alignment as $align => $after ) {
                if ( ! empty( $settings[ $align ] ) ) {
                    $this->add_render_attribute( 'wrapper', 'class', $settings[ $align ] . $after );
                }
            }
            if ( $is_slider ) {
                $this->add_render_attribute( 'wrapper', 'class', 'testimonials-slider' );
                $this->add_render_attribute( 'wrapper', 'data-column', $settings[ 'slider_column' ] );
                $this->add_render_attribute( 'wrapper', 'data-autoplay', $settings[ 'autoplay' ] );
				$this->add_render_attribute( 'wrapper', 'data-autoplay-speed', ( 'on' == $settings[ 'autoplay' ] ? $settings[ 'autoplay_speed' ] : 5000 ) );
                $this->add_render_attribute( 'wrapper', 'data-show-arrows', $settings[ 'show_arrows' ] );
                $this->add_render_attribute( 'wrapper', 'data-show-dots', $settings[ 'show_dots' ] );
                if ( ( 'on' == $settings[ 'show_dots' ] ) && ! empty( $settings[ 'dots_alignment' ] ) ) {
                    $this->add_render_attribute( 'wrapper', 'class', $settings[ 'dots_alignment' ] );
                }
            } else {
                $this->add_render_attribute( 'wrapper', 'class', array( 'testimonials-grid', $settings[ 'layout' ] ) );
            }
            if ( ! empty( $settings[ 'image_position' ] ) ) {
                $this->add_render_attribute( 'wrapper', 'class', $settings[ 'image_position' ] );
            }
			$rating_position = false;
			$rating_html = '<div class="cs-testimonial-rating"><span class="cs-testimonial-stars"></span></div>';
			if ( 'on' == $settings[ 'enable_rating' ] ) {
				$rating_position = empty( $settings[ 'rating_position' ] ) ? 'below' : 'above';
			} ?>

            <div <?php $this->print_render_attribute_string( 'wrapper' ); ?>><?php
                if ( $is_slider ) : ?><div class="cs-ts-wrap"><?php endif;
                foreach( $settings[ 'list' ] as $index => $testimonial ) :
					$content_key = 'list.' . $index . '.content';
					$name_key = 'list.' . $index . '.name';
					$title_key = 'list.' . $index . '.title';

					$this->add_render_attribute( $content_key, 'class', 'cs-testimonial-content' );
					$this->add_inline_editing_attributes( $content_key, 'none' );
					$this->add_render_attribute( $name_key, 'class', 'elementor-testimonial-name' );
					$this->add_inline_editing_attributes( $name_key, 'none' );
					$this->add_render_attribute( $title_key, 'class', 'elementor-testimonial-job' );
					$this->add_inline_editing_attributes( $title_key, 'none' );
                    if ( $is_slider ) : ?><div class="cs-ts-item"><?php endif; ?>
                    <div class="cs-testimonial"><?php
						if ( 'above' == $rating_position ) { echo $rating_html; }
						if ( ! empty( $testimonial[ 'content' ] ) ) : ?>
                        <div <?php $this->print_render_attribute_string( $content_key ); ?>><?php $this->print_unescaped_setting( 'content', 'list', $index ); ?></div><?php
						endif;
						if ( 'below' == $rating_position ) { echo $rating_html; }
						if ( ! empty( $testimonial[ 'image' ][ 'id' ] ) || ! empty( $testimonial[ 'title' ] ) || ! empty( $testimonial[ 'name' ] ) ) : ?>
                        <div class="cs-testimonial-meta">
                            <div class="elementor-testimonial-meta-inner"><?php
                                if ( ! empty( $testimonial[ 'image' ][ 'id' ] ) ) : ?>
                                    <div class="elementor-testimonial-image">
                                        <?php \Elementor\Group_Control_Image_Size::print_attachment_image_html( $testimonial, 'image', 'image' ); ?>
                                    </div><?php
                                endif;
								if ( ! empty( $testimonial[ 'name' ] ) || ! empty( $testimonial[ 'title' ] ) ) : ?>
	                                <div class="elementor-testimonial-details"><?php
									if ( ! empty( $testimonial[ 'name' ] ) ) : ?>
	                                    <div <?php $this->print_render_attribute_string( $name_key ); ?>><?php $this->print_unescaped_setting( 'name', 'list', $index ); ?></div><?php
									endif;
									if ( ! empty( $testimonial[ 'title' ] ) ) : ?>
	                                    <div <?php $this->print_render_attribute_string( $title_key ); ?>><?php $this->print_unescaped_setting( 'title', 'list', $index ); ?></div><?php
									endif; ?>
									</div><?php
								endif; ?>
                            </div>
                        </div><?php
						endif; ?>
                    </div><?php
                    if ( $is_slider ) : ?></div><?php endif;
                endforeach;
                if ( $is_slider ) : ?></div><?php endif; ?>
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
        if ( Array.isArray( settings[ 'list' ] ) && settings[ 'list' ].length ) {
            view.addRenderAttribute( 'wrapper', 'class', [ 'cs-testimonials', 'testimonial-style-1' ] );
            var isSlider = 'slider' == settings[ 'layout' ],
                alignment = { 'alignment': '', 'alignment_mobile': '-mobile', 'alignment_tablet': '-tablet' };
            jQuery.each( alignment, function( align, after ) {
                if ( settings[ align ] ) {
                    view.addRenderAttribute( 'wrapper', 'class', settings[ align ] + after );
                }
            } );
            if ( isSlider ) {
                view.addRenderAttribute( 'wrapper', 'class', 'testimonials-slider' );
                view.addRenderAttribute( 'wrapper', 'data-column', settings[ 'slider_column' ] );
                view.addRenderAttribute( 'wrapper', 'data-autoplay', settings[ 'autoplay' ] );
				view.addRenderAttribute( 'wrapper', 'data-autoplay-speed', ( 'on' == settings[ 'autoplay' ] ? settings[ 'autoplay_speed' ] : 5000 ) );
                view.addRenderAttribute( 'wrapper', 'data-show-arrows', settings[ 'show_arrows' ] );
                view.addRenderAttribute( 'wrapper', 'data-show-dots', settings[ 'show_dots' ] );
                if ( ( 'on' == settings[ 'show_dots' ] ) && settings[ 'dots_alignment' ] ) {
                    view.addRenderAttribute( 'wrapper', 'class', settings[ 'dots_alignment' ] );
                }
            } else {
                view.addRenderAttribute( 'wrapper', 'class', [ 'testimonials-grid', settings[ 'layout' ] ] );
            }
            if ( settings[ 'image_position' ] ) {
                view.addRenderAttribute( 'wrapper', 'class', settings[ 'image_position' ] );
            }

			var ratingPosition = false, ratingHTML = '<div class="cs-testimonial-rating"><span class="cs-testimonial-stars"></span></div>';
			if ( 'on' == settings[ 'enable_rating' ] ) {
				ratingPosition = settings[ 'rating_position' ] ? 'above' : 'below';
			} #>

            <div {{{ view.getRenderAttributeString( 'wrapper' ) }}}><#
                if ( isSlider ) { #><div class="cs-ts-wrap"><# }
                settings[ 'list' ].forEach( function( testimonial, index ) {
					var contentKey = 'list.' + index + '.content',
						nameKey = 'list.' + index + '.name',
						titleKey = 'list.' + index + '.title';

						view.addRenderAttribute( contentKey, 'class', 'cs-testimonial-content' );
						view.addInlineEditingAttributes( contentKey, 'none' );
						view.addRenderAttribute( nameKey, 'class', 'elementor-testimonial-name' );
						view.addInlineEditingAttributes( nameKey, 'none' );
						view.addRenderAttribute( titleKey, 'class', 'elementor-testimonial-job' );
						view.addInlineEditingAttributes( titleKey, 'none' );

                    if ( isSlider ) { #><div class="cs-ts-item"><# } #>
                    <div class="cs-testimonial">
						<# if ( 'above' == ratingPosition ) { print( ratingHTML ); } #>
                        <# if ( testimonial.content ) { #><div {{{ view.getRenderAttributeString( contentKey ) }}}>{{{ testimonial[ 'content' ] }}}</div><# } #>
						<# if ( 'below' == ratingPosition ) { print( ratingHTML ); } #>
						<# if ( testimonial.image.url || testimonial.name || testimonial.title ) { #>
                        <div class="cs-testimonial-meta">
                            <div class="elementor-testimonial-meta-inner"><#
                                if ( testimonial.image.url ) {
                                    var image = {
                        				id: testimonial.image.id,
                        				url: testimonial.image.url,
                        				size: testimonial.image_size,
                        				dimension: testimonial.image_custom_dimension,
                        				model: view.getEditModel()
                        			};
                        			var imageURL = elementor.imagesManager.getImageUrl( image );
                                    if ( imageURL ) { #>
                                        <div class="elementor-testimonial-image">
                                            <img src="{{ imageURL }}">
                                        </div><#
                                    }
                                }
								if ( testimonial.title || testimonial.name ) { #>
	                                <div class="elementor-testimonial-details">
	                                    <# if ( testimonial.name ) { #><div {{{ view.getRenderAttributeString( nameKey ) }}}>{{{ testimonial[ 'name' ] }}}</div><# } #>
	                                    <# if ( testimonial.title ) { #><div {{{ view.getRenderAttributeString( titleKey ) }}}>{{{ testimonial[ 'title' ] }}}</div><# } #>
	                                </div><#
								} #>
                            </div>
                        </div>
						<# } #>
                    </div><#
                    if ( isSlider ) { #></div><# }
                } );
                if ( isSlider ) { #></div><# } #>
            </div><#
        }
        #><?php
	}
}
