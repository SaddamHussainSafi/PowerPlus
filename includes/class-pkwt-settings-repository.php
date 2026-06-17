<?php
/**
 * Settings repository with in-request caching.
 *
 * @package PKWT
 */

namespace PKWT\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_Settings_Repository {

	/**
	 * Cached settings.
	 *
	 * @var array<string,mixed>|null
	 */
	private $settings;

	/**
	 * Get defaults.
	 *
	 * @return array<string,mixed>
	 */
	public function defaults(): array {
		return array(
			'enabled'                => 1,
			'login_page_id'          => 0,
			'register_page_id'       => 0,
			'lost_password_page_id'  => 0,
			'reset_password_page_id' => 0,
			'after_login_redirect'   => home_url( '/' ),
			'after_login_redirect_page_id' => 0,
			'pkwt_custom_login_url'   => '',
			'login_mode'             => 'legacy', // legacy | template | native
			'login_template_id'      => 0,
			'block_default_wp_auth'  => 1,
			'filter_priority'        => PKWT_FILTER_PRIORITY,
			'max_attempts'           => 5,
			'lockout_minutes'        => 15,
			'enable_rate_limiting'   => 1,
			'ip_allowlist'           => '',
			'auto_update_all_plugins'=> 0,
			'captcha_provider'       => 'none',
			'recaptcha_site_key'     => '',
			'recaptcha_secret_key'   => '',
			'hcaptcha_site_key'      => '',
			'hcaptcha_secret_key'    => '',
			'woocommerce_mode'       => 0,
			'plugin_menu_name'       => __( 'PowerPlus', 'powerplus-toolkit' ),
			'plugin_description'     => __( 'Power Packed Tools - Complete WordPress Management Suite', 'powerplus-toolkit' ),
			'support_url'            => 'https://inceptastudio.com/',
			'hide_plugins_list'      => 0,
			'custom_admin_menu_icon' => '',
			'role_redirects'         => array(),
			'security_dashboard_enabled' => 1,
			'settings_activity_log'  => 1,
			'admin_test_mode'        => 0,
			'access_roles'           => array( 'administrator' ),
		);
	}

	/**
	 * Get settings.
	 *
	 * @return array<string,mixed>
	 */
	public function get(): array {
		if ( null !== $this->settings ) {
			return $this->settings;
		}

		$cached = wp_cache_get( 'settings', 'pkwt_options' );
		if ( is_array( $cached ) ) {
			$this->settings = $cached;
			return $this->settings;
		}

		$raw            = get_option( 'pkwt_settings', array() );
		$this->settings = wp_parse_args( is_array( $raw ) ? $raw : array(), $this->defaults() );
		wp_cache_set( 'settings', $this->settings, 'pkwt_options' );

		return $this->settings;
	}

	/**
	 * Update settings.
	 *
	 * @param array<string,mixed> $settings Settings.
	 *
	 * @return bool
	 */
	public function update( array $settings ): bool {
		$this->settings = wp_parse_args( $settings, $this->defaults() );
		wp_cache_set( 'settings', $this->settings, 'pkwt_options' );
		return (bool) update_option( 'pkwt_settings', $this->settings );
	}

	/**
	 * Ensure settings version.
	 *
	 * @return void
	 */
	public function ensure_version(): void {
		$version = (string) get_option( 'pkwt_settings_version', '' );
		if ( $version !== PKWT_SETTINGS_VERSION ) {
			update_option( 'pkwt_settings_version', PKWT_SETTINGS_VERSION );
		}
	}
}
