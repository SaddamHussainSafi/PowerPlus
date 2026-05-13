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
		'label'      => __( 'Auth Redirects', 'powerkit-powerful-tools-for-your-website' ),
		'active'     => ! empty( $pkwt_settings['enabled'] ),
		'icon'       => 'dashicons-lock',
		'plugin'     => __( 'PowerKit - Powerful Tools For Your Website', 'powerkit-powerful-tools-for-your-website' ),
		'link'       => admin_url( 'admin.php?page=pkwt-settings-general' ),
		'link_label' => __( 'Open', 'powerkit-powerful-tools-for-your-website' ),
	),
	array(
		'key'        => 'duplicate',
		'label'      => __( 'Duplicate', 'powerkit-powerful-tools-for-your-website' ),
		'active'     => ! empty( $dpp_settings['enabled'] ),
		'icon'       => 'dashicons-admin-page',
		'plugin'     => __( 'Duplicate Module', 'powerkit-powerful-tools-for-your-website' ),
		'link'       => admin_url( 'admin.php?page=pkwt-settings-duplicate' ),
		'link_label' => __( 'Open', 'powerkit-powerful-tools-for-your-website' ),
	),
	array(
		'key'        => 'svg',
		'label'      => __( 'SVG Upload', 'powerkit-powerful-tools-for-your-website' ),
		'active'     => ! empty( $svg_settings['dpp_svg_enabled'] ),
		'icon'       => 'dashicons-format-image',
		'plugin'     => __( 'SVG Security', 'powerkit-powerful-tools-for-your-website' ),
		'link'       => admin_url( 'admin.php?page=pkwt-settings-svg-upload' ),
		'link_label' => __( 'Open', 'powerkit-powerful-tools-for-your-website' ),
	),
	array(
		'key'        => 'ghost',
		'label'      => __( 'Ghost Mode', 'powerkit-powerful-tools-for-your-website' ),
		'active'     => ! empty( $ghost_settings['dpp_ghost_enabled'] ),
		'icon'       => 'dashicons-shield',
		'plugin'     => __( 'Ghost Protection', 'powerkit-powerful-tools-for-your-website' ),
		'link'       => admin_url( 'admin.php?page=pkwt-settings-ghost-mode' ),
		'link_label' => __( 'Open', 'powerkit-powerful-tools-for-your-website' ),
	),
	array(
		'key'        => 'classic',
		'label'      => __( 'Classic Editor', 'powerkit-powerful-tools-for-your-website' ),
		'active'     => ! empty( $classic_settings['dpp_classic_enabled'] ),
		'icon'       => 'dashicons-edit',
		'plugin'     => __( 'Editor Mode', 'powerkit-powerful-tools-for-your-website' ),
		'link'       => admin_url( 'admin.php?page=pkwt-settings-classic-editor' ),
		'link_label' => __( 'Open', 'powerkit-powerful-tools-for-your-website' ),
	),
);

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin notice key.
$notice = isset( $_GET['pkwt_notice'] ) ? sanitize_key( wp_unslash( $_GET['pkwt_notice'] ) ) : '';
?>
<div class="wrap pkwt-ui pkwt-overview-wrap">
	<h1><?php esc_html_e( 'PowerKit - Powerful Tools For Your Website', 'powerkit-powerful-tools-for-your-website' ); ?></h1>
	<p class="description"><?php esc_html_e( 'Developed by Saddam Hussain Safi | Incepta Studio', 'powerkit-powerful-tools-for-your-website' ); ?></p>

	<div class="pkwt-guide-panel" data-pkwt-guide-panel>
		<div>
			<strong><?php esc_html_e( 'Quick Guided Setup', 'powerkit-powerful-tools-for-your-website' ); ?></strong>
			<p><?php esc_html_e( '1) Enable modules on this page. 2) Open each module and configure. 3) Save changes and run module test actions.', 'powerkit-powerful-tools-for-your-website' ); ?></p>
		</div>
		<button type="button" class="button button-secondary" data-pkwt-guide-dismiss><?php esc_html_e( 'Hide', 'powerkit-powerful-tools-for-your-website' ); ?></button>
	</div>

	<?php
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	?>
	<div class="pkwt-info-box" style="margin-bottom:14px;">
		<strong><?php esc_html_e( 'Smart Recommendations', 'powerkit-powerful-tools-for-your-website' ); ?></strong>
		<ul class="pkwt-issue-list">
			<li><?php echo is_plugin_active( 'elementor/elementor.php' ) ? esc_html__( 'Elementor detected: keep Auth Redirects enabled for custom auth pages.', 'powerkit-powerful-tools-for-your-website' ) : esc_html__( 'Elementor not detected: install Elementor to use custom auth widgets.', 'powerkit-powerful-tools-for-your-website' ); ?></li>
			<li><?php echo is_plugin_active( 'woocommerce/woocommerce.php' ) ? esc_html__( 'WooCommerce detected: enable Duplicate for product template workflows.', 'powerkit-powerful-tools-for-your-website' ) : esc_html__( 'WooCommerce not detected: Duplicate still works for posts/pages/CPT.', 'powerkit-powerful-tools-for-your-website' ); ?></li>
			<li><?php echo ! empty( $svg_settings['dpp_svg_enabled'] ) ? esc_html__( 'SVG enabled: use Strict or Paranoid mode for client sites.', 'powerkit-powerful-tools-for-your-website' ) : esc_html__( 'SVG disabled: enable only if your workflow requires SVG uploads.', 'powerkit-powerful-tools-for-your-website' ); ?></li>
		</ul>
	</div>

	<?php if ( 'module_saved' === $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Feature updated.', 'powerkit-powerful-tools-for-your-website' ); ?></p></div>
	<?php elseif ( 'module_error' === $notice ) : ?>
		<div class="notice notice-error is-dismissible"><p><?php esc_html_e( 'Could not update feature.', 'powerkit-powerful-tools-for-your-website' ); ?></p></div>
	<?php endif; ?>

	<div class="pkwt-feature-table pkwt-feature-table-wide">
		<div class="pkwt-feature-row pkwt-feature-head pkwt-feature-columns-5">
			<div><?php esc_html_e( 'Element', 'powerkit-powerful-tools-for-your-website' ); ?></div>
			<div><?php esc_html_e( 'Status', 'powerkit-powerful-tools-for-your-website' ); ?></div>
			<div><?php esc_html_e( 'Usage', 'powerkit-powerful-tools-for-your-website' ); ?></div>
			<div><?php esc_html_e( 'Module', 'powerkit-powerful-tools-for-your-website' ); ?></div>
			<div><?php esc_html_e( 'Action', 'powerkit-powerful-tools-for-your-website' ); ?></div>
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
				<div><a href="<?php echo esc_url( $module['link'] ); ?>"><?php esc_html_e( 'Edit', 'powerkit-powerful-tools-for-your-website' ); ?></a></div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<?php // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>
