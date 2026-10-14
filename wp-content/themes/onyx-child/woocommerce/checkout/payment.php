<?php
/**
 * Checkout payment step – section 4 of the React checkout, plus the research-use confirmation
 * and the "Place order · £total" button. Re-rendered by WooCommerce on every order review update.
 *
 * Each crypto network is shown as its own option (like live); it selects the onyx_crypto gateway
 * and fills onyx_crypto_network.
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;

$gateways = WC()->payment_gateways() ? WC()->payment_gateways()->get_available_payment_gateways() : array();
$posted   = isset( $_POST['payment_method'] ) ? wc_clean( wp_unslash( $_POST['payment_method'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$net_post = isset( $_POST['onyx_crypto_network'] ) ? absint( $_POST['onyx_crypto_network'] ) : null; // phpcs:ignore WordPress.Security.NonceVerification
if ( null === $net_post && isset( $_POST['post_data'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
	parse_str( wp_unslash( $_POST['post_data'] ), $pd ); // phpcs:ignore
	$net_post = isset( $pd['onyx_crypto_network'] ) ? absint( $pd['onyx_crypto_network'] ) : null;
	$posted   = $posted ? $posted : ( $pd['payment_method'] ?? '' );
}
$options = array();
foreach ( $gateways as $gw ) {
	if ( 'onyx_crypto' === $gw->id && method_exists( $gw, 'get_wallets' ) ) {
		$w = $gw->get_wallets();
		// Live lists ERC20 before TRC20.
		uksort( $w, function ( $a, $b ) use ( $w ) {
			return strcmp( $w[ $a ]['label'], $w[ $b ]['label'] );
		} );
		foreach ( $w as $i => $wallet ) {
			$options[] = array( 'gw' => $gw->id, 'net' => (string) $i, 'label' => $wallet['label'] );
		}
	} else {
		$options[] = array( 'gw' => $gw->id, 'net' => '', 'label' => $gw->get_title() );
	}
}
$sel = 0;
foreach ( $options as $k => $o ) {
	if ( $o['gw'] === $posted && ( '' === $o['net'] || (string) $net_post === $o['net'] ) ) {
		$sel = $k;
		break;
	}
}
$total = WC()->cart ? WC()->cart->get_total( 'edit' ) : 0;
$block = onyx_vial_shortfall() > 0;
?>
<div id="payment" class="woocommerce-checkout-payment space-y-6">
	<section class="rounded-2xl border bg-card p-6">
		<h2 class="mb-4 flex items-center gap-2 text-lg font-bold"><span class="grid h-7 w-7 place-items-center rounded-full bg-primary text-xs text-primary-foreground">4</span><?php echo onyx_icon( 'coins', 'h-5 w-5' ); // phpcs:ignore ?>Payment</h2>
		<?php if ( $options ) : ?>
			<div class="space-y-2">
				<?php foreach ( $options as $k => $o ) : ?>
					<label class="onyx-opt flex cursor-pointer items-center gap-3 rounded-lg border p-4 transition <?php echo $k === $sel ? 'border-accent bg-accent/5' : 'hover:bg-secondary'; ?>">
						<input type="radio" name="payment_method" class="input-radio" value="<?php echo esc_attr( $o['gw'] ); ?>" data-onyx-net="<?php echo esc_attr( $o['net'] ); ?>" <?php checked( $k, $sel ); ?>>
						<span class="text-sm font-semibold"><?php echo esc_html( $o['label'] ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
			<input type="hidden" name="onyx_crypto_network" value="<?php echo esc_attr( $options[ $sel ]['net'] ); ?>">
		<?php else : ?>
			<p class="text-sm text-muted-foreground"><?php esc_html_e( 'No payment methods are available for your location.', 'onyx-child' ); ?></p>
		<?php endif; ?>
		<p class="mt-4 text-sm text-muted-foreground">The wallet address and a QR code will appear on the confirmation screen once your order is placed. You'll then submit your transaction hash or a receipt for verification.</p>
	</section>

	<div data-onyx-field="onyx_terms">
		<label class="flex cursor-pointer items-start gap-3 text-sm leading-relaxed">
			<input type="checkbox" name="onyx_terms" id="onyx_terms" value="1" class="mt-1 h-4 w-4 shrink-0" aria-invalid="false" <?php checked( ! empty( $_POST['onyx_terms'] ) ); // phpcs:ignore ?>>
			<span>I confirm that I am 21 years of age or older and that I am buying these products strictly for laboratory research use. I understand they are not intended for human or animal consumption. I accept the ONYX Research <a class="font-semibold text-accent hover:underline" href="<?php echo esc_url( home_url( '/terms-conditions/' ) ); ?>" target="_blank">terms and conditions</a> and <a class="font-semibold text-accent hover:underline" href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" target="_blank">privacy policy</a>, and I consent to receiving marketing communications from ONYX Research. <span class="text-red-600">*</span></span>
		</label>
		<p class="mt-1.5 text-xs text-muted-foreground" data-onyx-terms-hint>Please confirm this before placing your order.</p>
	</div>

	<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
	<button type="submit" name="woocommerce_checkout_place_order" id="place_order" value="Place order" class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-lg bg-primary py-4 text-base font-bold text-primary-foreground hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-60" <?php disabled( $block || ! $options ); ?>><?php echo onyx_icon( 'lock', 'h-4 w-4' ); // phpcs:ignore ?> Place order · £<?php echo esc_html( number_format( (float) $total, 2 ) ); ?></button>
</div>
