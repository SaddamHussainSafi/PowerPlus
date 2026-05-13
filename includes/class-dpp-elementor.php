<?php
/**
 * Elementor duplication helpers.
 *
 * @package PKWT
 */

namespace PKWT\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_DPP_Elementor {

	/**
	 * Check if source post uses Elementor.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return bool
	 */
	public function is_elementor_post( int $post_id ): bool {
		$edit_mode = get_post_meta( $post_id, '_elementor_edit_mode', true );
		$data      = get_post_meta( $post_id, '_elementor_data', true );

		return ! empty( $edit_mode ) || ! empty( $data );
	}

	/**
	 * Regenerate element IDs in copied Elementor data.
	 *
	 * @param int $target_post_id Target post ID.
	 *
	 * @return void
	 */
	public function regenerate_elementor_data( int $target_post_id ): void {
		$raw = get_post_meta( $target_post_id, '_elementor_data', true );
		if ( empty( $raw ) || ! is_string( $raw ) ) {
			return;
		}

		$data = json_decode( $raw, true );
		if ( ! is_array( $data ) ) {
			return;
		}

		$data = $this->regenerate_elements_recursive( $data );
		update_post_meta( $target_post_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );

		delete_post_meta( $target_post_id, '_elementor_css' );
		if ( class_exists( '\\Elementor\\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
	}

	/**
	 * Recursively regenerate element IDs.
	 *
	 * @param array<int|string,mixed> $elements Elements.
	 *
	 * @return array<int|string,mixed>
	 */
	private function regenerate_elements_recursive( array $elements ): array {
		foreach ( $elements as $index => $element ) {
			if ( ! is_array( $element ) ) {
				continue;
			}

			if ( isset( $element['id'] ) ) {
				$elements[ $index ]['id'] = substr( str_replace( '-', '', wp_generate_uuid4() ), 0, 8 );
			}

			if ( ! empty( $element['elements'] ) && is_array( $element['elements'] ) ) {
				$elements[ $index ]['elements'] = $this->regenerate_elements_recursive( $element['elements'] );
			}
		}

		return $elements;
	}
}
