<?php
/**
 * From-scratch portal homepage.
 *
 * The public page intentionally avoids invented doctors, medical outcomes,
 * review scores, prices, or availability. Those fields render only when the
 * governed directory and editorial records exist.
 *
 * @package HeaLthPortal
 */

get_header();

$topic_cards = array(
	array(
		'number' => '01',
		'title'  => __( 'רפואה אסתטית', 'hea-lth-portal' ),
		'copy'   => __( 'טיפולי עור, הזרקות, מכשור רפואי ושאלות שכדאי להכין לפני פגישה.', 'hea-lth-portal' ),
		'route'  => 'aesthetic_medicine',
		'tone'   => 'pearl',
	),
	array(
		'number' => '02',
		'title'  => __( 'ניתוחים פלסטיים', 'hea-lth-portal' ),
		'copy'   => __( 'מידע על תהליכים, החלמה, סיכונים, ייעוץ ובחירת מסגרת טיפול.', 'hea-lth-portal' ),
		'route'  => 'plastic_surgery_consultation',
		'tone'   => 'sage',
	),
	array(
		'number' => '03',
		'title'  => __( 'השתלת שיער', 'hea-lth-portal' ),
		'copy'   => __( 'נשירה, השתלות, בריאות קרקפת ושאלות לבירור אצל מומחה.', 'hea-lth-portal' ),
		'route'  => 'hair_transplant_consultation',
		'tone'   => 'ink',
	),
	array(
		'number' => '04',
		'title'  => __( 'עור ודרמטולוגיה', 'hea-lth-portal' ),
		'copy'   => __( 'מדריכים למחלות עור, טיפולים אסתטיים, בדיקות והכנה לפגישה.', 'hea-lth-portal' ),
		'foundationRoute' => 'skin',
		'tone'   => 'sand',
	),
	array(
		'number' => '05',
		'title'  => __( 'רפואה פרטית', 'hea-lth-portal' ),
		'copy'   => __( 'חוות דעת נוספת, רופאים מומחים, בירור מסלולים והכנה לשיחה.', 'hea-lth-portal' ),
		'foundationRoute' => 'private_medicine',
		'tone'   => 'forest',
	),
	array(
		'number' => '06',
		'title'  => __( 'בדיקות ואבחון', 'hea-lth-portal' ),
		'copy'   => __( 'דימות, מעבדה, הכנה לבדיקה, פענוח והמשך בירור אצל הצוות המטפל.', 'hea-lth-portal' ),
		'foundationRoute' => 'diagnostics',
		'tone'   => 'mist',
	),
	array(
		'number' => '07',
		'title'  => __( 'בריאות ואיכות חיים', 'hea-lth-portal' ),
		'copy'   => __( 'שינה, תזונה, מניעה, בדיקות תקופתיות וטכנולוגיות בריאות.', 'hea-lth-portal' ),
		'foundationRoute' => 'wellness',
		'tone'   => 'blush',
	),
	array(
		'number' => '08',
		'title'  => __( 'ציוד וטכנולוגיות', 'hea-lth-portal' ),
		'copy'   => __( 'הסברים על מכשור, התאמה לשירות, שאלות בטיחות ומקורות מידע.', 'hea-lth-portal' ),
		'foundationRoute' => 'health_technology',
		'tone'   => 'slate',
	),
);

$reviewed_guides = hea_lth_portal_get_reviewed_guides( 3 );
?>

