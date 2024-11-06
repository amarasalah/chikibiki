<?php
namespace LoftOcean\Multilingual;

if ( ! class_exists( '\LoftOcean\Multilingual\Polylang' ) ) {
    class Polylang {
        /**
        * String current language from front
        */
        protected $current_language = '';
        /**
        * String defualt language from front
        */
        protected $default_language = '';
        /**
        * Array active language list
        */
        protected $active_languages = array();
        /**
        * Construct function
        */
        public function __construct() {
            add_action( 'wp_loaded', array( $this, 'load_hooks' ) );
            add_action( 'wp_body_open', array( $this, 'load_front_hooks' ) );
            add_action( 'init', array( $this, 'room_tranlation' ), 99 );

            add_filter( 'pll_translation_url', array( $this, 'check_room_search_url' ), 99, 2 );
            add_filter( 'pll_get_taxonomies', array( $this, 'get_taxnomies_for_settings' ), 99, 2 );
        }
        /**
        * Init hooks
        */
        public function load_hooks() {
            if ( ( ! is_admin() ) || wp_doing_ajax() ) {
                $this->current_language = pll_current_language( 'slug' );
                $this->default_language = pll_default_language( 'slug' );
                if ( ! empty( $this->current_language ) && ! empty( $this->default_language ) && ( $this->default_language != $this->current_language ) ) {
                    $options = apply_filters( 'loftocean_translate_page_options', array( 'page_on_front' ) );
                    $terms = apply_filters( 'loftocean_translate_taxomony', array() );

                    add_filter( 'loftocean_ajax_load_more_parameters', array( $this, 'make_ajax_translatable' ) );
                    if ( is_array( $options ) && ( count( $options ) > 0 ) ) {
                        foreach( $options as $option ) {
                            add_filter( 'option_' . $option, array( $this, 'get_current_page_id' ), 9999, 2 );
                        }
                    }
                    if ( is_array( $terms ) && ( count( $terms ) > 0 ) ) {
                        foreach( $terms as $filter ) {
                            add_filter( $filter, array( $this, 'get_current_taxonomy' ), 9999 );
                        }
                    }
                }
                add_filter( 'loftocean_multilingual_get_post_id', array( $this, 'get_post_id' ), 10, 2 );
                // add_filter( 'woocommerce_get_cart_url', array( $this, 'cart_url' ), 99 );
                // add_filter( 'woocommerce_get_checkout_url', array( $this, 'checkout_url' ), 99 );
                // add_filter( 'woocommerce_get_script_data', array( $this, 'woocommerce_ajax_script_data' ), 99, 2 );
                // add_filter( 'woocommerce_ajax_get_endpoint', [ $this, 'add_language_to_endpoint' ] );
                // add_action( 'woocommerce_checkout_order_review', array( $this, 'woocommerce_order_review' ), 99 );
            }
        }
        /**
        * Actions for room translation
        */
        public function room_tranlation() {
            $this->active_languages = pll_the_languages( array( 'echo' => false, 'raw' => true ) );
            $this->default_language = pll_default_language( 'slug' );

            if ( \LoftOcean\is_valid_array( $this->active_languages ) ) {
                $this->active_languages = array_column( $this->active_languages, 'slug' );
                $this->active_languages = array_combine( $this->active_languages, $this->active_languages );
                if ( empty( $this->default_language ) ) {
                    $this->active_languages = array_keys( $this->active_languages );
                } else {
                    unset( $this->active_languages[ $this->default_language ] );
                    $this->active_languages = array_keys( $this->active_languages );
                    if ( count( $this->active_languages ) > 0 ) {
                        array_unshift( $this->active_languages, $this->default_language );
                    } else {
                        $this->active_languages = array( $this->default_language );
                    }
                }
            } else {
                $this->active_languages = array();
            }
            add_filter( 'loftocean_multilingual_get_room_ids', array( $this, 'get_room_ids' ), 10, 3 );
        }
        /**
        * Make the pages translatable
        */
        public function get_current_page_id( $value, $option ) {
            return empty( $value ) ? '' : pll_get_post( $value, $this->current_language );
        }
        /**
        * Make the categories translatable
        */
        public function get_current_taxonomy( $tax ) {
            if ( ! empty( $tax ) ) {
                if ( is_array( $tax ) ) {
                    $new_tax = array_map( function( $t ) {
                        return $this->get_translated_category( $t );
                    }, $tax );
                    return array_filter( $new_tax );
                } else {
                    return $this->get_translated_category( $tax );
                }
            }
            return $tax;
        }
        /**
        * Make ajax request translatable
        */
        public function make_ajax_translatable( $data ) {
            return array_merge( array( 'lang' => $this->current_language ), $data );
        }
        /**
        * Get translated category
        */
        protected function get_translated_category( $tax ) {
            if ( is_numeric ( $tax ) ) {
                return pll_get_term( $tax, $this->current_language );
            } else if ( is_string( $tax ) ) {
                $terms = get_terms( array( 'slug' => $tax, 'fields' => 'ids', 'taxonomy' => 'category', 'lang' => $this->default_language ) );
                if ( is_array( $terms ) ) {
                    $new_id = pll_get_term( $terms[0], $this->current_language );
                    if ( ! empty( $new_id ) ) {
                        $new_term = get_category( $new_id );
                        return $new_term->slug;
                    }
                }
            }
            return '';
        }
        /**
        * Front hooks only
        */
        public function load_front_hooks() {
            $mods = apply_filters( 'loftocean_translate_mc4wp_form', array() );
            add_filter( 'option_mc4wp_default_form_id', array( $this, 'get_current_mc4wp_form' ) );
            add_filter( 'loftocean_mc4wp_form_id', array( $this, 'get_current_mc4wp_form' ) );
            add_filter( 'loftocean_search_url', array( $this, 'search_url' ) );
            add_action( 'loftocean_search_form', array( $this, 'add_search_form_element' ) );
            if ( is_array( $mods ) && ( count( $mods ) > 0 ) ) {
                foreach ( $mods as $mod ) {
                    add_filter( 'theme_mod_' . $mod, array( $this, 'get_current_mc4wp_form' ) );
                }
            }
        }
        /**
        * Hook callback function to get current form id
        */
        public function get_current_mc4wp_form( $val ) {
            $current_settings = apply_filters( 'loftocean_translate_mc4wp_forms', array() );
            if ( is_array( $current_settings ) && isset( $current_settings['default'] ) && ( $val == $current_settings['default'] ) ) {
                return empty( $current_settings[ $this->current_language ] ) ? $val : $current_settings[ $this->current_language ];
            }
            return $val;
        }
        /**
        * Add lang hide element for search form
        */
        public function add_search_form_element() {
            if ( ! empty( $this->current_language ) && ( $this->current_language != $this->default_language ) ) : ?>
                <input type="hidden" name="lang" value="<?php echo esc_attr( $this->current_language ); ?>"><?php
            endif;
        }
        /**
        * Search url
        */
        public function search_url( $url ) {
            $pll = PLL();

            if ( get_class( $pll ) === 'PLL_Frontend' ) {
                return $pll->links_model->using_permalinks ? $pll->curlang->get_search_url() : $pll->links_model->home;
            }
            return $url;
        }
        /**
        * Get the translated post id
        */
        public function get_post_id( $pid, $post_type = '' ) {
             return empty( $pid ) ? '' : pll_get_post( $pid, $this->current_language );
        }
        /**
        * Get room ids
        */
        public function get_room_ids( $ids, $id, $include_current_id = true ) {
            if ( empty( $id ) || ( ! is_array( $this->active_languages ) ) || ( count( $this->active_languages ) < 2 ) ) return false;

            $new_ids = array();
            $post_type = 'loftocean_room';
            foreach( $this->active_languages as $language ) {
                $translated_id = pll_get_post( $id, $language );
                if ( ! empty( $translated_id ) ) {
                    if ( $include_current_id || ( $translated_id != $id ) ) {
                        $new_ids[ $language ] = $translated_id;
                    }
                }
            }
            return count( $new_ids ) > 0 ? $new_ids : false;
        }
        /**
        * Check room search result page url
        */
        public function check_room_search_url( $url, $language ) {
            $pll = PLL();
            if ( ( get_class( $pll ) === 'PLL_Frontend' ) && isset( $_GET, $_GET[ 'search_rooms' ], $_GET[ 'roomSearchNonce' ] ) ) {
                $search_url = $pll->model->get_language( $language )->get_search_url();
                $query_vars = array();
                $vars = array( 'room-quantity', 'adult-quantity', 'child-quantity', 'search_rooms', 'checkin', 'checkout', 'roomSearchNonce' );
                foreach( $vars as $var ) {
                    if ( isset( $_GET[ $var ] ) ) {
                        $query_vars[ $var ] = wp_unslash( $_GET[ $var ] );
                    }
                }
                return add_query_arg( $query_vars, $search_url );
            }

            return $url;
        }
        /**
        * Extra taxonomies for Polylang settings page
        */
        public function get_taxnomies_for_settings( $tax, $for_settings_page ) {
            if ( $for_settings_page ) {
                return array_merge( $tax, array(
                    'lo_room_type' => 'lo_room_type',
                    'lo_room_booking_rules' => 'lo_room_booking_rules',
                    'lo_room_extra_services' => 'lo_room_extra_services',
                    'lo_room_facilities' => 'lo_room_facilities'
                ) );
            }
            return $tax;
        }
        /*
        * WooCommerce cart url
        */
        public function cart_url( $url ) {
            $page_id = wc_get_page_id( 'cart' );
            if ( 0 < $page_id ) {
                $page_id = pll_get_post( $page_id, $this->current_language );
                if ( 0 < $page_id ) {
                    $url = get_permalink( $page_id );
                }
            }

            return $url;
        }
        /*
        * WooCommerce checkout url
        */
        public function checkout_url( $url ) {
            $page_id = wc_get_page_id( 'checkout' );
            if ( 0 < $page_id ) {
                $page_id = pll_get_post( $page_id, $this->current_language );
                if ( 0 < $page_id ) {
                    $url = get_permalink( $page_id );
                }
            }

            return $url;
        }
        /**
        * Add lang to woocommerce ajax params
        */
        public function woocommerce_ajax_script_data( $param, $handle ) {
            if ( \LoftOcean\is_valid_array( $param ) && isset( $param[ 'ajax_url' ] ) ) {
                $param[ 'ajax_url' ] = add_query_arg( 'lang', pll_current_language( 'slug' ), $param[ 'ajax_url' ] );
            }
            return $param;
        }
        /*
        * Add hidden input to order review form
        */
        public function woocommerce_order_review() {
            echo '<input type="hidden" name="lang" value="' . pll_current_language( 'slug' ) . '" />';
        }
        /*
        * Change endpoint url
        */
        public function add_language_to_endpoint( $endpoint ) { 
            $lang = pll_current_language( 'slug' );
            $default_lang = pll_default_language( 'slug' ); 
            $default_relative = wp_make_link_relative( pll_home_url( $default_lang ) );
            $current_relative = wp_make_link_relative( pll_home_url( $lang ) );

            $endpoint = str_replace( $default_relative, $current_relative, $endpoint );
            $endpoint = urldecode( $endpoint );

            return $endpoint;
        }
    }
    new Polylang();
}
