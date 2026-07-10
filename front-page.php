<?php
/**
 * Front page for the rebuilt Hea-lth platform.
 *
 * @package HealthRevenue
 */

get_header();
?>

<section class="hr-hero">
	<div class="hr-container hr-hero__grid">
		<div class="hr-hero__content">
			<p class="hr-kicker"><?php esc_html_e( 'פלטפורמת בריאות פרימיום חדשה לישראל', 'health-revenue' ); ?></p>
			<h1><?php esc_html_e( 'השוואה, בדיקה ותיאום של רפואה פרטית, אסתטיקה ושירותי בריאות מתקדמים', 'health-revenue' ); ?></h1>
			<p class="hr-hero__lede">
				<?php esc_html_e( 'Hea-lth נבנית כמרכז ידע, השוואה ותיאום שמחבר בין מטופלים, רופאים, קליניקות ושירותי פרימיום — עם שקיפות, היררכיית תוכן רפואית, ותהליך פנייה מסודר שלא מחליף ייעוץ רפואי אישי.', 'health-revenue' ); ?>
			</p>

			<div class="hr-hero__actions">
				<a class="hr-button hr-button--primary" href="#lead-intake"><?php esc_html_e( 'בדיקת אפשרויות', 'health-revenue' ); ?></a>
				<a class="hr-button hr-button--ghost" href="<?php echo esc_url( home_url( '/doctor-clinic-index/' ) ); ?>"><?php esc_html_e( 'רופאים וקליניקות', 'health-revenue' ); ?></a>
			</div>

			<ul class="hr-trust-list" aria-label="<?php esc_attr_e( 'עקרונות אמון', 'health-revenue' ); ?>">
				<li><?php esc_html_e( 'שימור URLים קיימים עם ערך SEO', 'health-revenue' ); ?></li>
				<li><?php esc_html_e( 'תוכן לפי SERP וכוונת חיפוש', 'health-revenue' ); ?></li>
				<li><?php esc_html_e( 'פניות עם הסכמה וללא איסוף אבחנות בטופס ראשון', 'health-revenue' ); ?></li>
			</ul>
		</div>

		<aside class="hr-intake-card" id="lead-intake" aria-labelledby="lead-intake-title">
			<p class="hr-card-eyebrow"><?php esc_html_e( 'מסלול התאמה ראשוני', 'health-revenue' ); ?></p>
			<h2 id="lead-intake-title"><?php esc_html_e( 'מה אתם רוצים לבדוק?', 'health-revenue' ); ?></h2>
			<form class="hr-lead-form" data-hr-lead-form>
				<input type="text" name="website" tabindex="-1" autocomplete="off" class="hr-honeypot" aria-hidden="true">

				<label>
					<span><?php esc_html_e( 'תחום עניין', 'health-revenue' ); ?></span>
					<select name="service" required>
						<option value="aesthetic-medicine"><?php esc_html_e( 'רפואה אסתטית / הזרקות / טיפולי עור', 'health-revenue' ); ?></option>
						<option value="plastic-surgery"><?php esc_html_e( 'כירורגיה פלסטית', 'health-revenue' ); ?></option>
						<option value="hair-transplant"><?php esc_html_e( 'השתלת שיער / נשירת שיער', 'health-revenue' ); ?></option>
						<option value="private-medicine"><?php esc_html_e( 'רופא פרטי / חוות דעת שנייה', 'health-revenue' ); ?></option>
						<option value="diagnostics-wellness"><?php esc_html_e( 'בדיקות, אבחון ו-Wellness', 'health-revenue' ); ?></option>
						<option value="professional-join"><?php esc_html_e( 'רופא/קליניקה — הצטרפות לפלטפורמה', 'health-revenue' ); ?></option>
					</select>
				</label>

				<label>
					<span><?php esc_html_e( 'אזור בארץ', 'health-revenue' ); ?></span>
					<input type="text" name="region" autocomplete="address-level2" placeholder="<?php esc_attr_e( 'למשל תל אביב, ירושלים, חיפה, אונליין', 'health-revenue' ); ?>">
				</label>

				<div class="hr-form-row">
					<label>
						<span><?php esc_html_e( 'דחיפות', 'health-revenue' ); ?></span>
						<select name="timing">
							<option value="research"><?php esc_html_e( 'רק מתחיל/ה לבדוק', 'health-revenue' ); ?></option>
							<option value="month"><?php esc_html_e( 'בחודש הקרוב', 'health-revenue' ); ?></option>
							<option value="week"><?php esc_html_e( 'השבוע', 'health-revenue' ); ?></option>
						</select>
					</label>

					<label>
						<span><?php esc_html_e( 'מסלול תשלום', 'health-revenue' ); ?></span>
						<select name="payer">
							<option value="private"><?php esc_html_e( 'פרטי', 'health-revenue' ); ?></option>
							<option value="insurance"><?php esc_html_e( 'ביטוח/שב״ן לבדיקה', 'health-revenue' ); ?></option>
							<option value="business"><?php esc_html_e( 'קליניקה/עסק', 'health-revenue' ); ?></option>
						</select>
					</label>
				</div>

				<label>
					<span><?php esc_html_e( 'שם מלא', 'health-revenue' ); ?></span>
					<input type="text" name="name" autocomplete="name" required>
				</label>

				<label>
					<span><?php esc_html_e( 'טלפון או אימייל לחזרה', 'health-revenue' ); ?></span>
					<input type="text" name="contact" autocomplete="tel email" required>
				</label>

				<label class="hr-consent">
					<input type="checkbox" name="consent" value="1" required>
					<span><?php esc_html_e( 'אני מאשר/ת יצירת קשר לצורך בדיקת אפשרויות. אין לצרף כאן מידע רפואי אישי או מסמכים.', 'health-revenue' ); ?></span>
				</label>

				<button class="hr-button hr-button--primary hr-button--wide" type="submit"><?php esc_html_e( 'שליחת פנייה', 'health-revenue' ); ?></button>
				<p class="hr-form-status" data-hr-form-status role="status" aria-live="polite"></p>
			</form>
		</aside>
	</div>