<section class="hp-hero">
	<div class="hp-shell hp-hero__grid">
		<div class="hp-hero__copy">
			<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'מרכז בחירה לבריאות פרטית', 'hea-lth-portal' ); ?></p>
			<h1><?php esc_html_e( 'מחפשים טיפול, רופא או בדיקה פרטית?', 'hea-lth-portal' ); ?></h1>
			<p class="hp-hero__lede"><?php esc_html_e( 'Hea-lth מחברת בין מידע ברור, מסלולי בחירה, אנשי מקצוע ומרפאות. מתחילים במה שמעניין אתכם ומתקדמים בקצב שנכון לכם.', 'hea-lth-portal' ); ?></p>
			<div class="hp-hero__actions">
				<a class="hp-button hp-button--light" href="#start-search"><?php esc_html_e( 'להתחיל בחיפוש', 'hea-lth-portal' ); ?></a>
				<a class="hp-inline-link hp-inline-link--light" href="<?php echo esc_url( hea_lth_portal_route( 'doctor_clinic_index' ) ); ?>"><?php esc_html_e( 'חיפוש רופאים ומרפאות', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a>
			</div>
			<ul class="hp-hero__assurance" aria-label="<?php esc_attr_e( 'עקרונות המידע באתר', 'hea-lth-portal' ); ?>">
				<li><?php esc_html_e( 'מידע והכנה לשיחה עם איש מקצוע', 'hea-lth-portal' ); ?></li>
				<li><?php esc_html_e( 'פרופילים לפי שדות אימות גלויים', 'hea-lth-portal' ); ?></li>
				<li><?php esc_html_e( 'ללא אבחון או הבטחת תוצאה', 'hea-lth-portal' ); ?></li>
			</ul>
		</div>

		<div class="hp-care-navigator" data-explorer>
			<div class="hp-care-navigator__meta">
				<span class="hp-care-navigator__status"><?php esc_html_e( 'כלי בחירה', 'hea-lth-portal' ); ?></span>
				<span><?php esc_html_e( 'שלב 1 מתוך 3', 'hea-lth-portal' ); ?></span>
			</div>
			<div class="hp-care-navigator__content" data-explorer-content>
				<p class="hp-eyebrow hp-eyebrow--small"><?php esc_html_e( 'מה מחפשים היום?', 'hea-lth-portal' ); ?></p>
				<strong data-explorer-title><?php esc_html_e( 'מתחילים מהצורך שלכם', 'hea-lth-portal' ); ?></strong>
				<p data-explorer-copy><?php esc_html_e( 'בחרו נקודת מוצא כדי להגיע למידע, שאלות, אנשי מקצוע ושירותים רלוונטיים.', 'hea-lth-portal' ); ?></p>
			</div>
			<div class="hp-care-navigator__choices" role="group" aria-label="<?php esc_attr_e( 'נקודות התחלה', 'hea-lth-portal' ); ?>">
				<button class="is-active" type="button" data-explorer-option data-title="טיפול או ניתוח" data-copy="מדריכים, שאלות לפגישה ומסלולי בחירה לפי טיפול, אזור גוף או צורך."><span>01</span><b><?php esc_html_e( 'טיפול או ניתוח', 'hea-lth-portal' ); ?></b><i aria-hidden="true">←</i></button>
				<button type="button" data-explorer-option data-title="רופא או מרפאה" data-copy="חיפוש לפי תחום, אזור, שפה, סוג שירות ושדות אימות גלויים."><span>02</span><b><?php esc_html_e( 'רופא או מרפאה', 'hea-lth-portal' ); ?></b><i aria-hidden="true">←</i></button>
				<button type="button" data-explorer-option data-title="בדיקה או דימות" data-copy="הכנה לבדיקות, מסמכים, שאלות לצוות המטפל ובחירת מסלול שירות."><span>03</span><b><?php esc_html_e( 'בדיקה או דימות', 'hea-lth-portal' ); ?></b><i aria-hidden="true">←</i></button>
				<button type="button" data-explorer-option data-title="ציוד וטכנולוגיה" data-copy="הסברים על מכשור, התאמה לשירות, שאלות בטיחות ומקורות מידע."><span>04</span><b><?php esc_html_e( 'ציוד וטכנולוגיה', 'hea-lth-portal' ); ?></b><i aria-hidden="true">←</i></button>
			</div>
			<div class="hp-care-navigator__footer">
				<span><?php esc_html_e( 'מידע לפני פנייה', 'hea-lth-portal' ); ?></span>
				<a href="#start-search"><?php esc_html_e( 'פתחו חיפוש', 'hea-lth-portal' ); ?><b aria-hidden="true">←</b></a>
			</div>
		</div>
	</div>
</section>

<section class="hp-start-search" id="start-search">
	<div class="hp-shell hp-start-search__inner">
		<div>
			<p class="hp-eyebrow"><?php esc_html_e( 'התחילו מנקודת המוצא שלכם', 'hea-lth-portal' ); ?></p>
			<h2><?php esc_html_e( 'מחפשים מידע, שירות או איש מקצוע?', 'hea-lth-portal' ); ?></h2>
		</div>
		<form class="hp-start-search__form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search">
			<label class="screen-reader-text" for="portal-entry-search"><?php esc_html_e( 'חיפוש באתר', 'hea-lth-portal' ); ?></label>
			<input id="portal-entry-search" name="s" type="search" placeholder="<?php esc_attr_e( 'למשל: ניתוח אף, נשירת שיער, MRI, רופא עור', 'hea-lth-portal' ); ?>">
			<button type="submit"><?php esc_html_e( 'חיפוש', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></button>
		</form>
		<div class="hp-start-search__chips" aria-label="<?php esc_attr_e( 'חיפושים נפוצים', 'hea-lth-portal' ); ?>">
			<a href="<?php echo esc_url( hea_lth_portal_route( 'aesthetic_medicine' ) ); ?>"><?php esc_html_e( 'רפואה אסתטית', 'hea-lth-portal' ); ?></a>
			<a href="<?php echo esc_url( hea_lth_portal_route( 'rhinoplasty_discovery' ) ); ?>"><?php esc_html_e( 'ניתוח אף', 'hea-lth-portal' ); ?></a>
			<a href="<?php echo esc_url( hea_lth_portal_route( 'hair_transplant_discovery' ) ); ?>"><?php esc_html_e( 'השתלת שיער', 'hea-lth-portal' ); ?></a>
			<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'diagnostics_imaging' ) ); ?>"><?php esc_html_e( 'בדיקות דימות', 'hea-lth-portal' ); ?></a>
			<a href="<?php echo esc_url( hea_lth_portal_route( 'doctor_clinic_index' ) ); ?>"><?php esc_html_e( 'רופאים ומרפאות', 'hea-lth-portal' ); ?></a>
		</div>
	</div>
