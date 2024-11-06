<?php
namespace LoftOcean\iCal;

/**
* iCal Sync management class
*/
class iCal_Sync {
    /**
    * Room post type
    */
    protected $room_post_type = 'loftocean_room';
    /**
    * Include file dir
    */
    protected $files_dir = '';
    /**
    * Logger instance
    */
    protected $logger = null;
    /**
    * Impotered booking manager
    */
    protected $imported_bookings_manager = null;
    /**
    * Imprter instance
    */
    protected $importer = null;
    /**
    * Construction function
    */
    public function __construct() {
        $this->files_dir = LOFTOCEAN_DIR . 'includes/custom-post-types/rooms/utils/';
        $this->require_files();
        if ( is_null( $this->logger ) ) {
            $this->logger = new \LoftOcean\iCal\Logger();
        }

		add_action( 'init', array( $this, 'add_ical_sync' ), 9999 );
	}
    /**
    * Add iCal sync actions
    */
	public function add_ical_sync() {
		add_feed( 'loftocean.ics', array( $this, 'export_ics' ) );
        add_action( 'loftocean_ical_sync_import_by_url', array( $this, 'import_by_url' ), 10, 4 );
	}
    /**
    * Export ics
    */
	public function export_ics() {
		if ( ( ! isset( $_GET[ 'room_id' ] ) ) && ( $this->room_post_type != get_post_type( $_GET[ 'room_id' ] ) ) ) return;

        require_once $this->files_dir . 'class-ical-exporter.php';
		$roomID = absint( $_GET[ 'room_id' ] );
        $exporter = new \LoftOcean\iCal\Exporter();
		$exporter->export( $roomID );
    }
    /**
    * Import ics by URL
    */
    public function import_by_url( $url, $roomID, $source_title, $time ) {
        if ( ( ! isset( $roomID ) ) && ( $this->room_post_type != get_post_type( $roomID ) ) ) return;

        if ( ! empty( $url ) ) {
            $response = wp_remote_get( $url );
            $this->logger->add_log( sprintf(
                // translators: 1: room title 2: source title 3: source url
                __( 'Start syncing for "%1$s" from "%2$s"(%3$s)', 'loftocean' ) . PHP_EOL,
                get_post_field( 'post_title', $roomID ),
                $source_title,
                $url
            ), $time );
            if ( ( ! is_wp_error( $response ) ) && ( 200 == wp_remote_retrieve_response_code( $response ) ) ) {
                $content = wp_remote_retrieve_body( $response );
                $this->import( $content, $roomID, base64_encode( $url ), $source_title, $time );
                $this->logger->add_log( PHP_EOL, $time );
            } else {
                $this->logger->add_log( esc_html__( 'Failed. Cannot get any feeds from the url.', 'loftocean' ), $time );
            }
        }
    }
    /**
    *
    */
    public function import( $content, $roomID, $source, $source_title, $time ) {
        if ( ! empty( $content ) ) {
            if ( is_null( $this->importer ) ) {
                $this->importer = new \LoftOcean\iCal\Importer( $this );
            }
            $this->importer->import( $content, $roomID, $source, $source_title, $time );
        }
    }
    /*
    * Require files for importer
    */
    protected function require_files() {
        $files = array( 'class-ical-importer.php', 'class-ical-sync-logger.php' );
        foreach( $files as $file ) {
            require_once $this->files_dir . $file ;
        }
    }
    /**
    * Get logger manager
    */
    public function get_logger() {
        return $this->logger;
    }
    /**
    * Get imported booking manager
    */
    public function get_imported_bookings_manager() {
        return $this->imported_bookings_manager;
    }
}
new iCal_Sync();
