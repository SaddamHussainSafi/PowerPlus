<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View template file; variables are scoped to this included file.

/**
 * Duplicate settings view.
 *
 * @package PKWT
 */

$dpp_admin  = new \PKWT\Includes\Class_PKWT_DPP_Admin();
$dpp        = $dpp_admin->get_settings();
$post_types = get_post_types( array( 'show_ui' => true ), 'objects' );
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin notice key.
$notice     = isset( $_GET['pkwt_notice'] ) ? sanitize_key( wp_unslash( $_GET['pkwt_notice'] ) ) : '';
?>
<div class="wrap pkwt-ui dpp-feature-panel">
	<h1><?php esc_html_e( 'Duplicate Post', 'powerkit-powerful-tools-for-your-website' ); ?></h1>
	<details class="pkwt-learn-more">
		<summary><?php esc_html_e( 'Learn More', 'powerkit-powerful-tools-for-your-website' ); ?></summary>
		<p><?php esc_html_e( 'Enable this module to duplicate posts/pages/CPT with meta and taxonomy. Leave post type selection empty to allow all admin-visible types.', 'powerkit-powerful-tools-for-your-website' ); ?></p>
	</details>

	<?php if ( 'preset_applied' === $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Recommended settings applied.', 'powerkit-powerful-tools-for-your-website' ); ?></p></div>
	<?php endif; ?>

	<form method="post" action="options.php">
		<?php settings_fields( 'pkwt_dpp_settings_group' ); ?>

		<div class="pkwt-savebar pkwt-savebar-top">
			<div class="pkwt-savebar-text"><?php esc_html_e( 'Enable Duplicate and save to apply globally.', 'powerkit-powerful-tools-for-your-website' ); ?></div>
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'powerkit-powerful-tools-for-your-website' ); ?></button>
		</div>

		<div class="pkwt-card-grid">
			<section class="pkwt-card">
				<h2><?php esc_html_e( 'Module', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<div class="pkwt-field pkwt-field-inline">
					<label class="pkwt-label" for="dpp-enabled"><?php esc_html_e( 'Enable post duplicator', 'powerkit-powerful-tools-for-your-website' ); ?></label>
					<span class="pkwt-switch-wrap"><input id="dpp-enabled" type="checkbox" class="dpp-toggle" name="pkwt_dpp_settings[enabled]" value="1" <?php checked( ! empty( $dpp['enabled'] ) ); ?> /></span>
				</div>
				<p class="description"><?php esc_html_e( 'One click duplicate for posts, pages, products, and custom post types.', 'powerkit-powerful-tools-for-your-website' ); ?></p>
			</section>

			<section class="pkwt-card">
				<h2><?php esc_html_e( 'Behavior', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<fieldset <?php disabled( empty( $dpp['enabled'] ) ); ?>>
					<div class="pkwt-field">
						<label class="pkwt-label" for="dpp-title-suffix"><?php esc_html_e( 'Title suffix', 'powerkit-powerful-tools-for-your-website' ); ?></label>
						<input id="dpp-title-suffix" type="text" name="pkwt_dpp_settings[title_suffix]" value="<?php echo esc_attr( (string) $dpp['title_suffix'] ); ?>" />
					</div>
					<div class="pkwt-field">
						<label class="pkwt-label" for="dpp-copy-author"><?php esc_html_e( 'Copy author', 'powerkit-powerful-tools-for-your-website' ); ?></label>
						<select id="dpp-copy-author" name="pkwt_dpp_settings[copy_author]">
							<option value="current" <?php selected( $dpp['copy_author'], 'current' ); ?>><?php esc_html_e( 'Current logged-in user', 'powerkit-powerful-tools-for-your-website' ); ?></option>
							<option value="original" <?php selected( $dpp['copy_author'], 'original' ); ?>><?php esc_html_e( 'Original author', 'powerkit-powerful-tools-for-your-website' ); ?></option>
						</select>
					</div>
					<div class="pkwt-field pkwt-field-inline">
						<label class="pkwt-label" for="dpp-enable-row-action"><?php esc_html_e( 'Show row action link', 'powerkit-powerful-tools-for-your-website' ); ?></label>
						<span class="pkwt-switch-wrap"><input id="dpp-enable-row-action" type="checkbox" class="dpp-toggle" name="pkwt_dpp_settings[enable_row_action]" value="1" <?php checked( ! empty( $dpp['enable_row_action'] ) ); ?> /></span>
					</div>
					<div class="pkwt-field pkwt-field-inline">
						<label class="pkwt-label" for="dpp-enable-elementor-button"><?php esc_html_e( 'Show Elementor editor button', 'powerkit-powerful-tools-for-your-website' ); ?></label>
						<span class="pkwt-switch-wrap"><input id="dpp-enable-elementor-button" type="checkbox" class="dpp-toggle" name="pkwt_dpp_settings[enable_elementor_button]" value="1" <?php checked( ! empty( $dpp['enable_elementor_button'] ) ); ?> /></span>
					</div>
				</fieldset>
			</section>

			<section class="pkwt-card pkwt-card-span-2">
				<h2><?php esc_html_e( 'Allowed Post Types', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<p class="description"><?php esc_html_e( 'If none are checked, duplication is allowed for all visible post types.', 'powerkit-powerful-tools-for-your-website' ); ?></p>
				<fieldset <?php disabled( empty( $dpp['enabled'] ) ); ?>>
					<div class="pkwt-field-grid">
						<?php foreach ( $post_types as $post_type ) : ?>
							<label>
								<input type="checkbox" name="pkwt_dpp_settings[enabled_post_types][]" value="<?php echo esc_attr( $post_type->name ); ?>" <?php checked( in_array( $post_type->name, $dpp['enabled_post_types'], true ) ); ?> />
								<?php echo esc_html( $post_type->labels->name ); ?>
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
</div>
<?php // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>
