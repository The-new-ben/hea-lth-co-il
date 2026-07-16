<?php
/**
 * Contract for the directory-map gate after the keyless leaflet-osm extension.
 *
 * The gate must: approve the shipped default manifest (reviews + origin), fix
 * the tile source in code, expose disclosed featured providers, keep the
 * google path keyed, and stay closed for unapproved manifests.
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

$GLOBALS['__options'] = array();

function get_option( $key, $default = false ) {
	return isset( $GLOBALS['__options'][ $key ] ) ? $GLOBALS['__options'][ $key ] : $default;
}
function add_action( $hook, $cb, $priority = 10, $args = 1 ) {}
function add_filter( $hook, $cb, $priority = 10, $args = 1 ) {}
function sanitize_key( $value ) {
	return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $value ) ) );
}
function sanitize_text_field( $value ) {
	return trim( preg_replace( '/[\r\n\t ]+/', ' ', strip_tags( (string) $value ) ) );
}
function esc_url_raw( $url, $protocols = null ) {
	$url = trim( (string) $url );
	return preg_match( '#^https://#', $url ) ? $url : '';
}
function wp_parse_url( $url ) {
	return parse_url( (string) $url );
}
function home_url( $path = '/' ) {
	return 'https://hea-lth.co.il' . $path;
}
function wp_json_encode( $data, $flags = 0 ) {
	return json_encode( $data, $flags );
}

$root = dirname( __DIR__, 2 );
require_once $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-directory-map-registry.php';

// 1. Default shipped manifest (no admin option) → approved keyless payload.
$config = Hea_Lth_Directory_Map_Registry::public_configuration();
assert_true( 'approved' === $config['status'], 'The shipped default map manifest must pass the gate. Got: ' . wp_json_encode( $config ) );
assert_true( 'leaflet-osm' === $config['provider'], 'The default provider must be the keyless leaflet-osm.' );
assert_true( 0 === strpos( (string) $config['tiles'], 'https://tile.openstreetmap.org/' ), 'The tile source must be the fixed code value.' );
assert_true( 'assets/data/healthcare-poi-il.json' === $config['poiData'], 'The POI dataset path must be exposed.' );
assert_true( is_array( $config['featuredProviders'] ) && count( $config['featuredProviders'] ) >= 1, 'Featured providers must ship with the payload.' );

$first = $config['featuredProviders'][0];
assert_true( '' !== $first['name'] && '' !== $first['disclosure'], 'A featured provider must carry a name and a commercial disclosure.' );
assert_true( $first['lat'] > 29.0 && $first['lat'] < 34.0, 'Featured coordinates must be inside Israel.' );
assert_true( '' === $first['url'] || 0 === strpos( $first['url'], 'https://' ), 'Featured URLs must be https.' );

// 2. The POI dataset itself must exist in the theme and be attributed.
$poi_path = $root . '/theme-src/hea-lth-portal/assets/data/healthcare-poi-il.json';
assert_true( is_file( $poi_path ), 'The healthcare POI dataset must ship in the theme.' );
$poi = json_decode( (string) file_get_contents( $poi_path ), true );
assert_true( is_array( $poi ) && count( $poi['pois'] ) > 400, 'The POI dataset must be rich (hundreds of institutions).' );
assert_true( false !== strpos( (string) $poi['license'], 'OpenStreetMap' ), 'The dataset must carry its OSM attribution.' );

// 3. A google manifest without a real key must stay gated.
$GLOBALS['__options'][ Hea_Lth_Directory_Map_Registry::OPTION ] = wp_json_encode( array(
	'status'                     => 'approved',
	'provider'                   => 'google-maps-js',
	'browserKey'                 => 'YOUR_API_KEY_HERE',
	'mapId'                      => 'abc123',
	'allowedOrigin'              => 'https://hea-lth.co.il',
	'countryCode'                => 'IL',
	'owner'                      => 'Hea-lth',
	'reviewedAt'                 => '2026-07-16',
	'keyRestrictionReview'       => 'passed',
	'locationDataReview'         => 'passed',
	'commercialDisclosureReview' => 'passed',
) );
$google = Hea_Lth_Directory_Map_Registry::public_configuration();
assert_true( 'approved' !== $google['status'], 'A google manifest with a placeholder key must stay gated.' );

// 4. A leaflet manifest for a foreign origin must stay gated.
$GLOBALS['__options'][ Hea_Lth_Directory_Map_Registry::OPTION ] = wp_json_encode( array(
	'status'                     => 'approved',
	'provider'                   => 'leaflet-osm',
	'allowedOrigin'              => 'https://evil.example',
	'countryCode'                => 'IL',
	'owner'                      => 'Hea-lth',
	'reviewedAt'                 => '2026-07-16',
	'keyRestrictionReview'       => 'passed',
	'locationDataReview'         => 'passed',
	'commercialDisclosureReview' => 'passed',
	'featuredProviders'          => array(),
) );
$foreign = Hea_Lth_Directory_Map_Registry::public_configuration();
assert_true( 'approved' !== $foreign['status'], 'A manifest approved for a different origin must stay gated.' );

echo "Directory map gate contract passed.\n";
