<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View template file; variables are scoped to this included file.

/**
 * General settings view.
 *
 * @package PKWT
 */

$pages               = get_pages( array( 'post_status' => 'publish' ) );
$login_page_id       = isset( $settings['login_page_id'] ) ? absint( $settings['login_page_id'] ) : 0;
$login_page_url      = $login_page_id ? get_permalink( $login_page_id ) : '';
$effective_login_url = isset( $settings['pkwt_custom_login_url'] ) && '' !== (string) $settings['pkwt_custom_login_url'] ? (string) $settings['pkwt_custom_login_url'] : $login_page_url;
// Extract just the slug portion stored in settings for the slug editor.
$saved_full_url      = isset( $settings['pkwt_custom_login_url'] ) ? (string) $settings['pkwt_custom_login_url'] : '';
$saved_slug          = '' !== $saved_full_url ? trim( (string) wp_parse_url( $saved_full_url, PHP_URL_PATH ), '/' ) : '';
$home_prefix         = trailingslashit( home_url( '/' ) );
?>
<div class="wrap pkwt-ui">
	<h1><?php esc_html_e( 'Authentication', 'powerplus-toolkit' ); ?></h1>
	<details class="pkwt-learn-more">
		<summary><?php esc_html_e( 'Learn More', 'powerplus-toolkit' ); ?></summary>
		<p><?php esc_html_e( 'Set a unique custom login URL first. Then assign auth pages and choose a post-login destination page.', 'powerplus-toolkit' ); ?></p>
	</details>
	<?php settings_errors(); ?>

	<form method="post" action="options.php" class="pkwt-settings-form">
		<?php settings_fields( 'pkwt_settings_group' ); ?>

		<div class="pkwt-savebar pkwt-savebar-top">
			<div class="pkwt-savebar-text"><?php esc_html_e( 'Save your changes to apply routing and page assignments.', 'powerplus-toolkit' ); ?></div>
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'powerplus-toolkit' ); ?></button>
		</div>

		<div class="pkwt-card-grid">
			<section class="pkwt-card">
				<h2><?php esc_html_e( 'Core', 'powerplus-toolkit' ); ?></h2>
				<div class="pkwt-field pkwt-field-inline">
					<label class="pkwt-label" for="pkwt-enabled"><?php esc_html_e( 'Enable custom auth redirects', 'powerplus-toolkit' ); ?></label>
					<div class="pkwt-switch-wrap">
						<input type="hidden" name="pkwt_settings[enabled]" value="0" />
						<label class="pkwt-switch" aria-label="<?php esc_attr_e( 'Enable custom auth redirects', 'powerplus-toolkit' ); ?>">
							<input id="pkwt-enabled" class="pkwt-toggle" type="checkbox" name="pkwt_settings[enabled]" value="1" <?php checked( ! empty( $settings['enabled'] ) ); ?> />
							<span class="pkwt-switch-track"></span>
						</label>
					</div>
				</div>
			</section>

			<section class="pkwt-card">
				<h2><?php esc_html_e( 'Login URL', 'powerplus-toolkit' ); ?></h2>

				<?php // Hidden field carries the full normalized URL to the server. ?>
				<input type="hidden" id="pkwt-custom-login-url" name="pkwt_settings[pkwt_custom_login_url]" value="<?php echo esc_attr( $saved_full_url ); ?>" />

				<div class="pkwt-field">
					<label class="pkwt-label"><?php esc_html_e( 'Login URL', 'powerplus-toolkit' ); ?></label>

					<?php // Permalink display row — shown when not editing. ?>
					<div id="pkwt-login-url-display" class="pkwt-permalink-row">
						<span class="pkwt-permalink-label">
							<?php echo esc_html( $effective_login_url ? $effective_login_url : __( 'Not assigned yet', 'powerplus-toolkit' ) ); ?>
						</span>
						<?php if ( $effective_login_url ) : ?>
							<button type="button" class="button button-small" id="pkwt-edit-slug-btn"><?php esc_html_e( 'Edit', 'powerplus-toolkit' ); ?></button>
							<a class="button button-small" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $effective_login_url ); ?>"><?php esc_html_e( 'View', 'powerplus-toolkit' ); ?></a>
						<?php else : ?>
							<button type="button" class="button button-small" id="pkwt-edit-slug-btn"><?php esc_html_e( 'Set URL', 'powerplus-toolkit' ); ?></button>
						<?php endif; ?>
					</div>

					<?php // Permalink editor row — shown when editing. ?>
					<div id="pkwt-login-url-editor" class="pkwt-permalink-editor" style="display:none;">
						<span class="pkwt-permalink-prefix"><?php echo esc_html( $home_prefix ); ?></span>
						<input type="text"
							id="pkwt-slug-input"
							class="pkwt-slug-input"
							value="<?php echo esc_attr( $saved_slug ); ?>"
							placeholder="my-login"
							spellcheck="false"
							autocomplete="off"
						/>
						<span class="pkwt-permalink-suffix">/</span>
						<button type="button" class="button button-primary button-small" id="pkwt-slug-ok"><?php esc_html_e( 'OK', 'powerplus-toolkit' ); ?></button>
						<button type="button" class="button button-small" id="pkwt-slug-cancel"><?php esc_html_e( 'Cancel', 'powerplus-toolkit' ); ?></button>
					</div>

					<p class="description"><?php esc_html_e( 'This URL becomes your login page address. Changing it renames the page slug automatically.', 'powerplus-toolkit' ); ?></p>
				</div>
			</section>

			<section class="pkwt-card pkwt-card-span-2">
				<h2><?php esc_html_e( 'Auth Pages', 'powerplus-toolkit' ); ?></h2>
				<div class="pkwt-field-grid">
					<?php foreach ( array( 'login_page_id' => __( 'Login page', 'powerplus-toolkit' ), 'register_page_id' => __( 'Register page', 'powerplus-toolkit' ), 'lost_password_page_id' => __( 'Lost password page', 'powerplus-toolkit' ), 'reset_password_page_id' => __( 'Reset password page', 'powerplus-toolkit' ) ) as $key => $label ) : ?>
						<div class="pkwt-field">
							<label class="pkwt-label" for="<?php echo esc_attr( 'pkwt-' . $key ); ?>"><?php echo esc_html( $label ); ?></label>
							<select id="<?php echo esc_attr( 'pkwt-' . $key ); ?>" name="pkwt_settings[<?php echo esc_attr( $key ); ?>]">
								<option value="0"><?php esc_html_e( 'Select page', 'powerplus-toolkit' ); ?></option>
								<?php foreach ( $pages as $page ) : ?>
									<option value="<?php echo esc_attr( $page->ID ); ?>" <?php selected( isset( $settings[ $key ] ) ? (int) $settings[ $key ] : 0, (int) $page->ID ); ?>><?php echo esc_html( $page->post_title ); ?></option>
								<?php endforeach; ?>
							</select>
							<?php if ( ! empty( $settings[ $key ] ) ) : ?>
								<a class="button-link" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( admin_url( 'post.php?post=' . absint( $settings[ $key ] ) . '&action=elementor' ) ); ?>"><?php esc_html_e( 'Edit in Elementor', 'powerplus-toolkit' ); ?></a>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</section>

			<section class="pkwt-card">
				<h2><?php esc_html_e( 'After Login', 'powerplus-toolkit' ); ?></h2>
				<div class="pkwt-field">
					<label class="pkwt-label" for="pkwt-after-login-redirect"><?php esc_html_e( 'Redirect page', 'powerplus-toolkit' ); ?></label>
					<select id="pkwt-after-login-redirect" name="pkwt_settings[after_login_redirect_page_id]">
						<option value="0"><?php esc_html_e( 'Home Page', 'powerplus-toolkit' ); ?></option>
						<?php foreach ( $pages as $page ) : ?>
							<option value="<?php echo esc_attr( $page->ID ); ?>" <?php selected( isset( $settings['after_login_redirect_page_id'] ) ? (int) $settings['after_login_redirect_page_id'] : 0, (int) $page->ID ); ?>><?php echo esc_html( $page->post_title ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</section>
		</div>

		<div class="pkwt-savebar pkwt-savebar-bottom">
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'powerplus-toolkit' ); ?></button>
		</div>
	</form>
</div>
<?php // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound ?>
