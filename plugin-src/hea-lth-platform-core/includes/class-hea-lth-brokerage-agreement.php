<?php
/**
 * Private brokerage agreement records and delivery.
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates an immutable confirmation document for each acceptance and sends a
 * private copy to the supplier and the Hea-lth owner.
 */
final class Hea_Lth_Brokerage_Agreement {
	const DOWNLOAD_ACTION = 'hea_lth_brokerage_document';
	const RETRY_HOOK      = 'hea_lth_retry_brokerage_document';
	const DOCUMENTS_META  = 'hp_brokerage_documents';
	const DELIVERY_META   = 'hp_brokerage_document_delivery';

	/** @return void */
	public static function boot() {
		add_action( 'init', array( __CLASS__, 'register_metadata' ), 23 );
		add_action( 'admin_post_' . self::DOWNLOAD_ACTION, array( __CLASS__, 'handle_download' ) );
		add_action( self::RETRY_HOOK, array( __CLASS__, 'retry_delivery' ), 10, 2 );
	}

	/** @return void */
	public static function register_metadata() {
		foreach ( array( self::DOCUMENTS_META => 'sanitize_documents', self::DELIVERY_META => 'sanitize_delivery_log' ) as $key => $sanitizer ) {
			register_post_meta(
				'hp_b2b_request',
				$key,
				array(
					'single'            => true,
					'type'              => 'array',
					'default'           => array(),
					'sanitize_callback' => array( __CLASS__, $sanitizer ),
					'auth_callback'     => array( 'Hea_Lth_Brokerage_Ledger', 'can_edit_meta' ),
					'show_in_rest'      => false,
				)
			);
		}
	}

	/**
	 * @param int    $request_id Request ID.
	 * @param int    $supplier_id Supplier ID.
	 * @param int    $user_id Accepting or recording user ID.
	 * @param array  $terms Accepted terms.
	 * @param string $snapshot_hash Acceptance fingerprint.
	 * @return array<string, mixed>
	 */
	public static function create_and_deliver( $request_id, $supplier_id, $user_id, $terms, $snapshot_hash ) {
		$accepted_utc = sanitize_text_field( (string) get_post_meta( $request_id, 'hp_terms_accepted_utc', true ) );
		$document     = self::build_document( $request_id, $supplier_id, $user_id, $terms, $snapshot_hash, $accepted_utc );
		$documents    = self::sanitize_documents( get_post_meta( $request_id, self::DOCUMENTS_META, true ) );
		$documents[]  = $document;
		update_post_meta( $request_id, self::DOCUMENTS_META, array_slice( $documents, -20 ) );
		self::deliver_document( $request_id, $document );
		return $document;
	}

	/**
	 * @param int $request_id Request ID.
	 * @return array<string, mixed>
	 */
	public static function latest_document( $request_id ) {
		$documents = self::sanitize_documents( get_post_meta( $request_id, self::DOCUMENTS_META, true ) );
		for ( $index = count( $documents ) - 1; $index >= 0; $index-- ) {
			if ( self::document_is_valid( $documents[ $index ] ) ) {
				return $documents[ $index ];
			}
		}
		return array();
	}

	/**
	 * @param int    $request_id Request ID.
	 * @param string $snapshot_hash Acceptance hash.
	 * @return bool
	 */
	public static function has_matching_document( $request_id, $snapshot_hash ) {
		$document = self::latest_document( $request_id );
		return $document && hash_equals( $document['snapshot_hash'], sanitize_text_field( $snapshot_hash ) );
	}

	/**
	 * @param int    $request_id Request ID.
	 * @param string $snapshot_hash Acceptance hash.
	 * @return bool
	 */
	public static function is_fully_delivered( $request_id, $snapshot_hash ) {
		$document = self::latest_document( $request_id );
		if ( ! $document || ! hash_equals( $document['snapshot_hash'], sanitize_text_field( $snapshot_hash ) ) ) {
			return false;
		}
		$status = self::delivery_status( $request_id, $document['document_id'] );
		return $status['supplier'] && $status['owner'];
	}

