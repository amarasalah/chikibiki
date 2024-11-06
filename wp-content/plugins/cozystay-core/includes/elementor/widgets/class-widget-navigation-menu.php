<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Navigation Menu
 */
class Widget_Navigation_Menu extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceannavigationmenu', array( 'id' => 'navigation-menu' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Navigation Menu', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-nav-menu';
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
		return array( 'navigation menu', 'menu', 'navigation' );
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
    * Helper function to get menus
    */
    protected function get_menus() {
        $menu_list = array();
    	$nav_menus = wp_get_nav_menus( array( 'orderby' => 'term_id', 'order' => 'ASC' ) );
    	if ( count( $nav_menus ) ) {
    		foreach ( $nav_menus as $nav ) {
    			$menu_list[ $nav->term_id ] = esc_html( $nav->name );
    		}
    	} else {
    		$menu_list = array( '' => esc_html__( 'No Menu set yet', 'loftocean' ) );
    	}
    	return $menu_list;
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
		$menus = $this->get_menus();
		$menu_keys = \LoftOcean\is_valid_array( $menus ) ? array_keys( $menus ) : array( '' );
        $this->add_control( 'menu', array(
            'label' => esc_html__( 'Select Menu', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
			'default' => reset( $menu_keys ),
            'options' => $menus
        ) );
        $this->end_controls_section();

        $this->start_controls_section( 'general_style_section', array(
            'label' => esc_html__( 'General', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'style', array(
            'label' => esc_html__( 'Menu Style', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'main-navigation',
            'options' => array(
                'main-navigation' => esc_html__( 'Primary Menu', 'loftocean' ),
                'cs-menu-inline' => esc_html__( 'Inline', 'loftocean' ),
                'footer-menu' => esc_html__( 'Footer Menu', 'loftocean' ),
                'cs-menu-mobile' => esc_html__( 'Mobile Menu', 'loftocean' ),
            )
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
            ),
            'default' => '',
        ) );
		$this->add_responsive_control( 'width', array(
			'label' => esc_html__( 'Width', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'size_units' => array( 'px', 'vw', '%' ),
			'range' => array(
				'px' => array( 'max' => 1000, 'min' => 1 ),
				'%' => array( 'max' => 100, 'min' => 1 ),
				'vw' => array( 'max' => 100, 'min' => 1 )
			),
			'render_type' => 'ui',
			'separator' => 'before',
			'selectors' => array( '{{WRAPPER}} .cs-menu' => 'max-width: {{SIZE}}{{UNIT}};' )
		) );
		$this->add_control( 'preset_color', array(
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
		$this->add_control( 'custom_color', array(
			'label' => esc_html__( 'Custom Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
			'condition' => array( 'preset_color[value]' => 'custom' ),
			'selectors' => array(
				'{{WRAPPER}} .cs-menu' => 'color: {{VALUE}};',
			)
		) );
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'label'    => esc_html__( 'Typography', 'loftocean' ),
				'selector' => '{{WRAPPER}} .cs-menu .menu > li > a',
			)
		);
		$this->add_responsive_control( 'item_distance', array(
			'label' => esc_html__( 'Menu Item Distance', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => array(
				'px' => array( 'max' => 200, 'step' => 1 )
			),
			'render_type' => 'ui',
			'separator' => 'before',
			'condition' => array( 'style[value]!' => 'cs-menu-mobile' ),
			'selectors' => array( '{{WRAPPER}} .cs-menu.not-mobile-menu' => '--item-padding: {{SIZE}}px;' )
		) );
        $this->add_responsive_control( 'mobile_submenu_top_offset', array(
            'label' => esc_html__( 'Submenu Toggle Button Top Offset', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
            'condition' => array( 'style[value]' => 'cs-menu-mobile' ),
			'size_units' => array( 'px' ),
			'range' => array(
				'px' => array( 'min' => 1, 'max' => 500 )
			),
			'separator' => 'before',
			'selectors' => array( '{{WRAPPER}} .cs-menu-mobile .mobile-menu .dropdown-toggle ' => 'top: {{SIZE}}px;' )
        ) );
		$this->end_controls_section();

		$this->start_controls_section( 'submenu_style_section', array(
			'label' => esc_html__( 'Submenu Dropdown', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'submenu_color_scheme', array(
			'label' => esc_html__( 'Submenu Drop Down Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'condition' => array( 'style[value]' => 'main-navigation' ),
			'default' => 'dropdown-dark',
			'options' => array(
				'dropdown-light' => esc_html__( 'Light', 'loftocean' ),
				'dropdown-dark' => esc_html__( 'Dark', 'loftocean' ),
			)
		) );
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'submenu_typography',
				'label'    => esc_html__( 'Typography', 'loftocean' ),
				'selector' => '{{WRAPPER}} .cs-menu .menu ul.sub-menu li a',
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
        if ( ! empty( $settings[ 'menu' ] ) ) :
            $style = $settings[ 'style' ];
    		$container_class = array( 'cs-menu', $style );
			( 'cs-menu-mobile' == $style ) ? '' : array_push( $container_class, 'not-mobile-menu' );
            $menu_class = array( 'menu' );
            $styles = array(
                'main-navigation' => 'primary',
                'cs-menu-inline' => 'inline',
                'footer-menu' => 'footer',
                'cs-menu-mobile' => 'mobile'
            );
            if ( 'main-navigation' == $style ) {
                array_push( $container_class, $settings[ 'submenu_color_scheme' ] );
                array_push( $menu_class, 'primary-menu' );
            } else if ( 'cs-menu-mobile' == $style ) {
                array_push( $menu_class, 'mobile-menu' );
            }
            $alignment = array( 'alignment' => '', 'alignment_mobile' => '-mobile', 'alignment_tablet' => '-tablet' );
    		foreach( $alignment as $align => $after ) {
    			if ( ! empty( $settings[ $align ] ) ) {
    				array_push( $container_class, $settings[ $align ] . $after );
    			}
    		}
			if ( ! empty( $settings[ 'preset_color' ] ) && ( 'custom' != $settings[ 'preset_color' ] ) ) {
				array_push( $container_class, $settings[ 'preset_color' ] );
			}
            do_action( 'loftocean_elementor_navigation_menu', $settings[ 'menu' ], array(
                'container_class' => implode( ' ', $container_class ),
                'menu_id' => 'menu-' . $this->get_id(),
                'menu_class' => implode( ' ', $menu_class ),
                'style' => $styles[ $style ]
            ) );
        elseif ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) : ?>
			<div class="cs-notice"><?php printf(
				// translators: 1/2. html tag
				esc_html__( 'Please go to %1$sDashboard > Appearance > Menus%2$s to create a menu first.', 'loftocean' ),
				'<strong>',
				'</strong>'
			); ?></div><?php
		endif;
    }
	/**
	 * Import navigation menu
	 * Elementor template JSON file, and replacing the old data.
	 */
	public function on_import( $settings ) {
		$styles = array( 'main-navigation' => 'primary-menu', 'footer-menu' => 'footer-menu' );
		$style = isset( $settings[ 'settings' ][ 'style' ] ) ? $settings[ 'settings' ][ 'style' ] : '';
		$locations = get_nav_menu_locations();
		if ( isset( $styles[ $style ] ) && isset( $locations[ $styles[ $style ] ] ) ) {
		   $menu = wp_get_nav_menu_object( $locations[ $styles[ $style ] ] );
		   if ( false !== $menu ) {
			   $settings[ 'settings' ][ 'menu' ] = $locations[ $styles[ $style ] ];
			   return  $settings;
		   }
	   }

		$menus = $this->get_menus();
		$menu_keys = \LoftOcean\is_valid_array( $menus ) ? array_keys( $menus ) : array( '' );
		$settings[ 'settings' ][ 'menu' ] = reset( $menu_keys );
		return $settings;
	}
}
