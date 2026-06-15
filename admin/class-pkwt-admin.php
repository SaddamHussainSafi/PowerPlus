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
		add_action( 'wp_ajax_pkwt_dash_save', array( $this, 'handle_dash_save' ) );
		add_action( 'wp_ajax_pkwt_install_elementor', array( $this, 'handle_install_elementor' ) );
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
		$name       = __( 'PowerPlus', 'powerplus-toolkit' );
		// The real PowerPlus P-bolt logo, vectorized, as a monochrome SVG data URI.
		// WP renders data-URI SVG menu icons at 20px and recolors them via svg-painter.js
		// to match the admin colour scheme (grey at rest, white when active/hovered).
		// A plain PNG URL would render as an unconstrained <img> and overflow the menu slot.
		$icon_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><g transform="scale(0.019692) translate(0.000000,1625.000000) scale(0.100000,-0.100000)" fill="#a7aaad"><path d="M5827 14393 c-3 -5 -743 -1974 -1645 -4378 -902 -2403 -1954 -5205 -2337 -6225 -383 -1020 -703 -1872 -711 -1892 l-13 -38 1667 0 1667 1 868 1477 c477 812 1388 2364 2026 3447 l1158 1970 -1068 5 -1068 5 2687 2749 c2571 2631 2759 2827 2762 2879 0 9 -5987 9 -5993 0z"/><path d="M11348 12030 c-698 -1193 -1268 -2171 -1268 -2174 0 -4 478 -6 1062 -6 l1062 0 -80 -82 c-1242 -1285 -5152 -5283 -5166 -5282 -10 0 -18 -3 -18 -8 0 -4 1147 -8 2549 -8 l2549 0 1541 2683 c848 1475 1541 2687 1542 2692 0 12 -2493 4355 -2501 4355 -3 0 -575 -976 -1272 -2170z"/></g></svg>';
		$icon       = 'data:image/svg+xml;base64,' . base64_encode( $icon_svg ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Inlining a static SVG icon, the documented WP pattern for menu icons.
		$page_title = __( 'PowerPlus — All-in-One Powerful Toolkit', 'powerplus-toolkit' );

		add_menu_page(
			esc_html( $page_title ),
			esc_html( $name ),
			'read',
			'pkwt-settings',
			array( $this, 'render_settings_page' ),
			esc_attr( $icon ),
			58
		);

		// Register the modern React dashboard as the first submenu item.
		add_submenu_page(
			'pkwt-settings',
			esc_html__( 'Dashboard', 'powerplus-toolkit' ),
			'✦ ' . esc_html__( 'Dashboard', 'powerplus-toolkit' ),
			'read',
			'pkwt-settings',
			array( $this, 'render_settings_page' )
		);

		// All sub-pages are handled inside the React SPA — no WP submenu items needed.
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

		// React dashboard loads on every PowerPlus admin page. Everything is bundled
		// locally — no external CDNs, no in-browser Babel (WP.org compliant).
		$is_dashboard = 0 === strpos( $page, 'pkwt-settings' );
		if ( $is_dashboard ) {
			// The Branding page uses the media picker to choose a login logo.
			wp_enqueue_media();
			// Compiled Tailwind utilities (preflight disabled, scoped to #pkwt-dashboard-root)
			// plus the hand-written dashboard theme CSS.
			wp_enqueue_style( 'pkwt-tailwind', PKWT_PLUGIN_URL . 'assets/css/pkwt-tailwind.css', array(), PKWT_VERSION );
			wp_enqueue_style( 'pkwt-dashboard', PKWT_PLUGIN_URL . 'assets/css/pkwt-dashboard.css', array( 'pkwt-tailwind' ), PKWT_VERSION );

			// Vendored React 18 production builds (UMD globals window.React / window.ReactDOM).
			wp_enqueue_script( 'pkwt-react',     PKWT_PLUGIN_URL . 'assets/vendor/react.min.js',     array(),               '18.3.1', true );
			wp_enqueue_script( 'pkwt-react-dom', PKWT_PLUGIN_URL . 'assets/vendor/react-dom.min.js', array( 'pkwt-react' ), '18.3.1', true );

			// Precompiled dashboard app (JSX already transpiled to plain JS at build time).
			wp_enqueue_script( 'pkwt-dashboard', PKWT_PLUGIN_URL . 'assets/js/pkwt-dashboard.min.js', array( 'pkwt-react', 'pkwt-react-dom' ), PKWT_VERSION, true );
		}

		if ( false !== strpos( $hook, 'pkwt-settings-templates' ) || 'pkwt-settings-templates' === $page ) {
			wp_enqueue_style( 'pkwt-templates', PKWT_PLUGIN_URL . 'assets/css/pkwt-templates.css', array(), PKWT_VERSION );
			wp_enqueue_script( 'pkwt-templates', PKWT_PLUGIN_URL . 'assets/js/pkwt-templates.js', array(), PKWT_VERSION, true );
			wp_localize_script(
				'pkwt-templates',
				'pkwtTemplatesData',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'i18n'    => array(
						'importing'        => __( 'Importing…', 'powerplus-toolkit' ),
						'imported'         => __( 'Imported!', 'powerplus-toolkit' ),
						'openElementor'    => __( 'Open in Elementor →', 'powerplus-toolkit' ),
						'viewPage'         => __( 'View Page →', 'powerplus-toolkit' ),
						'importFailed'     => __( 'Import failed.', 'powerplus-toolkit' ),
						'sessionExpired'   => __( 'Session expired — please refresh the page.', 'powerplus-toolkit' ),
						'importFailedRetry'=> __( 'Import failed. Please try again.', 'powerplus-toolkit' ),
					),
				)
			);
		}
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
	 * AJAX: save settings from the React dashboard.
	 *
	 * POST: nonce (pkwt_dashboard_nonce), settings (JSON object of changed fields).
	 * Only the provided fields are changed — sanitize_settings() merges the rest
	 * from the currently saved option.
	 *
	 * @return void
	 */
	public function handle_dash_save(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'powerplus-toolkit' ) ) );
		}

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'pkwt_dashboard_nonce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed. Please reload the page.', 'powerplus-toolkit' ) ) );
		}

		$raw = isset( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON decoded and sanitized below.
		$patch = json_decode( (string) $raw, true );
		if ( ! is_array( $patch ) || empty( $patch ) ) {
			wp_send_json_error( array( 'message' => __( 'No settings provided.', 'powerplus-toolkit' ) ) );
		}

		$group = isset( $_POST['group'] ) ? sanitize_key( wp_unslash( $_POST['group'] ) ) : 'core';

		// ── Module option groups (booleans/ints only, merged into the saved option) ──
		$module_groups = array(
			'ghost'      => array(
				'option' => 'pkwt_dpp_ghost_settings',
				'bool'   => array( 'dpp_ghost_enabled', 'dpp_ghost_remove_generator', 'dpp_ghost_strip_version_urls', 'dpp_ghost_remove_emoji', 'dpp_ghost_disable_xmlrpc', 'dpp_ghost_hide_rest_users', 'dpp_ghost_disable_author_archives' ),
				'int'    => array(),
				'text'   => array( 'dpp_ghost_custom_cms_name' ),
			),
			'svg'        => array(
				'option' => 'pkwt_dpp_svg_settings',
				'bool'   => array( 'dpp_svg_enabled', 'dpp_svg_preview', 'dpp_svg_blocked_log' ),
				'int'    => array( 'dpp_svg_max_size_kb' ),
				'text'   => array(),
			),
			'classic'    => array(
				'option' => 'pkwt_dpp_classic_settings',
				'bool'   => array( 'dpp_classic_enabled', 'dpp_classic_allow_user_choice', 'dpp_classic_allow_admin_bypass' ),
				'int'    => array(),
				'text'   => array(),
			),
			'duplicator' => array(
				'option' => 'pkwt_dpp_settings',
				'bool'   => array( 'enabled', 'enable_row_action', 'enable_elementor_button' ),
				'int'    => array(),
				'text'   => array( 'title_suffix' ),
			),
		);

		if ( isset( $module_groups[ $group ] ) ) {
			$spec    = $module_groups[ $group ];
			$current = get_option( $spec['option'], array() );
			$current = is_array( $current ) ? $current : array();
			// Min/max clamps for specific integer fields (mirror the legacy sanitizers).
			$int_clamps = array( 'dpp_svg_max_size_kb' => array( 64, 4096 ) );
			$changed    = false;
			foreach ( $patch as $key => $value ) {
				if ( in_array( $key, $spec['bool'], true ) ) {
					$current[ $key ] = empty( $value ) ? 0 : 1;
					$changed = true;
				} elseif ( in_array( $key, $spec['int'], true ) ) {
					$int = absint( $value );
					if ( isset( $int_clamps[ $key ] ) ) {
						$int = max( $int_clamps[ $key ][0], min( $int_clamps[ $key ][1], $int ) );
					}
					$current[ $key ] = $int;
					$changed = true;
				} elseif ( in_array( $key, $spec['text'], true ) ) {
					$current[ $key ] = sanitize_text_field( (string) $value );
					$changed = true;
				}
			}
			if ( ! $changed ) {
				wp_send_json_error( array( 'message' => __( 'No valid settings provided.', 'powerplus-toolkit' ) ) );
			}
			update_option( $spec['option'], $current );
			wp_send_json_success( array(
				'message'  => __( 'Settings saved.', 'powerplus-toolkit' ),
				'settings' => $current,
				'group'    => $group,
			) );
		}

		// ── Core pkwt_settings ──
		$allowed = array(
			'enabled', 'woocommerce_mode', 'enable_rate_limiting', 'hide_plugins_list',
			'block_default_wp_auth', 'security_dashboard_enabled', 'settings_activity_log',
			'admin_test_mode', 'auto_update_all_plugins', 'login_page_id', 'register_page_id', 'lost_password_page_id',
			'reset_password_page_id', 'after_login_redirect', 'after_login_redirect_page_id',
			'pkwt_custom_login_url', 'max_attempts', 'lockout_minutes', 'captcha_provider',
			'recaptcha_site_key', 'recaptcha_secret_key', 'hcaptcha_site_key', 'hcaptcha_secret_key',
			'plugin_menu_name', 'plugin_description', 'support_url', 'role_redirects', 'ip_allowlist',
			'branding',
		);
		$patch = array_intersect_key( $patch, array_flip( $allowed ) );

		// A blank CAPTCHA secret means "unchanged" (the field is never preloaded with the
		// real secret), so never let an empty value wipe a stored key.
		foreach ( array( 'recaptcha_secret_key', 'hcaptcha_secret_key' ) as $secret ) {
			if ( isset( $patch[ $secret ] ) && '' === trim( (string) $patch[ $secret ] ) ) {
				unset( $patch[ $secret ] );
			}
		}

		if ( empty( $patch ) ) {
			wp_send_json_error( array( 'message' => __( 'No valid settings provided.', 'powerplus-toolkit' ) ) );
		}

		// sanitize_settings() wp_unslash()es each field; the JSON payload was already
		// unslashed before json_decode(), so re-slash here to keep the two balanced and
		// avoid stripping legitimate backslashes from values.
		$patch = wp_slash( $patch );

		$sanitizer = new Class_PKWT_Settings();
		$sanitized = $sanitizer->sanitize_settings( $patch );

		update_option( 'pkwt_settings', $sanitized );
		wp_cache_delete( 'settings', 'pkwt_options' );
		$this->option_cache = array();

		// Don't echo secrets back to the client.
		foreach ( array( 'recaptcha_secret_key', 'hcaptcha_secret_key' ) as $secret ) {
			if ( isset( $sanitized[ $secret ] ) ) {
				$sanitized[ $secret . '_set' ] = ( '' !== (string) $sanitized[ $secret ] );
				$sanitized[ $secret ]          = '';
			}
		}

		wp_send_json_success( array(
			'message'  => __( 'Settings saved.', 'powerplus-toolkit' ),
			'settings' => $sanitized,
		) );
	}

	/**
	 * AJAX: install and activate the latest Elementor from wordpress.org in one click.
	 *
	 * @return void
	 */
	public function handle_install_elementor(): void {
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to install plugins.', 'powerplus-toolkit' ) ) );
		}
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'pkwt_dashboard_nonce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed. Please reload the page.', 'powerplus-toolkit' ) ) );
		}

		$basename = 'elementor/elementor.php';

		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		// Already active?
		if ( is_plugin_active( $basename ) ) {
			wp_send_json_success( array( 'message' => __( 'Elementor is already active.', 'powerplus-toolkit' ), 'active' => true ) );
		}

		// Installed but inactive — just activate.
		if ( file_exists( WP_PLUGIN_DIR . '/' . $basename ) ) {
			$activated = activate_plugin( $basename );
			if ( is_wp_error( $activated ) ) {
				wp_send_json_error( array( 'message' => $activated->get_error_message() ) );
			}
			wp_send_json_success( array( 'message' => __( 'Elementor activated.', 'powerplus-toolkit' ), 'active' => true ) );
		}

		// Not installed — download + install from the wp.org repository, then activate.
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/misc.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

		$api = plugins_api( 'plugin_information', array( 'slug' => 'elementor', 'fields' => array( 'sections' => false ) ) );
		if ( is_wp_error( $api ) ) {
			wp_send_json_error( array( 'message' => __( 'Could not reach the WordPress.org plugin directory.', 'powerplus-toolkit' ) ) );
		}

		$skin     = new \WP_Ajax_Upgrader_Skin();
		$upgrader = new \Plugin_Upgrader( $skin );
		$result   = $upgrader->install( $api->download_link );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}
		if ( is_wp_error( $skin->result ) ) {
			wp_send_json_error( array( 'message' => $skin->result->get_error_message() ) );
		}
		if ( ! $result ) {
			$errors = $skin->get_errors();
			$msg    = ( is_wp_error( $errors ) && $errors->has_errors() ) ? $errors->get_error_message() : __( 'Elementor installation failed.', 'powerplus-toolkit' );
			wp_send_json_error( array( 'message' => $msg ) );
		}

		$activated = activate_plugin( $basename );
		if ( is_wp_error( $activated ) ) {
			wp_send_json_error( array( 'message' => __( 'Elementor installed but could not be activated automatically.', 'powerplus-toolkit' ) ) );
		}

		wp_send_json_success( array( 'message' => __( 'Elementor installed and activated.', 'powerplus-toolkit' ), 'active' => true ) );
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

		// All routes are handled by the React SPA.
		include PKWT_PLUGIN_DIR . 'admin/views/powerplus-ui.php';
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
			esc_html__( '© %s PowerPlus — All-in-One Powerful Toolkit | Developed by Saddam Hussain Safi', 'powerplus-toolkit' ),
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
		return esc_html__( 'PowerPlus — All-in-One Powerful Toolkit', 'powerplus-toolkit' );
	}

	/**
	 * Toggle module state from overview quick actions.
	 *
	 * @return void
	 */
	public function handle_toggle_module(): void {
		// Configuration changes require full admin capability, not merely dashboard view access.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'powerplus-toolkit' ) );
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
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'powerplus-toolkit' ) );
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
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'powerplus-toolkit' ) );
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
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'powerplus-toolkit' ) );
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
			'message' => __( 'Login URL is not configured.', 'powerplus-toolkit' ),
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
				$result['message']= ( 'ok' === $result['status'] ) ? __( 'Login URL is reachable.', 'powerplus-toolkit' ) : __( 'Login URL returned an unexpected status.', 'powerplus-toolkit' );
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
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'powerplus-toolkit' ) );
		}

		check_admin_referer( 'pkwt_run_security_scan' );

		$pkwt_settings     = $this->get_cached_option( 'pkwt_settings', array() );
		$svg     = $this->get_cached_option( 'pkwt_dpp_svg_settings', array() );
		$ghost   = $this->get_cached_option( 'pkwt_dpp_ghost_settings', array() );
		$classic = $this->get_cached_option( 'pkwt_dpp_classic_settings', array() );

		$checks = array(
			array(
				'key'    => 'rate_limit',
				'label'  => __( 'Rate limiting enabled', 'powerplus-toolkit' ),
				'pass'   => ! empty( $pkwt_settings['enable_rate_limiting'] ),
				'fix'    => __( 'Enable rate limiting in Security tab.', 'powerplus-toolkit' ),
			),
			array(
				'key'    => 'captcha',
				'label'  => __( 'CAPTCHA configured', 'powerplus-toolkit' ),
				'pass'   => ! empty( $pkwt_settings['captcha_provider'] ) && 'none' !== $pkwt_settings['captcha_provider'],
				'fix'    => __( 'Configure CAPTCHA provider and keys.', 'powerplus-toolkit' ),
			),
			array(
				'key'    => 'custom_login',
				'label'  => __( 'Custom login URL set', 'powerplus-toolkit' ),
				'pass'   => ! empty( $pkwt_settings['pkwt_custom_login_url'] ) || ! empty( $pkwt_settings['login_page_id'] ),
				'fix'    => __( 'Set custom login slug or assign login page.', 'powerplus-toolkit' ),
			),
			array(
				'key'    => 'svg_strict',
				'label'  => __( 'SVG strictness hardened', 'powerplus-toolkit' ),
				'pass'   => empty( $svg['dpp_svg_enabled'] ) || ( isset( $svg['dpp_svg_strictness'] ) && in_array( $svg['dpp_svg_strictness'], array( 'strict', 'paranoid' ), true ) ),
				'fix'    => __( 'Use Strict or Paranoid SVG sanitization.', 'powerplus-toolkit' ),
			),
			array(
				'key'    => 'ghost',
				'label'  => __( 'Ghost mode protection active', 'powerplus-toolkit' ),
				'pass'   => ! empty( $ghost['dpp_ghost_enabled'] ),
				'fix'    => __( 'Enable Ghost Mode for endpoint and fingerprint hardening.', 'powerplus-toolkit' ),
			),
			array(
				'key'    => 'classic_scope',
				'label'  => __( 'Classic Editor safely configured', 'powerplus-toolkit' ),
				'pass'   => empty( $classic['dpp_classic_enabled'] ) || ! empty( $classic['dpp_classic_post_types'] ),
				'fix'    => __( 'Select at least one post type for Classic Editor mode.', 'powerplus-toolkit' ),
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
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'powerplus-toolkit' ) );
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
