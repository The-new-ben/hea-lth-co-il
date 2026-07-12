<?php
/**
 * Behavior and source contract for individual editorial review disclosure.
 */

declare( strict_types = 1 );

define( 'ABSPATH', __DIR__ . DIRECTORY_SEPARATOR );

$fixture_meta = array();

function absint( $value ) {
	return abs( (int) $value );
}

function get_the_ID() {
	return 701;
}

function get_post_meta( $post_id, $key, $single = true ) {
	global $fixture_meta;

	return $fixture_meta[ $post_id ][ $key ] ?? '';
}

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . "\n" );
		exit( 1 );
	}
}

require_once dirname( __DIR__, 2 ) . '/theme-src/hea-lth-portal/inc/portal-template-helpers.php';

$fixture_meta[701] = array(
	'hp_editorial_state' => 'approved',
	'hp_last_reviewed'   => '2026-07-11',
	'hp_source_note'     => 'Verified source note',
);
$reviewed = hea_lth_portal_get_editorial_status( 701 );

assert_true( true === $reviewed['is_reviewed'], 'Approved content with review date and source must disclose reviewed status.' );
assert_true( '2026-07-11' === $reviewed['last_reviewed'], 'Reviewed status must retain its review date.' );
assert_true( 'Verified source note' === $reviewed['source_note'], 'Reviewed status must retain its source note.' );

$fixture_meta[702] = array(
	'hp_editorial_state' => 'approved',
	'hp_last_reviewed'   => '2026-07-11',
	'hp_source_note'     => '',
);
$unreviewed = hea_lth_portal_get_editorial_status( 702 );

assert_true( false === $unreviewed['is_reviewed'], 'Missing source note must prevent a reviewed disclosure.' );

$single = (string) file_get_contents( dirname( __DIR__, 2 ) . '/theme-src/hea-lth-portal/single.php' );
assert_true( false !== strpos( $single, 'hea_lth_portal_get_editorial_status' ), 'Single template must read the explicit editorial status.' );
assert_true( false !== strpos( $single, 'hp-editorial-status__evidence' ), 'Single template must render review evidence when available.' );
assert_true( false !== strpos( $single, 'אינה מוצגת בפידים של תוכן שנבדק' ), 'Single template must state the unreviewed feed boundary truthfully.' );

echo "Single editorial status contract passed.\n";
