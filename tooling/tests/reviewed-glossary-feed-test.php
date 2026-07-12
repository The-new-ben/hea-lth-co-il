<?php
/**
 * Regression test for the governed glossary feed.
 *
 * The glossary preserves existing WordPress post URLs, but must not surface a
 * published category item until editorial state, review date, and source note
 * are all present.
 */

declare( strict_types = 1 );

define( 'ABSPATH', __DIR__ . DIRECTORY_SEPARATOR );

function absint( $value ) {
	return abs( (int) $value );
}

final class WP_Query {
	public $args;

	public function __construct( $args ) {
		$this->args = $args;
	}
}

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . "\n" );
		exit( 1 );
	}
}

require_once dirname( __DIR__, 2 ) . '/theme-src/hea-lth-portal/inc/portal-template-helpers.php';

$query    = hea_lth_portal_get_reviewed_glossary_terms( 99 );
$template = (string) file_get_contents( dirname( __DIR__, 2 ) . '/theme-src/hea-lth-portal/page-templates/template-glossary.php' );

assert_true( 'post' === $query->args['post_type'], 'Glossary feed must retain existing WordPress post URLs.' );
assert_true( 'publish' === $query->args['post_status'], 'Glossary feed must query published records only.' );
assert_true( 30 === $query->args['posts_per_page'], 'Glossary feed card limit must be capped.' );
assert_true( 'glossary' === $query->args['category_name'], 'Glossary feed must target the controlled legacy category.' );

$meta_conditions = array_values( array_filter( $query->args['meta_query'], 'is_array' ) );
$meta_by_key     = array();
foreach ( $meta_conditions as $condition ) {
	$meta_by_key[ $condition['key'] ] = $condition;
}

assert_true( 'approved' === $meta_by_key['hp_editorial_state']['value'], 'Glossary feed must require approved editorial state.' );
assert_true( '!=' === $meta_by_key['hp_last_reviewed']['compare'], 'Glossary feed must require a review date.' );
assert_true( '!=' === $meta_by_key['hp_source_note']['compare'], 'Glossary feed must require a source note.' );
assert_true( false !== strpos( $template, 'hea_lth_portal_get_reviewed_glossary_terms( 18 )' ), 'Glossary template must use the shared reviewed glossary query.' );
assert_true( false === strpos( $template, 'new WP_Query' ), 'Glossary template must not bypass the review gate.' );
assert_true( false !== strpos( $template, 'hp-glossary-card__evidence' ), 'Glossary cards must expose review-source context to visitors.' );

echo "Reviewed glossary feed tests passed.\n";
