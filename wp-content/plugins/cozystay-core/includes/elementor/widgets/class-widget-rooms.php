<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Blog
 */
class Widget_Rooms extends \LoftOcean\Elementor_Widget_Base {
    /**
    * Pagination base
    */
    public $pagination = '';
    /**
    * Frontend settings
    */
    protected $front_settings = false;
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceanrooms', array( 'id' => 'rooms' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Rooms', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-post-list';
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
		return [ 'rooms', 'room' ];
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
        $this->add_control( 'filter-by', array(
			'label' => esc_html__( 'Select Posts', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => array(
				'' => esc_html__( 'All', 'loftocean' ),
                'room-types' => esc_html__( 'By Room Type', 'loftocean' ),
                'room-facilities' => esc_html__( 'By Room Facility', 'loftocean' ),
                'static' => esc_html__( 'By IDs', 'loftocean' )
			)
		) );
        $this->add_control( 'room_types', array(
            'type' => \Elementor\Controls_Manager::SELECT2,
			'multiple' => true,
    		'label' => esc_html__( 'Choose Room Types', 'loftocean' ),
            'default' => array(),
            'condition' => array( 'filter-by[value]' => 'room-types' ),
            'options' => \LoftOcean\get_terms( 'lo_room_type', false )
        ) );
        $this->add_control( 'room_facilities', array(
            'type' => \Elementor\Controls_Manager::SELECT2,
			'multiple' => true,
    		'label' => esc_html__( 'Choose Room Facilities', 'loftocean' ),
            'default' => array(),
            'condition' => array( 'filter-by[value]' => 'room-facilities' ),
            'options' => \LoftOcean\get_terms( 'lo_room_facilities', false )
        ) );
        $this->add_control( 'staticids', array(
            'type' => \Elementor\Controls_Manager::TEXT,
    		'label' => esc_html__( 'Room IDs', 'loftocean' ),
            'description' => esc_html__( 'Seperated by comma(,) if there are multiple room IDs.', 'loftocean' ),
            'default' => '',
            'condition' => array( 'filter-by[value]' => 'static' ),
            'placeholder' => esc_html__( 'Room IDs', 'loftocean' )
        ) );
        // $this->add_control( 'sorting', array(
        //     'label' => esc_html__( 'Room Sorting', 'loftocean' ),
        //     'type' => \Elementor\Controls_Manager::SELECT,
        //     'default' => '',
        //     'options' => array(
        //         '' => esc_html__( 'Sort by most recent', 'loftocean' ),
        //         'menu_order' => esc_html__( 'Sort by room title (custom ordering + name)', 'loftocean' ),
        //         'popularity' => esc_html__( 'Popularity (sales)', 'loftocean' ),
        //         'rating' => esc_html__( 'Average rating', 'loftocean' ),
        //         'price' => esc_html__( 'Sort by price (asc)', 'loftocean' ),
        //         'price-desc' => esc_html__( 'Sort by price (desc)', 'loftocean' )
        //     )
        // ) );
        $this->end_controls_section();

        $this->start_controls_section( 'general_style_section', array(
            'label' => __( 'General', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE
        ) );
        $this->add_control( 'style', array(
            'label' => esc_html__( 'Rooms Style', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'list',
            'options' => array(
                'standard'		    => esc_html__( 'Standard', 'loftocean' ),
                'list'              => esc_html__( 'List', 'loftocean' ),
                'zigzag'            => esc_html__( 'Zigzag', 'loftocean' ),
                'grid-2cols'        => esc_html__( 'Grid 2 Columns', 'loftocean' ),
                'grid-3cols'        => esc_html__( 'Grid 3 Columns', 'loftocean' ),
                'overlay-2cols'	    => esc_html__( 'Overlay 2 Columns', 'loftocean' ),
                'overlay-3cols'	    => esc_html__( 'Overlay 3 Columns', 'loftocean' ),
                'carousel-1cols'    => esc_html__( 'Carousel Center Mode', 'loftocean' ),
                'carousels-1cols'    => esc_html__( 'Carousel 1 Column', 'loftocean' ),
                'carousel-2cols'    => esc_html__( 'Carousel 2 Columns', 'loftocean' ),
                'carousel-3cols'    => esc_html__( 'Carousel 3 Columns', 'loftocean' ),
                'coverlay-1cols'    => esc_html__( 'Carousel Overlay Center Mode', 'loftocean' ),
                'coverlays-1cols'    => esc_html__( 'Carousel Overlay 1 Column', 'loftocean' ),
                'coverlay-2cols'    => esc_html__( 'Carousel Overlay 2 Columns', 'loftocean' ),
                'coverlay-3cols'    => esc_html__( 'Carousel Overlay 3 Columns', 'loftocean' )
            )
        ) );
        $this->add_responsive_control( 'image_ratio', array(
            'label' => esc_html__( 'Image Ratio', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'img-ratio-3-2',
            'condition' => array( 'style!' => array( 'standard' ) ),
            'options' => array(
                'img-ratio-3-2'     => esc_html__( '3:2', 'loftocean' ),
                'img-ratio-4-3'     => esc_html__( '4:3', 'loftocean' ),
                'img-ratio-1-1'     => esc_html__( '1:1', 'loftocean' ),
                'img-ratio-4-5'     => esc_html__( '4:5', 'loftocean' ),
                'img-ratio-2-3'     => esc_html__( '2:3', 'loftocean' ),
                'img-ratio-custom'  => esc_html__( 'Custom', 'loftocean' )
            )
        ) );
        $this->add_responsive_control( 'custom_image_ratio', array(
            'label' => esc_html__( 'Custom Image Ratio', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'condition' => array( 'image_ratio' => 'img-ratio-custom' ),
            'range' => array( 'px' => array( 'min' => 0, 'max' => 200, 'step' => 1 ) ),
            'render_type' => 'ui',
            'separator' => 'before',
            'selectors' => array( '{{WRAPPER}} .posts.cs-rooms' => '--img-ratio: {{SIZE}}%;' )
        ) );
        $this->add_control( 'center_text', array(
            'label' => esc_html__( 'Center Text', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'content_vertical_position', array(
            'label' => esc_html__( 'Content Vertical Position', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'condition' => array( 'style' => array( 'overlay-2cols', 'overlay-3cols', 'coverlays-1cols', 'coverlay-1cols', 'coverlay-2cols', 'coverlay-3cols' ) ),
            'default' => '',
            'options' => array(
                ''               => esc_html__( 'Bottom', 'loftocean' ),
                'text-v-middle'  => esc_html__( 'Middle', 'loftocean' )
            )
        ) );
        $this->add_control( 'list_zigzag_with_border', array(
            'label' => esc_html__( 'With Border', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'condition' => array( 'style' => array( 'list', 'zigzag' ) ),
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'show_subtitle', array(
            'label' => esc_html__( 'Show Subtitle', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'on',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'show_excerpt', array(
            'label' => esc_html__( 'Show Excerpt', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'on',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'show_read_more_button', array(
            'label' => esc_html__( 'Show Read More Button', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'on',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'show_facilities', array(
            'label' => esc_html__( 'Show Room Facility Info', 'loftocean' ),
            'description' => sprintf(
                // translators: 1/2 description text
                '%1$s<br>%2$s',
                esc_html__( 'Show the first 4 items for 2 Columns and 3 items for 3 Columns only.', 'loftocean' ),
                esc_html__( 'To display more items please go to "Room Facility" below.', 'loftocean' )
            ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'show_label', array(
            'label' => esc_html__( 'Show Label', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'number', array(
            'label' => esc_html__( 'Posts Per Page', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => '10'
        ) );
        $this->add_control( 'pagination', array(
            'label' => esc_html__( 'Pagination', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'condition' => array( 'style!' => array( 'carousel-1cols', 'carousels-1cols', 'carousel-2cols', 'carousel-3cols', 'coverlay-1cols', 'coverlays-1cols', 'coverlay-2cols', 'coverlay-3cols' ) ),
            'default' => '',
            'options' => array(
                '' => esc_html__( 'None', 'loftocean' ),
                'link-only' => esc_html__( 'Next/Prev Links', 'loftocean' ),
				'link-number' => esc_html__( 'With Page Number', 'loftocean' ),
                'ajax-manual' => esc_html__( 'Load More', 'loftocean' ),
                'ajax-auto' => esc_html__( 'Infinite Scroll', 'loftocean' )
            )
        ) );
        $this->end_controls_section();

        $this->start_controls_section( 'subtitle_style_section', array(
            'label' => __( 'Subtitle', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => array( 'show_subtitle' => 'on' )
        ) );
        $this->add_control( 'subtitle_position', array(
            'label' => esc_html__( 'Subtitle Position', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'condition' => array( 'show_subtitle' => 'on' ),
            'default' => 'after_title',
            'options' => array(
                'before_title' => esc_html__( 'Before Room Title', 'loftocean' ),
                'after_title' => esc_html__( 'After Room Title', 'loftocean' )
            )
        ) );
        $this->end_controls_section();

        $this->start_controls_section( 'carousel_style_section', array(
            'label' => __( 'Carousel', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => array( 'style' => array( 'carousel-1cols', 'carousels-1cols', 'carousel-2cols', 'carousel-3cols', 'coverlay-1cols', 'coverlays-1cols', 'coverlay-2cols', 'coverlay-3cols' ) )
        ) );
        $this->add_control( 'autoplay', array(
            'label' => esc_html__( 'Autoplay', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'autoplay_speed', array(
            'label' => esc_html__( 'Autoplay Speed', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'condition' => array( 'autoplay[value]' => 'on' ),
            'default' => '5000'
        ) );
        $this->add_control( 'show_arrows', array(
            'label' => esc_html__( 'Show Arrows', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'on',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'arrow_position', array(
            'label' => esc_html__( 'Arrow Position', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'condition' => array( 'show_arrows' => 'on' ),
            'options' => array(
                '' => esc_html__( 'Default', 'loftocean' ),
                'slider-arrow-top' => esc_html__( 'Top', 'loftocean' )
            )
        ) );
        $this->add_control( 'slider_arrows_background_color', array(
            'label' => esc_html__( 'Slider Arrow Background Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'condition' => array( 'show_arrows[value]' => 'on' ),
            'selectors' => array(
                '{{WRAPPER}} .slick-arrow' => 'background-color: {{VALUE}};',
            )
        ) );
        $this->add_control( 'slider_arrows_icon_color', array(
            'label' => esc_html__( 'Slider Arrow Icon Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'condition' => array( 'show_arrows[value]' => 'on' ),
            'selectors' => array(
                '{{WRAPPER}} .slick-arrow' => 'color: {{VALUE}};',
            )
        ) );
        $this->add_control( 'show_dots', array(
            'label' => esc_html__( 'Show Dots', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'on',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'dots_position', array(
            'label' => esc_html__( 'Dots Position', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'condition' => array( 'show_dots' => 'on' ),
            'options' => array(
                '' => esc_html__( 'Below', 'loftocean' ),
                'slider-dots-overlap' => esc_html__( 'Overlap', 'loftocean' )
            )
        ) );
        $this->add_control( 'slider_dots_color', array(
            'label' => esc_html__( 'Slider Dots Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'condition' => array( 'show_dots' => 'on' ),
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .slick-dots li' => 'color: {{VALUE}};',
            )
        ) );
        $this->end_controls_section();

        $this->start_controls_section( 'label_style_section', array(
            'label' => __( 'Label', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => array( 'show_label' => array( 'on' ) )
        ) );
        $this->add_control( 'label_background_color', array(
            'label' => esc_html__( 'Background Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .posts.cs-rooms' => '--label-bg: {{VALUE}};',
            )
        ) );
        $this->add_control( 'label_border_color', array(
            'label' => esc_html__( 'Border Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .posts.cs-rooms' => '--label-border: {{VALUE}};',
            )
        ) );
        $this->add_control( 'label_text_color', array(
            'label' => esc_html__( 'Text Color', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default' => '',
            'selectors' => array(
                '{{WRAPPER}} .posts.cs-rooms' => '--label-color: {{VALUE}};',
            )
        ) );
        $this->end_controls_section();

        $this->start_controls_section( 'facility_style_section', array(
            'label' => __( 'Room Facility', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => array( 'show_facilities' => array( 'on' ) )
        ) );
        $this->add_control( 'room_facility_icon_size', array(
            'label' => esc_html__( 'Icon Size', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => array( 'px' => array( 'min' => 1, 'max' => 50, 'step' => 1 ) ),
            'render_type' => 'ui',
            'separator' => 'before',
            'selectors' => array( '{{WRAPPER}} .posts.cs-rooms' => '--icon-size: {{SIZE}}px;' )
        ) );
        $this->add_control( 'room_facility_number', array(
            'label' => esc_html__( 'Facility items show at most', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'min' => 1,
            'step' => 1
        ) );
        $this->end_controls_section();
	}
	/**
	* Written in PHP and used to generate the final HTML.
    * @access protected
	*/
	protected function render() {
        $settings = $this->get_settings_for_display();
        if ( ! empty( $settings[ 'style' ] ) ) {
            $this->pagination = $this->get_id() . '_page';
            $style_parts = explode( '-', $settings[ 'style' ] );
            $layout = $style_parts[ 0 ];
            $column = count( $style_parts ) > 1 ? str_replace( 'cols', '', $style_parts[ 1 ] ) : false;
            $list_args = apply_filters( 'loftocean_front_rooms_list_args', array(
                'layout'	=> $layout,
                'columns' 	=> $column,
                'metas'	    => $this->get_enabled_post_meta(),
                'page_layout' => '',
                'subtitle_position' => $settings[ 'subtitle_position' ],
                'facility_limit' => $settings[ 'room_facility_number' ]
            ) );
            $query_args = $this->get_query_rooms_args();
            query_posts( $query_args );
            $class = $this->get_wrap_class( $layout, $column );

            add_filter( 'post_class', array( $this, 'room_item_class' ), 999 );
            if ( have_posts() ) :
                $nav = $settings[ 'pagination' ];
                $this->front_settings = array(
                    'query' => $query_args,
                    'settings' => array_merge( $list_args, array( 'pagination' => $nav, 'action' => 'loftocean_ajax_room_load_more', 'archive_page' => 'elementor' ) )
                );
                add_action( 'loftocean_rooms_wrap_attributes', array( $this, 'print_frontend_settings' ) );

                $paged_value = get_query_var( 'paged' ) ? (int) get_query_var( 'paged' ) : 1;
                $not_ajax_nav = ( ! empty( $nav ) ) && in_array( $nav, array( 'link-only', 'link-number' ) );
                if ( $not_ajax_nav ) {
                    global $paged;
                    $current_page = isset( $_GET[ $this->pagination ] ) ? (int) $_GET[ $this->pagination ] : 1;
                    $paged = $current_page;
                    set_query_var( 'paged', $current_page );
                    add_filter( 'get_pagenum_link', array( $this, 'get_pagenum_link' ), 999999, 2 );
                    add_filter( 'paginate_links', array( $this, 'paginate_links' ), 999999 );
                }

                do_action( 'loftocean_rooms_widget_the_list_content', array(
                    'args' => $list_args,
                    'wrap_class' => apply_filters( 'loftocean_rooms_block_wrapper_class', $class, $list_args ),
                    'pagination' => $nav
                ), empty( $settings[ 'pagination' ] ) );
                remove_action( 'loftocean_rooms_wrap_attributes', array( $this, 'print_frontend_settings' ) );

                if ( $not_ajax_nav ) {
                    global $paged;
                    remove_filter( 'get_pagenum_link', array( $this, 'get_pagenum_link' ), 999999, 2 );
                    remove_filter( 'paginate_links', array( $this, 'paginate_links' ), 999999 );
                    $paged = $paged_value;
                    set_query_var( 'paged', $paged_value );
                }

                $this->front_settings = false;
            elseif ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) :
                $errors = array(
                    'all' => esc_html__( 'No room was found in the website.', 'loftocean' ),
                    'room-types' => esc_html__( 'No room was found with the room types you selected.', 'loftocean' ),
                    'room-facilities' => esc_html__( 'No room was found with the room facilities you selected.', 'loftocean' ),
                    'static' => esc_html__( 'No room was found for the IDs you entered.', 'loftocean' ),
                );
                $filter = empty( $settings[ 'filter-by' ] ) ? 'all' : $settings[ 'filter-by' ]; ?>
                <div class="cs-notice"><?php echo esc_html( $errors[ $filter ] ); ?></div><?php
            endif;
            remove_filter( 'post_class', array( $this, 'room_item_class' ), 999 );
            wp_reset_query();
        }
	}
    /**
    * Get list class
    */
    protected function get_wrap_class( $layout, $column ) {
        $settings = $this->get_settings_for_display();
        $class = array( 'posts', 'cs-rooms' );
    	if ( ! empty( $layout ) ) {
            $is_carousel = false;
            ( 'on' == $settings[ 'center_text' ] ) ? array_push( $class, 'text-center' ) : '';
            if ( in_array( $layout, array( 'carousel', 'coverlay', 'carousels', 'coverlays' ) ) ) {
                $is_carousel = true;
                array_push( $class, 'cs-rooms-carousel' );
                array_push( $class, 'layout-grid' );
                in_array( $layout, array( 'coverlay', 'coverlays' ) ) ? array_push( $class, 'layout-overlay' ) : '';
                if ( 'on' == $settings[ 'show_arrows' ] ) {
                    empty( $settings[ 'arrow_position' ] ) ? array_push( $class, 'slider-arrows-overlap' ) : array_push( $class, $settings[ 'arrow_position' ] );
                }
                empty( $settings[ 'dots_position' ] ) ? '' : array_push( $class, $settings[ 'dots_position' ] );
                ( 'on' == $settings[ 'show_dots' ] ) ? '' : array_push( $class, 'hide-slider-dots' );

                in_array( $layout, array( 'carousel', 'coverlay' ) ) && ( 1 == $column ) ? array_push( $class, 'carousel-center-mode' ) : '';
            } else {
        		array_push( $class, 'layout-' . $layout );
            }
            empty( $column ) ? '' : array_push( $class, 'column-' . $column );
            ( 'zigzag' == $layout ) ? array_push( $class, 'layout-list' ) : '';
    		( 'overlay' == $layout ) ? array_push( $class, 'layout-grid' ) : '';
    		if ( 'standard' != $settings[ 'style' ] ) {
                $image_ratios = array( 'image_ratio' => '', 'image_ratio_mobile' => '-mobile', 'image_ratio_tablet' => '-tablet' );
                foreach( $image_ratios as $image_ratio => $after ) {
                    empty( $settings[ $image_ratio ] ) ? '' : array_push( $class, $settings[ $image_ratio ] . $after );
                }
            }
            in_array( $layout, array( 'list', 'zigzag' ) ) && ( 'on' == $settings[ 'list_zigzag_with_border' ] ) ? array_push( $class, 'with-border' ) : '';
            in_array( $layout, array( 'overlay', 'coverlay', 'coverlays' ) ) && ( ! empty( $settings[ 'content_vertical_position' ] ) ) ? array_push( $class, $settings[ 'content_vertical_position' ] ) : '';

            if ( in_array( $layout, array( 'overlay', 'coverlay', 'coverlays' ) ) && ( 'on' == $settings[ 'show_excerpt']  || 'on' == $settings[ 'show_read_more_button' ] ) ) {
                array_push( $class, 'with-hover-effect' );
            }

    		$class = apply_filters( 'loftocean_rooms_list_wrap_class', $class, array( 'layout' => $layout, 'columns' => $column ) );
    		return array_unique( array_filter( $class ) );
    	}
    	return $class;
    }
    /**
    * Get enabled posts metas
    */
    protected function get_enabled_post_meta() {
        $settings = $this->get_settings_for_display();
    	$metas = array();
    	$all = array(
    		'excerpt' => 'show_excerpt',
    		'read_more_btn' => 'show_read_more_button',
    		'subtitle' => 'show_subtitle',
    		'facilities' => 'show_facilities',
    		'label' => 'show_label'
    	);
    	foreach( $all as $meta => $id ) {
    		if ( 'on' == $settings[ $id ] ) {
    			array_push( $metas, $meta );
    		}
    	}
    	return $metas;
    }
	/**
	* Add class name sticky to post class
	*/
	public function room_item_class( $class ) {
		array_push( $class, 'post', 'cs-room-item' );
		$class = array_diff( $class, array( 'sticky' ) );
		return array_unique( $class );
	}
    /**
	* Get posts by current widget settings
	* @return object WP_Query object
	*/
	protected function get_query_rooms_args() {
        $current_page = isset( $_GET[ $this->pagination ] ) ? (int) $_GET[ $this->pagination ] : 1;
		$settings = $this->get_settings_for_display();
		$args = array( 'paged' => $current_page, 'posts_per_page' => $settings[ 'number' ], 'ignore_sticky_posts' => true, 'post_type' => 'loftocean_room' );
        $args[ 'post_status' ] = is_user_logged_in() ? array( 'publish', 'private' ) : 'publish';
        switch ( $settings[ 'filter-by' ] ) {
            case 'room-types':
                if ( $settings[ 'room_types' ] ) {
                    $args['tax_query'] =  array( array(
                        'taxonomy' => 'lo_room_type',
                        'field' => 'slug',
                        'terms' => $settings[ 'room_types' ]
                    ) );
                }
                break;
            case 'room-facilities':
                if ( $settings[ 'room_facilities' ] ) {
                    $args['tax_query'] =  array( array(
                        'taxonomy' => 'lo_room_facilities',
                        'field' => 'slug',
                        'terms' => $settings[ 'room_facilities' ]
                    ) );
                }
                break;
            case 'static':
                if ( ! empty( $settings['staticids'] ) ) {
                    $args['post__in'] = explode( ',', $settings[ 'staticids' ] );
                }
                break;
        }
		return apply_filters( 'loftocean_get_widget_posts_query_args', $args,  array_merge( $settings, array( 'element-widget' => true ) ) );
	}
    /**
    * Output frontend settings
    */
    public function print_frontend_settings() {
        if ( $this->front_settings ) {
            $sets = $this->front_settings;
            $settings = $this->get_settings_for_display();
            $nav = $settings[ 'pagination' ];
            if ( ( ! empty( $nav ) ) && in_array( $nav, array( 'ajax-manual', 'ajax-auto' ) ) ) {
                foreach ( $sets as $prop => $value ) {
                    foreach ( $value as $id => $val ) {
                        if ( is_array( $val ) ) {
                            $sets[ $prop ][ $id ] = maybe_serialize( $val );
                        }
                    }
                }
                printf( " data-settings='%s'", json_encode( $sets ) );
            }

            if ( isset( $sets[ 'settings' ], $sets[ 'settings' ][ 'layout' ] ) && in_array( $sets[ 'settings' ][ 'layout' ], array( 'carousel', 'coverlay', 'carousels', 'coverlays' ) ) ) {
                $slider_data = array(
                    'autoplay' => $settings[ 'autoplay' ],
                    'autoplay-speed' => $settings[ 'autoplay_speed' ],
                    'show-arrows' => $settings[ 'show_arrows' ],
                    'show-dots' => 'on',
                    'column' => $sets[ 'settings' ][ 'columns' ]
                );
                foreach ( $slider_data as $name => $val ) {
                    printf( ' data-%1$s="%2$s"', $name, $val );
                }
            }
        }
    }
    /**
    * Pagination rewrite
    */
    public function get_pagenum_link( $result, $pagenum ) {
        global $wp_rewrite;

        $widget_paged = $this->pagination;
        $request = remove_query_arg( $widget_paged );

        $home_root = parse_url( home_url() );
        $home_root = ( isset( $home_root['path'] ) ) ? $home_root['path'] : '';
        $home_root = preg_quote( $home_root, '|' );

        $request = preg_replace( '|^' . $home_root . '|i', '', $request );
        $request = preg_replace( '|^/+|', '', $request );

        if ( ! $wp_rewrite->using_permalinks() || is_admin() ) {
            $base = trailingslashit( get_bloginfo( 'url' ) );
            $result = $base . $request;
            if ( $pagenum > 1 ) {
                $result = add_query_arg( $widget_paged, $pagenum, $result );
            }
        } else {
            $qs_regex = '|\?.*?$|';
            preg_match( $qs_regex, $request, $qs_match );

            if ( ! empty( $qs_match[0] ) ) {
                $query_string = $qs_match[0];
                $request      = preg_replace( $qs_regex, '', $request );
            } else {
                $query_string = '';
            }

            $request = preg_replace( "|$wp_rewrite->pagination_base/\d+/?$|", '', $request );
            $request = preg_replace( '|^' . preg_quote( $wp_rewrite->index, '|' ) . '|i', '', $request );
            $request = ltrim( $request, '/' );

            $base = trailingslashit( get_bloginfo( 'url' ) );

            if ( $wp_rewrite->using_index_permalinks() && ( $pagenum > 1 || '' !== $request ) ) {
                $base .= $wp_rewrite->index . '/';
            }

            $result = $base . $request . $query_string;
            if ( $pagenum > 1 ) {
                $result = add_query_arg( $widget_paged, $pagenum, $result );
            }
        }
        return $result;
    }
    /**
    * Change pagination
    */
    public function paginate_links( $link ) {
        if ( 1 === preg_match( '/\/page\/(\d+)/', $link, $match ) ) {
            $link = preg_replace( '/\/page\/\d+/', '', $link );
            $link = add_query_arg( $this->pagination, $match[ 1 ], $link );
        } else if ( 1 === preg_match( '/[?&]paged=(\d+)/', $link, $match ) ) {
            $link = remove_query_arg( 'paged', $link );
            $link = add_query_arg( $this->pagination, $match[ 1 ], $link );
        }
        return $link;
    }
}
