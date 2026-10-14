<?php
/**
 * Checkout – port of the React pages/Checkout.tsx onto the classic WooCommerce checkout.
 * Fields keep WooCommerce names (billing_*), so validation, orders, emails and gateways work as usual.
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;

const ONYX_MIN_VIAL_SUBTOTAL = 150;

/** Checkout fields in the live order: email, names, address, apt, city, postcode, country, phone. */
add_filter( 'woocommerce_checkout_fields', function ( $fields ) {
	$keep  = array( 'billing_email', 'billing_first_name', 'billing_last_name', 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_postcode', 'billing_country', 'billing_phone' );
	$order = 10;
	$out   = array();
	foreach ( $keep as $k ) {
		if ( isset( $fields['billing'][ $k ] ) ) {
			$out[ $k ]             = $fields['billing'][ $k ];
			$out[ $k ]['priority'] = $order;
			$order                += 10;
		}
	}
	$out['billing_address_2']['required'] = false;
	$out['billing_phone']['required']     = true;
	$fields['billing']  = $out;
	$fields['shipping'] = array();
	$fields['order']    = array();
	return $fields;
}, 20 );
add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false' );
add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );
add_filter( 'woocommerce_checkout_registration_enabled', '__return_false' );
// Live default country is the UK.
add_filter( 'default_checkout_billing_country', function ( $c ) {
	return $c ? $c : 'GB';
} );
// No state/county field on live: never require it.
add_filter( 'woocommerce_get_country_locale', function ( $locale ) {
	foreach ( $locale as $cc => $f ) {
		$locale[ $cc ]['state']['required'] = false;
		$locale[ $cc ]['state']['hidden']   = true;
	}
	return $locale;
} );
add_filter( 'woocommerce_get_country_locale_default', function ( $f ) {
	$f['state']['required'] = false;
	return $f;
} );

/** Live shows two rates: Standard (Free above the threshold) and Express. Hide paid Standard when free applies. */
add_filter( 'woocommerce_package_rates', function ( $rates ) {
	$has_free = false;
	foreach ( $rates as $r ) {
		if ( 'free_shipping' === $r->get_method_id() ) {
			$has_free = true;
		}
	}
	if ( $has_free ) {
		foreach ( $rates as $id => $r ) {
			if ( 'flat_rate' === $r->get_method_id() && 0 === stripos( $r->get_label(), 'standard' ) ) {
				unset( $rates[ $id ] );
			}
		}
	}
	return $rates;
}, 100 );

/** "Standard (2-4 business days)" -> ['standard shipping', '2-4 business days']. */
function onyx_rate_parts( $rate ) {
	$label = $rate->get_label();
	$eta   = preg_match( '/\(([^)]+)\)/', $label, $m ) ? $m[1] : __( 'Calculated at checkout', 'onyx-child' );
	$kind  = stripos( $label, 'express' ) !== false ? 'express' : 'standard';
	return array( $kind . ' shipping', $eta );
}

function onyx_cart_has_vial() {
	foreach ( WC()->cart ? WC()->cart->get_cart() : array() as $item ) {
		if ( 'vial' === get_post_meta( $item['product_id'], '_onyx_type', true ) ) {
			return true;
		}
	}
	return false;
}
function onyx_vial_shortfall() {
	if ( ! onyx_cart_has_vial() ) {
		return 0;
	}
	$sub = (float) WC()->cart->get_subtotal();
	return $sub < ONYX_MIN_VIAL_SUBTOTAL ? ONYX_MIN_VIAL_SUBTOTAL - $sub : 0;
}

