<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Mini Cart Button
 */
class Widget_Mini_Cart extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceanminicart', array( 'id' => 'mini-cart' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Mini Cart', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-cart';
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
		return array( 'mini cart', 'cart' );
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
			'selectors' => array( '{{WRAPPER}} .cart-icon:before' => 'font-size: {{SIZE}}px;' )
		) );

		$this->start_controls_tabs( 'tabs_color_style' );
        $this->start_controls_tab( 'tab_color_normal', array(
        	'label' => esc_html__( 'Normal', 'loftocean' )
        ) );
		$this->add_control( 'icom_color', array(
			'label' => esc_html__( 'Icon Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
			'separator' => 'after',
			'selectors' => array(
				'{{WRAPPER}} .cs-mini-cart .cart-contents' => 'color: {{VALUE}};',
			)
		) );
        $this->end_controls_tab();

        $this->start_controls_tab( 'tab_color_hover', array(
            'label' => esc_html__( 'Hover', 'loftocean' )
        ) );
		$this->add_control( 'icon_hover_color', array(
			'label' => esc_html__( 'Icon Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
			'separator' => 'after',
			'selectors' => array(
				'{{WRAPPER}} .cs-mini-cart .cart-contents:hover' => 'color: {{VALUE}};',
			)
		) );
        $this->end_controls_tab();
    	$this->end_controls_tabs();

        $this->add_control( 'dropdown_color_scheme', array(
            'label' => esc_html__( 'Drop Down Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'dropdown-dark',
            'options' => array(
                'dropdown-light' => esc_html__( 'Light', 'loftocean' ),
                'dropdown-dark' => esc_html__( 'Dark', 'loftocean' ),
            )
        ) );
		$this->add_control( 'item_indicator', array(
			'label' => esc_html__( 'Item Indicator', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '',
            'options' => array(
                '' => esc_html__( 'None', 'loftocean' ),
                'bubble' => esc_html__( 'Bubble', 'loftocean' ),
            )
		) );
		$this->add_control( 'bubble_description', array(
			'type' => \Elementor\Controls_Manager::RAW_HTML,
			'condition' => array( 'item_indicator' => 'bubble' ),
			'raw' => '<span class="description">' . esc_html__( 'The preview shows the default placeholder character "2" while editing.', 'loftocean' ) . '</span>'
		) );
		$this->add_control( 'hide_empty', array(
            'label' => esc_html__( 'Hide indicator if cart is empty ', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on',
			'condition' => array( 'item_indicator' => array( 'bubble' ) ),
			'selectors' => array(
				'{{WRAPPER}} .cs-mini-cart.hide-empty .cart-count.empty' => 'display: none;',
			)
        ) );
		$this->end_controls_section();

		$this->start_controls_section( 'indicator_style_section', array(
			'label' => __( 'Item Indicator', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			'condition' => array( 'item_indicator' => array( 'bubble' ) )
		) );
        $this->add_control( 'indicator_background_color', array(
			'label' => esc_html__( 'Background Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
			'selectors' => array(
				'{{WRAPPER}} .cs-mini-cart' => '--item-indicator-bg: {{VALUE}};',
			)
		) );
        $this->add_control( 'indicator_text_color', array(
            'label' => esc_html__( 'Text Color', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::COLOR,
			'default' => '',
			'selectors' => array(
				'{{WRAPPER}} .cs-mini-cart' => '--item-indicator-color: {{VALUE}};',
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
        $this->add_render_attribute( 'wrapper', 'class', array( 'cs-mini-cart', $settings[ 'dropdown_color_scheme' ] ) );
		$is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();
		if ( ( 'bubble' == $settings[ 'item_indicator' ] ) && ( 'on' == $settings[ 'hide_empty' ] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'hide-empty' );
		}

        $cart_url = function_exists( '\wc_get_cart_url' ) ? \wc_get_cart_url() : \WC()->cart->get_cart_url(); ?>
        <div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
            <a class="cart-contents" href="<?php echo esc_url( $cart_url ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'loftocean' ); ?>">
				<span class="cart-icon"></span><?php
				if ( 'bubble' == $settings[ 'item_indicator' ] ) :
					$item_count = $is_edit_mode ? 2 : WC()->cart->get_cart_contents_count();
					$this->add_render_attribute( 'indicator', 'class', array( 'cart-count', 'loftocean-woocommerce-cart-item-indicator' ) );
					( 'on' == $settings[ 'hide_empty' ] && empty( $item_count ) ) ? $this->add_render_attribute( 'indicator', 'class', 'empty' ) : ''; ?>
					<span <?php $this->print_render_attribute_string( 'indicator' ); ?>><?php echo $item_count; ?></span><?php
				endif; ?>
			</a><?php
            if ( ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) :
				$wc_cart = \WC()->cart;
				if ( isset( $wc_cart ) ) : ?>
	                <div class="widget woocommerce widget_shopping_cart">
	                    <div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
	                </div><?php
				endif;
            endif; ?>
        </div><?php
    }
    /**
	* Render button widget output in the editor.
	* Written as a Backbone JavaScript template and used to generate the live preview.
	* @access protected
	*/
	protected function content_template() {
        $cart_url = function_exists( '\wc_get_cart_url' ) ? \wc_get_cart_url() : \WC()->cart->get_cart_url(); ?><#
        view.addRenderAttribute( 'wrapper', 'class', [ 'cs-mini-cart', settings[ 'dropdown_color_scheme' ] ] ); #>
        <div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
            <a class="cart-contents" href="<?php echo esc_url( $cart_url ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'loftocean' ); ?>">
				<span class="cart-icon"></span><#
				if ( 'bubble' == settings[ 'item_indicator' ] ) {
					view.addRenderAttribute( 'indicator', 'class', 'cart-count' ); #>
					<span {{{ view.getRenderAttributeString( 'indicator' ) }}}>2</span><#
				} #>
			</a>
        </div><?php
    }
}
