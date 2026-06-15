<?php
/**
 * Ghost mode feature.
 *
 * @package PKWT
 */

namespace PKWT\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_DPP_Ghost {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'init', array( $this, 'bootstrap_ghost_mode' ), 1 );
		add_action( 'init', array( $this, 'maybe_serve_aliased_asset' ), 0 );
		add_action( 'template_redirect', array( $this, 'start_output_masking' ), 0 );
		add_filter( 'the_generator', array( $this, 'filter_generator' ) );
		add_filter( 'script_loader_src', array( $this, 'strip_version_param' ), 99 );
		add_filter( 'style_loader_src', array( $this, 'strip_version_param' ), 99 );
		add_filter( 'xmlrpc_enabled', array( $this, 'disable_xmlrpc' ) );
		add_filter( 'rest_authentication_errors', array( $this, 'block_rest_users_endpoint' ) );
		add_filter( 'rest_endpoints', array( $this, 'remove_rest_users_routes' ) );
		add_filter( 'rest_url_prefix', array( $this, 'filter_rest_prefix' ) );
		add_filter( 'body_class', array( $this, 'strip_wordpress_body_classes' ) );
		add_filter( 'all_plugins', array( $this, 'mask_plugin_names_admin' ) );
		add_action( 'template_redirect', array( $this, 'maybe_block_author_archives' ), 1 );
		add_action( 'template_redirect', array( $this, 'maybe_block_probe_requests' ), 1 );
		add_action( 'send_headers', array( $this, 'cleanup_headers' ) );
		add_action( 'admin_post_pkwt_ghost_test', array( $this, 'run_detection_test' ) );
		add_action( 'pkwt_dpp_ghost_weekly_test', array( $this, 'run_weekly_auto_test' ) );
		add_action( 'update_option_pkwt_dpp_ghost_settings', array( $this, 'maybe_reschedule_test_cron' ), 10, 2 );
		add_filter( 'cron_schedules', array( $this, 'register_weekly_schedule' ) );
	}

	/**
	 * Register weekly schedule if missing.
	 *
	 * @param array<string,array<string,mixed>> $schedules Schedules.
	 * @return array<string,array<string,mixed>>
	 */
	public function register_weekly_schedule( array $schedules ): array {
		if ( ! isset( $schedules['weekly'] ) ) {
			$schedules['weekly'] = array(
				'interval' => WEEK_IN_SECONDS,
				'display'  => __( 'Once Weekly', 'powerplus-toolkit' ),
			);
		}
		return $schedules;
	}

	/**
	 * Get settings.
	 *
	 * @return array<string,mixed>
	 */
	private function get_settings(): array {
		$defaults = array(
			'dpp_ghost_enabled'                  => 0,
			'dpp_ghost_custom_cms_name'          => 'Nebula Runtime',
			'dpp_ghost_remove_generator'         => 1,
			'dpp_ghost_strip_version_urls'       => 1,
			'dpp_ghost_remove_rsd'               => 1,
			'dpp_ghost_remove_wlw'               => 1,
			'dpp_ghost_remove_shortlink'         => 1,
			'dpp_ghost_remove_feed_links'        => 1,
			'dpp_ghost_remove_emoji'             => 1,
			'dpp_ghost_remove_oembed'            => 1,
			'dpp_ghost_remove_rest_link'         => 1,
			'dpp_ghost_disable_author_archives'  => 1,
			'dpp_ghost_disable_xmlrpc'           => 1,
			'dpp_ghost_hide_rest_users'          => 1,
			'dpp_ghost_rest_prefix'              => '',
			'dpp_ghost_alias_content'            => 'assets',
			'dpp_ghost_alias_includes'           => 'core',
			'dpp_ghost_alias_themes'             => 'designs',
			'dpp_ghost_alias_plugins'            => 'extensions',
			'dpp_ghost_mask_plugin_names'        => 0,
			'dpp_ghost_plugin_name_map'          => array(),
			'dpp_ghost_block_probes'             => 1,
			'dpp_ghost_auto_test'                => 0,
			'dpp_ghost_auto_test_threshold'      => 8,
		);
		$saved = get_option( 'pkwt_dpp_ghost_settings', array() );
		return wp_parse_args( is_array( $saved ) ? $saved : array(), $defaults );
	}

	/**
	 * Check enabled.
	 *
	 * @return bool
	 */
	private function is_enabled(): bool {
		$settings = $this->get_settings();
		return ! empty( $settings['dpp_ghost_enabled'] );
	}

	/**
	 * Apply head cleanups.
	 *
	 * @return void
	 */
	public function bootstrap_ghost_mode(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}
		$s = $this->get_settings();

		if ( ! empty( $s['dpp_ghost_remove_rsd'] ) ) {
			remove_action( 'wp_head', 'rsd_link' );
		}
		if ( ! empty( $s['dpp_ghost_remove_wlw'] ) ) {
			remove_action( 'wp_head', 'wlwmanifest_link' );
		}
		if ( ! empty( $s['dpp_ghost_remove_shortlink'] ) ) {
			remove_action( 'wp_head', 'wp_shortlink_wp_head' );
		}
		if ( ! empty( $s['dpp_ghost_remove_feed_links'] ) ) {
			remove_action( 'wp_head', 'feed_links', 2 );
			remove_action( 'wp_head', 'feed_links_extra', 3 );
		}
		if ( ! empty( $s['dpp_ghost_remove_emoji'] ) ) {
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
		}
		if ( ! empty( $s['dpp_ghost_remove_oembed'] ) ) {
			remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		}
		if ( ! empty( $s['dpp_ghost_remove_rest_link'] ) ) {
			remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
		}
	}

	/**
	 * Filter generator output.
	 *
	 * @param string $generator Generator.
	 * @return string
	 */
	public function filter_generator( string $generator ): string {
		if ( ! $this->is_enabled() ) {
			return $generator;
		}
		$s = $this->get_settings();
		if ( empty( $s['dpp_ghost_remove_generator'] ) ) {
			return $generator;
		}
		$custom = isset( $s['dpp_ghost_custom_cms_name'] ) ? sanitize_text_field( (string) $s['dpp_ghost_custom_cms_name'] ) : '';
		return '' === $custom ? '' : '<meta name="generator" content="' . esc_attr( $custom ) . '" />';
	}

	/**
	 * Strip version from asset URLs.
	 *
	 * @param string $src URL.
	 * @return string
	 */
	public function strip_version_param( string $src ): string {
		if ( ! $this->is_enabled() ) {
			return $src;
		}
		$s = $this->get_settings();
		if ( empty( $s['dpp_ghost_strip_version_urls'] ) ) {
			return $src;
		}
		$masked = remove_query_arg( 'ver', $src );
		$salt   = gmdate( 'Ym' );
		$masked = add_query_arg( 'v', substr( md5( home_url() . $salt ), 0, 8 ), $masked );
		return $this->mask_asset_url( $masked );
	}

	/**
	 * Start output masking for frontend.
	 *
	 * @return void
	 */
	public function start_output_masking(): void {
		if ( ! $this->is_enabled() || is_admin() ) {
			return;
		}
		ob_start( array( $this, 'mask_output_html' ) );
		// Ensure the buffer is always closed cleanly on shutdown to prevent buffer stack misalignment.
		add_action( 'shutdown', array( $this, 'end_output_masking' ), 0 );
	}

	/**
	 * Close output masking buffer on shutdown.
	 *
	 * Called via the 'shutdown' action to ensure ob_start() is always paired
	 * with a matching ob_end_flush() within the same logical flow.
	 *
	 * @return void
	 */
	public function end_output_masking(): void {
		if ( ob_get_level() > 0 && ob_get_length() !== false ) {
			ob_end_flush();
		}
	}

	/**
	 * Mask WordPress path fingerprints in HTML output.
	 *
	 * @param string $html HTML.
	 * @return string
	 */
	public function mask_output_html( string $html ): string {
		if ( '' === $html ) {
			return $html;
		}
		$aliases = $this->get_aliases();
		$map     = $this->get_plugin_path_alias_map();
		$find    = array(
			'/wp-content/plugins/',
			'/wp-content/themes/',
			'/wp-content/',
			'/wp-includes/',
		);
		$replace = array(
			'/' . $aliases['plugins'] . '/',
			'/' . $aliases['themes'] . '/',
			'/' . $aliases['content'] . '/',
			'/' . $aliases['includes'] . '/',
		);
		$html = str_replace( $find, $replace, $html );
		if ( ! empty( $map ) ) {
			foreach ( $map as $plugin_slug => $alias_slug ) {
				$html = str_replace(
					'/' . $aliases['plugins'] . '/' . $plugin_slug . '/',
					'/' . $aliases['plugins'] . '/' . $alias_slug . '/',
					$html
				);
				$html = str_replace(
					'\\/' . $aliases['plugins'] . '\\/' . $plugin_slug . '\\/',
					'\\/' . $aliases['plugins'] . '\\/' . $alias_slug . '\\/',
					$html
				);
			}
		}
		return $html;
	}

	/**
	 * Map and serve masked asset URLs.
	 *
	 * @return void
	 */
	public function maybe_serve_aliased_asset(): void {
		if ( ! $this->is_enabled() || is_admin() ) {
			return;
		}
		$uri_path = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
		$path     = trim( $uri_path, '/' );
		if ( '' === $path ) {
			return;
		}

		$aliases = $this->get_aliases();
		$map     = array(
			$aliases['plugins']  => 'wp-content/plugins',
			$aliases['themes']   => 'wp-content/themes',
			$aliases['content']  => 'wp-content',
			$aliases['includes'] => 'wp-includes',
		);

		// Only ever serve known static asset types — never PHP or config files.
		$allowed_ext = array(
			'css', 'js', 'mjs', 'map', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'avif',
			'svg', 'ico', 'woff', 'woff2', 'ttf', 'otf', 'eot', 'mp4', 'webm', 'ogg',
			'mp3', 'wav', 'json', 'txt', 'pdf',
		);

		foreach ( $map as $alias => $real_base ) {
			if ( '' === $alias ) {
				continue;
			}
			$prefix = $alias . '/';
			if ( 0 !== strpos( $path, $prefix ) ) {
				continue;
			}
			$relative = substr( $path, strlen( $prefix ) );

			// Reject path-traversal sequences and null bytes BEFORE building the path.
			// Without this, '/core/../wp-config.php' would resolve back inside ABSPATH
			// and leak sensitive files through readfile().
			if ( false !== strpos( $relative, '..' ) || false !== strpos( $relative, "\0" ) ) {
				continue;
			}

			if ( $aliases['plugins'] === $alias ) {
				$relative = $this->restore_real_plugin_path_from_alias( $relative );
				if ( false !== strpos( $relative, '..' ) || false !== strpos( $relative, "\0" ) ) {
					continue;
				}
			}

			// Enforce a static-asset extension allowlist; this alone blocks .php/.htaccess/config reads.
			$ext = strtolower( pathinfo( $relative, PATHINFO_EXTENSION ) );
			if ( '' === $ext || ! in_array( $ext, $allowed_ext, true ) ) {
				continue;
			}

			// Use trailingslashit() to safely build path without raw ABSPATH concatenation.
			$target   = trailingslashit( ABSPATH ) . $real_base . '/' . ltrim( $relative, '/' );
			$realpath = realpath( $target );
			if ( ! $realpath || ! is_file( $realpath ) ) {
				continue;
			}

			// Resolved file MUST live under the SPECIFIC mapped base directory — not merely
			// anywhere inside ABSPATH — so it cannot reach wp-config.php or sibling roots.
			$base_real = realpath( trailingslashit( ABSPATH ) . $real_base );
			if ( ! $base_real || 0 !== strpos( $realpath, trailingslashit( $base_real ) ) ) {
				continue;
			}

			// Belt-and-braces: WP's own filetype check must also resolve to a permitted ext.
			$filetype = wp_check_filetype( $realpath, null );
			$ft_ext   = isset( $filetype['ext'] ) ? strtolower( (string) $filetype['ext'] ) : '';
			if ( '' === $ft_ext || ! in_array( $ft_ext, $allowed_ext, true ) ) {
				continue;
			}

			if ( ! headers_sent() ) {
				header( 'Content-Type: ' . ( ! empty( $filetype['type'] ) ? $filetype['type'] : 'application/octet-stream' ) );
				header( 'X-Content-Type-Options: nosniff' );
				header( 'Cache-Control: public, max-age=86400' );
			}
			readfile( $realpath ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
			exit;
		}
	}

	/**
	 * Replace default WP paths in one URL.
	 *
	 * @param string $src URL.
	 * @return string
	 */
	private function mask_asset_url( string $src ): string {
		$aliases = $this->get_aliases();
		$src     = str_replace( '/wp-content/plugins/', '/' . $aliases['plugins'] . '/', $src );
		$src     = str_replace( '/wp-content/themes/', '/' . $aliases['themes'] . '/', $src );
		$src     = str_replace( '/wp-content/', '/' . $aliases['content'] . '/', $src );
		$src     = str_replace( '/wp-includes/', '/' . $aliases['includes'] . '/', $src );
		$map     = $this->get_plugin_path_alias_map();
		if ( ! empty( $map ) ) {
			foreach ( $map as $plugin_slug => $alias_slug ) {
				$src = str_replace(
					'/' . $aliases['plugins'] . '/' . $plugin_slug . '/',
					'/' . $aliases['plugins'] . '/' . $alias_slug . '/',
					$src
				);
			}
		}
		return $src;
	}

	/**
	 * Build plugin folder alias map.
	 *
	 * @return array<string,string>
	 */
	private function get_plugin_path_alias_map(): array {
		$settings = $this->get_settings();
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugins = function_exists( 'get_plugins' ) ? get_plugins() : array();
		if ( ! is_array( $plugins ) ) {
			return array();
		}

		$name_map = isset( $settings['dpp_ghost_plugin_name_map'] ) && is_array( $settings['dpp_ghost_plugin_name_map'] ) ? $settings['dpp_ghost_plugin_name_map'] : array();
		$aliases  = array();
		foreach ( array_keys( $plugins ) as $plugin_file ) {
			$parts = explode( '/', (string) $plugin_file );
			$slug  = isset( $parts[0] ) ? sanitize_title( (string) $parts[0] ) : '';
			if ( '' === $slug || isset( $aliases[ $slug ] ) ) {
				continue;
			}
			$alias = '';
			if ( isset( $name_map[ $plugin_file ] ) ) {
				$alias = sanitize_title( (string) $name_map[ $plugin_file ] );
			} elseif ( isset( $name_map[ $slug ] ) ) {
				$alias = sanitize_title( (string) $name_map[ $slug ] );
			}
			if ( '' === $alias ) {
				$alias = sanitize_title( $this->generate_default_plugin_alias( (string) $plugin_file ) );
			}
			if ( '' === $alias ) {
				$alias = 'ext-' . substr( md5( $slug ), 0, 6 );
			}
			if ( isset( $aliases[ $slug ] ) || in_array( $alias, $aliases, true ) ) {
				$alias = 'ext-' . substr( md5( $slug . $plugin_file ), 0, 6 );
			}
			$aliases[ $slug ] = $alias;
		}
		return $aliases;
	}

	/**
	 * Restore real plugin folder from masked alias.
	 *
	 * @param string $relative Relative path.
	 *
	 * @return string
	 */
	private function restore_real_plugin_path_from_alias( string $relative ): string {
		$relative = ltrim( $relative, '/' );
		if ( '' === $relative ) {
			return $relative;
		}
		$parts = explode( '/', $relative );
		if ( empty( $parts[0] ) ) {
			return $relative;
		}
		$first = sanitize_title( (string) $parts[0] );
		$map   = $this->get_plugin_path_alias_map();
		if ( empty( $map ) ) {
			return $relative;
		}
		$reverse = array_flip( $map );
		if ( ! isset( $reverse[ $first ] ) ) {
			return $relative;
		}
		$parts[0] = $reverse[ $first ];
		return implode( '/', $parts );
	}

	/**
	 * Get aliases with sane fallback.
	 *
	 * @return array<string,string>
	 */
	private function get_aliases(): array {
		$s = $this->get_settings();
		return array(
			'content'  => ! empty( $s['dpp_ghost_alias_content'] ) ? sanitize_title( (string) $s['dpp_ghost_alias_content'] ) : 'assets',
			'includes' => ! empty( $s['dpp_ghost_alias_includes'] ) ? sanitize_title( (string) $s['dpp_ghost_alias_includes'] ) : 'core',
			'themes'   => ! empty( $s['dpp_ghost_alias_themes'] ) ? sanitize_title( (string) $s['dpp_ghost_alias_themes'] ) : 'designs',
			'plugins'  => ! empty( $s['dpp_ghost_alias_plugins'] ) ? sanitize_title( (string) $s['dpp_ghost_alias_plugins'] ) : 'extensions',
		);
	}

	/**
	 * Replace plugin names in admin plugins list.
	 *
	 * @param array<string,array<string,mixed>> $plugins Plugins.
	 * @return array<string,array<string,mixed>>
	 */
	public function mask_plugin_names_admin( array $plugins ): array {
		if ( ! $this->is_enabled() || ! is_admin() ) {
			return $plugins;
		}

		$settings = $this->get_settings();
		if ( empty( $settings['dpp_ghost_mask_plugin_names'] ) ) {
			return $plugins;
		}

		$map = isset( $settings['dpp_ghost_plugin_name_map'] ) && is_array( $settings['dpp_ghost_plugin_name_map'] ) ? $settings['dpp_ghost_plugin_name_map'] : array();
		foreach ( $plugins as $plugin_file => $plugin_data ) {
			$replacement = '';
			if ( isset( $map[ $plugin_file ] ) && '' !== trim( (string) $map[ $plugin_file ] ) ) {
				$replacement = sanitize_text_field( (string) $map[ $plugin_file ] );
			} else {
				$replacement = $this->generate_default_plugin_alias( (string) $plugin_file );
			}

			$plugins[ $plugin_file ]['Name']  = $replacement;
			$plugins[ $plugin_file ]['Title'] = $replacement;
		}

		return $plugins;
	}

	/**
	 * Generate stable default alias for plugin file.
	 *
	 * @param string $plugin_file Plugin file path.
	 * @return string
	 */
	private function generate_default_plugin_alias( string $plugin_file ): string {
		$defaults = array(
			'Site Toolkit',
			'Core Extension',
			'Content Module',
			'Utility Pack',
			'Performance Layer',
			'Security Module',
			'Workflow Engine',
			'Media Suite',
			'Commerce Pack',
			'Integration Bridge',
		);
		$index = absint( crc32( $plugin_file ) ) % count( $defaults );
		return $defaults[ $index ] . ' ' . ( ( $index % 5 ) + 1 );
	}

	/**
	 * Disable XML-RPC.
	 *
	 * @param bool $enabled Enabled.
	 * @return bool
	 */
	public function disable_xmlrpc( bool $enabled ): bool {
		if ( ! $this->is_enabled() ) {
			return $enabled;
		}
		$s = $this->get_settings();
		return empty( $s['dpp_ghost_disable_xmlrpc'] ) ? $enabled : false;
	}

	/**
	 * Block rest users endpoint.
	 *
	 * @param mixed $result Result.
	 * @return mixed
	 */
	public function block_rest_users_endpoint( $result ) {
		if ( ! $this->is_enabled() ) {
			return $result;
		}
		$s = $this->get_settings();
		if ( empty( $s['dpp_ghost_hide_rest_users'] ) ) {
			return $result;
		}
		// Normalize both REST request forms: /wp-json/wp/v2/users and ?rest_route=/wp/v2/users.
		$uri   = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$route = isset( $_GET['rest_route'] ) ? sanitize_text_field( wp_unslash( $_GET['rest_route'] ) ) : (string) wp_parse_url( $uri, PHP_URL_PATH ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$route = strtolower( untrailingslashit( $route ) );
		if ( (bool) preg_match( '#/wp/v2/users(/|$)#', $route ) ) {
			return new \WP_Error( 'forbidden', __( 'Not allowed.', 'powerplus-toolkit' ), array( 'status' => 403 ) );
		}
		return $result;
	}

	/**
	 * Remove the users REST routes entirely — the robust, non-bypassable guard.
	 *
	 * @param array<string,mixed> $endpoints REST endpoints.
	 * @return array<string,mixed>
	 */
	public function remove_rest_users_routes( $endpoints ) {
		if ( ! $this->is_enabled() ) {
			return $endpoints;
		}
		$s = $this->get_settings();
		if ( empty( $s['dpp_ghost_hide_rest_users'] ) ) {
			return $endpoints;
		}
		// Logged-in users with list_users (admins) keep access; block only public enumeration.
		if ( is_user_logged_in() && current_user_can( 'list_users' ) ) {
			return $endpoints;
		}
		foreach ( array_keys( $endpoints ) as $route ) {
			if ( 0 === strpos( (string) $route, '/wp/v2/users' ) ) {
				unset( $endpoints[ $route ] );
			}
		}
		return $endpoints;
	}

	/**
	 * Change REST URL prefix.
	 *
	 * @param string $prefix Prefix.
	 * @return string
	 */
	public function filter_rest_prefix( string $prefix ): string {
		if ( ! $this->is_enabled() ) {
			return $prefix;
		}
		$s      = $this->get_settings();
		$custom = isset( $s['dpp_ghost_rest_prefix'] ) ? sanitize_title( (string) $s['dpp_ghost_rest_prefix'] ) : '';
		return '' === $custom ? $prefix : $custom;
	}

	/**
	 * Remove wp classes.
	 *
	 * @param string[] $classes Classes.
	 * @return string[]
	 */
	public function strip_wordpress_body_classes( array $classes ): array {
		if ( ! $this->is_enabled() ) {
			return $classes;
		}
		return array_values(
			array_filter(
				$classes,
				static function ( $class ) {
					$class = (string) $class;
					return ( false === strpos( $class, 'wp-' ) && false === strpos( $class, 'wordpress' ) );
				}
			)
		);
	}

	/**
	 * Block author archive.
	 *
	 * @return void
	 */
	public function maybe_block_author_archives(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}
		$s = $this->get_settings();
		if ( empty( $s['dpp_ghost_disable_author_archives'] ) ) {
			return;
		}
		if ( is_author() ) {
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
			nocache_headers();
		}
	}

	/**
	 * Block common probe endpoints.
	 *
	 * @return void
	 */
	public function maybe_block_probe_requests(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}
		$s = $this->get_settings();
		if ( empty( $s['dpp_ghost_block_probes'] ) ) {
			return;
		}
		$uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$uri = wp_parse_url( $uri, PHP_URL_PATH );
		$uri = is_string( $uri ) ? trim( $uri, '/' ) : '';
		$blocked = array( 'readme.html', 'license.txt', 'wp-trackback.php', 'wp-links-opml.php', 'wp-activate.php', 'wp-config.php' );
		if ( in_array( $uri, $blocked, true ) ) {
			status_header( 404 );
			exit;
		}
	}

	/**
	 * Cleanup headers.
	 *
	 * @return void
	 */
	public function cleanup_headers(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}
		if ( function_exists( 'header_remove' ) ) {
			header_remove( 'X-Powered-By' );
		}
	}

	/**
	 * Detection test runner.
	 *
	 * @return void
	 */
	public function run_detection_test(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'powerplus-toolkit' ) );
		}
		check_admin_referer( 'dpp_ghost_test' );
		$this->perform_detection_test();
		wp_safe_redirect( admin_url( 'admin.php?page=pkwt-settings&tab=ghost-mode&pkwt_notice=ghost_tested' ) );
		exit;
	}

	/**
	 * Weekly auto test callback.
	 *
	 * @return void
	 */
	public function run_weekly_auto_test(): void {
		$this->perform_detection_test();
		$results   = get_option( 'pkwt_dpp_ghost_last_test', array() );
		$score     = isset( $results['score'] ) ? absint( $results['score'] ) : 0;
		$settings  = $this->get_settings();
		$threshold = isset( $settings['dpp_ghost_auto_test_threshold'] ) ? absint( $settings['dpp_ghost_auto_test_threshold'] ) : 8;
		if ( $score < $threshold ) {
			wp_mail(
				get_option( 'admin_email' ),
				__( 'Ghost Mode score dropped', 'powerplus-toolkit' ),
				sprintf(
					/* translators: 1: score 2: threshold */
					__( 'Ghost Mode score is %1$d which is below threshold %2$d.', 'powerplus-toolkit' ),
					$score,
					$threshold
				)
			);
		}
	}

	/**
	 * Schedule/unschedule weekly test on settings update.
	 *
	 * @param mixed $old Old.
	 * @param mixed $new New.
	 * @return void
	 */
	public function maybe_reschedule_test_cron( $old, $new ): void {
		$old = is_array( $old ) ? $old : array();
		$new = is_array( $new ) ? $new : array();
		$was_enabled = ! empty( $old['dpp_ghost_auto_test'] );
		$is_enabled  = ! empty( $new['dpp_ghost_auto_test'] );

		if ( $is_enabled && ! wp_next_scheduled( 'pkwt_dpp_ghost_weekly_test' ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'weekly', 'pkwt_dpp_ghost_weekly_test' );
		}
		if ( $was_enabled && ! $is_enabled ) {
			wp_clear_scheduled_hook( 'pkwt_dpp_ghost_weekly_test' );
		}
	}

	/**
	 * Perform detector checks.
	 *
	 * @return void
	 */
	private function perform_detection_test(): void {
		$response = wp_remote_get(
			home_url( '/' ),
			array(
				'timeout'   => 10,
				'sslverify' => true,
			)
		);
		$body = is_wp_error( $response ) ? '' : (string) wp_remote_retrieve_body( $response );
		$checks = array(
			'generator'      => false === stripos( $body, 'name="generator"' ),
			'wp-content'     => false === stripos( $body, 'wp-content' ),
			'wp-login'       => false === stripos( $body, 'wp-login.php' ),
			'rsd'            => false === stripos( $body, 'EditURI' ),
			'wlw'            => false === stripos( $body, 'wlwmanifest' ),
			'api_w_org'      => false === stripos( $body, 'api.w.org' ),
			'emoji'          => false === stripos( $body, 'emoji' ),
			'shortlink'      => false === stripos( $body, 'shortlink' ),
			'xmlrpc'         => false === stripos( $body, 'xmlrpc.php' ),
			'wp-json'        => false === stripos( $body, '/wp-json/' ),
			'plugins'        => false === stripos( $body, '/plugins/' ),
		);
		$score = 0;
		foreach ( $checks as $ok ) {
			if ( $ok ) {
				$score++;
			}
		}
		update_option(
			'pkwt_dpp_ghost_last_test',
			array(
				'time'   => time(),
				'score'  => $score,
				'total'  => count( $checks ),
				'checks' => $checks,
			),
			false
		);
	}
}
