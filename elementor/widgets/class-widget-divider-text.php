<?php
/**
 * Divider text widget.
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

class Class_Widget_Divider_Text extends Widget_Base {

	public function get_name() { return 'pkwt-divider-text'; }
	public function get_title() { return esc_html__( 'PowerKit Divider Text', 'powerkit-powerful-tools-for-your-website' ); }
	public function get_icon() { return 'eicon-divider'; }
	public function get_categories() { return array( 'powerkit-powerful-tools-for-your-website' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'Divider', 'powerkit-powerful-tools-for-your-website' ) ) );
		$this->add_control( 'text', array( 'label' => esc_html__( 'Text', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'OR', 'powerkit-powerful-tools-for-your-website' ) ) );
		$this->add_control( 'line_color', array( 'label' => esc_html__( 'Line Color', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-divider::before, {{WRAPPER}} .pkwt-divider::after' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'line_thickness', array( 'label' => esc_html__( 'Line Thickness', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 1, 'max' => 10 ) ), 'selectors' => array( '{{WRAPPER}} .pkwt-divider::before, {{WRAPPER}} .pkwt-divider::after' => 'border-top-width: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'text_color', array( 'label' => esc_html__( 'Text Color', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-divider span' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'typography', 'selector' => '{{WRAPPER}} .pkwt-divider span' ) );
		$this->add_responsive_control( 'spacing', array( 'label' => esc_html__( 'Spacing', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::DIMENSIONS, 'selectors' => array( '{{WRAPPER}} .pkwt-divider' => 'margin: {{TOP}}{{UNIT}} 0 {{BOTTOM}}{{UNIT}} 0;' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		echo '<div class="pkwt-divider"><span>' . esc_html( $this->get_settings_for_display( 'text' ) ) . '</span></div>';
	}
}
