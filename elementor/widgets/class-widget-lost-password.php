<?php
/**
 * Lost password widget.
 *
 * @package PKWT
 */

namespace PKWT\Elementor\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_Widget_Lost_Password extends Class_Abstract_Form_Widget {

	public function get_name() { return 'pkwt-lost-password'; }
	public function get_title() { return esc_html__( 'PowerKit Lost Password', 'powerplus-toolkit' ); }
	public function get_icon() { return 'eicon-mail'; }
	public function get_keywords() { return array( 'lost password', 'forgot', 'auth' ); }
	public function get_categories() { return array( 'powerplus-toolkit' ); }

	protected function get_page_type(): string { return 'lost'; }

	protected function register_controls() {
		$this->register_shared_controls();
		$this->start_controls_section( 'lost_content', array( 'label' => esc_html__( 'Fields', 'powerplus-toolkit' ) ) );
		$this->add_control( 'field_label', array( 'label' => esc_html__( 'Field Label', 'powerplus-toolkit' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Username or Email', 'powerplus-toolkit' ) ) );
		$this->add_control( 'placeholder', array( 'label' => esc_html__( 'Placeholder', 'powerplus-toolkit' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Enter username or email', 'powerplus-toolkit' ) ) );
		$this->add_control( 'button_text', array( 'label' => esc_html__( 'Button Text', 'powerplus-toolkit' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Reset Password', 'powerplus-toolkit' ) ) );
		$this->add_control( 'back_text', array( 'label' => esc_html__( 'Back Link Text', 'powerplus-toolkit' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Back to login', 'powerplus-toolkit' ) ) );
		$this->add_control( 'back_url', array( 'label' => esc_html__( 'Back Link URL', 'powerplus-toolkit' ), 'type' => Controls_Manager::URL ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$back_url = ! empty( $settings['back_url']['url'] ) ? $settings['back_url']['url'] : wp_login_url();
		$this->render_form_open( 'lostpw', 'pkwt_lostpw', 'lostpw_nonce', '', '', ! empty( $settings['loading_text'] ) ? (string) $settings['loading_text'] : '' );
		$this->render_form_heading( $settings );
		?>
		<div class="pkwt-form-field">
			<label for="pkwt-lost-user-login"><?php echo esc_html( $settings['field_label'] ); ?></label>
			<input id="pkwt-lost-user-login" type="text" name="user_login" placeholder="<?php echo esc_attr( $settings['placeholder'] ); ?>" required aria-required="true" autocomplete="username" />
		</div>
		<button class="pkwt-submit" type="submit"><?php echo esc_html( $settings['button_text'] ); ?></button>
		<p class="pkwt-message" aria-live="polite"></p>
		<div class="pkwt-form-footer-link">
			<a href="<?php echo esc_url( $back_url ); ?>">&larr; <?php echo esc_html( $settings['back_text'] ); ?></a>
		</div>
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
					<label>{{ settings.field_label }}</label>
					<input type="text" placeholder="{{ settings.placeholder }}" />
				</div>
				<button class="pkwt-submit" type="button">{{ settings.button_text }}</button>
				<p class="pkwt-message"></p>
				<div class="pkwt-form-footer-link">
					<a href="#">&larr; {{ settings.back_text }}</a>
				</div>
			</form>
		</div>
		<?php
	}
}
