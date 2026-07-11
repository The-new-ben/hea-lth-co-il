<?php
/**
 * Internal, non-PII lead-route eligibility resolver.
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stores recipient routing eligibility separately from public profiles and
 * resolves only verified, active, capacity-accepting recipients.
 *
 * This class does not accept visitor contact data, symptom information,
 * documents, payments, or consent. A future consent-first intake service may
 * call resolve() only after its CRM, retention, and consent gates are approved.
 */
final class Hea_Lth_Lead_Route_Resolver {
	/**
	 * Private route configuration type.
	 */
	const POST_TYPE = 'hp_lead_route';

	/**
	 * Register internal model hooks.
	 *
	 * @return void
	 */
	public static function boot() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ), 15 );
		add_action( 'init', array( __CLASS__, 'register_metadata' ), 20 );
		add_action( 'admin_menu', array( __CLASS__, 'register_admin_page' ) );
	}

	/**
	 * Register an admin-only route configuration record.
	 *
	 * It has no rewrite, public query, archive, or REST surface. A route is a
	 * routing instruction, not public content and not a billable lead record.
	 *
	 * @return void
	 */
	public static function register_post_type() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels' => array(
					'name'          => __( 'נתיבי פנייה', 'hea-lth-platform-core' ),
					'singular_name' => __( 'נתיב פנייה', 'hea-lth-platform-core' ),
					'add_new_item'  => __( 'הוספת נתיב פנייה', 'hea-lth-platform-core' ),
					'edit_item'     => __( 'עריכת נתיב פנייה', 'hea-lth-platform-core' ),
				),
				'description'        => __( 'הגדרות פנימיות להתאמת פניות לרשומות מאומתות.', 'hea-lth-platform-core' ),
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_rest'       => false,
				'has_archive'        => false,
				'rewrite'            => false,
				'query_var'          => false,
				'menu_icon'          => 'dashicons-randomize',
				'supports'           => array( 'title', 'revisions', 'custom-fields' ),
				'map_meta_cap'       => true,
			)
		);

		foreach ( array( 'hp_specialty', 'hp_region', 'hp_service_type', 'hp_body_region' ) as $taxonomy ) {
			register_taxonomy_for_object_type( $taxonomy, self::POST_TYPE );
		}
	}

	/**
	 * Register typed configuration metadata. No field stores visitor data.
	 *
	 * @return void
	 */
	public static function register_metadata() {
		self::register_route_meta( 'hp_route_state', 'string', 'draft', array( __CLASS__, 'sanitize_route_state' ) );
		self::register_route_meta( 'hp_route_recipient_id', 'integer', 0, 'absint' );
		self::register_route_meta( 'hp_route_capacity_state', 'string', 'unavailable', array( __CLASS__, 'sanitize_capacity_state' ) );
		self::register_route_meta( 'hp_route_priority', 'integer', 100, array( __CLASS__, 'sanitize_priority' ) );
		self::register_route_meta( 'hp_route_sponsorship_state', 'string', 'organic', array( __CLASS__, 'sanitize_sponsorship_state' ) );
		self::register_route_meta( 'hp_route_disclosure_version', 'string', '', 'sanitize_text_field' );
		self::register_route_meta( 'hp_route_consent_version', 'string', '', 'sanitize_text_field' );
		self::register_route_meta( 'hp_route_last_reviewed', 'string', '', array( 'Hea_Lth_Platform_Core', 'sanitize_iso_date' ) );
	}

	/**
	 * Register one typed, admin-only metadata field.
	 *
	 * @param string          $key Metadata key.
	 * @param string          $type REST schema type.
	 * @param mixed           $default Default value.
	 * @param callable|string $sanitizer Sanitizer.
	 * @return void
	 */
	private static function register_route_meta( $key, $type, $default, $sanitizer ) {
		register_post_meta(
			self::POST_TYPE,
			$key,
			array(
				'single'            => true,
				'type'              => $type,
				'default'           => $default,
				'sanitize_callback' => $sanitizer,
				'auth_callback'     => array( 'Hea_Lth_Platform_Core', 'can_edit_post_meta' ),
				'show_in_rest'      => false,
			)
		);
	}

	/**
	 * Register the internal routing-health screen below the route configuration
	 * post type. The page is not a public report and never receives visitor
	 * data. It makes configuration gaps visible before an intake system exists.
	 *
	 * @return void
	 */
	public static function register_admin_page() {
		add_submenu_page(
			'edit.php?post_type=' . self::POST_TYPE,
			__( 'ביקורת נתיבי פנייה', 'hea-lth-platform-core' ),
			__( 'ביקורת נתיבים', 'hea-lth-platform-core' ),
			'manage_options',
			'hea-lth-lead-routing-audit',
			array( __CLASS__, 'render_admin_page' )
		);
	}

	/**
	 * Render a restricted operational report for route configuration health.
	 *
	 * No contact details, health information, lead contents, CRM identifiers,
	 * or sponsorship priority are rendered here. The report only assesses
	 * whether an internal route could be safely eligible for a future,
	 * consent-first intake handoff.
	 *
	 * @return void
	 */
	public static function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'אין הרשאה לצפות בביקורת נתיבי הפנייה.', 'hea-lth-platform-core' ) );
		}

		$report = self::get_route_audit_report();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'ביקורת נתיבי פנייה', 'hea-lth-platform-core' ); ?></h1>
			<p><?php esc_html_e( 'מסך פנימי זה בודק הגדרות, אימות, קיבולת, הסכמה ותאריך בקרה. הוא אינו מציג פניות, פרטי מטופלים או נתוני CRM.', 'hea-lth-platform-core' ); ?></p>

			<ul class="subsubsub" aria-label="<?php esc_attr_e( 'סיכום נתיבי פנייה', 'hea-lth-platform-core' ); ?>">
				<li><?php esc_html_e( 'מוכנים', 'hea-lth-platform-core' ); ?>: <strong><?php echo esc_html( (string) $report['summary']['ready'] ); ?></strong> | </li>
				<li><?php esc_html_e( 'דורשים בדיקה', 'hea-lth-platform-core' ); ?>: <strong><?php echo esc_html( (string) $report['summary']['needs_review'] ); ?></strong> | </li>
				<li><?php esc_html_e( 'חסומים', 'hea-lth-platform-core' ); ?>: <strong><?php echo esc_html( (string) $report['summary']['blocked'] ); ?></strong></li>
			</ul>

			<table class="widefat fixed striped" aria-label="<?php esc_attr_e( 'טבלת ביקורת נתיבי פנייה', 'hea-lth-platform-core' ); ?>">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'נתיב', 'hea-lth-platform-core' ); ?></th>
						<th scope="col"><?php esc_html_e( 'מצב', 'hea-lth-platform-core' ); ?></th>
						<th scope="col"><?php esc_html_e( 'בדיקה אחרונה', 'hea-lth-platform-core' ); ?></th>
						<th scope="col"><?php esc_html_e( 'ממצאים', 'hea-lth-platform-core' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $report['entries'] ) ) : ?>
						<tr><td colspan="4"><?php esc_html_e( 'לא הוגדרו עדיין נתיבי פנייה.', 'hea-lth-platform-core' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $report['entries'] as $entry ) : ?>
							<?php $edit_url = get_edit_post_link( $entry['route_id'], '' ); ?>
							<tr>
								<td>
									<?php if ( $edit_url ) : ?>
										<a href="<?php echo esc_url( $edit_url ); ?>"><?php echo esc_html( $entry['title'] ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $entry['title'] ); ?>
									<?php endif; ?>
								</td>
								<td><?php echo esc_html( self::audit_status_label( $entry['status'] ) ); ?></td>
								<td><?php echo esc_html( $entry['last_reviewed'] ? $entry['last_reviewed'] : __( 'לא הוגדר', 'hea-lth-platform-core' ) ); ?></td>
								<td><?php echo esc_html( implode( ', ', $entry['issues'] ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Return a deterministic health report for internal route configuration.
	 *
	 * The report intentionally omits recipient contact details, sponsorship
	 * priority, visitor identity, lead contents, and CRM identifiers.
	 *
	 * @param int $limit Maximum number of route records to inspect.
	 * @return array{summary:array{total:int,ready:int,needs_review:int,blocked:int},entries:array<int,array{route_id:int,title:string,status:string,last_reviewed:string,issues:array<int,string>}>}
	 */
	public static function get_route_audit_report( $limit = 200 ) {
		$query = new WP_Query(
			array(
				'post_type'              => self::POST_TYPE,
				'post_status'            => array( 'publish', 'draft', 'pending', 'private' ),
				'posts_per_page'         => min( 500, max( 1, absint( $limit ) ) ),
				'orderby'                => 'modified',
				'order'                  => 'DESC',
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_meta_cache' => true,
			)
		);

		$report = array(
			'summary' => array(
				'total'        => 0,
				'ready'        => 0,
				'needs_review' => 0,
				'blocked'      => 0,
			),
			'entries' => array(),
		);
		$now = time();

		foreach ( $query->posts as $route ) {
			if ( ! $route instanceof WP_Post ) {
				continue;
			}

			$entry = self::audit_route( $route, $now );
			$report['summary']['total']++;
			$report['summary'][ $entry['status'] ]++;
			$report['entries'][] = $entry;
		}

		return $report;
	}

	/**
	 * Audit one internal route without resolving or sending a lead.
	 *
	 * @param WP_Post $route Route configuration record.
	 * @param int     $now Current Unix timestamp for deterministic checks.
	 * @return array{route_id:int,title:string,status:string,last_reviewed:string,issues:array<int,string>}
	 */
	private static function audit_route( $route, $now ) {
		$issues        = array();
		$blocked       = false;
		$state         = (string) get_post_meta( $route->ID, 'hp_route_state', true );
		$capacity      = (string) get_post_meta( $route->ID, 'hp_route_capacity_state', true );
		$recipient_id  = absint( get_post_meta( $route->ID, 'hp_route_recipient_id', true ) );
		$recipient     = $recipient_id ? get_post( $recipient_id ) : null;
		$consent       = (string) get_post_meta( $route->ID, 'hp_route_consent_version', true );
		$reviewed      = (string) get_post_meta( $route->ID, 'hp_route_last_reviewed', true );
		$sponsorship   = (string) get_post_meta( $route->ID, 'hp_route_sponsorship_state', true );
		$disclosure    = (string) get_post_meta( $route->ID, 'hp_route_disclosure_version', true );
		$is_active     = 'active' === $state;

		if ( 'publish' !== $route->post_status ) {
			$issues[] = __( 'רשומת הנתיב אינה מפורסמת', 'hea-lth-platform-core' );
		}

		if ( ! $is_active ) {
			$issues[] = __( 'הנתיב אינו פעיל', 'hea-lth-platform-core' );
		}

		if ( $is_active && ! self::is_eligible_recipient( $recipient ) ) {
			$issues[] = __( 'חסר נמען מאומת ומפורסם', 'hea-lth-platform-core' );
			$blocked  = true;
		}

		if ( $is_active && 'accepting' !== $capacity ) {
			$issues[] = __( 'הנמען אינו מסומן כמקבל פניות', 'hea-lth-platform-core' );
			$blocked  = true;
		}

		if ( $is_active && '' === $consent ) {
			$issues[] = __( 'חסרה גרסת הסכמה', 'hea-lth-platform-core' );
		}

		if ( $is_active && ! self::is_fresh_review_date( $reviewed, $now ) ) {
			$issues[] = __( 'נדרשת בדיקת נתיב עדכנית', 'hea-lth-platform-core' );
		}

		if ( $is_active && 'disclosed-sponsored' === $sponsorship && '' === $disclosure ) {
			$issues[] = __( 'חסרה גרסת גילוי מסחרי', 'hea-lth-platform-core' );
		}

		$status = $blocked ? 'blocked' : ( $issues ? 'needs_review' : 'ready' );

		return array(
			'route_id'       => (int) $route->ID,
			'title'          => (string) $route->post_title,
			'status'         => $status,
			'last_reviewed'  => $reviewed,
			'issues'         => $issues ? $issues : array( __( 'מוכן לבדיקת מערכת קליטה מאושרת', 'hea-lth-platform-core' ) ),
		);
	}

	/**
	 * A route review is current for 90 days and cannot be dated in the future.
	 *
	 * @param string $date ISO date.
	 * @param int    $now Current Unix timestamp.
	 * @return bool
	 */
	private static function is_fresh_review_date( $date, $now ) {
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
			return false;
		}

		$timestamp = strtotime( $date . ' 00:00:00 UTC' );
		if ( false === $timestamp || $timestamp > $now ) {
			return false;
		}

		return ( $now - $timestamp ) <= ( 90 * 24 * 60 * 60 );
	}

	/**
	 * Return a concise Hebrew status label for the restricted admin report.
	 *
	 * @param string $status Internal status key.
	 * @return string
	 */
	private static function audit_status_label( $status ) {
		$labels = array(
			'ready'        => __( 'מוכן', 'hea-lth-platform-core' ),
			'needs_review' => __( 'דורש בדיקה', 'hea-lth-platform-core' ),
			'blocked'      => __( 'חסום', 'hea-lth-platform-core' ),
		);

		return isset( $labels[ $status ] ) ? $labels[ $status ] : __( 'לא ידוע', 'hea-lth-platform-core' );
	}

	/**
	 * Resolve eligible internal recipient routes for a public discovery context.
	 *
	 * Sponsorship state is deliberately not returned or used as a relevance
	 * override. A future commercial policy may choose among already equivalent,
	 * verified matches only after it records a disclosure decision elsewhere.
	 *
	 * @param array $context specialty, region, service, and body_region slugs.
	 * @param int   $limit Maximum internal route candidates to return.
	 * @return array<int, array{route_id:int,recipient_id:int,recipient_type:string}>
	 */
	public static function resolve( $context, $limit = 12 ) {
		$context = self::sanitize_context( $context );
		$limit   = min( 24, max( 1, absint( $limit ) ) );
		$args    = array(
			'post_type'              => self::POST_TYPE,
			'post_status'            => 'publish',
			'posts_per_page'         => $limit,
			'orderby'                => 'meta_value_num',
			'order'                  => 'ASC',
			'meta_key'               => 'hp_route_priority',
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => true,
			'meta_query'             => array(
				'relation' => 'AND',
				array(
					'key'   => 'hp_route_state',
					'value' => 'active',
				),
				array(
					'key'   => 'hp_route_capacity_state',
					'value' => 'accepting',
				),
			),
		);

		$tax_query = self::build_tax_query( $context );
		if ( $tax_query ) {
			$args['tax_query'] = $tax_query;
		}

		$query  = new WP_Query( $args );
		$routes = array();

		foreach ( $query->posts as $route ) {
			$recipient_id = absint( get_post_meta( $route->ID, 'hp_route_recipient_id', true ) );
			$recipient    = $recipient_id ? get_post( $recipient_id ) : null;

			if ( ! self::is_eligible_recipient( $recipient ) ) {
				continue;
			}

			$routes[] = array(
				'route_id'       => (int) $route->ID,
				'recipient_id'   => (int) $recipient->ID,
				'recipient_type' => (string) $recipient->post_type,
			);
		}

		return $routes;
	}

	/**
	 * Normalize discovery filters to the controlled taxonomy contract.
	 *
	 * @param mixed $context Raw context.
	 * @return array<string, string>
	 */
	private static function sanitize_context( $context ) {
		if ( ! is_array( $context ) ) {
			return array();
		}

		$filters = array();
		foreach ( array( 'specialty', 'region', 'service', 'body_region' ) as $key ) {
			if ( ! isset( $context[ $key ] ) || is_array( $context[ $key ] ) ) {
				continue;
			}

			$value = sanitize_title( (string) $context[ $key ] );
			if ( '' !== $value ) {
				$filters[ $key ] = $value;
			}
		}

		return $filters;
	}

	/**
	 * Map public discovery keys to internal taxonomies.
	 *
	 * @param array<string, string> $context Controlled context filters.
	 * @return array
	 */
	private static function build_tax_query( $context ) {
		$map       = array(
			'specialty'  => 'hp_specialty',
			'region'     => 'hp_region',
			'service'    => 'hp_service_type',
			'body_region' => 'hp_body_region',
		);
		$tax_query = array( 'relation' => 'AND' );

		foreach ( $map as $parameter => $taxonomy ) {
			if ( ! isset( $context[ $parameter ] ) ) {
				continue;
			}

			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $context[ $parameter ],
			);
		}

		return count( $tax_query ) > 1 ? $tax_query : array();
	}

	/**
	 * Require a public, verified provider or clinic before it can receive a
	 * future consented contact request.
	 *
	 * @param WP_Post|null $recipient Candidate recipient record.
	 * @return bool
	 */
	private static function is_eligible_recipient( $recipient ) {
		if ( ! $recipient instanceof WP_Post ) {
			return false;
		}

		if ( ! in_array( $recipient->post_type, array( 'hp_provider', 'hp_clinic' ), true ) || 'publish' !== $recipient->post_status ) {
			return false;
		}

		return 'verified' === get_post_meta( $recipient->ID, 'hp_public_state', true );
	}

	/**
	 * Sanitize internal route lifecycle state.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	public static function sanitize_route_state( $value ) {
		$value   = sanitize_key( (string) $value );
		$allowed = array( 'draft', 'active', 'paused', 'retired' );

		return in_array( $value, $allowed, true ) ? $value : 'draft';
	}

	/**
	 * Sanitize declared recipient capacity without inferring availability.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	public static function sanitize_capacity_state( $value ) {
		$value   = sanitize_key( (string) $value );
		$allowed = array( 'accepting', 'waitlist', 'unavailable' );

		return in_array( $value, $allowed, true ) ? $value : 'unavailable';
	}

	/**
	 * Sanitize an internal tie-break priority. This is not a public ranking.
	 *
	 * @param mixed $value Raw value.
	 * @return int
	 */
	public static function sanitize_priority( $value ) {
		return min( 1000, absint( $value ) );
	}

	/**
	 * Sanitize commercial disclosure state without exposing it through resolve.
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	public static function sanitize_sponsorship_state( $value ) {
		$value   = sanitize_key( (string) $value );
		$allowed = array( 'organic', 'disclosed-sponsored' );

		return in_array( $value, $allowed, true ) ? $value : 'organic';
	}
}

Hea_Lth_Lead_Route_Resolver::boot();
