<?php
/**
 * Not found template.
 *
 * @package HeaLthPortal
 */

get_header();
?>
<section class="hp-page-hero hp-page-hero--compact">
	<div class="hp-shell">
		<p class="hp-eyebrow hp-eyebrow--light">404</p>
		<h1><?php esc_html_e( 'לא מצאנו את העמוד שחיפשתם', 'hea-lth-portal' ); ?></h1>
	</div>
</section>

<section class="hp-template-section hp-template-section--paper">
	<div class="hp-shell hp-not-found">
		<div>
			<h2><?php esc_html_e( 'אפשר להתחיל מחדש ממסלול ברור', 'hea-lth-portal' ); ?></h2>
			<p><?php esc_html_e( 'האתר החדש בנוי סביב מרכזי טיפול, מדריכים, אינדקס מקצוענים וחיפוש לפי צורך.', 'hea-lth-portal' ); ?></p>
		</div>
		<div class="hp-not-found__actions">
			<a class="hp-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'חזרה לדף הבית', 'hea-lth-portal' ); ?></a>
			<a class="hp-inline-link" href="<?php echo esc_url( hea_lth_portal_route( 'doctor_clinic_index' ) ); ?>"><?php esc_html_e( 'אינדקס רופאים ומרפאות', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a>
		</div>
	</div>
</section>
<?php get_footer(); ?>
