<?php
/**
 * Plugin Name: Hea-lth Operations
 * Plugin URI: https://hea-lth.co.il
 * Description: Governed operational layer for Hea-lth health checks, release metadata, and future portal services.
 * Version: 0.1.0
 * Requires at least: 6.9
 * Requires PHP: 8.1
 * Author: Hea-lth
 * Text Domain: hea-lth-ops
 *
 * @package HeaLthOps
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const HEA_LTH_OPS_VERSION = '0.1.0';

/**
 * Record an authenticated agent deployment without persisting credentials.
 */
function hea_lth_ops_record_deployment(): void {
	$deployment_id = sanitize_text_field( wp_unslash( (string) ( $_SERVER['HTTP_X_HEA_LTH_DEPLOYMENT_ID'] ?? '' ) ) );
	if ( '' === $deployment_id || 1 !== preg_match( '/^[a-zA-Z0-9._-]{8,96}$/', $deployment_id ) ) {
		return;
	}

	update_option(
		'hea_lth_ops_last_deployment',
		array(
			'deployment_id' => $deployment_id,
			'version'       => HEA_LTH_OPS_VERSION,
			'deployed_at'   => gmdate( 'c' ),
		),
		false
	);
}

register_activation_hook( __FILE__, 'hea_lth_ops_record_deployment' );

/**
 * Return a deliberately small public health response.
 *
 * Do not expose filesystem paths, active plugins, credentials, or server details.
 */
function hea_lth_ops_healthcheck(): WP_REST_Response {
	$last_deployment = get_option( 'hea_lth_ops_last_deployment', array() );

	return new WP_REST_Response(
		array(
			'status'        => 'ok',
			'component'     => 'hea-lth-ops',
			'version'       => HEA_LTH_OPS_VERSION,
			'deployment_id' => is_array( $last_deployment ) ? (string) ( $last_deployment['deployment_id'] ?? '' ) : '',
		),
		200
	);
}

add_action(
	'rest_api_init',
	static function (): void {
		register_rest_route(
			'hea-lth-ops/v1',
			'/healthcheck',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'permission_callback' => '__return_true',
				'callback'            => 'hea_lth_ops_healthcheck',
			)
		);
	}
);

/**
 * Record successful package overwrites without storing credentials or request data.
 */
add_action(
	'upgrader_overwrote_package',
	static function ( string $package, array $data, string $package_type ): void {
		if ( 'plugin' !== $package_type || empty( $data['TextDomain'] ) || 'hea-lth-ops' !== $data['TextDomain'] ) {
			return;
		}

		hea_lth_ops_record_deployment();
	},
	10,
	3
);

/**
 * Optional Plugin Update Checker fallback.
 *
 * Direct GitHub Actions delivery is primary. Define HEA_LTH_OPS_MANIFEST_URL in
 * wp-config.php only when a separately governed distribution endpoint exists.
 */
add_action(
	'init',
	static function (): void {
		if ( ! defined( 'HEA_LTH_OPS_MANIFEST_URL' ) || ! is_string( HEA_LTH_OPS_MANIFEST_URL ) || '' === HEA_LTH_OPS_MANIFEST_URL ) {
			return;
		}

		$library = __DIR__ . '/lib/plugin-update-checker/plugin-update-checker.php';
		if ( ! is_readable( $library ) ) {
			return;
		}

		require_once $library;

		$factory = '\\YahnisElsts\\PluginUpdateChecker\\v5\\PucFactory';
		if ( ! class_exists( $factory ) ) {
			return;
		}

		try {
			$factory::buildUpdateChecker(
				esc_url_raw( HEA_LTH_OPS_MANIFEST_URL ),
				__FILE__,
				'hea-lth-ops'
			);
		} catch ( Throwable $error ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Operational bootstrap faults must be visible without exposing credentials.
			error_log( 'Hea-lth Ops update checker failed to initialize: ' . $error->getMessage() );
		}
	},
	5
);
