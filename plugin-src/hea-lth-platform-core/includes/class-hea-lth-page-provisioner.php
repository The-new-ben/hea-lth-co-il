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

	const BLUEPRINT_VERSION = '2026-07-13-01';

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
	 * Foundation pages owned by the portal blueprint.
	 *
	 * Every slug must match a path in the theme route registry, and every
	 * template must ship in the parent theme. Routes without a dedicated
	 * template stay out of the blueprint until their template exists.
	 *
	 * @return array<string, array<string, string>>
	 */
	public static function blueprint() {
		return array(
			'anatomy'           => array(
				'title'    => 'הגוף האינטראקטיבי',
				'template' => 'page-templates/template-anatomy.php',
			),
			'guides'            => array(
				'title'    => 'מדריכים ומחקרים',
				'template' => 'page-templates/template-guides.php',
			),
			'glossary'          => array(
				'title'    => 'מילון בריאות',
				'template' => 'page-templates/template-glossary.php',
			),
			'find-care'         => array(
				'title'    => 'מסלול בחירה',
				'template' => 'page-templates/template-find-care.php',
			),
			'health-technology' => array(
				'title'    => 'טכנולוגיות בריאות וציוד',
				'template' => 'page-templates/template-health-technology.php',
			),
			'professionals'     => array(
				'title'    => 'אזור למקצוענים',
				'template' => 'page-templates/template-professionals.php',
			),
			'treatments'        => array(
				'title'    => 'מרכזי טיפול',
				'template' => 'page-templates/template-treatment-hub.php',
			),
			'accessibility'     => array(
				'title'    => 'הצהרת נגישות',
				'template' => '',
				'content'  => self::accessibility_statement_content(),
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
	 * Create missing foundation pages once per blueprint version.
	 *
	 * @return void
	 */
	public static function maybe_provision() {
		if ( self::BLUEPRINT_VERSION === get_option( self::OPTION_KEY ) ) {
			return;
		}

		foreach ( self::blueprint() as $slug => $page ) {
			$existing = get_page_by_path( $slug, OBJECT, 'page' );

			if ( $existing instanceof WP_Post ) {
				continue;
			}

			$page_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_name'    => $slug,
					'post_title'   => $page['title'],
					'post_content' => isset( $page['content'] ) ? $page['content'] : '',
				),
				true
			);

			if ( ! is_wp_error( $page_id ) && '' !== $page['template'] ) {
				update_post_meta( (int) $page_id, '_wp_page_template', $page['template'] );
			}
		}

		self::provision_site_identity();
		self::retire_legacy_toolbar();

		update_option( self::OPTION_KEY, self::BLUEPRINT_VERSION, false );
	}

	/**
	 * Update an option while tolerating third-party option listeners that
	 * throw in anonymous contexts (the still-installed Elementor kit sync
	 * raised "Access denied" here and 500-ed the request). WordPress persists
	 * the value before listeners run, so a listener failure must not abort
	 * provisioning or the surrounding request.
	 *
	 * @param string $option Option name.
	 * @param string $value  New value.
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
