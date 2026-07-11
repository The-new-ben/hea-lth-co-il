<?php
/**
 * Archive template for real WordPress records.
 *
 * @package HeaLthPortal
 */

get_header();
?>
<section class="hp-page-hero hp-page-hero--compact">
	<div class="hp-shell">
		<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'ספריית Hea-lth', 'hea-lth-portal' ); ?></p>
		<h1><?php the_archive_title(); ?></h1>
		<?php the_archive_description( '<div class="hp-page-hero__description">', '</div>' ); ?>
	</div>
</section>

<section class="hp-template-section hp-template-section--paper">
	<div class="hp-shell">
		<?php if ( have_posts() ) : ?>
			<div class="hp-record-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<article <?php post_class( 'hp-record-card' ); ?>>
						<a class="hp-record-card__link" href="<?php the_permalink(); ?>">
							<span><?php echo esc_html( get_the_date( 'j.n.Y' ) ); ?></span>
							<h2><?php the_title(); ?></h2>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
							<b aria-hidden="true">←</b>
						</a>
					</article>
				<?php endwhile; ?>
			</div>
			<div class="hp-pagination"><?php the_posts_pagination(); ?></div>
		<?php else : ?>
			<div class="hp-empty-state">
				<h2><?php esc_html_e( 'אין עדיין רשומות להצגה', 'hea-lth-portal' ); ?></h2>
				<p><?php esc_html_e( 'העמוד מוכן להציג רק תוכן שאושר ופורסם.', 'hea-lth-portal' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php get_footer(); ?>
