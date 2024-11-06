<?php
namespace LoftOcean\Instagram;

/**
* Fetch remote file
*/
if ( ! class_exists( '\LoftOcean\Instagram\Download_Instagram_Feeds' ) ) {
	class Download_Instagram_Feeds {
		protected $limit = 20;
		protected $previous_image_map = array();
		protected $current_image_map = array();
		protected $feeds = array();
		protected $process_ids = array();
		/**
		* Main process control function
		*/
		public function init( $ids ) {
			if ( ! empty( $ids ) ) {
				$this->start( $ids );
				$this->download_files();
				$this->destroy();
			}
		}
		/**
		* Initialize environment
		*/
		protected function start( $ids ) {
			add_filter( 'import_post_meta_key', array( $this, 'is_valid_meta_key' ) );
			add_filter( 'http_request_timeout', array( &$this, 'bump_request_timeout' ) );

			$this->process_ids = explode( ',', $ids );
			$this->feeds = apply_filters( 'loftocean_instagram_get_feed', '', '' );
			$current = get_option( 'loftocean_instagram_feed_attachment_map', array() );
			if ( ! empty( $current ) && is_array( $current ) ) {
				$this->previous_image_map = $current;
			}
		}
		/**
		* Start to download files
		*/
		protected function download_files() {
			$start_download = false;
			foreach( $this->feeds as $index => $feed ) {
				$key = 'item-' . $feed['feed_id'];
				if ( in_array( $feed['feed_id'], $this->process_ids ) ) {
					$start_download = true;
					$download_feed = true;
					if ( isset( $this->previous_image_map[ $key ] ) ) {
						$attach_id = $this->previous_image_map[ $key ];
						$this->delete_from_previous_map( $key );
						if ( \LoftOcean\media_exists( $attach_id ) ) {
							$download_feed = false;
							$this->update_map( $key, $attach_id );
							$this->update_map_option();
						}
					}
					if ( $download_feed ) {
						$attachment_id = $this->process_attachment( array(), $feed['url'], $feed['feed_id'] );
						// $attachment_id = $this->process_attachment( array(), $feed['url'], $key );
						if ( ! is_wp_error( $attachment_id ) ) {
							$this->feeds[ $index ]['attachment_id'] = $attachment_id;
							$this->save_cache();
							$this->update_map( $key, $attachment_id );
							$this->update_map_option();
						}
					}
				} else if ( $start_download ) {
					break;
				} else if ( isset( $this->previous_image_map[ $key ] ) ) {
					$attach_id = $this->previous_image_map[ $key ];
					$this->delete_from_previous_map( $key );
					if ( \LoftOcean\media_exists( $attach_id ) ) {
						$this->update_map( $key, $attach_id );
						$this->update_map_option();
					}
				}
			}
		}
		/**
		* Reset environment
		*/
		public function destroy() {
			remove_filter( 'import_post_meta_key', array( $this, 'is_valid_meta_key' ) );
			remove_filter( 'http_request_timeout', array( &$this, 'bump_request_timeout' ) );
		}
		/**
		* Decide if the given meta key maps to information we will want to import
		* @param string $key The meta key to check
		* @return string|bool The key if we do want to import, false if not
		*/
		public function is_valid_meta_key( $key ) {
			if ( in_array( $key, array( '_wp_attached_file', '_wp_attachment_metadata', '_edit_lock' ) ) ) {
				return false;
			}
			return $key;
		}
		/**
		* Added to http_request_timeout filter to force timeout at 60 seconds during import
		* @return int 70
		*/
		public function bump_request_timeout( $val ) {
			return 70;
		}
		/**
		* Attempt to create a new attachment
		* @param array $post Attachment post details from WXR
		* @param string $url URL to fetch attachment from
		* @return int|WP_Error Post ID on success, WP_Error otherwise
	 	*/
		public function process_attachment( $post, $url, $id ) {
			$upload = $this->fetch_file( $url, $id );
			if ( is_wp_error( $upload ) ) {
				return $upload;
			}

			if ( $info = wp_check_filetype( $upload['file'] ) ) {
				$post['post_mime_type'] = $info['type'];
			} else {
				return new WP_Error( 'attachment_processing_error', __( 'Invalid file type', 'loftocean' ) );
			}

			$post['guid'] = $upload['url'];
			$post['post_title'] = $upload['name'];

			// as per wp-admin/includes/upload.php
			$post_id = wp_insert_attachment( $post, $upload['file'] );
			if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
				require_once ABSPATH . 'wp-admin/includes/image.php';
			}
			wp_update_attachment_metadata( $post_id, wp_generate_attachment_metadata( $post_id, $upload['file'] ) );
			return $post_id;
		}
		/**
		* Attempt to download a remote file attachment
		* @param string $url URL of item to fetch
		* @param array $post Attachment details
		* @return array|WP_Error Local file location details on success, WP_Error otherwise
		*/
		public function fetch_file( $url, $id ) {
			// Extract the file name from the URL.
			$file_name = empty( $id ) ? basename( parse_url( $url, PHP_URL_PATH ) ) : ( 'instagram-' . $id );

			if ( ! $file_name ) {
				$file_name = md5( $url );
			}
			if ( ! function_exists( 'wp_tempnam' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}
			$tmp_file_name = wp_tempnam( $file_name );
			if ( ! $tmp_file_name ) {
				return new WP_Error( 'import_no_file', __( 'Could not create temporary file.', 'loftocean' ) );
			}

			// Fetch the remote URL and write it to the placeholder file.
			$remote_response = wp_safe_remote_get( $url, array(
				'timeout'    => 300,
				'stream'     => true,
				'filename'   => $tmp_file_name,
				'headers'    => array(
					'Accecs-Encoding' => 'identity',
				),
			) );

			if ( is_wp_error( $remote_response ) ) {
				@unlink( $tmp_file_name );
				return new WP_Error(
					'import_file_error',
					sprintf(
						/* translators: 1: The WordPress error message. 2: The WordPress error code. */
						__( 'Request failed due to an error: %1$s (%2$s)', 'loftocean' ),
						esc_html( $remote_response->get_error_message() ),
						esc_html( $remote_response->get_error_code() )
					)
				);
			}

			$remote_response_code = (int) wp_remote_retrieve_response_code( $remote_response );
			// Make sure the fetch was successful.
			if ( 200 !== $remote_response_code ) {
				@unlink( $tmp_file_name );
				return new WP_Error(
					'import_file_error',
					sprintf(
						/* translators: 1: The HTTP error message. 2: The HTTP error code. */
						__( 'Remote server returned the following unexpected result: %1$s (%2$s)', 'loftocean' ),
						get_status_header_desc( $remote_response_code ),
						esc_html( $remote_response_code )
					)
				);
			}

			$headers = wp_remote_retrieve_headers( $remote_response );
			// Request failed.
			if ( ! $headers ) {
				@unlink( $tmp_file_name );
				return new WP_Error( 'import_file_error', __( 'Remote server did not respond', 'loftocean' ) );
			}

			$filesize = (int) filesize( $tmp_file_name );

			if ( 0 === $filesize ) {
				@unlink( $tmp_file_name );
				return new WP_Error( 'import_file_error', __( 'Zero size file downloaded', 'loftocean' ) );
			}

			if ( ! isset( $headers['content-encoding'] ) && isset( $headers['content-length'] ) && $filesize !== (int) $headers['content-length'] ) {
				@unlink( $tmp_file_name );
				return new WP_Error( 'import_file_error', __( 'Downloaded file has incorrect size', 'loftocean' ) );
			}
			// Override file name with Content-Disposition header value.
			if ( ! empty( $headers['content-disposition'] ) ) {
				$file_name_from_disposition = self::get_filename_from_disposition( (array) $headers['content-disposition'] );
				if ( $file_name_from_disposition ) {
					$file_name = $file_name_from_disposition;
				}
			}
			// Set file extension if missing.
			$file_ext = pathinfo( $file_name, PATHINFO_EXTENSION );
			if ( ! $file_ext && ! empty( $headers['content-type'] ) ) {
				$extension = self::get_file_extension_by_mime_type( $headers['content-type'] );
				if ( $extension ) {
					$file_name = "{$file_name}.{$extension}";
				}
			}
			// Handle the upload like _wp_handle_upload() does.
			$wp_filetype     = wp_check_filetype_and_ext( $tmp_file_name, $file_name );
			$ext             = empty( $wp_filetype['ext'] ) ? '' : $wp_filetype['ext'];
			$type            = empty( $wp_filetype['type'] ) ? '' : $wp_filetype['type'];
			$proper_filename = empty( $wp_filetype['proper_filename'] ) ? '' : $wp_filetype['proper_filename'];

			// Check to see if wp_check_filetype_and_ext() determined the filename was incorrect.
			if ( $proper_filename ) {
				$file_name = $proper_filename;
			}
			if ( ( ! $type || ! $ext ) && ! current_user_can( 'unfiltered_upload' ) ) {
				return new WP_Error( 'import_file_error', __( 'Sorry, this file type is not permitted for security reasons.', 'loftocean' ) );
			}
			$uploads = wp_upload_dir();
			if ( ! ( $uploads && false === $uploads['error'] ) ) {
				return new WP_Error( 'upload_dir_error', $uploads['error'] );
			}
			$feed_dirname = $uploads['basedir'].'/loftocean-instagram';
			if ( ! file_exists( $feed_dirname ) ) {
				wp_mkdir_p( $feed_dirname );
			}

			// Move the file to the uploads dir.
			$file_name     = wp_unique_filename( $feed_dirname, $file_name );
			$new_file      = $feed_dirname . "/{$file_name}";
			$move_new_file = copy( $tmp_file_name, $new_file );

			if ( ! $move_new_file ) {
				@unlink( $tmp_file_name );
				return new WP_Error( 'import_file_error', __( 'The uploaded file could not be moved', 'loftocean' ) );
			}

			// Set correct file permissions.
			$stat  = stat( dirname( $new_file ) );
			$perms = $stat['mode'] & 0000666;
			chmod( $new_file, $perms );

			$upload = array(
				'file'  => $new_file,
				'name' 	=> $file_name,
				'url'   => $uploads['baseurl'] . "/loftocean-instagram/{$file_name}",
				'type'  => $wp_filetype['type'],
				'error' => false,
			);
			return $upload;
		}

		/**
		 * Decide what the maximum file size for downloaded attachments is.
		 * Default is 0 (unlimited), can be filtered via import_attachment_size_limit
		 * @return int Maximum attachment file size to import
		 */
		public function max_attachment_size() {
			return apply_filters( 'import_attachment_size_limit', 0 );
		}
		// return the difference in length between two strings
		public function cmpr_strlen( $a, $b ) {
			return strlen($b) - strlen($a);
		}
		/**
		* @param string[] $disposition_header List of Content-Disposition header values.
		* @return string|null Filename if available, or null if not found.
		*/
		protected static function get_filename_from_disposition( $disposition_header ) {
			// Get the filename.
			$filename = null;

			foreach ( $disposition_header as $value ) {
				$value = trim( $value );

				if ( strpos( $value, ';' ) === false ) {
					continue;
				}

				list( $type, $attr_parts ) = explode( ';', $value, 2 );

				$attr_parts = explode( ';', $attr_parts );
				$attributes = array();

				foreach ( $attr_parts as $part ) {
					if ( strpos( $part, '=' ) === false ) {
						continue;
					}

					list( $key, $value ) = explode( '=', $part, 2 );

					$attributes[ trim( $key ) ] = trim( $value );
				}

				if ( empty( $attributes['filename'] ) ) {
					continue;
				}

				$filename = trim( $attributes['filename'] );

				// Unquote quoted filename, but after trimming.
				if ( substr( $filename, 0, 1 ) === '"' && substr( $filename, -1, 1 ) === '"' ) {
					$filename = substr( $filename, 1, -1 );
				}
			}

			return $filename;
		}

		/**
		* Retrieves file extension by mime type.
		* @param string $mime_type Mime type to search extension for.
		* @return string|null File extension if available, or null if not found.
		*/
		protected static function get_file_extension_by_mime_type( $mime_type ) {
			static $map = null;

			if ( is_array( $map ) ) {
				return isset( $map[ $mime_type ] ) ? $map[ $mime_type ] : null;
			}

			$mime_types = wp_get_mime_types();
			$map        = array_flip( $mime_types );

			// Some types have multiple extensions, use only the first one.
			foreach ( $map as $type => $extensions ) {
				$map[ $type ] = strtok( $extensions, '|' );
			}

			return isset( $map[ $mime_type ] ) ? $map[ $mime_type ] : null;
		}
		/**
		* Save feed cache
		*/
		public function save_cache() {
			set_transient( 'loftocean_instagram-token-user', maybe_serialize( $this->feeds ), apply_filters( 'loftocean_instagram_cache_time', HOUR_IN_SECONDS * 12 ) );
		}
		/**
		* Update feed attachment map option
		*/
		protected function update_map_option() {
			if ( empty( $this->previous_image_map ) ) {
				$map = empty( $this->current_image_map ) ? array() : $this->current_image_map;
			} else if ( empty( $this->current_image_map ) ) {
				$map = $this->previous_image_map;
			} else {
				$map = array_merge( $this->current_image_map, $this->previous_image_map );
			}
			update_option( 'loftocean_instagram_feed_attachment_map', $map );
		}
		/**
		* Update current feed attachmet map
		*/
		protected function update_map( $key, $val ) {
			$this->current_image_map = array_merge( $this->current_image_map, array( $key => $val ) );
		}
		/**
		* Delete from previous map
		*/
		protected function delete_from_previous_map( $key ) {
			if ( isset( $this->previous_image_map[ $key ] ) ) {
				unset( $this->previous_image_map[ $key ] );
			}
		}
	}
}
