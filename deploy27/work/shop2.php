<?php
/**
 * [onyx_shop2] – "Peptides 2" collection page (deploy27 mockup).
 * Live WooCommerce data; GET filters: s2_cat, s2_form (pen|vial), s2_avail (in|soon), s2_min, s2_max, s2_sort, s2_pg.
 * Does not touch the shop archive (/peptides/) – separate shortcode, used only on page peptides-2.
 *
 * @package onyx-child
 */

defined( 'ABSPATH' ) || exit;

/** Premium studio images keyed by product ID, from assets/data/shop2-premium.json (built by swap27.py). Products' own images are untouched. */
function onyx_shop2_premium_images() {
	$file = get_stylesheet_directory() . '/assets/data/shop2-premium.json';
	$map  = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : array(); // phpcs:ignore
	$base = trailingslashit( wp_get_upload_dir()['baseurl'] );
	$out  = array();
	foreach ( (array) $map as $id => $files ) {
		ksort( $files, SORT_NUMERIC );
		$set = array();
		foreach ( $files as $w => $rel ) {
			$set[] = $base . $rel . ' ' . (int) $w . 'w';
		}
		$src = $base . ( $files['800'] ?? end( $files ) );
		$out[ (int) $id ] = array( 'src' => $src, 'srcset' => implode( ', ', $set ) );
	}
	return $out;
}

function onyx_shop2_card( $p, $premium, $eager = false ) {
	$id     = $p->get_id();
	$url    = get_permalink( $id );
	$coming = onyx_is_coming_soon( $p );
	$oos    = ! $p->is_in_stock() && ! $coming;
	$multi  = $p->is_type( 'variable' ) && count( $p->get_children() ) > 1;
	$lp     = $p->get_meta( '_onyx_list_price' );
	$price  = '' !== $lp ? $lp : ( $p->is_type( 'variable' ) ? $p->get_variation_price( 'min', true ) : $p->get_price() );
	$orig   = $p->get_meta( '_onyx_original_price' );
	$cats   = wc_get_product_terms( $id, 'product_cat', array( 'fields' => 'names' ) );
	$cat    = $cats ? $cats[0] : '';
	$load   = $eager ? 'eager' : 'lazy';
	ob_start();
	?>
	<li class="s2-card<?php echo isset( $premium[ $id ] ) ? ' s2-premium' : ''; ?>">
		<a class="s2-media" href="<?php echo esc_url( $url ); ?>">
			<?php if ( isset( $premium[ $id ] ) ) : ?>
				<img src="<?php echo esc_url( $premium[ $id ]['src'] ); ?>" srcset="<?php echo esc_attr( $premium[ $id ]['srcset'] ); ?>" sizes="(max-width:767px) 46vw, 280px" width="800" height="800" alt="<?php echo esc_attr( $p->get_name() ); ?>" loading="<?php echo esc_attr( $load ); ?>" decoding="async">
			<?php elseif ( $p->get_image_id() ) : ?>
				<?php echo wp_get_attachment_image( $p->get_image_id(), 'woocommerce_single', false, array( 'sizes' => '(max-width:767px) 46vw, 280px', 'loading' => $load, 'decoding' => 'async', 'alt' => $p->get_name() ) ); // phpcs:ignore ?>
			<?php endif; ?>
			<span class="s2-badges">
				<?php if ( $coming ) : ?><span class="s2-badge s2-b-soon">Coming soon</span><?php endif; ?>
				<?php if ( $oos ) : ?><span class="s2-badge s2-b-oos">Out of stock</span><?php endif; ?>
				<?php foreach ( onyx_product_badges( $p ) as $b ) : ?><span class="s2-badge s2-b-<?php echo esc_attr( $b ); ?>"><?php echo esc_html( onyx_badge_label( $b ) ); ?></span><?php endforeach; ?>
			</span>
		</a>
		<?php onyx_wishlist_button( $p, 's2-wish', 's2-wish-ic' ); ?>
		<div class="s2-body">
			<?php if ( $cat ) : ?><span class="s2-cat"><?php echo esc_html( $cat ); ?></span><?php endif; ?>
			<a class="s2-title" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( trim( preg_replace( '/\s+/', ' ', $p->get_name() ) ) ); ?></a>
			<?php if ( ! $coming ) : ?>
				<span class="s2-price"><?php if ( $orig ) : ?><s>£<?php echo esc_html( $orig ); ?></s> <?php endif; ?><?php echo $multi ? 'From ' : ''; ?><?php echo onyx_price( $price ); // phpcs:ignore ?></span>
			<?php endif; ?>
		</div>
	</li>
	<?php
	return ob_get_clean();
}

