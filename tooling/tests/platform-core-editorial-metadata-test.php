<?php
/**
 * Regression test for editorial review metadata on powered WordPress URLs.
 */

declare( strict_types = 1 );

define( 'ABSPATH', __DIR__ . DIRECTORY_SEPARATOR );

$hea_lth_registered_meta = array();

function register_post_meta( $post_type, $key, $args ) {
	global $hea_lth_registered_meta;

	$hea_lth_registered_meta[ $post_type ][ $key ] = $args;
}

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . "\n" );
		exit( 1 );
	}
}

require_once dirname( __DIR__, 2 ) . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-platform-core.php';

Hea_Lth_Platform_Core::register_metadata();

foreach ( array( 'post', 'page', 'hp_treatment', 'hp_glossary', 'hp_equipment' ) as $post_type ) {
	assert_true( isset( $hea_lth_registered_meta[ $post_type ]['hp_editorial_state'] ), $post_type . ' must receive editorial state metadata.' );
	assert_true( isset( $hea_lth_registered_meta[ $post_type ]['hp_last_reviewed'] ), $post_type . ' must receive review-date metadata.' );
	assert_true( isset( $hea_lth_registered_meta[ $post_type ]['hp_source_note'] ), $post_type . ' must receive source-note metadata.' );
	assert_true( true === $hea_lth_registered_meta[ $post_type ]['hp_editorial_state']['show_in_rest'], $post_type . ' review metadata must remain editor-manageable.' );
}

echo "Platform editorial metadata tests passed.\n";
