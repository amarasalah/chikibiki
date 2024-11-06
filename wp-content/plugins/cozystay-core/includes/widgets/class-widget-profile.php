<?php
namespace LoftOcean\Widget;
if ( ! class_exists( '\LoftOcean\Widget\Profile' ) ) {
	class Profile extends \LoftOcean\Widget {
		/**
		* Force output the widget json settings
		*/
		protected $force_add_widget = true;
		/**
		* Construct function
		*/
		public function __construct() {
			$class = apply_filters( 'loftocean_get_widget_class', 'loftocean-widget_profile', array( 'id' => 'profile' ) );
			$title = apply_filters( 'loftocean_get_widget_title', esc_html__( 'LoftOcean Profile', 'loftocean' ), array( 'id' => 'profile' ) );
			$description = apply_filters(
				'loftocean_get_widget_description',
				esc_html__( 'Add brief information about you and your site.', 'loftocean' ),
				array( 'id' => 'profile' )
			);
			parent::__construct(
				'loftocean-widget-profile',
				$title,
				array(
					'classname' => $class,
					'description' => $description,
					'customize_selective_refresh' => true,
				)
			);
		}
		/**
		* The buildin function to output the setting html for frontend
		 * @param array $args Arguments.
		 * @param array $instance Instance.
		*/
		public function widget( $args, $instance ) {
			$photo_id = empty( $instance['photo'] ) ? '' : $instance['photo'];
			if ( \LoftOcean\media_exists( $photo_id ) ) {
				$image_size = apply_filters( 'loftocean_get_image_size', 'medium', array( 'module' => 'widget', 'sub_module' => 'profile-image' ) );
				$attrs = wp_get_attachment_image_src( $photo_id, $image_size );
				if ( false !== $attrs ) {
					$args['before_widget'] = str_replace( ' class="', ' class="has-img ', $args['before_widget'] );
				}
			}
			parent::widget( $args, $instance );
		}
		/**
		* Generate main content
		* @return html string
		*/
		public function widget_content() {
			$image_size = apply_filters( 'loftocean_get_image_size', 'medium', array( 'module' => 'widget', 'sub_module' => 'profile-image' ) );
			$subtitle = esc_html( $this->get_value( 'subtitle' ) );
			$photo_id = intval( $this->get_value( 'photo' ) );

			$link_text = $this->get_value( 'link-text' );
			$link_url = $this->get_value( 'link-url' );
			$description = $this->get_value( 'description' );
			$show_social = $this->is_checked( 'show-social' );

			$allowed_html = array_merge( array( 'div' => array( 'class' => array() ) ), \LoftOcean\get_img_allowed_attrs() );

			if ( \LoftOcean\media_exists( $photo_id ) ) {
				$photo_src	= \LoftOcean\get_image_src( $photo_id, $image_size, false );
				$photo_width = intval( $this->get_value( 'photo-width' ) );
				$attrs = wp_get_attachment_image_src( $photo_id, $image_size );
				if ( false !== $attrs ) {
					$photo = \LoftOcean\filter_content_tags(
						sprintf(
							'<div class="profile"><img%1$s%2$s class="profile-img wp-image-%3$s" alt="%4$s" src="%5$s"></div>',
							' width=' . intval( $photo_width ),
							' height=' . intval( $photo_width / $attrs[1] * $attrs[2] ),
							esc_attr( $photo_id ),
							esc_attr( \LoftOcean\get_image_alt( $photo_id ) ),
							esc_url( $photo_src )
						)
					);
					echo wp_kses( $photo, $allowed_html );
				}
			} ?>
			<div class="textwidget"><?php
				if ( ! empty( $subtitle ) ) : ?>
					<h5 class="subheading"><?php echo esc_html( $subtitle ); ?></h5><?php
				endif;
				if ( ! empty( $description ) ) : ?>
					 <p><?php echo wp_kses_post( do_shortcode( $description ) ); ?></p><?php
				endif; ?>
			</div><?php
			if ( $show_social && apply_filters( 'loftocean_front_has_social_menu', false ) ) {
				do_action( 'loftocean_front_the_social_menu', $this->id );
			}
			if ( ! empty( $link_text ) || empty( $link_url ) ) : ?>
				<a href="<?php echo esc_url( $link_url ); ?>" class="button cs-btn-underline"><?php echo esc_html( $link_text ); ?></a><?php
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
				'sanitize'	=> 'text',
				'title'		=> esc_html__( 'Title:', 'loftocean' )
			) );
			$this->add_setting( array(
				'id' 		=> 'photo',
				'type'		=> 'image',
				'default'	=> '',
				'title'		=> esc_html__( 'Photo:', 'loftocean' ),
				'sanitize' 	=> 'number'
			) );
			$this->add_setting( array(
				'id' 		=> 'photo-width',
				'type'		=> 'number',
				'default'	=> '300',
				'title'		=> esc_html__( 'Photo Width:', 'loftocean' ),
				'sanitize' 	=> 'number'
			) );
			$this->add_setting( array(
				'id' 		=> 'subtitle',
				'type'		=> 'text',
				'default'	=> '',
				'sanitize' 	=> 'text',
				'title'		=> esc_html__( 'Sub Heading (optional):', 'loftocean' )
			) );
			$this->add_setting( array(
				'id' 		=> 'description',
				'type'		=> 'textarea',
				'default'	=> '',
				'sanitize' 	=> 'html',
				'title'		=> esc_html__( 'Description:', 'loftocean' )
			) );
			$this->add_setting( array(
				'id' 		=> 'show-social',
				'type'		=> 'checkbox',
				'default'	=> '',
				'sanitize' 	=> 'checkbox',
				'title'		=> esc_html__( 'Display Social Icons', 'loftocean' )
			) );
			$this->add_setting( array(
				'id' 		=> 'link-text',
				'type'		=> 'text',
				'default'	=> '',
				'sanitize' 	=> 'text',
				'title'		=> esc_html__( 'Button Text (optional):', 'loftocean' )
			) );
			$this->add_setting( array(
				'id' 		=> 'link-url',
				'type'		=> 'text',
				'default'	=> '#',
				'sanitize' 	=> 'url',
				'title'		=> esc_html__( 'Button URL (optional):', 'loftocean' )
			) );
		}
	}
}
