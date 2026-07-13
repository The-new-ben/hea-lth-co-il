<?php
/**
 * Contract for the SEO/structured-data module.
 *
 * Verifies the JSON-LD graph is valid and self-descriptive, and that Open
 * Graph / Twitter tags are emitted — without fabricating medical content.
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

$GLOBALS['__hooks'] = array();

function add_action( $hook, $callback, $priority = 10, $args = 1 ) {
	$GLOBALS['__hooks'][ $hook ][] = $callback;
}
function add_filter( $hook, $callback, $priority = 10, $args = 1 ) {}
function is_admin() {
	return false;
}
function is_front_page() {
	return true;
}
function is_singular( $types = '' ) {
	return false;
}
function home_url( $path = '/' ) {
	return 'https://hea-lth.co.il' . $path;
}
function get_theme_file_uri( $path = '' ) {
	return 'https://hea-lth.co.il/wp-content/themes/hea-lth-portal/' . ltrim( (string) $path, '/' );
}
function get_bloginfo( $key = '' ) {
	return 'name' === $key ? 'Hea-lth' : 'פורטל בריאות פרטית';
}
function add_query_arg( $args ) {
	return '';
}
function wp_get_document_title() {
	return 'Hea-lth';
}
function esc_attr( $value ) {
	return htmlspecialchars( (string) $value, ENT_QUOTES );
}
function esc_url( $value ) {
	return (string) $value;
}
function wp_json_encode( $data, $flags = 0 ) {
	return json_encode( $data, $flags );
}
function get_option( $key ) {
	return isset( $GLOBALS['__options'][ $key ] ) ? $GLOBALS['__options'][ $key ] : false;
}

require_once dirname( __DIR__, 2 ) . '/theme-src/hea-lth-portal/inc/portal-seo.php';

// JSON-LD
ob_start();
hea_lth_portal_json_ld();
$json_ld_output = (string) ob_get_clean();

assert_true( false !== strpos( $json_ld_output, 'application/ld+json' ), 'JSON-LD must be emitted in a script tag.' );

$start = strpos( $json_ld_output, '{' );
$end   = strrpos( $json_ld_output, '}' );
$payload = substr( $json_ld_output, $start, $end - $start + 1 );
$decoded = json_decode( $payload, true );

assert_true( is_array( $decoded ), 'JSON-LD must be valid JSON.' );
assert_true( isset( $decoded['@context'] ) && 'https://schema.org' === $decoded['@context'], 'JSON-LD must declare the schema.org context.' );
assert_true( isset( $decoded['@graph'] ) && is_array( $decoded['@graph'] ) && count( $decoded['@graph'] ) >= 2, 'JSON-LD graph must contain Organization and WebSite.' );

$types = array_map(
	static function ( $node ) {
		return isset( $node['@type'] ) ? $node['@type'] : '';
	},
	$decoded['@graph']
);

assert_true( in_array( 'Organization', $types, true ), 'JSON-LD must include an Organization node.' );
assert_true( in_array( 'WebSite', $types, true ), 'JSON-LD must include a WebSite node.' );

foreach ( $decoded['@graph'] as $node ) {
	if ( 'WebSite' === ( $node['@type'] ?? '' ) ) {
		assert_true( isset( $node['potentialAction']['@type'] ) && 'SearchAction' === $node['potentialAction']['@type'], 'WebSite must expose a SearchAction for sitelinks search.' );
		assert_true( 'he-IL' === ( $node['inLanguage'] ?? '' ), 'WebSite must declare Hebrew as its language.' );
	}
}

// Social meta
ob_start();
hea_lth_portal_social_meta();
$social_output = (string) ob_get_clean();

foreach ( array( 'og:site_name', 'og:type', 'og:title', 'og:url', 'og:image', 'twitter:card' ) as $needle ) {
	assert_true( false !== strpos( $social_output, $needle ), 'Social metadata must include ' . $needle . '.' );
}
assert_true( false !== strpos( $social_output, 'content="website"' ), 'Front page Open Graph type must be website.' );

// With a dedicated SEO plugin active (Yoast is live on the real site), the
// theme must not duplicate Open Graph output, and must emit its own
// Organization/WebSite graph only while the plugin does not.
define( 'WPSEO_VERSION', '28.0' );

ob_start();
hea_lth_portal_social_meta();
$social_with_yoast = (string) ob_get_clean();
assert_true( '' === trim( $social_with_yoast ), 'Social metadata must defer entirely to the active SEO plugin.' );

$GLOBALS['__options']['wpseo_titles'] = array( 'company_name' => '' );
ob_start();
hea_lth_portal_json_ld();
$ld_unconfigured_yoast = (string) ob_get_clean();
assert_true( false !== strpos( $ld_unconfigured_yoast, '"Organization"' ), 'While Yoast has no site representation, the theme must still provide Organization schema.' );

$GLOBALS['__options']['wpseo_titles'] = array( 'company_name' => 'Hea-lth' );
ob_start();
hea_lth_portal_json_ld();
$ld_configured_yoast = (string) ob_get_clean();
assert_true( '' === trim( $ld_configured_yoast ), 'Once the SEO plugin owns site schema, the theme must not duplicate it.' );

echo "Portal SEO contract passed.\n";