</section>

<section class="hp-section hp-section--paper">
	<div class="hp-shell">
		<div class="hp-section-heading hp-section-heading--split">
			<div>
				<p class="hp-eyebrow"><?php esc_html_e( 'מרכזי בחירה', 'hea-lth-portal' ); ?></p>
				<h2><?php esc_html_e( 'כל תחום מתחיל במקום מסודר', 'hea-lth-portal' ); ?></h2>
			</div>
			<p><?php esc_html_e( 'היכנסו לעמוד תחום כדי להבין אפשרויות, לקרוא מדריכים, להכין שאלות ולמצוא את מסלול החיפוש המתאים.', 'hea-lth-portal' ); ?></p>
		</div>

		<div class="hp-topic-grid">
			<?php foreach ( $topic_cards as $card ) : ?>
				<?php $topic_url = isset( $card['route'] ) ? hea_lth_portal_route( $card['route'] ) : hea_lth_portal_foundation_route( $card['foundationRoute'] ); ?>
				<a class="hp-topic-card hp-topic-card--<?php echo esc_attr( $card['tone'] ); ?>" href="<?php echo esc_url( $topic_url ); ?>">
					<span class="hp-topic-card__number"><?php echo esc_html( $card['number'] ); ?></span>
					<span class="hp-topic-card__mark" aria-hidden="true"></span>
					<h3><?php echo esc_html( $card['title'] ); ?></h3>
					<p><?php echo esc_html( $card['copy'] ); ?></p>
					<span class="hp-topic-card__link"><?php esc_html_e( 'למרכז התחום', 'hea-lth-portal' ); ?><b aria-hidden="true">←</b></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="hp-section hp-section--steps">
	<div class="hp-shell">
		<div class="hp-section-heading hp-section-heading--center">
			<p class="hp-eyebrow"><?php esc_html_e( 'דרך ברורה יותר לבחור', 'hea-lth-portal' ); ?></p>
			<h2><?php esc_html_e( 'לא חייבים לדעת הכול כדי להתחיל נכון', 'hea-lth-portal' ); ?></h2>
			<p><?php esc_html_e( 'הפורטל בנוי כדי לעזור לכם להתקדם משאלה כללית למידע מסודר, לשיחה טובה יותר ולחיפוש מותאם.', 'hea-lth-portal' ); ?></p>
		</div>
		<ol class="hp-step-grid">
			<li>
				<span>01</span>
				<h3><?php esc_html_e( 'מגדירים את השאלה', 'hea-lth-portal' ); ?></h3>
				<p><?php esc_html_e( 'טיפול, תסמין, בדיקה, אזור גוף או צורך בשירות פרטי.', 'hea-lth-portal' ); ?></p>
			</li>
			<li>
				<span>02</span>
				<h3><?php esc_html_e( 'מבינים את האפשרויות', 'hea-lth-portal' ); ?></h3>
				<p><?php esc_html_e( 'מדריכים, מילון, שאלות שכדאי לשאול ומסלולים להמשך בירור.', 'hea-lth-portal' ); ?></p>
			</li>
			<li>
				<span>03</span>
				<h3><?php esc_html_e( 'מגיעים למסלול מתאים', 'hea-lth-portal' ); ?></h3>
				<p><?php esc_html_e( 'חיפוש אנשי מקצוע, מרפאות, בדיקות ושירותים לפי נתונים שקופים.', 'hea-lth-portal' ); ?></p>
			</li>
		</ol>
	</div>
