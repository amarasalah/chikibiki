<?php
namespace LoftOcean;
// Elementor Extension Manager

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

final class Elementor_Extension {
	/**
	 * Minimum Elementor Version
	 */
	const MINIMUM_ELEMENTOR_VERSION = '3.0.0';
	/**
	 * Instance
	 * @access private
	 * @static
	 */
	private static $_instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 * @access public
	 * @static
	 */
	public static function _instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	/**
	 * Constructor
	 * @access public
	 */
	public function __construct() {
		$this->init_extension();
	}
	/**
	 * Checks if Elementor has loaded, and performs some compatibility checks.
	 * If All checks pass, inits the plugin.
	 * @access public
	 */
	public function init_extension() {
		if ( $this->is_compatible() ) {
			add_action( 'elementor/init', array( $this, 'init' ), 0 );
		}
	}
	/**
	 * Compatibility Checks
	 * Checks if the installed version of Elementor meets the plugin's minimum requirement.
	 * Checks if the installed PHP version meets the plugin's minimum requirement.
	 * @access public
	 */
	public function is_compatible() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			return false;
		}
		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
			return false;
		}
		return true;
	}
	/**
	 * Initialize the plugin
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Load the files required to run the plugin.
	 * @access public
	 */
	public function init() {
		// Add Plugin actions
		add_action( 'elementor/elements/categories_registered', array( $this, 'init_categories' ), 999999 );
		add_action( 'elementor/controls/register', array( $this, 'init_controls' ) );
		add_action( 'elementor/widgets/register', array( $this, 'init_widgets' ) );
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue_editor_assets' ) );
		add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_elementor_style' ) );

		add_filter( 'loftocean_is_built_with_elementor', array( $this, 'is_built_with_elementor' ), 10, 2 );
		add_filter( 'loftocean_elementor_parse_content', array( $this, 'parse_content' ), 10, 2 );
		// add_filter( 'elementor/widgets/black_list', array( $this, 'exclude_wp_widgets' ), 99 );

		$inc = LOFTOCEAN_DIR . 'includes/elementor/';
		require_once $inc . 'page-settings/class-manager.php';
		require_once $inc . 'library/class-manager.php';
		require_once $inc . 'extra/class-section.php';
		require_once $inc . 'extra/class-column.php';
		require_once $inc . 'extra/class-commons.php';
		require_once $inc . 'extra/class-widgets.php';
		require_once $inc . 'icons/class-custom-icons-manager.php';

		do_action( 'loftocean_elementor_loaded' );
	}
	/**
	 * Init Widgets
	 * Include widgets files and register them
	 * @access public
	 */
	public function init_widgets() {
		$inc = LOFTOCEAN_DIR . 'includes/elementor/widgets/';
		$widgets = $this->get_widgets();
		require_once LOFTOCEAN_DIR . 'includes/abstracts/abstract-elementor-widget-base.php';
		foreach ( $widgets as $file => $class ) {
			require_once $inc . $file;
			if ( version_compare( ELEMENTOR_VERSION, '3.5.0', '<' ) ) {
				\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new $class );
			} else {
				\Elementor\Plugin::instance()->widgets_manager->register( new $class );
			}
		}
	}
	/**
	 * Init Controls
	 * Include controls files and register them
	 * @access public
	 */
	public function init_controls() {
		$inc = LOFTOCEAN_DIR . 'includes/elementor/controls/';
		$controls = $this->get_controls();
		foreach ( $controls as $control ) {
			require_once $inc . $control[ 'file' ];
			if ( version_compare( ELEMENTOR_VERSION, '3.5.0', '<' ) ) {
				\Elementor\Plugin::$instance->controls_manager->register_control( $control[ 'type' ], new $control[ 'class' ] );
			} else {
				\Elementor\Plugin::$instance->controls_manager->register( new $control[ 'class' ] );
			}
		}
	}
	/**
	* Init Categories
	* @access public
	*/
	public function init_categories( $elements_manager ) {
		$elements_manager->add_category( 'loftocean-theme-category', array(
			'title' => apply_filters( 'loftocean_elementor_category_title', esc_html__( 'Loft.Ocean Theme', 'loftocean' ) ),
			'icon' => 'fa fa-plug',
		) );

		$reorder_cats = function() {
            uksort( $this->categories, function( $keyOne, $keyTwo ) {
                return 'loftocean-theme-category' == $keyOne ? -1
                	: ( 'loftocean-theme-category' == $keyTwo ? 1 : 0 );
            } );
        };
		\Closure::bind( $reorder_cats, $elements_manager, $elements_manager )();
	}
	/**
	 * Admin notice
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {
		if ( isset( $_GET[ 'activate' ] ) ) unset( $_GET[ 'activate' ] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'loftocean' ),
			'<strong>' . esc_html__( 'CozyStay Core', 'loftocean' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'loftocean' ) . '</strong>',
			 self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}
	/**
	* Widgets
	* @access private
	*/
	private function get_widgets() {
		$widgets = array(
			'class-widget-button.php' => '\LoftOcean\Elementor\Widget_Button',
			'class-widget-section-title.php' => '\LoftOcean\Elementor\Widget_Section_Title',
			'class-widget-circle-button.php' => '\LoftOcean\Elementor\Widget_Circle_Button',
			'class-widget-call-to-action.php' => '\LoftOcean\Elementor\Widget_Call_To_Action',
			'class-widget-info-box.php' => '\LoftOcean\Elementor\Widget_Info_Box',
			'class-widget-image-gallery.php' => '\LoftOcean\Elementor\Widget_Image_Gallery',
			'class-widget-fancy-card.php' => '\LoftOcean\Elementor\Widget_Fancy_Card',
			'class-widget-reservation-filter.php' => '\LoftOcean\Elementor\Widget_Reservation_Filter',
			'class-widget-rooms.php' => '\LoftOcean\Elementor\Widget_Rooms',
			'class-widget-social-menu.php' => '\LoftOcean\Elementor\Widget_Social_Menu',
			'class-widget-site-logo.php' => '\LoftOcean\Elementor\Widget_Site_Logo',
			'class-widget-testimonials.php' => '\LoftOcean\Elementor\Widget_Testimonials',
			'class-widget-block-links.php' => '\LoftOcean\Elementor\Widget_Block_Links',
			'class-widget-list.php' => '\LoftOcean\Elementor\Widget_List',
			'class-widget-instagram.php' => '\LoftOcean\Elementor\Widget_Instagram',
			'class-widget-countdown.php' => '\LoftOcean\Elementor\Widget_Count_Down',
			'class-widget-team-member.php' => '\LoftOcean\Elementor\Widget_Team_Member',
			'class-widget-divider.php' => '\LoftOcean\Elementor\Widget_Divider',
			'class-widget-vertical-divider.php' => '\LoftOcean\Elementor\Widget_Vertical_Divider',
			'class-widget-mobile-menu-toggle.php' => '\LoftOcean\Elementor\Widget_Mobile_Menu_Toggle',
			'class-widget-navigation-menu.php' => '\LoftOcean\Elementor\Widget_Navigation_Menu',
			'class-widget-search.php' => '\LoftOcean\Elementor\Widget_Search_Button',
			'class-widget-tabs.php' => '\LoftOcean\Elementor\Widget_Tabs',
			'class-widget-blog.php' => '\LoftOcean\Elementor\Widget_Blog',
			'class-widget-slider.php' => '\LoftOcean\Elementor\Widget_Slider',
			'class-widget-food-menu.php' => '\LoftOcean\Elementor\Widget_Food_Menu',
			'class-widget-opentable.php' => '\LoftOcean\Elementor\Widget_OpenTable',
			'class-widget-food-card.php' => '\LoftOcean\Elementor\Widget_Food_Card',
		);
		if ( class_exists( '\WPCF7' ) ) {
			$widgets[ 'class-widget-contact-form7.php' ] = '\LoftOcean\Elementor\Widget_Contact_Form7';
		}
		if ( class_exists( '\MC4WP_Form_Manager' ) ) {
			$widgets[ 'class-widget-mc4wp-form.php' ] = '\LoftOcean\Elementor\Widget_MC4WP_Form';
		}
		if ( class_exists( '\WooCommerce' ) ) {
			$widgets[ 'class-widget-mini-cart.php' ] = '\LoftOcean\Elementor\Widget_Mini_Cart';
			$widgets[ 'class-widget-products.php' ] = '\LoftOcean\Elementor\Widget_Products';
		}
		$widgets[ 'class-widget-video.php' ] = '\LoftOcean\Elementor\Widget_Video';
		return $widgets;
	}
	/**
	* Controls
	* @access private
	*/
	private function get_controls() {
		return array();
	}
	/**
	* Enquque style for widgets/controls
	*/
	public function enqueue_editor_assets() {
		wp_enqueue_style( 'loftocean-elementor-editor-style', LOFTOCEAN_ASSETS_URI . 'styles/elementor.min.css', array(), LOFTOCEAN_ASSETS_VERSION );
		wp_enqueue_script( 'loftocean-elementor-editor-script', LOFTOCEAN_ASSETS_URI . 'scripts/admin/elementor.min.js', array( 'jquery' ), LOFTOCEAN_ASSETS_VERSION, true );
		do_action( 'loftocean_enqueue_font_awesome' );
		do_action( 'loftocean_elementor_editor_enqueue_assets' );
       	do_action( 'loftocean_load_flaticons' );
	}
	/**
	* Enqueue scripts for frontend
	*/
	public function enqueue_frontend_assets() {
		wp_enqueue_style( 'jquery-daterangepicker', LOFTOCEAN_ASSETS_URI . 'libs/daterangepicker/daterangepicker.min.css', array(), '3.1.1' );
		wp_enqueue_script( 'loftocean-parallax-bundle', LOFTOCEAN_ASSETS_URI . 'scripts/front/parallax-bundle.min.js', array( 'jquery' ), LOFTOCEAN_ASSETS_VERSION, true );
		wp_enqueue_script( 'moment', LOFTOCEAN_ASSETS_URI . 'libs/daterangepicker/moment.min.js', array(), '2.18.1', true );
		wp_enqueue_script( 'loftocean-base64', LOFTOCEAN_ASSETS_URI . 'libs/base64/base64.min.js', array(), LOFTOCEAN_ASSETS_VERSION, true );
		wp_enqueue_script( 'jquery-daterangepicker', LOFTOCEAN_ASSETS_URI . 'libs/daterangepicker/daterangepicker.min.js', array( 'jquery', 'moment' ), LOFTOCEAN_ASSETS_VERSION, true );

		wp_enqueue_script( 'loftocean-elementor-frontend', LOFTOCEAN_ASSETS_URI . 'scripts/front/elementor.min.js', array( 'jquery-daterangepicker', 'elementor-frontend', 'loftocean-parallax-bundle', 'loftocean-base64' ), LOFTOCEAN_ASSETS_VERSION, true );
		wp_localize_script( 'loftocean-elementor-frontend', 'loftoceanElementorFront', array(
			'countDown' => array(
				'days' => apply_filters( 'loftocean_elementor_days', esc_html__( 'Days', 'loftocean' ) ),
				'hours' => apply_filters( 'loftocean_elementor_hours', esc_html__( 'Hours', 'loftocean' ) ),
				'min' => apply_filters( 'loftocean_elementor_minutes', esc_html__( 'Minutes', 'loftocean' ) ),
				'sec' => apply_filters( 'loftocean_elementor_seconds', esc_html__( 'Seconds', 'loftocean' ) )
			),
			'reservation' => array(
				'room' => array( 'single' => esc_html__( 'Room', 'loftocean' ), 'plural' => esc_html__( 'Rooms', 'loftocean' ), 'usePluralIfZero' => apply_filters( 'loftocean_room_use_plural_if_rooms_number_is_zero', false ) ),
				'adult' => array( 'single' => esc_html__( 'Adult', 'loftocean' ), 'plural' => esc_html__( 'Adults', 'loftocean' ), 'usePluralIfZero' => apply_filters( 'loftocean_room_use_plural_if_adults_number_is_zero', false ) ),
				'child' => array( 'single' => esc_html__( 'Child', 'loftocean' ), 'plural' => esc_html__( 'Children', 'loftocean' ), 'usePluralIfZero' => apply_filters( 'loftocean_room_use_plural_if_children_number_is_zero', false ) )
			)
		) );
	}
	/**
	* Conditional function if a post is built with elementor
	*/
	public function is_built_with_elementor( $status, $pid ) {
		return ! empty( $pid ) && ( false !== get_post_status( $pid ) ) && \Elementor\Plugin::$instance->documents->get( $pid )->is_built_with_elementor();
	}
	/**
	* Parse post content built with elementor
	*/
	public function parse_content( $content, $pid ) {
		$post = new \Elementor\Core\Files\CSS\Post( $pid );
		$meta = $post->get_meta();

		ob_start();
		if ( $post::CSS_STATUS_FILE === $meta[ 'status' ] ) : ?>
			<link rel="stylesheet" id="elementor-post-<?php echo esc_attr( $pid ); ?>-css" href="<?php echo esc_url( $post->get_url() ); ?>" type="text/css" media="all"><?php
		else :
			echo '<style>' . $post->get_content() . '</style>';
			\Elementor\Plugin::$instance->frontend->print_fonts_links();
		endif;

		echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $pid, false );
		wp_deregister_style( 'elementor-post-' . $pid );
		wp_dequeue_style( 'elementor-post-' . $pid );
		return ob_get_clean();
	}
	/**
	* Exclude WP widgets from elementor widget list
	*/
	public function exclude_wp_widgets( $black_list = array() ) {
		$black_list = array_merge( $black_list, array(
			'LoftOcean\Instagram\Widget',
			'LoftOcean\Widget\Facebook',
			'LoftOcean\Widget\Posts',
			'LoftOcean\Widget\Opening_Hours',
			'LoftOcean\Widget\Category',
			'LoftOcean\Widget\Profile',
			'LoftOcean\Widget\Social'
		) );
		return $black_list;
	}
	/**
	* Enqueue elementor style
	*/
	public function enqueue_elementor_style() {
		\Elementor\Plugin::$instance->frontend->enqueue_styles();
	}
}
add_action( 'loftocean_load_core_modules', array( '\LoftOcean\Elementor_Extension', '_instance' ) );
