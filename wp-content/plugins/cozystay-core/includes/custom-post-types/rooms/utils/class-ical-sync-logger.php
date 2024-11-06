<?php
namespace LoftOcean\iCal;

class Logger {
    /**
    * Admin notice message
    */
    protected $admin_notice_message = '';
    /**
    * Log file folder name
    */
    protected $log_files_folder = '';
    /**
    * Log file folder url
    */
    protected $log_files_folder_url = '';
    /**
    * Construction function
    */
    public function __construct() {
        if ( ! function_exists( '\get_filesystem_method' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
        $verified_credentials = $this->check_wp_filesystem_credentials();
		if ( is_wp_error( $verified_credentials ) ) {
            $this->admin_notice_message = $verified_credentials->get_error_message();
            add_action( 'admin_notices', array( $this, 'admin_notice' ) );
        } else {
            global $wp_filesystem;
            $upload_dir = wp_upload_dir();
            $this->log_files_folder_url = $upload_dir[ 'baseurl' ] . '/cozystay-core/';
            $this->log_files_folder =  $upload_dir['basedir'] . '/cozystay-core';
            if ( ! $wp_filesystem->is_dir( $this->log_files_folder ) ) {
                $wp_filesystem->mkdir( $this->log_files_folder );
            }

            $this->init();
        }
    }
    /**
    * Register filters
    */
    protected function init() {
        add_filter( 'loftocean_get_log_file_list', array( $this, 'get_log_file_list' ) );
        add_action( 'loftocean_remove_log_files', array( $this, 'remove_log_files' ) );
    }
    /**
    * No direct file access error admin notice
    */
    public function admin_notice() {
        if ( ! empty( $this->admin_notice_message ) ) {
            $class = 'notice notice-error';
            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $this->admin_notice_message );
        }
    }
    /**
    * Add message to log file
    */
    public function add_log( $message, $file ) {
        if ( empty( $message ) || empty( $file ) ) return;

        $this->append_to_file( $message, $this->get_log_path( $file ) );
    }
	/**
	* Append content to the file.
	*/
	public function append_to_file( $content, $file_path, $separator_text = '' ) {
		$verified_credentials = $this->check_wp_filesystem_credentials();
		if ( is_wp_error( $verified_credentials ) ) {
			return $verified_credentials;
		}
		global $wp_filesystem;
		$existing_data = '';

		if ( file_exists( $file_path ) ) {
			$existing_data = $wp_filesystem->get_contents( $file_path );
		}

        $content = empty( $existing_data ) ? $content : $existing_data . $content;

		if ( ! $wp_filesystem->put_contents( $file_path, $content . PHP_EOL ) ) {
			return new \WP_Error(
				'failed_writing_file_to_server',
				sprintf( /* translators: %1$s - br HTML tag, %2$s - file path */
					__( 'An error occurred while writing file to your server! Tried to write a file to: %1$s%2$s.', 'loftocean' ),
					'<br>',
					$file_path
				)
			);
		}
		return true;
	}
	/**
	* Get data from a file
	*/
	public function data_from_file( $file_path ) {
		$verified_credentials = $this->check_wp_filesystem_credentials();
		if ( is_wp_error( $verified_credentials ) ) {
			return $verified_credentials;
		}
		global $wp_filesystem;
		$data = $wp_filesystem->get_contents( $file_path );
		if ( ! $data ) {
			return new \WP_Error(
				'failed_reading_file_from_server',
				sprintf( /* translators: %1$s - br HTML tag, %2$s - file path */
					__( 'An error occurred while reading a file from your server! Tried reading file from path: %1$s%2$s.', 'loftocean' ),
					'<br>',
					$file_path
				)
			);
		}
		return $data;
	}
	/**
	* Helper function: check for WP file-system credentials needed for reading and writing to a file.
	*/
	private function check_wp_filesystem_credentials() {
		if ( ! ( 'direct' === get_filesystem_method() ) ) {
			return new \WP_Error(
				'no_direct_file_access',
				sprintf( /* translators: %1$s and %2$s - strong HTML tags, %3$s - HTML link to a doc page. */
					__( 'Your website does not have %1$sdirect%2$s write file access. This plugin needs it in order to save the log file to the upload directory of your site. You can change this setting with these instructions: %3$s.', 'loftocean' ),
					'<strong>',
					'</strong>',
					'<a href="https://kinsta.com/knowledgebase/constant-fs_method/" target="_blank">How To Change the Constant FS_METHOD</a>'
				)
			);
		}
		$page_url = wp_nonce_url( 'edit.php?post_type=loftocean_room&page=loftocean_room_ical_sync_settings', 'loftocean_log' );
		if ( false === ( $creds = request_filesystem_credentials( $page_url, '', false, false, null ) ) ) {
			return new \WP_error(
				'filesystem_credentials_could_not_be_retrieved',
				__( 'An error occurred while retrieving reading/writing permissions to your server (could not retrieve WP filesystem credentials)!', 'loftocean' )
			);
		}
		if ( ! WP_Filesystem( $creds ) ) {
			return new \WP_Error(
				'wrong_login_credentials',
				__( 'Your WordPress login credentials don\'t allow to use WP_Filesystem!', 'loftocean' )
			);
		}
		return true;
	}
	/**
	* Get log file path
	*/
	public function get_log_path( $file ) {
		return $this->log_files_folder . '/log-file-' . $file . '.txt';
	}
	/**
	* Get log file url
	*/
	public function get_log_url( $file ) {
		return $this->log_files_folder_url . $file;
	}
    /**
    * Get log file list
    */
    public function get_log_file_list( $list ) {
        $verified_credentials = $this->check_wp_filesystem_credentials();
		if ( ! is_wp_error( $verified_credentials ) ) {
            global $wp_filesystem;
            $files = $wp_filesystem->dirlist( $this->log_files_folder );
            if ( \LoftOcean\is_valid_array( $files ) ) {
                $list = array();
                $date_format = sprintf( '%1$s %2$s', get_option( 'date_format', 'Y-m-d' ), get_option( 'time_format', 'H:i:s' ) );
                foreach ( $files as $file ) {
                    $time = str_replace( array( 'log-file-', '.txt' ), '', $file[ 'name' ] );
                    if ( 'f' === $file[ 'type' ] && is_numeric( $time ) ) {
                        $list[ $file[ 'name' ] ] = array(
                            'name' => $file[ 'name' ],
                            'link' => $this->get_log_url( $file[ 'name' ] ),
                            'created_time' => $time,
                            'created_date' => date( $date_format, $time ),
                            'size' => $this->get_file_size( $file[ 'size' ] )
                        );
                    }
                }
                if ( \LoftOcean\is_valid_array( $list ) ) {
                    ksort( $list );
                    $list = array_reverse( $list );
                }
            }
        }
        return $list;
    }
    /**
    * Remove log files
    */
    public function remove_log_files( $list ) {
        if ( \LoftOcean\is_valid_array( $list ) ) {
            $verified_credentials = $this->check_wp_filesystem_credentials();
    		if ( ! is_wp_error( $verified_credentials ) ) {
                global $wp_filesystem;
                foreach( $list as $file ) {
                    $files = $wp_filesystem->delete( $this->log_files_folder . '/' . $file );
                }
            }
        }
    }
    /**
    * Get file size
    */
    protected function get_file_size( $size ) {
        if ( $size >= 1000000 ) {
            return round( $size / 100000 ) / 10 . 'MB';
        } else if ( $size >= 1000 ) {
            return round( $size / 100 ) / 10 . 'KB';
        } else {
            return $size . 'B';
        }
    }
}