/** Section 3 – shipping method cards (refreshed by the update_order_review fragment). */
function onyx_checkout_shipping_html() {
	ob_start();
	echo '<div class="onyx-co-shipping space-y-2">';
	$packages = WC()->shipping() ? WC()->shipping()->get_packages() : array();
	$chosen   = WC()->session ? (array) WC()->session->get( 'chosen_shipping_methods' ) : array();
	$any      = false;
	foreach ( $packages as $i => $package ) {
		$current = $chosen[ $i ] ?? '';
		if ( ! isset( $package['rates'][ $current ] ) ) {
			$current = (string) key( $package['rates'] );
		}
		foreach ( $package['rates'] as $id => $rate ) {
			$any            = true;
			list( $t, $eta ) = onyx_rate_parts( $rate );
			$cost           = (float) $rate->get_cost();
			$on             = $id === $current;
			printf(
				'<label class="onyx-opt flex cursor-pointer items-center justify-between rounded-lg border p-4 transition %1$s"><div class="flex items-center gap-3"><input type="radio" name="shipping_method[%2$d]" data-index="%2$d" value="%3$s" class="shipping_method" %4$s><div><div class="text-sm font-semibold capitalize">%5$s</div><div class="text-xs text-muted-foreground">%6$s</div></div></div><div class="text-sm font-bold">%7$s</div></label>',
				$on ? 'border-accent bg-accent/5' : 'hover:bg-secondary',
				(int) $i,
				esc_attr( $id ),
				checked( $on, true, false ),
				esc_html( $t ),
				esc_html( $eta ),
				$cost <= 0 ? esc_html__( 'Free', 'onyx-child' ) : '£' . esc_html( number_format( $cost, 2 ) )
			);
		}
	}
	if ( ! $any ) {
		echo '<p class="text-sm text-muted-foreground">' . esc_html__( 'Enter your address to see shipping options.', 'onyx-child' ) . '</p>';
	}
	echo '</div>';
	return ob_get_clean();
}

/** Order summary totals + coupon state + vial minimum notice. */
function onyx_checkout_totals_html() {
	$cart = WC()->cart;
	ob_start();
	echo '<div class="onyx-co-totals space-y-4">';
	$coupons = $cart->get_applied_coupons();
	echo '<div class="onyx-co-coupon-msg">';
	foreach ( $coupons as $code ) {
		printf(
			'<p class="mt-1.5 text-xs font-medium text-emerald-700">"%1$s" applied: -%2$s <a href="%3$s" class="woocommerce-remove-coupon ml-1 underline" data-coupon="%4$s">%5$s</a></p>',
			esc_html( strtoupper( $code ) ),
			onyx_price( $cart->get_coupon_discount_amount( $code, $cart->display_cart_ex_tax ) ), // phpcs:ignore
			esc_url( add_query_arg( 'remove_coupon', rawurlencode( $code ), wc_get_checkout_url() ) ),
			esc_attr( $code ),
			esc_html__( 'Remove', 'onyx-child' )
		);
	}
	echo '</div>';
	$ship = (float) $cart->get_shipping_total();
	echo '<dl class="space-y-2 border-t pt-4 text-sm">';
	echo '<div class="flex justify-between"><dt>Subtotal</dt><dd>' . onyx_price( $cart->get_subtotal() ) . '</dd></div>'; // phpcs:ignore
	if ( $cart->get_discount_total() > 0 ) {
		echo '<div class="flex justify-between text-emerald-700"><dt>Discount</dt><dd>' . onyx_price( $cart->get_discount_total() ) . '</dd></div>'; // phpcs:ignore
	}
	echo '<div class="flex justify-between"><dt>Shipping</dt><dd>' . ( $ship <= 0 ? 'Free' : onyx_price( $ship ) ) . '</dd></div>'; // phpcs:ignore
	echo '<div class="mt-3 flex justify-between border-t pt-3 text-lg font-black"><dt>Total</dt><dd>' . onyx_price( $cart->get_total( 'edit' ) ) . '</dd></div>'; // phpcs:ignore
	echo '</dl>';
	$short = onyx_vial_shortfall();
	if ( $short > 0 ) {
		$sub = (float) $cart->get_subtotal();
		printf(
			'<div class="rounded-lg border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900"><p class="font-semibold">Minimum order for vial products is £150.00.</p><p class="mt-1">Your cart contains one or more peptide vials, so the minimum product order value is £150.00. Your current subtotal is £%1$s.</p><p class="mt-2">Please add £%2$s more to your cart to continue with this order, or remove all vial products if you would like to place an order below £150.00.</p><a href="%3$s" class="mt-2 inline-block font-semibold text-accent hover:underline">Continue shopping →</a></div>',
			esc_html( number_format( $sub, 2 ) ),
			esc_html( number_format( $short, 2 ) ),
			esc_url( wc_get_page_permalink( 'shop' ) )
		);
	}
	echo '</div>';
	return ob_get_clean();
}

