<?php
/**
 * PowerPlus modern React dashboard UI.
 * Renders inside WordPress admin — WP sidebar stays visible.
 *
 * @package PKWT
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pkwt_nonce   = wp_create_nonce( 'pkwt_dashboard_nonce' );
$pkwt_ajax    = admin_url( 'admin-ajax.php' );
$pkwt_ver     = PKWT_VERSION;
$pkwt_icon    = PKWT_PLUGIN_URL . 'assets/images/powerplus-icon.png';

// Map WP page slug → React route id.
$pkwt_page_map = array(
	'pkwt-settings'                => 'dashboard',
	'pkwt-settings-overview'       => 'dashboard',
	'pkwt-settings-general'        => 'general',
	'pkwt-settings-templates'      => 'templates',
	'pkwt-settings-redirects'      => 'redirects',
	'pkwt-settings-compatibility'  => 'compatibility',
	'pkwt-settings-security'       => 'security',
	'pkwt-settings-duplicate'      => 'duplicator',
	'pkwt-settings-svg-upload'     => 'svg-upload',
	'pkwt-settings-ghost-mode'     => 'ghost-mode',
	'pkwt-settings-classic-editor' => 'classic-editor',
	'pkwt-settings-import-export'  => 'import-export',
	'pkwt-settings-branding'       => 'branding',
	'pkwt-settings-login'          => 'login',
	'pkwt-settings-widgets'        => 'widgets',
);
$pkwt_slug         = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : 'pkwt-settings'; // phpcs:ignore WordPress.Security.NonceVerification
$pkwt_current_page = isset( $pkwt_page_map[ $pkwt_slug ] ) ? $pkwt_page_map[ $pkwt_slug ] : 'dashboard';

// Whether the current user may actually change configuration. The menu is registered
// with the 'read' cap and access_roles can admit non-admins for VIEW access, but all
// saves require manage_options — the SPA renders read-only for everyone else.
$pkwt_can_manage = current_user_can( 'manage_options' );

// Real plugin settings for the dashboard.
$pkwt_repo     = new \PKWT\Includes\Class_PKWT_Settings_Repository();
$pkwt_settings = $pkwt_repo->get();

// Attach the resolved login-logo URL so the Branding page preview can render it.
if ( isset( $pkwt_settings['branding'] ) && is_array( $pkwt_settings['branding'] ) && ! empty( $pkwt_settings['branding']['logo_id'] ) ) {
	$pkwt_logo_url = wp_get_attachment_image_url( (int) $pkwt_settings['branding']['logo_id'], 'medium' );
	$pkwt_settings['branding']['logo_url'] = $pkwt_logo_url ? $pkwt_logo_url : '';
}

// Never expose CAPTCHA secret keys in page source. Replace with a boolean "is set"
// flag so the UI can show a "saved" placeholder without leaking the secret.
foreach ( array( 'recaptcha_secret_key', 'hcaptcha_secret_key' ) as $pkwt_secret ) {
	if ( isset( $pkwt_settings[ $pkwt_secret ] ) ) {
		$pkwt_settings[ $pkwt_secret . '_set' ] = ( '' !== (string) $pkwt_settings[ $pkwt_secret ] );
		$pkwt_settings[ $pkwt_secret ]          = '';
	}
}

// Auth pages map: id, title, edit-in-Elementor URL, view URL.
$pkwt_pages = array();
foreach ( array(
	'login'    => 'login_page_id',
	'register' => 'register_page_id',
	'lost'     => 'lost_password_page_id',
	'reset'    => 'reset_password_page_id',
) as $pkwt_type => $pkwt_key ) {
	$pkwt_pid  = isset( $pkwt_settings[ $pkwt_key ] ) ? absint( $pkwt_settings[ $pkwt_key ] ) : 0;
	$pkwt_post = $pkwt_pid ? get_post( $pkwt_pid ) : null;
	$pkwt_pages[ $pkwt_type ] = array(
		'id'      => $pkwt_pid,
		'title'   => $pkwt_post ? $pkwt_post->post_title : '',
		'status'  => $pkwt_post ? $pkwt_post->post_status : '',
		'editUrl' => $pkwt_post ? add_query_arg( array( 'post' => $pkwt_pid, 'action' => 'elementor' ), admin_url( 'post.php' ) ) : '',
		'viewUrl' => $pkwt_post ? get_permalink( $pkwt_pid ) : '',
	);
}

// Template layout sets for the in-dashboard template browser.
$pkwt_tpl_library = new \PKWT\Elementor\Class_PKWT_Template_Library();
$pkwt_tpl_sets    = array();
foreach ( $pkwt_tpl_library->get_layout_sets() as $pkwt_set_slug => $pkwt_set ) {
	$pkwt_tpl_sets[] = array(
		'slug'        => $pkwt_set_slug,
		'label'       => $pkwt_set['label'],
		'description' => $pkwt_set['description'],
		'color'       => $pkwt_set['color'],
		'accent'      => $pkwt_set['accent'],
		'pages'       => array_keys( $pkwt_set['pages'] ),
	);
}

$pkwt_elementor_active = class_exists( '\Elementor\Plugin' );

// Saved Elementor templates (for the "Elementor template @ secret URL" login mode picker).
$pkwt_elementor_templates = array();
if ( $pkwt_elementor_active ) {
	$pkwt_tpl_posts = get_posts( array(
		'post_type'      => 'elementor_library',
		'posts_per_page' => 100,
		'post_status'    => 'publish',
		'orderby'        => 'title',
		'order'          => 'ASC',
	) );
	foreach ( $pkwt_tpl_posts as $pkwt_tp ) {
		$pkwt_elementor_templates[] = array(
			'id'    => $pkwt_tp->ID,
			'title' => $pkwt_tp->post_title !== '' ? $pkwt_tp->post_title : ( '#' . $pkwt_tp->ID ),
			'type'  => (string) get_post_meta( $pkwt_tp->ID, '_elementor_template_type', true ),
			'edit'  => add_query_arg( array( 'post' => $pkwt_tp->ID, 'action' => 'elementor' ), admin_url( 'post.php' ) ),
		);
	}
}

// Module option groups for the dashboard (booleans the UI can toggle).
$pkwt_modules = array(
	'ghost'      => wp_parse_args( (array) get_option( 'pkwt_dpp_ghost_settings', array() ), array( 'dpp_ghost_enabled' => 0, 'dpp_ghost_remove_generator' => 1, 'dpp_ghost_strip_version_urls' => 1, 'dpp_ghost_remove_emoji' => 1, 'dpp_ghost_disable_xmlrpc' => 1, 'dpp_ghost_hide_rest_users' => 1, 'dpp_ghost_disable_author_archives' => 1, 'dpp_ghost_custom_cms_name' => '' ) ),
	'svg'        => wp_parse_args( (array) get_option( 'pkwt_dpp_svg_settings', array() ), array( 'dpp_svg_enabled' => 0, 'dpp_svg_preview' => 1, 'dpp_svg_blocked_log' => 0, 'dpp_svg_max_size_kb' => 512 ) ),
	'classic'    => wp_parse_args( (array) get_option( 'pkwt_dpp_classic_settings', array() ), array( 'dpp_classic_enabled' => 0, 'dpp_classic_allow_user_choice' => 0, 'dpp_classic_allow_admin_bypass' => 1 ) ),
	'duplicator' => wp_parse_args( (array) get_option( 'pkwt_dpp_settings', array() ), array( 'enabled' => 1, 'enable_row_action' => 1, 'enable_elementor_button' => 1, 'title_suffix' => '(Copy)' ) ),
);

// Nonced admin-post URLs for export / import / reset.
$pkwt_export_url   = wp_nonce_url( admin_url( 'admin-post.php?action=pkwt_export_settings' ), 'pkwt_export_settings' );
$pkwt_reset_url    = wp_nonce_url( admin_url( 'admin-post.php?action=pkwt_reset_settings' ), 'pkwt_reset_settings' );
$pkwt_import_nonce = wp_create_nonce( 'pkwt_import_settings' );

// Notice from an admin-post redirect (e.g. settings import) so the SPA can toast it.
$pkwt_notice = isset( $_GET['pkwt_notice'] ) ? sanitize_key( wp_unslash( $_GET['pkwt_notice'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
?>
<style>
/* Remove default .wrap margins so our UI starts flush */
#wpbody-content .wrap { margin-top: 0 !important; padding-top: 0 !important; }
#wpbody-content       { padding-bottom: 0 !important; }
#wpcontent            { padding-left: 0 !important; }
#pkwt-dashboard-root  { min-height: calc(100vh - 32px); }
</style>
<div class="wrap" style="margin:0;padding:0;">
  <div id="pkwt-dashboard-root"></div>
