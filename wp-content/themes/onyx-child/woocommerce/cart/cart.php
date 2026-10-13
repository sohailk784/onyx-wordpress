<?php
/**
 * Cart – port of the React pages/Cart.tsx (line items, qty stepper, order summary).
 * Quantity buttons submit the standard WooCommerce cart form, so all cart hooks still run.
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );
?>
<div class="onyx-tw onyx-cart"><div class="mx-auto max-w-7xl px-4 py-10">
	<h1 class="mb-6 text-3xl font-black">Shopping Cart</h1>
	<div class="grid gap-8 lg:grid-cols-[1fr_360px]">
		<form class="space-y-3 woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
			<?php foreach ( WC()->cart->get_cart() as $key => $item ) :
				$p = $item['data'];
				if ( ! $p || ! $p->exists() || $item['quantity'] <= 0 ) {
					continue;
				}
				$parent = $item['variation_id'] ? wc_get_product( $item['product_id'] ) : $p;
				$url    = $parent->get_permalink();
				$img    = wp_get_attachment_image_url( $p->get_image_id() ?: $parent->get_image_id(), 'woocommerce_thumbnail' );
				$label  = $item['variation_id'] ? wc_get_formatted_variation( $p, true, false, false ) : '';
				?>
				<div class="grid grid-cols-[80px_1fr_auto] items-center gap-4 rounded-xl border bg-card p-4">
					<a href="<?php echo esc_url( $url ); ?>" class="aspect-square overflow-hidden rounded-md bg-white"><?php if ( $img ) : ?><img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $parent->get_name() ); ?>" loading="lazy" class="h-full w-full object-contain p-1"><?php endif; ?></a>
					<div class="min-w-0">
						<a href="<?php echo esc_url( $url ); ?>" class="font-semibold hover:text-accent"><?php echo esc_html( $parent->get_name() ); ?></a>
						<?php if ( $label ) : ?><div class="text-xs text-muted-foreground"><?php echo esc_html( $label ); ?></div><?php endif; ?>
						<div class="text-sm text-muted-foreground"><?php echo onyx_price( $p->get_price() ); // phpcs:ignore ?> each</div>
						<div class="mt-2 flex items-center gap-2">
							<div class="flex items-center rounded-md border">
								<button type="submit" name="update_cart" value="1" data-onyx-cart-step="-1" class="grid h-8 w-8 place-items-center hover:bg-secondary"><?php echo onyx_icon( 'minus', 'h-3 w-3' ); // phpcs:ignore ?></button>
								<span class="w-10 text-center text-sm font-semibold"><?php echo (int) $item['quantity']; ?></span>
								<input type="hidden" name="cart[<?php echo esc_attr( $key ); ?>][qty]" value="<?php echo (int) $item['quantity']; ?>" data-onyx-cart-qty>
								<button type="submit" name="update_cart" value="1" data-onyx-cart-step="1" class="grid h-8 w-8 place-items-center hover:bg-secondary"><?php echo onyx_icon( 'plus', 'h-3 w-3' ); // phpcs:ignore ?></button>
							</div>
							<a href="<?php echo esc_url( wc_get_cart_remove_url( $key ) ); ?>" aria-label="Remove" class="grid h-8 w-8 cursor-pointer place-items-center rounded-md text-muted-foreground hover:bg-secondary hover:text-destructive"><?php echo onyx_icon( 'trash-2', 'h-4 w-4' ); // phpcs:ignore ?></a>
						</div>
					</div>
					<div class="text-right font-bold"><?php echo onyx_price( $item['line_total'] + $item['line_tax'] ); // phpcs:ignore ?></div>
				</div>
			<?php endforeach; ?>
			<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
			<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'onyx_clear_cart', 1, wc_get_cart_url() ), 'onyx_clear_cart' ) ); ?>" class="text-sm text-muted-foreground hover:text-destructive">Clear cart</a>
		</form>
		<aside class="h-fit rounded-xl border bg-card p-6">
			<h2 class="text-lg font-bold">Order summary</h2>
			<dl class="mt-4 space-y-2 text-sm"><div class="flex justify-between"><dt>Subtotal</dt><dd><?php echo onyx_price( WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax() ); // phpcs:ignore ?></dd></div></dl>
			<p class="mt-2 text-xs text-muted-foreground">Shipping is calculated at checkout based on your country.</p>
			<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="mt-6 block w-full rounded-md bg-primary py-3 text-center text-sm font-bold text-primary-foreground hover:bg-primary/90">Checkout</a>
			<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="mt-3 block text-center text-sm text-muted-foreground hover:text-accent">Continue shopping</a>
		</aside>
	</div>
</div></div>
<script>
document.querySelectorAll('[data-onyx-cart-step]').forEach(function(b){b.addEventListener('click',function(){var i=b.parentNode.querySelector('[data-onyx-cart-qty]');i.value=Math.max(0,(+i.value||0)+(+b.dataset.onyxCartStep));});});
</script>
<?php
do_action( 'woocommerce_after_cart' );
