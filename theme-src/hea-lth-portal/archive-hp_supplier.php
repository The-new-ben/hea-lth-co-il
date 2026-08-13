<?php
/** Supplier showroom index. @package HeaLthPortal */
get_header();
?>
<section class="hp-showroom-index-hero">
	<div class="hp-shell">
		<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'המרקטפלייס המקצועי של Hea-lth', 'hea-lth-portal' ); ?></p>
		<h1><?php esc_html_e( 'ספקים, יבואנים ואולמות תצוגה למרפאות', 'hea-lth-portal' ); ?></h1>
		<p><?php esc_html_e( 'מרכז אחד לתכנון רכש, הכרת טכנולוגיות ובניית סל ציוד למרפאה.', 'hea-lth-portal' ); ?></p>
	</div>
</section>
<section class="hp-template-section hp-template-section--paper">
	<div class="hp-shell">
		<?php if ( have_posts() ) : ?>
			<div class="hp-showroom-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php
					if ( 'verified' !== get_post_meta( get_the_ID(), 'hp_public_state', true ) ) {
						continue;
					}
					?>
					<article class="hp-showroom-card">
						<span class="hp-showroom-card__mark" aria-hidden="true"><?php echo esc_html( substr( get_the_title(), 0, 1 ) ); ?></span>
						<p class="hp-eyebrow"><?php esc_html_e( 'אולם תצוגה מקצועי', 'hea-lth-portal' ); ?></p>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html( get_the_excerpt() ); ?></p>
						<a class="hp-text-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'לצפייה בחברה ובציוד', 'hea-lth-portal' ); ?> <span aria-hidden="true">←</span></a>
					</article>
				<?php endwhile; ?>
			</div>
			<div class="hp-pagination"><?php the_posts_pagination(); ?></div>
		<?php endif; ?>
	</div>
</section>
<?php get_footer(); ?>