</section>

<section class="hp-section hp-section--journal">
	<div class="hp-shell">
		<div class="hp-section-heading hp-section-heading--split">
			<div>
				<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'מרכז המדריכים', 'hea-lth-portal' ); ?></p>
				<h2><?php esc_html_e( 'מדריכים שמסדרים את השאלות לפני הפגישה', 'hea-lth-portal' ); ?></h2>
			</div>
			<a class="hp-inline-link hp-inline-link--light" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'guides' ) ); ?>"><?php esc_html_e( 'לכל המדריכים', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a>
		</div>
		<?php if ( $reviewed_guides->have_posts() ) : ?>
			<div class="hp-journal-grid hp-journal-grid--reviewed">
				<?php while ( $reviewed_guides->have_posts() ) : $reviewed_guides->the_post(); ?>
					<?php hea_lth_portal_render_reviewed_guide_card(); ?>
				<?php endwhile; ?>
			</div>
			<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<div class="hp-reviewed-feed-empty">
				<div class="hp-reviewed-feed-empty__intro">
					<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'תקן הפרסום שלנו', 'hea-lth-portal' ); ?></p>
					<strong><?php esc_html_e( 'המדריכים הראשונים נמצאים בבדיקה עריכתית.', 'hea-lth-portal' ); ?></strong>
					<p><?php esc_html_e( 'כל מדריך מתפרסם כאן רק אחרי שעמד בשלושת תנאי הסף. בינתיים אפשר לעיין במרכזי התחומים ובמילון הבריאות.', 'hea-lth-portal' ); ?></p>
					<a class="hp-inline-link hp-inline-link--light" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'guides' ) ); ?>"><?php esc_html_e( 'לספריית המדריכים', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a>
				</div>
				<ol class="hp-reviewed-feed-empty__standard">
					<li>
						<span aria-hidden="true">01</span>
						<b><?php esc_html_e( 'אישור עריכתי', 'hea-lth-portal' ); ?></b>
						<p><?php esc_html_e( 'עורך אחראי מאשר את התוכן לפני שהוא מוצג לציבור.', 'hea-lth-portal' ); ?></p>
					</li>
					<li>
						<span aria-hidden="true">02</span>
						<b><?php esc_html_e( 'תאריך בדיקה', 'hea-lth-portal' ); ?></b>
						<p><?php esc_html_e( 'מועד הבדיקה האחרון מוצג בגלוי על כל מדריך.', 'hea-lth-portal' ); ?></p>
					</li>
					<li>
						<span aria-hidden="true">03</span>
						<b><?php esc_html_e( 'מקור גלוי', 'hea-lth-portal' ); ?></b>
						<p><?php esc_html_e( 'ההפניה המקצועית שעליה נשען המדריך מוצגת לצד התוכן.', 'hea-lth-portal' ); ?></p>
					</li>
				</ol>
			</div>
		<?php endif; ?>
	</div>
