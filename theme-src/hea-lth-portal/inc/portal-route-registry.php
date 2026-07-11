<?php
/**
 * Canonical public routes for the approved migration inventory.
 *
 * A new template must not invent a public URL just because it needs a link.
 * This registry contains only routes whose current live inventory is marked
 * "existing" and "keep-improve" in the SEO source of truth. Planned URLs
 * remain deliberately absent until their research, editorial, migration, and
 * approval gates are complete.
 *
 * @package HeaLthPortal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve an approved public route from the migration registry.
 *
 * @param string $route_key Stable route identifier, never a raw path.
 * @return string Absolute public URL, or the home URL for an unknown key.
 */
function hea_lth_portal_route( $route_key ) {
	$routes = array(
		'aesthetic_medicine'            => '/aesthetic-medicine-treatments/',
		'plastic_surgery_consultation'  => '/plastic-surgery-consultation/',
		'hair_transplant_consultation'  => '/hair-transplant-consultation/',
		'nose_surgery_price'            => '/nose-surgery-cost/',
		'hair_transplant_israel_price'  => '/hair-transplant-israel-cost/',
		'laser_hair_removal_private'    => '/laser-hair-removal-private/',
		'private_doctor_appointment'    => '/private-doctor-appointment/',
		'mri_ct_appointment'            => '/mri-ct-appointment/',
		'medical_second_opinion'        => '/medical-second-opinion/',
		'health_insurance_refund'       => '/health-insurance-refund/',
		'doctor_home_visit'             => '/doctor-home-visit/',
		'premium_health_services'       => '/premium-health-services/',
		'doctor_clinic_index'           => '/doctor-clinic-index/',
		// /nose-surgery/ remains on hold. This is the approved live fallback.
		'rhinoplasty_discovery'         => '/plastic-surgery-consultation/',
		// The old source used /hair-and-scalp/, which is not a mapped live pillar.
		'hair_transplant_discovery'     => '/hair-transplant-consultation/',
	);

	/**
	 * Filters allow a later, approved migration plan to change a route in one
	 * place. They do not permit templates to bypass the registry with raw URLs.
	 */
	$routes = apply_filters( 'hea_lth_portal_canonical_routes', $routes );

	if ( ! isset( $routes[ $route_key ] ) ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			trigger_error( 'Unknown Hea-lth canonical route key: ' . (string) $route_key, E_USER_WARNING );
		}

		return home_url( '/' );
	}

	return home_url( $routes[ $route_key ] );
}

/**
 * Return the controlled portal-hierarchy route register.
 *
 * These paths describe the from-scratch portal's information architecture.
 * They are deliberately separate from hea_lth_portal_route(), which exposes
 * only current, evidence-approved SEO destinations. A foundation record is
 * not an indexation, migration, redirect, or publication approval. Before
 * production launch every record must be reconciled against the legacy crawl,
 * Search Console, backlinks, content equivalence, and the keyword-to-URL map.
 *
 * @return array<string, array{path: string, release: string}>
 */
function hea_lth_portal_foundation_routes() {
	$routes = array(
		'account'                       => array( 'path' => '/account/', 'release' => 'evidence-gated' ),
		'accessibility'                 => array( 'path' => '/accessibility/', 'release' => 'evidence-gated' ),
		'anatomy'                       => array( 'path' => '/anatomy/', 'release' => 'evidence-gated' ),
		'about'                         => array( 'path' => '/about/', 'release' => 'evidence-gated' ),
		'contact'                       => array( 'path' => '/contact/', 'release' => 'evidence-gated' ),
		'diagnostics'                   => array( 'path' => '/diagnostics/', 'release' => 'evidence-gated' ),
		'diagnostics_imaging'           => array( 'path' => '/diagnostics/imaging/', 'release' => 'evidence-gated' ),
		'diagnostics_laboratory'        => array( 'path' => '/diagnostics/laboratory/', 'release' => 'evidence-gated' ),
		'diagnostics_second_opinion'    => array( 'path' => '/diagnostics/second-opinion/', 'release' => 'evidence-gated' ),
		'editorial_policy'               => array( 'path' => '/editorial-policy/', 'release' => 'evidence-gated' ),
		'find_care'                     => array( 'path' => '/find-care/', 'release' => 'evidence-gated' ),
		'glossary'                      => array( 'path' => '/glossary/', 'release' => 'evidence-gated' ),
		'guides'                        => array( 'path' => '/guides/', 'release' => 'evidence-gated' ),
		'health_technology'             => array( 'path' => '/health-technology/', 'release' => 'evidence-gated' ),
		'health_technology_equipment'   => array( 'path' => '/health-technology/equipment/', 'release' => 'evidence-gated' ),
		'privacy'                       => array( 'path' => '/privacy/', 'release' => 'evidence-gated' ),
		'private_medicine'              => array( 'path' => '/private-medicine/', 'release' => 'evidence-gated' ),
		'professional_profile_update'   => array( 'path' => '/professionals/profile-update/', 'release' => 'evidence-gated' ),
		'professionals'                 => array( 'path' => '/professionals/', 'release' => 'evidence-gated' ),
		'skin'                          => array( 'path' => '/skin/', 'release' => 'evidence-gated' ),
		'terms'                         => array( 'path' => '/terms/', 'release' => 'evidence-gated' ),
		'treatments'                    => array( 'path' => '/treatments/', 'release' => 'evidence-gated' ),
		'wellness'                      => array( 'path' => '/wellness/', 'release' => 'evidence-gated' ),
		'wellness_prevention'           => array( 'path' => '/wellness/prevention/', 'release' => 'evidence-gated' ),
	);

	return apply_filters( 'hea_lth_portal_foundation_routes', $routes );
}

/**
 * Resolve a controlled portal-hierarchy route without granting it production
 * SEO status. Unknown keys fail closed to the homepage in the same way as the
 * canonical migration registry above.
 *
 * @param string $route_key Stable hierarchy key, never a raw path.
 * @return string
 */
function hea_lth_portal_foundation_route( $route_key ) {
	$routes = hea_lth_portal_foundation_routes();

	if ( ! isset( $routes[ $route_key ] ) || ! is_array( $routes[ $route_key ] ) || empty( $routes[ $route_key ]['path'] ) ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			trigger_error( 'Unknown Hea-lth foundation route key: ' . (string) $route_key, E_USER_WARNING );
		}

		return home_url( '/' );
	}

	return home_url( (string) $routes[ $route_key ]['path'] );
}
