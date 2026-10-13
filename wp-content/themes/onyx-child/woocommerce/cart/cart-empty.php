<?php
/**
 * Empty cart – port of the React pages/Cart.tsx empty state.
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="onyx-tw"><div class="mx-auto max-w-3xl px-4 py-20 text-center">
	<h1 class="text-3xl font-black">Your cart is empty</h1>
	<p class="mt-2 text-muted-foreground">Browse our catalog and find what your research needs.</p>
	<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="mt-6 inline-block rounded-md bg-primary px-6 py-3 text-sm font-bold text-primary-foreground hover:bg-primary/90">Shop peptides</a>
</div></div>
