<?php
/**
 * Template Name: מרכז תחום
 * Template Post Type: page
 *
 * Generic pillar hub for foundation topics (diagnostics, wellness, private
 * medicine and their children). Renders the page's own reviewed intro content
 * plus a fixed grid of governed portal destinations. No medical claims are
 * generated here; detailed editorial content arrives only through the
 * reviewed-content gates.
 *
 * @package HeaLthPortal
 */

get_header();

$hub_paths = array(
	array(
		'number' => '01',
		'title'  => __( 'מסלול בחירה מודרך', 'hea-lth-portal' ),
		'copy'   => __( 'שאלות מסודרות שמובילות למידע, לשאלות נכונות ולשירותים רלוונטיים.', 'hea-lth-portal' ),
		'url'    => hea_lth_portal_foundation_route( 'find_care' ),
	),
	array(
		'number' => '02',
		'title'  => __( 'רופאים ומרפאות', 'hea-lth-portal' ),
		'copy'   => __( 'אינדקס מקצוענים לפי תחום, אזור ושדות אימות גלויים.', 'hea-lth-portal' ),
		'url'    => hea_lth_portal_route( 'doctor_clinic_index' ),
	),
	array(
		'number' => '03',
		'title'  => __( 'מדריכים ומילון', 'hea-lth-portal' ),
		'copy'   => __( 'ידע ערוך שמתפרסם רק עם אישור עריכתי, תאריך בדיקה ומקור גלוי.', 'hea-lth-portal' ),
		'url'    => hea_lth_portal_foundation_route( 'guides' ),
	),
	array(
		'number' => '04',
		'title'  => __( 'הגוף האינטראקטיבי', 'hea-lth-portal' ),
		'copy'   => __( 'בחירת אזור גוף שמתחברת להקשרים, לתחומים ולשירותים.', 'hea-lth-portal' ),
		'url'    => hea_lth_portal_foundation_route( 'anatomy' ),
	),
);

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		$title = hea_lth_portal_current_title( get_the_title() );
		?>
		<section class="hp-page-hero hp-page-hero--technology">
			<div class="hp-shell hp-template-hero-grid">
				<div>
					<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'מרכז תחום', 'hea-lth-portal' ); ?></p>
					<h1><?php echo esc_html( $title ); ?></h1>
					<p><?php esc_html_e( 'נקודת פתיחה מסודרת: מידע, שאלות והמשך דרך, בקצב שלכם ולפי הצורך שלכם.', 'hea-lth-portal' ); ?></p>
				</div>
				<div class="hp-technology-hero-core" aria-hidden="true"><span>H</span><i></i><i></i><i></i></div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--paper">
			<div class="hp-reading-layout">
				<div><?php hea_lth_portal_render_current_content(); ?></div>
				<?php hea_lth_portal_render_information_boundary(); ?>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--soft">
			<div class="hp-shell">
				<div class="hp-section-heading">
					<p class="hp-eyebrow"><?php esc_html_e( 'ממשיכים מכאן', 'hea-lth-portal' ); ?></p>
					<h2><?php esc_html_e( 'הדרכים המרכזיות להמשך', 'hea-lth-portal' ); ?></h2>
				</div>
				<div class="hp-technology-path-grid">
					<?php foreach ( $hub_paths as $path ) : ?>
						<a href="<?php echo esc_url( $path['url'] ); ?>">
							<span><?php echo esc_html( $path['number'] ); ?></span>
							<h3><?php echo esc_html( $path['title'] ); ?></h3>
							<p><?php echo esc_html( $path['copy'] ); ?></p>
							<b aria-hidden="true">←</b>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--paper">
			<div class="hp-shell hp-catalog-gate">
				<div>
					<p class="hp-eyebrow"><?php esc_html_e( 'תקן הפרסום שלנו', 'hea-lth-portal' ); ?></p>
					<h2><?php esc_html_e( 'תכנים מפורטים מתפרסמים רק אחרי בדיקה', 'hea-lth-portal' ); ?></h2>
					<p><?php esc_html_e( 'כל מדריך במרכז הזה יופיע עם אישור עריכתי, תאריך בדיקה ומקור גלוי. עד אז, מסלולי הבחירה והאינדקס זמינים במלואם.', 'hea-lth-portal' ); ?></p>
				</div>
				<div class="hp-catalog-gate__tokens" aria-hidden="true"><span>01</span><span>02</span><span>03</span></div>
			</div>
		</section>
		<?php
	endwhile;
endif;

get_footer();
