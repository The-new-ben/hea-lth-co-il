<?php
/**
 * Default template.
 *
 * @package HealthRevenue
 */

get_header();
?>

<section class="hr-section hr-section--plain">
	<div class="hr-container">
		<?php if ( have_posts() ) : ?>
			<div class="hr-post-list">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'hr-post-card' ); ?>>
						<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
						<div class="hr-post-card__excerpt">
							<?php the_excerpt(); ?>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<h1><?php esc_html_e( 'לא נמצא תוכן', 'health-revenue' ); ?></h1>
			<p><?php esc_html_e( 'התוכן המבוקש עדיין לא פורסם במערכת החדשה.', 'health-revenue' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
