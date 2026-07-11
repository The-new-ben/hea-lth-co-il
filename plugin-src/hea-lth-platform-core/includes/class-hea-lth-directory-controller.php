<?php
/**
 * Read-only directory API controller.
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Exposes a minimal public directory feed.
 *
 * This route never accepts inquiry, medical, payment, or account information.
 * It returns only records that are both published and explicitly marked as
 * verified by the platform's controlled state field.
 */
final class Hea_Lth_Directory_Controller {
	/**
	 * REST namespace.
	 */
	const NAMESPACE = 'hea-lth/v1';

	/**
	 * Register hook.
	 *
	 * @return void
	 */
	public static function boot() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}

	/**
	 * Register the read-only endpoint.
	 *
	 * @return void
	 */
	public static function register_routes() {
		register_rest_route(
			self::NAMESPACE,
			'/directory',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'get_items' ),
				'permission_callback' => '__return_true',
				'args'                => self::directory_filter_args(),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/directory/map',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'get_map_items' ),
				'permission_callback' => '__return_true',
				'args'                => self::directory_filter_args(),
			)
		);
	}

	/**
	 * Reuse the same controlled semantic filters for list and map surfaces.
	 * The browser may never submit free-form geography or a renderer mesh name.
	 *
	 * @return array
	 */
	private static function directory_filter_args() {
		return array(
			'specialty' => array(
				'sanitize_callback' => 'sanitize_title',
			),
			'region' => array(
				'sanitize_callback' => 'sanitize_title',
			),
			'service' => array(
				'sanitize_callback' => 'sanitize_title',
			),
			'body_region' => array(
				'sanitize_callback' => 'sanitize_title',
			),
			'limit' => array(
				'default'           => 12,
				'sanitize_callback' => 'absint',
				'validate_callback' => array( __CLASS__, 'validate_limit' ),
			),
		);
	}

	/**
	 * Validate requested result size.
	 *
	 * @param mixed           $value Raw value.
	 * @param WP_REST_Request $request Request.
	 * @param string          $param Parameter name.
	 * @return bool
	 */
	public static function validate_limit( $value, $request, $param ) {
		$value = (int) $value;

		return $value >= 1 && $value <= 24;
	}

	/**
	 * Return verified directory cards only.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public static function get_items( WP_REST_Request $request ) {
		$query = new WP_Query( self::directory_query_args( $request ) );
		$items = array();

		foreach ( $query->posts as $post ) {
			$items[] = self::prepare_item( $post );
		}

		return rest_ensure_response(
			array(
				'items' => $items,
				'meta'  => array(
					'count' => count( $items ),
					'note'  => __( 'Only published, verified directory records are included.', 'hea-lth-platform-core' ),
				),
			)
		);
	}

	/**
	 * Return only verified providers and clinics that separately consented to
	 * public location display. The result deliberately omits street address,
	 * page body, contact data, payment data, and sponsorship state.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public static function get_map_items( WP_REST_Request $request ) {
		$query = new WP_Query( self::directory_query_args( $request, true ) );
		$items = array();

		foreach ( $query->posts as $post ) {
			$item = self::prepare_map_item( $post );
			if ( $item ) {
				$items[] = $item;
			}
		}

		return rest_ensure_response(
			array(
				'items' => $items,
				'meta'  => array(
					'count' => count( $items ),
					'note'  => __( 'Only verified records with separately approved public Israel map locations are included.', 'hea-lth-platform-core' ),
				),
			)
		);
	}

	/**
	 * Build bounded public queries for directory surfaces. Map records add an
	 * independent location-disclosure and country gate.
	 *
	 * @param WP_REST_Request $request Request.
	 * @param bool            $map_only Require an approved public map location.
	 * @return array
	 */
	private static function directory_query_args( WP_REST_Request $request, $map_only = false ) {
		$meta_query = array(
			'relation' => 'AND',
			array(
				'key'   => 'hp_public_state',
				'value' => 'verified',
			),
		);

		if ( $map_only ) {
			$meta_query[] = array(
				'key'   => 'hp_map_public_state',
				'value' => 'approved',
			);
			$meta_query[] = array(
				'key'   => 'hp_map_country_code',
				'value' => 'IL',
			);
		}

		$args = array(
			'post_type'              => array( 'hp_provider', 'hp_clinic' ),
			'post_status'            => 'publish',
			'posts_per_page'         => min( 24, max( 1, (int) $request->get_param( 'limit' ) ) ),
			'orderby'                => 'title',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => true,
			'meta_query'             => $meta_query,
		);

		$tax_query = self::build_tax_query( $request );
		if ( $tax_query ) {
			$args['tax_query'] = $tax_query;
		}

		return $args;
	}

	/**
	 * Turn accepted filter parameters into an AND taxonomy query.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return array
	 */
	private static function build_tax_query( WP_REST_Request $request ) {
		$map = array(
			'specialty'  => 'hp_specialty',
			'region'     => 'hp_region',
			'service'    => 'hp_service_type',
			'body_region' => 'hp_body_region',
		);

		$tax_query = array( 'relation' => 'AND' );

		foreach ( $map as $parameter => $taxonomy ) {
			$value = $request->get_param( $parameter );
			if ( ! $value ) {
				continue;
			}

			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $value,
			);
		}

		return count( $tax_query ) > 1 ? $tax_query : array();
	}

	/**
	 * Prepare an intentionally minimal public record.
	 *
	 * @param WP_Post $post Directory post.
	 * @return array
	 */
	private static function prepare_item( WP_Post $post ) {
		return array(
			'id'            => (int) $post->ID,
			'kind'          => 'hp_provider' === $post->post_type ? 'provider' : 'clinic',
			'name'          => get_the_title( $post ),
			'city'          => (string) get_post_meta( $post->ID, 'hp_city', true ),
			'languages'     => self::string_list_meta( $post->ID, 'hp_languages' ),
			'accessibility' => self::string_list_meta( $post->ID, 'hp_accessibility' ),
			'specialties'   => self::term_names( $post->ID, 'hp_specialty' ),
			'areas'         => self::term_names( $post->ID, 'hp_region' ),
			'services'      => self::term_names( $post->ID, 'hp_service_type' ),
			'bodyRegions'   => self::term_names( $post->ID, 'hp_body_region' ),
			'lastVerified'  => (string) get_post_meta( $post->ID, 'hp_last_verified', true ),
			'disclosure'    => (string) get_post_meta( $post->ID, 'hp_public_disclosure', true ),
		);
	}

	/**
	 * Prepare the narrow map-marker record. A malformed coordinate is excluded
	 * even if an editor accidentally approved its visibility state.
	 *
	 * @param WP_Post $post Directory post.
	 * @return array|null
	 */
	private static function prepare_map_item( WP_Post $post ) {
		$latitude  = self::coordinate( get_post_meta( $post->ID, 'hp_map_latitude', true ), -90, 90 );
		$longitude = self::coordinate( get_post_meta( $post->ID, 'hp_map_longitude', true ), -180, 180 );
		$precision = sanitize_key( (string) get_post_meta( $post->ID, 'hp_map_precision', true ) );

		if ( null === $latitude || null === $longitude || ! in_array( $precision, array( 'exact', 'city' ), true ) ) {
			return null;
		}

		return array(
			'id'           => (int) $post->ID,
			'kind'         => 'hp_provider' === $post->post_type ? 'provider' : 'clinic',
			'name'         => get_the_title( $post ),
			'city'         => (string) get_post_meta( $post->ID, 'hp_city', true ),
			'latitude'     => $latitude,
			'longitude'    => $longitude,
			'precision'    => $precision,
			'lastVerified' => (string) get_post_meta( $post->ID, 'hp_last_verified', true ),
		);
	}

	/**
	 * Validate and normalize one map coordinate without accepting strings such
	 * as a street address or non-finite JavaScript-style values.
	 *
	 * @param mixed $value Raw coordinate.
	 * @param int   $minimum Inclusive lower bound.
	 * @param int   $maximum Inclusive upper bound.
	 * @return float|null
	 */
	private static function coordinate( $value, $minimum, $maximum ) {
		if ( ! is_scalar( $value ) || '' === trim( (string) $value ) || ! is_numeric( $value ) ) {
			return null;
		}

		$coordinate = (float) $value;
		if ( $coordinate < $minimum || $coordinate > $maximum ) {
			return null;
		}

		return round( $coordinate, 6 );
	}

	/**
	 * Read a registered list meta field safely.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key Meta key.
	 * @return array
	 */
	private static function string_list_meta( $post_id, $key ) {
		$value = get_post_meta( $post_id, $key, true );

		return is_array( $value ) ? array_values( array_map( 'strval', $value ) ) : array();
	}

	/**
	 * Read names from a controlled taxonomy.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $taxonomy Taxonomy.
	 * @return array
	 */
	private static function term_names( $post_id, $taxonomy ) {
		$terms = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'names' ) );

		return is_wp_error( $terms ) ? array() : array_values( $terms );
	}
}

Hea_Lth_Directory_Controller::boot();
