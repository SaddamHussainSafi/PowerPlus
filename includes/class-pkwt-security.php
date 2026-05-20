<?php
/**
 * Security helper.
 *
 * @package PKWT
 */

namespace PKWT\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_Security {

	/**
	 * Register security hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_init', array( $this, 'register_privacy_policy' ) );
		add_filter( 'nonce_life', array( $this, 'filter_nonce_life' ) );
	}

	/**
	 * Use shorter nonce lifetime for auth actions.
	 *
	 * @param int $lifetime Lifetime in seconds.
	 *
	 * @return int
	 */
	public function filter_nonce_life( int $lifetime ): int {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce not available yet; this adjusts nonce lifetime for AJAX action namespace.
		if ( wp_doing_ajax() && isset( $_POST['action'] ) ) {
			$action = sanitize_key( wp_unslash( $_POST['action'] ) );
			if ( 0 === strpos( $action, 'pkwt_' ) ) {
				return HOUR_IN_SECONDS * 4;
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
		return $lifetime;
	}

	/**
	 * Register privacy policy section.
	 *
	 * @return void
	 */
	public function register_privacy_policy(): void {
		if ( function_exists( 'wp_add_privacy_policy_content' ) ) {
			wp_add_privacy_policy_content(
				__( 'PowerKit - Powerful Tools For Your Website', 'powerplus-toolkit' ),
				'<p>' . esc_html__( 'This plugin stores temporary login attempt counters in transients and plugin settings in wp_options. It does not store passwords.', 'powerplus-toolkit' ) . '</p>'
			);
		}
	}

	/**
	 * Verify nonce.
	 *
	 * @param string $nonce Nonce value.
	 * @param string $action Action key.
	 *
	 * @return bool
	 */
	public function verify_nonce( string $nonce, string $action ): bool {
		return (bool) wp_verify_nonce( $nonce, self::nonce_action( $action ) );
	}

	/**
	 * Check same-site redirect.
	 *
	 * @param string $url URL.
	 *
	 * @return string
	 */
	public function sanitize_redirect( string $url ): string {
		$sanitized = esc_url_raw( $url );
		if ( '' === $sanitized ) {
			return '';
		}

		$home_host     = wp_parse_url( home_url(), PHP_URL_HOST );
		$allowed_hosts = array( $home_host );
		$validated     = wp_validate_redirect( $sanitized, '' );
		$dest_host     = wp_parse_url( $validated, PHP_URL_HOST );

		if ( '' === $validated ) {
			return '';
		}

		if ( empty( $dest_host ) || in_array( $dest_host, $allowed_hosts, true ) ) {
			return $validated;
		}

		return '';
	}

	/**
	 * Is Wordfence active.
	 *
	 * @return bool
	 */
	public function should_skip_rate_limit(): bool {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		return function_exists( 'is_plugin_active' ) && is_plugin_active( 'wordfence/wordfence.php' );
	}

	/**
	 * Get attempts transient key.
	 *
	 * @return string
	 */
	private function attempts_key(): string {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0.0.0.0';
		return 'pkwt_attempts_' . md5( $ip );
	}

	/**
	 * Get lockout settings.
	 *
	 * @return array<string,int>
	 */
	private function settings(): array {
		$settings = get_option( 'pkwt_settings', array() );

		$max_attempts = isset( $settings['max_attempts'] ) ? absint( $settings['max_attempts'] ) : 5;
		$lock_minutes = isset( $settings['lockout_minutes'] ) ? absint( $settings['lockout_minutes'] ) : 15;

		return array(
			'max_attempts' => max( 1, min( 20, $max_attempts ) ),
			'lock_minutes' => max( 5, min( 1440, $lock_minutes ) ),
		);
	}

	/**
	 * Rate limit status.
	 *
	 * @return array<string,mixed>
	 */
	public function get_rate_limit_status(): array {
		if ( $this->should_skip_rate_limit() ) {
			return array(
				'limited'    => false,
				'remaining'  => 0,
				'retry_after' => 0,
			);
		}

		$settings = get_option( 'pkwt_settings', array() );
		if ( empty( $settings['enable_rate_limiting'] ) ) {
			return array(
				'limited'    => false,
				'remaining'  => 0,
				'retry_after' => 0,
			);
		}

		$key      = $this->attempts_key();
		$attempts = get_transient( $key );
		if ( ! is_array( $attempts ) ) {
			$attempts = array(
				'count'      => 0,
				'locked'     => 0,
				'created_at' => time(),
			);
		}

		$cfg = $this->settings();
		if ( ! empty( $attempts['locked'] ) && (int) $attempts['locked'] > time() ) {
			return array(
				'limited'    => true,
				'remaining'  => 0,
				'retry_after'=> (int) $attempts['locked'] - time(),
			);
		}

		return array(
			'limited'    => false,
			'remaining'  => max( 0, $cfg['max_attempts'] - (int) $attempts['count'] ),
			'retry_after'=> 0,
		);
	}

	/**
	 * Increment failed attempt.
	 *
	 * @return void
	 */
	public function increment_failed_attempt( string $username_attempted = '' ): void {
		if ( $this->should_skip_rate_limit() ) {
			return;
		}

		$settings = get_option( 'pkwt_settings', array() );
		if ( empty( $settings['enable_rate_limiting'] ) ) {
			return;
		}

		$key      = $this->attempts_key();
		$attempts = get_transient( $key );
		if ( ! is_array( $attempts ) ) {
			$attempts = array(
				'count'      => 0,
				'locked'     => 0,
				'created_at' => time(),
			);
		}

		$cfg = $this->settings();
		$attempts['count'] = (int) $attempts['count'] + 1;

		if ( $attempts['count'] >= $cfg['max_attempts'] ) {
			$attempts['locked'] = time() + ( $cfg['lock_minutes'] * MINUTE_IN_SECONDS );
		}

		set_transient( $key, $attempts, $cfg['lock_minutes'] * MINUTE_IN_SECONDS );

		if ( ! empty( $attempts['locked'] ) && (int) $attempts['locked'] > time() ) {
			$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
			do_action( 'pkwt_ip_locked_out', $ip, sanitize_user( $username_attempted ), (int) $attempts['count'] );
		}
	}

	/**
	 * Clear failed attempt state.
	 *
	 * @return void
	 */
	public function reset_failed_attempts(): void {
		delete_transient( $this->attempts_key() );
	}

	/**
	 * Verify CAPTCHA token.
	 *
	 * @param string $token Token.
	 *
	 * @return bool
	 */
	public function verify_captcha( string $token ): bool {
		$settings = get_option( 'pkwt_settings', array() );
		$provider = isset( $settings['captcha_provider'] ) ? sanitize_text_field( $settings['captcha_provider'] ) : 'none';

		if ( 'none' === $provider ) {
			return true;
		}

		$secret = '';
		$url    = '';
		if ( 'hcaptcha' === $provider ) {
			$secret = isset( $settings['hcaptcha_secret_key'] ) ? sanitize_text_field( $settings['hcaptcha_secret_key'] ) : '';
			$url    = 'https://hcaptcha.com/siteverify';
		} else {
			$secret = isset( $settings['recaptcha_secret_key'] ) ? sanitize_text_field( $settings['recaptcha_secret_key'] ) : '';
			$url    = 'https://www.google.com/recaptcha/api/siteverify';
		}

		if ( '' === $secret || '' === $token ) {
			return false;
		}

		$response = wp_remote_post(
			$url,
			array(
				'timeout' => 10,
				'body'    => array(
					'secret'   => $secret,
					'response' => $token,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		return ! empty( $body['success'] );
	}

	/**
	 * Build nonce action name with site hash suffix.
	 *
	 * @param string $action Base action.
	 *
	 * @return string
	 */
	public static function nonce_action( string $action ): string {
		return 'pkwt_' . sanitize_key( $action ) . '_' . wp_hash( site_url() );
	}

	/**
	 * Build nonce by action.
	 *
	 * @param string $action Base action.
	 *
	 * @return string
	 */
	public static function nonce( string $action ): string {
		return wp_create_nonce( self::nonce_action( $action ) );
	}
}
