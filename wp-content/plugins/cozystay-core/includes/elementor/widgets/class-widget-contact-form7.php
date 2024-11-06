<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Contact Form7.
 */
class Widget_Contact_Form7 extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceancontactform7', array( 'id' => 'contact-form7' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Contact Form 7', 'loftocean' );
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
		return [ 'form', 'contact form 7', 'contact' ];
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
		$forms = $this->get_forms();
        $this->add_control( 'form_id', array(
			'label' => esc_html__( 'Select Form', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => ( false !== $forms ) ? array_keys( $forms )[1] : '',
			'options' => $forms
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'style_section', array(
			'label' => __( 'Style', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		) );

		$this->start_controls_tabs( 'opentable_form_border' );
        $this->start_controls_tab( 'tab_form_border_normal', array(
        	'label' => esc_html__( 'Normal State', 'loftocean' ),
        ) );

        $this->add_control( 'form_border_color', array(
			'label' => esc_html__( 'Border Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'selectors' => array(
				'{{WRAPPER}}' => '--form-bd: {{VALUE}};',
			)
		) );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_form_border_focus', array(
            'label' => esc_html__( 'Focus State', 'loftocean' )
        ) );

        $this->add_control( 'form_border_focus_color', array(
            'label' => esc_html__( 'Border Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}}' => '--form-bd-focus: {{VALUE}};',
            )
        ) );
        $this->end_controls_tab();
    	$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section( 'button_style_section', array(
			'label' => __( 'Button Color', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->start_controls_tabs( 'tabs_button_style' );
        $this->start_controls_tab( 'tab_button_normal', array(
        	'label' => esc_html__( 'Normal', 'loftocean' ),
        ) );
        $this->add_control( 'button_background_color', array(
			'label' => esc_html__( 'Button Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'selectors' => array(
				'{{WRAPPER}} .wpcf7-submit' => '--btn-bg: {{VALUE}};',
			)
		) );
        $this->add_control( 'button_text_color', array(
			'label' => esc_html__( 'Text Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
            'selectors' => array(
				'{{WRAPPER}} .wpcf7-submit' => '--btn-color: {{VALUE}};',
			)
		) );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_button_hover', array(
            'label' => esc_html__( 'Hover', 'loftocean' ),
        ) );

        $this->add_control( 'button_hover_background_color', array(
            'label' => esc_html__( 'Button Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .wpcf7-submit' => '--btn-bg-hover: {{VALUE}};',
            )
        ) );
        $this->add_control( 'button_hover_text_color', array(
            'label' => esc_html__( 'Text Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .wpcf7-submit' => '--btn-color-hover: {{VALUE}};',
            )
        ) );
        $this->end_controls_tab();
    	$this->end_controls_tabs();

		$this->end_controls_section();
	}
	/**
	* Written in PHP and used to generate the final HTML.
    * @access protected
	*/
	protected function render() {
        $settings = $this->get_settings_for_display();

        if ( ! empty( $settings[ 'form_id' ] ) ) :
            $shortcode = '[contact-form-7 id="' . $settings[ 'form_id' ] . '"]'; ?>
            <div class="cs-form-cf7 placeholder-normal">
                <div class="cs-form-cf7-wrap"><?php echo do_shortcode( $shortcode ); ?></div>
            </div><?php
		elseif ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) : ?>
			<div class="cs-notice"><?php esc_html_e( 'Please choose a form on the widget setting panel.', 'loftocean' ); ?></div><?php
        endif;
	}
    /**
    * Get all contact form 7 forms
    */
    protected function get_forms() {
        $args = array( 'post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1 );
    	$forms = get_posts( $args );
        if ( \LoftOcean\is_valid_array( $forms ) ) {
            $post_ids = wp_list_pluck( $forms , 'ID' );
        	$form_titles = wp_list_pluck( $forms , 'post_title' );

            array_unshift( $post_ids, '' );
            array_unshift( $form_titles, esc_html__( 'Select a form', 'loftocean' ) );
            return array_combine( $post_ids, $form_titles );
        }
        return false;
    }
	/**
	 * Import navigation menu
	 * Elementor template JSON file, and replacing the old data.
	 */
	public function on_import( $settings ) {
		$forms = $this->get_forms();
		$settings[ 'settings' ][ 'form_id' ] = ( false === $forms ) ? '' : array_keys( $forms )[1];
		return $settings;
	}
}