add_shortcode( 'onyx_shop2', function () {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$sorts = array( 'featured' => 'Featured', 'best-selling' => 'Best selling', 'new-in' => 'New in', 'price-asc' => 'Price, low to high', 'price-desc' => 'Price, high to low', 'name' => 'Alphabetically, A–Z' );
	$sort  = isset( $_GET['s2_sort'], $sorts[ $_GET['s2_sort'] ] ) ? sanitize_key( $_GET['s2_sort'] ) : 'featured';
	$form  = isset( $_GET['s2_form'] ) && in_array( $_GET['s2_form'], array( 'pen', 'vial' ), true ) ? sanitize_key( $_GET['s2_form'] ) : '';
	$avail = isset( $_GET['s2_avail'] ) && in_array( $_GET['s2_avail'], array( 'in', 'soon' ), true ) ? sanitize_key( $_GET['s2_avail'] ) : '';
	$catq  = isset( $_GET['s2_cat'] ) ? sanitize_title( wp_unslash( $_GET['s2_cat'] ) ) : '';
	$min   = isset( $_GET['s2_min'] ) && '' !== $_GET['s2_min'] ? max( 0, (float) $_GET['s2_min'] ) : null;
	$max   = isset( $_GET['s2_max'] ) && '' !== $_GET['s2_max'] ? max( 0, (float) $_GET['s2_max'] ) : null;
	$page  = max( 1, (int) ( $_GET['s2_pg'] ?? 1 ) );
	// phpcs:enable
	$per   = 16;

	$all = wc_get_products( array( 'limit' => -1, 'status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC', 'visibility' => 'catalog' ) );
	$all = array_values( array_filter( $all, fn( $p ) => 'programs' !== $p->get_meta( '_onyx_category' ) && ! has_term( 'programs', 'product_cat', $p->get_id() ) ) );
	$pp  = function ( $p ) {
		$lp = $p->get_meta( '_onyx_list_price' );
		return (float) ( '' !== $lp ? $lp : ( $p->is_type( 'variable' ) ? $p->get_variation_price( 'min', true ) : $p->get_price() ) );
	};
	$prices   = array_map( $pp, $all );
	$ceil     = $prices ? (int) ceil( max( $prices ) ) : 0;
	$has_vial = (bool) array_filter( $all, fn( $p ) => 'vial' === $p->get_meta( '_onyx_type' ) );

	$list = $all;
	if ( $catq ) {
		$list = array_filter( $list, fn( $p ) => has_term( $catq, 'product_cat', $p->get_id() ) );
	}
	if ( $form ) {
		$list = array_filter( $list, fn( $p ) => $form === $p->get_meta( '_onyx_type' ) );
	}
	if ( 'in' === $avail ) {
		$list = array_filter( $list, fn( $p ) => $p->is_in_stock() && ! onyx_is_coming_soon( $p ) );
	} elseif ( 'soon' === $avail ) {
		$list = array_filter( $list, fn( $p ) => onyx_is_coming_soon( $p ) );
	}
	if ( null !== $min ) {
		$list = array_filter( $list, fn( $p ) => $pp( $p ) >= $min );
	}
	if ( null !== $max ) {
		$list = array_filter( $list, fn( $p ) => $pp( $p ) <= $max );
	}
	$badge = array( 'best-selling' => 'bestseller', 'new-in' => 'new' )[ $sort ] ?? '';
	if ( $badge ) {
		$list = array_filter( $list, fn( $p ) => in_array( $badge, onyx_product_badges( $p ), true ) );
	}
	$list = array_values( $list );
	if ( 'price-asc' === $sort ) {
		usort( $list, fn( $a, $b ) => $pp( $a ) <=> $pp( $b ) );
	} elseif ( 'price-desc' === $sort ) {
		usort( $list, fn( $a, $b ) => $pp( $b ) <=> $pp( $a ) );
	} elseif ( 'name' === $sort ) {
		usort( $list, fn( $a, $b ) => strcasecmp( $a->get_name(), $b->get_name() ) );
	} elseif ( 'featured' === $sort ) {
		$idx = array_flip( array_map( fn( $p ) => $p->get_id(), $list ) );
		usort( $list, fn( $a, $b ) => ( (int) $b->get_meta( '_onyx_featured' ) <=> (int) $a->get_meta( '_onyx_featured' ) ) ?: ( $idx[ $a->get_id() ] <=> $idx[ $b->get_id() ] ) );
	}
	$total = count( $list );
	$pages = max( 1, (int) ceil( $total / $per ) );
	$page  = min( $page, $pages );
	$shown = array_slice( $list, ( $page - 1 ) * $per, $per );

	$base = get_permalink();
	$cur  = array_filter( array( 's2_cat' => $catq, 's2_form' => $form, 's2_avail' => $avail, 's2_min' => null !== $min ? $min : '', 's2_max' => null !== $max ? $max : '', 's2_sort' => 'featured' !== $sort ? $sort : '' ), fn( $v ) => '' !== $v && null !== $v );
	$link = fn( $args ) => esc_url( add_query_arg( array_filter( array_merge( $cur, $args ), fn( $v ) => '' !== $v && null !== $v ), $base ) );

	$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'exclude' => array_filter( array( (int) get_option( 'default_product_cat' ) ) ) ) );
	$terms = array_values( array_filter( is_array( $terms ) ? $terms : array(), fn( $t ) => ! in_array( $t->slug, array( 'programs', 'plans', 'essentials', 'coming-soon' ), true ) ) );
	$active_cat = $catq ? get_term_by( 'slug', $catq, 'product_cat' ) : null;
	$premium = onyx_shop2_premium_images();
	$hidden  = function ( $skip ) use ( $cur ) {
		$h = '';
		foreach ( $cur as $k => $v ) {
			if ( ! in_array( $k, $skip, true ) ) {
				$h .= '<input type="hidden" name="' . esc_attr( $k ) . '" value="' . esc_attr( $v ) . '">';
			}
		}
		return $h;
	};
	$chev = '<svg class="s2-chev" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>';
	$nf   = count( array_intersect_key( $cur, array_flip( array( 's2_form', 's2_avail', 's2_min', 's2_max' ) ) ) );
	ob_start();
	?>
	<div class="s2">
		<header class="s2-head s2-wrap">
			<nav class="s2-crumbs" aria-label="Breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a><span>/</span><a href="<?php echo esc_url( $base ); ?>">Peptides</a><?php if ( $active_cat ) : ?><span>/</span><em><?php echo esc_html( $active_cat->name ); ?></em><?php endif; ?></nav>
			<div class="s2-head-grid">
				<div>
					<p class="s2-eyebrow">The ONYX catalogue</p>
					<h1><?php echo $active_cat ? esc_html( $active_cat->name ) : 'Research <em>peptides</em>'; ?></h1>
				</div>
				<div class="s2-intro">
					<p>Every peptide in the ONYX Research catalogue is manufactured to a ≥99% purity specification, with each batch independently verified by third-party laboratories using HPLC and mass spectrometry. A unique authentication code ships with every vial.</p>
					<p>From metabolic and growth-signalling compounds to tissue-repair and neuroendocrine research peptides, every compound is supplied in the format your protocol requires: lyophilised powder, pre-mixed solution, or a pre-filled dual-chamber delivery pen.</p>
				</div>
			</div>
			<div class="s2-cats" role="list">
				<a role="listitem" class="s2-chip<?php echo $catq ? '' : ' is-on'; ?>" href="<?php echo $link( array( 's2_cat' => '', 's2_pg' => '' ) ); // phpcs:ignore ?>">All</a>
				<?php foreach ( $terms as $t ) : ?>
					<a role="listitem" class="s2-chip<?php echo $catq === $t->slug ? ' is-on' : ''; ?>" href="<?php echo $link( array( 's2_cat' => $t->slug, 's2_pg' => '' ) ); // phpcs:ignore ?>"><?php echo esc_html( $t->name ); ?></a>
				<?php endforeach; ?>
			</div>
		</header>

		<div class="s2-wrap">
			<div class="s2-bar">
				<details class="s2-mobile-filter" open><summary><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12M20 18h0" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><circle cx="16" cy="6" r="2" fill="none" stroke="currentColor" stroke-width="1.6"/><circle cx="10" cy="12" r="2" fill="none" stroke="currentColor" stroke-width="1.6"/><circle cx="18" cy="18" r="2" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>Filter and sort<?php echo $nf ? ' <b>' . (int) $nf . '</b>' : ''; ?></summary>
					<div class="s2-bar-in"><div class="s2-filters">
						<span class="s2-lab">Filter:</span>
						<?php if ( $has_vial ) : ?>
						<details class="s2-dd"><summary>Format<?php echo $form ? ' · ' . esc_html( ucfirst( $form ) ) : ''; ?><?php echo $chev; // phpcs:ignore ?></summary>
							<div class="s2-pop">
								<a class="s2-opt<?php echo '' === $form ? ' is-on' : ''; ?>" href="<?php echo $link( array( 's2_form' => '', 's2_pg' => '' ) ); // phpcs:ignore ?>">All formats</a>
								<a class="s2-opt<?php echo 'pen' === $form ? ' is-on' : ''; ?>" href="<?php echo $link( array( 's2_form' => 'pen', 's2_pg' => '' ) ); // phpcs:ignore ?>">Pen <small>Pre-filled and dual chamber pens</small></a>
								<a class="s2-opt<?php echo 'vial' === $form ? ' is-on' : ''; ?>" href="<?php echo $link( array( 's2_form' => 'vial', 's2_pg' => '' ) ); // phpcs:ignore ?>">Vial <small>Traditional vial format</small></a>
							</div>
						</details>
						<?php endif; ?>
						<details class="s2-dd"><summary>Availability<?php echo $avail ? ' · ' . ( 'in' === $avail ? 'In stock' : 'Coming soon' ) : ''; ?><?php echo $chev; // phpcs:ignore ?></summary>
							<div class="s2-pop">
								<a class="s2-opt<?php echo '' === $avail ? ' is-on' : ''; ?>" href="<?php echo $link( array( 's2_avail' => '', 's2_pg' => '' ) ); // phpcs:ignore ?>">Any</a>
								<a class="s2-opt<?php echo 'in' === $avail ? ' is-on' : ''; ?>" href="<?php echo $link( array( 's2_avail' => 'in', 's2_pg' => '' ) ); // phpcs:ignore ?>">In stock</a>
								<a class="s2-opt<?php echo 'soon' === $avail ? ' is-on' : ''; ?>" href="<?php echo $link( array( 's2_avail' => 'soon', 's2_pg' => '' ) ); // phpcs:ignore ?>">Coming soon</a>
							</div>
						</details>
						<details class="s2-dd"><summary>Price<?php echo ( null !== $min || null !== $max ) ? ' · £' . esc_html( (int) $min ) . '–£' . esc_html( null !== $max ? (int) $max : $ceil ) : ''; ?><?php echo $chev; // phpcs:ignore ?></summary>
							<form class="s2-pop s2-price-form" method="get" action="<?php echo esc_url( $base ); ?>">
								<p class="s2-note">The highest price is £<?php echo (int) $ceil; ?></p>
								<div class="s2-range"><label>£ <input type="number" name="s2_min" min="0" max="<?php echo (int) $ceil; ?>" placeholder="From" value="<?php echo null !== $min ? esc_attr( (int) $min ) : ''; ?>"></label><label>£ <input type="number" name="s2_max" min="0" max="<?php echo (int) $ceil; ?>" placeholder="To" value="<?php echo null !== $max ? esc_attr( (int) $max ) : ''; ?>"></label></div>
								<?php echo $hidden( array( 's2_min', 's2_max' ) ); // phpcs:ignore ?>
								<button type="submit" class="s2-apply">Apply</button>
							</form>
						</details>
						<?php if ( $nf ) : ?><a class="s2-clear" href="<?php echo $link( array( 's2_form' => '', 's2_avail' => '', 's2_min' => '', 's2_max' => '', 's2_pg' => '' ) ); // phpcs:ignore ?>">Clear filters</a><?php endif; ?>
					</div>
					<form class="s2-sort" method="get" action="<?php echo esc_url( $base ); ?>">
						<label for="s2-sort-sel">Sort by:</label>
						<select id="s2-sort-sel" name="s2_sort" onchange="this.form.submit()">
							<?php foreach ( $sorts as $k => $l ) : ?><option value="<?php echo esc_attr( $k ); ?>" <?php selected( $sort, $k ); ?>><?php echo esc_html( $l ); ?></option><?php endforeach; ?>
						</select>
						<?php echo $hidden( array( 's2_sort' ) ); // phpcs:ignore ?>
						<noscript><button type="submit">Go</button></noscript>
					</form></div>
				</details>
				<span class="s2-count"><?php echo (int) $total; ?> <?php echo 1 === $total ? 'product' : 'products'; ?></span>
			</div>

			<?php if ( ! $total ) : ?>
				<div class="s2-empty"><p>No products match these filters.</p><a class="s2-apply" href="<?php echo esc_url( $base ); ?>">Clear all</a></div>
			<?php else : ?>
				<ul class="s2-grid">
					<?php foreach ( $shown as $i => $p ) { echo onyx_shop2_card( $p, $premium, false ); } // phpcs:ignore ?>
				</ul>
				<?php if ( $pages > 1 ) : ?>
					<nav class="s2-pager" aria-label="Pagination">
						<?php if ( $page > 1 ) : ?><a class="s2-pg s2-pg-arrow" href="<?php echo $link( array( 's2_pg' => $page - 1 > 1 ? $page - 1 : '' ) ); // phpcs:ignore ?>" aria-label="Previous page">‹</a><?php endif; ?>
						<?php for ( $n = 1; $n <= $pages; $n++ ) : ?>
							<?php if ( $n === $page ) : ?><span class="s2-pg is-on" aria-current="page"><?php echo (int) $n; ?></span><?php elseif ( 1 === $n || $n === $pages || abs( $n - $page ) <= 1 ) : ?><a class="s2-pg" href="<?php echo $link( array( 's2_pg' => $n > 1 ? $n : '' ) ); // phpcs:ignore ?>"><?php echo (int) $n; ?></a><?php elseif ( 2 === abs( $n - $page ) ) : ?><span class="s2-pg-gap">…</span><?php endif; ?>
						<?php endfor; ?>
						<?php if ( $page < $pages ) : ?><a class="s2-pg s2-pg-arrow" href="<?php echo $link( array( 's2_pg' => $page + 1 ) ); // phpcs:ignore ?>" aria-label="Next page">›</a><?php endif; ?>
					</nav>
				<?php endif; ?>
			<?php endif; ?>
			<aside class="s2-ruo"><strong>Research use only.</strong> All ONYX materials are supplied exclusively for legitimate laboratory and scientific research. They are not intended for human or veterinary use, diagnosis, treatment, or therapeutic application.</aside>
		</div>
	</div>
	<script>(function(){var m=document.querySelector('.s2 .s2-mobile-filter');if(m&&window.matchMedia('(max-width:767px)').matches)m.removeAttribute('open');})();document.addEventListener('click',function(e){document.querySelectorAll('.s2 details.s2-dd[open]').forEach(function(d){if(!d.contains(e.target))d.removeAttribute('open');});});</script>
	<?php
	return ob_get_clean();
} );
