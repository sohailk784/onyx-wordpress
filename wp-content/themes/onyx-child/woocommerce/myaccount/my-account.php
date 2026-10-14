<?php
/**
 * Account dashboard – port of React Profile.tsx (live /profile): profile card, tab bar, tab content.
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;

$u    = wp_get_current_user();
$name = trim( $u->first_name . ' ' . $u->last_name );
$role = ! empty( $u->roles ) ? reset( $u->roles ) : 'customer';
global $wp;
$cur = 'orders';
foreach ( array( 'edit-address', 'edit-account', 'view-order', 'orders' ) as $ep ) {
	if ( isset( $wp->query_vars[ $ep ] ) ) {
		$cur = 'view-order' === $ep ? 'orders' : $ep;
		break;
	}
}
?>
<div class="onyx-tw onyx-profile"><div class="mx-auto max-w-5xl px-4 py-10">
	<div class="mb-8 flex flex-wrap items-center justify-between gap-4 rounded-2xl border bg-card p-6">
		<div class="flex items-center gap-4">
			<div class="grid h-14 w-14 place-items-center rounded-full bg-accent/15 text-accent"><?php echo onyx_icon( 'user', 'h-7 w-7' ); // phpcs:ignore ?></div>
			<div>
				<h1 class="text-xl font-black"><?php echo esc_html( $name ? $name : $u->user_email ); ?></h1>
				<p class="text-sm text-muted-foreground"><?php echo esc_html( $u->user_email ); ?></p>
				<span class="mt-1 inline-block rounded-full bg-secondary px-2.5 py-0.5 text-xs font-semibold capitalize text-muted-foreground"><?php echo esc_html( $role ); ?></span>
			</div>
		</div>
		<a href="<?php echo esc_url( wc_logout_url() ); ?>" class="rounded-md border px-4 py-2 text-sm font-semibold hover:bg-secondary">Log out</a>
	</div>
	<nav class="mb-6 flex gap-2 overflow-x-auto border-b" aria-label="Account">
		<?php
		foreach ( wc_get_account_menu_items() as $ep => $label ) {
			if ( 'customer-logout' === $ep ) {
				continue;
			}
			$on = $ep === $cur;
			printf(
				'<a href="%s" class="whitespace-nowrap border-b-2 px-4 py-3 text-sm font-bold transition %s">%s</a>',
				esc_url( wc_get_account_endpoint_url( $ep ) ),
				$on ? 'border-accent text-accent' : 'border-transparent text-muted-foreground hover:text-foreground',
				esc_html( $label )
			);
		}
		?>
	</nav>
	<div class="woocommerce-MyAccount-content onyx-account-content">
		<?php wc_print_notices(); ?>
		<?php do_action( 'woocommerce_account_content' ); ?>
	</div>
</div></div>
