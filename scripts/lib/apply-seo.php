<?php
/**
 * Apply Yoast SEO titles/descriptions per page + templates for products/categories.
 * Usage: wp eval-file scripts/lib/apply-seo.php data/seo-pages.json
 */
$map = json_decode( file_get_contents( $args[0] ), true );
foreach ( $map as $slug => $m ) {
	$id = 'home' === $slug ? (int) get_option( 'page_on_front' ) : ( 'peptides' === $slug ? wc_get_page_id( 'shop' ) : ( get_page_by_path( $slug )->ID ?? 0 ) );
	if ( ! $id ) {
		WP_CLI::warning( "No page for $slug" );
		continue;
	}
	update_post_meta( $id, '_yoast_wpseo_title', $m['title'] );
	update_post_meta( $id, '_yoast_wpseo_metadesc', $m['description'] );
	update_post_meta( $id, '_yoast_wpseo_opengraph-title', $m['title'] );
	update_post_meta( $id, '_yoast_wpseo_opengraph-description', $m['description'] );
	if ( ! empty( $m['noindex'] ) ) {
		update_post_meta( $id, '_yoast_wpseo_meta-robots-noindex', '1' );
	}
	WP_CLI::log( "$slug #$id" );
}
$t = get_option( 'wpseo_titles', array() );
$t['separator']                    = 'sc-pipe';
$t['title-product']                = '%%title%% %%sep%% %%sitename%%';
$t['metadesc-product']             = '%%title%% — research-grade laboratory material with batch-level analytical information. Supplied exclusively for legitimate scientific research.';
$t['title-tax-product_cat']        = '%%term_title%% %%sep%% %%sitename%%';
$t['metadesc-tax-product_cat']     = 'Research-grade %%term_title%% materials with batch-level analytical information. Supplied exclusively for legitimate scientific research.';
$t['title-page']                   = '%%title%% %%sep%% %%sitename%%';
$t['noindex-tax-product_tag']      = true;
$t['noindex-elementor-hf']         = true;
$t['noindex-elementor_library']    = true;
$t['company_or_person']            = 'company';
$t['company_name']                 = 'ONYX Research';
update_option( 'wpseo_titles', $t );
WP_CLI::success( 'SEO applied' );
