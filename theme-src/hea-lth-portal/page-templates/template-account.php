<?php
/**
 * Template Name: אזור אישי
 * Template Post Type: page
 *
 * This is a real WordPress session-aware entry page. Saved items, inquiries,
 * and profile workflows remain disconnected until their responsible service
 * is implemented and approved.
 *
 * @package HeaLthPortal
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		$is_logged_in = is_user_logged_in();
		$user         = $is_logged_in ? wp_get_current_user() : null;
		$title        = hea_lth_portal_current_title( __( 'האזור האישי', 'hea-lth-portal' ) );
		?>
		<section class="hp-page-hero hp-page-hero--account">
			<div class="hp-shell hp-template-hero-grid">
				<div>
					<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'אזור אישי', 'hea-lth-portal' ); ?></p>
					<h1><?php echo esc_html( $title ); ?></h1>
					<p><?php esc_html_e( 'מקום אחד לשמירת מסלולים ולחזרה למידע שבחרתם, בלי להציג מידע רפואי אישי בעמוד הציבורי.', 'hea-lth-portal' ); ?></p>
				</div>
				<div class="hp-account-hero-badge">
					<span><?php echo $is_logged_in ? esc_html__( 'מחוברים', 'hea-lth-portal' ) : esc_html__( 'כניסה מאובטחת', 'hea-lth-portal' ); ?></span>
					<strong><?php esc_html_e( 'הגישה נשענת על חשבון מאובטח קיים', 'hea-lth-portal' ); ?></strong>
				</div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--paper">
			<div class="hp-shell">
				<?php if ( $is_logged_in && $user ) : ?>
					<div class="hp-account-welcome">
						<div>
							<p class="hp-eyebrow"><?php esc_html_e( 'ברוכים הבאים', 'hea-lth-portal' ); ?></p>
							<h2><?php echo esc_html( $user->display_name ); ?></h2>
							<p><?php esc_html_e( 'עמוד זה מחובר לחשבון הקיים שלכם. פעולות שמירה, תיאום ופניות יופעלו רק לאחר חיבור שירות ייעודי והרשאות מתאימות.', 'hea-lth-portal' ); ?></p>
						</div>
						<a class="hp-button" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'guides' ) ); ?>"><?php esc_html_e( 'לספריית המידע', 'hea-lth-portal' ); ?></a>
					</div>
					<div class="hp-account-grid">
						<article><span>01</span><h3><?php esc_html_e( 'מסלולים שמורים', 'hea-lth-portal' ); ?></h3><p><?php esc_html_e( 'יופיעו כאן כאשר פעולת שמירה תחובר לחשבון.', 'hea-lth-portal' ); ?></p></article>
						<article><span>02</span><h3><?php esc_html_e( 'פרטי תקשורת', 'hea-lth-portal' ); ?></h3><p><?php esc_html_e( 'נשמרים ומוצגים רק דרך תהליך מורשה ומפורש.', 'hea-lth-portal' ); ?></p></article>
						<article><span>03</span><h3><?php esc_html_e( 'העדפות', 'hea-lth-portal' ); ?></h3><p><?php esc_html_e( 'שליטה בהעדפות ובפרטיות תופיע עם שירות החשבון.', 'hea-lth-portal' ); ?></p></article>
					</div>
				<?php else : ?>
					<div class="hp-account-entry">
						<div>
							<p class="hp-eyebrow"><?php esc_html_e( 'גישה לחשבון', 'hea-lth-portal' ); ?></p>
							<h2><?php esc_html_e( 'התחברו כדי לחזור לבחירות ששמרתם', 'hea-lth-portal' ); ?></h2>
							<p><?php esc_html_e( 'החיבור נעשה דרך חשבון מאובטח. אין צורך להזין כאן פרטים רפואיים או מסמכים.', 'hea-lth-portal' ); ?></p>
						</div>
						<div class="hp-account-entry__actions">
							<a class="hp-button" href="<?php echo esc_url( wp_login_url( hea_lth_portal_foundation_route( 'account' ) ) ); ?>"><?php esc_html_e( 'כניסה לחשבון', 'hea-lth-portal' ); ?></a>
							<a class="hp-inline-link" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'contact' ) ); ?>"><?php esc_html_e( 'עזרה בגישה', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--soft">
			<div class="hp-reading-layout hp-reading-layout--account">
				<div><?php hea_lth_portal_render_current_content(); ?></div>
				<aside class="hp-information-boundary">
					<span class="hp-information-boundary__number" aria-hidden="true">✓</span>
					<div>
						<strong><?php esc_html_e( 'מינימום מידע כברירת מחדל', 'hea-lth-portal' ); ?></strong>
						<p><?php esc_html_e( 'החשבון לא נועד לאיסוף אבחנות, תרופות או קבצים רפואיים בשלב זה.', 'hea-lth-portal' ); ?></p>
					</div>
				</aside>
			</div>
		</section>
		<?php
	endwhile;
endif;

get_footer();
