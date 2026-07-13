<?php
/**
 * Contract for the foundation-page provisioner.
 *
 * Every provisioned page must map to an approved foundation route and to a
 * page template that actually ships in the parent theme, and the provisioner
 * must never overwrite existing owner content.
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

require_once $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-page-provisioner.php';
require_once $root . '/theme-src/hea-lth-portal/inc/portal-route-registry.php';

$blueprint         = Hea_Lth_Page_Provisioner::blueprint();
$foundation_routes = hea_lth_portal_foundation_routes();
$foundation_paths  = array();

foreach ( $foundation_routes as $route ) {
	if ( is_array( $route ) && ! empty( $route['path'] ) ) {
		$foundation_paths[] = (string) $route['path'];
	}
}

assert_true( count( $blueprint ) >= 7, 'Blueprint must cover the seven UI-linked foundation pages.' );
assert_true( isset( $blueprint['anatomy'] ), 'Blueprint must provision the interactive-body page — the 3D full experience must not 404.' );

foreach ( $blueprint as $slug => $page ) {
	assert_true( in_array( '/' . $slug . '/', $foundation_paths, true ), 'Provisioned slug must exist in the foundation route registry: ' . $slug );
	assert_true( is_string( $page['title'] ) && '' !== trim( $page['title'] ), 'Provisioned page needs a real title: ' . $slug );
	assert_true( '' !== $page['template'], 'Provisioned page needs a template: ' . $slug );
	assert_true( is_file( $root . '/theme-src/hea-lth-portal/' . $page['template'] ), 'Blueprint template must ship in the parent theme: ' . $page['template'] );
}

$source = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-page-provisioner.php' );

assert_true( false !== strpos( $source, 'get_page_by_path' ), 'Provisioner must check for an existing page before creating one.' );
assert_true( false !== strpos( $source, 'continue;' ), 'Provisioner must skip existing pages — owner content always wins.' );
assert_true( false === strpos( $source, 'wp_update_post' ), 'Provisioner must never update existing pages.' );
assert_true( false === strpos( $source, 'wp_delete_post' ), 'Provisioner must never delete pages.' );

$plugin_main = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/hea-lth-platform-core.php' );

assert_true( false !== strpos( $plugin_main, 'class-hea-lth-page-provisioner.php' ), 'Plugin must load the page provisioner.' );
assert_true( false !== strpos( $plugin_main, 'Hea_Lth_Page_Provisioner::boot()' ), 'Plugin must boot the page provisioner.' );

echo "Page provisioner contract passed.\n";
