<?php
/**
 * Auth logo widget.
 *
 * @package PKWT
 */

namespace PKWT\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_Widget_Auth_Logo extends Widget_Base {

	public function get_name() { return 'pkwt-auth-logo'; }
	public function get_title() { return esc_html__( 'PowerKit Auth Logo', 'powerplus-toolkit' ); }
	public function get_icon() { return 'eicon-site-logo'; }
	public function get_keywords() { return array( 'logo', 'brand', 'auth' ); }
	public function get_categories() { return array( 'powerplus-toolkit' ); }

	protected function register_controls() {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'Logo', 'powerplus-toolkit' ) ) );
		$this->add_control( 'source', array( 'label' => esc_html__( 'Logo Source', 'powerplus-toolkit' ), 'type' => Controls_Manager::SELECT, 'default' => 'site', 'options' => array( 'site' => esc_html__( 'Site Logo', 'powerplus-toolkit' ), 'custom' => esc_html__( 'Custom', 'powerplus-toolkit' ) ) ) );
		$this->add_control( 'custom_logo', array( 'label' => esc_html__( 'Custom Logo', 'powerplus-toolkit' ), 'type' => Controls_Manager::MEDIA, 'condition' => array( 'source' => 'custom' ) ) );
		$this->add_control( 'link_url', array( 'label' => esc_html__( 'Link URL', 'powerplus-toolkit' ), 'type' => Controls_Manager::URL, 'default' => array( 'url' => home_url( '/' ) ) ) );
		$this->add_control( 'new_tab', array( 'label' => esc_html__( 'Open In New Tab', 'powerplus-toolkit' ), 'type' => Controls_Manager::SWITCHER ) );
		$this->end_controls_section();

		$this->start_controls_section( 'style', array( 'label' => esc_html__( 'Style', 'powerplus-toolkit' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_responsive_control( 'logo_width', array( 'label' => esc_html__( 'Logo Width', 'powerplus-toolkit' ), 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px', '%' ), 'selectors' => array( '{{WRAPPER}} .pkwt-auth-logo img' => 'width: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'logo_max_width', array( 'label' => esc_html__( 'Logo Max Width', 'powerplus-toolkit' ), 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px', '%' ), 'selectors' => array( '{{WRAPPER}} .pkwt-auth-logo img' => 'max-width: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'logo_height', array( 'label' => esc_html__( 'Logo Height', 'powerplus-toolkit' ), 'type' => Controls_Manager::SLIDER, 'size_units' => array( 'px', '%' ), 'selectors' => array( '{{WRAPPER}} .pkwt-auth-logo img' => 'height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'align', array( 'label' => esc_html__( 'Alignment', 'powerplus-toolkit' ), 'type' => Controls_Manager::CHOOSE, 'options' => array( 'left' => array( 'title' => esc_html__( 'Left', 'powerplus-toolkit' ), 'icon' => 'eicon-text-align-left' ), 'center' => array( 'title' => esc_html__( 'Center', 'powerplus-toolkit' ), 'icon' => 'eicon-text-align-center' ), 'right' => array( 'title' => esc_html__( 'Right', 'powerplus-toolkit' ), 'icon' => 'eicon-text-align-right' ) ), 'selectors' => array( '{{WRAPPER}} .pkwt-auth-logo' => 'text-align: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'logo_margin', array( 'label' => esc_html__( 'Margin', 'powerplus-toolkit' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'em', '%' ), 'selectors' => array( '{{WRAPPER}} .pkwt-auth-logo' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'logo_padding', array( 'label' => esc_html__( 'Padding', 'powerplus-toolkit' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', 'em', '%' ), 'selectors' => array( '{{WRAPPER}} .pkwt-auth-logo' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_group_control( Group_Control_Border::get_type(), array( 'name' => 'logo_border', 'selector' => '{{WRAPPER}} .pkwt-auth-logo img' ) );
		$this->add_responsive_control( 'logo_radius', array( 'label' => esc_html__( 'Border Radius', 'powerplus-toolkit' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', '%' ), 'selectors' => array( '{{WRAPPER}} .pkwt-auth-logo img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array( 'name' => 'logo_shadow', 'selector' => '{{WRAPPER}} .pkwt-auth-logo img' ) );
		$this->add_group_control( Group_Control_Css_Filter::get_type(), array( 'name' => 'logo_css_filters', 'selector' => '{{WRAPPER}} .pkwt-auth-logo img' ) );
		$this->add_control( 'logo_opacity', array( 'label' => esc_html__( 'Opacity', 'powerplus-toolkit' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0.1, 'max' => 1, 'step' => 0.1 ) ), 'selectors' => array( '{{WRAPPER}} .pkwt-auth-logo img' => 'opacity: {{SIZE}};' ) ) );
		$this->add_control( 'logo_transition', array( 'label' => esc_html__( 'Transition (ms)', 'powerplus-toolkit' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 50, 'max' => 2000 ) ), 'selectors' => array( '{{WRAPPER}} .pkwt-auth-logo img' => 'transition-duration: {{SIZE}}ms;' ) ) );
		$this->add_control( 'logo_hover_opacity', array( 'label' => esc_html__( 'Hover Opacity', 'powerplus-toolkit' ), 'type' => Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0.1, 'max' => 1, 'step' => 0.1 ) ), 'selectors' => array( '{{WRAPPER}} .pkwt-auth-logo:hover img' => 'opacity: {{SIZE}};' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$logo_url = '';
		if ( 'custom' === $settings['source'] && ! empty( $settings['custom_logo']['url'] ) ) {
			$logo_url = $settings['custom_logo']['url'];
		} else {
			$logo_id  = get_theme_mod( 'custom_logo' );
			$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
		}
		if ( ! $logo_url ) {
			return;
		}
		$link = ! empty( $settings['link_url']['url'] ) ? $settings['link_url']['url'] : home_url( '/' );
		?>
		<div class="pkwt-auth-logo">
			<a href="<?php echo esc_url( $link ); ?>" <?php echo ! empty( $settings['new_tab'] ) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php esc_attr_e( 'Logo', 'powerplus-toolkit' ); ?>" />
			</a>
		</div>
		<?php
	}
}
