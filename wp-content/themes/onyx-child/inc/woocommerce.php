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

// "Buy Now" on the product page: add to cart, then go straight to checkout.
add_filter( 'woocommerce_add_to_cart_redirect', function ( $url ) {
	return ! empty( $_REQUEST['onyx_buy_now'] ) ? wc_get_checkout_url() : $url; // phpcs:ignore WordPress.Security.NonceVerification
} );

// The product page shows its own "Added" state, like the React app – no notice banner.
add_filter( 'wc_add_to_cart_message_html', function ( $msg ) {
	return ! empty( $_REQUEST['onyx_pp'] ) ? '' : $msg; // phpcs:ignore WordPress.Security.NonceVerification
} );

// "Clear cart" link on the cart page.
add_action( 'template_redirect', function () {
	if ( isset( $_GET['onyx_clear_cart'], $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'onyx_clear_cart' ) && WC()->cart ) {
		WC()->cart->empty_cart();
		wp_safe_redirect( wc_get_cart_url() );
		exit;
	}
} );

// Cart/checkout/account pages carry their own headings, like the React app.
add_filter( 'hello_elementor_page_title', function ( $show ) {
	return ( is_cart() || is_checkout() ) ? false : $show;
} );

// Cart page: React layout has its own container – drop Hello's page wrapper padding and cart notices styling.
add_filter( 'body_class', function ( $c ) {
	if ( is_cart() ) {
		$c[] = 'onyx-cart-page';
	}
	return $c;
} );

// Cart cross-sells are not part of the storefront design.
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

// Peptide plans live at /peptide-plans/<slug>/ like the React storefront.
add_action( 'init', function () {
	add_rewrite_rule( '^peptide-plans/([^/]+)/?$', 'index.php?product=$matches[1]', 'top' );
} );
add_filter( 'post_type_link', function ( $link, $post ) {
	if ( 'product' === $post->post_type && get_post_meta( $post->ID, '_onyx_plan', true ) ) {
		return home_url( '/peptide-plans/' . $post->post_name . '/' );
	}
	return $link;
}, 10, 2 );
// Old /product/<plan>/ URLs redirect to the plan URL.
add_action( 'template_redirect', function () {
	if ( is_singular( 'product' ) && get_post_meta( get_queried_object_id(), '_onyx_plan', true ) && false === strpos( (string) $_SERVER['REQUEST_URI'], '/peptide-plans/' ) ) { // phpcs:ignore
		wp_safe_redirect( get_permalink( get_queried_object_id() ), 301 );
		exit;
	}
} );

/** [onyx_plans_grid] – plan cards from React pages/PeptidePlans.tsx. */
add_shortcode( 'onyx_plans_grid', function () {
	$plans = wc_get_products( array( 'limit' => -1, 'status' => 'publish', 'meta_key' => '_onyx_plan_order', 'orderby' => 'meta_value_num', 'order' => 'ASC' ) ); // phpcs:ignore
	ob_start();
	echo '<div class="onyx-tw"><div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">';
	foreach ( $plans as $p ) {
		$d = onyx_json_meta( $p, '_onyx_plan' );
		?>
		<a href="<?php echo esc_url( get_permalink( $p->get_id() ) ); ?>" class="group flex flex-col overflow-hidden rounded-2xl border bg-card transition hover:-translate-y-0.5 hover:border-accent/50 hover:shadow-lg">
			<div class="relative aspect-[4/3] overflow-hidden bg-white">
				<img src="<?php echo esc_url( wp_get_attachment_image_url( $p->get_image_id(), 'large' ) ); ?>" alt="<?php echo esc_attr( $p->get_name() ); ?>" loading="lazy" class="h-full w-full object-cover transition-transform group-hover:scale-105">
				<div class="absolute inset-0 bg-primary/25"></div>
				<div class="absolute inset-0 bg-gradient-to-t from-primary/85 via-primary/50 to-primary/20 transition-opacity group-hover:from-primary/90"></div>
			</div>
			<div class="flex flex-1 flex-col gap-2 p-5">
				<h3 class="text-lg font-black group-hover:text-accent"><?php echo esc_html( $d['name'] ?? $p->get_name() ); ?></h3>
				<p class="line-clamp-2 text-sm text-muted-foreground"><?php echo esc_html( $d['description'] ?? '' ); ?></p>
				<div class="mt-auto flex items-center justify-between pt-2"><span class="text-lg font-bold text-primary"><?php echo onyx_price( $d['price'] ?? $p->get_price() ); // phpcs:ignore ?></span><span class="text-sm font-semibold text-accent">View program →</span></div>
			</div>
		</a>
		<?php
	}
	echo '</div></div>';
	return ob_get_clean();
} );

