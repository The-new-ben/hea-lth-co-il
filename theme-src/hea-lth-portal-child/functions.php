<?php
/**
 * Controlled production overrides for the Hea-lth Portal child theme.
 *
 * @package HeaLthPortalChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HEA_LTH_PORTAL_CHILD_VERSION', '0.1.0' );

/**
 * Load only child-level styles after the tokenized parent asset stack.
 *
 * @return void
 */
function hea_lth_portal_child_enqueue_assets() {
	wp_enqueue_style(
		'hea-lth-portal-child',
		get_stylesheet_uri(),
		array( 'hea-lth-portal-templates' ),
		HEA_LTH_PORTAL_CHILD_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'hea_lth_portal_child_enqueue_assets', 20 );

/**
 * Register a narrow public release-verification surface for the activated
 * production child theme.
 *
 * @return void
 */
function hea_lth_portal_child_register_healthcheck() {
	register_rest_route(
		'hea-lth-portal/v1',
		'/healthcheck',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'permission_callback' => '__return_true',
			'callback'            => 'hea_lth_portal_child_public_healthcheck',
		)
	);
}
add_action( 'rest_api_init', 'hea_lth_portal_child_register_healthcheck' );

/**
 * Confirm that the activated child theme and its persisted release record
 * agree. This route intentionally returns only release identity.
 *
 * @param WP_REST_Request $request REST request.
 * @return WP_REST_Response|WP_Error
 */
function hea_lth_portal_child_public_healthcheck( WP_REST_Request $request ) {
	$release = get_option( 'hea_lth_agent_release_hea_lth_portal_child', array() );
	if ( ! is_array( $release ) || ! isset( $release['deployment_id'], $release['version'] ) ) {
		return new WP_Error( 'hea_lth_theme_release_unknown', 'No verified theme release record is available.', array( 'status' => 503 ) );
	}

	$deployment_id = sanitize_text_field( (string) $release['deployment_id'] );
	$version       = sanitize_text_field( (string) $release['version'] );
	if ( ! hash_equals( HEA_LTH_PORTAL_CHILD_VERSION, $version ) || '' === $deployment_id ) {
		return new WP_Error( 'hea_lth_theme_release_mismatch', 'The active theme does not match its verified release record.', array( 'status' => 503 ) );
	}

	$expected_deployment = sanitize_text_field( (string) $request->get_param( 'deployment' ) );
	if ( '' !== $expected_deployment && ! hash_equals( $deployment_id, $expected_deployment ) ) {
		return new WP_Error( 'hea_lth_theme_release_not_current', 'The requested deployment is not the active release.', array( 'status' => 409 ) );
	}

	return rest_ensure_response(
		array(
			'status'        => 'ok',
			'component'     => 'hea-lth-portal-child',
			'version'       => HEA_LTH_PORTAL_CHILD_VERSION,
			'deployment_id' => $deployment_id,
		)
	);
}