</section>

<section class="hp-section hp-section--directory">
	<div class="hp-shell hp-directory-preview">
		<div class="hp-directory-preview__copy">
			<p class="hp-eyebrow"><?php esc_html_e( 'רופאים, מרפאות ושירותים', 'hea-lth-portal' ); ?></p>
			<h2><?php esc_html_e( 'מקום אחד לחיפוש לפי מה שחשוב לכם', 'hea-lth-portal' ); ?></h2>
			<p><?php esc_html_e( 'החיפוש בנוי סביב תחום, אזור, שפה, סוג שירות ושדות אימות. פרופיל יופיע רק לאחר שהמידע הציבורי שלו הוגדר ונבדק.', 'hea-lth-portal' ); ?></p>
			<a class="hp-button" href="<?php echo esc_url( hea_lth_portal_route( 'doctor_clinic_index' ) ); ?>"><?php esc_html_e( 'לפתיחת האינדקס', 'hea-lth-portal' ); ?></a>
		</div>
		<div class="hp-directory-preview__interface" aria-label="<?php esc_attr_e( 'תצוגת חיפוש אנשי מקצוע', 'hea-lth-portal' ); ?>">
			<div class="hp-directory-preview__toolbar">
				<span><?php esc_html_e( 'תחום או שירות', 'hea-lth-portal' ); ?></span>
				<span><?php esc_html_e( 'אזור בארץ', 'hea-lth-portal' ); ?></span>
				<span><?php esc_html_e( 'רשומות מאומתות', 'hea-lth-portal' ); ?></span>
			</div>
			<div class="hp-directory-preview__map">
				<div class="hp-directory-preview__map-gate">
					<span><?php esc_html_e( 'מפת שירותים', 'hea-lth-portal' ); ?></span>
					<strong><?php esc_html_e( 'המפה תיפתח רק מול מיקומים שאושרו להצגה', 'hea-lth-portal' ); ?></strong>
					<p><?php esc_html_e( 'בחירת תחום, אזור או חלק בגוף תחבר את הרשימה ואת המפה יחד, לאחר אישור נתונים ותצורה.', 'hea-lth-portal' ); ?></p>
				</div>
			</div>
			<div class="hp-directory-preview__status">
				<span class="hp-directory-preview__verification-dot" aria-hidden="true"></span>
				<p><?php esc_html_e( 'תוצאות, זמינות ונתוני מיקום יוצגו רק מול רשומות מאומתות.', 'hea-lth-portal' ); ?></p>
			</div>
		</div>
	</div>
</section>

