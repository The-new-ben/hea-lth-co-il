<?php
/**
 * SEO seed-map contract.
 *
 * This test protects the core no-cannibalization rule before any content or
 * live migration work begins. It does not claim the seed map is final keyword
 * research, and it never publishes, redirects, or edits WordPress.
 */

declare( strict_types = 1 );

define( 'ABSPATH', __DIR__ . DIRECTORY_SEPARATOR );
define( 'WP_DEBUG', false );

function home_url( $path = '/' ) {
	return 'https://hea-lth.co.il' . $path;
}

function apply_filters( $hook, $value ) {
	return $value;
}

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . "\n" );
		exit( 1 );
	}
}

$root   = dirname( __DIR__, 2 );
$handle = fopen( $root . '/docs/KEYWORD_URL_MAP_SEED_2026-07-10.csv', 'r' );
assert_true( false !== $handle, 'Keyword seed map must be readable.' );

$headers = fgetcsv( $handle );
assert_true( is_array( $headers ), 'Keyword seed map must include headers.' );

$rows             = array();
$keyword_to_urls  = array();
$existing_targets = array();

while ( false !== ( $values = fgetcsv( $handle ) ) ) {
	if ( count( $values ) !== count( $headers ) ) {
		continue;
	}

	$row = array_combine( $headers, $values );
	assert_true( is_array( $row ), 'Every keyword row must have the declared fields.' );

	foreach ( array( 'cluster', 'primary_keyword', 'intent', 'target_url', 'url_state', 'decision' ) as $field ) {
		assert_true( isset( $row[ $field ] ) && '' !== trim( $row[ $field ] ), 'Required keyword-map field is blank: ' . $field );
	}

	assert_true( 0 === strpos( $row['target_url'], '/' ), 'Target URLs must remain root-relative: ' . $row['target_url'] );
	$rows[] = $row;

	if ( ! isset( $keyword_to_urls[ $row['primary_keyword'] ] ) ) {
		$keyword_to_urls[ $row['primary_keyword'] ] = array();
	}
	$keyword_to_urls[ $row['primary_keyword'] ][] = $row['target_url'];

	if ( 'existing' === $row['url_state'] && 'keep-improve' === $row['decision'] ) {
		$existing_targets[] = $row['target_url'];
	}
}
fclose( $handle );

assert_true( count( $rows ) >= 40, 'Seed map is unexpectedly incomplete.' );

foreach ( $keyword_to_urls as $keyword => $urls ) {
	$unique_urls = array_values( array_unique( $urls ) );
	assert_true( 1 === count( $unique_urls ), 'Primary keyword maps to more than one canonical URL: ' . $keyword );
}

require_once $root . '/theme-src/hea-lth-portal/inc/portal-route-registry.php';

$route_keys = array(
	'aesthetic_medicine',
	'plastic_surgery_consultation',
	'hair_transplant_consultation',
	'nose_surgery_price',
	'hair_transplant_israel_price',
	'laser_hair_removal_private',
	'private_doctor_appointment',
	'mri_ct_appointment',
	'medical_second_opinion',
	'health_insurance_refund',
	'doctor_home_visit',
	'premium_health_services',
	'doctor_clinic_index',
	'rhinoplasty_discovery',
	'hair_transplant_discovery',
);

foreach ( $route_keys as $route_key ) {
	$path = str_replace( 'https://hea-lth.co.il', '', hea_lth_portal_route( $route_key ) );
	assert_true( in_array( $path, $existing_targets, true ), 'Canonical route is not an existing keep-improve target: ' . $route_key );
}

assert_true(
	'https://hea-lth.co.il/plastic-surgery-consultation/' === hea_lth_portal_route( 'rhinoplasty_discovery' ),
	'Rhinoplasty discovery must not expose the held /nose-surgery/ route.'
);

echo "Keyword URL map contract passed for " . count( $rows ) . " seed rows.\n";
