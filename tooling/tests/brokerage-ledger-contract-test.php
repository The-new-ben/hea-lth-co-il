<?php
/**
 * Contract for brokerage acceptance, revenue calculation, and invoice state.
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
if ( ! defined( 'DAY_IN_SECONDS' ) ) {
	define( 'DAY_IN_SECONDS', 86400 );
}
if ( ! function_exists( 'sanitize_key' ) ) {
	function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $value ) ); }
}
if ( ! function_exists( 'absint' ) ) {
	function absint( $value ) { return abs( (int) $value ); }
}
if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); }
}
if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $value ) { return json_encode( $value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); }
}
if ( ! function_exists( 'number_format_i18n' ) ) {
	function number_format_i18n( $value ) { return number_format( (float) $value, 0, '.', ',' ); }
}

$GLOBALS['hea_lth_ledger_meta'] = array();
function get_post_meta( $post_id, $key, $single = false ) {
	return isset( $GLOBALS['hea_lth_ledger_meta'][ $post_id ][ $key ] ) ? $GLOBALS['hea_lth_ledger_meta'][ $post_id ][ $key ] : '';
}
function update_post_meta( $post_id, $key, $value ) {
	$GLOBALS['hea_lth_ledger_meta'][ $post_id ][ $key ] = $value;
	return true;
}
function delete_post_meta( $post_id, $key ) {
	unset( $GLOBALS['hea_lth_ledger_meta'][ $post_id ][ $key ] );
	return true;
}
function get_current_user_id() { return 99; }

class Hea_Lth_Supplier_Portal {
	public static function sanitize_audit_log( $value ) { return is_array( $value ) ? $value : array(); }
}

class Hea_Lth_Brokerage_Agreement {
	public static function create_and_deliver( $request_id, $supplier_id, $user_id, $terms, $snapshot_hash ) { return array(); }
	public static function has_matching_document( $request_id, $snapshot_hash ) { return true; }
	public static function is_fully_delivered( $request_id, $snapshot_hash ) { return true; }
}

$root = dirname( __DIR__, 2 );
require_once $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-brokerage-ledger.php';

assert_true( 10000 === Hea_Lth_Brokerage_Ledger::calculate_commission( 100000, 'percent', 1000, 0, 8000 ), '10% commission calculation failed.' );
assert_true( 8000 === Hea_Lth_Brokerage_Ledger::calculate_commission( 50000, 'percent', 1000, 0, 8000 ), 'Minimum commission must apply.' );
assert_true( 12000 === Hea_Lth_Brokerage_Ledger::calculate_commission( 50000, 'fixed', 1000, 12000, 8000 ), 'Fixed commission calculation failed.' );
assert_true( 12000 === Hea_Lth_Brokerage_Ledger::calculate_commission( 50000, 'hybrid', 1000, 12000, 8000 ), 'Hybrid commission must choose the highest fee.' );
assert_true( 1000 === Hea_Lth_Brokerage_Ledger::percent_to_basis_points( '10' ), 'Percentage must convert to basis points.' );
assert_true( '10% מסכום העסקה או ₪15,000 — הגבוה מביניהם' === Hea_Lth_Brokerage_Ledger::public_terms_summary( array( 'fee_model' => 'hybrid', 'rate_bps' => 1000, 'fixed_fee_ils' => 12000, 'min_fee_ils' => 15000 ) ), 'Hybrid public terms must disclose the actual highest fixed floor.' );

$request_id = 7;
update_post_meta( $request_id, 'hp_assigned_supplier_id', 41 );
update_post_meta( $request_id, 'hp_terms_status', 'offered' );
update_post_meta( $request_id, 'hp_terms_version', Hea_Lth_Brokerage_Ledger::TERMS_VERSION );
update_post_meta( $request_id, 'hp_fee_model', 'percent' );
update_post_meta( $request_id, 'hp_commission_rate_bps', 1000 );
update_post_meta( $request_id, 'hp_min_fee_ils', 8000 );
update_post_meta( $request_id, 'hp_attribution_days', 180 );
$accept = new ReflectionMethod( Hea_Lth_Brokerage_Ledger::class, 'record_acceptance' );
$accept->setAccessible( true );
$accept->invoke( null, $request_id, 41, 5, 'supplier_portal', 'authenticated-account' );
assert_true( Hea_Lth_Brokerage_Ledger::can_release( $request_id ), 'A complete authenticated acceptance fingerprint must unlock the release gate.' );
update_post_meta( $request_id, 'hp_terms_version', 'brokerage-legacy-v0' );
assert_true( ! Hea_Lth_Brokerage_Ledger::can_release( $request_id ), 'An acceptance for an obsolete contract version must not unlock a lead.' );
update_post_meta( $request_id, 'hp_terms_version', Hea_Lth_Brokerage_Ledger::TERMS_VERSION );
update_post_meta( $request_id, 'hp_commission_rate_bps', 1200 );
assert_true( ! Hea_Lth_Brokerage_Ledger::can_release( $request_id ), 'Changing accepted economics must invalidate the release fingerprint.' );

assert_true( 'not_ready' === Hea_Lth_Brokerage_Ledger::next_invoice_state( 'not_ready', 'issued', false ), 'An unready deal cannot issue an invoice.' );
assert_true( 'ready' === Hea_Lth_Brokerage_Ledger::next_invoice_state( 'not_ready', 'paid', true ), 'A newly ready deal must not skip to paid.' );
assert_true( 'issued' === Hea_Lth_Brokerage_Ledger::next_invoice_state( 'ready', 'issued', true ), 'A ready deal may become issued.' );
assert_true( 'paid' === Hea_Lth_Brokerage_Ledger::next_invoice_state( 'issued', 'paid', true ), 'An issued invoice may become paid.' );
assert_true( 'paid' === Hea_Lth_Brokerage_Ledger::next_invoice_state( 'paid', 'cancelled', false ), 'A paid invoice is immutable.' );

$ledger   = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-brokerage-ledger.php' );
$supplier = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-supplier-portal.php' );
$template = (string) file_get_contents( $root . '/theme-src/hea-lth-portal/page-templates/template-supplier-portal.php' );

foreach ( array( 'hp_terms_snapshot_hash', 'hp_attribution_expires_utc', 'hp_closed_deal_value_ils', 'hp_commission_due_ils', 'hp_invoice_state' ) as $field ) {
	assert_true( false !== strpos( $ledger, $field ), 'Ledger field missing: ' . $field );
}
assert_true( false !== strpos( $ledger, 'hash_equals' ), 'Lead release must validate the immutable acceptance fingerprint.' );
assert_true( false !== strpos( $ledger, 'is_fully_delivered' ), 'Lead release must require an agreement copy delivered to supplier and owner.' );
assert_true( false !== strpos( $ledger, 'terms_changed' ), 'Accepted terms must reopen after an economic change.' );
assert_true( false !== strpos( $ledger, 'is_financially_locked' ), 'Issued and paid records must lock supplier attribution and economics.' );
assert_true( false !== strpos( $supplier, 'Hea_Lth_Brokerage_Ledger::can_release' ), 'Supplier contact access must pass the terms gate.' );
assert_true( false !== strpos( $template, 'hea_lth_accept_brokerage_terms' ), 'Supplier portal must provide recorded terms acceptance.' );
assert_true( false !== strpos( $template, 'terms_confirmed' ), 'Terms acceptance must require an affirmative checkbox.' );

foreach ( array( 'canonical', 'cannibalization', 'search intent', 'deployment id', 'release state' ) as $internal_term ) {
	assert_true( false === stripos( $template, $internal_term ), 'Internal project language reached the supplier portal: ' . $internal_term );
}

echo "Brokerage ledger contract passed.\n";
