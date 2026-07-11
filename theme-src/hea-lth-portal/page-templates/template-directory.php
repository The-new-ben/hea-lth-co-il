<?php
/**
 * Template Name: אינדקס מקצוענים
 * Template Post Type: page
 *
 * @package HeaLthPortal
 */

get_header();

$directory_context      = hea_lth_portal_get_directory_context();
$directory_context_json = wp_json_encode( $directory_context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
$directory_is_preview   = defined( 'HEA_LTH_PORTAL_PREVIEW' ) && HEA_LTH_PORTAL_PREVIEW;

$directory_paths = array(
	array( 'title' => __( 'לפי תחום מומחיות', 'hea-lth-portal' ), 'focus' => 'directory-specialty', 'copy' => __( 'התחלה מתחום, שירות או סוג בדיקה.', 'hea-lth-portal' ) ),
	array( 'title' => __( 'לפי אזור', 'hea-lth-portal' ), 'focus' => 'directory-region', 'copy' => __( 'חיפוש לפי עיר, אזור שירות או מרפאה.', 'hea-lth-portal' ) ),
	array( 'title' => __( 'לפי סוג שירות', 'hea-lth-portal' ), 'focus' => 'directory-service', 'copy' => __( 'מרפאה, ייעוץ, דימות או חוות דעת נוספת.', 'hea-lth-portal' ) ),
);

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		$title = hea_lth_portal_current_title( __( 'רופאים, מרפאות ושירותים', 'hea-lth-portal' ) );
		?>
		<section class="hp-page-hero hp-page-hero--directory">
			<div class="hp-shell hp-template-hero-grid">
				<div>
					<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'אינדקס מקצוענים', 'hea-lth-portal' ); ?></p>
					<h1><?php echo esc_html( $title ); ?></h1>
					<p><?php esc_html_e( 'מחפשים לפי מה שחשוב לכם: תחום, אזור, שפה, סוג שירות ופרטים ציבוריים שנבדקו להצגה.', 'hea-lth-portal' ); ?></p>
				</div>
				<div class="hp-directory-hero-card">
					<span><?php esc_html_e( 'עקרון האינדקס', 'hea-lth-portal' ); ?></span>
					<strong><?php esc_html_e( 'אין פרופילים דמיוניים, אין דירוגים ללא שיטה גלויה.', 'hea-lth-portal' ); ?></strong>
				</div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--paper">
			<div class="hp-shell">
				<form id="directory-search" class="hp-directory-form" action="<?php echo esc_url( hea_lth_portal_route( 'doctor_clinic_index' ) ); ?>" method="get" role="search">
					<div>
						<label for="directory-specialty"><?php esc_html_e( 'תחום או שירות', 'hea-lth-portal' ); ?></label>
						<input id="directory-specialty" name="specialty" type="search" value="<?php echo esc_attr( isset( $directory_context['specialty'] ) ? $directory_context['specialty'] : '' ); ?>" placeholder="<?php esc_attr_e( 'למשל: כירורגיה פלסטית, רופא עור', 'hea-lth-portal' ); ?>">
					</div>
					<div>
						<label for="directory-region"><?php esc_html_e( 'אזור שירות', 'hea-lth-portal' ); ?></label>
						<input id="directory-region" name="region" type="text" value="<?php echo esc_attr( isset( $directory_context['region'] ) ? $directory_context['region'] : '' ); ?>" placeholder="<?php esc_attr_e( 'עיר או אזור', 'hea-lth-portal' ); ?>">
					</div>
					<div>
						<label for="directory-service"><?php esc_html_e( 'סוג שירות', 'hea-lth-portal' ); ?></label>
						<input id="directory-service" name="service" type="text" value="<?php echo esc_attr( isset( $directory_context['service'] ) ? $directory_context['service'] : '' ); ?>" placeholder="<?php esc_attr_e( 'למשל: ייעוץ, דימות, חוות דעת', 'hea-lth-portal' ); ?>">
					</div>
					<?php if ( isset( $directory_context['body_region'] ) ) : ?>
						<input name="body_region" type="hidden" value="<?php echo esc_attr( $directory_context['body_region'] ); ?>">
					<?php endif; ?>
					<button class="hp-button" type="submit"><?php esc_html_e( 'חיפוש באתר', 'hea-lth-portal' ); ?></button>
				</form>
				<p class="hp-directory-form__note"><?php esc_html_e( 'החיפוש אינו מבקש מידע רפואי אישי. התוצאות מוצגות רק מרשומות שפורסמו ואומתו.', 'hea-lth-portal' ); ?></p>
				<div class="hp-directory-browser" data-directory-browser data-api-url="<?php echo esc_url( rest_url( 'hea-lth/v1/directory' ) ); ?>" data-directory-filters="<?php echo esc_attr( $directory_context_json ); ?>" data-directory-preview="<?php echo esc_attr( $directory_is_preview ? 'true' : 'false' ); ?>">
					<p class="hp-directory-browser__status" data-directory-status aria-live="polite"><?php esc_html_e( 'בודקים רשומות מאומתות להצגה.', 'hea-lth-portal' ); ?></p>
					<div class="hp-directory-browser__results" data-directory-results aria-live="polite"></div>
				</div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--soft">
			<div class="hp-shell hp-directory-layout">
				<div>
					<div class="hp-section-heading">
						<p class="hp-eyebrow"><?php esc_html_e( 'שלוש נקודות התחלה', 'hea-lth-portal' ); ?></p>
						<h2><?php esc_html_e( 'בחרו את הדרך שנוחה לכם', 'hea-lth-portal' ); ?></h2>
					</div>
					<div class="hp-directory-paths">
						<?php foreach ( $directory_paths as $path ) : ?>
							<button type="button" data-directory-focus="<?php echo esc_attr( $path['focus'] ); ?>" aria-controls="<?php echo esc_attr( $path['focus'] ); ?>">
								<h3><?php echo esc_html( $path['title'] ); ?></h3>
								<p><?php echo esc_html( $path['copy'] ); ?></p>
								<b aria-hidden="true">←</b>
							</button>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="hp-directory-map-shell" aria-label="<?php esc_attr_e( 'אזור המפה יופעל עם רשומות מאומתות', 'hea-lth-portal' ); ?>">
					<div class="hp-directory-map-shell__grid" aria-hidden="true"><i></i><i></i><i></i><i></i></div>
					<div class="hp-directory-map-shell__empty">
						<span><?php esc_html_e( 'שכבת מפה', 'hea-lth-portal' ); ?></span>
						<strong><?php esc_html_e( 'מפה תוצג רק מול נתוני מיקום מאומתים', 'hea-lth-portal' ); ?></strong>
						<p><?php esc_html_e( 'בחירת תחום תעדכן את המפה ואת הרשימה יחד, לאחר חיבור מקור הנתונים.', 'hea-lth-portal' ); ?></p>
					</div>
				</div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--paper">
			<div class="hp-reading-layout hp-reading-layout--directory">
				<div>
					<?php hea_lth_portal_render_current_content(); ?>
				</div>
				<aside class="hp-directory-empty-state">
					<span><?php esc_html_e( 'מצב הצגת פרופילים', 'hea-lth-portal' ); ?></span>
					<h2><?php esc_html_e( 'רשומות יופיעו רק לאחר אימות נתונים ציבוריים', 'hea-lth-portal' ); ?></h2>
					<p><?php esc_html_e( 'שם, תחום, מרפאה, אזור ופרטי התאמה יוצגו רק כשהם קיימים, עדכניים ומורשים לפרסום.', 'hea-lth-portal' ); ?></p>
					<a class="hp-inline-link" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'professionals' ) ); ?>"><?php esc_html_e( 'מידע למקצוענים', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a>
				</aside>
			</div>
		</section>
		<?php
	endwhile;
endif;

get_footer();
