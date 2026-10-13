<?php
/**
 * Site footer – port of the React components/layout/Footer.tsx.
 * Link columns come from the "Footer: Help" / "Footer: Policies" menus when assigned.
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;

$u    = fn( $p ) => home_url( $p );
$help = onyx_menu_tree( 'help' ) ?: array_map( fn( $a ) => array( 'title' => $a[0], 'url' => home_url( $a[1] ) ), array( array( 'All Peptides', '/peptides/' ), array( 'Blends', '/peptides/blends/' ), array( 'Essentials', '/peptides/plans/' ), array( 'FAQ', '/faq/' ), array( 'Check Code', '/check-code/' ), array( 'Payment & Delivery', '/payment-delivery/' ), array( 'Returns', '/product-returns/' ), array( 'Contact', '/contact/' ), array( 'Wholesale Application', '/wholesale-application/' ) ) );
$pol  = onyx_menu_tree( 'policies' ) ?: array_map( fn( $a ) => array( 'title' => $a[0], 'url' => home_url( $a[1] ) ), array( array( 'Privacy Policy', '/privacy-policy/' ), array( 'Terms & Conditions', '/terms-conditions/' ), array( 'Refund Policy', '/refund-policy/' ), array( 'Shipping & Delivery Policy', '/shipping-policy/' ), array( 'Cookies Policy', '/cookies-policy/' ), array( 'Accessibility Policy', '/accessibility-policy/' ) ) );
$tiktok = '<svg viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 opacity-80 transition group-hover:text-accent group-hover:opacity-100" aria-hidden="true"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>';
$social = array(
	array( 'https://www.youtube.com/@Onyxhealth._net', 'YouTube', onyx_icon( 'youtube', 'h-5 w-5 opacity-80 transition group-hover:text-accent group-hover:opacity-100' ) ),
	array( 'https://www.instagram.com/onyx_h_ealth/', 'Instagram', onyx_icon( 'instagram', 'h-5 w-5 opacity-80 transition group-hover:text-accent group-hover:opacity-100' ) ),
	array( 'https://www.tiktok.com/@onyxhealthonyx', 'TikTok', $tiktok ),
	array( 'https://www.facebook.com/profile.php?id=61593091294487', 'Facebook', onyx_icon( 'facebook', 'h-5 w-5 opacity-80 transition group-hover:text-accent group-hover:opacity-100' ) ),
);
$wa_icon = '<svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>';
?>
<div class="onyx-tw" style="display:contents">
<footer class="onyx-footer relative overflow-hidden bg-gradient-to-br from-primary to-[#0a1733] text-primary-foreground">
	<div aria-hidden="true" class="pointer-events-none absolute -left-40 -top-24 h-80 w-80 rounded-full bg-accent/20 blur-[150px]"></div>
	<div aria-hidden="true" class="pointer-events-none absolute -right-40 bottom-0 h-80 w-80 rounded-full bg-sky-500/10 blur-[150px]"></div>
	<div aria-hidden="true" class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-accent/50 to-transparent"></div>
	<div class="relative mx-auto grid max-w-7xl grid-cols-1 gap-7 px-4 py-12 max-md:[&>div:not(:last-child)]:border-b max-md:[&>div:not(:last-child)]:border-white/10 max-md:[&>div:not(:last-child)]:pb-7 md:grid-cols-2 md:gap-10 md:py-16 lg:flex lg:flex-row lg:justify-between lg:gap-8">
		<div class="lg:w-72 lg:shrink-0">
			<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/img/logo-onyx-white.webp' ); ?>" alt="ONYX Research" width="410" height="156" loading="lazy" class="h-12 w-auto">
			<p class="mt-3 text-sm opacity-80">Premium research peptides, discreetly dispatched across Europe. For laboratory research only.</p>
			<div class="mt-5 flex items-center gap-2.5">
				<?php foreach ( $social as $s ) : ?>
					<a href="<?php echo esc_url( $s[0] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $s[1] ); ?>" class="group grid h-10 w-10 place-items-center rounded-xl border border-white/20 bg-white/[0.03] backdrop-blur-sm transition hover:-translate-y-0.5 hover:border-accent hover:bg-accent/10 hover:shadow-[0_0_16px_-2px] hover:shadow-accent/50"><?php echo $s[2]; // phpcs:ignore ?></a>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="lg:w-72 lg:shrink-0">
			<h4 class="mb-3 flex items-center gap-2 text-sm font-bold uppercase tracking-wider"><?php echo onyx_icon( 'globe', 'h-4 w-4 shrink-0 text-accent' ); // phpcs:ignore ?>Research Enquiries</h4>
			<div class="text-sm font-bold">Middle East • United Kingdom • Europe</div>
			<p class="mt-2.5 text-sm leading-relaxed opacity-80">For laboratory research, analytical documentation, and business enquiries, contact our team.</p>
			<a href="https://wa.me/447404932343" target="_blank" rel="noopener noreferrer" class="group mt-4 flex items-center gap-3 rounded-xl border border-white/15 bg-white/[0.03] p-3 backdrop-blur-sm transition hover:border-[#25d366]/60 hover:bg-[#25d366]/10">
				<span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-[#25d366]/15 text-[#25d366] ring-1 ring-inset ring-[#25d366]/25 transition group-hover:bg-[#25d366] group-hover:text-white"><?php echo $wa_icon; // phpcs:ignore ?></span>
				<span class="min-w-0"><span class="block text-[10px] font-bold uppercase tracking-wider opacity-70">WhatsApp</span><span class="mt-0.5 block text-sm font-black tracking-tight transition group-hover:text-[#25d366]">+44 7404 932343</span></span>
			</a>
			<p class="mt-4 border-t border-white/10 pt-3 text-sm leading-relaxed opacity-80">Serving business enquiries across the UK, Europe &amp; Middle East.</p>
		</div>
		<div>
			<h4 class="mb-3 flex items-center gap-2 text-sm font-bold uppercase tracking-wider"><?php echo onyx_icon( 'circle-help', 'h-4 w-4 shrink-0 text-accent' ); // phpcs:ignore ?>Help</h4>
			<ul class="space-y-2 text-sm opacity-80"><?php foreach ( $help as $l ) : ?><li><a href="<?php echo esc_url( $l['url'] ); ?>" class="hover:text-accent"><?php echo esc_html( $l['title'] ); ?></a></li><?php endforeach; ?></ul>
		</div>
		<div>
			<h4 class="mb-3 flex items-center gap-2 text-sm font-bold uppercase tracking-wider"><?php echo onyx_icon( 'file-text', 'h-4 w-4 shrink-0 text-accent' ); // phpcs:ignore ?>Policies</h4>
			<ul class="space-y-2 text-sm opacity-80"><?php foreach ( $pol as $l ) : ?><li><a href="<?php echo esc_url( $l['url'] ); ?>" class="hover:text-accent"><?php echo esc_html( $l['title'] ); ?></a></li><?php endforeach; ?></ul>
		</div>
	</div>
	<div class="relative border-t border-white/10 px-4 py-6">
		<div class="mx-auto max-w-5xl space-y-2 text-center text-xs leading-relaxed opacity-60 md:text-[13px]">
			<p>Disclaimer: Statements on this website have not been evaluated by any regulatory authority. All products are supplied strictly for laboratory research purposes and are not intended for human or veterinary use. Nothing on this site is intended to diagnose, treat, cure, or prevent any disease.</p>
			<p>Product information is compiled from a range of studies and analyses gathered over time and is provided for research reference only, it is not intended to support the diagnosis, treatment, or prevention of any disease.</p>
			<p>This site is protected against automated abuse by Cloudflare, in accordance with the <a href="https://www.cloudflare.com/privacypolicy/" target="_blank" rel="noopener noreferrer" class="underline hover:text-accent">Cloudflare Privacy Policy</a>.</p>
		</div>
	</div>
	<div class="border-t border-white/10 px-4 py-4 text-center text-xs leading-relaxed opacity-70">© <?php echo esc_html( gmdate( 'Y' ) ); ?> ONYX Research. All rights reserved. Reproduction of any content without prior written consent is strictly prohibited.</div>
</footer>
<a href="https://wa.me/447404932343" target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp" class="fixed bottom-[18px] right-[43px] z-50 flex h-12 w-12 items-center justify-center rounded-full bg-[#25d366] shadow-lg shadow-[#25d366]/40 transition hover:scale-110 hover:shadow-[#25d366]/60 lg:bottom-12 lg:right-12 lg:h-14 lg:w-14"><?php echo str_replace( 'class="h-4 w-4"', 'class="h-6 w-6 text-white lg:h-7 lg:w-7"', $wa_icon ); // phpcs:ignore ?></a>
<aside role="status" aria-label="Order delay notice" class="onyx-delay fixed right-4 bottom-4 z-[100] w-[calc(100vw-2rem)] max-w-[420px] overflow-hidden rounded-xl border border-slate-200 border-l-4 border-l-red-600 bg-white p-4 shadow-xl sm:right-6 sm:bottom-6" hidden>
	<div class="flex items-start gap-3">
		<?php echo onyx_icon( 'circle-alert', 'mt-0.5 h-7 w-7 shrink-0 text-red-600' ); // phpcs:ignore ?>
		<div class="min-w-0 flex-1 text-sm text-slate-800"><p class="mb-1 font-bold">Attention</p><p class="leading-relaxed">We are currently experiencing slight delays in processing some orders. Rest assured, all orders will be processed. Thank you for your patience and understanding!</p></div>
		<button type="button" aria-label="Dismiss order delay notice" class="rounded p-1 text-slate-700 hover:bg-slate-100" data-onyx-delay-close><?php echo onyx_icon( 'x', 'h-5 w-5' ); // phpcs:ignore ?></button>
	</div>
</aside>
</div>