/** Products by storefront slug (_onyx_slug), in the given order. */
function onyx_products_by_slugs( array $slugs ) {
	$out = array();
	foreach ( $slugs as $s ) {
		$q = get_posts( array( 'post_type' => 'product', 'numberposts' => 1, 'meta_key' => '_onyx_slug', 'meta_value' => $s ) ); // phpcs:ignore
		if ( $q ) {
			$out[] = wc_get_product( $q[0] );
		}
	}
	return array_filter( $out );
}

/** [onyx_pen_carousel slugs="a,b,c"] – Home "Research Materials" dual-chamber pen slider. */
add_shortcode( 'onyx_pen_carousel', function ( $atts ) {
	$atts  = shortcode_atts( array( 'slugs' => 'retadualpen,motscdualpen,ghkcudualpen,bpctbdualpen,semadualpen,hghdualpen,hcgdualpen,bactwater' ), $atts );
	$items = onyx_products_by_slugs( array_map( 'trim', explode( ',', $atts['slugs'] ) ) );
	$n     = count( $items );
	ob_start();
	?>
	<div class="onyx-tw onyx-pen-carousel">
		<div class="mx-auto grid max-w-[90rem] grid-cols-2 gap-2 px-2 pb-4 pt-2 md:hidden">
			<?php foreach ( array_values( $items ) as $i => $p ) : ?>
				<div class="<?php echo ( 1 === $n % 2 && $i === $n - 1 ) ? 'col-span-2 w-full max-w-[calc(50%-0.25rem)]' : ''; ?>"><?php echo onyx_product_card( $p, 'pen' ); // phpcs:ignore ?></div>
			<?php endforeach; ?>
		</div>
		<div class="mx-auto hidden max-w-[90rem] overflow-hidden px-4 pb-4 pt-2 md:block" data-onyx-pen-viewport>
			<div class="flex gap-3" data-onyx-pen-track style="transition:transform .9s cubic-bezier(.22,1,.36,1)">
				<?php foreach ( $items as $p ) : ?>
					<div class="shrink-0 md:w-[calc((100%_-_0.75rem)/2)] lg:w-[calc((100%_-_1.5rem)/3)] xl:w-[calc((100%_-_2.25rem)/4)] transition-transform hover:-translate-y-0.5"><?php echo onyx_product_card( $p, 'pen' ); // phpcs:ignore ?></div>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="mx-auto hidden max-w-[90rem] justify-center gap-2 px-4 md:flex">
			<button type="button" data-onyx-pen-dir="-1" aria-label="Scroll left" class="grid h-10 w-10 cursor-pointer place-items-center rounded-full border transition hover:border-accent hover:text-accent"><?php echo onyx_icon( 'chevron-left', 'h-5 w-5' ); // phpcs:ignore ?></button>
			<button type="button" data-onyx-pen-dir="1" aria-label="Scroll right" class="grid h-10 w-10 cursor-pointer place-items-center rounded-full border transition hover:border-accent hover:text-accent"><?php echo onyx_icon( 'chevron-right', 'h-5 w-5' ); // phpcs:ignore ?></button>
		</div>
	</div>
	<script>
	(function(){var r=document.currentScript.previousElementSibling,v=r.querySelector('[data-onyx-pen-viewport]'),t=r.querySelector('[data-onyx-pen-track]'),o=0;
	function vis(){var s=getComputedStyle(v);return v.clientWidth-parseFloat(s.paddingLeft)-parseFloat(s.paddingRight);}
	function max(){return Math.max(0,t.scrollWidth-vis());}
	r.querySelectorAll('[data-onyx-pen-dir]').forEach(function(b){b.addEventListener('click',function(){o=Math.min(Math.max(o+(+b.dataset.onyxPenDir)*vis(),0),max());t.style.transform='translateX('+(-o)+'px)';});});
	addEventListener('resize',function(){o=Math.min(o,max());t.style.transform='translateX('+(-o)+'px)';});})();
	</script>
	<?php
	return ob_get_clean();
} );