<section class="hp-section hp-section--private">
	<div class="hp-shell hp-private-grid">
		<div class="hp-private-grid__visual" aria-hidden="true">
			<div class="hp-private-card hp-private-card--primary"><span>01</span><strong><?php esc_html_e( 'שאלה', 'hea-lth-portal' ); ?></strong></div>
			<div class="hp-private-card hp-private-card--secondary"><span>02</span><strong><?php esc_html_e( 'אפשרויות', 'hea-lth-portal' ); ?></strong></div>
			<div class="hp-private-card hp-private-card--tertiary"><span>03</span><strong><?php esc_html_e( 'שיחה', 'hea-lth-portal' ); ?></strong></div>
			<div class="hp-private-grid__line"></div>
		</div>
		<div class="hp-private-grid__copy">
			<p class="hp-eyebrow"><?php esc_html_e( 'מסלול רפואה פרטית', 'hea-lth-portal' ); ?></p>
			<h2><?php esc_html_e( 'רפואה פרטית מתחילה בהכנה טובה', 'hea-lth-portal' ); ?></h2>
			<p><?php esc_html_e( 'בדקו מה חשוב לשאול, אילו מסמכים רלוונטיים לשיחה, ואילו אפשרויות שירות קיימות באזור שלכם.', 'hea-lth-portal' ); ?></p>
			<ul class="hp-check-list">
				<li><?php esc_html_e( 'התכוננו לחוות דעת נוספת', 'hea-lth-portal' ); ?></li>
				<li><?php esc_html_e( 'חפשו מומחיות או מרפאה לפי צורך', 'hea-lth-portal' ); ?></li>
				<li><?php esc_html_e( 'שמרו שאלות ומידע להמשך שיחה', 'hea-lth-portal' ); ?></li>
			</ul>
			<a class="hp-inline-link" href="<?php echo esc_url( hea_lth_portal_route( 'private_doctor_appointment' ) ); ?>"><?php esc_html_e( 'למציאת רופא פרטי', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a>
		</div>
	</div>
</section>

<?php
$hp_anatomy_config = function_exists( 'hea_lth_portal_anatomy_viewer_config' )
	? hea_lth_portal_anatomy_viewer_config()
	: ( isset( $GLOBALS['hea_lth_preview_front_anatomy'] ) ? $GLOBALS['hea_lth_preview_front_anatomy'] : array( 'status' => 'license-gated', 'engine' => 'none' ) );
$hp_anatomy_ready = isset( $hp_anatomy_config['status'], $hp_anatomy_config['engine'] )
	&& 'approved' === $hp_anatomy_config['status'] && 'three-webgl' === $hp_anatomy_config['engine'];
?>
<section class="hp-section hp-section--anatomy" id="body-discovery">
	<div class="hp-shell hp-anatomy-teaser">
		<div class="hp-anatomy-teaser__copy">
			<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'גוף, מידע וטכנולוגיה', 'hea-lth-portal' ); ?></p>
			<h2><?php esc_html_e( 'הגוף האינטראקטיבי', 'hea-lth-portal' ); ?></h2>
			<?php if ( $hp_anatomy_ready ) : ?>
				<p><?php esc_html_e( 'סובבו, התקרבו ולחצו על מבנה אנטומי. כל אזור גוף מתחבר למידע, למומחיות ולשירותים שעברו בקרה.', 'hea-lth-portal' ); ?></p>
				<a class="hp-button hp-button--light" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'anatomy' ) ); ?>"><?php esc_html_e( 'המשיכו לחוויית הגוף המלאה', 'hea-lth-portal' ); ?></a>
				<p class="hp-anatomy-teaser__note"><?php esc_html_e( 'אטלס אנטומי להמחשה בלבד — אינו ייעוץ, אבחון או המלצה רפואית. מקור המודל: Z-Anatomy (רישיון CC-BY-SA 4.0), נגזר מ־BodyParts3D © The Database Center for Life Science, Japan (CC-BY-SA 2.1). בדיקת נומנקלטורה אנטומית: צוות העריכה של Hea-lth.', 'hea-lth-portal' ); ?></p>
			<?php else : ?>
				<p><?php esc_html_e( 'הכניסה העתידית לאנטומיה, טיפולים, מומחיות, ציוד ומפה. כל אזור גוף יתחבר רק למידע ולשירותים שעברו בקרה.', 'hea-lth-portal' ); ?></p>
				<a class="hp-button hp-button--light" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'anatomy' ) ); ?>"><?php esc_html_e( 'גלו את חוויית הגוף', 'hea-lth-portal' ); ?></a>
				<p class="hp-anatomy-teaser__note"><?php esc_html_e( 'המודל האנטומי הציבורי יופעל רק עם נכס בעל רישיון ובקרה קלינית. עד אז, החיפוש הטקסטואלי נשאר זמין במלואו.', 'hea-lth-portal' ); ?></p>
			<?php endif; ?>
		</div>
		<?php if ( $hp_anatomy_ready ) : ?>
			<div class="hp-anatomy-live" data-anatomy-live>
				<div class="hp-anatomy-live__topline">
					<span><?php esc_html_e( 'מודל אנטומי תלת ממדי', 'hea-lth-portal' ); ?></span>
					<span><?php esc_html_e( 'מבט חי', 'hea-lth-portal' ); ?></span>
				</div>
				<div class="hp-anatomy-live__stage" data-anatomy-model-stage aria-label="<?php esc_attr_e( 'מודל אנטומי תלת ממדי אינטראקטיבי', 'hea-lth-portal' ); ?>"></div>
				<p class="hp-anatomy-live__hint"><?php esc_html_e( 'גררו לסיבוב · גלגלת לזום · לחצו על מבנה לזיהוי', 'hea-lth-portal' ); ?></p>
			</div>
		<?php else : ?>
			<div class="hp-anatomy-teaser__interface" data-anatomy-teaser>
				<div class="hp-anatomy-teaser__topline"><span><?php esc_html_e( 'בחרו אזור', 'hea-lth-portal' ); ?></span><span><?php esc_html_e( 'תצוגת חקר', 'hea-lth-portal' ); ?></span></div>
				<div class="hp-anatomy-teaser__stage">
					<div class="hp-anatomy-teaser__axis hp-anatomy-teaser__axis--vertical"></div>
					<div class="hp-anatomy-teaser__axis hp-anatomy-teaser__axis--horizontal"></div>
					<div class="hp-anatomy-teaser__rings"></div>
					<span class="hp-anatomy-node hp-anatomy-node--face" aria-hidden="true"></span>
					<span class="hp-anatomy-node hp-anatomy-node--chest" aria-hidden="true"></span>
					<span class="hp-anatomy-node hp-anatomy-node--knee" aria-hidden="true"></span>
				</div>
				<div class="hp-anatomy-teaser__region-list">
					<button type="button" data-anatomy-region data-region="אף ונשימה"><?php esc_html_e( 'אף ונשימה', 'hea-lth-portal' ); ?></button>
					<button type="button" data-anatomy-region data-region="עור ופנים"><?php esc_html_e( 'עור ופנים', 'hea-lth-portal' ); ?></button>
					<button type="button" data-anatomy-region data-region="שיער וקרקפת"><?php esc_html_e( 'שיער וקרקפת', 'hea-lth-portal' ); ?></button>
					<button type="button" data-anatomy-region data-region="מפרקים ותנועה"><?php esc_html_e( 'מפרקים ותנועה', 'hea-lth-portal' ); ?></button>
				</div>
				<div class="hp-anatomy-teaser__output"><span><?php esc_html_e( 'מסלול שנבחר', 'hea-lth-portal' ); ?></span><strong data-anatomy-output><?php esc_html_e( 'בחרו אזור כדי להמשיך', 'hea-lth-portal' ); ?></strong></div>
			</div>
		<?php endif; ?>
	</div>
