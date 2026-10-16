<?php
/**
 * ONYX Research child theme bootstrap.
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;

define( 'ONYX_CHILD_VERSION', '1.4.1' );

/** Cache-busting version for theme assets: theme version + file mtime (deploy28l). */
function onyx_asset_ver( $rel ) {
	$f = get_stylesheet_directory() . $rel;
	return ONYX_CHILD_VERSION . ( is_readable( $f ) ? '.' . filemtime( $f ) : '' );
}

add_action( 'wp_enqueue_scripts', function () {
	$dir = get_stylesheet_directory_uri();
	wp_enqueue_style( 'onyx-tokens', $dir . '/assets/css/tokens.css', array(), onyx_asset_ver( '/assets/css/tokens.css' ) );
	wp_enqueue_style( 'onyx-base', $dir . '/assets/css/base.css', array( 'onyx-tokens', 'hello-elementor' ), onyx_asset_ver( '/assets/css/base.css' ) );
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style( 'onyx-woocommerce', $dir . '/assets/css/woocommerce.css', array( 'onyx-base' ), onyx_asset_ver( '/assets/css/woocommerce.css' ) );
	}
	// Compiled Tailwind stylesheet of the React storefront, scoped to .onyx-tw wrappers.
	wp_enqueue_style( 'onyx-app', $dir . '/assets/css/onyx-app.css', array( 'onyx-base' ), onyx_asset_ver( '/assets/css/onyx-app.css' ) );
	wp_enqueue_style( 'onyx-elementor-skin', $dir . '/assets/css/elementor-skin.css', array( 'onyx-base' ), onyx_asset_ver( '/assets/css/elementor-skin.css' ) );
	wp_enqueue_style( 'onyx-tw-fixes', $dir . '/assets/css/onyx-tw-fixes.css', array( 'onyx-app' ), onyx_asset_ver( '/assets/css/onyx-tw-fixes.css' ) );
	wp_enqueue_style( 'onyx-fluent-skin', $dir . '/assets/css/fluent-skin.css', array(), onyx_asset_ver( '/assets/css/fluent-skin.css' ) );
	if ( function_exists( 'is_product' ) && is_product() ) {
		wp_enqueue_script( 'onyx-product', $dir . '/assets/js/onyx-product.js', array(), onyx_asset_ver( '/assets/js/onyx-product.js' ), true );
	}
	// Home V2 (Elementor page, slug home-v2): section styling hooked to hv2-* CSS classes.
	if ( is_page( 'home-v2' ) ) {
		wp_enqueue_style( 'onyx-home-v2', $dir . '/assets/css/home-v2.css', array( 'onyx-tw-fixes', 'onyx-fluent-skin' ), onyx_asset_ver( '/assets/css/home-v2.css' ) . '.' . filemtime( get_stylesheet_directory() . '/assets/css/home-v2.css' ) );
		wp_enqueue_script( 'onyx-home-v2', $dir . '/assets/js/home-v2.js', array(), onyx_asset_ver( '/assets/js/home-v2.js' ) . '.' . filemtime( get_stylesheet_directory() . '/assets/js/home-v2.js' ), true );
	}
	wp_enqueue_script( 'onyx-theme', $dir . '/assets/js/theme.js', array(), onyx_asset_ver( '/assets/js/theme.js' ), true );
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

// Home V2 (deploy24): crisp product images in loop cards on the home-v2 page only (600px source + WP srcset instead of the 300px thumbnail).
add_filter( 'single_product_archive_thumbnail_size', function ( $size ) {
	return is_page( 'home-v2' ) ? 'woocommerce_single' : $size;
} );

// Home V2 (deploy26): self-hosted Cormorant Garamond (headings) + Hanken Grotesk (body), home-v2 only; font-display: swap.
add_action( 'wp_enqueue_scripts', function () {
	if ( is_page( 'home-v2' ) ) {
		$css = get_stylesheet_directory() . '/assets/css/onyx-fonts.css';
		wp_enqueue_style( 'onyx-home-v2-fonts', get_stylesheet_directory_uri() . '/assets/css/onyx-fonts.css', array(), ONYX_CHILD_VERSION . '.' . filemtime( $css ) );
	}
}, 5 );
// Home V2 (deploy26): preload the two critical latin woff2 files (serif normal + sans normal) on home-v2 only.
add_action( 'wp_head', function () {
	if ( ! is_page( 'home-v2' ) ) {
		return;
	}
	foreach ( array( 'cormorant-garamond-normal-latin-2fed1d.woff2', 'hanken-grotesk-normal-latin-605034.woff2' ) as $f ) {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( get_stylesheet_directory_uri() . '/assets/fonts/' . $f ) );
	}
}, 1 );

// Home V2 (deploy26): every WooCommerce loop image on home-v2 sits below the hero, so force lazy loading (WP would otherwise leave the first few eager).
add_filter( 'wp_get_attachment_image_attributes', function ( $attr ) {
	if ( is_page( 'home-v2' ) && ! is_admin() && ( ! empty( $attr['class'] ) && false !== strpos( $attr['class'], 'size-woocommerce' ) ) ) {
		$attr['loading']  = 'lazy';
		$attr['decoding'] = 'async';
		unset( $attr['fetchpriority'] );
	}
	return $attr;
}, 20 );

// Peptides 2 (deploy27): collection-page mockup shortcode [onyx_shop2] + its CSS and the self-hosted fonts, page peptides-2 only.
require_once get_stylesheet_directory() . '/inc/shop2.php';
add_action( 'wp_enqueue_scripts', function () {
	if ( is_page( 'peptides-2' ) ) {
		$d = get_stylesheet_directory();
		wp_enqueue_style( 'onyx-home-v2-fonts', get_stylesheet_directory_uri() . '/assets/css/onyx-fonts.css', array(), ONYX_CHILD_VERSION . '.' . filemtime( $d . '/assets/css/onyx-fonts.css' ) );
		wp_enqueue_style( 'onyx-shop2', get_stylesheet_directory_uri() . '/assets/css/shop2.css', array( 'onyx-tw-fixes', 'onyx-fluent-skin' ), ONYX_CHILD_VERSION . '.' . filemtime( $d . '/assets/css/shop2.css' ) );
	}
}, 20 );
add_action( 'wp_head', function () {
	if ( ! is_page( 'peptides-2' ) ) {
		return;
	}
	foreach ( array( 'cormorant-garamond-normal-latin-2fed1d.woff2', 'hanken-grotesk-normal-latin-605034.woff2' ) as $f ) {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( get_stylesheet_directory_uri() . '/assets/fonts/' . $f ) );
	}
}, 1 );

// deploy28: "<Title> 2" pages – scoped premium style + v2 flow routing (inc/pages2.php).
require_once get_stylesheet_directory() . '/inc/pages2.php';
