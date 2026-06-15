<?php
/**
 * Branding module — white-labels the native wp-login.php screen and the admin chrome.
 *
 * Purely hook-based: it NEVER creates pages or modifies core files. Settings live under the
 * 'branding' key of the existing pkwt_settings option. Patterns adapted from LoginPress
 * (login screen) and White Label CMS (admin chrome).
 *
 * @package PKWT
 */

namespace PKWT\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_Branding {

	/**
	 * Cached branding settings.
	 *
	 * @var array<string,mixed>|null
	 */
	private $branding = null;

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		$b = $this->get_branding();
		if ( empty( $b['enabled'] ) ) {
			return;
		}

		// ── Login screen ──
		// Skip native-login styling if the plugin redirects wp-login to an Elementor page,
		// to avoid double-rendering / conflicting styles.
		if ( ! empty( $b['style_login'] ) ) {
			add_action( 'login_enqueue_scripts', array( $this, 'print_login_styles' ) );
			add_filter( 'login_headerurl', array( $this, 'filter_login_header_url' ) );
			add_filter( 'login_headertext', array( $this, 'filter_login_header_text' ) );
			add_filter( 'login_message', array( $this, 'filter_login_message' ) );
		}
		if ( ! empty( $b['hide_login_errors'] ) ) {
			add_filter( 'login_errors', array( $this, 'filter_login_errors' ) );
		}

