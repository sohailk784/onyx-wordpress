<?php
/**
 * ONYX Research child theme bootstrap.
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;

define( 'ONYX_CHILD_VERSION', '1.0.0' );

add_action( 'wp_enqueue_scripts', function () {
	$dir = get_stylesheet_directory_uri();
	wp_enqueue_style( 'onyx-tokens', $dir . '/assets/css/tokens.css', array(), ONYX_CHILD_VERSION );
	wp_enqueue_style( 'onyx-base', $dir . '/assets/css/base.css', array( 'onyx-tokens', 'hello-elementor' ), ONYX_CHILD_VERSION );
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style( 'onyx-woocommerce', $dir . '/assets/css/woocommerce.css', array( 'onyx-base' ), ONYX_CHILD_VERSION );
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

require_once __DIR__ . '/inc/woocommerce.php';
