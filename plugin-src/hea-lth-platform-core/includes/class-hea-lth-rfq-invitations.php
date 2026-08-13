<?php
/**
 * Private, anonymized RFQ invitations and single-supplier award workflow.
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Hea_Lth_RFQ_Invitations {
	const POST_TYPE       = 'hp_rfq_invitation';
	const RESPONSE_ACTION = 'hea_lth_rfq_response';
	const RETRY_HOOK      = 'hea_lth_retry_rfq_invitation';
	const MAX_ATTEMPTS    = 3;

	/** @var array<string, string> */
	private static $statuses = array(
		'invited'    => 'הוזמן לעיין',
		'interested' => 'מעוניין',
		'declined'   => 'לא רלוונטי',
		'awarded'    => 'נבחר להמשך',
		'withdrawn'  => 'ההזמנה נסגרה',
	);

	/** @return void */
	public static function boot() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ), 11 );
		add_action( 'init', array( __CLASS__, 'register_metadata' ), 23 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'register_admin_box' ) );
		add_action( 'save_post_hp_b2b_request', array( __CLASS__, 'save_request_admin' ), 30, 2 );
		add_action( 'admin_post_' . self::RESPONSE_ACTION, array( __CLASS__, 'handle_supplier_response' ) );
		add_action( self::RETRY_HOOK, array( __CLASS__, 'retry_delivery' ) );
	}

	/** @return void */
	public static function register_post_type() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'              => array(
					'name'          => 'הזמנות RFQ',
					'singular_name' => 'הזמנת RFQ',
					'edit_item'     => 'בדיקת הזמנת RFQ',
				),
				'description'         => 'רשומות פרטיות של הזמנת ספק לעיין בהזדמנות רכש אנונימית.',
				'public'              => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'show_ui'             => true,
				'show_in_menu'        => 'edit.php?post_type=hp_b2b_request',
				'show_in_rest'        => false,
				'supports'            => array( 'title' ),
				'map_meta_cap'        => true,
			)
		);
	}

	/** @return void */
	public static function register_metadata() {
		foreach ( array( 'hp_rfq_status', 'hp_rfq_created_utc', 'hp_rfq_responded_utc', 'hp_rfq_awarded_utc' ) as $key ) {
			self::meta( $key, 'string', '', 'sanitize_text_field' );
		}
		foreach ( array( 'hp_rfq_request_id', 'hp_rfq_supplier_id', 'hp_rfq_created_by', 'hp_rfq_responded_by', 'hp_rfq_delivery_attempts' ) as $key ) {
			self::meta( $key, 'integer', 0, 'absint' );
		}
		self::meta( 'hp_rfq_delivery_log', 'array', array(), array( __CLASS__, 'sanitize_delivery_log' ) );
		self::meta( 'hp_rfq_audit', 'array', array(), array( __CLASS__, 'sanitize_audit' ) );
	}

	/**
	 * @param string          $key Meta key.
	 * @param string          $type Meta type.
	 * @param mixed           $default Default value.
	 * @param callable|string $sanitizer Sanitizer.
	 * @return void
	 */
	private static function meta( $key, $type, $default, $sanitizer ) {
		register_post_meta(
			self::POST_TYPE,
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

	/** @return bool */
	public static function can_edit_meta( $allowed, $meta_key, $object_id, $user_id ) {
		unset( $allowed, $meta_key );
		return user_can( (int) $user_id, 'edit_post', (int) $object_id );
	}

	/** @return array<string, string> */
	public static function statuses() {
		return self::$statuses;
	}

	/** @return string */
	public static function status_label( $status ) {
		$status = self::sanitize_status( $status );
		return self::$statuses[ $status ];
	}

	/** @return string */
	public static function sanitize_status( $status ) {
		$status = sanitize_key( (string) $status );
		return isset( self::$statuses[ $status ] ) ? $status : 'invited';
	}

	/** @return void */
	public static function register_admin_box() {
		add_meta_box( 'hea-lth-rfq-invitations', 'הזמנות ספקים להצעת רכש', array( __CLASS__, 'render_admin_box' ), 'hp_b2b_request', 'normal', 'high' );
	}

	/** @param WP_Post $post Request. @return void */
	public static function render_admin_box( $post ) {
		$request_id   = (int) $post->ID;
		$candidate_ids = Hea_Lth_Platform_Core::sanitize_id_list( get_post_meta( $request_id, 'hp_candidate_supplier_ids', true ) );
		$suppliers    = get_posts(
			array(
				'post_type'      => 'hp_supplier',
				'post_status'    => 'publish',
				'posts_per_page' => 200,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'meta_key'       => 'hp_public_state', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Small verified supplier directory.
				'meta_value'     => 'verified', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Admin shortlist.
				'no_found_rows'  => true,
			)
		);
		$invitations  = self::invitations_for_request( $request_id );

		wp_nonce_field( 'hea_lth_rfq_admin', 'hea_lth_rfq_admin_nonce' );
		echo '<p>בחרו ספקים לקבלת תקציר אנונימי בלבד. פרטי הקשר ותנאי התיווך אינם נכללים בהזמנה.</p>';
		echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:8px">';
		foreach ( $suppliers as $supplier ) {
			$is_candidate = in_array( (int) $supplier->ID, $candidate_ids, true );
			echo '<label style="border:1px solid #dcdcde;padding:10px"><input type="checkbox" name="hp_rfq_invite_supplier_ids[]" value="' . (int) $supplier->ID . '"> <strong>' . esc_html( get_the_title( $supplier ) ) . '</strong>' . ( $is_candidate ? ' <span style="color:#08786d">· התאמה מהקטלוג</span>' : '' ) . '</label>';
		}
		echo '</div><p><label><input type="checkbox" name="hp_send_rfq_invitations" value="1"> <strong>יצירת ההזמנות ושליחתן לספקים שנבחרו</strong></label></p>';

		if ( $invitations ) {
			echo '<table class="widefat striped"><thead><tr><th>ספק</th><th>מצב</th><th>מסירה</th><th>בחירה להמשך</th></tr></thead><tbody>';
			foreach ( $invitations as $invitation ) {
				$invitation_id = (int) $invitation->ID;
				$supplier_id   = absint( get_post_meta( $invitation_id, 'hp_rfq_supplier_id', true ) );
				$status        = self::sanitize_status( get_post_meta( $invitation_id, 'hp_rfq_status', true ) );
				$delivered     = self::is_delivered( $invitation_id );
				echo '<tr><td>' . esc_html( get_the_title( $supplier_id ) ) . '</td><td>' . esc_html( self::status_label( $status ) ) . '</td><td>' . esc_html( $delivered ? 'נשלח' : 'ממתין' ) . '</td><td>';
				if ( 'interested' === $status ) {
					echo '<label><input type="radio" name="hp_rfq_award_supplier_id" value="' . (int) $supplier_id . '"> בחירת הספק</label>';
				} elseif ( 'awarded' === $status ) {
					echo '<strong>נבחר</strong>';
				} else {
					echo '—';
				}
				echo '</td></tr>';
			}
			echo '</tbody></table><p><label><input type="checkbox" name="hp_award_rfq_supplier" value="1"> <strong>אישור בחירת הספק המסומן והעברתו למסלול העסקה</strong></label></p>';
		}
	}

	/** @param int $post_id Request ID. @param WP_Post $post Request. @return void */
	public static function save_request_admin( $post_id, $post ) {
		if ( 'hp_b2b_request' !== $post->post_type || ! self::admin_save_allowed( $post_id ) ) {
			return;
		}

		$send = isset( $_POST['hp_send_rfq_invitations'] ) && 1 === absint( wp_unslash( $_POST['hp_send_rfq_invitations'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified above.
		if ( $send ) {
			$raw_ids      = isset( $_POST['hp_rfq_invite_supplier_ids'] ) && is_array( $_POST['hp_rfq_invite_supplier_ids'] ) ? array_map( 'absint', wp_unslash( $_POST['hp_rfq_invite_supplier_ids'] ) ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified above.
			$supplier_ids = Hea_Lth_Platform_Core::sanitize_id_list( $raw_ids );
			foreach ( $supplier_ids as $supplier_id ) {
				if ( 'hp_supplier' === get_post_type( $supplier_id ) && 'publish' === get_post_status( $supplier_id ) && 'verified' === get_post_meta( $supplier_id, 'hp_public_state', true ) ) {
					self::create_invitation( $post_id, $supplier_id, get_current_user_id() );
				}
			}
		}

		$award = isset( $_POST['hp_award_rfq_supplier'] ) && 1 === absint( wp_unslash( $_POST['hp_award_rfq_supplier'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified above.
		$supplier_id = isset( $_POST['hp_rfq_award_supplier_id'] ) ? absint( wp_unslash( $_POST['hp_rfq_award_supplier_id'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified above.
		if ( $award && $supplier_id > 0 ) {
			self::award_supplier( $post_id, $supplier_id, get_current_user_id() );
		}
	}

	/** @return bool */
	private static function admin_save_allowed( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}
		if ( ! isset( $_POST['hea_lth_rfq_admin_nonce'] ) ) {
			return false;
		}
		$nonce = sanitize_text_field( wp_unslash( $_POST['hea_lth_rfq_admin_nonce'] ) );
		return wp_verify_nonce( $nonce, 'hea_lth_rfq_admin' ) && current_user_can( 'edit_post', $post_id );
	}

	/** @return int */
	public static function create_invitation( $request_id, $supplier_id, $user_id ) {
		$request  = get_post( $request_id );
		$supplier = get_post( $supplier_id );
		if ( ! $request instanceof WP_Post || 'hp_b2b_request' !== $request->post_type || ! $supplier instanceof WP_Post || 'hp_supplier' !== $supplier->post_type ) {
			return 0;
		}
		if ( class_exists( 'Hea_Lth_Brokerage_Ledger' ) && Hea_Lth_Brokerage_Ledger::is_financially_locked( $request_id ) ) {
			return 0;
		}
		foreach ( self::invitations_for_request( $request_id ) as $request_invitation ) {
			if ( 'awarded' === self::sanitize_status( get_post_meta( (int) $request_invitation->ID, 'hp_rfq_status', true ) ) ) {
				return 0;
			}
		}

		$existing = self::find_invitation( $request_id, $supplier_id );
		if ( $existing ) {
			$status = self::sanitize_status( get_post_meta( $existing, 'hp_rfq_status', true ) );
			if ( in_array( $status, array( 'declined', 'withdrawn' ), true ) ) {
				wp_clear_scheduled_hook( self::RETRY_HOOK, array( $existing ) );
				update_post_meta( $existing, 'hp_rfq_status', 'invited' );
				update_post_meta( $existing, 'hp_rfq_responded_utc', '' );
				update_post_meta( $existing, 'hp_rfq_responded_by', 0 );
				update_post_meta( $existing, 'hp_rfq_delivery_attempts', 0 );
				update_post_meta( $existing, 'hp_rfq_delivery_log', array() );
				self::append_audit( $existing, 'reinvited', $status, 'invited', $user_id );
				self::deliver( $existing );
			}
			return $existing;
		}

		$invitation_id = wp_insert_post(
			array(
				'post_type'   => self::POST_TYPE,
				'post_status' => 'private',
				'post_title'  => sprintf( 'RFQ #%d · %s', $request_id, get_the_title( $supplier_id ) ),
			),
			true
		);
		if ( is_wp_error( $invitation_id ) ) {
			return 0;
		}

		update_post_meta( (int) $invitation_id, 'hp_rfq_request_id', $request_id );
		update_post_meta( (int) $invitation_id, 'hp_rfq_supplier_id', $supplier_id );
		update_post_meta( (int) $invitation_id, 'hp_rfq_status', 'invited' );
		update_post_meta( (int) $invitation_id, 'hp_rfq_created_utc', gmdate( 'c' ) );
		update_post_meta( (int) $invitation_id, 'hp_rfq_created_by', $user_id );
		self::append_audit( (int) $invitation_id, 'created', '', 'invited', $user_id );
		self::deliver( (int) $invitation_id );
		return (int) $invitation_id;
	}

	/** @return int */
	private static function find_invitation( $request_id, $supplier_id ) {
		$posts = get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'private',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Private pair uniqueness lookup.
					'relation' => 'AND',
					array( 'key' => 'hp_rfq_request_id', 'value' => (string) absint( $request_id ) ),
					array( 'key' => 'hp_rfq_supplier_id', 'value' => (string) absint( $supplier_id ) ),
				),
				'no_found_rows'  => true,
			)
		);
		return $posts ? (int) $posts[0] : 0;
	}

	/** @return array<int, WP_Post> */
	public static function invitations_for_request( $request_id ) {
		return get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'private',
				'posts_per_page' => 100,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'meta_key'       => 'hp_rfq_request_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Bounded private request invitations.
				'meta_value'     => (string) absint( $request_id ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Bounded private request invitations.
				'no_found_rows'  => true,
			)
		);
	}

	/** @return array<int, WP_Post> */
	public static function invitations_for_supplier( $supplier_id ) {
		return get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'private',
				'posts_per_page' => 50,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'meta_key'       => 'hp_rfq_supplier_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Bounded supplier invitation inbox.
				'meta_value'     => (string) absint( $supplier_id ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Bounded supplier invitation inbox.
				'no_found_rows'  => true,
			)
		);
	}

	/** @return array<string, mixed> */
	public static function invitation_view( $invitation, $supplier_id ) {
		if ( ! $invitation instanceof WP_Post || self::POST_TYPE !== $invitation->post_type || absint( get_post_meta( (int) $invitation->ID, 'hp_rfq_supplier_id', true ) ) !== absint( $supplier_id ) ) {
			return array();
		}
		$request_id = absint( get_post_meta( (int) $invitation->ID, 'hp_rfq_request_id', true ) );
		$request    = get_post( $request_id );
		if ( ! $request instanceof WP_Post || 'hp_b2b_request' !== $request->post_type ) {
			return array();
		}
		return array(
			'id'         => (int) $invitation->ID,
			'request_id' => $request_id,
			'status'     => self::sanitize_status( get_post_meta( (int) $invitation->ID, 'hp_rfq_status', true ) ),
			'buyer'      => 'מרפאה או גורם מקצועי מאומת בישראל',
			'stage'      => sanitize_key( (string) get_post_meta( $request_id, 'hp_project_stage', true ) ),
			'equipment'  => Hea_Lth_Platform_Core::sanitize_string_list( get_post_meta( $request_id, 'hp_equipment_names', true ) ),
			'categories' => Hea_Lth_Platform_Core::sanitize_string_list( get_post_meta( $request_id, 'hp_categories', true ) ),
			'created'    => sanitize_text_field( (string) get_post_meta( (int) $invitation->ID, 'hp_rfq_created_utc', true ) ),
		);
	}

	/** @return void */
	public static function handle_supplier_response() {
		if ( ! is_user_logged_in() ) {
			auth_redirect();
		}
		check_admin_referer( self::RESPONSE_ACTION, 'hea_lth_rfq_nonce' );
		$invitation_id = isset( $_POST['invitation_id'] ) ? absint( wp_unslash( $_POST['invitation_id'] ) ) : 0;
		$response      = isset( $_POST['rfq_response'] ) ? sanitize_key( wp_unslash( $_POST['rfq_response'] ) ) : '';
		$supplier      = Hea_Lth_Supplier_Portal::supplier_for_user();
		$invitation    = $invitation_id ? get_post( $invitation_id ) : null;

		if ( ! $supplier instanceof WP_Post || ! $invitation instanceof WP_Post || self::POST_TYPE !== $invitation->post_type || absint( get_post_meta( $invitation_id, 'hp_rfq_supplier_id', true ) ) !== (int) $supplier->ID || ! in_array( $response, array( 'interested', 'declined' ), true ) ) {
			wp_die( esc_html__( 'לא ניתן לעדכן הזמנה זו מהחשבון הנוכחי.', 'hea-lth-platform-core' ), '', array( 'response' => 403 ) );
		}
		$previous = self::sanitize_status( get_post_meta( $invitation_id, 'hp_rfq_status', true ) );
		if ( ! in_array( $previous, array( 'invited', 'interested' ), true ) ) {
			wp_die( esc_html__( 'ההזמנה אינה פתוחה לעדכון.', 'hea-lth-platform-core' ), '', array( 'response' => 409 ) );
		}

		update_post_meta( $invitation_id, 'hp_rfq_status', $response );
		update_post_meta( $invitation_id, 'hp_rfq_responded_utc', gmdate( 'c' ) );
		update_post_meta( $invitation_id, 'hp_rfq_responded_by', get_current_user_id() );
		self::append_audit( $invitation_id, 'supplier_response', $previous, $response, get_current_user_id() );
		self::notify_owner_response( $invitation_id, $response );
		wp_safe_redirect( add_query_arg( 'portal', 'rfq-updated', home_url( '/professionals/supplier-portal/' ) ) );
		exit;
	}

	/** @return bool */
	public static function award_supplier( $request_id, $supplier_id, $user_id ) {
		if ( class_exists( 'Hea_Lth_Brokerage_Ledger' ) && Hea_Lth_Brokerage_Ledger::is_financially_locked( $request_id ) ) {
			return false;
		}
		$selected = self::find_invitation( $request_id, $supplier_id );
		if ( ! $selected || 'interested' !== self::sanitize_status( get_post_meta( $selected, 'hp_rfq_status', true ) ) ) {
			return false;
		}

		$previous_supplier = absint( get_post_meta( $request_id, 'hp_assigned_supplier_id', true ) );
		update_post_meta( $request_id, 'hp_assigned_supplier_id', $supplier_id );
		update_post_meta( $request_id, 'hp_lead_release_state', 'held' );
		update_post_meta( $request_id, 'hp_supplier_pipeline_status', 'new' );
		if ( class_exists( 'Hea_Lth_Brokerage_Ledger' ) && $previous_supplier !== $supplier_id ) {
			Hea_Lth_Brokerage_Ledger::invalidate_terms( $request_id );
		}
		foreach ( self::invitations_for_request( $request_id ) as $invitation ) {
			$invitation_id = (int) $invitation->ID;
			$old_status    = self::sanitize_status( get_post_meta( $invitation_id, 'hp_rfq_status', true ) );
			$new_status    = $invitation_id === $selected ? 'awarded' : 'withdrawn';
			update_post_meta( $invitation_id, 'hp_rfq_status', $new_status );
			if ( 'awarded' === $new_status ) {
				update_post_meta( $invitation_id, 'hp_rfq_awarded_utc', gmdate( 'c' ) );
			}
			self::append_audit( $invitation_id, 'owner_award', $old_status, $new_status, $user_id );
			wp_clear_scheduled_hook( self::RETRY_HOOK, array( $invitation_id ) );
		}
		return true;
	}

	/** @return void */
	private static function deliver( $invitation_id ) {
		$attempts = absint( get_post_meta( $invitation_id, 'hp_rfq_delivery_attempts', true ) ) + 1;
		update_post_meta( $invitation_id, 'hp_rfq_delivery_attempts', $attempts );
		$supplier_id = absint( get_post_meta( $invitation_id, 'hp_rfq_supplier_id', true ) );
		$request_id  = absint( get_post_meta( $invitation_id, 'hp_rfq_request_id', true ) );
		$recipients  = self::supplier_recipients( $supplier_id );
		$log         = self::sanitize_delivery_log( get_post_meta( $invitation_id, 'hp_rfq_delivery_log', true ) );
		$delivered   = array();
		foreach ( $log as $entry ) {
			if ( 'sent' === $entry['status'] ) {
				$delivered[] = $entry['recipient_hash'];
			}
		}

		$equipment = Hea_Lth_Platform_Core::sanitize_string_list( get_post_meta( $request_id, 'hp_equipment_names', true ) );
		$body      = implode(
			"\n",
			array(
				'שלום רב,',
				'',
				'ב-Hea-lth התקבלה בקשת רכש מגורם מקצועי מאומת בישראל שעשויה להתאים לחברה שלכם.',
				'מערכות או תחומים: ' . ( $equipment ? implode( ', ', $equipment ) : 'ציוד וטכנולוגיה למרפאה' ),
				'',
				'בשלב זה פרטי הרוכש נשמרים חסויים. ניתן לציין עניין או לוותר באזור הספקים:',
				home_url( '/professionals/supplier-portal/' ),
			)
		);

		foreach ( $recipients as $recipient ) {
			$hash = hash( 'sha256', strtolower( $recipient ) );
			if ( in_array( $hash, $delivered, true ) ) {
				continue;
			}
			$sent  = wp_mail( $recipient, 'הזדמנות רכש מקצועית חדשה | Hea-lth', $body );
			$log[] = array( 'recipient_hash' => $hash, 'status' => $sent ? 'sent' : 'failed', 'utc' => gmdate( 'c' ), 'attempt' => $attempts );
		}
		update_post_meta( $invitation_id, 'hp_rfq_delivery_log', $log );
		if ( ! self::is_delivered( $invitation_id ) && $attempts < self::MAX_ATTEMPTS && ! wp_next_scheduled( self::RETRY_HOOK, array( $invitation_id ) ) ) {
			wp_schedule_single_event( time() + ( 15 * MINUTE_IN_SECONDS ), self::RETRY_HOOK, array( $invitation_id ) );
		}
	}

	/** @return void */
	public static function retry_delivery( $invitation_id ) {
		$invitation = get_post( absint( $invitation_id ) );
		if ( $invitation instanceof WP_Post && self::POST_TYPE === $invitation->post_type && 'invited' === self::sanitize_status( get_post_meta( (int) $invitation->ID, 'hp_rfq_status', true ) ) ) {
			self::deliver( (int) $invitation->ID );
		}
	}

	/** @return bool */
	public static function is_delivered( $invitation_id ) {
		$recipients = self::supplier_recipients( absint( get_post_meta( $invitation_id, 'hp_rfq_supplier_id', true ) ) );
		if ( ! $recipients ) {
			return false;
		}
		$sent = array();
		foreach ( self::sanitize_delivery_log( get_post_meta( $invitation_id, 'hp_rfq_delivery_log', true ) ) as $entry ) {
			if ( 'sent' === $entry['status'] ) {
				$sent[] = $entry['recipient_hash'];
			}
		}
		foreach ( $recipients as $recipient ) {
			if ( ! in_array( hash( 'sha256', strtolower( $recipient ) ), $sent, true ) ) {
				return false;
			}
		}
		return true;
	}

	/** @return array<int, string> */
	private static function supplier_recipients( $supplier_id ) {
		$emails = array();
		$owners = Hea_Lth_Supplier_Portal::sanitize_id_list( get_post_meta( $supplier_id, 'hp_account_user_ids', true ) );
		foreach ( $owners as $user_id ) {
			$user = get_user_by( 'id', $user_id );
			if ( $user instanceof WP_User && is_email( $user->user_email ) ) {
				$emails[] = sanitize_email( $user->user_email );
			}
		}
		$fallback = sanitize_email( (string) get_post_meta( $supplier_id, 'hp_contact_email', true ) );
		if ( ! $emails && is_email( $fallback ) ) {
			$emails[] = $fallback;
		}
		return array_values( array_unique( $emails ) );
	}

	/** @return void */
	private static function notify_owner_response( $invitation_id, $response ) {
		$owner = sanitize_email( (string) get_option( 'admin_email' ) );
		if ( ! is_email( $owner ) ) {
			return;
		}
		$request_id  = absint( get_post_meta( $invitation_id, 'hp_rfq_request_id', true ) );
		$supplier_id = absint( get_post_meta( $invitation_id, 'hp_rfq_supplier_id', true ) );
		wp_mail( $owner, '[Hea-lth RFQ] תגובת ספק', get_the_title( $supplier_id ) . ' · ' . self::status_label( $response ) . "\n" . admin_url( 'post.php?post=' . $request_id . '&action=edit' ) );
	}

	/** @return array<int, array<string, mixed>> */
	public static function sanitize_delivery_log( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}
		$clean = array();
		foreach ( array_slice( $value, -30 ) as $entry ) {
			if ( ! is_array( $entry ) ) {
				continue;
			}
			$hash = isset( $entry['recipient_hash'] ) ? preg_replace( '/[^a-f0-9]/', '', strtolower( (string) $entry['recipient_hash'] ) ) : '';
			$hash = is_string( $hash ) ? $hash : '';
			if ( 64 !== strlen( $hash ) ) {
				continue;
			}
			$clean[] = array(
				'recipient_hash' => $hash,
				'status'         => isset( $entry['status'] ) && 'sent' === $entry['status'] ? 'sent' : 'failed',
				'utc'            => isset( $entry['utc'] ) ? sanitize_text_field( (string) $entry['utc'] ) : '',
				'attempt'        => isset( $entry['attempt'] ) ? absint( $entry['attempt'] ) : 0,
			);
		}
		return $clean;
	}

	/** @return array<int, array<string, mixed>> */
	public static function sanitize_audit( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}
		$clean = array();
		foreach ( array_slice( $value, -50 ) as $entry ) {
			if ( ! is_array( $entry ) ) {
				continue;
			}
			$clean[] = array(
				'event' => isset( $entry['event'] ) ? sanitize_key( $entry['event'] ) : '',
				'from'  => isset( $entry['from'] ) ? sanitize_key( $entry['from'] ) : '',
				'to'    => isset( $entry['to'] ) ? sanitize_key( $entry['to'] ) : '',
				'user'  => isset( $entry['user'] ) ? absint( $entry['user'] ) : 0,
				'utc'   => isset( $entry['utc'] ) ? sanitize_text_field( (string) $entry['utc'] ) : '',
			);
		}
		return $clean;
	}

	/** @return void */
	private static function append_audit( $invitation_id, $event, $from, $to, $user_id ) {
		$audit   = self::sanitize_audit( get_post_meta( $invitation_id, 'hp_rfq_audit', true ) );
		$audit[] = array( 'event' => $event, 'from' => $from, 'to' => $to, 'user' => $user_id, 'utc' => gmdate( 'c' ) );
		update_post_meta( $invitation_id, 'hp_rfq_audit', $audit );
	}
}
