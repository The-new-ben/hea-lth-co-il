<?php
/**
 * Editorial article template.
 *
 * @package HeaLthPortal
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		$editorial_status = hea_lth_portal_get_editorial_status( get_the_ID() );
		?>
		<section class="hp-page-hero hp-page-hero--editorial">
			<div class="hp-shell hp-editorial-hero">
				<div>
					<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'מרכז המדריכים', 'hea-lth-portal' ); ?></p>
					<h1><?php the_title(); ?></h1>
				</div>
				<p class="hp-editorial-hero__meta">
					<?php if ( $editorial_status['is_reviewed'] ) : ?>
						<?php esc_html_e( 'נבדק:', 'hea-lth-portal' ); ?> <?php echo esc_html( $editorial_status['last_reviewed'] ); ?>
					<?php else : ?>
						<?php esc_html_e( 'פורסם:', 'hea-lth-portal' ); ?> <?php echo esc_html( get_the_date( 'j.n.Y' ) ); ?>
					<?php endif; ?>
				</p>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--paper">
			<div class="hp-reading-layout">
				<article <?php post_class( 'hp-page-article hp-page-article--single' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="hp-page-article__media"><?php the_post_thumbnail( 'large' ); ?></div>
					<?php endif; ?>
					<?php hea_lth_portal_render_current_content(); ?>
				</article>
				<div class="hp-reading-layout__rail">
					<aside class="hp-editorial-status">
						<span><?php esc_html_e( 'סטטוס תוכן', 'hea-lth-portal' ); ?></span>
						<?php if ( $editorial_status['is_reviewed'] ) : ?>
							<strong><?php esc_html_e( 'רשומה זו נבדקה ומציגה את פרטי המקור שלה.', 'hea-lth-portal' ); ?></strong>
							<dl class="hp-editorial-status__evidence">
								<dt><?php esc_html_e( 'נבדק:', 'hea-lth-portal' ); ?></dt>
								<dd><?php echo esc_html( $editorial_status['last_reviewed'] ); ?></dd>
								<dt><?php esc_html_e( 'מקור:', 'hea-lth-portal' ); ?></dt>
								<dd><?php echo esc_html( $editorial_status['source_note'] ); ?></dd>
							</dl>
						<?php else : ?>
							<strong><?php esc_html_e( 'פרטי בדיקה ומקור עדיין אינם מוגדרים במלואם.', 'hea-lth-portal' ); ?></strong>
							<p><?php esc_html_e( 'הרשומה נשארת זמינה בכתובת הקיימת שלה, אך אינה מוצגת בפידים של תוכן שנבדק עד להשלמת תהליך העריכה.', 'hea-lth-portal' ); ?></p>
						<?php endif; ?>
					</aside>
					<?php hea_lth_portal_render_information_boundary( true ); ?>
				</div>
			</div>
		</section>
		<?php
	endwhile;
endif;

get_footer();
