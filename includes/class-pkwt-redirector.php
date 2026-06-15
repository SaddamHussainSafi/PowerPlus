<?php
/**
 * Login URL redirector.
 *
 * @package PKWT
 */

namespace PKWT\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_Redirector {

	/**
	 * Page manager.
	 *
	 * @var Class_PKWT_Page_Manager
	 */
	private $pages;

	/**
	 * Settings repository.
	 *
	 * @var Class_PKWT_Settings_Repository
	 */
	private $settings_repo;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->pages         = new Class_PKWT_Page_Manager();
		$this->settings_repo = new Class_PKWT_Settings_Repository();
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		$settings = $this->settings_repo->get();
		$priority = isset( $settings['filter_priority'] ) ? absint( $settings['filter_priority'] ) : PKWT_FILTER_PRIORITY;
		$priority = max( 1, min( 99, $priority ) );

		add_filter( 'login_url', array( $this, 'filter_login_url' ), $priority, 3 );
		add_filter( 'register_url', array( $this, 'filter_register_url' ), $priority );
		add_filter( 'lostpassword_url', array( $this, 'filter_lost_password_url' ), $priority, 2 );
		add_filter( 'logout_redirect', array( $this, 'filter_logout_redirect' ), $priority, 3 );

		add_action( 'init', array( $this, 'maybe_handle_custom_login_route' ), 0 );
		add_action( 'init', array( $this, 'maybe_block_native_auth_endpoints' ), 1 );
		add_action( 'template_redirect', array( $this, 'maybe_redirect_legacy_login_page' ), 0 );
		add_action( 'template_redirect', array( $this, 'maybe_redirect_wp_login' ), 1 );
	}

	/**
	 * Is plugin enabled.
	 *
	 * @return bool
	 */
	private function is_enabled(): bool {
		$settings = $this->settings_repo->get();
		return ! empty( $settings['enabled'] );
	}

	/**
	 * Login URL filter.
	 *
	 * @param string $login_url URL.
	 * @param string $redirect Redirect.
	 * @param bool   $force_reauth Force.
	 *
	 * @return string
	 */
	public function filter_login_url( string $login_url, string $redirect, $force_reauth ): string {
		if ( ! $this->is_enabled() ) {
			return $login_url;
		}

		$custom = $this->get_login_target_url();
		if ( ! $this->is_valid_target( $custom, $login_url, true ) ) {
			return $login_url;
		}

		if ( ! empty( $redirect ) ) {
			$custom = add_query_arg( 'redirect_to', rawurlencode( $redirect ), $custom );
		}
		if ( $force_reauth ) {
			$custom = add_query_arg( 'reauth', '1', $custom );
		}

		return $custom;
	}

	/**
	 * Register URL filter.
	 *
	 * @param string $register_url URL.
	 *
	 * @return string
	 */
	public function filter_register_url( string $register_url ): string {
		if ( ! $this->is_enabled() ) {
			return $register_url;
		}

		$custom = $this->pages->get_page_url_by_setting( 'register_page_id' );
		return $this->is_valid_target( $custom, $register_url ) ? $custom : $register_url;
	}

	/**
	 * Lost password URL filter.
	 *
	 * @param string $lostpassword_url URL.
	 * @param string $redirect Redirect.
	 *
	 * @return string
	 */
	public function filter_lost_password_url( string $lostpassword_url, string $redirect ): string {
		if ( ! $this->is_enabled() ) {
			return $lostpassword_url;
		}

		$custom = $this->pages->get_page_url_by_setting( 'lost_password_page_id' );
		if ( ! $this->is_valid_target( $custom, $lostpassword_url ) ) {
			return $lostpassword_url;
		}

		if ( ! empty( $redirect ) ) {
			$custom = add_query_arg( 'redirect_to', rawurlencode( $redirect ), $custom );
		}

		return $custom;
	}

	/**
	 * Logout redirect.
	 *
	 * @param string  $redirect_to Redirect url.
	 * @param string  $requested_redirect_to Requested.
	 * @param \WP_User $user User.
	 *
	 * @return string
	 */
	public function filter_logout_redirect( string $redirect_to, string $requested_redirect_to, $user ): string {
		if ( ! $this->is_enabled() ) {
			return $redirect_to;
		}

		$login = $this->pages->get_page_url_by_setting( 'login_page_id' );
		return $login ? $login : $redirect_to;
	}

	/**
	 * Detect Elementor preview/editor context to prevent redirects inside the editor iframe.
	 *
	 * @return bool
	 */
	private function is_elementor_editing(): bool {
		// The Elementor preview iframe loads the page with ?elementor-preview=<id>.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only query arg check for editor detection.
		if ( isset( $_GET['elementor-preview'] ) ) {
			return true;
		}
		// Elementor editor admin page uses ?action=elementor.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only routing check.
		if ( isset( $_GET['action'] ) && 'elementor' === sanitize_key( wp_unslash( $_GET['action'] ) ) ) {
			return true;
		}
		// Elementor's own API check.
		if ( class_exists( '\Elementor\Plugin' ) ) {
			$plugin = \Elementor\Plugin::$instance;
			if ( isset( $plugin->preview ) && $plugin->preview->is_preview_mode() ) {
				return true;
			}
			if ( isset( $plugin->editor ) && $plugin->editor->is_edit_mode() ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Redirect direct wp-login.php access.
	 *
	 * @return void
	 */
	public function maybe_redirect_wp_login(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}

		if ( $this->is_elementor_editing() ) {
			return;
		}

		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		if ( false === strpos( $request_uri, 'wp-login.php' ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only routing parameter from wp-login requests.
		$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : 'login';
		$target = '';

		switch ( $action ) {
			case 'register':
				$target = $this->pages->get_page_url_by_setting( 'register_page_id' );
				break;
			case 'lostpassword':
			case 'rp':
			case 'resetpass':
				$target = $this->pages->get_page_url_by_setting( 'lost_password_page_id' );
				break;
			default:
				$target = $this->get_login_target_url();
		}

		$current_url = home_url( $request_uri );
		if ( ! $this->is_valid_target( $target, $current_url, true ) ) {
			return;
		}

		wp_safe_redirect( $target );
		exit;
	}

	/**
	 * Redirect the legacy login page slug to the custom login URL — but ONLY when
	 * the custom URL is a purely external/non-page route (e.g. a rewrite rule or a
	 * path that does not correspond to any WordPress page). When the custom URL IS a
	 * WordPress page or resolves to the same destination as the login page, skip the
	 * redirect entirely to avoid infinite loops.
	 *
	 * @return void
	 */
	public function maybe_redirect_legacy_login_page(): void {
		if ( ! $this->is_enabled() || is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}

		if ( $this->is_elementor_editing() ) {
			return;
		}

		$settings = $this->settings_repo->get();
		$custom   = isset( $settings['pkwt_custom_login_url'] ) ? esc_url_raw( (string) $settings['pkwt_custom_login_url'] ) : '';
		if ( '' === $custom ) {
			return;
		}

		// When a login page is configured, the custom URL redirects TO the login page
		// (handled by maybe_handle_custom_login_route). Do NOT redirect away from the
		// login page back to the custom URL — that creates a loop.
		$login_page_id = isset( $settings['login_page_id'] ) ? absint( $settings['login_page_id'] ) : 0;
		if ( $login_page_id > 0 ) {
			return;
		}

		// No login page configured: redirect the old login page slug to the custom URL
		// so the custom URL (serving wp-login.php) is the single entry point.
		$legacy = $this->pages->get_page_url_by_setting( 'login_page_id' );
		if ( '' === $legacy ) {
			return;
		}

		$current_path = $this->get_request_path();
		$legacy_path  = trim( (string) wp_parse_url( $legacy, PHP_URL_PATH ), '/' );
		$custom_path  = trim( (string) wp_parse_url( $custom, PHP_URL_PATH ), '/' );

		if ( '' !== $legacy_path && $current_path === $legacy_path && $legacy_path !== $custom_path ) {
			wp_safe_redirect( $custom, 301 );
			exit;
		}
	}

	/**
	 * Block native wp-login/wp-admin endpoints for logged-out users.
	 *
	 * @return void
	 */
	public function maybe_block_native_auth_endpoints(): void {
		if ( ! $this->is_enabled() || $this->is_recovery_mode() || is_user_logged_in() ) {
			return;
		}

		if ( $this->is_elementor_editing() ) {
			return;
		}

		$settings = $this->settings_repo->get();
		if ( empty( $settings['block_default_wp_auth'] ) ) {
			return;
		}

		$path = $this->get_request_path();
		if ( '' === $path || $this->is_allowed_native_endpoint( $path ) ) {
			return;
		}

		// Never block legitimate wp-login.php actions, or core flows break: password reset
		// link clicks (rp/resetpass), password-protected post submissions (postpass), logout,
		// GDPR confirm-action links, and the post-request "check your email" screen.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only routing check; these are core actions with their own nonces.
		$action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : '';
		$allowed_actions = array( 'postpass', 'logout', 'rp', 'resetpass', 'confirmaction', 'lostpassword', 'retrievepassword' );
		if ( in_array( $action, $allowed_actions, true ) ) {
			return;
		}

		if ( $this->is_blocked_native_auth_path( $path ) ) {
			$this->render_not_found();
		}
	}

	/**
	 * Handle the custom login route.
	 *
	 * If a login page is configured, redirect the custom URL to that page.
	 * If no login page is configured, serve wp-login.php at the custom URL.
	 *
	 * @return void
	 */
	public function maybe_handle_custom_login_route(): void {
		if ( ! $this->is_enabled() || is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}

		if ( $this->is_elementor_editing() ) {
			return;
		}

		$settings = $this->settings_repo->get();

		// Get the custom login URL setting (e.g. http://test.local/my-login).
		$custom_url = isset( $settings['pkwt_custom_login_url'] ) ? esc_url_raw( (string) $settings['pkwt_custom_login_url'] ) : '';
		if ( '' === $custom_url ) {
			return;
		}

		// Check if the current request matches the custom URL path.
		$custom_path  = trim( (string) wp_parse_url( $custom_url, PHP_URL_PATH ), '/' );
		$request_path = isset( $_SERVER['REQUEST_URI'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			? trim( (string) wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ), '/' ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			: '';

		if ( '' === $custom_path || $request_path !== $custom_path ) {
			return;
		}

		// We are on the custom login URL path. Decide what to do:
		$login_page_id = isset( $settings['login_page_id'] ) ? absint( $settings['login_page_id'] ) : 0;
		if ( $login_page_id > 0 ) {
			// A login page is configured — redirect to it.
			$login_page_url = get_permalink( $login_page_id );
			if ( $login_page_url ) {
				$login_path = trim( (string) wp_parse_url( $login_page_url, PHP_URL_PATH ), '/' );
				if ( $request_path !== $login_path ) {
					wp_safe_redirect( $login_page_url, 302 );
					exit;
				}
			}
			return;
		}

		// No login page configured — redirect to the native WordPress login page.
		// Direct inclusion of wp-login.php is not permitted per WP.org plugin guidelines.
		// Instead, forward the request to the standard login URL, preserving any query args.
		$query_args = array();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_REQUEST['action'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$query_args['action'] = sanitize_key( wp_unslash( $_REQUEST['action'] ) );
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_REQUEST['redirect_to'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$query_args['redirect_to'] = rawurlencode( sanitize_url( wp_unslash( $_REQUEST['redirect_to'] ) ) );
		}
		$login_url = wp_login_url();
		if ( ! empty( $query_args ) ) {
			$login_url = add_query_arg( $query_args, $login_url );
		}
		wp_safe_redirect( $login_url, 302 );
		exit;
	}

	/**
	 * Validate redirect target and avoid loops.
	 *
	 * @param string $target Target URL.
	 * @param string $current Current URL.
	 *
	 * @return bool
	 */
	private function is_valid_target( string $target, string $current, bool $allow_non_page = false ): bool {
		if ( '' === $target ) {
			return false;
		}

		if ( untrailingslashit( $target ) === untrailingslashit( $current ) ) {
			return false;
		}

		if ( $allow_non_page ) {
			$host = wp_parse_url( $target, PHP_URL_HOST );
			$home = wp_parse_url( home_url(), PHP_URL_HOST );
			if ( empty( $host ) || $host === $home ) {
				return true;
			}
		}

		$page_id = url_to_postid( $target );
		if ( $page_id <= 0 ) {
			return false;
		}

		return $this->pages->has_renderable_content( $page_id );
	}

	/**
	 * Get login target URL from custom setting or login page.
	 *
	 * @return string
	 */
	private function get_login_target_url(): string {
		$settings = $this->settings_repo->get();
		$custom   = isset( $settings['pkwt_custom_login_url'] ) ? (string) $settings['pkwt_custom_login_url'] : '';
		if ( '' !== $custom ) {
			// The stored value may be an absolute URL captured when the site lived at a
			// different host/scheme (http↔https, www toggle, or a full site move). Treat its
			// PATH as the source of truth and rebuild on the CURRENT home_url() so the custom
			// login URL never silently falls back to the default after the site address changes.
			$path = trim( (string) wp_parse_url( $custom, PHP_URL_PATH ), '/' );
			if ( '' !== $path ) {
				return esc_url_raw( home_url( '/' . $path . '/' ) );
			}
		}

		return $this->pages->get_page_url_by_setting( 'login_page_id' );
	}

	/**
	 * Get normalized request path.
	 *
	 * @return string
	 */
	private function get_request_path(): string {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		return trim( (string) wp_parse_url( $request_uri, PHP_URL_PATH ), '/' );
	}

	/**
	 * Check if endpoint is allowed despite blocking.
	 *
	 * Uses wp_parse_url() on admin_url() to derive correct admin-ajax and admin-post
	 * paths dynamically, rather than assuming a hardcoded wp-admin location.
	 *
	 * @param string $path Path.
	 *
	 * @return bool
	 */
	private function is_allowed_native_endpoint( string $path ): bool {
		$ajax_path      = ltrim( (string) wp_parse_url( admin_url( 'admin-ajax.php' ), PHP_URL_PATH ), '/' );
		$admin_post_path = ltrim( (string) wp_parse_url( admin_url( 'admin-post.php' ), PHP_URL_PATH ), '/' );
		return in_array( $path, array( $ajax_path, $admin_post_path ), true );
	}

	/**
	 * Check if path should be blocked.
	 *
	 * Only block wp-login.php — never block wp-admin, which is the dashboard
	 * and must remain accessible so logged-in users can reach the admin area.
	 *
	 * @param string $path Path.
	 *
	 * @return bool
	 */
	private function is_blocked_native_auth_path( string $path ): bool {
		return in_array( $path, array( 'wp-login.php', 'wp-login' ), true );
	}

	/**
	 * Emergency recovery mode bypass.
	 *
	 * @return bool
	 */
	private function is_recovery_mode(): bool {
		if ( defined( 'POWERKIT_AUTH_RECOVERY_MODE' ) ) {
			return (bool) POWERKIT_AUTH_RECOVERY_MODE;
		}
		return defined( 'PKWT_AUTH_RECOVERY_MODE' ) && PKWT_AUTH_RECOVERY_MODE;
	}

	/**
	 * Render a 404 response and exit.
	 *
	 * @return void
	 */
	private function render_not_found(): void {
		global $wp_query;

		if ( $wp_query instanceof \WP_Query ) {
			$wp_query->set_404();
		}

		status_header( 404 );
		nocache_headers();

		$template = get_404_template();
		if ( $template && file_exists( $template ) ) {
			include $template;
		} else {
			wp_die( esc_html__( 'Not found.', 'powerplus-toolkit' ), '404', array( 'response' => 404 ) );
		}

		exit;
	}
}
