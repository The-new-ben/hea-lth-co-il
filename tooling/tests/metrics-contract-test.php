<?php
/**
 * Contract for the engagement metrics store.
 *
 * The metrics layer may count, and nothing else: allowlisted types, bounded
 * opaque keys, monthly capped storage, and absolutely no visitor identity
 * (no IP, user agent, cookies, or user lookup anywhere in the class).
 */

declare( strict_types = 1 );

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . "\n" );
		exit( 1 );
	}
}

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

$GLOBALS['hea_test_options'] = array();

if ( ! function_exists( 'add_action' ) ) {
	function add_action( $hook, $callback, $priority = 10, $args = 1 ) {}
}

if ( ! function_exists( 'get_option' ) ) {
	function get_option( $name, $fallback = false ) {
		return array_key_exists( $name, $GLOBALS['hea_test_options'] ) ? $GLOBALS['hea_test_options'][ $name ] : $fallback;
	}
}

if ( ! function_exists( 'add_option' ) ) {
	function add_option( $name, $value, $deprecated = '', $autoload = 'yes' ) {
		$GLOBALS['hea_test_options'][ $name ] = $value;

		return true;
	}
}

if ( ! function_exists( 'update_option' ) ) {
	function update_option( $name, $value, $autoload = null ) {
		$GLOBALS['hea_test_options'][ $name ] = $value;

		return true;
	}
}

if ( ! function_exists( 'sanitize_key' ) ) {
	function sanitize_key( $key ) {
		return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $key ) );
	}
}

$root = dirname( __DIR__, 2 );

require_once $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-metrics.php';

/* --- Key sanitization: bounded, restricted alphabet, fail-empty. --------- */

assert_true( 'pin-3f2a1b9c' === Hea_Lth_Metrics::sanitize_metric_key( 'pin-3f2a1b9c' ), 'Opaque pin ids must pass through unchanged.' );
assert_true( '/products/hair-loss/' === Hea_Lth_Metrics::sanitize_metric_key( '/products/hair-loss/' ), 'Site paths must pass through unchanged.' );
assert_true( '' === Hea_Lth_Metrics::sanitize_metric_key( 'שם בעברית' ), 'Free Hebrew text must sanitize to empty (opaque ids only).' );
assert_true( 'script' === Hea_Lth_Metrics::sanitize_metric_key( '<script>' ), 'Markup characters must be stripped from keys.' );
assert_true( 64 >= strlen( Hea_Lth_Metrics::sanitize_metric_key( str_repeat( 'a', 500 ) ) ), 'Keys must be bounded to 64 characters.' );

/* --- Counter round-trip through the option store. ------------------------- */

Hea_Lth_Metrics::increment( 'pin_view', 'pin-abc12345' );
Hea_Lth_Metrics::increment( 'pin_view', 'pin-abc12345' );
Hea_Lth_Metrics::increment( 'pin_click', 'pin-abc12345' );
Hea_Lth_Metrics::increment( 'wa_open', '/products/hair-loss/' );

$report = Hea_Lth_Metrics::report();

assert_true( isset( $report['pins']['pin-abc12345'] ), 'Report must group pin counters by pin id.' );
assert_true( 2 === $report['pins']['pin-abc12345']['views'], 'Two views must count as two.' );
assert_true( 1 === $report['pins']['pin-abc12345']['clicks'], 'One click must count as one.' );
assert_true( 1 === $report['whatsapp']['/products/hair-loss/'], 'WhatsApp opens must group by page path.' );
assert_true( 4 === $report['total_hits'], 'Total must sum every counter.' );

/* --- Monthly key cap. ------------------------------------------------------ */

$option = Hea_Lth_Metrics::option_name();
$full   = array();
for ( $i = 0; $i < 300; $i++ ) {
	$full[ 'pin_view:cap' . $i ] = 1;
}
$GLOBALS['hea_test_options'][ $option ] = $full;

Hea_Lth_Metrics::increment( 'pin_view', 'overflow-key' );
assert_true( ! isset( $GLOBALS['hea_test_options'][ $option ]['pin_view:overflow-key'] ), 'New keys past the monthly cap must be dropped.' );

Hea_Lth_Metrics::increment( 'pin_view', 'cap5' );
assert_true( 2 === $GLOBALS['hea_test_options'][ $option ]['pin_view:cap5'], 'Existing keys must keep counting at the cap.' );

/* --- Privacy + endpoint source contracts. --------------------------------- */

$source = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-metrics.php' );

foreach ( array( 'REMOTE_ADDR', 'HTTP_USER_AGENT', 'HTTP_COOKIE', 'wp_get_current_user', 'get_current_user_id', '$_COOKIE', '$_SERVER' ) as $forbidden ) {
	assert_true( false === strpos( $source, $forbidden ), 'Metrics must never touch visitor identity: ' . $forbidden );
}

assert_true( false !== strpos( $source, 'in_array( $type, self::TYPES, true )' ), 'The endpoint must enforce the metric-type allowlist.' );
assert_true( false !== strpos( $source, "add_option( \$option, \$counters, '', false )" ), 'Metric options must not autoload on every request.' );

$engagement = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/assets/js/engagement.js' );
assert_true( false !== strpos( $engagement, 'heaLthMetricBeacon' ) && false !== strpos( $engagement, 'wa_open' ), 'Engagement layer must expose the beacon and count WhatsApp opens.' );

$care_map = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/assets/js/care-map.js' );
assert_true( false !== strpos( $care_map, 'pin_view' ) && false !== strpos( $care_map, 'pin_click' ), 'Care map must count featured pin views and clicks.' );

echo "Metrics contract passed.\n";
