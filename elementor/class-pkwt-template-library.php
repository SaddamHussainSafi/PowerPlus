<?php
/**
 * Template library — registers pre-built Elementor page layouts and handles one-click import.
 *
 * @package PKWT
 */

namespace PKWT\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_Template_Library {

	/**
	 * Directory that contains the .json template files.
	 */
	private string $templates_dir;

	public function __construct() {
		$this->templates_dir = plugin_dir_path( __FILE__ ) . 'templates/';
	}

	/**
	 * Register hooks.
	 */
	public function register(): void {
		add_action( 'admin_post_pkwt_import_template', array( $this, 'handle_import' ) );
		add_action( 'wp_ajax_pkwt_ajax_import_template', array( $this, 'handle_ajax_import' ) );
		// Nonce-refresh endpoint: JS calls this right before import to get a guaranteed-fresh nonce.
		add_action( 'wp_ajax_pkwt_get_import_nonce', array( $this, 'handle_get_nonce' ) );
	}

	/**
	 * Return a fresh nonce for the import action.
	 * Called by JS just before submitting the import request.
	 *
	 * @return void
	 */
	public function handle_get_nonce(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Not allowed.' ) );
			wp_die();
		}
		wp_send_json_success( array( 'nonce' => wp_create_nonce( 'pkwt_ajax_import_template' ) ) );
		wp_die();
	}

	/**
	 * Returns the manifest of all available templates, grouped by layout set.
	 *
	 * Each entry contains the metadata from the JSON file plus a derived slug.
	 *
	 * @return array<string, array<string, mixed>>  Keyed by layout slug.
	 */
	public function get_layout_sets(): array {
		$sets = array(
			'split-left'  => array(
				'label'       => __( 'Split Left — Dark Panel + Form', 'powerplus-toolkit' ),
				'description' => __( 'Full-height split layout. Dark image/colour panel on the left, clean white form on the right. Perfect for agency and portfolio sites.', 'powerplus-toolkit' ),
				'color'       => '#0f172a',
				'accent'      => '#6366f1',
				'pages'       => array(
					'login'    => 'tpl-split-left-login.json',
					'register' => 'tpl-split-left-register.json',
					'lost'     => 'tpl-split-left-lost.json',
					'reset'    => 'tpl-split-left-reset.json',
				),
			),
			'centered-card' => array(
				'label'       => __( 'Centered Card — Gradient Background', 'powerplus-toolkit' ),
				'description' => __( 'White card centred on a deep blue gradient full-screen background. Minimal, modern and easy to customise.', 'powerplus-toolkit' ),
				'color'       => '#1a1a4e',
				'accent'      => '#2563eb',
				'pages'       => array(
					'login'    => 'tpl-centered-card-login.json',
					'register' => 'tpl-centered-card-register.json',
					'lost'     => 'tpl-centered-card-lost.json',
					'reset'    => 'tpl-centered-card-reset.json',
				),
			),
			'form-left'   => array(
				'label'       => __( 'Gradient Panel Right — Form + Visual', 'powerplus-toolkit' ),
				'description' => __( 'Clean white form on the left, bold indigo-purple gradient panel on the right. Great for SaaS and product sites.', 'powerplus-toolkit' ),
				'color'       => '#4f46e5',
				'accent'      => '#7c3aed',
				'pages'       => array(
					'login'    => 'tpl-form-left-login.json',
					'register' => 'tpl-form-left-register.json',
					'lost'     => 'tpl-form-left-lost.json',
					'reset'    => 'tpl-form-left-reset.json',
				),
			),
			'dreamer'     => array(
				'label'       => __( 'Dreamer — Floating Card + 3D Character', 'powerplus-toolkit' ),
				'description' => __( 'Soft lavender full-page background with a floating white card on the left and a 3D character illustration on the right. Fun, modern and eye-catching.', 'powerplus-toolkit' ),
				'color'       => '#e8e0f0',
				'accent'      => '#3b5bdb',
				'pages'       => array(
					'login'    => 'tpl-dreamer-login.json',
					'register' => 'tpl-dreamer-register.json',
					'lost'     => 'tpl-dreamer-lost.json',
					'reset'    => 'tpl-dreamer-reset.json',
				),
			),
			'uxolist'     => array(
				'label'       => __( 'UXOLIST — White Form + Dark Image Card', 'powerplus-toolkit' ),
				'description' => __( 'Clean white form on the left with a dark navy image panel on the right featuring a branded header and customer testimonial. Perfect for professional SaaS and agency sites.', 'powerplus-toolkit' ),
				'color'       => '#0f172a',
				'accent'      => '#3b82f6',
				'pages'       => array(
					'login'    => 'tpl-uxolist-login.json',
					'register' => 'tpl-uxolist-register.json',
					'lost'     => 'tpl-uxolist-lost.json',
					'reset'    => 'tpl-uxolist-reset.json',
				),
			),
			'gradient-hub' => array(
				'label'       => __( 'Gradient Hub — Vibrant Panel + Clean Form', 'powerplus-toolkit' ),
				'description' => __( 'Bold blue-to-purple gradient panel on the left with a tagline, and a clean white form on the right. Great for productivity apps, SaaS platforms, and startups.', 'powerplus-toolkit' ),
				'color'       => '#3b4ce8',
				'accent'      => '#7c3aed',
				'pages'       => array(
					'login'    => 'tpl-gradient-hub-login.json',
					'register' => 'tpl-gradient-hub-register.json',
					'lost'     => 'tpl-gradient-hub-lost.json',
					'reset'    => 'tpl-gradient-hub-reset.json',
				),
			),
		);

		return $sets;
	}

	/**
	 * Read a template JSON file and return its decoded content.
	 *
	 * @param string $filename  Basename of the JSON file.
	 *
	 * @return array<string,mixed>|null
	 */
	public function get_template( string $filename ): ?array {
		$path = $this->templates_dir . $filename;
		if ( ! file_exists( $path ) ) {
			return null;
		}
		$raw     = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$decoded = json_decode( $raw, true );
		return is_array( $decoded ) ? $decoded : null;
	}

	/**
	 * Import a template onto a page.
	 *
	 * Writes _elementor_data, sets _elementor_edit_mode, and schedules CSS regeneration.
	 *
	 * @param int                  $post_id  Target page ID.
	 * @param array<string,mixed>  $template Decoded template JSON.
	 *
	 * @return bool
	 */
	public function apply_template_to_page( int $post_id, array $template ): bool {
		if ( $post_id <= 0 || empty( $template['content'] ) || ! is_array( $template['content'] ) ) {
			return false;
		}

		// Write the Elementor data.
		$elementor_data = wp_json_encode( $template['content'] );
		if ( false === $elementor_data ) {
			return false;
		}

		update_post_meta( $post_id, '_elementor_data', wp_slash( $elementor_data ) );
		update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );

		// Delete cached CSS so Elementor regenerates it on next load.
		delete_post_meta( $post_id, '_elementor_css' );

		// Tell Elementor to regenerate its data.
		if ( class_exists( '\Elementor\Plugin' ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}

		return true;
	}

	/**
	 * Handle AJAX template import called from Elementor editor widget panel OR
	 * from the dashboard Page Templates tab.
	 *
	 * Expected POST fields:
	 *   nonce      – security nonce (pkwt_ajax_import_template)
	 *   set_slug   – layout set key (e.g. "split-left")
	 *   page_type  – "login" | "register" | "lost" | "reset"
	 *
	 * IMPORTANT: We use wp_verify_nonce() instead of check_ajax_referer() because
	 * check_ajax_referer() outputs plain-text "-1" and dies immediately on failure,
	 * which breaks the JSON response contract and causes the JS .catch() to fire.
	 * wp_verify_nonce() returns false silently, letting us return a proper JSON error.
	 *
	 * ob_start()/ob_get_clean() is used before sending JSON to ensure that any PHP
	 * notices or warnings that WordPress or third-party plugins may output do not
	 * corrupt the JSON response.
	 *
	 * @return void
	 */
	public function handle_ajax_import(): void {
		// Buffer any stray output (PHP notices, warnings, other plugin output)
		// that would otherwise corrupt the JSON response and cause res.json() to throw.
		ob_start();

		// ── 1. Permission check ───────────────────────────────────────────────
		if ( ! current_user_can( 'manage_options' ) ) {
			ob_end_clean();
			wp_send_json_error( array( 'message' => __( 'Permission denied. You must be an administrator to import templates.', 'powerplus-toolkit' ) ) );
			wp_die();
		}

		// ── 2. Nonce verification ────────────────────────────────────────────
		// We deliberately use wp_verify_nonce() instead of check_ajax_referer()
		// so that failure returns proper JSON instead of raw "-1" text.
		$raw_nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $raw_nonce, 'pkwt_ajax_import_template' ) ) {
			ob_end_clean();
			wp_send_json_error( array( 'message' => __( 'Security check failed. Please refresh the page and try again.', 'powerplus-toolkit' ) ) );
			wp_die();
		}

		// ── 3. Sanitise and validate inputs ──────────────────────────────────
		$set_slug  = isset( $_POST['set_slug'] ) ? sanitize_key( wp_unslash( $_POST['set_slug'] ) ) : '';
		$page_type = isset( $_POST['page_type'] ) ? sanitize_key( wp_unslash( $_POST['page_type'] ) ) : '';

		if ( empty( $set_slug ) || empty( $page_type ) ) {
			ob_end_clean();
			wp_send_json_error( array( 'message' => __( 'Missing required parameters (set_slug, page_type).', 'powerplus-toolkit' ) ) );
			wp_die();
		}

		$sets = $this->get_layout_sets();

		if ( ! isset( $sets[ $set_slug ] ) ) {
			ob_end_clean();
			// translators: %s is the layout set slug name.
			wp_send_json_error( array( 'message' => sprintf( __( 'Unknown layout set: "%s".', 'powerplus-toolkit' ), esc_html( $set_slug ) ) ) );
			wp_die();
		}

		$allowed_page_types = array( 'login', 'register', 'lost', 'reset' );
		if ( ! in_array( $page_type, $allowed_page_types, true ) ) {
			ob_end_clean();
			// translators: %s is the page type identifier (e.g. login, register).
			wp_send_json_error( array( 'message' => sprintf( __( 'Unknown page type: "%s". Must be one of: login, register, lost, reset.', 'powerplus-toolkit' ), esc_html( $page_type ) ) ) );
			wp_die();
		}

		if ( ! isset( $sets[ $set_slug ]['pages'][ $page_type ] ) ) {
			ob_end_clean();
			wp_send_json_error( array( 'message' => __( 'No template file defined for this set and page type combination.', 'powerplus-toolkit' ) ) );
			wp_die();
		}

		// ── 4. Elementor active check ────────────────────────────────────────
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			ob_end_clean();
			wp_send_json_error( array( 'message' => __( 'Elementor is not active. Please install and activate Elementor to use templates.', 'powerplus-toolkit' ) ) );
			wp_die();
		}

		// ── 5. Load template file ────────────────────────────────────────────
		$filename = $sets[ $set_slug ]['pages'][ $page_type ];
		$template = $this->get_template( $filename );

		if ( null === $template ) {
			$expected_path = $this->templates_dir . $filename;
			ob_end_clean();
			wp_send_json_error( array(
				'message' => sprintf(
					// translators: %s is the template filename.
					__( 'Template file not found: %s. Please reinstall the plugin.', 'powerplus-toolkit' ),
					esc_html( $filename )
				),
			) );
			wp_die();
		}

		if ( empty( $template['content'] ) ) {
			ob_end_clean();
			wp_send_json_error( array(
				'message' => sprintf(
					// translators: %s is the template filename.
					__( 'Template file "%s" is empty or has no content. Please reinstall the plugin.', 'powerplus-toolkit' ),
					esc_html( $filename )
				),
			) );
			wp_die();
		}

		// ── 6. Resolve target page ───────────────────────────────────────────
		$target_id = $this->resolve_page_id( $page_type );

		if ( $target_id <= 0 ) {
			ob_end_clean();
			wp_send_json_error( array(
				'message' => sprintf(
					/* translators: %s: page type label */
					__( 'No page is configured for the "%s" form. Please go to PowerPlus → General settings and assign a page first.', 'powerplus-toolkit' ),
					esc_html( $page_type )
				),
			) );
			wp_die();
		}

		// Verify the target post actually exists and is a page/post.
		$target_post = get_post( $target_id );
		if ( ! $target_post ) {
			ob_end_clean();
			wp_send_json_error( array(
				'message' => sprintf(
					// translators: %d is the WordPress page ID number.
					__( 'The configured page (ID: %d) does not exist. Please update your PowerPlus settings.', 'powerplus-toolkit' ),
					$target_id
				),
			) );
			wp_die();
		}

		// ── 7. Apply template ────────────────────────────────────────────────
		$ok = $this->apply_template_to_page( $target_id, $template );

		if ( ! $ok ) {
			ob_end_clean();
			wp_send_json_error( array( 'message' => __( 'Failed to write template data to the page. Please check file permissions and try again.', 'powerplus-toolkit' ) ) );
			wp_die();
		}

		// ── 8. Discard any buffered noise and send success ───────────────────
		ob_end_clean();

		$edit_url = get_edit_post_link( $target_id, 'raw' );
		$view_url = get_permalink( $target_id );

		wp_send_json_success( array(
			'message'  => sprintf(
				/* translators: %s: layout name */
				__( '"%s" template imported successfully!', 'powerplus-toolkit' ),
				esc_html( $sets[ $set_slug ]['label'] )
			),
			'edit_url' => $edit_url ? add_query_arg( 'action', 'elementor', $edit_url ) : '',
			'view_url' => $view_url ? $view_url : '',
			'page_id'  => $target_id,
		) );
		wp_die();
	}

	/**
	 * Handle the admin_post import action.
	 *
	 * Expected POST fields:
	 *   pkwt_tpl_nonce  – security nonce
	 *   set_slug        – layout set key (e.g. "split-left")
	 *   page_type       – "login" | "register" | "lost" | "reset"
	 *   target_page_id  – WP post ID to write to (optional; falls back to plugin-managed page)
	 *
	 * @return void
	 */
	public function handle_import(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'powerplus-toolkit' ) );
		}

		check_admin_referer( 'pkwt_import_template' );

		$set_slug  = isset( $_POST['set_slug'] ) ? sanitize_key( wp_unslash( $_POST['set_slug'] ) ) : '';
		$page_type = isset( $_POST['page_type'] ) ? sanitize_key( wp_unslash( $_POST['page_type'] ) ) : '';
		$target_id = isset( $_POST['target_page_id'] ) ? absint( $_POST['target_page_id'] ) : 0;

		$sets = $this->get_layout_sets();

		if ( ! isset( $sets[ $set_slug ]['pages'][ $page_type ] ) ) {
			wp_safe_redirect( add_query_arg( array( 'page' => 'pkwt-settings', 'tab' => 'templates', 'pkwt_notice' => 'tpl_invalid' ), admin_url( 'admin.php' ) ) );
			exit;
		}

		$filename = $sets[ $set_slug ]['pages'][ $page_type ];
		$template = $this->get_template( $filename );

		if ( ! $template ) {
			wp_safe_redirect( add_query_arg( array( 'page' => 'pkwt-settings', 'tab' => 'templates', 'pkwt_notice' => 'tpl_not_found' ), admin_url( 'admin.php' ) ) );
			exit;
		}

		// Resolve target page ID if not provided.
		if ( $target_id <= 0 ) {
			$target_id = $this->resolve_page_id( $page_type );
		}

		if ( $target_id <= 0 ) {
			wp_safe_redirect( add_query_arg( array( 'page' => 'pkwt-settings', 'tab' => 'templates', 'pkwt_notice' => 'tpl_no_page' ), admin_url( 'admin.php' ) ) );
			exit;
		}

		$ok = $this->apply_template_to_page( $target_id, $template );

		$notice = $ok ? 'tpl_imported' : 'tpl_failed';
		wp_safe_redirect( add_query_arg( array( 'page' => 'pkwt-settings', 'tab' => 'templates', 'pkwt_notice' => $notice, 'tpl_page_id' => $target_id ), admin_url( 'admin.php' ) ) );
		exit;
	}

	/**
	 * Resolve the plugin-managed page ID for a given page type.
	 *
	 * @param string $page_type  login|register|lost|reset
	 *
	 * @return int
	 */
	private function resolve_page_id( string $page_type ): int {
		$settings = get_option( 'pkwt_settings', array() );

		$map = array(
			'login'    => 'login_page_id',
			'register' => 'register_page_id',
			'lost'     => 'lost_password_page_id',
			'reset'    => 'reset_password_page_id',
		);

		$key = $map[ $page_type ] ?? '';
		return $key && isset( $settings[ $key ] ) ? absint( $settings[ $key ] ) : 0;
	}
}
