<?php
namespace LoftOcean\Metas;

// Post meta related functions
if ( ! class_exists( '\LoftOcean\Metas\Post_Metas' ) ) {
	class Post_Metas {
		/**
		* Object current class instance
		*/
		public static $_instance = false;
		/**
		* Array to cache post authors
		*/
		protected $post_authors = array();
		/**
		* Array actual author IDs for current author page
		*/
		protected $actual_author_IDs = array();
		/**
		* Construct function
		*/
		function __construct() {
			add_action( 'loftocean_post_metabox_html', array( $this, 'view_like_count_options' ) );
			add_action( 'loftocean_save_post_metabox_settings', array( $this, 'save_view_like_count' ) );
		}
		/**
		* Add settings to theme option panel for post
		*/
		public function view_like_count_options() {
			global $post;
			$pid 	= $post->ID;
			$items 	= array(
				'like' => array( 'title' => esc_html__( 'Like Counts: ', 'loftocean' ), 'count' => intval( get_post_meta( $pid, 'loftocean-like-count', true ) ) )
			);

			foreach ( $items as $id => $attrs ) : ?>
				<p class="loftocean-post-counter-wrap">
					<label><?php echo esc_html( $attrs['title'] ); ?></label>
					<input type="number" min="0" name="loftocean-post-<?php echo esc_attr( $id ); ?>-count" value="<?php echo esc_attr( $attrs['count'] ); ?>" readonly style="width: 90px;" />
					<a href="#" class="edit"><?php esc_html_e( 'Edit', 'loftocean' ); ?></a>
					<a href="#" class="cancel" style="display: none;"><?php esc_html_e( 'Cancel', 'loftocean' ); ?></a>
					<a href="#" class="save" style="display: none;"><?php esc_html_e( 'Done', 'loftocean' ); ?></a>
				</p> <?php
			endforeach;
		}
		/**
		* save like view count
		*/
		public function save_view_like_count( $pid ) {
			$like = empty( $_REQUEST['loftocean-post-like-count'] ) ? 0 : intval( wp_unslash( $_REQUEST['loftocean-post-like-count'] ) );
			update_post_meta( $pid, 'loftocean-like-count', $like );
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
	// Add action to initialize Instagram
	add_action( 'loftocean_load_core_modules', array( 'LoftOcean\Metas\Post_Metas', '_instance' ) );
}
