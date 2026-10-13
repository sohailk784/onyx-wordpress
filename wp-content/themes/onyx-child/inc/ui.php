<?php
/**
 * Shared UI helpers that reproduce the React storefront markup (Tailwind classes
 * from the compiled onyx-app.css, scoped under .onyx-tw).
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;

/** Inline a lucide icon from assets/icons with the given classes. */
function onyx_icon( $name, $class = 'h-4 w-4' ) {
	static $cache = array();
	if ( ! isset( $cache[ $name ] ) ) {
		$file           = get_stylesheet_directory() . '/assets/icons/' . $name . '.svg';
		$svg            = file_exists( $file ) ? file_get_contents( $file ) : ''; // phpcs:ignore
		$svg            = preg_replace( '/<!--.*?-->/s', '', $svg );
		$cache[ $name ] = trim( preg_replace( '/class="[^"]*"/', 'class="%s" aria-hidden="true"', $svg, 1 ) );
	}
	return sprintf( $cache[ $name ], esc_attr( 'lucide ' . $class ) );
}

/** Price formatted like the React <Price/> component (£1,234.00). */
function onyx_price( $value ) {
	return '<span translate="no" class="notranslate">' . wp_kses_post( wc_price( (float) $value ) ) . '</span>';
}

function onyx_badge_label( $b ) {
	$map = array( 'new' => 'New', 'bestseller' => 'BestSeller', 'sale' => 'Sale' );
	return $map[ $b ] ?? $b;
}
function onyx_badge_class( $b ) {
	return 'new' === $b ? 'bg-emerald-600' : ( 'sale' === $b ? 'bg-amber-500' : 'bg-[#1e3a5f]' );
}
function onyx_product_badges( $product ) {
	return array_values( array_filter( array_map( 'trim', explode( ',', (string) $product->get_meta( '_onyx_badges' ) ) ) ) );
}
function onyx_is_coming_soon( $product ) {
	return has_term( 'coming-soon', 'product_tag', $product->get_id() );
}
function onyx_json_meta( $product, $key ) {
	$v = json_decode( (string) $product->get_meta( $key ), true );
	return is_array( $v ) ? $v : array();
}

