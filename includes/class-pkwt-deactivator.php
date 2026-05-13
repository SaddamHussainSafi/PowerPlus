<?php
/**
 * Deactivation class.
 *
 * @package PKWT
 */

namespace PKWT\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_Deactivator {

	/**
	 * Deactivate plugin.
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		global $wpdb;

		$settings = get_option( 'pkwt_settings', array() );
		$priority = isset( $settings['filter_priority'] ) ? absint( $settings['filter_priority'] ) : PKWT_FILTER_PRIORITY;
		$priority = max( 1, min( 99, $priority ) );

		$redirector = new Class_PKWT_Redirector();
		remove_filter( 'login_url', array( $redirector, 'filter_login_url' ), $priority );
		remove_filter( 'register_url', array( $redirector, 'filter_register_url' ), $priority );
		remove_filter( 'lostpassword_url', array( $redirector, 'filter_lost_password_url' ), $priority );
		remove_filter( 'logout_redirect', array( $redirector, 'filter_logout_redirect' ), $priority );

		$like = $wpdb->esc_like( '_transient_pkwt_' ) . '%';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- One-time uninstall-style cleanup query.
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $like ) );

		flush_rewrite_rules();
	}
}
