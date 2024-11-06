<?php
namespace LoftOcean\Elementor;
/**
 * Elementor Widget Video
 */
class Widget_Video extends \LoftOcean\Elementor_Widget_Base {
	/**
	 * Get widget name.
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return apply_filters( 'loftocean_elementor_widget_name', 'loftoceanvideo', array( 'id' => 'video' ) );
	}
	/**
	 * Get widget title.
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Video Block', 'loftocean' );
	}
	/**
	 * Get widget icon.
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-youtube';
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
	 * Get widget keywords.
	 * Retrieve the list of keywords the widget belongs to.
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'video', 'youtube' ];
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
	 * Register widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 * @access protected
	 */
	protected function register_controls() {
        $this->start_controls_section( 'general_content_section', array(
			'label' => __( 'General', 'loftocean' ),
			'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
		) );
		$this->add_control( 'source', array(
			'label' => esc_html__( 'External URL', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'description' => esc_html__( 'Note: Support .mp4 .webm and .ogg only for video from Media Libray, and YouTube/Vimeo for custom URL typed (link only, not embeded code).', 'loftocean' ),
			'default' => '',
            'label_on' => 'on',
            'label_off' => 'off',
            'return_value' => 'on'
		) );
		$this->add_control( 'hosted_url', array(
			'label' => esc_html__( 'Choose a Video', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::MEDIA,
			'media_type' => 'video',
			'condition' => array( 'source' => '' )
		) );
		$this->add_control( 'video_url', array(
			'label' => esc_html__( 'Video URL', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::URL,
			'default' => array( 'url' => 'https://www.youtube.com/watch?v=XHOmBV4js_E' ),
			'autocomplete' => false,
			'options' => false,
			'label_block' => true,
			'show_label' => false,
			'media_type' => 'video',
			'condition' => array( 'source' => 'on' )
		) );
		$this->add_control( 'play_button_size', array(
			'label' => esc_html__( 'Play Button Size', 'loftocean' ),
			'type' => \Elementor\Controls_Manager::NUMBER,
			'default' => '',
			'selectors' => array(
				'{{WRAPPER}} .cs-video-btn .video-play-btn' => 'width: {{VALUE}}px; height: {{VALUE}}px;',
			)
		) );
        $this->end_controls_section();
	}
	/**
	* Written in PHP and used to generate the final HTML.
    * @access protected
	*/
	protected function render() {
        $settings = $this->get_settings_for_display();
		$show_error = true;
		$insert_url = ( 'on' == $settings[ 'source' ] );
		$video_url = $insert_url && ! empty( $settings[ 'video_url' ][ 'url' ] ) ? $settings[ 'video_url' ][ 'url' ] : ( ! $insert_url && ! empty( $settings[ 'hosted_url' ][ 'url' ] ) ? $settings[ 'hosted_url' ][ 'url' ] : false );
		if ( ! empty( $video_url ) ) :
			$show_error = false;
			$video_type = 'hosted';
			$this->add_render_attribute( 'wrapper', 'class', array( 'cs-video-btn', 'elementor-video-block', 'text-center' ) );
			if ( preg_match( '#^https?://(?:www\.)?(?:youtube\.com/watch|youtu\.be/)#', $video_url ) ) {
				$video_type = 'youtube';
				$video_url = \Elementor\Embed::get_embed_url( $video_url, array( 'playsinline' => 1, 'wmode' => 'opaque', 'muted' => 'muted' ), array() );
			} else if ( preg_match( '#^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)#', $video_url ) ) {
				$video_type = 'vimeo';
				$video_url = \Elementor\Embed::get_embed_url( $video_url, array( 'playsinline' => 1, 'mute' => 'muted', 'vimeo_title' => 'title', 'vimeo_portrait' => 'portrait', 'vimeo_byline' => 'byline', 'autopause' => '0' ), array() );
			}
			$lightbox_options = array(
				'type' => 'video',
				'videoType' => $video_type,
				'url' => $video_url,
				'modalOptions' => array(
					'id' => 'elementor-lightbox-' . $this->get_id()
				)
			);
			$this->add_render_attribute( 'wrapper', array(
				'data-elementor-open-lightbox' => 'yes',
				'data-elementor-lightbox' => wp_json_encode( $lightbox_options ),
				'data-e-action-hash' => \Elementor\Plugin::instance()->frontend->create_action_hash( 'lightbox', $lightbox_options ),
			) ); ?>
			<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
				<div class="video-play-btn">
					<div class="btn-svg">
						<svg xmlns="http://www.w3.org/2000/svg" width="64" height="72" viewBox="0 0 64 72">
							<path stroke="#FFF" stroke-width="2" fill="none" d="m3.121 1.446 58.545 35.412L1.708 69.853 3.121 1.446Z"></path>
						</svg>
					</div>
				</div>
			</div><?php
		endif;
		if ( $show_error && \Elementor\Plugin::$instance->editor->is_edit_mode() ) : ?>
			<div class="cs-notice"><?php esc_html_e( 'Please choose a video on the widget setting panel.', 'loftocean' ); ?></div><?php
        endif;
	}
}
