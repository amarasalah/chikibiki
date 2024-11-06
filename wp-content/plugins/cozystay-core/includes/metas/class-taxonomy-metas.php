<?php
namespace LoftOcean\Metas;
/**
* Add tax image field for Category and Post Tag editing page
*/

if ( ! class_exists( '\LoftOcean\Metas\Taxonomy_Metas' ) ) {
	class Taxonomy_Metas {
		/**
		* Make sure only one instance exists
		*/
		static public $_instance = false;
		/**
		* Taxonomy which support featured image
		*/
		protected $text = array();
		/**
		* Featured image text
		*/
		protected $tax = array();
		/**
		* Construct function
		*/
		public function __construct() {
			$this->tax 	= apply_filters( 'loftocean_taxonomy_support_featured_image', array( 'category', 'post_tag' ) );
			$this->text	= apply_filters( 'loftocean_taxonomy_featured_image_text', array(
				'choose' 	=> esc_html__( 'Choose Image', 'loftocean' ),
				'remove'	=> esc_html__( 'Remove Image', 'loftocean' ),
				'label' 	=> array(
					'category' => esc_html__( 'Category Image', 'loftocean' ),
					'post_tag' => esc_html__( 'Post Tag Image', 'loftocean' )
				),
				'description' => array(
					'category' => esc_html__( 'Image for Category', 'loftocean' ),
					'post_tag' => esc_html__( 'Image for Post Tag', 'loftocean' )
				)
			) );

			add_filter( 'loftocean_front_get_taxonomy_featured_image', array( $this, 'get_taxonomy_image_bg' ), 99, 4 );
			add_filter( 'loftocean_front_has_taxonomy_featured_image', array( $this, 'has_taxonomy_image' ), 10, 2 );
			add_action( 'loftocean_front_the_taxonomy_featured_image', array( $this, 'the_taxonomy_image_bg' ), 10, 3 );
			add_action( 'edited_term', array( $this, 'save_tax_fileds' ), 10, 3 );
			add_action( 'created_term', array( $this, 'save_tax_fileds' ), 10, 3 );
			add_action( 'admin_print_scripts-edit-tags.php', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_print_scripts-term.php', array( $this, 'enqueue_scripts' ) );
			foreach ( $this->tax as $tax ) {
				add_action( $tax . '_add_form_fields', array( $this, 'add_taxonomy_fields' ) );
				add_action( $tax . '_edit_form_fields', array( $this, 'edit_taxonomy_fields' ) );
			}
		}
		/**
		* Add a image uploader for category/post_tag edit page
		* @param object term
		*/
		public function edit_taxonomy_fields( $tag ) {
			$tax = $this->tax;
			$taxonomy = $tag->taxonomy;
			if ( in_array( $taxonomy, $tax ) ) :
				$tax_id 	= $tag->term_id;
				$img_id 	= intval( get_term_meta( $tax_id, 'loftocean_tax_image', true ) );
				$img_src 	= empty( $img_id ) ? false : $this->get_taxonomy_image_src( $tag, 'thumbnail' );
				$text		= $this->text; ?>

				<tr class="form-field">
					<th scope="row" valign="top">
						<label for="loftocean_tax_image"><?php echo esc_html( $text['label'][ $taxonomy ] ); ?></label>
					</th>
					<td>
						<a href="#" class="loftocean-upload-image" style="display: block;" data-upload="<?php echo esc_attr( $text['choose'] ); ?>">
							<?php if ( $img_src ) :
								$image_info = wp_get_attachment_image_src( $img_id, 'thumbnail' ); ?>
								<img width=<?php echo esc_attr( $image_info[1] ); ?> height=<?php echo esc_attr( $image_info[2] ); ?> alt="img" src="<?php echo esc_url( $img_src ); ?>">
							<?php else : ?>
								<?php echo esc_html( $text['choose'] ); ?>
							<?php endif; ?>
						</a>
						<a href="#" class="loftocean-remove-image" style="display: <?php if ( $img_src ) : ?>block<?php else : ?>none<?php endif; ?>;">
							<?php echo esc_html( $text['remove'] ); ?>
						</a>
						<input type="hidden" class="loftocean-image-hidden" name="loftocean_tax_image" value="<?php echo esc_attr( $img_id ); ?>">
						<span class="description"><?php echo esc_html( $text['description'][ $taxonomy ] ); ?></span>

					</td>
				</tr> <?php
				do_action( 'loftocean_' . $taxonomy . '_edit_form_fields', $tag->term_id );
			endif;
		}
		/**
		* Add a image uploader for new category/post_tag page
		*/
		public function add_taxonomy_fields() {
			$taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ) : false;
			$tax = array( 'category', 'post_tag' );
			if ( $taxonomy && in_array( $taxonomy, $tax ) ) :
				$text = $this->text; ?>
				<div class="form-field term-img-wrap">
					<label for="loftocean_tax_image"><?php echo esc_html( $text['label'][ $taxonomy ] ); ?></label>
					<a href="#" class="loftocean-upload-image" style="display: block;" data-upload="<?php echo esc_attr( $text['choose'] ); ?>"><?php echo esc_html( $text['choose'] ); ?></a>
					<a href="#" class="loftocean-remove-image" style="display:none;"><?php echo esc_html( $text['remove'] ); ?></a>
					<input type="hidden" class="loftocean-image-hidden" name="loftocean_tax_image" id="loftocean_tax_image">
					<p><?php echo wp_kses_post( $text['description'][ $taxonomy ] ); ?></p>
				</div><?php

				do_action( 'loftocean_' . $taxonomy . '_add_form_fields' );
			endif;
		}
		/*
		* Save taxonomy image for category/post_tag
		*/
		public function save_tax_fileds( $term_id, $tt_id, $taxonomy ) {
			$tax = $this->tax;
			if ( in_array( $taxonomy, $tax ) && isset( $_POST['loftocean_tax_image'] ) ) {
				update_term_meta( $term_id, 'loftocean_tax_image', intval( wp_unslash( $_POST['loftocean_tax_image'] ) ) );
			}
			do_action( 'loftocean_' . $taxonomy . '_save_fields', $term_id );
		}
		/*
		* Enqueue scripts needed for taxonomy image field
		*/
		public function enqueue_scripts() {
			wp_enqueue_media();
			wp_enqueue_script( 'loftocean-admin-media', LOFTOCEAN_URI . 'assets/scripts/admin/admin-media.min.js', array( 'jquery' ), LOFTOCEAN_ASSETS_VERSION, true );
		}
		/**
		* Test if taxonomy has featured image
		*/
		public function has_taxonomy_image( $has, $term ) {
			if ( ( $term instanceof \WP_Term ) && in_array( $term->taxonomy, $this->tax ) ) {
				$taxID = $term->term_id;
				$imageID = intval( get_term_meta( $taxID, 'loftocean_tax_image', true ) );
				return \LoftOcean\media_exists( $imageID );
			}
			return false;
		}
		/**
		* Get taxonomy image background html for category/post_tag
		*/
		public function get_taxonomy_image_bg( $html, $term, $sizes = array( 'full', 'full' ), $args = array() ) {
			if ( ( $term instanceof \WP_Term ) && in_array( $term->taxonomy, $this->tax ) ) {
				$taxID = $term->term_id;
				$imageID = intval( get_term_meta( $taxID, 'loftocean_tax_image', true ) );
				return apply_filters( 'loftocean_media_get_background_image', '', $imageID, $sizes, $args );
			}
			return false;
		}
		/**
		* Output the taxonomy image background html for category/post_tag
		*/
		public function the_taxonomy_image_bg( $term, $sizes = array( 'full', 'full' ), $args = array() ) {
			if ( ( $term instanceof \WP_Term ) && in_array( $term->taxonomy, $this->tax ) ) {
				$taxID = $term->term_id;
				$imageID = intval( get_term_meta( $taxID, 'loftocean_tax_image', true ) );
				if ( ! empty( $imageID ) ) {
					do_action( 'loftocean_media_the_background_image', $imageID, $sizes, $args );
				}
			}
		}
		/**
		* Get taxonomy image src for category/post_tag
		*/
		private function get_taxonomy_image_src( $term, $size = false ) {
			if ( ( $term instanceof \WP_Term ) && in_array( $term->taxonomy, $this->tax ) ) {
				$tax_id 	= $term->term_id;
				$image_id 	= intval( get_term_meta( $tax_id, 'loftocean_tax_image', true ) );
				$image 		= wp_get_attachment_image_src( $image_id, $size );
				return $image ? $image[0] : false;
			}
			return false;
		}
		/**
		* Instantiate class to make sure only once instance exists
		*/
		public static function _instance() {
			if ( false === self::$_instance ) {
				self::$_instance = new Taxonomy_Metas();
			}
			return self::$_instance;
		}
	}
	// Add action to initialize Homepage Builder
	add_action( 'init', array( 'LoftOcean\Metas\Taxonomy_Metas', '_instance' ), 99 );
}
