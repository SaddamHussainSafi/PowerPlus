<?php
/**
 * Shared form widget base.
 *
 * @package PKWT
 */

namespace PKWT\Elementor\Widgets;

use PKWT\Includes\Class_PKWT_Security;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Class_Abstract_Form_Widget extends Widget_Base {

	/**
	 * Frontend style dependencies.
	 *
	 * @return string[]
	 */
	public function get_style_depends(): array {
		return array( 'pkwt-frontend' );
	}

	/**
	 * Frontend script dependencies.
	 *
	 * @return string[]
	 */
	public function get_script_depends(): array {
		return array( 'pkwt-frontend' );
	}

	/**
	 * Return the page type key for this widget (login|register|lost|reset).
	 * Subclasses override this.
	 *
	 * @return string
	 */
	protected function get_page_type(): string {
		return 'login';
	}

	/**
	 * Register shared controls (style sections only — content is handled per-widget).
	 *
	 * @return void
	 */
	protected function register_shared_controls(): void {

		// ── Page Template Picker (Content tab) ─────────────────────────────────
		// NOTE: nonce and ajaxUrl are NOT embedded here — they come from the
		// PKWTEditorTpl JS object (localized fresh on every editor page load).
		$page_type = $this->get_page_type();

		$sets = array(
			'split-left'    => array(
				'label'   => __( 'Split Left', 'powerkit-powerful-tools-for-your-website' ),
				'desc'    => __( 'Dark panel + white form', 'powerkit-powerful-tools-for-your-website' ),
				'bg'      => '#0f172a',
				'accent'  => '#6366f1',
				'layout'  => 'split-left',
			),
			'centered-card' => array(
				'label'   => __( 'Centered Card', 'powerkit-powerful-tools-for-your-website' ),
				'desc'    => __( 'Card on gradient background', 'powerkit-powerful-tools-for-your-website' ),
				'bg'      => 'linear-gradient(135deg,#1a1a4e,#2563eb)',
				'accent'  => '#2563eb',
				'layout'  => 'centered',
			),
			'form-left'     => array(
				'label'   => __( 'Gradient Panel Right', 'powerkit-powerful-tools-for-your-website' ),
				'desc'    => __( 'Form left + gradient right', 'powerkit-powerful-tools-for-your-website' ),
				'bg'      => '#fff',
				'accent'  => '#7c3aed',
				'layout'  => 'form-left',
			),
		);

		$import_label = esc_html__( 'Import', 'powerkit-powerful-tools-for-your-website' );

		// Helper: build mini mockup SVG for each layout
		$cards_html = '<div class="pkwt-tpl-picker" data-page-type="' . esc_attr( $page_type ) . '">';

		foreach ( $sets as $slug => $set ) {
			$label  = esc_html( $set['label'] );
			$desc   = esc_html( $set['desc'] );
			$accent = esc_attr( $set['accent'] );
			$layout = $set['layout'];

			// Build inline SVG mini preview for each layout type
			if ( 'split-left' === $layout ) {
				// Dark panel left | white form right
				$svg = '<svg viewBox="0 0 52 36" xmlns="http://www.w3.org/2000/svg" style="display:block;width:52px;height:36px;">'
					. '<rect width="52" height="36" fill="#0f172a"/>'
					. '<rect x="26" width="26" height="36" fill="#fff"/>'
					. '<rect x="4" y="7" width="14" height="2" rx="1" fill="rgba(255,255,255,.5)"/>'
					. '<rect x="4" y="11" width="10" height="1.5" rx="1" fill="rgba(255,255,255,.25)"/>'
					. '<rect x="4" y="15" width="12" height="1.5" rx="1" fill="rgba(255,255,255,.2)"/>'
					. '<rect x="28" y="8" width="18" height="3" rx="1.5" fill="#e2e8f0"/>'
					. '<rect x="28" y="13" width="18" height="3" rx="1.5" fill="#e2e8f0"/>'
					. '<rect x="28" y="19" width="18" height="4" rx="2" fill="' . $accent . '"/>'
					. '</svg>';
			} elseif ( 'centered' === $layout ) {
				// Gradient bg | centred white card
				$svg = '<svg viewBox="0 0 52 36" xmlns="http://www.w3.org/2000/svg" style="display:block;width:52px;height:36px;">'
					. '<defs><linearGradient id="cg' . esc_attr( $slug ) . '" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#1a1a4e"/><stop offset="100%" stop-color="#2563eb"/></linearGradient></defs>'
					. '<rect width="52" height="36" fill="url(#cg' . esc_attr( $slug ) . ')"/>'
					. '<rect x="10" y="5" width="32" height="26" rx="3" fill="#fff" opacity=".97"/>'
					. '<rect x="14" y="10" width="24" height="2" rx="1" fill="#1e293b"/>'
					. '<rect x="14" y="15" width="24" height="3" rx="1.5" fill="#e2e8f0"/>'
					. '<rect x="14" y="20" width="24" height="3" rx="1.5" fill="#e2e8f0"/>'
					. '<rect x="14" y="25" width="24" height="4" rx="2" fill="' . $accent . '"/>'
					. '</svg>';
			} else {
				// Form left | gradient panel right
				$svg = '<svg viewBox="0 0 52 36" xmlns="http://www.w3.org/2000/svg" style="display:block;width:52px;height:36px;">'
					. '<rect width="52" height="36" fill="#fff"/>'
					. '<defs><linearGradient id="fg' . esc_attr( $slug ) . '" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#4f46e5"/><stop offset="100%" stop-color="#7c3aed"/></linearGradient></defs>'
					. '<rect x="28" width="24" height="36" fill="url(#fg' . esc_attr( $slug ) . ')"/>'
					. '<rect x="3" y="8" width="18" height="3" rx="1.5" fill="#e2e8f0"/>'
					. '<rect x="3" y="13" width="18" height="3" rx="1.5" fill="#e2e8f0"/>'
					. '<rect x="3" y="19" width="18" height="4" rx="2" fill="' . $accent . '"/>'
					. '<rect x="33" y="10" width="14" height="2" rx="1" fill="rgba(255,255,255,.5)"/>'
					. '<rect x="33" y="14" width="10" height="1.5" rx="1" fill="rgba(255,255,255,.3)"/>'
					. '</svg>';
			}

			$cards_html .= '<div class="pkwt-tpl-picker__card" data-set="' . esc_attr( $slug ) . '">';
			$cards_html .= '<div class="pkwt-tpl-picker__swatch">' . $svg . '</div>';
			$cards_html .= '<div class="pkwt-tpl-picker__info"><strong>' . $label . '</strong><span>' . $desc . '</span></div>';
			$cards_html .= '<button type="button" class="pkwt-tpl-picker__btn" data-set="' . esc_attr( $slug ) . '">' . $import_label . '</button>';
			$cards_html .= '</div>';
		}
		$cards_html .= '<div class="pkwt-tpl-picker__status" style="display:none;"></div>';
		$cards_html .= '</div>';

		$this->start_controls_section(
			'pkwt_template_picker',
			array(
				'label' => esc_html__( 'Page Templates', 'powerkit-powerful-tools-for-your-website' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'pkwt_tpl_picker_html',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => $cards_html,
				'content_classes' => 'pkwt-tpl-picker-wrap',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'pkwt_form_container_style', array( 'label' => esc_html__( 'Form Container', 'powerkit-powerful-tools-for-your-website' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Background::get_type(), array( 'name' => 'form_background', 'types' => array( 'classic', 'gradient' ), 'selector' => '{{WRAPPER}} .pkwt-form-wrap' ) );
		$this->add_responsive_control( 'form_padding', array( 'label' => esc_html__( 'Padding', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'em', '%' ), 'selectors' => array( '{{WRAPPER}} .pkwt-form-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'form_margin', array( 'label' => esc_html__( 'Margin', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'em', '%' ), 'selectors' => array( '{{WRAPPER}} .pkwt-form-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_group_control( Group_Control_Border::get_type(), array( 'name' => 'form_border', 'selector' => '{{WRAPPER}} .pkwt-form-wrap' ) );
		$this->add_responsive_control( 'form_border_radius', array( 'label' => esc_html__( 'Border Radius', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', '%' ), 'selectors' => array( '{{WRAPPER}} .pkwt-form-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array( 'name' => 'form_box_shadow', 'selector' => '{{WRAPPER}} .pkwt-form-wrap' ) );
		$this->add_responsive_control( 'form_width', array( 'label' => esc_html__( 'Width', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px', '%' ), 'range' => array( 'px' => array( 'min' => 200, 'max' => 1200 ), '%' => array( 'min' => 10, 'max' => 100 ) ), 'selectors' => array( '{{WRAPPER}} .pkwt-form-wrap' => 'width: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'form_max_width', array( 'label' => esc_html__( 'Max Width', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px', '%' ), 'range' => array( 'px' => array( 'min' => 200, 'max' => 1600 ), '%' => array( 'min' => 10, 'max' => 100 ) ), 'selectors' => array( '{{WRAPPER}} .pkwt-form-wrap' => 'max-width: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'form_z_index', array( 'label' => esc_html__( 'Z-Index', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::NUMBER, 'selectors' => array( '{{WRAPPER}} .pkwt-form-wrap' => 'z-index: {{VALUE}};' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'pkwt_title_desc_style', array( 'label' => esc_html__( 'Title & Description', 'powerkit-powerful-tools-for-your-website' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .pkwt-form-title' ) );
		$this->add_control( 'title_color', array( 'label' => esc_html__( 'Title Color', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-form-title' => 'color: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'title_align', array( 'label' => esc_html__( 'Title Alignment', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::CHOOSE, 'options' => array( 'left' => array( 'title' => esc_html__( 'Left', 'powerkit-powerful-tools-for-your-website' ), 'icon' => 'eicon-text-align-left' ), 'center' => array( 'title' => esc_html__( 'Center', 'powerkit-powerful-tools-for-your-website' ), 'icon' => 'eicon-text-align-center' ), 'right' => array( 'title' => esc_html__( 'Right', 'powerkit-powerful-tools-for-your-website' ), 'icon' => 'eicon-text-align-right' ) ), 'selectors' => array( '{{WRAPPER}} .pkwt-form-title' => 'text-align: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'desc_typography', 'selector' => '{{WRAPPER}} .pkwt-form-description' ) );
		$this->add_control( 'desc_color', array( 'label' => esc_html__( 'Description Color', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-form-description' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'pkwt_inputs_style', array( 'label' => esc_html__( 'Input Fields', 'powerkit-powerful-tools-for-your-website' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->start_controls_tabs( 'pkwt_input_state_tabs' );
		$this->start_controls_tab( 'pkwt_input_state_normal', array( 'label' => esc_html__( 'Normal', 'powerkit-powerful-tools-for-your-website' ) ) );
		$this->add_control( 'field_background_color', array( 'label' => esc_html__( 'Field Background', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-form input' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'field_text_color', array( 'label' => esc_html__( 'Field Text Color', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-form input' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'field_placeholder_color', array( 'label' => esc_html__( 'Placeholder Color', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-form input::placeholder' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'field_typography', 'selector' => '{{WRAPPER}} .pkwt-form input' ) );
		$this->add_group_control( Group_Control_Border::get_type(), array( 'name' => 'field_border', 'selector' => '{{WRAPPER}} .pkwt-form input' ) );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array( 'name' => 'field_box_shadow', 'selector' => '{{WRAPPER}} .pkwt-form input' ) );
		$this->end_controls_tab();

		$this->start_controls_tab( 'pkwt_input_state_focus', array( 'label' => esc_html__( 'Focus', 'powerkit-powerful-tools-for-your-website' ) ) );
		$this->add_control( 'field_focus_background_color', array( 'label' => esc_html__( 'Focus Background', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-form input:focus' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'field_focus_border_color', array( 'label' => esc_html__( 'Focus Border Color', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-form input:focus' => 'border-color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array( 'name' => 'field_focus_shadow', 'selector' => '{{WRAPPER}} .pkwt-form input:focus' ) );
		$this->end_controls_tab();

		$this->start_controls_tab( 'pkwt_input_state_error', array( 'label' => esc_html__( 'Error', 'powerkit-powerful-tools-for-your-website' ) ) );
		$this->add_control( 'field_error_background', array( 'label' => esc_html__( 'Error Field Background', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-form input.is-invalid' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'field_error_border', array( 'label' => esc_html__( 'Error Border Color', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-form input.is-invalid' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'field_error_message_color', array( 'label' => esc_html__( 'Error Message Color', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-field-error, {{WRAPPER}} .pkwt-message.is-error' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'field_error_message_typography', 'selector' => '{{WRAPPER}} .pkwt-field-error, {{WRAPPER}} .pkwt-message' ) );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control( 'field_padding', array( 'label' => esc_html__( 'Field Padding', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'em', '%' ), 'selectors' => array( '{{WRAPPER}} .pkwt-form input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'field_margin', array( 'label' => esc_html__( 'Field Margin', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'em', '%' ), 'selectors' => array( '{{WRAPPER}} .pkwt-form input' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'field_border_radius', array( 'label' => esc_html__( 'Field Border Radius', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', '%' ), 'selectors' => array( '{{WRAPPER}} .pkwt-form input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'pkwt_button_style', array( 'label' => esc_html__( 'Submit Button', 'powerkit-powerful-tools-for-your-website' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->start_controls_tabs( 'pkwt_button_state_tabs' );
		$this->start_controls_tab( 'pkwt_button_state_normal', array( 'label' => esc_html__( 'Normal', 'powerkit-powerful-tools-for-your-website' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'button_typography', 'selector' => '{{WRAPPER}} .pkwt-submit' ) );
		$this->add_control( 'button_text_color', array( 'label' => esc_html__( 'Text Color', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-submit' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Background::get_type(), array( 'name' => 'button_background', 'types' => array( 'classic', 'gradient' ), 'selector' => '{{WRAPPER}} .pkwt-submit' ) );
		$this->add_group_control( Group_Control_Border::get_type(), array( 'name' => 'button_border', 'selector' => '{{WRAPPER}} .pkwt-submit' ) );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array( 'name' => 'button_shadow', 'selector' => '{{WRAPPER}} .pkwt-submit' ) );
		$this->add_group_control( Group_Control_Text_Shadow::get_type(), array( 'name' => 'button_text_shadow', 'selector' => '{{WRAPPER}} .pkwt-submit' ) );
		$this->end_controls_tab();
		$this->start_controls_tab( 'pkwt_button_state_hover', array( 'label' => esc_html__( 'Hover', 'powerkit-powerful-tools-for-your-website' ) ) );
		$this->add_control( 'button_hover_text_color', array( 'label' => esc_html__( 'Hover Text Color', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-submit:hover' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Background::get_type(), array( 'name' => 'button_hover_background', 'types' => array( 'classic', 'gradient' ), 'selector' => '{{WRAPPER}} .pkwt-submit:hover' ) );
		$this->add_control( 'button_hover_border_color', array( 'label' => esc_html__( 'Hover Border Color', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-submit:hover' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'button_hover_translate_y', array( 'label' => esc_html__( 'Hover Translate Y', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px' ), 'selectors' => array( '{{WRAPPER}} .pkwt-submit:hover' => 'transform: translateY({{SIZE}}{{UNIT}});' ) ) );
		$this->add_control( 'button_transition_duration', array( 'label' => esc_html__( 'Transition Duration (ms)', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 50, 'max' => 2000 ) ), 'selectors' => array( '{{WRAPPER}} .pkwt-submit' => 'transition-duration: {{SIZE}}ms;' ) ) );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control( 'button_padding', array( 'label' => esc_html__( 'Button Padding', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'em', '%' ), 'selectors' => array( '{{WRAPPER}} .pkwt-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'button_margin', array( 'label' => esc_html__( 'Button Margin', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'em', '%' ), 'selectors' => array( '{{WRAPPER}} .pkwt-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'button_border_radius', array( 'label' => esc_html__( 'Button Border Radius', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', '%' ), 'selectors' => array( '{{WRAPPER}} .pkwt-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_control( 'loading_text', array( 'label' => esc_html__( 'Loading Text', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Please wait...', 'powerkit-powerful-tools-for-your-website' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'pkwt_links_style', array( 'label' => esc_html__( 'Links', 'powerkit-powerful-tools-for-your-website' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'links_typography', 'selector' => '{{WRAPPER}} .pkwt-form a' ) );
		$this->add_control( 'links_color', array( 'label' => esc_html__( 'Link Color', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-form a' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'links_hover_color', array( 'label' => esc_html__( 'Link Hover Color', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-form a:hover' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'pkwt_messages_style', array( 'label' => esc_html__( 'Messages', 'powerkit-powerful-tools-for-your-website' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_control( 'success_background', array( 'label' => esc_html__( 'Success Background', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-message.is-success' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'success_text_color', array( 'label' => esc_html__( 'Success Text Color', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-message.is-success' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'error_background', array( 'label' => esc_html__( 'Error Background', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-message.is-error' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'error_text_color', array( 'label' => esc_html__( 'Error Text Color', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-message.is-error' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'messages_typography', 'selector' => '{{WRAPPER}} .pkwt-message' ) );
		$this->add_group_control( Group_Control_Border::get_type(), array( 'name' => 'messages_border', 'selector' => '{{WRAPPER}} .pkwt-message' ) );
		$this->add_responsive_control( 'messages_padding', array( 'label' => esc_html__( 'Message Padding', 'powerkit-powerful-tools-for-your-website' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'em', '%' ), 'selectors' => array( '{{WRAPPER}} .pkwt-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->end_controls_section();
	}

	/**
	 * Render optional heading content.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 *
	 * @return void
	 */
	protected function render_form_heading( array $settings ): void {
		if ( ! empty( $settings['form_title'] ) ) {
			echo '<h3 class="pkwt-form-title">' . esc_html( $settings['form_title'] ) . '</h3>';
		}
		if ( ! empty( $settings['form_description'] ) ) {
			echo '<p class="pkwt-form-description">' . esc_html( $settings['form_description'] ) . '</p>';
		}
	}

	/**
	 * Render standard wrapper opening.
	 *
	 * @param string $form_type       Form type.
	 * @param string $ajax_action     Ajax action.
	 * @param string $nonce_action    Nonce action base.
	 * @param string $success_redirect Redirect.
	 * @param string $error_message   Default error.
	 * @param string $loading_text    Loading text.
	 *
	 * @return void
	 */
	protected function render_form_open( string $form_type, string $ajax_action, string $nonce_action, string $success_redirect = '', string $error_message = '', string $loading_text = '' ): void {
		echo '<div class="pkwt-form-wrap" data-pkwt-form="' . esc_attr( $form_type ) . '" data-action="' . esc_attr( $ajax_action ) . '" data-nonce="' . esc_attr( Class_PKWT_Security::nonce( $nonce_action ) ) . '" data-default-error="' . esc_attr( $error_message ) . '" data-success-redirect="' . esc_url( $success_redirect ) . '" data-loading-text="' . esc_attr( $loading_text ) . '">';
		echo '<form class="pkwt-form" role="form" aria-label="' . esc_attr( $this->get_title() ) . '" novalidate>';
		// Honeypot field for bot filtering.
		echo '<input type="text" name="website_url" value="" class="pkwt-honeypot" tabindex="-1" autocomplete="off" aria-hidden="true" />';
	}

	/**
	 * Render standard wrapper closing.
	 *
	 * @return void
	 */
	protected function render_form_close(): void {
		echo '</form></div>';
	}
}
