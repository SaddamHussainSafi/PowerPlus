<?php
/**
 * Core duplication logic.
 *
 * @package PKWT
 */

namespace PKWT\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_DPP_Duplicator {

	/**
	 * Settings option key.
	 *
	 * @var string
	 */
	private $option_key = 'pkwt_dpp_settings';

	/**
	 * Get module settings.
	 *
	 * @return array<string,mixed>
	 */
	public function get_settings(): array {
		$defaults = array(
			'enabled'                 => 1,
			'enabled_post_types'      => array(),
			'title_suffix'            => '(Copy)',
			'copy_author'             => 'current',
			'enable_elementor_button' => 1,
			'enable_row_action'       => 1,
		);
		$settings = get_option( $this->option_key, array() );

		return wp_parse_args( is_array( $settings ) ? $settings : array(), $defaults );
	}

	/**
	 * Check if duplicator is globally enabled.
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		$settings = $this->get_settings();
		return ! empty( $settings['enabled'] );
	}

	/**
	 * Check if a post type is enabled for duplication.
	 *
	 * @param string $post_type Post type key.
	 *
	 * @return bool
	 */
	public function is_post_type_enabled( string $post_type ): bool {
		$settings      = $this->get_settings();
		$enabled_types = isset( $settings['enabled_post_types'] ) && is_array( $settings['enabled_post_types'] ) ? $settings['enabled_post_types'] : array();

		// Empty selection means "all UI post types enabled".
		if ( empty( $enabled_types ) ) {
			return true;
		}

		return in_array( $post_type, $enabled_types, true );
	}

	/**
	 * Duplicate post.
	 *
	 * @param int $post_id Source post ID.
	 *
	 * @return int|\WP_Error
	 */
	public function duplicate_post( int $post_id ) {
		$source = get_post( $post_id );
		if ( ! $source ) {
			return new \WP_Error( 'dpp_source_missing', __( 'Source post not found.', 'powerkit-powerful-tools-for-your-website' ) );
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return new \WP_Error( 'dpp_permission_denied', __( 'You do not have permission to duplicate this post.', 'powerkit-powerful-tools-for-your-website' ) );
		}

		if ( ! $this->is_enabled() ) {
			return new \WP_Error( 'dpp_disabled', __( 'Post duplicator is disabled.', 'powerkit-powerful-tools-for-your-website' ) );
		}

		if ( ! $this->is_post_type_enabled( $source->post_type ) ) {
			return new \WP_Error( 'dpp_type_disabled', __( 'Duplication is disabled for this post type.', 'powerkit-powerful-tools-for-your-website' ) );
		}

		$settings    = $this->get_settings();
		$suffix      = isset( $settings['title_suffix'] ) ? sanitize_text_field( (string) $settings['title_suffix'] ) : '(Copy)';
		$author_mode = isset( $settings['copy_author'] ) ? sanitize_key( (string) $settings['copy_author'] ) : 'current';
		$author_id   = ( 'original' === $author_mode ) ? (int) $source->post_author : get_current_user_id();
		$title       = '' === $suffix ? $source->post_title : trim( $source->post_title . ' ' . $suffix );

		$new_post_id = wp_insert_post(
			array(
				'post_type'      => $source->post_type,
				'post_status'    => 'draft',
				'post_title'     => $title,
				'post_content'   => $source->post_content,
				'post_excerpt'   => $source->post_excerpt,
				'post_author'    => $author_id,
				'post_parent'    => (int) $source->post_parent,
				'menu_order'     => (int) $source->menu_order,
				'ping_status'    => $source->ping_status,
				'comment_status' => $source->comment_status,
			)
		);

		if ( is_wp_error( $new_post_id ) ) {
			return $new_post_id;
		}

		$this->copy_taxonomies( $post_id, $new_post_id, $source->post_type );
		$this->copy_post_meta( $post_id, $new_post_id );
		$this->copy_featured_image( $post_id, $new_post_id );

		return (int) $new_post_id;
	}

	/**
	 * Copy taxonomies.
	 *
	 * @param int    $source_id Source ID.
	 * @param int    $target_id Target ID.
	 * @param string $post_type Post type.
	 *
	 * @return void
	 */
	private function copy_taxonomies( int $source_id, int $target_id, string $post_type ): void {
		$taxonomies = get_object_taxonomies( $post_type, 'names' );
		foreach ( $taxonomies as $taxonomy ) {
			$term_ids = wp_get_object_terms( $source_id, $taxonomy, array( 'fields' => 'ids' ) );
			if ( is_wp_error( $term_ids ) ) {
				continue;
			}
			wp_set_object_terms( $target_id, $term_ids, $taxonomy );
		}
	}

	/**
	 * Copy post meta.
	 *
	 * @param int $source_id Source ID.
	 * @param int $target_id Target ID.
	 *
	 * @return void
	 */
	private function copy_post_meta( int $source_id, int $target_id ): void {
		$meta = get_post_meta( $source_id );
		$skip = array(
			'_edit_lock',
			'_edit_last',
			'_wp_old_slug',
			'_wp_trash_meta_status',
			'_wp_trash_meta_time',
			'_wc_average_rating',
			'_wc_rating_count',
			'_wc_review_count',
			'_sku',
		);

		foreach ( $meta as $meta_key => $values ) {
			if ( in_array( $meta_key, $skip, true ) ) {
				continue;
			}

			foreach ( (array) $values as $value ) {
				add_post_meta( $target_id, $meta_key, maybe_unserialize( $value ) );
			}
		}
	}

	/**
	 * Copy featured image.
	 *
	 * @param int $source_id Source ID.
	 * @param int $target_id Target ID.
	 *
	 * @return void
	 */
	private function copy_featured_image( int $source_id, int $target_id ): void {
		$thumb_id = get_post_thumbnail_id( $source_id );
		if ( $thumb_id ) {
			set_post_thumbnail( $target_id, $thumb_id );
		}
	}
}
