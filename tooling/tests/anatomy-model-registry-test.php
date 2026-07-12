<?php
/**
 * CLI regression test for the public anatomy-model gate.
 *
 * This uses narrow WordPress function stubs so it can run without connecting
 * to an installation. It proves the registry returns a runtime model only
 * after every contract, clinical, visual, performance, and delivery condition
 * is present.
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

function register_rest_route( $namespace, $route, $args ) {
}

function rest_ensure_response( $value ) {
	return $value;
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

function wp_http_validate_url( $url ) {
	return filter_var( $url, FILTER_VALIDATE_URL ) ? $url : false;
}

function sanitize_text_field( $value ) {
	return trim( strip_tags( (string) $value ) );
}

function sanitize_textarea_field( $value ) {
	return trim( strip_tags( (string) $value ) );
}

function sanitize_key( $value ) {
	return strtolower( preg_replace( '/[^a-z0-9_-]/', '', (string) $value ) );
}

function absint( $value ) {
	return abs( (int) $value );
}

function wp_json_encode( $value, $options = 0 ) {
	return json_encode( $value, $options );
}

function wp_unslash( $value ) {
	return $value;
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

require_once dirname( __DIR__, 2 ) . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-anatomy-model-registry.php';

$approved_manifest = array(
	'modelId' => 'adult-human-v1',
	'version' => '1.0.0',
	'status'  => 'approved',
	'license' => array(
		'owner'                 => 'Licensed anatomy vendor',
		'sourceUrl'             => 'https://vendor.example.com/anatomy/adult-human',
		'webDeliveryAllowed'    => true,
		'derivativeUseAllowed'  => true,
		'contractReference'     => 'HEA-3D-2026-001',
		'attributionRequired'   => false,
		'reviewedAt'            => '2026-07-11',
	),
	'clinicalReview' => array(
		'status'     => 'approved',
		'owner'      => 'Clinical reviewer',
		'reviewedAt' => '2026-07-11',
	),
	'asset' => array(
		'sourceGlb' => '/protected/anatomy/adult-human-source.glb',
		'lods'      => array(
			array(
				'id'              => 'lod-0',
				'path'            => '/wp-content/uploads/anatomy/adult-human-desktop.glb',
				'purpose'         => 'desktop',
				'triangleCount'   => 48000,
				'compressedBytes' => 4800000,
			),
			array(
				'id'              => 'lod-1',
				'path'            => '/wp-content/uploads/anatomy/adult-human-detail.glb',
				'purpose'         => 'detail',
				'triangleCount'   => 128000,
				'compressedBytes' => 12800000,
			),
		),
		'validation' => array(
			'gltfValid'     => true,
			'visualQa'      => 'passed',
			'performanceQa' => 'passed',
		),
		'quality' => array(
			'sourceTriangleCount'  => 132000,
			'anatomicalFidelityQa' => 'passed',
			'semanticMeshQa'       => 'passed',
		),
	),
	'layers' => array(
		array(
			'id'             => 'skin',
			'kind'           => 'surface',
			'meshIds'        => array( 'skin.outer' ),
			'defaultVisible' => true,
		),
		array(
			'id'             => 'respiratory',
			'kind'           => 'system',
			'meshIds'        => array( 'respiratory.nasal-cavity' ),
			'defaultVisible' => false,
		),
	),
	'structures' => array(
		array(
			'id'      => 'anatomy:nose',
			'meshIds' => array( 'face.nose.external', 'respiratory.nasal-cavity' ),
			'labels'  => array( 'he' => 'אף', 'en' => 'Nose' ),
			'contexts' => array(
				array(
					'id'      => 'breathing',
					'labelHe' => 'נשימה',
					'resolverEntityIds' => array(
						'topics'              => array( 'topic:nasal-breathing' ),
						'specialties'         => array( 'specialty:otolaryngology' ),
						'treatments'          => array(),
						'equipmentCategories' => array(),
					),
				),
			),
		),
	),
);

$hea_lth_test_options['hea_lth_anatomy_model_manifest'] = json_encode( $approved_manifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
$public = Hea_Lth_Anatomy_Model_Registry::get_public_configuration();
assert_same( 'approved', $public['status'], 'An approved manifest must produce a public runtime configuration.' );
assert_same( 'three-webgl', $public['engine'], 'The approved asset must target the owned Three.js engine.' );
assert_same( '/wp-content/uploads/anatomy/adult-human-desktop.glb', $public['asset']['lods'][0]['path'], 'The public response must expose the approved runtime LOD.' );
assert_same( false, array_key_exists( 'license', $public ), 'The public response must not expose contract metadata.' );
assert_same( false, array_key_exists( 'sourceGlb', $public['asset'] ), 'The public response must not expose the protected source asset path.' );
assert_same( false, array_key_exists( 'contractReference', $public ), 'The public response must not expose a contract reference.' );
assert_same( 'nose', $public['structures'][0]['regionId'], 'The public structure must resolve to the anatomy region ID.' );

$blocked_manifest = $approved_manifest;
$blocked_manifest['clinicalReview']['status'] = 'in-review';
$hea_lth_test_options['hea_lth_anatomy_model_manifest'] = json_encode( $blocked_manifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
$blocked = Hea_Lth_Anatomy_Model_Registry::get_public_configuration();
assert_same( 'license-gated', $blocked['status'], 'An in-review clinical record must block public asset delivery.' );
assert_same( 'none', $blocked['engine'], 'A blocked asset must not request the WebGL engine.' );
assert_same( false, array_key_exists( 'asset', $blocked ), 'A blocked asset must not expose a runtime asset path.' );

$external_asset_manifest = $approved_manifest;
foreach ( $external_asset_manifest['asset']['lods'] as $index => $lod ) {
	$external_asset_manifest['asset']['lods'][ $index ]['path'] = 'https://cdn.example.com/anatomy/' . $lod['id'] . '.glb';
}
$hea_lth_test_options['hea_lth_anatomy_model_manifest'] = json_encode( $external_asset_manifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
$external_asset = Hea_Lth_Anatomy_Model_Registry::get_public_configuration();
assert_same( 'license-gated', $external_asset['status'], 'An unallowlisted external asset path must block public model delivery.' );

$low_poly_manifest = $approved_manifest;
$low_poly_manifest['asset']['quality']['sourceTriangleCount'] = 4672;
$low_poly_manifest['asset']['lods'][1]['triangleCount'] = 4672;
$hea_lth_test_options['hea_lth_anatomy_model_manifest'] = json_encode( $low_poly_manifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
$low_poly = Hea_Lth_Anatomy_Model_Registry::get_public_configuration();
assert_same( 'license-gated', $low_poly['status'], 'A low-poly generic-person candidate must not activate the public anatomy engine.' );
assert_same( 'anatomy-quality-not-approved', $low_poly['reason'], 'A low-poly generic-person candidate must fail the anatomy-quality gate.' );

echo "Anatomy model registry gate tests passed.\n";