</div>
<script>
window.pkwtDashboard = {
	ajaxUrl     : <?php echo wp_json_encode( $pkwt_ajax ); ?>,
	nonce       : <?php echo wp_json_encode( $pkwt_nonce ); ?>,
	version     : <?php echo wp_json_encode( $pkwt_ver ); ?>,
	siteUrl     : <?php echo wp_json_encode( home_url() ); ?>,
	adminUrl    : <?php echo wp_json_encode( admin_url() ); ?>,
	iconUrl     : <?php echo wp_json_encode( $pkwt_icon ); ?>,
	currentPage : <?php echo wp_json_encode( $pkwt_current_page ); ?>,
	settings    : <?php echo wp_json_encode( $pkwt_settings ); ?>,
	canManage   : <?php echo wp_json_encode( $pkwt_can_manage ); ?>,
	pages       : <?php echo wp_json_encode( $pkwt_pages ); ?>,
	templates   : <?php echo wp_json_encode( $pkwt_tpl_sets ); ?>,
	elementor   : <?php echo wp_json_encode( $pkwt_elementor_active ); ?>,
	wizardComplete : <?php echo wp_json_encode( (bool) get_option( 'pkwt_wizard_complete', false ) ); ?>,
	onboardingChoices : <?php echo wp_json_encode( (array) get_option( 'pkwt_onboarding_choices', array() ) ); ?>,
	elementorTemplates : <?php echo wp_json_encode( $pkwt_elementor_templates ); ?>,
	modules     : <?php echo wp_json_encode( $pkwt_modules ); ?>,
	exportUrl   : <?php echo wp_json_encode( $pkwt_export_url ); ?>,
	resetUrl    : <?php echo wp_json_encode( $pkwt_reset_url ); ?>,
	importAction: <?php echo wp_json_encode( admin_url( 'admin-post.php' ) ); ?>,
	importNonce : <?php echo wp_json_encode( $pkwt_import_nonce ); ?>,
	notice      : <?php echo wp_json_encode( $pkwt_notice ); ?>,
};
</script>
