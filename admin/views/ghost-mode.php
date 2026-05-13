<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View template file; variables are scoped to this included file.

/**
 * Ghost mode settings view.
 *
 * @package PKWT
 */

$dpp_admin  = new \PKWT\Includes\Class_PKWT_DPP_Admin();
$ghost      = $dpp_admin->get_ghost_settings();
$ghost_last = get_option( 'pkwt_dpp_ghost_last_test', array() );
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin notice key.
$notice     = isset( $_GET['pkwt_notice'] ) ? sanitize_key( wp_unslash( $_GET['pkwt_notice'] ) ) : '';
if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
$installed_plugins = get_plugins();
$default_aliases   = array(
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
?>
<div class="wrap pkwt-ui dpp-feature-panel">
	<h1><?php esc_html_e( 'Ghost Mode', 'powerkit-powerful-tools-for-your-website' ); ?></h1>
	<details class="pkwt-learn-more">
		<summary><?php esc_html_e( 'Learn More', 'powerkit-powerful-tools-for-your-website' ); ?></summary>
		<p><?php esc_html_e( 'Enable core hardening first, then advanced alias and masking settings. Run detection test after each change set to validate score impact.', 'powerkit-powerful-tools-for-your-website' ); ?></p>
	</details>

	<?php if ( 'ghost_tested' === $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Ghost mode detection test completed.', 'powerkit-powerful-tools-for-your-website' ); ?></p></div>
	<?php elseif ( 'preset_applied' === $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Recommended settings applied.', 'powerkit-powerful-tools-for-your-website' ); ?></p></div>
	<?php endif; ?>

	<form method="post" action="options.php">
		<?php settings_fields( 'pkwt_dpp_settings_group' ); ?>

		<div class="pkwt-savebar pkwt-savebar-top">
			<div class="pkwt-savebar-text"><?php esc_html_e( 'Ghost settings are live only after Save Changes.', 'powerkit-powerful-tools-for-your-website' ); ?></div>
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'powerkit-powerful-tools-for-your-website' ); ?></button>
		</div>

		<div class="pkwt-card-grid">
			<section class="pkwt-card">
				<h2><?php esc_html_e( 'Module', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<div class="pkwt-field pkwt-field-inline">
					<label class="pkwt-label" for="ghost-enabled"><?php esc_html_e( 'Enable Ghost Mode', 'powerkit-powerful-tools-for-your-website' ); ?></label>
					<span class="pkwt-switch-wrap"><input id="ghost-enabled" type="checkbox" class="dpp-toggle" name="pkwt_dpp_ghost_settings[dpp_ghost_enabled]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_enabled'] ) ); ?> /></span>
				</div>
				<div class="pkwt-field">
					<label class="pkwt-label" for="ghost-cms-name"><?php esc_html_e( 'Custom CMS Name', 'powerkit-powerful-tools-for-your-website' ); ?></label>
					<input id="ghost-cms-name" type="text" name="pkwt_dpp_ghost_settings[dpp_ghost_custom_cms_name]" value="<?php echo esc_attr( (string) $ghost['dpp_ghost_custom_cms_name'] ); ?>" <?php disabled( empty( $ghost['dpp_ghost_enabled'] ) ); ?> />
				</div>
				<div class="pkwt-field pkwt-field-inline">
					<label class="pkwt-label" for="ghost-show-advanced"><?php esc_html_e( 'Show advanced controls', 'powerkit-powerful-tools-for-your-website' ); ?></label>
					<span class="pkwt-switch-wrap"><input id="ghost-show-advanced" type="checkbox" class="dpp-toggle" data-pkwt-toggle-advanced="ghost" /></span>
				</div>
			</section>

			<section class="pkwt-card">
				<h2><?php esc_html_e( 'Core hardening', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<fieldset <?php disabled( empty( $ghost['dpp_ghost_enabled'] ) ); ?>>
					<div class="pkwt-field pkwt-field-inline"><label class="pkwt-label" for="ghost-generator"><?php esc_html_e( 'Remove generator meta', 'powerkit-powerful-tools-for-your-website' ); ?></label><span class="pkwt-switch-wrap"><input id="ghost-generator" type="checkbox" class="dpp-toggle" name="pkwt_dpp_ghost_settings[dpp_ghost_remove_generator]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_remove_generator'] ) ); ?> /></span></div>
					<div class="pkwt-field pkwt-field-inline"><label class="pkwt-label" for="ghost-ver"><?php esc_html_e( 'Strip version query strings', 'powerkit-powerful-tools-for-your-website' ); ?></label><span class="pkwt-switch-wrap"><input id="ghost-ver" type="checkbox" class="dpp-toggle" name="pkwt_dpp_ghost_settings[dpp_ghost_strip_version_urls]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_strip_version_urls'] ) ); ?> /></span></div>
					<div class="pkwt-field pkwt-field-inline"><label class="pkwt-label" for="ghost-xmlrpc"><?php esc_html_e( 'Disable XML-RPC', 'powerkit-powerful-tools-for-your-website' ); ?></label><span class="pkwt-switch-wrap"><input id="ghost-xmlrpc" type="checkbox" class="dpp-toggle" name="pkwt_dpp_ghost_settings[dpp_ghost_disable_xmlrpc]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_disable_xmlrpc'] ) ); ?> /></span></div>
					<div class="pkwt-field pkwt-field-inline"><label class="pkwt-label" for="ghost-rest-users"><?php esc_html_e( 'Hide REST users endpoint', 'powerkit-powerful-tools-for-your-website' ); ?></label><span class="pkwt-switch-wrap"><input id="ghost-rest-users" type="checkbox" class="dpp-toggle" name="pkwt_dpp_ghost_settings[dpp_ghost_hide_rest_users]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_hide_rest_users'] ) ); ?> /></span></div>
					<div class="pkwt-field pkwt-field-inline"><label class="pkwt-label" for="ghost-probes"><?php esc_html_e( 'Block common probes', 'powerkit-powerful-tools-for-your-website' ); ?></label><span class="pkwt-switch-wrap"><input id="ghost-probes" type="checkbox" class="dpp-toggle" name="pkwt_dpp_ghost_settings[dpp_ghost_block_probes]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_block_probes'] ) ); ?> /></span></div>
				</fieldset>
			</section>

			<section class="pkwt-card" data-pkwt-advanced-group="ghost">
				<h2><?php esc_html_e( 'URL aliases', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<fieldset <?php disabled( empty( $ghost['dpp_ghost_enabled'] ) ); ?>>
					<div class="pkwt-field-grid">
						<div class="pkwt-field"><label class="pkwt-label" for="ghost-alias-content"><?php esc_html_e( 'wp-content alias', 'powerkit-powerful-tools-for-your-website' ); ?></label><input id="ghost-alias-content" type="text" name="pkwt_dpp_ghost_settings[dpp_ghost_alias_content]" value="<?php echo esc_attr( (string) $ghost['dpp_ghost_alias_content'] ); ?>" /></div>
						<div class="pkwt-field"><label class="pkwt-label" for="ghost-alias-includes"><?php esc_html_e( 'wp-includes alias', 'powerkit-powerful-tools-for-your-website' ); ?></label><input id="ghost-alias-includes" type="text" name="pkwt_dpp_ghost_settings[dpp_ghost_alias_includes]" value="<?php echo esc_attr( (string) $ghost['dpp_ghost_alias_includes'] ); ?>" /></div>
						<div class="pkwt-field"><label class="pkwt-label" for="ghost-alias-themes"><?php esc_html_e( 'themes alias', 'powerkit-powerful-tools-for-your-website' ); ?></label><input id="ghost-alias-themes" type="text" name="pkwt_dpp_ghost_settings[dpp_ghost_alias_themes]" value="<?php echo esc_attr( (string) $ghost['dpp_ghost_alias_themes'] ); ?>" /></div>
						<div class="pkwt-field"><label class="pkwt-label" for="ghost-alias-plugins"><?php esc_html_e( 'plugins alias', 'powerkit-powerful-tools-for-your-website' ); ?></label><input id="ghost-alias-plugins" type="text" name="pkwt_dpp_ghost_settings[dpp_ghost_alias_plugins]" value="<?php echo esc_attr( (string) $ghost['dpp_ghost_alias_plugins'] ); ?>" /></div>
					</div>
					<div class="pkwt-field"><label class="pkwt-label" for="ghost-rest-prefix"><?php esc_html_e( 'Custom REST base', 'powerkit-powerful-tools-for-your-website' ); ?></label><input id="ghost-rest-prefix" type="text" name="pkwt_dpp_ghost_settings[dpp_ghost_rest_prefix]" value="<?php echo esc_attr( (string) $ghost['dpp_ghost_rest_prefix'] ); ?>" /></div>
				</fieldset>
			</section>

			<section class="pkwt-card" data-pkwt-advanced-group="ghost">
				<h2><?php esc_html_e( 'Additional source/API signals', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<fieldset <?php disabled( empty( $ghost['dpp_ghost_enabled'] ) ); ?>>
					<div class="pkwt-field-grid">
						<label><input type="checkbox" class="dpp-toggle" name="pkwt_dpp_ghost_settings[dpp_ghost_remove_rsd]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_remove_rsd'] ) ); ?> /> <?php esc_html_e( 'Remove RSD link', 'powerkit-powerful-tools-for-your-website' ); ?></label>
						<label><input type="checkbox" class="dpp-toggle" name="pkwt_dpp_ghost_settings[dpp_ghost_remove_wlw]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_remove_wlw'] ) ); ?> /> <?php esc_html_e( 'Remove WLW manifest', 'powerkit-powerful-tools-for-your-website' ); ?></label>
						<label><input type="checkbox" class="dpp-toggle" name="pkwt_dpp_ghost_settings[dpp_ghost_remove_shortlink]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_remove_shortlink'] ) ); ?> /> <?php esc_html_e( 'Remove shortlink', 'powerkit-powerful-tools-for-your-website' ); ?></label>
						<label><input type="checkbox" class="dpp-toggle" name="pkwt_dpp_ghost_settings[dpp_ghost_remove_feed_links]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_remove_feed_links'] ) ); ?> /> <?php esc_html_e( 'Remove feed links', 'powerkit-powerful-tools-for-your-website' ); ?></label>
						<label><input type="checkbox" class="dpp-toggle" name="pkwt_dpp_ghost_settings[dpp_ghost_remove_emoji]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_remove_emoji'] ) ); ?> /> <?php esc_html_e( 'Remove emoji scripts', 'powerkit-powerful-tools-for-your-website' ); ?></label>
						<label><input type="checkbox" class="dpp-toggle" name="pkwt_dpp_ghost_settings[dpp_ghost_remove_oembed]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_remove_oembed'] ) ); ?> /> <?php esc_html_e( 'Remove oEmbed links', 'powerkit-powerful-tools-for-your-website' ); ?></label>
						<label><input type="checkbox" class="dpp-toggle" name="pkwt_dpp_ghost_settings[dpp_ghost_remove_rest_link]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_remove_rest_link'] ) ); ?> /> <?php esc_html_e( 'Remove REST link', 'powerkit-powerful-tools-for-your-website' ); ?></label>
						<label><input type="checkbox" class="dpp-toggle" name="pkwt_dpp_ghost_settings[dpp_ghost_disable_author_archives]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_disable_author_archives'] ) ); ?> /> <?php esc_html_e( 'Disable author archives', 'powerkit-powerful-tools-for-your-website' ); ?></label>
					</div>
				</fieldset>
			</section>

			<section class="pkwt-card pkwt-card-span-2" data-pkwt-advanced-group="ghost">
				<h2><?php esc_html_e( 'Plugin name masking', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<fieldset <?php disabled( empty( $ghost['dpp_ghost_enabled'] ) ); ?>>
					<div class="pkwt-field pkwt-field-inline">
						<label class="pkwt-label" for="ghost-mask-names"><?php esc_html_e( 'Mask plugin names in admin', 'powerkit-powerful-tools-for-your-website' ); ?></label>
						<span class="pkwt-switch-wrap"><input id="ghost-mask-names" type="checkbox" class="dpp-toggle" name="pkwt_dpp_ghost_settings[dpp_ghost_mask_plugin_names]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_mask_plugin_names'] ) ); ?> /></span>
					</div>
					<table class="widefat striped">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Installed Plugin', 'powerkit-powerful-tools-for-your-website' ); ?></th>
								<th><?php esc_html_e( 'Custom Display Name', 'powerkit-powerful-tools-for-your-website' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $installed_plugins as $plugin_file => $plugin_data ) : ?>
								<?php
								$map         = isset( $ghost['dpp_ghost_plugin_name_map'] ) && is_array( $ghost['dpp_ghost_plugin_name_map'] ) ? $ghost['dpp_ghost_plugin_name_map'] : array();
								$default_idx = absint( crc32( (string) $plugin_file ) ) % count( $default_aliases );
								$default     = $default_aliases[ $default_idx ] . ' ' . ( ( $default_idx % 5 ) + 1 );
								$current     = isset( $map[ $plugin_file ] ) && '' !== (string) $map[ $plugin_file ] ? (string) $map[ $plugin_file ] : $default;
								?>
								<tr>
									<td><?php echo esc_html( isset( $plugin_data['Name'] ) ? (string) $plugin_data['Name'] : $plugin_file ); ?></td>
									<td><input type="text" name="pkwt_dpp_ghost_settings[dpp_ghost_plugin_name_map][<?php echo esc_attr( $plugin_file ); ?>]" value="<?php echo esc_attr( $current ); ?>" /></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</fieldset>
			</section>

			<section class="pkwt-card" data-pkwt-advanced-group="ghost">
				<h2><?php esc_html_e( 'Auto test', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<fieldset <?php disabled( empty( $ghost['dpp_ghost_enabled'] ) ); ?>>
					<div class="pkwt-field pkwt-field-inline"><label class="pkwt-label" for="ghost-auto-test"><?php esc_html_e( 'Run weekly and email if weak', 'powerkit-powerful-tools-for-your-website' ); ?></label><span class="pkwt-switch-wrap"><input id="ghost-auto-test" type="checkbox" class="dpp-toggle" name="pkwt_dpp_ghost_settings[dpp_ghost_auto_test]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_auto_test'] ) ); ?> /></span></div>
					<div class="pkwt-field"><label class="pkwt-label" for="ghost-threshold"><?php esc_html_e( 'Alert threshold score', 'powerkit-powerful-tools-for-your-website' ); ?></label><input id="ghost-threshold" type="number" min="1" max="11" name="pkwt_dpp_ghost_settings[dpp_ghost_auto_test_threshold]" value="<?php echo esc_attr( (string) $ghost['dpp_ghost_auto_test_threshold'] ); ?>" /></div>
				</fieldset>
			</section>
		</div>

		<div class="pkwt-savebar pkwt-savebar-bottom">
			<div class="pkwt-savebar-text"><?php esc_html_e( 'Changes apply after save.', 'powerkit-powerful-tools-for-your-website' ); ?></div>
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'powerkit-powerful-tools-for-your-website' ); ?></button>
		</div>
	</form>

	<div class="pkwt-card" style="margin-top:14px;">
		<h2><?php esc_html_e( 'Detection test', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-pkwt-async-form>
			<?php wp_nonce_field( 'dpp_ghost_test' ); ?>
			<input type="hidden" name="action" value="dpp_ghost_test" />
			<button type="submit" class="button button-secondary"><?php esc_html_e( 'Test My Site Now', 'powerkit-powerful-tools-for-your-website' ); ?></button>
		</form>
		<?php if ( ! empty( $ghost_last ) && is_array( $ghost_last ) ) : ?>
			<p><?php echo esc_html( sprintf( 'Ghost Score: %1$d / %2$d', isset( $ghost_last['score'] ) ? (int) $ghost_last['score'] : 0, isset( $ghost_last['total'] ) ? (int) $ghost_last['total'] : 0 ) ); ?></p>
			<p><?php echo esc_html( sprintf( 'Last test: %s', isset( $ghost_last['time'] ) ? wp_date( 'Y-m-d H:i', (int) $ghost_last['time'] ) : '-' ) ); ?></p>
		<?php endif; ?>
	</div>

	<div class="pkwt-info-box pkwt-info-box-warning" style="margin-top:14px;">
		<?php esc_html_e( 'Ghost Mode reduces common detection signals but cannot make WordPress 100% undetectable in all scenarios.', 'powerkit-powerful-tools-for-your-website' ); ?>
	</div>
</div>
<?php // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>
