<?php
/**
 * Peptide plan (program) page – port of the React pages/PlanDetail.tsx.
 * Loaded from single-product.php for products carrying the _onyx_plan meta.
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;

global $product;
$plan  = onyx_json_meta( $product, '_onyx_plan' );
$pid   = $product->get_id();
$price = (float) ( $plan['price'] ?? $product->get_price() );
$added = did_action( 'woocommerce_add_to_cart' ) > 0;
$img   = wp_get_attachment_image_url( $product->get_image_id(), 'full' );
$arrow = onyx_icon( 'arrow-right', 'h-4 w-4 transition-transform group-hover:translate-x-1' );
$check = onyx_icon( 'check', 'h-4 w-4' );
$btn   = function ( $label, $extra ) use ( $pid, $added, $arrow, $check ) {
	?>
	<form method="post" action="<?php echo esc_url( get_permalink( $pid ) ); ?>" class="contents">
		<input type="hidden" name="add-to-cart" value="<?php echo (int) $pid; ?>"><input type="hidden" name="onyx_pp" value="1">
		<button type="submit" class="group <?php echo esc_attr( $extra ); ?> inline-flex cursor-pointer items-center gap-2 rounded-full px-7 py-3.5 text-sm font-bold shadow-lg transition hover:scale-[1.02] <?php echo $added ? 'bg-emerald-600 text-white shadow-emerald-600/30' : 'bg-accent text-accent-foreground shadow-accent/30 hover:shadow-accent/50'; ?>">
			<?php echo $added ? $check . ' Added' : esc_html( $label ) . $arrow; // phpcs:ignore ?>
		</button>
	</form>
	<?php
};
$included = array();
foreach ( $plan['included'] ?? array() as $inc ) {
	$q          = get_posts( array( 'post_type' => 'product', 'numberposts' => 1, 'meta_key' => '_onyx_slug', 'meta_value' => trim( $inc['slug'] ) ) ); // phpcs:ignore
	$inc['url'] = $q ? get_permalink( $q[0] ) : home_url( '/product/' . trim( $inc['slug'] ) . '/' );
	$included[] = $inc;
}
?>
<div class="onyx-tw onyx-plan">
	<section class="relative overflow-hidden bg-secondary">
		<div class="relative mx-auto max-w-7xl px-4 py-14 md:py-10 2xl:py-20">
			<nav class="mb-6 text-xs text-muted-foreground"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-accent">Home</a> / <a href="<?php echo esc_url( home_url( '/peptide-plans/' ) ); ?>" class="hover:text-accent">Peptide Plans</a> / <span class="text-foreground"><?php echo esc_html( $plan['name'] ?? $product->get_name() ); ?></span></nav>
			<div class="grid items-center gap-10 md:grid-cols-2">
				<div>
					<span class="mb-4 inline-flex items-center gap-2 rounded-full border border-accent/30 bg-accent/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] text-accent">Peptide Program</span>
					<h1 class="text-3xl font-black leading-tight md:text-5xl"><?php echo esc_html( $plan['name'] ?? $product->get_name() ); ?></h1>
					<p class="mt-4 max-w-xl text-base text-muted-foreground md:text-lg"><?php echo esc_html( $plan['intro'] ?? '' ); ?></p>
					<div class="mt-6 flex flex-wrap items-center gap-4">
						<span class="text-3xl font-black"><?php echo onyx_price( $price ); // phpcs:ignore ?></span>
						<?php if ( ! empty( $plan['cycleLength'] ) ) : ?><span class="inline-flex items-center gap-2 rounded-full border bg-background px-3 py-1.5 text-xs font-semibold text-muted-foreground"><?php echo onyx_icon( 'calendar-days', 'h-3.5 w-3.5 text-accent' ); // phpcs:ignore ?><?php echo esc_html( $plan['cycleLength'] ); ?></span><?php endif; ?>
					</div>
					<?php $btn( 'Add program to cart', 'mt-6' ); ?>
				</div>
				<div class="hidden md:block">
					<div class="relative overflow-hidden rounded-3xl border bg-white shadow-lg">
						<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" loading="lazy" class="aspect-[4/3] w-full object-cover">
						<div class="absolute inset-0 bg-gradient-to-t from-primary/30 via-primary/5 to-transparent"></div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="mx-auto max-w-7xl px-4 py-14"><div class="grid gap-10 md:grid-cols-[1fr_2fr]">
		<div><span class="text-xs font-bold uppercase tracking-widest text-accent">Overview</span><h2 class="mt-2 text-2xl font-black leading-snug md:text-3xl">About this program</h2></div>
		<p class="text-base leading-relaxed text-muted-foreground"><?php echo esc_html( $plan['overview'] ?? '' ); ?></p>
	</div></section>
	<?php if ( $included ) : ?>
		<section class="bg-secondary"><div class="mx-auto max-w-7xl px-4 py-14 md:py-16">
			<span class="text-xs font-bold uppercase tracking-widest text-accent">What's included</span>
			<h2 class="mt-2 text-2xl font-black md:text-3xl">Peptides in this program</h2>
			<div class="mt-8 grid gap-5 md:grid-cols-3">
				<?php foreach ( $included as $inc ) : ?>
					<a href="<?php echo esc_url( $inc['url'] ); ?>" class="group flex flex-col rounded-2xl border bg-card p-6 transition hover:-translate-y-0.5 hover:border-accent/50 hover:shadow-lg">
						<h3 class="text-lg font-black group-hover:text-accent"><?php echo esc_html( $inc['name'] ); ?></h3>
						<p class="mt-2 flex-1 text-sm leading-relaxed text-muted-foreground"><?php echo esc_html( $inc['role'] ); ?></p>
						<span class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-accent">View product<?php echo $arrow; // phpcs:ignore ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div></section>
	<?php endif; ?>
	<section class="mx-auto max-w-7xl px-4 py-14 md:py-16">
		<span class="text-xs font-bold uppercase tracking-widest text-accent">Why this combination</span>
		<h2 class="mt-2 text-2xl font-black md:text-3xl">How this program helps</h2>
		<div class="mt-8 grid gap-4 sm:grid-cols-2">
			<?php foreach ( $plan['howItHelps'] ?? array() as $pt ) : ?>
				<div class="flex items-start gap-3 rounded-2xl border bg-card p-5"><span class="mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full bg-accent/15 text-accent"><?php echo $check; // phpcs:ignore ?></span><p class="text-sm leading-relaxed text-muted-foreground"><?php echo esc_html( $pt ); ?></p></div>
			<?php endforeach; ?>
		</div>
	</section>
	<section class="mx-auto max-w-5xl px-4 py-14 md:py-16">
		<div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary to-[#0a1733] p-8 text-center text-primary-foreground shadow-2xl md:p-12">
			<div aria-hidden="true" class="pointer-events-none absolute -left-20 -top-20 h-64 w-64 rounded-full bg-accent/20 blur-[120px]"></div>
			<h2 class="relative text-2xl font-black md:text-3xl">Ready to start?</h2>
			<p class="relative mx-auto mt-3 max-w-2xl text-sm leading-relaxed opacity-75 md:text-base"><?php echo esc_html( $plan['conclusion'] ?? '' ); ?></p>
			<?php $btn( 'Add program to cart · £' . ( floor( $price ) == $price ? (int) $price : $price ), 'relative mt-7' ); ?>
		</div>
	</section>
</div>
