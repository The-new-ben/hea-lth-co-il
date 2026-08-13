<?php
/**
 * Per-opportunity brokerage terms, acceptance evidence, and revenue ledger.
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Keeps commercial acceptance, attribution, deal value, and invoice readiness
 * on the private B2B request that generated the revenue.
 */
final class Hea_Lth_Brokerage_Ledger {
	const ACCEPT_ACTION = 'hea_lth_accept_brokerage_terms';
	const TERMS_VERSION = 'brokerage-2026-08-13-v1';

	/** @var array<string, string> */
	private static $fee_models = array(
		'percent' => 'אחוז מסכום העסקה',
		'fixed'   => 'עמלה קבועה',
		'hybrid'  => 'הגבוה מבין אחוז ועמלה קבועה',
	);

	/** @var array<string, string> */
	private static $invoice_states = array(
		'not_ready' => 'טרם מוכן',
		'ready'     => 'מוכן לחשבונית',
		'issued'    => 'הופקה חשבונית',
		'paid'      => 'שולם',
		'cancelled' => 'בוטל',
	);

	/**
	 * @return void
	 */
	public static function boot() {
		add_action( 'init', array( __CLASS__, 'register_metadata' ), 22 );
		add_action( 'admin_post_' . self::ACCEPT_ACTION, array( __CLASS__, 'handle_acceptance' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'register_admin_box' ) );
		add_action( 'save_post_hp_b2b_request', array( __CLASS__, 'save_request_admin' ), 20, 2 );
	}

	/**
	 * @return void
	 */
	public static function register_metadata() {
		foreach ( array( 'hp_terms_status', 'hp_terms_version', 'hp_fee_model', 'hp_terms_offered_utc', 'hp_terms_accepted_utc', 'hp_terms_acceptance_source', 'hp_terms_evidence_reference', 'hp_terms_snapshot_hash', 'hp_attribution_expires_utc', 'hp_deal_closed_utc', 'hp_invoice_state', 'hp_invoice_reference', 'hp_invoice_issued_utc', 'hp_invoice_paid_utc', 'hp_invoice_prompted_utc' ) as $key ) {
			self::meta( $key, 'string', '', 'sanitize_text_field' );
		}
		foreach ( array( 'hp_commission_rate_bps', 'hp_fixed_fee_ils', 'hp_min_fee_ils', 'hp_attribution_days', 'hp_terms_accepted_user_id', 'hp_estimated_deal_value_ils', 'hp_closed_deal_value_ils', 'hp_commission_due_ils' ) as $key ) {
			self::meta( $key, 'integer', 0, 'absint' );
		}
	}

