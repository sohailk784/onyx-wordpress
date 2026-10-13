<?php
/**
 * WooCommerce presentation tweaks to match the ONYX React storefront.
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/**
 * Render an Elementor library template by option name, so marketing sections around
 * WooCommerce archives stay editable in (free) Elementor. With Elementor Pro these can
 * move into Theme Builder "Products Archive" / "Single Product" templates.
 */
function onyx_render_elementor_template_option( $option ) {
	$id = (int) get_option( $option );
	if ( ! $id || ! did_action( 'elementor/loaded' ) ) {
		return;
	}
	echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $id, true ); // phpcs:ignore WordPress.Security.EscapeOutput
}

// Shop / category hero (Elementor template "Shop Hero").
add_action( 'woocommerce_before_main_content', function () {
	if ( is_shop() || is_product_taxonomy() ) {
		onyx_render_elementor_template_option( 'onyx_shop_hero_template' );
	}
}, 5 );

// Hide default archive title on shop (the hero carries it).
add_filter( 'woocommerce_show_page_title', function ( $show ) {
	return ( is_shop() && get_option( 'onyx_shop_hero_template' ) ) ? false : $show;
} );

// Grid: 4 columns, 12 per page like the React catalogue.
add_filter( 'loop_shop_columns', fn() => 4 );
add_filter( 'loop_shop_per_page', fn() => 12 );

// Product card: badge from product tags (BestSeller / New / Sale / Coming Soon).
add_action( 'woocommerce_before_shop_loop_item', function () {
	global $product;
	$badges = array_filter( array_map( 'trim', explode( ',', (string) $product->get_meta( '_onyx_badges' ) ) ) );
	if ( has_term( 'coming-soon', 'product_tag', $product->get_id() ) ) {
		$badges[] = 'coming soon';
	}
	if ( $badges ) {
		echo '<span class="onyx-loop-badges">';
		foreach ( $badges as $b ) {
			printf( '<span class="onyx-loop-badge onyx-loop-badge--%1$s">%2$s</span>', esc_attr( sanitize_title( $b ) ), esc_html( 'bestseller' === $b ? 'BestSeller' : ucwords( $b ) ) );
		}
		echo '</span>';
	}
	if ( shortcode_exists( 'ti_wishlists_addtowishlist' ) ) {
		echo '<span class="onyx-loop-wishlist">' . do_shortcode( '[ti_wishlists_addtowishlist loop=yes]' ) . '</span>';
	}
}, 5 );

// Wrap title+price in a body block.
add_action( 'woocommerce_shop_loop_item_title', fn() => print '<div class="onyx-loop-body">', 5 );
add_action( 'woocommerce_after_shop_loop_item_title', fn() => print '</div>', 20 );

// Replace "Add to cart" in the grid with "View Details" (variants are chosen on the product page).
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
add_action( 'woocommerce_after_shop_loop_item', function () {
	global $product;
	printf( '<a class="button onyx-view-details" href="%s">%s</a>', esc_url( $product->get_permalink() ), esc_html__( 'View Details', 'onyx-child' ) );
}, 10 );

// "From £x" for variable products.
add_filter( 'woocommerce_variable_price_html', function ( $price, $product ) {
	$min = $product->get_variation_price( 'min', true );
	$max = $product->get_variation_price( 'max', true );
	return $min !== $max ? sprintf( '<span class="onyx-from">%s</span> %s', esc_html__( 'From', 'onyx-child' ), wc_price( $min ) ) : wc_price( $min );
}, 10, 2 );

// Single product: research-use notice + trust list under the add-to-cart form.
add_action( 'woocommerce_single_product_summary', function () {
	echo '<ul class="onyx-trust-list"><li>Research grade · ≥99% purity (HPLC)</li><li>Third-party verified · unique batch code</li><li>Discreet EU dispatch · tracked on every order</li></ul>';
	echo '<div class="onyx-ruo"><strong>For research use only.</strong> Not approved for human consumption, food, veterinary, or drug use. By purchasing you confirm you are a qualified researcher.</div>';
}, 35 );

// Description tab title.
add_filter( 'woocommerce_product_description_heading', fn() => '' );
add_filter( 'woocommerce_product_tabs', function ( $tabs ) {
	if ( isset( $tabs['description'] ) ) {
		$tabs['description']['title'] = __( 'Product Information', 'onyx-child' );
	}
	unset( $tabs['reviews'] );
	return $tabs;
}, 98 );

// Related products: 4 in a row.
add_filter( 'woocommerce_output_related_products_args', function ( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
} );
