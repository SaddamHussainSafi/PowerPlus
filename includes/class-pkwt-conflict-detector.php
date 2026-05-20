<?php
/**
 * Conflict detector.
 *
 * @package PKWT
 */

namespace PKWT\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_Conflict_Detector {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_init', array( $this, 'maybe_scan' ) );
		add_action( 'admin_notices', array( $this, 'render_notices' ) );
		add_action( 'activated_plugin', array( $this, 'flush_transient' ) );
		add_action( 'deactivated_plugin', array( $this, 'flush_transient' ) );
	}

	/**
	 * Flush conflict transient so the next scan picks up fresh plugin state.
	 *
	 * @return void
	 */
	public function flush_transient(): void {
		delete_transient( 'pkwt_conflict_report' );
	}

	/**
	 * Run scan every 12h.
	 *
	 * @return void
	 */
	public function maybe_scan(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings     = get_option( 'pkwt_settings', array() );
		$page_manager = new Class_PKWT_Page_Manager();
		$pages        = $page_manager->ensure_default_pages();
		$key_map      = array(
			'login'         => 'login_page_id',
			'register'      => 'register_page_id',
			'lost_password' => 'lost_password_page_id',
			'reset_password'=> 'reset_password_page_id',
		);
		$updated_settings = $settings;
		$has_updates      = false;
		foreach ( $key_map as $type => $setting_key ) {
			$current_id = isset( $settings[ $setting_key ] ) ? absint( $settings[ $setting_key ] ) : 0;
			$new_id     = isset( $pages[ $type ] ) ? absint( $pages[ $type ] ) : 0;
			if ( $current_id <= 0 && $new_id > 0 ) {
				$updated_settings[ $setting_key ] = $new_id;
				$has_updates = true;
			}
		}
		if ( $has_updates ) {
			update_option( 'pkwt_settings', $updated_settings );
			$settings = $updated_settings;
			delete_transient( 'pkwt_conflict_report' );
		}

		if ( get_transient( 'pkwt_conflict_report' ) ) {
			return;
		}

		$report = array(
			'issues'      => array(),
			'scanned_at'  => current_time( 'mysql' ),
		);

		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			$report['issues'][] = __( 'Elementor is not active. PowerKit widgets are disabled.', 'powerplus-toolkit' );
		} elseif ( version_compare( ELEMENTOR_VERSION, PKWT_MIN_ELEMENTOR, '<' ) ) {
			$report['issues'][] = __( 'Elementor version is below 3.5.0.', 'powerplus-toolkit' );
		}

			foreach ( array( 'login_page_id', 'register_page_id', 'lost_password_page_id' ) as $key ) {
				$page_id = isset( $settings[ $key ] ) ? absint( $settings[ $key ] ) : 0;
				if ( $page_id <= 0 || 'publish' !== get_post_status( $page_id ) ) {
					/* translators: %s: required auth page setting key. */
					$report['issues'][] = sprintf( __( 'Required auth page missing: %s.', 'powerplus-toolkit' ), $key );
				}
			}

		if ( function_exists( 'WC' ) && empty( $settings['woocommerce_mode'] ) ) {
			$report['issues'][] = __( 'WooCommerce is active but WooCommerce mode is disabled.', 'powerplus-toolkit' );
		}

		set_transient( 'pkwt_conflict_report', $report, 12 * HOUR_IN_SECONDS );
	}

	/**
	 * Output admin notices.
	 *
	 * @return void
	 */
	public function render_notices(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$report = get_transient( 'pkwt_conflict_report' );
		if ( ! is_array( $report ) || empty( $report['issues'] ) ) {
			return;
		}

		// Always filter out stale Elementor notices: if Elementor is now active, remove that issue in real time.
		$issues = array_filter(
			$report['issues'],
			static function ( $issue ) {
				if ( defined( 'ELEMENTOR_VERSION' ) && false !== strpos( $issue, 'Elementor is not active' ) ) {
					return false;
				}
				return true;
			}
		);

		if ( empty( $issues ) ) {
			return;
		}

		echo '<div class="notice notice-warning is-dismissible"><p><strong>' . esc_html__( 'PowerKit Conflict Report', 'powerplus-toolkit' ) . '</strong></p><ul>';
		foreach ( $issues as $issue ) {
			echo '<li>' . esc_html( $issue ) . '</li>';
		}
		echo '</ul></div>';
	}
}
