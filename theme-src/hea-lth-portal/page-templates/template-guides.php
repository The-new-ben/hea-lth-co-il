<?php
/**
 * Template Name: מדריכים ומחקרים
 * Template Post Type: page
 *
 * @package HeaLthPortal
 */

get_header();

$guides_query = hea_lth_portal_get_reviewed_guides( 9 );

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		$title = hea_lth_portal_current_title( __( 'מדריכים ומחקרים', 'hea-lth-portal' ) );
		?>
		<section class="hp-page-hero hp-page-hero--guides">
			<div class="hp-shell hp-template-hero-grid">
				<div>
					<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'מרכז המדריכים', 'hea-lth-portal' ); ?></p>
					<h1><?php echo esc_html( $title ); ?></h1>
					<p><?php esc_html_e( 'ספריית ידע שמחברת בין מדריכים, מילון, שאלות הכנה ומקורות שיוצגו באופן גלוי עם פרטי עדכון ובדיקה.', 'hea-lth-portal' ); ?></p>
				</div>
				<div class="hp-guide-hero-stats" aria-label="<?php esc_attr_e( 'עקרונות ספריית הידע', 'hea-lth-portal' ); ?>">
					<div><span>01</span><strong><?php esc_html_e( 'מקורות', 'hea-lth-portal' ); ?></strong></div>
					<div><span>02</span><strong><?php esc_html_e( 'עדכון', 'hea-lth-portal' ); ?></strong></div>
					<div><span>03</span><strong><?php esc_html_e( 'בקרה', 'hea-lth-portal' ); ?></strong></div>
				</div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--paper">
			<div class="hp-shell">
				<div class="hp-guide-navigation">
					<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'guides' ) ); ?>"><?php esc_html_e( 'מדריכים', 'hea-lth-portal' ); ?></a>
					<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'glossary' ) ); ?>"><?php esc_html_e( 'מילון בריאות', 'hea-lth-portal' ); ?></a>
					<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'health_technology' ) ); ?>"><?php esc_html_e( 'טכנולוגיות בריאות', 'hea-lth-portal' ); ?></a>
					<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'private_medicine' ) ); ?>"><?php esc_html_e( 'הכנה לרפואה פרטית', 'hea-lth-portal' ); ?></a>
				</div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--soft">
			<div class="hp-shell">
				<div class="hp-section-heading hp-section-heading--split">
					<div>
						<p class="hp-eyebrow"><?php esc_html_e( 'פרסומים אחרונים', 'hea-lth-portal' ); ?></p>
						<h2><?php esc_html_e( 'רק תוכן שקיים במערכת מוצג כאן', 'hea-lth-portal' ); ?></h2>
					</div>
					<p><?php esc_html_e( 'העיצוב אינו מייצר כתבות, מקורות או זהויות מקצועיות. הרשימה קוראת רק רשומות שפורסמו בוורדפרס.', 'hea-lth-portal' ); ?></p>
				</div>

				<?php if ( $guides_query->have_posts() ) : ?>
					<div class="hp-record-grid hp-record-grid--journal">
						<?php while ( $guides_query->have_posts() ) : $guides_query->the_post(); ?>
							<?php
							$reviewed_date = (string) get_post_meta( get_the_ID(), 'hp_last_reviewed', true );
							$source_note   = (string) get_post_meta( get_the_ID(), 'hp_source_note', true );
							?>
							<article <?php post_class( 'hp-record-card' ); ?>>
								<a class="hp-record-card__link" href="<?php the_permalink(); ?>">
									<span><?php esc_html_e( 'נבדק:', 'hea-lth-portal' ); ?> <?php echo esc_html( $reviewed_date ); ?></span>
									<h3><?php the_title(); ?></h3>
									<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 26 ) ); ?></p>
									<small class="hp-record-card__evidence"><strong><?php esc_html_e( 'מקור:', 'hea-lth-portal' ); ?></strong> <?php echo esc_html( $source_note ); ?></small>
									<b aria-hidden="true">←</b>
								</a>
							</article>
						<?php endwhile; ?>
					</div>
					<?php wp_reset_postdata(); ?>
				<?php else : ?>
					<div class="hp-empty-state">
						<h3><?php esc_html_e( 'עדיין אין מדריכים מאושרים להצגה', 'hea-lth-portal' ); ?></h3>
						<p><?php esc_html_e( 'כאן יופיעו פרסומים לאחר מחקר, כתיבה, מקור, בדיקה ואישור פרסום.', 'hea-lth-portal' ); ?></p>
					</div>
				<?php endif; ?>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--paper">
			<div class="hp-reading-layout hp-reading-layout--guides">
				<div><?php hea_lth_portal_render_current_content(); ?></div>
				<?php hea_lth_portal_render_information_boundary( true ); ?>
			</div>
		</section>
		<?php
	endwhile;
endif;

wp_reset_postdata();
get_footer();
