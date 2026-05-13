<?php
/**
 * Social login widget.
 *
 * @package PKWT
 */

namespace PKWT\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_Widget_Social_Login extends Widget_Base {

	public function get_name() { return 'pkwt-social-login'; }
	public function get_title() { return esc_html__( 'PowerKit Social Login', 'powerkit-powerful-tools-for-your-website' ); }
	public function get_icon() { return 'eicon-share-arrow'; }
	public function get_categories() { return array( 'powerkit-powerful-tools-for-your-website' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'Providers', 'powerkit-powerful-tools-for-your-website' ) ) );
		$this->add_control( 'providers', array( 'label' => esc_html__( 'Providers', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::SELECT2, 'multiple' => true, 'options' => array( 'google' => 'Google', 'facebook' => 'Facebook', 'twitter' => 'X/Twitter', 'apple' => 'Apple' ), 'default' => array( 'google', 'facebook' ) ) );
		$this->add_control( 'style', array( 'label' => esc_html__( 'Button Style', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::SELECT, 'options' => array( 'icon-only' => esc_html__( 'Icon Only', 'powerkit-powerful-tools-for-your-website' ), 'icon-text' => esc_html__( 'Icon + Text', 'powerkit-powerful-tools-for-your-website' ), 'full-width' => esc_html__( 'Full Width', 'powerkit-powerful-tools-for-your-website' ) ), 'default' => 'icon-text' ) );
		$this->add_control( 'alignment', array( 'label' => esc_html__( 'Alignment', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::CHOOSE, 'options' => array( 'left' => array( 'title' => esc_html__( 'Left', 'powerkit-powerful-tools-for-your-website' ), 'icon' => 'eicon-text-align-left' ), 'center' => array( 'title' => esc_html__( 'Center', 'powerkit-powerful-tools-for-your-website' ), 'icon' => 'eicon-text-align-center' ), 'right' => array( 'title' => esc_html__( 'Right', 'powerkit-powerful-tools-for-your-website' ), 'icon' => 'eicon-text-align-right' ) ), 'default' => 'left', 'selectors' => array( '{{WRAPPER}} .pkwt-social-login' => 'text-align: {{VALUE}};' ) ) );
		$this->add_control( 'gap', array( 'label' => esc_html__( 'Button Gap', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 40 ) ), 'selectors' => array( '{{WRAPPER}} .pkwt-social-login' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'radius', array( 'label' => esc_html__( 'Border Radius', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 30 ) ), 'selectors' => array( '{{WRAPPER}} .pkwt-social-login .pkwt-social-btn' => 'border-radius: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$providers = ! empty( $settings['providers'] ) ? (array) $settings['providers'] : array();
		$nextend_active = function_exists( 'NextendSocialLogin' ) || class_exists( 'NextendSocialLogin' );

		echo '<div class="pkwt-social-login">';
		if ( $nextend_active ) {
			foreach ( $providers as $provider ) {
				echo '<a class="pkwt-social-btn" href="' . esc_url( wp_login_url() ) . '">' . esc_html( ucfirst( $provider ) ) . '</a>';
			}
		} else {
			echo '<p>' . esc_html__( 'No OAuth provider plugin detected. Install Nextend Social Login to enable social auth.', 'powerkit-powerful-tools-for-your-website' ) . '</p>';
		}
		echo '</div>';
	}
}