/** Product card – port of components/ProductThumb.tsx */
function onyx_product_card( $product, $variant = 'thumb' ) {
	$pen = 'pen' === $variant;
	$url          = get_permalink( $product->get_id() );
	$img          = wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_single' );
	$coming       = onyx_is_coming_soon( $product );
	$out_of_stock = ! $product->is_in_stock() && ! $coming;
	$has_variants = $product->is_type( 'variable' ) && count( $product->get_children() ) > 1;
	// The storefront lists the product's own base price ("From £x"), which can differ from the cheapest variation.
	$list_price   = $product->get_meta( '_onyx_list_price' );
	$price        = '' !== $list_price ? $list_price : ( $product->is_type( 'variable' ) ? $product->get_variation_price( 'min', true ) : $product->get_price() );
	$orig         = $product->get_meta( '_onyx_original_price' );
	ob_start();
	?>
	<div class="group relative flex h-full flex-col overflow-hidden rounded-xl border bg-card transition hover:shadow-lg">
		<a href="<?php echo esc_url( $url ); ?>" class="relative block <?php echo $pen ? 'aspect-[3/4] overflow-hidden bg-white pb-1' : 'aspect-square overflow-hidden bg-white'; ?>">
			<?php if ( $img ) : ?>
				<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" loading="lazy" class="<?php echo $pen ? 'h-full w-full object-contain transition-opacity duration-300 opacity-100' : 'h-full w-full object-contain transition-all duration-300 group-hover:scale-105 opacity-100'; ?>">
			<?php else : ?>
				<div class="grid h-full w-full place-items-center bg-secondary"><?php echo onyx_icon( 'image-off', 'h-8 w-8 text-muted-foreground' ); // phpcs:ignore ?></div>
			<?php endif; ?>
			<div class="absolute left-3 top-3 flex flex-col items-start gap-1">
				<?php if ( $coming ) : ?><span class="rounded-full bg-slate-600 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white">Coming soon</span><?php endif; ?>
				<?php if ( $out_of_stock ) : ?><span class="rounded-full bg-red-600 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white">Out of stock</span><?php endif; ?>
				<?php foreach ( onyx_product_badges( $product ) as $b ) : ?>
					<span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white <?php echo esc_attr( onyx_badge_class( $b ) ); ?>"><?php echo esc_html( onyx_badge_label( $b ) ); ?></span>
				<?php endforeach; ?>
			</div>
		</a>
		<?php onyx_wishlist_button( $product, 'absolute right-3 top-3 grid h-9 w-9 cursor-pointer place-items-center rounded-full bg-white/90 shadow transition-transform hover:bg-white', 'h-4 w-4 transition-transform scale-100 text-foreground' ); ?>
		<div class="<?php echo $pen ? 'flex flex-1 flex-col gap-2 border-t px-2 pb-2 pt-1 md:px-4 md:pb-4 md:pt-2' : 'flex flex-1 flex-col gap-2 p-2 md:p-4'; ?>">
			<a href="<?php echo esc_url( $url ); ?>" class="<?php echo $pen ? 'line-clamp-1 text-[11px] font-semibold hover:text-accent md:text-xs' : 'line-clamp-none text-xs font-semibold hover:text-accent md:line-clamp-2 md:text-sm'; ?>"><?php echo esc_html( $product->get_name() ); ?></a>
			<?php if ( $coming ) : ?>
				<div class="mt-auto"><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="block rounded-md border border-accent px-3 py-2 text-center text-xs font-semibold text-accent transition hover:bg-accent hover:text-accent-foreground">Notify me</a></div>
			<?php else : ?>
				<div class="mt-auto">
					<?php if ( $orig ) : ?><span class="mr-2 text-xs text-muted-foreground line-through">£<?php echo esc_html( $orig ); ?></span><?php endif; ?>
					<span class="<?php echo $pen ? 'text-sm font-bold text-primary md:text-base' : 'text-base font-bold text-primary md:text-lg'; ?>"><?php echo $has_variants ? 'From ' : ''; ?><?php echo onyx_price( $price ); // phpcs:ignore ?></span>
				</div>
				<?php if ( $out_of_stock ) : ?>
					<button disabled class="w-full cursor-not-allowed rounded-md border border-border bg-secondary px-3 py-2.5 text-sm font-bold text-muted-foreground">Out of Stock</button>
				<?php else : ?>
					<a href="<?php echo esc_url( $url ); ?>" class="block w-full cursor-pointer rounded-md bg-accent px-3 py-2.5 text-center text-sm font-bold text-accent-foreground shadow-md shadow-accent/30 transition hover:scale-[1.02] hover:shadow-accent/50">View Details</a>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/** Wishlist toggle: our heart icon with the TI Wishlist link layered invisibly on top. */
function onyx_wishlist_button( $product, $btn_class, $icon_class ) {
	echo '<div class="onyx-wish ' . esc_attr( $btn_class ) . '" aria-label="Toggle wishlist">' . onyx_icon( 'heart', $icon_class ); // phpcs:ignore
	if ( shortcode_exists( 'ti_wishlists_addtowishlist' ) ) {
		echo '<span class="onyx-wish-hit">' . do_shortcode( '[ti_wishlists_addtowishlist loop=yes product_id="' . (int) $product->get_id() . '"]' ) . '</span>'; // phpcs:ignore
	}
	echo '</div>';
}

/** Nav menu as a two-level tree: [ [title,url,children=>[...]] ]. */
function onyx_menu_tree( $location ) {
	$locs = get_nav_menu_locations();
	if ( empty( $locs[ $location ] ) ) {
		return array();
	}
	$items = wp_get_nav_menu_items( $locs[ $location ] ) ?: array();
	$tree  = array();
	$by_id = array();
	foreach ( $items as $it ) {
		$node = array( 'title' => $it->title, 'url' => $it->url, 'children' => array() );
		if ( $it->menu_item_parent && isset( $by_id[ $it->menu_item_parent ] ) ) {
			$tree[ $by_id[ $it->menu_item_parent ] ]['children'][] = $node;
		} else {
			$tree[]               = $node;
			$by_id[ $it->ID ] = count( $tree ) - 1;
		}
	}
	return array_values( $tree );
}
