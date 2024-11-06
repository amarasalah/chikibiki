<?php
namespace LoftOcean\Metas;
/**
* Author social related functions
*/

if ( ! class_exists( '\LoftOcean\Metas\User_Metas' ) ) {
	class User_Metas {
		/**
		* Array signature display on options
		*/
		public $signature_display_on = array(
			'posts',
			'pages',
			'both'
		);
		/**
		* Object current class instance
		*/
		public static $_instance = false;
		/**
		* Construct function
		*/
		public function __construct() {
			add_filter( 'user_contactmethods', array( $this, 'add_user_socials' ) );
			add_filter( 'loftocean_has_user_featured_image', array( $this, 'has_featured_image' ), 10, 2 );

			add_action( 'show_user_profile', array( $this, 'more_user_settings' ) );
			add_action( 'edit_user_profile', array( $this, 'more_user_settings' ) );
			add_action( 'personal_options_update', array( $this, 'save_more_settings' ) );
			add_action( 'edit_user_profile_update', array( $this, 'save_more_settings' ) );
			add_action( 'loftocean_front_the_user_social', array( $this, 'show_user_socials' ) );
			add_action( 'admin_print_scripts-user-new.php', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_print_scripts-user-edit.php', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_print_scripts-profile.php', array( $this, 'enqueue_scripts' ) );
			add_action( 'loftocean_the_user_featured_image', array( $this, 'the_user_featured_image' ), 10, 3 );
		}
		/**
		* Test if has user featured image
		*/
		public function has_featured_image( $has, $userID ) {
			if ( ! empty( $userID ) ) {
				$imageID = absint( get_user_meta( $userID, 'loftocean_user_featured_image', true ) );
				return \LoftOcean\media_exists( $imageID );
			}
			return $has;
		}
		/**
		* Output the user featured image
		*/
		public function the_user_featured_image( $userID, $sizes = array( 'full', 'full' ), $args = array() ) {
			if ( ! empty( $userID ) ) {
				$imageID = absint( get_user_meta( $userID, 'loftocean_user_featured_image', true ) );
				if ( \LoftOcean\media_exists( $imageID ) ) {
					do_action( 'loftocean_media_the_background_image', $imageID, $sizes, $args );
				}
			}
		}
		/**
		* @desciption get user social links html
		*/
		public function show_user_socials( $uid = false ) {
			$user_id = empty( $uid ) ? get_the_author_meta( 'ID' ) : $uid;
			$items = array();
			$socials = array(
				'website' 	=> array(
					'url' 	=> get_the_author_meta( 'url', $user_id ),
					'title' => esc_html__( 'Website', 'loftocean' )
				),
				'youtube' 	=> array(
					'url' 	=> get_the_author_meta( 'youtube', $user_id ),
					'title' => esc_html__( 'YouTube', 'loftocean' )
				),
				'twitter' 	=> array(
					'url' 	=> get_user_meta( $user_id, 'loftocean-twitter', true ),
					'title' => esc_html__( 'Twitter', 'loftocean' )
				),
				'facebook' 	=> array(
					'url' 	=> get_user_meta( $user_id, 'facebook', true ),
					'title' => esc_html__( 'Facebook', 'loftocean' )
				),
				'instagram' => array(
					'url' 	=> get_user_meta( $user_id, 'instagram', true ),
					'title' => esc_html__( 'Instagram', 'loftocean' )
				),
				'pinterest' => array(
					'url' 	=> get_user_meta( $user_id, 'pinterest', true ),
					'title' => esc_html__( 'Pinterest', 'loftocean' )
				)
			);
			foreach ( $socials as $id => $attr ) {
				if ( ! empty( $attr['url'] ) ) {
					array_push( $items, $attr );
				}
			}
			if ( ! empty( $items ) && ( count( $items ) > 0 ) ) : ?>
				<div class="author-social">
					<ul class="social-nav">
					<?php foreach ( $items as $attr ) : ?>
						<li>
							<a href="<?php echo esc_url( $attr['url'] ); ?>" title="<?php echo esc_attr( $attr['title'] ); ?>">
								<?php echo esc_html( $attr['title'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
					</ul>
				</div> <?php
			endif;
		}
		/**
		* @description add social links UI in user profile page
		* @return array
		*/
		public function add_user_socials( $profile_fields ) {
			// Add new fields
			$profile_fields['youtube'] = esc_html__( 'YouTube URL', 'loftocean' );
			$profile_fields['facebook'] = esc_html__( 'Facebook URL', 'loftocean' );
			$profile_fields['loftocean-twitter'] = esc_html__( 'Twitter URL', 'loftocean' );
			$profile_fields['instagram'] = esc_html__( 'Instagram URL', 'loftocean' );
			$profile_fields['pinterest'] = esc_html__( 'Pinterest URL', 'loftocean' );

			return $profile_fields;
		}
		/**
		* Add Field to able to upload featured image and author label
		* @param object
		*/
		public function more_user_settings( $user ) {
			$userID = $user->ID;
			$imageID = intval( get_user_meta( $userID, 'loftocean_user_featured_image', true ) );
			$image_info = false;
			$image = false;
			if ( ! empty( $imageID ) ) {
				$image_info = wp_get_attachment_image_src( $imageID, 'thumbnail' );
				$image = empty( $image_info ) ? false : esc_url( $image_info[0] );
	    	} ?>
			<h2><?php esc_html_e( 'Author Page Header Image', 'loftocean' ); ?></h2>
			<table class="form-table">
				<tr>
					<th><label for="loftocean_user_featured_image"><?php esc_html_e( 'Header Image', 'loftocean' ); ?></label></th>
					<td>
						<a href="#" class="loftocean-upload-image" data-upload="<?php esc_attr_e( 'Choose Image', 'loftocean' );?>">
						<?php if( $image ) : ?>
							<img width=<?php echo esc_attr( $image_info[1] ); ?> height=<?php echo esc_attr( $image_info[2] ); ?> alt="<?php esc_attr_e( 'featured-image', 'loftocean' ); ?>" src="<?php echo esc_url( $image ); ?>">
						<?php else : ?>
							<?php esc_html_e( 'Choose Image', 'loftocean' ); ?>
						<?php endif; ?>
						</a>
						<a href="#" class="loftocean-remove-image" style="display: <?php if ( $image ) : ?>block<?php else: ?>none<?php endif; ?>;"><?php esc_html_e( 'Remove Image', 'loftocean' );?></a>
						<input type="hidden" name="loftocean_user_featured_image" class="loftocean-image-hidden" value="<?php echo esc_attr( $imageID ); ?>">
					</td>
				</tr>
			</table><?php
		}
		/**
		* Save user featured image and author label
		* @param string
		*/
		public function save_more_settings( $user_id ) {
			$settings = array( 'loftocean_user_featured_image' => 'intval', );
			foreach( $settings as $setting => $sanitize_cb ) {
				if ( isset( $_REQUEST[ $setting ] ) ) {
					update_user_meta(
						$user_id,
						$setting,
						call_user_func( $sanitize_cb, wp_unslash( $_REQUEST[ $setting ] ) )
					);
				}
			}
		}
		/*
		* Enqueue scripts needed for taxonomy image field
		*/
		public function enqueue_scripts() {
			wp_enqueue_media();
			wp_enqueue_script( 'loftocean-admin-media', LOFTOCEAN_URI . 'assets/scripts/admin/admin-media.min.js', array( 'jquery' ), LOFTOCEAN_ASSETS_VERSION, true );
		}
		/**
		* Get message allowed html
		* @return array
		*/
		protected function get_message_allowed_html() {
			return array(
				'br' => array(),
				'b' => array(),
				'i' => array()
			);
		}
		/**
		* Instantiate class to make sure only once instance exists
		*/
		public static function _instance() {
			if ( false === self::$_instance ) {
				self::$_instance = new User_Metas();
			}
			return self::$_instance;
		}
	}
	// Add action to initialize Instagram
	add_action( 'loftocean_load_core_modules', array( 'LoftOcean\Metas\User_Metas', '_instance' ) );
}
