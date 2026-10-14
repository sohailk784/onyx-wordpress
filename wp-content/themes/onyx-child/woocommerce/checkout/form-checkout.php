<?php
/**
 * Checkout form – port of the React pages/Checkout.tsx (live onyxhealth.net/checkout).
 * Standard WooCommerce field names, nonce, AJAX review updates and gateways are kept.
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;

$co   = WC()->checkout();
$cart = WC()->cart;
$v    = function ( $k ) use ( $co ) {
	return esc_attr( (string) $co->get_value( $k ) );
};
$in    = 'w-full rounded-md border bg-background px-4 py-3 text-sm outline-none focus:border-accent focus:ring-2 focus:ring-accent/20';
$label = 'mb-1.5 block text-xs font-semibold uppercase tracking-wider text-muted-foreground';
$step  = function ( $n, $title, $icon = '' ) {
	return '<h2 class="mb-4 flex items-center gap-2 text-lg font-bold"><span class="grid h-7 w-7 place-items-center rounded-full bg-primary text-xs text-primary-foreground">' . (int) $n . '</span>' . ( $icon ? onyx_icon( $icon, 'h-5 w-5' ) : '' ) . esc_html( $title ) . '</h2>';
};
$field = function ( $key, $title, $span = false, $type = 'text', $extra = '' ) use ( $v, $in, $label ) {
	return sprintf(
		'<div class="%1$s" data-onyx-field="%2$s"><label for="%2$s" class="%3$s">%4$s</label><input type="%5$s" name="%2$s" id="%2$s" value="%6$s" class="%7$s" aria-invalid="false" %8$s></div>',
		$span ? 'sm:col-span-2' : '',
		esc_attr( $key ),
		$label,
		esc_html( $title ),
		esc_attr( $type ),
		$v( $key ),
		$in,
		$extra
	);
};
$country   = $co->get_value( 'billing_country' ) ? $co->get_value( 'billing_country' ) : 'GB';
$countries = WC()->countries->get_allowed_countries();
$countries = array_map( function ( $n ) { return trim( preg_replace( "/\\s*\\((UK|US)\\)\$/", "", html_entity_decode( $n ) ) ); }, $countries );
$dials     = onyx_dial_codes();
$phone     = (string) $co->get_value( 'billing_phone' );
$dial_cc   = 'GB';
$national  = $phone;
if ( 0 === strpos( $phone, '+' ) ) {
	$compact = preg_replace( '/[\s\-()]/', '', $phone );
	$best    = '';
	foreach ( $dials as $cc => $d ) {
		if ( 0 === strpos( $compact, $d[1] ) && strlen( $d[1] ) > strlen( $best ) ) {
			$best    = $d[1];
			$dial_cc = $cc;
		}
	}
	$national = $best ? substr( $compact, strlen( $best ) ) : ltrim( $phone, '+' );
}

do_action( 'woocommerce_before_checkout_form', $co );
?>
<div class="onyx-tw onyx-checkout"><div class="bg-secondary/40"><div class="mx-auto max-w-6xl px-4 py-10">
	<div class="mb-6 flex items-center gap-2 text-xs text-muted-foreground"><a class="hover:text-accent" href="<?php echo esc_url( wc_get_cart_url() ); ?>">Cart</a><span>›</span><span class="font-semibold text-foreground">Checkout</span><span>›</span><span>Confirmation</span></div>

	<form name="checkout" method="post" class="checkout woocommerce-checkout grid gap-8 lg:grid-cols-[minmax(0,1fr)_440px]" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" novalidate>
		<div class="space-y-6 onyx-co-main">
			<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
			<section class="rounded-2xl border bg-card p-6">
				<?php echo $step( 1, 'Contact' ); // phpcs:ignore ?>
				<div data-onyx-field="billing_email"><label for="billing_email" class="<?php echo esc_attr( $label ); ?>">Email address</label><input type="email" name="billing_email" id="billing_email" value="<?php echo $v( 'billing_email' ); // phpcs:ignore ?>" placeholder="you@example.com" autocomplete="email" class="<?php echo esc_attr( $in ); ?>" aria-invalid="false"></div>
			</section>

			<section class="rounded-2xl border bg-card p-6">
				<?php echo $step( 2, 'Shipping address' ); // phpcs:ignore ?>
				<div class="grid gap-4 sm:grid-cols-2">
					<?php
					echo $field( 'billing_first_name', 'First name', false, 'text', 'autocomplete="given-name"' ); // phpcs:ignore
					echo $field( 'billing_last_name', 'Last name', false, 'text', 'autocomplete="family-name"' ); // phpcs:ignore
					echo $field( 'billing_address_1', 'Address', true, 'text', 'autocomplete="address-line1"' ); // phpcs:ignore
					echo $field( 'billing_address_2', 'Apartment, suite, etc. (optional)', true, 'text', 'autocomplete="address-line2"' ); // phpcs:ignore
					echo $field( 'billing_city', 'City', false, 'text', 'autocomplete="address-level2"' ); // phpcs:ignore
					echo $field( 'billing_postcode', 'Postal code', false, 'text', 'autocomplete="postal-code"' ); // phpcs:ignore
					?>
					<div data-onyx-field="billing_country">
						<label for="billing_country" class="<?php echo esc_attr( $label ); ?>">Country</label>
						<div class="onyx-select relative flex w-full items-center gap-2 px-4 py-3 text-left text-sm outline-none transition focus-within:border-accent focus-within:ring-2 focus-within:ring-accent/20 border bg-background text-foreground rounded-md">
							<span class="fi fi-<?php echo esc_attr( strtolower( $country ) ); ?> h-4 w-5 shrink-0 rounded-[2px]" data-onyx-flag></span>
							<span class="flex-1 truncate" data-onyx-name><?php echo esc_html( html_entity_decode( $countries[ $country ] ?? '' ) ); ?></span>
							<?php echo onyx_icon( 'chevron-down', 'h-4 w-4 shrink-0 opacity-60' ); // phpcs:ignore ?>
							<select name="billing_country" id="billing_country" class="country_to_state onyx-select-native" autocomplete="country" aria-invalid="false">
								<?php foreach ( $countries as $cc => $name ) : ?>
									<option value="<?php echo esc_attr( $cc ); ?>" <?php selected( $country, $cc ); ?>><?php echo esc_html( html_entity_decode( $name ) ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<input type="hidden" name="billing_state" id="billing_state" value="<?php echo $v( 'billing_state' ); // phpcs:ignore ?>">
					</div>
					<div data-onyx-field="billing_phone">
						<label for="onyx_phone_national" class="<?php echo esc_attr( $label ); ?>">Phone</label>
						<div class="onyx-phone flex w-full items-stretch overflow-hidden text-sm transition focus-within:border-accent focus-within:ring-2 focus-within:ring-accent/20 border bg-background text-foreground rounded-md">
							<div class="relative flex shrink-0 items-center gap-1.5 border-r border-inherit px-3 py-3 transition hover:bg-black/[0.03]">
								<span class="fi fi-<?php echo esc_attr( strtolower( $dial_cc ) ); ?> h-4 w-5 shrink-0 rounded-[2px]" data-onyx-dial-flag></span>
								<span class="font-medium tabular-nums" data-onyx-dial-code><?php echo esc_html( $dials[ $dial_cc ][1] ?? '+44' ); ?></span>
								<?php echo onyx_icon( 'chevron-down', 'h-4 w-4 opacity-60' ); // phpcs:ignore ?>
								<select class="onyx-select-native" data-onyx-dial aria-label="Country code">
									<?php foreach ( $dials as $cc => $d ) : ?>
										<option value="<?php echo esc_attr( $cc ); ?>" data-dial="<?php echo esc_attr( $d[1] ); ?>" <?php selected( $dial_cc, $cc ); ?>><?php echo esc_html( $d[0] . ' (' . $d[1] . ')' ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<input type="tel" id="onyx_phone_national" inputmode="tel" autocomplete="tel-national" placeholder="7911 123456" value="<?php echo esc_attr( $national ); ?>" class="w-full flex-1 bg-transparent px-3 py-3 outline-none placeholder:text-muted-foreground" aria-invalid="false">
						</div>
						<input type="hidden" name="billing_phone" id="billing_phone" value="<?php echo esc_attr( $phone ); ?>">
					</div>
				</div>
			</section>
			<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

			<section class="rounded-2xl border bg-card p-6">
				<?php echo $step( 3, 'Shipping method', 'truck' ); // phpcs:ignore ?>
				<?php echo onyx_checkout_shipping_html(); // phpcs:ignore ?>
			</section>

			<?php woocommerce_checkout_payment(); ?>
		</div>

		<aside class="h-fit space-y-4 rounded-2xl border bg-card p-6 lg:sticky lg:top-24">
			<h2 class="text-lg font-bold">Order summary</h2>
			<ul class="divide-y">
				<?php foreach ( $cart->get_cart() as $item ) :
					$p = $item['data'];
					if ( ! $p || ! $p->exists() || $item['quantity'] <= 0 ) {
						continue;
					}
					$parent = $item['variation_id'] ? wc_get_product( $item['product_id'] ) : $p;
					$img    = wp_get_attachment_image_url( $p->get_image_id() ?: $parent->get_image_id(), 'woocommerce_thumbnail' );
					$var    = $item['variation_id'] ? wc_get_formatted_variation( $p, true, false, false ) : '';
					?>
					<li class="flex items-center gap-3 py-3">
						<div class="relative h-16 w-16 shrink-0 overflow-hidden rounded-md border bg-white"><?php if ( $img ) : ?><img alt="<?php echo esc_attr( $parent->get_name() ); ?>" loading="lazy" class="h-full w-full object-contain p-1" src="<?php echo esc_url( $img ); ?>"><?php endif; ?><span class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-primary px-1 text-[10px] font-bold text-primary-foreground"><?php echo (int) $item['quantity']; ?></span></div>
						<div class="min-w-0 flex-1">
							<div class="truncate text-sm font-semibold"><?php echo esc_html( $parent->get_name() ); ?></div>
							<?php if ( $var ) : ?><div class="text-xs text-muted-foreground"><?php echo esc_html( $var ); ?></div><?php endif; ?>
							<div class="text-xs text-muted-foreground"><?php echo onyx_price( wc_get_price_to_display( $p ) ); // phpcs:ignore ?> each</div>
						</div>
						<div class="text-sm font-bold"><?php echo onyx_price( $item['line_subtotal'] ); // phpcs:ignore ?></div>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( wc_coupons_enabled() ) : ?>
				<div>
					<div class="flex gap-2"><input placeholder="Discount code" id="onyx_coupon_code" class="<?php echo esc_attr( $in ); ?>" autocomplete="off"><button type="button" data-onyx-apply-coupon class="rounded-md border px-4 text-sm font-semibold hover:bg-secondary disabled:opacity-60">Apply</button></div>
					<p class="mt-1.5 text-xs font-medium text-red-600" data-onyx-coupon-error hidden></p>
				</div>
			<?php endif; ?>
			<?php echo onyx_checkout_totals_html(); // phpcs:ignore ?>
			<div class="rounded-2xl bg-[#f5f6f8] px-4 py-5 sm:px-5"><div class="flex items-start gap-3.5"><span aria-hidden="true" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#6b99d8] text-xl font-bold leading-none text-white">i</span><div class="min-w-0 flex-1"><h3 class="text-sm font-bold leading-5 text-slate-950">Customs &amp; Import Charges</h3><p class="mt-1.5 text-xs leading-[1.7] text-slate-600">All customs duties, taxes, and clearance fees are the customer's responsibility. Onyx Health is not liable for customs-related delays, losses, or damages beyond our control, subject to applicable law.</p></div></div></div>
			<div class="flex items-center gap-3 px-3 py-2"><svg viewBox="0 0 24 24" class="h-6 w-6 shrink-0 text-emerald-600" fill="none" aria-hidden="true"><path d="M7 10V7a5 5 0 0 1 10 0v3" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path><rect x="4" y="10" width="16" height="12" rx="2" fill="currentColor"></rect><path d="M12 15v3" stroke="white" stroke-width="1.8" stroke-linecap="round"></path></svg><span aria-hidden="true" class="h-10 w-px shrink-0 bg-slate-200"></span><p class="min-w-0 text-xs leading-[1.65] text-slate-500">Secure 256-bit SSL encrypted checkout. Your payment details are never stored.</p></div>
			<div class="border-t pt-5">
				<h3 class="text-lg font-bold">Why Customers Trust ONYX Research</h3>
				<ul class="mt-5 space-y-5">
					<li class="flex gap-3"><?php echo onyx_icon( 'truck', 'h-7 w-7 shrink-0 text-accent onyx-sw15' ); // phpcs:ignore ?><div><div class="text-sm font-bold">Fast Shipping</div><p class="mt-0.5 text-sm text-muted-foreground">Every order is dispatched promptly and sent with fully tracked express delivery.</p></div></li>
					<li class="flex gap-3"><?php echo onyx_icon( 'shield-check', 'h-7 w-7 shrink-0 text-accent onyx-sw15' ); // phpcs:ignore ?><div><div class="text-sm font-bold">Quality Assured Products</div><p class="mt-0.5 text-sm text-muted-foreground">Each product is put through rigorous quality testing, so you consistently receive compounds of the highest standard.</p></div></li>
					<li class="flex gap-3"><?php echo onyx_icon( 'headset', 'h-7 w-7 shrink-0 text-accent onyx-sw15' ); // phpcs:ignore ?><div><div class="text-sm font-bold">Customer Service</div><p class="mt-0.5 text-sm text-muted-foreground">Have a question? Our team is always happy to help. Just reach out at <a href="mailto:sales@onyxhealth.net" class="font-medium text-accent hover:underline">sales@onyxhealth.net</a> and we'll get you whatever you need.</p></div></li>
				</ul>
			</div>
		</aside>
	</form>
</div></div></div>
<?php do_action( 'woocommerce_after_checkout_form', $co ); ?>
