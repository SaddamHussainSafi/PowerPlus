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
				'label'   => __( 'Split Left', 'powerplus-toolkit' ),
				'desc'    => __( 'Dark panel + white form', 'powerplus-toolkit' ),
				'bg'      => '#0f172a',
				'accent'  => '#6366f1',
				'layout'  => 'split-left',
			),
			'centered-card' => array(
				'label'   => __( 'Centered Card', 'powerplus-toolkit' ),
				'desc'    => __( 'Card on gradient background', 'powerplus-toolkit' ),
				'bg'      => 'linear-gradient(135deg,#1a1a4e,#2563eb)',
				'accent'  => '#2563eb',
				'layout'  => 'centered',
			),
			'form-left'     => array(
				'label'   => __( 'Gradient Panel Right', 'powerplus-toolkit' ),
				'desc'    => __( 'Form left + gradient right', 'powerplus-toolkit' ),
				'bg'      => '#fff',
				'accent'  => '#7c3aed',
				'layout'  => 'form-left',
			),
		);

		$import_label = esc_html__( 'Import', 'powerplus-toolkit' );

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
				'label' => esc_html__( 'Page Templates', 'powerplus-toolkit' ),
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

		$this->start_controls_section( 'pkwt_form_container_style', array( 'label' => esc_html__( 'Form Container', 'powerplus-toolkit' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Background::get_type(), array( 'name' => 'form_background', 'types' => array( 'classic', 'gradient' ), 'fields_options' => array( 'background' => array( 'default' => 'classic' ), 'color' => array( 'default' => '#ffffff' ) ), 'selector' => '{{WRAPPER}} .pkwt-form-wrap' ) );
		$this->add_responsive_control( 'form_padding', array( 'label' => esc_html__( 'Padding', 'powerplus-toolkit' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'em', '%' ), 'default' => array( 'top' => '40', 'right' => '36', 'bottom' => '40', 'left' => '36', 'unit' => 'px', 'isLinked' => false ), 'selectors' => array( '{{WRAPPER}} .pkwt-form-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'form_margin', array( 'label' => esc_html__( 'Margin', 'powerplus-toolkit' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'em', '%' ), 'selectors' => array( '{{WRAPPER}} .pkwt-form-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_group_control( Group_Control_Border::get_type(), array( 'name' => 'form_border', 'selector' => '{{WRAPPER}} .pkwt-form-wrap' ) );
		$this->add_responsive_control( 'form_border_radius', array( 'label' => esc_html__( 'Border Radius', 'powerplus-toolkit' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', '%' ), 'default' => array( 'top' => '16', 'right' => '16', 'bottom' => '16', 'left' => '16', 'unit' => 'px', 'isLinked' => false ), 'selectors' => array( '{{WRAPPER}} .pkwt-form-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array( 'name' => 'form_box_shadow', 'selector' => '{{WRAPPER}} .pkwt-form-wrap' ) );
		$this->add_responsive_control( 'form_width', array( 'label' => esc_html__( 'Width', 'powerplus-toolkit' ), 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px', '%' ), 'range' => array( 'px' => array( 'min' => 200, 'max' => 1200 ), '%' => array( 'min' => 10, 'max' => 100 ) ), 'default' => array( 'size' => 100, 'unit' => '%' ), 'selectors' => array( '{{WRAPPER}} .pkwt-form-wrap' => 'width: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'form_max_width', array( 'label' => esc_html__( 'Max Width', 'powerplus-toolkit' ), 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px', '%' ), 'range' => array( 'px' => array( 'min' => 200, 'max' => 1600 ), '%' => array( 'min' => 10, 'max' => 100 ) ), 'default' => array( 'size' => 480, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .pkwt-form-wrap' => 'max-width: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'form_z_index', array( 'label' => esc_html__( 'Z-Index', 'powerplus-toolkit' ), 'type' => Controls_Manager::NUMBER, 'selectors' => array( '{{WRAPPER}} .pkwt-form-wrap' => 'z-index: {{VALUE}};' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'pkwt_title_desc_style', array( 'label' => esc_html__( 'Title & Description', 'powerplus-toolkit' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'title_typography', 'fields_options' => array( 'typography' => array( 'default' => 'custom' ), 'font_size' => array( 'default' => array( 'size' => 26, 'unit' => 'px' ) ), 'font_weight' => array( 'default' => '700' ), 'line_height' => array( 'default' => array( 'size' => 1.25, 'unit' => 'em' ) ) ), 'selector' => '{{WRAPPER}} .pkwt-form-title' ) );
		$this->add_control( 'title_color', array( 'label' => esc_html__( 'Title Color', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'default' => '#111827', 'selectors' => array( '{{WRAPPER}} .pkwt-form-title' => 'color: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'title_align', array( 'label' => esc_html__( 'Title Alignment', 'powerplus-toolkit' ), 'type' => Controls_Manager::CHOOSE, 'options' => array( 'left' => array( 'title' => esc_html__( 'Left', 'powerplus-toolkit' ), 'icon' => 'eicon-text-align-left' ), 'center' => array( 'title' => esc_html__( 'Center', 'powerplus-toolkit' ), 'icon' => 'eicon-text-align-center' ), 'right' => array( 'title' => esc_html__( 'Right', 'powerplus-toolkit' ), 'icon' => 'eicon-text-align-right' ) ), 'selectors' => array( '{{WRAPPER}} .pkwt-form-title' => 'text-align: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'desc_typography', 'fields_options' => array( 'typography' => array( 'default' => 'custom' ), 'font_size' => array( 'default' => array( 'size' => 14, 'unit' => 'px' ) ), 'line_height' => array( 'default' => array( 'size' => 1.6, 'unit' => 'em' ) ) ), 'selector' => '{{WRAPPER}} .pkwt-form-description' ) );
		$this->add_control( 'desc_color', array( 'label' => esc_html__( 'Description Color', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'default' => '#6b7280', 'selectors' => array( '{{WRAPPER}} .pkwt-form-description' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'pkwt_inputs_style', array( 'label' => esc_html__( 'Input Fields', 'powerplus-toolkit' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->start_controls_tabs( 'pkwt_input_state_tabs' );
		$this->start_controls_tab( 'pkwt_input_state_normal', array( 'label' => esc_html__( 'Normal', 'powerplus-toolkit' ) ) );
		$this->add_control( 'field_background_color', array( 'label' => esc_html__( 'Field Background', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'default' => '#f9fafb', 'selectors' => array( '{{WRAPPER}} .pkwt-form input' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'field_text_color', array( 'label' => esc_html__( 'Field Text Color', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'default' => '#111827', 'selectors' => array( '{{WRAPPER}} .pkwt-form input' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'field_placeholder_color', array( 'label' => esc_html__( 'Placeholder Color', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'default' => '#9ca3af', 'selectors' => array( '{{WRAPPER}} .pkwt-form input::placeholder' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'field_typography', 'fields_options' => array( 'typography' => array( 'default' => 'custom' ), 'font_size' => array( 'default' => array( 'size' => 14, 'unit' => 'px' ) ) ), 'selector' => '{{WRAPPER}} .pkwt-form input' ) );
		$this->add_group_control( Group_Control_Border::get_type(), array( 'name' => 'field_border', 'fields_options' => array( 'border' => array( 'default' => 'solid' ), 'width' => array( 'default' => array( 'top' => '1.5', 'right' => '1.5', 'bottom' => '1.5', 'left' => '1.5', 'unit' => 'px', 'isLinked' => true ) ), 'color' => array( 'default' => '#e5e7eb' ) ), 'selector' => '{{WRAPPER}} .pkwt-form input' ) );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array( 'name' => 'field_box_shadow', 'selector' => '{{WRAPPER}} .pkwt-form input' ) );
		$this->end_controls_tab();

		$this->start_controls_tab( 'pkwt_input_state_focus', array( 'label' => esc_html__( 'Focus', 'powerplus-toolkit' ) ) );
		$this->add_control( 'field_focus_background_color', array( 'label' => esc_html__( 'Focus Background', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => array( '{{WRAPPER}} .pkwt-form input:focus' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'field_focus_border_color', array( 'label' => esc_html__( 'Focus Border Color', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'default' => '#7c3aed', 'selectors' => array( '{{WRAPPER}} .pkwt-form input:focus' => 'border-color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array( 'name' => 'field_focus_shadow', 'fields_options' => array( 'box_shadow_type' => array( 'default' => 'yes' ), 'box_shadow' => array( 'default' => array( 'horizontal' => 0, 'vertical' => 0, 'blur' => 0, 'spread' => 3, 'color' => 'rgba(124,58,237,0.12)' ) ) ), 'selector' => '{{WRAPPER}} .pkwt-form input:focus' ) );
		$this->end_controls_tab();

		$this->start_controls_tab( 'pkwt_input_state_error', array( 'label' => esc_html__( 'Error', 'powerplus-toolkit' ) ) );
		$this->add_control( 'field_error_background', array( 'label' => esc_html__( 'Error Field Background', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'default' => '#fef2f2', 'selectors' => array( '{{WRAPPER}} .pkwt-form input.is-invalid' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'field_error_border', array( 'label' => esc_html__( 'Error Border Color', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'default' => '#ef4444', 'selectors' => array( '{{WRAPPER}} .pkwt-form input.is-invalid' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'field_error_message_color', array( 'label' => esc_html__( 'Error Message Color', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'default' => '#ef4444', 'selectors' => array( '{{WRAPPER}} .pkwt-field-error, {{WRAPPER}} .pkwt-message.is-error' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'field_error_message_typography', 'selector' => '{{WRAPPER}} .pkwt-field-error, {{WRAPPER}} .pkwt-message' ) );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control( 'field_padding', array( 'label' => esc_html__( 'Field Padding', 'powerplus-toolkit' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'em', '%' ), 'default' => array( 'top' => '11', 'right' => '14', 'bottom' => '11', 'left' => '14', 'unit' => 'px', 'isLinked' => false ), 'selectors' => array( '{{WRAPPER}} .pkwt-form input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'field_margin', array( 'label' => esc_html__( 'Field Margin', 'powerplus-toolkit' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'em', '%' ), 'selectors' => array( '{{WRAPPER}} .pkwt-form input' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'field_border_radius', array( 'label' => esc_html__( 'Field Border Radius', 'powerplus-toolkit' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', '%' ), 'default' => array( 'top' => '10', 'right' => '10', 'bottom' => '10', 'left' => '10', 'unit' => 'px', 'isLinked' => false ), 'selectors' => array( '{{WRAPPER}} .pkwt-form input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'pkwt_button_style', array( 'label' => esc_html__( 'Submit Button', 'powerplus-toolkit' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->start_controls_tabs( 'pkwt_button_state_tabs' );
		$this->start_controls_tab( 'pkwt_button_state_normal', array( 'label' => esc_html__( 'Normal', 'powerplus-toolkit' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'button_typography', 'fields_options' => array( 'typography' => array( 'default' => 'custom' ), 'font_size' => array( 'default' => array( 'size' => 15, 'unit' => 'px' ) ), 'font_weight' => array( 'default' => '600' ) ), 'selector' => '{{WRAPPER}} .pkwt-submit' ) );
		$this->add_control( 'button_text_color', array( 'label' => esc_html__( 'Text Color', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => array( '{{WRAPPER}} .pkwt-submit' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Background::get_type(), array( 'name' => 'button_background', 'types' => array( 'classic', 'gradient' ), 'fields_options' => array( 'background' => array( 'default' => 'gradient' ), 'color' => array( 'default' => '#7c3aed' ), 'color_b' => array( 'default' => '#6d28d9' ), 'gradient_angle' => array( 'default' => array( 'size' => 135, 'unit' => 'deg' ) ) ), 'selector' => '{{WRAPPER}} .pkwt-submit' ) );
		$this->add_group_control( Group_Control_Border::get_type(), array( 'name' => 'button_border', 'selector' => '{{WRAPPER}} .pkwt-submit' ) );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array( 'name' => 'button_shadow', 'fields_options' => array( 'box_shadow_type' => array( 'default' => 'yes' ), 'box_shadow' => array( 'default' => array( 'horizontal' => 0, 'vertical' => 2, 'blur' => 10, 'spread' => 0, 'color' => 'rgba(124,58,237,0.3)' ) ) ), 'selector' => '{{WRAPPER}} .pkwt-submit' ) );
		$this->add_group_control( Group_Control_Text_Shadow::get_type(), array( 'name' => 'button_text_shadow', 'selector' => '{{WRAPPER}} .pkwt-submit' ) );
		$this->end_controls_tab();
		$this->start_controls_tab( 'pkwt_button_state_hover', array( 'label' => esc_html__( 'Hover', 'powerplus-toolkit' ) ) );
		$this->add_control( 'button_hover_text_color', array( 'label' => esc_html__( 'Hover Text Color', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-submit:hover' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Background::get_type(), array( 'name' => 'button_hover_background', 'types' => array( 'classic', 'gradient' ), 'selector' => '{{WRAPPER}} .pkwt-submit:hover' ) );
		$this->add_control( 'button_hover_border_color', array( 'label' => esc_html__( 'Hover Border Color', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .pkwt-submit:hover' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'button_hover_translate_y', array( 'label' => esc_html__( 'Hover Translate Y', 'powerplus-toolkit' ), 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px' ), 'default' => array( 'size' => -2, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .pkwt-submit:hover' => 'transform: translateY({{SIZE}}{{UNIT}});' ) ) );
		$this->add_control( 'button_transition_duration', array( 'label' => esc_html__( 'Transition Duration (ms)', 'powerplus-toolkit' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 50, 'max' => 2000 ) ), 'default' => array( 'size' => 180 ), 'selectors' => array( '{{WRAPPER}} .pkwt-submit' => 'transition-duration: {{SIZE}}ms;' ) ) );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control( 'button_padding', array( 'label' => esc_html__( 'Button Padding', 'powerplus-toolkit' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'em', '%' ), 'default' => array( 'top' => '13', 'right' => '20', 'bottom' => '13', 'left' => '20', 'unit' => 'px', 'isLinked' => false ), 'selectors' => array( '{{WRAPPER}} .pkwt-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'button_margin', array( 'label' => esc_html__( 'Button Margin', 'powerplus-toolkit' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'em', '%' ), 'default' => array( 'top' => '4', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false ), 'selectors' => array( '{{WRAPPER}} .pkwt-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'button_border_radius', array( 'label' => esc_html__( 'Button Border Radius', 'powerplus-toolkit' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', '%' ), 'default' => array( 'top' => '10', 'right' => '10', 'bottom' => '10', 'left' => '10', 'unit' => 'px', 'isLinked' => false ), 'selectors' => array( '{{WRAPPER}} .pkwt-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_control( 'loading_text', array( 'label' => esc_html__( 'Loading Text', 'powerplus-toolkit' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Please wait...', 'powerplus-toolkit' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'pkwt_links_style', array( 'label' => esc_html__( 'Links', 'powerplus-toolkit' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'links_typography', 'selector' => '{{WRAPPER}} .pkwt-form a' ) );
		$this->add_control( 'links_color', array( 'label' => esc_html__( 'Link Color', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'default' => '#7c3aed', 'selectors' => array( '{{WRAPPER}} .pkwt-form a' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'links_hover_color', array( 'label' => esc_html__( 'Link Hover Color', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'default' => '#6d28d9', 'selectors' => array( '{{WRAPPER}} .pkwt-form a:hover' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'pkwt_messages_style', array( 'label' => esc_html__( 'Messages', 'powerplus-toolkit' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_control( 'success_background', array( 'label' => esc_html__( 'Success Background', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'default' => '#ecfdf5', 'selectors' => array( '{{WRAPPER}} .pkwt-message.is-success' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'success_text_color', array( 'label' => esc_html__( 'Success Text Color', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'default' => '#065f46', 'selectors' => array( '{{WRAPPER}} .pkwt-message.is-success' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'error_background', array( 'label' => esc_html__( 'Error Background', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'default' => '#fef2f2', 'selectors' => array( '{{WRAPPER}} .pkwt-message.is-error' => 'background-color: {{VALUE}};' ) ) );
		$this->add_control( 'error_text_color', array( 'label' => esc_html__( 'Error Text Color', 'powerplus-toolkit' ), 'type' => Controls_Manager::COLOR, 'default' => '#b91c1c', 'selectors' => array( '{{WRAPPER}} .pkwt-message.is-error' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'messages_typography', 'selector' => '{{WRAPPER}} .pkwt-message' ) );
		$this->add_group_control( Group_Control_Border::get_type(), array( 'name' => 'messages_border', 'selector' => '{{WRAPPER}} .pkwt-message' ) );
		$this->add_responsive_control( 'messages_padding', array( 'label' => esc_html__( 'Message Padding', 'powerplus-toolkit' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'em', '%' ), 'default' => array( 'top' => '10', 'right' => '14', 'bottom' => '10', 'left' => '14', 'unit' => 'px', 'isLinked' => false ), 'selectors' => array( '{{WRAPPER}} .pkwt-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
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
