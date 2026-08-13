<?php
/** Contract checks for supplier showroom architecture. */

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . PHP_EOL );
		exit( 1 );
	}
}

$root        = dirname( __DIR__, 2 );
$core        = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-platform-core.php' );
$provisioner = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-showroom-provisioner.php' );
$routes      = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/inc/portal-route-registry.php' );
$supplier    = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/single-hp_supplier.php' );
$equipment   = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/single-hp_equipment.php' );

assert_true( false !== strpos( $core, "'hp_supplier'" ), 'Supplier entity must be registered.' );
assert_true( false !== strpos( $core, "'slug' => 'medical-equipment'" ), 'Equipment must use its canonical route family.' );
assert_true( false !== strpos( $core, "'hp_supplier_id'" ), 'Equipment must have a typed supplier relationship.' );
assert_true( false !== strpos( $routes, "'suppliers'" ), 'Supplier index must be in the governed route registry.' );
assert_true( false !== strpos( $provisioner, "'supplier:nubway'" ) || false !== strpos( $provisioner, "'nubway'" ), 'NUBWAY showroom must be provisioned.' );
assert_true( false !== strpos( $provisioner, "'galaxy'" ), 'Galaxy showroom must be provisioned.' );
assert_true( 0 === preg_match( '/[\'\"]05\d[\s-]?\d{7}[\'\"]/', $provisioner ), 'Private mobile details must not enter the public seed.' );
assert_true( false !== strpos( $supplier, "'hp_supplier_id'" ), 'Showroom must query its own equipment.' );
assert_true( false === strpos( $supplier, '<main' ), 'Showroom content must not nest a second main landmark inside the theme shell.' );
assert_true( false !== strpos( $equipment, 'get_permalink( $supplier_id )' ), 'Equipment must link back to its supplier showroom.' );

echo "Supplier showroom contract passed.\n";
