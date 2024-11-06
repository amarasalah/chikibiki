<?php
namespace LoftOcean;
/*
 *************************************************************************************
 * Initial verison
 *		1. Initilize the post like view count for each post
 *************************************************************************************
 */
if ( ! class_exists( '\LoftOcean\Upgrader' ) ) {
	class Upgrader {
		/**
		* String current theme version
		*/
		protected $version = '0.1';
		/**
		* If the previous verion if older than current version,
		*	do the upgrade and update theme version
		*/
		public function __construct() {
			$this->version = LOFTOCEAN_THEME_VERSION;
			$old_version = get_option( LOFTOCEAN_THEME_PLUGIN_VERSION_META_NAME, '0.1' );
			if ( version_compare( $old_version, $this->version, '<' ) ) {
				add_action( 'init', array( $this, 'start_update' ), 1 );
			}
			add_action( 'loftocean_elementor_loaded', array( $this, 'check_custom_site_header' ) );
		}
		/**
		* Start updating process
		*/
		public function start_update() {
			$old_version = get_option( LOFTOCEAN_THEME_PLUGIN_VERSION_META_NAME, '0.1' );

			if ( version_compare( $old_version, '1.0.0', '<' ) ) {
				$this->init_updates();
				$this->elementor_updates();
			}
			if ( version_compare( $old_version, '1.0.4', '<' ) ) {
				if ( defined( 'ELEMENTOR_VERSION' ) && class_exists( '\Elementor\Plugin' ) ) {
					\Elementor\Plugin::$instance->files_manager->clear_cache();
				}
			}
			if ( version_compare( $old_version, '1.2.0', '<' ) ) {
				add_action( 'init', array( $this, 'remove_duplicated_facility' ), 150 );
			}
			$this->update_version();
		}
		/**
		* Initialize like and view count settings for each post
		*/
		protected function init_updates() {
		 	$metas = array( 'loftocean-view-count' => 0, 'loftocean-like-count' => 0 );
			$ppp = 50; $args = array(
				'fields' => 'ids',
				'offset' => 0,
				'posts_per_page' => $ppp,
				'post_type' => 'post'
			);
			do {
				$q = get_posts( $args );
				foreach ( $q as $pid ) {
					foreach( $metas as $meta => $default ) {
						$value = get_post_meta( $pid, $meta, true );
						if ( empty( $value ) ) {
							update_post_meta( $pid, $meta, $default );
						}
					}
				}
				$args['offset'] += $ppp;
			} while ( count( $q ) === $ppp );

			$post_types = (array)get_option( 'elementor_cpt_support', array() );
			$post_types[] = 'custom_blocks';
			$post_types[] = 'custom_site_headers';
			$post_types[] = 'loftocean_room';
			update_option( 'elementor_cpt_support', $post_types );
		}
		/**
		* Update elementor settings
		*/
		public function elementor_updates() {
			$post_types = (array)get_option( 'elementor_cpt_support', array() );
			$post_types[] = 'custom_blocks';
			$post_types[] = 'custom_site_headers';
			$post_types[] = 'loftocean_room';
			if ( ! in_array( 'page', $post_types ) ) {
				$post_types[] = 'page';
			}
			update_option( 'elementor_cpt_support', $post_types );
		}
		/**
		* Check initial custom site header
		*/
		public function check_custom_site_header() {
			if ( ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) return '';

			$init_custom_site_header = get_option( 'loftocean_init_custom_site_header', false );
			if ( empty( $init_custom_site_header ) && class_exists( '\LoftOcean\Elementor\Library\Source' ) ) {
				$data = $this->get_default_site_header_data();
				$source = new \LoftOcean\Elementor\Library\Source();
				$data = json_decode( $data, true );
				$data = $source->get_init_import_data( $data );
				$custom_header_id = wp_insert_post( array(
					'post_author' => get_current_user_id(),
					'post_status' => 'publish',
					'post_title' => esc_html__( 'Sample Site Header', 'loftocean' ),
					'post_type' => 'custom_site_headers',
				) );
				update_post_meta( $custom_header_id, '_elementor_edit_mode', 'builder' );
				update_post_meta( $custom_header_id, '_elementor_data', $data );
				update_post_meta( $custom_header_id, '_elementor_version', ELEMENTOR_VERSION );
				$post_css = \Elementor\Core\Files\CSS\Post::create( $custom_header_id );
				$post_css->update();
				update_option( 'loftocean_init_custom_site_header', $custom_header_id );
			}
		}
		/**
		* Update current version to the latest
		*/
		protected function update_version() {
			update_option( LOFTOCEAN_THEME_PLUGIN_VERSION_META_NAME, $this->version );
		}
		/**
		* Get default custom site header data
		*/
		protected function get_default_site_header_data() {
			$data = '[{"id":"b819605","elType":"section","settings":{"gap":"no","structure":"21","padding":{"unit":"px","top":"20","right":"0","bottom":"20","left":"0","isLinked":false},"hide_tablet":"hidden-tablet","hide_mobile":"hidden-mobile","background_background":"classic","background_color":"#0E0D0A","fullwidth":"cs-section-content-fullwidth","content_width":{"unit":"px","size":1536,"sizes":[]},"content_width_tablet":{"unit":"px","size":1024,"sizes":[]},"content_width_mobile":{"unit":"px","size":767,"sizes":[]}},"elements":[{"id":"350459d","elType":"column","settings":{"_column_size":33,"_inline_size":null,"content_position":"center","theme_color_scheme":"dark-color"},"elements":[{"id":"9e2273f","elType":"widget","settings":{"enable_custom_logo":"off","image":{"url":"https:\/\/cozystay.loftocean.com\/wp-content\/uploads\/2022\/03\/cs-logo-white.png","id":356,"alt":"","source":"library"},"image_size":"full","alignment":"left","width":{"unit":"px","size":186,"sizes":[]},"width_tablet":{"unit":"px","size":"","sizes":[]},"width_mobile":{"unit":"px","size":"","sizes":[]}},"elements":[],"widgetType":"cs_logo"}],"isInner":false},{"id":"538b775","elType":"column","settings":{"_column_size":66,"_inline_size":null,"content_position":"center","theme_color_scheme":"dark-color","align":"flex-end"},"elements":[{"id":"e95d6ea","elType":"widget","settings":{"menu":"2","alignment":"text-center","_element_width":"auto"},"elements":[],"widgetType":"cs_menu"},{"id":"188ad31","elType":"widget","settings":{"_element_width":"auto"},"elements":[],"widgetType":"cs_search"},{"id":"ce15576","elType":"widget","settings":{"_element_width":"auto"},"elements":[],"widgetType":"cs_mini_cart"}],"isInner":false}],"isInner":false},{"id":"2468c0b","elType":"section","settings":{"gap":"no","structure":"20","padding":{"unit":"px","top":"20","right":"0","bottom":"20","left":"0","isLinked":false},"hide_desktop":"hidden-desktop","background_background":"classic","background_color":"#0E0D0A","fullwidth":"cs-section-content-fullwidth","content_width":{"unit":"px","size":1536,"sizes":[]},"content_width_tablet":{"unit":"px","size":1024,"sizes":[]},"content_width_mobile":{"unit":"px","size":767,"sizes":[]}},"elements":[{"id":"690058f","elType":"column","settings":{"_column_size":50,"_inline_size":null,"content_position":"center","theme_color_scheme":"dark-color","_inline_size_mobile":50},"elements":[{"id":"b7ee0ff","elType":"widget","settings":{"enable_custom_logo":"off","image":{"url":"https:\/\/cozystay.loftocean.com\/wp-content\/uploads\/2022\/03\/cs-logo-white.png","id":356,"alt":"","source":"library"},"image_size":"full","alignment":"left","width":{"unit":"px","size":140,"sizes":[]},"width_tablet":{"unit":"px","size":"","sizes":[]},"width_mobile":{"unit":"px","size":"","sizes":[]}},"elements":[],"widgetType":"cs_logo"}],"isInner":false},{"id":"649d45e","elType":"column","settings":{"_column_size":50,"_inline_size":null,"content_position":"center","theme_color_scheme":"dark-color","_inline_size_mobile":50,"align":"flex-end"},"elements":[{"id":"07bb9ff","elType":"widget","settings":{"_element_width":"auto"},"elements":[],"widgetType":"cs_search"},{"id":"4a51114","elType":"widget","settings":{"_element_width":"auto"},"elements":[],"widgetType":"cs_menu_toggle"}],"isInner":false}],"isInner":false}]';

			return str_replace( '"menu":"2",', '"menu":"' . $this->get_primary_menu() . '",', $data );
		}
		/**
		* Get primary menu id
		*/
		protected function get_primary_menu() {
			$locations = get_nav_menu_locations();
			$location = 'primary-menu';
			if ( $locations && isset( $locations[ $location ] ) ) {
			   $menu = wp_get_nav_menu_object( $locations[ $location ] );
			   return $menu === false ? '' : $locations[ $location ];
			}
			$navs = wp_get_nav_menus();
			return is_array( $navs ) && ( count( $navs ) > 0 ) ? $navs[ 0 ]->term_id : '';
		}
		/**
		* Remove duplicated facility
		*/
		public function remove_duplicated_facility() {
			$taxonomy = 'lo_room_facilities';
			$buit_in_facility_types = array( 'room-footage', 'guests', 'beds', 'bathrooms', 'free-wifi', 'air-conditioning' );
			foreach ( $buit_in_facility_types as $bift ) {
				$facilities = \get_terms( array(
					'taxonomy' => $taxonomy,
					'hide_empty' => false,
					'meta_key' => 'facility_type',
					'meta_value'=> $bift,
					'orderby' => 'count',
					'order' => 'DESC'
				) );
				if ( ( ! is_wp_error( $facilities ) ) && ( count( $facilities ) > 1 ) ) {
					$post_type = 'loftocean_room';
					$kept_facility_id = $facilities[ 0 ]->term_id;
					for ( $i = 1; $i < count( $facilities ); $i ++ ) {
						$term_id = $facilities[ $i ]->term_id;
						if ( $facilities[ $i ]->count > 0 ) {
							$rooms = \get_posts( array(
						        'posts_per_page' => -1,
						        'post_type' => $post_type,
						        'tax_query' => array(
						            array(
						                'taxonomy' => $taxonomy,
						                'field' => 'term_id',
						                'terms' => $term_id,
						            )
						        )
						    ) );
							foreach ( $rooms as $room ) {
								$rfs = get_the_terms( $room->ID, $taxonomy );
								if ( $rfs && ( ! is_wp_error( $rfs ) ) ) {
									$rfs = wp_list_pluck( $rfs, 'term_id' );
									array_push( $rfs, $kept_facility_id );
									wp_set_post_terms( $room->ID, $rfs, $taxonomy );
								}
							}
						}
						wp_delete_term( $term_id, $taxonomy );
					}
				}
			}
		}
	}
	new Upgrader();
}
