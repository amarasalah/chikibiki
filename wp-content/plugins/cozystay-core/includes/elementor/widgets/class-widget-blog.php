<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Blog
 */
class Widget_Blog extends \LoftOcean\Elementor_Widget_Base {
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
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceanblog', array( 'id' => 'blog' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Blog', 'loftocean' );
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
		return [ 'blog posts', 'posts', 'blog' ];
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
                'category' => esc_html__( 'By Category', 'loftocean' ),
                'tag' => esc_html__( 'By Tag', 'loftocean' ),
                'static' => esc_html__( 'By IDs', 'loftocean' )
			)
		) );
        $this->add_control( 'category', array(
            'type' => \Elementor\Controls_Manager::SELECT,
    		'label' => esc_html__( 'Choose a Category', 'loftocean' ),
            'default' => '',
            'condition' => array( 'filter-by[value]' => 'category' ),
            'options' => \LoftOcean\get_terms( 'category', true, esc_html__( 'Choose a category', 'loftocean' ) )
        ) );
        $this->add_control( 'tag', array(
            'type' => \Elementor\Controls_Manager::SELECT,
    		'label' => esc_html__( 'Choose a Tag', 'loftocean' ),
            'default' => '',
            'condition' => array( 'filter-by[value]' => 'tag' ),
            'options' => \LoftOcean\get_terms( 'post_tag', true, esc_html__( 'Choose a tag', 'loftocean' ) )
        ) );
        $this->add_control( 'staticids', array(
            'type' => \Elementor\Controls_Manager::TEXT,
    		'label' => esc_html__( 'Post IDs', 'loftocean' ),
            'description' => esc_html__( 'Seperated by comma(,) if there are multiple post IDs.', 'loftocean' ),
            'default' => '',
            'condition' => array( 'filter-by[value]' => 'static' ),
            'placeholder' => esc_html__( 'Post IDs', 'loftocean' )
        ) );
        $this->end_controls_section();

        $this->start_controls_section( 'general_style_section', array(
            'label' => __( 'General', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE
        ) );
        $this->add_control( 'style', array(
            'label' => esc_html__( 'Posts Style', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'masonry-3cols',
            'options' => array(
                'standard'		=> esc_html__( 'Standard', 'loftocean' ),
                'list'			=> esc_html__( 'List', 'loftocean' ),
                'zigzag'        => esc_html__( 'Zigzag', 'loftocean' ),
                'grid-2cols'	=> esc_html__( 'Grid 2 Columns', 'loftocean' ),
                'grid-3cols'	=> esc_html__( 'Grid 3 Columns', 'loftocean' ),
                'masonry-2cols'	=> esc_html__( 'Masonry 2 Columns', 'loftocean' ),
                'masonry-3cols'	=> esc_html__( 'Masonry 3 Columns', 'loftocean' ),
                'overlay-2cols'	=> esc_html__( 'Overlay 2 Columns', 'loftocean' ),
                'overlay-3cols'	=> esc_html__( 'Overlay 3 Columns', 'loftocean' )
            )
        ) );
        $this->add_control( 'overlay_image_ratio', array(
            'label' => esc_html__( 'Image Ratio', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'img-ratio-2-3',
            'condition' => array( 'style[value]' => array( 'overlay-2cols', 'overlay-3cols' ) ),
            'options' => array(
                'img-ratio-3-2' => esc_html__( '3:2', 'loftocean' ),
                'img-ratio-4-3' => esc_html__( '4:3', 'loftocean' ),
                'img-ratio-1-1' => esc_html__( '1:1', 'loftocean' ),
                'img-ratio-4-5' => esc_html__( '4:5', 'loftocean' ),
                'img-ratio-2-3' => esc_html__( '2:3', 'loftocean' )
            )
        ) );
        $this->add_control( 'image_ratio', array(
            'label' => esc_html__( 'Image Ratio', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'img-ratio-3-2',
            'condition' => array( 'style[value]' => array( 'grid-2cols', 'grid-3cols', 'list', 'zigzag' ) ),
            'options' => array(
                'img-ratio-3-2' => esc_html__( '3:2', 'loftocean' ),
                'img-ratio-4-3' => esc_html__( '4:3', 'loftocean' ),
                'img-ratio-1-1' => esc_html__( '1:1', 'loftocean' ),
                'img-ratio-4-5' => esc_html__( '4:5', 'loftocean' ),
                'img-ratio-2-3' => esc_html__( '2:3', 'loftocean' )
            )
        ) );
        $this->add_control( 'center_text', array(
            'label' => esc_html__( 'Center Text', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'list_zigzag_with_border', array(
            'label' => esc_html__( 'With Border', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'condition' => array( 'style[value]' => array( 'list', 'zigzag' ) ),
            'default' => 'off',
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
        $this->add_control( 'meta_title', array(
            'label' => esc_html__( 'Display Selected Post Meta', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::HEADING
        ) );
        $this->add_control( 'show_author', array(
            'label' => esc_html__( 'Show Author', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'show_category', array(
            'label' => esc_html__( 'Show Category', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'on',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'show_publish_date', array(
            'label' => esc_html__( 'Show Publish Date', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'on',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->add_control( 'show_comment_counter', array(
            'label' => esc_html__( 'Show Comment Counts', 'loftocean' ),
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
            'options' => array(
                '' => esc_html__( 'None', 'loftocean' ),
                'link-only' => esc_html__( 'Next/Prev Links', 'loftocean' ),
				'link-number' => esc_html__( 'With Page Number', 'loftocean' ),
                'ajax-manual' => esc_html__( 'Load More', 'loftocean' ),
                'ajax-auto' => esc_html__( 'Infinite Scroll', 'loftocean' )
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
        if ( ! empty( $settings[ 'style' ] ) && has_action( 'loftocean_posts_block_the_list_content' ) ) {
            $this->pagination = $this->get_id() . '_page';
            $style_parts = explode( '-', $settings[ 'style' ] );
            $layout = $style_parts[ 0 ];
            $column = count( $style_parts ) > 1 ? str_replace( 'cols', '', $style_parts[ 1 ] ) : false;
            $list_args = apply_filters( 'loftocean_front_post_list_args', array(
                'layout'	=> $layout,
                'columns' 	=> $column,
                'post_meta'	=> $this->get_enabled_post_meta(),
                'page_layout' => ''
            ) );
            $query_args = $this->get_query_post_args();
            query_posts( $query_args );
            $class = $this->get_wrap_class( $layout, $column );

            add_filter( 'post_class', array( $this, 'post_class' ), 999 );
            if ( have_posts() ) :
                $nav = $settings[ 'pagination' ];
                $this->front_settings = array(
                    'query' => $query_args,
                    'settings' => array_merge( $list_args, array( 'pagination' => $nav, 'archive_page' => 'elementor' ) )
                );
                add_action( 'loftocean_posts_wrap_attributes', array( $this, 'print_frontend_settings' ) );

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

                do_action( 'loftocean_posts_block_the_list_content', array(
                    'args' => $list_args,
                    'wrap_class' => apply_filters( 'loftocean_posts_block_wrapper_class', $class, $list_args ),
                    'pagination' => $settings[ 'pagination' ]
                ), empty( $settings[ 'pagination' ] ) );
                remove_action( 'loftocean_posts_wrap_attributes', array( $this, 'print_frontend_settings' ) );

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
                    'all' => esc_html__( 'No posts were found in the website.', 'loftocean' ),
                    'category' => esc_html__( 'No posts were found in the category you selected.', 'loftocean' ),
                    'tag' => esc_html__( 'No posts were found in the tag you selected.', 'loftocean' ),
                    'static' => esc_html__( 'No posts were found for the IDs you entered.', 'loftocean' ),
                );
                $filter = empty( $settings[ 'filter-by' ] ) ? 'all' : $settings[ 'filter-by' ]; ?>
                <div class="cs-notice"><?php echo esc_html( $errors[ $filter ] ); ?></div><?php
            endif;
            remove_filter( 'post_class', array( $this, 'post_class' ), 999 );
            wp_reset_query();
        }
	}
    /**
    * Get list class
    */
    protected function get_wrap_class( $layout, $column ) {
        $settings = $this->get_settings_for_display();
        $class = array( 'posts' );
    	if ( ! empty( $layout ) ) {
            ( 'on' == $settings[ 'center_text' ] ) ? array_push( $class, 'text-center' ) : '';
    		array_push( $class, 'layout-' . $layout );
            ( 'zigzag' == $layout ) ? array_push( $class, 'layout-list' ) : '';
    		empty( $column ) ? '' : array_push( $class, 'column-' . $column );
    		if ( 'overlay' == $layout ) {
                array_push( $class, 'layout-grid' );
                array_push( $class, $settings[ 'overlay_image_ratio' ] );
    		} else if ( in_array( $layout, array( 'grid', 'list', 'zigzag' ) ) ) {
    			array_push( $class, $settings[ 'image_ratio' ] );
    		}
            if ( in_array( $layout, array( 'list', 'zigzag' ) ) ) {
                ( 'on' == $settings[ 'list_zigzag_with_border' ] ) ? array_push( $class, 'with-border' ) : '';
            }

    		$class = apply_filters( 'loftocean_post_list_wrap_class', $class, array( 'layout' => $layout, 'columns' => $column ) );
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
    		'category' => 'show_category',
    		'author' => 'show_author',
    		'date' => 'show_publish_date',
    		'comment_counts' => 'show_comment_counter'
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
	public function post_class( $class ) {
		array_push( $class, 'post' );
		$class = array_diff( $class, array( 'sticky' ) );
		return array_unique( $class );
	}
    /**
	* Get posts by current widget settings
	* @return object WP_Query object
	*/
	protected function get_query_post_args() {
        $current_page = isset( $_GET[ $this->pagination ] ) ? (int) $_GET[ $this->pagination ] : 1;
		$settings = $this->get_settings_for_display();
		$args = array( 'paged' => $current_page, 'posts_per_page' => $settings[ 'number' ], 'ignore_sticky_posts' => true, 'post_type' => 'post' );
        $args[ 'post_status' ] = is_user_logged_in() ? array( 'publish', 'private' ) : 'publish';
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
