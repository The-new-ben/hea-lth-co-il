<?php
/**
 * Authenticated supplier workspace, catalog review queue, and private lead inbox.
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Connects a WordPress account to one reviewed supplier profile.
 */
final class Hea_Lth_Supplier_Portal {
	const ROLE              = 'hea_lth_supplier';
	const CATALOG_POST_TYPE = 'hp_catalog_submit';
	const CATALOG_ACTION    = 'hea_lth_catalog_submission';
	const PIPELINE_ACTION   = 'hea_lth_supplier_pipeline';
	const USER_SUPPLIER_META = 'hea_lth_supplier_id';

	/** @var array<string, string> */
	private static $plans = array(
		'verified' => 'Verified',
		'showroom' => 'Showroom',
		'growth'   => 'Growth',
	);

	/** @var array<string, string> */
	private static $membership_states = array(
		'pending' => 'ממתין לאישור',
		'active'  => 'פעיל',
		'paused'  => 'מושהה',
	);

	/** @var array<string, string> */
	private static $pipeline_states = array(
		'new'         => 'חדש',
		'viewed'      => 'נצפה',
		'contacted'   => 'נוצר קשר',
		'qualified'   => 'מתאים להמשך',
		'closed_won'  => 'נסגרה עסקה',
		'closed_lost' => 'לא הבשיל לעסקה',
	);

