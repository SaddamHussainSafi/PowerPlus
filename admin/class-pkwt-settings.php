<?php
/**
 * Settings API.
 *
 * @package PKWT
 */

namespace PKWT\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_Settings {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_filter( 'option_page_capability_pkwt_settings_group', array( $this, 'settings_group_capability' ) );
		add_action( 'admin_post_pkwt_export_settings', array( $this, 'export_settings' ) );
		add_action( 'admin_post_pkwt_import_settings', array( $this, 'import_settings' ) );
		add_action( 'admin_post_pkwt_reset_settings', array( $this, 'reset_settings' ) );
	}

	/**
	 * Dynamic capability for PKWT settings group.
	 *
	 * @param string $default_cap Default capability.
	 *
	 * @return string
	 */
	public function settings_group_capability( string $default_cap ): string {
		$settings = get_option( 'pkwt_settings', array() );
		if ( ! empty( $settings['admin_test_mode'] ) ) {
			return 'manage_options';
		}
		if ( current_user_can( 'manage_options' ) ) {
			return 'manage_options';
		}
		$allowed = isset( $settings['access_roles'] ) && is_array( $settings['access_roles'] ) ? $settings['access_roles'] : array( 'administrator' );
		$user    = wp_get_current_user();
		if ( $user && ! empty( $user->roles ) ) {
			foreach ( $user->roles as $role ) {
				if ( in_array( $role, $allowed, true ) ) {
					return 'read';
				}
			}
		}
		return $default_cap;
	}

	/**
	 * Register option group.
	 *
	 * @return void
	 */
	public function register_settings(): void {
		register_setting( 'pkwt_settings_group', 'pkwt_settings', array( $this, 'sanitize_settings' ) );
	}

	/**
	 * Sanitize settings.
	 *
	 * @param array<string,mixed> $settings Settings.
	 *
	 * @return array<string,mixed>
	 */
	public function sanitize_settings( $settings ): array {
		$settings = is_array( $settings ) ? $settings : array();
		$current = get_option( 'pkwt_settings', array() );
		$output  = wp_parse_args( $settings, $current );

		$output['enabled']               = isset( $settings['enabled'] ) ? ( empty( $settings['enabled'] ) ? 0 : 1 ) : ( isset( $current['enabled'] ) ? absint( $current['enabled'] ) : 1 );
		$output['woocommerce_mode']      = isset( $settings['woocommerce_mode'] ) ? ( empty( $settings['woocommerce_mode'] ) ? 0 : 1 ) : ( isset( $current['woocommerce_mode'] ) ? absint( $current['woocommerce_mode'] ) : 0 );
		$output['enable_rate_limiting']  = isset( $settings['enable_rate_limiting'] ) ? ( empty( $settings['enable_rate_limiting'] ) ? 0 : 1 ) : ( isset( $current['enable_rate_limiting'] ) ? absint( $current['enable_rate_limiting'] ) : 1 );
		$output['hide_plugins_list']     = isset( $settings['hide_plugins_list'] ) ? ( empty( $settings['hide_plugins_list'] ) ? 0 : 1 ) : ( isset( $current['hide_plugins_list'] ) ? absint( $current['hide_plugins_list'] ) : 0 );
		$output['block_default_wp_auth'] = isset( $settings['block_default_wp_auth'] ) ? ( empty( $settings['block_default_wp_auth'] ) ? 0 : 1 ) : ( isset( $current['block_default_wp_auth'] ) ? absint( $current['block_default_wp_auth'] ) : 1 );
		$output['security_dashboard_enabled'] = isset( $settings['security_dashboard_enabled'] ) ? ( empty( $settings['security_dashboard_enabled'] ) ? 0 : 1 ) : ( isset( $current['security_dashboard_enabled'] ) ? absint( $current['security_dashboard_enabled'] ) : 1 );
		$output['settings_activity_log'] = isset( $settings['settings_activity_log'] ) ? ( empty( $settings['settings_activity_log'] ) ? 0 : 1 ) : ( isset( $current['settings_activity_log'] ) ? absint( $current['settings_activity_log'] ) : 1 );
		$output['admin_test_mode']       = isset( $settings['admin_test_mode'] ) ? ( empty( $settings['admin_test_mode'] ) ? 0 : 1 ) : ( isset( $current['admin_test_mode'] ) ? absint( $current['admin_test_mode'] ) : 0 );
		$output['login_page_id']         = isset( $settings['login_page_id'] ) ? absint( $settings['login_page_id'] ) : ( isset( $current['login_page_id'] ) ? absint( $current['login_page_id'] ) : 0 );
		$output['register_page_id']      = isset( $settings['register_page_id'] ) ? absint( $settings['register_page_id'] ) : ( isset( $current['register_page_id'] ) ? absint( $current['register_page_id'] ) : 0 );
		$output['lost_password_page_id'] = isset( $settings['lost_password_page_id'] ) ? absint( $settings['lost_password_page_id'] ) : ( isset( $current['lost_password_page_id'] ) ? absint( $current['lost_password_page_id'] ) : 0 );
		$output['reset_password_page_id']= isset( $settings['reset_password_page_id'] ) ? absint( $settings['reset_password_page_id'] ) : ( isset( $current['reset_password_page_id'] ) ? absint( $current['reset_password_page_id'] ) : 0 );

		$output['after_login_redirect'] = isset( $settings['after_login_redirect'] ) ? esc_url_raw( wp_unslash( $settings['after_login_redirect'] ) ) : ( isset( $current['after_login_redirect'] ) ? esc_url_raw( (string) $current['after_login_redirect'] ) : '' );
		$output['after_login_redirect_page_id'] = isset( $settings['after_login_redirect_page_id'] ) ? absint( $settings['after_login_redirect_page_id'] ) : ( isset( $current['after_login_redirect_page_id'] ) ? absint( $current['after_login_redirect_page_id'] ) : 0 );
		$output['pkwt_custom_login_url'] = isset( $settings['pkwt_custom_login_url'] ) ? $this->normalize_custom_login_url( (string) wp_unslash( $settings['pkwt_custom_login_url'] ) ) : ( isset( $current['pkwt_custom_login_url'] ) ? esc_url_raw( (string) $current['pkwt_custom_login_url'] ) : '' );

		// When the custom login URL changes, rename the login page slug to match so
		// the page is accessible directly at that URL — no redirect needed.
		$new_custom_url = (string) $output['pkwt_custom_login_url'];
		$old_custom_url = isset( $current['pkwt_custom_login_url'] ) ? (string) $current['pkwt_custom_login_url'] : '';
		if ( '' !== $new_custom_url && $new_custom_url !== $old_custom_url ) {
			$login_page_id = absint( $output['login_page_id'] );
			if ( $login_page_id > 0 ) {
				$new_slug = sanitize_title( trim( (string) wp_parse_url( $new_custom_url, PHP_URL_PATH ), '/' ) );
				if ( '' !== $new_slug ) {
					wp_update_post( array(
						'ID'        => $login_page_id,
						'post_name' => $new_slug,
					) );
					// Flush rewrite rules so the new slug is immediately routable.
					flush_rewrite_rules( false );
				}
			}
		}

		$output['filter_priority']      = isset( $settings['filter_priority'] ) ? max( 1, min( 99, absint( $settings['filter_priority'] ) ) ) : ( isset( $current['filter_priority'] ) ? max( 1, min( 99, absint( $current['filter_priority'] ) ) ) : 20 );
		$output['max_attempts']         = isset( $settings['max_attempts'] ) ? max( 1, min( 20, absint( $settings['max_attempts'] ) ) ) : ( isset( $current['max_attempts'] ) ? max( 1, min( 20, absint( $current['max_attempts'] ) ) ) : 5 );
		$output['lockout_minutes']      = isset( $settings['lockout_minutes'] ) ? max( 5, min( 1440, absint( $settings['lockout_minutes'] ) ) ) : ( isset( $current['lockout_minutes'] ) ? max( 5, min( 1440, absint( $current['lockout_minutes'] ) ) ) : 15 );
		$output['captcha_provider']     = isset( $settings['captcha_provider'] ) ? sanitize_key( $settings['captcha_provider'] ) : ( isset( $current['captcha_provider'] ) ? sanitize_key( (string) $current['captcha_provider'] ) : 'none' );
		foreach ( array( 'recaptcha_site_key', 'recaptcha_secret_key', 'hcaptcha_site_key', 'hcaptcha_secret_key' ) as $key ) {
			$output[ $key ] = isset( $settings[ $key ] ) ? sanitize_text_field( wp_unslash( $settings[ $key ] ) ) : ( isset( $current[ $key ] ) ? sanitize_text_field( (string) $current[ $key ] ) : '' );
		}

		$output['plugin_menu_name']       = isset( $settings['plugin_menu_name'] ) ? sanitize_text_field( wp_unslash( $settings['plugin_menu_name'] ) ) : ( isset( $current['plugin_menu_name'] ) ? sanitize_text_field( (string) $current['plugin_menu_name'] ) : '' );
		$output['plugin_description']     = isset( $settings['plugin_description'] ) ? sanitize_textarea_field( wp_unslash( $settings['plugin_description'] ) ) : ( isset( $current['plugin_description'] ) ? sanitize_textarea_field( (string) $current['plugin_description'] ) : '' );
		$output['support_url']            = isset( $settings['support_url'] ) ? esc_url_raw( wp_unslash( $settings['support_url'] ) ) : ( isset( $current['support_url'] ) ? esc_url_raw( (string) $current['support_url'] ) : '' );
		$output['custom_admin_menu_icon'] = isset( $settings['custom_admin_menu_icon'] ) ? sanitize_text_field( wp_unslash( $settings['custom_admin_menu_icon'] ) ) : ( isset( $current['custom_admin_menu_icon'] ) ? sanitize_text_field( (string) $current['custom_admin_menu_icon'] ) : 'dashicons-lock' );

		$role_redirects = array();
		if ( isset( $settings['role_redirects'] ) && is_array( $settings['role_redirects'] ) ) {
			foreach ( $settings['role_redirects'] as $role => $url ) {
				$role_redirects[ sanitize_key( $role ) ] = esc_url_raw( wp_unslash( $url ) );
			}
		}
		$output['role_redirects'] = ! empty( $role_redirects ) ? $role_redirects : ( isset( $current['role_redirects'] ) && is_array( $current['role_redirects'] ) ? $current['role_redirects'] : array() );

		$access_roles = array( 'administrator' );
		if ( isset( $settings['access_roles'] ) && is_array( $settings['access_roles'] ) ) {
			$access_roles = array();
			foreach ( $settings['access_roles'] as $role ) {
				$role = sanitize_key( (string) $role );
				if ( '' !== $role ) {
					$access_roles[] = $role;
				}
			}
			$access_roles = array_values( array_unique( $access_roles ) );
			if ( empty( $access_roles ) ) {
				$access_roles = array( 'administrator' );
			}
		} elseif ( isset( $current['access_roles'] ) && is_array( $current['access_roles'] ) && ! empty( $current['access_roles'] ) ) {
			$access_roles = array_values( array_unique( array_map( 'sanitize_key', $current['access_roles'] ) ) );
		}
		$output['access_roles'] = $access_roles;

		// Ensure legacy notification/digest keys are fully removed from saved settings.
		foreach ( array(
			'pkwt_notify_url_change',
			'pkwt_notify_lockout',
			'pkwt_notify_on_login',
			'pkwt_notify_skip_own',
			'pkwt_digest_enabled',
			'pkwt_notify_lockdown_email',
			'pkwt_digest_frequency',
			'pkwt_digest_time',
			'pkwt_notify_url_recipients',
			'pkwt_notify_url_subject',
			'pkwt_notify_login_recipients',
			'pkwt_digest_recipients',
			'pkwt_notify_login_roles',
			'pkwt_emergency_lockdown',
		) as $legacy_key ) {
			unset( $output[ $legacy_key ] );
		}

		return $output;
	}

	/**
	 * Normalize custom login URL value.
	 * Accepts full URL, /path, or plain slug.
	 *
	 * @param string $raw Raw setting value.
	 *
	 * @return string
	 */
	private function normalize_custom_login_url( string $raw ): string {
		$raw = trim( $raw );
		if ( '' === $raw ) {
			return '';
		}

		$reserved = array( 'wp-login', 'wp-login.php' );

		if ( preg_match( '#^https?://#i', $raw ) ) {
			$path = trim( (string) wp_parse_url( $raw, PHP_URL_PATH ), '/' );
			if ( in_array( $path, $reserved, true ) ) {
				return '';
			}
			return esc_url_raw( $raw );
		}

		if ( '/' === substr( $raw, 0, 1 ) ) {
			$path = trim( ltrim( $raw, '/' ), '/' );
			if ( in_array( $path, $reserved, true ) ) {
				return '';
			}
			return esc_url_raw( home_url( trailingslashit( $path ) ) );
		}

		$slug = sanitize_title( $raw );
		if ( '' === $slug ) {
			return '';
		}
		if ( in_array( $slug, $reserved, true ) ) {
			return '';
		}

		return esc_url_raw( home_url( '/' . $slug . '/' ) );
	}

	/**
	 * Export settings.
	 *
	 * @return void
	 */
	public function export_settings(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'powerkit-powerful-tools-for-your-website' ) );
		}

		check_admin_referer( 'pkwt_export_settings' );

		$data = array(
			'generated_at' => gmdate( 'c' ),
			'settings'     => get_option( 'pkwt_settings', array() ),
		);

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=pkwt-settings-' . gmdate( 'Ymd-His' ) . '.json' );
		echo wp_json_encode( $data );
		exit;
	}

	/**
	 * Import settings.
	 *
	 * @return void
	 */
	public function import_settings(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'powerkit-powerful-tools-for-your-website' ) );
		}

		check_admin_referer( 'pkwt_import_settings' );

		if ( empty( $_FILES['pkwt_import_file']['tmp_name'] ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=pkwt-settings&tab=import-export&pkwt_notice=import_failed' ) );
			exit;
		}

		$tmp_name = isset( $_FILES['pkwt_import_file']['tmp_name'] ) ? sanitize_text_field( wp_unslash( (string) $_FILES['pkwt_import_file']['tmp_name'] ) ) : '';
		if ( '' === $tmp_name ) {
			wp_safe_redirect( admin_url( 'admin.php?page=pkwt-settings&tab=import-export&pkwt_notice=import_failed' ) );
			exit;
		}

		$raw = file_get_contents( $tmp_name );
		if ( false === $raw ) {
			wp_safe_redirect( admin_url( 'admin.php?page=pkwt-settings&tab=import-export&pkwt_notice=import_failed' ) );
			exit;
		}

		$decoded = json_decode( $raw, true );
		if ( ! is_array( $decoded ) || empty( $decoded['settings'] ) || ! is_array( $decoded['settings'] ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=pkwt-settings&tab=import-export&pkwt_notice=import_failed' ) );
			exit;
		}

		$settings = $this->sanitize_settings( $decoded['settings'] );
		update_option( 'pkwt_settings', $settings );

		$page_manager = new \PKWT\Includes\Class_PKWT_Page_Manager();
		$page_manager->ensure_default_pages();

		wp_safe_redirect( admin_url( 'admin.php?page=pkwt-settings&tab=import-export&pkwt_notice=import_ok' ) );
		exit;
	}

	/**
	 * Reset settings.
	 *
	 * @return void
	 */
	public function reset_settings(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'powerkit-powerful-tools-for-your-website' ) );
		}

		check_admin_referer( 'pkwt_reset_settings' );
		delete_option( 'pkwt_settings' );

		\PKWT\Includes\Class_PKWT_Activator::activate();
		wp_safe_redirect( admin_url( 'admin.php?page=pkwt-settings&tab=general&pkwt_notice=reset_ok' ) );
		exit;
	}
}
