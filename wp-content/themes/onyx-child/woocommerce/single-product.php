<?php
/**
 * Single product – port of the React pages/Product.tsx layout.
 * Content blocks (profile, overview, mechanism, FAQs…) come from the _onyx_detail meta.
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	global $product;
	$product = wc_get_product( get_the_ID() );
	if ( $product->get_meta( '_onyx_plan' ) ) {
		wc_get_template( 'single-plan.php' );
		continue;
	}
	$pid     = $product->get_id();
	$detail  = onyx_json_meta( $product, '_onyx_detail' );
	$lvars   = onyx_json_meta( $product, '_onyx_variants' );
	$coas    = onyx_json_meta( $product, '_onyx_coa' );
	$coming  = onyx_is_coming_soon( $product );
	$orig    = $product->get_meta( '_onyx_original_price' );

	// Variants in shop order, with live availability flags.
	$variants = array();
	if ( $product->is_type( 'variable' ) ) {
		$flags = array();
		foreach ( $lvars as $lv ) {
			$flags[ $lv['label'] ] = $lv;
		}
		foreach ( $product->get_children() as $vid ) {
			$v = wc_get_product( $vid );
			if ( ! $v || ! $v->is_purchasable() && 'publish' !== $v->get_status() ) {
				continue;
			}
			$attrs = $v->get_variation_attributes();
			$label = wc_get_formatted_variation( $v, true, false, false );
			$f     = $flags[ $label ] ?? array();
			$variants[] = array(
				'id'      => $vid,
				'label'   => $label,
				'price'   => (float) $v->get_price(),
				'attrs'   => $attrs,
				'soon'    => ! empty( $f['comingSoon'] ),
				'oos'     => ! empty( $f['outOfStock'] ) || ! $v->is_in_stock(),
				'coa'     => isset( $coas[ $label ] ) ? get_stylesheet_directory_uri() . '/assets/certificates/' . rawurlencode( $coas[ $label ] ) : '',
			);
		}
	}
	// Simple products that the storefront still shows with a single "Specification" pill.
	if ( ! $variants && $lvars ) {
		foreach ( $lvars as $lv ) {
			$variants[] = array(
				'id'     => 0,
				'label'  => $lv['label'],
				'price'  => (float) $product->get_price(),
				'attrs'  => array(),
				'soon'   => ! empty( $lv['comingSoon'] ),
				'oos'    => ! empty( $lv['outOfStock'] ) || ! $product->is_in_stock(),
				'coa'    => isset( $coas[ $lv['label'] ] ) ? get_stylesheet_directory_uri() . '/assets/certificates/' . rawurlencode( $coas[ $lv['label'] ] ) : '',
				'simple' => true,
			);
		}
	}
	$sel = 0;
	foreach ( $variants as $i => $v ) {
		if ( ! $v['soon'] && ! $v['oos'] ) {
			$sel = $i;
			break;
		}
	}
	$cur          = $variants[ $sel ] ?? null;
	$active_price = $cur ? $cur['price'] : (float) $product->get_price();
	$oos          = $cur ? $cur['oos'] : ! $product->is_in_stock();
	$soon         = $coming || ( $cur && $cur['soon'] );
	$coa_url      = $cur ? $cur['coa'] : ( isset( $coas['*'] ) ? get_stylesheet_directory_uri() . '/assets/certificates/' . rawurlencode( $coas['*'] ) : '' );
	$gallery      = array_filter( array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() ) );
	$linked_slug  = (string) $product->get_meta( '_onyx_linked' );
	$type         = (string) $product->get_meta( '_onyx_type' );
	$linked       = $linked_slug ? get_page_by_path( $linked_slug, OBJECT, 'product' ) : null;
	if ( ! $linked && $linked_slug ) {
		$q      = get_posts( array( 'post_type' => 'product', 'meta_key' => '_onyx_slug', 'meta_value' => $linked_slug, 'numberposts' => 1 ) ); // phpcs:ignore
		$linked = $q ? $q[0] : null;
	}
	$just_added = did_action( 'woocommerce_add_to_cart' ) > 0;
	$info       = array_merge( $detail['info'] ?? array(), ! empty( $detail['conclusion'] ) ? array( array( 'title' => 'Conclusion', 'body' => $detail['conclusion'] ) ) : array() );
	$apps       = array(
		array( 'flask-conical', 'In-Vitro Binding & Interaction Assays', 'Suitable for laboratory studies examining interactions with relevant receptors, proteins, enzymes, and other molecular targets.' ),
		array( 'shield-check', 'Stability Studies', 'Used in controlled studies evaluating compound stability under defined laboratory and environmental conditions.' ),
		array( 'microscope', 'Analytical Characterisation', 'Applicable to analytical workflows examining identity, purity, composition, and physicochemical characteristics.' ),
		array( 'activity', 'Cell-Based Assays', 'Suitable for in-vitro experimental models investigating cellular responses and associated molecular pathways.' ),
		array( 'circle-check-big', 'Degradation & Integrity Studies', 'Can be incorporated into laboratory studies examining degradation profiles and molecular integrity under controlled conditions.' ),
		array( 'dna', 'Molecular Interaction Studies', 'Applicable to experimental research investigating interactions between compounds and relevant biological or molecular targets.' ),
		array( 'clipboard-check', 'Method Development & Validation', 'Suitable for developing and evaluating analytical methods, laboratory procedures, and experimental workflows.' ),
		array( 'chart-column', 'Comparative Laboratory Research', 'Can be incorporated into controlled experimental designs comparing compounds, formulations, concentrations, or laboratory conditions.' ),
	);
	$cat     = (string) $product->get_meta( '_onyx_category' );
	$related = array();
	if ( $cat ) {
		$related = wc_get_products( array( 'limit' => 4, 'exclude' => array( $pid ), 'meta_key' => '_onyx_category', 'meta_value' => $cat, 'orderby' => 'menu_order', 'order' => 'ASC', 'status' => 'publish' ) ); // phpcs:ignore
	}
	?>
<div class="onyx-tw onyx-product" data-variants="<?php echo esc_attr( wp_json_encode( $variants ) ); ?>">
	<div class="border-b bg-secondary">
		<div class="mx-auto max-w-7xl px-4 py-3 text-xs text-muted-foreground">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-accent">Home</a> / <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="hover:text-accent">Peptides</a> / <span class="text-foreground"><?php echo esc_html( $product->get_name() ); ?></span>
		</div>
	</div>

	<section class="mx-auto grid max-w-7xl gap-10 px-4 py-12 md:grid-cols-2">
		<div>
			<div class="relative overflow-hidden rounded-2xl border bg-white shadow-sm">
				<div class="overflow-hidden cursor-zoom-in onyx-zoom">
					<img data-onyx-main src="<?php echo esc_url( wp_get_attachment_image_url( reset( $gallery ), 'full' ) ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" class="aspect-square w-full object-contain transition-transform duration-200 ease-out">
				</div>
				<div class="pointer-events-none absolute left-4 top-4 flex flex-col gap-1.5">
					<?php foreach ( onyx_product_badges( $product ) as $b ) : ?>
						<span class="rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-white shadow <?php echo esc_attr( onyx_badge_class( $b ) ); ?>"><?php echo esc_html( onyx_badge_label( $b ) ); ?></span>
					<?php endforeach; ?>
				</div>
			</div>
			<?php if ( count( $gallery ) > 1 ) : ?>
				<div class="mt-3 flex flex-wrap gap-2">
					<?php foreach ( array_values( $gallery ) as $i => $gid ) : ?>
						<button type="button" data-onyx-thumb="<?php echo esc_url( wp_get_attachment_image_url( $gid, 'full' ) ); ?>" class="h-20 w-20 shrink-0 overflow-hidden rounded-lg border bg-white p-1.5 transition hover:border-accent <?php echo 0 === $i ? 'border-accent ring-2 ring-accent/30' : ''; ?>"><img src="<?php echo esc_url( wp_get_attachment_image_url( $gid, 'thumbnail' ) ); ?>" alt="" loading="lazy" class="h-full w-full object-contain"></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="flex flex-col">
			<div class="flex items-start justify-between gap-3 sm:block">
				<h1 class="text-xl font-black leading-tight sm:text-3xl md:text-4xl"><?php echo esc_html( $product->get_name() ); ?></h1>
				<?php if ( $soon ) : ?>
					<div class="shrink-0 sm:mt-5"><span class="inline-flex items-center gap-2 rounded-full border border-accent/30 bg-accent/10 px-4 py-1.5 text-sm font-bold uppercase tracking-wider text-accent"><?php echo onyx_icon( 'sparkles', 'h-4 w-4' ); // phpcs:ignore ?>Coming soon</span></div>
				<?php else : ?>
					<div class="flex shrink-0 flex-col items-end gap-1 sm:mt-5 sm:flex-row sm:flex-wrap sm:items-baseline sm:gap-3">
						<span class="text-2xl font-black text-primary sm:text-4xl" data-onyx-price><?php echo onyx_price( $active_price ); // phpcs:ignore ?></span>
						<?php if ( $orig ) : ?>
							<span class="text-lg text-muted-foreground line-through"><?php echo onyx_price( $orig ); // phpcs:ignore ?></span>
							<span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-bold text-amber-700">-<?php echo (int) round( ( 1 - $product->get_price() / $orig ) * 100 ); ?>% OFF</span>
						<?php endif; ?>
						<?php if ( $oos ) : ?><span class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold uppercase tracking-wider text-red-700">Out of stock</span><?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<p class="mt-4 text-sm leading-relaxed text-muted-foreground"><?php echo wp_kses_post( wp_strip_all_tags( $product->get_short_description() ) ); ?></p>

			<?php if ( $type && $linked ) : ?>
				<div class="mt-6">
					<div class="mb-2 text-xs font-bold uppercase tracking-widest text-muted-foreground">Select variant</div>
					<div class="flex flex-wrap gap-2">
						<?php foreach ( array( 'pen', 'vial' ) as $form ) : ?>
							<?php if ( $form === $type ) : ?>
								<span class="rounded-xl border border-accent bg-accent/10 px-6 py-2.5 text-sm font-semibold capitalize text-accent"><?php echo esc_html( $form ); ?></span>
							<?php else : ?>
								<a href="<?php echo esc_url( get_permalink( $linked ) ); ?>" class="rounded-xl border border-border px-6 py-2.5 text-sm font-semibold capitalize transition hover:border-accent/60"><?php echo esc_html( $form ); ?></a>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $variants ) : ?>
				<div class="mt-6">
					<div class="mb-2 text-xs font-bold uppercase tracking-widest text-muted-foreground"><?php echo count( $variants ) > 1 ? 'Select specification' : 'Specification'; ?></div>
					<div class="flex flex-wrap gap-2">
						<?php foreach ( $variants as $i => $v ) : $un = $v['oos'] || $v['soon']; ?>
							<button type="button" data-onyx-variant="<?php echo (int) $i; ?>" <?php disabled( $un ); ?> class="rounded-xl border px-5 py-2.5 text-sm font-semibold transition <?php echo $un ? 'cursor-not-allowed border-border text-muted-foreground opacity-50' : ( $i === $sel ? 'cursor-pointer border-accent bg-accent/10 text-accent' : 'cursor-pointer border-border hover:border-accent/60' ); ?>"><?php echo esc_html( $v['label'] ); ?> - <?php echo onyx_price( $v['price'] ); // phpcs:ignore ?><?php echo $v['soon'] ? ' (coming soon)' : ( $v['oos'] ? ' (out of stock)' : '' ); ?></button>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $soon ) : ?>
				<div class="mt-6 flex items-center gap-3">
					<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-accent px-6 py-3.5 text-sm font-bold text-accent-foreground shadow-lg shadow-accent/30 transition hover:scale-[1.01] hover:shadow-accent/50"><?php echo onyx_icon( 'bell', 'h-4 w-4' ); // phpcs:ignore ?>Notify me</a>
					<?php onyx_wishlist_button( $product, 'relative grid h-12 w-12 cursor-pointer place-items-center rounded-xl border transition hover:bg-secondary', 'h-5 w-5 transition-transform scale-100' ); ?>
				</div>
			<?php elseif ( $oos ) : ?>
				<div class="mt-6 flex items-center gap-3">
					<button disabled class="flex-1 cursor-not-allowed rounded-xl border border-border bg-secondary px-6 py-3.5 text-sm font-bold text-muted-foreground">Out of Stock</button>
					<?php onyx_wishlist_button( $product, 'relative grid h-12 w-12 cursor-pointer place-items-center rounded-xl border transition hover:bg-secondary', 'h-5 w-5 transition-transform scale-100' ); ?>
				</div>
			<?php else : ?>
				<form class="mt-6 space-y-3 onyx-cart-form" method="post" action="<?php echo esc_url( get_permalink( $pid ) ); ?>">
					<input type="hidden" name="add-to-cart" value="<?php echo (int) $pid; ?>">
					<input type="hidden" name="product_id" value="<?php echo (int) $pid; ?>">
					<input type="hidden" name="onyx_pp" value="1">
					<?php if ( $cur && empty( $cur['simple'] ) ) : ?>
						<input type="hidden" name="variation_id" value="<?php echo (int) $cur['id']; ?>" data-onyx-vid>
						<?php foreach ( $cur['attrs'] as $k => $val ) : ?>
							<input type="hidden" name="<?php echo esc_attr( $k ); ?>" value="<?php echo esc_attr( $val ); ?>" data-onyx-attr="<?php echo esc_attr( $k ); ?>">
						<?php endforeach; ?>
					<?php endif; ?>
					<input type="hidden" name="quantity" value="1" data-onyx-qty-input>
					<div class="flex items-center gap-3">
						<div class="flex items-center rounded-xl border">
							<button type="button" data-onyx-qty="-1" class="grid h-12 w-12 place-items-center rounded-l-xl hover:bg-secondary"><?php echo onyx_icon( 'minus', 'h-4 w-4' ); // phpcs:ignore ?></button>
							<span class="w-12 text-center font-bold" data-onyx-qty-label>1</span>
							<button type="button" data-onyx-qty="1" class="grid h-12 w-12 place-items-center rounded-r-xl hover:bg-secondary"><?php echo onyx_icon( 'plus', 'h-4 w-4' ); // phpcs:ignore ?></button>
						</div>
						<button type="submit" class="flex-1 cursor-pointer rounded-xl border px-6 py-3.5 text-sm font-bold transition disabled:cursor-not-allowed <?php echo $just_added ? 'border-primary bg-primary text-primary-foreground' : 'border-primary bg-transparent text-primary hover:bg-primary hover:text-primary-foreground'; ?>">
							<?php echo $just_added ? '<span class="flex items-center justify-center gap-2">' . onyx_icon( 'check', 'h-4 w-4' ) . ' Added</span>' : 'Add to Cart'; // phpcs:ignore ?>
						</button>
						<?php onyx_wishlist_button( $product, 'relative grid h-12 w-12 cursor-pointer place-items-center rounded-xl border transition hover:bg-secondary', 'h-5 w-5 transition-transform scale-100' ); ?>
					</div>
					<button type="submit" name="onyx_buy_now" value="1" class="w-full cursor-pointer rounded-xl bg-accent px-6 py-3.5 text-sm font-bold text-accent-foreground shadow-lg shadow-accent/30 transition hover:scale-[1.01] hover:shadow-accent/50">Buy Now</button>
				</form>
			<?php endif; ?>

			<a href="<?php echo esc_url( $coa_url ?: '#' ); ?>" target="_blank" rel="noopener noreferrer" data-onyx-coa class="mt-4 flex items-center gap-2 self-start py-2 text-base font-bold text-accent transition hover:underline" <?php echo $coa_url ? '' : 'hidden'; ?>><?php echo onyx_icon( 'file-check-2', 'h-5 w-5' ); // phpcs:ignore ?>View Certificate of Authenticity</a>

			<ul class="mt-8 space-y-3 border-t pt-6 text-sm">
				<li class="flex items-center gap-3 text-muted-foreground"><?php echo onyx_icon( 'flask-conical', 'h-5 w-5 shrink-0 text-accent' ); // phpcs:ignore ?> Research grade · ≥99% purity (HPLC verified)</li>
				<li class="flex items-center gap-3 text-muted-foreground"><?php echo onyx_icon( 'shield-check', 'h-5 w-5 shrink-0 text-accent' ); // phpcs:ignore ?> Third-party verified · unique batch authentication code</li>
				<li class="flex items-center gap-3 text-muted-foreground"><?php echo onyx_icon( 'truck', 'h-5 w-5 shrink-0 text-accent' ); // phpcs:ignore ?> Discreet EU dispatch · tracked on every order</li>
			</ul>
			<div class="mt-6 rounded-xl border border-amber-300/60 bg-amber-50 p-4 text-xs leading-relaxed text-amber-900"><strong>For research use only.</strong> Not approved for human consumption, food, veterinary, or drug use. By purchasing you confirm you are a qualified researcher.</div>
		</div>
	</section>

	<?php if ( ! empty( $detail['profile'] ) ) : ?>
		<section class="bg-secondary"><div class="mx-auto max-w-7xl px-4 py-14">
			<span class="text-xs font-bold uppercase tracking-widest text-accent">Compound data</span>
			<h2 class="mt-2 text-2xl font-black md:text-3xl">Peptide Profile</h2>
			<div class="mt-8 overflow-hidden rounded-2xl border bg-card"><table class="w-full text-sm"><tbody class="divide-y">
				<?php foreach ( $detail['profile'] as $row ) : ?>
					<tr class="transition hover:bg-secondary/50"><td class="w-44 px-6 py-4 font-semibold text-foreground"><?php echo esc_html( $row['label'] ); ?></td><td class="px-6 py-4 <?php echo 'Storage' === $row['label'] ? 'font-medium text-foreground' : 'text-muted-foreground'; ?>"><?php echo esc_html( $row['value'] ); ?></td></tr>
				<?php endforeach; ?>
			</tbody></table></div>
		</div></section>
	<?php endif; ?>

	<?php if ( ! empty( $detail['summary'] ) ) : ?>
		<section class="mx-auto max-w-7xl px-4 py-14"><div class="grid gap-10 md:grid-cols-[1fr_2fr]">
			<div><span class="text-xs font-bold uppercase tracking-widest text-accent">Overview</span><h2 class="mt-2 text-2xl font-black leading-snug md:text-3xl">About this product</h2></div>
			<p class="text-base leading-relaxed text-muted-foreground"><?php echo esc_html( $detail['summary'] ); ?></p>
		</div></section>
	<?php endif; ?>

	<?php if ( ! empty( $detail['steps'] ) ) : ?>
		<section class="relative overflow-hidden bg-gradient-to-br from-secondary to-background">
			<div aria-hidden="true" class="pointer-events-none absolute -left-32 top-0 h-96 w-96 rounded-full bg-accent/10 blur-[150px]"></div>
			<div aria-hidden="true" class="pointer-events-none absolute -right-32 bottom-0 h-96 w-96 rounded-full bg-sky-500/10 blur-[150px]"></div>
			<div class="relative mx-auto max-w-7xl px-4 py-16 md:py-24">
				<div class="mx-auto max-w-2xl text-center">
					<span class="mb-4 inline-flex items-center gap-2 rounded-full border border-accent/30 bg-accent/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] text-accent"><?php echo onyx_icon( 'sparkles', 'h-3.5 w-3.5' ); // phpcs:ignore ?>Mechanism</span>
					<h2 class="text-3xl font-black tracking-tight text-foreground md:text-4xl">How it works</h2>
				</div>
				<div class="mt-14 grid items-center gap-12 md:grid-cols-2 md:gap-16">
					<?php if ( ! empty( $detail['diagram'] ) ) : ?>
						<div class="relative md:sticky md:top-28">
							<div aria-hidden="true" class="pointer-events-none absolute -inset-6 rounded-[2rem] bg-gradient-to-br from-accent/20 via-sky-400/10 to-transparent opacity-70 blur-2xl"></div>
							<div class="relative rounded-3xl border bg-white p-6 shadow-xl md:p-8"><img src="<?php echo esc_url( $detail['diagram'] ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?> mechanism diagram" width="396" height="480" loading="lazy" class="mx-auto w-full max-w-sm object-contain"></div>
						</div>
					<?php endif; ?>
					<ol class="relative space-y-8">
						<span aria-hidden="true" class="absolute bottom-4 left-[1.125rem] top-4 w-px bg-gradient-to-b from-accent/50 via-border to-transparent"></span>
						<?php foreach ( $detail['steps'] as $i => $s ) : ?>
							<li class="relative flex gap-5"><span class="relative z-10 grid h-9 w-9 shrink-0 place-items-center rounded-full bg-gradient-to-br from-accent to-sky-400 text-sm font-black text-accent-foreground ring-4 ring-background"><?php echo (int) $i + 1; ?></span><div class="pt-1"><h3 class="font-bold tracking-tight text-foreground"><?php echo esc_html( $s['title'] ); ?></h3><p class="mt-1.5 text-sm leading-relaxed text-muted-foreground"><?php echo esc_html( $s['body'] ); ?></p></div></li>
						<?php endforeach; ?>
					</ol>
				</div>
				<?php if ( ! empty( $detail['note'] ) ) : ?>
					<div class="mt-14 flex items-start gap-4 rounded-2xl border border-accent/30 bg-accent/5 p-6 md:p-7"><span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-accent/15 text-accent ring-1 ring-inset ring-accent/20"><?php echo onyx_icon( 'sparkles', 'h-5 w-5' ); // phpcs:ignore ?></span><p class="text-sm leading-relaxed text-muted-foreground md:text-base"><strong class="font-bold text-foreground">Why it matters: </strong><?php echo esc_html( $detail['note'] ); ?></p></div>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<section class="mx-auto max-w-7xl px-4 py-14">
		<span class="text-xs font-bold uppercase tracking-widest text-accent">Applications</span>
		<h2 class="mt-2 text-2xl font-black md:text-3xl">Technical &amp; Laboratory Applications</h2>
		<p class="mt-4 max-w-2xl text-sm leading-relaxed text-muted-foreground">Designed for controlled laboratory research, research compounds may be evaluated across a range of analytical, biochemical, and experimental applications.</p>
		<div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
			<?php foreach ( $apps as $a ) : ?>
				<div class="rounded-2xl border p-5 text-center md:p-6"><?php echo onyx_icon( $a[0], 'mx-auto h-12 w-12 text-accent' ); // phpcs:ignore ?><h3 class="mt-4 text-lg font-bold"><?php echo esc_html( $a[1] ); ?></h3><p class="mt-2 text-sm leading-relaxed text-muted-foreground"><?php echo esc_html( $a[2] ); ?></p></div>
			<?php endforeach; ?>
		</div>
	</section>

	<?php if ( $info ) : ?>
		<section class="mx-auto max-w-7xl px-4 py-14"><div class="grid gap-5">
			<?php foreach ( $info as $b ) : ?>
				<div class="rounded-2xl border bg-card p-6"><h3 class="text-lg font-bold"><?php echo esc_html( $b['title'] ); ?></h3><p class="mt-2 text-sm leading-relaxed text-muted-foreground"><?php echo esc_html( $b['body'] ); ?></p></div>
			<?php endforeach; ?>
		</div></section>
	<?php endif; ?>

	<?php if ( ! empty( $detail['faqs'] ) ) : ?>
		<section class="bg-secondary"><div class="mx-auto max-w-5xl px-4 py-14">
			<span class="text-xs font-bold uppercase tracking-widest text-accent">FAQs</span>
			<h2 class="mt-2 text-2xl font-black md:text-3xl">Frequently Asked Questions</h2>
			<div class="mt-8 space-y-3">
				<?php foreach ( $detail['faqs'] as $f ) : ?>
					<details class="onyx-faq overflow-hidden rounded-xl border bg-card"><summary class="flex w-full cursor-pointer items-center justify-between px-6 py-4 text-left text-sm font-semibold hover:bg-secondary"><?php echo esc_html( $f['q'] ); ?><?php echo onyx_icon( 'chevron-down', 'h-4 w-4 shrink-0 text-muted-foreground' ); // phpcs:ignore ?></summary><div class="border-t px-6 py-4 text-sm leading-relaxed text-muted-foreground"><?php echo esc_html( $f['a'] ); ?></div></details>
				<?php endforeach; ?>
			</div>
		</div></section>
	<?php endif; ?>

	<?php if ( $related ) : ?>
		<section class="mx-auto max-w-7xl px-4 py-14">
			<h2 class="mb-6 text-2xl font-black">Related products</h2>
			<div class="grid grid-cols-2 gap-2 md:grid-cols-3 md:gap-4 lg:grid-cols-4">
				<?php foreach ( $related as $rp ) { echo onyx_product_card( $rp ); } // phpcs:ignore ?>
			</div>
		</section>
	<?php endif; ?>
</div>
	<?php
endwhile;

get_footer();
