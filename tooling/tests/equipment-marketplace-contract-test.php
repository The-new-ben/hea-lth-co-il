<?php
/** Contract checks for equipment discovery, comparison and private RFQ routing. */

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . PHP_EOL );
		exit( 1 );
	}
}

$root        = dirname( __DIR__, 2 );
$template    = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/page-templates/template-medical-equipment.php' );
$form        = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/template-parts/b2b-intake-form.php' );
$script      = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/assets/js/equipment-marketplace.js' );
$intake      = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-b2b-intake.php' );
$core        = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-platform-core.php' );
$provisioner = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-page-provisioner.php' );
$showrooms   = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-showroom-provisioner.php' );

assert_true( false !== strpos( $provisioner, "'/medical-equipment/'" ), 'The canonical equipment marketplace page must be provisioned.' );
assert_true( false !== strpos( $provisioner, 'template-medical-equipment.php' ), 'The marketplace must use its dedicated template.' );
assert_true( false !== strpos( $template, "'hp_editorial_state'" ) && false !== strpos( $template, "'approved'" ), 'Only approved equipment may enter the public marketplace.' );
assert_true( false !== strpos( $template, 'data-equipment-search' ) && false !== strpos( $template, 'data-equipment-family' ) && false !== strpos( $template, 'data-equipment-supplier' ), 'The catalog must support search, use-family and supplier filters.' );
assert_true( false !== strpos( $template, 'data-comparison-panel' ) && false !== strpos( $script, 'const maximum = 4' ), 'The marketplace must provide a bounded comparison workspace.' );
assert_true( false !== strpos( $form, 'name="equipment[]"' ) && false !== strpos( $form, 'data-equipment-hidden' ), 'Selected equipment must flow into the RFQ form.' );
assert_true( false !== strpos( $intake, 'get_page_by_path' ) && false !== strpos( $intake, "'hp_editorial_state'" ), 'Submitted slugs must be resolved against reviewed canonical records.' );
assert_true( false !== strpos( $intake, "'hp_candidate_supplier_ids'" ) && false !== strpos( $core, "'hp_candidate_supplier_ids'" ), 'Validated supplier candidates must be stored privately.' );
assert_true( false !== strpos( $intake, "'post_status' => 'private'" ), 'RFQ records must remain private.' );
assert_true( false !== strpos( $showrooms, "const VERSION    = '2026-08-13-03'" ) && false !== strpos( $showrooms, "'hp_editorial_state', 'approved'" ), 'The showroom data migration must refresh existing records into the approved marketplace.' );
assert_true( false === strpos( $template, '10%' ) && false === strpos( $template, 'עמלת תיווך' ) && false === strpos( $template, 'תנאי התיווך' ), 'Public marketplace content must not expose brokerage terms.' );

echo "Equipment marketplace contract passed.\n";
