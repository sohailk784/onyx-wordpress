<?php
/**
 * Plugin Name:       ONYX Core
 * Description:       Site features for ONYX Research: age gate, batch code verification, analytical reference calculator, research-use notices.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      8.1
 * Author:            ONYX Research
 * License:           Proprietary
 * Text Domain:       onyx-core
 *
 * @package onyx-core
 */

defined( 'ABSPATH' ) || exit;

define( 'ONYX_CORE_VERSION', '1.0.0' );
define( 'ONYX_CORE_FILE', __FILE__ );
define( 'ONYX_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'ONYX_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once ONYX_CORE_DIR . 'includes/class-age-gate.php';
require_once ONYX_CORE_DIR . 'includes/class-batch-codes.php';
require_once ONYX_CORE_DIR . 'includes/class-calculator.php';

add_action( 'plugins_loaded', function () {
	Onyx_Age_Gate::init();
	Onyx_Batch_Codes::init();
	Onyx_Calculator::init();
} );

register_activation_hook( __FILE__, function () {
	Onyx_Batch_Codes::register_post_type();
	flush_rewrite_rules();
} );
