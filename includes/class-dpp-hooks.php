<?php
/**
 * DPP hooks coordinator.
 *
 * @package PKWT
 */

namespace PKWT\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_DPP_Hooks {

	/**
	 * Duplicator.
	 *
	 * @var Class_PKWT_DPP_Duplicator
	 */
	private $duplicator;

	/**
	 * Elementor helper.
	 *
	 * @var Class_PKWT_DPP_Elementor
	 */
	private $elementor;

	/**
	 * WooCommerce helper.
	 *
	 * @var Class_PKWT_DPP_WooCommerce
	 */
	private $woocommerce;

	/**
	 * Admin helper.
	 *
	 * @var Class_PKWT_DPP_Admin
	 */
	private $admin;

	/**
	 * SVG feature.
	 *
	 * @var Class_PKWT_DPP_SVG
	 */
	private $svg;

	/**
	 * Ghost mode feature.
	 *
	 * @var Class_PKWT_DPP_Ghost
	 */
	private $ghost;

	/**
	 * Classic editor feature.
	 *
	 * @var Class_PKWT_DPP_Classic
	 */
	private $classic;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->duplicator  = new Class_PKWT_DPP_Duplicator();
		$this->elementor   = new Class_PKWT_DPP_Elementor();
		$this->woocommerce = new Class_PKWT_DPP_WooCommerce();
		$this->admin       = new Class_PKWT_DPP_Admin();
		$this->svg         = new Class_PKWT_DPP_SVG();
		$this->ghost       = new Class_PKWT_DPP_Ghost();
		$this->classic     = new Class_PKWT_DPP_Classic();
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->admin->register();
		$this->svg->register();
		$this->ghost->register();
		$this->classic->register();

		$post_types = get_post_types(
			array(
				'show_ui' => true,
			),
			'names'
		);
		foreach ( $post_types as $post_type ) {
			add_filter( $post_type . '_row_actions', array( $this, 'add_duplicate_row_action' ), 10, 2 );
		}

		add_action( 'admin_action_dpp_duplicate_post', array( $this, 'handle_duplicate_request' ) );
	}

	/**
	 * Add duplicate action link.
	 *
	 * @param array<string,string> $actions Existing actions.
	 * @param \WP_Post             $post    Current post.
	 *
	 * @return array<string,string>
	 */
	public function add_duplicate_row_action( array $actions, \WP_Post $post ): array {
		$settings = $this->duplicator->get_settings();
		if ( ! $this->duplicator->is_enabled() || empty( $settings['enable_row_action'] ) ) {
			return $actions;
		}

		if ( ! $this->duplicator->is_post_type_enabled( $post->post_type ) ) {
			return $actions;
		}

		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return $actions;
		}

		$url = wp_nonce_url( admin_url( 'admin.php?action=dpp_duplicate_post&post=' . $post->ID ), 'pkwt_dpp_duplicate_' . $post->ID );
		$actions['dpp_duplicate'] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Duplicate', 'powerplus-toolkit' ) . '</a>';

		return $actions;
	}

	/**
	 * Handle duplicate action request.
	 *
	 * @return void
	 */
	public function handle_duplicate_request(): void {
		if ( ! $this->duplicator->is_enabled() ) {
			wp_die( esc_html__( 'Post duplicator is currently disabled.', 'powerplus-toolkit' ) );
		}

		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
		if ( $post_id <= 0 ) {
			wp_die( esc_html__( 'Invalid post ID.', 'powerplus-toolkit' ) );
		}

		$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'pkwt_dpp_duplicate_' . $post_id ) ) {
			wp_die( esc_html__( 'Security check failed.', 'powerplus-toolkit' ) );
		}

		$result = $this->duplicator->duplicate_post( $post_id );
		if ( is_wp_error( $result ) ) {
			wp_die( esc_html( $result->get_error_message() ) );
		}

		$new_post_id = (int) $result;
		if ( $this->elementor->is_elementor_post( $post_id ) ) {
			$this->elementor->regenerate_elementor_data( $new_post_id );
		}

		if ( $this->woocommerce->is_product_post( $post_id ) ) {
			$this->woocommerce->process_product_duplicate( $post_id, $new_post_id );
		}

		$post_type = get_post_type( $post_id );
		$redirect  = admin_url( 'edit.php' );
		if ( $post_type && 'post' !== $post_type ) {
			$redirect = add_query_arg( 'post_type', $post_type, $redirect );
		}

		$redirect = add_query_arg(
			array(
				'dpp_duplicated'   => '1',
				'dpp_new_post_id'  => $new_post_id,
			),
			$redirect
		);

		wp_safe_redirect( $redirect );
		exit;
	}
}
