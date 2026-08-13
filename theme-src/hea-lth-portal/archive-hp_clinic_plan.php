<?php
/** Clinic-build plan index. @package HeaLthPortal */
get_header();
?>
<section class="hp-clinic-build-hero">
	<div class="hp-shell">
		<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'מתכנון לרכש', 'hea-lth-portal' ); ?></p>
		<h1><?php esc_html_e( 'הקמת מרפאה כמערכת עסקית אחת', 'hea-lth-portal' ); ?></h1>
		<p><?php esc_html_e( 'מפות רכש לפי סוג מרפאה: ציוד, טכנולוגיה, תשתיות, שירותים, תוכנה ומנועי צמיחה.', 'hea-lth-portal' ); ?></p>
	</div>
</section>
<section class="hp-template-section hp-template-section--paper">
	<div class="hp-shell hp-clinic-plan-grid">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php
			if ( 'verified' !== get_post_meta( get_the_ID(), 'hp_public_state', true ) ) {
				continue;
			}
			?>
			<article class="hp-clinic-plan-card">
				<p class="hp-eyebrow"><?php esc_html_e( 'מפת הקמה ורכש', 'hea-lth-portal' ); ?></p>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<p><?php echo esc_html( get_the_excerpt() ); ?></p>
				<a class="hp-button" href="<?php the_permalink(); ?>"><?php esc_html_e( 'פתיחת תכנית ההקמה', 'hea-lth-portal' ); ?></a>
			</article>
		<?php endwhile; ?>
	</div>
</section>
<?php get_footer(); ?>
