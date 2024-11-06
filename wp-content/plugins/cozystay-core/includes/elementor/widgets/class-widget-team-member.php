<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Team Member
 */
class Widget_Team_Member extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceanteammember', array( 'id' => 'team-member' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Team Member', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-facebook-comments';
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
		return array( 'team member', 'team', 'member' );
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
        $this->add_control( 'image', array(
			'label' => esc_html__( 'Choose Image', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::MEDIA,
			'default' => array()
		) );
        $this->add_group_control( \Elementor\Group_Control_Image_Size::get_type(), array(
			'name' => 'image',
			'default' => 'full',
			'separator' => 'none'
		) );
        $this->add_control( 'position', array(
			'label' => esc_html__( 'Position', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'Chef', 'loftocean' ),
            'placeholder' => esc_html__( 'Position', 'loftocean' )
		) );
        $this->add_control( 'name', array(
			'label' => esc_html__( 'Name', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'James Fisher', 'loftocean' )
		) );
        $this->add_control( 'text', array(
			'label' => esc_html__( 'Text', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXTAREA
		) );

        $repeater = new \Elementor\Repeater();
		$repeater->add_control( 'social_title',array(
			'label' => esc_html__( 'Name', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'label_block' => true,
			'default' => esc_html__( 'Facebook', 'loftocean' )
		) );
		$repeater->add_control( 'social_link', array(
			'type' => \Elementor\Controls_Manager::URL,
			'default' => array( 'url' => '' ),
			'label' => esc_html__( 'Link', 'loftocean' ),
            'placeholder' => __( 'Enter the URL', 'loftocean' ),
		) );
		$this->add_control( 'social_links', array(
			'label' => esc_html__( 'Social Links', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::REPEATER,
			'fields' => $repeater->get_controls(),
			'default' => array(
                array( 'social_title' => esc_html__( 'Facebook', 'loftocean' ), 'social_link' => array( 'url' => 'https://facebook.com' ) ),
                array( 'social_title' => esc_html__( 'Twitter', 'loftocean' ), 'social_link' => array( 'url' => 'https://twitter.com' ) ),
                array( 'social_title' => esc_html__( 'YouTube', 'loftocean' ), 'social_link' => array( 'url' => 'https://youtube.com' ) )
            ),
            'title_field' => '{{{ social_title }}}',
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'general_style_section', array(
            'label' => esc_html__( 'General', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'layout', array(
            'label' => esc_html__( 'Layout', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => array(
                '' => esc_html__( 'Default', 'loftocean' ),
                'style-overlay' => esc_html__( 'Overlay', 'loftocean' ),
            )
        ) );
		$this->add_responsive_control( 'text_alignment', array(
            'label'	=> esc_html__( 'Text Alignment', 'loftocean' ),
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
            'default' => 'text-center',
        ) );
        $this->end_controls_section();

        $this->start_controls_section( 'position_style_section', array(
			'label' => esc_html__( 'Position', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'position_preset_color', array(
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
		$this->add_control( 'position_custom_color', array(
			'label' => esc_html__( 'Custom Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
			'condition' => array( 'position_preset_color[value]' => 'custom' ),
			'selectors' => array(
				'{{WRAPPER}} .cs-subtitle.cs-team-position' => 'color: {{VALUE}};',
			)
		) );
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'position_typography',
				'label'    => esc_html__( 'Typography', 'loftocean' ),
				'selector' => '{{WRAPPER}} .cs-subtitle.cs-team-position',
			)
		);
		$this->end_controls_section();

        $this->start_controls_section( 'name_style_section', array(
            'label' => esc_html__( 'Name', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'name_tag', array(
            'label' => esc_html__( 'HTML Tag', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'h5',
            'options' => array(
                'h1' => esc_html__( 'H1', 'loftocean' ),
                'h2' => esc_html__( 'H2', 'loftocean' ),
                'h3' => esc_html__( 'H3', 'loftocean' ),
                'h4' => esc_html__( 'H4', 'loftocean' ),
                'h5' => esc_html__( 'H5', 'loftocean' ),
                'h6' => esc_html__( 'H6', 'loftocean' ),
            )
        ) );
		$this->add_control( 'name_preset_color', array(
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
        $this->add_control( 'name_custom_color', array(
            'label' => esc_html__( 'Custom Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'condition' => array( 'name_preset_color[value]' => 'custom' ),
            'selectors' => array(
                '{{WRAPPER}} .cs-title.cs-team-name' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'name_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .cs-title.cs-team-name',
            )
        );
		$this->end_controls_section();

		$this->start_controls_section( 'text_style_section', array(
			'label' => esc_html__( 'Text', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'text_preset_color', array(
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
		$this->add_control( 'text_custom_color', array(
			'label' => esc_html__( 'Custom Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
			'condition' => array( 'text_preset_color[value]' => 'custom' ),
			'selectors' => array(
				'{{WRAPPER}} .cs-team-text' => 'color: {{VALUE}};',
			)
		) );
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'label'    => esc_html__( 'Typography', 'loftocean' ),
				'selector' => '{{WRAPPER}} .cs-team-text',
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

		$this->add_render_attribute( array(
			'wrapper' => array( 'class' => array( 'cs-team' ) ),
			'position' => array( 'class' => array( 'cs-subtitle', 'cs-team-position' ) ),
			'name' => array( 'class' => array( 'cs-title', 'cs-team-name' ) ),
			'text' => array( 'class' => array( 'cs-team-text' ) )
		) );
		$this->add_inline_editing_attributes( 'position', 'none' );
		$this->add_inline_editing_attributes( 'name', 'none' );
		$this->add_inline_editing_attributes( 'text', 'none' );

		$name_tag = $settings[ 'name_tag' ];
		if ( ! empty( $settings[ 'layout' ] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', $settings[ 'layout' ] );
		}
		$color_settings = array( 'position', 'name', 'text' );
		foreach( $color_settings as $element ) {
			$id = $element . '_preset_color';
			if ( ! empty( $settings[ $id ] ) && ( 'custom' != $settings[ $id ] ) ) {
				$this->add_render_attribute( $element, 'class', $settings[ $id ] );
			}
		}
        $alignment = array( 'text_alignment' => '', 'text_alignment_mobile' => '-mobile', 'text_alignment_tablet' => '-tablet' );
		foreach( $alignment as $align => $after ) {
			if ( ! empty( $settings[ $align ] ) ) {
				$this->add_render_attribute( 'wrapper', 'class', $settings[ $align ] . $after );
			}
		} ?>

        <div <?php $this->print_render_attribute_string( 'wrapper' ); ?>><?php
            if ( ! empty( $settings[ 'image' ][ 'id' ] ) ) : ?>
                <div class="cs-team-photo">
                    <?php \Elementor\Group_Control_Image_Size::print_attachment_image_html( $settings, 'image', 'image' ); ?>
                </div><?php
            endif; ?>
            <div class="cs-team-info"><?php
            if ( ! empty( $settings[ 'position' ] ) ) : ?>
                <span <?php $this->print_render_attribute_string( 'position' ); ?>><?php $this->print_unescaped_setting( 'position' ); ?></span><?php
            endif;
            if ( ! empty( $settings[ 'name' ] ) ) : ?>
                <<?php echo esc_attr( $name_tag ); ?> <?php $this->print_render_attribute_string( 'name' ); ?>>
                    <?php $this->print_unescaped_setting( 'name' ); ?>
                </<?php echo esc_attr( $name_tag ); ?>><?php
            endif;
            if ( ! empty( $settings[ 'text' ] ) ) : ?>
                <div <?php $this->print_render_attribute_string( 'text' ); ?>>
                    <?php $this->print_unescaped_setting( 'text' ); ?>
                </div><?php
            endif;
            if ( \LoftOcean\is_valid_array( $settings[ 'social_links' ] ) ) : ?>
                <div class="cs-team-social">
                    <ul class="social-nav menu"><?php
                    foreach( $settings[ 'social_links' ] as $index => $item ) :
                        $has_link = false; ?>
                        <li><?php
                            if ( ! empty( $item[ 'social_link' ] ) ) :
                                $has_link = true;
                                $this->add_link_attributes( 'social_link_' . $index, $item[ 'social_link' ] ); ?>
                                <a <?php $this->print_render_attribute_string( 'social_link_' . $index ); ?>><?php
                            endif;
                            $this->print_unescaped_setting( 'social_title', 'social_links', $index );
                            if ( $has_link ) : ?>
                                </a><?php
                            endif; ?>
                        </li><?php
                    endforeach; ?>
                    </ul>
                </div><?php
            endif; ?>
            </div>
        </div><?php
    }
    /**
	* Render button widget output in the editor.
	* Written as a Backbone JavaScript template and used to generate the live preview.
	* @access protected
	*/
	protected function content_template() { ?>
        <#
        view.addRenderAttribute( 'wrapper', 'class', [ 'cs-team' ] );
        view.addRenderAttribute( 'position', 'class', [ 'cs-subtitle', 'cs-team-position' ] );
		view.addInlineEditingAttributes( 'position', 'none' );
        view.addRenderAttribute( 'name', 'class', [ 'cs-title', 'cs-team-name' ] );
		view.addInlineEditingAttributes( 'name', 'none' );
        view.addRenderAttribute( 'text', 'class', [ 'cs-team-text' ] );
		view.addInlineEditingAttributes( 'text', 'none' );
        var nameTag = settings[ 'name_tag' ];
        if ( settings[ 'layout' ] ) {
            view.addRenderAttribute( 'wrapper', 'class', settings[ 'layout' ] );
        }
        [ 'position', 'name', 'text' ].forEach( function( element ) {
            var id = element + '_preset_color';
            if ( settings[ id ] && ( 'custom' != settings[ id ] ) ) {
                view.addRenderAttribute( element, 'class', settings[ id ] );
            }
        } );
        var alignment = { 'text_alignment':'', 'text_alignment_mobile': '-mobile', 'text_alignment_tablet': '-tablet' };
        jQuery.each( alignment, function( align, after ) {
            if ( settings[ align ] ) {
                view.addRenderAttribute( 'wrapper', 'class', settings[ align ] + after );
            }
        } ); #>

        <div {{{ view.getRenderAttributeString( 'wrapper' ) }}}><#
            if ( settings[ 'image' ][ 'id' ] ) {
                var image = {
                    id: settings.image.id,
                    url: settings.image.url,
                    size: settings.image_size,
                    dimension: settings.image_custom_dimension,
                    model: view.getEditModel()
                };
                var imageURL = elementor.imagesManager.getImageUrl( image );
                if ( imageURL ) { #>
                    <div class="cs-team-photo">
                        <img src="{{ imageURL }}">
                    </div><#
                }
            } #>
            <div class="cs-team-info"><#
            if ( settings[ 'position' ] ) { #>
                <span {{{ view.getRenderAttributeString( 'position' ) }}}>{{{ settings[ 'position' ] }}}</span><#
            }
            if ( settings[ 'name' ] ) { #>
                <{{{ nameTag }}} {{{ view.getRenderAttributeString( 'name' ) }}}>
                    {{{ settings.name }}}
                </{{{ nameTag }}}><#
            }
            if ( settings[ 'text' ] ) { #>
                <div {{{ view.getRenderAttributeString( 'text' ) }}}>
                    {{{ settings.text }}}
                </div><#
            }
            if ( settings[ 'social_links' ] ) { #>
                <div class="cs-team-social">
                    <ul class="social-nav menu"><#
                    settings[ 'social_links' ].forEach( function( item, index ) {
                        var hasLink = false; #>
                        <li><#
                            if ( item[ 'social_link' ] ) {
                                hasLink = true; #>
                                <a href="{{ item[ 'social_link' ][ 'url' ] }}"><#
                            } #>
                            {{{ item[ 'social_title' ] }}}<#
                            if ( hasLink ) { #>
                                </a><#
                            } #>
                        </li><#
                    } ); #>
                    </ul>
                </div><#
            } #>
            </div>
        </div><?php
	}
}