	/**
	 * @param string          $key Meta key.
	 * @param string          $type Meta type.
	 * @param mixed           $default Default.
	 * @param callable|string $sanitizer Sanitizer.
	 * @return void
	 */
	private static function meta( $key, $type, $default, $sanitizer ) {
		register_post_meta(
			'hp_b2b_request',
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
	 * @return void
	 */
	public static function register_admin_box() {
		add_meta_box( 'hea-lth-brokerage-ledger', 'תנאי תיווך והכנסה', array( __CLASS__, 'render_admin_box' ), 'hp_b2b_request', 'normal', 'high' );
	}

	/**
	 * @param WP_Post $post Request post.
	 * @return void
	 */
	public static function render_admin_box( $post ) {
		$request_id = (int) $post->ID;
		$terms      = self::terms_view( $request_id );
		$estimated  = absint( get_post_meta( $request_id, 'hp_estimated_deal_value_ils', true ) );
		$closed     = absint( get_post_meta( $request_id, 'hp_closed_deal_value_ils', true ) );
		$due        = absint( get_post_meta( $request_id, 'hp_commission_due_ils', true ) );
		$invoice    = self::sanitize_invoice_state( get_post_meta( $request_id, 'hp_invoice_state', true ) );
		$reference  = sanitize_text_field( (string) get_post_meta( $request_id, 'hp_invoice_reference', true ) );
		$evidence   = sanitize_text_field( (string) get_post_meta( $request_id, 'hp_terms_evidence_reference', true ) );
		$locked     = self::is_financially_locked( $request_id );

		wp_nonce_field( 'hea_lth_brokerage_admin', 'hea_lth_brokerage_admin_nonce' );
		echo '<p><strong>מצב התנאים:</strong> ' . esc_html( self::terms_status_label( $terms['status'] ) ) . '</p>';
		if ( 'accepted' === $terms['status'] ) {
			echo '<p class="description">אושר ב-' . esc_html( $terms['accepted_utc'] ) . ' · מקור: ' . esc_html( 'written_external' === $terms['acceptance_source'] ? 'אישור כתוב חיצוני' : 'אזור הספקים' ) . ' · אסמכתה <code>' . esc_html( substr( $terms['snapshot_hash'], 0, 16 ) ) . '</code></p>';
		}
		if ( $locked ) {
			echo '<div class="notice notice-info inline"><p><strong>הרשומה הפיננסית נעולה.</strong> לאחר הפקת חשבונית נשמרים הספק ותנאי העסקה ללא שינוי.</p></div>';
		}
		echo '<div style="display:grid;grid-template-columns:repeat(2,minmax(220px,1fr));gap:12px">';
		echo '<p><label for="hp-fee-model"><strong>מודל עמלה</strong></label><select class="widefat" id="hp-fee-model" name="hp_fee_model">';
		foreach ( self::$fee_models as $key => $label ) {
			echo '<option value="' . esc_attr( $key ) . '" ' . selected( $terms['fee_model'], $key, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select></p>';
		echo self::number_input( 'hp_commission_rate_percent', 'אחוז עמלה', self::basis_points_to_percent( $terms['rate_bps'] ), '0.01' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by helper.
		echo self::number_input( 'hp_fixed_fee_ils', 'עמלה קבועה (₪)', $terms['fixed_fee_ils'], '1' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by helper.
		echo self::number_input( 'hp_min_fee_ils', 'עמלת מינימום (₪)', $terms['min_fee_ils'], '1' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by helper.
		echo self::number_input( 'hp_attribution_days', 'חלון שיוך (ימים)', $terms['attribution_days'], '1' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by helper.
		echo self::number_input( 'hp_estimated_deal_value_ils', 'שווי עסקה משוער (₪)', $estimated, '1' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by helper.
		echo self::number_input( 'hp_closed_deal_value_ils', 'שווי עסקה שנסגרה (₪)', $closed, '1' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by helper.
		echo '</div>';
		echo '<p><label><input type="checkbox" name="hp_offer_terms" value="1"> <strong>שליחת התנאים לעיון ולאישור הספק</strong></label><br><span class="description">שמירה עם סימון זה פותחת הצעה חדשה. שינוי תנאים שאושרו פותח אותם מחדש לאישור.</span></p>';
		echo '<p><label for="hp-terms-evidence"><strong>אסמכתה לאישור כתוב קיים</strong></label><input class="widefat" id="hp-terms-evidence" name="hp_terms_evidence_reference" value="' . esc_attr( $evidence ) . '" placeholder="לדוגמה: מייל מ-13.08.2026 / מזהה שיחה"></p>';
		echo '<p><label><input type="checkbox" name="hp_record_written_acceptance" value="1"> <strong>רישום אישור כתוב שכבר התקבל מהספק</strong></label><br><span class="description">יש לסמן רק לאחר בדיקת האישור והאסמכתה מול הספק שהוקצה לפנייה.</span></p>';
		echo '<hr><h3>גבייה</h3><p><strong>עמלה מחושבת:</strong> ₪' . esc_html( number_format_i18n( $due ) ) . '</p>';
		echo '<p><label for="hp-invoice-state"><strong>מצב חשבונית</strong></label><select id="hp-invoice-state" name="hp_invoice_state">';
		foreach ( self::$invoice_states as $key => $label ) {
			echo '<option value="' . esc_attr( $key ) . '" ' . selected( $invoice, $key, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select></p><p><label for="hp-invoice-reference"><strong>מספר חשבונית / אסמכתה</strong></label><input class="regular-text" id="hp-invoice-reference" name="hp_invoice_reference" value="' . esc_attr( $reference ) . '"></p>';
	}

	/**
	 * @param string $name Field name.
	 * @param string $label Label.
	 * @param mixed  $value Value.
	 * @param string $step Number step.
	 * @return string
	 */
	private static function number_input( $name, $label, $value, $step ) {
		return '<p><label for="' . esc_attr( $name ) . '"><strong>' . esc_html( $label ) . '</strong></label><input class="widefat" type="number" min="0" step="' . esc_attr( $step ) . '" id="' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $value ) . '"></p>';
	}

	/**
	 * @param int     $post_id Request ID.
	 * @param WP_Post $post Request post.
	 * @return void
	 */
	public static function save_request_admin( $post_id, $post ) {
		if ( 'hp_b2b_request' !== $post->post_type || ! self::admin_save_allowed( $post_id ) ) {
			return;
		}

		$old_terms  = self::terms_view( $post_id );
		$old_invoice = self::sanitize_invoice_state( get_post_meta( $post_id, 'hp_invoice_state', true ) );
		$old_reference = sanitize_text_field( (string) get_post_meta( $post_id, 'hp_invoice_reference', true ) );
		$fee_model  = isset( $_POST['hp_fee_model'] ) ? self::sanitize_fee_model( sanitize_key( wp_unslash( $_POST['hp_fee_model'] ) ) ) : 'percent'; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified above.
		$rate_bps   = isset( $_POST['hp_commission_rate_percent'] ) ? self::percent_to_basis_points( sanitize_text_field( wp_unslash( $_POST['hp_commission_rate_percent'] ) ) ) : 1000; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified above.
		$fixed_fee  = isset( $_POST['hp_fixed_fee_ils'] ) ? self::bounded_money( absint( wp_unslash( $_POST['hp_fixed_fee_ils'] ) ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified above.
		$min_fee    = isset( $_POST['hp_min_fee_ils'] ) ? self::bounded_money( absint( wp_unslash( $_POST['hp_min_fee_ils'] ) ) ) : 8000; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified above.
		$days       = isset( $_POST['hp_attribution_days'] ) ? self::bounded_days( absint( wp_unslash( $_POST['hp_attribution_days'] ) ) ) : 180; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified above.
		$estimated  = isset( $_POST['hp_estimated_deal_value_ils'] ) ? self::bounded_money( absint( wp_unslash( $_POST['hp_estimated_deal_value_ils'] ) ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified above.
		$closed     = isset( $_POST['hp_closed_deal_value_ils'] ) ? self::bounded_money( absint( wp_unslash( $_POST['hp_closed_deal_value_ils'] ) ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified above.
		$requested_invoice = isset( $_POST['hp_invoice_state'] ) ? self::sanitize_invoice_state( sanitize_key( wp_unslash( $_POST['hp_invoice_state'] ) ) ) : 'not_ready'; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified above.
		$reference  = isset( $_POST['hp_invoice_reference'] ) ? sanitize_text_field( wp_unslash( $_POST['hp_invoice_reference'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified above.
		$evidence   = isset( $_POST['hp_terms_evidence_reference'] ) ? sanitize_text_field( wp_unslash( $_POST['hp_terms_evidence_reference'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified above.
		$offer      = isset( $_POST['hp_offer_terms'] ) && 1 === absint( wp_unslash( $_POST['hp_offer_terms'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified above.
		$record_written = isset( $_POST['hp_record_written_acceptance'] ) && 1 === absint( wp_unslash( $_POST['hp_record_written_acceptance'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified above.
		if ( self::is_financially_locked( $post_id ) ) {
			$fee_model = $old_terms['fee_model'];
			$rate_bps  = $old_terms['rate_bps'];
			$fixed_fee = $old_terms['fixed_fee_ils'];
			$min_fee   = $old_terms['min_fee_ils'];
			$days      = $old_terms['attribution_days'];
			$estimated = absint( get_post_meta( $post_id, 'hp_estimated_deal_value_ils', true ) );
			$closed    = absint( get_post_meta( $post_id, 'hp_closed_deal_value_ils', true ) );
			$offer     = false;
			$record_written = false;
			if ( '' === $reference ) {
				$reference = $old_reference;
			}
		}

		$new_fingerprint = self::terms_fingerprint( $fee_model, $rate_bps, $fixed_fee, $min_fee, $days );
		$old_fingerprint = self::terms_fingerprint( $old_terms['fee_model'], $old_terms['rate_bps'], $old_terms['fixed_fee_ils'], $old_terms['min_fee_ils'], $old_terms['attribution_days'] );
		$terms_changed   = ! hash_equals( $old_fingerprint, $new_fingerprint );

		update_post_meta( $post_id, 'hp_fee_model', $fee_model );
		update_post_meta( $post_id, 'hp_commission_rate_bps', $rate_bps );
		update_post_meta( $post_id, 'hp_fixed_fee_ils', $fixed_fee );
		update_post_meta( $post_id, 'hp_min_fee_ils', $min_fee );
		update_post_meta( $post_id, 'hp_attribution_days', $days );
		update_post_meta( $post_id, 'hp_estimated_deal_value_ils', $estimated );
		update_post_meta( $post_id, 'hp_closed_deal_value_ils', $closed );

		if ( $offer || ( 'accepted' === $old_terms['status'] && $terms_changed ) ) {
			update_post_meta( $post_id, 'hp_terms_status', 'offered' );
			update_post_meta( $post_id, 'hp_terms_version', self::TERMS_VERSION );
			update_post_meta( $post_id, 'hp_terms_offered_utc', gmdate( 'c' ) );
			foreach ( array( 'hp_terms_accepted_utc', 'hp_terms_snapshot_hash', 'hp_attribution_expires_utc' ) as $key ) {
				delete_post_meta( $post_id, $key );
			}
			delete_post_meta( $post_id, 'hp_terms_accepted_user_id' );
			delete_post_meta( $post_id, 'hp_terms_acceptance_source' );
			delete_post_meta( $post_id, 'hp_terms_evidence_reference' );
			update_post_meta( $post_id, 'hp_lead_release_state', 'held' );
			delete_post_meta( $post_id, 'hp_invoice_prompted_utc' );
			self::record_audit( $post_id, 'terms_offered', $old_terms['status'], 'offered', get_current_user_id() );
		}
		if ( $record_written && '' !== $evidence ) {
			$supplier_id = absint( get_post_meta( $post_id, 'hp_assigned_supplier_id', true ) );
			$current     = self::terms_view( $post_id );
			if ( $supplier_id && 'offered' === $current['status'] ) {
				self::record_acceptance( $post_id, $supplier_id, get_current_user_id(), 'written_external', $evidence );
			}
		}

		$pipeline = isset( $_POST['hp_supplier_pipeline_status'] ) ? Hea_Lth_Supplier_Portal::sanitize_pipeline_state( sanitize_key( wp_unslash( $_POST['hp_supplier_pipeline_status'] ) ) ) : Hea_Lth_Supplier_Portal::sanitize_pipeline_state( get_post_meta( $post_id, 'hp_supplier_pipeline_status', true ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Request save nonce verified by both metabox handlers.
		$is_ready    = 'closed_won' === $pipeline && $closed > 0 && self::can_release( $post_id );
		if ( $is_ready ) {
			$due = self::calculate_commission( $closed, $fee_model, $rate_bps, $fixed_fee, $min_fee );
			update_post_meta( $post_id, 'hp_commission_due_ils', $due );
			if ( '' === (string) get_post_meta( $post_id, 'hp_deal_closed_utc', true ) ) {
				update_post_meta( $post_id, 'hp_deal_closed_utc', gmdate( 'c' ) );
			}
		} elseif ( ! in_array( $old_invoice, array( 'issued', 'paid', 'cancelled' ), true ) ) {
			update_post_meta( $post_id, 'hp_commission_due_ils', 0 );
			delete_post_meta( $post_id, 'hp_deal_closed_utc' );
		}

		if ( 'issued' === $requested_invoice && '' === $reference ) {
			$requested_invoice = 'ready';
		}
		if ( 'paid' === $requested_invoice && '' === $reference ) {
			$requested_invoice = 'issued';
		}
		$invoice = self::next_invoice_state( $old_invoice, $requested_invoice, $is_ready );
		update_post_meta( $post_id, 'hp_invoice_state', $invoice );
		update_post_meta( $post_id, 'hp_invoice_reference', $reference );
		if ( 'issued' === $invoice && 'issued' !== $old_invoice ) {
			update_post_meta( $post_id, 'hp_invoice_issued_utc', gmdate( 'c' ) );
		}
		if ( 'paid' === $invoice && 'paid' !== $old_invoice ) {
			update_post_meta( $post_id, 'hp_invoice_paid_utc', gmdate( 'c' ) );
		}
		if ( 'ready' === $invoice && '' === (string) get_post_meta( $post_id, 'hp_invoice_prompted_utc', true ) ) {
			self::notify_invoice_ready( $post_id );
		}
	}

	/**
	 * @return void
	 */
	public static function handle_acceptance() {
		if ( ! is_user_logged_in() ) {
			auth_redirect();
		}
		check_admin_referer( self::ACCEPT_ACTION, 'hea_lth_terms_nonce' );
		$request_id = isset( $_POST['request_id'] ) ? absint( wp_unslash( $_POST['request_id'] ) ) : 0;
		$confirmed  = isset( $_POST['terms_confirmed'] ) && 1 === absint( wp_unslash( $_POST['terms_confirmed'] ) );
		$supplier   = Hea_Lth_Supplier_Portal::supplier_for_user();
		$request    = $request_id ? get_post( $request_id ) : null;

		if ( ! $confirmed || ! $supplier instanceof WP_Post || ! $request instanceof WP_Post || 'hp_b2b_request' !== $request->post_type || absint( get_post_meta( $request_id, 'hp_assigned_supplier_id', true ) ) !== (int) $supplier->ID ) {
			wp_die( esc_html__( 'לא ניתן לאשר את התנאים עבור חשבון זה.', 'hea-lth-platform-core' ), '', array( 'response' => 403 ) );
		}
		$terms = self::terms_view( $request_id );
		if ( 'offered' !== $terms['status'] ) {
			wp_die( esc_html__( 'הצעת התנאים אינה פתוחה לאישור.', 'hea-lth-platform-core' ), '', array( 'response' => 409 ) );
		}

		self::record_acceptance( $request_id, (int) $supplier->ID, get_current_user_id(), 'supplier_portal', 'authenticated-account' );
		wp_safe_redirect( add_query_arg( 'portal', 'terms-accepted', home_url( '/professionals/supplier-portal/' ) ) );
		exit;
	}

	/**
	 * @param int $request_id Request ID.
	 * @return bool
	 */
	public static function can_release( $request_id ) {
		$terms       = self::terms_view( $request_id );
		$supplier_id = absint( get_post_meta( $request_id, 'hp_assigned_supplier_id', true ) );
		$user_id     = absint( get_post_meta( $request_id, 'hp_terms_accepted_user_id', true ) );
		$accepted    = (string) get_post_meta( $request_id, 'hp_terms_accepted_utc', true );
		$stored_hash = (string) get_post_meta( $request_id, 'hp_terms_snapshot_hash', true );
		if ( ! $supplier_id || ! $user_id || '' === $accepted || '' === $stored_hash || 'accepted' !== $terms['status'] || self::TERMS_VERSION !== $terms['version'] ) {
			return false;
		}
		$expected = hash( 'sha256', wp_json_encode( self::acceptance_snapshot( $request_id, $supplier_id, $user_id, $accepted, $terms ) ) );
		return hash_equals( $stored_hash, $expected );
	}

	/**
	 * Clear acceptance when an opportunity is reassigned to another supplier.
	 *
	 * @param int $request_id Request ID.
	 * @return void
	 */
	public static function invalidate_terms( $request_id ) {
		if ( self::is_financially_locked( $request_id ) ) {
			return;
		}
		$old_status = self::sanitize_terms_status( get_post_meta( $request_id, 'hp_terms_status', true ) );
		update_post_meta( $request_id, 'hp_terms_status', 'none' );
		update_post_meta( $request_id, 'hp_lead_release_state', 'held' );
		foreach ( array( 'hp_terms_version', 'hp_terms_offered_utc', 'hp_terms_accepted_utc', 'hp_terms_acceptance_source', 'hp_terms_evidence_reference', 'hp_terms_snapshot_hash', 'hp_attribution_expires_utc' ) as $key ) {
			delete_post_meta( $request_id, $key );
		}
		delete_post_meta( $request_id, 'hp_terms_accepted_user_id' );
		delete_post_meta( $request_id, 'hp_invoice_prompted_utc' );
		self::record_audit( $request_id, 'terms_invalidated', $old_status, 'none', get_current_user_id() );
	}

	/**
	 * Once invoiced, supplier attribution and deal economics are immutable.
	 *
	 * @param int $request_id Request ID.
	 * @return bool
	 */
	public static function is_financially_locked( $request_id ) {
		return in_array( self::sanitize_invoice_state( get_post_meta( $request_id, 'hp_invoice_state', true ) ), array( 'issued', 'paid', 'cancelled' ), true );
	}

	/**
	 * @param int $request_id Request ID.
	 * @return array<string, mixed>
	 */
	public static function terms_view( $request_id ) {
		$min_fee_raw = get_post_meta( $request_id, 'hp_min_fee_ils', true );
		return array(
			'status'           => self::sanitize_terms_status( get_post_meta( $request_id, 'hp_terms_status', true ) ),
			'version'          => sanitize_text_field( (string) get_post_meta( $request_id, 'hp_terms_version', true ) ),
			'fee_model'        => self::sanitize_fee_model( get_post_meta( $request_id, 'hp_fee_model', true ) ),
			'rate_bps'         => self::bounded_basis_points( get_post_meta( $request_id, 'hp_commission_rate_bps', true ) ),
			'fixed_fee_ils'    => self::bounded_money( get_post_meta( $request_id, 'hp_fixed_fee_ils', true ) ),
			'min_fee_ils'      => '' === (string) $min_fee_raw ? 8000 : self::bounded_money( $min_fee_raw ),
			'attribution_days' => self::bounded_days( get_post_meta( $request_id, 'hp_attribution_days', true ) ),
			'offered_utc'      => sanitize_text_field( (string) get_post_meta( $request_id, 'hp_terms_offered_utc', true ) ),
			'accepted_utc'     => sanitize_text_field( (string) get_post_meta( $request_id, 'hp_terms_accepted_utc', true ) ),
			'expires_utc'      => sanitize_text_field( (string) get_post_meta( $request_id, 'hp_attribution_expires_utc', true ) ),
			'snapshot_hash'    => sanitize_text_field( (string) get_post_meta( $request_id, 'hp_terms_snapshot_hash', true ) ),
			'acceptance_source'=> sanitize_key( (string) get_post_meta( $request_id, 'hp_terms_acceptance_source', true ) ),
			'evidence_reference'=> sanitize_text_field( (string) get_post_meta( $request_id, 'hp_terms_evidence_reference', true ) ),
		);
	}

	/**
	 * @param array<string, mixed> $terms Terms view.
	 * @return string
	 */
	public static function public_terms_summary( $terms ) {
		$percent = self::basis_points_to_percent( isset( $terms['rate_bps'] ) ? $terms['rate_bps'] : 0 );
		$fixed   = isset( $terms['fixed_fee_ils'] ) ? absint( $terms['fixed_fee_ils'] ) : 0;
		$minimum = isset( $terms['min_fee_ils'] ) ? absint( $terms['min_fee_ils'] ) : 0;
		$model   = isset( $terms['fee_model'] ) ? self::sanitize_fee_model( $terms['fee_model'] ) : 'percent';

		if ( 'fixed' === $model ) {
			return sprintf( 'עמלה קבועה בסך ₪%s', number_format_i18n( $fixed ) );
		}
		if ( 'hybrid' === $model ) {
			return sprintf( '%s%% מסכום העסקה או ₪%s — הגבוה מביניהם', $percent, number_format_i18n( max( $fixed, $minimum ) ) );
		}
		return $minimum > 0
			? sprintf( '%s%% מסכום העסקה, מינימום ₪%s', $percent, number_format_i18n( $minimum ) )
			: sprintf( '%s%% מסכום העסקה', $percent );
	}

	/**
	 * Integer-only commission calculation in ILS.
	 *
	 * @param int    $deal_value Deal value.
	 * @param string $model Fee model.
	 * @param int    $rate_bps Basis points.
	 * @param int    $fixed Fixed fee.
	 * @param int    $minimum Minimum fee.
	 * @return int
	 */
	public static function calculate_commission( $deal_value, $model, $rate_bps, $fixed, $minimum ) {
		$deal_value = self::bounded_money( $deal_value );
		$model      = self::sanitize_fee_model( $model );
		$rate_bps   = self::bounded_basis_points( $rate_bps );
		$fixed      = self::bounded_money( $fixed );
		$minimum    = self::bounded_money( $minimum );
		$percent    = (int) round( ( $deal_value * $rate_bps ) / 10000 );
		if ( 'fixed' === $model ) {
			return $fixed;
		}
		if ( 'hybrid' === $model ) {
			return max( $percent, $fixed, $minimum );
		}
		return max( $percent, $minimum );
	}

	/**
	 * Enforce an auditable invoice lifecycle without state skipping.
	 *
	 * @param string $current Current state.
	 * @param string $requested Requested state.
	 * @param bool   $ready Whether the deal evidence is invoice-ready.
	 * @return string
	 */
	public static function next_invoice_state( $current, $requested, $ready ) {
		$current   = self::sanitize_invoice_state( $current );
		$requested = self::sanitize_invoice_state( $requested );
		if ( 'paid' === $current ) {
			return 'paid';
		}
		if ( 'issued' === $current ) {
			return in_array( $requested, array( 'paid', 'cancelled' ), true ) ? $requested : 'issued';
		}
		if ( 'cancelled' === $current ) {
			return 'cancelled';
		}
		if ( ! $ready ) {
			return 'not_ready';
		}
		if ( 'ready' === $current && in_array( $requested, array( 'issued', 'cancelled' ), true ) ) {
			return $requested;
		}
		return 'ready';
	}

	/**
	 * Render aggregate owner revenue without exposing it publicly.
	 *
	 * @return void
	 */
	public static function render_owner_summary() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$summary = array( 'offered' => 0, 'accepted' => 0, 'ready' => 0, 'issued' => 0, 'paid' => 0, 'due' => 0, 'collected' => 0 );
		$page    = 1;
		do {
			$requests = get_posts( array( 'post_type' => 'hp_b2b_request', 'post_status' => 'private', 'posts_per_page' => 200, 'paged' => $page, 'orderby' => 'ID', 'order' => 'ASC', 'no_found_rows' => true ) );
			foreach ( $requests as $request ) {
				$terms = self::sanitize_terms_status( get_post_meta( (int) $request->ID, 'hp_terms_status', true ) );
				if ( isset( $summary[ $terms ] ) ) {
					$summary[ $terms ]++;
				}
				$invoice = self::sanitize_invoice_state( get_post_meta( (int) $request->ID, 'hp_invoice_state', true ) );
				if ( isset( $summary[ $invoice ] ) ) {
					$summary[ $invoice ]++;
				}
				$due = absint( get_post_meta( (int) $request->ID, 'hp_commission_due_ils', true ) );
				if ( in_array( $invoice, array( 'ready', 'issued' ), true ) ) {
					$summary['due'] += $due;
				} elseif ( 'paid' === $invoice ) {
					$summary['collected'] += $due;
				}
			}
			$page++;
		} while ( 200 === count( $requests ) );
		echo '<h3>' . esc_html__( 'Brokerage revenue ledger', 'hea-lth-platform-core' ) . '</h3>';
		echo '<table class="widefat striped" style="max-width:880px"><thead><tr><th>תנאים שהוצעו</th><th>תנאים שאושרו</th><th>מוכן לחשבונית</th><th>חשבוניות שהופקו</th><th>לתשלום</th><th>נגבה</th></tr></thead><tbody><tr>';
		foreach ( array( $summary['offered'], $summary['accepted'], $summary['ready'], $summary['issued'], '₪' . number_format_i18n( $summary['due'] ), '₪' . number_format_i18n( $summary['collected'] ) ) as $value ) {
			echo '<td><strong>' . esc_html( (string) $value ) . '</strong></td>';
		}
		echo '</tr></tbody></table>';
	}

	/** @param mixed $value Raw. @return string */
	public static function sanitize_terms_status( $value ) {
		$value = sanitize_key( (string) $value );
		return in_array( $value, array( 'none', 'offered', 'accepted', 'declined', 'superseded' ), true ) ? $value : 'none';
	}

	/** @param mixed $value Raw. @return string */
	public static function sanitize_fee_model( $value ) {
		$value = sanitize_key( (string) $value );
		return isset( self::$fee_models[ $value ] ) ? $value : 'percent';
	}

	/** @param mixed $value Raw. @return string */
	public static function sanitize_invoice_state( $value ) {
		$value = sanitize_key( (string) $value );
		return isset( self::$invoice_states[ $value ] ) ? $value : 'not_ready';
	}

	/** @param mixed $value Raw. @return int */
	public static function bounded_money( $value ) {
		return min( 1000000000, absint( $value ) );
	}

	/** @param mixed $value Raw. @return int */
	public static function bounded_basis_points( $value ) {
		$value = absint( $value );
		return $value > 0 ? min( 10000, $value ) : 1000;
	}

	/** @param mixed $value Raw. @return int */
	public static function bounded_days( $value ) {
		$value = absint( $value );
		return $value > 0 ? min( 730, $value ) : 180;
	}

	/** @param mixed $value Raw percent. @return int */
	public static function percent_to_basis_points( $value ) {
		$value = (float) str_replace( ',', '.', (string) $value );
		return self::bounded_basis_points( (int) round( $value * 100 ) );
	}

	/** @param mixed $value Basis points. @return string */
	public static function basis_points_to_percent( $value ) {
		return rtrim( rtrim( number_format( self::bounded_basis_points( $value ) / 100, 2, '.', '' ), '0' ), '.' );
	}

	/**
	 * @param string $status Status.
	 * @return string
	 */
	public static function terms_status_label( $status ) {
		$labels = array( 'none' => 'טרם הוצעו', 'offered' => 'ממתינים לאישור הספק', 'accepted' => 'אושרו', 'declined' => 'נדחו', 'superseded' => 'הוחלפו' );
		$status = self::sanitize_terms_status( $status );
		return $labels[ $status ];
	}

	/**
	 * @param int    $request_id Request ID.
	 * @param int    $supplier_id Supplier ID.
	 * @param int    $user_id User ID.
	 * @param string $accepted_utc Acceptance timestamp.
	 * @param array  $terms Terms.
	 * @return array<string, mixed>
	 */
	private static function acceptance_snapshot( $request_id, $supplier_id, $user_id, $accepted_utc, $terms ) {
		return array(
			'request_id'       => absint( $request_id ),
			'supplier_id'      => absint( $supplier_id ),
			'user_id'          => absint( $user_id ),
			'accepted_utc'     => sanitize_text_field( $accepted_utc ),
			'version'          => sanitize_text_field( (string) $terms['version'] ),
			'fee_model'        => self::sanitize_fee_model( $terms['fee_model'] ),
			'rate_bps'         => self::bounded_basis_points( $terms['rate_bps'] ),
			'fixed_fee_ils'    => self::bounded_money( $terms['fixed_fee_ils'] ),
			'min_fee_ils'      => self::bounded_money( $terms['min_fee_ils'] ),
			'attribution_days' => self::bounded_days( $terms['attribution_days'] ),
			'acceptance_source'=> sanitize_key( isset( $terms['acceptance_source'] ) ? (string) $terms['acceptance_source'] : '' ),
			'evidence_reference'=> sanitize_text_field( isset( $terms['evidence_reference'] ) ? (string) $terms['evidence_reference'] : '' ),
		);
	}

	/**
	 * Store one acceptance path with the same fingerprint and audit rules.
	 *
	 * @param int    $request_id Request ID.
	 * @param int    $supplier_id Supplier ID.
	 * @param int    $user_id Recording user ID.
	 * @param string $source Acceptance source.
	 * @param string $evidence Evidence reference.
	 * @return void
	 */
	private static function record_acceptance( $request_id, $supplier_id, $user_id, $source, $evidence ) {
		$source = in_array( $source, array( 'supplier_portal', 'written_external' ), true ) ? $source : 'supplier_portal';
		update_post_meta( $request_id, 'hp_terms_acceptance_source', $source );
		update_post_meta( $request_id, 'hp_terms_evidence_reference', sanitize_text_field( $evidence ) );
		$terms        = self::terms_view( $request_id );
		$accepted_utc = gmdate( 'c' );
		$expires_utc  = gmdate( 'c', time() + ( DAY_IN_SECONDS * $terms['attribution_days'] ) );
		$snapshot     = self::acceptance_snapshot( $request_id, $supplier_id, $user_id, $accepted_utc, $terms );
		update_post_meta( $request_id, 'hp_terms_status', 'accepted' );
		update_post_meta( $request_id, 'hp_terms_accepted_utc', $accepted_utc );
		update_post_meta( $request_id, 'hp_terms_accepted_user_id', $user_id );
		update_post_meta( $request_id, 'hp_terms_snapshot_hash', hash( 'sha256', wp_json_encode( $snapshot ) ) );
		update_post_meta( $request_id, 'hp_attribution_expires_utc', $expires_utc );
		self::record_audit( $request_id, 'terms_accepted', 'offered', 'accepted', $user_id );
	}

	/** @return bool */
	private static function admin_save_allowed( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}
		if ( ! isset( $_POST['hea_lth_brokerage_admin_nonce'] ) ) {
			return false;
		}
		$nonce = sanitize_text_field( wp_unslash( $_POST['hea_lth_brokerage_admin_nonce'] ) );
		return wp_verify_nonce( $nonce, 'hea_lth_brokerage_admin' ) && current_user_can( 'edit_post', $post_id );
	}

	/** @return string */
	private static function terms_fingerprint( $model, $rate_bps, $fixed, $minimum, $days ) {
		return hash( 'sha256', implode( '|', array( self::TERMS_VERSION, self::sanitize_fee_model( $model ), self::bounded_basis_points( $rate_bps ), self::bounded_money( $fixed ), self::bounded_money( $minimum ), self::bounded_days( $days ) ) ) );
	}

	/** @return void */
	private static function record_audit( $request_id, $event, $from, $to, $user_id ) {
		$log   = Hea_Lth_Supplier_Portal::sanitize_audit_log( get_post_meta( $request_id, 'hp_request_audit', true ) );
		$log[] = array( 'at' => gmdate( 'c' ), 'event' => sanitize_key( $event ), 'from' => sanitize_text_field( $from ), 'to' => sanitize_text_field( $to ), 'user_id' => absint( $user_id ) );
		update_post_meta( $request_id, 'hp_request_audit', array_slice( $log, -100 ) );
	}

	/** @return void */
	private static function notify_invoice_ready( $request_id ) {
		if ( '' !== (string) get_post_meta( $request_id, 'hp_invoice_prompted_utc', true ) ) {
			return;
		}
		$recipient = sanitize_email( (string) get_option( 'admin_email' ) );
		if ( ! is_email( $recipient ) ) {
			return;
		}
		$due = absint( get_post_meta( $request_id, 'hp_commission_due_ils', true ) );
		$sent = wp_mail( $recipient, sprintf( '[Hea-lth] עמלה מוכנה לחשבונית — פנייה #%d', $request_id ), sprintf( "פנייה #%d נסגרה. עמלה מחושבת: ₪%s\n%s", $request_id, number_format_i18n( $due ), admin_url( 'post.php?post=' . $request_id . '&action=edit' ) ) );
		if ( $sent ) {
			update_post_meta( $request_id, 'hp_invoice_prompted_utc', gmdate( 'c' ) );
		}
	}
}
