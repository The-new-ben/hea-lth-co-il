<?php
/**
 * Contract test for anatomy-to-directory routing.
 *
 * The test verifies that the public anatomy resolver can express a body
 * region, and that the verified-provider directory model accepts the same
 * filter. It never creates providers or sends a lead.
 */

declare( strict_types = 1 );

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . "\n" );
		exit( 1 );
	}
}

$root       = dirname( __DIR__, 2 );
$core       = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-platform-core.php' );
$controller = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-directory-controller.php' );
$template   = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/page-templates/template-anatomy.php' );
$resolver   = json_decode( (string) file_get_contents( $root . '/theme-src/hea-lth-portal/assets/data/anatomy-discovery-v1.json' ), true );

assert_true( is_array( $resolver ), 'Anatomy resolver must remain valid JSON.' );
assert_true( false !== strpos( $core, "'hp_provider', 'hp_clinic', 'hp_treatment', 'hp_equipment'" ), 'Body-region taxonomy must be available to providers and clinics.' );
assert_true( false !== strpos( $controller, "'body_region' => array(" ), 'Directory REST endpoint must accept body_region.' );
assert_true( false !== strpos( $controller, "'body_region' => 'hp_body_region'" ), 'Directory REST endpoint must map body_region to the controlled taxonomy.' );
assert_true( false !== strpos( $controller, "'bodyRegions'" ), 'Directory response must expose only controlled public body-region labels.' );
assert_true( false !== strpos( $template, "hea_lth_portal_route( 'doctor_clinic_index' )" ), 'Anatomy fallback must use the canonical directory route.' );
assert_true( false === strpos( $template, 'Future Medicine' ), 'Public anatomy screen must use Hebrew visitor language.' );

$directory_links = array();
foreach ( $resolver['regions'] as $region ) {
	foreach ( $region['contexts'] as $context ) {
		$routing = $context['routing']['directory'] ?? array();
		if ( isset( $routing['bodyRegion'] ) ) {
			$directory_links[] = $context['entries'];
		}
	}
}

$serialized_entries = json_encode( $directory_links, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
assert_true( false !== strpos( $serialized_entries, '"routeKey":"doctor_clinic_index"' ), 'Body-region contexts must expose the canonical directory route key.' );
assert_true( false !== strpos( $serialized_entries, '"body_region"' ), 'Body-region contexts must retain the controlled directory query parameter.' );
assert_true( false === strpos( $serialized_entries, '"url"' ), 'Body-region contexts must not bypass the route registry with raw URLs.' );

echo "Anatomy directory routing contract passed.\n";
