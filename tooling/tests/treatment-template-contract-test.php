<?php
/**
 * Contract test for future public treatment pages.
 *
 * A treatment can never become a public page merely because a post exists.
 * It must satisfy the shared editorial evidence gate and remain free of
 * uncontrolled intake or private routing fields.
 */

declare( strict_types = 1 );

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . "\n" );
		exit( 1 );
	}
}

$root      = dirname( __DIR__, 2 );
$shell     = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/single-hp_treatment.php' );
$template  = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/template-parts/treatment-public.php' );
$styles    = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/assets/css/templates.css' );

assert_true( false !== strpos( $shell, 'hea_lth_portal_get_editorial_status' ), 'Treatment shell must use the shared editorial status function.' );
assert_true( false !== strpos( $shell, "'hp_treatment' !== get_post_type" ), 'Treatment shell must be bound to the treatment content type.' );
assert_true( false !== strpos( $shell, 'status_header( 404 )' ), 'Unreviewed treatment pages must return not found.' );
assert_true( false !== strpos( $template, "! \$editorial_status['is_reviewed']" ), 'Treatment content cannot render without reviewed status.' );
assert_true( false !== strpos( $template, "'hp_treatment' !== get_post_type" ), 'Treatment partial must repeat the content-type guard.' );
assert_true( false === strpos( $template, "'hp_source_note'" ), 'Treatment template must rely on the shared source-note status rather than raw metadata.' );
assert_true( false !== strpos( $template, "hea_lth_portal_foundation_route( 'find_care' )" ), 'Treatment next-step CTA must use the controlled care route.' );
assert_true( false !== strpos( $template, "hea_lth_portal_route( 'doctor_clinic_index' )" ), 'Treatment template must use the canonical directory route.' );
assert_true( false !== strpos( $template, 'hea_lth_portal_render_information_boundary( true )' ), 'Treatment page must carry the information boundary.' );
assert_true( false === strpos( $template, '<form' ), 'Treatment page must not create an uncontrolled medical intake form.' );
assert_true( false === strpos( $template, 'hp_route_' ), 'Treatment page must not read private lead-route fields.' );
assert_true( false !== strpos( $styles, '.hp-page-hero--treatment-detail' ), 'Treatment detail needs a dedicated responsive visual system.' );
assert_true( false !== strpos( $styles, '.hp-treatment-evidence' ), 'Treatment detail must visibly expose editorial evidence.' );

echo "Treatment template contract tests passed.\n";
