<?php
namespace LoftOcean\Multilingual;
if ( ! class_exists( '\LoftOcean\Multilingual\WPML' ) ) {
    class WPML {
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

            add_filter( 'icl_ls_languages', array( $this, 'check_room_search_url' ), 99, 1 );
        }
        /**
        * Init hooks
        */
        public function load_hooks() {
            if ( ( ! is_admin() ) || wp_doing_ajax() ) {
                $this->current_language = apply_filters( 'wpml_current_language', '' );
                $this->default_language = apply_filters( 'wpml_default_language', '' );

                $pages = apply_filters( 'loftocean_translate_page_options', array() );
                $attachment = apply_filters( 'loftocean_translate_attachment_options', array() );
                $terms = apply_filters( 'loftocean_translate_taxomony', array() );
                if ( is_array( $pages ) && ( count( $pages ) > 0 ) ) {
                    foreach ( $pages as $page ) {
                        add_filter( 'option_' . $page, array( $this, 'get_current_page_id' ) );
                    }
                }
                if ( is_array( $terms ) && ( count( $terms ) > 0 ) ) {
                    foreach( $terms as $filter ) {
                        add_filter( $filter, array( $this, 'get_current_taxonomy' ), 9999 );
                    }
                }
                if ( is_array( $attachment ) && ( count( $attachment ) > 0 ) ) {
                    foreach ( $attachment as $attach ) {
                        add_filter( $attach, array( $this, 'get_current_attachment_id' ) );
                    }
                }

                add_filter( 'loftocean_ajax_load_more_parameters', array( $this, 'make_ajax_translatable' ) );
                add_filter( 'loftocean_multilingual_get_post_id', array( $this, 'get_post_id' ), 10, 2 );
            }
        }
        /**
        * Actions for room translation
        */
        public function room_tranlation() {
            $this->active_languages = apply_filters( 'wpml_active_languages', array(), array( 'skip_missing' => 1 ) );
            $this->default_language = apply_filters( 'wpml_default_language', '' );
            if ( \LoftOcean\is_valid_array( $this->active_languages ) ) {
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
        * Init hooks
        */
        public function load_front_hooks() {
        	$forms = apply_filters( 'loftocean_translate_mc4wp_form', array() );
            if ( is_array( $forms ) && ( count( $forms ) > 0 ) ) {
                foreach ( $forms as $form ) {
            		add_filter( 'theme_mod_' . $form, array( $this, 'get_current_mc4wp_form' ) );
            	}
            }

            add_action( 'loftocean_search_form', array( $this, 'add_search_form_element' ) );
        }
        /**
        * Hook callback function to get current form id
        */
        public function get_current_mc4wp_form( $val ) {
        	return apply_filters( 'wpml_object_id', $val, 'mc4wp-form' );
        }
        /**
        * Make the pages translatable
        */
        public function get_current_page_id( $value, $option = '' ) {
            return empty( $value ) ? '' : apply_filters( 'wpml_object_id', $value, 'page' );
        }
        /**
        * Make the attachment translatable
        */
        public function get_current_attachment_id( $value ) {
            return empty( $value ) ? '' : apply_filters( 'wpml_object_id', $value, 'attachment' );
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
                return apply_filters( 'wpml_object_id', $tax, 'category' );
            } else if ( is_string( $tax ) ) {
                $terms = get_terms( array( 'slug' => $tax, 'fields' => 'ids', 'taxonomy' => 'category', 'lang' => $this->default_language ) );
                if ( is_array( $terms ) && ( ! empty( $terms ) ) ) {
                    $new_id = apply_filters( 'wpml_object_id', $terms[0], 'category', true, $this->current_language );
                    if ( ! empty( $new_id ) ) {
                        $new_term = get_category( $new_id );
                        return $new_term->slug;
                    }
                }
            }
            return '';
        }
        /**
        * Add lang hide element for search form
        */
        public function add_search_form_element() {
            do_action( 'wpml_add_language_form_field' );
        }
        /**
        * Get post id
        */
        public function get_post_id( $pid, $post_type ) {
            return empty( $pid ) ? '' : apply_filters( 'wpml_object_id', $pid, $post_type, true );
        }
        /**
        * Get room ids
        */
        public function get_room_ids( $ids, $id, $include_current_id = true ) {
            if ( empty( $id ) || ( ! is_array( $this->active_languages ) ) || ( count( $this->active_languages ) < 2 ) ) return false;

            $new_ids = array();
            $post_type = 'loftocean_room';
            foreach( $this->active_languages as $language ) {
                $translated_id = apply_filters( 'wpml_object_id', $id, $post_type, false, $language );
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
        public function check_room_search_url( $languages ) {
            if ( isset( $_GET, $_GET[ 'search_rooms' ], $_GET[ 'roomSearchNonce' ] ) ) {
                global $wpml_url_converter;
                $abs_home = $wpml_url_converter->get_abs_home();
                $query_vars = array();
                $vars = array( 'room-quantity', 'adult-quantity', 'child-quantity', 'search_rooms', 'checkin', 'checkout', 'roomSearchNonce' );
                foreach( $vars as $var ) {
                    if ( isset( $_GET[ $var ] ) ) {
                        $query_vars[ $var ] = wp_unslash( $_GET[ $var ] );
                    }
                }
                foreach( $languages as $language ) {
            		 $search_url = $wpml_url_converter->convert_url( $abs_home, $language[ 'code' ] );
                     $languages[ $language[ 'code' ] ][ 'url' ] = add_query_arg( $query_vars, $search_url );
                }
            }
            return $languages;
        }
        public function test() {
            $args = array('element_id' => 10, 'element_type' => 'category' );
            $my_category_language_code = apply_filters( 'wpml_element_language_code', null, $args );

            $args = array('element_id' => 10, 'element_type' => 'category' );
            $my_category_language_info = apply_filters( 'wpml_element_language_details', null, $args );
        }
    }
    new WPML();
}
