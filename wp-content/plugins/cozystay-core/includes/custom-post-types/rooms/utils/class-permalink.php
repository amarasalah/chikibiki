<?php
namespace LoftOcean\Room;

if ( ! class_exists( '\LoftOcean\Room\Permalink' ) ) {
    class Permalink {
        /**
        * String Post type
        */
        protected $post_type = 'loftocean_room';
        /**
        * Permalink settings.
        */
        private $permalinks = '';
        /**
        * construction function
        */
        public function __construct() {
            add_action( 'current_screen', array( $this, 'conditional_includes' ) );

            add_filter( 'loftocean_single_room_rewrite_slug', array( $this, 'get_room_slug' ), 10, 1 );

            $this->check_room_permalink();
        }
        /*
        * Screen based actions
        */
        public function conditional_includes() {
            $screen = get_current_screen();

            if ( ! $screen ) {
                return;
            }

            switch ( $screen->id ) {
                case 'options-permalink':
                    $this->settings_init();
                    $this->settings_save();
            }
        }
        /**
        * Init permalink settings.
        */
        public function settings_init() {
            add_settings_field(
                'loftocean_room_slug',
                esc_html__( 'Room Base', 'loftocean' ),
                array( $this, 'room_slug_input' ),
                'permalink',
                'optional'
            );

            $this->permalinks = get_option( 'loftocean_room_permalink', 'room' );
        }
        /**
        * Show a slug input box.
        */
        public function room_slug_input() { ?>
            <input name="loftocean_room_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $this->permalinks ); ?>" placeholder="<?php esc_attr_e( 'Room slug', 'loftocean' ); ?>" /><?php
        }
        /**
        * Save the settings.
        */
        public function settings_save() {
            if ( ! is_admin() ) {
                return;
            }

            if ( isset( $_POST[ 'permalink_structure' ], $_POST[ 'loftocean_room_slug' ] ) ) {
                $permalinks = sanitize_text_field( wp_unslash( $_POST[ 'loftocean_room_slug' ] ) ); // WPCS: input var ok, sanitization ok.

                update_option( 'loftocean_room_permalink', $permalinks );
            }
        }
        /*
        * Get room slug
        */
        public function get_room_slug( $slug ) {
            $value = get_option( 'loftocean_room_permalink', 'room' );
            return $value;
        }
        /*
        * Check room permalink
        */
        protected function check_room_permalink() {
            $current = apply_filters( 'loftocean_single_room_rewrite_slug', '' );
            if ( empty( $current ) ) {
                // remove CPT base slug from URLs
                add_filter( 'post_type_link', array( $this, 'remove_slug' ), 10, 3 );
                add_filter( 'request', array( $this, 'request_check' ), 10, 1 );
        
                // auto redirect old URLs to non-base versions
                add_action( 'template_redirect', array( $this, 'redirect_check' ), 1 );
            }
        }
        /*
        * Template redirect check
        */
        public function redirect_check() {
            global $post;
            if ( ! is_preview() && is_singular( $this->post_type ) && is_object( $post ) ) {
                $new_url = get_permalink();
                $real_url = $this->get_current_url();
                if ( substr_count( $new_url, '/' ) != substr_count( $real_url, '/' ) && strstr( $real_url, $new_url ) == false ) {
                    remove_filter( 'post_type_link', array( $this, 'remove_slug' ), 10 );
                    $old_url = get_permalink();
                    add_filter( 'post_type_link', array( $this, 'remove_slug' ), 10, 3 );
                    $fixed_url = str_replace( $old_url, $new_url, $real_url );
                    wp_redirect( $fixed_url, 301 );
                }
            }
        }
        /* 
        * To parse the request 
        */
        public function request_check( $query_vars ) {
            if( ! is_admin() && ! isset( $query_vars['post_type'] ) && ( ( isset( $query_vars['error'] ) && $query_vars['error'] == 404 ) || isset( $query_vars['pagename'] ) || isset( $query_vars['attachment'] ) || isset( $query_vars['name'] ) || isset( $query_vars['category_name'] ) ) ) {

                $web_roots = array();
                $web_roots[] = site_url();
                if ( site_url() != home_url() ){
                    $web_roots[] = home_url();
                }
                // polylang fix
                if ( function_exists( 'pll_home_url' ) ) {
                    if ( site_url() != pll_home_url() ) {
                        $web_roots[] = pll_home_url();
                    }
                }

                foreach( $web_roots as $web_root ) {
                    // get clean current URL path
                    $path = $this->get_current_url();
                    $path = str_replace( $web_root, '', $path );
                    $path = trim( $path, '/' );

                    // clean custom rewrite endpoints
                    $path = explode( '/', $path );
                    foreach( $path as $i => $path_part ){
                        if ( isset( $query_vars[ $path_part ] ) ) {
                            $path = array_slice( $path, 0, $i );
                            break;
                        }
                    }
                    $path = implode( '/', $path );

                    $post_data = get_page_by_path( $path, OBJECT, $this->post_type );
                    if ( is_object( $post_data ) ) {
                        $post_name = $post_data->post_name;
                        $ancestors = get_post_ancestors( $post_data->ID );
                        foreach ( $ancestors as $ancestor ) {
                            $post_name = get_post_field( 'post_name', $ancestor ) . '/' . $post_name;
                        }
                        unset( $query_vars['error'] );
                        unset( $query_vars['pagename'] );
                        unset( $query_vars['attachment'] );
                        unset( $query_vars['category_name'] );
                        $query_vars['page'] = '';
                        $query_vars['name'] = $path;
                        $query_vars['post_type'] = $post_data->post_type;
                        $query_vars[ $post_data->post_type ] = $path;
                        break;
                    } else {
                        global $wp_rewrite;
                        // get CPT slug and its length
                        $query_var = get_post_type_object( $this->post_type )->query_var;
                        foreach( $wp_rewrite->rules as $pattern => $rewrite ){
                            // test only rules for this CPT
                            if ( strpos( $pattern, $query_var ) !== false ) {
                                if ( strpos( $pattern, '(' . $query_var . ')' ) === false ) {
                                    preg_match_all( '#' . $pattern . '#', '/' . $query_var . '/' . $path, $matches, PREG_SET_ORDER );
                                } else {
                                    preg_match_all( '#' . $pattern . '#', $query_var . '/' . $path, $matches, PREG_SET_ORDER );
                                }

                                if ( count( $matches ) !== 0 && isset( $matches[0] ) ) {
                                    $rewrite = str_replace( 'index.php?', '', $rewrite );
                                    parse_str( $rewrite, $url_query );
                                    foreach ( $url_query as $key => $value ) {
                                        $value = (int)str_replace( array( '$matches[', ']' ), '', $value );
                                        if ( isset( $matches[0][ $value ] ) ) {
                                            $value = $matches[0][ $value ];
                                            $url_query[ $key ] = $value;
                                        }
                                    }
                                    if ( isset( $url_query[ $query_var ] ) ) {
                                        $post_data = get_page_by_path( '/' . $url_query[ $query_var ], OBJECT, $this->post_type );
                                        if ( is_object( $post_data ) ) {
                                            unset( $query_vars['error'] );
                                            unset( $query_vars['pagename'] );
                                            unset( $query_vars['attachment'] );
                                            unset( $query_vars['category_name'] );
                                            $query_vars['page'] = '';
                                            $query_vars['name'] = $path;
                                            $query_vars['post_type'] = $post_data->post_type;
                                            $query_vars[ $post_data->post_type ] = $path;
                                            foreach ( $url_query as $key => $value ) {
                                                if ( $key != 'post_type' && substr( $value, 0, 8 ) != '$matches' ) {
                                                    $query_vars[ $key ] = $value;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $query_vars;
        }
        /*
        * Remove post type slug base
        */
        public function remove_slug( $permalink, $post, $leavename ) {
            if ( $this->post_type == $post->post_type ) {
                $permalink = str_replace( '/' . $this->post_type . '/', '/', $permalink );
            }
            return $permalink;
        }
        /*
        * Get current url
        */
        public function get_current_url() {
            $REQUEST_URI = strtok( $_SERVER['REQUEST_URI'], '?' );
            $real_url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) ? 'https://' : 'http://';
            $real_url .= $_SERVER['SERVER_NAME'] . $REQUEST_URI;
            return $real_url;
        }
    }
    new Permalink();
}