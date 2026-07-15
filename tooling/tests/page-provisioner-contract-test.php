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

assert_true( count( $blueprint ) >= 22, 'Blueprint must cover every UI-linked foundation route so no navigation link 404s.' );

$blueprint_paths = array();

foreach ( $blueprint as $index => $page ) {
	assert_true( isset( $page['path'], $page['title'] ), 'Blueprint entry needs path and title (index ' . $index . ').' );
	assert_true( in_array( $page['path'], $foundation_paths, true ), 'Provisioned path must exist in the foundation route registry: ' . $page['path'] );
	assert_true( is_string( $page['title'] ) && '' !== trim( $page['title'] ), 'Provisioned page needs a real title: ' . $page['path'] );

	$has_template = '' !== $page['template'];
	$has_content  = isset( $page['content'] ) && '' !== trim( (string) $page['content'] );
	assert_true( $has_template || $has_content, 'Provisioned page needs a template or real content: ' . $page['path'] );

	if ( $has_template ) {
		assert_true( is_file( $root . '/theme-src/hea-lth-portal/' . $page['template'] ), 'Blueprint template must ship in the parent theme: ' . $page['template'] );
	}

	// Parents must be provisioned before their children so post_parent resolves.
	$trimmed  = trim( (string) $page['path'], '/' );
	$segments = explode( '/', $trimmed );
	array_pop( $segments );
	if ( ! empty( $segments ) ) {
		$parent_path = '/' . implode( '/', $segments ) . '/';
		assert_true( in_array( $parent_path, $blueprint_paths, true ), 'Parent page must be listed before its child: ' . $page['path'] );
	}

	$blueprint_paths[] = $page['path'];
}

foreach ( array( '/anatomy/', '/accessibility/', '/diagnostics/', '/diagnostics/imaging/', '/wellness/', '/private-medicine/', '/about/', '/editorial-policy/', '/privacy/', '/terms/', '/contact/', '/account/' ) as $required_path ) {
	assert_true( in_array( $required_path, $blueprint_paths, true ), 'Blueprint must provision ' . $required_path );
}

assert_true( false !== strpos( Hea_Lth_Page_Provisioner::about_content(), 'Z-Anatomy' ), 'About page must credit the anatomy source.' );
assert_true( false !== strpos( Hea_Lth_Page_Provisioner::editorial_policy_content(), 'תנאי הסף' ), 'Editorial policy must state the publication conditions.' );
assert_true( false !== strpos( Hea_Lth_Page_Provisioner::terms_content(), 'אינו ייעוץ רפואי' ), 'Terms must carry the no-medical-advice boundary.' );

$statement = Hea_Lth_Page_Provisioner::accessibility_statement_content();

foreach ( array( '5568', 'WCAG 2.1', 'נגישות', 'רכז' ) as $needle ) {
	assert_true( false !== strpos( $statement, $needle ), 'Accessibility statement must reference: ' . $needle );
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
