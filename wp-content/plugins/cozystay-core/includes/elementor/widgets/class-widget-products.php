<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Blog
 */
class Widget_Products extends \LoftOcean\Elementor_Widget_Base {
	/**
	* Pagination query name
	*/
	protected $pagination = '';
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceanproducts', array( 'id' => 'products' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Products', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-products';
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
		return [ 'products' ];
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
			'label' => esc_html__( 'Select Products', 'loftocean' ),
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
            'options' => \LoftOcean\get_terms( 'product_cat', true, esc_html__( 'Choose a category', 'loftocean' ) )
        ) );
        $this->add_control( 'tag', array(
            'type' => \Elementor\Controls_Manager::SELECT,
    		'label' => esc_html__( 'Choose a Tag', 'loftocean' ),
            'default' => '',
            'condition' => array( 'filter-by[value]' => 'tag' ),
            'options' => \LoftOcean\get_terms( 'product_tag', true, esc_html__( 'Choose a tag', 'loftocean' ) )
        ) );
        $this->add_control( 'staticids', array(
            'type' => \Elementor\Controls_Manager::TEXT,
    		'label' => esc_html__( 'Product IDs', 'loftocean' ),
            'description' => esc_html__( 'Seperated by comma(,) if there are multiple product IDs.', 'loftocean' ),
            'default' => '',
            'condition' => array( 'filter-by[value]' => 'static' ),
            'placeholder' => esc_html__( 'Post IDs', 'loftocean' )
        ) );
		$this->add_control( 'enable_pagination', array(
            'label' => esc_html__( 'Show Pagination', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
        ) );
        $this->end_controls_section();

        $this->start_controls_section( 'general_style_section', array(
            'label' => __( 'General', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE
        ) );
		$this->add_control( 'style', array(
            'label' => esc_html__( 'Style', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '',
            'options' => array(
                '' => esc_html__( 'Default', 'loftocean' ),
				'food-menu-style' => esc_html__( 'Food Menu Style', 'loftocean' )
            )
        ) );
		$this->add_control( 'food-menu-style', array(
            'label' => esc_html__( 'Food Menu Style', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'food-menu-style-1',
			'condition' => array( 'style[value]' => 'food-menu-style' ),
            'options' => array(
				'food-menu-style-1' => esc_html__( 'Style 1', 'loftocean' ),
				'food-menu-style-2' => esc_html__( 'Style 2', 'loftocean' ),
				'food-menu-style-3' => esc_html__( 'Style 3', 'loftocean' ),
				'food-menu-style-4' => esc_html__( 'Style 4', 'loftocean' ),
				'food-menu-style-5' => esc_html__( 'Style 5', 'loftocean' ),
				'food-menu-style-6' => esc_html__( 'Style 6', 'loftocean' )
            )
        ) );
        $this->add_control( 'columns', array(
            'label' => esc_html__( 'Products Per Row', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '4',
            'options' => array(
                '1' => esc_html__( '1 Column', 'loftocean' ),
                '2' => esc_html__( '2 Columns', 'loftocean' ),
				'3' => esc_html__( '3 Columns', 'loftocean' ),
				'4' => esc_html__( '4 Columns', 'loftocean' ),
				'5' => esc_html__( '5 Columns', 'loftocean' ),
				'6' => esc_html__( '6 Columns', 'loftocean' )
            )
        ) );
        $this->add_control( 'rows', array(
            'label' => esc_html__( 'Rows Per Page', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '2',
            'options' => array(
    			'1' => esc_html__( '1 Row', 'loftocean' ),
    			'2' => esc_html__( '2 Rows', 'loftocean' ),
    			'3' => esc_html__( '3 Rows', 'loftocean' ),
    			'4' => esc_html__( '4 Rows', 'loftocean' )
			)
        ) );
        $this->add_control( 'sorting', array(
            'label' => esc_html__( 'Product Sorting', 'loftocean' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'menu_order',
            'options' => array(
                'menu_order' => esc_html__( 'Default sorting (custom ordering + name)', 'loftocean' ),
                'popularity' => esc_html__( 'Popularity (sales)', 'loftocean' ),
                'rating' => esc_html__( 'Average rating', 'loftocean' ),
                'date' => esc_html__( 'Sort by most recent', 'loftocean' ),
                'price' => esc_html__( 'Sort by price (asc)', 'loftocean' ),
                'price-desc' => esc_html__( 'Sort by price (desc)', 'loftocean' )
            )
        ) );
		$this->add_control( 'show_description', array(
			'label' => esc_html__( 'Show Short Description', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'off',
			'label_on' => 'on',
			'label_off' => 'off',
			'return_value' => 'on'
		) );
		$this->add_control( 'description_length', array(
			'label' => esc_html__( 'Short Description Length', 'loftocean' ),
			'condition' => array( 'show_description[value]' => 'on' ),
			'type' => \Elementor\Controls_Manager::NUMBER,
			'default' => '15'
		) );
        $this->end_controls_section();
	}
	/**
	* Written in PHP and used to generate the final HTML.
    * @access protected
	*/
	protected function render() {
        $settings = $this->get_settings_for_display();

        $products = $this->get_products();
		if ( $products && $products->have_posts() ) :
			$is_food_menu_style = false;
            $column = absint( $settings[ 'columns' ] );
            $this->add_render_attribute( 'ul', 'class', array( 'products', 'columns-' . ( is_int( $column ) ? $column : 4 ) ) );
			if ( ! empty( $settings[ 'style' ] ) ) {
				$is_food_menu_style = true;
				$this->add_render_attribute( 'ul', 'class', array( 'cs-food-menu', $settings[ 'food-menu-style' ] ) );
			} ?>
            <div class="woocommerce">
    			<ul <?php $this->print_render_attribute_string( 'ul' ); ?>><?php
    			add_filter( 'option_woocommerce_catalog_columns', array( $this, 'change_column' ), 99, 2 );
    			while ( $products->have_posts() ) :
    				$products->the_post();
    				$product_class = wc_get_product_class();
    				$product_class = $this->modify_product_class( $product_class, $products->current_post, $column ); ?>

    				<li class="<?php echo esc_attr( implode( ' ', $product_class ) ); ?>">
					 	<?php $is_food_menu_style ? $this->the_food_menu_item_content() : $this->the_default_item_content(); ?>
    				</li><?php
    			endwhile; ?>
    			</ul><?php
				'on' == $settings[ 'enable_pagination' ] ? $this->the_pagination( $products ) : '';
    			wp_reset_postdata();
    			remove_filter( 'option_woocommerce_catalog_columns', array( $this, 'change_column' ), 99 );
				woocommerce_reset_loop(); ?>
            </div><?php
		elseif ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) :
			$errors = array(
				'all' => esc_html__( 'No products were found in the website.', 'loftocean' ),
				'category' => esc_html__( 'No products were found in the category you selected.', 'loftocean' ),
				'tag' => esc_html__( 'No products were found in the tag you selected.', 'loftocean' ),
				'static' => esc_html__( 'No products were found for the IDs you entered.', 'loftocean' ),
			);
			$filter = empty( $settings[ 'filter-by' ] ) ? 'all' : $settings[ 'filter-by' ]; ?>
			<div class="cs-notice"><?php echo esc_html( $errors[ $filter ] ); ?></div><?php
		endif;
		$this->remove_ordering_args();
	}
    /**
	 * Query the products and return them.
	 *
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 *
	 * @return WP_Query
	 */
	public function get_products() {
        $settings = $this->get_settings_for_display();
		$this->pagination = $this->get_id() . '-product-page';

		$columns = absint( $settings[ 'columns' ] );
		$rows = absint( $settings[ 'rows' ] );
		$number = ( is_int( $columns ) ? $columns : 4 ) * ( is_int( $rows ) ? $rows : 2 );
		$product_visibility_term_ids = wc_get_product_visibility_term_ids();

		$page_num = empty( $_GET[ $this->pagination ] ) ? 1 : $_GET[ $this->pagination ];

		$query_args = array(
			'paged' => absint( $page_num ),
			'posts_per_page' => $number,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'meta_query'     => array(),
			'tax_query'      => array()
		); // WPCS: slow query ok.

		if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$query_args[ 'tax_query' ][] = array(
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids[ 'outofstock' ],
					'operator' => 'NOT IN',
				),
			); // WPCS: slow query ok.
		}

		switch ( $settings[ 'filter-by' ] ) {
            case 'category':
                if ( ! empty( $settings[ 'category' ] ) ) {
                    $query_args[ 'product_cat' ] = $settings[ 'category' ];
                }
                break;
            case 'tag':
                if ( ! empty( $settings[ 'tag' ] ) ) {
                    $query_args[ 'product_tag' ] = $settings[ 'tag' ];
                }
                break;
            case 'static':
                if ( ! empty( $settings[ 'staticids' ] ) ) {
                    $query_args[ 'post__in' ] = explode( ',', $settings[ 'staticids' ] );
                }
                break;
		}

		$order = $this->get_ordering_args( $settings[ 'sorting' ] );
		if ( is_array( $order ) && ! empty( $order ) ) {
			$query_args = array_merge( $query_args, $order );
		}
		if ( is_array( $query_args[ 'tax_query' ] ) && ( count( $query_args[ 'tax_query' ] ) > 0 ) ) {
			$query_args[ 'tax_query' ][ 'relation' ] = 'AND';
		}

		return new \WP_Query( $query_args );
	}
	/**
	 * Returns an array of arguments for ordering products based on the selected values.
	 *
	 * @param string $orderby Order by param.
	 * @param string $order Order param.
	 * @return array
	 */
	public function get_ordering_args( $sorting ) {
		$order = ''; $orderby = '';
		// Get ordering from query string unless defined.
		if ( ! empty( $sorting ) ) {
			$orderby_value = explode( '-', $sorting );
			$orderby = esc_attr( $orderby_value[ 0 ] );
			$order = ! empty( $orderby_value[ 1 ] ) ? strtoupper( $orderby_value[ 1 ] ) : $order;
		}

		$args = array(
			'orderby'  => $orderby,
			'order'    => ( 'DESC' == $order ) ? 'DESC' : 'ASC',
			'meta_key' => '', // @codingStandardsIgnoreLine
		);

		switch ( $orderby ) {
			case 'menu_order':
				$args['orderby'] = 'menu_order title';
				break;
			case 'popularity':
				add_filter( 'posts_clauses', array( $this, 'order_by_popularity_post_clauses' ) );
				break;
			case 'rating':
				add_filter( 'posts_clauses', array( $this, 'order_by_rating_post_clauses' ) );
				break;
			case 'date':
				$args[ 'orderby' ] = 'date ID';
				$args[ 'order' ]   = ( 'ASC' == $order ) ? 'ASC' : 'DESC';
				break;
			case 'price':
				$callback = 'DESC' === $order ? 'order_by_price_desc_post_clauses' : 'order_by_price_asc_post_clauses';
				add_filter( 'posts_clauses', array( $this, $callback ) );
				break;
		}

		return $args;
	}
	/**
	 * Remove ordering queries.
	 */
	protected function remove_ordering_args() {
		remove_filter( 'posts_clauses', array( $this, 'order_by_price_asc_post_clauses' ) );
		remove_filter( 'posts_clauses', array( $this, 'order_by_price_desc_post_clauses' ) );
		remove_filter( 'posts_clauses', array( $this, 'order_by_popularity_post_clauses' ) );
		remove_filter( 'posts_clauses', array( $this, 'order_by_rating_post_clauses' ) );
	}
	/**
	* Join wc_product_meta_lookup to posts if not already joined.
	* @param string $sql SQL join.
 	* @return string
 	*/
	protected function append_product_sorting_table_join( $sql ) {
		global $wpdb;

		if ( ! strstr( $sql, 'wc_product_meta_lookup' ) ) {
			$sql .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
		}
		return $sql;
	}
	/**
	 * Handle numeric price sorting.
	 * @param array $args Query args.
	 * @return array
	 */
	public function order_by_price_asc_post_clauses( $args ) {
		$args[ 'join' ]    = $this->append_product_sorting_table_join( $args[ 'join' ] );
		$args[ 'orderby' ] = ' wc_product_meta_lookup.min_price ASC, wc_product_meta_lookup.product_id ASC ';
		return $args;
	}
	/**
	 * Handle numeric price sorting.
	 * @param array $args Query args.
	 * @return array
	 */
	public function order_by_price_desc_post_clauses( $args ) {
		$args[ 'join' ]    = $this->append_product_sorting_table_join( $args[ 'join' ] );
		$args[ 'orderby' ] = ' wc_product_meta_lookup.max_price DESC, wc_product_meta_lookup.product_id DESC ';
		return $args;
	}
	/**
	 * WP Core does not let us change the sort direction for individual orderby params - https://core.trac.wordpress.org/ticket/17065.
	 * This lets us sort by meta value desc, and have a second orderby param.
	 * @param array $args Query args.
	 * @return array
	 */
	public function order_by_popularity_post_clauses( $args ) {
		$args[ 'join' ]    = $this->append_product_sorting_table_join( $args[ 'join' ] );
		$args[ 'orderby' ] = ' wc_product_meta_lookup.total_sales DESC, wc_product_meta_lookup.product_id DESC ';
		return $args;
	}
	/**
 	* Order by rating post clauses.
	 * @param array $args Query args.
	 * @return array
	 */
	public function order_by_rating_post_clauses( $args ) {
		$args[ 'join' ]    = $this->append_product_sorting_table_join( $args[ 'join' ] );
		$args[ 'orderby' ] = ' wc_product_meta_lookup.average_rating DESC, wc_product_meta_lookup.rating_count DESC, wc_product_meta_lookup.product_id DESC ';
		return $args;
	}
	/**
	* Out of stock label
	*/
	protected function loop_out_of_stock() {
		global $product;
		if ( ! $product->managing_stock() && ! $product->is_in_stock() ) : ?>
			<span class="stock out-of-stock"><?php esc_html_e( 'Sold Out', 'loftocean' ); ?></span><?php
		endif;
	}
	/**
	* Change default columns setting for product list
	*/
	public function change_column( $value, $name ) {
		if ( 'woocommerce_catalog_columns' == $name ) {
            $settings = $this->get_settings_for_display();
			return $settings[ 'columns' ];
		}
		return $value;
	}
	/**
	* Modify default product class
	*/
	protected function modify_product_class( $class, $index, $column ) {
		$class = array_diff( $class, array( 'first', 'last' ) );
		0 === ( $index % $column ) ? array_push( $class, 'first' ) : '';
		( $column - 1 ) === ( $index % $column ) ? array_push( $class, 'last' ) : '';
		return $class;
	}
	/**
	* Default item content
	*/
	protected function the_default_item_content() { ?>
		<div class="product-image"><?php
			woocommerce_template_loop_product_link_open();
			$this->loop_out_of_stock();
			woocommerce_show_product_loop_sale_flash();
			woocommerce_template_loop_product_thumbnail();

			woocommerce_template_loop_product_link_close();
			woocommerce_template_loop_add_to_cart(); ?>
		</div><?php
		woocommerce_template_loop_product_link_open();
		woocommerce_template_loop_product_title();
		woocommerce_template_loop_price();
		woocommerce_template_loop_rating();
		$settings = $this->get_settings_for_display();
		if ( 'on' == $settings[ 'show_description' ] ) {
			add_filter( 'loftocean_woocommerce_short_description_length', array( $this, 'change_short_description_length' ), 30 );
			do_action( 'loftocean_woocommerce_the_short_description' );
			remove_filter( 'loftocean_woocommerce_short_description_length', array( $this, 'change_short_description_length' ), 30 );
		}
		woocommerce_template_loop_product_link_close();
	}
	/**
	* Food menu style item content
	*/
	protected function the_food_menu_item_content() { ?>
		<div class="cs-food-menu-item">
			<div class="product-image cs-food-menu-img"><?php
				woocommerce_template_loop_product_link_open();
				woocommerce_template_loop_product_thumbnail();
				woocommerce_template_loop_product_link_close(); ?>
			</div>
			<div class="cs-food-menu-main"><?php
				ob_start();
				woocommerce_template_loop_product_link_open();
				$product_link = ob_get_clean();
				echo str_replace( ' woocommerce-loop-product__link', ' woocommerce-loop-product__link cs-food-menu-header', $product_link ); ?>
				<h2 class="<?php echo esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title cs-food-menu-title' ) ); ?>"><?php
					the_title();
					ob_start();
					$this->loop_out_of_stock();
					woocommerce_show_product_loop_sale_flash();
					$sale_label = ob_get_clean();
					if ( ! empty( $sale_label ) ) {
						echo str_replace( array( 'class="onsale"', 'class="stock out-of-stock"' ), array( 'class="menu-label"', 'class="menu-label out-of-stock"' ), $sale_label );
					} ?>
				</h2>
				<div class="cs-food-menu-lines"></div><?php
				ob_start();
				woocommerce_template_loop_price();
				$product_price = ob_get_clean();
				echo str_replace( 'class="price"', 'class="price cs-food-menu-price"', $product_price );
				woocommerce_template_loop_product_link_close();
				woocommerce_template_loop_rating(); ?>
				<div class="cs-food-menu-footer"><?php
					$settings = $this->get_settings_for_display();
					if ( 'on' == $settings[ 'show_description' ] ) {
						add_filter( 'loftocean_woocommerce_short_description_length', array( $this, 'change_short_description_length' ), 30 );
						do_action( 'loftocean_woocommerce_the_short_description', 'cs-food-menu-details' );
						remove_filter( 'loftocean_woocommerce_short_description_length', array( $this, 'change_short_description_length' ), 30 );
					}
					woocommerce_template_loop_add_to_cart(); ?>
				</div>
			</div>
		</div><?php
	}
	/**
	* Short description length
	*/
	public function change_short_description_length( $default ) {
		$settings = $this->get_settings_for_display();
		$length = absint( $settings[ 'description_length' ] );
		return empty( $length ) ? $default : $length;
	}
	/**
	* The pagination
	*/
	protected function the_pagination( $products ) {
		wc_set_loop_prop( 'total', $products->found_posts );
		wc_set_loop_prop( 'total_pages', $products->max_num_pages );
		wc_set_loop_prop( 'current_page', $products->query[ 'paged' ] );
		wc_set_loop_prop( 'is_shortcode', true );

        add_filter( 'paginate_links', array( $this, 'paginate_links' ), 999999 );
		woocommerce_pagination();
        remove_filter( 'paginate_links', array( $this, 'paginate_links' ), 999999 );
	}
    /**
    * Change pagination
    */
    public function paginate_links( $link ) {
        if ( 1 === preg_match( '/[?&]product-page=(\d+)/', $link, $match ) ) {
            $link = remove_query_arg( 'product-page', $link );
            $link = add_query_arg( $this->pagination, $match[ 1 ], $link );
        }
        return $link;
    }
}
