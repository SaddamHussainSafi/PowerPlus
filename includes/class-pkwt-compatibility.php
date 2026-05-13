<?php
/**
 * Compatibility layer.
 *
 * @package PKWT
 */

namespace PKWT\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_Compatibility {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'init', array( $this, 'register_cache_exclusions' ), 20 );
		add_filter( 'login_redirect', array( $this, 'woocommerce_login_redirect' ), 20, 3 );
		add_filter( 'woocommerce_registration_redirect', array( $this, 'woocommerce_registration_redirect' ), 20 );
		add_filter( 'rocket_cache_reject_uri', array( $this, 'filter_wp_rocket_reject_uris' ) );
	}

	/**
	 * Cache exclusions.
	 *
	 * @return void
	 */
	public function register_cache_exclusions(): void {
		$settings = get_option( 'pkwt_settings', array() );
		$page_ids = array(
			isset( $settings['login_page_id'] ) ? absint( $settings['login_page_id'] ) : 0,
			isset( $settings['register_page_id'] ) ? absint( $settings['register_page_id'] ) : 0,
			isset( $settings['lost_password_page_id'] ) ? absint( $settings['lost_password_page_id'] ) : 0,
			isset( $settings['reset_password_page_id'] ) ? absint( $settings['reset_password_page_id'] ) : 0,
		);

		foreach ( array_filter( $page_ids ) as $id ) {
			$url = get_permalink( $id );
			if ( ! $url ) {
				continue;
			}
			if ( function_exists( 'rocket_clean_domain' ) ) {
				// Intentional: modifying WP Rocket's own option to exclude auth pages from cache.
				// This is the documented API for programmatically adding cache exclusions in WP Rocket.
				// See: https://docs.wp-rocket.me/article/1304-using-wp-rocket-functions-in-your-custom-code
				$rejected = get_option( 'rocket_cache_reject_uri', array() );
				$path     = wp_parse_url( $url, PHP_URL_PATH );
				if ( $path && ! in_array( $path, $rejected, true ) ) {
					$rejected[] = $path;
					update_option( 'rocket_cache_reject_uri', $rejected );
				}
			}
		}
	}

	/**
	 * Add auth pages to WP Rocket reject URIs.
	 *
	 * @param array<int,string> $uris Existing URIs.
	 *
	 * @return array<int,string>
	 */
	public function filter_wp_rocket_reject_uris( array $uris ): array {
		$settings = get_option( 'pkwt_settings', array() );
		$page_ids = array(
			isset( $settings['login_page_id'] ) ? absint( $settings['login_page_id'] ) : 0,
			isset( $settings['register_page_id'] ) ? absint( $settings['register_page_id'] ) : 0,
			isset( $settings['lost_password_page_id'] ) ? absint( $settings['lost_password_page_id'] ) : 0,
			isset( $settings['reset_password_page_id'] ) ? absint( $settings['reset_password_page_id'] ) : 0,
		);

		foreach ( array_filter( $page_ids ) as $id ) {
			$url = get_permalink( $id );
			if ( ! $url ) {
				continue;
			}
			$path = wp_parse_url( $url, PHP_URL_PATH );
			if ( $path && ! in_array( $path, $uris, true ) ) {
				$uris[] = $path;
			}
		}

		return $uris;
	}

	/**
	 * Woo login redirect.
	 *
	 * @param string              $redirect Redirect.
	 * @param string              $requested Requested.
	 * @param \WP_User|\WP_Error $user User.
	 *
	 * @return string
	 */
	public function woocommerce_login_redirect( string $redirect, string $requested, $user ): string {
		$settings = get_option( 'pkwt_settings', array() );
		if ( empty( $settings['woocommerce_mode'] ) ) {
			return $redirect;
		}

		if ( ! function_exists( 'WC' ) ) {
			return $redirect;
		}

		return $redirect;
	}

	/**
	 * Woo registration redirect.
	 *
	 * @param string $redirect Redirect.
	 *
	 * @return string
	 */
	public function woocommerce_registration_redirect( string $redirect ): string {
		$settings = get_option( 'pkwt_settings', array() );
		if ( empty( $settings['woocommerce_mode'] ) ) {
			return $redirect;
		}

		return $redirect;
	}
}
