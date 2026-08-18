<?php
/**
 * Private per-client advisory rooms.
 *
 * Each room is a code-gated page that concentrates one party's view of a
 * procurement process. Buyer rooms carry the client's brief, supplier
 * tracks, curated equipment with decision context, and interest CTAs.
 * Supplier rooms carry an anonymized opportunity brief and the material
 * requests. Room data lives in this class only — never in post content and
 * never in registered meta — so nothing reachable through REST, sitemaps,
 * or search can leak a brief. Pages are provisioned once (create-only),
 * password-protected (native post_password) and noindexed; the template
 * additionally accepts the same code as a query parameter for one-click
 * links. Price honesty: rooms never carry invented amounts — price fields
 * are status-tracked until a supplier's written range arrives.
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hea_Lth_Advisory_Rooms {

	const VERSION = '2026-08-18-03';
	const OPTION  = 'hea_lth_advisory_blueprint';

	public static function boot() {
		add_action( 'init', array( __CLASS__, 'maybe_provision' ), 45 );
	}

	/**
	 * Technology categories: visual badge key + neutral, claim-free
	 * decision context shared by every room.
	 */
	public static function categories() {
		return array(
			'cryo'  => array(
				'name'  => 'הקפאת שומן (Cryolipolysis)',
				'badge' => 'cryo',
				'copy'  => 'קירור מבוקר של רקמת שומן מקומית ללא ניתוח. נקודות לבדיקה: גדלי אפליקטורים, משך טיפול, עלות מתכלים לטיפול.',
			),
			'hifem' => array(
				'name'  => 'HIFEM / EMS',
				'badge' => 'pulse',
				'copy'  => 'גירוי אלקטרומגנטי לשריר לצד עבודה על היקפים. נקודות לבדיקה: עוצמה, מספר ידיות בו-זמניות, פרוטוקולי טיפול.',
			),
			'rf'    => array(
				'name'  => 'גלי רדיו (RF)',
				'badge' => 'wave',
				'copy'  => 'אנרגיית RF לחימום רקמה ומיצוק. נקודות לבדיקה: תדר, בקרת טמפרטורה, התאמה לאזורי גוף שונים.',
			),
			'hifu'  => array(
				'name'  => 'אולטרסאונד ממוקד (HIFU)',
				'badge' => 'focus',
				'copy'  => 'אנרגיה ממוקדת לעומק רקמה למיצוק ועיצוב. נקודות לבדיקה: עומקי פעולה, ראשי טיפול, עלות קרטרידג׳ים.',
			),
			'laser' => array(
				'name'  => 'לייזר / פוטותרפיה',
				'badge' => 'beam',
				'copy'  => 'טכנולוגיות אור להסרת שיער וטיפולי גוף. נקודות לבדיקה: אורכי גל, קצב פולסים, קירור מובנה, התאמה לגווני עור.',
			),
		);
	}

	/**
	 * The universal pre-purchase checklist rendered in buyer rooms.
	 */
	public static function decision_criteria() {
		return array(
			'סטטוס רגולטורי בישראל (אמ"ר) ואישורי FDA / CE למכשיר עצמו',
			'אחריות: משך, מה כלול, וזמני תגובה של טכנאי בישראל',
			'הדרכה קלינית לצוות והסמכה — מה נכלל במחיר',
			'עלות מתכלים וקרטרידג׳ים לכל טיפול — המספר שקובע רווחיות',
			'תפוקה: משך טיפול ממוצע וכמה טיפולים אפשריים בשעה',
			'תנאי תשלום, ליסינג ומימון — פריסה מול הון עצמי',
			'שדרוגים עתידיים: האם הפלטפורמה מתרחבת ביישומים',
		);
	}

	/**
	 * Room registry. Access codes are personal shared secrets for gated
	 * commercial pages (not platform credentials): digits only.
	 */
	public static function rooms() {
		return array(
			'clinic-2026-001' => array(
				'type'      => 'buyer',
				'client'    => 'ד"ר אחסאן',
				'code'      => '0524018782',
				'updated'   => '18.08.2026',
				'title'     => 'חדר ייעוץ הצטיידות — מרפאה לטיפול בהשמנה ואסתטיקה',
				'intro'     => 'ריכזנו עבורך במקום אחד את תמונת המצב המלאה של תהליך ההצטיידות: הדרישות שהגדרת, הספקים שגויסו, והמערכות הרלוונטיות עם ההקשר שחשוב להחלטה. ליד כל מערכת יש כפתור "מעניין אותי" — כל סימון מגיע ישירות אלינו ומכוון את איסוף ההצעות עבורך.',
				'howto'     => array(
					'עברו על המערכות בכל קטגוריה ועל טבלת ההשוואה',
					'סמנו "מעניין אותי" ליד כל מה שרלוונטי — זה פותח הודעה מוכנה אלינו',
					'אנחנו חוזרים אליכם עם הצעות המחיר וההמלצות לפי הסימונים',
				),
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
						'status' => 'אישר בכתב את תנאי התהליך ונמצא בקשר פעיל.',
						'quotes' => 'מפרטים וטווחי מחירים — התבקשו, בהשלמה',
					),
					array(
						'name'   => 'Galaxy Medical Technologies',
						'role'   => 'יבואן מערכות לייזר ואסתטיקה רפואית',
						'status' => 'בתיאום מתקדם מול סמנכ"ל החברה.',
						'quotes' => 'מפרטים וטווחי מחירים — התבקשו, בהשלמה',
					),
					array(
						'name'   => 'Venus Concept',
						'role'   => 'יצרן בינלאומי עם פעילות ישירה בישראל',
						'status' => 'בתהליך אישור תנאי שיתוף הפעולה; שיחת התאמה בהמשך השבוע.',
						'quotes' => 'ייפתח מיד עם אישור התנאים',
					),
				),
				'equipment' => array(
					array(
						'label' => 'פירוק שומן וחיטוב הגוף',
						'items' => array(
							array( 'slug' => 'nubway-torasculpt-360', 'category' => 'cryo', 'why' => 'מענה ישיר לדרישת הקפאת השומן — טיפול מקומי ללא ניתוח.' ),
							array( 'slug' => 'galaxy-sculpsure', 'category' => 'laser', 'why' => 'פירוק שומן בלייזר — חלופה לא-פולשנית להקפאה, זמני טיפול קצרים.' ),
							array( 'slug' => 'nubway-kaipulse-ems', 'category' => 'hifem', 'why' => 'בניית שריר לצד עבודה על היקפים — המרכיב השני בדרישת ה-HIFEM.' ),
							array( 'slug' => 'nubway-zenlift-hifu', 'category' => 'hifu', 'why' => 'מיצוק ועיצוב באנרגיה ממוקדת — משלים סל טיפולי גוף רחב.' ),
							array( 'slug' => 'nubway-shefa-robot', 'category' => 'rf', 'why' => 'פלטפורמת RF 448kHz רב-יישומית — מרחיבה את תפריט הטיפולים.' ),
						),
					),
					array(
						'label' => 'הסרת שיער בלייזר',
						'items' => array(
							array( 'slug' => 'nubway-depi-ai', 'category' => 'laser', 'why' => 'עמדת הסרת שיער רובוטית — חיסכון בכוח אדם ותפוקה גבוהה.' ),
							array( 'slug' => 'nubway-hikaripro-d8', 'category' => 'laser', 'why' => 'לייזר דיודה קליני — הקונפיגורציה המבוקשת ביותר לפתיחת מרפאה.' ),
							array( 'slug' => 'galaxy-vectus', 'category' => 'laser', 'why' => 'לייזר דיודה ייעודי להסרת שיער מבית יצרן מוכר.' ),
							array( 'slug' => 'galaxy-icon', 'category' => 'laser', 'why' => 'פלטפורמת לייזר + IPL רב-יישומית — הסרת שיער לצד טיפולי עור.' ),
						),
					),
				),
				'process'   => array(
					'ריכוז הדרישות המלאות של המרפאה — בוצע',
					'גיוס ספקים מובילים והסכמות תיווך — בתהליך מתקדם',
					'איסוף הצעות מחיר והשוואה מסודרת כאן בעמוד — בימים הקרובים',
					'ליווי עד בחירה, אספקה, התקנה והדרכה',
				),
			),

			'supplier-nicro'  => array(
				'type'     => 'supplier',
				'client'   => 'Nicro / NUBWAY',
				'contact'  => 'אבי פרץ',
				'code'     => '0528750006',
				'updated'  => '18.08.2026',
				'title'    => 'חדר עסקה — הזדמנות הצטיידות פעילה',
				'intro'    => 'רופא מורשה המקים מרפאה לטיפול בהשמנה ואסתטיקה מנהל את תהליך ההצטיידות שלו במרוכז דרך Hea-lth, לבקשתו — ללא שיחות מכירה ישירות. העמוד הזה מרכז את מה שדרוש כדי שהמערכות שלכם יוצגו בפניו בהשוואה המסודרת.',
				'brief'    => array(
					'הרוכש: רופא מורשה, מרפאה חדשה לטיפול בהשמנה ואסתטיקה (פרטים מלאים נמסרו בערוץ הישיר)',
					'צורך 1: מערכת לפירוק/הקפאת שומן וחיטוב הגוף (Cryolipolysis / HIFEM / RF) — יחידה אחת',
					'צורך 2: מערכת לייזר מקצועית להסרת שיער — בעדיפות לשלב הפתיחה',
					'לוח זמנים: החלטה מתגבשת בימים הקרובים; ההצעות הראשונות שיתקבלו יוצגו ראשונות',
				),
				'asks'     => array(
					'One-Pager או PDF מפרט לכל מערכת רלוונטית',
					'טווח מחירים ותנאי תשלום / ליסינג מקובלים',
					'תמונות מוצר רשמיות באיכות גבוהה',
					'סטטוס רגולטורי בישראל, אחריות, שירות והדרכה',
				),
				'terms'    => 'תנאי שיתוף הפעולה אושרו בכתב: עמלת הצלחה 10% (או עמלה קבועה מוסכמת), אי-עקיפה ועדכון התקדמות. ההפניה בוצעה — האסמכתאות בערוץ המייל.',
				'machines' => array( 'nubway-torasculpt-360', 'nubway-kaipulse-ems', 'nubway-zenlift-hifu', 'nubway-shefa-robot', 'nubway-depi-ai', 'nubway-hikaripro-d8' ),
			),

			'supplier-galaxy' => array(
				'type'     => 'supplier',
				'client'   => 'Galaxy Medical Technologies',
				'contact'  => 'איתי גל',
				'code'     => '0527021057',
				'updated'  => '18.08.2026',
				'title'    => 'חדר עסקה — הזדמנות הצטיידות פעילה',
				'intro'    => 'רופא מורשה המקים מרפאה לטיפול בהשמנה ואסתטיקה מנהל את תהליך ההצטיידות שלו במרוכז דרך Hea-lth, לבקשתו — ללא שיחות מכירה ישירות. העמוד הזה מרכז את מה שדרוש כדי שהמערכות שלכם יוצגו בפניו בהשוואה המסודרת.',
				'brief'    => array(
					'הרוכש: רופא מורשה, מרפאה חדשה לטיפול בהשמנה ואסתטיקה (פרטים נמסרים לאחר אישור תנאים בכתב)',
					'צורך 1: מערכת לפירוק/הקפאת שומן וחיטוב הגוף (Cryolipolysis / HIFEM / RF) — יחידה אחת',
					'צורך 2: מערכת לייזר מקצועית להסרת שיער — בעדיפות לשלב הפתיחה',
					'לוח זמנים: החלטה מתגבשת בימים הקרובים; ההצעות הראשונות שיתקבלו יוצגו ראשונות',
				),
				'asks'     => array(
					'One-Pager או PDF מפרט לכל מערכת רלוונטית',
					'טווח מחירים ותנאי תשלום / ליסינג מקובלים',
					'תמונות מוצר רשמיות באיכות גבוהה',
					'אישור קצר בכתב של תנאי שיתוף הפעולה (נוסח נשלח במייל)',
				),
				'terms'    => 'מסגרת שיתוף הפעולה: עמלת הצלחה 10% ממחיר העסקה (או עמלה קבועה מוסכמת מראש), אי-עקיפה ועדכון התקדמות. אישור קצר בכתב פותח את העברת מלוא הפרטים.',
				'machines' => array( 'galaxy-sculpsure', 'galaxy-vectus', 'galaxy-icon' ),
			),

			'supplier-venus'  => array(
				'type'     => 'supplier',
				'client'   => 'Venus Concept',
				'contact'  => 'קרן זילברמן',
				'code'     => '036075301',
				'updated'  => '18.08.2026',
				'title'    => 'חדר עסקה — הזדמנות הצטיידות פעילה',
				'intro'    => 'רופא מורשה המקים מרפאה לטיפול בהשמנה ואסתטיקה מנהל את תהליך ההצטיידות שלו במרוכז דרך Hea-lth, לבקשתו — ללא שיחות מכירה ישירות. העמוד הזה מרכז את מה שדרוש כדי שהפתרונות שלכם ייכנסו להשוואה המסודרת.',
				'brief'    => array(
					'הרוכש: רופא מורשה, מרפאה חדשה לטיפול בהשמנה ואסתטיקה (פרטים נמסרים מיד לאחר אישור תנאים בכתב)',
					'צורך 1: מערכת המשלבת HIFEM/EMS בעוצמה גבוהה עם RF לחיטוב, פירוק שומן ובניית שריר',
					'צורך 2: מערכת לייזר מקצועית להסרת שיער — בעדיפות לשלב הפתיחה',
					'לוח זמנים: החלטה מתגבשת בימים הקרובים; מומלץ לא להמתין לשיחת יום ראשון עם החומרים',
				),
				'asks'     => array(
					'אישור קצר בכתב מגורם מוסמך של תנאי שיתוף הפעולה (נוסח עמוד אחד נשלח במייל)',
					'One-Pager למערכת הרלוונטית (דגם, רגולציה בישראל, אחריות, שירות והדרכה)',
					'טווח מחירים ותנאי תשלום / ליסינג',
					'תמונות מוצר רשמיות',
				),
				'terms'    => 'מסגרת שיתוף הפעולה שהוצעה: עמלת הצלחה 10% ממחיר העסקה במועד הסגירה (או עמלה קבועה מוסכמת מראש בטווח 8,000–15,000 ש"ח למכשיר), אי-עקיפה ועדכון התקדמות. פרטי הרופא יימסרו מיד עם האישור בכתב.',
				'machines' => array(),
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

		self::sync_room_passwords();

		update_option( self::OPTION, self::VERSION, false );
	}

	/**
	 * Keep each room page's native post password equal to its current room
	 * code. This is the ONLY mutation ever applied to an existing page, and
	 * it touches only pages this class provisioned (matched by room meta).
	 */
	private static function sync_room_passwords() {
		foreach ( self::rooms() as $key => $room ) {
			$page = get_page_by_path( 'advisory/' . $key );
			if ( ! $page || $key !== (string) get_post_meta( $page->ID, '_hea_lth_advisory_room', true ) ) {
				continue;
			}
			if ( (string) $page->post_password !== (string) $room['code'] ) {
				wp_update_post(
					array(
						'ID'            => $page->ID,
						'post_password' => $room['code'],
					)
				);
			}
		}
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
