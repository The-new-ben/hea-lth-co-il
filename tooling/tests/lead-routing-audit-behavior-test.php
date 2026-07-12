<?php
/**
 * Behavior test for the non-PII lead-route health audit.
 *
 * The fixture contains only internal route configuration and provider state.
 * It contains no visitor, patient, contact, or CRM data.
 */

declare( strict_types = 1 );

define( 'ABSPATH', __DIR__ . DIRECTORY_SEPARATOR );

function add_action( $hook, $callback, $priority = 10 ) {
}

function __( $text, $domain = '' ) {
	return $text;
}

function sanitize_key( $value ) {
	return strtolower( preg_replace( '/[^a-z0-9_-]/', '', (string) $value ) );
}

function sanitize_text_field( $value ) {
	return trim( (string) $value );
}

function sanitize_title( $value ) {
	return sanitize_key( $value );
}

function absint( $value ) {
	return abs( (int) $value );
}

class WP_Post {
	public $ID;
	public $post_type;
	public $post_status;
	public $post_title;

	public function __construct( $id, $post_type, $post_status, $post_title ) {
		$this->ID          = $id;
		$this->post_type   = $post_type;
		$this->post_status = $post_status;
		$this->post_title  = $post_title;
	}
}

$fixture_posts = array(
	201 => new WP_Post( 201, 'hp_provider', 'publish', 'Verified provider' ),
	202 => new WP_Post( 202, 'hp_clinic', 'publish', 'Verified clinic' ),
	203 => new WP_Post( 203, 'hp_provider', 'publish', 'Unverified provider' ),
);

$fixture_meta = array(
	201 => array( 'hp_public_state' => 'verified' ),
	202 => array( 'hp_public_state' => 'verified' ),
	203 => array( 'hp_public_state' => 'draft' ),
);

$today = gmdate( 'Y-m-d' );
$fixture_routes = array(
	new WP_Post( 101, 'hp_lead_route', 'publish', 'Ready route' ),
	new WP_Post( 102, 'hp_lead_route', 'publish', 'Consent review route' ),
	new WP_Post( 103, 'hp_lead_route', 'publish', 'Recipient block route' ),
	new WP_Post( 104, 'hp_lead_route', 'draft', 'Draft route' ),
	new WP_Post( 105, 'hp_lead_route', 'publish', 'Disclosure review route' ),
);

$fixture_meta[101] = array(
	'hp_route_state'              => 'active',
	'hp_route_capacity_state'     => 'accepting',
	'hp_route_recipient_id'       => '201',
	'hp_route_consent_version'    => 'consent-v1',
	'hp_route_last_reviewed'      => $today,
	'hp_route_sponsorship_state'  => 'organic',
	'hp_route_disclosure_version' => '',
);
$fixture_meta[102] = array(
	'hp_route_state'              => 'active',
	'hp_route_capacity_state'     => 'accepting',
	'hp_route_recipient_id'       => '202',
	'hp_route_consent_version'    => '',
	'hp_route_last_reviewed'      => $today,
	'hp_route_sponsorship_state'  => 'organic',
	'hp_route_disclosure_version' => '',
);
$fixture_meta[103] = array(
	'hp_route_state'              => 'active',
	'hp_route_capacity_state'     => 'accepting',
	'hp_route_recipient_id'       => '203',
	'hp_route_consent_version'    => 'consent-v1',
	'hp_route_last_reviewed'      => $today,
	'hp_route_sponsorship_state'  => 'organic',
	'hp_route_disclosure_version' => '',
);
$fixture_meta[104] = array(
	'hp_route_state'              => 'draft',
	'hp_route_capacity_state'     => 'unavailable',
	'hp_route_recipient_id'       => '0',
	'hp_route_consent_version'    => '',
	'hp_route_last_reviewed'      => '',
	'hp_route_sponsorship_state'  => 'organic',
	'hp_route_disclosure_version' => '',
);
$fixture_meta[105] = array(
	'hp_route_state'              => 'active',
	'hp_route_capacity_state'     => 'accepting',
	'hp_route_recipient_id'       => '201',
	'hp_route_consent_version'    => 'consent-v1',
	'hp_route_last_reviewed'      => $today,
	'hp_route_sponsorship_state'  => 'disclosed-sponsored',
	'hp_route_disclosure_version' => '',
);

function get_post( $post_id ) {
	global $fixture_posts;

	return isset( $fixture_posts[ $post_id ] ) ? $fixture_posts[ $post_id ] : null;
}

function get_post_meta( $post_id, $key, $single = true ) {
	global $fixture_meta;

	return isset( $fixture_meta[ $post_id ][ $key ] ) ? $fixture_meta[ $post_id ][ $key ] : '';
}

class WP_Query {
	public $posts = array();

	public function __construct( $args = array() ) {
		global $fixture_routes;

		$this->posts = $fixture_routes;
	}
}

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . "\n" );
		exit( 1 );
	}
}

require_once dirname( __DIR__, 2 ) . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-lead-route-resolver.php';

$report = Hea_Lth_Lead_Route_Resolver::get_route_audit_report();

assert_true( 5 === $report['summary']['total'], 'Audit must inspect every internal route configuration record.' );
assert_true( 1 === $report['summary']['ready'], 'One complete verified route should be ready.' );
assert_true( 3 === $report['summary']['needs_review'], 'Draft, missing-consent, and missing-disclosure routes should require review.' );
assert_true( 1 === $report['summary']['blocked'], 'An unverified recipient must block an active route.' );

$statuses = array();
$issues   = array();
foreach ( $report['entries'] as $entry ) {
	$statuses[ $entry['route_id'] ] = $entry['status'];
	$issues[ $entry['route_id'] ]   = $entry['issues'];
}

assert_true( 'ready' === $statuses[101], 'Verified, accepting, consented, reviewed route should be ready.' );
assert_true( 'needs_review' === $statuses[102], 'A route without a consent version must not be ready.' );
assert_true( 'blocked' === $statuses[103], 'A route with an unverified recipient must be blocked.' );
assert_true( 'needs_review' === $statuses[104], 'A draft route must not be presented as ready.' );
assert_true( 'needs_review' === $statuses[105], 'A sponsored route without disclosure version must not be ready.' );
assert_true( in_array( 'חסרה גרסת הסכמה', $issues[102], true ), 'Consent gap must be visible to the operator.' );
assert_true( in_array( 'חסר נמען מאומת ומפורסם', $issues[103], true ), 'Recipient verification gap must be visible to the operator.' );
assert_true( in_array( 'חסרה גרסת גילוי מסחרי', $issues[105], true ), 'Commercial disclosure gap must be visible to the operator.' );

echo "Lead routing audit behavior tests passed.\n";
