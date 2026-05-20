<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View template file; variables are scoped to this included file.

/**
 * Redirects settings view.
 *
 * @package PKWT
 */

global $wp_roles;
$roles = $wp_roles ? $wp_roles->roles : array();
?>
<div class="wrap pkwt-ui">
	<h1><?php esc_html_e( 'Redirect Rules', 'powerplus-toolkit' ); ?></h1>
	<?php settings_errors(); ?>

	<form method="post" action="options.php" class="pkwt-settings-form">
		<?php settings_fields( 'pkwt_settings_group' ); ?>
		<div class="pkwt-savebar pkwt-savebar-top">
			<div class="pkwt-savebar-text"><?php esc_html_e( 'Configure redirect behavior by role.', 'powerplus-toolkit' ); ?></div>
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'powerplus-toolkit' ); ?></button>
		</div>

		<div class="pkwt-card-grid">
			<section class="pkwt-card">
				<h2><?php esc_html_e( 'Filter Priority', 'powerplus-toolkit' ); ?></h2>
				<div class="pkwt-field">
					<label class="pkwt-label" for="pkwt-filter-priority"><?php esc_html_e( 'Priority', 'powerplus-toolkit' ); ?></label>
					<input id="pkwt-filter-priority" type="number" min="1" max="99" name="pkwt_settings[filter_priority]" value="<?php echo esc_attr( isset( $settings['filter_priority'] ) ? (int) $settings['filter_priority'] : 20 ); ?>" />
				</div>
			</section>

			<section class="pkwt-card pkwt-card-span-2">
				<h2><?php esc_html_e( 'Role Redirects', 'powerplus-toolkit' ); ?></h2>
				<div class="pkwt-field-grid">
					<?php foreach ( $roles as $role_key => $role_data ) : ?>
						<div class="pkwt-field">
							<label class="pkwt-label" for="<?php echo esc_attr( 'pkwt-role-' . $role_key ); ?>"><?php echo esc_html( $role_data['name'] ); ?></label>
							<?php if ( 'administrator' === $role_key ) : ?>
								<div class="pkwt-info-box"><?php esc_html_e( 'Always redirected to admin dashboard.', 'powerplus-toolkit' ); ?></div>
							<?php else : ?>
								<input id="<?php echo esc_attr( 'pkwt-role-' . $role_key ); ?>" type="url" class="regular-text" name="pkwt_settings[role_redirects][<?php echo esc_attr( $role_key ); ?>]" value="<?php echo esc_attr( isset( $settings['role_redirects'][ $role_key ] ) ? $settings['role_redirects'][ $role_key ] : '' ); ?>" placeholder="<?php echo esc_attr( home_url( '/' ) ); ?>" />
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</section>
		</div>

		<div class="pkwt-savebar pkwt-savebar-bottom">
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'powerplus-toolkit' ); ?></button>
		</div>
	</form>
</div>
<?php // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>
