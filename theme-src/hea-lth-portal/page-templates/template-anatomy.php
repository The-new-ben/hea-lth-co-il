<?php
/**
 * Template Name: הגוף האינטראקטיבי
 * Template Post Type: page
 *
 * The page contains the accessible semantic resolver and its non-3D fallback.
 * It must not display a human model until the asset acceptance gate passes.
 *
 * @package HeaLthPortal
 */

get_header();

$anatomy_model_config = function_exists( 'hea_lth_portal_anatomy_viewer_config' )
	? hea_lth_portal_anatomy_viewer_config()
	: array(
		'status' => 'license-gated',
		'engine' => 'none',
	);
$anatomy_model_ready = isset( $anatomy_model_config['status'], $anatomy_model_config['engine'] ) && 'approved' === $anatomy_model_config['status'] && 'three-webgl' === $anatomy_model_config['engine'];

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		$title = hea_lth_portal_current_title( __( 'הגוף האינטראקטיבי', 'hea-lth-portal' ) );
		?>
		<section class="hp-page-hero hp-page-hero--anatomy">
			<div class="hp-shell hp-template-hero-grid">
				<div>
					<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'חוויית גוף ומסלולי בחירה', 'hea-lth-portal' ); ?></p>
					<h1><?php echo esc_html( $title ); ?></h1>
					<p><?php esc_html_e( 'חקרו אזור גוף והמשיכו למידע, תחומים ושירותים רלוונטיים. זהו כלי לגילוי, לא לאבחון או להמלצה טיפולית.', 'hea-lth-portal' ); ?></p>
				</div>
				<div class="hp-anatomy-hero-proof">
					<span><?php echo esc_html( $anatomy_model_ready ? __( 'מודל תלת ממד', 'hea-lth-portal' ) : __( 'שער נכס', 'hea-lth-portal' ) ); ?></span>
					<strong><?php echo esc_html( $anatomy_model_ready ? __( 'המודל האנטומי עבר את שערי הרישוי, הבדיקה הקלינית והביצועים שהוגדרו למערכת.', 'hea-lth-portal' ) : __( 'הדגם הציבורי יופעל רק אחרי רישוי, בדיקה קלינית ובדיקת ביצועים.', 'hea-lth-portal' ) ); ?></strong>
				</div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--anatomy">
			<div class="hp-shell">
				<div class="hp-anatomy-viewer" data-anatomy-viewer data-config-url="<?php echo esc_url( get_theme_file_uri( 'assets/data/anatomy-discovery-v1.json' ) ); ?>">
					<div class="hp-anatomy-viewer__topline">
						<div><span><?php esc_html_e( 'גילוי אנטומי', 'hea-lth-portal' ); ?></span><strong><?php esc_html_e( 'בחרו אזור והקשר', 'hea-lth-portal' ); ?></strong></div>
						<p data-anatomy-status aria-live="polite"><?php esc_html_e( 'טוענים את מסלולי הבחירה.', 'hea-lth-portal' ); ?></p>
					</div>
					<div class="hp-anatomy-viewer__grid">
						<div class="hp-anatomy-model-stage" data-anatomy-model-stage aria-label="<?php esc_attr_e( 'אזור מודל אנטומי', 'hea-lth-portal' ); ?>"></div>
						<div class="hp-anatomy-selector">
							<section>
								<p><?php esc_html_e( 'אזור גוף', 'hea-lth-portal' ); ?></p>
								<div class="hp-anatomy-controls" data-anatomy-region-controls></div>
							</section>
							<section>
								<p><?php esc_html_e( 'הקשר לבחירה', 'hea-lth-portal' ); ?></p>
								<div class="hp-anatomy-controls" data-anatomy-context-controls></div>
							</section>
						</div>
					</div>
					<div class="hp-anatomy-results" data-anatomy-results aria-live="polite"></div>
					<section class="hp-anatomy-directory-map" data-anatomy-directory-map data-map-endpoint="<?php echo esc_url( rest_url( 'hea-lth/v1/directory/map' ) ); ?>">
						<div class="hp-anatomy-directory-map__heading">
							<div>
								<p class="hp-eyebrow"><?php esc_html_e( 'מפת שירותים מאומתים', 'hea-lth-portal' ); ?></p>
								<h2><?php esc_html_e( 'הבחירה בגוף יכולה לעדכן גם את המפה', 'hea-lth-portal' ); ?></h2>
							</div>
							<p><?php esc_html_e( 'מיקומים יוצגו רק כשיש הסכמה להצגת מיקום, אימות פרופיל ובדיקת נתוני מפה.', 'hea-lth-portal' ); ?></p>
						</div>
						<p class="hp-anatomy-directory-map__status" data-directory-map-status aria-live="polite"><?php esc_html_e( 'מפת השירותים ממתינה לאישור תצורה ולבחירת אזור.', 'hea-lth-portal' ); ?></p>
						<div class="hp-anatomy-directory-map__canvas" data-directory-map-canvas role="region" aria-label="<?php esc_attr_e( 'מפת שירותים מאומתים', 'hea-lth-portal' ); ?>"></div>
						<div class="hp-anatomy-directory-map__results" data-directory-map-results aria-live="polite"></div>
					</section>
				</div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--paper">
			<div class="hp-shell hp-anatomy-alternative">
				<div>
					<p class="hp-eyebrow"><?php esc_html_e( 'גם ללא תלת ממד', 'hea-lth-portal' ); ?></p>
					<h2><?php esc_html_e( 'כל מסלול נשאר זמין גם בחיפוש רגיל', 'hea-lth-portal' ); ?></h2>
					<p><?php esc_html_e( 'הגישה למידע, לאינדקס ולמפה אינה תלויה במנוע גרפי. זו חלופה קבועה ונגישה, לא מצב חירום.', 'hea-lth-portal' ); ?></p>
				</div>
				<div class="hp-anatomy-alternative__links">
					<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'treatments' ) ); ?>"><?php esc_html_e( 'מרכזי טיפול', 'hea-lth-portal' ); ?><b aria-hidden="true">←</b></a>
					<a href="<?php echo esc_url( hea_lth_portal_route( 'doctor_clinic_index' ) ); ?>"><?php esc_html_e( 'אינדקס מקצוענים', 'hea-lth-portal' ); ?><b aria-hidden="true">←</b></a>
					<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'guides' ) ); ?>"><?php esc_html_e( 'מדריכים ומילון', 'hea-lth-portal' ); ?><b aria-hidden="true">←</b></a>
				</div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--soft">
			<div class="hp-reading-layout hp-reading-layout--anatomy">
				<div><?php hea_lth_portal_render_current_content(); ?></div>
				<?php hea_lth_portal_render_information_boundary( true ); ?>
			</div>
		</section>
		<?php
	endwhile;
endif;

get_footer();
