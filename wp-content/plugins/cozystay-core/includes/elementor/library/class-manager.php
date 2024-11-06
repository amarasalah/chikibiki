<?php
namespace LoftOcean\Elementor\Library;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
class Manager {
	/**
	 * Object constructor. Init basic things.
	 */
	public function __construct() {
		$this->hooks();
		$this->register_templates_source();
	}
	/**
	 * Initialize Hooks
	 */
	public function hooks() {
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'editor_scripts' ) );
		add_action( 'elementor/editor/footer', array( $this, 'html_templates' ) );
	}

	/**
	 * Register source.
	 *
	 * @since 1.0.0
	 */
	public function register_templates_source() {
        require_once LOFTOCEAN_DIR . 'includes/elementor/library/class-library-source.php';
		Plugin::instance()->templates_manager->register_source( '\LoftOcean\Elementor\Library\Source' );
	}

	/**
	 * Load Editor JS
	 *
	 * @since 1.0.0
	 */
	public function editor_scripts() {
		wp_enqueue_script( 'loftocean-elementor-library', LOFTOCEAN_ASSETS_URI . 'scripts/admin/elementor-library.min.js', array( 'jquery' ), LOFTOCEAN_ASSETS_VERSION, true );
		wp_localize_script( 'loftocean-elementor-library', 'loftoceanElementorLibrary', array(
			'demoAjaxUrl' => 'https://cozystay.loftocean.com/wp-json/cozystay-library/v1/get-list'
		) );
	}
	/**
	 * Templates Modal Markup
	 *
	 * @since 1.0.0
	 */
	public function html_templates() { ?>
		<script type="text/html" id="tmpl-elementor-loftocean-library-modal-header">
			<div class="elementor-templates-modal__header">
				<div class="elementor-templates-modal__header__logo-area">
					<div class="elementor-templates-modal__header__logo">
						<span class="elementor-templates-modal__header__logo__title"><?php esc_html_e( 'CozyStay Library', 'loftocean' ); ?></span>
					</div>
				</div>
				<div class="elementor-templates-modal__header__menu-area">
					<div id="elementor-template-library-header-menu" class="tab-title-container"></div>
				</div>
				<div class="elementor-templates-modal__header__items-area">
					<div class="elementor-templates-modal__header__close elementor-templates-modal__header__close--normal elementor-templates-modal__header__item">
						<i class="eicon-close" aria-hidden="true" title="<?php echo esc_attr__( 'Close', 'loftocean' ); ?>"></i>
						<span class="elementor-screen-only"><?php echo esc_html__( 'Close', 'loftocean' ); ?></span>
					</div>
				</div>
			</div>
		</script>

		<script type="text/html" id="tmpl-elementor-loftocean-library-tab-title-item"><#
			_.each( data, function( item, i ) { #>
				<div class="elementor-component-tab elementor-template-library-menu-item" data-tab="elementor-template-library-{{ item.slug }}">{{{ item.name }}}</div><#
			} ); #>
		</script>

		<script type="text/html" id="tmpl-elementor-loftocean-library-modal-order">
			<div class="elementor-template-library-filter">
				<select class="elementor-template-library-filter-select" data-elementor-filter="subtype">
					<option value="all"><?php echo esc_html__( 'All', 'loftocean' ); ?></option><#
                    _.each( data.tags, function( item, i ) { #>
                        <option value="{{{ item.slug }}}">{{{ item.title }}}</option><#
                    } ); #>
				</select>
			</div>
		</script>

		<script type="text/html" id="tmpl-elementor-loftocean-library-modal">
			<div id="elementor-template-library-templates" data-template-source="remote"></div>
			<div class="elementor-loader-wrapper" style="display: none">
				<div class="elementor-loader">
					<div class="elementor-loader-boxes">
						<div class="elementor-loader-box"></div>
						<div class="elementor-loader-box"></div>
						<div class="elementor-loader-box"></div>
						<div class="elementor-loader-box"></div>
					</div>
				</div>
				<div class="elementor-loading-title"><?php echo esc_html__( 'Loading', 'loftocean' ); ?></div>
			</div>
		</script>

		<script type="text/html" id="tmpl-elementor-loftocean-library-tab-content"><#
            _.each( data, function( item, i ) { #>
				<div class="elementor-template-library-tab-content elementor-template-library-{{ item.slug }}">
					<div class="elementor-template-library-toolbar">
						<div class="elementor-template-library-filter-toolbar-remote elementor-template-library-filter-toolbar"></div>
						<div class="elementor-template-library-filter-text-wrapper">
							<label class="elementor-screen-only"><?php echo esc_html__( 'Search Templates:', 'loftocean' ); ?></label>
							<input placeholder="<?php echo esc_attr__( 'Search', 'loftocean' ); ?>">
							<i class="eicon-search"></i>
						</div>
					</div>
					<div class="elementor-template-library-templates-container"></div>
				</div><#
			} ); #>
		</script>

		<script type="text/html" id="tmpl-elementor-loftocean-library-modal-item"><#
            _.each( data.elements, function( item, i ) { #>
			<div class="elementor-template-library-template elementor-template-library-template-remote elementor-template-library-template-block" data-title="{{{ item.image }}}" data-slug="{{{ item.slug }}}" data-tag="{{{ item.class }}}">
				<div class="elementor-template-library-template-body">
					<img src="{{{ item.image }}}" alt="{{{ item.title }}}" />

					<a class="elementor-template-library-template-preview" href="{{{ item.link }}}" target="_blank">
						<i class="eicon-zoom-in-bold" aria-hidden="true"></i>
					</a>
				</div>

				<div class="elementor-template-library-template-footer">
					<a class="elementor-template-library-template-action elementor-template-library-template-insert elementor-button" data-id="{{{ item.id }}}">
						<i class="eicon-file-download" aria-hidden="true"></i>
						<span class="elementor-button-title"><?php esc_html_e( 'Insert', 'loftocean' ); ?></span>
					</a>
					<div class="elementor-template-library-template-name">{{{ item.title }}}</div>
				</div>
			</div><#
            } ); #>
		</script>
		<?php
	}
}

new \LoftOcean\Elementor\Library\Manager();
