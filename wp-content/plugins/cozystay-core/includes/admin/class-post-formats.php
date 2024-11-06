<?php
namespace LoftOcean;
if ( ! class_exists( 'Meta_Post_Format' ) ) {
	class Meta_Post_Format {
		protected $format_media 	= false;
		protected $format_meta_name = 'loftocean-format-media';
		protected $background_video = array();
		protected $posts_media 		= array();
		/**
		* Construct function
		*/
		public function __construct() {
			add_action( 'loftocean_save_post_metabox_settings', array( $this, 'save_post_formats' ) );
			add_action( 'admin_footer-post.php', array( $this, 'format_meta_box' ) );
			add_action( 'admin_footer-post-new.php', array( $this, 'format_meta_box' ) );
			add_action( 'loftocean_front_the_post_featured_media', array( $this, 'the_post_format_media' ), 10, 3 );

			add_filter( 'loftocean_front_background_video', array( $this, 'background_video' ), 10, 2 );
			add_filter( 'loftocean_front_has_background_video', array( $this, 'has_background_video' ), 10, 2 );
			add_filter( 'loftocean_front_has_vimeo_bg_video', array( $this, 'has_vimeo_background_video' ), 10, 2 );
			add_filter( 'loftocean_front_get_post_featured_media', array( $this, 'get_post_format_media' ) );
			add_filter( 'loftocean_front_has_post_featured_media', array( $this, 'has_post_format_media' ) );
		}
		/**
		* Save post format settings
		*/
		public function save_post_formats( $post_id ) {
			if ( ! empty( $_REQUEST[ $this->format_meta_name ] ) ) {
				add_filter( 'wp_kses_allowed_html', array( $this, 'support_video_audio' ), 999, 2 );
				$format_media = array_map( 'wp_kses_post', wp_unslash( $_REQUEST[ $this->format_meta_name ] ) );
				remove_filter( 'wp_kses_allowed_html', array( $this, 'support_video_audio' ), 999, 2 );
				$format_media['gallery-code'] = $this->sanitize_textarea( $format_media, 'gallery-code' );
				$format_media['gallery-id'] = $this->sanitize_textarea( $format_media, 'gallery-id' );
				$format_media['audio-code'] = $this->sanitize_html( $format_media, 'audio-code' );
				$format_media['audio-id'] = $this->sanitize_num( $format_media, 'audio-id' );
				$format_media['video-code'] = $this->sanitize_html( $format_media, 'video-code' );
				$format_media['video-id'] = $this->sanitize_num( $format_media, 'video-id' );

				update_post_meta( $post_id, 'loftocean_post_format_gallery', $format_media['gallery-code'] );
				update_post_meta( $post_id, 'loftocean_post_format_gallery_ids', $format_media['gallery-id'] );

				update_post_meta( $post_id, 'loftocean_post_format_video_id', $format_media['video-id'] );
				update_post_meta( $post_id, 'loftocean_post_format_video', $format_media['video-code'] );

				update_post_meta( $post_id, 'loftocean_post_format_audio_id', $format_media['audio-id'] );
				update_post_meta( $post_id, 'loftocean_post_format_audio', $format_media['audio-code'] );
			}
		}
		/*
		* @description show background video
		* @param string video html string
		* @param int post id
		* @return string video html string or empty
		*/
		public function background_video( $video, $pid ) {
			$key = 'post-' . $pid;
			if ( array_key_exists( $key, $this->background_video ) ) {
				return $this->background_video[ $key ];
			}

			if ( ! empty( $pid ) && ( false !== get_post_status( $pid ) ) ) {
				$p = get_post( $pid );
				$p_type = $p->post_type;
				$video = '';
				if ( ( 'post' == $p_type ) && ( 'video' == get_post_format( $pid ) ) ) {
					$media = $this->get_media_settings( $pid );
					$video = ( is_array( $media ) && isset( $media['video-code'] ) ) ? $media['video-code'] : '';
				} else if ( 'page' == $p_type ) {
					$media = get_post_meta( $pid, 'loftocean-page-background-video', true );
					$video = ( is_array( $media ) && isset( $media['code'] ) ) ? $media['code'] : '';
				}
				$this->background_video[ $key ] = empty( $video ) ? '' : $this->get_video( $video );
				return $this->background_video[ $key ];
			}
			return '';
		}
		/*
		* @description test if has background video
		* @param boolean current result
		* @param int post id
		* @return boolean true if background video exists
		*/
		public function has_background_video( $has, $pid ) {
			$video = apply_filters( 'loftocean_front_background_video', '', $pid );
			return ! empty( $video );
		}
		/*
		* @description test if has vimeo background video
		* @param boolean current result
		* @param int post id
		* @return boolean true if vimeo background video exists
		*/
		public function has_vimeo_background_video( $has, $pid ) {
			$regex = '/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)/';
			$video = apply_filters( 'loftocean_front_background_video', '', $pid );
			return ! empty( $video ) && preg_match( $regex, $video );
		}
		/**
		* Output template html for post format metabox
		*/
		public function format_meta_box() {
			global $post; 
			if ( ( $post->post_type == 'post' ) && current_theme_supports( 'post-formats' ) && post_type_supports( $post->post_type, 'post-formats' ) ) :
				$pid = $post->ID;
				$format = get_post_format( $pid );  ?>
				<script type="text/html" id="loftocean-tmpl-format-meta-box" data-format="<?php echo esc_attr( $format ); ?>">
					<div id="loftocean-format-media" style="padding-top:10px;">
						<h4 style="margin: 0;"><?php esc_html_e( 'Set the cover image gallery shown in the post list (instead of featured image)', 'loftocean' ); ?> </h4>
						<div class="format gallery">
							<p><a href="#" class="format-media gallery"><?php esc_html_e( 'Choose Gallery', 'loftocean' ); ?></a></p>
							<label>
								<?php esc_html_e( 'Or type manually:', 'loftocean' ); ?>
								<textarea <?php $this->get_format_name( 'gallery-code' ); ?> style="width: 98%; height: 70px;" class="gallery-code"><?php $this->get_format_content( 'gallery-code', 'textarea' ); ?></textarea>
								<input <?php $this->get_format_name( 'gallery-id' ); ?> type="hidden" value="<?php $this->get_format_content( 'gallery-id' ); ?>" class="gallery-id" >
							</label>
							<span class="description"><?php esc_html_e( '(gallery shortcode allowed only)', 'loftocean' ); ?></span>
						</div>
						<div class="format audio">
							<p>
								<a href="#" class="format-media audio"><?php esc_html_e( 'Choose Audio', 'loftocean' ); ?></a>&nbsp;&nbsp;&nbsp;
								<a href="#" class="clear-audio"><?php esc_html_e( 'Clear Audio', 'loftocean' ); ?></a>
							</p>
							<label>
								<textarea readonly="readonly" class="audio-code" <?php $this->get_format_name( 'audio-code' ); ?> style="width: 98%; height: 115px;"><?php $this->get_format_content( 'audio-code', 'textarea' ); ?></textarea>
								<input class="audio-id" type="hidden" <?php $this->get_format_name( 'audio-id' ); ?> value="<?php $this->get_format_content( 'audio-id' ); ?>" />
							</label>
						</div>
						<div class="format video">
							<p><a href="#" class="format-media video"><?php esc_html_e( 'Choose Video', 'loftocean' ); ?></a></p>
							<label>
								<?php esc_html_e( 'Or type manually:', 'loftocean' ); ?>
								<textarea class="video-code" <?php $this->get_format_name( 'video-code' ); ?> style="width: 98%; height: 115px;"><?php $this->get_format_content( 'video-code', 'textarea' ); ?></textarea>
								<input class="video-id" type="hidden" <?php $this->get_format_name( 'video-id' ); ?> value="<?php $this->get_format_content( 'video-id' ); ?>" />
							</label>
							<span style="font-size: 11px;">
								<?php /* translators: 1: html tag start. 2: html tag end. */ ?>
								<?php printf( esc_html__( '%1$sNote:%2$s support %1$sYoutube/Vimeo Embed <iframe>%2$s or %1$sHTML5 <video>%2$s only.', 'loftocean' ), '<b>', '</b>' ); ?>
							</span>
							<?php do_action( 'loftocean_post_metabox_format_video', $post ); ?>
						</div>
					</div>
				</script> <?php
			endif;
		}
		/**
		* get post format media string
		* @param array
		*/
		public function get_post_format_media( $media ) {
			global $post, $content_width;
			$pid = $post->ID;
			if ( ! isset( $this->posts_media[ $pid ] ) ) {
				$format_media = '';
				$format_content = $this->get_media_settings();
				if ( ! empty( $format_content ) && is_array( $format_content ) ) {
					switch( get_post_format() ) {
						case 'gallery':
							$format_media = empty( $format_content['gallery-code'] ) ? '' : $this->get_gallery( $format_content['gallery-code'] );
							break;
						case 'video':
							$width 			= $content_width;
							$content_width 	= 600; // Make the initial width to 600.
							$format_media 	= empty( $format_content['video-code'] ) ? '' : $this->get_video( $format_content['video-code'] );
							$content_width 	= $width; // Reset the default content width.
							break;
						case 'audio':
							$format_media = empty( $format_content['audio-code'] ) ? '' : do_shortcode( $format_content['audio-code'] );
							break;
						default:
							$format_media = false;
					}
				}
				$this->posts_media[ $pid ] = $format_media;
			}
			return $this->posts_media[ $pid ];
		}
		/**
		* Display the featured media
		* @param boolean show zoom button for gallery
		*/
		public function the_post_format_media( $zoom = false, $class = '', $args = false ) {
			global $content_width;
			$format_content = $this->get_media_settings();
			if ( ! empty( $format_content ) && is_array( $format_content ) ) {
				switch( get_post_format() ) {
					case 'gallery':
						$format_media = empty( $format_content['gallery-code'] ) ? '' : $this->the_gallery(
							$format_content['gallery-code'],
							array( 'zoom' => $zoom, 'class' => $class, 'multi-image-gallery' => ! empty( $args[ 'multi-image-gallery' ] ), 'no-caption' => ! empty( $args['no-caption'] ) )
						);
						break;
					case 'video':
						if ( ! empty( $format_content['video-code'] ) ) {
							$width = $content_width;
							$content_width 	= 600; // Make the initial width to 600.
							$this->the_video( $format_content['video-code'] ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
							$content_width 	= $width; // Reset the default content width.
						}
						break;
					case 'audio':
						if ( ! empty( $format_content['audio-code'] ) ) {
							echo do_shortcode( $format_content['audio-code'] ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
						}
						break;
				}
			}
		}
		/**
		* Test if current post has featured media
		* @param boolean
		* @return boolean
		*/
		public function has_post_format_media( $has ) {
			$media = apply_filters( 'loftocean_front_get_post_featured_media', '' );
			return ! empty( $media );
		}
		/**
		* Get format media content for post format meta box
		* @param string post format type name
		* @param string content type
		* @return string post format media if exists
		*/
		protected function get_format_content( $name, $type = 'input' ) {
			$media = $this->get_media_settings();
			if ( ! empty( $media ) && isset( $media[ $name ] ) ) {
				if ( 'input' === $type ) {
					echo esc_attr( $media[ $name ] );
				} else {
					echo esc_textarea( $media[ $name ] );
				}
			}
		}
		/**
		* Get media settings
		* @param mix specific post id or current post id
		* @return array
		*/
		protected function get_media_settings( $pid = false ) {
			if ( empty( $pid ) || ( false === get_post_status( $pid ) ) ) {
				global $post;
				$pid = $post->ID;
			}
			return array(
				'gallery-id' => get_post_meta( $pid, 'loftocean_post_format_gallery_ids', true ),
				'gallery-code' => get_post_meta( $pid, 'loftocean_post_format_gallery', true ),
				'audio-id' => get_post_meta( $pid, 'loftocean_post_format_audio_id', true ),
				'audio-code' => get_post_meta( $pid, 'loftocean_post_format_audio', true ),
				'video-id' => get_post_meta( $pid, 'loftocean_post_format_video_id', true ),
				'video-code' => get_post_meta( $pid, 'loftocean_post_format_video', true )
			);
		}
		/**
		* Get format media name
		* @param string meta name
		* @return string
		*/
		protected function get_format_name( $name ) {
			if ( ! empty( $name ) ) {
				printf( 'name="%1$s[%2$s]"', esc_attr( $this->format_meta_name ), esc_attr( $name ) );
			}
		}
		/**
		* Sanitization function for textarea value
		* @param array
		* @param string meta name
		* @return string
		*/
		protected function sanitize_textarea( $values, $name ) {
			return empty( $values[ $name ] ) ? '' : sanitize_text_field( $values[ $name ] );
		}
		/**
		* Sanitization function for html string
		* @param array
		* @param string meta name
		* @return string
		*/
		protected function sanitize_html( $values, $name ) {
			if ( current_user_can( 'unfiltered_html' ) ) {
				return $values[ $name ];
			} else {
				global $allowedposttags;
				return empty( $values[ $name ] ) ? '' : wp_kses( $values[ $name ], array_merge( $allowedposttags, array(
					'iframe' => array(
						'width' 			=> true,
						'height' 			=> true,
						'src' 				=> true,
						'frameborder' 		=> true,
						'allowfullscreen' 	=> true
					)
				) ) );
			}
		}
		/**
		* Sanitization function for number
		* @param array
		* @param string meta name
		* @return mix
		*/
		protected function sanitize_num( $values, $name ) {
			return empty( $values[ $name ] ) ? '' : absint( $values[ $name ] );
		}
		/**
		* Sanitization function for url
		* @param array
		* @param string meta name
		* @return string
		*/
		protected function sanitize_url( $values, $name ) {
			return empty( $values[ $name ] ) ? '' : esc_url_raw( $values[ $name ] );
		}
		/**
		* Sanitization function for video
		* @param string
		* @return mix
		*/
		protected function get_video( $video ) {
			if ( ! empty( $video ) ) {
				$regex_video 	= '/<video[^>]*>.*<\/video>/';
				$regex_youtube	= '/^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/';
				$regex_vimeo 	= '/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)/';
				if ( has_shortcode( $video, 'video' ) ) {
					return do_shortcode( $video );
				} else if ( preg_match( $regex_video, $video ) || preg_match( $regex_youtube, $video ) || preg_match( $regex_vimeo, $video ) ) {
					return $video;
				}
			}
			return false;
		}
		/**
		* Output video html
		* @param string
		*/
		protected function the_video( $video ) {
			if ( ! empty( $video ) ) {
				$regex_video 	= '/<video[^>]*>.*<\/video>/';
				$regex_youtube	= '/^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/';
				$regex_vimeo 	= '/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)/';
				if ( has_shortcode( $video, 'video' ) ) {
					echo do_shortcode( $video );
				} else if ( preg_match( $regex_video, $video ) || preg_match( $regex_youtube, $video ) || preg_match( $regex_vimeo, $video ) ) {
					echo wp_kses( $video, $this->support_video_audio( wp_kses_allowed_html( 'post' ) ) );
				}
			}
		}
		/**
		* Get gallery html
		* @param string
		*/
		protected function get_gallery( $gallery ) {
			if ( apply_filters( 'loftocean_front_show_post_featured_media_gallery', true )
				&& ! empty( $gallery )
				&& has_shortcode( $gallery, 'gallery' )
				&& preg_match_all( '/' . get_shortcode_regex() . '/s', $gallery, $matches, PREG_SET_ORDER ) ) {
				$list_wrap = '<ul class="thumbnail-gallery">%s</ul>';
				$item_wrap = '<li class="%1$s">%2$s</li>';
				$image_sizes = apply_filters( 'loftocean_get_post_format_gallery_image_sizes', array( 'full', 'full' ) );
				foreach ( $matches as $shortcode ) {
					if ( 'gallery' === $shortcode[2] ) {
						$atts = (array) shortcode_parse_atts( $shortcode[3] );
						$html = '';
						$ids = array();
						if ( ! empty( $atts['ids'] ) ) {
							$ids = explode( ',', $atts['ids'] );
						} else {
							$images = get_attached_media( 'image' );
							foreach ( $images as $img ) {
								array_push( $ids, $img->ID );
							}
						}
						if ( ! empty( $ids ) ) {
							$index = 0;
							foreach ( $ids as $id ) {
								$image_html = apply_filters( 'loftocean_media_get_responsive_image', '', $id, $image_sizes[0] );
								if ( ! empty( $image_html ) ) {
									$is_first = ( $index++ === 0 );
									$html .= sprintf( $item_wrap, $is_first ? ' first' : ' hide', $image_html );
								}
							}
							if ( ! empty( $html ) ) {
								return sprintf( $list_wrap, $html );
							}
						}
					}
				}
			}
			return $gallery;
		}
		/**
		* Output the gallery html
		* @param string
		* @param boolean
		*/
		protected function the_gallery( $gallery, $args = array() ) {
			$args = array_merge( array( 'zoom' => false, 'class' => '', 'multi-image-gallery' => false, 'no-caption' => false ), $args );
			if ( apply_filters( 'loftocean_front_show_post_featured_media_gallery', true )
				&& ! empty( $gallery )
				&& has_shortcode( $gallery, 'gallery' )
				&& preg_match_all( '/' . get_shortcode_regex() . '/s', $gallery, $matches, PREG_SET_ORDER ) ) {
				$image_sizes = apply_filters( 'loftocean_get_post_format_gallery_image_sizes', array( 'full', 'full' ) );
				foreach ( $matches as $shortcode ) {
					if ( 'gallery' === $shortcode[2] ) {
						$atts = (array) shortcode_parse_atts( $shortcode[3] );
						$has_gallery = false;
						$ids = array();
						if ( ! empty( $atts['ids'] ) ) {
							$ids = explode( ',', $atts['ids'] );
						} else {
							$images = get_attached_media( 'image' );
							foreach ( $images as $img ) {
								array_push( $ids, $img->ID );
							}
						}
						if ( ! empty( $ids ) ) {
							foreach ( $ids as $id ) {
								if ( \LoftOcean\media_exists( $id ) ) {
									$has_gallery = true;
									break;
								}
							}
						}
						if ( $has_gallery ) :
							$is_multi_image_gallery = $args['multi-image-gallery'];
							$class = empty( $args['class'] ) ? 'single-img-gallery' : $args['class'];
							$index = 0; ?>
							<ul class="thumbnail-gallery <?php echo esc_attr( $class ); ?>"><?php
							foreach ( $ids as $id ) :
								if ( \LoftOcean\media_exists( $id ) ) :
									$caption = wp_get_attachment_caption( $id );
									$is_first = ( $index++ === 0 ); ?>
									<li class="<?php if ( $is_first ) : ?>first<?php else: ?>hide<?php endif; ?>"><?php
										do_action( 'loftocean_media_the_responsive_image', $id, $image_sizes[0] );
										if ( ( ! empty( $caption ) ) && ( ! $args['no-caption'] ) ) : ?>
											<span class="featured-img-caption"><?php echo wp_kses_post( $caption ); ?></span><?php
										endif; ?>
									</li><?php
								endif;
							endforeach; ?>
							</ul>
							<?php if ( $args['zoom'] ) : ?><div class="loftocean-gallery-zoom zoom"></div><?php endif;
						endif;
						break;
					}
				}
			}
		}
		/**
		* Add support for video and audio
		* @param array
		* @param string
		* @return array
		*/
		public function support_video_audio( $allowedtags, $context = '' ) {
			$media = array(
				'iframe' => array(
					'width' => array(),
					'height' => array(),
					'src' => array(),
					'frameborder' => array(),
					'allow' => array(),
					'allowfullscreen' => array()
				),
				'video' => array(
					'class' => array(),
					'id' => array(),
					'width' => array(),
					'height' => array(),
					'preload' => array(),
					'controls' => array(),
					'src' => array()
				),
				'audio' => array(
					'class' => array(),
					'id' => array(),
					'width' => array(),
					'height' => array(),
					'preload' => array(),
					'controls' => array(),
					'src' => array()
				),
				'source' => array( 'type' => array(), 'src' => array() )
			);
			foreach( $media as $tag => $attrs ) {
				if ( empty( $allowedtags[ $tag ] ) ) {
					$allowedtags[ $tag ] = $attrs;
				} else {
					$allowedtags[ $tag ] = array_merge( $allowedtags[ $tag ], $attrs );
				}
			}
			return $allowedtags;
		}
	}
	new Meta_Post_Format();
}
