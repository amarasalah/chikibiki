<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Socail Menu.
 */
class Widget_Social_Menu extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceansocialmenu', array( 'id' => 'social-menu' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Social Menu', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-social-icons';
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
		return [ 'social menu', 'social', 'menu' ];
	}
	/**
	 * Register widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 * @access protected
	 */
	protected function register_controls() {
        $this->start_controls_section( 'general_style_section', array(
			'label' => __( 'General', 'loftocean' ),
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
			),
			'default' => '',
		) );
		$this->add_control( 'icon_size', array(
			'label' => esc_html__( 'Icon Size', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => array( 'px' => array( 'max' => 100, 'step' => 1, 'min' => 1 ) ),
			'render_type' => 'ui',
			'separator' => 'before',
			'selectors' => array( '{{WRAPPER}} ul.social-nav li a:before' => 'font-size: {{SIZE}}{{UNIT}};' )
		) );
		$this->end_controls_section();
	}
	/**
	* Written in PHP and used to generate the final HTML.
    * @access protected
	*/
	protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();
        if ( apply_filters( 'loftocean_front_has_social_menu', false ) ) :
            $class = array( 'social-nav', 'menu' );
            $alignment = array( 'alignment' => '', 'alignment_mobile' => '-mobile', 'alignment_tablet' => '-tablet' );
            foreach( $alignment as $align => $after ) {
                if ( ! empty( $settings[ $align ] ) ) {
                    array_push( $class, $settings[ $align ] . $after );
                }
            }
            wp_nav_menu( array(
    			'theme_location' => 'social-menu',
    			'depth' => 1,
    			'echo' => true,
                'container' => 'nav',
                'container_id' => $widget_id . '-social-menu-container',
        		'container_class' => 'social-navigation',
        		'menu_id' => $widget_id . '-social-menu',
        		'menu_class' => implode( ' ', $class ),
    		) );
        elseif ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) : ?>
			<div class="cs-notice"><?php printf(
				// translators: 1/2. html tag
				esc_html__( 'Please go to %1$sDashboard > Appearance > Menus > Manage Locations%2$s to setup the social menu first.', 'loftocean' ),
				'<strong>',
				'</strong>'
			); ?></div><?php
		endif;
	}
    /**
	* Render button widget output in the editor.
	* Written as a Backbone JavaScript template and used to generate the live preview.
	* @access protected
	*/
	protected function content_template() {
        $widget_id = '[[widgetID]]';
        if ( apply_filters( 'loftocean_front_has_social_menu', false ) ) : ?>
            <#
            var widgetID = view.getID(), menu = <?php echo json_encode( wp_nav_menu( array(
    			'theme_location' => 'social-menu',
    			'depth' => 1,
    			'echo' => false,
                'container' => 'nav',
                'container_id' => $widget_id . '-social-menu-container',
        		'container_class' => 'social-navigation',
        		'menu_id' => $widget_id . '-social-menu',
        		'menu_class' => 'social-nav menu[[extraClass]]',
    		) ) ); ?>;
            var extraClass = [], alignment = { 'alignment': '', 'alignment_mobile': '-mobile', 'alignment_tablet': '-tablet' };
            jQuery.each( alignment, function( align, after ) {
                if ( settings[ align ] ) {
                    extraClass.push( settings[ align ] + after );
                }
            } );
            menu = menu.replace( /\[\[widgetID\]\]/g, widgetID );
            if ( alignment ) {
                menu = menu.replace( '[[extraClass]]', ' ' + extraClass.join( ' ' ) );
            }
            #>

            {{{ menu }}}<?php
		else : ?>
			<div class="cs-notice"><?php printf(
				esc_html__( 'Please go to %1$sDashboard > Appearance > Menus > Manage Locations%2$s to setup the social menu first.', 'loftocean' ),
				'<strong>',
				'</strong>'
			); ?></div><?php
        endif;
    }
}
