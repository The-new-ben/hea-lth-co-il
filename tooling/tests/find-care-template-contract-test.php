<?php
/**
 * Contract test for the central care-discovery route.
 *
 * The page must route choices safely without becoming an uncontrolled medical
 * intake form or inventing a lead destination before operational approval.
 */

declare( strict_types = 1 );

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . "\n" );
		exit( 1 );
	}
}

$root     = dirname( __DIR__, 2 );
$template = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/page-templates/template-find-care.php' );
$preview  = (string) file_get_contents( $root . '/tooling/theme-preview/index.php' );
$styles   = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/assets/css/templates.css' );

assert_true( false !== strpos( $template, 'Template Name: מסלול בחירה' ), 'Care-discovery template must be selectable in WordPress.' );
assert_true( false !== strpos( $template, "hea_lth_portal_foundation_route( 'treatments' )" ), 'Treatment route must be controlled by the route registry.' );
assert_true( false !== strpos( $template, "hea_lth_portal_route( 'doctor_clinic_index' )" ), 'Directory route must remain the canonical professional index.' );
assert_true( false !== strpos( $template, "hea_lth_portal_foundation_route( 'diagnostics' )" ), 'Diagnostics route must be controlled by the route registry.' );
assert_true( false !== strpos( $template, "hea_lth_portal_route( 'medical_second_opinion' )" ), 'Second-opinion intent must retain the approved existing URL.' );
assert_true( false !== strpos( $template, "hea_lth_portal_foundation_route( 'health_technology' )" ), 'Technology route must be controlled by the route registry.' );
assert_true( false !== strpos( $template, 'hea_lth_portal_render_information_boundary( true )' ), 'Care route must include the medical-information boundary.' );
assert_true( false === strpos( $template, '<form' ), 'Care route cannot collect an uncontrolled intake.' );
assert_true( false === strpos( $template, 'wp_remote_' ), 'Care route cannot send visitor data from the template.' );
assert_true( false !== strpos( $preview, "'find-care'" ), 'Visual harness must expose the care-discovery route.' );
assert_true( false !== strpos( $styles, '.hp-page-hero--find-care' ), 'Care route must have a dedicated premium hero system.' );
assert_true( false !== strpos( $styles, '.hp-care-choice-grid' ), 'Care route must include responsive selection cards.' );

echo "Find-care template contract tests passed.\n";
