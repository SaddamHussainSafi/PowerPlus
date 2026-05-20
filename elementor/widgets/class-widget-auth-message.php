<?php
/**
 * Auth message widget.
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

class Class_Widget_Auth_Message extends Widget_Base {

	public function get_name() { return 'pkwt-auth-message'; }
	public function get_title() { return esc_html__( 'PowerKit Auth Message', 'powerplus-toolkit' ); }
	public function get_icon() { return 'eicon-alert'; }
	public function get_categories() { return array( 'powerplus-toolkit' ); }

	protected function register_controls() {
		$this->start_controls_section( 'style', array( 'label' => esc_html__( 'Style', 'powerplus-toolkit' ) ) );
		$this->add_control( 'success_color', array( 'label' => esc_html__( 'Success Color', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'default' => '#1f8a4d' ) );
		$this->add_control( 'error_color', array( 'label' => esc_html__( 'Error Color', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'default' => '#b42318' ) );
		$this->add_control( 'info_color', array( 'label' => esc_html__( 'Info Color', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'default' => '#175cd3' ) );
		$this->add_control( 'padding', array( 'label' => esc_html__( 'Padding', 'powerplus-toolkit' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => array( '{{WRAPPER}} .pkwt-auth-message' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;' ) ) );
		$this->add_control( 'radius', array( 'label' => esc_html__( 'Border Radius', 'powerplus-toolkit' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 30 ) ), 'selectors' => array( '{{WRAPPER}} .pkwt-auth-message' => 'border-radius: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'show_icon', array( 'label' => esc_html__( 'Show Icon', 'powerplus-toolkit' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'typography', 'selector' => '{{WRAPPER}} .pkwt-auth-message' ) );
		$this->end_controls_section();
	}

	protected function render() {
		$message = '';
		if ( function_exists( 'login_header' ) ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- login_message is a WordPress core hook, not defined by this plugin.
			$message = apply_filters( 'login_message', '' );
		}
			if ( '' === trim( $message ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only message parameter for display.
				$message = isset( $_GET['message'] ) ? sanitize_text_field( wp_unslash( $_GET['message'] ) ) : '';
			}
		if ( '' === $message ) {
			return;
		}
		echo '<div class="pkwt-auth-message">' . wp_kses_post( $message ) . '</div>';
	}
}
