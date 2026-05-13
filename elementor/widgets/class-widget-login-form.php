<?php
/**
 * Login form widget.
 *
 * @package PKWT
 */

namespace PKWT\Elementor\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_Widget_Login_Form extends Class_Abstract_Form_Widget {

	public function get_name() {
		return 'pkwt-login-form';
	}

	public function get_title() {
		return esc_html__( 'PowerKit Login Form', 'powerkit-powerful-tools-for-your-website' );
	}

	public function get_icon() {
		return 'eicon-lock';
	}

	public function get_keywords() {
		return array( 'login', 'auth', 'signin', 'form' );
	}

	public function get_categories() {
		return array( 'powerkit-powerful-tools-for-your-website' );
	}

	protected function get_page_type(): string {
		return 'login';
	}

	protected function register_controls() {
		$this->register_shared_controls();

		$this->start_controls_section(
			'login_content',
			array( 'label' => esc_html__( 'Fields & Options', 'powerkit-powerful-tools-for-your-website' ) )
		);

		$this->add_control(
			'form_title',
			array(
				'label'   => esc_html__( 'Heading', 'powerkit-powerful-tools-for-your-website' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Welcome back', 'powerkit-powerful-tools-for-your-website' ),
			)
		);

		$this->add_control(
			'form_description',
			array(
				'label'   => esc_html__( 'Subheading', 'powerkit-powerful-tools-for-your-website' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Sign in to your account to continue.', 'powerkit-powerful-tools-for-your-website' ),
				'rows'    => 2,
			)
		);

		$this->add_control(
			'username_label',
			array(
				'label'   => esc_html__( 'Username Label', 'powerkit-powerful-tools-for-your-website' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Username or Email', 'powerkit-powerful-tools-for-your-website' ),
			)
		);

		$this->add_control(
			'username_placeholder',
			array(
				'label'   => esc_html__( 'Username Placeholder', 'powerkit-powerful-tools-for-your-website' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'you@example.com', 'powerkit-powerful-tools-for-your-website' ),
			)
		);

		$this->add_control(
			'password_label',
			array(
				'label'   => esc_html__( 'Password Label', 'powerkit-powerful-tools-for-your-website' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Password', 'powerkit-powerful-tools-for-your-website' ),
			)
		);

		$this->add_control(
			'password_placeholder',
			array(
				'label'   => esc_html__( 'Password Placeholder', 'powerkit-powerful-tools-for-your-website' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( '••••••••', 'powerkit-powerful-tools-for-your-website' ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => esc_html__( 'Button Text', 'powerkit-powerful-tools-for-your-website' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Sign In', 'powerkit-powerful-tools-for-your-website' ),
			)
		);

		$this->add_control(
			'show_password_toggle',
			array(
				'label'   => esc_html__( 'Show Password Toggle', 'powerkit-powerful-tools-for-your-website' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_remember',
			array(
				'label'   => esc_html__( 'Show Remember Me', 'powerkit-powerful-tools-for-your-website' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'remember_text',
			array(
				'label'     => esc_html__( 'Remember Text', 'powerkit-powerful-tools-for-your-website' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Remember me', 'powerkit-powerful-tools-for-your-website' ),
				'condition' => array( 'show_remember' => 'yes' ),
			)
		);

		$this->add_control(
			'show_lost_password',
			array(
				'label'   => esc_html__( 'Show Forgot Password Link', 'powerkit-powerful-tools-for-your-website' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'lost_password_text',
			array(
				'label'     => esc_html__( 'Forgot Password Text', 'powerkit-powerful-tools-for-your-website' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Forgot password?', 'powerkit-powerful-tools-for-your-website' ),
				'condition' => array( 'show_lost_password' => 'yes' ),
			)
		);

		$this->add_control(
			'lost_password_url',
			array(
				'label'     => esc_html__( 'Forgot Password URL', 'powerkit-powerful-tools-for-your-website' ),
				'type'      => Controls_Manager::URL,
				'condition' => array( 'show_lost_password' => 'yes' ),
			)
		);

		$this->add_control(
			'show_register_link',
			array(
				'label'   => esc_html__( 'Show Register Link', 'powerkit-powerful-tools-for-your-website' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'register_link_text',
			array(
				'label'     => esc_html__( 'Register Link Text', 'powerkit-powerful-tools-for-your-website' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( "Don't have an account? Sign up", 'powerkit-powerful-tools-for-your-website' ),
				'condition' => array( 'show_register_link' => 'yes' ),
			)
		);

		$this->add_control(
			'register_url',
			array(
				'label'     => esc_html__( 'Register URL', 'powerkit-powerful-tools-for-your-website' ),
				'type'      => Controls_Manager::URL,
				'condition' => array( 'show_register_link' => 'yes' ),
			)
		);

		$this->add_control(
			'success_redirect',
			array(
				'label' => esc_html__( 'Success Redirect URL', 'powerkit-powerful-tools-for-your-website' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->add_control(
			'error_message',
			array(
				'label'   => esc_html__( 'Failure Message', 'powerkit-powerful-tools-for-your-website' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Incorrect username or password.', 'powerkit-powerful-tools-for-your-website' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings      = $this->get_settings_for_display();
		$lost_password = ! empty( $settings['lost_password_url']['url'] ) ? $settings['lost_password_url']['url'] : wp_lostpassword_url();
		$success_url   = ! empty( $settings['success_redirect']['url'] ) ? $settings['success_redirect']['url'] : '';
		$loading_text  = ! empty( $settings['loading_text'] ) ? (string) $settings['loading_text'] : esc_html__( 'Signing in…', 'powerkit-powerful-tools-for-your-website' );
		$error_message = ! empty( $settings['error_message'] ) ? (string) $settings['error_message'] : '';

		$this->render_form_open( 'login', 'pkwt_login', 'login_nonce', $success_url, $error_message, $loading_text );
		$this->render_form_heading( $settings );
		?>

		<div class="pkwt-form-field">
			<label for="pkwt-login-username"><?php echo esc_html( $settings['username_label'] ); ?></label>
			<input
				id="pkwt-login-username"
				type="text"
				name="username"
				placeholder="<?php echo esc_attr( $settings['username_placeholder'] ); ?>"
				required
				aria-required="true"
				autocomplete="username"
			/>
		</div>

		<div class="pkwt-form-field">
			<label for="pkwt-login-password"><?php echo esc_html( $settings['password_label'] ); ?></label>
			<div class="pkwt-password-wrap">
				<input
					id="pkwt-login-password"
					type="password"
					name="password"
					placeholder="<?php echo esc_attr( $settings['password_placeholder'] ); ?>"
					required
					aria-required="true"
					autocomplete="current-password"
				/>
				<?php if ( 'yes' === $settings['show_password_toggle'] ) : ?>
					<button type="button" class="pkwt-password-toggle" aria-label="<?php esc_attr_e( 'Toggle password visibility', 'powerkit-powerful-tools-for-your-website' ); ?>"></button>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( 'yes' === $settings['show_remember'] || 'yes' === $settings['show_lost_password'] ) : ?>
		<div class="pkwt-row-space">
			<?php if ( 'yes' === $settings['show_remember'] ) : ?>
				<label class="pkwt-checkbox">
					<input type="checkbox" name="remember" value="1" />
					<?php echo esc_html( $settings['remember_text'] ); ?>
				</label>
			<?php endif; ?>
			<?php if ( 'yes' === $settings['show_lost_password'] ) : ?>
				<a href="<?php echo esc_url( $lost_password ); ?>" class="pkwt-lost-link-inline"><?php echo esc_html( $settings['lost_password_text'] ); ?></a>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<button class="pkwt-submit" type="submit"><?php echo esc_html( $settings['button_text'] ); ?></button>
		<p class="pkwt-message" aria-live="polite"></p>

		<?php if ( 'yes' === $settings['show_register_link'] && ! empty( $settings['register_link_text'] ) ) : ?>
			<div class="pkwt-form-footer-link">
				<?php if ( ! empty( $settings['register_url']['url'] ) ) : ?>
					<a href="<?php echo esc_url( $settings['register_url']['url'] ); ?>"><?php echo esc_html( $settings['register_link_text'] ); ?></a>
				<?php else : ?>
					<?php echo esc_html( $settings['register_link_text'] ); ?>
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
					<label>{{{ settings.username_label }}}</label>
					<input type="text" placeholder="{{ settings.username_placeholder }}" />
				</div>

				<div class="pkwt-form-field">
					<label>{{{ settings.password_label }}}</label>
					<div class="pkwt-password-wrap">
						<input type="password" placeholder="{{ settings.password_placeholder }}" />
						<# if ( 'yes' === settings.show_password_toggle ) { #>
							<button type="button" class="pkwt-password-toggle" aria-label="Toggle"></button>
						<# } #>
					</div>
				</div>

				<# if ( 'yes' === settings.show_remember || 'yes' === settings.show_lost_password ) { #>
				<div class="pkwt-row-space">
					<# if ( 'yes' === settings.show_remember ) { #>
						<label class="pkwt-checkbox">
							<input type="checkbox" /> {{{ settings.remember_text }}}
						</label>
					<# } #>
					<# if ( 'yes' === settings.show_lost_password ) { #>
						<a href="#" class="pkwt-lost-link-inline">{{{ settings.lost_password_text }}}</a>
					<# } #>
				</div>
				<# } #>

				<button class="pkwt-submit" type="button">{{{ settings.button_text }}}</button>
				<p class="pkwt-message"></p>

				<# if ( 'yes' === settings.show_register_link && settings.register_link_text ) { #>
					<div class="pkwt-form-footer-link">
						<a href="#">{{{ settings.register_link_text }}}</a>
					</div>
				<# } #>
			</form>
		</div>
		<?php
	}
}
