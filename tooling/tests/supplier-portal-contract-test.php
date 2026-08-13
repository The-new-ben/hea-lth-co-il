<?php
/**
 * Contract for supplier ownership, catalog review, and lead-release privacy.
 */

declare( strict_types = 1 );

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . "\n" );
		exit( 1 );
	}
}

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

foreach ( array( 'sanitize_key', 'sanitize_text_field', 'sanitize_textarea_field' ) as $function_name ) {
	if ( ! function_exists( $function_name ) ) {
		eval( 'function ' . $function_name . '( $value ) { return preg_replace( "/[^A-Za-z0-9_\\-:. @]/", "", (string) $value ); }' );
	}
}
if ( ! function_exists( 'absint' ) ) {
	function absint( $value ) { return abs( (int) $value ); }
}

$root = dirname( __DIR__, 2 );
require_once $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-supplier-portal.php';

assert_true( 'held' === Hea_Lth_Supplier_Portal::sanitize_release_state( 'unexpected' ), 'Unknown release state must fail closed.' );
assert_true( 'new' === Hea_Lth_Supplier_Portal::sanitize_pipeline_state( 'unexpected' ), 'Unknown pipeline state must fail closed.' );
assert_true( 'verified' === Hea_Lth_Supplier_Portal::sanitize_plan( 'unknown' ), 'Unknown membership plan must use the lowest plan.' );
assert_true( array( 7, 9 ) === Hea_Lth_Supplier_Portal::sanitize_id_list( array( 7, '7', -9, 0 ) ), 'Owner IDs must be positive and unique.' );

$class_source = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-supplier-portal.php' );
$template     = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/page-templates/template-supplier-portal.php' );
$routes       = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/inc/portal-route-registry.php' );
$provisioner  = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-page-provisioner.php' );

assert_true( false !== strpos( $class_source, "'hp_account_user_ids'" ), 'Supplier accounts require explicit ownership metadata.' );
assert_true( false !== strpos( $class_source, "'hea_lth_supplier_id'" ), 'Supplier ownership requires a scalable reverse user lookup.' );
assert_true( false !== strpos( $class_source, "'hp_assigned_supplier_id'" ), 'Leads require explicit supplier assignment.' );
assert_true( false !== strpos( $class_source, "'hp_lead_release_state'" ), 'Leads require an explicit release state.' );
assert_true( false !== strpos( $class_source, "if ( \$released )" ), 'Contact details must be added only inside the release gate.' );
assert_true( false !== strpos( $class_source, "'publicly_queryable'  => false" ), 'Catalog submissions must never receive a public route.' );
assert_true( false !== strpos( $class_source, 'check_admin_referer' ), 'Supplier writes must verify a nonce.' );
assert_true( false !== strpos( $class_source, 'user_owns_supplier' ) || false !== strpos( $class_source, 'supplier_for_user' ), 'Supplier actions must resolve server-side ownership.' );

assert_true( false !== strpos( $template, 'Hea_Lth_Supplier_Portal::request_view' ), 'The dashboard must use the privacy-gated lead view.' );
assert_true( false !== strpos( $template, 'wp_login_url' ), 'Logged-out supplier access must use WordPress authentication.' );
assert_true( false !== strpos( $template, 'wp_nonce_field' ), 'Dashboard writes must carry nonces.' );
assert_true( false !== strpos( $routes, "'supplier_portal'" ), 'The supplier portal route must be registered.' );
assert_true( false !== strpos( $provisioner, "'/professionals/supplier-portal/'" ), 'The supplier portal page must be provisioned.' );
assert_true( false !== strpos( $provisioner, "'noindex' => true" ), 'The private supplier portal must remain out of search.' );

foreach ( array( 'cannibalization', 'canonical', 'search intent', 'route key', 'deployment', 'release state' ) as $internal_term ) {
	assert_true( false === stripos( $template, $internal_term ), 'Internal project language reached the supplier portal: ' . $internal_term );
}

echo "Supplier portal ownership and privacy contract passed.\n";
