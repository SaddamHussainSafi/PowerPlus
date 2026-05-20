<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View template file; variables are scoped to this included file.

/**
 * Overview feature toggles view.
 *
 * @package PKWT
 */

$pkwt_settings     = get_option( 'pkwt_settings', array() );
$dpp_settings     = get_option( 'pkwt_dpp_settings', array() );
$svg_settings     = get_option( 'pkwt_dpp_svg_settings', array() );
$ghost_settings   = get_option( 'pkwt_dpp_ghost_settings', array() );
$classic_settings = get_option( 'pkwt_dpp_classic_settings', array() );

$modules = array(
	array(
		'key'        => 'auth',
		'label'      => __( 'Auth Redirects', 'powerplus-toolkit' ),
		'active'     => ! empty( $pkwt_settings['enabled'] ),
		'icon'       => 'dashicons-lock',
		'plugin'     => __( 'PowerPlus — All-in-One Powerful Toolkit', 'powerplus-toolkit' ),
		'link'       => admin_url( 'admin.php?page=pkwt-settings-general' ),
		'link_label' => __( 'Open', 'powerplus-toolkit' ),
	),
	array(
		'key'        => 'duplicate',
		'label'      => __( 'Duplicate', 'powerplus-toolkit' ),
		'active'     => ! empty( $dpp_settings['enabled'] ),
		'icon'       => 'dashicons-admin-page',
		'plugin'     => __( 'Duplicate Module', 'powerplus-toolkit' ),
		'link'       => admin_url( 'admin.php?page=pkwt-settings-duplicate' ),
		'link_label' => __( 'Open', 'powerplus-toolkit' ),
	),
	array(
		'key'        => 'svg',
		'label'      => __( 'SVG Upload', 'powerplus-toolkit' ),
		'active'     => ! empty( $svg_settings['dpp_svg_enabled'] ),
		'icon'       => 'dashicons-format-image',
		'plugin'     => __( 'SVG Security', 'powerplus-toolkit' ),
		'link'       => admin_url( 'admin.php?page=pkwt-settings-svg-upload' ),
		'link_label' => __( 'Open', 'powerplus-toolkit' ),
	),
	array(
		'key'        => 'ghost',
		'label'      => __( 'Ghost Mode', 'powerplus-toolkit' ),
		'active'     => ! empty( $ghost_settings['dpp_ghost_enabled'] ),
		'icon'       => 'dashicons-shield',
		'plugin'     => __( 'Ghost Protection', 'powerplus-toolkit' ),
		'link'       => admin_url( 'admin.php?page=pkwt-settings-ghost-mode' ),
		'link_label' => __( 'Open', 'powerplus-toolkit' ),
	),
	array(
		'key'        => 'classic',
		'label'      => __( 'Classic Editor', 'powerplus-toolkit' ),
		'active'     => ! empty( $classic_settings['dpp_classic_enabled'] ),
		'icon'       => 'dashicons-edit',
		'plugin'     => __( 'Editor Mode', 'powerplus-toolkit' ),
		'link'       => admin_url( 'admin.php?page=pkwt-settings-classic-editor' ),
		'link_label' => __( 'Open', 'powerplus-toolkit' ),
	),
);

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin notice key.
$notice = isset( $_GET['pkwt_notice'] ) ? sanitize_key( wp_unslash( $_GET['pkwt_notice'] ) ) : '';
?>
<div class="wrap pkwt-ui pkwt-overview-wrap">
	<h1><?php esc_html_e( 'PowerPlus — All-in-One Powerful Toolkit', 'powerplus-toolkit' ); ?></h1>
	<p class="description"><?php esc_html_e( 'Developed by Saddam Hussain Safi | Incepta Studio', 'powerplus-toolkit' ); ?></p>

	<div class="pkwt-guide-panel" data-pkwt-guide-panel>
		<div>
			<strong><?php esc_html_e( 'Quick Guided Setup', 'powerplus-toolkit' ); ?></strong>
			<p><?php esc_html_e( '1) Enable modules on this page. 2) Open each module and configure. 3) Save changes and run module test actions.', 'powerplus-toolkit' ); ?></p>
		</div>
		<button type="button" class="button button-secondary" data-pkwt-guide-dismiss><?php esc_html_e( 'Hide', 'powerplus-toolkit' ); ?></button>
	</div>

	<?php
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	?>
	<div class="pkwt-info-box" style="margin-bottom:14px;">
		<strong><?php esc_html_e( 'Smart Recommendations', 'powerplus-toolkit' ); ?></strong>
		<ul class="pkwt-issue-list">
			<li><?php echo is_plugin_active( 'elementor/elementor.php' ) ? esc_html__( 'Elementor detected: keep Auth Redirects enabled for custom auth pages.', 'powerplus-toolkit' ) : esc_html__( 'Elementor not detected: install Elementor to use custom auth widgets.', 'powerplus-toolkit' ); ?></li>
			<li><?php echo is_plugin_active( 'woocommerce/woocommerce.php' ) ? esc_html__( 'WooCommerce detected: enable Duplicate for product template workflows.', 'powerplus-toolkit' ) : esc_html__( 'WooCommerce not detected: Duplicate still works for posts/pages/CPT.', 'powerplus-toolkit' ); ?></li>
			<li><?php echo ! empty( $svg_settings['dpp_svg_enabled'] ) ? esc_html__( 'SVG enabled: use Strict or Paranoid mode for client sites.', 'powerplus-toolkit' ) : esc_html__( 'SVG disabled: enable only if your workflow requires SVG uploads.', 'powerplus-toolkit' ); ?></li>
		</ul>
	</div>

	<?php if ( 'module_saved' === $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Feature updated.', 'powerplus-toolkit' ); ?></p></div>
	<?php elseif ( 'module_error' === $notice ) : ?>
		<div class="notice notice-error is-dismissible"><p><?php esc_html_e( 'Could not update feature.', 'powerplus-toolkit' ); ?></p></div>
	<?php endif; ?>

	<div class="pkwt-feature-table pkwt-feature-table-wide">
		<div class="pkwt-feature-row pkwt-feature-head pkwt-feature-columns-5">
			<div><?php esc_html_e( 'Element', 'powerplus-toolkit' ); ?></div>
			<div><?php esc_html_e( 'Status', 'powerplus-toolkit' ); ?></div>
			<div><?php esc_html_e( 'Usage', 'powerplus-toolkit' ); ?></div>
			<div><?php esc_html_e( 'Module', 'powerplus-toolkit' ); ?></div>
			<div><?php esc_html_e( 'Action', 'powerplus-toolkit' ); ?></div>
		</div>
		<?php foreach ( $modules as $module ) : ?>
			<?php $next_state = ! empty( $module['active'] ) ? 'off' : 'on'; ?>
			<div class="pkwt-feature-row pkwt-feature-columns-5">
				<div class="pkwt-feature-name">
					<span class="dashicons <?php echo esc_attr( (string) $module['icon'] ); ?>" aria-hidden="true"></span>
					<span><?php echo esc_html( $module['label'] ); ?></span>
				</div>
				<div class="pkwt-feature-toggle">
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="pkwt-module-toggle-form" data-pkwt-module-toggle-form>
						<?php wp_nonce_field( 'pkwt_toggle_module' ); ?>
						<input type="hidden" name="action" value="pkwt_toggle_module" />
						<input type="hidden" name="module" value="<?php echo esc_attr( $module['key'] ); ?>" />
						<input type="hidden" name="state" value="<?php echo esc_attr( $next_state ); ?>" data-pkwt-module-state />
						<label class="pkwt-switch" aria-label="<?php echo esc_attr( $module['label'] ); ?>">
							<input type="checkbox" <?php checked( ! empty( $module['active'] ) ); ?> data-pkwt-module-switch />
							<span class="pkwt-switch-track"></span>
						</label>
					</form>
				</div>
				<div><a class="button button-secondary" href="<?php echo esc_url( $module['link'] ); ?>"><?php echo esc_html( $module['link_label'] ); ?></a></div>
				<div><?php echo esc_html( $module['plugin'] ); ?></div>
				<div><a href="<?php echo esc_url( $module['link'] ); ?>"><?php esc_html_e( 'Edit', 'powerplus-toolkit' ); ?></a></div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<?php // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>
