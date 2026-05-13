<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View template file; variables are scoped to this included file.

/**
 * SVG settings view.
 *
 * @package PKWT
 */

$dpp_admin    = new \PKWT\Includes\Class_PKWT_DPP_Admin();
$svg          = $dpp_admin->get_svg_settings();
$wp_roles     = wp_roles();
$all_roles    = $wp_roles ? $wp_roles->roles : array();
$scan_results = get_transient( 'pkwt_svg_scan_results' );
$svg_log      = get_option( 'pkwt_dpp_svg_log', array() );
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin notice key.
$notice       = isset( $_GET['pkwt_notice'] ) ? sanitize_key( wp_unslash( $_GET['pkwt_notice'] ) ) : '';
?>
<div class="wrap pkwt-ui dpp-feature-panel">
	<h1><?php esc_html_e( 'SVG Upload', 'powerkit-powerful-tools-for-your-website' ); ?></h1>
	<details class="pkwt-learn-more">
		<summary><?php esc_html_e( 'Learn More', 'powerkit-powerful-tools-for-your-website' ); ?></summary>
		<p><?php esc_html_e( 'Use Strict or Paranoid sanitization on production sites. Keep uploads limited to trusted roles and use scan action after migration imports.', 'powerkit-powerful-tools-for-your-website' ); ?></p>
	</details>

	<?php if ( 'svg_scanned' === $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'SVG scan completed.', 'powerkit-powerful-tools-for-your-website' ); ?></p></div>
	<?php elseif ( 'preset_applied' === $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Recommended settings applied.', 'powerkit-powerful-tools-for-your-website' ); ?></p></div>
	<?php endif; ?>

	<form method="post" action="options.php">
		<?php settings_fields( 'pkwt_dpp_settings_group' ); ?>

		<div class="pkwt-savebar pkwt-savebar-top">
			<div class="pkwt-savebar-text"><?php esc_html_e( 'Save to apply upload restrictions and sanitization.', 'powerkit-powerful-tools-for-your-website' ); ?></div>
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'powerkit-powerful-tools-for-your-website' ); ?></button>
		</div>

		<div class="pkwt-card-grid">
			<section class="pkwt-card">
				<h2><?php esc_html_e( 'Module', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<div class="pkwt-field pkwt-field-inline">
					<label class="pkwt-label" for="dpp-svg-enabled"><?php esc_html_e( 'Enable SVG uploads', 'powerkit-powerful-tools-for-your-website' ); ?></label>
					<span class="pkwt-switch-wrap"><input id="dpp-svg-enabled" type="checkbox" class="dpp-toggle" name="pkwt_dpp_svg_settings[dpp_svg_enabled]" value="1" <?php checked( ! empty( $svg['dpp_svg_enabled'] ) ); ?> /></span>
				</div>
				<div class="pkwt-field pkwt-field-inline">
					<label class="pkwt-label" for="dpp-svg-preview"><?php esc_html_e( 'Show SVG preview in Media Library', 'powerkit-powerful-tools-for-your-website' ); ?></label>
					<span class="pkwt-switch-wrap"><input id="dpp-svg-preview" type="checkbox" class="dpp-toggle" name="pkwt_dpp_svg_settings[dpp_svg_preview]" value="1" <?php checked( ! empty( $svg['dpp_svg_preview'] ) ); ?> <?php disabled( empty( $svg['dpp_svg_enabled'] ) ); ?> /></span>
				</div>
				<div class="pkwt-field pkwt-field-inline">
					<label class="pkwt-label" for="dpp-svg-log"><?php esc_html_e( 'Blocked elements log', 'powerkit-powerful-tools-for-your-website' ); ?></label>
					<span class="pkwt-switch-wrap"><input id="dpp-svg-log" type="checkbox" class="dpp-toggle" name="pkwt_dpp_svg_settings[dpp_svg_blocked_log]" value="1" <?php checked( ! empty( $svg['dpp_svg_blocked_log'] ) ); ?> <?php disabled( empty( $svg['dpp_svg_enabled'] ) ); ?> /></span>
				</div>
			</section>

			<section class="pkwt-card">
				<h2><?php esc_html_e( 'Sanitization', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<fieldset <?php disabled( empty( $svg['dpp_svg_enabled'] ) ); ?>>
					<div class="pkwt-field">
						<label class="pkwt-label" for="dpp-svg-size"><?php esc_html_e( 'Max SVG file size (KB)', 'powerkit-powerful-tools-for-your-website' ); ?></label>
						<input id="dpp-svg-size" type="number" min="64" max="4096" name="pkwt_dpp_svg_settings[dpp_svg_max_size_kb]" value="<?php echo esc_attr( (string) $svg['dpp_svg_max_size_kb'] ); ?>" />
					</div>
					<div class="pkwt-field">
						<span class="pkwt-label"><?php esc_html_e( 'Sanitization strictness', 'powerkit-powerful-tools-for-your-website' ); ?></span>
						<label><input type="radio" name="pkwt_dpp_svg_settings[dpp_svg_strictness]" value="standard" <?php checked( $svg['dpp_svg_strictness'], 'standard' ); ?> /> <?php esc_html_e( 'Standard', 'powerkit-powerful-tools-for-your-website' ); ?></label><br />
						<label><input type="radio" name="pkwt_dpp_svg_settings[dpp_svg_strictness]" value="strict" <?php checked( $svg['dpp_svg_strictness'], 'strict' ); ?> /> <?php esc_html_e( 'Strict', 'powerkit-powerful-tools-for-your-website' ); ?></label><br />
						<label><input type="radio" name="pkwt_dpp_svg_settings[dpp_svg_strictness]" value="paranoid" <?php checked( $svg['dpp_svg_strictness'], 'paranoid' ); ?> /> <?php esc_html_e( 'Paranoid', 'powerkit-powerful-tools-for-your-website' ); ?></label>
					</div>
				</fieldset>
			</section>

			<section class="pkwt-card pkwt-card-span-2">
				<h2><?php esc_html_e( 'Allowed Roles', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<fieldset <?php disabled( empty( $svg['dpp_svg_enabled'] ) ); ?>>
					<div class="pkwt-field-grid">
						<?php foreach ( $all_roles as $role_key => $role_data ) : ?>
							<label>
								<input type="checkbox" name="pkwt_dpp_svg_settings[dpp_svg_roles][]" value="<?php echo esc_attr( $role_key ); ?>" <?php checked( in_array( $role_key, $svg['dpp_svg_roles'], true ) ); ?> />
								<?php echo esc_html( $role_data['name'] ); ?>
							</label>
						<?php endforeach; ?>
					</div>
				</fieldset>
			</section>
		</div>

		<div class="pkwt-savebar pkwt-savebar-bottom">
			<div class="pkwt-savebar-text"><?php esc_html_e( 'Changes apply after save.', 'powerkit-powerful-tools-for-your-website' ); ?></div>
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'powerkit-powerful-tools-for-your-website' ); ?></button>
		</div>
	</form>

	<div class="pkwt-card" style="margin-top:14px;">
		<h2><?php esc_html_e( 'Security Scan', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-pkwt-async-form>
			<?php wp_nonce_field( 'pkwt_svg_scan' ); ?>
			<input type="hidden" name="action" value="pkwt_svg_scan" />
			<button type="submit" class="button button-secondary"><?php esc_html_e( 'Scan Media Library for Unsafe SVGs', 'powerkit-powerful-tools-for-your-website' ); ?></button>
		</form>
	</div>

	<?php if ( ! empty( $scan_results ) && is_array( $scan_results ) ) : ?>
		<div class="pkwt-card" style="margin-top:14px;">
			<h2><?php esc_html_e( 'Scan Results', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
			<table class="widefat striped">
				<thead><tr><th><?php esc_html_e( 'File', 'powerkit-powerful-tools-for-your-website' ); ?></th><th><?php esc_html_e( 'Issue', 'powerkit-powerful-tools-for-your-website' ); ?></th><th><?php esc_html_e( 'Status', 'powerkit-powerful-tools-for-your-website' ); ?></th></tr></thead>
				<tbody>
				<?php foreach ( $scan_results as $row ) : ?>
					<tr>
						<td><?php echo esc_html( isset( $row['file'] ) ? (string) $row['file'] : '' ); ?></td>
						<td><?php echo esc_html( isset( $row['issue'] ) ? (string) $row['issue'] : '' ); ?></td>
						<td><?php echo esc_html( isset( $row['status'] ) ? (string) $row['status'] : '' ); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $svg_log ) && is_array( $svg_log ) ) : ?>
		<div class="pkwt-card" style="margin-top:14px;">
			<h2><?php esc_html_e( 'Sanitization Log', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
			<table class="widefat striped">
				<thead><tr><th><?php esc_html_e( 'Time', 'powerkit-powerful-tools-for-your-website' ); ?></th><th><?php esc_html_e( 'File', 'powerkit-powerful-tools-for-your-website' ); ?></th><th><?php esc_html_e( 'Removed', 'powerkit-powerful-tools-for-your-website' ); ?></th></tr></thead>
				<tbody>
				<?php foreach ( array_reverse( $svg_log ) as $entry ) : ?>
					<tr>
						<td><?php echo esc_html( isset( $entry['time'] ) ? wp_date( 'Y-m-d H:i', (int) $entry['time'] ) : '' ); ?></td>
						<td><?php echo esc_html( isset( $entry['file'] ) ? (string) $entry['file'] : '' ); ?></td>
						<td><?php echo esc_html( isset( $entry['removed'] ) ? (string) $entry['removed'] : '' ); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</div>
<?php // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>
