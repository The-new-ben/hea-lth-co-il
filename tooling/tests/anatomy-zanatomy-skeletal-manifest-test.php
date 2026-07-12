<?php
/**
 * Contract test: the shipped Z-Anatomy skeletal manifest passes the real public
 * gate and produces a browser-safe runtime configuration for the homepage viewer.
 *
 * It loads the canonical manifest committed at
 * design-lab/3d-human-engine/examples/z-anatomy-skeletal-v1.manifest.json and
 * runs it through the actual Hea_Lth_Anatomy_Model_Registry gate with narrow
 * WordPress stubs (no installation required). If the manifest ever drifts below
 * an approvable state — bad path, missing QA flag, sub-threshold triangles,
 * leaked contract metadata — this test fails.
 */

declare( strict_types = 1 );

define( 'ABSPATH', __DIR__ . DIRECTORY_SEPARATOR );

$hea_lth_test_options = array();

function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {}
function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {}
function register_setting( $group, $name, $args = array() ) {}
function add_options_page( $page_title, $menu_title, $capability, $menu_slug, $callback ) {}
function register_rest_route( $namespace, $route, $args ) {}
function rest_ensure_response( $value ) { return $value; }
function get_option( $key, $default = false ) {
	global $hea_lth_test_options;
	return array_key_exists( $key, $hea_lth_test_options ) ? $hea_lth_test_options[ $key ] : $default;
}
function home_url( $path = '/' ) { return 'https://hea-lth.co.il' . $path; }
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function esc_url_raw( $url ) { return filter_var( $url, FILTER_SANITIZE_URL ); }
function wp_http_validate_url( $url ) { return filter_var( $url, FILTER_VALIDATE_URL ) ? $url : false; }
function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); }
function sanitize_textarea_field( $value ) { return trim( strip_tags( (string) $value ) ); }
function sanitize_key( $value ) { return strtolower( preg_replace( '/[^a-z0-9_-]/', '', (string) $value ) ); }
function absint( $value ) { return abs( (int) $value ); }
function wp_json_encode( $value, $options = 0 ) { return json_encode( $value, $options ); }
function wp_unslash( $value ) { return $value; }
function __( $value, $domain = '' ) { return $value; }
function add_settings_error( $setting, $code, $message ) {}

function assert_same( $expected, $actual, string $message ): void {
	if ( $expected !== $actual ) {
		fwrite( STDERR, $message . "\nExpected: " . var_export( $expected, true ) . "\nActual: " . var_export( $actual, true ) . "\n" );
		exit( 1 );
	}
}

function assert_true( $actual, string $message ): void {
	if ( true !== $actual ) {
		fwrite( STDERR, $message . "\nActual: " . var_export( $actual, true ) . "\n" );
		exit( 1 );
	}
}

require_once dirname( __DIR__, 2 ) . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-anatomy-model-registry.php';

$manifest_path = dirname( __DIR__, 2 ) . '/design-lab/3d-human-engine/examples/z-anatomy-skeletal-v1.manifest.json';
$raw = file_get_contents( $manifest_path );
assert_true( is_string( $raw ) && '' !== $raw, 'The canonical skeletal manifest file must exist and be readable.' );

$decoded = json_decode( $raw, true );
assert_true( is_array( $decoded ), 'The canonical skeletal manifest must be valid JSON.' );

$hea_lth_test_options['hea_lth_anatomy_model_manifest'] = $raw;
$public = Hea_Lth_Anatomy_Model_Registry::get_public_configuration();

assert_same( 'approved', $public['status'], 'The shipped Z-Anatomy skeletal manifest must pass the public gate.' );
assert_same( 'three-webgl', $public['engine'], 'The approved skeletal asset must target the owned Three.js engine.' );
assert_same( 'z-anatomy-skeletal-v1', $public['modelId'], 'The public config must expose the skeletal model id.' );

// Runtime LODs are same-origin theme assets.
$paths = array_map( static function ( $lod ) { return $lod['path']; }, $public['asset']['lods'] );
assert_true( in_array( '/wp-content/themes/hea-lth-portal/assets/models/skeletal-preview.glb', $paths, true ), 'The mobile preview LOD must be exposed to the browser.' );
assert_true( in_array( '/wp-content/themes/hea-lth-portal/assets/models/skeletal-detail.glb', $paths, true ), 'The desktop/detail LOD must be exposed to the browser.' );

// A high-detail LOD at or above the triangle floor must exist.
$has_detail = false;
foreach ( $public['asset']['lods'] as $lod ) {
	if ( 'detail' === $lod['purpose'] && (int) $lod['triangleCount'] >= 100000 ) {
		$has_detail = true;
	}
}
assert_true( $has_detail, 'A detail LOD at or above 100,000 triangles must be present.' );

// Semantic structures resolve with real, space-free mesh ids.
$region_ids = array_map( static function ( $structure ) { return $structure['regionId']; }, $public['structures'] );
assert_true( in_array( 'auditory-ossicles', $region_ids, true ), 'The auditory ossicles structure must resolve.' );
assert_true( in_array( 'femur', $region_ids, true ), 'The femur structure must resolve.' );

$ossicles = null;
foreach ( $public['structures'] as $structure ) {
	if ( 'auditory-ossicles' === $structure['regionId'] ) {
		$ossicles = $structure;
	}
}
assert_true( is_array( $ossicles ) && in_array( 'Incus.l', $ossicles['meshIds'], true ), 'Real semantic mesh names must survive normalization.' );

// The browser payload must never leak contract or protected-source metadata.
assert_same( false, array_key_exists( 'license', $public ), 'The public response must not expose license/contract metadata.' );
assert_same( false, array_key_exists( 'contractReference', $public ), 'The public response must not expose a contract reference.' );
assert_same( false, array_key_exists( 'sourceGlb', $public['asset'] ), 'The public response must not expose the protected source asset path.' );

echo "Z-Anatomy skeletal manifest gate test passed.\n";
