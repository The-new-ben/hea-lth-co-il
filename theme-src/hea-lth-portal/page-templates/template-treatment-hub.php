<?php
/**
 * Template Name: מרכזי טיפול
 * Template Post Type: page
 *
 * A reusable commercial-intent hub that does not invent medical outcomes,
 * prices, doctors, or availability.
 *
 * @package HeaLthPortal
 */

get_header();

$steps = array(
	array(
		'title' => __( 'מגדירים את השאלה', 'hea-lth-portal' ),
		'copy'  => __( 'מתחילים בטיפול, בדיקה, אזור גוף או צורך שמבקשים להבין.', 'hea-lth-portal' ),
	),
	array(
		'title' => __( 'מכינים שיחה טובה', 'hea-lth-portal' ),
		'copy'  => __( 'אוספים שאלות ומסמכים רלוונטיים לפני פגישה עם איש מקצוע.', 'hea-lth-portal' ),
	),
	array(
		'title' => __( 'משווים אפשרויות', 'hea-lth-portal' ),
		'copy'  => __( 'בודקים מסלולי מידע, סוגי שירות, תחום מומחיות ומיקום.', 'hea-lth-portal' ),
	),
	array(
		'title' => __( 'ממשיכים בבטחה', 'hea-lth-portal' ),
		'copy'  => __( 'מחפשים רק רשומות ופרטים ציבוריים שניתן להציג באופן שקוף.', 'hea-lth-portal' ),
	),
);

$centers = array(
	array(
		'label' => __( 'רפואה אסתטית', 'hea-lth-portal' ),
		'copy'  => __( 'עור, טיפולים לא ניתוחיים, שאלות ותכנון פגישה.', 'hea-lth-portal' ),
		'route' => 'aesthetic_medicine',
		'tone'  => 'pearl',
	),
	array(
		'label' => __( 'כירורגיה פלסטית', 'hea-lth-portal' ),
		'copy'  => __( 'מסלולי מידע לקראת התייעצות, שחזור או ניתוח.', 'hea-lth-portal' ),
		'route' => 'plastic_surgery_consultation',
		'tone'  => 'forest',
	),
	array(
		'label' => __( 'השתלת שיער', 'hea-lth-portal' ),
		'copy'  => __( 'בירור, אפשרויות טיפול, השתלות והכנה לשיחה.', 'hea-lth-portal' ),
		'route' => 'hair_transplant_consultation',
		'tone'  => 'sand',
	),
	array(
		'label' => __( 'בדיקות ואבחון', 'hea-lth-portal' ),
		'copy'  => __( 'דימות, מעבדה, הכנה והמשך בירור.', 'hea-lth-portal' ),
		'foundationRoute' => 'diagnostics',
		'tone'  => 'mist',
	),
);

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		$title = hea_lth_portal_current_title( __( 'טיפולים וניתוחים', 'hea-lth-portal' ) );
		?>
		<section class="hp-page-hero hp-page-hero--treatment">
			<div class="hp-shell hp-template-hero-grid">
				<div>
					<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'מרכזי בחירה', 'hea-lth-portal' ); ?></p>
					<h1><?php echo esc_html( $title ); ?></h1>
					<p><?php esc_html_e( 'כל מרכז נבנה כדי לעזור להבין אפשרויות, להכין שאלות ולמצוא את מסלול ההמשך המתאים.', 'hea-lth-portal' ); ?></p>
				</div>
				<div class="hp-template-hero-grid__signal" aria-label="<?php esc_attr_e( 'מבנה מסלול הבחירה', 'hea-lth-portal' ); ?>">
					<span>01</span><i></i><span>02</span><i></i><span>03</span>
					<strong><?php esc_html_e( 'שאלה, הבנה, המשך', 'hea-lth-portal' ); ?></strong>
				</div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--paper">
			<div class="hp-shell">
				<div class="hp-section-heading hp-section-heading--split">
					<div>
						<p class="hp-eyebrow"><?php esc_html_e( 'מסלול בחירה', 'hea-lth-portal' ); ?></p>
						<h2><?php esc_html_e( 'לא חייבים להחליט לפני שמבינים', 'hea-lth-portal' ); ?></h2>
					</div>
					<p><?php esc_html_e( 'השלבים כאן אינם המלצה רפואית. הם מסגרת מסודרת לחיפוש, הכנה ושיחה.', 'hea-lth-portal' ); ?></p>
				</div>
				<?php hea_lth_portal_render_path_steps( $steps ); ?>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--soft">
			<div class="hp-shell">
				<div class="hp-section-heading">
					<p class="hp-eyebrow"><?php esc_html_e( 'מרכזי טיפול', 'hea-lth-portal' ); ?></p>
					<h2><?php esc_html_e( 'היכנסו לפי התחום שמעסיק אתכם עכשיו', 'hea-lth-portal' ); ?></h2>
				</div>
				<div class="hp-template-card-grid">
					<?php foreach ( $centers as $center ) : ?>
						<?php $center_url = isset( $center['route'] ) ? hea_lth_portal_route( $center['route'] ) : hea_lth_portal_foundation_route( $center['foundationRoute'] ); ?>
						<a class="hp-template-topic hp-template-topic--<?php echo esc_attr( $center['tone'] ); ?>" href="<?php echo esc_url( $center_url ); ?>">
							<span><?php esc_html_e( 'מרכז בחירה', 'hea-lth-portal' ); ?></span>
							<h3><?php echo esc_html( $center['label'] ); ?></h3>
							<p><?php echo esc_html( $center['copy'] ); ?></p>
							<b aria-hidden="true">←</b>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--paper">
			<div class="hp-reading-layout hp-reading-layout--treatment">
				<div>
					<?php hea_lth_portal_render_current_content(); ?>
				</div>
				<div class="hp-treatment-rail">
					<aside class="hp-action-card">
						<p><?php esc_html_e( 'הצעד הבא', 'hea-lth-portal' ); ?></p>
						<h2><?php esc_html_e( 'חיפוש לפי תחום, אזור וסוג שירות', 'hea-lth-portal' ); ?></h2>
						<a class="hp-button" href="<?php echo esc_url( hea_lth_portal_route( 'doctor_clinic_index' ) ); ?>"><?php esc_html_e( 'פתיחת האינדקס', 'hea-lth-portal' ); ?></a>
					</aside>
					<?php hea_lth_portal_render_information_boundary(); ?>
				</div>
			</div>
		</section>
		<?php
	endwhile;
endif;

get_footer();
