<?php
/**
 * Public treatment presentation after editorial review.
 *
 * The template is intentionally content-led. It never manufactures clinical
 * facts, provider claims, prices, outcomes, rankings, or a contact route.
 *
 * @package HeaLthPortal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$treatment_id      = get_queried_object_id();
$editorial_status  = hea_lth_portal_get_editorial_status( $treatment_id );
$treatment_content = trim( (string) get_the_content( null, false, $treatment_id ) );

if ( 'hp_treatment' !== get_post_type( $treatment_id ) || ! $editorial_status['is_reviewed'] || '' === wp_strip_all_tags( $treatment_content ) ) {
	?>
	<section class="hp-template-section hp-template-section--paper">
		<div class="hp-shell hp-treatment-not-found">
			<p class="hp-eyebrow"><?php esc_html_e( 'מידע אינו זמין', 'hea-lth-portal' ); ?></p>
			<h1><?php esc_html_e( 'העמוד המבוקש אינו זמין להצגה', 'hea-lth-portal' ); ?></h1>
			<p><?php esc_html_e( 'אפשר להמשיך למרכזי הטיפול או לספריית המדריכים.', 'hea-lth-portal' ); ?></p>
			<a class="hp-button" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'treatments' ) ); ?>"><?php esc_html_e( 'למרכזי הטיפול', 'hea-lth-portal' ); ?></a>
		</div>
	</section>
	<?php
	return;
}

$specialties   = get_the_terms( $treatment_id, 'hp_specialty' );
$service_types = get_the_terms( $treatment_id, 'hp_service_type' );
$body_regions  = get_the_terms( $treatment_id, 'hp_body_region' );
$topic_groups  = array(
	array( 'label' => __( 'תחומי מומחיות', 'hea-lth-portal' ), 'terms' => $specialties ),
	array( 'label' => __( 'סוגי שירות', 'hea-lth-portal' ), 'terms' => $service_types ),
	array( 'label' => __( 'אזורי גוף', 'hea-lth-portal' ), 'terms' => $body_regions ),
);
?>

<section class="hp-page-hero hp-page-hero--treatment-detail">
	<div class="hp-shell hp-treatment-hero">
		<div>
			<a class="hp-profile-hero__back" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'treatments' ) ); ?>"><span aria-hidden="true">→</span><?php esc_html_e( 'חזרה למרכזי טיפול', 'hea-lth-portal' ); ?></a>
			<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'טיפול ומסלול מידע', 'hea-lth-portal' ); ?></p>
			<h1><?php echo esc_html( get_the_title( $treatment_id ) ); ?></h1>
			<p><?php esc_html_e( 'עמוד מידע שנבדק לפני הצגה. ההחלטה אם טיפול מתאים מתקבלת רק בשיחה עם איש או אשת מקצוע מוסמכים.', 'hea-lth-portal' ); ?></p>
		</div>
		<dl class="hp-treatment-evidence" aria-label="<?php esc_attr_e( 'פרטי בדיקת התוכן', 'hea-lth-portal' ); ?>">
			<div><dt><?php esc_html_e( 'מצב', 'hea-lth-portal' ); ?></dt><dd><?php esc_html_e( 'נבדק להצגה', 'hea-lth-portal' ); ?></dd></div>
			<div><dt><?php esc_html_e( 'עודכן', 'hea-lth-portal' ); ?></dt><dd><time datetime="<?php echo esc_attr( $editorial_status['last_reviewed'] ); ?>"><?php echo esc_html( $editorial_status['last_reviewed'] ); ?></time></dd></div>
			<div><dt><?php esc_html_e( 'מקור', 'hea-lth-portal' ); ?></dt><dd><?php echo esc_html( $editorial_status['source_note'] ); ?></dd></div>
		</dl>
	</div>
</section>

<section class="hp-template-section hp-template-section--paper">
	<div class="hp-treatment-layout">
		<article class="hp-treatment-content">
			<?php foreach ( $topic_groups as $group ) : ?>
				<?php if ( is_array( $group['terms'] ) && ! empty( $group['terms'] ) ) : ?>
					<section class="hp-treatment-topics">
						<h2><?php echo esc_html( $group['label'] ); ?></h2>
						<ul>
							<?php foreach ( $group['terms'] as $term ) : ?>
								<li><?php echo esc_html( $term->name ); ?></li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>
			<?php endforeach; ?>

			<section class="hp-treatment-article">
				<p class="hp-eyebrow"><?php esc_html_e( 'המידע בעמוד', 'hea-lth-portal' ); ?></p>
				<div class="hp-prose"><?php echo apply_filters( 'the_content', $treatment_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			</section>
		</article>

		<aside class="hp-treatment-rail">
			<section class="hp-treatment-next">
				<p class="hp-eyebrow"><?php esc_html_e( 'השלב הבא', 'hea-lth-portal' ); ?></p>
				<h2><?php esc_html_e( 'להמשיך עם שאלות ברורות ומידע מאומת', 'hea-lth-portal' ); ?></h2>
				<p><?php esc_html_e( 'אפשר להשוות מסלולים, לעבור לאינדקס או לקרוא מדריך הכנה. העמוד אינו מבטיח התאמה, תוצאה או זמינות.', 'hea-lth-portal' ); ?></p>
				<a class="hp-button" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'find_care' ) ); ?>"><?php esc_html_e( 'בדיקת אפשרויות להמשך', 'hea-lth-portal' ); ?></a>
				<a class="hp-inline-link hp-inline-link--light" href="<?php echo esc_url( hea_lth_portal_route( 'doctor_clinic_index' ) ); ?>"><?php esc_html_e( 'פתיחת אינדקס מקצוענים', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a>
			</section>
			<?php hea_lth_portal_render_information_boundary( true ); ?>
		</aside>
	</div>
</section>
