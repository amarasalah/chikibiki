<?php
namespace LoftOcean\Widget;
if ( ! class_exists( '\LoftOcean\Widget\Posts' ) ) {
	class Posts extends \LoftOcean\Widget {
		/**
		* Construct function
		*/
		public function __construct() {
			$class = apply_filters( 'loftocean_get_widget_class', 'loftocean-widget_posts', array( 'id' => 'posts' ) );
			$title = apply_filters( 'loftocean_get_widget_title', esc_html__( 'LoftOcean Posts', 'loftocean' ), array( 'id' => 'posts' ) );
			$description = apply_filters( 'loftocean_get_widget_description', esc_html__( 'Display your posts.', 'loftocean' ), array( 'id' => 'posts' ) );
			parent::__construct(
				'loftocean-widget-posts',
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
			$class = $this->get_class();
			if ( ! empty( $class ) ) {
				$replace = sprintf( 'class="%s ', $class );
				$args['before_widget'] = str_replace( 'class="', $replace, $args['before_widget'] );
			}
			parent::widget_start( $args, $instance );
		}
		/**
		* Generate main content
		* @return html string
		*/
		public function widget_content() {
			$posts = $this->get_posts();
			$meta = $this->get_meta_show();
			$has_meta = ! empty( $meta );
			$has_meta_category = $has_meta || $this->is_checked( 'post-meta-category' );

			if ( $posts->have_posts() ) : ?>
				<ul data-show-list-number="<?php echo $this->is_checked( 'show-list-number' ) ? 'on' : ''; ?>"><?php
				while ( $posts->have_posts() ) :
					$posts->the_post(); ?>
					<li>
						<a href="<?php the_permalink(); ?>" class="post-link"><?php
							if ( has_post_thumbnail() ) : ?><div class="thumbnail"><?php the_post_thumbnail( 'thumbnail', array( 'data-no-lazy' => 1 ) ); ?></div><?php endif; ?>
							<div class="post-content">
								<h4 class="post-title"><?php the_title(); ?></h4><?php
								if ( $has_meta_category ) : ?>
									<div class="meta-wrap"><?php
										if ( $this->is_checked( 'post-meta-category' ) ) : ?>
											<div class="cat-links"><?php $this->the_category_list( get_the_ID() ); ?></div><?php
										endif;
										if ( $has_meta ) : ?>
											<div class="meta"><?php
											if ( in_array( 'author', $meta ) ) : ?>
												<div class="meta-item author"><?php the_author(); ?></div><?php
											endif;
											if ( in_array( 'date', $meta ) ) : ?>
												<span class="meta-item"><?php echo esc_html( get_the_date() ); ?></span><?php
											endif; ?>
											</div> <?php
										endif; ?>
									</div><?php
								endif; ?>
							</div>
						</a>
					</li><?php
				endwhile;
				wp_reset_postdata(); ?>
				</ul><?php
			else : ?>
				<div class="post-content"><h4 class="post-title"><?php esc_html_e( 'Nothing Found', 'loftocean' ); ?></h4></div><?php
			endif;
		}
		/**
		* Get widget custom classes
		* @return string class
		*/
		private function get_class() {
			$class = array( 'small-thumbnail' );
			$this->is_checked( 'show-list-number' ) ? array_push( $class, 'with-post-number' ) : '';
			return empty( $class ) ? '' : sprintf( '%s', implode( ' ', $class ) );
		}
		/**
		* Get the category list
		*/
		protected function the_category_list( $id ) {
			$cats = get_the_category( $id );
			$list = array();
			foreach ( $cats as $c ) {
				array_push( $list, '<span>' . $c->name . '</span>' );
			}
			echo implode( ' ', $list );
		}
		/**
		* Get posts by current widget settings
		* @return object WP_Query object
		*/
		protected function get_posts() {
			$ppp = $this->get_value( 'number' );
			$args = array(
				'posts_per_page' => $ppp,
				'ignore_sticky_posts' => true,
				'post_type' => 'post',
				'paged' => 1
			);
			return new \WP_Query( apply_filters(
				'loftocean_get_widget_posts_query_args',
				$args,
				array_merge( $this->defaults, $this->instance )
			) );
		}
		/**
		* Get meta list checked to show
		* @return array
		*/
		private function get_meta_show() {
			$meta = array();
			$sets = array(
				'date' 		=> 'post-meta-date',
				'author'	=> 'post-meta-author'
			);
			foreach( $sets as $id => $name ) {
				if ( $this->is_checked( $name ) ) {
					array_push( $meta, $id );
				}
			}
			return $meta;
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
				'sanitize'	=> 'text',
				'title'		=> esc_html__( 'Title:', 'loftocean' )
			) );
			$this->add_setting( array(
				'id' 		=> 'filter-by',
				'type'		=> 'select',
				'default'	=> 'latest',
				'title'		=> esc_html__( 'Choose Posts:', 'loftocean' ),
				'sanitize'	=> 'choice',
				'choices'	=> array(
					'latest' 	=> esc_html__( 'Latest', 'loftocean' ),
					'category'	=> esc_html__( 'From a selected category', 'loftocean' ),
					'featured' 	=> esc_html__( 'Featured posts', 'loftocean' ),
					'views'		=> esc_html__( 'Most viewed', 'loftocean' ),
					'likes' 	=> esc_html__( 'Most liked', 'loftocean' ),
					'comments'	=> esc_html__( 'Most commented', 'loftocean' )
				)
			) );
			$this->add_setting( array(
				'id' 			=> 'category',
				'type'			=> 'select',
				'default'		=> '',
				'title'			=> '',
				'sanitize' 		=> 'choice',
				'dependency'	=> array( 'filter-by' => array( 'value' => array( 'category' ) ) ),
				'choices'		=> \LoftOcean\get_terms( 'category', true, esc_html__( 'Choose a category', 'loftocean' ) )
			) );
			$this->add_setting( array(
				'id' 		=> 'show-list-number',
				'type'		=> 'checkbox',
				'default'	=> 'on',
				'sanitize'	=> 'checkbox',
				'title'		=> esc_html__( 'Show list Number', 'loftocean' )
			) );
			$this->add_setting( array(
				'id' 			=> 'number',
				'type'			=> 'number',
				'default'		=> 3,
				'sanitize'		=> 'number',
				'title'			=> esc_html__( 'Number of posts to show', 'loftocean' ),
				'input_attr'	=> array( 'min' => 1, 'max' => 10 )
			) );
			$this->add_setting( array(
				'id' 		=> 'post-meta-title',
				'type'		=> 'title',
				'default'	=> '',
				'title'		=> esc_html__( 'Display selected post meta', 'loftocean' )
			) );
			$this->add_setting( array(
				'id' 		=> 'post-meta-author',
				'type'		=> 'checkbox',
				'default'	=> '',
				'sanitize'	=> 'checkbox',
				'title'		=> esc_html__( 'Author', 'loftocean' )
			) );
			$this->add_setting( array(
				'id' 		=> 'post-meta-category',
				'type'		=> 'checkbox',
				'default'	=> 'on',
				'sanitize'	=> 'checkbox',
				'title'		=> esc_html__( 'Category', 'loftocean' )
			) );
			$this->add_setting( array(
				'id' 		=> 'post-meta-date',
				'type'		=> 'checkbox',
				'default'	=> 'on',
				'sanitize'	=> 'checkbox',
				'title'		=> esc_html__( 'Publish Date', 'loftocean' )
			) );
		}
	}
}
