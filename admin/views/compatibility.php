<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View template file; variables are scoped to this included file.

/**
 * Compatibility view.
 *
 * @package PKWT
 */

$report = get_transient( 'pkwt_conflict_report' );
?>
<div class="wrap pkwt-ui">
	<h1><?php esc_html_e( 'Compatibility', 'powerkit-powerful-tools-for-your-website' ); ?></h1>
	<?php settings_errors(); ?>

	<form method="post" action="options.php" class="pkwt-settings-form">
		<?php settings_fields( 'pkwt_settings_group' ); ?>
		<div class="pkwt-savebar pkwt-savebar-top">
			<div class="pkwt-savebar-text"><?php esc_html_e( 'Enable integrations and save to apply.', 'powerkit-powerful-tools-for-your-website' ); ?></div>
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'powerkit-powerful-tools-for-your-website' ); ?></button>
		</div>

		<div class="pkwt-card-grid">
			<section class="pkwt-card">
				<h2><?php esc_html_e( 'Integrations', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<div class="pkwt-field pkwt-field-inline">
					<label class="pkwt-label" for="pkwt-woo-mode"><?php esc_html_e( 'WooCommerce mode', 'powerkit-powerful-tools-for-your-website' ); ?></label>
					<div class="pkwt-switch-wrap">
						<input type="hidden" name="pkwt_settings[woocommerce_mode]" value="0" />
						<label class="pkwt-switch" aria-label="<?php esc_attr_e( 'WooCommerce mode', 'powerkit-powerful-tools-for-your-website' ); ?>">
							<input id="pkwt-woo-mode" class="pkwt-toggle" type="checkbox" name="pkwt_settings[woocommerce_mode]" value="1" <?php checked( ! empty( $settings['woocommerce_mode'] ) ); ?> />
							<span class="pkwt-switch-track"></span>
						</label>
					</div>
				</div>
				<?php if ( is_multisite() ) : ?>
					<div class="pkwt-info-box"><?php esc_html_e( 'Multisite detected. Each site keeps separate auth pages.', 'powerkit-powerful-tools-for-your-website' ); ?></div>
				<?php endif; ?>
			</section>

			<section class="pkwt-card">
				<h2><?php esc_html_e( 'Conflict Report', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<?php if ( ! empty( $report['issues'] ) && is_array( $report['issues'] ) ) : ?>
					<ul class="pkwt-issue-list">
						<?php foreach ( $report['issues'] as $issue ) : ?>
							<li><?php echo esc_html( $issue ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<div class="pkwt-info-box pkwt-info-box-success"><?php esc_html_e( 'No conflicts detected.', 'powerkit-powerful-tools-for-your-website' ); ?></div>
				<?php endif; ?>
			</section>
		</div>

		<div class="pkwt-savebar pkwt-savebar-bottom">
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'powerkit-powerful-tools-for-your-website' ); ?></button>
		</div>
	</form>
</div>
<?php // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>