</section>

<section class="hr-section">
	<div class="hr-container">
		<div class="hr-section__heading">
			<p class="hr-kicker"><?php esc_html_e( 'היררכיית הכסף והסמכות', 'health-revenue' ); ?></p>
			<h2><?php esc_html_e( 'לא אתר מאמרים. מערכת שמובילה מידע רחב אל דפי כסף מדויקים', 'health-revenue' ); ?></h2>
			<p><?php esc_html_e( 'הבסיס הרחב הוא אנציקלופדיה רפואית בעברית. מעליו נבנים עמודי עמוד-תווך, השוואות, מחירונים, אינדקסים ותיאום — בלי קניבליזציה בין כוונות חיפוש.', 'health-revenue' ); ?></p>
		</div>

		<div class="hr-pyramid" aria-label="<?php esc_attr_e( 'מבנה האתר', 'health-revenue' ); ?>">
			<div class="hr-pyramid__tier hr-pyramid__tier--top">
				<strong><?php esc_html_e( 'דפי כסף', 'health-revenue' ); ?></strong>
				<span><?php esc_html_e( 'בוטוקס מחיר, השתלת שיער, ניתוח אף, רופא פרטי, MRI/CT פרטי', 'health-revenue' ); ?></span>
			</div>
			<div class="hr-pyramid__tier">
				<strong><?php esc_html_e( 'עמודי עמוד-תווך', 'health-revenue' ); ?></strong>
				<span><?php esc_html_e( 'רפואה אסתטית, ניתוחים פלסטיים, שיער ועור, רפואה פרטית, בדיקות ו-Wellness', 'health-revenue' ); ?></span>
			</div>
			<div class="hr-pyramid__tier hr-pyramid__tier--base">
				<strong><?php esc_html_e( 'יקום ידע רפואי', 'health-revenue' ); ?></strong>
				<span><?php esc_html_e( 'מונחים, שאלות, סיכונים, החלמה, טכנולוגיות, השוואות, רגולציה, מחקרים ונתונים', 'health-revenue' ); ?></span>
			</div>
		</div>
	</div>
