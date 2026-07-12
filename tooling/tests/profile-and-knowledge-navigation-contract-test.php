<?php
/**
 * Contract test for the public-profile shell and the knowledge mega menu.
 *
 * These files are source foundations only. The test proves that a future
 * release cannot quietly remove the verified-state boundary or expose private
 * routing metadata while adding profile routes.
 */

declare( strict_types = 1 );

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . "\n" );
		exit( 1 );
	}
}

$root             = dirname( __DIR__, 2 );
$header           = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/header.php' );
$profile_template = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/template-parts/profile-public.php' );
$provider_shell   = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/single-hp_provider.php' );
$clinic_shell     = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/single-hp_clinic.php' );
$styles           = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/assets/css/templates.css' );

assert_true( false !== strpos( $header, 'aria-controls="mega-knowledge"' ), 'Primary navigation must expose the knowledge mega trigger.' );
assert_true( false !== strpos( $header, 'id="mega-knowledge" hidden data-mega-panel' ), 'Knowledge panel must use the shared accessible mega-panel contract.' );
assert_true( false !== strpos( $header, "hea_lth_portal_foundation_route( 'glossary' )" ), 'Knowledge menu must link to the governed glossary route.' );
assert_true( false !== strpos( $header, "hea_lth_portal_foundation_route( 'anatomy' )" ), 'Knowledge menu must link to the anatomy discovery route.' );
assert_true( false !== strpos( $header, "hea_lth_portal_foundation_route( 'health_technology' )" ), 'Knowledge menu must link to the health-technology route.' );

assert_true( false !== strpos( $provider_shell, "get_template_part( 'template-parts/profile', 'public' )" ), 'Provider route must use the shared profile template.' );
assert_true( false !== strpos( $clinic_shell, "get_template_part( 'template-parts/profile', 'public' )" ), 'Clinic route must use the shared profile template.' );
assert_true( false !== strpos( $profile_template, '"verified" !== get_post_meta( $profile_id, "hp_public_state", true )' ) || false !== strpos( $profile_template, "'verified' !== get_post_meta( \$profile_id, 'hp_public_state', true )" ), 'Public profile template must stop unverified records before rendering.' );
assert_true( false !== strpos( $profile_template, 'status_header( 404 )' ), 'Unavailable profiles must be returned as not found.' );
assert_true( false !== strpos( $profile_template, "hea_lth_portal_foundation_route( 'find_care' )" ), 'Profile CTA must use the governed care-discovery route.' );
assert_true( false !== strpos( $profile_template, 'hea_lth_portal_render_information_boundary( true )' ), 'Profile pages must retain the information boundary.' );
assert_true( false === strpos( $profile_template, 'hp_route_' ), 'Public profile template must never read private lead-route fields.' );
assert_true( false !== strpos( $profile_template, 'hp_public_disclosure' ), 'Approved public disclosure must remain visible when supplied.' );
assert_true( false !== strpos( $profile_template, 'esc_html( $term->name )' ), 'Taxonomy labels must be escaped before rendering.' );
assert_true( false !== strpos( $styles, '.hp-page-hero--profile' ), 'Profile template must have its own responsive visual system.' );
assert_true( false !== strpos( $styles, '.hp-profile-layout' ), 'Profile detail layout must be present.' );

echo "Profile and knowledge navigation contract tests passed.\n";
