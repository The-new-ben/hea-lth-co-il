<?php
/**
 * Regression test for the directory-map release gate.
 *
 * It verifies that the browser receives a Maps configuration only after a
 * restricted key, same-origin approval, verified location-data review, and
 * commercial-disclosure review have all passed.
 */

declare( strict_types = 1 );

define( 'ABSPATH', __DIR__ . DIRECTORY_SEPARATOR );

$hea_lth_test_options = array();

function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
}

function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
}

function register_setting( $group, $name, $args = array() ) {
}

function add_options_page( $page_title, $menu_title, $capability, $menu_slug, $callback ) {
}

function get_option( $key, $default = false ) {
	global $hea_lth_test_options;

	return array_key_exists( $key, $hea_lth_test_options ) ? $hea_lth_test_options[ $key ] : $default;
}

function home_url( $path = '/' ) {
	return 'https://hea-lth.co.il' . $path;
}

function wp_parse_url( $url, $component = -1 ) {
	return parse_url( $url, $component );
}

function esc_url_raw( $url ) {
	return filter_var( $url, FILTER_SANITIZE_URL );
}

function sanitize_key( $value ) {
	return strtolower( preg_replace( '/[^a-z0-9_-]/', '', (string) $value ) );
}

function sanitize_text_field( $value ) {
	return trim( strip_tags( (string) $value ) );
}

function wp_unslash( $value ) {
	return $value;
}

function wp_json_encode( $value, $options = 0 ) {
	return json_encode( $value, $options );
}

function __( $value, $domain = '' ) {
	return $value;
}

function add_settings_error( $setting, $code, $message ) {
}

function assert_same( $expected, $actual, string $message ): void {
	if ( $expected !== $actual ) {
		fwrite( STDERR, $message . "\nExpected: " . var_export( $expected, true ) . "\nActual: " . var_export( $actual, true ) . "\n" );
		exit( 1 );
	}
}

require_once dirname( __DIR__, 2 ) . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-directory-map-registry.php';

$approved_manifest = array(
	'status'                      => 'approved',
	'provider'                    => 'google-maps-js',
	'browserKey'                  => 'AIzaSyExampleBrowserKey_1234567890',
	'mapId'                       => 'hea-lth-il-map-2026',
	'allowedOrigin'               => 'https://hea-lth.co.il',
	'countryCode'                 => 'IL',
	'owner'                       => 'Hea-lth Maps owner',
	'reviewedAt'                  => '2026-07-11',
	'keyRestrictionReview'        => 'passed',
	'locationDataReview'          => 'passed',
	'commercialDisclosureReview'  => 'passed',
);

$hea_lth_test_options['hea_lth_directory_map_manifest'] = json_encode( $approved_manifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
$public = Hea_Lth_Directory_Map_Registry::public_configuration();
assert_same( 'approved', $public['status'], 'A fully reviewed map manifest must produce a public map configuration.' );
assert_same( 'google-maps-js', $public['provider'], 'Only the approved Maps JavaScript provider may be exposed.' );
assert_same( 'he', $public['language'], 'The map must use the Hebrew visitor language.' );
assert_same( 'IL', $public['region'], 'The initial market must remain Israel.' );
assert_same( false, array_key_exists( 'owner', $public ), 'Internal map ownership must not leak to the public payload.' );
assert_same( false, array_key_exists( 'allowedOrigin', $public ), 'Internal origin-review details must not leak to the public payload.' );

$blocked_location = $approved_manifest;
$blocked_location['locationDataReview'] = 'pending';
$hea_lth_test_options['hea_lth_directory_map_manifest'] = json_encode( $blocked_location, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
$location_result = Hea_Lth_Directory_Map_Registry::public_configuration();
assert_same( 'configuration-gated', $location_result['status'], 'Unreviewed location data must block the public map.' );
assert_same( 'location-data-not-approved', $location_result['reason'], 'The location-data gate must explain the blocked map state.' );

$blocked_origin = $approved_manifest;
$blocked_origin['allowedOrigin'] = 'https://invalid.example';
$hea_lth_test_options['hea_lth_directory_map_manifest'] = json_encode( $blocked_origin, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
$origin_result = Hea_Lth_Directory_Map_Registry::public_configuration();
assert_same( 'configuration-gated', $origin_result['status'], 'A mismatched origin must block the public map.' );
assert_same( 'origin-not-approved', $origin_result['reason'], 'The origin gate must explain the blocked map state.' );

echo "Directory map registry gate tests passed.\n";