</section>

<section class="hr-section hr-section--mint">
	<div class="hr-container">
		<div class="hr-section__heading">
			<p class="hr-kicker"><?php esc_html_e( 'תפריט ראשי חדש', 'health-revenue' ); ?></p>
			<h2><?php esc_html_e( 'שש כניסות ראשיות שמכסות את הכסף, התוכן והסמכות', 'health-revenue' ); ?></h2>
		</div>

		<div class="hr-card-grid">
			<a class="hr-domain-card" href="<?php echo esc_url( home_url( '/aesthetic-medicine-treatments/' ) ); ?>">
				<span><?php esc_html_e( '01', 'health-revenue' ); ?></span>
				<h3><?php esc_html_e( 'רפואה אסתטית', 'health-revenue' ); ?></h3>
				<p><?php esc_html_e( 'הזרקות, חומרי מילוי, בוטוקס, סקולפטרה, רדיאס, פיסול פנים, טיפולי עור ומכשור.', 'health-revenue' ); ?></p>
			</a>
			<a class="hr-domain-card" href="<?php echo esc_url( home_url( '/plastic-surgery-consultation/' ) ); ?>">
				<span><?php esc_html_e( '02', 'health-revenue' ); ?></span>
				<h3><?php esc_html_e( 'ניתוחים פלסטיים', 'health-revenue' ); ?></h3>
				<p><?php esc_html_e( 'ניתוח אף, חזה, עפעפיים, שאיבת שומן, מתיחת בטן, פנים וגוף — לפי סיכון, התאמה ומומחיות.', 'health-revenue' ); ?></p>
			</a>
			<a class="hr-domain-card" href="<?php echo esc_url( home_url( '/hair-transplant-consultation/' ) ); ?>">
				<span><?php esc_html_e( '03', 'health-revenue' ); ?></span>
				<h3><?php esc_html_e( 'שיער ועור', 'health-revenue' ); ?></h3>
				<p><?php esc_html_e( 'השתלות שיער, נשירת שיער, טיפולי עור, לייזר, אקנה וצלקות — עם השוואת שיטות ומחירים.', 'health-revenue' ); ?></p>
			</a>
			<a class="hr-domain-card" href="<?php echo esc_url( home_url( '/private-doctor-appointment/' ) ); ?>">
				<span><?php esc_html_e( '04', 'health-revenue' ); ?></span>
				<h3><?php esc_html_e( 'רפואה פרטית', 'health-revenue' ); ?></h3>
				<p><?php esc_html_e( 'רופאים פרטיים, חוות דעת שנייה, בדיקות, תורים מהירים, החזרים וביטוחים.', 'health-revenue' ); ?></p>
			</a>
			<a class="hr-domain-card" href="<?php echo esc_url( home_url( '/premium-health-services/' ) ); ?>">
				<span><?php esc_html_e( '05', 'health-revenue' ); ?></span>
				<h3><?php esc_html_e( 'בדיקות ו-Wellness', 'health-revenue' ); ?></h3>
				<p><?php esc_html_e( 'בדיקות מנהלים, MRI/CT, PET CT, בדיקות דם פרטיות, longevity, ביומרקרים ומניעה.', 'health-revenue' ); ?></p>
			</a>
			<a class="hr-domain-card" href="<?php echo esc_url( home_url( '/doctor-clinic-index/' ) ); ?>">
				<span><?php esc_html_e( '06', 'health-revenue' ); ?></span>
				<h3><?php esc_html_e( 'רופאים וקליניקות', 'health-revenue' ); ?></h3>
				<p><?php esc_html_e( 'אינדקס מקצועי, אימות, תחומי טיפול, אזורים, שפות, ביטוחים וזמינות — הבסיס להכנסות B2B.', 'health-revenue' ); ?></p>
			</a>
		</div>
	</div>
