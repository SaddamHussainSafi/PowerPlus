<?php
/**
 * Redirect timer widget.
 *
 * @package PKWT
 */

namespace PKWT\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_Widget_Redirect_Timer extends Widget_Base {

	public function get_name() { return 'pkwt-redirect-timer'; }
	public function get_title() { return esc_html__( 'PowerKit Redirect Timer', 'powerkit-powerful-tools-for-your-website' ); }
	public function get_icon() { return 'eicon-time-line'; }
	public function get_categories() { return array( 'powerkit-powerful-tools-for-your-website' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'Timer', 'powerkit-powerful-tools-for-your-website' ) ) );
		$this->add_control( 'seconds', array( 'label' => esc_html__( 'Countdown Seconds', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::NUMBER, 'default' => 3, 'min' => 1, 'max' => 60 ) );
		$this->add_control( 'message', array( 'label' => esc_html__( 'Message', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Redirecting in {seconds} seconds...', 'powerkit-powerful-tools-for-your-website' ) ) );
		$this->add_control( 'show_progress', array( 'label' => esc_html__( 'Show Progress Bar', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->add_control( 'bar_color', array( 'label' => esc_html__( 'Bar Color', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-redirect-timer-bar-fill' => 'background: {{VALUE}};' ) ) );
		$this->add_control( 'align', array( 'label' => esc_html__( 'Alignment', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::CHOOSE, 'default' => 'left', 'options' => array( 'left' => array( 'title' => esc_html__( 'Left', 'powerkit-powerful-tools-for-your-website' ), 'icon' => 'eicon-text-align-left' ), 'center' => array( 'title' => esc_html__( 'Center', 'powerkit-powerful-tools-for-your-website' ), 'icon' => 'eicon-text-align-center' ), 'right' => array( 'title' => esc_html__( 'Right', 'powerkit-powerful-tools-for-your-website' ), 'icon' => 'eicon-text-align-right' ) ), 'selectors' => array( '{{WRAPPER}} .pkwt-redirect-timer' => 'text-align: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'typography', 'selector' => '{{WRAPPER}} .pkwt-redirect-timer-message' ) );
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<div class="pkwt-redirect-timer" data-pkwt-redirect-timer data-seconds="<?php echo esc_attr( (int) $s['seconds'] ); ?>" data-message="<?php echo esc_attr( $s['message'] ); ?>">
			<div class="pkwt-redirect-timer-message"></div>
			<?php if ( 'yes' === $s['show_progress'] ) : ?>
			<div class="pkwt-redirect-timer-bar"><div class="pkwt-redirect-timer-bar-fill"></div></div>
			<?php endif; ?>
		</div>
		<?php
	}
}
