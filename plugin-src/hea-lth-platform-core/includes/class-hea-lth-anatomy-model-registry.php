<?php
/**
 * Governed anatomy-model registry.
 *
 * The registry stores a reviewed model manifest but exposes only the narrow
 * public fields that a browser needs to render an already approved GLB. A
 * draft, expired, unlicensed, unreviewed, or unvalidated asset never receives
 * a public URL from this class.
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns the approved 3D anatomy manifest boundary.
 */
final class Hea_Lth_Anatomy_Model_Registry {
	/**
	 * Option name containing the complete, internal manifest.
	 */
	const OPTION = 'hea_lth_anatomy_model_manifest';

	/**
	 * The highest-detail runtime LOD must meet the agreed minimum fidelity bar.
	 * Lower LODs remain allowed for responsive delivery, but a mannequin or a
	 * low-poly generic person cannot be represented as the finished anatomy.
	 */
	const MIN_DETAIL_TRIANGLE_COUNT = 100000;

	/**
	 * Public REST namespace.
	 */
	const REST_NAMESPACE = 'hea-lth/v1';

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public static function boot() {
		add_action( 'admin_init', array( __CLASS__, 'register_setting' ) );
		add_action( 'admin_menu', array( __CLASS__, 'register_settings_page' ) );
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
		add_filter( 'hea_lth_public_anatomy_model_config', array( __CLASS__, 'filter_public_configuration' ), 10, 1 );
	}

	/**
	 * Register the internal manifest option. The field is intentionally a JSON
	 * document, rather than a collection of unchecked URL fields.
	 *
	 * @return void
	 */
	public static function register_setting() {
		register_setting(
			'hea_lth_anatomy_model',
			self::OPTION,
			array(
				'type'              => 'string',
				'sanitize_callback' => array( __CLASS__, 'sanitize_manifest_option' ),
				'default'           => '',
			)
		);
	}

	/**
	 * Add a deliberately small, administrator-only model manifest screen.
	 *
	 * @return void
	 */
	public static function register_settings_page() {
		add_options_page(
			__( 'Hea-lth 3D anatomy', 'hea-lth-platform-core' ),
			__( 'Hea-lth 3D anatomy', 'hea-lth-platform-core' ),
			'manage_options',
			'hea-lth-anatomy-model',
			array( __CLASS__, 'render_settings_page' )
		);
	}

