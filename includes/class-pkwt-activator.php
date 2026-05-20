<?php
/**
 * Activation class.
 *
 * @package PKWT
 */

namespace PKWT\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_Activator {

	/**
	 * Activate plugin.
	 *
	 * @return void
	 */
	public static function activate(): void {
		global $wp_version;

		if ( version_compare( PHP_VERSION, PKWT_MIN_PHP, '<' ) || version_compare( $wp_version, PKWT_MIN_WP, '<' ) ) {
			deactivate_plugins( plugin_basename( PKWT_PLUGIN_FILE ) );
			wp_die( esc_html__( 'PowerPlus — All-in-One Powerful Toolkit requires PHP 8.0+ and WordPress 6.0+.', 'powerplus-toolkit' ) );
		}

		$page_manager = new Class_PKWT_Page_Manager();
		$pages        = $page_manager->ensure_default_pages();
		if ( count( $pages ) < 3 ) {
			deactivate_plugins( plugin_basename( PKWT_PLUGIN_FILE ) );
			wp_die( esc_html__( 'Activation failed: required auth pages could not be created.', 'powerplus-toolkit' ) );
		}

		$repo     = new Class_PKWT_Settings_Repository();
		$settings = $repo->get();
		$defaults = self::defaults( $pages );
		$repo->update( wp_parse_args( $settings, $defaults ) );
		$repo->ensure_version();

		update_option( 'pkwt_onboarding_redirect', 1 );
		flush_rewrite_rules();
	}

	/**
	 * Defaults.
	 *
	 * @param array<string,int> $pages Page ids.
	 *
	 * @return array<string,mixed>
	 */
	private static function defaults( array $pages ): array {
		return array(
			'enabled'                 => 1,
			'login_page_id'           => isset( $pages['login'] ) ? (int) $pages['login'] : 0,
			'register_page_id'        => isset( $pages['register'] ) ? (int) $pages['register'] : 0,
			'lost_password_page_id'   => isset( $pages['lost_password'] ) ? (int) $pages['lost_password'] : 0,
			'reset_password_page_id'  => isset( $pages['reset_password'] ) ? (int) $pages['reset_password'] : 0,
			'after_login_redirect'    => home_url( '/' ),
			'after_login_redirect_page_id' => 0,
			'pkwt_custom_login_url'    => '',
			'block_default_wp_auth'   => 1,
			'filter_priority'         => PKWT_FILTER_PRIORITY,
			'max_attempts'            => 5,
			'lockout_minutes'         => 15,
			'enable_rate_limiting'    => 1,
			'captcha_provider'        => 'none',
			'recaptcha_site_key'      => '',
			'recaptcha_secret_key'    => '',
			'hcaptcha_site_key'       => '',
			'hcaptcha_secret_key'     => '',
			'woocommerce_mode'        => 0,
			'plugin_menu_name'        => __( 'PowerPlus — All-in-One Powerful Toolkit', 'powerplus-toolkit' ),
			'plugin_description'      => __( 'Elementor-based auth page builder.', 'powerplus-toolkit' ),
			'support_url'             => '',
			'hide_plugins_list'       => 0,
			'custom_admin_menu_icon'  => 'dashicons-lock',
			'role_redirects'          => array(),
		);
	}
}
