<?php
/**
 * Reset password widget.
 *
 * @package PKWT
 */

namespace PKWT\Elementor\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_Widget_Reset_Password extends Class_Abstract_Form_Widget {

	public function get_name() { return 'pkwt-reset-password'; }
	public function get_title() { return esc_html__( 'PowerKit Reset Password', 'powerkit-powerful-tools-for-your-website' ); }
	public function get_icon() { return 'eicon-lock-user'; }
	public function get_keywords() { return array( 'reset password', 'auth', 'security' ); }
	public function get_categories() { return array( 'powerkit-powerful-tools-for-your-website' ); }

	protected function get_page_type(): string { return 'reset'; }

	protected function register_controls() {
		$this->register_shared_controls();
		$this->start_controls_section( 'reset_content', array( 'label' => esc_html__( 'Fields', 'powerkit-powerful-tools-for-your-website' ) ) );
		$this->add_control( 'password_label', array( 'label' => esc_html__( 'Password Label', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'New Password', 'powerkit-powerful-tools-for-your-website' ) ) );
		$this->add_control( 'confirm_label', array( 'label' => esc_html__( 'Confirm Label', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Confirm Password', 'powerkit-powerful-tools-for-your-website' ) ) );
		$this->add_control( 'button_text', array( 'label' => esc_html__( 'Button Text', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Set New Password', 'powerkit-powerful-tools-for-your-website' ) ) );
		$this->add_control( 'show_strength', array( 'label' => esc_html__( 'Show Strength Indicator', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->add_control( 'success_redirect', array( 'label' => esc_html__( 'Success Redirect URL', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::URL ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reset key/login are read-only URL tokens.
		$key   = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reset key/login are read-only URL tokens.
		$login = isset( $_GET['login'] ) ? sanitize_text_field( wp_unslash( $_GET['login'] ) ) : '';

		// In editor preview mode, show the form even without URL params.
		$is_preview = class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->preview ) && \Elementor\Plugin::$instance->preview->is_preview_mode();

		if ( ! $is_preview && ( '' === $key || '' === $login ) ) {
			echo '<div class="pkwt-form-wrap"><p>' . esc_html__( 'Invalid password reset link.', 'powerkit-powerful-tools-for-your-website' ) . '</p></div>';
			return;
		}

		$redirect = ! empty( $settings['success_redirect']['url'] ) ? $settings['success_redirect']['url'] : '';
		$this->render_form_open( 'resetpw', 'pkwt_resetpw', 'resetpw_nonce', $redirect, '', ! empty( $settings['loading_text'] ) ? (string) $settings['loading_text'] : '' );
		$this->render_form_heading( $settings );
		?>
		<input type="hidden" name="rp_key" value="<?php echo esc_attr( $key ); ?>" />
		<input type="hidden" name="rp_login" value="<?php echo esc_attr( $login ); ?>" />

		<div class="pkwt-form-field">
			<label for="pkwt-reset-password"><?php echo esc_html( $settings['password_label'] ); ?></label>
			<input id="pkwt-reset-password" type="password" name="password" minlength="8" maxlength="72" required aria-required="true" autocomplete="new-password" />
		</div>

		<div class="pkwt-form-field">
			<label for="pkwt-reset-password-confirm"><?php echo esc_html( $settings['confirm_label'] ); ?></label>
			<input id="pkwt-reset-password-confirm" type="password" name="confirm_password" minlength="8" maxlength="72" required aria-required="true" autocomplete="new-password" />
		</div>

		<?php if ( 'yes' === $settings['show_strength'] ) : ?>
			<div class="pkwt-strength" aria-live="polite"></div>
		<?php endif; ?>
		<button class="pkwt-submit" type="submit"><?php echo esc_html( $settings['button_text'] ); ?></button>
		<p class="pkwt-message" aria-live="polite"></p>
		<?php
		$this->render_form_close();
	}

	protected function content_template() {
		?>
		<div class="pkwt-form-wrap">
			<form class="pkwt-form">
				<# if ( settings.form_title ) { #>
					<h3 class="pkwt-form-title">{{{ settings.form_title }}}</h3>
				<# } #>
				<# if ( settings.form_description ) { #>
					<p class="pkwt-form-description">{{{ settings.form_description }}}</p>
				<# } #>
				<div class="pkwt-form-field">
					<label>{{ settings.password_label }}</label>
					<input type="password" />
				</div>
				<div class="pkwt-form-field">
					<label>{{ settings.confirm_label }}</label>
					<input type="password" />
				</div>
				<button class="pkwt-submit" type="button">{{ settings.button_text }}</button>
				<p class="pkwt-message"></p>
			</form>
		</div>
		<?php
	}
}
