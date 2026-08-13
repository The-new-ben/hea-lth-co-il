<?php
/**
 * Idempotent clinic-build plans and equipment procurement relationships.
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Hea_Lth_Clinic_Provisioner {
	const OPTION_KEY = 'hea_lth_clinic_blueprint';
	const VERSION    = '2026-08-13-01';

	public static function boot() {
		add_action( 'init', array( __CLASS__, 'maybe_provision' ), 40 );
	}

	public static function procurement_categories() {
		return array(
			'consultation-assessment' => array( 'הערכה ומדידה', 'מערכות למדידת הרכב גוף, צילום ותיעוד נקודת פתיחה.' ),
			'body-contouring'         => array( 'עיצוב גוף וחיזוק שרירים', 'מערכות גוף מקצועיות המבוססות על אנרגיה, קירור או גירוי שרירים.' ),
			'skin-analysis'           => array( 'אבחון עור ותכנון טיפול', 'כלי צילום וניתוח המסייעים לבנות מסלול טיפול ולתעד התקדמות.' ),
			'skin-treatment'          => array( 'טכנולוגיות עור ופנים', 'מערכות לייזר, RF, HIFU ופלטפורמות מקצועיות לטיפולי עור.' ),
			'hair-removal'            => array( 'הסרת שיער', 'מערכות לייזר מקצועיות להרחבת סל הטיפולים במרפאה.' ),
			'pelvic-floor'            => array( 'רצפת אגן ובריאות משלימה', 'ציוד משלים למרפאות המשלבות טיפול ברצפת האגן.' ),
			'clinic-software'         => array( 'ניהול מרפאה ומסע לקוח', 'CRM, תורים, מסמכים, הסכמות, מעקב, סליקה ותקשורת עם מטופלים.' ),
			'room-infrastructure'     => array( 'חדרי טיפול ותשתיות', 'מיטות טיפול, תאורה, עגלות, אחסון, חשמל, צילום וסביבת עבודה.' ),
			'consumables-service'     => array( 'מתכלים, הדרכה ושירות', 'מתכלים, תחזוקה, אחריות, הכשרת צוות ונהלי הפעלה.' ),
			'finance-growth'          => array( 'מימון וצמיחת המרפאה', 'מימון ציוד, תמחור מסלולים, סליקה, שיווק ומדידת ביצועים.' ),
		);
	}

	public static function equipment_map() {
		return array(
			'equipment:nubway:hakuvision'       => array( 'consultation-assessment', 'skin-analysis' ),
			'equipment:nubway:torasculpt-360'   => array( 'body-contouring' ),
			'equipment:nubway:kaipulse-ems'     => array( 'body-contouring' ),
			'equipment:nubway:shefa-robot'      => array( 'body-contouring' ),
			'equipment:nubway:hikaripro-d8'     => array( 'hair-removal' ),
			'equipment:nubway:shinco2-elite'    => array( 'skin-treatment' ),
			'equipment:nubway:shinpico-770'     => array( 'skin-treatment' ),
			'equipment:nubway:shinyag-pro'      => array( 'skin-treatment' ),
			'equipment:nubway:zenlift-hifu'     => array( 'skin-treatment' ),
			'equipment:nubway:dermaneedle-rf6'  => array( 'skin-treatment' ),
			'equipment:nubway:kaipelvi'         => array( 'pelvic-floor' ),
			'equipment:galaxy:sculpsure'        => array( 'body-contouring' ),
			'equipment:galaxy:vectus'           => array( 'hair-removal' ),
			'equipment:galaxy:picosure'         => array( 'skin-treatment' ),
			'equipment:galaxy:icon'             => array( 'skin-treatment' ),
			'equipment:galaxy:advatex'          => array( 'skin-treatment' ),
			'equipment:galaxy:potenza'          => array( 'skin-treatment' ),
		);
	}

	public static function maybe_provision() {
		if ( self::VERSION === get_option( self::OPTION_KEY ) ) {
			return;
		}

		$term_ids = self::provision_terms();
		self::connect_equipment( $term_ids );
		self::provision_plan( $term_ids );
		flush_rewrite_rules( false );
		update_option( self::OPTION_KEY, self::VERSION, false );
	}

	private static function provision_terms() {
		$ids = array();
		foreach ( self::procurement_categories() as $slug => $definition ) {
			$term = term_exists( $slug, 'hp_procurement' );
			if ( ! $term ) {
				$term = wp_insert_term( $definition[0], 'hp_procurement', array( 'slug' => $slug, 'description' => $definition[1] ) );
			}
			if ( ! is_wp_error( $term ) ) {
				$ids[ $slug ] = (int) $term['term_id'];
			}
		}

		return $ids;
	}

	private static function connect_equipment( $term_ids ) {
		foreach ( self::equipment_map() as $seed_key => $categories ) {
			$posts = get_posts(
				array(
					'post_type'      => 'hp_equipment',
					'post_status'    => 'any',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'meta_key'       => '_hea_lth_seed_key',
					'meta_value'     => $seed_key,
				)
			);
			if ( empty( $posts ) ) {
				continue;
			}

			$ids = array();
			foreach ( $categories as $category ) {
				if ( isset( $term_ids[ $category ] ) ) {
					$ids[] = $term_ids[ $category ];
				}
			}
			wp_set_object_terms( (int) $posts[0], $ids, 'hp_procurement', false );
		}
	}

	private static function provision_plan( $term_ids ) {
		$existing = get_posts(
			array(
				'post_type'      => 'hp_clinic_plan',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => '_hea_lth_seed_key',
				'meta_value'     => 'clinic-plan:weight-management-aesthetics',
			)
		);

		if ( $existing ) {
			$plan_id = (int) $existing[0];
		} else {
			$plan_id = wp_insert_post(
				array(
					'post_type'    => 'hp_clinic_plan',
					'post_status'  => 'publish',
					'post_name'    => 'weight-management-aesthetics',
					'post_title'   => 'הקמת מרפאה לטיפול בהשמנה ואסתטיקה',
					'post_excerpt' => 'מפת רכש מלאה למרפאה המשלבת ניהול משקל, עיצוב גוף, טיפולי עור ושירותים משלימים.',
					'post_content' => '<p>תכנית ההקמה מחברת בין מסלול המטופל, חדרי הטיפול, מערכות המדידה, ציוד הגוף והעור, תפעול המרפאה ומודל הצמיחה. כך ניתן לבנות סל רכש בשלבים ולבקש הצעות ממספר ספקים מתוך תמונה אחת.</p>',
				),
				true
			);
			if ( is_wp_error( $plan_id ) ) {
				return;
			}
			update_post_meta( (int) $plan_id, '_hea_lth_seed_key', 'clinic-plan:weight-management-aesthetics' );
		}

		$order = array_keys( self::procurement_categories() );
		update_post_meta( (int) $plan_id, 'hp_public_state', 'verified' );
		update_post_meta( (int) $plan_id, 'hp_last_verified', '2026-08-13' );
		update_post_meta( (int) $plan_id, 'hp_plan_summary', 'תכנון רכש רב-שלבי: ליבה קלינית, ציוד מניב, תפעול, שירות וצמיחה.' );
		update_post_meta( (int) $plan_id, 'hp_procurement_order', $order );
		wp_set_object_terms( (int) $plan_id, array_values( $term_ids ), 'hp_procurement', false );

		$type = term_exists( 'weight-management-aesthetics', 'hp_clinic_type' );
		if ( ! $type ) {
			$type = wp_insert_term( 'מרפאת השמנה ואסתטיקה', 'hp_clinic_type', array( 'slug' => 'weight-management-aesthetics' ) );
		}
		if ( ! is_wp_error( $type ) ) {
			wp_set_object_terms( (int) $plan_id, array( (int) $type['term_id'] ), 'hp_clinic_type', false );
		}
	}
}