/** [onyx_product_tabs] – Home Bestsellers / Latest / Specials tabs (first 4 of each, storefront order). */
add_shortcode( 'onyx_product_tabs', function () {
	$all  = wc_get_products( array( 'limit' => -1, 'status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC' ) );
	$tabs = array(
		'Bestsellers' => 'bestseller',
		'Latest'      => 'new',
		'Specials'    => 'sale',
	);
	ob_start();
	echo '<div class="onyx-tw onyx-tabs"><div class="mb-6 flex flex-wrap items-end justify-between gap-4"><div class="flex gap-1 rounded-xl border bg-secondary p-1">';
	$i = 0;
	foreach ( array_keys( $tabs ) as $t ) {
		printf( '<button type="button" data-onyx-tab="%1$s" class="rounded-lg px-5 py-2 text-sm font-semibold transition %2$s">%1$s</button>', esc_html( $t ), 0 === $i++ ? 'bg-primary text-primary-foreground shadow' : 'text-muted-foreground hover:text-foreground' );
	}
	printf( '</div><a href="%s" class="group inline-flex items-center gap-1 text-base font-semibold text-accent transition-colors hover:text-primary">View all<span class="inline-block transition-transform group-hover:translate-x-1">→</span></a></div>', esc_url( wc_get_page_permalink( 'shop' ) ) );
	$i = 0;
	foreach ( $tabs as $t => $badge ) {
		$list = array_slice( array_values( array_filter( $all, fn( $p ) => in_array( $badge, onyx_product_badges( $p ), true ) ) ), 0, 4 );
		printf( '<div data-onyx-tab-panel="%s"%s>', esc_attr( $t ), 0 === $i++ ? '' : ' hidden' );
		if ( $list ) {
			echo '<div class="grid grid-cols-2 gap-2 md:grid-cols-3 md:gap-4 lg:grid-cols-4">';
			foreach ( $list as $p ) {
				echo '<div class="h-full">' . onyx_product_card( $p ) . '</div>'; // phpcs:ignore
			}
			echo '</div>';
		} else {
			echo '<p class="py-12 text-center text-muted-foreground">No products in this category yet.</p>';
		}
		echo '</div>';
	}
	echo '</div>';
	?>
	<script>
	(function(){var r=document.currentScript.previousElementSibling,on=['bg-primary','text-primary-foreground','shadow'],off=['text-muted-foreground','hover:text-foreground'];
	r.querySelectorAll('[data-onyx-tab]').forEach(function(b){b.addEventListener('click',function(){r.querySelectorAll('[data-onyx-tab]').forEach(function(o){var a=o===b;on.forEach(function(c){o.classList.toggle(c,a)});off.forEach(function(c){o.classList.toggle(c,!a)});});r.querySelectorAll('[data-onyx-tab-panel]').forEach(function(p){p.hidden=p.dataset.onyxTabPanel!==b.dataset.onyxTab;});});});})();
	</script>
	<?php
	return ob_get_clean();
} );
