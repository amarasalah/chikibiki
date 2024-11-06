<?php
namespace LoftOcean\Gutenberg;
/**
* Gutenberg block manager class
*/

if ( ! class_exists( '\LoftOcean\Gutenberg\Manager' ) ) {
	class Manager {
		/**
		* Array of post meta name list
		*/
		public $meta_list = array(
			'post' => array(
				'loftocean_post_format_gallery' => array( 'type' => 'string', 'default' => '' ),
				'loftocean_post_format_gallery_ids' => array( 'type' => 'string', 'default' => '' ),
				'loftocean_post_format_gallery_urls' => array( 'type' => 'string', 'default' => '' ),
				'loftocean_post_format_video_id' => array( 'type' => 'number', 'default' => 0 ),
				'loftocean_post_format_video_url' => array( 'type' => 'string', 'default' => '' ),
				'loftocean_post_format_video_type' => array( 'type' => 'string', 'default' => '' ),
				'loftocean_post_format_video' => array( 'type' => 'string', 'default' => '' ),
				'loftocean_post_format_audio_type' => array( 'type' => 'string', 'default' => '' ),
				'loftocean_post_format_audio_url' => array( 'type' => 'string', 'default' => '' ),
				'loftocean_post_format_audio_id' => array( 'type' => 'number', 'default' => 0 ),
				'loftocean_post_format_audio' => array( 'type' => 'string', 'default' => '' ),
				'loftocean-like-count' => array( 'type' => 'number', 'default' => 0 )
			),
			'page' => array()
		);
		/**
		* Construct function
		*/
		public function __construct() {
			add_action( 'rest_api_init', array( $this, 'register_custom_fields' ) );
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
			add_filter( 'is_protected_meta', array( $this, 'protected_metas' ), 999, 3 );

			$this->register_metas();
		}
		/**
		* Enqueue editor assets
		*/
		public function enqueue_editor_assets() {
			switch ( $this->get_current_post_type() ) {
				case 'post':
					wp_enqueue_script(
						'loftocean-gutenberg-post-script',
						LOFTOCEAN_URI . 'includes/gutenberg/plugins/plugin-post.js',
						array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-hooks' ),
						LOFTOCEAN_ASSETS_VERSION,
						true
					);
					break;
				case 'page':
					if ( ! apply_filters( 'loftocean_hide_page_settings', false ) ) {
						wp_enqueue_script(
							'loftocean-gutenberg-page-script',
							LOFTOCEAN_URI . 'includes/gutenberg/plugins/plugin-page.js',
							array( 'wp-blocks', 'wp-element', 'wp-i18n' ),
							LOFTOCEAN_ASSETS_VERSION,
							true
						);
					}
					break;
			}
		}
		/**
		* Register metas for gutenberg
		*/
		public function register_metas() {
			$metas = array(
				'post' => apply_filters( 'loftocean_get_post_gutenberg_metas', $this->meta_list['post'] ),
				'page' => apply_filters( 'loftocean_get_page_gutenberg_metas', $this->meta_list['page'] )
			);
			foreach( $metas as $pt => $pms ) {
				foreach ( $pms as $mid => $attrs ) {
					register_meta( 'post', $mid, array(
						'object_subtype' => $pt,
						'auth_callback' => array( $this, 'permission_check' ),
						'show_in_rest' 	=> true,
						'single' 		=> true,
						'type' 			=> $attrs[ 'type' ],
						'default'		=> $attrs[ 'default' ]
					) );
				}
			}
		}
		/**
		* Check permission for meta registration
		*/
		public function permission_check( $arg1, $meta_name, $post_id ) {
			if ( current_user_can( 'edit_post', $post_id ) ) {
				return true;
			}
			return false;
		}
		/**
		* Get current post type
		* @return mix post type string or boolean false
		*/
		protected function get_current_post_type() {
			global $post;
			if ( is_admin() && ! empty( $post ) && ! empty( $post->post_type ) ) {
				return $post->post_type;
			} else {
				return false;
			}
		}
		/**
		* Register custom fields for REST API
		*/
		public function register_custom_fields() {
			register_rest_field( 'post', 'loftoceanMetas', array(
				'get_callback' => array( $this, 'get_metas' )
			) );

			// clear gutenberg issue
			register_rest_route( 'loftocean/v1', '/clear-gutenberg-conflicts/', array(
				'methods' 	=> 'GET',
				'permission_callback' => '__return_true',
				'callback' 	=> array( $this, 'clear_gutenberg_conflicts' )
			) );
		}
		/**
		* Get metas for theme blocks
		*/
		public function get_metas( $object, $field_name, $request ) {
			$metas = array();
			$metas['authorName'] = get_the_author();
			$metas['categories'] = \LoftOcean\get_category_list( $object['id'] );
			$metas['date'] = get_the_date( get_option( 'date_format' ), $object['id'] );
			if ( $object['featured_media'] ) {
				$metas['featuredImageSRC'] = \LoftOcean\get_image_src( $object['featured_media'], 'thumbnail' );
			}
			return $metas;
		}
		/**
		* Get formats supported
		*/
		public function get_formats( $format ) {
			return array( 'value' => $format, 'label' => ucfirst( $format ) );
		}
		/**
		* Clear Gutenberg conflicts when there are multiple setting records from theme
		*/
		public function clear_gutenberg_conflicts() {
			$ppp = 50; $args = array(
				'fields' => 'ids',
				'offset' => 0,
				'posts_per_page' => $ppp,
				'post_type' => array( 'post', 'page' )
			);
			do {
				$q = get_posts( $args );
				foreach ( $q as $pid ) {
					$metas = get_post_meta( $pid );
					foreach ( $metas as $mname => $mvalue ) {
						if ( ( ( false !== strpos( $mname, 'loftocean' ) ) || ( false !== strpos( $mname, LOFTOCEAN_THEME_PREFIX ) ) ) && \LoftOcean\is_valid_array( $mvalue ) ) {
							delete_post_meta( $pid, $mname );
							update_post_meta( $pid, $mname, $mvalue[0] );
						}
					}
				}
				$args['offset'] += $ppp;
			} while ( count( $q ) === $ppp );

			return array( 'code' => 200, 'status' => 'done' );
		}
		/**
		* Check protected metas
		*/
		public function protected_metas( $protected, $meta_key, $meta_type ) {
			if ( preg_match( '/^(loftocean|cozystay)/', $meta_key ) ) {
				return true;
			}
			return $protected;
		}
	}
	function init_gutenberg() {
		if ( function_exists( '\register_block_type' ) ) {
			new Manager();
		}
	}
	add_action( 'init', 'LoftOcean\Gutenberg\init_gutenberg' );
}
