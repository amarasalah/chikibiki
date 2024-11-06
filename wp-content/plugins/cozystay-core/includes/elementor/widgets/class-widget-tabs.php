<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Tabs
 */
class Widget_Tabs extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceantabs', array( 'id' => 'tabs' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Tabs', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-tabs';
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
		return [ 'tabs', 'tab' ];
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
    * Helper function get custom block
    */
    protected function get_custom_block() {
        return apply_filters( 'loftocean_get_custom_post_type_list', array(), 'custom_blocks' );
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

        $repeater = new \Elementor\Repeater();
        $repeater->add_control( 'title', array(
            'label'   => esc_html__( 'Title', 'loftocean' ),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__( 'Tab', 'loftocean' )
        ) );
        $repeater->add_control( 'content_type', array(
            'label'	=> esc_html__( 'Content Type', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'editor',
            'options' => array(
				'editor' => esc_html__( 'Text', 'loftocean' ),
				'custom' => esc_html__( 'Custom Block', 'loftocean' ),
			)
		) );
        $repeater->add_control( 'text', array(
            'label'   => esc_html__( 'Text', 'loftocean' ),
            'type'    => \Elementor\Controls_Manager::WYSIWYG,
            'condition' => array( 'content_type[value]' => 'editor' ),
            'default' => '<p>' . esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.', 'loftocean' ) . '</p>'
        ) );
        $repeater->add_control( 'custom_block', array(
            'label'	=> esc_html__( 'Custom Block', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '0',
            'condition' => array( 'content_type[value]' => 'custom' ),
            'options' => $this->get_custom_block()
		) );
		$repeater->add_control( 'unique_id', array(
            'label'   => esc_html__( 'Unique ID', 'loftocean' ),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'description' => esc_html__( 'Note: The unique ID ONLY accepts these chars: `A-Z, a-z, 0-9, _ , -`', 'loftocean' )
        ) );
        $repeater->add_control( 'auto_scroll', array(
            'label'	=> esc_html__( 'Auto Scroll to The Active Tab', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'off',
			'label_on' => 'on',
			'label_off' => 'off',
			'return_value' => 'on',
            'condition' => array( 'unique_id[value]!' => '' )
		) );
		$this->add_control( 'tabs', array(
			'label' => esc_html__( 'Tabs', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::REPEATER,
			'fields' => $repeater->get_controls(),
			'default' => array(
                array( 'title' => esc_html__( 'Tab #1', 'loftocean' ) ),
                array( 'title' => esc_html__( 'Tab #2', 'loftocean' ) )
            ),
            'title_field' => '{{{ title }}}',
		) );
        $this->end_controls_section();

        $this->start_controls_section( 'title_style_section', array(
            'label' => __( 'Title', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .elementor-tabs-wrapper .elementor-tab-title',
            )
        );
        $this->end_controls_section();

        $this->start_controls_section( 'content_style_section', array(
            'label' => __( 'Content', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE
        ) );
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name'     => 'content_typography',
                'label'    => esc_html__( 'Typography', 'loftocean' ),
                'selector' => '{{WRAPPER}} .elementor-tabs-content-wrapper .elementor-tab-content',
            )
        );
        $this->end_controls_section();
	}
    /**
    * Helper function to print the custom block content
    */
    protected function print_custom_block( $block ) {
        if ( ! empty( $block ) ) {
            do_action( 'loftocean_the_custom_blocks_content', $block );
        }
    }
	/**
	* Written in PHP and used to generate the final HTML.
    * @access protected
	*/
	protected function render() {
        $settings = $this->get_settings_for_display();
        if ( \LoftOcean\is_valid_array( $settings[ 'tabs' ] ) ) :
            $widgetID = $this->get_id(); ?>
            <div class="cs-tabs">
                <div class="elementor-tabs-wrapper" role="tablist"><?php
                foreach( $settings[ 'tabs' ] as $index => $tab ) :
                    $element_title = 'tab_title_' . $widgetID . '_' . $index;
                    $element_link = 'tab_link_' . $widgetID . '_' . $index;
					$inline_title = 'tabs.' . $index . '.title';
                    $this->add_render_attribute( array(
                        $element_title => array( 'class' => array( 'elementor-tab-title', 'elementor-tab-desktop-title' ) ),
                        $inline_title => array( 'href' => sprintf( '#elementor-tab-%1$s-%2$s', $widgetID, $index ), 'class' => 'tab-title-link' )
                    ) );
					$this->add_inline_editing_attributes( $inline_title, 'none' );
					if ( ! empty( $tab[ 'unique_id' ] ) ) {
						$this->add_render_attribute( $inline_title, 'data-id', $tab[ 'unique_id' ] );
						if ( ! empty( $tab[ 'auto_scroll' ] ) && ( 'on' == $tab[ 'auto_scroll' ] ) ) {
							$this->add_render_attribute( $inline_title, 'data-auto-scroll', 'on' );
						}
					}
                    if ( empty( $index ) ) {
                        $this->add_render_attribute( $element_title, 'class', 'elementor-active' );
                    } ?>
                    <div <?php $this->print_render_attribute_string( $element_title ); ?>>
                        <a <?php $this->print_render_attribute_string( $inline_title ); ?>><?php $this->print_unescaped_setting( 'title', 'tabs', $index ); ?></a>
                    </div><?php
                endforeach; ?>
                </div>

                <div class="elementor-tabs-content-wrapper" role="tablist" aria-orientation="horizontal"><?php
                foreach( $settings[ 'tabs' ] as $index => $tab ) :
                    $element = 'tabs.' . $index . '.text';
					$is_source_editor = false;
					if ( 'editor' == $tab[ 'content_type' ] ) {
						$is_source_editor = true;
						$this->add_inline_editing_attributes( $element, 'advanced' );
					}
                    $this->add_render_attribute( $element, array( 'id' => sprintf( 'elementor-tab-%1$s-%2$s', $widgetID, $index ), 'class' => array( 'elementor-tab-content', 'elementor-clearfix', ( empty( $index ) ? 'elementor-active' : 'hide' ) ) ) ); ?>
                    <div <?php $this->print_render_attribute_string( $element ); ?>><?php
                        if ( $is_source_editor ) {
                            $this->print_text_editor( $tab[ 'text' ] );
                        } else {
							$this->print_custom_block( $tab[ 'custom_block' ] );
                        } ?>
                    </div><?php
                endforeach; ?>
                </div>
            </div><?php
        endif;
	}
}
