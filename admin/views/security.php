<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View template file; variables are scoped to this included file.

/**
 * Security settings view.
 *
 * @package PKWT
 */
$roles_obj            = wp_roles();
$roles                = $roles_obj ? $roles_obj->roles : array();
$security_helper      = new \PKWT\Includes\Class_PKWT_Security();
$rate_limit_status    = $security_helper->get_rate_limit_status();
$activity_log         = get_option( 'pkwt_settings_activity_log', array() );
$activity_log         = is_array( $activity_log ) ? $activity_log : array();
$recent_activity      = array_slice( array_reverse( $activity_log ), 0, 12 );
$security_scan        = get_option( 'pkwt_last_security_scan', array() );
$security_scan        = is_array( $security_scan ) ? $security_scan : array();
$recent_24h_changes   = 0;
$last_24h_cutoff      = time() - DAY_IN_SECONDS;
foreach ( $activity_log as $log_entry ) {
	if ( isset( $log_entry['time'] ) && (int) $log_entry['time'] >= $last_24h_cutoff ) {
		$recent_24h_changes++;
	}
}
$unusual_pattern = ( ! empty( $rate_limit_status['limited'] ) || $recent_24h_changes > 40 );
?>
<div class="wrap pkwt-ui">
	<h1><?php esc_html_e( 'Security', 'powerplus-toolkit' ); ?></h1>
	<details class="pkwt-learn-more">
		<summary><?php esc_html_e( 'Learn More', 'powerplus-toolkit' ); ?></summary>
		<p><?php esc_html_e( 'Recommended baseline: enable rate limiting, set lockout values, and block native WordPress auth endpoints after custom login URL is verified.', 'powerplus-toolkit' ); ?></p>
	</details>
	<?php settings_errors(); ?>

	<form method="post" action="options.php" class="pkwt-settings-form">
		<?php settings_fields( 'pkwt_settings_group' ); ?>
		<div class="pkwt-savebar pkwt-savebar-top">
			<div class="pkwt-savebar-text"><?php esc_html_e( 'Review protection rules before saving.', 'powerplus-toolkit' ); ?></div>
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'powerplus-toolkit' ); ?></button>
		</div>

		<div class="pkwt-card-grid">
			<section class="pkwt-card">
				<h2><?php esc_html_e( 'Login Protection', 'powerplus-toolkit' ); ?></h2>
				<div class="pkwt-field pkwt-field-inline">
					<label class="pkwt-label" for="pkwt-rate-limiting"><?php esc_html_e( 'Enable rate limiting', 'powerplus-toolkit' ); ?></label>
					<div class="pkwt-switch-wrap">
						<input type="hidden" name="pkwt_settings[enable_rate_limiting]" value="0" />
						<label class="pkwt-switch" aria-label="<?php esc_attr_e( 'Enable rate limiting', 'powerplus-toolkit' ); ?>">
							<input id="pkwt-rate-limiting" class="pkwt-toggle" type="checkbox" name="pkwt_settings[enable_rate_limiting]" value="1" <?php checked( ! empty( $settings['enable_rate_limiting'] ) ); ?> />
							<span class="pkwt-switch-track"></span>
						</label>
					</div>
				</div>
				<div class="pkwt-field-grid">
					<div class="pkwt-field">
						<label class="pkwt-label" for="pkwt-max-attempts"><?php esc_html_e( 'Max login attempts', 'powerplus-toolkit' ); ?></label>
						<input id="pkwt-max-attempts" type="number" min="1" max="20" name="pkwt_settings[max_attempts]" value="<?php echo esc_attr( isset( $settings['max_attempts'] ) ? (int) $settings['max_attempts'] : 5 ); ?>" />
					</div>
					<div class="pkwt-field">
						<label class="pkwt-label" for="pkwt-lockout-mins"><?php esc_html_e( 'Lockout minutes', 'powerplus-toolkit' ); ?></label>
						<input id="pkwt-lockout-mins" type="number" min="5" max="1440" name="pkwt_settings[lockout_minutes]" value="<?php echo esc_attr( isset( $settings['lockout_minutes'] ) ? (int) $settings['lockout_minutes'] : 15 ); ?>" />
					</div>
				</div>
			</section>

			<section class="pkwt-card">
				<h2><?php esc_html_e( 'Native Endpoint Blocking', 'powerplus-toolkit' ); ?></h2>
				<div class="pkwt-field pkwt-field-inline">
					<label class="pkwt-label" for="pkwt-block-native"><?php esc_html_e( 'Block /wp-login and guest /wp-admin', 'powerplus-toolkit' ); ?></label>
					<div class="pkwt-switch-wrap">
						<input type="hidden" name="pkwt_settings[block_default_wp_auth]" value="0" />
						<label class="pkwt-switch" aria-label="<?php esc_attr_e( 'Block native endpoints', 'powerplus-toolkit' ); ?>">
							<input id="pkwt-block-native" class="pkwt-toggle" type="checkbox" name="pkwt_settings[block_default_wp_auth]" value="1" <?php checked( ! empty( $settings['block_default_wp_auth'] ) ); ?> />
							<span class="pkwt-switch-track"></span>
						</label>
					</div>
				</div>
				<p class="description"><?php esc_html_e( 'Returns 404 for unauthenticated access to native login/admin paths.', 'powerplus-toolkit' ); ?></p>
				<div class="pkwt-info-box pkwt-info-box-warning"><?php esc_html_e( 'Emergency bypass: set POWERPLUS_RECOVERY_MODE to true in wp-config.php.', 'powerplus-toolkit' ); ?></div>
			</section>

			<section class="pkwt-card pkwt-card-span-2">
				<h2><?php esc_html_e( 'CAPTCHA', 'powerplus-toolkit' ); ?></h2>
				<div class="pkwt-field">
					<label class="pkwt-label" for="pkwt-captcha-provider"><?php esc_html_e( 'Provider', 'powerplus-toolkit' ); ?></label>
					<select id="pkwt-captcha-provider" name="pkwt_settings[captcha_provider]">
						<?php foreach ( array( 'none' => 'None', 'recaptcha_v2' => 'reCAPTCHA v2', 'recaptcha_v3' => 'reCAPTCHA v3', 'hcaptcha' => 'hCaptcha' ) as $k => $l ) : ?>
							<option value="<?php echo esc_attr( $k ); ?>" <?php selected( isset( $settings['captcha_provider'] ) ? $settings['captcha_provider'] : 'none', $k ); ?>><?php echo esc_html( $l ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="pkwt-field-grid">
					<div class="pkwt-field">
						<label class="pkwt-label" for="pkwt-recaptcha-site"><?php esc_html_e( 'reCAPTCHA Site Key', 'powerplus-toolkit' ); ?></label>
						<input id="pkwt-recaptcha-site" class="regular-text" type="text" name="pkwt_settings[recaptcha_site_key]" value="<?php echo esc_attr( isset( $settings['recaptcha_site_key'] ) ? $settings['recaptcha_site_key'] : '' ); ?>" />
					</div>
					<div class="pkwt-field">
						<label class="pkwt-label" for="pkwt-recaptcha-secret"><?php esc_html_e( 'reCAPTCHA Secret Key', 'powerplus-toolkit' ); ?></label>
						<input id="pkwt-recaptcha-secret" class="regular-text" type="text" name="pkwt_settings[recaptcha_secret_key]" value="<?php echo esc_attr( isset( $settings['recaptcha_secret_key'] ) ? $settings['recaptcha_secret_key'] : '' ); ?>" />
					</div>
					<div class="pkwt-field">
						<label class="pkwt-label" for="pkwt-hcaptcha-site"><?php esc_html_e( 'hCaptcha Site Key', 'powerplus-toolkit' ); ?></label>
						<input id="pkwt-hcaptcha-site" class="regular-text" type="text" name="pkwt_settings[hcaptcha_site_key]" value="<?php echo esc_attr( isset( $settings['hcaptcha_site_key'] ) ? $settings['hcaptcha_site_key'] : '' ); ?>" />
					</div>
					<div class="pkwt-field">
						<label class="pkwt-label" for="pkwt-hcaptcha-secret"><?php esc_html_e( 'hCaptcha Secret Key', 'powerplus-toolkit' ); ?></label>
						<input id="pkwt-hcaptcha-secret" class="regular-text" type="text" name="pkwt_settings[hcaptcha_secret_key]" value="<?php echo esc_attr( isset( $settings['hcaptcha_secret_key'] ) ? $settings['hcaptcha_secret_key'] : '' ); ?>" />
					</div>
				</div>
			</section>

			<section class="pkwt-card pkwt-card-span-2">
				<h2><?php esc_html_e( 'Security Operations', 'powerplus-toolkit' ); ?></h2>
				<div class="pkwt-field-grid">
					<div class="pkwt-field">
						<label class="pkwt-label"><?php esc_html_e( 'Threat status', 'powerplus-toolkit' ); ?></label>
						<div class="pkwt-info-box <?php echo $unusual_pattern ? 'pkwt-info-box-warning' : 'pkwt-info-box-success'; ?>">
							<?php echo $unusual_pattern ? esc_html__( 'Unusual activity detected. Review lockouts and recent settings changes.', 'powerplus-toolkit' ) : esc_html__( 'No unusual pattern detected.', 'powerplus-toolkit' ); ?>
						</div>
					</div>
					<div class="pkwt-field">
						<label class="pkwt-label"><?php esc_html_e( 'Rate limit monitor', 'powerplus-toolkit' ); ?></label>
						<div class="pkwt-info-box">
								<?php
								if ( ! empty( $rate_limit_status['limited'] ) ) {
									/* translators: %d: seconds until current IP lockout expires. */
									echo esc_html( sprintf( __( 'IP currently locked. Retry after %d seconds.', 'powerplus-toolkit' ), (int) $rate_limit_status['retry_after'] ) );
								} else {
									/* translators: %d: remaining attempts before IP lockout. */
									echo esc_html( sprintf( __( 'No active lockout. Remaining attempts for current IP: %d', 'powerplus-toolkit' ), (int) $rate_limit_status['remaining'] ) );
								}
								?>
						</div>
					</div>
					<div class="pkwt-field">
						<label class="pkwt-label"><?php esc_html_e( 'Last security scan score', 'powerplus-toolkit' ); ?></label>
						<div class="pkwt-info-box">
							<?php
							$score = isset( $security_scan['score'] ) ? absint( $security_scan['score'] ) : 0;
							$total = isset( $security_scan['total'] ) ? absint( $security_scan['total'] ) : 0;
							echo esc_html( sprintf( '%1$d / %2$d', $score, $total ) );
							?>
						</div>
					</div>
					<div class="pkwt-field">
						<label class="pkwt-label"><?php esc_html_e( 'Settings changes (24h)', 'powerplus-toolkit' ); ?></label>
						<div class="pkwt-info-box"><?php echo esc_html( (string) $recent_24h_changes ); ?></div>
					</div>
				</div>

				<div class="pkwt-field pkwt-field-inline">
					<label class="pkwt-label" for="pkwt-security-dashboard-enabled"><?php esc_html_e( 'Enable security dashboard', 'powerplus-toolkit' ); ?></label>
					<div class="pkwt-switch-wrap">
						<input type="hidden" name="pkwt_settings[security_dashboard_enabled]" value="0" />
						<label class="pkwt-switch" aria-label="<?php esc_attr_e( 'Enable security dashboard', 'powerplus-toolkit' ); ?>">
							<input id="pkwt-security-dashboard-enabled" class="pkwt-toggle" type="checkbox" name="pkwt_settings[security_dashboard_enabled]" value="1" <?php checked( ! empty( $settings['security_dashboard_enabled'] ) ); ?> />
							<span class="pkwt-switch-track"></span>
						</label>
					</div>
				</div>

				<div class="pkwt-field pkwt-field-inline">
					<label class="pkwt-label" for="pkwt-settings-activity-log"><?php esc_html_e( 'Enable settings activity logging', 'powerplus-toolkit' ); ?></label>
					<div class="pkwt-switch-wrap">
						<input type="hidden" name="pkwt_settings[settings_activity_log]" value="0" />
						<label class="pkwt-switch" aria-label="<?php esc_attr_e( 'Enable settings activity logging', 'powerplus-toolkit' ); ?>">
							<input id="pkwt-settings-activity-log" class="pkwt-toggle" type="checkbox" name="pkwt_settings[settings_activity_log]" value="1" <?php checked( ! empty( $settings['settings_activity_log'] ) ); ?> />
							<span class="pkwt-switch-track"></span>
						</label>
					</div>
				</div>

				<div class="pkwt-field pkwt-field-inline">
					<label class="pkwt-label" for="pkwt-admin-test-mode"><?php esc_html_e( 'Test mode (administrators only)', 'powerplus-toolkit' ); ?></label>
					<div class="pkwt-switch-wrap">
						<input type="hidden" name="pkwt_settings[admin_test_mode]" value="0" />
						<label class="pkwt-switch" aria-label="<?php esc_attr_e( 'Enable admin-only test mode', 'powerplus-toolkit' ); ?>">
							<input id="pkwt-admin-test-mode" class="pkwt-toggle" type="checkbox" name="pkwt_settings[admin_test_mode]" value="1" <?php checked( ! empty( $settings['admin_test_mode'] ) ); ?> />
							<span class="pkwt-switch-track"></span>
						</label>
					</div>
				</div>

				<div class="pkwt-field">
					<label class="pkwt-label"><?php esc_html_e( 'Plugin access roles', 'powerplus-toolkit' ); ?></label>
					<div class="pkwt-field-grid">
						<?php foreach ( $roles as $role_key => $role_data ) : ?>
							<label>
								<input type="checkbox" name="pkwt_settings[access_roles][]" value="<?php echo esc_attr( $role_key ); ?>" <?php checked( isset( $settings['access_roles'] ) && is_array( $settings['access_roles'] ) && in_array( $role_key, $settings['access_roles'], true ) ); ?> />
								<?php echo esc_html( isset( $role_data['name'] ) ? (string) $role_data['name'] : $role_key ); ?>
							</label>
						<?php endforeach; ?>
					</div>
					<p class="description"><?php esc_html_e( 'Administrators always retain access.', 'powerplus-toolkit' ); ?></p>
				</div>
			</section>
		</div>

		<div class="pkwt-savebar pkwt-savebar-bottom">
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'powerplus-toolkit' ); ?></button>
		</div>
	</form>

	<section class="pkwt-card pkwt-card-span-2" style="margin-top:14px;">
		<h2><?php esc_html_e( 'Settings Activity Log', 'powerplus-toolkit' ); ?></h2>
		<?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin notice key. ?>
		<?php if ( isset( $_GET['pkwt_notice'] ) && 'activity_log_cleared' === sanitize_key( wp_unslash( $_GET['pkwt_notice'] ) ) ) : ?>
			<div class="notice notice-success inline is-dismissible"><p><?php esc_html_e( 'Activity log cleared.', 'powerplus-toolkit' ); ?></p></div>
		<?php endif; ?>
		<?php if ( empty( $recent_activity ) ) : ?>
			<p><?php esc_html_e( 'No activity entries yet.', 'powerplus-toolkit' ); ?></p>
		<?php else : ?>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Time', 'powerplus-toolkit' ); ?></th>
						<th><?php esc_html_e( 'Event', 'powerplus-toolkit' ); ?></th>
						<th><?php esc_html_e( 'User', 'powerplus-toolkit' ); ?></th>
						<th><?php esc_html_e( 'IP', 'powerplus-toolkit' ); ?></th>
						<th><?php esc_html_e( 'Option', 'powerplus-toolkit' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ( $recent_activity as $entry ) : ?>
					<tr>
						<td><?php echo esc_html( isset( $entry['time'] ) ? wp_date( 'Y-m-d H:i:s', (int) $entry['time'] ) : '' ); ?></td>
						<td><?php echo esc_html( isset( $entry['event'] ) ? (string) $entry['event'] : '' ); ?></td>
						<td><?php echo esc_html( isset( $entry['user'] ) ? (string) $entry['user'] : '' ); ?></td>
						<td><?php echo esc_html( isset( $entry['ip'] ) ? (string) $entry['ip'] : '' ); ?></td>
						<td><?php echo esc_html( isset( $entry['option'] ) ? (string) $entry['option'] : '' ); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:10px;">
			<?php wp_nonce_field( 'pkwt_clear_activity_log' ); ?>
			<input type="hidden" name="action" value="pkwt_clear_activity_log" />
			<button type="submit" class="button button-secondary"><?php esc_html_e( 'Clear Activity Log', 'powerplus-toolkit' ); ?></button>
		</form>
	</section>
</div>
<?php // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>
