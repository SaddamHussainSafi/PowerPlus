<?php
/**
 * Login renderer — serves a chosen Elementor TEMPLATE at the custom login URL, with NO
 * separate WordPress page (the "Elementor template @ secret URL" login mode).
 *
 * This is the hybrid of WPS Hide Login (secret URL, no page) and Elementor design freedom.
 * Elementor cannot render on raw wp-login.php, so instead we intercept the secret login slug
 * and output a full-bleed canvas document whose body is the Elementor template's rendered HTML
 * (which contains the PowerPlus Login Form widget). The form still posts to core auth.
 *
 * Opt-in: only active when pkwt_settings['login_mode'] === 'template' AND a template is chosen.
 * Default mode is 'legacy' (the existing page-based behavior), so nothing changes unless enabled.
 *
 * @package PKWT
 */

namespace PKWT\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_Login_Renderer {

	/**
	 * Settings repository.
	 *
	 * @var Class_PKWT_Settings_Repository
	 */
	private $settings_repo;

	public function __construct() {
		$this->settings_repo = new Class_PKWT_Settings_Repository();
	}

	/**
	 * Register hooks. We run early on template_redirect so we win before the 404 handler.
	 *
	 * @return void
	 */
	public function register(): void {
		if ( ! $this->is_template_mode() ) {
			return;
		}
		add_action( 'template_redirect', array( $this, 'maybe_render_login' ), 0 );
	}

	/**
	 * Whether the Elementor-template login mode is active and usable.
	 *
	 * @return bool
	 */
	private function is_template_mode(): bool {
		$settings = $this->settings_repo->get();
		$mode     = isset( $settings['login_mode'] ) ? (string) $settings['login_mode'] : 'legacy';
		if ( 'template' !== $mode ) {
			return false;
		}
		if ( empty( $settings['login_template_id'] ) ) {
			return false;
		}
		return class_exists( '\Elementor\Plugin' );
	}

	/**
	 * The secret login slug (path) derived from the custom login URL setting.
	 *
	 * @return string
	 */
	private function login_slug(): string {
		$settings = $this->settings_repo->get();
		$custom   = isset( $settings['pkwt_custom_login_url'] ) ? (string) $settings['pkwt_custom_login_url'] : '';
		if ( '' === $custom ) {
			return '';
		}
		return trim( (string) wp_parse_url( $custom, PHP_URL_PATH ), '/' );
	}

	/**
	 * Current request path, normalized (no leading/trailing slash, no query).
	 *
	 * @return string
	 */
	private function request_path(): string {
		$uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return trim( (string) wp_parse_url( $uri, PHP_URL_PATH ), '/' );
	}

	/**
	 * If the request is the secret login URL, render the Elementor template and exit.
	 *
	 * @return void
	 */
	public function maybe_render_login(): void {
		if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}

		$slug = $this->login_slug();
		if ( '' === $slug || $this->request_path() !== $slug ) {
			return;
		}

		// Already authenticated? Bounce to the configured destination (default: admin).
		if ( is_user_logged_in() ) {
			$settings = $this->settings_repo->get();
			$dest     = ! empty( $settings['after_login_redirect'] ) ? (string) $settings['after_login_redirect'] : admin_url();
			wp_safe_redirect( $dest );
			exit;
		}

		$settings    = $this->settings_repo->get();
		$template_id = absint( $settings['login_template_id'] );

		$template = get_post( $template_id );
		if ( ! $template || 'elementor_library' !== get_post_type( $template ) ) {
			// Misconfigured — fall through to normal WP handling rather than white-screen.
			return;
		}

		$this->render_canvas( $template_id );
		exit;
	}

	/**
	 * Output a full-bleed canvas HTML document whose body is the rendered Elementor template.
	 *
	 * @param int $template_id Elementor template (elementor_library) post ID.
	 *
	 * @return void
	 */
	private function render_canvas( int $template_id ): void {
		// Ensure Elementor enqueues its frontend assets for this request.
		$elementor = \Elementor\Plugin::$instance;
		if ( isset( $elementor->frontend ) && method_exists( $elementor->frontend, 'register_styles' ) ) {
			$elementor->frontend->register_styles();
		}

		nocache_headers();
		status_header( 200 );

		// Render the template HTML (with its inline CSS) up front so any enqueues it triggers
		// are registered before wp_head() prints them.
		$content = $elementor->frontend->get_builder_content_for_display( $template_id, true );

		// Minimal canvas shell — no theme header/footer, just wp_head/wp_footer so Elementor's
		// styles and scripts load exactly as in the editor.
		?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex,nofollow">
	<title><?php echo esc_html( get_bloginfo( 'name' ) . ' — ' . __( 'Log In', 'powerplus-toolkit' ) ); ?></title>
	<?php wp_head(); ?>
	<style>
		html,body{margin:0;padding:0;min-height:100%;}
		body.pkwt-login-canvas{display:flex;flex-direction:column;}
		body.pkwt-login-canvas .pkwt-login-canvas__inner{flex:1 0 auto;}
	</style>
</head>
<body <?php body_class( 'pkwt-login-canvas elementor-page' ); ?>>
	<div class="pkwt-login-canvas__inner">
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapingOutput.OutputNotEscaped -- Elementor returns sanitized builder HTML. ?>
	</div>
	<?php wp_footer(); ?>
</body>
</html>
		<?php
	}
}
