<?php
/**
 * AJAX handlers.
 *
 * @package PKWT
 */

namespace PKWT\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_AJAX_Handler {

	/**
	 * Security class.
	 *
	 * @var Class_PKWT_Security
	 */
	private $security;

	/**
	 * Settings repository.
	 *
	 * @var Class_PKWT_Settings_Repository
	 */
	private $settings_repo;

	/**
	 * Constructor.
	 *
	 * @param Class_PKWT_Security $security Security.
	 */
	public function __construct( Class_PKWT_Security $security ) {
		$this->security      = $security;
		$this->settings_repo = new Class_PKWT_Settings_Repository();
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		$actions = array( 'login', 'register', 'lostpw', 'resetpw' );
		foreach ( $actions as $action ) {
			add_action( 'wp_ajax_nopriv_pkwt_' . $action, array( $this, 'handle_' . $action ) );
			add_action( 'wp_ajax_pkwt_' . $action, array( $this, 'handle_' . $action ) );
		}
	}

	/**
	 * Handle login.
	 *
	 * @return void
	 */
	// phpcs:disable WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended -- AJAX handlers validate nonce via verify_nonce_from_post().
	public function handle_login(): void {
		try {
			if ( ! $this->verify_nonce_from_post( 'login_nonce' ) ) {
				$this->send_error( __( 'Security check failed.', 'powerplus-toolkit' ), 403, 'login_nonce' );
			}

			if ( $this->is_honeypot_triggered() ) {
				$this->send_error( __( 'Request could not be processed.', 'powerplus-toolkit' ), 400, 'login_nonce' );
			}

			$status = $this->security->get_rate_limit_status();
			if ( ! empty( $status['limited'] ) ) {
				$this->send_error( __( 'Too many attempts. Please wait and try again.', 'powerplus-toolkit' ), 429, 'login_nonce', array( 'retry_after' => (int) $status['retry_after'] ) );
			}

			$captcha_token = isset( $_POST['captcha_token'] ) ? sanitize_text_field( wp_unslash( $_POST['captcha_token'] ) ) : '';
			if ( ! $this->security->verify_captcha( $captcha_token ) ) {
				$this->send_error( __( 'CAPTCHA verification failed.', 'powerplus-toolkit' ), 400, 'login_nonce' );
			}

			$username_raw = isset( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '';
				$username_raw = trim( $username_raw );
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Password must remain raw; sanitizing would corrupt special characters.
				$password     = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : '';
			$remember     = ! empty( $_POST['remember'] );

			if ( '' === $username_raw || '' === $password || false !== strpos( $username_raw, ' ' ) ) {
				$this->send_error( __( 'Incorrect username or password.', 'powerplus-toolkit' ), 401, 'login_nonce' );
			}

			$username = sanitize_user( $username_raw );
			// wp_signon() is WordPress core's authentication function. It fires the authenticate
			// filter chain, so security plugins (brute-force blockers, 2FA, etc.) can intercept.
			$user     = wp_signon(
				array(
					'user_login'    => $username,
					'user_password' => $password,
					'remember'      => $remember,
				),
				is_ssl()
			);

			if ( is_wp_error( $user ) ) {
				$this->security->increment_failed_attempt( $username );
				$this->send_error( __( 'Incorrect username or password.', 'powerplus-toolkit' ), 401, 'login_nonce' );
			}

			if ( ! $user instanceof \WP_User ) {
				$this->send_error( __( 'Additional authentication required.', 'powerplus-toolkit' ), 202, 'login_nonce' );
			}

			$this->security->reset_failed_attempts();
			$redirect = $this->get_redirect_for_user( $user );
			wp_send_json_success(
				array(
					'message'  => __( 'Login successful.', 'powerplus-toolkit' ),
					'redirect' => $redirect,
					'nonce'    => Class_PKWT_Security::nonce( 'login_nonce' ),
				)
			);
		} catch ( \Throwable $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'PowerPlus Error: ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}
			$this->send_error( __( 'Login could not be completed.', 'powerplus-toolkit' ), 500, 'login_nonce' );
		}
	}

	/**
	 * Handle register.
	 *
	 * @return void
	 */
	public function handle_register(): void {
		try {
			if ( ! $this->verify_nonce_from_post( 'register_nonce' ) ) {
				$this->send_error( __( 'Security check failed.', 'powerplus-toolkit' ), 403, 'register_nonce' );
			}

			if ( $this->is_honeypot_triggered() ) {
				$this->send_error( __( 'Request could not be processed.', 'powerplus-toolkit' ), 400, 'register_nonce' );
			}

			$captcha_token = isset( $_POST['captcha_token'] ) ? sanitize_text_field( wp_unslash( $_POST['captcha_token'] ) ) : '';
			if ( ! $this->security->verify_captcha( $captcha_token ) ) {
				$this->send_error( __( 'CAPTCHA verification failed.', 'powerplus-toolkit' ), 400, 'register_nonce' );
			}

			$username_raw = isset( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '';
				$username_raw = trim( $username_raw );
				$email_raw    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
				$email        = strtolower( $email_raw );
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Password must remain raw; sanitizing would corrupt special characters.
				$password     = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : '';
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Password confirmation must remain raw for equality check.
				$confirm      = isset( $_POST['confirm_password'] ) ? (string) wp_unslash( $_POST['confirm_password'] ) : '';
			$errors       = array();

			if ( '' === $username_raw || false !== strpos( $username_raw, ' ' ) ) {
				$errors['username'] = __( 'Username is required and cannot contain spaces.', 'powerplus-toolkit' );
			}

			$username = sanitize_user( $username_raw );
			if ( ! is_email( $email ) ) {
				$errors['email'] = __( 'A valid email is required.', 'powerplus-toolkit' );
			}
			if ( strlen( $password ) < 8 || strlen( $password ) > 72 ) {
				$errors['password'] = __( 'Password must be 8-72 characters.', 'powerplus-toolkit' );
			}
			if ( $password !== $confirm ) {
				$errors['confirm_password'] = __( 'Passwords do not match.', 'powerplus-toolkit' );
			}
			if ( username_exists( $username ) ) {
				$errors['username'] = __( 'Username already exists.', 'powerplus-toolkit' );
			}
			if ( email_exists( $email ) ) {
				$errors['email'] = __( 'Email already exists.', 'powerplus-toolkit' );
			}

			if ( ! empty( $errors ) ) {
				wp_send_json_error(
					array(
						'message'     => __( 'Please correct the highlighted fields.', 'powerplus-toolkit' ),
						'field_errors'=> $errors,
						'nonce'       => Class_PKWT_Security::nonce( 'register_nonce' ),
					),
					400
				);
			}

			// wp_create_user() is WordPress core's registration function. It fires the
			// user_register and wp_pre_insert_user_data hooks, allowing security plugins to validate
			// or block registrations. This plugin's primary purpose is a custom register UI.
			$user_id = wp_create_user( $username, $password, $email );
			if ( is_wp_error( $user_id ) ) {
				$this->send_error( __( 'Registration failed. Please try again.', 'powerplus-toolkit' ), 400, 'register_nonce' );
			}

			wp_new_user_notification( $user_id, null, 'both' );

			$redirect = isset( $_POST['success_redirect'] ) ? $this->security->sanitize_redirect( sanitize_text_field( wp_unslash( $_POST['success_redirect'] ) ) ) : '';
			if ( '' === $redirect ) {
				$redirect = home_url( '/' );
			}

			wp_send_json_success(
				array(
					'message'  => __( 'Registration successful.', 'powerplus-toolkit' ),
					'redirect' => $redirect,
					'nonce'    => Class_PKWT_Security::nonce( 'register_nonce' ),
				)
			);
		} catch ( \Throwable $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'PowerPlus Error: ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}
			$this->send_error( __( 'Registration could not be completed.', 'powerplus-toolkit' ), 500, 'register_nonce' );
		}
	}

	/**
	 * Handle lost password.
	 *
	 * @return void
	 */
	public function handle_lostpw(): void {
		try {
			if ( ! $this->verify_nonce_from_post( 'lostpw_nonce' ) ) {
				$this->send_error( __( 'Security check failed.', 'powerplus-toolkit' ), 403, 'lostpw_nonce' );
			}

			if ( $this->is_honeypot_triggered() ) {
				$this->send_error( __( 'Request could not be processed.', 'powerplus-toolkit' ), 400, 'lostpw_nonce' );
			}

			$status = $this->security->get_rate_limit_status();
			if ( ! empty( $status['limited'] ) ) {
				$this->send_error( __( 'Too many attempts. Please wait.', 'powerplus-toolkit' ), 429, 'lostpw_nonce', array( 'retry_after' => (int) $status['retry_after'] ) );
			}

			$user_login = isset( $_POST['user_login'] ) ? sanitize_text_field( wp_unslash( $_POST['user_login'] ) ) : '';
			retrieve_password( $user_login );
			wp_send_json_success(
				array(
					'message' => __( 'If the account exists, password reset instructions were sent.', 'powerplus-toolkit' ),
					'nonce'   => Class_PKWT_Security::nonce( 'lostpw_nonce' ),
				)
			);
		} catch ( \Throwable $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'PowerPlus Error: ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}
			$this->send_error( __( 'Password reset request failed.', 'powerplus-toolkit' ), 500, 'lostpw_nonce' );
		}
	}

	/**
	 * Handle reset password.
	 *
	 * @return void
	 */
	public function handle_resetpw(): void {
		try {
			if ( ! $this->verify_nonce_from_post( 'resetpw_nonce' ) ) {
				$this->send_error( __( 'Security check failed.', 'powerplus-toolkit' ), 403, 'resetpw_nonce' );
			}

			if ( $this->is_honeypot_triggered() ) {
				$this->send_error( __( 'Request could not be processed.', 'powerplus-toolkit' ), 400, 'resetpw_nonce' );
			}

			$key      = isset( $_POST['rp_key'] ) ? sanitize_text_field( wp_unslash( $_POST['rp_key'] ) ) : '';
			$login    = isset( $_POST['rp_login'] ) ? sanitize_text_field( wp_unslash( $_POST['rp_login'] ) ) : '';
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Password must remain raw for reset.
				$password = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : '';
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Password confirmation must remain raw for equality check.
				$confirm  = isset( $_POST['confirm_password'] ) ? (string) wp_unslash( $_POST['confirm_password'] ) : '';

			if ( strlen( $password ) < 8 || strlen( $password ) > 72 ) {
				$this->send_error( __( 'Password must be 8-72 characters.', 'powerplus-toolkit' ), 400, 'resetpw_nonce' );
			}

			if ( $password !== $confirm ) {
				$this->send_error( __( 'Passwords do not match.', 'powerplus-toolkit' ), 400, 'resetpw_nonce' );
			}

			$user = check_password_reset_key( $key, $login );
			if ( is_wp_error( $user ) ) {
				$this->send_error( __( 'Invalid or expired reset link.', 'powerplus-toolkit' ), 400, 'resetpw_nonce' );
			}

			reset_password( $user, $password );

			$settings = $this->settings_repo->get();
			$login_id = isset( $settings['login_page_id'] ) ? absint( $settings['login_page_id'] ) : 0;
			$redirect = $login_id ? get_permalink( $login_id ) : wp_login_url();

			wp_send_json_success(
				array(
					'message'  => __( 'Password reset successfully.', 'powerplus-toolkit' ),
					'redirect' => $redirect,
					'nonce'    => Class_PKWT_Security::nonce( 'resetpw_nonce' ),
				)
			);
		} catch ( \Throwable $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'PowerPlus Error: ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}
			$this->send_error( __( 'Password reset failed.', 'powerplus-toolkit' ), 500, 'resetpw_nonce' );
		}
	}

	/**
	 * Verify nonce from post payload.
	 *
	 * @param string $action Base nonce action.
	 *
	 * @return bool
	 */
	private function verify_nonce_from_post( string $action ): bool {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		return $this->security->verify_nonce( $nonce, $action );
	}

	/**
	 * Check bot honeypot.
	 *
	 * @return bool
	 */
	private function is_honeypot_triggered(): bool {
		$honeypot = isset( $_POST['website_url'] ) ? sanitize_text_field( wp_unslash( $_POST['website_url'] ) ) : '';
		return '' !== $honeypot;
	}

	/**
	 * Send common error response.
	 *
	 * @param string               $message Message.
	 * @param int                  $status  Status.
	 * @param string               $action  Nonce action.
	 * @param array<string,mixed>  $extra   Extra data.
	 *
	 * @return void
	 */
	private function send_error( string $message, int $status, string $action, array $extra = array() ): void {
		$data = array_merge(
			array(
				'message' => $message,
				'nonce'   => Class_PKWT_Security::nonce( $action ),
			),
			$extra
		);

		wp_send_json_error( $data, $status );
	}

	/**
	 * Resolve user redirect.
	 *
	 * @param \WP_User $user User.
	 *
	 * @return string
	 */
	private function get_redirect_for_user( \WP_User $user ): string {
		$settings = $this->settings_repo->get();

		$posted_redirect = isset( $_POST['redirect_to'] ) ? sanitize_text_field( wp_unslash( $_POST['redirect_to'] ) ) : '';
		if ( '' !== $posted_redirect ) {
			$validated_posted = $this->security->sanitize_redirect( $posted_redirect );
			if ( '' !== $validated_posted ) {
				return $validated_posted;
			}
		}

		$role_redirects = isset( $settings['role_redirects'] ) && is_array( $settings['role_redirects'] ) ? $settings['role_redirects'] : array();
		foreach ( $user->roles as $role ) {
			if ( ! empty( $role_redirects[ $role ] ) ) {
				$clean = $this->security->sanitize_redirect( (string) $role_redirects[ $role ] );
				if ( '' !== $clean ) {
					return $clean;
				}
			}
		}

		$default = isset( $settings['after_login_redirect'] ) ? $this->security->sanitize_redirect( (string) $settings['after_login_redirect'] ) : '';
		$redirect_page_id = isset( $settings['after_login_redirect_page_id'] ) ? absint( $settings['after_login_redirect_page_id'] ) : 0;
		if ( $redirect_page_id > 0 ) {
			$redirect_page_url = get_permalink( $redirect_page_id );
			if ( $redirect_page_url ) {
				$default_page = $this->security->sanitize_redirect( $redirect_page_url );
				if ( '' !== $default_page ) {
					return $default_page;
				}
			}
		}

		if ( '' !== $default ) {
			return $default;
		}

		return home_url( '/' );
	}
	// phpcs:enable WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended
}
