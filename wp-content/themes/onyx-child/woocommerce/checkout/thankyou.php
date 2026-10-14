<?php
/**
 * Order received – mirrors the React "Your order has been created" screen (crypto orders awaiting
 * payment, with PaymentProofForm) and PaymentSuccess.tsx (paid orders).
 *
 * @package onyx-child
 * @var WC_Order|false $order
 */

defined( 'ABSPATH' ) || exit;

$shop = home_url( '/peptides/' );
?>
<div class="onyx-tw onyx-thankyou">
<?php
if ( ! $order ) :
	?>
	<div class="mx-auto max-w-xl px-4 py-16 text-center">
		<h1 class="text-3xl font-black">Thank you</h1>
		<p class="mt-2 text-muted-foreground">Your order has been received.</p>
		<a href="<?php echo esc_url( $shop ); ?>" class="mt-8 inline-block rounded-md bg-primary px-6 py-3 text-sm font-bold text-primary-foreground hover:bg-primary/90">Continue shopping</a>
	</div>
	<?php
elseif ( $order->has_status( 'failed' ) ) :
	?>
	<div class="mx-auto max-w-xl px-4 py-16 text-center">
		<h1 class="text-3xl font-black">Payment failed</h1>
		<p class="mt-2 text-muted-foreground">Unfortunately your order cannot be processed as the payment was declined. Please try again.</p>
		<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="mt-8 inline-block rounded-md bg-primary px-6 py-3 text-sm font-bold text-primary-foreground hover:bg-primary/90">Try again</a>
	</div>
	<?php
elseif ( $order->is_paid() ) :
	?>
	<div class="mx-auto max-w-2xl px-4 py-16 text-center">
		<div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-emerald-100 text-3xl text-emerald-700" aria-hidden="true">✓</div>
		<h1 class="mt-6 text-3xl font-black">Payment successful</h1>
		<p class="mt-3 text-muted-foreground">Thank you. Your payment has been received and your order is being processed.</p>
		<p class="mt-2 text-sm text-muted-foreground">You will receive order updates using the contact details provided at checkout.</p>
		<div class="mt-8 flex flex-wrap justify-center gap-3">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="rounded-md bg-primary px-5 py-3 font-semibold text-primary-foreground">Return home</a>
			<a href="<?php echo esc_url( $shop ); ?>" class="rounded-md border px-5 py-3 font-semibold">Continue browsing</a>
		</div>
	</div>
	<?php
