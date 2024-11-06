<?php
namespace LoftOcean\Custom_Post_Type;

if ( ! class_exists( '\LoftOcean\Custom_Post_Type\Custom_Site_Headers' ) ) {
    class Custom_Site_Headers {
        /**
        * String Post type
        */
        protected $post_type = 'custom_site_headers';
        /**
        * Construct function
        */
        public function __construct() {
            add_action( 'init', array( $this, 'register_posttype_site_header' ) );
            add_action( 'loftocean_the_custom_site_headers_content', array( $this, 'the_content' ), 10, 1 );
            add_action( 'admin_action_lo_duplicate_site_header', array( $this, 'do_duplicate_action' ) );
            add_filter( 'loftocean_get_custom_site_headers_content', array( $this, 'get_content' ), 10, 2 );
            add_filter( 'post_row_actions', array( $this, 'duplicate_button' ), 10, 2 );
        }
        /**
        * Create custom post types
        */
        public function register_posttype_site_header() {
            register_post_type( $this->post_type, array(
                'labels' => array(
                    'name' => esc_html__( 'Header Builder', 'loftocean' ),
                    'all_items' => __( 'All Site Headers', 'loftocean' ),
                    'add_new' => esc_html__( 'Add New Site Header', 'loftocean' ),
                    'add_new_item' => esc_html__( 'Add New Site Header', 'loftocean' ),
                    'singular_name' => esc_html__( 'Site Header', 'loftocean' )
                ),
                'show_ui' => true,
                'public' => true,
                'publicly_queryable' => true,
                'has_archive' => false,
                'rewrite' => array( 'slug' => 'custom-site-header' ),
                'capability_type' => 'post',
                'show_in_rest' => true
            ) );
        }
        /**
        * Parse custom block content
        */
        public function the_content( $pid ) {
            $content = apply_filters( 'loftocean_get_custom_site_headers_content', '', $pid );
            if ( ! empty( $content ) ) {
                $args = array( 'enable_overlap' => '' );
                if ( apply_filters( 'loftocean_is_built_with_elementor', false, $pid ) ) {
                    $args[ 'enable_overlap' ] = $this->get_setting( $pid, 'enable_overlap' );
                }
                do_action( 'loftocean_custom_site_headers_before', $args );
                echo $content;
                do_action( 'loftocean_custom_site_headers_after' );
            }
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
        * Get element settings
        */
        protected function get_setting( $pid, $setting ) {
            $settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );
            $settings_model = $settings_manager->get_model( $pid );
            $settings = $settings_model->get_data( 'settings' );
            return isset( $settings[ $setting ] ) ? $settings[ $setting ] : '';
        }
        /**
        * Add the duplicate link to action list for post_row_actions
        */
        public function duplicate_button( $actions, $post ) {
            $post_status = 'publish';
            if ( current_user_can( 'edit_posts' ) && ( $this->post_type == $post->post_type ) ) {
                $url = add_query_arg( array(
                    'action' => 'lo_duplicate_site_header',
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
                if ( current_user_can( 'manage_options' ) || current_user_can( 'edit_others_posts' ) ) {
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
    }
    new Custom_Site_Headers();
}
