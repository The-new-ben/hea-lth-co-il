<?php
/**
 * Runtime-shaped regression test for the verified directory-map endpoint.
 *
 * It executes the controller with narrow WordPress stubs. It proves an
 * approved map result is minimal and valid, invalid coordinates are excluded,
 * and the query retains public-profile, location-approval, Israel, and body
 * region constraints.
 */

declare( strict_types = 1 );

define( 'ABSPATH', __DIR__ . DIRECTORY_SEPARATOR );

$hea_lth_map_posts      = array();
$hea_lth_map_meta       = array();
$hea_lth_map_titles     = array();
$hea_lth_map_last_query = array();

function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
}

function register_rest_route( $namespace, $route, $args ) {
}

function rest_ensure_response( $value ) {
	return $value;
}

function sanitize_title( $value ) {
	return strtolower( trim( preg_replace( '/\s+/', '-', (string) $value ) ) );
}

function sanitize_key( $value ) {
	return strtolower( preg_replace( '/[^a-z0-9_-]/', '', (string) $value ) );
}

function get_post_meta( $post_id, $key, $single = true ) {
	global $hea_lth_map_meta;

	return $hea_lth_map_meta[ $post_id ][ $key ] ?? '';
}

function get_the_title( $post ) {
	global $hea_lth_map_titles;

	return $hea_lth_map_titles[ $post->ID ] ?? '';
}

function __( $value, $domain = '' ) {
	return $value;
}

class WP_REST_Server {
	const READABLE = 'GET';
}

class WP_REST_Request {
	private $params;

	public function __construct( array $params ) {
		$this->params = $params;
	}

	public function get_param( $key ) {
		return $this->params[ $key ] ?? null;
	}
}

class WP_Post {
	public $ID;
	public $post_type;

	public function __construct( $id, $post_type ) {
		$this->ID        = $id;
		$this->post_type = $post_type;
	}
}

class WP_Query {
	public $posts;

	public function __construct( $args ) {
		global $hea_lth_map_last_query, $hea_lth_map_posts;

		$hea_lth_map_last_query = $args;
		$this->posts             = $hea_lth_map_posts;
	}
}

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . "\n" );
		exit( 1 );
	}
}

require_once dirname( __DIR__, 2 ) . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-directory-controller.php';

$hea_lth_map_posts = array(
	new WP_Post( 101, 'hp_provider' ),
	new WP_Post( 102, 'hp_clinic' ),
);
$hea_lth_map_titles = array(
	101 => 'מרפאה מאומתת',
	102 => 'מרפאה עם נתון שגוי',
);
$hea_lth_map_meta = array(
	101 => array(
		'hp_city'           => 'תל אביב',
		'hp_map_latitude'   => '32.0853',
		'hp_map_longitude'  => '34.7818',
		'hp_map_precision'  => 'exact',
		'hp_last_verified'  => '2026-07-11',
	),
	102 => array(
		'hp_city'           => 'ירושלים',
		'hp_map_latitude'   => '999',
		'hp_map_longitude'  => '35.2137',
		'hp_map_precision'  => 'exact',
		'hp_last_verified'  => '2026-07-11',
	),
);

$response = Hea_Lth_Directory_Controller::get_map_items(
	new WP_REST_Request(
		array(
			'body_region' => 'nose',
			'specialty'   => 'plastic-surgery',
			'limit'       => 12,
		)
	)
);

assert_true( 1 === $response['meta']['count'], 'Invalid coordinates must not appear in the public map response.' );
assert_true( 1 === count( $response['items'] ), 'Only a valid approved map record must be returned.' );
$item = $response['items'][0];
assert_true( 'מרפאה מאומתת' === $item['name'], 'The public map response must retain the verified record name.' );
assert_true( 32.0853 === $item['latitude'] && 34.7818 === $item['longitude'], 'The public map response must normalize valid coordinates.' );
assert_true( 'exact' === $item['precision'], 'The public map response must declare approved precision.' );
assert_true( ! array_key_exists( 'address', $item ) && ! array_key_exists( 'content', $item ) && ! array_key_exists( 'tier', $item ), 'Map response must omit street address, body content, and paid-placement data.' );

$meta_query = $hea_lth_map_last_query['meta_query'];
$serialized_meta_query = json_encode( $meta_query );
assert_true( false !== strpos( $serialized_meta_query, 'hp_public_state' ), 'Map query must require a verified public profile.' );
assert_true( false !== strpos( $serialized_meta_query, 'hp_map_public_state' ), 'Map query must require separately approved map visibility.' );
assert_true( false !== strpos( $serialized_meta_query, 'hp_map_country_code' ), 'Map query must remain Israel-scoped.' );
assert_true( isset( $hea_lth_map_last_query['tax_query'] ) && false !== strpos( json_encode( $hea_lth_map_last_query['tax_query'] ), 'hp_body_region' ), 'Map query must preserve the semantic body-region filter.' );
assert_true( 12 === $hea_lth_map_last_query['posts_per_page'], 'Map query must remain bounded.' );

echo "Directory map endpoint contract passed.\n";
