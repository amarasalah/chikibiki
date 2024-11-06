<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Instagram
 */
class Widget_Instagram extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceaninstagram', array( 'id' => 'instagram' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Instagram', 'loftocean' );
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
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'instagram', 'gallery', 'image' ];
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
        $this->add_control( 'source', array(
			'label' => esc_html__( 'Image Source', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'gallery',
			'options' => array(
				'instagram' => esc_html__( 'Instagram API', 'loftocean' ),
				'gallery' => esc_html__( 'Local Image', 'loftocean' )
			)
		) );
		if ( ! $this->check_instagram_settings() ) {
			$this->add_control( 'warning', array(
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'condition' => array( 'source[value]' => 'instagram' ),
				'raw' => '<span class="description">' . sprintf(
					// translators: 1. html tag start 2. html tag end
					esc_html__( 'Click %1$shere%2$s to know how to set up and configure your Instagram account.', 'loftocean' ), '<a href="https://loftocean.com/doc/cozystay/ptkb/instagram/" target="_blank">', '</a>'
				) . '</span>'
			) );
		} else {
			$this->add_control( 'feed', array(
				'label' => esc_html__( 'Instagram Feed', 'loftocean' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'condition' => array( 'source[value]' => 'instagram' ),
				'default' => '',
				'options' => \LoftOcean\get_instagram_feeds()
			) );
		}
        $this->add_control( 'gallery', array(
			'label' => esc_html__( 'Add Images', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::GALLERY,
			'show_label' => false,
            'condition' => array( 'source[value]' => 'gallery' )
		) );
        $this->add_group_control( \Elementor\Group_Control_Image_Size::get_type(), array(
			'name' => 'image',
			'default' => 'full',
			'separator' => 'none',
            'condition' => array( 'source[value]' => 'gallery' )
		) );
        $this->add_control( 'title', array(
			'label' => esc_html__( 'Instagram Title', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => '',
            'placeholder' => esc_html__( 'Your Instagram Title', 'loftocean' )
		) );
        $this->add_control( 'link', array(
			'type' => \Elementor\Controls_Manager::URL,
			'default' => array( 'url' => '' ),
			'label' => esc_html__( 'Link', 'loftocean' ),
            'placeholder' => __( 'Enter the URL', 'loftocean' ),
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'style_section', array(
            'label' => esc_html__( 'Style', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'layout', array(
			'label' => esc_html__( 'Layout', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'column-6',
			'options' => array(
				'column-4' => esc_html__( 'Column 4', 'loftocean' ),
				'column-5' => esc_html__( 'Column 5', 'loftocean' ),
				'column-6' => esc_html__( 'Column 6', 'loftocean' ),
				'column-7' => esc_html__( 'Column 7', 'loftocean' ),
				'column-8' => esc_html__( 'Column 8', 'loftocean' ),
				'column-mosaic' => esc_html__( 'Mosaic', 'loftocean' ),
				'column-mosaic-2' => esc_html__( 'Mosaic 2', 'loftocean' )
            )
		) );
        $this->add_control( 'number', array(
			'label' => esc_html__( 'Number of Photos', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::NUMBER,
			'default' => '6'
		) );
		$this->add_responsive_control( 'space_between', array(
			'label' => esc_html__( 'Space Between (px)', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => array(
				'px' => array( 'max' => 50, 'step' => 1 )
			),
			'render_type' => 'ui',
			'separator' => 'before',
			'selectors' => array( '{{WRAPPER}} .cs-instagram' => '--ig-gap: {{SIZE}}px;' )
		) );
        $this->end_controls_section();

		$this->start_controls_section( 'title_style_section', array(
			'label' => __( 'Title Box', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE
		) );
		$this->add_responsive_control( 'title_width', array(
			'label' => esc_html__( 'Width', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => array(
				'px' => array( 'max' => 1000, 'step' => 1 )
			),
			'render_type' => 'ui',
			'separator' => 'before',
			'selectors' => array( '{{WRAPPER}} .cs-instagram .cs-instagram-title' => 'width: {{SIZE}}px;' )
		) );
		$this->add_responsive_control( 'title_height', array(
			'label' => esc_html__( 'Height', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => array(
				'px' => array( 'max' => 1000, 'step' => 1 )
			),
			'render_type' => 'ui',
			'separator' => 'before',
			'selectors' => array( '{{WRAPPER}} .cs-instagram .cs-instagram-title' => 'height: {{SIZE}}px;' )
		) );
		$this->add_control( 'border_radius', array(
			'label' => esc_html__( 'Border Radius', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => array(
				'px' => array( 'max' => 500, 'step' => 1 )
			),
			'render_type' => 'ui',
			'separator' => 'before',
			'selectors' => array( '{{WRAPPER}} .cs-instagram .cs-instagram-title' => 'border-radius: {{SIZE}}px;' )
		) );
		$this->add_control( 'background_color', array(
            'label' => esc_html__( 'Background', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
			'selectors' => array( '{{WRAPPER}} .cs-instagram .cs-instagram-title' => 'background: {{VALUE}};' )
        ) );
		$this->add_control( 'text_color', array(
            'label' => esc_html__( 'Text Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
			'selectors' => array( '{{WRAPPER}} .cs-instagram .cs-instagram-title' => 'color: {{VALUE}};' )
        ) );
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'instagram_title_typography',
				'label'    => esc_html__( 'Typography', 'loftocean' ),
				'selector' => '{{WRAPPER}} .cs-instagram .cs-instagram-title',
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
        $number = absint( $settings[ 'number' ] );
        $number = empty( $number ) ? 6 : $number;
        $has_link = false;
        $this->add_render_attribute( 'wrapper', 'class', 'cs-instagram' );
		if ( 'column-mosaic-2' == $settings[ 'layout' ] ) {
			$this->add_render_attribute( 'wrapper', 'class', array( 'column-mosaic', 'mosaic-2' ) );
		} else {
			$this->add_render_attribute( 'wrapper', 'class', $settings[ 'layout' ] );
		} ?>

        <div <?php $this->print_render_attribute_string( 'wrapper' ); ?>><?php
            if ( ! empty( $settings[ 'title' ] ) ) :
				$show_title = false;
				if ( 'instagram' == $settings[ 'source' ] ) {
					$show_title = $this->check_instagram_settings();
				} else {
					$show_title = \LoftOcean\is_valid_array( $settings[ 'gallery' ] );
				}
				if ( $show_title ) : ?>
	                <div class="cs-instagram-title"><?php
	                    if ( ! empty( $settings[ 'link'][ 'url' ] ) ) :
	                        $has_link = true;
	                        $this->add_link_attributes( 'link', $settings[ 'link' ] ); ?>
	                        <a <?php $this->print_render_attribute_string( 'link' ); ?>><?php
	                    endif;
	                    $this->print_unescaped_setting( 'title' );
	                    if ( $has_link ) : ?>
	                        </a><?php
	                    endif; ?>
	                </div><?php
				endif;
            endif;
            if ( 'instagram' == $settings[ 'source' ] ) :
				if ( $this->check_instagram_settings() ) :
					if ( ! has_action( 'loftocean_instagram_the_html' ) ) {
						do_action( 'loftocean_instagram_actions' );
					}
                	do_action( 'loftocean_instagram_the_html', ( isset( $settings[ 'feed' ] ) ? $settings[ 'feed' ] : '' ), $number );
				elseif ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) :
					if ( function_exists( '\sbi_get_database_settings' ) ) : ?>
						<div class="cs-notice"><?php esc_html_e( 'You need to set up and configure your Instagram feed first. Please go to "Dashboard > Instagram Feed" to set it up first.', 'loftocean' ); ?></div><?php
					else : ?>
						<div class="cs-notice"><?php printf(
							// translators: 1/2. html tag
							esc_html__( 'You need to install and activate the third-party plugin %1$sSmash Balloon Instagram Feed%2$s first.', 'loftocean' ),
							'<strong>',
							'</strong>'
						); ?></div><?php
					endif;
				endif;
            else :
				if ( \LoftOcean\is_valid_array( $settings[ 'gallery' ] ) ) : ?>
	                <ul><?php
	                foreach( $settings[ 'gallery' ] as $index => $image ) :
	                    if ( $index >= $number ) break;
	                    $settings[ 'current_image' ] = $image;
	                    $src = \Elementor\Group_Control_Image_Size::get_attachment_image_src( $image[ 'id' ], 'image', $settings );
	                    if ( ! empty( $src ) ) : ?>
	                        <li><div class="feed-bg" style="background-image:url(<?php echo esc_attr( $src ); ?>);"></div></li><?php
	                    endif;
	                endforeach; ?>
	                </ul><?php
				elseif ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) : ?>
					<div class="cs-notice"><?php esc_html_e( 'Please choose some images on the widget setting panel.', 'loftocean' ); ?></div><?php
		        endif;
            endif; ?>
        </div><?php
    }
	/**
	* Helper function is instagram setup correctly
	*/
	protected function check_instagram_settings() {
		if ( function_exists( '\sbi_get_database_settings' ) ) {
			$feeds = \LoftOcean\get_instagram_feeds();
			return \LoftOcean\is_valid_array( $feeds );
		}
		return false;
	}
}
