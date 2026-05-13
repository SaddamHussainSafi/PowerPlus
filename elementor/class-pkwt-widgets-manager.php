<?php
/**
 * Elementor widgets manager.
 *
 * @package PKWT
 */

namespace PKWT\Elementor;

use PKWT\Includes\Class_PKWT_Page_Manager;
use PKWT\Includes\Class_PKWT_Settings_Repository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_Widgets_Manager {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		// If Elementor is already loaded (loaded before us), boot immediately.
		// Otherwise, defer to elementor/loaded to guarantee correct order.
		if ( did_action( 'elementor/loaded' ) ) {
			$this->boot_elementor();
		} else {
			add_action( 'elementor/loaded', array( $this, 'boot_elementor' ) );
			// Show admin notice only if Elementor never loads at all (i.e., not installed/active).
			add_action( 'admin_notices', array( $this, 'maybe_elementor_missing_notice' ) );
		}
	}

	/**
	 * Show "Elementor missing" notice only when Elementor truly never loaded.
	 *
	 * @return void
	 */
	public function maybe_elementor_missing_notice(): void {
		// By the time admin_notices fires, Elementor would have loaded if it were active.
		if ( did_action( 'elementor/loaded' ) ) {
			return;
		}
		$this->elementor_missing_notice();
	}

	/**
	 * Boot Elementor integration (called after elementor/loaded fires).
	 *
	 * @return void
	 */
	public function boot_elementor(): void {
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		// Also enqueue inside the Elementor editor preview iframe.
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_assets' ) );
		add_filter( 'script_loader_tag', array( $this, 'defer_frontend_script' ), 10, 3 );
		// Enqueue template picker JS/CSS in the Elementor editor panel itself.
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue_editor_assets' ) );
	}

	/**
	 * Elementor missing notice.
	 *
	 * @return void
	 */
	public function elementor_missing_notice(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__( 'PowerKit widgets require Elementor to be active.', 'powerkit-powerful-tools-for-your-website' ) . '</p></div>';
	}

	/**
	 * Enqueue assets needed inside the Elementor editor panel (not the preview iframe).
	 * This loads the template picker interaction JS and its CSS.
	 *
	 * @return void
	 */
	public function enqueue_editor_assets(): void {
		$js_file  = PKWT_PLUGIN_DIR . 'assets/js/pkwt-editor-tpl.js';
		$css_file = PKWT_PLUGIN_DIR . 'assets/css/pkwt-editor-tpl.css';
		$js_ver   = file_exists( $js_file ) ? (string) filemtime( $js_file ) : PKWT_VERSION;
		$css_ver  = file_exists( $css_file ) ? (string) filemtime( $css_file ) : PKWT_VERSION;

		if ( file_exists( $css_file ) ) {
			wp_enqueue_style( 'pkwt-editor-tpl', PKWT_PLUGIN_URL . 'assets/css/pkwt-editor-tpl.css', array(), $css_ver );
		}

		// Output the config as an inline script first — this is guaranteed to
		// reach the page even if the external JS file fails to load.
		// The nonce is generated fresh on every editor page load.
		$config = array(
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'pkwt_ajax_import_template' ),
			'importing'  => __( 'Importing\u2026', 'powerkit-powerful-tools-for-your-website' ),
			'success'    => __( 'Imported!', 'powerkit-powerful-tools-for-your-website' ),
			'error'      => __( 'Import failed.', 'powerkit-powerful-tools-for-your-website' ),
			'openEditor' => __( 'Open in Elementor', 'powerkit-powerful-tools-for-your-website' ),
			'viewPage'   => __( 'View Page', 'powerkit-powerful-tools-for-your-website' ),
		);
		wp_add_inline_script(
			'elementor-editor', // Always present in the Elementor editor.
			'window.PKWTEditorTpl = ' . wp_json_encode( $config ) . ';',
			'before'
		);

		if ( file_exists( $js_file ) ) {
			wp_enqueue_script( 'pkwt-editor-tpl', PKWT_PLUGIN_URL . 'assets/js/pkwt-editor-tpl.js', array( 'elementor-editor' ), $js_ver, true );
		}
	}

	/**
	 * Enqueue frontend assets.
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		if ( ! $this->should_load_frontend_assets() ) {
			return;
		}

		$css_file = PKWT_PLUGIN_DIR . 'assets/css/pkwt-frontend.css';
		$js_file  = PKWT_PLUGIN_DIR . 'assets/js/pkwt-frontend.js';
		$css_ver  = file_exists( $css_file ) ? (string) filemtime( $css_file ) : PKWT_VERSION;
		$js_ver   = file_exists( $js_file ) ? (string) filemtime( $js_file ) : PKWT_VERSION;

		wp_enqueue_style( 'pkwt-frontend', PKWT_PLUGIN_URL . 'assets/css/pkwt-frontend.css', array(), $css_ver );
		wp_enqueue_script( 'pkwt-frontend', PKWT_PLUGIN_URL . 'assets/js/pkwt-frontend.js', array(), $js_ver, true );
		wp_localize_script(
			'pkwt-frontend',
			'PKWTFrontend',
			array(
				'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
				'pleaseWait'    => __( 'Please wait...', 'powerkit-powerful-tools-for-your-website' ),
				'connectionError' => __( 'Connection error. Please try again.', 'powerkit-powerful-tools-for-your-website' ),
				'slowConnection' => __( 'Connection is slow. Please try again.', 'powerkit-powerful-tools-for-your-website' ),
				'offlineError'   => __( 'You appear to be offline. Please check your connection.', 'powerkit-powerful-tools-for-your-website' ),
			)
		);
	}

	/**
	 * Register category.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Manager.
	 *
	 * @return void
	 */
	public function register_category( $elements_manager ): void {
		$elements_manager->add_category(
			'powerkit-powerful-tools-for-your-website',
			array(
				'title' => __( 'Login Pages', 'powerkit-powerful-tools-for-your-website' ),
				'icon'  => 'eicon-lock-user',
			)
		);
	}

	/**
	 * Register widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager.
	 *
	 * @return void
	 */
	public function register_widgets( $widgets_manager ): void {
		$widget_classes = array(
			'PKWT\\Elementor\\Widgets\\Class_Widget_Login_Form',
			'PKWT\\Elementor\\Widgets\\Class_Widget_Register_Form',
			'PKWT\\Elementor\\Widgets\\Class_Widget_Lost_Password',
			'PKWT\\Elementor\\Widgets\\Class_Widget_Reset_Password',
			'PKWT\\Elementor\\Widgets\\Class_Widget_Auth_Logo',
			'PKWT\\Elementor\\Widgets\\Class_Widget_Auth_Message',
			'PKWT\\Elementor\\Widgets\\Class_Widget_Social_Login',
			'PKWT\\Elementor\\Widgets\\Class_Widget_Auth_Tabs',
			'PKWT\\Elementor\\Widgets\\Class_Widget_Captcha',
			'PKWT\\Elementor\\Widgets\\Class_Widget_Divider_Text',
			'PKWT\\Elementor\\Widgets\\Class_Widget_Terms_Privacy',
			'PKWT\\Elementor\\Widgets\\Class_Widget_Redirect_Timer',
		);

		foreach ( $widget_classes as $widget_class ) {
			if ( class_exists( $widget_class ) ) {
				$widgets_manager->register( new $widget_class() );
			}
		}
	}

	/**
	 * Defer frontend script.
	 *
	 * @param string $tag    Script tag.
	 * @param string $handle Handle.
	 * @param string $src    Src.
	 *
	 * @return string
	 */
	public function defer_frontend_script( string $tag, string $handle, string $src ): string {
		if ( 'pkwt-frontend' !== $handle ) {
			return $tag;
		}

		if ( false !== strpos( $tag, ' defer' ) ) {
			return $tag;
		}

		return str_replace( '<script ', '<script defer ', $tag );
	}

	/**
	 * Load assets on any page that may contain our widgets.
	 *
	 * @return bool
	 */
	private function should_load_frontend_assets(): bool {
		// Always load in Elementor preview/editor mode (runs in iframe, not is_admin()).
		if ( class_exists( '\Elementor\Plugin' ) ) {
			$plugin = \Elementor\Plugin::$instance;
			if ( isset( $plugin->preview ) && $plugin->preview->is_preview_mode() ) {
				return true;
			}
			if ( isset( $plugin->editor ) && $plugin->editor->is_edit_mode() ) {
				return true;
			}
		}

		if ( is_admin() ) {
			return false;
		}

		// Load on any singular page/post that has our widgets in Elementor data.
		if ( is_singular() ) {
			$post_id = get_the_ID();
			if ( $post_id ) {
				$elementor_data = get_post_meta( $post_id, '_elementor_data', true );
				if ( ! empty( $elementor_data ) && false !== strpos( (string) $elementor_data, 'pkwt-' ) ) {
					return true;
				}
			}
		}

		// Also load on the registered auth pages (for non-Elementor content).
		$page_manager = new Class_PKWT_Page_Manager();
		if ( $page_manager->is_current_auth_page() ) {
			return true;
		}

		return false;
	}
}
