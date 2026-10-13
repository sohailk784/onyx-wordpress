<?php
/**
 * Create/update Elementor Header & Footer Builder templates (header/footer) and
 * Elementor library section templates (e.g. Shop Hero) from JSON.
 *
 * Usage: wp eval-file scripts/lib/import-templates.php <dir> --user=admin
 * Expects <dir>/header.json, <dir>/footer.json and optional <dir>/shop-hero.json.
 */

$dir = rtrim( $args[0] ?? '', '/' );

function onyx_tpl_upsert( $title, $post_type, $file, $meta = array() ) {
	if ( ! is_readable( $file ) ) {
		return 0;
	}
	$data = json_decode( str_replace( '{{SITE_URL}}', untrailingslashit( home_url() ), file_get_contents( $file ) ), true );
	$found = get_posts( array( 'post_type' => $post_type, 'title' => $title, 'post_status' => 'any', 'numberposts' => 1 ) );
	$id    = $found ? $found[0]->ID : wp_insert_post( array( 'post_type' => $post_type, 'post_status' => 'publish', 'post_title' => $title ) );
	foreach ( $meta as $k => $v ) {
		update_post_meta( $id, $k, $v );
	}
	update_post_meta( $id, '_elementor_edit_mode', 'builder' );
	$doc = \Elementor\Plugin::$instance->documents->get( $id, false );
	$doc->save( array( 'elements' => $data ) );
	WP_CLI::log( "$title #$id" );
	return $id;
}

$global = array( 'rule' => array( 'basic-global' ), 'specific' => array() );
onyx_tpl_upsert( 'ONYX Header', 'elementor-hf', "$dir/header.json", array( 'ehf_template_type' => 'type_header', 'ehf_target_include_locations' => $global, 'ehf_target_exclude_locations' => array(), 'ehf_target_user_roles' => array( 'all' ), '_wp_page_template' => 'elementor_canvas' ) );
onyx_tpl_upsert( 'ONYX Footer', 'elementor-hf', "$dir/footer.json", array( 'ehf_template_type' => 'type_footer', 'ehf_target_include_locations' => $global, 'ehf_target_exclude_locations' => array(), 'ehf_target_user_roles' => array( 'all' ), '_wp_page_template' => 'elementor_canvas' ) );
$hero = onyx_tpl_upsert( 'Shop Hero', 'elementor_library', "$dir/shop-hero.json", array( '_elementor_template_type' => 'container' ) );
if ( $hero ) {
	wp_set_object_terms( $hero, 'container', 'elementor_library_type' );
	update_option( 'onyx_shop_hero_template', $hero );
}
\Elementor\Plugin::$instance->files_manager->clear_cache();
