<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Image Gallery
 */
class Widget_Image_Gallery extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceanimagegallery', array( 'id' => 'image-gallery' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Image Gallery', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
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
		return [ 'image', 'images', 'gallery' ];
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
        $this->add_control( 'gallery', array(
			'label' => esc_html__( 'Add Images', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::GALLERY,
			'show_label' => false
		) );
        $this->add_group_control( \Elementor\Group_Control_Image_Size::get_type(), array(
			'name' => 'image',
			'default' => 'full',
			'separator' => 'none',
		) );
        $this->add_control( 'click_action', array(
			'label' => esc_html__( 'Click Action', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => array(
				'file' => esc_html__( 'Link to media file', 'loftocean' ),
				'light-box' => esc_html__( 'Open in lightbox', 'loftocean' ),
                '' => esc_html__( 'None', 'loftocean' )
			)
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'style_section', array(
            'label' => esc_html__( 'Style', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'layout', array(
			'label' => esc_html__( 'Layout', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'grid',
			'options' => array(
				'grid' => esc_html__( 'Grid', 'loftocean' ),
                'slider' => esc_html__( 'Slider', 'loftocean' )
			)
		) );
        $this->add_control( 'space', array(
			'label' => esc_html__( 'Space Between', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'gap-5',
			'options' => array(
				'gap-0' => esc_html__( '0', 'loftocean' ),
				'gap-2' => esc_html__( '4px', 'loftocean' ),
				'gap-5' => esc_html__( '10px', 'loftocean' ),
                'gap-10' => esc_html__( '20px', 'loftocean' ),
				'gap-16' => esc_html__( '32px', 'loftocean' ),
				'gap-20' => esc_html__( '40px', 'loftocean' ),
                'gap-32' => esc_html__( '64px', 'loftocean' )
			)
		) );
        $this->add_control( 'vertical_alignment', array(
			'label' => esc_html__( 'Vertical Alignment', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => array(
				'' => esc_html__( 'Top', 'loftocean' ),
				'align-middle-v' => esc_html__( 'Middle', 'loftocean' ),
                'align-bottom-v' => esc_html__( 'Bottom', 'loftocean' )
			)
		) );
        $this->add_control( 'grid_column', array(
			'label' => esc_html__( 'Column', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'grid-col-3',
			'options' => array(
				'grid-col-2' => esc_html__( '2 Columns', 'loftocean' ),
				'grid-col-3' => esc_html__( '3 Columns', 'loftocean' ),
                'grid-col-4' => esc_html__( '4 Columns', 'loftocean' ),
				'grid-col-5' => esc_html__( '5 Columns', 'loftocean' ),
                'grid-col-6' => esc_html__( '6 Columns', 'loftocean' )
			),
            'condition' => array( 'layout[value]' => 'grid' )
		) );
		$this->end_controls_section();

        $this->start_controls_section( 'slider_style_section', array(
            'label' => esc_html__( 'Slider', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => array( 'layout[value]' => 'slider' )
        ) );
        $this->add_control( 'slider_column', array(
            'label' => esc_html__( 'Column', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '3',
            'options' => array(
                '1' => esc_html__( '1 Column', 'loftocean' ),
                '2' => esc_html__( '2 Columns', 'loftocean' ),
                '3' => esc_html__( '3 Columns', 'loftocean' ),
                '4' => esc_html__( '4 Columns', 'loftocean' ),
                '5' => esc_html__( '5 Columns', 'loftocean' ),
                '6' => esc_html__( '6 Columns', 'loftocean' )
            )
        ) );
        $this->add_control( 'slider_variable_width', array(
            'label' => esc_html__( 'Variable Width', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_responsive_control( 'slider_gallery_height', array(
        	'label'     => esc_html__( 'Gallery Height', 'loftocean' ),
            'type'      => \Elementor\Controls_Manager::SLIDER,
            'condition'	=> array( 'slider_variable_width' => 'on' ),
            'size_units' => array( 'vh', 'px' ),
            'range' => array(
                'vh'  => array(
                    'min' => 0,
                    'max' => 100,
                ),
                'px' => array(
                    'min' => 0,
                    'max' => 1000,
                ),
            ),
            'selectors' => array(
                '{{WRAPPER}} .cs-gallery.gallery-carousel .cs-gallery-item' => 'height: {{SIZE}}{{UNIT}};',
            )
        ) );
        $this->add_control( 'slider_overflow_style', array(
        	'label' => esc_html__( 'Overflow Style', 'loftocean' ),
        	'description' => esc_html__( 'If enabled, Center Mode will not work.', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on',
            'separator' => 'after'
        ) );
        $this->add_control( 'slider_center_mode', array(
            'label' => esc_html__( 'Center Mode', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on',
            'condition' => array( 'slider_overflow_style[value]!' => 'on' )
        ) );
        $this->add_control( 'slider_height', array(
            'label' => esc_html__( 'Slider Height', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '',
            'options' => array(
                '' => esc_html__( 'Default', 'loftocean' ),
                'slider-height-full' => esc_html__( 'Fit To Screen', 'loftocean' )
            ),
            'condition' => array( 'slider_column[value]' => '1' )
        ) );
        $this->add_control( 'slider_fade', array(
            'label' => esc_html__( 'Fade', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on',
            'condition' => array( 'slider_column[value]' => '1' )
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
		$this->add_control( 'dots_position', array(
            'label'	=> esc_html__( 'Dots Position', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
            'options' => array(
                '' => esc_html__( 'Below', 'loftocean' ),
                'slider-dots-overlap' => esc_html__( 'Overlap', 'loftocean' )
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
	}
	/**
	* Written in PHP and used to generate the final HTML.
    * @access protected
	*/
	protected function render() {
        $settings = $this->get_settings_for_display();
        if ( \LoftOcean\is_valid_array( $settings[ 'gallery' ] ) ) :
            $this->add_render_attribute( 'wrapper', 'class', array( 'cs-gallery', $settings[ 'space' ] ) );
            $is_grid = 'grid' == $settings[ 'layout' ];
            $has_link = ! empty( $settings[ 'click_action' ] );
            $lightbox = 'light-box' == $settings[ 'click_action' ];
            if ( $is_grid ) {
                $this->add_render_attribute( 'wrapper', 'class', array( 'gallery-grid', $settings[ 'grid_column' ] ) );
            } else {
                $this->add_render_attribute( 'wrapper', 'class', 'gallery-carousel' );
                $this->add_render_attribute( 'wrapper', 'data-column', $settings[ 'slider_column' ] );
                $this->add_render_attribute( 'wrapper', 'data-fade', $settings[ 'slider_fade' ] );
                $this->add_render_attribute( 'wrapper', 'data-autoplay', $settings[ 'autoplay' ] );
				$this->add_render_attribute( 'wrapper', 'data-autoplay-speed', ( 'on' == $settings[ 'autoplay' ] ? $settings[ 'autoplay_speed' ] : 5000 ) );
                $this->add_render_attribute( 'wrapper', 'data-show-arrows', $settings[ 'show_arrows' ] );
                $this->add_render_attribute( 'wrapper', 'data-show-dots', $settings[ 'show_dots' ] );
                $this->add_render_attribute( 'wrapper', 'data-variable-width', $settings[ 'slider_variable_width' ] );
                ( 'on' == $settings[ 'slider_variable_width' ] ) ? $this->add_render_attribute( 'wrapper', 'class', 'variable-width' ) : '';
                if ( 'on' == $settings[ 'slider_overflow_style' ] ) {
                	$this->add_render_attribute( 'wrapper', 'class', 'style-overflow' );
	                $this->add_render_attribute( 'wrapper', 'data-overflow-style', $settings[ 'slider_overflow_style' ] );
	                $this->add_render_attribute( 'wrapper', 'data-center-mode', '' );
	            } else {
	            	$this->add_render_attribute( 'wrapper', 'data-overflow-style', '' );
	                $this->add_render_attribute( 'wrapper', 'data-center-mode', $settings[ 'slider_center_mode' ] );
	            }
                if ( '1' == $settings[ 'slider_column' ] && ! empty( $settings[ 'slider_height' ] ) ) {
                    $this->add_render_attribute( 'wrapper', 'class', $settings[ 'slider_height' ] );
                }
                if ( 'on' == $settings[ 'show_dots' ] && ! empty( $settings[ 'dots_position' ] ) ) {
                    $this->add_render_attribute( 'wrapper', 'class', $settings[ 'dots_position' ] );
                }
            }
            if ( ! empty( $settings[ 'vertical_alignment' ] ) ) {
                $this->add_render_attribute( 'wrapper', 'class', $settings[ 'vertical_alignment' ] );
            } ?>

            <div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
                <div class="cs-gallery-wrap"><?php
                foreach ( $settings[ 'gallery' ] as $index => $image ) :
                    $settings[ 'image' ] = $image; ?>
                    <div class="cs-gallery-item"><?php
                        if ( $has_link ) :
                            $this->add_render_attribute( 'link', 'href', $image[ 'url' ], true );
                            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                    			$this->add_render_attribute( 'link', 'class', 'elementor-clickable', true );
                    		}
                            if ( $lightbox ) {
                                $this->add_lightbox_data_attributes( 'link', $image[ 'id' ], 'yes', $this->get_id(), true );
                            } else {
                                $this->add_render_attribute( 'link', 'data-elementor-open-lightbox', 'no', true );
                            } ?>
                            <a <?php $this->print_render_attribute_string( 'link' ); ?> aria-label="<?php esc_attr_e( 'Image', 'loftocean' ); ?>"><?php
                        endif;
                        \Elementor\Group_Control_Image_Size::print_attachment_image_html( $settings, 'image', 'image' );
                        if ( $has_link ) : ?></a><?php endif; ?>
                    </div><?php
                endforeach; ?>
                </div>
            </div><?php
		elseif ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) : ?>
			<div class="cs-notice"><?php esc_html_e( 'Please choose some images on the widget setting panel.', 'loftocean' ); ?></div><?php
        endif;
	}
}
