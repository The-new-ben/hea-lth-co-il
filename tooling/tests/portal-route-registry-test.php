<?php
/**
 * Regression checks for the canonical migration route registry.
 *
 * The test proves high-intent homepage routes resolve only to current,
 * approved existing pages. It does not call WordPress or alter the live site.
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

require_once dirname( __DIR__, 2 ) . '/theme-src/hea-lth-portal/inc/portal-route-registry.php';

$expected = array(
	'aesthetic_medicine'        => 'https://hea-lth.co.il/aesthetic-medicine-treatments/',
	'plastic_surgery_consultation' => 'https://hea-lth.co.il/plastic-surgery-consultation/',
	'hair_transplant_consultation' => 'https://hea-lth.co.il/hair-transplant-consultation/',
	'rhinoplasty_discovery'     => 'https://hea-lth.co.il/plastic-surgery-consultation/',
	'hair_transplant_discovery' => 'https://hea-lth.co.il/hair-transplant-consultation/',
	'doctor_clinic_index'       => 'https://hea-lth.co.il/doctor-clinic-index/',
);

foreach ( $expected as $route_key => $url ) {
	assert_true( $url === hea_lth_portal_route( $route_key ), 'Unexpected route for ' . $route_key . '.' );
}

$foundation_expected = array(
	'guides'                      => 'https://hea-lth.co.il/guides/',
	'diagnostics_imaging'         => 'https://hea-lth.co.il/diagnostics/imaging/',
	'health_technology_equipment' => 'https://hea-lth.co.il/health-technology/equipment/',
	'professional_profile_update' => 'https://hea-lth.co.il/professionals/profile-update/',
);

foreach ( $foundation_expected as $route_key => $url ) {
	assert_true( $url === hea_lth_portal_foundation_route( $route_key ), 'Unexpected foundation route for ' . $route_key . '.' );
	assert_true( 'evidence-gated' === hea_lth_portal_foundation_routes()[ $route_key ]['release'], 'Foundation route must remain evidence-gated: ' . $route_key . '.' );
}

assert_true(
	'https://hea-lth.co.il/' === hea_lth_portal_route( 'unapproved_future_route' ),
	'Unknown keys must not expose an invented public route.'
);

assert_true(
	'https://hea-lth.co.il/' === hea_lth_portal_foundation_route( 'unapproved_future_route' ),
	'Unknown foundation keys must not expose an invented hierarchy route.'
);

$source_files = array(
	'/theme-src/hea-lth-portal/404.php',
	'/theme-src/hea-lth-portal/footer.php',
	'/theme-src/hea-lth-portal/front-page.php',
	'/theme-src/hea-lth-portal/functions.php',
	'/theme-src/hea-lth-portal/header.php',
	'/theme-src/hea-lth-portal/search.php',
	'/theme-src/hea-lth-portal/page-templates/template-account.php',
	'/theme-src/hea-lth-portal/page-templates/template-anatomy.php',
	'/theme-src/hea-lth-portal/page-templates/template-directory.php',
	'/theme-src/hea-lth-portal/page-templates/template-guides.php',
	'/theme-src/hea-lth-portal/page-templates/template-health-technology.php',
	'/theme-src/hea-lth-portal/page-templates/template-professionals.php',
	'/theme-src/hea-lth-portal/page-templates/template-treatment-hub.php',
);

foreach ( $source_files as $source_file ) {
	$source = (string) file_get_contents( dirname( __DIR__, 2 ) . $source_file );
	assert_true( 0 === preg_match( "/home_url\\( '\\/[A-Za-z]/", $source ), 'Named portal routes must come from the controlled registries: ' . $source_file );
}

echo "Portal route registry tests passed.\n";
