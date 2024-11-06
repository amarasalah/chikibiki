<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Site Logo.
 */
class Widget_Site_Logo extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceansitelogo', array( 'id' => 'site-logo' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Site Logo', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-image';
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
		return [ 'site logo', 'logo' ];
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
        $this->add_control( 'enable_custom_logo', array(
            'label' => esc_html__( 'Custom Image', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'image', array(
            'label' => esc_html__( 'Choose Image', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::MEDIA,
            'condition' => array( 'enable_custom_logo[value]' => 'on' )
        ) );
        $this->add_group_control( \Elementor\Group_Control_Image_Size::get_type(), array(
			'name' => 'image',
			'default' => 'thumbnail',
			'separator' => 'none'
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
                )
            ),
            'prefix_class' => 'elementor%s-align-'
        ) );
        $this->add_control( 'link', array(
            'label' => esc_html__( 'Link', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'home',
            'options' => array(
                'home' => esc_html__( 'Default', 'loftocean' ),
                'none' => esc_html__( 'None', 'loftocean' ),
                'custom' => esc_html__( 'Custom URL', 'loftocean' )
            )
        ) );
		$this->add_control( 'custom_link', array(
			'type' => \Elementor\Controls_Manager::URL,
			'default' => array( 'url' => '#' ),
			'label' => esc_html__( 'Link', 'loftocean' ),
            'placeholder' => __( 'Enter the URL', 'loftocean' ),
            'condition' => array( 'link[value]' => 'custom' )
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
			'selectors' => array( '{{WRAPPER}} img' => 'width: {{SIZE}}{{UNIT}};' )
		) );
		$this->end_controls_section();
	}
	/**
	* Written in PHP and used to generate the final HTML.
    * @access protected
	*/
	protected function render() {
        $settings = $this->get_settings_for_display();
		$show_notice = true;
        $has_link = false;

        if (  'home' == $settings[ 'link' ] ) {
            $has_link = true;
            $this->add_link_attributes( 'link', array( 'url' => home_url( '/' ) ) );
        } else if ( 'custom' == $settings[ 'link' ] && ! empty( $settings[ 'custom_link' ][ 'url' ] ) ) {
            $has_link = true;
            $this->add_link_attributes( 'link', $settings[ 'custom_link' ] );
        }

        if ( 'on' == $settings[ 'enable_custom_logo' ] ) :
            if ( \LoftOcean\media_exists( $settings[ 'image' ][ 'id' ] ) ) :
				$show_notice = false; ?>
                <?php if ( $has_link ) : ?><a <?php $this->print_render_attribute_string( 'link' ); ?> aria-label="<?php esc_attr_e( 'Site Logo Image', 'loftocean' ); ?>"><?php endif; ?>
                    <?php \Elementor\Group_Control_Image_Size::print_attachment_image_html( $settings, 'image', 'image' ); ?>
                <?php if ( $has_link ) : ?></a><?php endif;
            endif;
        else :
            $custom_logo_id = get_theme_mod( 'custom_logo' );
            if ( $custom_logo_id && \LoftOcean\media_exists( $custom_logo_id ) ) :
				$show_notice = false;
                $settings[ 'current_image' ] = array( 'id' => $custom_logo_id ); ?>
                <?php if ( $has_link ) : ?><a <?php $this->print_render_attribute_string( 'link' ); ?> aria-label="<?php esc_attr_e( 'Site Logo Image', 'loftocean' ); ?>"><?php endif; ?>
                    <?php \Elementor\Group_Control_Image_Size::print_attachment_image_html( $settings, 'image', 'current_image' ); ?>
                <?php if ( $has_link ) : ?></a><?php endif;
            endif;
        endif;

		if ( $show_notice && \Elementor\Plugin::$instance->editor->is_edit_mode() ) : ?>
			<div class="cs-notice">
				<?php printf(
					// translators: 1/2. html tag
					esc_html__( 'Please go to %1$sWordPress Customizer > Site Identity > Logo%2$s to choose a logo image.', 'loftocean' ),
					'<strong>',
					'</strong>'
				); ?><br>
				<?php esc_html_e( 'Or enable "Custom Image" and choose an image on the widget setting panel.', 'loftocean' ); ?>
			</div><?php
		endif;
	}
    /**
	* Render button widget output in the editor.
	* Written as a Backbone JavaScript template and used to generate the live preview.
	* @access protected
	*/
	protected function content_template() {
        $logo_image_id = get_theme_mod( 'custom_logo' ); ?>
        <#
        var hasLink = false, link = '', homeURL = <?php echo json_encode( home_url( '/' ) ); ?>,
            showNotice = true, defaultLogoID = <?php echo json_encode( $logo_image_id ); ?>,
            defaultLogoURL = <?php echo json_encode( \LoftOcean\get_image_src( $logo_image_id, array( 180, 99999 ) ) ); ?>;

        if (  'home' == settings[ 'link' ] ) {
            hasLink = true;
            link = homeURL;
        } else if ( 'custom' == settings[ 'link' ] && settings[ 'custom_link' ][ 'url' ] ) {
            hasLink = true;
            link = settings[ 'custom_link' ][ 'url' ];
        }

        if ( 'on' == settings[ 'enable_custom_logo' ] ) {
            var imageSettings = {
                id: settings.image.id,
				url: settings.image.url,
				size: settings.image_size,
				dimension: settings.image_custom_dimension,
				model: view.getEditModel()
			};
			var imageURL = elementor.imagesManager.getImageUrl( imageSettings );
            if ( imageURL ) {
				showNotice = false;
                if ( hasLink ) { #><a href="{{ link }}"><# } #>
                    <img src="{{ imageURL }}"><#
                if ( hasLink ) { #></a><# }
            }
        } else {
            var image = {
				id: defaultLogoID,
				url: defaultLogoURL,
				size: settings.image_size,
				dimension: settings.image_custom_dimension,
				model: view.getEditModel()
			};
			var imageURL = elementor.imagesManager.getImageUrl( image );
            if ( imageURL ) {
				showNotice = false;
                if ( hasLink ) { #><a href="{{ link }}"><# } #>
                    <img src="{{ imageURL }}"><#
                if ( hasLink ) { #></a><# }
            }
        }

		if ( showNotice ) { #>
			<div class="cs-notice">
				<?php printf(
					esc_html__( 'Please go to %1$sWordPress Customizer > Site Identity > Logo%2$s to choose a logo image.', 'loftocean' ),
					'<strong>',
					'</strong>'
				); ?><br>
				<?php esc_html_e( 'Or enable "Custom Image" and choose an image on the widget setting panel.', 'loftocean' ); ?>
			</div><#
		} #><?php
	}
}
