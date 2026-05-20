<?php
/**
 * Register form widget.
 *
 * @package PKWT
 */

namespace PKWT\Elementor\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_Widget_Register_Form extends Class_Abstract_Form_Widget {

	public function get_name() { return 'pkwt-register-form'; }
	public function get_title() { return esc_html__( 'PowerPlus Register Form', 'powerplus-toolkit' ); }
	public function get_icon() { return 'eicon-user-circle-o'; }
	public function get_keywords() { return array( 'register', 'signup', 'auth', 'form' ); }
	public function get_categories() { return array( 'powerplus-toolkit' ); }

	protected function get_page_type(): string { return 'register'; }

	protected function register_controls() {
		$this->register_shared_controls();

		$this->start_controls_section(
			'register_content',
			array( 'label' => esc_html__( 'Fields & Options', 'powerplus-toolkit' ) )
		);

		$this->add_control(
			'form_title',
			array(
				'label'   => esc_html__( 'Heading', 'powerplus-toolkit' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Create your account', 'powerplus-toolkit' ),
			)
		);

		$this->add_control(
			'form_description',
			array(
				'label'   => esc_html__( 'Subheading', 'powerplus-toolkit' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Join thousands of users. Start for free today.', 'powerplus-toolkit' ),
				'rows'    => 2,
			)
		);

		$this->add_control(
			'username_placeholder',
			array(
				'label'   => esc_html__( 'Username Placeholder', 'powerplus-toolkit' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Choose a username', 'powerplus-toolkit' ),
			)
		);

		$this->add_control(
			'email_placeholder',
			array(
				'label'   => esc_html__( 'Email Placeholder', 'powerplus-toolkit' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'you@example.com', 'powerplus-toolkit' ),
			)
		);

		$this->add_control(
			'password_placeholder',
			array(
				'label'   => esc_html__( 'Password Placeholder', 'powerplus-toolkit' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Create a strong password', 'powerplus-toolkit' ),
			)
		);

		$this->add_control(
			'confirm_placeholder',
			array(
				'label'   => esc_html__( 'Confirm Password Placeholder', 'powerplus-toolkit' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Repeat your password', 'powerplus-toolkit' ),
			)
		);

		$this->add_control(
			'show_password_toggle',
			array(
				'label'   => esc_html__( 'Show Password Toggle', 'powerplus-toolkit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_phone',
			array(
				'label'   => esc_html__( 'Show Phone Field', 'powerplus-toolkit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'show_terms',
			array(
				'label'   => esc_html__( 'Show Terms Checkbox', 'powerplus-toolkit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'terms_text',
			array(
				'label'     => esc_html__( 'Terms Text', 'powerplus-toolkit' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'I agree to the Terms & Privacy Policy', 'powerplus-toolkit' ),
				'condition' => array( 'show_terms' => 'yes' ),
			)
		);

		$this->add_control(
			'terms_url',
			array(
				'label'     => esc_html__( 'Terms URL', 'powerplus-toolkit' ),
				'type'      => Controls_Manager::URL,
				'condition' => array( 'show_terms' => 'yes' ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => esc_html__( 'Button Text', 'powerplus-toolkit' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Create Account', 'powerplus-toolkit' ),
			)
		);

		$this->add_control(
			'show_login_link',
			array(
				'label'   => esc_html__( 'Show Login Link', 'powerplus-toolkit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'login_link_text',
			array(
				'label'     => esc_html__( 'Login Link Text', 'powerplus-toolkit' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Already have an account? Sign in', 'powerplus-toolkit' ),
				'condition' => array( 'show_login_link' => 'yes' ),
			)
		);

		$this->add_control(
			'login_url',
			array(
				'label'     => esc_html__( 'Login URL', 'powerplus-toolkit' ),
				'type'      => Controls_Manager::URL,
				'condition' => array( 'show_login_link' => 'yes' ),
			)
		);

		$this->add_control(
			'success_redirect',
			array(
				'label' => esc_html__( 'Success Redirect URL', 'powerplus-toolkit' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings     = $this->get_settings_for_display();
		$redirect_to  = ! empty( $settings['success_redirect']['url'] ) ? $settings['success_redirect']['url'] : '';
		$loading_text = ! empty( $settings['loading_text'] ) ? (string) $settings['loading_text'] : esc_html__( 'Creating account…', 'powerplus-toolkit' );

		$this->render_form_open( 'register', 'pkwt_register', 'register_nonce', $redirect_to, '', $loading_text );
		$this->render_form_heading( $settings );
		?>

		<div class="pkwt-form-field">
			<label for="pkwt-register-username"><?php esc_html_e( 'Username', 'powerplus-toolkit' ); ?></label>
			<input
				id="pkwt-register-username"
				type="text"
				name="username"
				placeholder="<?php echo esc_attr( $settings['username_placeholder'] ); ?>"
				required
				aria-required="true"
				autocomplete="username"
			/>
		</div>

		<div class="pkwt-form-field">
			<label for="pkwt-register-email"><?php esc_html_e( 'Email Address', 'powerplus-toolkit' ); ?></label>
			<input
				id="pkwt-register-email"
				type="email"
				name="email"
				placeholder="<?php echo esc_attr( $settings['email_placeholder'] ); ?>"
				required
				aria-required="true"
				autocomplete="email"
			/>
		</div>

		<div class="pkwt-form-field">
			<label for="pkwt-register-password"><?php esc_html_e( 'Password', 'powerplus-toolkit' ); ?></label>
			<div class="pkwt-password-wrap">
				<input
					id="pkwt-register-password"
					type="password"
					name="password"
					placeholder="<?php echo esc_attr( $settings['password_placeholder'] ); ?>"
					required
					aria-required="true"
					minlength="8"
					maxlength="72"
					autocomplete="new-password"
				/>
				<?php if ( 'yes' === $settings['show_password_toggle'] ) : ?>
					<button type="button" class="pkwt-password-toggle" aria-label="<?php esc_attr_e( 'Toggle password visibility', 'powerplus-toolkit' ); ?>"></button>
				<?php endif; ?>
			</div>
		</div>

		<div class="pkwt-form-field">
			<label for="pkwt-register-confirm-password"><?php esc_html_e( 'Confirm Password', 'powerplus-toolkit' ); ?></label>
			<input
				id="pkwt-register-confirm-password"
				type="password"
				name="confirm_password"
				placeholder="<?php echo esc_attr( $settings['confirm_placeholder'] ); ?>"
				required
				aria-required="true"
				minlength="8"
				maxlength="72"
				autocomplete="new-password"
			/>
		</div>

		<?php if ( 'yes' === $settings['show_phone'] ) : ?>
		<div class="pkwt-form-field">
			<label for="pkwt-register-phone"><?php esc_html_e( 'Phone (optional)', 'powerplus-toolkit' ); ?></label>
			<input
				id="pkwt-register-phone"
				type="tel"
				name="phone"
				autocomplete="tel"
			/>
		</div>
		<?php endif; ?>

		<?php if ( 'yes' === $settings['show_terms'] ) : ?>
		<div class="pkwt-form-field">
			<label class="pkwt-checkbox">
				<input type="checkbox" name="terms" value="1" required aria-required="true" />
				<?php if ( ! empty( $settings['terms_url']['url'] ) ) : ?>
					<a href="<?php echo esc_url( $settings['terms_url']['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $settings['terms_text'] ); ?></a>
				<?php else : ?>
					<?php echo esc_html( $settings['terms_text'] ); ?>
				<?php endif; ?>
			</label>
		</div>
		<?php endif; ?>

		<button class="pkwt-submit" type="submit"><?php echo esc_html( $settings['button_text'] ); ?></button>
		<p class="pkwt-message" aria-live="polite"></p>

		<?php if ( 'yes' === $settings['show_login_link'] && ! empty( $settings['login_link_text'] ) ) : ?>
			<div class="pkwt-form-footer-link">
				<?php if ( ! empty( $settings['login_url']['url'] ) ) : ?>
					<a href="<?php echo esc_url( $settings['login_url']['url'] ); ?>"><?php echo esc_html( $settings['login_link_text'] ); ?></a>
				<?php else : ?>
					<?php echo esc_html( $settings['login_link_text'] ); ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>

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
					<label>Username</label>
					<input type="text" placeholder="{{ settings.username_placeholder }}" />
				</div>

				<div class="pkwt-form-field">
					<label>Email Address</label>
					<input type="email" placeholder="{{ settings.email_placeholder }}" />
				</div>

				<div class="pkwt-form-field">
					<label>Password</label>
					<div class="pkwt-password-wrap">
						<input type="password" placeholder="{{ settings.password_placeholder }}" />
						<# if ( 'yes' === settings.show_password_toggle ) { #>
							<button type="button" class="pkwt-password-toggle" aria-label="Toggle"></button>
						<# } #>
					</div>
				</div>

				<div class="pkwt-form-field">
					<label>Confirm Password</label>
					<input type="password" placeholder="{{ settings.confirm_placeholder }}" />
				</div>

				<# if ( 'yes' === settings.show_phone ) { #>
				<div class="pkwt-form-field">
					<label>Phone (optional)</label>
					<input type="tel" />
				</div>
				<# } #>

				<# if ( 'yes' === settings.show_terms ) { #>
				<div class="pkwt-form-field">
					<label class="pkwt-checkbox">
						<input type="checkbox" /> {{{ settings.terms_text }}}
					</label>
				</div>
				<# } #>

				<button class="pkwt-submit" type="button">{{{ settings.button_text }}}</button>
				<p class="pkwt-message"></p>

				<# if ( 'yes' === settings.show_login_link && settings.login_link_text ) { #>
					<div class="pkwt-form-footer-link">
						<a href="#">{{{ settings.login_link_text }}}</a>
					</div>
				<# } #>
			</form>
		</div>
		<?php
	}
}
