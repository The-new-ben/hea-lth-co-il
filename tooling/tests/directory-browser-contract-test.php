<?php
/**
 * Regression contract for the verified-directory browser.
 *
 * The browser may read public verified records. It must preserve an anatomy
 * body-region filter, never fabricate results, and never render remote values
 * through HTML injection.
 */

declare( strict_types = 1 );

define( 'ABSPATH', __DIR__ . DIRECTORY_SEPARATOR );

function sanitize_title( $value ) {
	$value = strtolower( trim( (string) $value ) );
	$value = preg_replace( '/\s+/', '-', $value );

	return (string) $value;
}

function wp_unslash( $value ) {
	return stripslashes( (string) $value );
}

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . "\n" );
		exit( 1 );
	}
}

$_GET = array(
	'specialty'  => 'plastic surgery',
	'body_region' => 'nose',
	'unknown'    => 'must-not-pass',
	'region'     => array( 'must-not-pass' ),
);

require_once dirname( __DIR__, 2 ) . '/theme-src/hea-lth-portal/inc/portal-template-helpers.php';

$filters = hea_lth_portal_get_directory_context();
assert_true( 'plastic-surgery' === $filters['specialty'], 'Specialty filter must be normalized.' );
assert_true( 'nose' === $filters['body_region'], 'Anatomy body-region filter must be preserved.' );
assert_true( ! isset( $filters['unknown'] ), 'Unrecognized public parameters must be ignored.' );
assert_true( ! isset( $filters['region'] ), 'Array request values must be ignored.' );

$root      = dirname( __DIR__, 2 );
$template  = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/page-templates/template-directory.php' );
$script    = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/assets/js/directory-browser.js' );
$functions = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/functions.php' );
$footer    = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/footer.php' );

assert_true( false !== strpos( $template, "hea_lth_portal_route( 'doctor_clinic_index' )" ), 'Directory form must submit to the canonical directory route.' );
assert_true( false !== strpos( $template, 'data-api-url' ), 'Directory template must expose a read-only API URL to the browser.' );
assert_true( false !== strpos( $template, 'name="body_region" type="hidden"' ), 'Directory form must preserve an anatomy body-region filter.' );
assert_true( false !== strpos( $template, 'id="directory-service" name="service"' ), 'Directory form must expose the controlled service filter.' );
assert_true( false !== strpos( $template, 'data-directory-focus="<?php echo esc_attr( $path[\'focus\'] ); ?>"' ), 'Directory entry points must focus controlled form filters, not invent skeleton routes.' );
assert_true( false === strpos( $template, '/directory/specialties/' ), 'Directory template must not create an ungoverned specialties skeleton URL.' );
assert_true( false === strpos( $template, '/directory/cities/' ), 'Directory template must not create an ungoverned cities skeleton URL.' );
assert_true( false !== strpos( $script, "new Set(['specialty', 'region', 'service', 'body_region'])" ), 'Browser must limit request filters to the directory contract.' );
assert_true( false !== strpos( $script, "document.querySelectorAll('[data-directory-focus]')" ), 'Directory entry points must be wired to controlled filters.' );
assert_true( false !== strpos( $script, "window.fetch(requestUrl.toString(), { credentials: 'same-origin' })" ), 'Browser must make a same-origin read-only request.' );
assert_true( false === strpos( $script, '.innerHTML' ), 'Browser must use DOM text nodes rather than HTML injection for remote records.' );
assert_true( false !== strpos( $script, 'תוצאות יוצגו כאן רק לאחר חיבור WordPress ורשומות מאומתות.' ), 'Local preview must stay explicit that it contains no mock directory data.' );
assert_true( false !== strpos( $functions, 'hea_lth_portal_enqueue_directory_browser_assets' ), 'Directory browser must be enqueued only through its template gate.' );
assert_true( false !== strpos( $functions, 'hea_lth_portal_directory_filter_robots' ), 'Directory filter states must have an indexation guard.' );
assert_true( false !== strpos( $functions, '$robots[\'noindex\'] = true;' ), 'Directory filter states must be noindex.' );
assert_true( false !== strpos( $functions, '$robots[\'follow\']  = true;' ), 'Directory filter states must remain followable.' );
assert_true( false === strpos( $footer, '/directory/specialties/' ), 'Footer must not create an ungoverned specialties skeleton URL.' );
assert_true( false === strpos( $footer, '/directory/cities/' ), 'Footer must not create an ungoverned cities skeleton URL.' );

echo "Directory browser contract passed.\n";
