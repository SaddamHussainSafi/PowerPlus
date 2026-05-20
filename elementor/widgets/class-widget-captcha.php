<?php
/**
 * CAPTCHA widget.
 *
 * @package PKWT
 */

namespace PKWT\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_Widget_Captcha extends Widget_Base {

	public function get_name() { return 'pkwt-captcha'; }
	public function get_title() { return esc_html__( 'PowerPlus CAPTCHA', 'powerplus-toolkit' ); }
	public function get_icon() { return 'eicon-shield-check'; }
	public function get_categories() { return array( 'powerplus-toolkit' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'CAPTCHA', 'powerplus-toolkit' ) ) );
		$this->add_control( 'provider', array( 'label' => esc_html__( 'Provider', 'powerplus-toolkit' ), 'type' => Controls_Manager::SELECT, 'default' => 'recaptcha_v2', 'options' => array( 'recaptcha_v2' => 'reCAPTCHA v2', 'recaptcha_v3' => 'reCAPTCHA v3', 'hcaptcha' => 'hCaptcha' ) ) );
		$this->add_control( 'site_key', array( 'label' => esc_html__( 'Site Key', 'powerplus-toolkit' ), 'type' => Controls_Manager::TEXT ) );
		$this->add_control( 'secret_key', array( 'label' => esc_html__( 'Secret Key (stored in settings)', 'powerplus-toolkit' ), 'type' => Controls_Manager::TEXT ) );
		$this->add_control( 'theme', array( 'label' => esc_html__( 'Theme', 'powerplus-toolkit' ), 'type' => Controls_Manager::SELECT, 'default' => 'light', 'options' => array( 'light' => esc_html__( 'Light', 'powerplus-toolkit' ), 'dark' => esc_html__( 'Dark', 'powerplus-toolkit' ) ) ) );
		$this->add_control( 'size', array( 'label' => esc_html__( 'Size', 'powerplus-toolkit' ), 'type' => Controls_Manager::SELECT, 'default' => 'normal', 'options' => array( 'normal' => esc_html__( 'Normal', 'powerplus-toolkit' ), 'compact' => esc_html__( 'Compact', 'powerplus-toolkit' ) ) ) );
		$this->add_control( 'position', array( 'label' => esc_html__( 'Position', 'powerplus-toolkit' ), 'type' => Controls_Manager::SELECT, 'default' => 'before-submit', 'options' => array( 'top' => esc_html__( 'Top', 'powerplus-toolkit' ), 'before-submit' => esc_html__( 'Before Submit', 'powerplus-toolkit' ), 'bottom' => esc_html__( 'Bottom', 'powerplus-toolkit' ) ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="pkwt-captcha" data-pkwt-captcha data-provider="<?php echo esc_attr( $settings['provider'] ); ?>" data-site-key="<?php echo esc_attr( $settings['site_key'] ); ?>" data-theme="<?php echo esc_attr( $settings['theme'] ); ?>" data-size="<?php echo esc_attr( $settings['size'] ); ?>" data-position="<?php echo esc_attr( $settings['position'] ); ?>">
			<input type="hidden" name="captcha_token" value="" />
			<div class="pkwt-captcha-placeholder"><?php esc_html_e( 'CAPTCHA will render here.', 'powerplus-toolkit' ); ?></div>
		</div>
		<?php
	}
}
