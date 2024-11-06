<?php
namespace LoftOcean\Widget;
/**
* Facebook widget
*/

add_action( 'wp_enqueue_scripts', '\LoftOcean\Widget\loftocean_widget_facebook' );
function loftocean_widget_facebook() { 
	if ( is_active_widget( false, false, 'loftocean-widget_facebook', true )
		|| ( is_singular() && class_exists( '\Elementor\Plugin' ) && ( ! empty( \Elementor\Plugin::$instance->documents->get( get_the_ID() ) ) )
			&& ( \Elementor\Plugin::$instance->documents->get( get_the_ID() )->is_built_with_elementor() || \Elementor\Plugin::$instance->editor->is_edit_mode() )
		)
	) {
		wp_enqueue_script( 'loftocean-facebook', LOFTOCEAN_URI . 'assets/scripts/front/facebook-jssdk.min.js', array(), LOFTOCEAN_ASSETS_VERSION, true );
	}
}
// Facebook page widget
class Facebook extends \LoftOcean\Widget {
	/**
	* Construct function
	*/
	function __construct() {
		parent::__construct(
			'loftocean-widget_facebook',
			apply_filters( 'loftocean_get_widget_title', esc_html__( 'LoftOcean Facebook', 'loftocean' ), array( 'id' => 'facebook' ) ),
			array(
				'classname' => apply_filters( 'loftocean_get_widget_class', 'loftocean-widget_facebook', array( 'id' => 'facebook' ) ),
				'description' => esc_html__( 'Show your Facebook Page.', 'loftocean' ),
				'customize_selective_refresh' => true
			)
		);
	}
	/**
	* Generate main content
	* @return html string
	*/
	public function widget_content() {
		$username = $this->get_value( 'username' );
		if ( ! empty( $username ) ) :
			$url = ( false !== strpos( $username, 'facebook.com' ) ) ?  $username : ( 'https://facebook.com/' . $username ); ?>
			<div id="<?php echo esc_attr( $this->id ); ?>-wrap">
				<div class="fb-page loftocean-fb-page"
					data-href="<?php echo esc_attr( $url ); ?>"
					data-width="320"
					data-height="500"
					data-tabs=""
					data-hide-cover="0"
					data-show-facepile="1"
					data-hide-cta="0"
					data-small-header="0"
					data-adacs-container-width="1"
				></div>
			</div> <?php
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
			'default'	=> esc_html__( 'Like on Facebook', 'loftocean' ),
			'title'		=> esc_html__( 'Title', 'loftocean' ),
			'sanitize' 	=> 'text'
		) );
		$this->add_setting( array(
			'id' 		=> 'username',
			'type'		=> 'text',
			'default'	=> '',
			'title'		=> esc_html__( 'Facebook Username', 'loftocean' ),
			'sanitize' 	=> 'text'
		) );
	}
}
