<?php
/**
 * Auth page manager.
 *
 * @package PKWT
 */

namespace PKWT\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_Page_Manager {

	/**
	 * Auth page types.
	 *
	 * @var string[]
	 */
	private $auth_page_types = array( 'login', 'register', 'lost_password', 'reset_password' );

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'template_redirect', array( $this, 'send_no_cache_headers' ) );
		add_filter( 'the_generator', array( $this, 'strip_generator_on_auth_pages' ) );
	}

	/**
	 * Ensure default auth pages.
	 *
	 * @return array<string,int>
	 */
	public function ensure_default_pages(): array {
		$map = array(
			'login'          => array( 'slug' => 'pkwt-login', 'title' => __( 'Login', 'powerplus-toolkit' ) ),
			'register'       => array( 'slug' => 'pkwt-register', 'title' => __( 'Register', 'powerplus-toolkit' ) ),
			'lost_password'  => array( 'slug' => 'pkwt-lost-password', 'title' => __( 'Lost Password', 'powerplus-toolkit' ) ),
			'reset_password' => array( 'slug' => 'pkwt-reset-password', 'title' => __( 'Reset Password', 'powerplus-toolkit' ) ),
		);

		$ids = array();

		foreach ( $map as $type => $item ) {
			$page = $this->find_page_by_type( $type );
			if ( $page && 'publish' === get_post_status( $page->ID ) ) {
				$ids[ $type ] = (int) $page->ID;
				continue;
			}

			$post_id = wp_insert_post(
				array(
					'post_title'   => $item['title'],
					'post_name'    => sanitize_title( $item['slug'] ),
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_content' => sprintf(
						'<!-- wp:paragraph --><p>%s</p><!-- /wp:paragraph -->',
						esc_html__( 'Edit this page with Elementor and add PowerPlus widgets.', 'powerplus-toolkit' )
					),
				)
			);

			if ( ! is_wp_error( $post_id ) && $post_id > 0 ) {
				update_post_meta( $post_id, '_pkwt_page_type', $type );
				$ids[ $type ] = (int) $post_id;
			}
		}

		return $ids;
	}

	/**
	 * Find page by _pkwt_page_type.
	 *
	 * @param string $type Page type.
	 *
	 * @return \WP_Post|null
	 */
	public function find_page_by_type( string $type ) {
		$posts = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => array( 'publish', 'draft', 'private' ),
				'posts_per_page' => 1,
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Required point lookup for plugin-owned meta key.
				'meta_key'       => '_pkwt_page_type',
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Required point lookup for plugin-owned meta value.
				'meta_value'     => $type,
			)
		);

		return ! empty( $posts ) ? $posts[0] : null;
	}

	/**
	 * Get page URL by settings key.
	 *
	 * @param string $setting_key Option key.
	 *
	 * @return string
	 */
	public function get_page_url_by_setting( string $setting_key ): string {
		$settings = ( new Class_PKWT_Settings_Repository() )->get();
		$page_id  = isset( $settings[ $setting_key ] ) ? absint( $settings[ $setting_key ] ) : 0;

		if ( $page_id <= 0 ) {
			return '';
		}

		$url = get_permalink( $page_id );
		if ( ! $url ) {
			return '';
		}

		if ( has_filter( 'wpml_permalink' ) ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- wpml_permalink and wpml_current_language are WPML plugin hooks, not defined by this plugin.
			$url = (string) apply_filters( 'wpml_permalink', $url, apply_filters( 'wpml_current_language', null ) );
		}

		return $url;
	}

	/**
	 * No cache headers for auth pages.
	 *
	 * @return void
	 */
	public function send_no_cache_headers(): void {
		if ( ! $this->is_current_auth_page() ) {
			return;
		}

		nocache_headers();
		header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0', true );

		// Do NOT send X-Frame-Options or CSP frame-ancestors when Elementor (or any
		// page builder) is editing the page — they load the page inside an iframe,
		// and blocking that breaks the editor preview.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only query arg checks for editor detection only.
		$is_elementor_edit = (
			isset( $_GET['elementor-preview'] )
			|| isset( $_GET['preview_id'] )
			|| isset( $_GET['preview_nonce'] )
			|| ( isset( $_GET['preview'] ) && isset( $_GET['p'] ) )
			|| ( isset( $_GET['action'] ) && 'elementor' === sanitize_key( wp_unslash( $_GET['action'] ) ) )
			|| ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->preview ) && \Elementor\Plugin::$instance->preview->is_preview_mode() )
			|| ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->editor ) && \Elementor\Plugin::$instance->editor->is_edit_mode() )
		);
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( ! $is_elementor_edit ) {
			header( 'X-Frame-Options: SAMEORIGIN', true );
			header( 'Content-Security-Policy: frame-ancestors \'self\'', true );
		}

		header( 'Referrer-Policy: strict-origin-when-cross-origin', true );
	}

	/**
	 * Does page have content.
	 *
	 * @param int $page_id Page id.
	 *
	 * @return bool
	 */
	public function has_renderable_content( int $page_id ): bool {
		$page = get_post( $page_id );
		if ( ! $page || 'publish' !== $page->post_status ) {
			return false;
		}

		$content = trim( (string) $page->post_content );
		if ( '' !== $content ) {
			return true;
		}

		$elementor_data = get_post_meta( $page_id, '_elementor_data', true );
		return ! empty( $elementor_data );
	}

	/**
	 * Is current request one of PKWT auth pages.
	 *
	 * @return bool
	 */
	public function is_current_auth_page(): bool {
		if ( ! is_page() ) {
			return false;
		}

		$post = get_post();
		if ( ! $post ) {
			return false;
		}

		$type = get_post_meta( $post->ID, '_pkwt_page_type', true );
		return in_array( $type, $this->auth_page_types, true );
	}

	/**
	 * Remove generator value on auth pages.
	 *
	 * @param string $generator Generator tag.
	 *
	 * @return string
	 */
	public function strip_generator_on_auth_pages( string $generator ): string {
		return $this->is_current_auth_page() ? '' : $generator;
	}
}
