<?php
/**
 * Auth tabs widget.
 *
 * @package PKWT
 */

namespace PKWT\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_Widget_Auth_Tabs extends Widget_Base {

	public function get_name() { return 'pkwt-auth-tabs'; }
	public function get_title() { return esc_html__( 'PowerPlus Auth Tabs', 'powerplus-toolkit' ); }
	public function get_icon() { return 'eicon-tabs'; }
	public function get_categories() { return array( 'powerplus-toolkit' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'Tabs', 'powerplus-toolkit' ) ) );
		$this->add_control( 'login_label', array( 'label' => esc_html__( 'Login Label', 'powerplus-toolkit' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Login', 'powerplus-toolkit' ) ) );
		$this->add_control( 'register_label', array( 'label' => esc_html__( 'Register Label', 'powerplus-toolkit' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Register', 'powerplus-toolkit' ) ) );
		$this->add_control( 'default_tab', array( 'label' => esc_html__( 'Default Active Tab', 'powerplus-toolkit' ), 'type' => Controls_Manager::SELECT, 'default' => 'login', 'options' => array( 'login' => esc_html__( 'Login', 'powerplus-toolkit' ), 'register' => esc_html__( 'Register', 'powerplus-toolkit' ) ) ) );
		$this->add_control( 'indicator_color', array( 'label' => esc_html__( 'Active Indicator Color', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-auth-tab.is-active' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'animation', array( 'label' => esc_html__( 'Animation', 'powerplus-toolkit' ), 'type' => Controls_Manager::SELECT, 'default' => 'fade', 'options' => array( 'fade' => esc_html__( 'Fade', 'powerplus-toolkit' ), 'slide' => esc_html__( 'Slide', 'powerplus-toolkit' ) ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="pkwt-auth-tabs" data-pkwt-tabs data-default-tab="<?php echo esc_attr( $settings['default_tab'] ); ?>" data-animation="<?php echo esc_attr( $settings['animation'] ); ?>">
			<div class="pkwt-auth-tabs-nav">
				<button type="button" class="pkwt-auth-tab" data-tab="login"><?php echo esc_html( $settings['login_label'] ); ?></button>
				<button type="button" class="pkwt-auth-tab" data-tab="register"><?php echo esc_html( $settings['register_label'] ); ?></button>
			</div>
			<div class="pkwt-auth-tabs-panels">
				<div class="pkwt-auth-panel" data-panel="login"></div>
				<div class="pkwt-auth-panel" data-panel="register"></div>
			</div>
		</div>
		<?php
	}
}