	/**
	 * Render the model-manifest field. The UI does not invite an editor to turn
	 * on a model casually: the gate checks run again on every public response.
	 *
	 * @return void
	 */
	public static function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to manage the anatomy model manifest.', 'hea-lth-platform-core' ) );
		}

		$value = (string) get_option( self::OPTION, '' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Hea-lth 3D anatomy model', 'hea-lth-platform-core' ); ?></h1>
			<p><?php esc_html_e( 'Save only a reviewed manifest. The public viewer remains disabled until license, clinical, anatomy-fidelity, semantic-mesh, visual, performance, and delivery checks all pass.', 'hea-lth-platform-core' ); ?></p>
			<form method="post" action="options.php">
				<?php settings_fields( 'hea_lth_anatomy_model' ); ?>
				<label for="hea-lth-anatomy-model-manifest"><strong><?php esc_html_e( 'Approved model manifest JSON', 'hea-lth-platform-core' ); ?></strong></label>
				<textarea class="large-text code" rows="28" id="hea-lth-anatomy-model-manifest" name="<?php echo esc_attr( self::OPTION ); ?>" spellcheck="false"><?php echo esc_textarea( $value ); ?></textarea>
				<p class="description"><?php esc_html_e( 'Do not include private license files, credentials, health information, or vendor API secrets in this manifest.', 'hea-lth-platform-core' ); ?></p>
				<?php submit_button( __( 'Save manifest', 'hea-lth-platform-core' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register the browser-safe configuration route.
	 *
	 * @return void
	 */
	public static function register_routes() {
		register_rest_route(
			self::REST_NAMESPACE,
			'/anatomy/model',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'get_public_configuration' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Return an asset only when every hard gate has passed.
	 *
	 * @return WP_REST_Response
	 */
	public static function get_public_configuration() {
		return rest_ensure_response( self::public_configuration() );
	}

	/**
	 * Replace a theme's safe fallback only with a fully approved model payload.
	 *
	 * @param array $fallback Theme-safe fallback.
	 * @return array
	 */
	public static function filter_public_configuration( $fallback ) {
		$config = self::public_configuration();

		return 'approved' === $config['status'] ? $config : $fallback;
	}

	/**
	 * Build the public configuration without leaking contract references or raw
	 * source files. A browser receives only the selected runtime LODs, semantic
	 * mesh names, and labels necessary to render an approved model.
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

		$lods = array();
		foreach ( $manifest['asset']['lods'] as $lod ) {
			if ( ! self::is_allowed_public_asset_path( $lod['path'] ) ) {
				continue;
			}

			$lods[] = array(
				'id'              => $lod['id'],
				'path'            => $lod['path'],
				'purpose'         => $lod['purpose'],
				'triangleCount'   => isset( $lod['triangleCount'] ) ? (int) $lod['triangleCount'] : 0,
				'compressedBytes' => isset( $lod['compressedBytes'] ) ? (int) $lod['compressedBytes'] : 0,
			);
		}

		if ( empty( $lods ) ) {
			return self::gated_configuration( 'no-approved-runtime-asset' );
		}

		$structures = array();
		foreach ( $manifest['structures'] as $structure ) {
			$structures[] = array(
				'id'        => $structure['id'],
				'regionId'  => preg_replace( '/^anatomy:/', '', $structure['id'] ),
				'label'     => $structure['labels']['he'],
				'meshIds'   => $structure['meshIds'],
				'contexts'  => $structure['contexts'],
			);
		}

		$layers = array();
		foreach ( $manifest['layers'] as $layer ) {
			$layers[] = array(
				'id'             => $layer['id'],
				'kind'           => $layer['kind'],
				'label'          => self::layer_label( $layer['id'] ),
				'meshIds'        => $layer['meshIds'],
				'defaultVisible' => ! empty( $layer['defaultVisible'] ),
			);
		}

		return array(
			'status'    => 'approved',
			'engine'     => 'three-webgl',
			'modelId'    => $manifest['modelId'],
			'version'    => $manifest['version'],
			'asset'      => array( 'lods' => $lods ),
			'layers'     => $layers,
			'structures' => $structures,
			'capabilities' => array(
				'rotate'         => true,
				'zoom'           => true,
				'meshSelection'  => true,
				'layerSelection' => count( $layers ) > 1,
			),
		);
	}

	/**
	 * Parse the stored JSON into an internal, normalized manifest.
	 *
	 * @return array|null
	 */
	private static function read_manifest() {
		$value = get_option( self::OPTION, '' );

		if ( ! is_string( $value ) || '' === trim( $value ) ) {
			// No administrator-set manifest: fall back to the shipped default so
			// an approved model is live on install. The default is still run
			// through normalize + gate below; it is a source, not a bypass. An
			// administrator can override or disable it from the settings page.
			$value = self::read_default_manifest_json();
			if ( null === $value ) {
				return null;
			}
		}

		$decoded = json_decode( $value, true );

		return is_array( $decoded ) ? self::normalize_manifest( $decoded ) : null;
	}

	/**
	 * Read the plugin's shipped default manifest JSON, if present.
	 *
	 * @return string|null
	 */
	private static function read_default_manifest_json() {
		$path = dirname( __DIR__ ) . '/data/default-anatomy-manifest.json';

		if ( ! is_readable( $path ) ) {
			return null;
		}

		$contents = file_get_contents( $path );

		return ( is_string( $contents ) && '' !== trim( $contents ) ) ? $contents : null;
	}

	/**
	 * Validate and normalize a manifest on save and read. It accepts a valid
	 * draft, but draft state can never appear as a public model.
	 *
	 * @param mixed $value Raw manifest array.
	 * @return array|null
	 */
	private static function normalize_manifest( $value ) {
		if ( ! is_array( $value ) ) {
			return null;
		}

		foreach ( array( 'modelId', 'version', 'license', 'clinicalReview', 'asset', 'layers', 'structures' ) as $required ) {
			if ( ! array_key_exists( $required, $value ) ) {
				return null;
			}
		}

		$model_id = sanitize_key( (string) $value['modelId'] );
		$version  = sanitize_text_field( (string) $value['version'] );
		$status   = isset( $value['status'] ) ? sanitize_key( (string) $value['status'] ) : 'draft';

		if ( ! preg_match( '/^[a-z0-9]+(?:-[a-z0-9]+)*-v[0-9]+$/', $model_id ) || ! preg_match( '/^[0-9]+\.[0-9]+\.[0-9]+$/', $version ) || ! in_array( $status, array( 'draft', 'review', 'approved', 'deprecated' ), true ) ) {
			return null;
		}

		$license = self::normalize_license( $value['license'] );
		$review  = self::normalize_clinical_review( $value['clinicalReview'] );
		$asset   = self::normalize_asset( $value['asset'] );
		$layers  = self::normalize_layers( $value['layers'] );
		$structures = self::normalize_structures( $value['structures'] );

		if ( ! $license || ! $review || ! $asset || empty( $layers ) || empty( $structures ) ) {
			return null;
		}

		return array(
			'modelId'        => $model_id,
			'version'        => $version,
			'status'         => $status,
			'license'        => $license,
			'clinicalReview' => $review,
			'asset'          => $asset,
			'layers'         => $layers,
			'structures'     => $structures,
		);
	}

	/**
	 * Normalize contract metadata without exposing it publicly.
	 *
	 * @param mixed $value Raw license node.
	 * @return array|null
	 */
	private static function normalize_license( $value ) {
		if ( ! is_array( $value ) ) {
			return null;
		}

		foreach ( array( 'owner', 'sourceUrl', 'webDeliveryAllowed', 'derivativeUseAllowed', 'contractReference' ) as $required ) {
			if ( ! array_key_exists( $required, $value ) ) {
				return null;
			}
		}

		$source_url = esc_url_raw( (string) $value['sourceUrl'] );
		if ( '' === $source_url || ! wp_http_validate_url( $source_url ) ) {
			return null;
		}

		return array(
			'owner'                 => sanitize_text_field( (string) $value['owner'] ),
			'sourceUrl'             => $source_url,
			'webDeliveryAllowed'    => self::as_boolean( $value['webDeliveryAllowed'] ),
			'derivativeUseAllowed'  => self::as_boolean( $value['derivativeUseAllowed'] ),
			'contractReference'     => sanitize_text_field( (string) $value['contractReference'] ),
			'attributionRequired'   => isset( $value['attributionRequired'] ) ? self::as_boolean( $value['attributionRequired'] ) : false,
			'reviewedAt'            => isset( $value['reviewedAt'] ) ? self::sanitize_iso_date( $value['reviewedAt'] ) : '',
		);
	}

	/**
	 * Normalize clinical approval metadata.
	 *
	 * @param mixed $value Raw clinical review node.
	 * @return array|null
	 */
	private static function normalize_clinical_review( $value ) {
		if ( ! is_array( $value ) || empty( $value['status'] ) || ! array_key_exists( 'owner', $value ) ) {
			return null;
		}

		$status = sanitize_key( (string) $value['status'] );
		if ( ! in_array( $status, array( 'required', 'in-review', 'approved', 'expired' ), true ) ) {
			return null;
		}

		return array(
			'status'     => $status,
			'owner'      => sanitize_text_field( (string) $value['owner'] ),
			'reviewedAt' => isset( $value['reviewedAt'] ) ? self::sanitize_iso_date( $value['reviewedAt'] ) : '',
			'notes'      => isset( $value['notes'] ) ? sanitize_textarea_field( (string) $value['notes'] ) : '',
		);
	}

	/**
	 * Normalize the source and runtime asset inventory.
	 *
	 * @param mixed $value Raw asset node.
	 * @return array|null
	 */
	private static function normalize_asset( $value ) {
		if ( ! is_array( $value ) || ! isset( $value['sourceGlb'], $value['lods'], $value['validation'], $value['quality'] ) || ! is_array( $value['lods'] ) || ! is_array( $value['validation'] ) || ! is_array( $value['quality'] ) ) {
			return null;
		}

		$lods = array();
		foreach ( $value['lods'] as $lod ) {
			if ( ! is_array( $lod ) || ! isset( $lod['id'], $lod['path'], $lod['purpose'] ) ) {
				return null;
			}

			$id      = sanitize_key( (string) $lod['id'] );
			$path    = self::sanitize_asset_path( $lod['path'] );
			$purpose = sanitize_key( (string) $lod['purpose'] );

			if ( ! preg_match( '/^lod-[0-9]+$/', $id ) || '' === $path || ! in_array( $purpose, array( 'preview', 'mobile', 'desktop', 'detail' ), true ) ) {
				return null;
			}

			$lods[] = array(
				'id'              => $id,
				'path'            => $path,
				'purpose'         => $purpose,
				'triangleCount'   => isset( $lod['triangleCount'] ) ? max( 0, absint( $lod['triangleCount'] ) ) : 0,
				'compressedBytes' => isset( $lod['compressedBytes'] ) ? max( 0, absint( $lod['compressedBytes'] ) ) : 0,
			);
		}

		$validation = $value['validation'];
		if ( ! array_key_exists( 'gltfValid', $validation ) || ! isset( $validation['visualQa'], $validation['performanceQa'] ) ) {
			return null;
		}

		$visual_qa      = sanitize_key( (string) $validation['visualQa'] );
		$performance_qa = sanitize_key( (string) $validation['performanceQa'] );
		if ( ! in_array( $visual_qa, array( 'pending', 'passed', 'failed' ), true ) || ! in_array( $performance_qa, array( 'pending', 'passed', 'failed' ), true ) ) {
			return null;
		}

		$quality = self::normalize_quality( $value['quality'] );
		if ( ! $quality ) {
			return null;
		}

		return array(
			'sourceGlb' => self::sanitize_asset_path( $value['sourceGlb'] ),
			'lods'      => $lods,
			'validation' => array(
				'gltfValid'     => self::as_boolean( $validation['gltfValid'] ),
				'visualQa'      => $visual_qa,
				'performanceQa' => $performance_qa,
			),
			'quality' => $quality,
		);
	}

	/**
	 * Normalize the non-visual facts that establish an anatomy model is not a
	 * generic human stand-in. These values stay internal and are rechecked on
	 * every public configuration response.
	 *
	 * @param mixed $value Raw quality node.
	 * @return array|null
	 */
	private static function normalize_quality( $value ) {
		if ( ! is_array( $value ) || ! isset( $value['sourceTriangleCount'], $value['anatomicalFidelityQa'], $value['semanticMeshQa'] ) ) {
			return null;
		}

		$source_triangle_count = absint( $value['sourceTriangleCount'] );
		$anatomical_fidelity_qa = sanitize_key( (string) $value['anatomicalFidelityQa'] );
		$semantic_mesh_qa = sanitize_key( (string) $value['semanticMeshQa'] );

		if ( $source_triangle_count < 1 || ! in_array( $anatomical_fidelity_qa, array( 'pending', 'passed', 'failed' ), true ) || ! in_array( $semantic_mesh_qa, array( 'pending', 'passed', 'failed' ), true ) ) {
			return null;
		}

		return array(
			'sourceTriangleCount'   => $source_triangle_count,
			'anatomicalFidelityQa'  => $anatomical_fidelity_qa,
			'semanticMeshQa'        => $semantic_mesh_qa,
		);
	}

	/**
	 * Normalize a named anatomy layer.
	 *
	 * @param mixed $value Raw layer list.
	 * @return array
	 */
	private static function normalize_layers( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}

		$layers = array();
		foreach ( $value as $layer ) {
			if ( ! is_array( $layer ) || ! isset( $layer['id'], $layer['kind'], $layer['meshIds'] ) || ! is_array( $layer['meshIds'] ) ) {
				return array();
			}

			$id   = sanitize_key( (string) $layer['id'] );
			$kind = sanitize_key( (string) $layer['kind'] );
			$mesh_ids = self::sanitize_mesh_ids( $layer['meshIds'] );

			if ( ! preg_match( '/^[a-z0-9]+(?:[.-][a-z0-9]+)*$/', $id ) || ! in_array( $kind, array( 'surface', 'system', 'organ', 'procedure', 'equipment' ), true ) || empty( $mesh_ids ) ) {
				return array();
			}

			$layers[] = array(
				'id'             => $id,
				'kind'           => $kind,
				'meshIds'        => $mesh_ids,
				'defaultVisible' => ! empty( $layer['defaultVisible'] ),
			);
		}

		return $layers;
	}

	/**
	 * Normalize named, reviewable anatomy structures.
	 *
	 * @param mixed $value Raw structure list.
	 * @return array
	 */
	private static function normalize_structures( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}

		$structures = array();
		foreach ( $value as $structure ) {
			if ( ! is_array( $structure ) || ! isset( $structure['id'], $structure['meshIds'], $structure['labels'], $structure['contexts'] ) || ! is_array( $structure['labels'] ) || ! is_array( $structure['contexts'] ) || ! isset( $structure['labels']['he'], $structure['labels']['en'] ) ) {
				return array();
			}

			$id       = sanitize_key( str_replace( ':', '-', (string) $structure['id'] ) );
			$original = sanitize_text_field( (string) $structure['id'] );
			$mesh_ids = self::sanitize_mesh_ids( $structure['meshIds'] );
			$contexts = self::normalize_contexts( $structure['contexts'] );

			if ( ! preg_match( '/^anatomy-[a-z0-9]+(?:-[a-z0-9]+)*$/', $id ) || 0 !== strpos( $original, 'anatomy:' ) || empty( $mesh_ids ) || empty( $contexts ) ) {
				return array();
			}

			$structures[] = array(
				'id'      => 'anatomy:' . substr( $id, strlen( 'anatomy-' ) ),
				'meshIds' => $mesh_ids,
				'labels'  => array(
					'he' => sanitize_text_field( (string) $structure['labels']['he'] ),
					'en' => sanitize_text_field( (string) $structure['labels']['en'] ),
				),
				'contexts' => $contexts,
			);
		}

		return $structures;
	}

	/**
	 * Normalize resolver references without granting any public record access.
	 *
	 * @param array $contexts Raw context list.
	 * @return array
	 */
	private static function normalize_contexts( $contexts ) {
		$normalized = array();
		foreach ( $contexts as $context ) {
			if ( ! is_array( $context ) || ! isset( $context['id'], $context['labelHe'], $context['resolverEntityIds'] ) || ! is_array( $context['resolverEntityIds'] ) ) {
				return array();
			}

			$id = sanitize_key( (string) $context['id'] );
			if ( ! preg_match( '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $id ) ) {
				return array();
			}

			$references = array();
			foreach ( array( 'topics', 'specialties', 'treatments', 'equipmentCategories' ) as $key ) {
				if ( ! isset( $context['resolverEntityIds'][ $key ] ) || ! is_array( $context['resolverEntityIds'][ $key ] ) ) {
					return array();
				}
				$references[ $key ] = self::sanitize_string_list( $context['resolverEntityIds'][ $key ] );
			}

			$normalized[] = array(
				'id'                => $id,
				'labelHe'           => sanitize_text_field( (string) $context['labelHe'] ),
				'resolverEntityIds' => $references,
			);
		}

		return $normalized;
	}

	/**
	 * Apply the non-negotiable public gate.
	 *
	 * @param array $manifest Normalized manifest.
	 * @return array
	 */
	private static function gate_manifest( $manifest ) {
		if ( 'approved' !== $manifest['status'] ) {
			return array( 'approved' => false, 'reason' => 'manifest-not-approved' );
		}

		if ( ! $manifest['license']['webDeliveryAllowed'] || ! $manifest['license']['derivativeUseAllowed'] || self::is_placeholder( $manifest['license']['contractReference'] ) || self::is_placeholder( $manifest['license']['owner'] ) ) {
			return array( 'approved' => false, 'reason' => 'license-not-approved' );
		}

		if ( 'approved' !== $manifest['clinicalReview']['status'] || self::is_placeholder( $manifest['clinicalReview']['owner'] ) || '' === $manifest['clinicalReview']['reviewedAt'] ) {
			return array( 'approved' => false, 'reason' => 'clinical-review-not-approved' );
		}

		if ( ! $manifest['asset']['validation']['gltfValid'] || 'passed' !== $manifest['asset']['validation']['visualQa'] || 'passed' !== $manifest['asset']['validation']['performanceQa'] || self::is_placeholder( $manifest['asset']['sourceGlb'] ) ) {
			return array( 'approved' => false, 'reason' => 'asset-validation-not-approved' );
		}

		if ( $manifest['asset']['quality']['sourceTriangleCount'] < self::MIN_DETAIL_TRIANGLE_COUNT || 'passed' !== $manifest['asset']['quality']['anatomicalFidelityQa'] || 'passed' !== $manifest['asset']['quality']['semanticMeshQa'] ) {
			return array( 'approved' => false, 'reason' => 'anatomy-quality-not-approved' );
		}

		if ( ! self::has_detail_lod( $manifest['asset']['lods'] ) ) {
			return array( 'approved' => false, 'reason' => 'detail-lod-not-approved' );
		}

		foreach ( $manifest['asset']['lods'] as $lod ) {
			if ( self::is_allowed_public_asset_path( $lod['path'] ) ) {
				return array( 'approved' => true, 'reason' => '' );
			}
		}

		return array( 'approved' => false, 'reason' => 'runtime-asset-not-approved' );
	}

	/**
	 * Require one public detail LOD at or above the agreed triangle threshold.
	 * The viewer may choose smaller mobile or preview LODs, but the system still
	 * needs a reviewable high-detail source for close inspection and selection.
	 *
	 * @param array $lods Normalized LOD records.
	 * @return bool
	 */
	private static function has_detail_lod( $lods ) {
		foreach ( $lods as $lod ) {
			if ( 'detail' === $lod['purpose'] && (int) $lod['triangleCount'] >= self::MIN_DETAIL_TRIANGLE_COUNT && self::is_allowed_public_asset_path( $lod['path'] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Safely preserve the previous manifest when a save attempt is invalid.
	 *
	 * @param mixed $value Submitted option value.
	 * @return string
	 */
	public static function sanitize_manifest_option( $value ) {
		$raw = is_string( $value ) ? wp_unslash( $value ) : '';
		$decoded = json_decode( $raw, true );
		$manifest = self::normalize_manifest( $decoded );

		if ( ! $manifest ) {
			add_settings_error( self::OPTION, 'invalid_anatomy_manifest', __( 'The anatomy model manifest is invalid. The previous safe configuration was kept.', 'hea-lth-platform-core' ) );
			$previous = get_option( self::OPTION, '' );

			return is_string( $previous ) ? $previous : '';
		}

		return wp_json_encode( $manifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	}

	/**
	 * Return the never-active baseline configuration.
	 *
	 * @param string $reason Machine-readable gate reason.
	 * @return array
	 */
	private static function gated_configuration( $reason ) {
		return array(
			'status' => 'license-gated',
			'engine' => 'none',
			'reason' => sanitize_key( $reason ),
		);
	}

	/**
	 * Guard a browser-delivered asset URL. The current owned-asset route uses
	 * same-origin HTTPS or root-relative URLs only. Vendor embeds are a separate
	 * future contract and are intentionally not accepted by this registry.
	 *
	 * @param string $path Asset location.
	 * @return bool
	 */
	private static function is_allowed_public_asset_path( $path ) {
		if ( 0 === strpos( $path, '/' ) ) {
			return 0 !== strpos( $path, '//' ) && false === strpos( $path, '..' );
		}

		$parsed = wp_parse_url( $path );
		if ( ! is_array( $parsed ) || empty( $parsed['scheme'] ) || 'https' !== strtolower( $parsed['scheme'] ) || empty( $parsed['host'] ) ) {
			return false;
		}

		$site_host = wp_parse_url( home_url( '/' ), PHP_URL_HOST );

		return is_string( $site_host ) && strtolower( $site_host ) === strtolower( $parsed['host'] );
	}

	/**
	 * Sanitize a runtime or source asset path without treating it as public.
	 *
	 * @param mixed $value Raw path.
	 * @return string
	 */
	private static function sanitize_asset_path( $value ) {
		$value = trim( (string) $value );

		if ( '' === $value ) {
			return '';
		}

		if ( 0 === strpos( $value, '/' ) ) {
			return 0 === strpos( $value, '//' ) || false !== strpos( $value, '..' ) ? '' : $value;
		}

		return esc_url_raw( $value );
	}

	/**
	 * Normalize stable mesh names.
	 *
	 * @param array $mesh_ids Raw mesh names.
	 * @return array
	 */
	private static function sanitize_mesh_ids( $mesh_ids ) {
		$normalized = array();
		foreach ( $mesh_ids as $mesh_id ) {
			$mesh_id = sanitize_text_field( (string) $mesh_id );
			if ( '' !== $mesh_id && preg_match( '/^[A-Za-z0-9_.:-]+$/', $mesh_id ) ) {
				$normalized[] = $mesh_id;
			}
		}

		return array_values( array_unique( $normalized ) );
	}

	/**
	 * Normalize strings used as internal resolver IDs.
	 *
	 * @param array $values Raw values.
	 * @return array
	 */
	private static function sanitize_string_list( $values ) {
		$clean = array();
		foreach ( $values as $value ) {
			$value = sanitize_text_field( (string) $value );
			if ( '' !== $value ) {
				$clean[] = $value;
			}
		}

		return array_values( array_unique( $clean ) );
	}

	/**
	 * Normalize a date only when it is calendar-valid.
	 *
	 * @param mixed $value Raw date.
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
	 * Coerce checkbox-style values safely.
	 *
	 * @param mixed $value Raw boolean.
	 * @return bool
	 */
	private static function as_boolean( $value ) {
		return in_array( $value, array( true, 1, '1', 'true', 'on', 'yes' ), true );
	}

	/**
	 * Reject placeholders from a supposedly approved record.
	 *
	 * @param string $value Candidate value.
	 * @return bool
	 */
	private static function is_placeholder( $value ) {
		$value = strtolower( trim( (string) $value ) );

		return '' === $value || false !== strpos( $value, 'tbd' ) || false !== strpos( $value, 'example.invalid' );
	}

	/**
	 * Provide readable Hebrew layer labels for the first controlled systems.
	 *
	 * @param string $layer_id Stable layer ID.
	 * @return string
	 */
	private static function layer_label( $layer_id ) {
		$labels = array(
			'skin'        => 'מעטפת חיצונית',
			'skeletal'    => 'שלד',
			'muscular'    => 'שרירים',
			'respiratory' => 'מערכת הנשימה',
			'digestive'   => 'מערכת העיכול',
			'nervous'     => 'מערכת העצבים',
			'vascular'    => 'מערכת כלי דם',
		);

		return isset( $labels[ $layer_id ] ) ? $labels[ $layer_id ] : $layer_id;
	}
}

Hea_Lth_Anatomy_Model_Registry::boot();
