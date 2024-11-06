<?php
namespace LoftOcean\Custom_Post_Type;

if ( ! class_exists( '\LoftOcean\Custom_Post_Type\Room' ) ) {
    class Room {
        /**
        * String Post type
        */
        protected $post_type = 'loftocean_room';
        /**
        * Is WooCommerce checkout page
        */
        protected $is_woocommerce_checkout_page = false;
        /*
        * capability
        */
        protected $capability = 'manage_options';
        /**
        * Construct function
        */
        public function __construct() {
            $dir = LOFTOCEAN_DIR . 'includes/custom-post-types/rooms/';
            require_once $dir . 'taxonomy/class-room-facility.php';
            require_once $dir . 'taxonomy/class-booking-rules.php';
            require_once $dir . 'taxonomy/class-flexible-price-rules.php';
            require_once $dir . 'taxonomy/class-extra-services.php';
            require_once $dir . 'settings/class-ical-sync-settings.php';
            require_once $dir . 'settings/class-general-settings.php';
            require_once $dir . 'utils/class-relationship-tools.php';
            require_once $dir . 'utils/class-room-relationship.php';
            require_once $dir . 'utils/class-orders.php';
            require_once $dir . 'utils/class-availability.php';
            require_once $dir . 'utils/class-permalink.php';
            require_once $dir . 'utils/class-price.php';
            require_once $dir . 'order/class-cart-item-availability-check.php';

            $utils_dir = LOFTOCEAN_DIR . 'includes/utils/';
            require_once $utils_dir . 'class-rooms.php';
            require_once $utils_dir . 'class-room-booking-rules.php';
            require_once $utils_dir . 'class-room-flexible-price-rules.php';
            require_once $utils_dir . 'class-room-reservation.php';

            // iCal Sync
            require_once LOFTOCEAN_DIR . 'includes/libraries/zcontent-icalendar/zapcallib.php';
            require_once $dir . 'utils/class-ical.php';
            require_once $dir . 'utils/class-ical-sync.php';
            require_once $dir . 'utils/class-ical-sync-cron.php';

            $this->capability = \LoftOcean\get_room_section_capabilities();

            add_action( 'init', array( $this, 'admin_filter' ), 1 );
            add_action( 'init', array( $this, 'register_posttype_room' ) );
            add_action( 'admin_action_lo_duplicate_room', array( $this, 'do_duplicate_action' ) );
            add_filter( 'post_row_actions', array( $this, 'duplicate_button' ), 10, 2 );

            add_action( 'add_meta_boxes_' . $this->post_type, array( $this, 'register_meta_boxes' ), 999 );
            add_action( 'woocommerce_after_order_itemmeta', array( $this, 'woocommerce_order_details' ), 10, 3 );
            add_action( 'woocommerce_after_cart_item_name', array( $this, 'woocommerce_cart_item_product' ), 99, 2 );
            add_action( 'woocommerce_order_item_meta_end', array( $this, 'woocommerce_email_item_details' ), 99, 4 );
            add_filter( 'woocommerce_cart_item_thumbnail', array( $this, 'woocommerce_cart_item_thumbnail' ), 99, 3 );
            add_filter( 'woocommerce_admin_order_item_thumbnail', array( $this, 'woocommerce_admin_order_item_thumbnail' ), 99, 3 );
            add_filter( 'woocommerce_cart_item_quantity', array( $this, 'woocommerce_cart_item_quantity' ), 99, 3 );
            add_action( 'woocommerce_review_order_before_cart_contents', array( $this, 'is_checkout_page' ) );
            add_filter( 'woocommerce_get_item_data', array( $this, 'output_room_details' ), 999, 2 );
            add_action( 'woocommerce_review_order_after_cart_contents', array( $this, 'disable_checkout_page' ) );
            add_action( 'pre_get_posts', array( $this, 'hide_room_product' ), 999, 1 );
            add_action( 'save_post', array( $this, 'save_room_metas' ), 10, 3 );
            add_action( 'template_redirect', array( $this, 'front_actions' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

            add_filter( 'loftocean_get_all_rooms', array( $this, 'get_all_rooms' ) );
        }
        /**
        * Create custom post types
        */
        public function register_posttype_room() {
            register_post_type( $this->post_type, array(
                'labels' => array(
                    'name' => esc_html__( 'Rooms', 'loftocean' ),
                    'all_items' => __( 'All Rooms', 'loftocean' ),
                    'singular_name' => esc_html__( 'Room', 'loftocean' ),
                    'add_new' => esc_html__( 'Add New Room', 'loftocean' ),
                    'add_new_item' => esc_html__( 'Add New Room', 'loftocean' )
                ),
                'public' => true,
                'has_archive' => false,
                'show_in_rest' => false,
                'capability_type' => 'post',
                'publicly_queryable' => true,
                'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
                'rewrite' => array( 'slug' => apply_filters( 'loftocean_single_room_rewrite_slug', 'room' ) )
            ) );
            register_taxonomy( 'lo_room_type',array( $this->post_type ), array(
                    'hierarchical' => false,
                    'labels' => array(
                    'name' => esc_html__( 'Room Type', 'loftocean' ),
                    'singular_name' => esc_html__( 'Room Type', 'loftocean' )
                ),
                'show_ui' => true,
                'public' => false,
                'query_var' => true,
                'meta_box_cb' => false,
                'show_in_rest' => false,
                'show_admin_column' => true,
                'show_in_quick_edit' => false,
                'rewrite' => array( 'slug' => 'room-type' )
            ) );

            $this->register_defualt_metas();
        }
        /**
        * Parse custom block content
        */
        public function parse_content( $pid ) {
            echo apply_filters( 'loftocean_get_custom_room_content', '', $pid );
        }
        /**
        * Get custom block content
        */
        public function get_content( $content, $pid ) {
            $pid = apply_filters( 'loftocean_multilingual_get_post_id', $pid, $this->post_type );
            $custom_post = get_post( $pid );
            $content = '';

            if ( ! $custom_post || $custom_post->post_type != $this->post_type || ! $pid ) {
                return $content;
            }

            if ( apply_filters( 'loftocean_is_built_with_elementor', false, $pid ) ) {
                $content .= apply_filters( 'loftocean_elementor_parse_content', '', $pid );
            } else {
                $content .= do_shortcode( $custom_post->post_content );
                $shortcodes_custom_css = get_post_meta( $pid, '_wpb_shortcodes_custom_css', true );
                $loftocean_shortcodes_custom_css = get_post_meta( $pid, 'loftocean_shortcodes_custom_css', true );

                if ( ! empty( $shortcodes_custom_css ) || ! empty( $loftocean_shortcodes_custom_css ) ) {
                    $content .= '<style data-type="vc_shortcodes-custom-css">';
                    if ( ! empty( $shortcodes_custom_css ) ) {
                        $content .= $shortcodes_custom_css;
                    }

                    if ( ! empty( $loftocean_shortcodes_custom_css ) ) {
                        $content .= $loftocean_shortcodes_custom_css;
                    }
                    $content .= '</style>';
                }
            }
            return $content;
        }
        /**
        * Add new column for shortcode
        * @param array
        * @return array
        */
        public function add_column( $columns ) {
            return array_merge( $columns, array( 'loftocean-block-shortcode' => esc_html__( 'Shortcode', 'loftocean' ) ) );
        }
        /**
        * Display shortcode column html
        * @param array
        * @param int
        * @return array
        */
        public function show_shortcode( $column, $post_id ) {
            if ( 'loftocean-block-shortcode' == $column ) : ?>
                <strong>[lo_custom_block id="<?php echo esc_attr( $post_id ); ?>"]</strong><?php
            endif;
        }
        /**
        * Parse shortcode
        */
        public function parse_shortcode( $atts ) {
            $atts = shortcode_atts( array( 'id' => 0 ), $atts );
            return apply_filters( 'loftocean_get_room_content', '', $atts[ 'id' ] );
        }
        /**
        * Add the duplicate link to action list for post_row_actions
        */
        public function duplicate_button( $actions, $post ) {
            $post_status = 'publish';
            if ( current_user_can( 'edit_posts' ) && ( $this->post_type == $post->post_type ) ) {
                $url = add_query_arg( array(
                    'action' => 'lo_duplicate_room',
                    'post' => $post->ID,
                    'nonce' => wp_create_nonce( 'lo-duplicate-' . $post->ID )
                ), admin_url( 'admin.php' ) );
                $actions[ 'lo_duplicate' ] = sprintf(
                    '<a href="%1$s" title="%2$s" rel="permalink">%3$s</a>',
                    $url,
                    esc_attr__( 'Duplicate', 'loftocean' ),
                    esc_html__( 'Duplicate', 'loftocean' )
                );
            }
            return $actions;
        }
        /*
        * Action callback function
        */
        public function do_duplicate_action() {
            $nonce = sanitize_text_field( wp_unslash( $_REQUEST[ 'nonce' ] ) );
            $post_id = isset( $_GET[ 'post' ] ) ? intval( wp_unslash( $_GET[ 'post' ] ) ) : intval( wp_unslash( $_POST[ 'post' ] ) );
            $original_post = get_post( $post_id );
            $current_user_id = get_current_user_id();
            if ( wp_verify_nonce( $nonce, 'lo-duplicate-' . $post_id ) ) {
                if ( current_user_can( $this->capability ) || current_user_can( 'edit_others_posts' ) ) {
                    global $wpdb;
                    $returnpage = '';
                    $current_user = wp_get_current_user();
                    $new_post_author = $current_user->ID;

                    if ( isset( $original_post ) && $original_post != null ) {
                        $new_post_id = wp_insert_post( array(
                            'comment_status' => $original_post->comment_status,
                            'ping_status' => $original_post->ping_status,
                            'post_author' => $new_post_author,
                            'post_content' => $original_post->post_content,
                            'post_excerpt' => $original_post->post_excerpt,
                            'post_parent' => $original_post->post_parent,
                            'post_password' => $original_post->post_password,
                            'post_status' => $original_post->post_status,
                            'post_title' => 'Copied ' . $original_post->post_title,
                            'post_type' => $original_post->post_type,
                            'to_ping' => $original_post->to_ping,
                            'menu_order' => $original_post->menu_order,
                        ) );
                        $taxonomy = 'custom_blocks_category';
                        $post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
                        if ( \LoftOcean\is_valid_array( $post_terms ) ) {
                            wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
                        }
                        $post_meta_infos = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=%d", $post_id ) );
                        if ( count( $post_meta_infos ) !=0 ) {
                            $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
                            foreach ($post_meta_infos as $meta_info) {
                                $meta_key = sanitize_text_field( $meta_info->meta_key );
                                $meta_value = addslashes( $meta_info->meta_value );
                                $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
                            }
                            $sql_query.= implode( " UNION ALL ", $sql_query_sel );
                            $wpdb->query( $sql_query );
                        }
                        if ( apply_filters( 'loftocean_is_built_with_elementor', false, $new_post_id ) ) {
                            $post_css = \Elementor\Core\Files\CSS\Post::create( $new_post_id );
                            $post_css->update();
                        }
                        wp_redirect( esc_url_raw( admin_url( 'edit.php?post_type=' . $original_post->post_type ) ) );
                    } else {
                        wp_die( esc_html__( 'Error! Post creation failed, could not find original post: ', 'loftocean' ) . $post_id );
                    }
                } else {
                    wp_die( esc_html__( 'Unauthorized Access.', 'loftocean' ) );
                }
            } else {
                wp_die( esc_html__( 'Security check issue, Please try again.', 'loftocean' ) );
            }
        }
        /**
        * Register meta boxes
        */
        public function register_meta_boxes() {
            remove_meta_box( 'pageparentdiv', $this->post_type, 'side' );
            add_meta_box( 'loftocean-room-list-thumbnail', esc_html__( 'List Thumbnail (Optional)', 'loftocean' ), array( $this, 'meta_box_list_thumbnail' ), $this->post_type, 'side', 'default' );
            add_meta_box( 'loftocean-room-list-gallery', esc_html__( 'Room Gallery (Optional)', 'loftocean' ), array( $this, 'meta_box_gallery' ), $this->post_type, 'side', 'default' );
            add_meta_box( 'loftocean-room-settings-meta-box', esc_html__( 'Room Settings', 'loftocean' ), array( $this, 'meta_box_room_settings' ), $this->post_type, 'advanced', 'default' );

            wp_enqueue_script( 'loftocean-select2', LOFTOCEAN_URI . 'assets/libs/select2/js/select2.full.min.js', array( 'jquery' ), '4.0.13', true );
            wp_enqueue_style( 'loftocean-select2', LOFTOCEAN_URI . 'assets/libs/select2/css/select2.min.css', array(), '4.0.13' );
            wp_enqueue_style( 'loftocean-room-settings', LOFTOCEAN_URI . 'assets/styles/room-settings.min.css', array(), LOFTOCEAN_ASSETS_VERSION );
        }
        /*
        * Meta box list thumbnail
        */
        public function meta_box_list_thumbnail( $post ) {
            require_once LOFTOCEAN_DIR . 'includes/custom-post-types/rooms/view/meta/list-thumbnail.php';
        }
        /*
        * Meta box gallery
        */
        public function meta_box_gallery( $post ) {
            require_once LOFTOCEAN_DIR . 'includes/custom-post-types/rooms/view/meta/gallery.php';
        }
        /*
        * Meta box room settings HTML
        */
        public function meta_box_room_settings( $post ) {
            $this->import_files(); ?>
            <div class="panel-wrap room-data-settings">
                <ul class="loftocean-tabs loftocean-room-settings-tabs">
                    <?php do_action( 'loftocean_room_the_settings_tabs', $post->ID ); ?>
                </ul>
                <?php do_action( 'loftocean_room_the_settings_panel', $post->ID ); ?>
                <div class="clear"></div>
            </div><?php
        }
        /**
        * Woocommerce order details
        */
        public function woocommerce_order_details( $item_id, $item, $order ) {
            if ( 'WC_Order_Item_Product' !== get_class( $item ) ) return;

            $room_id = get_post_meta( $item->get_product_id(), '_loftocean_booking_id', true );
            if ( ! empty( $room_id ) && ( $this->post_type == get_post_type( $room_id ) ) ) {
                $room_order_item_data = get_post_meta( $item->get_variation_id(), 'data', true );
                require LOFTOCEAN_DIR . 'includes/custom-post-types/rooms/order/room-item-details.php';
            }
        }
        /**
        * Woocommerce cart item details
        */
        public function woocommerce_cart_item_product( $item, $cart_index ) {
            if ( isset( $item[ 'loftocean_booking_data' ] ) && ( $this->post_type == get_post_type( $item[ 'loftocean_booking_data' ][ 'loftocean_booking_id' ] ) ) ) {
                $room_order_item_data = $item[ 'loftocean_booking_data' ];
                require LOFTOCEAN_DIR . 'includes/custom-post-types/rooms/order/room-item-details.php';
            }
        }
        /**
        * Woocommerce email item details
        */
        public function woocommerce_email_item_details( $item_id, $item, $order, $plain_text ) {
            if ( 'WC_Order_Item_Product' !== get_class( $item ) ) return;

            $room_id = get_post_meta( $item->get_product_id(), '_loftocean_booking_id', true );
            if ( ! empty( $room_id ) && ( $this->post_type == get_post_type( $room_id ) ) ) {
                $room_order_item_data = get_post_meta( $item->get_variation_id(), 'data', true );
                require LOFTOCEAN_DIR . 'includes/custom-post-types/rooms/order/room-item-details.php';
            }
        }
        /**
        * Mark is WooCommerce checkout page
        */
        public function is_checkout_page() {
            $this->is_woocommerce_checkout_page = true;
        }
        /**
        * Output room details for checkout page
        */
        public function output_room_details( $data, $item ) {
            if ( $this->is_woocommerce_checkout_page && isset( $item[ 'loftocean_booking_data' ] ) && ( $this->post_type == get_post_type( $item[ 'loftocean_booking_data' ][ 'loftocean_booking_id' ] ) ) ) {
                $room_order_item_data = $item[ 'loftocean_booking_data' ];
                require LOFTOCEAN_DIR . 'includes/custom-post-types/rooms/order/room-item-details.php';
            }
            return $data;
        }
        /**
        * Disable WooCommerce checkout page
        */
        public function disable_checkout_page() {
            $this->is_woocommerce_checkout_page = false;
        }
        /**
        * Woocommerce cart item quantity
        */
        public function woocommerce_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
            if ( isset( $cart_item[ 'loftocean_booking_data' ] ) && ( $this->post_type == get_post_type( $cart_item[ 'loftocean_booking_data' ][ 'loftocean_booking_id' ] ) ) ) {
                return $product_quantity . '<div class="quantity">-</div>'; // $product_quantity . '<div class="quantity">' . $cart_item[ 'quantity' ] . '</div>';
            }
            return $product_quantity;
        }
        /**
        * Woocommerce cart item details
        */
        public function woocommerce_cart_item_thumbnail( $thumbnail, $item, $cart_index ) {
            if ( isset( $item[ 'loftocean_booking_data' ] ) && ( $this->post_type == get_post_type( $item[ 'loftocean_booking_data' ][ 'loftocean_booking_id' ] ) ) ) {
                return get_the_post_thumbnail( $item[ 'loftocean_booking_data' ][ 'loftocean_booking_id' ], 'thumbnail' );
            }
            return $thumbnail;
        }
        /**
        * Woocommerce admin order item details
        */
        public function woocommerce_admin_order_item_thumbnail( $thumbnail, $item_id, $item ) {
            $room_id = get_post_meta( $item->get_product_id(), '_loftocean_booking_id', true );
            if ( ! empty( $room_id ) && ( $this->post_type == get_post_type( $room_id ) ) ) {
                return get_the_post_thumbnail( $room_id, 'thumbnail' );
            }
            return $thumbnail;
        }
        /**
        * Save post metas
        * @param int post id
        * @param object
        * @param int
        */
        public function save_room_metas( $post_id, $post, $update ) {
            if ( empty( $update ) || ( $post->post_type != $this->post_type ) || empty( $_REQUEST[ 'loftocean_room_nonce' ] ) ) {
                return '';
            }
            if ( current_user_can( 'edit_post', $post_id ) ) {
                $list_thumbnail_id = wp_unslash( $_REQUEST[ 'loftocean_room_list_thumbnail_id' ] );
                update_post_meta( $post_id, 'loftocean_room_list_thumbnail_id', $list_thumbnail_id );

                $gallery_ids = sanitize_text_field( wp_unslash( $_REQUEST[ 'loftocean_room_gallery_ids' ] ) );
                update_post_meta( $post_id, 'loftocean_room_gallery_ids', $gallery_ids );

                $this->import_files();
                do_action( 'loftocean_save_room_settings', $post_id );
            }
        }
        /*
        * Front end actions
        */
        public function front_actions() {
            $this->check_single_product_page();
            if ( is_singular( $this->post_type ) ) {
                add_filter( 'body_class', array( $this, 'single_room_body_class' ), 999 );
                add_filter( 'post_class', array( $this, 'single_room_post_class' ), 999 );
                add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
                add_action( 'wp_footer', array( $this, 'load_templates' ), 99 );
                if ( ! apply_filters( 'loftocean_single_room_use_custom_template', false ) ) {
                    add_filter( 'loftocean_content_class', array( $this, 'single_room_page_content_class' ), 9999 );
                }
            } else if ( isset( $_GET[ 'search_rooms' ] ) && ( ! apply_filters( 'loftocean_rooms_search_page_use_custom_template', false ) ) ) {
                add_filter( 'loftocean_content_class', array( $this, 'search_rooms_page_content_class' ), 9999 );
                add_filter( 'body_class', array( $this, 'romms_search_body_class' ), 999 );
            }
        }
        /*
        * Body class for single room page
        */
        public function single_room_body_class( $class ) {
            $class = array_diff( $class, array( 'loftocean_room-template-default', 'single-loftocean_room' ) );
            $class = array_merge( $class, array( 'room-template-default', 'single-room' ) );

            return $class;
        }
        /*
        * Post class for single room page
        */
        public function single_room_post_class( $class ) {
            $class = array_diff( $class, array( 'loftocean_room', 'type-loftocean_room' ) );
            $class = array_merge( $class, array( 'post', 'room', 'type-room' ) );

            return $class;
        }
        /*
        * Check content class for single room page
        */
        public function single_room_page_content_class( $class ) {
            $class = array_diff( $class, array( 'with-sidebar-right', 'with-sidebar-left' ) );
            if ( class_exists( 'WooCommerce' ) ) {
                $roomDetails = apply_filters( 'loftocean_get_room_details', array(), get_queried_object_id() );
                if ( isset( $roomDetails, $roomDetails[ 'roomSettings' ], $roomDetails[ 'roomSettings' ][ 'bookingForm' ] ) ) {
                    if ( in_array( $roomDetails[ 'roomSettings' ][ 'bookingForm' ], array( 'right', '' ) ) ) {
                        array_push( $class, 'with-sidebar-right' );
                    } else if ( 'left' == $roomDetails[ 'roomSettings' ][ 'bookingForm' ] ) {
                        array_push( $class, 'with-sidebar-left' );
                    }
                }
            }
            return $class;
        }
        /*
        * Body class for rooms search page
        */
        public function romms_search_body_class( $class ) {
            $class = array_diff( $class, array( 'home', 'blog' ) );
            $class = array_merge( $class, array( 'rooms-search-results', 'search' ) );

            return $class;
        }
        /*
        * Check content class for rooms search page
        */
        public function search_rooms_page_content_class( $class ) {
            $class = array_diff( $class, array( 'with-sidebar-right', 'with-sidebar-left' ) );
            array_push( $class, 'with-sidebar-left' );
            return $class;
        }
        /**
        * Check single product page
        */
        public function check_single_product_page() {
            if ( ( ! is_admin() ) && is_singular( 'product' ) ) {
                $product_id = get_queried_object_id();
                $room_id = get_post_meta( $product_id, '_loftocean_booking_id', true );
                if ( ( ! empty( $room_id ) ) && ( $this->post_type == get_post_type( $room_id ) ) ) {
            		wp_redirect( get_the_permalink( $room_id ) );
            		die();
                }
        	}
        }
        /*
        * Room pages enqueue scripts
        */
        public function enqueue_scripts() {
            $roomID = get_queried_object_id();
            if ( ! empty( $roomID ) && ( $this->post_type == get_post_type( $roomID ) ) ) {
                $room_details = apply_filters( 'loftocean_get_room_details', array(), $roomID );
                $maximal_months_allowed = apply_filters( 'loftocean_room_reservation_maximal_months', 16 );
                $maximal_months_allowed = is_numeric( $maximal_months_allowed ) && ( $maximal_months_allowed > 0 ) ? $maximal_months_allowed : 16;

                $today_date = date( 'Y-m-d' );
                $prices = apply_filters( 'loftocean_get_room_reservation_data', array(), $roomID, $today_date, date( 'Y-m-d', strtotime( '+' . $maximal_months_allowed . ' month' ) ) );
                $current_flexible_price_rules = apply_filters( 'loftocean_get_room_current_flexible_rules', array(), $roomID );
                $has_flexible_price_rules = \LoftOcean\is_valid_array( $current_flexible_price_rules );
                $tax_enabled = \LoftOcean\is_tax_enabled();

                $bookingFormData = apply_filters( 'loftocean_room_booking_url_param', array() );
                $params_from_search = apply_filters( 'loftocean_pass_params_from_search_result', false ) && \LoftOcean\is_valid_array( $bookingFormData ) && isset( $bookingFormData[ 'checkin' ], $bookingFormData[ 'checkout' ] );

                $current_room_variable_prices = \LoftOcean\get_room_variable_prices( $roomID );

                wp_enqueue_style( 'jquery-daterangepicker', LOFTOCEAN_ASSETS_URI . 'libs/daterangepicker/daterangepicker.min.css', array(), '3.1.1' );
                wp_enqueue_style( 'loftocean-lightbox-style', LOFTOCEAN_ASSETS_URI . 'libs/lightbox/simple-lightbox.min.css', array(), '2.13.0' );
                wp_enqueue_script( 'moment', LOFTOCEAN_ASSETS_URI . 'libs/daterangepicker/moment.min.js', array(), '2.18.1', true );
                wp_enqueue_script( 'jquery-daterangepicker', LOFTOCEAN_ASSETS_URI . 'libs/daterangepicker/daterangepicker.min.js', array( 'jquery', 'moment' ), LOFTOCEAN_ASSETS_VERSION, true );

                wp_enqueue_script( 'loftocean-lightbox-script', LOFTOCEAN_ASSETS_URI . 'libs/lightbox/simple-lightbox.min.js', array( 'jquery' ), '2.13.0', true );
                wp_enqueue_script( 'loftocean-room-reservation', LOFTOCEAN_ASSETS_URI . 'scripts/front/room-reservation.min.js', array( 'jquery-daterangepicker', 'wp-api-request', 'wp-util' ), LOFTOCEAN_ASSETS_VERSION, true );
                wp_localize_script( 'loftocean-room-reservation', 'loftoceanRoomReservation', array(
                    'roomID' => $roomID,
                    'ajaxURL' => esc_js( admin_url( 'admin-ajax.php' ) ),
                    'addRoomToCartAjaxAction' => 'add_room_to_cart',
                    'getFlexiblePriceRuleAjaxAction' => 'get_room_discount',
                    'currency' => \LoftOcean\get_current_currency(),
                    'currencySettings' => \LoftOcean\get_current_currency_settings(),
                    'priceList' => \LoftOcean\is_valid_array( $prices ) ? array_combine( array_column( $prices, 'id' ), $prices ) : array(),
                    'i18nText' => array(
                        'getRemotePriceListErrorMessage' => esc_html__( 'Failed to get the price list for the date range picked. Please try again later.', 'loftocean' ),
                        'bookingError' => esc_html__( 'Something goes wrong, please try again later.', 'loftocean' ),
                        'bookingSuccess' => esc_html__( 'The room reservation has been successfully added to your cart.', 'loftocean' ),
                        'totalBasePriceLabel' => esc_html__( 'Total Base Price', 'loftocean' ),
                        'baseDiscountLabel' => esc_html__( 'Base Price Discount', 'loftocean' ),
                        'extraServiceLabel' => esc_html__( 'Extra Services', 'loftocean' ),
                        'totalPriceLabel' => esc_html__( 'Total', 'loftocean' ),
                        'noCheckin' => esc_html__( 'No Check-in', 'loftocean' ),
                        'noCheckout' => esc_html__( 'No Checkout', 'loftocean' ),
                        'minimum' => esc_html__( '-night Minimum', 'loftocean' ),
                        'maximum' => esc_html__( '-night Maximum', 'loftocean' ),
                        'adult' => array(
                            'minus' => array( 'general' => _x( 'Minimum [number] adults', 'keep the string [number]', 'loftocean' ), 'minimum' => _x( 'Minimum [number] guests', 'keep the string [number]', 'loftocean' ) ),
                            'plus' => array( 'general' => _x( 'Maximum [number] adults', 'keep the string [number]', 'loftocean' ), 'maximum' => _x( 'Maximum [number] guests', 'keep the string [number]', 'loftocean' ) )
                        ),
                        'child' => array(
                            'minus' => array( 'general' => _x( 'Minimum [number] children', 'keep the string [number]', 'loftocean' ), 'minimum' => _x( 'Minimum [number] guests', 'keep the string [number]', 'loftocean' ) ),
                            'plus' => array( 'general' => _x( 'Maximum [number] children', 'keep the string [number]', 'loftocean' ), 'maximum' => _x( 'Maximum [number] guests', 'keep the string [number]', 'loftocean' ) )
                        )
                    ),
					'cartPage' => class_exists( 'WooCommerce' ) ? wc_get_cart_url() : '',
                    'pricePerPerson' => ( 'on' == get_post_meta( $roomID, 'loftocean_room_price_by_people', true ) ),
                    'maximalMonthsAllowedForBooking' => $maximal_months_allowed,
                    'hasFlexibilePriceRules' => $has_flexible_price_rules ? 1 : 0,
                    'unavailableDates' => apply_filters( 'loftocean_room_get_unavailble_date', false, array( 'room_id' => $roomID ) ),
                    'extraServices' => apply_filters( 'loftocean_get_room_detailed_extra_services', array(), $roomID ),
                    'isTaxEnabled' => $tax_enabled,
                    'taxIncluded' => $tax_enabled && ( 'yes' == get_option( 'woocommerce_prices_include_tax' ) ),
                    'displayDateFormat' => apply_filters( 'loftocean_display_date_format', 'YYYY-MM-DD' ),
                    'taxRate' => $tax_enabled ? \LoftOcean\get_tax_rate() : 0,
                    'variablePrices' => $current_room_variable_prices,
                    'availabilityCalendarText' => array(
                        'cancel' => esc_html__( 'Cancel', 'loftocean' ),
                        'apply' => esc_html__( 'Apply', 'loftocean' )
                    ),
                    'guestLimitation' => array( 
                        'min' => is_numeric( $room_details[ 'roomSettings' ][ 'minPeople' ] ) ? $room_details[ 'roomSettings' ][ 'minPeople' ] : false, 
                        'max' => is_numeric( $room_details[ 'roomSettings' ][ 'maxPeople' ] ) ? $room_details[ 'roomSettings' ][ 'maxPeople' ] : false,
                        'maxAdult' => ( 'on' == $room_details[ 'roomSettings' ][ 'enableMaxAdultNumber' ] ) && is_numeric( $room_details[ 'roomSettings' ][ 'maxAdultNumber' ] ) ? $room_details[ 'roomSettings' ][ 'maxAdultNumber' ] : -1,
                        'maxChild' => ( 'on' == $room_details[ 'roomSettings' ][ 'enableMaxChildNumber' ] ) && is_numeric( $room_details[ 'roomSettings' ][ 'maxChildNumber' ] ) ? $room_details[ 'roomSettings' ][ 'maxChildNumber' ] : -1
                    ),
                    'passParamsFromSearchResultPage' => !! $params_from_search,
                    'searchResultParams' => array(
                        'checkin' => $params_from_search ? $bookingFormData[ 'checkin' ] : '',
                        'checkout' => $params_from_search ? $bookingFormData[ 'checkout' ] : ''
                    )
                ) );
            }
        }
        /**
        * Import room setting files
        */
        protected function import_files() {
            $dir = LOFTOCEAN_DIR . 'includes/custom-post-types/rooms/view/meta/';

            require_once $dir . 'class-settings-general.php';
            require_once $dir . 'class-settings-layout.php';
            require_once $dir . 'class-settings-price.php';
            require_once $dir . 'class-settings-facility.php';
            require_once $dir . 'class-settings-availability.php';
            require_once $dir . 'class-settings-booking-calendar.php';
            require_once $dir . 'class-settings-booking-rules.php';
            require_once $dir . 'class-settings-flexible-price-rules.php';
            require_once $dir . 'class-settings-extra-service.php';
        }
        /**
        * Check settings for admin section
        */
        public function admin_filter() {
			global $pagenow;
            if ( is_admin() && ( ! empty( $pagenow ) ) && ( 'post.php' == $pagenow ) && isset( $_GET[ 'post' ] ) ) {
                $room_id = get_post_meta( $_GET[ 'post' ], '_loftocean_booking_id', true );
                if ( $room_id && ( $this->post_type == get_post_type( $room_id ) ) ) {
                    wp_redirect( admin_url( 'post.php?post=' . $room_id . '&action=edit' ) );
                }
            }
        }
        /**
        * Filter room products in product list page
        */
        public function hide_room_product( $wp_query ) {
            global $pagenow;
            if ( is_admin() && ( ! empty( $pagenow ) ) && ( 'edit.php' == $pagenow ) && isset( $_GET[ 'post_type' ] ) && ( 'product' == $_GET[ 'post_type' ] ) ) {
                $wp_query->set( 'meta_query', array(
                    array( 'key' => '_loftocean_booking_id', 'compare' => 'NOT EXISTS' )
                ) );
            }
        }
        /**
        * Register metas with default value
        */
        public function register_defualt_metas() {
            register_post_meta( $this->post_type, 'loftocean_room_number', array( 'single' => true, 'type' => 'number', 'default' => 10 ) );
        }
        /**
        * Load JavaScript templates
        */
        public function load_templates() {
            require_once LOFTOCEAN_DIR . 'template-parts/reservation-templates.php';
        }
        /**
        * Get all rooms
        */
        public function get_all_rooms( $rooms ) {
            $rooms = array();
            $ppp = 50;
            $args = array( 'offset' => 0, 'posts_per_page' => $ppp, 'fields' => 'ids', 'orderby' => 'title', 'order' => 'ASC', 'post_status' => 'any', 'post_type' => $this->post_type );

            do {
                $items = new \WP_Query( $args );
                $items = \LoftOcean\is_valid_array( $items->posts ) ? $items->posts : array();
                foreach ( $items as $item ) {
                    if ( ! in_array( $item, $rooms ) ) {
                        array_push( $rooms, $item );
                    }
                }
                $args[ 'offset' ] += $ppp;
            } while( count( $items ) === $ppp );
            return $rooms;
        }
    }
    new Room();
}
