<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View template file; variables are scoped to this included file.

/**
 * Import export view.
 *
 * @package PKWT
 */

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin notice key.
$notice = isset( $_GET['pkwt_notice'] ) ? sanitize_key( wp_unslash( $_GET['pkwt_notice'] ) ) : '';
?>
<div class="wrap pkwt-ui">
	<h1><?php esc_html_e( 'Import / Export', 'powerplus-toolkit' ); ?></h1>
	<?php if ( 'import_ok' === $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings imported.', 'powerplus-toolkit' ); ?></p></div>
	<?php elseif ( 'import_failed' === $notice ) : ?>
		<div class="notice notice-error is-dismissible"><p><?php esc_html_e( 'Import failed.', 'powerplus-toolkit' ); ?></p></div>
	<?php elseif ( 'reset_ok' === $notice ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings reset.', 'powerplus-toolkit' ); ?></p></div>
	<?php endif; ?>

	<div class="pkwt-card-grid">
		<section class="pkwt-card">
			<h2><?php esc_html_e( 'Export', 'powerplus-toolkit' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Download all PowerKit settings as a JSON file.', 'powerplus-toolkit' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'pkwt_export_settings' ); ?>
				<input type="hidden" name="action" value="pkwt_export_settings" />
				<button type="submit" class="button button-primary"><?php esc_html_e( 'Export JSON', 'powerplus-toolkit' ); ?></button>
			</form>
		</section>

		<section class="pkwt-card">
			<h2><?php esc_html_e( 'Import', 'powerplus-toolkit' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Upload a previously exported JSON file.', 'powerplus-toolkit' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
				<?php wp_nonce_field( 'pkwt_import_settings' ); ?>
				<input type="hidden" name="action" value="pkwt_import_settings" />
				<input type="file" name="pkwt_import_file" accept="application/json" required />
				<button type="submit" class="button"><?php esc_html_e( 'Import JSON', 'powerplus-toolkit' ); ?></button>
			</form>
		</section>

		<section class="pkwt-card pkwt-card-span-2">
			<h2><?php esc_html_e( 'Reset', 'powerplus-toolkit' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Reset plugin settings and recreate defaults.', 'powerplus-toolkit' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'pkwt_reset_settings' ); ?>
				<input type="hidden" name="action" value="pkwt_reset_settings" />
				<button type="submit" class="button button-secondary" onclick="return confirm('<?php echo esc_js( __( 'Reset settings to defaults?', 'powerplus-toolkit' ) ); ?>');"><?php esc_html_e( 'Reset to Defaults', 'powerplus-toolkit' ); ?></button>
			</form>
		</section>
	</div>
</div>
<?php // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>
