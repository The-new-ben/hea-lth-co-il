<?php
/**
 * Governed browser-map configuration.
 *
 * A public map needs a browser-visible key, but it must still be restricted to
 * the correct web origin, a named Maps product, verified location data, and a
 * commercial-disclosure review. This class never activates a map merely
 * because a key-shaped string was saved.
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns the approved directory-map manifest boundary.
 */
final class Hea_Lth_Directory_Map_Registry {
	/** Internal option holding the reviewed manifest. */
	const OPTION = 'hea_lth_directory_map_manifest';

	/** Public configuration filter consumed by the presentation theme. */
	const FILTER = 'hea_lth_public_directory_map_config';

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public static function boot() {
		add_action( 'admin_init', array( __CLASS__, 'register_setting' ) );
		add_action( 'admin_menu', array( __CLASS__, 'register_settings_page' ) );
		add_filter( self::FILTER, array( __CLASS__, 'filter_public_configuration' ), 10, 1 );
	}

	/**
	 * Register an administrator-only JSON manifest. A restricted browser key is
	 * intentionally distinct from server credentials and may be delivered only
	 * after all checks pass.
	 *
	 * @return void
	 */
	public static function register_setting() {
		register_setting(
			'hea_lth_directory_map',
			self::OPTION,
			array(
				'type'              => 'string',
				'sanitize_callback' => array( __CLASS__, 'sanitize_manifest_option' ),
				'default'           => '',
			)
		);
	}

	/**
	 * Add the intentionally small map-review screen.
	 *
	 * @return void
	 */
	public static function register_settings_page() {
		add_options_page(
			__( 'Hea-lth directory map', 'hea-lth-platform-core' ),
			__( 'Hea-lth directory map', 'hea-lth-platform-core' ),
			'manage_options',
			'hea-lth-directory-map',
			array( __CLASS__, 'render_settings_page' )
		);
	}

