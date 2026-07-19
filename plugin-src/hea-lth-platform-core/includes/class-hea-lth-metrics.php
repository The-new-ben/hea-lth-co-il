<?php
/**
 * Privacy-clean engagement counters for the monetization surfaces.
 *
 * Deliberate boundaries: the store holds ONLY aggregate monthly counters
 * keyed by an allowlisted metric type and a bounded slug. No IP addresses,
 * user agents, identifiers, timestamps per hit, or personal health context
 * are ever recorded, so the numbers are directional product metrics, not
 * tracking. Counters are best-effort (read-modify-write, no locking) which
 * is the right trade for a trust-first health portal.
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Beacon endpoint + monthly counter store + admin report.
 */
final class Hea_Lth_Metrics {

	/** Metric types a beacon may increment; anything else is dropped. */
	const TYPES = array( 'pin_view', 'pin_click', 'wa_open' );

	/** Hard cap of distinct counter keys per month, an abuse backstop. */
	const MAX_KEYS_PER_MONTH = 300;

	/**
	 * @return void
	 */
	public static function boot() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_endpoint' ) );
	}

	/**
	 * @return void
	 */
	public static function register_endpoint() {
		register_rest_route(
			'hea-lth-platform/v1',
			'/metric',
			array(
				'methods'             => 'POST',
				'permission_callback' => '__return_true',
				'callback'            => array( __CLASS__, 'record' ),
				'args'                => array(
					't' => array( 'type' => 'string', 'required' => true ),
					'k' => array( 'type' => 'string', 'required' => true ),
				),
			)
		);
	}

	/**
	 * Record one beacon hit.
	 *
	 * @param WP_REST_Request $request Beacon request.
	 * @return WP_REST_Response
	 */
	public static function record( WP_REST_Request $request ) {
		$type = sanitize_key( (string) $request->get_param( 't' ) );
		$key  = self::sanitize_metric_key( (string) $request->get_param( 'k' ) );

		if ( ! in_array( $type, self::TYPES, true ) || '' === $key ) {
			return new WP_REST_Response( null, 400 );
		}

		self::increment( $type, $key );

		return new WP_REST_Response( null, 204 );
	}

	/**
	 * Metric keys are slugs or site paths, never free text: lowercase, bounded
	 * length, restricted alphabet.
	 *
	 * @param string $value Raw key.
	 * @return string
	 */
	public static function sanitize_metric_key( $value ) {
		$value = strtolower( trim( $value ) );
		$value = preg_replace( '/[^a-z0-9\/_-]/', '', $value );
		$value = substr( (string) $value, 0, 64 );

		return (string) $value;
	}

	/**
	 * @param string $month Month in Y-m, defaults to the current month.
	 * @return string
	 */
	public static function option_name( $month = '' ) {
		$month = preg_match( '/^\d{4}-\d{2}$/', $month ) ? $month : gmdate( 'Y-m' );

		return 'hea_lth_metrics_' . str_replace( '-', '_', $month );
	}

	/**
	 * @param string $type Allowlisted metric type.
	 * @param string $key  Sanitized metric key.
	 * @return void
	 */
	public static function increment( $type, $key ) {
		$option   = self::option_name();
		$counters = get_option( $option, array() );
		if ( ! is_array( $counters ) ) {
			$counters = array();
		}

		$counter_key = $type . ':' . $key;

		if ( ! isset( $counters[ $counter_key ] ) && count( $counters ) >= self::MAX_KEYS_PER_MONTH ) {
			return; // Full for this month: drop new keys, never grow unbounded.
		}

		$counters[ $counter_key ] = isset( $counters[ $counter_key ] ) ? (int) $counters[ $counter_key ] + 1 : 1;

		if ( false === get_option( $option, false ) ) {
			add_option( $option, $counters, '', false ); // autoload off: admin-read only.
		} else {
			update_option( $option, $counters, false );
		}
	}

	/**
	 * Monthly report grouped for the control center: per-pin rows and
	 * WhatsApp-per-page rows, each sorted by volume.
	 *
	 * @param string $month Month in Y-m.
	 * @return array{pins: array<string, array{views:int, clicks:int}>, whatsapp: array<string, int>, total_hits: int}
	 */
	public static function report( $month = '' ) {
		$counters = get_option( self::option_name( $month ), array() );
		if ( ! is_array( $counters ) ) {
			$counters = array();
		}

		$pins     = array();
		$whatsapp = array();
		$total    = 0;

		foreach ( $counters as $counter_key => $count ) {
			$count = (int) $count;
			$total += $count;
			$parts = explode( ':', (string) $counter_key, 2 );
			if ( 2 !== count( $parts ) ) {
				continue;
			}

			list( $type, $key ) = $parts;

			if ( 'pin_view' === $type || 'pin_click' === $type ) {
				if ( ! isset( $pins[ $key ] ) ) {
					$pins[ $key ] = array( 'views' => 0, 'clicks' => 0 );
				}
				$pins[ $key ][ 'pin_view' === $type ? 'views' : 'clicks' ] += $count;
			} elseif ( 'wa_open' === $type ) {
				$whatsapp[ $key ] = isset( $whatsapp[ $key ] ) ? $whatsapp[ $key ] + $count : $count;
			}
		}

		uasort(
			$pins,
			static function ( $a, $b ) {
				return ( $b['views'] + $b['clicks'] ) - ( $a['views'] + $a['clicks'] );
			}
		);
		arsort( $whatsapp );

		return array(
			'pins'       => $pins,
			'whatsapp'   => $whatsapp,
			'total_hits' => $total,
		);
	}
}

Hea_Lth_Metrics::boot();
