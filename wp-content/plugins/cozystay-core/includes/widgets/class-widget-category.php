<?php
namespace LoftOcean\Widget;
if ( ! class_exists( '\LoftOcean\Widget\Category' ) ) {
	class Category extends \LoftOcean\Widget{
		/**
		* Construct function
		*/
		public function __construct() {
			$class = apply_filters( 'loftocean_get_widget_class', 'loftocean-widget_category', array( 'id' => 'category' ) );
			$title = apply_filters( 'loftocean_get_widget_title', esc_html__( 'LoftOcean Category', 'loftocean' ), array( 'id' => 'category' ) );
			$description = apply_filters(
				'loftocean_get_widget_description',
				esc_html__( 'Display your selected categories with background image.', 'loftocean' ),
				array( 'id' => 'category' )
			);
			parent::__construct(
				'loftocean-widget-category',
				$title,
				array(
					'classname' => $class,
					'description' => $description,
					'customize_selective_refresh' => true,
				)
			);
		}
		/**
		 * Output the html at the start of a widget.
		 *
		 * @param array $args Arguments.
		 * @param array $instance Instance.
		 */
		public function widget_start( $args, $instance ) {
			$args['before_widget'] = str_replace( 'class="', 'class="style-stripe ', $args['before_widget'] );
			parent::widget_start( $args, $instance );
		}
		/**
		* Generate main content
		* @return html string
		*/
		public function widget_content() {
			$cids = \LoftOcean\convert_tax_slug2id( $this->get_value( 'categories' ) );
			if ( ! empty( $cids ) && is_array( $cids ) ) :
				$image_size = $image_size = apply_filters( 'loftocean_get_image_sizes', array( 'thumbnail', 'thumbnail' ), array( 'module' => 'widget', 'sub_module' => 'category-background' ) );
				$categories = get_terms( array( 'include' => $cids, 'taxonomy' => 'category', 'hide_empty' => $this->is_checked( 'hide-empty' ) ) ); ?>
				<div class="catwidget"><?php
				foreach ( $categories as $cat ) :
					$cat_url = get_term_link( $cat, 'category' ); ?>
					<div class="cat">
						<a href="<?php echo esc_url( $cat_url ); ?>">
							<?php do_action( 'loftocean_front_the_taxonomy_featured_image', $cat, $image_size, array( 'class' => 'cat-bg', 'attrs' => array( 'data-no-lazy' => 1 ) ) ); ?>
							<div class="cat-meta"><span class="category-name"><?php echo esc_html( $cat->name ); ?></span></div>
						</a>
					</div><?php
				endforeach; ?>
				</div><?php
			else : ?>
				<p class="error-message nothing-found category"><?php esc_html_e( 'No category selected.', 'loftocean' ); ?></p> <?php
			endif;
		}
		/**
		 * Register all the form elements for showing
		 * 	Each control has at least id, type and default value
		 * 	For control with type select, should has a list of choices
		 * 	For each control can has attributes to the form elements
		 */
		public function register_settings() {
			$this->add_setting( array(
				'id' 		=> 'title',
				'type'		=> 'text',
				'default'	=> '',
				'title'		=> esc_html__( 'Title', 'loftocean' ),
				'sanitize' 	=> 'text'
			) );
			$this->add_setting( array(
				'id' 			=> 'categories',
				'type'			=> 'select',
				'default'		=> array(),
				'input_attr'	=> array( 'multiple' => 'multiple' ),
				'title'			=> esc_html__( 'Choose Categories', 'loftocean' ),
				'sanitize' 		=> 'choices',
				'choices'		=> \LoftOcean\get_terms( 'category', false )
			) );
			$this->add_setting( array(
				'id' 		=> 'hide-empty',
				'type'		=> 'checkbox',
				'default'	=> 'on',
				'sanitize'	=> 'checkbox',
				'title'		=> esc_html__( 'Hide Empty Categories', 'loftocean' )
			) );
		}
	}
}
