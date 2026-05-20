<?php
/**
 * Terms/privacy widget.
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

class Class_Widget_Terms_Privacy extends Widget_Base {

	public function get_name() { return 'pkwt-terms-privacy'; }
	public function get_title() { return esc_html__( 'PowerKit Terms & Privacy', 'powerplus-toolkit' ); }
	public function get_icon() { return 'eicon-document-file'; }
	public function get_categories() { return array( 'powerplus-toolkit' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'Terms & Privacy', 'powerplus-toolkit' ) ) );
		$this->add_control( 'text_template', array( 'label' => esc_html__( 'Text Template', 'powerplus-toolkit' ), 'type' => Controls_Manager::TEXTAREA, 'default' => esc_html__( 'By registering you agree to our {link1} and {link2}.', 'powerplus-toolkit' ) ) );
		$this->add_control( 'link1_label', array( 'label' => esc_html__( 'Link 1 Label', 'powerplus-toolkit' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Terms', 'powerplus-toolkit' ) ) );
		$this->add_control( 'link1_url', array( 'label' => esc_html__( 'Link 1 URL', 'powerplus-toolkit' ), 'type' => Controls_Manager::URL ) );
		$this->add_control( 'link2_label', array( 'label' => esc_html__( 'Link 2 Label', 'powerplus-toolkit' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Privacy Policy', 'powerplus-toolkit' ) ) );
		$this->add_control( 'link2_url', array( 'label' => esc_html__( 'Link 2 URL', 'powerplus-toolkit' ), 'type' => Controls_Manager::URL ) );
		$this->add_control( 'new_tab', array( 'label' => esc_html__( 'Open links in new tab', 'powerplus-toolkit' ), 'type' => Controls_Manager::SWITCHER ) );
		$this->add_control( 'alignment', array( 'label' => esc_html__( 'Alignment', 'powerplus-toolkit' ), 'type' => Controls_Manager::CHOOSE, 'options' => array( 'left' => array( 'title' => esc_html__( 'Left', 'powerplus-toolkit' ), 'icon' => 'eicon-text-align-left' ), 'center' => array( 'title' => esc_html__( 'Center', 'powerplus-toolkit' ), 'icon' => 'eicon-text-align-center' ), 'right' => array( 'title' => esc_html__( 'Right', 'powerplus-toolkit' ), 'icon' => 'eicon-text-align-right' ) ), 'default' => 'left', 'selectors' => array( '{{WRAPPER}} .pkwt-terms-privacy' => 'text-align: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'typography', 'selector' => '{{WRAPPER}} .pkwt-terms-privacy' ) );
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$target = ! empty( $s['new_tab'] ) ? ' target="_blank" rel="noopener noreferrer"' : '';
		$link1 = '<a href="' . esc_url( ! empty( $s['link1_url']['url'] ) ? $s['link1_url']['url'] : '#' ) . '"' . $target . '>' . esc_html( $s['link1_label'] ) . '</a>';
		$link2 = '<a href="' . esc_url( ! empty( $s['link2_url']['url'] ) ? $s['link2_url']['url'] : '#' ) . '"' . $target . '>' . esc_html( $s['link2_label'] ) . '</a>';
		$text  = str_replace( array( '{link1}', '{link2}' ), array( $link1, $link2 ), wp_kses_post( $s['text_template'] ) );
		echo '<div class="pkwt-terms-privacy">' . wp_kses_post( $text ) . '</div>';
	}
}
