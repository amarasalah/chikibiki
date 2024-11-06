<?php
namespace LoftOcean;
/**
 * Elementor Widget Base.
 */
abstract class Elementor_Widget_Base extends \Elementor\Widget_Base {
    /**
    * Array Widget settings
    */
    protected $widget_settings = array();
    /***
    * Construct function
    */
    public function __construct( array $data = [], array $args = null ) {
        parent::__construct( $data, $args );
        add_filter( 'loftocean_elementor_editor_json', array( $this, 'add_editor_json' ) );
    }
	/**
	 * Get widget categories.
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'loftocean-theme-category' );
	}
    /**
	* Help function to get setting value by its id, return the default value if not set
	* @param string widget setting id
	* @return mix setting's current value
	*/
	public function get_value( $id ) {
		return isset( $this->widget_settings[ $id ] ) ? $this->widget_settings[ $id ] : '';
	}
	/**
	* Help functin to test checkbox type setting is checked
	* @param setting id
	* @return boolean true if check, otherwise false
	*/
	protected function is_checked( $id ) {
		return ( 'on' == $this->get_value( $id ) );
	}
    /**
    * Add JavaScript variables to elementor editor
    */
    public function add_editor_json( $json ) {
        $deps = $this->get_widget_deps();
        $widgetID = $this->get_name();
        if ( \LoftOcean\is_valid_array( $deps ) && ! empty( $widgetID ) ) {
            $json['widgetDependency'] = isset( $json[ 'widgetDependency' ] ) ? array_merge( $json['widgetDependency'], array( $widgetID => $deps ) ) : array( $widgetID => $deps );
        }
        return $json;
    }
    /**
    * Get dependency for each controls of widget in editor
    * @return mix default boolean false, if array if needed
    */
    protected function get_widget_deps() {
        return false;
    }
	/**
	* Get JavaScript dependency to render this widget
	* @return array of script handler
	*/
	public function get_script_depends() {
		return array();
	}
	/**
	* Get style dependency to render this widget
	* @return array of style handler
	*/
	public function get_style_depends() {
		return array();
	}
    /**
    * Print the attributes for links
    */
    protected function the_link_attributes( $id = '', $attrs = array() ) {
        if ( empty( $id ) ) return ;

        $url = $this->get_value( $id );
        if ( \LoftOcean\is_valid_array( $url ) && ! empty( $url[ 'url' ] ) ) {
            $attrs[ 'href' ] = esc_url( $url[ 'url' ] );
            $nofollow_enabled = ! empty( $url[ 'nofollow' ] ) && ( 'on' == $url[ 'nofollow' ] );
            $no_rel = empty( $attrs[ 'rel' ] );
            if ( ! empty( $url[ 'is_external' ] ) && ( 'on' == $url[ 'is_external' ] ) ) {
                $attrs[ 'target' ] = '_blank';
                if ( $no_rel ) {
                    $attrs[ 'rel' ] = 'noopenner noreferrer';
                }
            }
            if ( $nofollow_enabled ) {
                $attrs[ 'rel' ] = empty( $attrs[ 'rel' ] ) ? 'nofollow' : ( $attrs[ 'rel' ] . ' nofollow' );
            }
        }
        if ( ! empty( $url[ 'custom_attributes' ] ) ) {
            $custom_attrs = explode( ',', $url[ 'custom_attributes' ] );
            foreach ( $custom_attrs as $custom_attr ) {
                $attr_pair = explode( '|', $custom_attr );
                if ( \LoftOcean\is_valid_array( $attr_pair ) && ( count( $attr_pair ) > 1 ) ) {
                    $name = trim( $attr_pair[0] );
                    if ( ! empty( $name ) ) {
                        $attrs[ $name ] = $attr_pair[1];
                    }
                }
            }
        }
        $attrs = array_filter( $attrs );
        if ( \LoftOcean\is_valid_array( $attrs ) ) {
            foreach ( $attrs as $name => $value ) {
                printf( ' %1$s="%2$s"', trim( $name ), trim( $value ) );
            }
        }
    }
}
