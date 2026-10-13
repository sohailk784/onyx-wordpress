<?php
/**
 * Export Elementor page data back to data/elementor/*.json (round-trip after editing in wp-admin).
 * Usage: wp eval-file scripts/export-elementor.php data/elementor --user=admin
 * Absolute site URLs are replaced with the {{SITE_URL}} placeholder.
 */
$dir      = rtrim( $args[0] ?? 'data/elementor', '/' );
$manifest = json_decode( file_get_contents( "$dir/manifest.json" ), true );
$site     = untrailingslashit( home_url() );
$dump     = function ( $id, $file ) use ( $dir, $site ) {
	$data = get_post_meta( $id, '_elementor_data', true );
	if ( ! $data ) {
		return;
	}
	$json = wp_json_encode( json_decode( $data, true ), JSON_UNESCAPED_SLASHES );
	$json = str_replace( array( $site, str_replace( '/', '\/', $site ) ), '{{SITE_URL}}', $json );
	file_put_contents( "$dir/$file", $json );
	WP_CLI::log( "#$id -> $file" );
};
foreach ( $manifest as $page ) {
	$p = get_page_by_path( $page['slug'] );
	if ( $p ) {
		$dump( $p->ID, basename( $page['file'] ) );
	}
}
foreach ( array( 'ONYX Header' => 'header.json', 'ONYX Footer' => 'footer.json' ) as $title => $file ) {
	$q = get_posts( array( 'post_type' => 'elementor-hf', 'title' => $title, 'numberposts' => 1 ) );
	if ( $q ) {
		$dump( $q[0]->ID, $file );
	}
}
if ( $hero = (int) get_option( 'onyx_shop_hero_template' ) ) {
	$dump( $hero, 'shop-hero.json' );
}
