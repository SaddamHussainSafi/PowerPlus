<?php
/**
 * Uninstall routine.
 *
 * @package PKWT
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Uninstall script runs in global scope; variables are intentionally local to this file.
$pkwt_settings = get_option( 'pkwt_settings', array() );
$pkwt_page_ids = array_filter(
	array(
		isset( $pkwt_settings['login_page_id'] ) ? absint( $pkwt_settings['login_page_id'] ) : 0,
		isset( $pkwt_settings['register_page_id'] ) ? absint( $pkwt_settings['register_page_id'] ) : 0,
		isset( $pkwt_settings['lost_password_page_id'] ) ? absint( $pkwt_settings['lost_password_page_id'] ) : 0,
		isset( $pkwt_settings['reset_password_page_id'] ) ? absint( $pkwt_settings['reset_password_page_id'] ) : 0,
	)
);

foreach ( $pkwt_page_ids as $pkwt_page_id ) {
	wp_delete_post( $pkwt_page_id, true );
}
// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

// Clean up post meta added by this plugin.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- One-time uninstall cleanup query; meta_key lookup is intentional.
$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => '_pkwt_page_type' ), array( '%s' ) );

delete_option( 'pkwt_settings' );
delete_option( 'pkwt_wizard_complete' );
delete_option( 'pkwt_hide_plugins_list' );
delete_option( 'pkwt_onboarding_redirect' );
delete_transient( 'pkwt_conflict_report' );
delete_transient( 'pkwt_admin_notices' );

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Uninstall script; variables are local to this file.
$pkwt_transient_like = $wpdb->esc_like( '_transient_pkwt_' ) . '%';
$pkwt_option_like    = $wpdb->esc_like( 'pkwt_' ) . '%';
// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- One-time uninstall cleanup queries.
$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $pkwt_transient_like ) );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- One-time uninstall cleanup queries.
$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $pkwt_option_like ) );

if ( is_multisite() ) {
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Uninstall script; variables are local to this file.
	$pkwt_sites = get_sites( array( 'number' => 0 ) );
	foreach ( $pkwt_sites as $pkwt_site ) {
		switch_to_blog( (int) $pkwt_site->blog_id );
		delete_option( 'pkwt_settings' );
		delete_option( 'pkwt_wizard_complete' );
		restore_current_blog();
	}
}
