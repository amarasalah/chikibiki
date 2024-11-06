<?php
namespace LoftOcean\Instagram;
// Instagram feed widget
class Widget extends \LoftOcean\Widget {
	/**
	* Boolean
	*/
	protected $force_add_widget = true;
	/**
	* Construct function
	*/
	public function __construct() {
		parent::__construct(
			'loftocean-widget-instagram',
			apply_filters( 'loftocean_get_widget_title', esc_html__( 'LoftOcean Instagram', 'loftocean' ), array( 'id' => 'instagram' ) ),
			array(
				'classname' => apply_filters( 'loftocean_get_widget_class', 'loftocean-widget_instagram', array( 'id' => 'instagram' ) ),
				'description' => esc_html__( 'Show your Instagram images.', 'loftocean' ),
				'customize_selective_refresh' => false
			)
		);
	}
	/**
	* Generate main content
	* @return html string
	*/
	public function widget( $args, $instance ) {
		$this->register_settings();
		$this->instance = $instance;
		$url = $this->get_value( 'title_link' );
		$feed = isset( $instance[ 'feed' ] ) ? $this->get_value( 'feed' ) : '';
		$new_tab = $this->get_value( 'target' );
		$number = intval( $this->get_value( 'number' ) );
		if ( ! $number ) {
			$number = 5;
		}
		if ( ! has_action( 'loftocean_instagram_the_html' ) ) {
			do_action( 'loftocean_instagram_actions' );
		}
		$instagram = apply_filters( 'loftocean_instagram_get_html', '', $feed, $number, $this->is_checked( 'target' ) );

		if ( ! empty( $instagram ) ) {
			$by_ajax = ( 'ajax' === apply_filters( 'loftocean_instagram_render_method', '' ) );
			$cols = $this->get_value( 'columns' );
			if ( $by_ajax ) {
				$attrs = sprintf(
					' data-user="%1$s" data-feed-id="%2$s" data-limit="%3$s" data-new-tab="%4$s" data-column="" data-location="widget" class=',
					esc_attr( $url ),
					esc_attr( $feed ),
					esc_attr( $number ),
					esc_attr( $new_tab ),
					empty( $cols ) ? '' : sprintf( ' %s', $cols )
				);
				echo wp_kses( str_replace( 'class=',  $attrs, $args['before_widget'] ), array( 'div' => array( 'class' => 1, 'id' => 1, 'style' => 1, 'data-*' => 1 ) ) );
			} else {
				echo wp_kses_post( str_replace( 'class="', sprintf( 'class="%s ', $cols ), $args['before_widget'] ) );
			}
			if ( defined( 'ELEMENTOR_VERSION' ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) : ?>
				<div class="elementor-instagram-settings" data-columns="<?php echo esc_attr( $cols ); ?>"></div><?php
			endif;
			if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
				$url = trim( $url );
				if ( ! empty( $url ) ) {
					$title = sprintf( '<a href="%1$s"%2$s>%3$s</a>', esc_url( $url ), ( $this->is_checked( 'target' ) ? ' target="_blank"' : '' ), $title );
				}
				echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
			}
			if ( ! $by_ajax ) {
				if ( ! has_action( 'loftocean_instagram_the_html' ) ) {
					do_action( 'loftocean_instagram_actions' );
				}
				do_action( 'loftocean_instagram_the_html', $feed, $number, $this->is_checked( 'target' ), array( 'location' => 'widget' ) );
			}
			echo wp_kses_post( $args['after_widget'] );
		}
	}
	/**
	 * Register all the form elements for showing
	 * 	Each control has at least id, type and default value
	 * 	For control with type select, should has a list of choices
	 * 	For each control can has attributes to the form elements
	 */
	public function register_settings() {
		$feeds = \LoftOcean\get_instagram_feeds();
		if ( \LoftOcean\is_valid_array( $feeds ) ) {
			$this->add_setting( array(
				'id' 		=> 'feed',
				'type'		=> 'select',
				'default'	=> '',
				'title'		=> esc_html__( 'Select a Feed', 'loftocean' ),
				'sanitize' 	=> 'choice',
				'choices'	=> $feeds
			) );
		} else {
			$this->add_setting( array(
				'id' 		=> 'warning',
				'type'		=> 'description',
				'default'	=> '',
				'description' => sprintf(
					// translators: 1. html tag start 2. html tag end
					esc_html__( 'Click %1$shere%2$s to know how to set up and configure your Instagram account.', 'loftocean' ),
					'<a href="https://loftocean.com/doc/cozystay/ptkb/instagram/" target="_blank">',
					'</a>'
				)
			) );
		}
		$this->add_setting( array(
			'id' 		=> 'title',
			'type'		=> 'text',
			'default'	=> esc_html__( 'Instagram', 'loftocean' ),
			'title'		=> esc_html__( 'Title', 'loftocean' ),
			'sanitize' 	=> 'text'
		) );
		$this->add_setting( array(
			'id' 		=> 'title_link',
			'type'		=> 'text',
			'default'	=> '',
			'title'		=> esc_html__( 'Title Link', 'loftocean' ),
			'sanitize' 	=> 'text'
		) );
		$this->add_setting( array(
			'id' 		=> 'columns',
			'type'		=> 'select',
			'default'	=> 'column-3',
			'title'		=> esc_html__( 'Number of columns', 'loftocean' ),
			'sanitize' 	=> 'choice',
			'choices'	=> array(
				'column-3' 	=> esc_html__( '3 Columns', 'loftocean' ),
				'column-4' 	=> esc_html__( '4 Columns', 'loftocean' ),
				'column-5' 	=> esc_html__( '5 Columns', 'loftocean' ),
				'column-6' 	=> esc_html__( '6 Columns', 'loftocean' ),
				'column-7' 	=> esc_html__( '7 Columns', 'loftocean' ),
				'column-8' 	=> esc_html__( '8 Columns', 'loftocean' )
			)
		) );
		$this->add_setting( array(
			'id' 			=> 'number',
			'type'			=> 'number',
			'default'		=> 5,
			'title'			=> esc_html__( 'Number of photos to show', 'loftocean' ),
			'input_attr'	=> array( 'step' => '1', 'min' => '1' ),
			'sanitize' 		=> 'number'
		) );
		$this->add_setting( array(
			'id' 		=> 'target',
			'type'		=> 'checkbox',
			'default'	=> '',
			'title'		=> esc_html__( 'Open images in new tab', 'loftocean' ),
			'sanitize' 	=> 'checkbox'
		) );
	}
}
