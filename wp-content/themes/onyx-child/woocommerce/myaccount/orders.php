<?php
/**
 * Orders tab – port of React Profile.tsx OrdersTab.
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;

if ( ! $has_orders ) : ?>
	<div class="rounded-xl border bg-card p-12 text-center">
		<?php echo onyx_icon( 'package', 'mx-auto h-10 w-10 text-muted-foreground' ); // phpcs:ignore ?>
		<p class="mt-4 text-muted-foreground">You haven't placed any orders yet.</p>
		<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="mt-4 inline-block text-accent hover:underline">Browse peptides</a>
	</div>
<?php else : ?>
	<div class="overflow-x-auto rounded-xl border bg-card">
		<table class="w-full text-sm">
			<thead class="bg-secondary text-left text-xs uppercase tracking-wide text-muted-foreground"><tr>
				<th class="px-5 py-3">Order</th><th class="px-5 py-3">Date</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Total</th><th class="px-5 py-3"></th>
			</tr></thead>
			<tbody class="divide-y">
			<?php
			foreach ( $customer_orders->orders as $o ) :
				$o = wc_get_order( $o );
				if ( ! $o ) {
					continue;
				}
				$paid = $o->is_paid();
				?>
				<tr class="transition hover:bg-secondary/60">
					<td class="px-5 py-4 font-semibold">#<?php echo esc_html( $o->get_order_number() ); ?></td>
					<td class="px-5 py-4 text-muted-foreground"><?php echo esc_html( wc_format_datetime( $o->get_date_created(), 'j M Y' ) ); ?></td>
					<td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-bold capitalize <?php echo $paid ? 'bg-accent/15 text-accent' : 'bg-secondary text-muted-foreground'; ?>"><?php echo esc_html( wc_get_order_status_name( $o->get_status() ) ); ?></span></td>
					<td class="px-5 py-4 text-right font-bold"><?php echo wp_kses_post( $o->get_formatted_order_total() ); ?></td>
					<td class="px-5 py-4 text-right text-xs font-semibold text-accent"><a class="hover:underline" href="<?php echo esc_url( $o->get_view_order_url() ); ?>">View</a></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
		<div class="mt-4 flex justify-between text-sm font-semibold">
			<?php if ( 1 !== $current_page ) : ?><a class="text-accent hover:underline" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>">&larr; Previous</a><?php endif; ?>
			<?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?><a class="ml-auto text-accent hover:underline" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>">Next &rarr;</a><?php endif; ?>
		</div>
	<?php endif; ?>
<?php endif; ?>
