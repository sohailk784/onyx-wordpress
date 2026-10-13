<?php
/**
 * Create/update Elementor pages from JSON exports.
 *
 * Usage: wp eval-file scripts/lib/import-elementor-pages.php <manifest.json> [key ...]
 * manifest: { key: { slug, title, file } } where file holds the Elementor `elements` array.
 * URLs in the JSON may use the placeholder {{SITE_URL}}; it is replaced with home_url().
 */

$manifest_path = $args[0] ?? '';
$only          = array_slice( $args, 1 );
if ( ! is_readable( $manifest_path ) ) {
	WP_CLI::error( 'Manifest not found: ' . $manifest_path );
}
$manifest = json_decode( file_get_contents( $manifest_path ), true );
$base_dir = dirname( $manifest_path );

foreach ( $manifest as $key => $page ) {
	if ( $only && ! in_array( $key, $only, true ) ) {
		continue;
	}
	$file = is_readable( $page['file'] ) ? $page['file'] : $base_dir . '/' . basename( $page['file'] );
	$json = str_replace( '{{SITE_URL}}', untrailingslashit( home_url() ), file_get_contents( $file ) );
	$data = json_decode( $json, true );

	$existing = get_page_by_path( $page['slug'], OBJECT, 'page' );
	$post_id  = $existing ? $existing->ID : wp_insert_post(
		array(
			'post_type'   => 'page',
			'post_status' => 'publish',
			'post_title'  => $page['title'],
			'post_name'   => $page['slug'],
		)
	);
	update_post_meta( $post_id, '_wp_page_template', 'elementor_header_footer' );
	update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
	$doc = \Elementor\Plugin::$instance->documents->get( $post_id, false );
	$doc->save( array( 'elements' => $data, 'settings' => array( 'hide_title' => 'yes', 'template' => 'elementor_header_footer' ) ) );
	WP_CLI::log( sprintf( '%-34s #%d', $key, $post_id ) );
}
\Elementor\Plugin::$instance->files_manager->clear_cache();
