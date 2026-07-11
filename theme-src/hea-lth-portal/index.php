<?php
/**
 * Fallback template.
 *
 * @package HeaLthPortal
 */

get_header();
?>
<section class="hp-page-hero hp-page-hero--compact">
	<div class="hp-shell">
		<p class="hp-eyebrow"><?php esc_html_e( 'Hea-lth Journal', 'hea-lth-portal' ); ?></p>
		<h1><?php esc_html_e( 'מידע לבריאות פרטית, בחירה מודעת ושיחה טובה יותר עם אנשי מקצוע', 'hea-lth-portal' ); ?></h1>
	</div>
</section>
<section class="hp-section">
	<div class="hp-shell hp-content-stack">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>
				<article <?php post_class( 'hp-editorial-card' ); ?>>
					<p class="hp-editorial-card__meta"><?php echo esc_html( get_the_date() ); ?></p>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
					<a class="hp-inline-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'לקריאה', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'התוכן יופיע כאן לאחר פרסום ובקרה.', 'hea-lth-portal' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php get_footer(); ?>
