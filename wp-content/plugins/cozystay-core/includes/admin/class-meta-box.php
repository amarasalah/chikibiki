<?php
namespace LoftOcean;
if ( ! class_exists( 'Meta_Box' ) ) {
	class Meta_Box {
		/**
		* Construct function
		*/
		public function __construct(){
			add_action( 'save_post', array( $this, 'save_meta' ), 10, 3 );
			add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
		/**
		* Enqueue scripts
		*/
		public function enqueue_scripts() {
			wp_enqueue_media();
			wp_enqueue_script( 'loftocean-meta-box', LOFTOCEAN_URI . 'assets/scripts/admin/meta-box.min.js', array( 'jquery' ), LOFTOCEAN_ASSETS_VERSION, true );
		}
		/**
		* Register meta boxes
		*/
		public function register_meta_boxes() {
			global $post;
			if ( $post ) {
				$gutenberg_compat = array(
					'__block_editor_compatible_meta_box' => true,
					'__back_compat_meta_box' => true
				);
				switch ( $post->post_type ) {
					case 'post':
						$title = apply_filters( 'loftocean_metabox_get_post_title', esc_html__( 'Theme Options', 'loftocean' ), 'post' );
						add_meta_box( 'loftocean-post-meta-box', $title, array( $this, 'show_post_metabox' ), 'post', 'advanced', 'default', $gutenberg_compat );
						break;
					case 'page':
						if ( ! apply_filters( 'loftocean_hide_page_settings', false ) ) {
							$title = apply_filters( 'loftocean_metabox_get_page_title', esc_html__( 'Theme Options', 'loftocean' ), 'page' );
							add_meta_box( 'loftocean-page-meta-box', $title, array( $this, 'show_page_metabox' ), 'page', 'advanced', 'default', $gutenberg_compat );
						}
						break;
				}
			}
		}
		/**
		* Output post metabox html
		* @param object
		*/
		public function show_post_metabox( $post ) {
			do_action( 'loftocean_pre_post_metabox_html', $post );
			do_action( 'loftocean_post_metabox_html', $post );
			$this->get_nonce();
		}
		/**
		* Output page metabox html
		* @param object
		*/
		public function show_page_metabox( $post ) {
			do_action( 'loftocean_the_page_metabox_html', $post );
			$this->get_nonce();
		}
		/**
		* Save post metas
		* @param int post id
		* @param object
		* @param int
		*/
		public function save_meta( $post_id, $post, $update ) {
			$post_types = array( 'post', 'page', 'loftocean-quote', 'loftocean-portfolio' );
			if ( empty( $update ) || ! in_array( $post->post_type, $post_types ) || empty( $_REQUEST['loftocean_nonce'] ) || ! empty( $_REQUEST['loftocean_gutenberg_enabled'] ) ) {
				return '';
			}
			if ( current_user_can( 'edit_post', $post_id ) ) {
				switch ( $post->post_type ) {
					case 'post':
						do_action( 'loftocean_save_post_metabox_settings', $post_id );
						break;
					case 'page':
						do_action( 'loftocean_save_page_metabox_settings', $post_id );
						break;
				}
			}
		}
		/**
		* Ouput metabox nonce input
		*/
		protected function get_nonce() {?>
			<input type="hidden" name="loftocean_nonce" value="<?php echo esc_attr( wp_create_nonce( 'loftocean_nonce' ) ); ?>" /> <?php
		}
	}
	new Meta_Box();
}
