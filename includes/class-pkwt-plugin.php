<?php
/**
 * Main plugin loader.
 *
 * @package PKWT
 */

namespace PKWT\Includes;

use PKWT\Admin\Class_PKWT_Admin;
use PKWT\Admin\Class_PKWT_Settings;
use PKWT\Elementor\Class_PKWT_Widgets_Manager;
use PKWT\Elementor\Class_PKWT_Template_Library;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Class_PKWT_Plugin|null
	 */
	private static $instance;

	/**
	 * Is booted.
	 *
	 * @var bool
	 */
	private $booted = false;

	/**
	 * Get singleton.
	 *
	 * @return Class_PKWT_Plugin
	 */
	public static function instance(): Class_PKWT_Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Boot plugin.
	 *
	 * @return void
	 */
	public function boot(): void {
		if ( $this->booted ) {
			return;
		}

		$this->booted = true;

		add_action( 'admin_notices', array( $this, 'render_requirements_notice' ) );
		add_action( 'admin_init', array( $this, 'handle_onboarding_redirect' ) );
		add_filter( 'all_plugins', array( $this, 'maybe_hide_from_plugins_page' ) );

		if ( ! $this->requirements_met() ) {
			return;
		}

		$settings = new Class_PKWT_Settings();
		$settings->register();
		( new Class_PKWT_Settings_Repository() )->ensure_version();

		$admin = new Class_PKWT_Admin();
		$admin->register();

		$page_manager = new Class_PKWT_Page_Manager();
		$page_manager->register();

		$redirector = new Class_PKWT_Redirector();
		$redirector->register();

		$security = new Class_PKWT_Security();
		$security->register();

		$ajax = new Class_PKWT_AJAX_Handler( $security );
		$ajax->register();

		$compat = new Class_PKWT_Compatibility();
		$compat->register();

		$conflicts = new Class_PKWT_Conflict_Detector();
		$conflicts->register();

		$onboarding = new Class_PKWT_Onboarding();
		$onboarding->register();

		$elementor = new Class_PKWT_Widgets_Manager();
		$elementor->register();

		$tpl_library = new Class_PKWT_Template_Library();
		$tpl_library->register();

		$dpp = new Class_PKWT_DPP_Hooks();
		$dpp->register();
	}

	/**
	 * Requirements check.
	 *
	 * @return bool
	 */
	public function requirements_met(): bool {
		global $wp_version;

		if ( version_compare( PHP_VERSION, PKWT_MIN_PHP, '<' ) ) {
			return false;
		}

		if ( version_compare( $wp_version, PKWT_MIN_WP, '<' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Admin requirements notice.
	 *
	 * @return void
	 */
	public function render_requirements_notice(): void {
		if ( $this->requirements_met() ) {
			return;
		}

		echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'PowerKit - Powerful Tools For Your Website requires newer PHP/WordPress versions.', 'powerkit-powerful-tools-for-your-website' ) . '</p></div>';
	}

	/**
	 * Handle first activation redirect.
	 *
	 * @return void
	 */
	public function handle_onboarding_redirect(): void {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! get_option( 'pkwt_onboarding_redirect', false ) ) {
			return;
		}

		if ( wp_doing_ajax() ) {
			return;
		}

		delete_option( 'pkwt_onboarding_redirect' );
		wp_safe_redirect( admin_url( 'admin.php?page=pkwt-onboarding' ) );
		exit;
	}

	/**
	 * Hide plugin row if enabled.
	 *
	 * @param array<string,array<string,mixed>> $plugins Plugins list.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public function maybe_hide_from_plugins_page( array $plugins ): array {
		$settings = get_option( 'pkwt_settings', array() );
		if ( empty( $settings['hide_plugins_list'] ) ) {
			return $plugins;
		}

		$basename = plugin_basename( PKWT_PLUGIN_FILE );
		if ( isset( $plugins[ $basename ] ) && ! current_user_can( 'activate_plugins' ) ) {
			unset( $plugins[ $basename ] );
		}

		return $plugins;
	}
}