	/**
	 * @param int    $request_id Request ID.
	 * @param string $document_id Document ID.
	 * @return string
	 */
	public static function download_url( $request_id, $document_id ) {
		$url = add_query_arg(
			array(
				'action'      => self::DOWNLOAD_ACTION,
				'request_id'  => absint( $request_id ),
				'document_id' => sanitize_text_field( $document_id ),
			),
			admin_url( 'admin-post.php' )
		);
		return wp_nonce_url( $url, self::nonce_action( $request_id, $document_id ), 'agreement_nonce' );
	}

	/**
	 * @param int    $request_id Request ID.
	 * @param string $document_id Document ID.
	 * @return array<string, bool>
	 */
	public static function delivery_status( $request_id, $document_id ) {
		$status = array( 'supplier' => false, 'owner' => false );
		foreach ( self::sanitize_delivery_log( get_post_meta( $request_id, self::DELIVERY_META, true ) ) as $entry ) {
			if ( $entry['document_id'] !== $document_id || ! $entry['sent'] ) {
				continue;
			}
			if ( 'owner_supplier' === $entry['role'] ) {
				$status['owner']    = true;
				$status['supplier'] = true;
			} elseif ( isset( $status[ $entry['role'] ] ) ) {
				$status[ $entry['role'] ] = true;
			}
		}
		return $status;
	}

	/** @return void */
	public static function handle_download() {
		if ( ! is_user_logged_in() ) {
			auth_redirect();
		}
		$request_id  = isset( $_GET['request_id'] ) ? absint( wp_unslash( $_GET['request_id'] ) ) : 0;
		$document_id = isset( $_GET['document_id'] ) ? sanitize_text_field( wp_unslash( $_GET['document_id'] ) ) : '';
		$nonce       = isset( $_GET['agreement_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['agreement_nonce'] ) ) : '';
		if ( ! $request_id || '' === $document_id || ! wp_verify_nonce( $nonce, self::nonce_action( $request_id, $document_id ) ) ) {
			wp_die( esc_html__( 'קישור המסמך אינו תקין.', 'hea-lth-platform-core' ), '', array( 'response' => 403 ) );
		}
		$document = self::find_document( $request_id, $document_id );
		if ( ! $document || ! self::can_access_document( $document ) ) {
			wp_die( esc_html__( 'המסמך אינו זמין לחשבון זה.', 'hea-lth-platform-core' ), '', array( 'response' => 403 ) );
		}

		nocache_headers();
		header( 'Content-Type: text/html; charset=UTF-8' );
		header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $document_id . '.html' ) . '"' );
		echo self::document_html( $document ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The renderer escapes every dynamic field.
		exit;
	}

	/**
	 * @param int    $request_id Request ID.
	 * @param string $document_id Document ID.
	 * @return void
	 */
	public static function retry_delivery( $request_id, $document_id ) {
		$document = self::find_document( absint( $request_id ), sanitize_text_field( $document_id ) );
		if ( $document ) {
			self::deliver_document( absint( $request_id ), $document );
		}
	}

	/**
	 * @param mixed $value Raw records.
	 * @return array<int, array<string, mixed>>
	 */
	public static function sanitize_documents( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}
		$clean = array();
		foreach ( array_slice( $value, -20 ) as $document ) {
			if ( ! is_array( $document ) ) {
				continue;
			}
			$terms   = isset( $document['terms'] ) && is_array( $document['terms'] ) ? $document['terms'] : array();
			$clean[] = array(
				'document_id'       => sanitize_text_field( isset( $document['document_id'] ) ? $document['document_id'] : '' ),
				'document_hash'     => sanitize_text_field( isset( $document['document_hash'] ) ? $document['document_hash'] : '' ),
				'request_id'        => absint( isset( $document['request_id'] ) ? $document['request_id'] : 0 ),
				'supplier_id'       => absint( isset( $document['supplier_id'] ) ? $document['supplier_id'] : 0 ),
				'supplier_name'     => sanitize_text_field( isset( $document['supplier_name'] ) ? $document['supplier_name'] : '' ),
				'accepted_user_id'  => absint( isset( $document['accepted_user_id'] ) ? $document['accepted_user_id'] : 0 ),
				'accepted_by'       => sanitize_text_field( isset( $document['accepted_by'] ) ? $document['accepted_by'] : '' ),
				'accepted_utc'      => sanitize_text_field( isset( $document['accepted_utc'] ) ? $document['accepted_utc'] : '' ),
				'snapshot_hash'     => sanitize_text_field( isset( $document['snapshot_hash'] ) ? $document['snapshot_hash'] : '' ),
				'acceptance_source' => sanitize_key( isset( $document['acceptance_source'] ) ? $document['acceptance_source'] : '' ),
				'evidence_reference'=> sanitize_text_field( isset( $document['evidence_reference'] ) ? $document['evidence_reference'] : '' ),
				'terms'              => array(
					'version'          => sanitize_text_field( isset( $terms['version'] ) ? $terms['version'] : '' ),
					'fee_model'        => Hea_Lth_Brokerage_Ledger::sanitize_fee_model( isset( $terms['fee_model'] ) ? $terms['fee_model'] : '' ),
					'rate_bps'         => Hea_Lth_Brokerage_Ledger::bounded_basis_points( isset( $terms['rate_bps'] ) ? $terms['rate_bps'] : 0 ),
					'fixed_fee_ils'    => Hea_Lth_Brokerage_Ledger::bounded_money( isset( $terms['fixed_fee_ils'] ) ? $terms['fixed_fee_ils'] : 0 ),
					'min_fee_ils'      => Hea_Lth_Brokerage_Ledger::bounded_money( isset( $terms['min_fee_ils'] ) ? $terms['min_fee_ils'] : 0 ),
					'attribution_days' => Hea_Lth_Brokerage_Ledger::bounded_days( isset( $terms['attribution_days'] ) ? $terms['attribution_days'] : 0 ),
				),
			);
		}
		return $clean;
	}

