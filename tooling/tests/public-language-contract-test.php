<?php
/**
 * Public-language regression check for the Hebrew portal surface.
 *
 * Product-team English labels are not useful to a Hebrew visitor. Brand names
 * and medical acronyms are allowed where they identify the actual service.
 */

declare( strict_types = 1 );

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . "\n" );
		exit( 1 );
	}
}

$root  = dirname( __DIR__, 2 );
$files = array(
	$root . '/theme-src/hea-lth-portal/header.php',
	$root . '/theme-src/hea-lth-portal/footer.php',
	$root . '/theme-src/hea-lth-portal/front-page.php',
	$root . '/theme-src/hea-lth-portal/single.php',
	$root . '/theme-src/hea-lth-portal/page-templates/template-guides.php',
	$root . '/theme-src/hea-lth-portal/page-templates/template-directory.php',
	$root . '/theme-src/hea-lth-portal/page-templates/template-professionals.php',
	$root . '/theme-src/hea-lth-portal/page-templates/template-account.php',
	$root . '/theme-src/hea-lth-portal/page-templates/template-glossary.php',
	$root . '/theme-src/hea-lth-portal/page-templates/template-health-technology.php',
	$root . '/theme-src/hea-lth-portal/page-templates/template-anatomy.php',
	$root . '/theme-src/hea-lth-portal/page-templates/template-science-hub.php',
	$root . '/theme-src/hea-lth-portal/page-templates/template-supplier-join.php',
	$root . '/theme-src/hea-lth-portal/page-templates/template-supplier-portal.php',
);
$source = implode( "\n", array_map( 'file_get_contents', $files ) );

foreach ( array( 'Clinical Journal', 'Private Concierge', 'Future Medicine', 'Health glossary', 'Professional area', 'Personal area', 'Health technology', 'Map layer' ) as $retired_label ) {
	assert_true( false === strpos( $source, $retired_label ), 'Retired public label remains: ' . $retired_label );
}

foreach ( array( 'קנוני', 'קניבליזציה', 'כוונת חיפוש', 'commercial bridge', 'route key' ) as $internal_term ) {
	assert_true( false === stripos( $source, $internal_term ), 'Internal project language reached a public template: ' . $internal_term );
}

assert_true( false !== strpos( $source, 'מרכז המדריכים' ), 'Guide surface must use Hebrew visitor language.' );
assert_true( false !== strpos( $source, 'מסלול רפואה פרטית' ), 'Private-care surface must use Hebrew visitor language.' );
assert_true( false !== strpos( $source, 'גוף, מידע וטכנולוגיה' ), 'Anatomy teaser must use Hebrew visitor language.' );
assert_true( false !== strpos( $source, 'מילון בריאות' ), 'Glossary surface must use Hebrew visitor language.' );
assert_true( false !== strpos( $source, 'חשבון מאובטח' ), 'Account surface must not expose CMS implementation language.' );

echo "Public language contract passed.\n";
