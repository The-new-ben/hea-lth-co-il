<?php
/**
 * Source contract for the internal lead-route eligibility resolver.
 *
 * The resolver is deliberately not a public form endpoint. It must never take
 * visitor data, use sponsorship to override relevance, or accept a recipient
 * that is not both published and verified.
 */

declare( strict_types = 1 );

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, $message . "\n" );
		exit( 1 );
	}
}

$root   = dirname( __DIR__, 2 );
$source = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/includes/class-hea-lth-lead-route-resolver.php' );
$entry  = (string) file_get_contents( $root . '/plugin-src/hea-lth-platform-core/hea-lth-platform-core.php' );

assert_true( false !== strpos( $entry, 'class-hea-lth-lead-route-resolver.php' ), 'Plugin entry must load the lead-route resolver.' );
assert_true( false !== strpos( $source, "const POST_TYPE = 'hp_lead_route'" ), 'Resolver must own a dedicated internal route type.' );
assert_true( false !== strpos( $source, "add_action( 'admin_menu'" ), 'Resolver must register its audit screen through the admin-menu hook.' );
assert_true( false !== strpos( $source, 'add_submenu_page(' ), 'Resolver must expose an internal route-health audit screen.' );
assert_true( false !== strpos( $source, "'manage_options'" ), 'Route-health audit must require an administrator capability.' );
assert_true( false !== strpos( $source, "current_user_can( 'manage_options' )" ), 'Route-health audit renderer must verify the administrator capability.' );
assert_true( false !== strpos( $source, 'get_route_audit_report' ), 'Resolver must expose a non-PII configuration-health report.' );
assert_true( false !== strpos( $source, "'public'             => false" ), 'Route configuration must not become public content.' );
assert_true( false !== strpos( $source, "'publicly_queryable' => false" ), 'Route configuration must not be publicly queryable.' );
assert_true( false !== strpos( $source, "'show_in_rest'       => false" ), 'Route configuration must not expose a REST surface.' );
assert_true( false === strpos( $source, 'register_rest_route' ), 'Lead resolver must not expose a public intake route.' );
assert_true( false !== strpos( $source, "'hp_route_state'" ), 'Route state must be explicit.' );
assert_true( false !== strpos( $source, "'hp_route_capacity_state'" ), 'Recipient capacity must be explicit.' );
assert_true( false !== strpos( $source, "'hp_route_recipient_id'" ), 'Route must reference an explicit recipient.' );
assert_true( false !== strpos( $source, "'hp_route_disclosure_version'" ), 'Sponsored routes must carry an explicit disclosure version.' );
assert_true( false !== strpos( $source, "'body_region' => 'hp_body_region'" ), 'Resolver must match body-region context.' );
assert_true( false !== strpos( $source, "'value' => 'active'" ), 'Resolver must require active route state.' );
assert_true( false !== strpos( $source, "'value' => 'accepting'" ), 'Resolver must require accepting capacity.' );
assert_true( false !== strpos( $source, "'verified' === get_post_meta" ), 'Resolver must require a verified recipient.' );
assert_true( false === strpos( $source, "'sponsorship' =>" ), 'Resolver output must not expose sponsorship state.' );
assert_true( false !== strpos( $source, 'does not accept visitor contact data' ), 'Resolver boundary must explicitly exclude visitor data.' );

echo "Lead route resolver contract passed.\n";
