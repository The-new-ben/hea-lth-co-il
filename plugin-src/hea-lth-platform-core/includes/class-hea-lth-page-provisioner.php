<?php
/**
 * Idempotent provisioning of the portal's foundation pages.
 *
 * The theme ships page templates for approved foundation routes, but a
 * template renders only when a WordPress page exists at that path. Until now
 * the live navigation linked to routes whose pages were never created, so
 * every portal destination returned 404. This provisioner creates the missing
 * pages once per blueprint version. It never updates, overwrites, or deletes
 * an existing page: owner-managed content always wins.
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates missing foundation pages for shipped portal templates.
 */
class Hea_Lth_Page_Provisioner {

	const OPTION_KEY = 'hea_lth_provisioned_pages_blueprint';

	const BLUEPRINT_VERSION = '2026-07-15-02';

	const LEGACY_TOOLBAR_PLUGIN = 'pojo-accessibility/pojo-accessibility.php';

	/**
	 * Attach the provisioning check.
	 *
	 * @return void
	 */
	public static function boot() {
		add_action( 'init', array( __CLASS__, 'maybe_provision' ), 20 );
	}

	/**
	 * Foundation pages owned by the portal blueprint, ordered parents-first.
	 *
	 * Every path must exist in the theme route registry; every entry ships a
	 * template, real content, or both. `noindex` marks thin holding pages that
	 * stay out of search until their product exists.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function blueprint() {
		$hub = 'page-templates/template-hub.php';

		return array(
			array( 'path' => '/anatomy/', 'title' => 'הגוף האינטראקטיבי', 'template' => 'page-templates/template-anatomy.php' ),
			array( 'path' => '/guides/', 'title' => 'מדריכים ומחקרים', 'template' => 'page-templates/template-guides.php' ),
			array( 'path' => '/glossary/', 'title' => 'מילון בריאות', 'template' => 'page-templates/template-glossary.php' ),
			array( 'path' => '/find-care/', 'title' => 'מסלול בחירה', 'template' => 'page-templates/template-find-care.php' ),
			array( 'path' => '/health-technology/', 'title' => 'טכנולוגיות בריאות וציוד', 'template' => 'page-templates/template-health-technology.php' ),
			array( 'path' => '/professionals/', 'title' => 'אזור למקצוענים', 'template' => 'page-templates/template-professionals.php' ),
			array( 'path' => '/treatments/', 'title' => 'מרכזי טיפול', 'template' => 'page-templates/template-treatment-hub.php' ),
			array( 'path' => '/accessibility/', 'title' => 'הצהרת נגישות', 'template' => '', 'content' => self::accessibility_statement_content() ),
			array(
				'path'     => '/diagnostics/',
				'title'    => 'בדיקות ואבחון',
				'template' => $hub,
				'content'  => '<p>מרכז הבדיקות והאבחון מרכז מידע על בדיקות דימות, בדיקות מעבדה וחוות דעת נוספת: מה בודקים, איך מתכוננים ואילו שאלות כדאי להכין לשיחה עם הצוות המטפל. מדריכים מפורטים מתפרסמים כאן רק לאחר אישור עריכתי, תאריך בדיקה ומקור גלוי.</p>',
			),
			array(
				'path'     => '/diagnostics/imaging/',
				'title'    => 'בדיקות דימות',
				'template' => $hub,
				'content'  => '<p>MRI‏, CT‏, אולטרסאונד ורנטגן: הבנת סוגי הבדיקות, ההכנה הנדרשת ומה כדאי לשאול לפני הבדיקה ואחריה. התכנים המפורטים נבנים לפי תקן הפרסום של הפורטל ומתפרסמים בהדרגה.</p>',
			),
			array(
				'path'     => '/diagnostics/laboratory/',
				'title'    => 'בדיקות מעבדה',
				'template' => $hub,
				'content'  => '<p>בדיקות דם ומעבדה: איך מתכוננים, מונחים נפוצים במסמכים ואילו שאלות אפשר להכין להמשך הבירור. פענוח תוצאות נעשה תמיד מול גורם מקצועי מוסמך.</p>',
			),
			array(
				'path'     => '/diagnostics/second-opinion/',
				'title'    => 'חוות דעת נוספת',
				'template' => $hub,
				'content'  => '<p>מתי נהוג לבקש חוות דעת רפואית נוספת, אילו מסמכים כדאי לאסוף מראש ואיך מתכוננים לפגישה. המידע כאן הוא הכנה לשיחה מקצועית, לא תחליף לה.</p>',
			),
			array(
				'path'     => '/wellness/',
				'title'    => 'בריאות ואיכות חיים',
				'template' => $hub,
				'content'  => '<p>שינה, תזונה, פעילות גופנית, מניעה ובדיקות תקופתיות: מרכז ידע שמטרתו לעזור לכם לשאול את השאלות הנכונות ולבחור נקודת התחלה מתאימה, בלי הבטחות ובלי קיצורי דרך.</p>',
			),
			array(
				'path'     => '/wellness/prevention/',
				'title'    => 'מניעה ובדיקות תקופתיות',
				'template' => $hub,
				'content'  => '<p>אילו בדיקות תקופתיות מקובלות, למי הן רלוונטיות ומה מביאים לפגישה. ההמלצות המחייבות הן של הגורמים המקצועיים המטפלים; כאן תמצאו הכנה מסודרת לשיחה איתם.</p>',
			),
			array(
				'path'     => '/private-medicine/',
				'title'    => 'רפואה פרטית',
				'template' => $hub,
				'content'  => '<p>הבנת מסלולי הרפואה הפרטית בישראל: ההבדל בין המסלולים, אילו שאלות לשאול על עלויות וזכויות, ואיך מתכוננים לבחירה מושכלת של רופא, מרפאה או שירות.</p>',
			),
			array( 'path' => '/about/', 'title' => 'אודות Hea-lth', 'template' => '', 'content' => self::about_content() ),
			array( 'path' => '/editorial-policy/', 'title' => 'מדיניות עריכה ובדיקה', 'template' => '', 'content' => self::editorial_policy_content() ),
			array( 'path' => '/privacy/', 'title' => 'מדיניות פרטיות', 'template' => '', 'content' => self::privacy_content() ),
			array( 'path' => '/terms/', 'title' => 'תנאי שימוש', 'template' => '', 'content' => self::terms_content() ),
			array(
				'path'     => '/contact/',
				'title'    => 'יצירת קשר',
				'template' => '',
				'noindex'  => true,
				'content'  => '<p>ערוץ הפנייה המקוון של הפורטל נמצא בהקמה ויפורסם בעמוד זה.</p><p>פניות בנושא נגישות מטופלות בעדיפות — פרטים מלאים בעמוד <a href="/accessibility/">הצהרת הנגישות</a>. אנשי מקצוע המבקשים להצטרף לאינדקס ימצאו מידע בעמוד <a href="/professionals/">האזור למקצוענים</a>.</p>',
			),
			array(
				'path'     => '/professionals/profile-update/',
				'title'    => 'עדכון פרופיל מקצועי',
				'template' => '',
				'noindex'  => true,
				'content'  => '<p>פרופילים מקצועיים מוצגים בפורטל רק לאחר תהליך אימות. טופס העדכון המקוון יפורסם בעמוד זה; עד אז, מידע על תהליך ההצטרפות והאימות זמין בעמוד <a href="/professionals/">האזור למקצוענים</a>.</p>',
			),
			array(
				'path'     => '/account/',
				'title'    => 'אזור אישי',
				'template' => 'page-templates/template-account.php',
				'noindex'  => true,
			),
		);
	}

	/**
	 * The public accessibility statement required by the Israeli service
	 * accessibility regulations (IS 5568). Honest by design: it names the
	 * adjustments that actually exist and the parts that are not fully
	 * accessible yet. Editable later like any page — provisioning never
	 * overwrites an existing page.
	 *
	 * @return string
	 */
	public static function accessibility_statement_content() {
		return implode(
			"\n\n",
			array(
				'<h2>מחויבות לנגישות</h2>',
				'<p>Hea-lth פועלת כדי שהמידע והשירותים באתר יהיו זמינים לכל אדם, כולל אנשים עם מוגבלות. הנגשת האתר מבוססת על התקן הישראלי ת"י 5568 ("קווים מנחים לנגישות תכנים באינטרנט"), הנשען על הנחיות WCAG 2.1 ברמה AA.</p>',
				'<h2>התאמות הנגישות באתר</h2>',
				'<ul><li>תפריט התאמות נגישות קבוע (בפינת המסך): הגדלת טקסט, ניגודיות מוגברת, הדגשת קישורים ועצירת תנועה. ההעדפות נשמרות לביקורים הבאים.</li><li>ניווט מלא במקלדת, כולל קישור דילוג לתוכן, מחווני מיקוד ברורים ותפריטים נגישים.</li><li>מבנה כותרות תקין, טקסט חלופי לרכיבים גרפיים ותוויות ברורות בטפסים.</li><li>האתר מכבד את הגדרת "הפחתת תנועה" של מערכת ההפעלה.</li><li>צבעי הטקסט נבחנו ליחסי ניגודיות תקינים.</li></ul>',
				'<h2>חלקים שאינם נגישים במלואם</h2>',
				'<p>המודל התלת־ממדי האינטראקטיבי הוא רכיב גרפי מתקדם. לצידו קיימת תמיד חלופה טקסטואלית מלאה ושוות ערך: מסלול הבחירה לפי אזור גוף והקשר, הפועל בכפתורים נגישים. אנו ממשיכים לשפר את נגישות הרכיב עצמו.</p>',
				'<h2>פניות ומשוב בנושא נגישות</h2>',
				'<p>נתקלתם בקושי? נשמח לתקן. אפשר לפנות דרך <a href="/contact/">עמוד יצירת הקשר</a> ולציין את הדף ואת הקושי שחוויתם. פרטי רכז/ת הנגישות יפורסמו בעמוד זה.</p>',
				'<h2>עדכון ההצהרה</h2>',
				'<p>ההצהרה עודכנה לאחרונה בתאריך 13.07.2026.</p>',
			)
		);
	}

