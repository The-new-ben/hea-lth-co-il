<?php
/**
 * Advisory-room contract test.
 *
 * Guards the invariants of the private advisory rooms: gate mechanics,
 * privacy posture, and price honesty (no invented prices on the surface).
 */

$root     = dirname( __DIR__, 2 );
$plugin   = file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-advisory-rooms.php' );
$main     = file_get_contents( $root . '/plugin-src/hea-lth-platform-core/hea-lth-platform-core.php' );
$template = file_get_contents( $root . '/theme-src/hea-lth-portal/page-templates/template-advisory-room.php' );
$css      = file_get_contents( $root . '/theme-src/hea-lth-portal/assets/css/templates.css' );

if ( false === $plugin || false === $main || false === $template || false === $css ) {
	fwrite( STDERR, "advisory-room contract: missing source files\n" );
	exit( 1 );
}

$failures = array();

// Gate mechanics: timing-safe compare, native password fallback, no cache.
if ( false === strpos( $plugin, 'hash_equals(' ) ) {
	$failures[] = 'access-code comparison must use hash_equals';
}
if ( false === strpos( $template, 'post_password_required()' ) ) {
	$failures[] = 'template must honour the native post password as fallback';
}
if ( false === strpos( $template, 'nocache_headers();' ) ) {
	$failures[] = 'template must send nocache headers';
}
if ( false === strpos( $plugin, "'post_password' => \$room['code']" ) ) {
	$failures[] = 'provisioned room pages must carry the room code as post_password';
}

// Privacy posture: noindex on every provisioned page; room content must not
// live in post_content (it renders from class data only).
if ( substr_count( $plugin, "_yoast_wpseo_meta-robots-noindex" ) < 2 ) {
	$failures[] = 'both the advisory parent and room pages must be noindexed';
}
if ( false === strpos( $plugin, "'post_content'  => ''" ) ) {
	$failures[] = 'room pages must keep post_content empty (data stays in the class)';
}

// Editorial honesty: equipment renders only in approved state, and the
// surface never invents prices (no shekel literal anywhere in the room UI).
if ( false === strpos( $template, "'approved' !== get_post_meta" ) ) {
	$failures[] = 'equipment cards must gate on approved editorial state';
}
if ( false !== strpos( $template, "\xE2\x82\xAA" ) ) {
	$failures[] = 'advisory template must not hardcode prices (shekel sign found)';
}

// Wiring: class required and booted by the plugin main file.
if ( false === strpos( $main, "includes/class-hea-lth-advisory-rooms.php" ) || false === strpos( $main, 'Hea_Lth_Advisory_Rooms::boot();' ) ) {
	$failures[] = 'plugin main file must require and boot Hea_Lth_Advisory_Rooms';
}

// Styles present for the gate and the room grid.
if ( false === strpos( $css, '.hp-advisory-gate__form' ) || false === strpos( $css, '.hp-advisory-grid' ) ) {
	$failures[] = 'templates.css must style the advisory gate and grid';
}

// Create-only provisioning: the class must never update existing pages.
if ( false !== strpos( $plugin, 'wp_update_post' ) ) {
	$failures[] = 'advisory provisioner must be create-only (wp_update_post found)';
}

// v2 decision toolkit: comparison table, interest CTAs, supplier-room branch.
if ( false === strpos( $template, 'hp-advisory-table' ) ) {
	$failures[] = 'buyer rooms must render the comparison table';
}
if ( false === strpos( $template, 'https://wa.me/' ) ) {
	$failures[] = 'rooms must carry WhatsApp interest/reply CTAs';
}
if ( false === strpos( $template, "'supplier' === \$advisory['type']" ) ) {
	$failures[] = 'template must branch for supplier rooms';
}
if ( false === strpos( $plugin, "'supplier-nicro'" ) || false === strpos( $plugin, "'supplier-galaxy'" ) || false === strpos( $plugin, "'supplier-venus'" ) ) {
	$failures[] = 'the three supplier rooms must be registered';
}

if ( $failures ) {
	foreach ( $failures as $failure ) {
		fwrite( STDERR, "advisory-room contract FAILED: {$failure}\n" );
	}
	exit( 1 );
}

echo "Advisory-room contract passed (gate, privacy, honesty, wiring).\n";
