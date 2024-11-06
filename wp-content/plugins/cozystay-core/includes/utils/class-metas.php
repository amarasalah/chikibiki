<?php
namespace LoftOcean\Utils;
if ( ! class_exists( '\LoftOcean\Utils\Post_Metas' ) ) {
	class Post_Metas {
		/**
		* Object current class instance
		*/
		public static $_instance = false;
        /**
        * Cache post like count
        */
		private $likes = array();
        /**
        * Cache post view count
        */
		private $views = array();
		/**
		* Construct function
		*/
		public function __construct() {
			add_action( 'rest_api_init', array( $this, 'init_rest' ) );

    		add_action( 'wp', array( $this, 'update_post_view' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_ajax_loftocean_post_like', array( $this, 'update_post_like' ) );
			add_action( 'wp_ajax_nopriv_loftocean_post_like', array( $this, 'update_post_like' ) );
			add_action( 'wp_ajax_loftocean_social_counter', array( $this, 'update_social_counter' ) );
			add_action( 'wp_ajax_nopriv_loftocean_social_counter', array( $this, 'update_social_counter' ) );
			add_action( 'loftocean_the_social_like_icon', array( $this, 'the_share_like_icon' ) );

			add_filter( 'loftocean_get_post_metas_like_count', array( $this, 'get_like_count' ) );
			add_filter( 'loftocean_get_post_metas_view_count', array( $this, 'get_view_count' ) );
			add_action( 'loftocean_post_metas_the_like_button', array( $this, 'the_like_button' ) );
			add_action( 'loftocean_post_metas_the_like_icon', array( $this, 'the_like_icon' ) );
			add_action( 'loftocean_post_metas_view_label', array( $this, 'the_view_label' ) );
			add_action( 'loftocean_post_metas_like_label', array( $this, 'the_like_label' ) );
        }
		/**
		* Register fav like related javscript
		*/
		public function enqueue_scripts() {
			wp_enqueue_script( 'loftocean-post-metas', LOFTOCEAN_URI . 'assets/scripts/front/post-metas.min.js', array( 'jquery', 'wp-api' ), LOFTOCEAN_ASSETS_VERSION, true );
			wp_localize_script( 'loftocean-post-metas', 'loftoceanSocialAjax', array(
				'url' => esc_url( admin_url( 'admin-ajax.php' ) ),
				'like' => array( 'action' => 'loftocean_post_like' ),
				'social' => array( 'action' => 'loftocean_social_counter' ),
				'loadPostMetasDynamically' => apply_filters( 'loftocean_is_loading_post_meta_by_ajax', false ),
				'currentPostID' => is_singular( 'post' ) ? get_queried_object_id() : false
			) );
		}
		/**
		* Favour like ajax handler
		*/
		public function update_post_like() {
			if ( isset( $_POST['post_id'] ) ) {
				if ( get_post_status( intval( wp_unslash( $_POST['post_id'] ) ) ) !== false ) {
					$pid = intval( wp_unslash( $_POST['post_id'] ) );
					$likes = intval( get_post_meta( $pid, 'loftocean-like-count', true ) );
					$likes = empty( $_REQUEST['unliked'] ) ? ( $likes + 1 ) : max( 0, ( $likes - 1 ) );
					update_post_meta( $pid, 'loftocean-like-count', $likes );
				}
			}
			wp_send_json_success();
		}
		/**
		* Favour like ajax handler
		*/
		public function update_social_counter() {
			if ( isset( $_POST['post_id'] ) && ! empty( $_POST['social'] ) ) {
				$pid = intval( wp_unslash( $_POST['post_id'] ) );
				if ( get_post_status( $pid ) !== false ) {
					$social = sanitize_text_field( wp_unslash( $_POST['social'] ) );
					$counters = (array)get_post_meta( $pid, 'loftocean_social_counters', true );
					$counters[ $social ] = empty( $counters[ $social ] ) ? 1 : ( $counters[ $social ] + 1 );
					update_post_meta( $pid, 'loftocean_social_counters', $counters );
				}
			}
			wp_send_json_success();
		}
		/**
		* Visits ajax handler
		*/
		public function update_post_view() {
			if ( is_singular( 'post' ) && ( ! is_customize_preview() ) && ( ! is_admin() ) && ( ! apply_filters( 'loftocean_is_loading_post_meta_by_ajax', false ) ) ) {
				global $post;
				$pid = $post->ID;
				$view = get_post_meta( $pid, 'loftocean-view-count', true );
				update_post_meta( $pid, 'loftocean-view-count', ( intval( $view ) + 1 ) );
			}
		}
		/**
		* Get post like count
		*/
		public function get_like_count() {
			$pid = get_the_ID();
			if ( ! isset( $this->likes[ $pid ] ) ) {
				$like = get_post_meta( $pid, 'loftocean-like-count', true );
				$this->likes[ $pid ] = \LoftOcean\counter_format( $like );
			}
			return $this->likes[ $pid ];
		}
		/**
		* Get post like count
		*/
		public function get_view_count() {
			$pid = get_the_ID();
			if ( ! isset( $this->views[ $pid ] ) ) {
				$view = get_post_meta( $pid, 'loftocean-view-count', true );
				$this->views[ $pid ] = \LoftOcean\counter_format( $view );
			}
			return $this->views[ $pid ];
		}
		/**
		* Output clickable like button with number
		*/
		public function the_like_button() {
			$like_number = apply_filters( 'loftocean_get_post_metas_like_count', 0 );
			$raw_number = get_post_meta( get_the_ID(), 'loftocean-like-count', true ); ?>
			<div class="post-like loftocean-like-meta" data-post-id="<?php the_ID(); ?>" data-like-count="<?php echo esc_attr( intval( $raw_number ) ); ?>">
				<i class="fa fa-heart"></i>
				<span class="count"><?php echo esc_html( $like_number ); ?></span>
			</div><?php
		}
		/**
		* Output like icon
		*/
		public function the_like_icon() {
			$raw_number = get_post_meta( get_the_ID(), 'loftocean-like-count', true ); ?>
			<div class="overlay-label like post-like loftocean-like-meta" data-post-id="<?php the_ID(); ?>" data-like-count="<?php echo esc_attr( intval( $raw_number ) ); ?>">
				<i class="fas fa-heart"></i>
			</div><?php
		}
		/**
		* Output like label
		*/
		public function the_like_label() {
			$like_number = apply_filters( 'loftocean_get_post_metas_like_count', 0 );
			$raw_number = get_post_meta( get_the_ID(), 'loftocean-like-count', true );
			$label = ( $raw_number > 1 ) ? esc_html__( 'Likes', 'loftocean' ) : esc_html__( 'Like', 'loftocean' ); ?>
			<div
				class="post-like meta-item like-count loftocean-like-meta"
				data-post-id="<?php the_ID(); ?>"
				data-like-count="<?php echo esc_attr( intval( $raw_number ) ); ?>"
				data-single-label="<?php esc_attr_e( 'Like', 'loftocean' ); ?>"
				data-plural-label="<?php esc_attr_e( 'Likes', 'loftocean' ); ?>"
			><span class="count"><?php echo esc_html( $like_number ); ?></span> <span class="label"><?php echo esc_html( $label ); ?></span></div><?php
		}
		/**
		* Output view label
		*/
		public function the_view_label() {
			$view_number = apply_filters( 'loftocean_get_post_metas_view_count', 0 );
			$raw_number = get_post_meta( get_the_ID(), 'loftocean-view-count', true );
			$label = ( $raw_number > 1 ) ? esc_html__( 'Views', 'loftocean' ) : esc_html__( 'View', 'loftocean' ); ?>
			<div class="meta-item view-count loftocean-view-meta" data-post-id="<?php echo esc_attr( get_the_ID() ); ?>"><span class="count"><?php echo esc_html( $view_number ); ?></span> <?php echo esc_html( $label ); ?></div><?php
		}
		/**
		* register REST API
		*/
		public function init_rest() {
			register_rest_route( 'loftocean/v1', '/get-post-metas/(?P<ids>.+)/(?P<update>.+)', array(
				'methods' 	=> 'GET',
				'permission_callback' => '__return_true',
				'callback' 	=> array( $this, 'get_post_metas' )
			) );
		}
		public function get_post_metas( $data ) {
			if ( ! empty( $data['update'] ) ) {
				$pid = intval( $data['update'] );
				if ( false !== get_post_status( $pid ) ) {
					$view = get_post_meta( $pid, 'loftocean-view-count', true );
					update_post_meta( $pid, 'loftocean-view-count', ( intval( $view ) + 1 ) );

					$data['ids'] = empty( $data['ids'] ) ? $pid : ( $data['ids'] . ',' . $pid );
				}
			}
			if ( ! empty( $data['ids'] ) ) {
				$ids = explode( ',', $data['ids'] );
				$ids = array_unique( $ids );
				$ids = array_filter( $ids );
				$metas = array( 'loftocean-view-count', 'loftocean-like-count' );
				$return_data = array();
				foreach( $ids as $id ) {
					$return_data[ $id ] = array();
					foreach( $metas as $meta ) {
						$value = get_post_meta( $id, $meta, true );
						$return_data[ $id ][ $meta ] = array( 'raw' => $value, 'format' => \LoftOcean\counter_format( $value ) );
					}
				}
				return array( 'status' => 200, 'data' => $return_data );
			} else {
				return array( 'status' => 400, 'data' => false );
			}
		}
		/**
		* Output share like button
		*/
		public function the_share_like_icon( $args = array() ) {
			$class = array( 'post-like', 'sharing-like-icon', 'loftocean-like-meta' );
			isset( $args['class'] ) ? array_push( $class, $args[ 'class' ] ) : '';
			$like_number = apply_filters( 'loftocean_get_post_metas_like_count', 0 );
			$raw_number = get_post_meta( get_the_ID(), 'loftocean-like-count', true ); ?>
			<a
				target="_blank"
				title="<?php esc_attr_e( 'Like', 'loftocean' ); ?>"
				href="#"
				class="<?php echo esc_attr( implode( ' ', $class ) ); ?>"
				data-post-id="<?php the_ID(); ?>"
				data-like-count="<?php echo esc_attr( intval( $raw_number ) ); ?>"
			>
				<i class="fas fa-heart"></i>
				<span><?php esc_html_e( 'Like', 'loftocean' ); ?></span>
				<span class="like-count count"><?php echo esc_html( $like_number ); ?></span>
			</a><?php
		}
		/**
		* Instantiate class to make sure only once instance exists
		*/
		public static function _instance() {
			if ( false === self::$_instance ) {
				self::$_instance = new Post_Metas();
			}
			return self::$_instance;
		}
    }
    add_action( 'loftocean_load_core_modules', array( 'LoftOcean\Utils\Post_Metas', '_instance' ) );
}