	/**
	 * Create missing foundation pages once per blueprint version. Entries are
	 * ordered parents-first; child paths attach to their existing parent page
	 * so nested permalinks (e.g. /diagnostics/imaging/) resolve.
	 *
	 * @return void
	 */
	public static function maybe_provision() {
		if ( self::BLUEPRINT_VERSION === get_option( self::OPTION_KEY ) ) {
			return;
		}

		foreach ( self::blueprint() as $page ) {
			$path     = trim( (string) $page['path'], '/' );
			$existing = get_page_by_path( $path, OBJECT, 'page' );

			if ( $existing instanceof WP_Post ) {
				continue;
			}

			$segments  = explode( '/', $path );
			$slug      = array_pop( $segments );
			$parent_id = 0;

			if ( ! empty( $segments ) ) {
				$parent = get_page_by_path( implode( '/', $segments ), OBJECT, 'page' );

				if ( ! $parent instanceof WP_Post ) {
					continue; // Never create a child at the wrong path.
				}

				$parent_id = (int) $parent->ID;
			}

			$page_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_name'    => $slug,
					'post_parent'  => $parent_id,
					'post_title'   => $page['title'],
					'post_content' => isset( $page['content'] ) ? $page['content'] : '',
				),
				true
			);

			if ( is_wp_error( $page_id ) ) {
				continue;
			}

