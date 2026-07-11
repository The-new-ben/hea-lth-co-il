<?php
/**
 * Template Name: אזור למקצוענים
 * Template Post Type: page
 *
 * @package HeaLthPortal
 */

get_header();

$profile_principles = array(
	array( 'number' => '01', 'title' => __( 'פרופיל ברור', 'hea-lth-portal' ), 'copy' => __( 'תחום, מרפאה, אזור, שפות, נגישות ופרטי שירות מוצגים במבנה קבוע.', 'hea-lth-portal' ) ),
	array( 'number' => '02', 'title' => __( 'עדכון מבוקר', 'hea-lth-portal' ), 'copy' => __( 'פרטים ציבוריים עוברים תהליך בדיקה לפני הצגה או שינוי.', 'hea-lth-portal' ) ),
	array( 'number' => '03', 'title' => __( 'חיבור להקשר', 'hea-lth-portal' ), 'copy' => __( 'הפרופיל יתחבר רק לתחומים, מדריכים ושירותים שיש להם הקשר ברור.', 'hea-lth-portal' ) ),
	array( 'number' => '04', 'title' => __( 'שקיפות לציבור', 'hea-lth-portal' ), 'copy' => __( 'האתר נועד לאפשר לאנשים להבין מה ניתן למצוא בכל פרופיל.', 'hea-lth-portal' ) ),
);

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		$title = hea_lth_portal_current_title( __( 'לרופאים, מרפאות ונותני שירות', 'hea-lth-portal' ) );
		?>
		<section class="hp-page-hero hp-page-hero--professionals">
			<div class="hp-shell hp-template-hero-grid">
				<div>
					<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'אזור למקצוענים', 'hea-lth-portal' ); ?></p>
					<h1><?php echo esc_html( $title ); ?></h1>
					<p><?php esc_html_e( 'הצטרפות לאינדקס מתחילה בפרופיל מקצועי ומסודר, שנועד להסביר לציבור במה אתם עוסקים ואיך ניתן ליצור קשר.', 'hea-lth-portal' ); ?></p>
					<a class="hp-button hp-button--light" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'contact' ) ); ?>"><?php esc_html_e( 'פתיחת שיחת היכרות', 'hea-lth-portal' ); ?></a>
				</div>
				<div class="hp-professional-hero-rail" aria-hidden="true">
					<div><span>01</span><strong><?php esc_html_e( 'פרופיל', 'hea-lth-portal' ); ?></strong></div>
					<div><span>02</span><strong><?php esc_html_e( 'בדיקה', 'hea-lth-portal' ); ?></strong></div>
					<div><span>03</span><strong><?php esc_html_e( 'הצגה', 'hea-lth-portal' ); ?></strong></div>
				</div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--paper">
			<div class="hp-shell">
				<div class="hp-section-heading hp-section-heading--split">
					<div>
						<p class="hp-eyebrow"><?php esc_html_e( 'מה הציבור יפגוש', 'hea-lth-portal' ); ?></p>
						<h2><?php esc_html_e( 'מבנה אחיד שמכבד את זמנו של מי שמחפש שירות', 'hea-lth-portal' ); ?></h2>
					</div>
					<p><?php esc_html_e( 'העמוד הציבורי אינו מבטיח תוצאות או זמינות. הוא מציג עובדות שניתן לאמת ודרך מסודרת להמשך בירור.', 'hea-lth-portal' ); ?></p>
				</div>
				<div class="hp-professional-principles">
					<?php foreach ( $profile_principles as $principle ) : ?>
						<article>
							<span><?php echo esc_html( $principle['number'] ); ?></span>
							<h3><?php echo esc_html( $principle['title'] ); ?></h3>
							<p><?php echo esc_html( $principle['copy'] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--soft">
			<div class="hp-shell hp-professionals-next">
				<div>
					<p class="hp-eyebrow"><?php esc_html_e( 'איך מתחילים', 'hea-lth-portal' ); ?></p>
					<h2><?php esc_html_e( 'מכינים את הפרטים הציבוריים לפני שמבקשים להופיע באינדקס', 'hea-lth-portal' ); ?></h2>
					<p><?php esc_html_e( 'תחום עיסוק, פרטי מרפאה, אזור שירות, שפות, נגישות, סוגי שירות ופרטי קשר הם בסיס לשיחה מסודרת על פרופיל עתידי.', 'hea-lth-portal' ); ?></p>
				</div>
				<div class="hp-professionals-next__actions">
					<a class="hp-button" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'contact' ) ); ?>"><?php esc_html_e( 'יצירת קשר', 'hea-lth-portal' ); ?></a>
					<a class="hp-inline-link" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'professional_profile_update' ) ); ?>"><?php esc_html_e( 'בקשה לעדכון פרופיל', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a>
				</div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--paper">
			<div class="hp-reading-layout hp-reading-layout--professionals">
				<div><?php hea_lth_portal_render_current_content(); ?></div>
				<aside class="hp-information-boundary">
					<span class="hp-information-boundary__number" aria-hidden="true">✓</span>
					<div>
						<strong><?php esc_html_e( 'המידע הציבורי נשמר נפרד מתפעול פנימי', 'hea-lth-portal' ); ?></strong>
						<p><?php esc_html_e( 'הציבור רואה רק פרטים שנועדו להופיע בפרופיל. נתוני תפעול, הסכמים ושיחות אינם חלק מהתבנית הציבורית.', 'hea-lth-portal' ); ?></p>
					</div>
				</aside>
			</div>
		</section>
		<?php
	endwhile;
endif;

get_footer();
