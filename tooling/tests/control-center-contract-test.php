<?php
/**
 * Contract for the owner control center.
 *
 * The control center may manage the map, clients, index, content, and
 * monetization, but it must never weaken a gate: map writes go through the
 * registry sanitizer, index additions are allowlisted against the controlled
 * route map, the commercial disclosure is not owner-editable, and every
 * handler checks capability + nonce.
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

if ( ! function_exists( 'add_action' ) ) {
	function add_action( $hook, $callback, $priority = 10, $args = 1 ) {}
}

if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( $hook, $callback, $priority = 10, $args = 1 ) {}
}

$root = dirname( __DIR__, 2 );

require_once $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-control-center.php';

/* --- Pure merge logic: visibility + allowlisted additions. --------------- */

$resolver = array(
	'regions' => array(
		array(
			'id'       => 'scalp',
			'label'    => 'קרקפת',
			'contexts' => array(
				array(
					'label'   => 'הקשר',
					'entries' => array(
						array( 'kind' => 'מרכז', 'label' => 'שיער', 'routeKey' => 'hair_transplant_consultation' ),
						array( 'kind' => 'מדריך', 'label' => 'הכנה', 'routeKey' => 'guides' ),
					),
				),
			),
		),
	),
);

$overrides = array(
	'disabled' => array( 'scalp' => array( 0 => array( 1 ) ) ),
	'added'    => array(
		array( 'region' => 'scalp', 'context' => 0, 'kind' => 'מוצרים', 'label' => 'מוצרי שיער', 'routeKey' => 'products_hair' ),
		array( 'region' => 'scalp', 'context' => 0, 'kind' => 'זדוני', 'label' => 'קישור זר', 'routeKey' => 'evil_route' ),
	),
);

$allowed = array( 'hair_transplant_consultation', 'guides', 'products_hair' );

$merged  = Hea_Lth_Control_Center::merge_resolver_overrides( $resolver, $overrides, $allowed );
$entries = $merged['regions'][0]['contexts'][0]['entries'];
$labels  = array();
$routes  = array();
foreach ( $entries as $entry ) {
	$labels[] = $entry['label'];
	$routes[] = $entry['routeKey'];
}

assert_true( 2 === count( $entries ), 'Merge must hide the disabled shipped entry and drop the disallowed addition (got ' . count( $entries ) . ').' );
assert_true( in_array( 'שיער', $labels, true ), 'Merge must keep the visible shipped entry.' );
assert_true( ! in_array( 'הכנה', $labels, true ), 'Merge must hide the owner-hidden shipped entry.' );
assert_true( in_array( 'products_hair', $routes, true ), 'Merge must append the allowlisted owner entry.' );
assert_true( ! in_array( 'evil_route', $routes, true ), 'Merge must drop entries whose route key is outside the controlled route map.' );

// Empty overrides are a no-op.
$untouched = Hea_Lth_Control_Center::merge_resolver_overrides( $resolver, array(), $allowed );
assert_true( 2 === count( $untouched['regions'][0]['contexts'][0]['entries'] ), 'Empty overrides must leave the shipped dataset intact.' );

/* --- Source contracts: capability, nonces, gate routing. ----------------- */

$source = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-control-center.php' );

assert_true( 5 <= substr_count( $source, "current_user_can( 'manage_options' )" ), 'Every control-center surface must check manage_options.' );
assert_true( 4 <= substr_count( $source, 'check_admin_referer' ), 'Every mutating handler must verify its nonce.' );
assert_true( 4 <= substr_count( $source, 'wp_nonce_field' ), 'Every form must carry a nonce field.' );
assert_true( false !== strpos( $source, 'update_option( Hea_Lth_Directory_Map_Registry::OPTION' ), 'Client writes must land in the gated map-manifest option so the registry sanitizer re-validates them.' );
assert_true( false !== strpos( $source, "'disclosure' => 'שיבוץ מסחרי, פרופיל לקוח של Hea-lth'" ), 'The commercial disclosure must be hardcoded on every saved pin.' );
assert_true( false === strpos( $source, '[disclosure]' ), 'The disclosure must not be an owner-editable form field.' );
assert_true( false !== strpos( $source, 'hea_lth_portal_anatomy_route_map' ), 'Index additions must be allowlisted against the controlled theme route map.' );
assert_true( false !== strpos( $source, "register_rest_route" ) && false !== strpos( $source, '/anatomy-discovery' ), 'The merged index must be served from the governed endpoint.' );
assert_true( false === strpos( $source, 'wp_delete_post' ) && false === strpos( $source, 'wp_update_post' ), 'The control center must not edit or delete owner content directly.' );

/* --- Theme handshake. ----------------------------------------------------- */

$functions = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/functions.php' );
assert_true( false !== strpos( $functions, 'function hea_lth_portal_anatomy_discovery_url' ), 'Theme must expose the filterable discovery URL helper.' );
assert_true( false !== strpos( $functions, "apply_filters( 'hea_lth_anatomy_discovery_url'" ), 'Discovery URL must be filterable by the platform plugin.' );

foreach ( array( 'front-page.php', 'page-templates/template-anatomy.php' ) as $template ) {
	$body = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/' . $template );
	assert_true( false !== strpos( $body, 'hea_lth_portal_anatomy_discovery_url()' ), 'Template must use the discovery URL helper: ' . $template );
}

$plugin_main = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/hea-lth-platform-core.php' );
assert_true( false !== strpos( $plugin_main, 'class-hea-lth-control-center.php' ), 'Plugin must load the control center.' );

echo "Control center contract passed.\n";
