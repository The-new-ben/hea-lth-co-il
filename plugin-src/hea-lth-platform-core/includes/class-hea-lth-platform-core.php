<?php
/**
 * Hea-lth platform content model.
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns WordPress data types and metadata only.
 *
 * Public profiles, routing, payment, consent workflows, medical records, and
 * operational data are deliberately outside this class. The data model can be
 * installed safely before those systems are selected.
 */
final class Hea_Lth_Platform_Core {
	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function boot() {
		add_action( 'init', array( __CLASS__, 'register_content_model' ) );
		add_action( 'init', array( __CLASS__, 'register_metadata' ), 20 );
		add_action( 'rest_api_init', array( __CLASS__, 'register_public_healthcheck' ) );
	}

	/**
	 * Register a narrow release-verification surface for the deployment pipeline.
	 *
	 * It returns component identity only. It never exposes provider data, leads,
	 * credentials, medical data, or internal routing configuration.
	 *
	 * @return void
	 */
	public static function register_public_healthcheck() {
		register_rest_route(
			'hea-lth-platform/v1',
			'/healthcheck',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'permission_callback' => '__return_true',
				'callback'            => array( __CLASS__, 'public_healthcheck' ),
			)
		);
	}

	/**
	 * Confirm that the active package and its persisted deployment record agree.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function public_healthcheck( WP_REST_Request $request ) {
		$release = get_option( 'hea_lth_agent_release_hea_lth_platform_core', array() );
		if ( ! is_array( $release ) || ! isset( $release['deployment_id'], $release['version'] ) ) {
			return new WP_Error( 'hea_lth_release_unknown', 'No verified release record is available.', array( 'status' => 503 ) );
		}

		$deployment_id = sanitize_text_field( (string) $release['deployment_id'] );
		$version       = sanitize_text_field( (string) $release['version'] );
		if ( ! hash_equals( HEA_LTH_PLATFORM_CORE_VERSION, $version ) || '' === $deployment_id ) {
			return new WP_Error( 'hea_lth_release_mismatch', 'The active package does not match its verified release record.', array( 'status' => 503 ) );
		}

		$expected_deployment = sanitize_text_field( (string) $request->get_param( 'deployment' ) );
		if ( '' !== $expected_deployment && ! hash_equals( $deployment_id, $expected_deployment ) ) {
			return new WP_Error( 'hea_lth_release_not_current', 'The requested deployment is not the active release.', array( 'status' => 409 ) );
		}

		return rest_ensure_response(
			array(
				'status'        => 'ok',
				'component'     => 'hea-lth-platform-core',
				'version'       => HEA_LTH_PLATFORM_CORE_VERSION,
				'deployment_id' => $deployment_id,
			)
		);
	}

	/**
	 * Register the model before flushing rewrite rules on activation.
	 *
	 * @return void
	 */
	public static function activate() {
		self::register_content_model();
		flush_rewrite_rules();
	}

	/**
	 * Flush WordPress rewrite state on deactivation only.
	 *
	 * @return void
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}

	/**
	 * Register taxonomies and post types.
	 *
	 * All types are intentionally admin-managed and not public routes yet. This
	 * prevents a new plugin from taking over powered legacy URLs before the URL
	 * migration map and content review are approved.
	 *
	 * @return void
	 */
	public static function register_content_model() {
		self::register_post_types();
		self::register_taxonomies();
	}

	/**
	 * Register controlled internal taxonomies.
	 *
	 * @return void
	 */
	private static function register_taxonomies() {
		$object_types = array( 'hp_provider', 'hp_clinic', 'hp_treatment', 'hp_equipment', 'hp_supplier', 'hp_clinic_plan' );

		$taxonomies = array(
			'hp_specialty' => array(
				'label'       => __( 'תחומי מומחיות', 'hea-lth-platform-core' ),
				'singular'    => __( 'תחום מומחיות', 'hea-lth-platform-core' ),
				'object_types' => array( 'hp_provider', 'hp_clinic', 'hp_treatment' ),
			),
			'hp_region' => array(
				'label'       => __( 'אזורי שירות', 'hea-lth-platform-core' ),
				'singular'    => __( 'אזור שירות', 'hea-lth-platform-core' ),
				'object_types' => array( 'hp_provider', 'hp_clinic' ),
			),
			'hp_service_type' => array(
				'label'       => __( 'סוגי שירות', 'hea-lth-platform-core' ),
				'singular'    => __( 'סוג שירות', 'hea-lth-platform-core' ),
				'object_types' => $object_types,
			),
			'hp_body_region' => array(
				'label'       => __( 'אזורי גוף', 'hea-lth-platform-core' ),
				'singular'    => __( 'אזור גוף', 'hea-lth-platform-core' ),
				'object_types' => array( 'hp_provider', 'hp_clinic', 'hp_treatment', 'hp_equipment' ),
			),
			'hp_procurement' => array(
				'label'        => __( 'קטגוריות רכש למרפאה', 'hea-lth-platform-core' ),
				'singular'     => __( 'קטגוריית רכש', 'hea-lth-platform-core' ),
				'object_types' => array( 'hp_equipment', 'hp_clinic_plan' ),
			),
			'hp_clinic_type' => array(
				'label'        => __( 'סוגי מרפאות', 'hea-lth-platform-core' ),
				'singular'     => __( 'סוג מרפאה', 'hea-lth-platform-core' ),
				'object_types' => array( 'hp_clinic_plan' ),
			),
		);

		foreach ( $taxonomies as $taxonomy => $definition ) {
			register_taxonomy(
				$taxonomy,
				$definition['object_types'],
				array(
					'labels'            => array(
						'name'          => $definition['label'],
						'singular_name' => $definition['singular'],
					),
					'public'            => false,
					'show_ui'           => true,
					'show_admin_column' => true,
					'show_in_rest'      => true,
					'hierarchical'      => true,
					'rewrite'           => false,
					'query_var'         => false,
				)
			);
		}
	}

	/**
	 * Register durable entity types. Keep their names prefixed and below the
	 * WordPress post-type key limit.
	 *
	 * @return void
	 */
	private static function register_post_types() {
		$types = array(
			'hp_provider' => array(
				'label'       => __( 'מקצוענים', 'hea-lth-platform-core' ),
				'singular'    => __( 'מקצוען', 'hea-lth-platform-core' ),
				'description' => __( 'רופאים ואנשי מקצוע עם פרטי פרופיל מבוקרים.', 'hea-lth-platform-core' ),
				'menu_icon'   => 'dashicons-businessperson',
			),
			'hp_clinic' => array(
				'label'       => __( 'מרפאות וארגונים', 'hea-lth-platform-core' ),
				'singular'    => __( 'מרפאה או ארגון', 'hea-lth-platform-core' ),
				'description' => __( 'מרפאות, מרכזים וארגונים עם פרטי שירות מבוקרים.', 'hea-lth-platform-core' ),
				'menu_icon'   => 'dashicons-building',
			),
			'hp_treatment' => array(
				'label'       => __( 'טיפולים ומסלולים', 'hea-lth-platform-core' ),
				'singular'    => __( 'טיפול או מסלול', 'hea-lth-platform-core' ),
				'description' => __( 'יחידות מידע מאושרות לטיפולים, בדיקות ושירותים.', 'hea-lth-platform-core' ),
				'menu_icon'   => 'dashicons-heart',
			),
			'hp_glossary' => array(
				'label'       => __( 'מילון בריאות', 'hea-lth-platform-core' ),
				'singular'    => __( 'מונח בריאות', 'hea-lth-platform-core' ),
				'description' => __( 'רשומות מילון מבוקרות עם מקור ותאריך עדכון.', 'hea-lth-platform-core' ),
				'menu_icon'   => 'dashicons-editor-spellcheck',
			),
			'hp_equipment' => array(
				'label'       => __( 'ציוד וטכנולוגיות', 'hea-lth-platform-core' ),
				'singular'    => __( 'ציוד או טכנולוגיה', 'hea-lth-platform-core' ),
				'description' => __( 'רשומות ציוד וטכנולוגיה שטרם הוגדרו כקטלוג ציבורי.', 'hea-lth-platform-core' ),
				'menu_icon'   => 'dashicons-admin-tools',
			),
			'hp_supplier' => array(
				'label'       => __( 'ספקים ויבואנים', 'hea-lth-platform-core' ),
				'singular'    => __( 'ספק או יבואן', 'hea-lth-platform-core' ),
				'description' => __( 'אולמות תצוגה מקצועיים של ספקי ציוד, טכנולוגיה ושירותים למרפאות.', 'hea-lth-platform-core' ),
				'menu_icon'   => 'dashicons-store',
			),
			'hp_clinic_plan' => array(
				'label'       => __( 'תכניות הקמת מרפאה', 'hea-lth-platform-core' ),
				'singular'    => __( 'תכנית הקמת מרפאה', 'hea-lth-platform-core' ),
				'description' => __( 'מפות רכש מסודרות לפי סוג מרפאה, קטגוריות ציוד ושירותים משלימים.', 'hea-lth-platform-core' ),
				'menu_icon'   => 'dashicons-clipboard',
			),
			'hp_b2b_request' => array(
				'label'       => __( 'פניות B2B', 'hea-lth-platform-core' ),
				'singular'    => __( 'פניית B2B', 'hea-lth-platform-core' ),
				'description' => __( 'פניות עסקיות פרטיות להקמת מרפאה, הצעות מחיר והצטרפות ספקים.', 'hea-lth-platform-core' ),
				'menu_icon'   => 'dashicons-email-alt2',
			),
		);

		foreach ( $types as $post_type => $definition ) {
			$is_supplier  = 'hp_supplier' === $post_type;
			$is_equipment = 'hp_equipment' === $post_type;
			$is_clinic_plan = 'hp_clinic_plan' === $post_type;
			$is_request     = 'hp_b2b_request' === $post_type;
			$is_public      = $is_supplier || $is_equipment || $is_clinic_plan;

			register_post_type(
				$post_type,
				array(
					'labels'             => array(
						'name'          => $definition['label'],
						'singular_name' => $definition['singular'],
						'add_new_item'  => sprintf( __( 'הוספת %s', 'hea-lth-platform-core' ), $definition['singular'] ),
						'edit_item'     => sprintf( __( 'עריכת %s', 'hea-lth-platform-core' ), $definition['singular'] ),
					),
					'description'        => $definition['description'],
					'public'             => $is_public,
					'publicly_queryable' => $is_public,
					'show_ui'            => true,
					'show_in_menu'       => true,
					'show_in_rest'       => ! $is_request,
					'has_archive'        => $is_supplier ? 'suppliers' : ( $is_clinic_plan ? 'professionals/clinic-build' : false ),
					'rewrite'            => $is_supplier ? array( 'slug' => 'suppliers', 'with_front' => false ) : ( $is_equipment ? array( 'slug' => 'medical-equipment', 'with_front' => false ) : ( $is_clinic_plan ? array( 'slug' => 'professionals/clinic-build', 'with_front' => false ) : false ) ),
					'query_var'          => $is_public,
					'menu_icon'          => $definition['menu_icon'],
					'supports'           => $is_request ? array( 'title' ) : array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions' ),
					'map_meta_cap'       => true,
				)
			);
		}
	}

	/**
	 * Register only typed fields that have a clear public use or review purpose.
	 * Private commercial and workflow fields are intentionally not represented in
	 * the theme or public REST response.
	 *
	 * @return void
	 */
	public static function register_metadata() {
		$directory_types = array( 'hp_provider', 'hp_clinic', 'hp_supplier' );

		foreach ( $directory_types as $post_type ) {
			self::register_post_meta( $post_type, 'hp_public_state', 'string', 'pending', array( __CLASS__, 'sanitize_public_state' ), true );
			self::register_post_meta( $post_type, 'hp_city', 'string', '', 'sanitize_text_field', true );
			self::register_post_meta( $post_type, 'hp_languages', 'array', array(), array( __CLASS__, 'sanitize_string_list' ), true );
			self::register_post_meta( $post_type, 'hp_accessibility', 'array', array(), array( __CLASS__, 'sanitize_string_list' ), true );
			self::register_post_meta( $post_type, 'hp_last_verified', 'string', '', array( __CLASS__, 'sanitize_iso_date' ), true );
			self::register_post_meta( $post_type, 'hp_public_disclosure', 'string', '', 'sanitize_textarea_field', true );
			self::register_post_meta( $post_type, 'hp_map_public_state', 'string', 'pending', array( __CLASS__, 'sanitize_map_public_state' ), true );
			self::register_post_meta( $post_type, 'hp_map_latitude', 'string', '', array( __CLASS__, 'sanitize_latitude' ), true );
			self::register_post_meta( $post_type, 'hp_map_longitude', 'string', '', array( __CLASS__, 'sanitize_longitude' ), true );
			self::register_post_meta( $post_type, 'hp_map_country_code', 'string', 'IL', array( __CLASS__, 'sanitize_map_country_code' ), true );
			self::register_post_meta( $post_type, 'hp_map_precision', 'string', 'city', array( __CLASS__, 'sanitize_map_precision' ), true );
		}

		foreach ( array( 'hp_supplier', 'hp_equipment' ) as $post_type ) {
			self::register_post_meta( $post_type, 'hp_website_url', 'string', '', 'esc_url_raw', true );
			self::register_post_meta( $post_type, 'hp_contact_email', 'string', '', 'sanitize_email', true );
			self::register_post_meta( $post_type, 'hp_contact_phone', 'string', '', 'sanitize_text_field', true );
			self::register_post_meta( $post_type, 'hp_source_url', 'string', '', 'esc_url_raw', true );
		}

		self::register_post_meta( 'hp_supplier', 'hp_address', 'string', '', 'sanitize_text_field', true );
		self::register_post_meta( 'hp_supplier', 'hp_brands', 'array', array(), array( __CLASS__, 'sanitize_string_list' ), true );
		self::register_post_meta( 'hp_supplier', 'hp_capabilities', 'array', array(), array( __CLASS__, 'sanitize_string_list' ), true );
		self::register_post_meta( 'hp_equipment', 'hp_supplier_id', 'integer', 0, 'absint', true );
		self::register_post_meta( 'hp_equipment', 'hp_technology', 'string', '', 'sanitize_text_field', true );
		self::register_post_meta( 'hp_equipment', 'hp_product_family', 'string', '', 'sanitize_text_field', true );
		self::register_post_meta( 'hp_equipment', 'hp_clinic_roles', 'array', array(), array( __CLASS__, 'sanitize_string_list' ), true );
		self::register_post_meta( 'hp_clinic_plan', 'hp_public_state', 'string', 'pending', array( __CLASS__, 'sanitize_public_state' ), true );
		self::register_post_meta( 'hp_clinic_plan', 'hp_last_verified', 'string', '', array( __CLASS__, 'sanitize_iso_date' ), true );
		self::register_post_meta( 'hp_clinic_plan', 'hp_plan_summary', 'string', '', 'sanitize_textarea_field', true );
		self::register_post_meta( 'hp_clinic_plan', 'hp_procurement_order', 'array', array(), array( __CLASS__, 'sanitize_string_list' ), true );

		foreach ( array( 'hp_request_type', 'hp_request_status', 'hp_contact_name', 'hp_contact_phone', 'hp_company_name', 'hp_city', 'hp_project_stage', 'hp_plan_interest', 'hp_context_slug', 'hp_consent_version', 'hp_created_utc' ) as $key ) {
			self::register_post_meta( 'hp_b2b_request', $key, 'string', '', 'sanitize_text_field', false );
		}
		self::register_post_meta( 'hp_b2b_request', 'hp_contact_email', 'string', '', 'sanitize_email', false );
		self::register_post_meta( 'hp_b2b_request', 'hp_company_url', 'string', '', 'esc_url_raw', false );
		self::register_post_meta( 'hp_b2b_request', 'hp_categories', 'array', array(), array( __CLASS__, 'sanitize_string_list' ), false );

		/*
		 * Existing posts and pages keep their powered URLs. They receive the
		 * same review metadata as new portal entities so the new theme can show
		 * only governed content during the staged migration.
		 */
		foreach ( array( 'post', 'page', 'hp_treatment', 'hp_glossary', 'hp_equipment' ) as $post_type ) {
			self::register_post_meta( $post_type, 'hp_editorial_state', 'string', 'draft', array( __CLASS__, 'sanitize_editorial_state' ), true );
			self::register_post_meta( $post_type, 'hp_last_reviewed', 'string', '', array( __CLASS__, 'sanitize_iso_date' ), true );
			self::register_post_meta( $post_type, 'hp_source_note', 'string', '', 'sanitize_textarea_field', true );
		}
	}

	/**
	 * Register a typed post meta key.
	 *
	 * @param string          $post_type Post type.
	 * @param string          $key Meta key.
	 * @param string          $type REST schema type.
	 * @param mixed           $default Default value.
	 * @param callable|string $sanitizer Sanitizer.
	 * @param bool            $show_in_rest Whether the editor can manage it by REST.
	 * @return void
	 */
	private static function register_post_meta( $post_type, $key, $type, $default, $sanitizer, $show_in_rest ) {
		$args = array(
			'single'            => true,
			'type'              => $type,
			'default'           => $default,
			'sanitize_callback' => $sanitizer,
			'auth_callback'     => array( __CLASS__, 'can_edit_post_meta' ),
			'show_in_rest'      => $show_in_rest,
		);

		if ( 'array' === $type ) {
			$args['show_in_rest'] = array(
				'schema' => array(
					'type'  => 'array',
					'items' => array(
						'type' => 'string',
					),
				),
			);
		}

		register_post_meta( $post_type, $key, $args );
	}

	/**
	 * Restrict metadata editing to users who can edit the related post.
	 *
	 * @param bool   $allowed Existing capability result.
	 * @param string $meta_key Meta key.
	 * @param int    $object_id Post ID.
	 * @param int    $user_id User ID.
	 * @return bool
	 */
	public static function can_edit_post_meta( $allowed, $meta_key, $object_id, $user_id ) {
		return user_can( (int) $user_id, 'edit_post', (int) $object_id );
	}

	/**
	 * Sanitize public profile-state values.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	public static function sanitize_public_state( $value ) {
		$value   = sanitize_key( (string) $value );
		$allowed = array( 'pending', 'verified', 'withdrawn' );

		return in_array( $value, $allowed, true ) ? $value : 'pending';
	}

	/**
	 * Keep location visibility independently approved from the general public
	 * profile state. A verified profile must not become a public map marker by
	 * accident.
	 *
	 * @param mixed $value Raw map visibility state.
	 * @return string
	 */
	public static function sanitize_map_public_state( $value ) {
		$value   = sanitize_key( (string) $value );
		$allowed = array( 'pending', 'approved', 'withdrawn' );

		return in_array( $value, $allowed, true ) ? $value : 'pending';
	}

	/**
	 * @param mixed $value Raw latitude.
	 * @return string
	 */
	public static function sanitize_latitude( $value ) {
		return self::sanitize_coordinate( $value, -90, 90 );
	}

	/**
	 * @param mixed $value Raw longitude.
	 * @return string
	 */
	public static function sanitize_longitude( $value ) {
		return self::sanitize_coordinate( $value, -180, 180 );
	}

	/**
	 * @param mixed $value Coordinate value.
	 * @param int   $minimum Inclusive lower bound.
	 * @param int   $maximum Inclusive upper bound.
	 * @return string
	 */
	private static function sanitize_coordinate( $value, $minimum, $maximum ) {
		$value = trim( (string) $value );
		if ( '' === $value || ! is_numeric( $value ) ) {
			return '';
		}

		$coordinate = (float) $value;
		if ( $coordinate < $minimum || $coordinate > $maximum ) {
			return '';
		}

		return rtrim( rtrim( number_format( $coordinate, 6, '.', '' ), '0' ), '.' );
	}

	/**
	 * The current map launch is explicitly Israel-only. Expansion requires a
	 * separate country, compliance, and data-quality decision.
	 *
	 * @param mixed $value Country code.
	 * @return string
	 */
	public static function sanitize_map_country_code( $value ) {
		return 'IL' === strtoupper( sanitize_text_field( (string) $value ) ) ? 'IL' : 'IL';
	}

	/**
	 * @param mixed $value Location precision label.
	 * @return string
	 */
	public static function sanitize_map_precision( $value ) {
		$value   = sanitize_key( (string) $value );
		$allowed = array( 'exact', 'city' );

		return in_array( $value, $allowed, true ) ? $value : 'city';
	}

	/**
	 * Sanitize content review-state values.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	public static function sanitize_editorial_state( $value ) {
		$value   = sanitize_key( (string) $value );
		$allowed = array( 'draft', 'review', 'approved', 'retired' );

		return in_array( $value, $allowed, true ) ? $value : 'draft';
	}

	/**
	 * Sanitize a YYYY-MM-DD value without silently accepting a malformed date.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	public static function sanitize_iso_date( $value ) {
		$value = trim( sanitize_text_field( (string) $value ) );

		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ) {
			return '';
		}

		$parts = explode( '-', $value );

		return checkdate( (int) $parts[1], (int) $parts[2], (int) $parts[0] ) ? $value : '';
	}

	/**
	 * Sanitize an array of short public labels.
	 *
	 * @param mixed $value Raw value.
	 * @return array
	 */
	public static function sanitize_string_list( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}

		$values = array_map( 'sanitize_text_field', $value );
		$values = array_filter( $values, static function ( $item ) {
			return '' !== $item;
		} );

		return array_values( array_unique( $values ) );
	}
}
