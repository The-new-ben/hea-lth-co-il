<?php
/**
 * Default page template.
 *
 * @package HeaLthPortal
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		$title = hea_lth_portal_current_title( __( 'מידע ושירותים', 'hea-lth-portal' ) );
		?>
		<section class="hp-page-hero hp-page-hero--compact">
			<div class="hp-shell">
				<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'hea-lth', 'hea-lth-portal' ); ?></p>
				<h1><?php echo esc_html( $title ); ?></h1>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--paper">
			<div class="hp-reading-layout">
				<article <?php post_class( 'hp-page-article' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="hp-page-article__media"><?php the_post_thumbnail( 'large' ); ?></div>
					<?php endif; ?>
					<?php hea_lth_portal_render_current_content(); ?>
				</article>
				<?php hea_lth_portal_render_information_boundary(); ?>
			</div>
		</section>
		<?php
	endwhile;
endif;

get_footer();
