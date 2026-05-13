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
	public function get_title() { return esc_html__( 'PowerKit CAPTCHA', 'powerkit-powerful-tools-for-your-website' ); }
	public function get_icon() { return 'eicon-shield-check'; }
	public function get_categories() { return array( 'powerkit-powerful-tools-for-your-website' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'CAPTCHA', 'powerkit-powerful-tools-for-your-website' ) ) );
		$this->add_control( 'provider', array( 'label' => esc_html__( 'Provider', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::SELECT, 'default' => 'recaptcha_v2', 'options' => array( 'recaptcha_v2' => 'reCAPTCHA v2', 'recaptcha_v3' => 'reCAPTCHA v3', 'hcaptcha' => 'hCaptcha' ) ) );
		$this->add_control( 'site_key', array( 'label' => esc_html__( 'Site Key', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::TEXT ) );
		$this->add_control( 'secret_key', array( 'label' => esc_html__( 'Secret Key (stored in settings)', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::TEXT ) );
		$this->add_control( 'theme', array( 'label' => esc_html__( 'Theme', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::SELECT, 'default' => 'light', 'options' => array( 'light' => esc_html__( 'Light', 'powerkit-powerful-tools-for-your-website' ), 'dark' => esc_html__( 'Dark', 'powerkit-powerful-tools-for-your-website' ) ) ) );
		$this->add_control( 'size', array( 'label' => esc_html__( 'Size', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::SELECT, 'default' => 'normal', 'options' => array( 'normal' => esc_html__( 'Normal', 'powerkit-powerful-tools-for-your-website' ), 'compact' => esc_html__( 'Compact', 'powerkit-powerful-tools-for-your-website' ) ) ) );
		$this->add_control( 'position', array( 'label' => esc_html__( 'Position', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::SELECT, 'default' => 'before-submit', 'options' => array( 'top' => esc_html__( 'Top', 'powerkit-powerful-tools-for-your-website' ), 'before-submit' => esc_html__( 'Before Submit', 'powerkit-powerful-tools-for-your-website' ), 'bottom' => esc_html__( 'Bottom', 'powerkit-powerful-tools-for-your-website' ) ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="pkwt-captcha" data-pkwt-captcha data-provider="<?php echo esc_attr( $settings['provider'] ); ?>" data-site-key="<?php echo esc_attr( $settings['site_key'] ); ?>" data-theme="<?php echo esc_attr( $settings['theme'] ); ?>" data-size="<?php echo esc_attr( $settings['size'] ); ?>" data-position="<?php echo esc_attr( $settings['position'] ); ?>">
			<input type="hidden" name="captcha_token" value="" />
			<div class="pkwt-captcha-placeholder"><?php esc_html_e( 'CAPTCHA will render here.', 'powerkit-powerful-tools-for-your-website' ); ?></div>
		</div>
		<?php
	}
}
