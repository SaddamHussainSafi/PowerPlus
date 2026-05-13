<?php
/**
 * WooCommerce duplication helpers.
 *
 * @package PKWT
 */

namespace PKWT\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_DPP_WooCommerce {

	/**
	 * Check if source is a WooCommerce product.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return bool
	 */
	public function is_product_post( int $post_id ): bool {
		return 'product' === get_post_type( $post_id ) && function_exists( 'wc_get_product' );
	}

	/**
	 * Process copied WooCommerce product data.
	 *
	 * @param int $source_id Source product ID.
	 * @param int $target_id Target product ID.
	 *
	 * @return void
	 */
	public function process_product_duplicate( int $source_id, int $target_id ): void {
		if ( ! $this->is_product_post( $source_id ) ) {
			return;
		}

		update_post_meta( $target_id, '_sku', '' );

		$product = wc_get_product( $source_id );
		if ( ! $product || ! $product->is_type( 'variable' ) ) {
			return;
		}

		$this->duplicate_variations( $source_id, $target_id );
	}

	/**
	 * Duplicate variation posts.
	 *
	 * @param int $source_id Source product ID.
	 * @param int $target_id Target product ID.
	 *
	 * @return void
	 */
	private function duplicate_variations( int $source_id, int $target_id ): void {
		$variations = get_posts(
			array(
				'post_type'      => 'product_variation',
				'post_parent'    => $source_id,
				'post_status'    => array( 'publish', 'private', 'draft' ),
				'posts_per_page' => -1,
			)
		);

		foreach ( $variations as $variation ) {
			$new_variation_id = wp_insert_post(
				array(
					'post_type'   => 'product_variation',
					'post_status' => 'draft',
					'post_parent' => $target_id,
					'menu_order'  => (int) $variation->menu_order,
				)
			);

			if ( is_wp_error( $new_variation_id ) ) {
				continue;
			}

			$meta = get_post_meta( $variation->ID );
			foreach ( $meta as $meta_key => $values ) {
				if ( in_array( $meta_key, array( '_sku', '_edit_lock', '_edit_last' ), true ) ) {
					continue;
				}

				foreach ( (array) $values as $value ) {
					add_post_meta( $new_variation_id, $meta_key, maybe_unserialize( $value ) );
				}
			}

			update_post_meta( $new_variation_id, '_sku', '' );
		}
	}
}
