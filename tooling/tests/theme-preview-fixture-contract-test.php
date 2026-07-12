<?php
/**
 * Prevent local visual fixtures from being mistaken for public content.
 */

declare( strict_types = 1 );

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . "\n" );
		exit( 1 );
	}
}

$preview = (string) file_get_contents( dirname( __DIR__, 2 ) . '/tooling/theme-preview/index.php' );

assert_true( false !== strpos( $preview, "'profile-preview'" ), 'Local preview must expose the profile layout fixture.' );
assert_true( false !== strpos( $preview, "'treatment-preview'" ), 'Local preview must expose the treatment layout fixture.' );
assert_true( false !== strpos( $preview, 'data-preview-fixture="true"' ), 'Fixture renders must carry an explicit non-production marker.' );
assert_true( false !== strpos( $preview, 'אין כאן פרופיל, טיפול או מידע רפואי לפרסום' ), 'Fixture notice must prevent a misleading visual handoff.' );
assert_true( false !== strpos( $preview, "'hp_public_state' => 'verified'" ), 'Profile fixture must satisfy the same source gate as the template.' );
assert_true( false !== strpos( $preview, "'hp_editorial_state' => 'approved'" ), 'Treatment fixture must satisfy the shared editorial gate.' );
assert_true( false !== strpos( $preview, 'ואינו תוכן רפואי לפרסום' ), 'Treatment fixture must state that it is not publication content.' );

echo "Theme preview fixture contract tests passed.\n";
