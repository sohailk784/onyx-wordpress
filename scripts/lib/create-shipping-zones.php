<?php
/**
 * Create WooCommerce shipping zones from data/shipping-zones.json (export of the
 * React backend's shipping zones). Idempotent: zones with the same name are replaced.
 * Usage: wp eval-file scripts/lib/create-shipping-zones.php data/shipping-zones.json
 */
$zones  = json_decode( file_get_contents( $args[0] ), true );
$lookup = array_flip( array_map( 'html_entity_decode', WC()->countries->get_countries() ) );
$alias  = array( 'Czechia' => 'Czech Republic', 'Vatican City' => 'Vatican', 'North Macedonia' => 'North Macedonia', 'Moldova' => 'Moldova' );
foreach ( WC_Shipping_Zones::get_zones() as $z ) {
	foreach ( $zones as $def ) {
		if ( $z['zone_name'] === $def['name'] ) {
			WC_Shipping_Zones::delete_zone( $z['id'] );
		}
	}
}
$order = 0;
foreach ( $zones as $def ) {
	if ( ! $def['countries'] ) {
		$zone = new WC_Shipping_Zone( 0 ); // "Locations not covered by your other zones".
		foreach ( $zone->get_shipping_methods() as $m ) {
			$zone->delete_shipping_method( $m->instance_id );
		}
	} else {
		$zone = new WC_Shipping_Zone();
		$zone->set_zone_name( $def['name'] );
		$zone->set_zone_order( $order++ );
		foreach ( $def['countries'] as $name ) {
			$name = $alias[ $name ] ?? $name;
			$code = $lookup[ $name ] ?? null;
			if ( ! $code ) {
				foreach ( $lookup as $n => $c ) {
					if ( stripos( $n, $name ) === 0 ) {
						$code = $c;
						break;
					}
				}
			}
			if ( $code ) {
				$zone->add_location( $code, 'country' );
			} else {
				WP_CLI::warning( "Unknown country: $name" );
			}
		}
		$zone->save();
	}
	foreach ( $def['rates'] as $rate ) {
		$label = ucfirst( $rate['method'] ) . ' (' . $rate['etaLabel'] . ')';
		$iid   = $zone->add_shipping_method( 'flat_rate' );
		update_option( "woocommerce_flat_rate_{$iid}_settings", array( 'title' => $label, 'tax_status' => 'none', 'cost' => (string) $rate['flatPrice'] ) );
		if ( ! empty( $rate['freeShippingThreshold'] ) ) {
			$fid = $zone->add_shipping_method( 'free_shipping' );
			update_option( "woocommerce_free_shipping_{$fid}_settings", array( 'title' => 'Free ' . strtolower( $rate['method'] ) . ' shipping (' . $rate['etaLabel'] . ')', 'requires' => 'min_amount', 'min_amount' => (string) $rate['freeShippingThreshold'], 'ignore_discounts' => 'no' ) );
		}
	}
	WP_CLI::log( 'Zone: ' . $def['name'] );
}
WP_CLI::success( 'Shipping zones created' );