</section>

<section class="hr-section">
	<div class="hr-container hr-split">
		<div>
			<p class="hr-kicker"><?php esc_html_e( 'מודולים מתקדמים בתכנון', 'health-revenue' ); ?></p>
			<h2><?php esc_html_e( 'האתר נבנה כדי להכיל AI אמיתי, 3D, מפות והשוואות — לא רק טקסט', 'health-revenue' ); ?></h2>
			<p><?php esc_html_e( 'השלב הראשון מגדיר את התבנית, הנתונים והמסלולים. לאחר מכן נכניס רכיבים אינטראקטיביים מדורגים: מפות קליניקות, השוואות מחיר, שאלון התאמה לא-אבחנתי, מודלים תלת-ממדיים להסבר אנטומיה, ואזור מקצוענים.', 'health-revenue' ); ?></p>
			<a class="hr-button hr-button--ghost" href="<?php echo esc_url( home_url( '/professionals/' ) ); ?>"><?php esc_html_e( 'מסלול מקצוענים', 'health-revenue' ); ?></a>
		</div>

		<div class="hr-tech-board">
			<div>
				<strong><?php esc_html_e( 'AI Navigation', 'health-revenue' ); ?></strong>
				<span><?php esc_html_e( 'הכוונה למידע, לא אבחון רפואי.', 'health-revenue' ); ?></span>
			</div>
			<div>
				<strong><?php esc_html_e( '3D / AR Education', 'health-revenue' ); ?></strong>
				<span><?php esc_html_e( 'מודלים להסבר אזורי טיפול ותהליכים.', 'health-revenue' ); ?></span>
			</div>
			<div>
				<strong><?php esc_html_e( 'Interactive Maps', 'health-revenue' ); ?></strong>
				<span><?php esc_html_e( 'מומחים, קליניקות, זמינות ואזורים.', 'health-revenue' ); ?></span>
			</div>
			<div>
				<strong><?php esc_html_e( 'Price Intelligence', 'health-revenue' ); ?></strong>
				<span><?php esc_html_e( 'טווחים, משתנים, חבילות ומה משפיע על מחיר.', 'health-revenue' ); ?></span>
			</div>
		</div>
	</div>
</section>

<section class="hr-section hr-section--dark">
	<div class="hr-container hr-split">
		<div>
			<p class="hr-kicker"><?php esc_html_e( 'לרופאים, קליניקות וספקים', 'health-revenue' ); ?></p>
			<h2><?php esc_html_e( 'פלטפורמת ביקוש, אמון ותוכן שמקצוענים ירצו להיות חלק ממנה', 'health-revenue' ); ?></h2>
			<p><?php esc_html_e( 'הצד העסקי נבנה סביב נראות מקצועית, דפי פרופיל, התאמת פניות, שקיפות מסחרית, תוכן מומחה, אזורי שירות ונתונים. לא “ליד זול” — מערכת שמייצרת ביקוש איכותי.', 'health-revenue' ); ?></p>
		</div>
		<div class="hr-professional-card">
			<h3><?php esc_html_e( 'מודל הכנסה ראשוני', 'health-revenue' ); ?></h3>
			<ul>
				<li><?php esc_html_e( 'פניות מוסכמות ומסווגות לפי תחום, אזור ודחיפות', 'health-revenue' ); ?></li>
				<li><?php esc_html_e( 'פרופילים מקצועיים מאומתים', 'health-revenue' ); ?></li>
				<li><?php esc_html_e( 'עמודי מחיר והשוואה עם גילוי מסחרי ברור', 'health-revenue' ); ?></li>
				<li><?php esc_html_e( 'חבילות נראות ותוכן למומחים', 'health-revenue' ); ?></li>
			</ul>
			<a class="hr-button hr-button--light" href="#lead-intake"><?php esc_html_e( 'בדיקת הצטרפות', 'health-revenue' ); ?></a>
		</div>
	</div>
</section>

<?php
get_footer();