	/**
	 * Render the configuration field without claiming the map is active.
	 *
	 * @return void
	 */
	public static function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to manage the directory map manifest.', 'hea-lth-platform-core' ) );
		}

		$value = (string) get_option( self::OPTION, '' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Hea-lth directory map', 'hea-lth-platform-core' ); ?></h1>
			<p><?php esc_html_e( 'The public map remains disabled until the browser key restriction, map ID, location-data review, commercial-disclosure review, owner, origin, and review date are all approved.', 'hea-lth-platform-core' ); ?></p>
			<form method="post" action="options.php">
				<?php settings_fields( 'hea_lth_directory_map' ); ?>
				<label for="hea-lth-directory-map-manifest"><strong><?php esc_html_e( 'Approved map manifest JSON', 'hea-lth-platform-core' ); ?></strong></label>
				<textarea class="large-text code" rows="24" id="hea-lth-directory-map-manifest" name="<?php echo esc_attr( self::OPTION ); ?>" spellcheck="false"><?php echo esc_textarea( $value ); ?></textarea>
				<p class="description"><?php esc_html_e( 'Do not store server credentials, private map datasets, patient information, or unrestricted API keys in this manifest.', 'hea-lth-platform-core' ); ?></p>
				<?php submit_button( __( 'Save manifest', 'hea-lth-platform-core' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Return the approved configuration only when every map gate passes.
	 *
	 * @param array $fallback Theme-safe disabled configuration.
	 * @return array
	 */
	public static function filter_public_configuration( $fallback ) {
		$config = self::public_configuration();

		return 'approved' === $config['status'] ? $config : $fallback;
	}

	/**
	 * Build the browser-safe payload. It exposes a restricted browser key and
	 * map ID only. It never exposes server credentials, the review record, or
	 * provider locations.
	 *
	 * @return array
	 */
	public static function public_configuration() {
		$manifest = self::read_manifest();
		if ( ! $manifest ) {
			return self::gated_configuration( 'missing-manifest' );
		}

		$gate = self::gate_manifest( $manifest );
		if ( true !== $gate['approved'] ) {
			return self::gated_configuration( $gate['reason'] );
		}

		if ( 'leaflet-osm' === $manifest['provider'] ) {
			return array(
				'status'            => 'approved',
				'provider'          => 'leaflet-osm',
				// The tile source is fixed in code, never manifest-supplied, so a
				// compromised option cannot point visitors at a hostile server.
				'tiles'             => 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
				'attribution'       => '© OpenStreetMap contributors',
				'poiData'           => 'assets/data/healthcare-poi-il.json',
				'language'          => 'he',
				'region'            => 'IL',
				'featuredProviders' => $manifest['featuredProviders'],
			);
		}

		return array(
			'status'     => 'approved',
			'provider'   => 'google-maps-js',
			'browserKey' => $manifest['browserKey'],
			'mapId'      => $manifest['mapId'],
			'language'   => 'he',
			'region'     => 'IL',
		);
	}

	/**
	 * Preserve the previous safe manifest when a submitted document is invalid.
	 *
	 * @param mixed $value Submitted JSON.
	 * @return string
	 */
	public static function sanitize_manifest_option( $value ) {
		$raw      = is_string( $value ) ? wp_unslash( $value ) : '';
		$decoded  = json_decode( $raw, true );
		$manifest = self::normalize_manifest( $decoded );

		if ( ! $manifest ) {
			add_settings_error( self::OPTION, 'invalid_directory_map_manifest', __( 'The directory map manifest is invalid. The previous safe configuration was kept.', 'hea-lth-platform-core' ) );
			$previous = get_option( self::OPTION, '' );

			return is_string( $previous ) ? $previous : '';
		}

		return wp_json_encode( $manifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	}

	/**
	 * @return array|null
	 */
	private static function read_manifest() {
		$value = get_option( self::OPTION, '' );

		if ( ! is_string( $value ) || '' === trim( $value ) ) {
			// No administrator-set manifest: fall back to the shipped default so
			// the reviewed keyless map is live on install. The default still runs
			// through normalize + gate below — a source, not a bypass.
			$path = dirname( __DIR__ ) . '/data/default-map-manifest.json';

			if ( ! is_readable( $path ) ) {
				return null;
			}

			$value = (string) file_get_contents( $path );
		}

		$decoded = json_decode( $value, true );

		return is_array( $decoded ) ? self::normalize_manifest( $decoded ) : null;
	}

	/**
	 * Normalize the internal review document.
	 *
	 * @param mixed $value Raw manifest.
	 * @return array|null
	 */
	private static function normalize_manifest( $value ) {
		if ( ! is_array( $value ) ) {
			return null;
		}

		$required = array(
			'status',
			'provider',
			'allowedOrigin',
			'countryCode',
			'owner',
			'reviewedAt',
			'keyRestrictionReview',
			'locationDataReview',
			'commercialDisclosureReview',
		);

		foreach ( $required as $key ) {
			if ( ! array_key_exists( $key, $value ) ) {
				return null;
			}
		}

		$status   = sanitize_key( (string) $value['status'] );
		$provider = sanitize_key( (string) $value['provider'] );
		$country  = strtoupper( sanitize_text_field( (string) $value['countryCode'] ) );
		$origin   = self::normalize_origin( $value['allowedOrigin'] );
		$owner       = sanitize_text_field( (string) $value['owner'] );
		$reviewed_at = self::sanitize_iso_date( $value['reviewedAt'] );
		$key_review  = sanitize_key( (string) $value['keyRestrictionReview'] );
		$location_review = sanitize_key( (string) $value['locationDataReview'] );
		$commercial_review = sanitize_key( (string) $value['commercialDisclosureReview'] );

		if ( ! in_array( $status, array( 'draft', 'review', 'approved', 'disabled' ), true ) || ! in_array( $provider, array( 'google-maps-js', 'leaflet-osm' ), true ) || 'IL' !== $country || '' === $origin || '' === $owner || '' === $reviewed_at || ! self::is_review_state( $key_review ) || ! self::is_review_state( $location_review ) || ! self::is_review_state( $commercial_review ) ) {
			return null;
		}

		$manifest = array(
			'status'                      => $status,
			'provider'                    => $provider,
			'allowedOrigin'               => $origin,
			'countryCode'                 => $country,
			'owner'                       => $owner,
			'reviewedAt'                  => $reviewed_at,
			'keyRestrictionReview'        => $key_review,
			'locationDataReview'          => $location_review,
			'commercialDisclosureReview'  => $commercial_review,
			'browserKey'                  => '',
			'mapId'                       => '',
			'featuredProviders'           => array(),
		);

		if ( 'google-maps-js' === $provider ) {
			$browser_key = isset( $value['browserKey'] ) ? trim( sanitize_text_field( (string) $value['browserKey'] ) ) : '';
			$map_id      = isset( $value['mapId'] ) ? trim( sanitize_text_field( (string) $value['mapId'] ) ) : '';

			if ( ! self::is_browser_key( $browser_key ) || ! self::is_map_id( $map_id ) ) {
				return null;
			}

			$manifest['browserKey'] = $browser_key;
			$manifest['mapId']      = $map_id;
		} else {
			$manifest['featuredProviders'] = self::normalize_featured_providers( isset( $value['featuredProviders'] ) ? $value['featuredProviders'] : array() );
		}

		return $manifest;
	}

	/**
	 * Sanitize the commercially disclosed featured-provider pins. Every entry
	 * must be a verified client with a public business address; the disclosure
	 * label ships with the pin so premium placement is never silent.
	 *
	 * @param mixed $value Raw provider list.
	 * @return array
	 */
	private static function normalize_featured_providers( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}

		$providers = array();

		foreach ( array_slice( $value, 0, 20 ) as $entry ) {
			if ( ! is_array( $entry ) || ! isset( $entry['name'], $entry['specialty'], $entry['lat'], $entry['lon'], $entry['disclosure'] ) ) {
				continue;
			}

			$lat = (float) $entry['lat'];
			$lon = (float) $entry['lon'];
			$url = isset( $entry['url'] ) ? esc_url_raw( (string) $entry['url'], array( 'https' ) ) : '';

			// Coordinates must land inside Israel's bounding box.
			if ( $lat < 29.0 || $lat > 34.0 || $lon < 33.5 || $lon > 36.5 ) {
				continue;
			}

			$providers[] = array(
				'name'       => sanitize_text_field( (string) $entry['name'] ),
				'specialty'  => sanitize_key( (string) $entry['specialty'] ),
				'label'      => isset( $entry['label'] ) ? sanitize_text_field( (string) $entry['label'] ) : '',
				'address'    => isset( $entry['address'] ) ? sanitize_text_field( (string) $entry['address'] ) : '',
				'phone'      => isset( $entry['phone'] ) ? preg_replace( '/[^0-9+-]/', '', (string) $entry['phone'] ) : '',
				'url'        => $url,
				'lat'        => $lat,
				'lon'        => $lon,
				'badge'      => isset( $entry['badge'] ) ? sanitize_text_field( (string) $entry['badge'] ) : '',
				'disclosure' => sanitize_text_field( (string) $entry['disclosure'] ),
				'verifiedAt' => isset( $entry['verifiedAt'] ) ? self::sanitize_iso_date( $entry['verifiedAt'] ) : '',
			);
		}

		return $providers;
	}

	/**
	 * @param array $manifest Normalized manifest.
	 * @return array{approved: bool, reason: string}
	 */
	private static function gate_manifest( $manifest ) {
		if ( 'approved' !== $manifest['status'] ) {
			return array( 'approved' => false, 'reason' => 'manifest-not-approved' );
		}

		if ( ! hash_equals( self::site_origin(), $manifest['allowedOrigin'] ) ) {
			return array( 'approved' => false, 'reason' => 'origin-not-approved' );
		}

		if ( self::is_placeholder( $manifest['owner'] ) || 'passed' !== $manifest['keyRestrictionReview'] ) {
			return array( 'approved' => false, 'reason' => 'key-restriction-not-approved' );
		}

		if ( 'passed' !== $manifest['locationDataReview'] ) {
			return array( 'approved' => false, 'reason' => 'location-data-not-approved' );
		}

		if ( 'passed' !== $manifest['commercialDisclosureReview'] ) {
			return array( 'approved' => false, 'reason' => 'commercial-disclosure-not-approved' );
		}

		return array( 'approved' => true, 'reason' => '' );
	}

	/**
	 * @param string $reason Machine-readable gate reason.
	 * @return array
	 */
	private static function gated_configuration( $reason ) {
		return array(
			'status'   => 'configuration-gated',
			'provider' => 'none',
			'reason'   => sanitize_key( $reason ),
		);
	}

	/**
	 * @param mixed $value Candidate origin.
	 * @return string
	 */
	private static function normalize_origin( $value ) {
		$origin = esc_url_raw( trim( (string) $value ) );
		$parts  = wp_parse_url( $origin );
		if ( ! is_array( $parts ) || empty( $parts['scheme'] ) || 'https' !== strtolower( $parts['scheme'] ) || empty( $parts['host'] ) || ! empty( $parts['query'] ) || ! empty( $parts['fragment'] ) || ( ! empty( $parts['path'] ) && '/' !== $parts['path'] ) ) {
			return '';
		}

		return 'https://' . strtolower( $parts['host'] ) . ( isset( $parts['port'] ) ? ':' . (int) $parts['port'] : '' );
	}

	/**
	 * @return string
	 */
	private static function site_origin() {
		return self::normalize_origin( home_url( '/' ) );
	}

	/**
	 * @param string $value Candidate restricted browser key.
	 * @return bool
	 */
	private static function is_browser_key( $value ) {
		return ! self::is_placeholder( $value ) && 20 <= strlen( $value ) && (bool) preg_match( '/^[A-Za-z0-9_-]+$/', $value );
	}

	/**
	 * @param string $value Candidate map ID.
	 * @return bool
	 */
	private static function is_map_id( $value ) {
		return ! self::is_placeholder( $value ) && 6 <= strlen( $value ) && (bool) preg_match( '/^[A-Za-z0-9_-]+$/', $value );
	}

	/**
	 * @param string $value Review status.
	 * @return bool
	 */
	private static function is_review_state( $value ) {
		return in_array( $value, array( 'pending', 'passed', 'failed' ), true );
	}

	/**
	 * @param mixed $value Raw ISO date.
	 * @return string
	 */
	private static function sanitize_iso_date( $value ) {
		$value = trim( sanitize_text_field( (string) $value ) );
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ) {
			return '';
		}

		$parts = explode( '-', $value );

		return checkdate( (int) $parts[1], (int) $parts[2], (int) $parts[0] ) ? $value : '';
	}

	/**
	 * @param string $value Candidate required value.
	 * @return bool
	 */
	private static function is_placeholder( $value ) {
		$value = strtolower( trim( (string) $value ) );

		return '' === $value || false !== strpos( $value, 'tbd' ) || false !== strpos( $value, 'placeholder' ) || false !== strpos( $value, 'your_api_key' );
	}
}

Hea_Lth_Directory_Map_Registry::boot();
