<?php
namespace LoftOcean\Elementor\Library;

use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Base;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Source extends Source_Base {
	/**
	 * Get remote template ID.
	 * Retrieve the remote template ID.
	 * @return string The remote template ID.
	 */
	public function get_id() {
		return 'loftocean';
	}
	/**
	 * Get remote template title.
	 * Retrieve the remote template title.
	 *
	 * @return string The remote template title.
	 */
	public function get_title() {
		return esc_html__( 'CozyStay', 'loftocean' );
	}
	/**
	 * Register remote template data.
	 * Used to register custom template data like a post type, a taxonomy or any
	 * other data.
	 */
	public function register_data() {}
	/**
	 * Get remote templates.
	 * Retrieve remote templates from Elementor.com servers.
	 * @param array $args Optional. Nou used in remote source.
	 * @return array Remote templates.
	 */
	public function get_items( $args = [] ) {
		return [];
	}
	/**
	 * Get remote template.
	 * Retrieve a single remote template from Elementor.com servers.
	 * @param int $template_id The template ID.
	 * @return array Remote template.
	 */
	public function get_item( $template_id ) {
		$templates = $this->get_items();
		return $templates[ $template_id ];
	}

	/**
	 * Get template content.
	 *
	 * Retrieve the templates content received from a remote server.
	 *
	 * @since 1.0.0
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return object|WP_Error The template content.
	 */
	public function get_template_content( $template_id ) {
		$data = $this->curl( 'https://cozystay.loftocean.com/wp-json/cozystay-library/v1/get-element/' . $template_id );
		$data = json_decode( $data, true );
		if ( is_null( $data ) || isset( $data[ 'error' ] ) || empty( $data[ 'content' ] ) ) {
			return new WP_Error( 'no_data', $data[ 'error' ] );
		}
		return $data;
	}
	/**
	 * Get remote template data.
	 * Retrieve the data of a single remote template from Elementor.com servers.
	 * @param array  $args Custom template arguments.
	 * @param string $context Optional. The context. Default is `display`.
	 * @return array|WP_Error Remote Template data.
	 */
	public function get_data( array $args, $context = 'display' ) {
		if ( 'update' === $context ) {
			$data = $args['data'];
		} else {
			$data = $this->get_template_content( $args['template_id'] );
		}

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$data['content'] = $this->replace_elements_ids( $data['content'] );
		$data['content'] = $this->process_export_import_content( $data['content'], 'on_import' );

		$post_id  = $args['editor_post_id'];
		$document = Plugin::$instance->documents->get( $post_id );
		if ( $document ) {
			$data['content'] = $document->get_elements_raw_data( $data['content'], true );
		}

		if ( 'update' === $context ) {
			update_post_meta( $post_id, '_elementor_data', $data['content'] );
		}

		return $data;
	}
	/**
	 * Save remote template.
	 * Remote template from Elementor.com servers cannot be saved on the
	 * database as they are retrieved from remote servers.
	 * @param array $template_data Remote template data.
	 * @return WP_Error
	 */
	public function save_item( $template_data ) {
		return new WP_Error( 'invalid_request', 'Cannot save template to a remote source' );
	}
	/**
	 * Update remote template.
	 * Remote template from Elementor.com servers cannot be updated on the
	 * database as they are retrieved from remote servers.
	 * @param array $new_data New template data.
	 * @return WP_Error
	 */
	public function update_item( $new_data ) {
		return new WP_Error( 'invalid_request', 'Cannot update template to a remote source' );
	}
	/**
	 * Delete remote template.
	 * Remote template from Elementor.com servers cannot be deleted from the
	 * database as they are retrieved from remote servers.
	 * @param int $template_id The template ID.
	 * @return WP_Error
	 */
	public function delete_template( $template_id ) {
		return new WP_Error( 'invalid_request', 'Cannot delete template from a remote source' );
	}
	/**
	 * Export remote template.
	 * Remote template from Elementor.com servers cannot be exported from the
	 * database as they are retrieved from remote servers.
	 * @param int $template_id The template ID.
	 * @return WP_Error
	 */
	public function export_template( $template_id ) {
		return new WP_Error( 'invalid_request', 'Cannot export template from a remote source' );
	}
	/**
	* Get import data
	*/
	public function get_init_import_data( $data ) {
		$data = $this->replace_elements_ids( $data );
		$data = $this->process_export_import_content( $data, 'on_import' );
		return $data;
	}
	/**
	* curl get content
	*/
	protected function curl( $url ) {
		$curl = curl_init( $url );

		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_HEADER, 0 );
		curl_setopt( $curl, CURLOPT_USERAGENT, '' );
		curl_setopt( $curl, CURLOPT_TIMEOUT, 100 );

		$response = curl_exec( $curl );
		if ( 0 !== curl_errno( $curl ) || 200 !== curl_getinfo( $curl, CURLINFO_HTTP_CODE ) ) {
			$response = null;
		}
		curl_close( $curl );
		return $response;
	}
}
