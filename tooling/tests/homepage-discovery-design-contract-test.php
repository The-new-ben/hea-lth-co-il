<?php
/**
 * Source contract for the premium homepage discovery surface.
 *
 * This protects three non-negotiables: a task-first visitor interface, the
 * approved route registry, and the removal of the discarded abstract-orbit
 * visual. It does not render a browser or modify WordPress.
 */

declare( strict_types = 1 );

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . "\n" );
		exit( 1 );
	}
}

$root       = dirname( __DIR__, 2 );
$homepage   = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/front-page.php' );
$header     = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/header.php' );
$css        = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/assets/css/portal.css' );
$functions  = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/functions.php' );

assert_true( false !== strpos( $homepage, 'hp-care-navigator' ), 'Homepage must expose the task-first care navigator.' );
assert_true( false !== strpos( $homepage, 'data-explorer-title' ), 'Navigator must retain a dynamic, readable task result.' );
assert_true( false !== strpos( $homepage, 'טיפול או ניתוח' ), 'Navigator must include the treatment path.' );
assert_true( false !== strpos( $homepage, 'רופא או מרפאה' ), 'Navigator must include the provider path.' );
assert_true( false !== strpos( $homepage, 'בדיקה או דימות' ), 'Navigator must include the diagnostic path.' );
assert_true( false !== strpos( $homepage, 'ציוד וטכנולוגיה' ), 'Navigator must include the technology path.' );
assert_true( false === strpos( $homepage, 'hp-orbit' ), 'Discarded abstract orbit markup must not remain on the homepage.' );
assert_true( false === strpos( $homepage, 'hp-map-line' ), 'Homepage must not simulate unverified map geometry.' );
assert_true( false === strpos( $homepage, 'hp-map-marker' ), 'Homepage must not simulate unverified provider locations.' );
assert_true( false !== strpos( $homepage, 'hp-directory-preview__map-gate' ), 'Homepage directory preview must disclose the map release gate.' );
assert_true( false === strpos( $homepage, 'Private Concierge' ), 'Homepage must not expose product-team English labels.' );
assert_true( false === strpos( $homepage, 'Future Medicine' ), 'Homepage must not expose product-team English labels.' );
assert_true( false === strpos( $homepage, '/plastic-surgery/rhinoplasty/' ), 'Homepage must not link to the unapproved rhinoplasty path.' );
assert_true( false === strpos( $homepage, '/hair-and-scalp/' ), 'Homepage must not link hair-transplant traffic to the retired broad path.' );
assert_true( false !== strpos( $homepage, "hea_lth_portal_route( 'rhinoplasty_discovery' )" ), 'Rhinoplasty discovery must use the canonical route registry.' );
assert_true( false !== strpos( $header, '<rect x="3" y="3" width="42" height="42" rx="14" fill="currentColor"/>' ), 'Header must use the working H monogram geometry.' );
assert_true( false === strpos( $header, 'M24 3C12.4' ), 'Generic legacy medical-cross SVG must not remain in the header.' );
assert_true( false !== strpos( $css, '.hp-care-navigator' ), 'Care navigator requires dedicated visual tokens.' );
assert_true( false === strpos( $css, '.hp-hero__explorer' ), 'Discarded explorer styles must not remain in the design system.' );
assert_true( false === strpos( $css, '.hp-map-line' ), 'Deprecated mock-map line styling must not remain in the design system.' );
assert_true( false === strpos( $css, '.hp-map-marker' ), 'Deprecated mock-map marker styling must not remain in the design system.' );
$fonts_css = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/assets/css/fonts.css' );

assert_true( false !== strpos( $functions, "get_theme_file_uri( 'assets/css/fonts.css' )" ), 'The theme must enqueue the self-hosted font stylesheet.' );
assert_true( false === strpos( $functions, 'fonts.googleapis.com' ), 'The theme must not load fonts from a third-party CDN (AGENTS.md).' );
assert_true( false === strpos( $functions, 'fonts.gstatic.com' ), 'The theme must not preconnect to a third-party font origin.' );
assert_true( false !== strpos( $fonts_css, '"Noto Sans Hebrew"' ), 'The self-hosted font kit must declare the Hebrew UI family.' );
assert_true( false !== strpos( $fonts_css, '"Noto Serif Hebrew"' ), 'The self-hosted font kit must declare the Hebrew editorial family.' );

$font_files = array(
	'noto-sans-hebrew-var-hebrew.woff2',
	'noto-sans-hebrew-var-latin.woff2',
	'noto-serif-hebrew-var-hebrew.woff2',
	'noto-serif-hebrew-var-latin.woff2',
);

foreach ( $font_files as $font_file ) {
	$font_path = $root . '/theme-src/hea-lth-portal/assets/fonts/' . $font_file;
	assert_true( is_file( $font_path ) && filesize( $font_path ) > 4000, 'Vendored font file missing or truncated: ' . $font_file );
	$signature = (string) file_get_contents( $font_path, false, null, 0, 4 );
	assert_true( 'wOF2' === $signature, 'Vendored font must be a valid woff2 binary: ' . $font_file );
}

echo "Homepage discovery design contract passed.\n";