	/**
	 * @param mixed $value Raw delivery log.
	 * @return array<int, array<string, mixed>>
	 */
	public static function sanitize_delivery_log( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}
		$clean = array();
		foreach ( array_slice( $value, -100 ) as $entry ) {
			if ( ! is_array( $entry ) ) {
				continue;
			}
			$clean[] = array(
				'document_id' => sanitize_text_field( isset( $entry['document_id'] ) ? $entry['document_id'] : '' ),
				'recipient'   => sanitize_email( isset( $entry['recipient'] ) ? $entry['recipient'] : '' ),
				'role'        => sanitize_key( isset( $entry['role'] ) ? $entry['role'] : '' ),
				'sent'        => ! empty( $entry['sent'] ),
				'at'          => sanitize_text_field( isset( $entry['at'] ) ? $entry['at'] : '' ),
			);
		}
		return $clean;
	}

	/**
	 * @param int    $request_id Request ID.
	 * @param int    $supplier_id Supplier ID.
	 * @param int    $user_id User ID.
	 * @param array  $terms Terms.
	 * @param string $snapshot_hash Acceptance hash.
	 * @param string $accepted_utc Time.
	 * @return array<string, mixed>
	 */
	private static function build_document( $request_id, $supplier_id, $user_id, $terms, $snapshot_hash, $accepted_utc ) {
		$user     = get_userdata( $user_id );
		$base     = array(
			'document_id'       => sprintf( 'HP-BRK-%d-%s-%s', absint( $request_id ), gmdate( 'YmdHis' ), substr( sanitize_text_field( $snapshot_hash ), 0, 10 ) ),
			'request_id'        => absint( $request_id ),
			'supplier_id'       => absint( $supplier_id ),
			'supplier_name'     => sanitize_text_field( get_the_title( $supplier_id ) ),
			'accepted_user_id'  => absint( $user_id ),
			'accepted_by'       => $user instanceof WP_User ? sanitize_text_field( $user->display_name ) : '',
			'accepted_utc'      => sanitize_text_field( $accepted_utc ),
			'snapshot_hash'     => sanitize_text_field( $snapshot_hash ),
			'acceptance_source' => sanitize_key( isset( $terms['acceptance_source'] ) ? $terms['acceptance_source'] : '' ),
			'evidence_reference'=> sanitize_text_field( isset( $terms['evidence_reference'] ) ? $terms['evidence_reference'] : '' ),
			'terms'              => array(
				'version'          => sanitize_text_field( isset( $terms['version'] ) ? $terms['version'] : '' ),
				'fee_model'        => Hea_Lth_Brokerage_Ledger::sanitize_fee_model( isset( $terms['fee_model'] ) ? $terms['fee_model'] : '' ),
				'rate_bps'         => Hea_Lth_Brokerage_Ledger::bounded_basis_points( isset( $terms['rate_bps'] ) ? $terms['rate_bps'] : 0 ),
				'fixed_fee_ils'    => Hea_Lth_Brokerage_Ledger::bounded_money( isset( $terms['fixed_fee_ils'] ) ? $terms['fixed_fee_ils'] : 0 ),
				'min_fee_ils'      => Hea_Lth_Brokerage_Ledger::bounded_money( isset( $terms['min_fee_ils'] ) ? $terms['min_fee_ils'] : 0 ),
				'attribution_days' => Hea_Lth_Brokerage_Ledger::bounded_days( isset( $terms['attribution_days'] ) ? $terms['attribution_days'] : 0 ),
			),
		);
		$base['document_hash'] = hash( 'sha256', wp_json_encode( $base ) );
		return $base;
	}

	/**
	 * @param int   $request_id Request ID.
	 * @param array $document Document.
	 * @return void
	 */
	private static function deliver_document( $request_id, $document ) {
		$recipients = self::recipients( $document );
		$log        = self::sanitize_delivery_log( get_post_meta( $request_id, self::DELIVERY_META, true ) );
		$attempts   = 0;
		foreach ( $log as $entry ) {
			if ( $entry['document_id'] === $document['document_id'] ) {
				$attempts++;
			}
		}
		if ( $attempts >= 10 ) {
			return;
		}

		$html       = self::document_html( $document );
		$temp       = wp_tempnam( sanitize_file_name( $document['document_id'] . '.html' ) );
		$attachment = $temp ? dirname( $temp ) . DIRECTORY_SEPARATOR . sanitize_file_name( $document['document_id'] . '.html' ) : '';
		if ( $temp && ! @rename( $temp, $attachment ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.rename_rename -- Ephemeral file gets a meaningful attachment name.
			$attachment = $temp . '.html';
		}
		if ( $temp && false !== file_put_contents( $attachment, $html ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- Ephemeral mail attachment outside uploads.
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			foreach ( $recipients as $email => $role ) {
				$already_sent = false;
				foreach ( $log as $entry ) {
					if ( $entry['document_id'] === $document['document_id'] && $entry['recipient'] === $email && $entry['sent'] ) {
						$already_sent = true;
						break;
					}
				}
				if ( $already_sent ) {
					continue;
				}
				$sent  = wp_mail( $email, sprintf( 'אישור תנאי תיווך %s | Hea-lth', $document['document_id'] ), $html, $headers, array( $attachment ) );
				$log[] = array( 'document_id' => $document['document_id'], 'recipient' => $email, 'role' => $role, 'sent' => (bool) $sent, 'at' => gmdate( 'c' ) );
			}
			unlink( $attachment ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink -- Removes only the explicit ephemeral attachment.
		}
		if ( $temp && file_exists( $temp ) ) {
			unlink( $temp ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink -- Removes only the explicit ephemeral file.
		}
		update_post_meta( $request_id, self::DELIVERY_META, array_slice( $log, -100 ) );

		$all_sent = (bool) $recipients;
		foreach ( $recipients as $email => $role ) {
			$sent = false;
			foreach ( $log as $entry ) {
				if ( $entry['document_id'] === $document['document_id'] && $entry['recipient'] === $email && $entry['sent'] ) {
					$sent = true;
					break;
				}
			}
			$all_sent = $all_sent && $sent;
		}
		self::record_delivery_audit( $request_id, $document['document_id'], $all_sent );
		if ( ! $all_sent && $attempts < 8 && ! wp_next_scheduled( self::RETRY_HOOK, array( $request_id, $document['document_id'] ) ) ) {
			wp_schedule_single_event( time() + ( 15 * MINUTE_IN_SECONDS ), self::RETRY_HOOK, array( $request_id, $document['document_id'] ) );
		}
	}

	/** @param array $document Document. @return array<string, string> */
	private static function recipients( $document ) {
		$recipients = array();
		$owner      = sanitize_email( (string) get_option( 'admin_email' ) );
		if ( is_email( $owner ) ) {
			$recipients = self::add_recipient( $recipients, $owner, 'owner' );
		}
		$user_ids = Hea_Lth_Supplier_Portal::sanitize_id_list( get_post_meta( (int) $document['supplier_id'], 'hp_account_user_ids', true ) );
		if ( 'supplier_portal' === $document['acceptance_source'] && $document['accepted_user_id'] ) {
			$user_ids[] = absint( $document['accepted_user_id'] );
		}
		foreach ( array_unique( $user_ids ) as $user_id ) {
			$user = get_userdata( $user_id );
			if ( $user instanceof WP_User && is_email( $user->user_email ) ) {
				$recipients = self::add_recipient( $recipients, sanitize_email( $user->user_email ), 'supplier' );
			}
		}
		if ( ! in_array( 'supplier', $recipients, true ) && ! in_array( 'owner_supplier', $recipients, true ) ) {
			$contact = sanitize_email( (string) get_post_meta( (int) $document['supplier_id'], 'hp_contact_email', true ) );
			if ( is_email( $contact ) ) {
				$recipients = self::add_recipient( $recipients, $contact, 'supplier' );
			}
		}
		return $recipients;
	}

	/** @param array<string, string> $recipients Recipients. @param string $email Email. @param string $role Role. @return array<string, string> */
	private static function add_recipient( $recipients, $email, $role ) {
		if ( isset( $recipients[ $email ] ) && $recipients[ $email ] !== $role ) {
			$recipients[ $email ] = 'owner_supplier';
			return $recipients;
		}
		$recipients[ $email ] = $role;
		return $recipients;
	}

	/** @param int $request_id Request ID. @param string $document_id ID. @return array<string, mixed> */
	private static function find_document( $request_id, $document_id ) {
		foreach ( self::sanitize_documents( get_post_meta( $request_id, self::DOCUMENTS_META, true ) ) as $document ) {
			if ( hash_equals( $document['document_id'], $document_id ) && self::document_is_valid( $document ) ) {
				return $document;
			}
		}
		return array();
	}

	/** @param array $document Document. @return bool */
	private static function can_access_document( $document ) {
		if ( current_user_can( 'manage_options' ) || current_user_can( 'edit_post', (int) $document['request_id'] ) ) {
			return true;
		}
		$supplier = Hea_Lth_Supplier_Portal::supplier_for_user();
		return $supplier instanceof WP_Post && (int) $supplier->ID === (int) $document['supplier_id'];
	}

	/** @param array $document Document. @return string */
	private static function document_html( $document ) {
		$summary = Hea_Lth_Brokerage_Ledger::public_terms_summary( $document['terms'] );
		$source  = 'written_external' === $document['acceptance_source'] ? 'אישור כתוב שתועד ב-Hea-lth' : 'אישור בחשבון הספק המאובטח';
		$evidence = '' !== $document['evidence_reference'] ? '<br>אסמכתה: ' . esc_html( $document['evidence_reference'] ) : '';
		return '<!doctype html><html lang="he" dir="rtl"><head><meta charset="utf-8"><title>' . esc_html( $document['document_id'] ) . '</title><style>body{font-family:Arial,sans-serif;color:#123c3a;background:#f5f2e9;margin:0;padding:36px}.document{max-width:760px;margin:auto;background:#fff;padding:42px;border-top:8px solid #0a4d47}h1{font-size:28px;margin:0 0 8px}h2{font-size:19px;margin-top:30px}p,li{line-height:1.75}.terms{background:#fff7df;border:1px solid #ead6a0;padding:18px}.meta{color:#536b68;font-size:13px}.hash{direction:ltr;word-break:break-all;font-family:monospace}.signature{margin-top:36px;border-top:1px solid #ccd7d3;padding-top:22px}@media print{body{background:#fff;padding:0}.document{box-shadow:none}}</style></head><body><article class="document"><p class="meta">Hea-lth · hea-lth.co.il</p><h1>אישור תנאי תיווך ואי־עקיפה</h1><p class="meta">מסמך ' . esc_html( $document['document_id'] ) . '</p><h2>הצדדים וההזדמנות</h2><p><strong>Hea-lth</strong> והספק <strong>' . esc_html( $document['supplier_name'] ) . '</strong> · הזדמנות פרטית #' . esc_html( (string) $document['request_id'] ) . '.</p><h2>התנאים שאושרו</h2><div class="terms"><strong>' . esc_html( $summary ) . '</strong><p>חלון שיוך: ' . esc_html( (string) $document['terms']['attribution_days'] ) . ' ימים ממועד האישור.</p></div><ul><li>העמלה חלה עם סגירת עסקה הנובעת מן ההיכרות שיצרה Hea-lth.</li><li>הספק מתחייב שלא לעקוף את Hea-lth במהלך חלון השיוך.</li><li>הספק יעדכן את Hea-lth בהתקדמות, בהצעה, בסגירה ובשווי העסקה.</li><li>פרטי ההתקשרות של ההזדמנות נשמרים עד להשלמת האישור.</li></ul><div class="signature"><h2>רישום האישור</h2><p>אושר על ידי: <strong>' . esc_html( $document['accepted_by'] ) . '</strong><br>מזהה חשבון: ' . esc_html( (string) $document['accepted_user_id'] ) . '<br>מועד UTC: ' . esc_html( $document['accepted_utc'] ) . '<br>מקור: ' . esc_html( $source ) . $evidence . '</p><p class="meta">גרסת תנאים: ' . esc_html( $document['terms']['version'] ) . '</p><p class="meta hash">Acceptance: ' . esc_html( $document['snapshot_hash'] ) . '<br>Document: ' . esc_html( $document['document_hash'] ) . '</p></div></article></body></html>';
	}

	/** @param array $document Document. @return bool */
	private static function document_is_valid( $document ) {
		$stored = isset( $document['document_hash'] ) ? sanitize_text_field( $document['document_hash'] ) : '';
		if ( '' === $stored ) {
			return false;
		}
		$canonical = $document;
		unset( $canonical['document_hash'] );
		return hash_equals( $stored, hash( 'sha256', wp_json_encode( $canonical ) ) );
	}

	/** @param int $request_id Request ID. @param string $document_id ID. @param bool $sent Sent. @return void */
	private static function record_delivery_audit( $request_id, $document_id, $sent ) {
		$log   = Hea_Lth_Supplier_Portal::sanitize_audit_log( get_post_meta( $request_id, 'hp_request_audit', true ) );
		$log[] = array( 'at' => gmdate( 'c' ), 'event' => $sent ? 'agreement_delivered' : 'agreement_delivery_pending', 'from' => '', 'to' => sanitize_text_field( $document_id ), 'user_id' => get_current_user_id() );
		update_post_meta( $request_id, 'hp_request_audit', array_slice( $log, -100 ) );
	}

	/** @param int $request_id Request ID. @param string $document_id ID. @return string */
	private static function nonce_action( $request_id, $document_id ) {
		return 'hea_lth_brokerage_document_' . absint( $request_id ) . '_' . sanitize_text_field( $document_id );
	}
}