add_filter( 'woocommerce_update_order_review_fragments', function ( $f ) {
	$f['.onyx-co-shipping'] = onyx_checkout_shipping_html();
	$f['.onyx-co-totals']   = onyx_checkout_totals_html();
	return $f;
} );

/** Live validation copy + research-use confirmation + vial minimum. */
add_action( 'woocommerce_after_checkout_validation', function ( $data, $errors ) {
	if ( empty( $_POST['onyx_terms'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$errors->add( 'onyx_terms', __( 'You must read and agree to the terms and conditions to place your order.', 'onyx-child' ) );
	}
	$short = onyx_vial_shortfall();
	if ( $short > 0 ) {
		$errors->add( 'onyx_vial', sprintf( 'Vial orders require a minimum product subtotal of £%s. Please add £%s more to your cart.', number_format( ONYX_MIN_VIAL_SUBTOTAL, 2 ), number_format( $short, 2 ) ) );
	}
}, 10, 2 );
add_filter( 'woocommerce_checkout_required_field_notice', function ( $notice, $label, $key ) {
	$map = array(
		'billing_email'      => 'Email is required.',
		'billing_first_name' => 'First name is required.',
		'billing_last_name'  => 'Last name is required.',
		'billing_address_1'  => 'Address is required.',
		'billing_city'       => 'City is required.',
		'billing_postcode'   => 'Postal code is required.',
		'billing_country'    => 'Please select a country.',
		'billing_phone'      => 'Phone number is required.',
	);
	return $map[ $key ] ?? $notice;
}, 10, 3 );
add_action( 'woocommerce_checkout_create_order', function ( $order ) {
	if ( ! empty( $_POST['onyx_terms'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$order->update_meta_data( '_onyx_research_use_confirmed', current_time( 'mysql' ) );
	}
} );

/** Checkout + order-received use the React layout: full-width wrapper, no Hello page padding. */
add_filter( 'body_class', function ( $c ) {
	if ( function_exists( 'is_checkout' ) && is_checkout() ) {
		$c[] = 'onyx-cart-page';
		$c[] = 'onyx-checkout-page';
	}
	return $c;
} );
// Coupon lives inside the order summary (see form-checkout.php).
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
// Thank-you: no WooCommerce order-details table (live shows only the confirmation + payment step).
add_action( 'init', function () {
	remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );
} );

add_action( 'wp_enqueue_scripts', function () {
	if ( function_exists( 'is_checkout' ) && is_checkout() ) {
		wp_enqueue_script( 'onyx-checkout', get_stylesheet_directory_uri() . '/assets/js/onyx-checkout.js', array( 'jquery', 'wc-checkout' ), ONYX_CHILD_VERSION, true );
	}
	if ( function_exists( 'is_order_received_page' ) && is_order_received_page() && defined( 'ONYX_CRYPTO_URL' ) ) {
		wp_enqueue_script( 'onyx-qrcode', ONYX_CRYPTO_URL . 'assets/qrcode.min.js', array(), ONYX_CHILD_VERSION, true );
	}
}, 20 );

/** Dial codes for the phone picker, from WooCommerce's own calling-code table. */
function onyx_dial_codes() {
	$out = array();
	foreach ( WC()->countries->get_allowed_countries() as $cc => $name ) {
		$code = WC()->countries->get_country_calling_code( $cc );
		if ( is_array( $code ) ) {
			$code = reset( $code );
		}
		if ( $code ) {
			$out[ $cc ] = array( html_entity_decode( $name ), $code );
		}
	}
	return $out;
}
