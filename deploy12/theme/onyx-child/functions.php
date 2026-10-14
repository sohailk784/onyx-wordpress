<?php
/**
 * ONYX Research child theme bootstrap.
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;

define( 'ONYX_CHILD_VERSION', '1.4.1' );

add_action( 'wp_enqueue_scripts', function () {
	$dir = get_stylesheet_directory_uri();
	wp_enqueue_style( 'onyx-tokens', $dir . '/assets/css/tokens.css', array(), ONYX_CHILD_VERSION );
	wp_enqueue_style( 'onyx-base', $dir . '/assets/css/base.css', array( 'onyx-tokens', 'hello-elementor' ), ONYX_CHILD_VERSION );
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style( 'onyx-woocommerce', $dir . '/assets/css/woocommerce.css', array( 'onyx-base' ), ONYX_CHILD_VERSION );
	}
	// Compiled Tailwind stylesheet of the React storefront, scoped to .onyx-tw wrappers.
	wp_enqueue_style( 'onyx-app', $dir . '/assets/css/onyx-app.css', array( 'onyx-base' ), ONYX_CHILD_VERSION );
	wp_enqueue_style( 'onyx-elementor-skin', $dir . '/assets/css/elementor-skin.css', array( 'onyx-base' ), ONYX_CHILD_VERSION );
	wp_enqueue_style( 'onyx-tw-fixes', $dir . '/assets/css/onyx-tw-fixes.css', array( 'onyx-app' ), ONYX_CHILD_VERSION );
	wp_enqueue_style( 'onyx-fluent-skin', $dir . '/assets/css/fluent-skin.css', array(), ONYX_CHILD_VERSION );
	if ( function_exists( 'is_product' ) && is_product() ) {
		wp_enqueue_script( 'onyx-product', $dir . '/assets/js/onyx-product.js', array(), ONYX_CHILD_VERSION, true );
	}
	// Home V2 (Elementor page, slug home-v2): section styling hooked to hv2-* CSS classes.
	if ( is_page( 'home-v2' ) ) {
		wp_enqueue_style( 'onyx-home-v2', $dir . '/assets/css/home-v2.css', array( 'onyx-tw-fixes', 'onyx-fluent-skin' ), ONYX_CHILD_VERSION . '.' . filemtime( get_stylesheet_directory() . '/assets/css/home-v2.css' ) );
	}
	wp_enqueue_script( 'onyx-theme', $dir . '/assets/js/theme.js', array(), ONYX_CHILD_VERSION, true );
}, 20 );

add_action( 'after_setup_theme', function () {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
	register_nav_menus(
		array(
			'primary'  => __( 'Primary navigation', 'onyx-child' ),
			'help'     => __( 'Footer: Help', 'onyx-child' ),
			'policies' => __( 'Footer: Policies', 'onyx-child' ),
		)
	);
} );

// Favicon fallback when no Site Icon is set.
add_action( 'wp_head', function () {
	if ( ! has_site_icon() ) {
		printf( '<link rel="icon" href="%s" type="image/png">', esc_url( get_stylesheet_directory_uri() . '/assets/img/favicon.png' ) );
	}
} );

require_once __DIR__ . '/inc/ui.php';
require_once __DIR__ . '/inc/woocommerce.php';
require_once __DIR__ . '/inc/checkout.php';
require_once __DIR__ . '/inc/account.php';