</section>

<section class="hp-section hp-section--technology">
	<div class="hp-shell">
		<div class="hp-section-heading hp-section-heading--split">
			<div>
				<p class="hp-eyebrow"><?php esc_html_e( 'טכנולוגיות ושירותים מתקדמים', 'hea-lth-portal' ); ?></p>
				<h2><?php esc_html_e( 'להבין את הכלים שמאחורי הבחירה', 'hea-lth-portal' ); ?></h2>
			</div>
			<p><?php esc_html_e( 'מכשור רפואי, דימות, בדיקות, בריאות דיגיטלית ושירותים חדשים יופיעו עם הסבר, שאלות בטיחות ומידע על סוגי שירות.', 'hea-lth-portal' ); ?></p>
		</div>
		<div class="hp-technology-grid">
			<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'diagnostics_imaging' ) ); ?>"><span>01</span><h3><?php esc_html_e( 'דימות רפואי', 'hea-lth-portal' ); ?></h3><p><?php esc_html_e( 'הבדלים בין סוגי בדיקות, הכנה והמשך בירור.', 'hea-lth-portal' ); ?></p></a>
			<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'health_technology' ) ); ?>"><span>02</span><h3><?php esc_html_e( 'טכנולוגיות בריאות', 'hea-lth-portal' ); ?></h3><p><?php esc_html_e( 'הסברים על כלים, מכשירים ושירותים חדשים.', 'hea-lth-portal' ); ?></p></a>
			<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'wellness_prevention' ) ); ?>"><span>03</span><h3><?php esc_html_e( 'מניעה ואיכות חיים', 'hea-lth-portal' ); ?></h3><p><?php esc_html_e( 'ידע על בדיקות תקופתיות, שינה, תזונה ואורח חיים.', 'hea-lth-portal' ); ?></p></a>
			<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'guides' ) ); ?>"><span>04</span><h3><?php esc_html_e( 'מדריכי הכנה', 'hea-lth-portal' ); ?></h3><p><?php esc_html_e( 'שאלות, מסמכים ומידע לקראת שירות רפואי.', 'hea-lth-portal' ); ?></p></a>
		</div>
	</div>
