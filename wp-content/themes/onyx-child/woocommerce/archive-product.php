<?php
/**
 * Shop & category archive – port of the React pages/Peptides.tsx
 * (hero, Pen/Vial filter cards, sort select, 12-per-page grid, pagination).
 * Query args: ?form=pen|vial&sort=featured|best-selling|new-in|price-asc|price-desc|name&pg=N&q=term
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

$page_size = 12;
$sorts     = array( 'featured' => 'Featured', 'best-selling' => 'Best Selling', 'new-in' => 'New In', 'price-asc' => 'Price: Low to High', 'price-desc' => 'Price: High to Low', 'name' => 'Alphabetical (A - Z)' );
// phpcs:disable WordPress.Security.NonceVerification.Recommended
$sort = isset( $_GET['sort'], $sorts[ $_GET['sort'] ] ) ? sanitize_key( $_GET['sort'] ) : 'featured';
$form = isset( $_GET['form'] ) && in_array( $_GET['form'], array( 'pen', 'vial' ), true ) ? sanitize_key( $_GET['form'] ) : '';
$q    = strtolower( trim( (string) ( $_GET['q'] ?? ( is_search() ? get_search_query() : '' ) ) ) );
$page = max( 1, (int) ( $_GET['pg'] ?? 1 ) );
// phpcs:enable

$all = wc_get_products( array( 'limit' => -1, 'status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC', 'visibility' => 'catalog' ) );
$all = array_values( array_filter( $all, fn( $p ) => 'programs' !== $p->get_meta( '_onyx_category' ) && ! has_term( 'programs', 'product_cat', $p->get_id() ) ) );
$has_vials = (bool) array_filter( $all, fn( $p ) => 'vial' === $p->get_meta( '_onyx_type' ) );
if ( ! $has_vials ) {
	$form = '';
}
$min_price = fn( $p ) => (float) ( $p->is_type( 'variable' ) ? $p->get_variation_price( 'min', true ) : $p->get_price() );
$list      = $all;
if ( $q ) {
	$list = array_filter( $list, fn( $p ) => str_contains( strtolower( $p->get_name() ), $q ) || str_contains( strtolower( wp_strip_all_tags( $p->get_short_description() ) ), $q ) );
}
if ( $form ) {
	$list = array_filter( $list, fn( $p ) => $form === $p->get_meta( '_onyx_type' ) );
}
$badge = array( 'best-selling' => 'bestseller', 'new-in' => 'new' )[ $sort ] ?? '';
if ( $badge ) {
	$list = array_filter( $list, fn( $p ) => in_array( $badge, onyx_product_badges( $p ), true ) );
}
if ( in_array( $sort, array( 'price-asc', 'price-desc' ), true ) ) {
	$list = array_filter( $list, fn( $p ) => ! onyx_is_coming_soon( $p ) );
}
$list = array_values( $list );
if ( 'price-asc' === $sort ) {
	usort( $list, fn( $a, $b ) => $min_price( $a ) <=> $min_price( $b ) );
} elseif ( 'price-desc' === $sort ) {
	usort( $list, fn( $a, $b ) => $min_price( $b ) <=> $min_price( $a ) );
} elseif ( 'name' === $sort ) {
	usort( $list, fn( $a, $b ) => strcasecmp( $a->get_name(), $b->get_name() ) );
} elseif ( 'featured' === $sort ) {
	$idx = array_flip( array_map( fn( $p ) => $p->get_id(), $list ) );
	usort( $list, fn( $a, $b ) => ( (int) $b->get_meta( '_onyx_featured' ) <=> (int) $a->get_meta( '_onyx_featured' ) ) ?: ( (int) ! $a->is_in_stock() <=> (int) ! $b->is_in_stock() ) ?: ( $idx[ $a->get_id() ] <=> $idx[ $b->get_id() ] ) );
}
$total       = count( $list );
$total_pages = (int) ceil( $total / $page_size );
$page        = min( $page, max( 1, $total_pages ) );
$paged       = array_slice( $list, ( $page - 1 ) * $page_size, $page_size );
$range_start = ( $page - 1 ) * $page_size + 1;
$range_end   = min( $page * $page_size, $total );
$title       = $q ? 'Results for "' . $q . '"' : 'Research Peptides';
$base        = strtok( (string) ( $_SERVER['REQUEST_URI'] ?? '' ), '?' ); // phpcs:ignore
$link        = function ( $args ) use ( $sort, $form, $q, $base ) {
	$cur = array_filter( array( 'sort' => 'featured' !== $sort ? $sort : null, 'form' => $form ?: null, 'q' => $q ?: null ) );
	return esc_url( add_query_arg( array_filter( array_merge( $cur, $args ), fn( $v ) => null !== $v && '' !== $v ), $base ) );
};
$window = function ( $cur, $tot ) {
	if ( $tot <= 7 ) {
		return range( 1, $tot );
	}
	$s = array_values( array_unique( array_filter( array( 1, $tot, $cur, $cur - 1, $cur + 1 ), fn( $n ) => $n >= 1 && $n <= $tot ) ) );
	sort( $s );
	$out = array();
	foreach ( $s as $i => $n ) {
		if ( $i > 0 && $n - $s[ $i - 1 ] > 1 ) {
			$out[] = 'gap';
		}
		$out[] = $n;
	}
	return $out;
};
$forms = array(
	array( 'pen', 'Pen', 'Pre-filled and dual chamber pens for easier use and better preservation', 'pen.webp' ),
	array( 'vial', 'Vial', 'Research grade quality in a traditional, cost effective vial format', 'vial.webp' ),
);
?>
<div class="onyx-tw onyx-shop">
	<section class="relative overflow-hidden bg-gradient-to-br from-primary to-[#0a1733] text-primary-foreground">
		<div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-accent/20 via-transparent to-transparent"></div>
		<div class="relative mx-auto max-w-7xl px-4 py-8 2xl:py-10">
			<h1 class="text-3xl font-black md:text-4xl"><?php echo esc_html( $title ); ?></h1>
			<?php if ( ! $q ) : ?>
				<div class="mt-4 space-y-3 text-sm leading-relaxed opacity-80 md:text-base">
					<p>Every peptide in the ONYX Research catalogue is manufactured to a ≥99% purity specification, with each batch independently verified by third-party laboratories using HPLC and mass spectrometry. A unique authentication code ships with every vial.</p>
					<p>From metabolic and growth-signalling compounds to tissue-repair and neuroendocrine research peptides, every compound is supplied in the format your protocol requires: lyophilised powder, pre-mixed solution, or a pre-filled dual-chamber delivery pen.</p>
					<div class="inline-block rounded-xl border border-white/15 bg-white/10 p-4 text-xs leading-relaxed md:text-sm">
						<p class="mb-1 font-bold uppercase tracking-wider">Research Use Only</p>
						<p>All ONYX materials are supplied exclusively for legitimate laboratory and scientific research. They are not intended for human or veterinary use, diagnosis, treatment, or therapeutic application.</p>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<div class="mx-auto max-w-7xl px-4 py-8"><div>
		<?php if ( $has_vials ) : ?>
			<div class="mb-6 grid gap-3 sm:grid-cols-2 md:gap-4">
				<?php foreach ( $forms as $o ) : $sel = $form === $o[0]; ?>
					<a href="<?php echo $link( array( 'form' => $sel ? '' : $o[0], 'pg' => null ) ); // phpcs:ignore ?>" aria-pressed="<?php echo $sel ? 'true' : 'false'; ?>" class="flex cursor-pointer items-center gap-4 rounded-xl border p-4 text-left transition md:gap-5 md:p-5 <?php echo $sel ? 'border-primary bg-card' : 'border-transparent bg-secondary hover:border-primary/40'; ?>">
						<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/' . $o[3] ); ?>" alt="" class="h-16 w-20 shrink-0 object-contain md:h-20 md:w-24">
						<span><span class="block text-2xl font-bold text-primary md:text-3xl"><?php echo esc_html( $o[1] ); ?></span><span class="mt-1 block text-sm leading-snug text-muted-foreground"><?php echo esc_html( $o[2] ); ?></span></span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
			<span class="text-sm text-muted-foreground"><?php echo $total ? esc_html( "Showing {$range_start} to {$range_end} of {$total} products" ) : 'No products'; ?></span>
			<select class="w-full rounded-md border bg-background px-3 py-2 text-sm sm:w-auto onyx-sort" onchange="location.href=this.value">
				<?php foreach ( $sorts as $k => $label ) : ?>
					<option value="<?php echo $link( array( 'sort' => $k, 'pg' => null ) ); // phpcs:ignore ?>" <?php selected( $sort, $k ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php if ( ! $total ) : ?>
			<div class="rounded-xl border bg-card p-12 text-center text-muted-foreground">No products found.</div>
		<?php else : ?>
			<div class="grid grid-cols-2 gap-2 md:grid-cols-3 md:gap-4 xl:grid-cols-4">
				<?php foreach ( $paged as $p ) { echo onyx_product_card( $p ); } // phpcs:ignore ?>
			</div>
			<?php if ( $total_pages > 1 ) : $nav = 'flex h-9 w-9 cursor-pointer items-center justify-center rounded-md border bg-card text-sm transition hover:border-accent'; ?>
				<div class="mt-10 flex flex-wrap items-center justify-center gap-1">
					<?php if ( 1 === $page ) : ?><span class="<?php echo esc_attr( $nav ); ?> cursor-not-allowed opacity-30">‹</span><?php else : ?><a href="<?php echo $link( array( 'pg' => $page - 1 ) ); // phpcs:ignore ?>" class="<?php echo esc_attr( $nav ); ?>">‹</a><?php endif; ?>
					<?php foreach ( $window( $page, $total_pages ) as $n ) : ?>
						<?php if ( 'gap' === $n ) : ?><span class="flex h-9 w-6 items-center justify-center text-sm text-muted-foreground">…</span>
						<?php else : ?><a href="<?php echo $link( array( 'pg' => $n > 1 ? $n : null ) ); // phpcs:ignore ?>" class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-md border text-sm font-medium transition <?php echo $n === $page ? 'border-accent bg-accent text-accent-foreground' : 'bg-card hover:border-accent'; ?>"><?php echo (int) $n; ?></a><?php endif; ?>
					<?php endforeach; ?>
					<?php if ( $page === $total_pages ) : ?><span class="<?php echo esc_attr( $nav ); ?> cursor-not-allowed opacity-30">›</span><?php else : ?><a href="<?php echo $link( array( 'pg' => $page + 1 ) ); // phpcs:ignore ?>" class="<?php echo esc_attr( $nav ); ?>">›</a><?php endif; ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div></div>
</div>
<?php
get_footer();
