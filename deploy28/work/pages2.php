<?php
/**
 * deploy28 – "<Title> 2" pages (slug <slug>-2, marker meta _onyx_v2_source) in the Home V2 / Peptides 2 premium style.
 * Style only: content is the original page's content; CSS is scoped to body.onyx-v2.
 * WooCommerce settings are NOT changed. A "v2 context" (a -2 page, or ?v2=1 on product / order-received / account URLs)
 * routes the shop links (cart, checkout, account, product links, thank-you) to the -2 versions, so a visitor who starts on a
 * -2 page stays in the v2 flow, while everybody else sees the originals exactly as before.
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;

/** Slug of the original page if the current request is a deploy28 "2" page. */
function onyx_v2_source() {
	static $src = null;
	if ( null === $src ) {
		$src = '';
		if ( is_page() ) {
			$src = (string) get_post_meta( get_queried_object_id(), '_onyx_v2_source', true );
		}
	}
	return $src;
}

/** True on any v2 surface: a -2 page, Peptides 2, or a product / endpoint opened with ?v2=1 from one. */
function onyx_v2_context() {
	if ( did_action( 'wp' ) && ( '' !== onyx_v2_source() || is_page( 'peptides-2' ) ) ) {
		return true;
	}
	if ( isset( $_GET['v2'] ) && '1' === $_GET['v2'] ) { // phpcs:ignore WordPress.Security.NonceVerification
		return true;
	}
	if ( wp_doing_ajax() || ( isset( $_GET['wc-ajax'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$ref = wp_get_referer();
		return $ref && preg_match( '#/(cart|checkout|my-account|wishlist)-2/|[?&]v2=1#', $ref );
	}
	return false;
}

/** URL of the -2 copy of an original page slug (falls back to the original if the copy does not exist). */
function onyx_v2_url( $slug, $fallback ) {
	$p = get_page_by_path( $slug . '-2' );
	return $p ? get_permalink( $p ) : $fallback;
}

add_filter( 'body_class', function ( $c ) {
	$is_v2 = '' !== onyx_v2_source() || ( onyx_v2_context() && ( is_product() || is_wc_endpoint_url() ) );
	if ( $is_v2 ) {
		$c[] = 'onyx-v2';
		$c[] = 'onyx-v2-' . sanitize_html_class( '' !== onyx_v2_source() ? onyx_v2_source() : ( is_product() ? 'product' : 'endpoint' ) );
	}
	return $c;
} );

/* Link routing inside the v2 flow only. */
add_filter( 'woocommerce_get_cart_url', function ( $u ) {
	return onyx_v2_context() ? onyx_v2_url( 'cart', $u ) : $u;
} );
add_filter( 'woocommerce_get_checkout_url', function ( $u ) {
	return onyx_v2_context() ? onyx_v2_url( 'checkout', $u ) : $u;
} );
add_filter( 'woocommerce_get_myaccount_page_permalink', function ( $u ) {
	return onyx_v2_context() ? onyx_v2_url( 'my-account', $u ) : $u;
} );
add_filter( 'post_type_link', function ( $u, $post ) {
	if ( 'product' === $post->post_type && ! is_admin() && onyx_v2_context() ) {
		$u = add_query_arg( 'v2', '1', $u );
	}
	return $u;
}, 10, 2 );

/* Thank-you: orders placed from Checkout 2 land on /checkout-2/order-received/… (same Woo endpoint, styled). */
add_action( 'woocommerce_checkout_after_customer_details', function () {
	if ( onyx_v2_context() ) {
		echo '<input type="hidden" name="onyx_v2" value="1">';
	}
} );
add_action( 'woocommerce_checkout_create_order', function ( $order ) {
	if ( isset( $_POST['onyx_v2'] ) && '1' === $_POST['onyx_v2'] ) { // phpcs:ignore WordPress.Security.NonceVerification
		$order->update_meta_data( '_onyx_v2', '1' );
	}
} );
add_filter( 'woocommerce_get_checkout_order_received_url', function ( $u, $order ) {
	if ( $order && '1' === $order->get_meta( '_onyx_v2' ) ) {
		$p = get_page_by_path( 'checkout-2' );
		if ( $p ) {
			$u = add_query_arg( 'key', $order->get_order_key(), trailingslashit( trailingslashit( get_permalink( $p ) ) . 'order-received/' . $order->get_id() ) );
		}
	}
	return $u;
}, 10, 2 );

/* Assets: fonts + pages2.css on v2 surfaces only. */
add_action( 'wp_enqueue_scripts', function () {
	$is_v2 = '' !== onyx_v2_source() || ( onyx_v2_context() && ( is_product() || is_wc_endpoint_url() ) );
	if ( ! $is_v2 ) {
		return;
	}
	$d = get_stylesheet_directory();
	$u = get_stylesheet_directory_uri();
	wp_enqueue_style( 'onyx-home-v2-fonts', $u . '/assets/css/onyx-fonts.css', array(), ONYX_CHILD_VERSION . '.' . filemtime( $d . '/assets/css/onyx-fonts.css' ) );
	wp_enqueue_style( 'onyx-pages2', $u . '/assets/css/pages2.css', array( 'onyx-tw-fixes', 'onyx-fluent-skin' ), ONYX_CHILD_VERSION . '.' . filemtime( $d . '/assets/css/pages2.css' ) );
}, 30 );

/* Elementor sections keep their own per-element background colours; tag them once so pages2.css can restyle them as cards/tints/icon chips. */
add_action( 'wp_footer', function () {
	if ( '' === onyx_v2_source() ) {
		return;
	}
	?>
	<script id="onyx-v2-tag">(function(){var r=document.querySelector('.elementor[data-elementor-type="wp-page"]');if(!r)return;
	r.querySelectorAll('.e-con .e-con, .e-con .elementor-widget-text-editor').forEach(function(el){var cs=getComputedStyle(el),bg=cs.backgroundColor,m=bg.match(/\d+(\.\d+)?/g);
	if(/gradient/.test(cs.backgroundImage)){var g=(cs.backgroundImage.match(/rgba?\([^)]*\)/)||[''])[0].match(/\d+/g);if(g&&(+g[0]+ +g[1]+ +g[2])/3<120){el.classList.add('onyx-v2-tint','onyx-v2-dark');return}}
	if(!m||(m[3]!==undefined&&+m[3]===0))return;
	var w=el.offsetWidth,h=el.offsetHeight,lum=(+m[0]+ +m[1]+ +m[2])/3,blue=(+m[2])-(+m[0]);
	if(w<=72&&h<=72){el.classList.add('onyx-v2-icon');return}
	if(el.classList.contains('e-grid'))return;
	if(lum>=250)el.classList.add('onyx-v2-card');else if(lum>200)el.classList.add('onyx-v2-tint');else if(lum<120)el.classList.add('onyx-v2-tint','onyx-v2-dark');else if(blue>40)el.classList.add('onyx-v2-tint');});
	r.querySelectorAll('.onyx-v2-card').forEach(function(el){var w=el.querySelectorAll('.elementor-widget');if(w.length===2&&w[0].textContent.trim().length<=8&&!el.querySelector('img,svg,.elementor-button'))el.classList.add('onyx-v2-stat')});
	r.querySelectorAll('.elementor-heading-title').forEach(function(h){if(h.textContent.trim().length<=18)h.style.whiteSpace='nowrap'});
	[].forEach.call(r.children,function(el){if(parseFloat(getComputedStyle(el).paddingLeft)<12)el.classList.add('onyx-v2-pad')});})();</script>
	<?php
}, 50 );

/* B2B Login 2 and Wishlist 2 get the same account-page treatment as their originals (theme keys those on the original slug / page id). */
function onyx_v2_account_like() {
	return in_array( onyx_v2_source(), array( 'b2b-login', 'wishlist' ), true );
}
add_filter( 'hello_elementor_page_title', function ( $show ) {
	return onyx_v2_account_like() ? false : $show;
}, 21 );
add_filter( 'body_class', function ( $c ) {
	if ( onyx_v2_account_like() ) {
		$c[] = 'onyx-cart-page';
		$c[] = 'onyx-account-page';
	}
	return $c;
} );
