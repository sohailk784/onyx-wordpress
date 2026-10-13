<?php
/**
 * Site header – port of the React components/layout/Header.tsx.
 * Navigation items come from the "Primary navigation" menu (Appearance → Menus).
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;

$nav_link = 'whitespace-nowrap px-1 py-2 text-[12px] font-medium transition hover:text-accent xl:px-2.5 xl:text-sm';
$items    = onyx_menu_tree( 'primary' );
$cart_n   = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
$logo     = get_stylesheet_directory_uri() . '/assets/img/logo-onyx.webp';
$langs    = array( array( 'EN', 'English', 'gb', 'en' ), array( 'FR', 'French', 'fr', 'fr' ), array( 'ES', 'Spanish', 'es', 'es' ), array( 'DE', 'German', 'de', 'de' ), array( 'SV', 'Swedish', 'se', 'sv' ), array( 'RU', 'Russian', 'ru', 'ru' ), array( 'NL', 'Dutch', 'nl', 'nl' ), array( 'NO', 'Norwegian', 'no', 'no' ), array( 'DA', 'Danish', 'dk', 'da' ), array( 'RO', 'Romanian', 'ro', 'ro' ), array( 'HU', 'Hungarian', 'hu', 'hu' ), array( 'BG', 'Bulgarian', 'bg', 'bg' ), array( 'AE', 'Arabic (UAE)', 'ae', 'ar' ), array( 'ZH', 'Chinese', 'cn', 'zh-CN' ) );
$ticker   = array( array( 'truck', 'Fast Secure Shipping' ), array( 'users', 'Trusted by 1000s of customers' ), array( 'flask-conical', 'GMP-aligned laboratory' ), array( 'shield-check', 'Stabilized formula' ) );
$current  = trailingslashit( home_url( add_query_arg( array() ) ) );
?>
<div class="onyx-tw" style="display:contents">
<header class="onyx-header sticky top-0 z-50 border-b bg-background/95 backdrop-blur" id="onyx-header">
	<div class="bg-primary text-primary-foreground">
		<div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-1.5">
			<div class="relative h-5 min-w-0 flex-1 overflow-hidden" data-onyx-ticker>
				<?php foreach ( $ticker as $i => $t ) : ?>
					<div class="absolute inset-0 flex items-center gap-2 onyx-ticker-item<?php echo 0 === $i ? ' is-active' : ''; ?>"><?php echo onyx_icon( $t[0], 'h-3.5 w-3.5 shrink-0 text-accent' ); // phpcs:ignore ?><span class="truncate text-xs font-medium tracking-wide"><?php echo esc_html( $t[1] ); ?></span></div>
				<?php endforeach; ?>
			</div>
			<div class="flex shrink-0 items-center gap-0.5">
				<div class="relative hidden lg:block onyx-lang" dir="ltr">
					<button type="button" aria-label="Language" aria-haspopup="menu" class="notranslate inline-flex h-10 items-center justify-center gap-1 rounded-sm px-1.5 transition-colors hover:bg-transparent" translate="no" data-onyx-lang-toggle>
						<span class="fi fi-gb rounded-sm" style="width:1.5rem;height:1.1rem" data-onyx-lang-flag></span>
						<span class="text-[10px] font-bold leading-none tracking-wide" data-onyx-lang-code>EN</span>
					</button>
					<div role="menu" class="absolute right-0 top-full z-50 mt-2 min-w-[190px] overflow-hidden rounded-xl border border-border bg-background shadow-xl transition-all duration-200 pointer-events-none -translate-y-1 opacity-0" data-onyx-lang-menu>
						<ul class="notranslate py-1" translate="no">
							<?php foreach ( $langs as $l ) : ?>
								<li><button type="button" role="menuitem" data-lang="<?php echo esc_attr( $l[3] ); ?>" data-code="<?php echo esc_attr( $l[0] ); ?>" data-flag="<?php echo esc_attr( $l[2] ); ?>" class="flex w-full items-center gap-3 border-l-2 px-4 py-2.5 text-left text-sm transition-colors hover:bg-primary/10 border-transparent text-foreground"><span class="fi fi-<?php echo esc_attr( $l[2] ); ?> rounded-sm" style="width:1.25rem;height:.9rem"></span><span>(<?php echo esc_html( $l[0] ); ?>) <?php echo esc_html( $l[1] ); ?></span></button></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="hidden whitespace-nowrap px-2.5 py-2 text-xs font-semibold transition hover:text-accent lg:inline-flex">Trader Login</a>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="relative grid h-9 w-9 place-items-center rounded-full transition hover:bg-white/10" aria-label="Consumer Login"><?php echo onyx_icon( 'user', 'h-5 w-5' ); // phpcs:ignore ?></a>
				<a href="<?php echo esc_url( home_url( '/wishlist/' ) ); ?>" class="relative grid h-9 w-9 place-items-center rounded-full transition hover:bg-white/10" aria-label="Wishlist"><?php echo onyx_icon( 'heart', 'h-5 w-5' ); // phpcs:ignore ?></a>
				<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="relative grid h-9 w-9 place-items-center rounded-full transition hover:bg-white/10" aria-label="Cart"><?php echo onyx_icon( 'shopping-cart', 'h-5 w-5' ); // phpcs:ignore ?>
					<?php if ( $cart_n > 0 ) : ?><span class="absolute -right-0.5 -top-0.5 grid h-4 min-w-4 place-items-center rounded-full bg-accent px-1 text-[10px] font-bold text-accent-foreground"><?php echo (int) $cart_n; ?></span><?php endif; ?>
				</a>
			</div>
		</div>
	</div>

	<div class="relative mx-auto flex max-w-7xl items-center gap-2 px-2 py-2 lg:px-3">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex shrink-0 items-center gap-2" aria-label="ONYX Research home">
			<img src="<?php echo esc_url( $logo ); ?>" alt="ONYX Research" width="406" height="174" class="w-auto transition-all duration-300 h-12 md:h-14" data-onyx-logo>
		</a>
		<nav class="relative hidden flex-1 items-center justify-center lg:flex">
			<?php foreach ( $items as $i => $n ) : $active = trailingslashit( $n['url'] ) === $current; ?>
				<div class="flex items-center">
					<?php if ( $i > 0 ) : ?><span aria-hidden="true" class="h-4 w-px bg-border"></span><?php endif; ?>
					<?php if ( $n['children'] ) : ?>
						<div class="relative onyx-dd">
							<a href="<?php echo esc_url( $n['url'] ); ?>" class="flex items-center gap-1 <?php echo esc_attr( $nav_link . ( $active ? ' text-accent' : '' ) ); ?>"><?php echo esc_html( $n['title'] ); ?> <?php echo onyx_icon( 'chevron-down', 'h-3 w-3' ); // phpcs:ignore ?></a>
							<div class="absolute left-1/2 top-full z-50 -translate-x-1/2 pt-2 onyx-dd-panel">
								<div class="min-w-64 rounded-xl border bg-popover p-2 shadow-xl">
									<?php foreach ( $n['children'] as $c ) : ?>
										<a href="<?php echo esc_url( $c['url'] ); ?>" class="block rounded-lg px-3 py-2 text-sm font-medium transition hover:bg-secondary hover:text-accent"><?php echo esc_html( $c['title'] ); ?></a>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					<?php else : ?>
						<a href="<?php echo esc_url( $n['url'] ); ?>" class="<?php echo esc_attr( $nav_link . ( $active ? ' text-accent' : '' ) ); ?>"><?php echo esc_html( $n['title'] ); ?></a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
			<div class="flex items-center">
				<span aria-hidden="true" class="h-4 w-px bg-border"></span>
				<div class="relative onyx-search">
					<button type="button" class="flex items-center <?php echo esc_attr( $nav_link ); ?>" aria-label="Search" data-onyx-search-toggle><?php echo onyx_icon( 'search', 'h-4 w-4' ); // phpcs:ignore ?></button>
					<div class="absolute left-1/2 top-full z-50 w-[min(92vw,32rem)] -translate-x-1/2 pt-2 onyx-search-panel" hidden>
						<div class="overflow-hidden rounded-2xl border bg-popover text-popover-foreground shadow-2xl">
							<form role="search" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="relative border-b">
								<?php echo onyx_icon( 'search', 'pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground' ); // phpcs:ignore ?>
								<input type="search" name="s" placeholder="Search peptides…" class="w-full bg-transparent py-3.5 pl-11 pr-4 text-sm outline-none" autocomplete="off">
								<input type="hidden" name="post_type" value="product">
							</form>
						</div>
					</div>
				</div>
			</div>
		</nav>
		<div class="ml-auto flex items-center lg:hidden">
			<a href="<?php echo esc_url( home_url( '/?s=&post_type=product' ) ); ?>" class="grid h-10 w-10 shrink-0 place-items-center rounded-full transition hover:bg-secondary" aria-label="Search"><?php echo onyx_icon( 'search', 'h-5 w-5' ); // phpcs:ignore ?></a>
			<button type="button" class="grid h-10 w-10 shrink-0 place-items-center rounded-full hover:bg-secondary" aria-label="Menu" data-onyx-menu-toggle><?php echo onyx_icon( 'menu', 'h-5 w-5' ); // phpcs:ignore ?></button>
		</div>
	</div>
	<div class="fixed inset-x-0 z-40 overflow-y-auto border-t bg-background lg:hidden onyx-mobile-menu" hidden>
		<div class="flex flex-col pb-8">
			<?php foreach ( $items as $n ) : ?>
				<div>
					<div class="flex items-center border-b">
						<a href="<?php echo esc_url( $n['url'] ); ?>" class="flex flex-1 items-center gap-2 px-4 py-3 text-sm font-medium"><?php echo esc_html( $n['title'] ); ?></a>
						<?php if ( $n['children'] ) : ?><button type="button" class="grid h-11 w-11 shrink-0 place-items-center" data-onyx-sub-toggle><?php echo onyx_icon( 'chevron-down', 'h-4 w-4 transition-transform' ); // phpcs:ignore ?></button><?php endif; ?>
					</div>
					<?php if ( $n['children'] ) : ?>
						<div hidden data-onyx-sub>
							<?php foreach ( $n['children'] as $c ) : ?>
								<a href="<?php echo esc_url( $c['url'] ); ?>" class="block border-b bg-secondary/40 px-8 py-2.5 text-sm text-muted-foreground"><?php echo esc_html( $c['title'] ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</header>
</div>
