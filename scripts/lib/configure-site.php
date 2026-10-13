<?php
/**
 * Idempotent site configuration (WooCommerce, Elementor kit, wishlist, misc).
 * Run: wp eval-file scripts/lib/configure-site.php --user=admin
 */

// ---- General ----
update_option( 'blogname', 'ONYX Research' );
update_option( 'blogdescription', 'Laboratory Research Materials' );
update_option( 'timezone_string', 'Europe/London' );
update_option( 'permalink_structure', '/%postname%/' );

// ---- WooCommerce ----
update_option( 'woocommerce_currency', 'GBP' );
update_option( 'woocommerce_default_country', 'GB' );
update_option( 'woocommerce_coming_soon', 'no' );
update_option( 'woocommerce_enable_reviews', 'no' );
update_option( 'woocommerce_enable_guest_checkout', 'yes' );
update_option( 'woocommerce_enable_signup_and_login_from_checkout', 'yes' );
update_option( 'woocommerce_enable_myaccount_registration', 'yes' );
update_option( 'woocommerce_permalinks', array( 'product_base' => '/product', 'category_base' => 'peptides', 'tag_base' => 'product-tag', 'attribute_base' => '', 'use_verbose_page_rules' => false ) );
foreach ( array( 'bacs', 'cheque', 'cod' ) as $g ) {
	$s            = get_option( "woocommerce_{$g}_settings", array() );
	$s['enabled'] = 'no';
	update_option( "woocommerce_{$g}_settings", $s );
}

// ---- Wishlist (TI WooCommerce Wishlist): heart icon only, placed by the theme ----
$cat                 = get_option( 'tinvwl-add_to_wishlist_catalog', array() );
$cat['show_in_loop'] = true;
$cat['position']     = 'shortcode';
$cat['show_text']    = false;
$cat['icon']         = 'heart';
update_option( 'tinvwl-add_to_wishlist_catalog', $cat );
$prod              = get_option( 'tinvwl-add_to_wishlist', array() );
$prod['position']  = 'after';
$prod['show_text'] = false;
$prod['icon']      = 'heart';
update_option( 'tinvwl-add_to_wishlist', $prod );

// ---- Elementor ----
update_option( 'elementor_disable_color_schemes', 'yes' );
update_option( 'elementor_disable_typography_schemes', 'yes' );
update_option( 'elementor_experiment-e_lazyload', 'inactive' );
update_option( 'elementor_lazy_load_background_images', '' );
update_option( 'elementor_experiment-container', 'active' );
update_option( 'elementor_cpt_support', array( 'page', 'post', 'product' ) );
update_option( 'elementor_unfiltered_files_upload', '1' );

$kit_id = (int) get_option( 'elementor_active_kit' );
if ( $kit_id ) {
	$kit = \Elementor\Plugin::$instance->documents->get( $kit_id );
	$settings = $kit->get_settings();
	$settings['system_colors'] = array(
		array( '_id' => 'primary', 'title' => 'Primary (navy)', 'color' => '#001A61' ),
		array( '_id' => 'secondary', 'title' => 'Secondary', 'color' => '#ECF3FA' ),
		array( '_id' => 'text', 'title' => 'Text', 'color' => '#040D1E' ),
		array( '_id' => 'accent', 'title' => 'Accent (blue)', 'color' => '#2885EF' ),
	);
	$settings['custom_colors'] = array(
		array( '_id' => 'onyxmuted', 'title' => 'Muted text', 'color' => '#5D646F' ),
		array( '_id' => 'onyxborder', 'title' => 'Border', 'color' => '#DCE2E9' ),
		array( '_id' => 'onyxbg', 'title' => 'Background', 'color' => '#FAFCFE' ),
		array( '_id' => 'onyxmutedbg', 'title' => 'Muted background', 'color' => '#F0F4F9' ),
		array( '_id' => 'onyxdeep', 'title' => 'Deep navy', 'color' => '#0A1733' ),
	);
	$sys = array( 'primary' => '800', 'secondary' => '600', 'text' => '400', 'accent' => '600' );
	$settings['system_typography'] = array();
	foreach ( $sys as $id => $w ) {
		$settings['system_typography'][] = array( '_id' => $id, 'title' => ucfirst( $id ), 'typography_typography' => 'custom', 'typography_font_family' => '', 'typography_font_weight' => $w );
	}
	$settings['body_background_background'] = 'classic';
	$settings['body_background_color']      = '#FAFCFE';
	$settings['body_color']                 = '#040D1E';
	$settings['body_typography_typography'] = 'custom';
	$settings['body_typography_font_family'] = '';
	$settings['link_normal_color']          = '#2885EF';
	$settings['container_width']            = array( 'unit' => 'px', 'size' => 1280 );
	$settings['container_padding']          = array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true );
	$settings['space_between_widgets']      = array( 'unit' => 'px', 'size' => 0, 'column' => '0', 'row' => '0', 'isLinked' => true );
	$settings['viewport_md']                = 768;
	$settings['viewport_lg']                = 1025;
	$settings['site_name']                  = 'ONYX Research';
	$settings['hello_header_logo_display']  = 'yes';
	$settings['hello_footer_copyright_display'] = 'no';
	$kit->save( array( 'settings' => $settings ) );
}

// Hello Elementor: no default header/footer when EHF templates exist; disable description meta duplication.
update_option( 'hello_elementor_settings_description_meta_tag', 'yes' );
update_option( 'hello_elementor_settings_skip_link', '' );

// Click to Chat (WhatsApp) number from the live site.
$ctc = get_option( 'ht_ctc_chat_options', array() );
$ctc['number']      = '447404932343';
$ctc['pre_filled']  = 'Hello ONYX Research, I have a question.';
$ctc['call_to_action'] = 'WhatsApp us';
$ctc['style_desktop'] = '3';
$ctc['style_mobile']  = '3';
update_option( 'ht_ctc_chat_options', $ctc );

\Elementor\Plugin::$instance->files_manager->clear_cache();
flush_rewrite_rules();
echo "configured\n";
