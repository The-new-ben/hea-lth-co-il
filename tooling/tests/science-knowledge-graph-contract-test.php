<?php
/**
 * Contract for the evidence-governed science knowledge graph.
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

if ( ! function_exists( 'apply_filters' ) ) {
	function apply_filters( $hook, $value ) {
		return $value;
	}
}

if ( ! function_exists( 'home_url' ) ) {
	function home_url( $path = '/' ) {
		return 'https://hea-lth.example' . $path;
	}
}

$root = dirname( __DIR__, 2 );

require_once $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-knowledge-graph.php';
require_once $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-page-provisioner.php';
require_once $root . '/theme-src/hea-lth-portal/inc/portal-route-registry.php';

$nodes      = Hea_Lth_Knowledge_Graph::nodes();
$bridges    = Hea_Lth_Knowledge_Graph::bridges();
$routes     = hea_lth_portal_foundation_routes();
$blueprint  = Hea_Lth_Page_Provisioner::blueprint();
$page_paths = array_column( $blueprint, 'path' );

assert_true( count( $nodes ) >= 9, 'The first science graph must contain the full biology, skin, longevity, biomarker, and AI foundation.' );

foreach ( $nodes as $node_id => $node ) {
	foreach ( array( 'route_key', 'title', 'summary', 'focus', 'children', 'bridges', 'sources', 'review_level' ) as $field ) {
		assert_true( isset( $node[ $field ] ), 'Science node is missing ' . $field . ': ' . $node_id );
	}

	assert_true( isset( $routes[ $node['route_key'] ] ), 'Science node route key is not registered: ' . $node_id );
	assert_true( in_array( $routes[ $node['route_key'] ]['path'], $page_paths, true ), 'Science node route is not provisioned: ' . $node_id );
	assert_true( 'maximum' === $node['review_level'], 'Every first-release science node requires maximum review: ' . $node_id );
	assert_true( ! empty( $node['sources'] ), 'Science node requires at least one source: ' . $node_id );

	foreach ( $node['sources'] as $source ) {
		assert_true( 0 === strpos( $source['url'], 'https://' ), 'Evidence source must use HTTPS: ' . $node_id );
		assert_true( '' !== trim( $source['label'] ), 'Evidence source must have a public label: ' . $node_id );
	}

	foreach ( $node['children'] as $child_id ) {
		assert_true( isset( $nodes[ $child_id ] ), 'Unknown science child: ' . $child_id );
	}

	foreach ( $node['bridges'] as $bridge_id ) {
		assert_true( isset( $bridges[ $bridge_id ] ), 'Unknown science bridge: ' . $bridge_id );
	}
}

$all_copy = strtolower( print_r( $nodes, true ) );
foreach ( array( 'reverses aging', 'cures aging', 'guaranteed result', 'מרפא הזדקנות', 'היפוך הזדקנות' ) as $prohibited_claim ) {
	assert_true( false === strpos( $all_copy, $prohibited_claim ), 'Unsupported public claim found: ' . $prohibited_claim );
}

$template = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/page-templates/template-science-hub.php' );
assert_true( false === strpos( $template, 'href="/' ), 'Science template must resolve internal links through route keys.' );
assert_true( false !== strpos( $template, 'hp_last_reviewed' ), 'Science template must display review metadata.' );
assert_true( false !== strpos( $template, "rel=\"external noopener\"" ), 'Evidence links must be identified as external.' );

foreach ( array( 'כוונת חיפוש', 'קניבליזציה', 'SEO', 'commercial bridge', 'route key' ) as $internal_language ) {
	assert_true( false === stripos( $template, $internal_language ), 'Public science template exposes internal project language: ' . $internal_language );
}

$skin_node = $nodes['skin'];
assert_true( in_array( 'skin_treatments', $skin_node['bridges'], true ), 'Skin science must bridge to, not replace, the commercial treatment pillar.' );
assert_true( '/skin/' !== '/skin-treatments-private/', 'Skin science and treatment intent must remain separate.' );

echo "Science knowledge graph contract passed for " . count( $nodes ) . " nodes.\n";
