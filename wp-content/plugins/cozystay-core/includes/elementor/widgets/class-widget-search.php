<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Search Button
 */
class Widget_Search_Button extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceansearchbutton', array( 'id' => 'search-button' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Search', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-site-search';
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
		return array( 'search button', 'search' );
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
		$this->start_controls_section( 'style_section', array(
			'label' => __( 'General', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE
		) );
        $this->add_control( 'font_size', array(
			'label' => esc_html__( 'Font Size', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'range' => array( 'px' => array( 'max' => 150, 'step' => 1, 'min' => 1 ) ),
			'render_type' => 'ui',
			'separator' => 'before',
			'selectors' => array( '{{WRAPPER}} .toggle-button:after' => 'font-size: {{SIZE}}px;' )
		) );
		$this->add_control( 'post_types', array(
			'type' => \Elementor\Controls_Manager::SELECT2,
    		'label' => esc_html__( 'Post Types to Search', 'loftocean' ),
            'default' => array( 'post' ),
			'multiple' => true,
            'options' => \LoftOcean\get_post_types()
		) );
		$this->end_controls_section();
	}
	/**
	* Written in PHP and used to generate the final HTML.
    * @access protected
	*/
	protected function render() {
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute( 'toggle', 'class', 'toggle-button' );
		if ( \LoftOcean\is_valid_array( $settings[ 'post_types' ] ) ) {
			$this->add_render_attribute( 'toggle', 'data-post-types', implode( ',', $settings[ 'post_types' ] ) );
		} ?>
        <div class="cs-search-toggle">
            <span <?php $this->print_render_attribute_string( 'toggle' ); ?>>
                <span class="screen-reader-text"><?php esc_html_e( 'Search', 'loftocean' ); ?></span>
            </span>
        </div><?php
    }
    /**
	* Render button widget output in the editor.
	* Written as a Backbone JavaScript template and used to generate the live preview.
	* @access protected
	*/
	protected function content_template() { ?>
		<#
		view.addRenderAttribute( 'toggle', 'class', 'toggle-button' );
        if ( settings[ 'post_types' ] ) {
            view.addRenderAttribute( 'toggle', 'data-post-types', settings[ 'post_types' ].join( ',' ) );
        } #>
        <div class="cs-search-toggle">
            <span {{{ view.getRenderAttributeString( 'toggle' ) }}}>
                <span class="screen-reader-text"><?php esc_html_e( 'Search', 'loftocean' ); ?></span>
            </span>
        </div><?php
    }
}
