<?php
/**
 * Template Name: מסלול בחירה
 *
 * A public, non-clinical route selector. This page does not collect or send
 * personal or medical information. It gives visitors a safe starting path to
 * the controlled content and directory routes already governed by Hea-lth.
 *
 * @package HeaLthPortal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<section class="hp-page-hero hp-page-hero--find-care">
		<div class="hp-shell hp-find-care-hero">
			<div>
				<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'מסלול בחירה', 'hea-lth-portal' ); ?></p>
				<h1><?php echo esc_html( hea_lth_portal_current_title( __( 'מתחילים בשאלה הנכונה', 'hea-lth-portal' ) ) ); ?></h1>
				<p><?php esc_html_e( 'בחירה בין טיפולים, בדיקות, מקצוענים ושירותים מתחילה בהבנת נקודת המוצא. בחרו את המסלול הקרוב ביותר למה שמעסיק אתכם כעת.', 'hea-lth-portal' ); ?></p>
			</div>
			<div class="hp-find-care-hero__diagram" aria-label="<?php esc_attr_e( 'מפת מסלולי בחירה', 'hea-lth-portal' ); ?>">
				<span class="hp-find-care-hero__core"><?php esc_html_e( 'בחירה', 'hea-lth-portal' ); ?></span>
				<span class="hp-find-care-hero__node hp-find-care-hero__node--one">01</span>
				<span class="hp-find-care-hero__node hp-find-care-hero__node--two">02</span>
				<span class="hp-find-care-hero__node hp-find-care-hero__node--three">03</span>
				<span class="hp-find-care-hero__node hp-find-care-hero__node--four">04</span>
			</div>
		</div>
	</section>

	<section class="hp-template-section hp-template-section--paper">
		<div class="hp-shell">
			<div class="hp-section-heading hp-section-heading--split">
				<div>
					<p class="hp-eyebrow"><?php esc_html_e( 'נקודת התחלה', 'hea-lth-portal' ); ?></p>
					<h2><?php esc_html_e( 'לאן נכון להמשיך מכאן?', 'hea-lth-portal' ); ?></h2>
				</div>
				<p><?php esc_html_e( 'כל בחירה מעבירה למסלול מידע או חיפוש מתאים. אין כאן אבחון, המלצה רפואית או בקשה לפרטים אישיים.', 'hea-lth-portal' ); ?></p>
			</div>
			<div class="hp-care-choice-grid">
				<a class="hp-care-choice hp-care-choice--treatment" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'treatments' ) ); ?>">
					<span>01</span><h3><?php esc_html_e( 'טיפול או ניתוח', 'hea-lth-portal' ); ?></h3><p><?php esc_html_e( 'הבנת סוגי שירות, הכנה לפגישה ושאלות להשוואה.', 'hea-lth-portal' ); ?></p><b><?php esc_html_e( 'למרכזי טיפול', 'hea-lth-portal' ); ?><i aria-hidden="true">←</i></b>
				</a>
				<a class="hp-care-choice hp-care-choice--directory" href="<?php echo esc_url( hea_lth_portal_route( 'doctor_clinic_index' ) ); ?>">
					<span>02</span><h3><?php esc_html_e( 'רופא, מרפאה או שירות', 'hea-lth-portal' ); ?></h3><p><?php esc_html_e( 'חיפוש באינדקס לפי תחום, אזור וסוג שירות.', 'hea-lth-portal' ); ?></p><b><?php esc_html_e( 'לפתיחת האינדקס', 'hea-lth-portal' ); ?><i aria-hidden="true">←</i></b>
				</a>
				<a class="hp-care-choice hp-care-choice--diagnostics" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'diagnostics' ) ); ?>">
					<span>03</span><h3><?php esc_html_e( 'בדיקה או דימות', 'hea-lth-portal' ); ?></h3><p><?php esc_html_e( 'מידע על הכנה, מסמכים, פענוח והמשך בירור.', 'hea-lth-portal' ); ?></p><b><?php esc_html_e( 'למרכז הבדיקות', 'hea-lth-portal' ); ?><i aria-hidden="true">←</i></b>
				</a>
				<a class="hp-care-choice hp-care-choice--opinion" href="<?php echo esc_url( hea_lth_portal_route( 'medical_second_opinion' ) ); ?>">
					<span>04</span><h3><?php esc_html_e( 'חוות דעת נוספת', 'hea-lth-portal' ); ?></h3><p><?php esc_html_e( 'ריכוז שאלות ומסמכים לפני שיחה עם איש או אשת מקצוע.', 'hea-lth-portal' ); ?></p><b><?php esc_html_e( 'למסלול חוות הדעת', 'hea-lth-portal' ); ?><i aria-hidden="true">←</i></b>
				</a>
				<a class="hp-care-choice hp-care-choice--technology" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'health_technology' ) ); ?>">
					<span>05</span><h3><?php esc_html_e( 'טכנולוגיה או ציוד', 'hea-lth-portal' ); ?></h3><p><?php esc_html_e( 'כלים, מכשירים ושירותים מתקדמים שדורשים הבנה נוספת.', 'hea-lth-portal' ); ?></p><b><?php esc_html_e( 'לטכנולוגיות בריאות', 'hea-lth-portal' ); ?><i aria-hidden="true">←</i></b>
				</a>
			</div>
		</div>
	</section>

	<section class="hp-template-section hp-template-section--soft">
		<div class="hp-shell hp-find-care-steps">
			<div>
				<p class="hp-eyebrow"><?php esc_html_e( 'כך עובד המסלול', 'hea-lth-portal' ); ?></p>
				<h2><?php esc_html_e( 'קודם מבינים, אחר כך מחליטים איך להמשיך', 'hea-lth-portal' ); ?></h2>
			</div>
			<?php
			hea_lth_portal_render_path_steps(
				array(
					array( 'title' => __( 'בחירה', 'hea-lth-portal' ), 'copy' => __( 'בחרו תחום, שירות או סוג בירור.', 'hea-lth-portal' ) ),
					array( 'title' => __( 'הבנה', 'hea-lth-portal' ), 'copy' => __( 'קראו מידע, הכינו שאלות והכירו אפשרויות.', 'hea-lth-portal' ) ),
					array( 'title' => __( 'השוואה', 'hea-lth-portal' ), 'copy' => __( 'השתמשו באינדקס ובמדריכים כדי להתמקד.', 'hea-lth-portal' ) ),
					array( 'title' => __( 'המשך', 'hea-lth-portal' ), 'copy' => __( 'עברו למסלול הרלוונטי כאשר הוא זמין ומאומת.', 'hea-lth-portal' ) ),
				)
			);
			?>
		</div>
	</section>

	<section class="hp-template-section hp-template-section--paper">
		<div class="hp-find-care-boundaries">
			<div>
				<p class="hp-eyebrow"><?php esc_html_e( 'גבולות ברורים', 'hea-lth-portal' ); ?></p>
				<h2><?php esc_html_e( 'המסלול לא מחליף החלטה רפואית אישית', 'hea-lth-portal' ); ?></h2>
				<p><?php esc_html_e( 'לא נבקש כאן אבחנה, מסמכים רפואיים, רשימת תרופות או מידע בריאותי רגיש. המטרה היא לעזור להתמצא לפני שיחה מקצועית.', 'hea-lth-portal' ); ?></p>
			</div>
			<?php hea_lth_portal_render_information_boundary( true ); ?>
		</div>
	</section>
	<?php
endwhile;

get_footer();
