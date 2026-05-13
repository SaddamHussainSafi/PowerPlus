<?php
/**
 * Admin menu.
 *
 * @package PKWT
 */

namespace PKWT\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_Admin {
	/**
	 * Per-request option cache.
	 *
	 * @var array<string,mixed>
	 */
	private $option_cache = array();

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter( 'admin_body_class', array( $this, 'add_admin_body_class' ) );
		add_filter( 'script_loader_tag', array( $this, 'defer_admin_scripts' ), 10, 3 );
		add_action( 'admin_post_pkwt_toggle_module', array( $this, 'handle_toggle_module' ) );
		add_action( 'admin_post_pkwt_run_login_test', array( $this, 'handle_run_login_test' ) );
		add_action( 'admin_post_pkwt_run_security_scan', array( $this, 'handle_run_security_scan' ) );
		add_action( 'admin_post_pkwt_create_snapshot', array( $this, 'handle_create_snapshot' ) );
		add_action( 'admin_post_pkwt_restore_snapshot', array( $this, 'handle_restore_snapshot' ) );
		add_action( 'admin_post_pkwt_clear_activity_log', array( $this, 'handle_clear_activity_log' ) );
		add_action( 'update_option_pkwt_settings', array( $this, 'capture_change_snapshot' ), 10, 3 );
		add_action( 'update_option_pkwt_dpp_settings', array( $this, 'capture_change_snapshot' ), 10, 3 );
		add_action( 'update_option_pkwt_dpp_svg_settings', array( $this, 'capture_change_snapshot' ), 10, 3 );
		add_action( 'update_option_pkwt_dpp_ghost_settings', array( $this, 'capture_change_snapshot' ), 10, 3 );
		add_action( 'update_option_pkwt_dpp_classic_settings', array( $this, 'capture_change_snapshot' ), 10, 3 );
		add_filter( 'admin_footer_text', array( $this, 'filter_admin_footer_text' ) );
		add_filter( 'update_footer', array( $this, 'filter_admin_footer_version' ), 11 );
	}

	/**
	 * Register admin menu.
	 *
	 * @return void
	 */
	public function register_menu(): void {
		if ( ! $this->current_user_has_access() ) {
			return;
		}

		$settings   = $this->get_cached_option( 'pkwt_settings', array() );
		$name       = __( 'PowerKit', 'powerkit-powerful-tools-for-your-website' );
		$icon       = ! empty( $settings['custom_admin_menu_icon'] ) ? (string) $settings['custom_admin_menu_icon'] : 'dashicons-lock';
		$page_title = __( 'PowerKit - Powerful Tools For Your Website', 'powerkit-powerful-tools-for-your-website' );

		add_menu_page(
			esc_html( $page_title ),
			esc_html( $name ),
			'read',
			'pkwt-settings',
			array( $this, 'render_settings_page' ),
			esc_attr( $icon ),
			58
		);

		$submenu_tabs = array(
			'overview'      => __( 'Overview', 'powerkit-powerful-tools-for-your-website' ),
			'general'       => __( 'General', 'powerkit-powerful-tools-for-your-website' ),
			'templates'     => __( 'Page Templates', 'powerkit-powerful-tools-for-your-website' ),
			'redirects'     => __( 'Redirects', 'powerkit-powerful-tools-for-your-website' ),
			'compatibility' => __( 'Compatibility', 'powerkit-powerful-tools-for-your-website' ),
			'security'      => __( 'Security', 'powerkit-powerful-tools-for-your-website' ),
			'duplicate'     => __( 'Duplicate', 'powerkit-powerful-tools-for-your-website' ),
			'svg-upload'    => __( 'SVG Upload', 'powerkit-powerful-tools-for-your-website' ),
			'ghost-mode'    => __( 'Ghost Mode', 'powerkit-powerful-tools-for-your-website' ),
			'classic-editor'=> __( 'Classic Editor', 'powerkit-powerful-tools-for-your-website' ),
			'import-export' => __( 'Import / Export', 'powerkit-powerful-tools-for-your-website' ),
		);

		foreach ( $submenu_tabs as $tab => $label ) {
			add_submenu_page(
				'pkwt-settings',
				esc_html( $label ),
				esc_html( $label ),
				'read',
				'pkwt-settings-' . $tab,
				array( $this, 'render_settings_page' )
			);
		}
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Hook.
	 *
	 * @return void
	 */
	public function enqueue_assets( string $hook ): void {
		$page = $this->get_query_arg_key( 'page' );
		if ( false === strpos( $hook, 'pkwt-settings' ) && 0 !== strpos( $page, 'pkwt-settings' ) ) {
			return;
		}

		wp_enqueue_style( 'pkwt-admin', PKWT_PLUGIN_URL . 'assets/css/pkwt-admin.css', array(), PKWT_VERSION );
		wp_enqueue_script( 'pkwt-admin', PKWT_PLUGIN_URL . 'assets/js/pkwt-admin.js', array(), PKWT_VERSION, true );
	}

	/**
	 * Defer plugin admin scripts on settings screens.
	 *
	 * @param string $tag    Script tag.
	 * @param string $handle Script handle.
	 * @param string $src    Script source.
	 *
	 * @return string
	 */
	public function defer_admin_scripts( string $tag, string $handle, string $src ): string {
		if ( 'pkwt-admin' !== $handle && 'pkwt-dpp-editor' !== $handle ) {
			return $tag;
		}
		if ( false !== strpos( $tag, ' defer' ) ) {
			return $tag;
		}
		return str_replace( '<script ', '<script defer ', $tag );
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function render_settings_page(): void {
		if ( ! $this->current_user_has_access() ) {
			return;
		}

		$tab_map = array(
			'pkwt-settings'                 => 'overview',
			'pkwt-settings-overview'        => 'overview',
			'pkwt-settings-general'         => 'general',
			'pkwt-settings-templates'       => 'templates',
			'pkwt-settings-redirects'       => 'redirects',
			'pkwt-settings-compatibility'   => 'compatibility',
			'pkwt-settings-security'        => 'security',
			'pkwt-settings-duplicate'       => 'duplicate',
			'pkwt-settings-svg-upload'      => 'svg-upload',
			'pkwt-settings-ghost-mode'      => 'ghost-mode',
			'pkwt-settings-classic-editor'  => 'classic-editor',
			'pkwt-settings-import-export'   => 'import-export',
		);

		$page = $this->get_query_arg_key( 'page', 'pkwt-settings' );
		$tab  = isset( $tab_map[ $page ] ) ? $tab_map[ $page ] : 'overview';

		// Backward compatibility for old links using ?tab=.
		$fallback = $this->get_query_arg_key( 'tab' );
		if ( '' !== $fallback ) {
			if ( in_array( $fallback, array( 'overview', 'general', 'templates', 'redirects', 'compatibility', 'security', 'duplicate', 'svg-upload', 'ghost-mode', 'classic-editor', 'import-export' ), true ) ) {
				$tab = $fallback;
			}
		}

		$settings = $this->get_cached_option( 'pkwt_settings', array() );
		include PKWT_PLUGIN_DIR . 'admin/views/' . $tab . '.php';
	}

	/**
	 * Add helper body class on plugin admin pages.
	 *
	 * @param string $classes Existing classes.
	 *
	 * @return string
	 */
	public function add_admin_body_class( string $classes ): string {
		$page = $this->get_query_arg_key( 'page' );
		if ( 0 === strpos( $page, 'pkwt-settings' ) ) {
			$classes .= ' pkwt-admin-submenu-mode';
		}
		return $classes;
	}

	/**
	 * Render custom footer text on plugin pages.
	 *
	 * @param string $text Existing text.
	 *
	 * @return string
	 */
	public function filter_admin_footer_text( string $text ): string {
		$page = $this->get_query_arg_key( 'page' );
		if ( 0 !== strpos( $page, 'pkwt-settings' ) ) {
			return $text;
		}

		return sprintf(
			/* translators: %s: current year */
			esc_html__( '© %s PowerKit - Powerful Tools For Your Website | Developed by Saddam Hussain Safi', 'powerkit-powerful-tools-for-your-website' ),
			gmdate( 'Y' )
		) . ' | <a href="https://inceptastudio.com/" target="_blank" rel="noopener noreferrer">Incepta Studio</a> | <a href="https://saddamhussain.com.np/" target="_blank" rel="noopener noreferrer">Portfolio</a>';
	}

	/**
	 * Render footer version area text on plugin pages.
	 *
	 * @param string $text Existing version text.
	 *
	 * @return string
	 */
	public function filter_admin_footer_version( string $text ): string {
		$page = $this->get_query_arg_key( 'page' );
		if ( 0 !== strpos( $page, 'pkwt-settings' ) ) {
			return $text;
		}
		return esc_html__( 'PowerKit - Powerful Tools For Your Website', 'powerkit-powerful-tools-for-your-website' );
	}

	/**
	 * Toggle module state from overview quick actions.
	 *
	 * @return void
	 */
	public function handle_toggle_module(): void {
		if ( ! $this->current_user_has_access() ) {
			wp_die( esc_html__( 'Not allowed.', 'powerkit-powerful-tools-for-your-website' ) );
		}

		check_admin_referer( 'pkwt_toggle_module' );

		$module = isset( $_REQUEST['module'] ) ? sanitize_key( wp_unslash( $_REQUEST['module'] ) ) : '';
		$state  = isset( $_REQUEST['state'] ) ? sanitize_key( wp_unslash( $_REQUEST['state'] ) ) : '';
		$value  = ( 'on' === $state ) ? 1 : 0;

		switch ( $module ) {
			case 'auth':
				$settings            = $this->get_cached_option( 'pkwt_settings', array() );
				$settings['enabled'] = $value;
				update_option( 'pkwt_settings', $settings );
				$this->option_cache['pkwt_settings'] = $settings;
				break;
			case 'duplicate':
				$settings            = $this->get_cached_option( 'pkwt_dpp_settings', array() );
				$settings['enabled'] = $value;
				update_option( 'pkwt_dpp_settings', $settings );
				$this->option_cache['pkwt_dpp_settings'] = $settings;
				break;
			case 'svg':
				$settings                       = $this->get_cached_option( 'pkwt_dpp_svg_settings', array() );
				$settings['dpp_svg_enabled']    = $value;
				update_option( 'pkwt_dpp_svg_settings', $settings );
				$this->option_cache['pkwt_dpp_svg_settings'] = $settings;
				break;
			case 'ghost':
				$settings                         = $this->get_cached_option( 'pkwt_dpp_ghost_settings', array() );
				$settings['dpp_ghost_enabled']    = $value;
				update_option( 'pkwt_dpp_ghost_settings', $settings );
				$this->option_cache['pkwt_dpp_ghost_settings'] = $settings;
				break;
			case 'classic':
				$settings                           = $this->get_cached_option( 'pkwt_dpp_classic_settings', array() );
				$settings['dpp_classic_enabled']    = $value;
				update_option( 'pkwt_dpp_classic_settings', $settings );
				$this->option_cache['pkwt_dpp_classic_settings'] = $settings;
				break;
			default:
				wp_safe_redirect( admin_url( 'admin.php?page=pkwt-settings&tab=overview&pkwt_notice=module_error' ) );
				exit;
		}

		$this->increment_stat( 'module_toggles' );
		$this->save_snapshot( 'module_toggle_' . $module );
		$this->append_activity_log(
			'module_toggle',
			$module,
			array(
				'state' => $state,
			)
		);

		wp_safe_redirect( admin_url( 'admin.php?page=pkwt-settings&tab=overview&pkwt_notice=module_saved' ) );
		exit;
	}

	/**
	 * Persist snapshot after module setting changes.
	 *
	 * @param mixed  $old_value Previous value.
	 * @param mixed  $new_value New value.
	 * @param string $option Option name.
	 *
	 * @return void
	 */
	public function capture_change_snapshot( $old_value, $new_value, string $option ): void {
		if ( $old_value === $new_value ) {
			return;
		}

		$this->increment_stat( 'settings_saves' );
		$this->save_snapshot( 'settings_update_' . sanitize_key( $option ) );
		$this->append_activity_log(
			'settings_update',
			$option,
			array(
				'tab' => $this->get_query_arg_key( 'page' ),
			)
		);
	}

	/**
	 * Handle manual snapshot creation.
	 *
	 * @return void
	 */
	public function handle_create_snapshot(): void {
		if ( ! $this->current_user_has_access() ) {
			wp_die( esc_html__( 'Not allowed.', 'powerkit-powerful-tools-for-your-website' ) );
		}

		check_admin_referer( 'pkwt_create_snapshot' );
		$this->save_snapshot( 'manual_snapshot' );
		$this->increment_stat( 'manual_snapshots' );
		$this->append_activity_log( 'snapshot_create', 'manual_snapshot' );
		wp_safe_redirect( admin_url( 'admin.php?page=pkwt-settings&tab=overview&pkwt_notice=snapshot_saved' ) );
		exit;
	}

	/**
	 * Handle rollback to snapshot.
	 *
	 * @return void
	 */
	public function handle_restore_snapshot(): void {
		if ( ! $this->current_user_has_access() ) {
			wp_die( esc_html__( 'Not allowed.', 'powerkit-powerful-tools-for-your-website' ) );
		}

		check_admin_referer( 'pkwt_restore_snapshot' );

		$index     = $this->get_query_arg_int( 'index', -1 );
		$snapshots = $this->get_cached_option( 'pkwt_config_snapshots', array() );
		if ( ! is_array( $snapshots ) || ! isset( $snapshots[ $index ] ) || ! is_array( $snapshots[ $index ] ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=pkwt-settings&tab=overview&pkwt_notice=snapshot_error' ) );
			exit;
		}

		$snapshot = $snapshots[ $index ];
		$data     = isset( $snapshot['settings'] ) && is_array( $snapshot['settings'] ) ? $snapshot['settings'] : array();

		if ( isset( $data['pkwt_settings'] ) && is_array( $data['pkwt_settings'] ) ) {
			update_option( 'pkwt_settings', $data['pkwt_settings'] );
		}
		if ( isset( $data['pkwt_dpp_settings'] ) && is_array( $data['pkwt_dpp_settings'] ) ) {
			update_option( 'pkwt_dpp_settings', $data['pkwt_dpp_settings'] );
		}
		if ( isset( $data['pkwt_dpp_svg_settings'] ) && is_array( $data['pkwt_dpp_svg_settings'] ) ) {
			update_option( 'pkwt_dpp_svg_settings', $data['pkwt_dpp_svg_settings'] );
		}
		if ( isset( $data['pkwt_dpp_ghost_settings'] ) && is_array( $data['pkwt_dpp_ghost_settings'] ) ) {
			update_option( 'pkwt_dpp_ghost_settings', $data['pkwt_dpp_ghost_settings'] );
		}
		if ( isset( $data['pkwt_dpp_classic_settings'] ) && is_array( $data['pkwt_dpp_classic_settings'] ) ) {
			update_option( 'pkwt_dpp_classic_settings', $data['pkwt_dpp_classic_settings'] );
		}

		$this->increment_stat( 'rollbacks' );
		$this->save_snapshot( 'rollback_to_' . $index );
		$this->append_activity_log(
			'snapshot_restore',
			'rollback',
			array(
				'index' => $index,
			)
		);

		wp_safe_redirect( admin_url( 'admin.php?page=pkwt-settings&tab=overview&pkwt_notice=snapshot_restored' ) );
		exit;
	}

	/**
	 * Run login URL connectivity test.
	 *
	 * @return void
	 */
	public function handle_run_login_test(): void {
		if ( ! $this->current_user_has_access() ) {
			wp_die( esc_html__( 'Not allowed.', 'powerkit-powerful-tools-for-your-website' ) );
		}

		check_admin_referer( 'pkwt_run_login_test' );

		$settings = $this->get_cached_option( 'pkwt_settings', array() );
		$url      = '';
		if ( ! empty( $settings['pkwt_custom_login_url'] ) ) {
			$url = esc_url_raw( (string) $settings['pkwt_custom_login_url'] );
		} elseif ( ! empty( $settings['login_page_id'] ) ) {
			$url = get_permalink( absint( $settings['login_page_id'] ) );
		}

		$result = array(
			'time'    => time(),
			'url'     => (string) $url,
			'status'  => 'failed',
			'code'    => 0,
			'message' => __( 'Login URL is not configured.', 'powerkit-powerful-tools-for-your-website' ),
		);

		$notice = 'login_test_failed';
		if ( ! empty( $url ) ) {
			$response = wp_remote_get(
				$url,
				array(
					'timeout'    => 10,
					'redirection'=> 3,
					'sslverify'  => true,
				)
			);
			if ( is_wp_error( $response ) ) {
				$result['message'] = $response->get_error_message();
			} else {
				$code             = (int) wp_remote_retrieve_response_code( $response );
				$result['code']   = $code;
				$result['status'] = ( $code >= 200 && $code < 400 ) ? 'ok' : 'failed';
				$result['message']= ( 'ok' === $result['status'] ) ? __( 'Login URL is reachable.', 'powerkit-powerful-tools-for-your-website' ) : __( 'Login URL returned an unexpected status.', 'powerkit-powerful-tools-for-your-website' );
				$notice           = ( 'ok' === $result['status'] ) ? 'login_test_ok' : 'login_test_failed';
			}
		}

		update_option( 'pkwt_last_login_test', $result, false );
		$this->increment_stat( 'login_tests' );
		$this->append_activity_log(
			'login_test',
			'pkwt_last_login_test',
			array(
				'status' => $result['status'],
				'code'   => $result['code'],
			)
		);

		wp_safe_redirect( admin_url( 'admin.php?page=pkwt-settings&tab=overview&pkwt_notice=' . $notice ) );
		exit;
	}

	/**
	 * Run security configuration scan.
	 *
	 * @return void
	 */
	public function handle_run_security_scan(): void {
		if ( ! $this->current_user_has_access() ) {
			wp_die( esc_html__( 'Not allowed.', 'powerkit-powerful-tools-for-your-website' ) );
		}

		check_admin_referer( 'pkwt_run_security_scan' );

		$pkwt_settings     = $this->get_cached_option( 'pkwt_settings', array() );
		$svg     = $this->get_cached_option( 'pkwt_dpp_svg_settings', array() );
		$ghost   = $this->get_cached_option( 'pkwt_dpp_ghost_settings', array() );
		$classic = $this->get_cached_option( 'pkwt_dpp_classic_settings', array() );

		$checks = array(
			array(
				'key'    => 'rate_limit',
				'label'  => __( 'Rate limiting enabled', 'powerkit-powerful-tools-for-your-website' ),
				'pass'   => ! empty( $pkwt_settings['enable_rate_limiting'] ),
				'fix'    => __( 'Enable rate limiting in Security tab.', 'powerkit-powerful-tools-for-your-website' ),
			),
			array(
				'key'    => 'captcha',
				'label'  => __( 'CAPTCHA configured', 'powerkit-powerful-tools-for-your-website' ),
				'pass'   => ! empty( $pkwt_settings['captcha_provider'] ) && 'none' !== $pkwt_settings['captcha_provider'],
				'fix'    => __( 'Configure CAPTCHA provider and keys.', 'powerkit-powerful-tools-for-your-website' ),
			),
			array(
				'key'    => 'custom_login',
				'label'  => __( 'Custom login URL set', 'powerkit-powerful-tools-for-your-website' ),
				'pass'   => ! empty( $pkwt_settings['pkwt_custom_login_url'] ) || ! empty( $pkwt_settings['login_page_id'] ),
				'fix'    => __( 'Set custom login slug or assign login page.', 'powerkit-powerful-tools-for-your-website' ),
			),
			array(
				'key'    => 'svg_strict',
				'label'  => __( 'SVG strictness hardened', 'powerkit-powerful-tools-for-your-website' ),
				'pass'   => empty( $svg['dpp_svg_enabled'] ) || ( isset( $svg['dpp_svg_strictness'] ) && in_array( $svg['dpp_svg_strictness'], array( 'strict', 'paranoid' ), true ) ),
				'fix'    => __( 'Use Strict or Paranoid SVG sanitization.', 'powerkit-powerful-tools-for-your-website' ),
			),
			array(
				'key'    => 'ghost',
				'label'  => __( 'Ghost mode protection active', 'powerkit-powerful-tools-for-your-website' ),
				'pass'   => ! empty( $ghost['dpp_ghost_enabled'] ),
				'fix'    => __( 'Enable Ghost Mode for endpoint and fingerprint hardening.', 'powerkit-powerful-tools-for-your-website' ),
			),
			array(
				'key'    => 'classic_scope',
				'label'  => __( 'Classic Editor safely configured', 'powerkit-powerful-tools-for-your-website' ),
				'pass'   => empty( $classic['dpp_classic_enabled'] ) || ! empty( $classic['dpp_classic_post_types'] ),
				'fix'    => __( 'Select at least one post type for Classic Editor mode.', 'powerkit-powerful-tools-for-your-website' ),
			),
		);

		$passed = 0;
		foreach ( $checks as $check ) {
			if ( ! empty( $check['pass'] ) ) {
				$passed++;
			}
		}

		$scan = array(
			'time'    => time(),
			'score'   => $passed,
			'total'   => count( $checks ),
			'percent' => (int) round( ( $passed / max( 1, count( $checks ) ) ) * 100 ),
			'checks'  => $checks,
		);

		update_option( 'pkwt_last_security_scan', $scan, false );
		$this->increment_stat( 'security_scans' );
		$this->append_activity_log(
			'security_scan',
			'pkwt_last_security_scan',
			array(
				'score' => $scan['score'] . '/' . $scan['total'],
			)
		);

		wp_safe_redirect( admin_url( 'admin.php?page=pkwt-settings&tab=overview&pkwt_notice=security_scan_done' ) );
		exit;
	}

	/**
	 * Clear activity log.
	 *
	 * @return void
	 */
	public function handle_clear_activity_log(): void {
		if ( ! $this->current_user_has_access() ) {
			wp_die( esc_html__( 'Not allowed.', 'powerkit-powerful-tools-for-your-website' ) );
		}

		check_admin_referer( 'pkwt_clear_activity_log' );
		update_option( 'pkwt_settings_activity_log', array(), false );
		$this->option_cache['pkwt_settings_activity_log'] = array();
		wp_safe_redirect( admin_url( 'admin.php?page=pkwt-settings-security&pkwt_notice=activity_log_cleared' ) );
		exit;
	}

	/**
	 * Save configuration snapshot.
	 *
	 * @param string $reason Snapshot reason.
	 *
	 * @return void
	 */
	private function save_snapshot( string $reason ): void {
		$snapshots = $this->get_cached_option( 'pkwt_config_snapshots', array() );
		if ( ! is_array( $snapshots ) ) {
			$snapshots = array();
		}

		$snapshots[] = array(
			'time'     => time(),
			'user_id'  => get_current_user_id(),
			'reason'   => sanitize_key( $reason ),
			'settings' => $this->collect_snapshot_settings(),
		);

		if ( count( $snapshots ) > 20 ) {
			$snapshots = array_slice( $snapshots, -20 );
		}

		update_option( 'pkwt_config_snapshots', $snapshots, false );
		$this->option_cache['pkwt_config_snapshots'] = $snapshots;
	}

	/**
	 * Collect settings used in snapshots.
	 *
	 * @return array<string,mixed>
	 */
	private function collect_snapshot_settings(): array {
		return array(
			'pkwt_settings'         => $this->get_cached_option( 'pkwt_settings', array() ),
			'pkwt_dpp_settings'         => $this->get_cached_option( 'pkwt_dpp_settings', array() ),
			'pkwt_dpp_svg_settings'     => $this->get_cached_option( 'pkwt_dpp_svg_settings', array() ),
			'pkwt_dpp_ghost_settings'   => $this->get_cached_option( 'pkwt_dpp_ghost_settings', array() ),
			'pkwt_dpp_classic_settings' => $this->get_cached_option( 'pkwt_dpp_classic_settings', array() ),
		);
	}

	/**
	 * Increment dashboard stats.
	 *
	 * @param string $stat Stat key.
	 *
	 * @return void
	 */
	private function increment_stat( string $stat ): void {
		$stats = $this->get_cached_option( 'pkwt_admin_stats', array() );
		if ( ! is_array( $stats ) ) {
			$stats = array();
		}

		$key          = sanitize_key( $stat );
		$stats[ $key ] = isset( $stats[ $key ] ) ? absint( $stats[ $key ] ) + 1 : 1;
		$stats['last_activity'] = time();
		update_option( 'pkwt_admin_stats', $stats, false );
		$this->option_cache['pkwt_admin_stats'] = $stats;
	}

	/**
	 * Verify if current user can access plugin admin.
	 *
	 * @return bool
	 */
	private function current_user_has_access(): bool {
		$settings = $this->get_cached_option( 'pkwt_settings', array() );
		if ( ! empty( $settings['admin_test_mode'] ) && ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}
		$allowed  = isset( $settings['access_roles'] ) && is_array( $settings['access_roles'] ) ? $settings['access_roles'] : array( 'administrator' );
		$user     = wp_get_current_user();
		if ( ! $user || empty( $user->roles ) ) {
			return false;
		}
		foreach ( $user->roles as $role ) {
			if ( in_array( $role, $allowed, true ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Append settings activity log entry.
	 *
	 * @param string               $event   Event name.
	 * @param string               $option  Option key.
	 * @param array<string,string|int> $meta Meta details.
	 *
	 * @return void
	 */
	private function append_activity_log( string $event, string $option = '', array $meta = array() ): void {
		$settings = $this->get_cached_option( 'pkwt_settings', array() );
		if ( isset( $settings['settings_activity_log'] ) && empty( $settings['settings_activity_log'] ) ) {
			return;
		}

		$log = $this->get_cached_option( 'pkwt_settings_activity_log', array() );
		if ( ! is_array( $log ) ) {
			$log = array();
		}

		$user = wp_get_current_user();
		$ip   = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

		$log[] = array(
			'time'   => time(),
			'event'  => sanitize_key( $event ),
			'option' => sanitize_key( $option ),
			'user'   => $user ? sanitize_text_field( (string) $user->user_login ) : '',
			'ip'     => $ip,
			'uri'    => $uri,
			'meta'   => array_map(
				static function ( $value ) {
					return sanitize_text_field( (string) $value );
				},
				$meta
			),
		);

		if ( count( $log ) > 300 ) {
			$log = array_slice( $log, -300 );
		}

		update_option( 'pkwt_settings_activity_log', $log, false );
		$this->option_cache['pkwt_settings_activity_log'] = $log;
	}

	/**
	 * Get option from per-request cache.
	 *
	 * @param string $key     Option key.
	 * @param mixed  $default Default value.
	 *
	 * @return mixed
	 */
	private function get_cached_option( string $key, $default ) {
		if ( array_key_exists( $key, $this->option_cache ) ) {
			return $this->option_cache[ $key ];
		}
		$value                     = get_option( $key, $default );
		$this->option_cache[ $key ] = $value;
		return $value;
	}

	/**
	 * Read a sanitized key-like GET value.
	 *
	 * @param string $key     Query key.
	 * @param string $default Default value.
	 *
	 * @return string
	 */
	private function get_query_arg_key( string $key, string $default = '' ): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only query parsing.
		return isset( $_GET[ $key ] ) ? sanitize_key( wp_unslash( (string) $_GET[ $key ] ) ) : $default;
	}

	/**
	 * Read an integer GET value.
	 *
	 * @param string $key     Query key.
	 * @param int    $default Default value.
	 *
	 * @return int
	 */
	private function get_query_arg_int( string $key, int $default = 0 ): int {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only query parsing.
		return isset( $_GET[ $key ] ) ? absint( $_GET[ $key ] ) : $default;
	}
}