else :
	$is_crypto = 'onyx_crypto' === $order->get_payment_method();
	$address   = (string) $order->get_meta( '_onyx_crypto_address' );
	$net_label = (string) $order->get_meta( '_onyx_crypto_network' );
	$net_long  = false !== stripos( $net_label, 'TRC' ) ? 'Tron (TRC20)' : ( false !== stripos( $net_label, 'ERC' ) ? 'Ethereum (ERC20)' : $net_label );
	$txid      = (string) $order->get_meta( '_onyx_proof_txid' );
	$file      = (string) $order->get_meta( '_onyx_proof_file' );
	$pending   = $txid || $file;
	$errors    = wc_get_notices( 'error' );
	wc_clear_notices();
	?>
	<div class="mx-auto max-w-xl px-4 py-16 text-center">
		<div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-emerald-100 text-emerald-700"><?php echo onyx_icon( 'check', 'h-8 w-8' ); // phpcs:ignore ?></div>
		<h1 class="mt-6 text-3xl font-black">Your order has been created</h1>
		<p class="mt-2 text-muted-foreground">Thank you. Your order <strong><?php echo esc_html( $order->get_order_number() ); ?></strong> has been received — a confirmation email is on its way to <?php echo esc_html( $order->get_billing_email() ); ?>.<?php echo $is_crypto ? ' Complete step 2 below to send your payment and submit it for verification.' : ' Your payment is not complete yet.'; ?></p>

		<?php if ( $is_crypto ) : ?>
		<div class="mt-8 text-left">
			<div class="space-y-5">
				<?php if ( $address ) : ?>
				<div class="rounded-xl border bg-card p-5">
					<div class="text-xs font-semibold uppercase tracking-wider text-muted-foreground"><?php echo esc_html( $net_label ); ?> — send payment to</div>
					<div class="mt-3 flex flex-wrap items-center gap-4">
						<div class="rounded-lg border bg-white p-2"><div class="onyx-crypto-qr h-[112px] w-[112px]" data-address="<?php echo esc_attr( $address ); ?>"></div></div>
						<div class="min-w-0 flex-1">
							<div class="flex items-center gap-2">
								<code class="break-all rounded-md bg-secondary px-2.5 py-1.5 text-xs font-medium"><?php echo esc_html( $address ); ?></code>
								<button type="button" class="onyx-copy-addr grid h-8 w-8 shrink-0 place-items-center rounded-md border hover:bg-secondary" data-address="<?php echo esc_attr( $address ); ?>" aria-label="Copy wallet address"><span data-i="copy"><?php echo onyx_icon( 'copy', 'h-3.5 w-3.5' ); // phpcs:ignore ?></span><span data-i="check" hidden><?php echo onyx_icon( 'check', 'h-3.5 w-3.5 text-emerald-600' ); // phpcs:ignore ?></span></button>
							</div>
							<p class="mt-2 text-sm font-bold">Amount due: £<?php echo esc_html( number_format( (float) $order->get_total(), 2 ) ); ?> <?php echo esc_html( $order->get_currency() ); ?></p>
							<p class="mt-1 text-xs text-muted-foreground">Send the exact amount in USDT on the <?php echo esc_html( $net_long ); ?> network to the wallet address shown at checkout. Your order ships once payment is verified by our team, usually within 24 hours.</p>
						</div>
					</div>
				</div>
				<?php endif; ?>

				<?php if ( $pending ) : ?>
				<div class="rounded-xl border bg-card p-4">
					<div class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Submitted proof</div>
					<ul class="mt-2 space-y-2">
						<li class="flex items-center justify-between gap-3 text-xs">
							<span class="truncate text-muted-foreground"><?php echo esc_html( $txid ? 'Tx: ' . $txid : 'Receipt uploaded' ); ?></span>
							<span class="rounded-full bg-amber-100 px-2 py-0.5 font-semibold text-amber-700">pending</span>
						</li>
					</ul>
				</div>
				<div class="flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
					<?php echo onyx_icon( 'check', 'h-4 w-4 shrink-0' ); // phpcs:ignore ?>
					Your payment proof is under review — we'll notify you once it's verified. No need to submit again.
				</div>
				<?php else : ?>
				<form method="post" enctype="multipart/form-data" class="onyx-proof-form rounded-xl border bg-card p-5">
					<?php wp_nonce_field( 'onyx_proof_' . $order->get_id(), 'onyx_proof_nonce' ); ?>
					<input type="hidden" name="onyx_proof_order" value="<?php echo esc_attr( $order->get_id() ); ?>">
					<input type="hidden" name="onyx_proof_key" value="<?php echo esc_attr( $order->get_order_key() ); ?>">
					<h3 class="text-sm font-bold">Submit your payment for verification</h3>
					<p class="mt-1 text-xs text-muted-foreground">Once you've sent the payment, provide the transaction hash and/or upload a screenshot of the receipt so our team can verify it.</p>
					<label for="onyx_proof_txid" class="mb-1.5 mt-4 block text-xs font-semibold uppercase tracking-wider text-muted-foreground">Transaction hash</label>
					<input id="onyx_proof_txid" name="onyx_proof_txid" maxlength="200" placeholder="0x… or Tron tx hash" class="w-full rounded-md border bg-background px-4 py-2.5 text-sm outline-none focus:border-accent focus:ring-2 focus:ring-accent/20">
					<label class="mb-1.5 mt-4 block text-xs font-semibold uppercase tracking-wider text-muted-foreground">Or upload a receipt screenshot</label>
					<label class="flex cursor-pointer items-center gap-2 rounded-md border border-dashed px-4 py-3 text-sm text-muted-foreground hover:bg-secondary">
						<?php echo onyx_icon( 'cloud-upload', 'h-4 w-4' ); // phpcs:ignore ?>
						<span data-onyx-file-name>Choose an image (JPG, PNG or WEBP, max 5MB)</span>
						<input type="file" name="onyx_proof_file" accept="image/jpeg,image/png,image/webp" class="hidden">
					</label>
					<p class="mt-2 text-xs font-medium text-red-600" data-onyx-proof-error <?php echo $errors ? '' : 'hidden'; ?>><?php echo $errors ? esc_html( wp_strip_all_tags( $errors[0]['notice'] ) ) : ''; ?></p>
					<button type="submit" class="mt-4 w-full rounded-md bg-primary py-2.5 text-sm font-bold text-primary-foreground hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-60">Submit for verification</button>
				</form>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>

		<a href="<?php echo esc_url( $shop ); ?>" class="mt-8 inline-block rounded-md bg-primary px-6 py-3 text-sm font-bold text-primary-foreground hover:bg-primary/90">Continue shopping</a>
	</div>
	<?php
	if ( $is_crypto && $address ) {
		wp_enqueue_script( 'onyx-qrcode' );
		wp_add_inline_script(
			'onyx-qrcode',
			"document.querySelectorAll('.onyx-crypto-qr').forEach(function(el){new QRCode(el,{text:el.dataset.address,width:112,height:112});});"
			. "document.querySelectorAll('.onyx-copy-addr').forEach(function(b){b.addEventListener('click',function(){navigator.clipboard&&navigator.clipboard.writeText(b.dataset.address);b.querySelector('[data-i=copy]').hidden=true;b.querySelector('[data-i=check]').hidden=false;setTimeout(function(){b.querySelector('[data-i=copy]').hidden=false;b.querySelector('[data-i=check]').hidden=true;},2000);});});"
			. "document.querySelectorAll('.onyx-proof-form').forEach(function(f){var i=f.querySelector('input[type=file]'),n=f.querySelector('[data-onyx-file-name]'),e=f.querySelector('[data-onyx-proof-error]');i.addEventListener('change',function(){if(i.files[0])n.textContent=i.files[0].name;});f.addEventListener('submit',function(ev){var t=f.querySelector('#onyx_proof_txid').value.trim(),fl=i.files[0];if(!t&&!fl){ev.preventDefault();e.textContent='Enter a transaction hash or upload a receipt.';e.hidden=false;return;}if(fl&&fl.size>5*1024*1024){ev.preventDefault();e.textContent='Receipt must be 5MB or smaller.';e.hidden=false;return;}f.querySelector('button[type=submit]').disabled=true;f.querySelector('button[type=submit]').textContent='Submitting…';});});"
		);
	}
endif;
?>
</div>
<?php
if ( $order ) {
	do_action( 'woocommerce_thankyou', $order->get_id() );
}
