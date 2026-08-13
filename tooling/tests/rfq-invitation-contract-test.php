<?php
/**
 * Contract for anonymized RFQ invitations and controlled supplier award.
 */

declare( strict_types = 1 );

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . "\n" );
		exit( 1 );
	}
}

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $value ) ); }
function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); }
function absint( $value ) { return abs( (int) $value ); }

$root = dirname( __DIR__, 2 );
require_once $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-rfq-invitations.php';

assert_true( 'invited' === Hea_Lth_RFQ_Invitations::sanitize_status( 'unexpected' ), 'Unknown RFQ states must fail closed to invited.' );
assert_true( 'מעוניין' === Hea_Lth_RFQ_Invitations::status_label( 'interested' ), 'Supplier interest requires a clear private status.' );

$source      = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-rfq-invitations.php' );
$portal      = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/page-templates/template-supplier-portal.php' );
$supplier    = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-supplier-portal.php' );
$bootstrap   = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/hea-lth-platform-core.php' );

foreach ( array( "'public'              => false", "'publicly_queryable'  => false", "'show_in_rest'        => false" ) as $privacy_gate ) {
	assert_true( false !== strpos( $source, $privacy_gate ), 'RFQ invitation records must remain private: ' . $privacy_gate );
}
foreach ( array( 'check_admin_referer', 'supplier_for_user', "array( 'interested', 'declined'", "'interested' !==", 'is_financially_locked', 'invalidate_terms', "'hp_lead_release_state', 'held'", "'awarded'", "'withdrawn'" ) as $workflow_gate ) {
	assert_true( false !== strpos( $source, $workflow_gate ), 'RFQ authorization or award gate missing: ' . $workflow_gate );
}
assert_true( false !== strpos( $source, "'buyer'      => 'מרפאה או גורם מקצועי מאומת בישראל'" ), 'Invitation view must use an anonymized buyer label.' );
assert_true( false === strpos( $source, "get_post_meta( \$request_id, 'hp_contact_" ), 'RFQ invitations must never read buyer contact metadata.' );
assert_true( false === strpos( $source, "get_post_meta( \$request_id, 'hp_company_name'" ), 'RFQ invitations must never read the buyer company name.' );
assert_true( false === strpos( $source, "get_post_meta( \$request_id, 'hp_city'" ), 'RFQ invitations must never read the buyer location.' );
$mail_start = strpos( $source, '$body      = implode(' );
$mail_end   = false !== $mail_start ? strpos( $source, 'foreach ( $recipients as $recipient )', $mail_start ) : false;
$mail_body  = false !== $mail_start && false !== $mail_end ? substr( $source, $mail_start, $mail_end - $mail_start ) : '';
assert_true( '' !== $mail_body && false === strpos( $mail_body, '10%' ) && false === strpos( $mail_body, 'תנאי התיווך' ) && false === strpos( $mail_body, 'עמלה' ), 'Initial RFQ invitation delivery must not contain brokerage economics.' );
assert_true( false !== strpos( $source, "hash( 'sha256', strtolower( \$recipient ) )" ), 'Invitation delivery logs must hash recipient addresses.' );
assert_true( false !== strpos( $source, 'MAX_ATTEMPTS' ) && false !== strpos( $source, 'wp_schedule_single_event' ), 'Invitation delivery retries must be bounded.' );
assert_true( false !== strpos( $source, "update_post_meta( \$existing, 'hp_rfq_delivery_log', array() )" ), 'A deliberate reinvite must create fresh delivery evidence.' );

assert_true( false !== strpos( $portal, 'Hea_Lth_RFQ_Invitations::invitation_view' ), 'The authenticated supplier portal must use the anonymized invitation view.' );
assert_true( false !== strpos( $portal, 'hea_lth_rfq_response' ) && false !== strpos( $portal, 'hea_lth_rfq_nonce' ), 'Supplier responses require the private handler and nonce.' );
assert_true( false !== strpos( $bootstrap, "class-hea-lth-rfq-invitations.php" ) && false !== strpos( $bootstrap, 'Hea_Lth_RFQ_Invitations::boot()' ), 'The private RFQ workflow must boot with the platform plugin.' );

assert_true( false !== strpos( $supplier, "'company'    => \$released ?" ), 'Assigned opportunities must keep buyer identity anonymous until release.' );
assert_true( false !== strpos( $supplier, "'city'       => \$released ?" ), 'Assigned opportunities must keep buyer location anonymous until release.' );
assert_true( false !== strpos( $supplier, 'get_password_reset_key' ), 'Supplier activation must use a one-time password setup link.' );
assert_true( false === strpos( $supplier, 'user_pass' . "\n" ), 'Supplier activation must never include a password in an email body.' );
assert_true( false !== strpos( $supplier, "hash( 'sha256', strtolower( \$email ) )" ), 'Account activation delivery must hash the recipient address.' );

echo "Private RFQ invitation and account activation contract passed.\n";
