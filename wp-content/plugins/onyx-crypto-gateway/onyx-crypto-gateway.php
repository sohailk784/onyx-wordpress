<?php
/**
 * Plugin Name:          ONYX Crypto Payments (USDT/USDC)
 * Description:          Offline WooCommerce gateway for USDT/USDC transfers. Shows the wallet address + QR after checkout, lets the customer upload payment proof, and keeps the order on-hold until staff verify the transfer.
 * Version:              1.0.0
 * Requires at least:    6.4
 * Requires PHP:         8.1
 * Requires Plugins:     woocommerce
 * WC requires at least: 8.0
 * Author:               ONYX Research
 * License:              Proprietary
 * Text Domain:          onyx-crypto
 *
 * @package onyx-crypto-gateway
 */

defined( 'ABSPATH' ) || exit;

define( 'ONYX_CRYPTO_VERSION', '1.0.0' );
define( 'ONYX_CRYPTO_URL', plugin_dir_url( __FILE__ ) );

add_action( 'before_woocommerce_init', function () {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

add_action( 'plugins_loaded', function () {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}
	require_once __DIR__ . '/includes/class-onyx-crypto-gateway.php';
	require_once __DIR__ . '/includes/class-onyx-payment-proof.php';
	Onyx_Payment_Proof::init();
	add_filter( 'woocommerce_payment_gateways', function ( $gateways ) {
		$gateways[] = 'Onyx_Crypto_Gateway';
		return $gateways;
	} );
} );