	/**
	 * @return void
	 */
	public static function boot() {
		add_action( 'init', array( __CLASS__, 'register_role' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_catalog_post_type' ), 11 );
		add_action( 'init', array( __CLASS__, 'register_metadata' ), 21 );
		add_action( 'admin_post_' . self::CATALOG_ACTION, array( __CLASS__, 'handle_catalog_submission' ) );
		add_action( 'admin_post_' . self::PIPELINE_ACTION, array( __CLASS__, 'handle_pipeline_update' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'register_admin_boxes' ) );
		add_action( 'save_post_hp_supplier', array( __CLASS__, 'save_supplier_admin' ), 10, 2 );
		add_action( 'save_post_hp_b2b_request', array( __CLASS__, 'save_request_admin' ), 10, 2 );
		add_action( 'save_post_' . self::CATALOG_POST_TYPE, array( __CLASS__, 'save_catalog_admin' ), 10, 2 );
	}

	/**
	 * Create a minimal account role. Supplier actions are authorized by profile
	 * ownership in the handlers rather than broad WordPress editing rights.
	 *
	 * @return void
	 */
	public static function register_role() {
		if ( ! get_role( self::ROLE ) ) {
			add_role( self::ROLE, 'ספק Hea-lth', array( 'read' => true ) );
		}
	}

	/**
	 * A private review queue. Submissions never receive a public URL.
	 *
	 * @return void
	 */
	public static function register_catalog_post_type() {
		register_post_type(
			self::CATALOG_POST_TYPE,
			array(
				'labels'              => array(
					'name'          => 'בקשות קטלוג',
					'singular_name' => 'בקשת קטלוג',
					'edit_item'     => 'בדיקת בקשת קטלוג',
				),
				'description'         => 'בקשות פרטיות של ספקים להוספה ולעדכון מוצרים.',
				'public'              => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'show_ui'             => true,
				'show_in_menu'        => 'edit.php?post_type=hp_supplier',
				'show_in_rest'        => false,
				'menu_icon'           => 'dashicons-products',
				'supports'            => array( 'title' ),
				'map_meta_cap'        => true,
			)
		);
	}

	/**
	 * @return void
	 */
	public static function register_metadata() {
		self::meta( 'hp_supplier', 'hp_account_user_ids', 'array', array(), array( __CLASS__, 'sanitize_id_list' ) );
		self::meta( 'hp_supplier', 'hp_membership_plan', 'string', 'verified', array( __CLASS__, 'sanitize_plan' ) );
		self::meta( 'hp_supplier', 'hp_membership_state', 'string', 'pending', array( __CLASS__, 'sanitize_membership_state' ) );

		self::meta( 'hp_b2b_request', 'hp_assigned_supplier_id', 'integer', 0, 'absint' );
		self::meta( 'hp_b2b_request', 'hp_lead_release_state', 'string', 'held', array( __CLASS__, 'sanitize_release_state' ) );
		self::meta( 'hp_b2b_request', 'hp_supplier_pipeline_status', 'string', 'new', array( __CLASS__, 'sanitize_pipeline_state' ) );
		self::meta( 'hp_b2b_request', 'hp_supplier_acknowledged_utc', 'string', '', 'sanitize_text_field' );
		self::meta( 'hp_b2b_request', 'hp_request_audit', 'array', array(), array( __CLASS__, 'sanitize_audit_log' ) );

		self::meta( self::CATALOG_POST_TYPE, 'hp_supplier_id', 'integer', 0, 'absint' );
		self::meta( self::CATALOG_POST_TYPE, 'hp_submitting_user_id', 'integer', 0, 'absint' );
		self::meta( self::CATALOG_POST_TYPE, 'hp_submission_kind', 'string', 'new_product', array( __CLASS__, 'sanitize_submission_kind' ) );
		self::meta( self::CATALOG_POST_TYPE, 'hp_submission_status', 'string', 'pending', array( __CLASS__, 'sanitize_submission_status' ) );
		self::meta( self::CATALOG_POST_TYPE, 'hp_product_name', 'string', '', 'sanitize_text_field' );
		self::meta( self::CATALOG_POST_TYPE, 'hp_technology', 'string', '', 'sanitize_text_field' );
		self::meta( self::CATALOG_POST_TYPE, 'hp_product_family', 'string', '', 'sanitize_text_field' );
		self::meta( self::CATALOG_POST_TYPE, 'hp_source_url', 'string', '', 'esc_url_raw' );
		self::meta( self::CATALOG_POST_TYPE, 'hp_submission_notes', 'string', '', 'sanitize_textarea_field' );
		self::meta( self::CATALOG_POST_TYPE, 'hp_created_utc', 'string', '', 'sanitize_text_field' );
	}

	/**
	 * @param string          $post_type Post type.
	 * @param string          $key Meta key.
	 * @param string          $type Data type.
	 * @param mixed           $default Default value.
	 * @param callable|string $sanitizer Sanitizer.
	 * @return void
	 */
	private static function meta( $post_type, $key, $type, $default, $sanitizer ) {
		register_post_meta(
			$post_type,
			$key,
			array(
				'single'            => true,
				'type'              => $type,
				'default'           => $default,
				'sanitize_callback' => $sanitizer,
				'auth_callback'     => array( __CLASS__, 'can_edit_meta' ),
				'show_in_rest'      => false,
			)
		);
	}

	/**
	 * @param bool   $allowed Existing authorization.
	 * @param string $meta_key Meta key.
	 * @param int    $object_id Object ID.
	 * @param int    $user_id User ID.
	 * @return bool
	 */
	public static function can_edit_meta( $allowed, $meta_key, $object_id, $user_id ) {
		return user_can( (int) $user_id, 'edit_post', (int) $object_id );
	}

	/**
	 * Resolve the supplier owned by an account. A user can never select a
	 * supplier identifier in a public request.
	 *
	 * @param int $user_id User ID, defaults to the current account.
	 * @return WP_Post|null
	 */
	public static function supplier_for_user( $user_id = 0 ) {
		$user_id = $user_id ? absint( $user_id ) : get_current_user_id();
		if ( ! $user_id ) {
			return null;
		}

		$supplier_id = absint( get_user_meta( $user_id, self::USER_SUPPLIER_META, true ) );
		$supplier    = $supplier_id ? get_post( $supplier_id ) : null;
		if ( $supplier instanceof WP_Post && 'hp_supplier' === $supplier->post_type ) {
			$owners = self::sanitize_id_list( get_post_meta( $supplier_id, 'hp_account_user_ids', true ) );
			if ( in_array( $user_id, $owners, true ) ) {
				return $supplier;
			}
		}

		return null;
	}

	/**
	 * @param int $supplier_id Supplier ID.
	 * @param int $user_id User ID.
	 * @return bool
	 */
	public static function user_owns_supplier( $supplier_id, $user_id = 0 ) {
		$supplier = self::supplier_for_user( $user_id );
		return $supplier instanceof WP_Post && (int) $supplier->ID === absint( $supplier_id );
	}

	/**
	 * @param int $supplier_id Supplier ID.
	 * @return array<int, WP_Post>
	 */
	public static function assigned_requests( $supplier_id ) {
		return get_posts(
			array(
				'post_type'      => 'hp_b2b_request',
				'post_status'    => 'private',
				'posts_per_page' => 50,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'meta_key'       => 'hp_assigned_supplier_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Private bounded supplier inbox.
				'meta_value'     => (string) absint( $supplier_id ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Private bounded supplier inbox.
				'no_found_rows'  => true,
			)
		);
	}

	/**
	 * Render-safe view of an assigned request. Contact details remain absent
	 * until an administrator explicitly releases the lead.
	 *
	 * @param WP_Post $request Request post.
	 * @param int     $supplier_id Supplier ID.
	 * @return array<string, mixed>
	 */
	public static function request_view( $request, $supplier_id ) {
		if ( 'hp_b2b_request' !== $request->post_type ) {
			return array();
		}
		if ( absint( get_post_meta( (int) $request->ID, 'hp_assigned_supplier_id', true ) ) !== absint( $supplier_id ) ) {
			return array();
		}

		$released = 'released' === get_post_meta( (int) $request->ID, 'hp_lead_release_state', true );
		$view     = array(
			'id'         => (int) $request->ID,
			'type'       => sanitize_key( (string) get_post_meta( (int) $request->ID, 'hp_request_type', true ) ),
			'company'    => sanitize_text_field( (string) get_post_meta( (int) $request->ID, 'hp_company_name', true ) ),
			'city'       => sanitize_text_field( (string) get_post_meta( (int) $request->ID, 'hp_city', true ) ),
			'stage'      => sanitize_key( (string) get_post_meta( (int) $request->ID, 'hp_project_stage', true ) ),
			'categories' => Hea_Lth_Platform_Core::sanitize_string_list( get_post_meta( (int) $request->ID, 'hp_categories', true ) ),
			'created'    => sanitize_text_field( (string) get_post_meta( (int) $request->ID, 'hp_created_utc', true ) ),
			'status'     => self::sanitize_pipeline_state( get_post_meta( (int) $request->ID, 'hp_supplier_pipeline_status', true ) ),
			'released'   => $released,
		);

		if ( $released ) {
			$view['contact_name']  = sanitize_text_field( (string) get_post_meta( (int) $request->ID, 'hp_contact_name', true ) );
			$view['contact_phone'] = sanitize_text_field( (string) get_post_meta( (int) $request->ID, 'hp_contact_phone', true ) );
			$view['contact_email'] = sanitize_email( (string) get_post_meta( (int) $request->ID, 'hp_contact_email', true ) );
		}

		return $view;
	}

	/**
	 * @param int $supplier_id Supplier ID.
	 * @return array<int, WP_Post>
	 */
	public static function catalog_submissions( $supplier_id ) {
		return get_posts(
			array(
				'post_type'      => self::CATALOG_POST_TYPE,
				'post_status'    => 'private',
				'posts_per_page' => 30,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'meta_key'       => 'hp_supplier_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Private bounded supplier queue.
				'meta_value'     => (string) absint( $supplier_id ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Private bounded supplier queue.
				'no_found_rows'  => true,
			)
		);
	}

	/**
	 * @return array<string, string>
	 */
	public static function plans() {
		return self::$plans;
	}

	/**
	 * @return array<string, string>
	 */
	public static function membership_states() {
		return self::$membership_states;
	}

	/**
	 * @return array<string, string>
	 */
	public static function pipeline_states() {
		return self::$pipeline_states;
	}

	/**
	 * @param string $key State key.
	 * @return string
	 */
	public static function submission_status_label( $key ) {
		$labels = array(
			'pending'           => 'בבדיקה',
			'approved'          => 'אושר',
			'rejected'          => 'לא אושר',
			'changes_requested' => 'נדרשים פרטים נוספים',
		);
		$key = self::sanitize_submission_status( $key );
		return isset( $labels[ $key ] ) ? $labels[ $key ] : $labels['pending'];
	}

	/**
	 * @return void
	 */
	public static function handle_catalog_submission() {
		if ( ! is_user_logged_in() ) {
			auth_redirect();
		}
		check_admin_referer( self::CATALOG_ACTION, 'hea_lth_supplier_nonce' );
		$supplier = self::supplier_for_user();
		if ( ! $supplier instanceof WP_Post ) {
			wp_die( esc_html__( 'החשבון אינו מקושר לכרטיס ספק.', 'hea-lth-platform-core' ), '', array( 'response' => 403 ) );
		}

		$kind         = isset( $_POST['submission_kind'] ) ? self::sanitize_submission_kind( sanitize_key( wp_unslash( $_POST['submission_kind'] ) ) ) : 'new_product';
		$product_name = isset( $_POST['product_name'] ) ? sanitize_text_field( wp_unslash( $_POST['product_name'] ) ) : '';
		$technology   = isset( $_POST['technology'] ) ? sanitize_text_field( wp_unslash( $_POST['technology'] ) ) : '';
		$family       = isset( $_POST['product_family'] ) ? sanitize_text_field( wp_unslash( $_POST['product_family'] ) ) : '';
		$source_url   = isset( $_POST['source_url'] ) ? esc_url_raw( wp_unslash( $_POST['source_url'] ) ) : '';
		$notes        = isset( $_POST['submission_notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['submission_notes'] ) ) : '';
		$return_url   = self::portal_url();

		if ( '' === $product_name || '' === $notes ) {
			self::redirect( $return_url, 'catalog-required' );
		}

		$post_id = wp_insert_post(
			array(
				'post_type'   => self::CATALOG_POST_TYPE,
				'post_status' => 'private',
				'post_title'  => sprintf( '%s — %s', get_the_title( $supplier ), $product_name ),
			),
			true
		);
		if ( is_wp_error( $post_id ) ) {
			self::redirect( $return_url, 'catalog-error' );
		}

		$meta = array(
			'hp_supplier_id'        => (int) $supplier->ID,
			'hp_submitting_user_id' => get_current_user_id(),
			'hp_submission_kind'    => $kind,
			'hp_submission_status'  => 'pending',
			'hp_product_name'       => $product_name,
			'hp_technology'         => $technology,
			'hp_product_family'     => $family,
			'hp_source_url'         => $source_url,
			'hp_submission_notes'   => $notes,
			'hp_created_utc'        => gmdate( 'c' ),
		);
		foreach ( $meta as $key => $value ) {
			update_post_meta( (int) $post_id, $key, $value );
		}

		self::redirect( $return_url, 'catalog-received' );
	}

	/**
	 * @return void
	 */
	public static function handle_pipeline_update() {
		if ( ! is_user_logged_in() ) {
			auth_redirect();
		}
		check_admin_referer( self::PIPELINE_ACTION, 'hea_lth_pipeline_nonce' );
		$supplier  = self::supplier_for_user();
		$request_id = isset( $_POST['request_id'] ) ? absint( wp_unslash( $_POST['request_id'] ) ) : 0;
		$status     = isset( $_POST['pipeline_status'] ) ? self::sanitize_pipeline_state( sanitize_key( wp_unslash( $_POST['pipeline_status'] ) ) ) : 'new';
		$request    = $request_id ? get_post( $request_id ) : null;

		if ( ! $supplier instanceof WP_Post || ! $request instanceof WP_Post || 'hp_b2b_request' !== $request->post_type ) {
			wp_die( esc_html__( 'הפנייה אינה זמינה לחשבון זה.', 'hea-lth-platform-core' ), '', array( 'response' => 403 ) );
		}
		if ( absint( get_post_meta( $request_id, 'hp_assigned_supplier_id', true ) ) !== (int) $supplier->ID ) {
			wp_die( esc_html__( 'הפנייה אינה זמינה לחשבון זה.', 'hea-lth-platform-core' ), '', array( 'response' => 403 ) );
		}

		$previous = self::sanitize_pipeline_state( get_post_meta( $request_id, 'hp_supplier_pipeline_status', true ) );
		update_post_meta( $request_id, 'hp_supplier_pipeline_status', $status );
		if ( '' === (string) get_post_meta( $request_id, 'hp_supplier_acknowledged_utc', true ) ) {
			update_post_meta( $request_id, 'hp_supplier_acknowledged_utc', gmdate( 'c' ) );
		}
		self::append_audit( $request_id, 'supplier_status', $previous, $status, get_current_user_id() );
		self::redirect( self::portal_url(), 'pipeline-updated' );
	}

	/**
	 * @return void
	 */
	public static function register_admin_boxes() {
		add_meta_box( 'hea-lth-supplier-account', 'חשבון ומסלול', array( __CLASS__, 'render_supplier_admin' ), 'hp_supplier', 'side', 'high' );
		add_meta_box( 'hea-lth-request-routing', 'הקצאה לספק', array( __CLASS__, 'render_request_admin' ), 'hp_b2b_request', 'side', 'high' );
		add_meta_box( 'hea-lth-catalog-details', 'פרטי הבקשה', array( __CLASS__, 'render_catalog_admin' ), self::CATALOG_POST_TYPE, 'normal', 'high' );
	}

	/**
	 * @param WP_Post $post Supplier post.
	 * @return void
	 */
	public static function render_supplier_admin( $post ) {
		wp_nonce_field( 'hea_lth_supplier_admin', 'hea_lth_supplier_admin_nonce' );
		$owners = implode( ', ', self::sanitize_id_list( get_post_meta( (int) $post->ID, 'hp_account_user_ids', true ) ) );
		$plan   = self::sanitize_plan( get_post_meta( (int) $post->ID, 'hp_membership_plan', true ) );
		$state  = self::sanitize_membership_state( get_post_meta( (int) $post->ID, 'hp_membership_state', true ) );
		echo '<p><label for="hp-owner-users"><strong>מזהי משתמשים מורשים</strong></label><input class="widefat" id="hp-owner-users" name="hp_account_user_ids" value="' . esc_attr( $owners ) . '" /><span class="description">מספרי משתמשים, מופרדים בפסיקים.</span></p>';
		echo '<p><label for="hp-member-plan"><strong>מסלול</strong></label><select class="widefat" id="hp-member-plan" name="hp_membership_plan">';
		foreach ( self::$plans as $key => $label ) {
			echo '<option value="' . esc_attr( $key ) . '" ' . selected( $plan, $key, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select></p><p><label for="hp-member-state"><strong>מצב חברות</strong></label><select class="widefat" id="hp-member-state" name="hp_membership_state">';
		foreach ( self::$membership_states as $key => $label ) {
			echo '<option value="' . esc_attr( $key ) . '" ' . selected( $state, $key, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select></p>';
	}

	/**
	 * @param WP_Post $post Request post.
	 * @return void
	 */
	public static function render_request_admin( $post ) {
		wp_nonce_field( 'hea_lth_request_admin', 'hea_lth_request_admin_nonce' );
		$assigned = absint( get_post_meta( (int) $post->ID, 'hp_assigned_supplier_id', true ) );
		$release  = self::sanitize_release_state( get_post_meta( (int) $post->ID, 'hp_lead_release_state', true ) );
		$status   = self::sanitize_pipeline_state( get_post_meta( (int) $post->ID, 'hp_supplier_pipeline_status', true ) );
		$suppliers = get_posts( array( 'post_type' => 'hp_supplier', 'post_status' => array( 'publish', 'private', 'draft', 'pending' ), 'posts_per_page' => 200, 'orderby' => 'title', 'order' => 'ASC' ) );
		echo '<p><label for="hp-assigned-supplier"><strong>ספק מוקצה</strong></label><select class="widefat" id="hp-assigned-supplier" name="hp_assigned_supplier_id"><option value="0">ללא הקצאה</option>';
		foreach ( $suppliers as $supplier ) {
			echo '<option value="' . (int) $supplier->ID . '" ' . selected( $assigned, (int) $supplier->ID, false ) . '>' . esc_html( get_the_title( $supplier ) ) . '</option>';
		}
		echo '</select></p><p><label for="hp-release-state"><strong>חשיפת פרטי קשר</strong></label><select class="widefat" id="hp-release-state" name="hp_lead_release_state">';
		foreach ( array( 'held' => 'שמורים ב-Hea-lth', 'released' => 'שוחררו לספק', 'revoked' => 'הגישה בוטלה' ) as $key => $label ) {
			echo '<option value="' . esc_attr( $key ) . '" ' . selected( $release, $key, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select></p><p><label for="hp-pipeline-state"><strong>מצב טיפול</strong></label><select class="widefat" id="hp-pipeline-state" name="hp_supplier_pipeline_status">';
		foreach ( self::$pipeline_states as $key => $label ) {
			echo '<option value="' . esc_attr( $key ) . '" ' . selected( $status, $key, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select></p>';
	}

	/**
	 * @param WP_Post $post Submission post.
	 * @return void
	 */
	public static function render_catalog_admin( $post ) {
		wp_nonce_field( 'hea_lth_catalog_admin', 'hea_lth_catalog_admin_nonce' );
		$status = self::sanitize_submission_status( get_post_meta( (int) $post->ID, 'hp_submission_status', true ) );
		$fields = array(
			'הספק'        => get_the_title( absint( get_post_meta( (int) $post->ID, 'hp_supplier_id', true ) ) ),
			'המוצר'       => get_post_meta( (int) $post->ID, 'hp_product_name', true ),
			'הטכנולוגיה'  => get_post_meta( (int) $post->ID, 'hp_technology', true ),
			'משפחת המוצר' => get_post_meta( (int) $post->ID, 'hp_product_family', true ),
			'הערות'       => get_post_meta( (int) $post->ID, 'hp_submission_notes', true ),
			'קישור מקור'  => get_post_meta( (int) $post->ID, 'hp_source_url', true ),
		);
		echo '<table class="widefat striped"><tbody>';
		foreach ( $fields as $label => $value ) {
			echo '<tr><th>' . esc_html( $label ) . '</th><td>' . esc_html( (string) $value ) . '</td></tr>';
		}
		echo '</tbody></table>';
		echo '<p><label for="hp-submission-status"><strong>מצב הבקשה</strong></label><select class="widefat" id="hp-submission-status" name="hp_submission_status">';
		foreach ( array( 'pending', 'approved', 'changes_requested', 'rejected' ) as $key ) {
			echo '<option value="' . esc_attr( $key ) . '" ' . selected( $status, $key, false ) . '>' . esc_html( self::submission_status_label( $key ) ) . '</option>';
		}
		echo '</select></p>';
	}

	/**
	 * @param int     $post_id Submission ID.
	 * @param WP_Post $post Submission post.
	 * @return void
	 */
	public static function save_catalog_admin( $post_id, $post ) {
		if ( ! self::admin_save_allowed( $post_id, 'hea_lth_catalog_admin_nonce', 'hea_lth_catalog_admin' ) || self::CATALOG_POST_TYPE !== $post->post_type ) {
			return;
		}
		$status = isset( $_POST['hp_submission_status'] ) ? self::sanitize_submission_status( sanitize_key( wp_unslash( $_POST['hp_submission_status'] ) ) ) : 'pending'; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified by admin_save_allowed above.
		update_post_meta( $post_id, 'hp_submission_status', $status );
	}

	/**
	 * @param int     $post_id Supplier ID.
	 * @param WP_Post $post Supplier post.
	 * @return void
	 */
	public static function save_supplier_admin( $post_id, $post ) {
		if ( ! self::admin_save_allowed( $post_id, 'hea_lth_supplier_admin_nonce', 'hea_lth_supplier_admin' ) || 'hp_supplier' !== $post->post_type ) {
			return;
		}
		$previous    = self::sanitize_id_list( get_post_meta( $post_id, 'hp_account_user_ids', true ) );
		$owners_raw = isset( $_POST['hp_account_user_ids'] ) ? sanitize_text_field( wp_unslash( $_POST['hp_account_user_ids'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified by admin_save_allowed above.
		$owners     = self::sanitize_id_list( preg_split( '/\s*,\s*/', $owners_raw ) );
		$plan       = isset( $_POST['hp_membership_plan'] ) ? self::sanitize_plan( sanitize_key( wp_unslash( $_POST['hp_membership_plan'] ) ) ) : 'verified'; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified by admin_save_allowed above.
		$state      = isset( $_POST['hp_membership_state'] ) ? self::sanitize_membership_state( sanitize_key( wp_unslash( $_POST['hp_membership_state'] ) ) ) : 'pending'; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified by admin_save_allowed above.
		update_post_meta( $post_id, 'hp_account_user_ids', $owners );
		update_post_meta( $post_id, 'hp_membership_plan', $plan );
		update_post_meta( $post_id, 'hp_membership_state', $state );

		foreach ( array_diff( $previous, $owners ) as $removed_user_id ) {
			if ( $post_id === absint( get_user_meta( $removed_user_id, self::USER_SUPPLIER_META, true ) ) ) {
				delete_user_meta( $removed_user_id, self::USER_SUPPLIER_META );
			}
		}
		foreach ( $owners as $owner_user_id ) {
			$user = get_user_by( 'id', $owner_user_id );
			if ( ! $user instanceof WP_User ) {
				continue;
			}
			$previous_supplier_id = absint( get_user_meta( $owner_user_id, self::USER_SUPPLIER_META, true ) );
			if ( $previous_supplier_id && $previous_supplier_id !== $post_id && 'hp_supplier' === get_post_type( $previous_supplier_id ) ) {
				$previous_owners = self::sanitize_id_list( get_post_meta( $previous_supplier_id, 'hp_account_user_ids', true ) );
				update_post_meta( $previous_supplier_id, 'hp_account_user_ids', array_values( array_diff( $previous_owners, array( $owner_user_id ) ) ) );
			}
			update_user_meta( $owner_user_id, self::USER_SUPPLIER_META, $post_id );
			$user->add_role( self::ROLE );
		}
	}

	/**
	 * @param int     $post_id Request ID.
	 * @param WP_Post $post Request post.
	 * @return void
	 */
	public static function save_request_admin( $post_id, $post ) {
		if ( ! self::admin_save_allowed( $post_id, 'hea_lth_request_admin_nonce', 'hea_lth_request_admin' ) || 'hp_b2b_request' !== $post->post_type ) {
			return;
		}
		$old_supplier = absint( get_post_meta( $post_id, 'hp_assigned_supplier_id', true ) );
		$old_release  = self::sanitize_release_state( get_post_meta( $post_id, 'hp_lead_release_state', true ) );
		$new_supplier = isset( $_POST['hp_assigned_supplier_id'] ) ? absint( wp_unslash( $_POST['hp_assigned_supplier_id'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified by admin_save_allowed above.
		$new_release  = isset( $_POST['hp_lead_release_state'] ) ? self::sanitize_release_state( sanitize_key( wp_unslash( $_POST['hp_lead_release_state'] ) ) ) : 'held'; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified by admin_save_allowed above.
		$new_status   = isset( $_POST['hp_supplier_pipeline_status'] ) ? self::sanitize_pipeline_state( sanitize_key( wp_unslash( $_POST['hp_supplier_pipeline_status'] ) ) ) : 'new'; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified by admin_save_allowed above.

		if ( $new_supplier && 'hp_supplier' !== get_post_type( $new_supplier ) ) {
			$new_supplier = 0;
			$new_release  = 'held';
		}
		if ( ! $new_supplier ) {
			$new_release = 'held';
		}
		update_post_meta( $post_id, 'hp_assigned_supplier_id', $new_supplier );
		update_post_meta( $post_id, 'hp_lead_release_state', $new_release );
		update_post_meta( $post_id, 'hp_supplier_pipeline_status', $new_status );
		if ( $old_supplier !== $new_supplier ) {
			self::append_audit( $post_id, 'assignment', (string) $old_supplier, (string) $new_supplier, get_current_user_id() );
		}
		if ( $old_release !== $new_release ) {
			self::append_audit( $post_id, 'release', $old_release, $new_release, get_current_user_id() );
		}
	}

	/**
	 * @param int    $post_id Post ID.
	 * @param string $nonce_field Nonce field.
	 * @param string $action Nonce action.
	 * @return bool
	 */
	private static function admin_save_allowed( $post_id, $nonce_field, $action ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}
		if ( ! isset( $_POST[ $nonce_field ] ) ) {
			return false;
		}
		$nonce = sanitize_text_field( wp_unslash( $_POST[ $nonce_field ] ) );
		return wp_verify_nonce( $nonce, $action ) && current_user_can( 'edit_post', $post_id );
	}

	/**
	 * @param int    $request_id Request ID.
	 * @param string $event Event.
	 * @param string $from Previous state.
	 * @param string $to New state.
	 * @param int    $user_id Acting user.
	 * @return void
	 */
	private static function append_audit( $request_id, $event, $from, $to, $user_id ) {
		$log   = self::sanitize_audit_log( get_post_meta( $request_id, 'hp_request_audit', true ) );
		$log[] = array(
			'at'      => gmdate( 'c' ),
			'event'   => sanitize_key( $event ),
			'from'    => sanitize_text_field( $from ),
			'to'      => sanitize_text_field( $to ),
			'user_id' => absint( $user_id ),
		);
		update_post_meta( $request_id, 'hp_request_audit', array_slice( $log, -100 ) );
	}

	/**
	 * @return string
	 */
	private static function portal_url() {
		return home_url( '/professionals/supplier-portal/' );
	}

	/**
	 * @param string $url Return URL.
	 * @param string $status Status key.
	 * @return void
	 */
	private static function redirect( $url, $status ) {
		wp_safe_redirect( add_query_arg( 'portal', sanitize_key( $status ), $url ) );
		exit;
	}

	/** @param mixed $value Raw value. @return array<int, int> */
	public static function sanitize_id_list( $value ) {
		$value = is_array( $value ) ? $value : array();
		return array_values( array_unique( array_filter( array_map( 'absint', array_slice( $value, 0, 20 ) ) ) ) );
	}

	/** @param mixed $value Raw value. @return string */
	public static function sanitize_plan( $value ) {
		$value = sanitize_key( (string) $value );
		return isset( self::$plans[ $value ] ) ? $value : 'verified';
	}

	/** @param mixed $value Raw value. @return string */
	public static function sanitize_membership_state( $value ) {
		$value = sanitize_key( (string) $value );
		return isset( self::$membership_states[ $value ] ) ? $value : 'pending';
	}

	/** @param mixed $value Raw value. @return string */
	public static function sanitize_release_state( $value ) {
		$value = sanitize_key( (string) $value );
		return in_array( $value, array( 'held', 'released', 'revoked' ), true ) ? $value : 'held';
	}

	/** @param mixed $value Raw value. @return string */
	public static function sanitize_pipeline_state( $value ) {
		$value = sanitize_key( (string) $value );
		return isset( self::$pipeline_states[ $value ] ) ? $value : 'new';
	}

	/** @param mixed $value Raw value. @return string */
	public static function sanitize_submission_kind( $value ) {
		$value = sanitize_key( (string) $value );
		return in_array( $value, array( 'company_update', 'new_product', 'product_update' ), true ) ? $value : 'new_product';
	}

	/** @param mixed $value Raw value. @return string */
	public static function sanitize_submission_status( $value ) {
		$value = sanitize_key( (string) $value );
		return in_array( $value, array( 'pending', 'approved', 'rejected', 'changes_requested' ), true ) ? $value : 'pending';
	}

	/** @param mixed $value Raw value. @return array<int, array<string, mixed>> */
	public static function sanitize_audit_log( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}
		$clean = array();
		foreach ( array_slice( $value, -100 ) as $entry ) {
			if ( ! is_array( $entry ) ) {
				continue;
			}
			$clean[] = array(
				'at'      => sanitize_text_field( isset( $entry['at'] ) ? (string) $entry['at'] : '' ),
				'event'   => sanitize_key( isset( $entry['event'] ) ? (string) $entry['event'] : '' ),
				'from'    => sanitize_text_field( isset( $entry['from'] ) ? (string) $entry['from'] : '' ),
				'to'      => sanitize_text_field( isset( $entry['to'] ) ? (string) $entry['to'] : '' ),
				'user_id' => absint( isset( $entry['user_id'] ) ? $entry['user_id'] : 0 ),
			);
		}
		return $clean;
	}
}
