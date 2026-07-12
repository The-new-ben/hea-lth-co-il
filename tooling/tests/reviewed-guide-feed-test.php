<?php
/**
 * Regression test for the homepage's reviewed-content feed.
 *
 * This test proves the theme requests only approved, reviewed, source-backed
 * WordPress posts and escapes the card output. It does not create content or
 * connect to a WordPress installation.
 */

declare( strict_types = 1 );

define( 'ABSPATH', __DIR__ . DIRECTORY_SEPARATOR );

$hea_lth_test_current_post = null;

function absint( $value ) {
	return abs( (int) $value );
}

function esc_html( $value ) {
	return htmlspecialchars( (string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
}

function esc_url( $value ) {
	return filter_var( (string) $value, FILTER_SANITIZE_URL );
}

function esc_html_e( $value, $domain = '' ) {
	echo esc_html( $value );
}

function esc_attr_e( $value, $domain = '' ) {
	echo esc_html( $value );
}

function get_the_title() {
	global $hea_lth_test_current_post;

	return $hea_lth_test_current_post['title'];
}

function get_the_content() {
	return '';
}

function wp_strip_all_tags( $value ) {
	return trim( strip_tags( (string) $value ) );
}

function the_content() {
}

function get_the_ID() {
	global $hea_lth_test_current_post;

	return $hea_lth_test_current_post['id'];
}

function get_permalink() {
	global $hea_lth_test_current_post;

	return $hea_lth_test_current_post['url'];
}

function get_the_excerpt() {
	global $hea_lth_test_current_post;

	return $hea_lth_test_current_post['excerpt'];
}

function get_post_meta( $post_id, $key, $single = true ) {
	global $hea_lth_test_current_post;

	return $hea_lth_test_current_post['meta'][ $key ] ?? '';
}

function wp_trim_words( $value, $count ) {
	$words = preg_split( '/\s+/', trim( (string) $value ) );

	return implode( ' ', array_slice( $words, 0, (int) $count ) );
}

final class WP_Query {
	public $args;
	private $posts;
	private $position = 0;

	public function __construct( $args ) {
		$this->args  = $args;
		$this->posts = array(
			array(
				'id'      => 42,
				'title'   => '<script>unsafe</script> בדיקת מקור',
				'url'     => 'https://hea-lth.co.il/guides/source-check/',
				'excerpt' => 'תקציר למדריך שנבדק על ידי צוות מקצועי.',
				'meta'    => array(
					'hp_last_reviewed' => '2026-07-11',
					'hp_source_note'   => 'מקור מקצועי מאומת',
				),
			),
		);
	}

	public function have_posts() {
		return $this->position < count( $this->posts );
	}

	public function the_post() {
		global $hea_lth_test_current_post;

		$hea_lth_test_current_post = $this->posts[ $this->position ];
		$this->position++;
	}
}

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . "\n" );
		exit( 1 );
	}
}

require_once dirname( __DIR__, 2 ) . '/theme-src/hea-lth-portal/inc/portal-template-helpers.php';

$query = hea_lth_portal_get_reviewed_guides( 99 );
$template = (string) file_get_contents( dirname( __DIR__, 2 ) . '/theme-src/hea-lth-portal/page-templates/template-guides.php' );

assert_true( 'post' === $query->args['post_type'], 'The homepage feed must query existing WordPress posts, not a new public route.' );
assert_true( 12 === $query->args['posts_per_page'], 'The guide-card limit must be capped while allowing a rich index surface.' );
$meta_conditions = array_values( array_filter( $query->args['meta_query'], 'is_array' ) );
$meta_by_key     = array();
foreach ( $meta_conditions as $condition ) {
	$meta_by_key[ $condition['key'] ] = $condition;
}
assert_true( 'approved' === $meta_by_key['hp_editorial_state']['value'], 'The feed must require approved editorial state.' );
assert_true( '!=' === $meta_by_key['hp_last_reviewed']['compare'], 'The feed must require a review date.' );
assert_true( '!=' === $meta_by_key['hp_source_note']['compare'], 'The feed must require a source note.' );

$query->the_post();
ob_start();
hea_lth_portal_render_reviewed_guide_card();
$card = ob_get_clean();

assert_true( false === strpos( $card, '<script>' ), 'The feed must escape titles.' );
assert_true( false !== strpos( $card, '&lt;script&gt;unsafe&lt;/script&gt;' ), 'The feed must preserve escaped title text.' );
assert_true( false !== strpos( $card, '2026-07-11' ), 'The feed must render the review date.' );
assert_true( false !== strpos( $card, 'מקור מקצועי מאומת' ), 'The feed must render the public source note.' );

assert_true( false !== strpos( $template, 'hea_lth_portal_get_reviewed_guides( 9 )' ), 'Guide template must use the shared approved-content query.' );
assert_true( false === strpos( $template, 'new WP_Query' ), 'Guide template must not bypass the approved-content query gate.' );
assert_true( false !== strpos( $template, 'hp-record-card__evidence' ), 'Guide cards must expose review-source context to visitors.' );

echo "Reviewed guide feed tests passed.\n";
