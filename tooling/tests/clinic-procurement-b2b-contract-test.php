<?php
/** Contract checks for clinic procurement and B2B intake. */

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . PHP_EOL );
		exit( 1 );
	}
}

$root        = dirname( __DIR__, 2 );
$core        = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-platform-core.php' );
$intake      = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-b2b-intake.php' );
$provisioner = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-clinic-provisioner.php' );
$plan        = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/single-hp_clinic_plan.php' );
$form        = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/template-parts/b2b-intake-form.php' );
$routes      = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/inc/portal-route-registry.php' );

assert_true( false !== strpos( $core, "'hp_clinic_plan'" ), 'Clinic-plan entity must be registered.' );
assert_true( false !== strpos( $core, "'hp_b2b_request'" ), 'Private B2B request entity must be registered.' );
assert_true( false !== strpos( $core, "'slug' => 'professionals/clinic-build'" ), 'Clinic plans must own the governed route family.' );
assert_true( false !== strpos( $core, "'hp_procurement'" ), 'A procurement taxonomy must connect plans and equipment.' );
assert_true( false !== strpos( $intake, "'post_status' => 'private'" ), 'B2B requests must be private.' );
assert_true( false !== strpos( $intake, 'wp_verify_nonce' ), 'Public intake must verify its nonce.' );
assert_true( false !== strpos( $intake, "'clinic_quote', 'supplier_join'" ), 'Only the two controlled B2B request types are accepted.' );
assert_true( false === stripos( $form, 'diagnosis' ) && false === stripos( $form, 'symptom' ), 'B2B forms must not collect clinical details.' );
assert_true( false !== strpos( $form, 'contact_consent' ), 'B2B forms must record contact consent.' );
assert_true( false !== strpos( $provisioner, 'weight-management-aesthetics' ), 'The first full clinic plan must be provisioned.' );
assert_true( false !== strpos( $provisioner, 'clinic-software' ) && false !== strpos( $provisioner, 'finance-growth' ), 'The plan must extend beyond machines into operations and growth.' );
assert_true( false !== strpos( $plan, "'hp_equipment'" ) && false !== strpos( $plan, "'hp_procurement'" ), 'Clinic plan must resolve connected canonical equipment.' );
assert_true( false !== strpos( $routes, "'supplier_join'" ) && false !== strpos( $routes, "'clinic_build'" ), 'All new public roots must be governed by the route registry.' );

echo "Clinic procurement and B2B intake contract passed.\n";