			if ( '' !== $page['template'] ) {
				update_post_meta( (int) $page_id, '_wp_page_template', $page['template'] );
			}

			if ( ! empty( $page['noindex'] ) ) {
				update_post_meta( (int) $page_id, '_yoast_wpseo_meta-robots-noindex', '1' );
			}
		}

		self::provision_site_identity();
		self::retire_legacy_toolbar();

		update_option( self::OPTION_KEY, self::BLUEPRINT_VERSION, false );
	}

	/**
	 * Honest "who we are" page: mission and standards only — no invented
	 * company facts, staff, or history.
	 *
	 * @return string
	 */
	public static function about_content() {
		return implode(
			"\n\n",
			array(
				'<p>Hea-lth הוא פורטל עצמאי לבחירה מושכלת ברפואה פרטית בישראל: מידע ערוך, מסלולי בחירה מודרכים, גוף אינטראקטיבי תלת־ממדי ואינדקס אנשי מקצוע — במקום אחד, בעברית, בגובה העיניים.</p>',
				'<h2>העקרונות שלנו</h2>',
				'<ul><li><strong>אמינות לפני הכול:</strong> אין באתר עובדות רפואיות מומצאות, ביקורות מומצאות או הבטחות תוצאה. תוכן מפורט מתפרסם רק עם אישור עריכתי, תאריך בדיקה ומקור גלוי.</li><li><strong>ספקים מאומתים בלבד:</strong> פרופיל מקצועי מוצג רק לאחר אימות, עם שדות אימות גלויים. הצגה מסחרית מסומנת בשקיפות.</li><li><strong>מידע — לא אבחון:</strong> האתר מסייע להתכונן לשיחה עם אנשי מקצוע. הוא אינו מציע אבחון, ייעוץ רפואי או תחליף לטיפול.</li><li><strong>שקיפות מקורות:</strong> המודל האנטומי מבוסס Z-Anatomy (רישיון CC-BY-SA), והמקורות מוצגים לצד התוכן.</li></ul>',
				'<h2>מה תמצאו כאן</h2>',
				'<p>מרכזי תחום לבדיקות ואבחון, רפואה פרטית ואיכות חיים; גוף אינטראקטיבי שמחבר כל אזור בגוף למידע ולשירותים רלוונטיים; מדריכים ומילון מונחים; ומסלול בחירה שמסדר את השאלות לפני הפגישה.</p>',
				'<p>הערות, תיקונים ובקשות נגישות מתקבלים בברכה — ראו <a href="/accessibility/">הצהרת נגישות</a> ו<a href="/editorial-policy/">מדיניות עריכה ובדיקה</a>.</p>',
			)
		);
	}

	/**
	 * The editorial policy page mirrors the gates the code actually enforces.
	 *
	 * @return string
	 */
	public static function editorial_policy_content() {
		return implode(
			"\n\n",
			array(
				'<p>מדיניות זו מתארת את תנאי הפרסום המחייבים של תוכן בריאות בפורטל. התנאים אינם הצהרה שיווקית — הם נאכפים גם טכנית במערכת הפרסום של האתר.</p>',
				'<h2>שלושת תנאי הסף לכל מדריך</h2>',
				'<ul><li><strong>אישור עריכתי:</strong> עורך אחראי מאשר את התוכן לפני שהוא נחשף לציבור. ללא אישור — התוכן אינו מוצג.</li><li><strong>תאריך בדיקה:</strong> לכל מדריך מוצג מועד הבדיקה האחרון, כדי שתדעו עד כמה המידע עדכני.</li><li><strong>מקור גלוי:</strong> ההפניה המקצועית שעליה נשען המדריך מוצגת לצד התוכן.</li></ul>',
				'<h2>שפה וגבולות</h2>',
				'<p>התוכן נכתב בשפה רגועה ועניינית: ללא הבטחות רפואיות, ללא הפחדה וללא יצירת דחיפות מלאכותית. האתר אינו מציג אבחנות אישיות ואינו ממליץ על טיפול ספציפי; ההחלטות הרפואיות נעשות תמיד מול גורם מקצועי מוסמך.</p>',
				'<h2>המודל האנטומי</h2>',
				'<p>המודל התלת־ממדי מבוסס אטלס Z-Anatomy (רישיון CC-BY-SA, נגזר מ־BodyParts3D). תוויות המבנים עוקבות אחר הנומנקלטורה האנטומית התקנית (TA2) ונבדקו על ידי צוות העריכה. המודל נועד להמחשה ולהתמצאות — לא לאבחון.</p>',
				'<h2>ספקים ותוכן מסחרי</h2>',
				'<p>פרופילים מקצועיים מוצגים רק לאחר אימות. שיבוץ מסחרי, אם קיים, מסומן בגלוי. ניתוב פניות מתבצע בהסכמה מפורשת בלבד, לנמענים מאומתים.</p>',
			)
		);
	}

	/**
	 * Factual privacy baseline describing what the site actually does today.
	 * Marked for owner/legal review before being treated as final.
	 *
	 * @return string
	 */
	public static function privacy_content() {
		return implode(
			"\n\n",
			array(
				'<p>עמוד זה מתאר בפשטות אילו נתונים נאספים באתר וכיצד הם משמשים. הנוסח יעודכן ככל שיתווספו שירותים חדשים.</p>',
				'<h2>מה האתר אינו אוסף</h2>',
				'<p>האתר אינו מבקש ואינו שומר מידע רפואי אישי, אינו מנהל תיקים רפואיים ואינו דורש הרשמה לצפייה בתוכן. טפסי פנייה יופעלו רק עם מנגנון הסכמה מפורשת, ויתועדו במדיניות זו לפני הפעלתם.</p>',
				'<h2>נתונים טכניים</h2>',
				'<ul><li><strong>העדפות נגישות:</strong> נשמרות בדפדפן שלכם בלבד (localStorage) ואינן נשלחות לשרת.</li><li><strong>יומני שרת:</strong> ספק האחסון מתעד באופן סטנדרטי כתובות IP ובקשות לצורכי אבטחה ותפעול.</li><li><strong>עוגיות:</strong> האתר עושה שימוש מינימלי בעוגיות תפעוליות. ככל שיופעלו כלי מדידה, הדבר יעודכן כאן.</li></ul>',
				'<h2>פניות בנושא פרטיות</h2>',
				'<p>לשאלות או בקשות בנושא פרטיות ניתן לפנות דרך <a href="/contact/">עמוד יצירת הקשר</a>.</p>',
				'<p><em>הנוסח המלא נמצא בבדיקה משפטית ויעודכן בהתאם.</em></p>',
			)
		);
	}

	/**
	 * Terms-of-use baseline: informational site, no medical advice, license
	 * attributions. Marked for owner/legal review.
	 *
	 * @return string
	 */
	public static function terms_content() {
		return implode(
			"\n\n",
			array(
				'<p>השימוש באתר Hea-lth כפוף לתנאים אלה. עצם הגלישה מהווה הסכמה להם.</p>',
				'<h2>אופי המידע</h2>',
				'<p>האתר מספק מידע והכוונה לצורך התארגנות והכנה בלבד. המידע אינו ייעוץ רפואי, אינו אבחון ואינו תחליף לבדיקה או לטיפול אצל גורם מקצועי מוסמך. בכל מקרה של מצוקה רפואית יש לפנות לגורמי הרפואה המוסמכים.</p>',
				'<h2>אחריות</h2>',
				'<p>אנו פועלים לדיוק ולעדכניות התוכן לפי <a href="/editorial-policy/">מדיניות העריכה</a>, אך איננו נושאים באחריות להחלטות המתקבלות על בסיס המידע באתר. הקשר המקצועי, החוזי והטיפולי מתקיים ישירות מול נותני השירות.</p>',
				'<h2>קניין רוחני ורישיונות</h2>',
				'<p>התוכן, העיצוב והקוד של הפורטל מוגנים בזכויות. המודל האנטומי מבוסס Z-Anatomy ו־BodyParts3D ומוצג לפי רישיון CC-BY-SA; פרטי הייחוס מופיעים לצד המודל.</p>',
				'<h2>שינויים</h2>',
				'<p>התנאים עשויים להתעדכן; המועד הקובע הוא נוסח העמוד ביום השימוש.</p>',
				'<p><em>הנוסח המלא נמצא בבדיקה משפטית ויעודכן בהתאם.</em></p>',
			)
		);
	}

	/**
	 * Update an option while tolerating third-party option listeners that
	 * throw in anonymous contexts (the still-installed Elementor kit sync
	 * raised "Access denied" here and 500-ed the request). WordPress persists
	 * the value before listeners run, so a listener failure must not abort
	 * provisioning or the surrounding request.
	 *
	 * @param string $option Option name.
	 * @param mixed  $value  New value (string or structured option array).
	 * @return void
	 */
	private static function update_option_tolerantly( $option, $value ) {
		try {
			update_option( $option, $value );
		} catch ( \Throwable $listener_failure ) {
			unset( $listener_failure ); // The value is saved; a listener crash is not ours to surface.
		}
	}

	/**
	 * Replace only the known legacy site identity strings. Any value the owner
	 * has customised since is left untouched.
	 *
	 * @return void
	 */
	public static function provision_site_identity() {
		if ( 'שירותי בריאות פרימיום' === get_option( 'blogname' ) ) {
			self::update_option_tolerantly( 'blogname', 'Hea-lth' );
		}

		if ( 'שירותי בריאות פרטיים בתיאום אישי' === get_option( 'blogdescription' ) ) {
			self::update_option_tolerantly( 'blogdescription', 'מרכז בחירה לרפואה פרטית' );
		}

		// The SEO plugin's homepage title template can carry the legacy brand
		// even after the site identity is fixed; replace only legacy phrasing.
		$titles = get_option( 'wpseo_titles' );

		if ( is_array( $titles ) && isset( $titles['title-home-wpseo'] ) && is_string( $titles['title-home-wpseo'] ) ) {
			$home_title = $titles['title-home-wpseo'];

			if ( false !== strpos( $home_title, 'פרימיום' ) || false !== strpos( $home_title, 'בתיאום אישי' ) ) {
				$titles['title-home-wpseo'] = 'Hea-lth — מרכז בחירה לרפואה פרטית';
				self::update_option_tolerantly( 'wpseo_titles', $titles );
			}
		}

		// The site uses a static front page, so its browser/search title comes
		// from the page's own (legacy) title. Give the front page an explicit
		// SEO title — only while the legacy phrasing is still in effect and no
		// owner-set override exists.
		$front_page_id = (int) get_option( 'page_on_front' );

		if ( $front_page_id > 0 ) {
			$front_page = get_post( $front_page_id );
			$seo_title  = (string) get_post_meta( $front_page_id, '_yoast_wpseo_title', true );
			$legacy     = $front_page instanceof WP_Post && false !== strpos( (string) $front_page->post_title, 'בתיאום אישי' );

			if ( $legacy && ( '' === $seo_title || false !== strpos( $seo_title, 'פרימיום' ) || false !== strpos( $seo_title, 'בתיאום אישי' ) ) ) {
				update_post_meta( $front_page_id, '_yoast_wpseo_title', 'Hea-lth — מרכז בחירה לרפואה פרטית בישראל' );
				update_post_meta( $front_page_id, '_yoast_wpseo_metadesc', 'מידע ערוך עם מקורות, גוף אינטראקטיבי תלת־ממדי, מדריכים ואינדקס אנשי מקצוע מאומתים — הכול כדי לבחור נכון ברפואה הפרטית, בקצב שלכם.' );
			}
		}
	}

	/**
	 * Retire the legacy third-party accessibility toolbar in the same release
	 * that ships the theme's native accessibility panel and public statement,
	 * so the site is never left without an accessibility layer (IS 5568).
	 *
	 * @return void
	 */
	public static function retire_legacy_toolbar() {
		if ( ! function_exists( 'is_plugin_active' ) || ! function_exists( 'deactivate_plugins' ) ) {
			if ( ! defined( 'ABSPATH' ) || ! is_file( ABSPATH . 'wp-admin/includes/plugin.php' ) ) {
				return;
			}

			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( is_plugin_active( self::LEGACY_TOOLBAR_PLUGIN ) ) {
			try {
				deactivate_plugins( self::LEGACY_TOOLBAR_PLUGIN );
			} catch ( \Throwable $deactivation_failure ) {
				unset( $deactivation_failure ); // A plugin's own deactivation hook must not break the request.
			}
		}
	}
}
