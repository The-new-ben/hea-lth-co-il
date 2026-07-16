<?php
/**
 * Static contract for the anatomy-to-verified-map path.
 *
 * The map may receive only verified, separately location-approved, Israel
 * directory records through same-origin filters. It must not use mock data,
 * raw renderer mesh names, HTML injection, or a permanently exposed map key.
 */

declare( strict_types = 1 );

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . "\n" );
		exit( 1 );
	}
}

$root       = dirname( __DIR__, 2 );
$core       = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-platform-core.php' );
$controller = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-directory-controller.php' );
$registry   = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-directory-map-registry.php' );
$theme      = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/functions.php' );
$template   = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/page-templates/template-anatomy.php' );
$script     = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/assets/js/anatomy-directory-map.js' );

assert_true( false !== strpos( $core, "'hp_map_public_state'" ), 'Directory entities must have a separately approved map-visibility state.' );
assert_true( false !== strpos( $core, "'hp_map_latitude'" ), 'Directory entities must have typed latitude metadata.' );
assert_true( false !== strpos( $core, "'hp_map_longitude'" ), 'Directory entities must have typed longitude metadata.' );
assert_true( false !== strpos( $core, "'hp_map_country_code'" ), 'Directory entities must remain country-scoped.' );
assert_true( false !== strpos( $controller, "'/directory/map'" ), 'Platform core must expose a dedicated map route.' );
assert_true( false !== strpos( $controller, "'hp_map_public_state'" ), 'Map route must require separately approved location visibility.' );
assert_true( false !== strpos( $controller, "'hp_map_country_code'" ), 'Map route must require Israel-scoped records.' );
assert_true( false !== strpos( $controller, 'prepare_map_item' ), 'Map route must prepare a narrow marker record.' );
assert_true( false !== strpos( $controller, 'private static function coordinate' ), 'Map route must validate coordinate ranges before exposing markers.' );
assert_true( false === strpos( $controller, 'get_the_content()' ), 'Map route must not expose page body content.' );
assert_true( false !== strpos( $registry, "'keyRestrictionReview'" ), 'Map registry must require a browser-key restriction review.' );
assert_true( false !== strpos( $registry, "'locationDataReview'" ), 'Map registry must require a location-data review.' );
assert_true( false !== strpos( $registry, "'commercialDisclosureReview'" ), 'Map registry must require a commercial-disclosure review.' );
assert_true( false !== strpos( $theme, 'hea_lth_portal_directory_map_config' ), 'Theme must consume only the controlled map configuration.' );
assert_true( false !== strpos( $template, 'data-care-map' ), 'Anatomy page must provide the controlled care-map mount point.' );
assert_true( false !== strpos( $template, 'hea_lth_portal_directory_map_config' ), 'Anatomy map block must render only through the gated configuration.' );
assert_true( false !== strpos( $script, "https://maps.googleapis.com/maps/api/js" ), 'Map adapter must use the configured Maps JavaScript endpoint only after approval.' );
assert_true( false !== strpos( $script, "auth_referrer_policy" ), 'Map adapter must minimize referrer data for restricted browser keys.' );
assert_true( false !== strpos( $script, "source.searchParams.set('callback', callbackName)" ), 'Async Maps loading must use an explicit callback rather than the script load event.' );
assert_true( false === strpos( $script, "addEventListener('load'" ), 'Async Maps loading must not rely on the script load event.' );
assert_true( false !== strpos( $script, "bodyRegion: 'body_region'" ), 'Anatomy routing must map semantic body regions, not mesh IDs.' );
assert_true( false !== strpos( $script, "window.fetch(requestUrl.toString(), { credentials: 'same-origin' })" ), 'Map data must be requested same-origin.' );
assert_true( false === strpos( $script, '.innerHTML' ), 'Map cards must use DOM text nodes rather than HTML injection.' );
assert_true( false === strpos( $script, 'getMockData' ), 'Map adapter must never fabricate provider markers.' );

echo "Directory map contract passed.\n";
