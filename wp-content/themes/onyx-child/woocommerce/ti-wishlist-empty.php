<?php
/**
 * Empty wishlist – port of React Wishlist.tsx empty state.
 *
 * @version 2.5.2
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="tinv-wishlist woocommerce onyx-tw onyx-wishlist"><div class="mx-auto max-w-7xl px-4 py-10">
	<h1 class="mb-6 text-3xl font-black">My Wishlist</h1>
	<div class="rounded-xl border bg-card p-12 text-center">
		<p class="text-muted-foreground">Your wishlist is empty.</p>
		<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="mt-4 inline-block text-accent hover:underline">Browse peptides</a>
	</div>
</div></div>
