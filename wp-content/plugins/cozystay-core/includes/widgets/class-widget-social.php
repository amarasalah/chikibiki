<?php
namespace LoftOcean\Widget;
if ( ! class_exists( '\LoftOcean\Widget\Social' ) ) {
	class Social extends \LoftOcean\Widget {
		/**
		* Construct function
		*/
		public function __construct() {
			$class = apply_filters( 'loftocean_get_widget_class', 'loftocean-widget_social', array( 'id' => 'social' ) );
			$title = apply_filters( 'loftocean_get_widget_title', esc_html__( 'LoftOcean Social', 'loftocean' ), array( 'id' => 'social' ) );
			$description = apply_filters( 'loftocean_get_widget_description', esc_html__( 'Display your social menu.', 'loftocean' ), array( 'id' => 'social' ) );
			parent::__construct(
				'loftocean-widget-social',
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
			$this->register_settings();
			$this->instance = $instance;
			$this->widget_start( $args, $instance );
			$this->widget_content( $args );
			$this->widget_end( $args );
		}
		/**
		* Generate main content
		* @return html string
		*/
		public function widget_content( $args = array() ) {
			if ( has_nav_menu( 'social-menu' ) ) {
				wp_nav_menu( array(
					'theme_location' 	=> 'social-menu',
					'depth' 			=> 1,
					'container' 		=> 'div',
					'container_class' 	=> 'socialwidget',
					'menu_class' 		=> 'social-nav menu',
					'menu_id' 			=> sprintf( 'social-menu-%s', $args['widget_id'] )
				) );
			}
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
		}
	}
}
