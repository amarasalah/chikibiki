<?php
namespace LoftOcean\Elementor;

class Page_Setting_Manager {
    /**
    * Construct function
    */
    public function __construct() {
        add_action( 'elementor/documents/register_controls', array( $this, 'page_settings' ), 999 );
    }
    /**
    * Init
    */
    public function page_settings( $document ) {
        $inc = LOFTOCEAN_DIR . 'includes/elementor/page-settings/';
        switch ( get_post_type() ) {
            case 'custom_site_headers':
                require_once $inc . 'class-custom-site-headers.php';
                new \LoftOcean\Elementor\Page_Settings\Custom_Site_Headers( $document );
                break;
            case 'loftocean_room':
                $document->remove_control( 'template' );
                $document->remove_control( 'template_default_description' );
                break;
        }
    }
}
new Page_Setting_Manager();