		// ── Admin chrome ──
		if ( '' !== (string) ( $b['admin_footer_text'] ?? '' ) ) {
			add_filter( 'admin_footer_text', array( $this, 'filter_admin_footer_text' ), 999 );
		}
		if ( ! empty( $b['hide_admin_footer_version'] ) ) {
			add_filter( 'update_footer', array( $this, 'filter_update_footer' ), 999 );
		}
		if ( ! empty( $b['hide_wp_logo'] ) ) {
			add_action( 'admin_bar_menu', array( $this, 'remove_wp_logo' ), 999 );
		}
	}

	/**
	 * Branding settings accessor (cached).
	 *
	 * @return array<string,mixed>
	 */
	private function get_branding(): array {
		if ( null !== $this->branding ) {
			return $this->branding;
		}
		$settings = get_option( 'pkwt_settings', array() );
		$raw      = ( is_array( $settings ) && isset( $settings['branding'] ) && is_array( $settings['branding'] ) ) ? $settings['branding'] : array();
		$this->branding = wp_parse_args( $raw, self::defaults() );
		return $this->branding;
	}

	/**
	 * Default branding settings.
	 *
	 * @return array<string,mixed>
	 */
	public static function defaults(): array {
		return array(
			'enabled'                   => 0,
			'style_login'               => 1,
			'logo_id'                   => 0,
			'logo_link'                 => '',
			'logo_title'                => '',
			'bg_color'                  => '',
			'form_bg'                   => '',
			'accent_color'              => '#FF6500',
			'welcome_message'           => '',
			'hide_login_errors'         => 0,
			'admin_footer_text'         => '',
			'hide_admin_footer_version' => 0,
			'hide_wp_logo'              => 0,
		);
	}

	/**
	 * Whether the plugin actively redirects wp-login to a custom page (so we skip styling it).
	 *
	 * @return bool
	 */
	private function login_is_redirected(): bool {
		$settings = get_option( 'pkwt_settings', array() );
		return ! empty( $settings['pkwt_custom_login_url'] ) && ! empty( $settings['block_default_wp_auth'] );
	}

	/**
	 * Print inline CSS on the login screen built from the branding settings.
	 *
	 * @return void
	 */
	public function print_login_styles(): void {
		if ( $this->login_is_redirected() ) {
			return;
		}
		$b      = $this->get_branding();
		$accent = $this->safe_color( (string) $b['accent_color'], '#FF6500' );
		$css    = '';

		$logo_url = $b['logo_id'] ? wp_get_attachment_image_url( (int) $b['logo_id'], 'medium' ) : '';
		if ( $logo_url ) {
			$css .= '#login h1 a{background-image:url(' . esc_url( $logo_url ) . ');background-size:contain;background-position:center;width:100%;height:80px;}';
		}
		$bg = $this->safe_color( (string) $b['bg_color'], '' );
		if ( '' !== $bg ) {
			$css .= 'body.login{background:' . $bg . ';}';
		}
		$form_bg = $this->safe_color( (string) $b['form_bg'], '' );
		if ( '' !== $form_bg ) {
			$css .= '.login form{background:' . $form_bg . ';}';
		}
		$css .= '.login form .button.button-large{background:' . $accent . ';border-color:' . $accent . ';color:#fff;}';
		$css .= '.login #nav a:hover,.login #backtoblog a:hover,.login a:hover{color:' . $accent . ';}';

		if ( '' !== $css ) {
			wp_register_style( 'pkwt-login-branding', false, array(), PKWT_VERSION );
			wp_enqueue_style( 'pkwt-login-branding' );
			wp_add_inline_style( 'pkwt-login-branding', $css );
		}
	}

	/**
	 * Login logo link target.
	 *
	 * @param string $url Default URL.
	 *
	 * @return string
	 */
	public function filter_login_header_url( $url ): string {
		$b = $this->get_branding();
		return '' !== (string) $b['logo_link'] ? esc_url( (string) $b['logo_link'] ) : home_url( '/' );
	}

	/**
	 * Login logo title/alt text.
	 *
	 * @param string $text Default text.
	 *
	 * @return string
	 */
	public function filter_login_header_text( $text ): string {
		$b = $this->get_branding();
		return '' !== (string) $b['logo_title'] ? esc_attr( (string) $b['logo_title'] ) : get_bloginfo( 'name' );
	}

	/**
	 * Prepend a custom welcome message above the login form.
	 *
	 * @param string $message Existing message.
	 *
	 * @return string
	 */
	public function filter_login_message( $message ): string {
		if ( $this->login_is_redirected() ) {
			return $message;
		}
		$b = $this->get_branding();
		$welcome = trim( (string) $b['welcome_message'] );
		if ( '' === $welcome ) {
			return $message;
		}
		return '<div class="message pkwt-login-welcome">' . wp_kses_post( wpautop( $welcome ) ) . '</div>' . $message;
	}

	/**
	 * Replace login error strings with a generic message to reduce username enumeration.
	 *
	 * @param string $errors Existing errors HTML.
	 *
	 * @return string
	 */
	public function filter_login_errors( $errors ): string {
		// Preserve non-credential notices (e.g. password reset confirmation) which WP routes
		// through login_message, not login_errors — so a blanket generic message here is safe.
		return esc_html__( 'Invalid login details. Please try again.', 'powerplus-toolkit' );
	}

	/**
	 * Custom admin footer (left) text.
	 *
	 * @param string $text Existing footer text.
	 *
	 * @return string
	 */
	public function filter_admin_footer_text( $text ): string {
		$b = $this->get_branding();
		return wp_kses_post( (string) $b['admin_footer_text'] );
	}

	/**
	 * Clear the admin footer version string (right side).
	 *
	 * @param string $text Existing version text.
	 *
	 * @return string
	 */
	public function filter_update_footer( $text ): string {
		return '';
	}

	/**
	 * Remove the WordPress logo node from the admin bar.
	 *
	 * @param \WP_Admin_Bar $bar Admin bar.
	 *
	 * @return void
	 */
	public function remove_wp_logo( $bar ): void {
		if ( is_object( $bar ) && method_exists( $bar, 'remove_node' ) ) {
			$bar->remove_node( 'wp-logo' );
		}
	}

	/**
	 * Validate a color value (hex or rg/rgba); returns the fallback when invalid.
	 *
	 * @param string $value    Raw color.
	 * @param string $fallback Fallback when invalid.
	 *
	 * @return string
	 */
	private function safe_color( string $value, string $fallback ): string {
		$value = trim( $value );
		if ( '' === $value ) {
			return $fallback;
		}
		$hex = sanitize_hex_color( $value );
		if ( $hex ) {
			return $hex;
		}
		if ( preg_match( '/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*(?:,\s*(?:0|1|0?\.\d+)\s*)?\)$/i', $value ) ) {
			return $value;
		}
		return $fallback;
	}
}
