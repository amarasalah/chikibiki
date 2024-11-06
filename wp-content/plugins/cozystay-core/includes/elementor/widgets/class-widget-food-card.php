<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Food Card
 */
class Widget_Food_Card extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceanfoodcard', array( 'id' => 'food-card' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Food Card', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-image-box';
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
		return [ 'food card', 'food' ];
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
        $this->add_control( 'title', array(
			'label' => esc_html__( 'Title', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => esc_html__( 'Margherita', 'loftocean' ),
            'placeholder' => esc_html__( 'Title', 'loftocean' )
		) );
        $this->add_control( 'content', array(
			'label' => esc_html__( 'Text', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXTAREA,
			'default' => esc_html__( 'Tomato sauce, Mozzarella cheese, oregano and fresh basil.', 'loftocean' )
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'extra_content_section', array(
            'label' => __( 'Extra', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT
        ) );
        $repeater = new \Elementor\Repeater();
		$repeater->add_control( 'extra_label',array(
			'label' => esc_html__( 'Label', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'label_block' => true
		) );
		$repeater->add_control( 'extra_content',array(
			'label' => esc_html__( 'Content', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'label_block' => true
		) );
		$this->add_control( 'extra_list', array(
			'label' => esc_html__( 'Extra Items', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::REPEATER,
			'fields' => $repeater->get_controls(),
			'default' => array(
                array( 'extra_label' => esc_html__( 'Regular Size', 'loftocean' ), 'extra_content' =>  '$29' )
            ),
            'title_field' => '{{{ extra_label }}}',
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'general_style_section', array(
            'label' => esc_html__( 'General', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'reverse_column', array(
            'label' => esc_html__( 'Reverse Columns', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->end_controls_section();

        $this->start_controls_section( 'title_style_section', array(
            'label' => esc_html__( 'Title', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'title_tag', array(
            'label' => esc_html__( 'HTML Tag', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'h4',
            'options' => array(
                'h1' => esc_html__( 'H1', 'loftocean' ),
                'h2' => esc_html__( 'H2', 'loftocean' ),
                'h3' => esc_html__( 'H3', 'loftocean' ),
                'h4' => esc_html__( 'H4', 'loftocean' ),
                'h5' => esc_html__( 'H5', 'loftocean' ),
                'h6' => esc_html__( 'H6', 'loftocean' ),
            )
        ) );
		$this->add_control( 'title_preset_color', array(
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
        $this->add_control( 'title_custom_color', array(
            'label' => esc_html__( 'Custom Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'condition' => array( 'title_preset_color[value]' => 'custom' ),
            'selectors' => array(
                '{{WRAPPER}} .cs-fc-info-title' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .cs-fc-info-title',
            )
        );
		$this->end_controls_section();

		$this->start_controls_section( 'content_style_section', array(
			'label' => esc_html__( 'Text', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'content_preset_color', array(
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
		$this->add_control( 'content_custom_color', array(
			'label' => esc_html__( 'Custom Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
			'condition' => array( 'content_preset_color[value]' => 'custom' ),
			'selectors' => array(
				'{{WRAPPER}} .cs-fc-info-text' => 'color: {{VALUE}};',
			)
		) );
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'content_typography',
				'label'    => esc_html__( 'Typography', 'loftocean' ),
				'selector' => '{{WRAPPER}} .cs-fc-info-text',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'extra_label_style_section', array(
			'label' => esc_html__( 'Extra Label', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'extra_label_preset_color', array(
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
		$this->add_control( 'extra_label_custom_color', array(
			'label' => esc_html__( 'Custom Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
			'condition' => array( 'extra_label_preset_color[value]' => 'custom' ),
			'selectors' => array(
				'{{WRAPPER}} .cs-fc-info-extra .info-label' => 'color: {{VALUE}};',
			)
		) );
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'extra_label_typography',
				'label'    => esc_html__( 'Typography', 'loftocean' ),
				'selector' => '{{WRAPPER}} .cs-fc-info-extra .info-label',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'extra_content_style_section', array(
			'label' => esc_html__( 'Extra Content', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
		) );
		$this->add_control( 'extra_content_preset_color', array(
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
		$this->add_control( 'extra_content_custom_color', array(
			'label' => esc_html__( 'Custom Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
			'condition' => array( 'extra_content_preset_color[value]' => 'custom' ),
			'selectors' => array(
				'{{WRAPPER}} .cs-fc-info-extra .info-content' => 'color: {{VALUE}};',
			)
		) );
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'extra_content_typography',
				'label'    => esc_html__( 'Typography', 'loftocean' ),
				'selector' => '{{WRAPPER}} .cs-fc-info-extra .info-content',
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
			'wrapper' => array( 'class' => array( 'cs-food-card' ) ),
			'title' => array( 'class' => array( 'cs-fc-info-title' ) ),
			'content' => array( 'class' => array( 'cs-fc-info-text' ) )
		) );
		$this->add_inline_editing_attributes( 'title', 'none' );
		$this->add_inline_editing_attributes( 'content', 'none' );
		$title_tag = $settings[ 'title_tag' ];
		if ( 'on' == $settings[ 'reverse_column' ] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'column-reverse' );
		}
		$color_settings = array( 'title', 'content', 'extra_label', 'extra_content' );
		foreach( $color_settings as $element ) {
			$id = $element . '_preset_color';
			if ( ! empty( $settings[ $id ] ) && ( 'custom' != $settings[ $id ] ) ) {
				$this->add_render_attribute( $element, 'class', $settings[ $id ] );
			}
		} ?>

		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>><?php
			if ( ! empty( $settings[ 'image' ][ 'id' ] ) ) : ?>
	            <div class="cs-fc-img"><?php \Elementor\Group_Control_Image_Size::print_attachment_image_html( $settings, 'image', 'image' ); ?></div><?php
			endif; ?>
            <div class="cs-fc-info">
                <div class="cs-fc-info-wrap"><?php
				if ( ! empty( $settings[ 'title' ] ) ) : ?>
                    <<?php echo esc_attr( $title_tag ); ?> <?php $this->print_render_attribute_string( 'title' ); ?>><?php $this->print_unescaped_setting( 'title' ); ?></<?php echo esc_attr( $title_tag ); ?>><?php
				endif;
				if ( ! empty( $settings[ 'content' ] ) ) : ?>
                    <div <?php $this->print_render_attribute_string( 'content' ); ?>><p><?php $this->print_unescaped_setting( 'content' ); ?></p></div><?php
				endif;
				if ( ! empty( $settings[ 'extra_list' ] ) ) :
					foreach ( $settings[ 'extra_list' ] as $index => $item ) :
						$label_key = 'extra_list.' . $index . '.extra_label';
						$content_key = 'extra_list.' . $index . '.extra_content';
						$this->add_render_attribute( $label_key, 'class', 'info-label' );
						$this->add_inline_editing_attributes( $label_key, 'none' );
						$this->add_render_attribute( $content_key, 'class', 'info-content' );
						$this->add_inline_editing_attributes( $content_key, 'none' ); ?>
	                    <div class="cs-fc-info-extra"><?php
						if ( ! empty( $item[ 'extra_label' ] ) ) : ?>
							<span <?php $this->print_render_attribute_string( $label_key ); ?>><?php $this->print_unescaped_setting( 'extra_label', 'extra_list', $index ); ?></span><?php
						endif;
						if ( ! empty( $item[ 'extra_content' ] ) ) : ?>
	                        <span <?php $this->print_render_attribute_string( $content_key ); ?>><?php $this->print_unescaped_setting( 'extra_content', 'extra_list', $index ); ?></span><?php
						endif; ?>
						</div><?php
					endforeach;
				endif; ?>
                </div>
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
		view.addRenderAttribute( 'wrapper', 'class', [ 'cs-food-card' ] );
		view.addRenderAttribute( 'title', 'class', [ 'cs-fc-info-title' ] );
		view.addRenderAttribute( 'content', 'class', [ 'cs-fc-info-text' ] );

		view.addInlineEditingAttributes( 'title', 'none' );
		view.addInlineEditingAttributes( 'content', 'none' );

		var titleTag = settings[ 'title_tag' ];
		if ( 'on' == settings[ 'reverse_column' ] ) {
			view.addRenderAttribute( 'wrapper', 'class', 'column-reverse' );
		}
		[ 'title', 'content', 'extra_label', 'extra_content' ].forEach( function( element ) {
			var id = element + '_preset_color';
			if ( settings[ id ] && ( 'custom' != settings[ id ] ) ) {
				view.addRenderAttribute( element, 'class', settings[ id ] );
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
					<div class="cs-fc-img">
						<img src="{{ imageURL }}">
					</div><#
				}
			} #>
            <div class="cs-fc-info">
                <div class="cs-fc-info-wrap"><#
				if ( settings[ 'title' ] ) { #>
                    <{{{ titleTag }}} {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ settings.title }}}</{{{ titleTag }}}><#
				}
				if ( settings[ 'content' ] ) { #>
                    <div {{{ view.getRenderAttributeString( 'content' ) }}}><p>{{{ settings.content }}}</p></div><#
				}
				if ( settings[ 'extra_list' ] ) {
					settings[ 'extra_list' ].forEach( function( item, index ) {
						var labelKey = 'extra_list.' + index + '.extra_label';
							contentKkey = 'extra_list.' + index + '.extra_content';
						view.addRenderAttribute( labelKey, 'class', [ 'info-label'  ] );
						view.addInlineEditingAttributes( labelKey, 'none' );
						view.addRenderAttribute( contentKkey, 'class', [ 'info-content' ] );
						view.addInlineEditingAttributes( contentKkey, 'none' ); #>
	                    <div class="cs-fc-info-extra"><#
						if ( item[ 'extra_label' ] ) { #>
							<span {{{ view.getRenderAttributeString( labelKey ) }}}>{{{ item[ 'extra_label' ] }}}</span><#
						}
						if ( item[ 'extra_content' ] ) { #>
	                        <span {{{ view.getRenderAttributeString( contentKkey ) }}}>{{{ item[ 'extra_content' ] }}}</span><#
						} #>
						</div><#
					} );
				} #>
                </div>
            </div>
        </div><?php
	}
}
