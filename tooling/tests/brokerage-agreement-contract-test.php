<?php
/**
 * Contract for private brokerage agreement records and delivery.
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
if ( ! defined( 'MINUTE_IN_SECONDS' ) ) {
	define( 'MINUTE_IN_SECONDS', 60 );
}

function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $value ) ); }
function sanitize_email( $value ) { return filter_var( (string) $value, FILTER_SANITIZE_EMAIL ); }
function sanitize_file_name( $value ) { return preg_replace( '/[^a-zA-Z0-9._-]/', '-', (string) $value ); }
function absint( $value ) { return abs( (int) $value ); }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' ); }
function get_the_title( $post_id ) { return 41 === (int) $post_id ? 'חברת בדיקה' : ''; }
function get_userdata( $user_id ) { return new WP_User( (int) $user_id, 'נציג בדיקה', 'supplier@example.test' ); }
function wp_json_encode( $value ) { return json_encode( $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); }
function number_format_i18n( $value ) { return number_format( (float) $value, 0, '.', ',' ); }
function is_email( $value ) { return false !== filter_var( $value, FILTER_VALIDATE_EMAIL ); }
function get_option( $key ) { return 'admin_email' === $key ? 'owner@example.test' : ''; }
function get_current_user_id() { return 5; }
function wp_tempnam( $filename ) { return tempnam( sys_get_temp_dir(), 'hp-agreement-' ); }
function wp_next_scheduled( $hook, $args = array() ) { return false; }
function wp_schedule_single_event( $timestamp, $hook, $args = array() ) { $GLOBALS['hp_scheduled'][] = compact( 'timestamp', 'hook', 'args' ); return true; }

$GLOBALS['hp_agreement_meta'] = array();
$GLOBALS['hp_agreement_mail'] = array();
$GLOBALS['hp_scheduled']      = array();
$GLOBALS['hp_mail_fail']      = array();
function get_post_meta( $post_id, $key, $single = false ) {
	return isset( $GLOBALS['hp_agreement_meta'][ $post_id ][ $key ] ) ? $GLOBALS['hp_agreement_meta'][ $post_id ][ $key ] : '';
}
function update_post_meta( $post_id, $key, $value ) {
	$GLOBALS['hp_agreement_meta'][ $post_id ][ $key ] = $value;
	return true;
}
function wp_mail( $recipient, $subject, $message, $headers = '', $attachments = array() ) {
	$sent = ! in_array( $recipient, $GLOBALS['hp_mail_fail'], true );
	$GLOBALS['hp_agreement_mail'][] = array(
		'recipient'         => $recipient,
		'subject'           => $subject,
		'has_document'      => false !== strpos( $message, 'אישור תנאי תיווך ואי־עקיפה' ),
		'attachment_exists' => 1 === count( $attachments ) && file_exists( $attachments[0] ),
		'sent'              => $sent,
	);
	return $sent;
}

class WP_User {
	public $ID;
	public $display_name;
	public $user_email;
	public function __construct( $id, $name, $email ) { $this->ID = $id; $this->display_name = $name; $this->user_email = $email; }
}

class Hea_Lth_Brokerage_Ledger {
	public static function sanitize_fee_model( $value ) { return in_array( $value, array( 'percent', 'fixed', 'hybrid' ), true ) ? $value : 'percent'; }
	public static function bounded_basis_points( $value ) { return min( 10000, max( 1, absint( $value ) ) ); }
	public static function bounded_money( $value ) { return min( 1000000000, absint( $value ) ); }
	public static function bounded_days( $value ) { return min( 730, max( 1, absint( $value ) ) ); }
	public static function public_terms_summary( $terms ) { return '10% מסכום העסקה, מינימום ₪8,000'; }
}

class Hea_Lth_Supplier_Portal {
	public static function sanitize_id_list( $value ) { return is_array( $value ) ? array_values( array_unique( array_map( 'absint', $value ) ) ) : array(); }
	public static function sanitize_audit_log( $value ) { return is_array( $value ) ? $value : array(); }
}

$root = dirname( __DIR__, 2 );
require_once $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-brokerage-agreement.php';

$terms = array(
	'version'            => 'brokerage-2026-08-13-v1',
	'fee_model'          => 'percent',
	'rate_bps'           => 1000,
	'fixed_fee_ils'      => 0,
	'min_fee_ils'        => 8000,
	'attribution_days'   => 180,
	'acceptance_source'  => 'supplier_portal',
	'evidence_reference' => 'authenticated-account',
);
$builder = new ReflectionMethod( Hea_Lth_Brokerage_Agreement::class, 'build_document' );
$builder->setAccessible( true );
$document = $builder->invoke( null, 7, 41, 5, $terms, str_repeat( 'a', 64 ), '2026-08-13T12:00:00+00:00' );
assert_true( 7 === $document['request_id'] && 41 === $document['supplier_id'], 'Agreement must bind the exact private request and supplier.' );
assert_true( 64 === strlen( $document['document_hash'] ), 'Agreement must have an immutable SHA-256 document hash.' );
assert_true( 1000 === $document['terms']['rate_bps'] && 180 === $document['terms']['attribution_days'], 'Agreement must preserve accepted economics and attribution.' );

$renderer = new ReflectionMethod( Hea_Lth_Brokerage_Agreement::class, 'document_html' );
$renderer->setAccessible( true );
$html = $renderer->invoke( null, $document );
assert_true( false !== strpos( $html, 'אישור תנאי תיווך ואי־עקיפה' ), 'Delivered document must identify its commercial purpose.' );
assert_true( false !== strpos( $html, '10% מסכום העסקה' ), 'Delivered document must state the accepted economics.' );
assert_true( false === strpos( $html, 'buyer_phone' ), 'Agreement document must not expose buyer contact data.' );

$documents = Hea_Lth_Brokerage_Agreement::sanitize_documents( array_fill( 0, 25, $document ) );
assert_true( 20 === count( $documents ), 'Private agreement history must remain bounded.' );
$delivery = Hea_Lth_Brokerage_Agreement::sanitize_delivery_log( array( array( 'document_id' => $document['document_id'], 'recipient' => 'supplier@example.test', 'role' => 'supplier', 'sent' => true, 'at' => '2026-08-13T12:01:00+00:00' ) ) );
assert_true( $delivery[0]['sent'] && 'supplier' === $delivery[0]['role'], 'Delivery evidence must retain recipient role and result.' );

update_post_meta( 7, 'hp_terms_accepted_utc', '2026-08-13T12:00:00+00:00' );
update_post_meta( 41, 'hp_account_user_ids', array( 5 ) );
$delivered = Hea_Lth_Brokerage_Agreement::create_and_deliver( 7, 41, 5, $terms, str_repeat( 'b', 64 ) );
assert_true( 2 === count( $GLOBALS['hp_agreement_mail'] ), 'Agreement must be delivered separately to supplier and owner.' );
$mail_recipients = array_column( $GLOBALS['hp_agreement_mail'], 'recipient' );
sort( $mail_recipients );
assert_true( array( 'owner@example.test', 'supplier@example.test' ) === $mail_recipients, 'Agreement recipients must be the supplier and owner.' );
foreach ( $GLOBALS['hp_agreement_mail'] as $mail ) {
	assert_true( is_string( $mail['recipient'] ) && $mail['has_document'] && $mail['attachment_exists'], 'Each recipient must receive a private attached agreement copy.' );
}
$stored = Hea_Lth_Brokerage_Agreement::latest_document( 7 );
assert_true( $stored['document_hash'] === $delivered['document_hash'], 'The delivered document must match the immutable private record.' );
assert_true( Hea_Lth_Brokerage_Agreement::has_matching_document( 7, str_repeat( 'b', 64 ) ), 'Current acceptance must match its immutable agreement document.' );
assert_true( Hea_Lth_Brokerage_Agreement::is_fully_delivered( 7, str_repeat( 'b', 64 ) ), 'Lead release must require agreement delivery to supplier and owner.' );
$GLOBALS['hp_agreement_meta'][7][Hea_Lth_Brokerage_Agreement::DOCUMENTS_META][0]['terms']['rate_bps'] = 1200;
assert_true( ! Hea_Lth_Brokerage_Agreement::has_matching_document( 7, str_repeat( 'b', 64 ) ), 'A modified agreement record must fail its document hash.' );
$GLOBALS['hp_agreement_meta'][7][Hea_Lth_Brokerage_Agreement::DOCUMENTS_META][0] = $delivered;
$status = Hea_Lth_Brokerage_Agreement::delivery_status( 7, $delivered['document_id'] );
assert_true( $status['supplier'] && $status['owner'], 'Private delivery audit must prove both copies were sent.' );
assert_true( ! $GLOBALS['hp_scheduled'], 'A fully delivered agreement must not schedule a retry.' );

$GLOBALS['hp_mail_fail'] = array( 'supplier@example.test' );
update_post_meta( 8, 'hp_terms_accepted_utc', '2026-08-13T12:30:00+00:00' );
$pending = Hea_Lth_Brokerage_Agreement::create_and_deliver( 8, 41, 5, $terms, str_repeat( 'c', 64 ) );
$pending_status = Hea_Lth_Brokerage_Agreement::delivery_status( 8, $pending['document_id'] );
assert_true( ! $pending_status['supplier'] && $pending_status['owner'], 'A partial delivery must preserve each recipient result independently.' );
assert_true( ! Hea_Lth_Brokerage_Agreement::is_fully_delivered( 8, str_repeat( 'c', 64 ) ), 'A partially delivered agreement must keep contact details held.' );
assert_true( 1 === count( $GLOBALS['hp_scheduled'] ), 'A partial delivery must schedule a bounded retry.' );
$GLOBALS['hp_mail_fail'] = array();
Hea_Lth_Brokerage_Agreement::retry_delivery( 8, $pending['document_id'] );
$retried_status = Hea_Lth_Brokerage_Agreement::delivery_status( 8, $pending['document_id'] );
assert_true( $retried_status['supplier'] && $retried_status['owner'], 'A retry must complete the missing supplier delivery without losing owner evidence.' );
assert_true( Hea_Lth_Brokerage_Agreement::is_fully_delivered( 8, str_repeat( 'c', 64 ) ), 'Successful retry must satisfy the document delivery release gate.' );

$external = $document;
$external['acceptance_source']  = 'written_external';
$external['accepted_user_id']   = 99;
$external['supplier_id']        = 42;
update_post_meta( 42, 'hp_account_user_ids', array() );
update_post_meta( 42, 'hp_contact_email', 'supplier-contact@example.test' );
$recipient_builder = new ReflectionMethod( Hea_Lth_Brokerage_Agreement::class, 'recipients' );
$recipient_builder->setAccessible( true );
$external_recipients = $recipient_builder->invoke( null, $external );
assert_true( isset( $external_recipients['supplier-contact@example.test'] ), 'Externally recorded acceptance must deliver to the supplier contact, not the recording administrator.' );

$source   = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-brokerage-agreement.php' );
$template = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/page-templates/template-supplier-portal.php' );
foreach ( array( 'show_in_rest' => false, 'wp_mail', 'wp_tempnam', 'Content-Disposition: attachment', 'wp_schedule_single_event', 'current_user_can', 'supplier_for_user' ) as $needle => $unused ) {
	if ( is_int( $needle ) ) {
		$needle = $unused;
	}
	assert_true( false !== strpos( $source, (string) $needle ), 'Agreement privacy or delivery control missing: ' . $needle );
}
assert_true( false !== strpos( $template, 'הורדת מסמך האישור' ), 'Authenticated supplier portal must expose the private agreement copy.' );

$public_templates = glob( $root . '/theme-src/hea-lth-portal/page-templates/*.php' );
foreach ( $public_templates as $path ) {
	if ( basename( $path ) === 'template-supplier-portal.php' ) {
		continue;
	}
	assert_true( false === strpos( (string) file_get_contents( $path ), 'Hea_Lth_Brokerage_Agreement' ), 'Agreement controls reached a public content template: ' . basename( $path ) );
}

echo "Brokerage agreement contract passed.\n";
