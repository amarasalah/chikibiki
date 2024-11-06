<?php
namespace LoftOcean\Elementor;

class Custom_Icon_Manager {
    /**
    * Static unique instance
    */
    private static $instance = null;
    /**
    * Make sure only one instance exists
    */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
    * Construction function
    */
    public function __construct() {
        add_filter( 'elementor/icons_manager/additional_tabs', array( $this, 'register_icons' ), 1, 1 );
    }
    /**
    * Register custom icons
    */
	public function register_icons( $icons_args = array() ) {
        return array_merge( array( 'loftocean-flaticons' => array(
	        'name' => 'loftocean-flaticons',
	        'label' => esc_html__( 'LoftOcean:: Hotel Icons', 'loftocean' ),
	        'labelIcon' => 'fa fa-hotel',
	        'prefix' => 'flaticon-',
	        'displayPrefix' => '',
	        'icons' => apply_filters( 'loftocean_get_flaticons', array() ),
	        'ver' => LOFTOCEAN_ASSETS_VERSION,
            'native' => true,
	    ) ), $icons_args );
	}
}
Custom_Icon_Manager::instance();
