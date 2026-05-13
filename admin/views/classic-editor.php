<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View template file; variables are scoped to this included file.

/**
 * Classic editor settings view.
 *
 * @package PKWT
 */

$classic = new \PKWT\Includes\Class_PKWT_DPP_Classic();
$cfg     = $classic->get_settings();
$status  = $classic->get_status_rows();
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin notice key.
$notice  = isset( $_GET['pkwt_notice'] ) ? sanitize_key( wp_unslash( $_GET['pkwt_notice'] ) ) : '';
$types   = get_post_types(
	array(
		'public'  => true,
		'show_ui' => true,
	),
	'objects'
);
?>
<div class="wrap pkwt-ui dpp-feature-panel">
	<h1><?php esc_html_e( 'Classic Editor', 'powerkit-powerful-tools-for-your-website' ); ?></h1>
	<details class="pkwt-learn-more">
		<summary><?php esc_html_e( 'Learn More', 'powerkit-powerful-tools-for-your-website' ); ?></summary>
		<p><?php esc_html_e( 'Turn this on only when your editorial workflow requires TinyMCE. Use post type scope and cleanup toggles to avoid unnecessary Gutenberg asset loading.', 'powerkit-powerful-tools-for-your-website' ); ?></p>
	</details>

	<?php if ( 'preset_applied' === $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Recommended settings applied.', 'powerkit-powerful-tools-for-your-website' ); ?></p></div>
	<?php endif; ?>

	<form method="post" action="options.php">
		<?php settings_fields( 'pkwt_dpp_settings_group' ); ?>

		<div class="pkwt-savebar pkwt-savebar-top">
			<div class="pkwt-savebar-text"><?php esc_html_e( 'Save to apply editor mode changes.', 'powerkit-powerful-tools-for-your-website' ); ?></div>
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'powerkit-powerful-tools-for-your-website' ); ?></button>
		</div>

		<div class="pkwt-card-grid">
			<section class="pkwt-card">
				<h2><?php esc_html_e( 'Module', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<div class="pkwt-field pkwt-field-inline">
					<label class="pkwt-label" for="classic-enabled"><?php esc_html_e( 'Enable Classic Editor Mode', 'powerkit-powerful-tools-for-your-website' ); ?></label>
					<span class="pkwt-switch-wrap"><input id="classic-enabled" type="checkbox" class="dpp-toggle" name="pkwt_dpp_classic_settings[dpp_classic_enabled]" value="1" <?php checked( ! empty( $cfg['dpp_classic_enabled'] ) ); ?> /></span>
				</div>
				<div class="pkwt-field pkwt-field-inline">
					<label class="pkwt-label" for="classic-show-advanced"><?php esc_html_e( 'Show advanced controls', 'powerkit-powerful-tools-for-your-website' ); ?></label>
					<span class="pkwt-switch-wrap"><input id="classic-show-advanced" type="checkbox" class="dpp-toggle" data-pkwt-toggle-advanced="classic" /></span>
				</div>
			</section>

			<section class="pkwt-card">
				<h2><?php esc_html_e( 'Scope', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<fieldset <?php disabled( empty( $cfg['dpp_classic_enabled'] ) ); ?>>
					<div class="pkwt-field">
						<span class="pkwt-label"><?php esc_html_e( 'Apply to', 'powerkit-powerful-tools-for-your-website' ); ?></span>
						<label><input type="radio" name="pkwt_dpp_classic_settings[dpp_classic_scope]" value="all" <?php checked( $cfg['dpp_classic_scope'], 'all' ); ?> /> <?php esc_html_e( 'All posts', 'powerkit-powerful-tools-for-your-website' ); ?></label><br />
						<label><input type="radio" name="pkwt_dpp_classic_settings[dpp_classic_scope]" value="new" <?php checked( $cfg['dpp_classic_scope'], 'new' ); ?> /> <?php esc_html_e( 'New posts only', 'powerkit-powerful-tools-for-your-website' ); ?></label>
					</div>
				</fieldset>
			</section>

			<section class="pkwt-card pkwt-card-span-2">
				<h2><?php esc_html_e( 'Post Types', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<fieldset <?php disabled( empty( $cfg['dpp_classic_enabled'] ) ); ?>>
					<div class="pkwt-field-grid">
						<?php foreach ( $types as $type ) : ?>
							<label><input type="checkbox" name="pkwt_dpp_classic_settings[dpp_classic_post_types][]" value="<?php echo esc_attr( $type->name ); ?>" <?php checked( in_array( $type->name, $cfg['dpp_classic_post_types'], true ) ); ?> /> <?php echo esc_html( $type->labels->name ); ?></label>
						<?php endforeach; ?>
					</div>
				</fieldset>
			</section>

			<section class="pkwt-card" data-pkwt-advanced-group="classic">
				<h2><?php esc_html_e( 'User control', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<fieldset <?php disabled( empty( $cfg['dpp_classic_enabled'] ) ); ?>>
					<div class="pkwt-field pkwt-field-inline"><label class="pkwt-label" for="classic-user-choice"><?php esc_html_e( 'Allow users to choose editor', 'powerkit-powerful-tools-for-your-website' ); ?></label><span class="pkwt-switch-wrap"><input id="classic-user-choice" type="checkbox" class="dpp-toggle" name="pkwt_dpp_classic_settings[dpp_classic_allow_user_choice]" value="1" <?php checked( ! empty( $cfg['dpp_classic_allow_user_choice'] ) ); ?> /></span></div>
					<div class="pkwt-field pkwt-field-inline"><label class="pkwt-label" for="classic-admin-bypass"><?php esc_html_e( 'Allow admin bypass', 'powerkit-powerful-tools-for-your-website' ); ?></label><span class="pkwt-switch-wrap"><input id="classic-admin-bypass" type="checkbox" class="dpp-toggle" name="pkwt_dpp_classic_settings[dpp_classic_allow_admin_bypass]" value="1" <?php checked( ! empty( $cfg['dpp_classic_allow_admin_bypass'] ) ); ?> /></span></div>
				</fieldset>
			</section>

			<section class="pkwt-card" data-pkwt-advanced-group="classic">
				<h2><?php esc_html_e( 'Cleanup', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<fieldset <?php disabled( empty( $cfg['dpp_classic_enabled'] ) ); ?>>
					<div class="pkwt-field pkwt-field-inline"><label class="pkwt-label" for="classic-remove-css"><?php esc_html_e( 'Remove block CSS', 'powerkit-powerful-tools-for-your-website' ); ?></label><span class="pkwt-switch-wrap"><input id="classic-remove-css" type="checkbox" class="dpp-toggle" name="pkwt_dpp_classic_settings[dpp_classic_remove_block_css]" value="1" <?php checked( ! empty( $cfg['dpp_classic_remove_block_css'] ) ); ?> /></span></div>
					<div class="pkwt-field pkwt-field-inline"><label class="pkwt-label" for="classic-remove-js"><?php esc_html_e( 'Remove block JS', 'powerkit-powerful-tools-for-your-website' ); ?></label><span class="pkwt-switch-wrap"><input id="classic-remove-js" type="checkbox" class="dpp-toggle" name="pkwt_dpp_classic_settings[dpp_classic_remove_block_js]" value="1" <?php checked( ! empty( $cfg['dpp_classic_remove_block_js'] ) ); ?> /></span></div>
					<div class="pkwt-field pkwt-field-inline"><label class="pkwt-label" for="classic-disable-fse"><?php esc_html_e( 'Disable full site editor', 'powerkit-powerful-tools-for-your-website' ); ?></label><span class="pkwt-switch-wrap"><input id="classic-disable-fse" type="checkbox" class="dpp-toggle" name="pkwt_dpp_classic_settings[dpp_classic_disable_fse]" value="1" <?php checked( ! empty( $cfg['dpp_classic_disable_fse'] ) ); ?> /></span></div>
					<div class="pkwt-field pkwt-field-inline"><label class="pkwt-label" for="classic-disable-widgets"><?php esc_html_e( 'Disable widget block editor', 'powerkit-powerful-tools-for-your-website' ); ?></label><span class="pkwt-switch-wrap"><input id="classic-disable-widgets" type="checkbox" class="dpp-toggle" name="pkwt_dpp_classic_settings[dpp_classic_disable_widgets]" value="1" <?php checked( ! empty( $cfg['dpp_classic_disable_widgets'] ) ); ?> /></span></div>
					<div class="pkwt-field pkwt-field-inline"><label class="pkwt-label" for="classic-patterns"><?php esc_html_e( 'Remove block patterns', 'powerkit-powerful-tools-for-your-website' ); ?></label><span class="pkwt-switch-wrap"><input id="classic-patterns" type="checkbox" class="dpp-toggle" name="pkwt_dpp_classic_settings[dpp_classic_remove_patterns]" value="1" <?php checked( ! empty( $cfg['dpp_classic_remove_patterns'] ) ); ?> /></span></div>
					<div class="pkwt-field pkwt-field-inline"><label class="pkwt-label" for="classic-block-dir"><?php esc_html_e( 'Remove block directory', 'powerkit-powerful-tools-for-your-website' ); ?></label><span class="pkwt-switch-wrap"><input id="classic-block-dir" type="checkbox" class="dpp-toggle" name="pkwt_dpp_classic_settings[dpp_classic_remove_block_dir]" value="1" <?php checked( ! empty( $cfg['dpp_classic_remove_block_dir'] ) ); ?> /></span></div>
				</fieldset>
			</section>

			<section class="pkwt-card" data-pkwt-advanced-group="classic">
				<h2><?php esc_html_e( 'Editor style', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<fieldset <?php disabled( empty( $cfg['dpp_classic_enabled'] ) ); ?>>
					<div class="pkwt-field">
						<span class="pkwt-label"><?php esc_html_e( 'Toolbar style', 'powerkit-powerful-tools-for-your-website' ); ?></span>
						<label><input type="radio" name="pkwt_dpp_classic_settings[dpp_classic_toolbar_style]" value="full" <?php checked( $cfg['dpp_classic_toolbar_style'], 'full' ); ?> /> <?php esc_html_e( 'Full', 'powerkit-powerful-tools-for-your-website' ); ?></label><br />
						<label><input type="radio" name="pkwt_dpp_classic_settings[dpp_classic_toolbar_style]" value="basic" <?php checked( $cfg['dpp_classic_toolbar_style'], 'basic' ); ?> /> <?php esc_html_e( 'Basic', 'powerkit-powerful-tools-for-your-website' ); ?></label><br />
						<label><input type="radio" name="pkwt_dpp_classic_settings[dpp_classic_toolbar_style]" value="minimal" <?php checked( $cfg['dpp_classic_toolbar_style'], 'minimal' ); ?> /> <?php esc_html_e( 'Minimal', 'powerkit-powerful-tools-for-your-website' ); ?></label>
					</div>
					<div class="pkwt-field pkwt-field-inline"><label class="pkwt-label" for="classic-kitchen"><?php esc_html_e( 'Kitchen sink by default', 'powerkit-powerful-tools-for-your-website' ); ?></label><span class="pkwt-switch-wrap"><input id="classic-kitchen" type="checkbox" class="dpp-toggle" name="pkwt_dpp_classic_settings[dpp_classic_kitchen_sink]" value="1" <?php checked( ! empty( $cfg['dpp_classic_kitchen_sink'] ) ); ?> /></span></div>
					<div class="pkwt-field"><label class="pkwt-label" for="classic-default-tab"><?php esc_html_e( 'Default editor tab', 'powerkit-powerful-tools-for-your-website' ); ?></label><select id="classic-default-tab" name="pkwt_dpp_classic_settings[dpp_classic_default_editor_tab]"><option value="visual" <?php selected( $cfg['dpp_classic_default_editor_tab'], 'visual' ); ?>><?php esc_html_e( 'Visual', 'powerkit-powerful-tools-for-your-website' ); ?></option><option value="html" <?php selected( $cfg['dpp_classic_default_editor_tab'], 'html' ); ?>><?php esc_html_e( 'Text', 'powerkit-powerful-tools-for-your-website' ); ?></option></select></div>
				</fieldset>
			</section>

			<section class="pkwt-card" data-pkwt-advanced-group="classic">
				<h2><?php esc_html_e( 'Notice', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<fieldset <?php disabled( empty( $cfg['dpp_classic_enabled'] ) ); ?>>
					<div class="pkwt-field pkwt-field-inline"><label class="pkwt-label" for="classic-notice"><?php esc_html_e( 'Show notice to editors', 'powerkit-powerful-tools-for-your-website' ); ?></label><span class="pkwt-switch-wrap"><input id="classic-notice" type="checkbox" class="dpp-toggle" name="pkwt_dpp_classic_settings[dpp_classic_show_notice]" value="1" <?php checked( ! empty( $cfg['dpp_classic_show_notice'] ) ); ?> /></span></div>
					<div class="pkwt-field"><label class="pkwt-label" for="classic-notice-text"><?php esc_html_e( 'Notice text', 'powerkit-powerful-tools-for-your-website' ); ?></label><input id="classic-notice-text" type="text" name="pkwt_dpp_classic_settings[dpp_classic_notice_text]" value="<?php echo esc_attr( (string) $cfg['dpp_classic_notice_text'] ); ?>" /></div>
				</fieldset>
			</section>
		</div>

		<div class="pkwt-savebar pkwt-savebar-bottom">
			<div class="pkwt-savebar-text"><?php esc_html_e( 'Changes apply after save.', 'powerkit-powerful-tools-for-your-website' ); ?></div>
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'powerkit-powerful-tools-for-your-website' ); ?></button>
		</div>
	</form>

	<div class="pkwt-card" style="margin-top:14px;">
		<h2><?php esc_html_e( 'Classic Editor Status', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
		<ul class="pkwt-health-list">
			<?php foreach ( $status as $row ) : ?>
				<li>
					<strong><?php echo esc_html( 'ok' === $row['status'] ? 'OK' : 'WARN' ); ?></strong>
					<span><?php echo esc_html( (string) $row['label'] ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
		<p><?php echo esc_html( 'WordPress Version: ' . get_bloginfo( 'version' ) ); ?></p>
		<p><?php echo esc_html( 'Classic Editor: ' . ( ! empty( $cfg['dpp_classic_enabled'] ) ? 'Active via plugin' : 'Inactive' ) ); ?></p>
	</div>
</div>
<?php // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>
