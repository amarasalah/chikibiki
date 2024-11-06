<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget MC4WP Form.
 */
class Widget_MC4WP_Form extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceanmc4wp', array( 'id' => 'mc4wp' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'MailChimp for WP', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fab fa-mailchimp fa-fw'; //'eicon-form-horizontal';
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
		return [ 'form', 'MC4WP', 'mailchimp' ];
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
	* Get default form id
	*/
	protected function get_default_form_id() {
		return get_option( 'mc4wp_default_form_id', '' );
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
        $this->add_control( 'form_id', array(
			'label' => esc_html__( 'Select Form', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => $this->get_default_form_id(),
			'options' => $this->get_forms()
		) );
		$this->end_controls_section();

		$this->start_controls_section( 'general_style_section', array(
			'label' => __( 'General', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		) );
        $this->add_responsive_control( 'width', array(
			'label' => esc_html__( 'Width', 'loftocean' ),
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
			'selectors' => array( '{{WRAPPER}} form' => 'width: {{SIZE}}{{UNIT}};' )
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
			'default' => ''
		) );
		$this->add_control( 'form_field_style', array(
			'label' => esc_html__( 'Form Field Style', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'cs-form-square',
			'options' => array(
				'cs-form-square' => esc_html__( 'Square', 'loftocean' ),
				'cs-form-rounded' => esc_html__( 'Rounded', 'loftocean' ),
				'cs-form-pill' => esc_html__( 'Pill', 'loftocean' ),
				'cs-form-underline' => esc_html__( 'Underline', 'loftocean' ),
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

        if ( ! empty( $settings[ 'form_id' ] ) ) :
            ob_start();
            mc4wp_show_form( $settings[ 'form_id' ] );
            $html = ob_get_clean();
            echo str_replace( ' class="mc4wp-form ', sprintf( ' class="mc4wp-form cs-signup signup-style-1 %s ', $settings[ 'form_field_style' ] ), $html );
        elseif ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) : ?>
			<div class="cs-notice"><?php esc_html_e( 'Please choose a form on the widget setting panel.', 'loftocean' ); ?></div><?php
		endif;
	}
    /**
    * Get all contact form 7 forms
    */
    protected function get_forms() {
        $args = array( 'post_type' => 'mc4wp-form', 'posts_per_page' => -1 );
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
		$settings[ 'settings' ][ 'form_id' ] = $this->get_default_form_id();
		return $settings;
	}
}
