
// Home V2 (deploy21): small uppercase category label under the product title in loop cards on the home-v2 page only.
add_action( 'woocommerce_after_shop_loop_item_title', function () {
	if ( ! is_page( 'home-v2' ) ) {
		return;
	}
	$terms = get_the_terms( get_the_ID(), 'product_cat' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		echo '<span class="hv2-b2-cat">' . esc_html( $terms[0]->name ) . '</span>';
	}
}, 7 );