</section>

<section class="hp-section hp-section--professionals">
	<div class="hp-shell hp-professionals-callout">
		<div>
			<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'לרופאים, מרפאות ונותני שירות', 'hea-lth-portal' ); ?></p>
			<h2><?php esc_html_e( 'פרופיל מקצועי שמציג מידע ברור למי שמחפש אתכם', 'hea-lth-portal' ); ?></h2>
			<p><?php esc_html_e( 'הצטרפו לאינדקס שנבנה סביב תחום התמחות, פרטי מרפאה, שפות, נגישות, שירותים ועדכון פרטים באופן מסודר.', 'hea-lth-portal' ); ?></p>
		</div>
		<div class="hp-professionals-callout__actions">
			<a class="hp-button hp-button--light" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'professionals' ) ); ?>"><?php esc_html_e( 'הכירו את מסלול המקצוענים', 'hea-lth-portal' ); ?></a>
			<a class="hp-inline-link hp-inline-link--light" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'professional_profile_update' ) ); ?>"><?php esc_html_e( 'עדכון פרופיל קיים', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a>
		</div>
	</div>
</section>

<section class="hp-section hp-section--trust">
	<div class="hp-shell hp-trust-grid">
		<div class="hp-trust-grid__headline">
			<p class="hp-eyebrow"><?php esc_html_e( 'אמון קודם לכל', 'hea-lth-portal' ); ?></p>
			<h2><?php esc_html_e( 'מידע טוב מתחיל בשקיפות', 'hea-lth-portal' ); ?></h2>
		</div>
		<div class="hp-trust-grid__items">
			<article><span>01</span><h3><?php esc_html_e( 'מקורות ועדכון', 'hea-lth-portal' ); ?></h3><p><?php esc_html_e( 'דפי ידע מיועדים להציג מקור, תאריך עדכון ואחריות עריכתית כאשר הם מפורסמים.', 'hea-lth-portal' ); ?></p></article>
			<article><span>02</span><h3><?php esc_html_e( 'פרופילים מאומתים', 'hea-lth-portal' ); ?></h3><p><?php esc_html_e( 'שדות מקצועיים ופרטי מרפאה מוצגים רק לאחר תהליך אימות מתאים.', 'hea-lth-portal' ); ?></p></article>
			<article><span>03</span><h3><?php esc_html_e( 'פרטיות ובחירה', 'hea-lth-portal' ); ?></h3><p><?php esc_html_e( 'פניות מתחילות במידע בסיסי בלבד. אין צורך לשתף כאן מידע רפואי אישי או מסמכים.', 'hea-lth-portal' ); ?></p></article>
		</div>
	</div>
</section>

<section class="hp-final-callout">
	<div class="hp-shell hp-final-callout__inner">
		<div>
			<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'התחילו במקום הנכון', 'hea-lth-portal' ); ?></p>
			<h2><?php esc_html_e( 'איזו שאלה תרצו לברר היום?', 'hea-lth-portal' ); ?></h2>
		</div>
		<a class="hp-button hp-button--light" href="#start-search"><?php esc_html_e( 'פתיחת חיפוש', 'hea-lth-portal' ); ?></a>
	</div>
</section>

<?php get_footer(); ?>
