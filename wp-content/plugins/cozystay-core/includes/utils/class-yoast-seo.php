<?php
namespace LoftOcean\Utils;
use Yoast\WP\SEO\Presenters\Abstract_Indexable_Presenter;

if ( class_exists( '\Yoast\WP\SEO\Presenters\Abstract_Indexable_Presenter' ) ) {
    /**
     * Adds featured image for Category and tag pages.
     */
    class Yoast_SEO_Custom_Presenter extends Abstract_Indexable_Presenter {
    	/**
    	 * This output the full meta tags
    	 */
    	public function present() {
            $data = $this->get();
            if ( false === $data ) return '';
            return sprintf(
                '%1$s%2$s%3$s',
                '<meta property="og:image" content="' . esc_url( $data[0] ) . '" />',
                '<meta property="og:image:width" content="' . esc_attr( $data[1] ) . '" />',
                '<meta property="og:image:height" content="' . esc_attr( $data[2] ) . '" />'
            );
    	}

    	/**
    	 * Returns the value of the new tag.
    	 *
    	 * @return mix The value of our meta tag or false.
    	 */
    	public function get() {
            $image_id = intval( get_term_meta( get_queried_object_id(), 'loftocean_tax_image', true ) );
            return \LoftOcean\media_exists( $image_id ) ? wp_get_attachment_image_src( $image_id, 'full' ) : false;
    	}
    }
    /**
     * Adds custom presenter to the array of presenters.
     *
     * @param array $presenters The current array of presenters.
     *
     * @return array Presenters with our custom presenter added.
     */
    function add_tax_featured_image_presenter( $presenters ) {
        if ( is_category() || is_tag() ) {
            $presenters[] = new \LoftOcean\Utils\Yoast_SEO_Custom_Presenter();
        }

        return $presenters;
    }

    add_filter( 'wpseo_frontend_presenters', '\LoftOcean\Utils\add_tax_featured_image_presenter' );
}
