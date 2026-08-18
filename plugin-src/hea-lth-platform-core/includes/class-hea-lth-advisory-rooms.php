<?php
/**
 * Private per-client advisory rooms.
 *
 * Each room is a code-gated page that concentrates a client's procurement
 * brief: needs, engaged supplier tracks, and curated approved equipment.
 * Room data lives in this class only — never in post content and never in
 * registered meta — so nothing reachable through REST or sitemaps can leak
 * a client brief. The page itself is provisioned once, password-protected
 * (native post_password) and noindexed; the template additionally accepts
 * the same code as a query parameter for one-click links.
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hea_Lth_Advisory_Rooms {

	const VERSION = '2026-08-18-01';
	const OPTION  = 'hea_lth_advisory_blueprint';

	public static function boot() {
		add_action( 'init', array( __CLASS__, 'maybe_provision' ), 45 );
	}

	/**
	 * Room registry. Access codes are personal shared secrets for a gated
	 * commercial offer page (not platform credentials): digits only.
	 */
	public static function rooms() {
		return array(
			'clinic-2026-001' => array(
				'client'    => 'ד"ר אחסאן',
				'code'      => '0524013782',
				'title'     => 'חדר ייעוץ הצטיידות — מרפאה לטיפול בהשמנה ואסתטיקה',
				'intro'     => 'ריכזנו עבורך במקום אחד את תמונת המצב המלאה של תהליך ההצטיידות: הדרישות שהגדרת, הספקים שגויסו לתהליך, והמערכות הרלוונטיות מהקטלוג המקצועי שלנו. העמוד מתעדכן ככל שמתקבלות הצעות מהספקים.',
				'needs'     => array(
					array(
						'title' => 'מערכת לפירוק שומן וחיטוב הגוף',
						'copy'  => 'Cryolipolysis / HIFEM / RF — הקפאת שומן, פירוק שומן ובניית שריר למרפאה לטיפול בהשמנה.',
					),
					array(
						'title' => 'מערכת לייזר מקצועית להסרת שיער',
						'copy'  => 'בעדיפות לשלב הפתיחה — מערכת קלינית עם אחריות, שירות והדרכה בישראל.',
					),
				),
				'suppliers' => array(
					array(
						'name'   => 'Nicro מבית NUBWAY',
						'role'   => 'ספק מכשור אסתטי רב-תחומי',
						'status' => 'אישר בכתב את תנאי התהליך ונמצא בקשר פעיל. התבקשו מפרטים וטווחי מחירים למערכות הרלוונטיות.',
					),
					array(
						'name'   => 'Galaxy Medical Technologies',
						'role'   => 'יבואן מערכות לייזר ואסתטיקה רפואית',
						'status' => 'בתיאום מתקדם. התבקשו מפרטים וטווחי מחירים למערכות פירוק שומן והסרת שיער.',
					),
					array(
						'name'   => 'Venus Concept',
						'role'   => 'יצרן בינלאומי עם פעילות ישירה בישראל',
						'status' => 'בתהליך אישור תנאי שיתוף הפעולה; שיחת התאמה מתואמת להמשך השבוע.',
					),
				),
				'equipment' => array(
					array(
						'label' => 'פירוק שומן וחיטוב הגוף',
						'slugs' => array( 'nubway-torasculpt-360', 'galaxy-sculpsure', 'nubway-kaipulse-ems', 'nubway-zenlift-hifu', 'nubway-shefa-robot' ),
					),
					array(
						'label' => 'הסרת שיער בלייזר',
						'slugs' => array( 'nubway-depi-ai', 'nubway-hikaripro-d8', 'galaxy-vectus', 'galaxy-icon' ),
					),
				),
				'process'   => array(
					'ריכוז הדרישות המלאות של המרפאה — בוצע',
					'גיוס ספקים מובילים והסכמות תיווך חתומות — בתהליך מתקדם',
					'איסוף הצעות מחיר והשוואה מסודרת במקום אחד — בימים הקרובים',
					'ליווי עד בחירה, אספקה, התקנה והדרכה',
				),
			),
		);
	}

	/**
	 * Create-only provisioning: never edits an existing page.
	 */
	public static function maybe_provision() {
		if ( get_option( self::OPTION ) === self::VERSION ) {
			return;
		}

		$parent = get_page_by_path( 'advisory' );
		if ( ! $parent ) {
			$parent_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => 'אזור ייעוץ אישי',
					'post_name'    => 'advisory',
					'post_content' => '<p>אזור הייעוץ האישי של Hea-lth פתוח ללקוחות מלווים בלבד. לקבלת גישה — דברו איתנו דרך עמוד יצירת הקשר.</p>',
				)
			);
			if ( $parent_id ) {
				update_post_meta( $parent_id, '_yoast_wpseo_meta-robots-noindex', '1' );
			}
		} else {
			$parent_id = $parent->ID;
		}

		if ( ! $parent_id ) {
			return;
		}

		foreach ( self::rooms() as $key => $room ) {
			if ( get_page_by_path( 'advisory/' . $key ) ) {
				continue;
			}
			$page_id = wp_insert_post(
				array(
					'post_type'     => 'page',
					'post_status'   => 'publish',
					'post_parent'   => $parent_id,
					'post_title'    => $room['title'],
					'post_name'     => $key,
					'post_content'  => '',
					'post_password' => $room['code'],
				)
			);
			if ( ! $page_id ) {
				continue;
			}
			update_post_meta( $page_id, '_wp_page_template', 'page-templates/template-advisory-room.php' );
			update_post_meta( $page_id, '_yoast_wpseo_meta-robots-noindex', '1' );
			update_post_meta( $page_id, '_hea_lth_advisory_room', $key );
		}

		update_option( self::OPTION, self::VERSION, false );
	}

	/**
	 * Resolve the room definition for a provisioned page.
	 *
	 * @param int $post_id Page ID.
	 * @return array|null
	 */
	public static function room_for_page( $post_id ) {
		$key   = (string) get_post_meta( (int) $post_id, '_hea_lth_advisory_room', true );
		$rooms = self::rooms();
		return ( '' !== $key && isset( $rooms[ $key ] ) ) ? $rooms[ $key ] : null;
	}

	/**
	 * Timing-safe access-code comparison on digits only.
	 *
	 * @param array  $room      Room definition.
	 * @param string $candidate Raw user-supplied code.
	 * @return bool
	 */
	public static function code_matches( $room, $candidate ) {
		$given = preg_replace( '/\D+/', '', (string) $candidate );
		if ( '' === $given || empty( $room['code'] ) ) {
			return false;
		}
		return hash_equals( (string) $room['code'], $given );
	}
}
