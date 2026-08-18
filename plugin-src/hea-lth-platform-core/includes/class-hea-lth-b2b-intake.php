<?php
/**
 * Privacy-minimized B2B intake for clinic procurement and supplier onboarding.
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Hea_Lth_B2B_Intake {
	const ACTION          = 'hea_lth_b2b_intake';
	const CONSENT_VERSION = 'b2b-contact-2026-08-13';

	public static function boot() {
		add_action( 'admin_post_' . self::ACTION, array( __CLASS__, 'handle' ) );
		add_action( 'admin_post_nopriv_' . self::ACTION, array( __CLASS__, 'handle' ) );
	}

	public static function handle() {
		$return_url = isset( $_POST['return_url'] ) ? wp_validate_redirect( esc_url_raw( wp_unslash( $_POST['return_url'] ) ), home_url( '/' ) ) : home_url( '/' );
		$nonce      = isset( $_POST['hea_lth_b2b_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['hea_lth_b2b_nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, self::ACTION ) ) {
			self::redirect( $return_url, 'invalid' );
		}

		$honeypot = isset( $_POST['company_website'] ) ? sanitize_text_field( wp_unslash( $_POST['company_website'] ) ) : '';
		$started  = isset( $_POST['form_started'] ) ? absint( wp_unslash( $_POST['form_started'] ) ) : 0;
		if ( '' !== $honeypot || 0 === $started || ( time() - $started ) < 3 ) {
			self::redirect( $return_url, 'invalid' );
		}

		// Rate limit: an anonymous client may create at most 5 requests per
		// 10 minutes; excess attempts are bounced before any post is created.
		$fingerprint = isset( $_SERVER['REMOTE_ADDR'] ) ? md5( (string) sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) ) : 'unknown';
		$rate_key    = 'hea_lth_b2b_rl_' . $fingerprint;
		$rate_hits   = (int) get_transient( $rate_key );
		if ( $rate_hits >= 5 ) {
			self::redirect( $return_url, 'invalid' );
		}
		set_transient( $rate_key, $rate_hits + 1, 10 * MINUTE_IN_SECONDS );

		$type = isset( $_POST['request_type'] ) ? sanitize_key( wp_unslash( $_POST['request_type'] ) ) : '';
		if ( ! in_array( $type, array( 'clinic_quote', 'supplier_join' ), true ) ) {
			self::redirect( $return_url, 'invalid' );
		}

		$contact_name  = self::field( 'contact_name', 100 );
		$contact_email = isset( $_POST['contact_email'] ) ? sanitize_email( wp_unslash( $_POST['contact_email'] ) ) : '';
		$contact_phone = self::field( 'contact_phone', 40 );
		$company_name  = self::field( 'company_name', 140 );
		$company_url   = isset( $_POST['company_url'] ) ? esc_url_raw( wp_unslash( $_POST['company_url'] ) ) : '';
		$city          = self::field( 'city', 80 );
		$project_stage = self::key_field( 'project_stage' );
		$plan_interest = self::key_field( 'plan_interest' );
		$context_slug  = self::key_field( 'context_slug' );
		if ( ! in_array( $project_stage, array( '', 'immediate', 'planning', 'expansion', 'comparison' ), true ) ) {
			$project_stage = '';
		}
		if ( ! in_array( $plan_interest, array( '', 'verified', 'showroom', 'growth' ), true ) ) {
			$plan_interest = '';
		}
		$consent       = isset( $_POST['contact_consent'] ) && 1 === absint( wp_unslash( $_POST['contact_consent'] ) );
		$categories    = isset( $_POST['categories'] ) && is_array( $_POST['categories'] ) ? array_map( 'sanitize_key', wp_unslash( $_POST['categories'] ) ) : array();
		$categories    = array_values( array_filter( array_unique( array_slice( $categories, 0, 12 ) ) ) );
		$equipment     = self::equipment_selection();

		if ( '' === $contact_name || '' === $contact_email || ! is_email( $contact_email ) || '' === $contact_phone || '' === $company_name || ! $consent ) {
			self::redirect( $return_url, 'required' );
		}

		$title = sprintf(
			'%s — %s — %s',
			'clinic_quote' === $type ? 'בקשת רכש למרפאה' : 'בקשת הצטרפות ספק',
			$company_name,
			gmdate( 'Y-m-d H:i' )
		);
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'hp_b2b_request',
				'post_status' => 'private',
				'post_title'  => $title,
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			self::redirect( $return_url, 'error' );
		}

		$meta = array(
			'hp_request_type'    => $type,
			'hp_request_status'  => 'new',
			'hp_contact_name'    => $contact_name,
			'hp_contact_email'   => $contact_email,
			'hp_contact_phone'   => $contact_phone,
			'hp_company_name'    => $company_name,
			'hp_company_url'     => $company_url,
			'hp_city'            => $city,
			'hp_project_stage'   => $project_stage,
			'hp_plan_interest'   => $plan_interest,
			'hp_context_slug'    => $context_slug,
			'hp_categories'      => $categories,
			'hp_equipment_slugs' => $equipment['slugs'],
			'hp_equipment_ids'   => $equipment['ids'],
			'hp_equipment_names' => $equipment['names'],
			'hp_candidate_supplier_ids' => $equipment['supplier_ids'],
			'hp_consent_version' => self::CONSENT_VERSION,
			'hp_created_utc'     => gmdate( 'c' ),
		);

		foreach ( $meta as $key => $value ) {
			update_post_meta( (int) $post_id, $key, $value );
		}

		if ( class_exists( 'Hea_Lth_Metrics' ) ) {
			Hea_Lth_Metrics::increment( 'b2b_submit', $type . '-' . ( $context_slug ? $context_slug : 'general' ) );
		}

		self::notify( (int) $post_id, $meta );
		self::redirect( $return_url, 'received' );
	}

	/**
	 * Resolve submitted slugs to reviewed public equipment and suppliers.
	 * Browser values are hints; canonical records provide stored relationships.
	 *
	 * @return array{slugs: array<int, string>, ids: array<int, int>, names: array<int, string>, supplier_ids: array<int, int>}
	 */
	private static function equipment_selection() {
		$submitted = isset( $_POST['equipment'] ) && is_array( $_POST['equipment'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['equipment'] ) ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified in handle before helper calls.
		$slugs     = array_values( array_filter( array_unique( array_map( 'sanitize_title', array_slice( $submitted, 0, 4 ) ) ) ) );
		$result    = array(
			'slugs'        => array(),
			'ids'          => array(),
			'names'        => array(),
			'supplier_ids' => array(),
		);

		foreach ( $slugs as $slug ) {
			$machine = get_page_by_path( $slug, OBJECT, 'hp_equipment' );
			if ( ! $machine instanceof WP_Post || 'publish' !== $machine->post_status || 'approved' !== get_post_meta( (int) $machine->ID, 'hp_editorial_state', true ) ) {
				continue;
			}

			$result['slugs'][] = $machine->post_name;
			$result['ids'][]   = (int) $machine->ID;
			$result['names'][] = sanitize_text_field( get_the_title( $machine ) );
			$supplier_id       = absint( get_post_meta( (int) $machine->ID, 'hp_supplier_id', true ) );
			if ( $supplier_id > 0 && 'publish' === get_post_status( $supplier_id ) && 'verified' === get_post_meta( $supplier_id, 'hp_public_state', true ) ) {
				$result['supplier_ids'][] = $supplier_id;
			}
		}

		$result['supplier_ids'] = array_values( array_unique( $result['supplier_ids'] ) );
		return $result;
	}

	private static function field( $key, $limit ) {
		$value = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified in handle before helper calls.
		return function_exists( 'mb_substr' ) ? mb_substr( $value, 0, $limit ) : substr( $value, 0, $limit );
	}

	private static function key_field( $key ) {
		return isset( $_POST[ $key ] ) ? sanitize_key( wp_unslash( $_POST[ $key ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified in handle before helper calls.
	}

	private static function notify( $post_id, $meta ) {
		$recipient = sanitize_email( (string) get_option( 'admin_email' ) );
		if ( ! is_email( $recipient ) ) {
			update_post_meta( (int) $post_id, 'hp_mail_result', 'no-recipient' );
			return;
		}

		$subject = sprintf( '[Hea-lth B2B] %s #%d', 'clinic_quote' === $meta['hp_request_type'] ? 'בקשת רכש' : 'ספק חדש', $post_id );
		$body    = implode(
			"\n",
			array(
				'חברה: ' . $meta['hp_company_name'],
				'איש קשר: ' . $meta['hp_contact_name'],
				'טלפון: ' . $meta['hp_contact_phone'],
				'אימייל: ' . $meta['hp_contact_email'],
				'עיר: ' . $meta['hp_city'],
				'אתר חברה: ' . $meta['hp_company_url'],
				'הקשר: ' . $meta['hp_context_slug'],
				'מערכות שנבחרו: ' . implode( ', ', $meta['hp_equipment_names'] ),
				'לצפייה וניהול: ' . admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
			)
		);

		$mailed = wp_mail( $recipient, $subject, $body );
		update_post_meta( (int) $post_id, 'hp_mail_result', $mailed ? 'sent' : 'failed' );
		if ( ! $mailed ) {
			update_option( 'hea_lth_b2b_mail_failures', (int) get_option( 'hea_lth_b2b_mail_failures', 0 ) + 1, false );
		}
	}

	private static function redirect( $return_url, $status ) {
		wp_safe_redirect( add_query_arg( 'request', sanitize_key( $status ), $return_url ) );
		exit;
	}
}
