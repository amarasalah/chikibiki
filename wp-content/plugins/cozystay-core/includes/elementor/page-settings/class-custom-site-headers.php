<?php
namespace LoftOcean\Elementor\Page_Settings;

class Custom_Site_Headers {
    /**
    * Object
    */
    protected $document = false;
    /**
    * Construct function
    */
    public function __construct( $document ) {
        $this->document = $document;
        $this->init();
    }
    /**
    * Init function
    */
    protected function init() {
        $document = $this->document;
        if ( ! $document instanceof \Elementor\Core\DocumentTypes\PageBase || ! $document::get_property( 'has_elements' ) ) {
            return;
        }

        $document->start_controls_section( 'custom_post_page_settings', array(
            'label' => esc_html__( 'CozyStay Site Header Settings', 'loftocean' ),
            'tab' => \Elementor\Controls_Manager::TAB_SETTINGS
        ) );
    	$document->add_control( 'enable_overlap', array(
			'label' => esc_html__( 'Overlap Header', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
            'default' => 'off',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
    	) );
        $document->end_controls_section();
    }
}
