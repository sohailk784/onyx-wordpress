<?php
/**
 * Wishlist (TI WooCommerce Wishlist) – port of React Wishlist.tsx (live onyxhealth.net/wishlist).
 * Items are shown with the storefront product card; the card's heart removes the item.
 *
 * @version 2.3.3
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;
wp_enqueue_script( 'tinvwl' );
$onyx_seen = array();
?>
<div class="tinv-wishlist woocommerce onyx-tw onyx-wishlist"><div class="mx-auto max-w-7xl px-4 py-10">
	<h1 class="mb-6 text-3xl font-black">My Wishlist</h1>
	<div class="grid grid-cols-2 gap-2 md:grid-cols-3 md:gap-4 lg:grid-cols-4">
	<?php
	foreach ( $products as $wl_product ) {
		if ( empty( $wl_product['data'] ) ) {
			continue;
		}
		$p = apply_filters( 'tinvwl_wishlist_item', $wl_product['data'] );
		if ( $p && $p->is_type( 'variation' ) ) {
			$p = wc_get_product( $p->get_parent_id() );
		}
		if ( ! $p || isset( $onyx_seen[ $p->get_id() ] ) ) {
			continue;
		}
		$onyx_seen[ $p->get_id() ] = 1;
		echo onyx_product_card( $p, 'thumb' ); // phpcs:ignore
	}
	?>
	</div>
</div></div>
