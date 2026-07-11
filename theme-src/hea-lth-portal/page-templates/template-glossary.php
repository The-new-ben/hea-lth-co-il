<?php
/**
 * Template Name: מילון בריאות
 * Template Post Type: page
 *
 * @package HeaLthPortal
 */

get_header();

$glossary_query = hea_lth_portal_get_reviewed_glossary_terms( 18 );

$letters = array( 'א', 'ב', 'ג', 'ד', 'ה', 'ו', 'ז', 'ח', 'ט', 'י', 'כ', 'ל', 'מ', 'נ', 'ס', 'ע', 'פ', 'צ', 'ק', 'ר', 'ש', 'ת' );

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		$title = hea_lth_portal_current_title( __( 'מילון בריאות', 'hea-lth-portal' ) );
		?>
		<section class="hp-page-hero hp-page-hero--glossary">
			<div class="hp-shell hp-template-hero-grid">
				<div>
					<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'מילון בריאות', 'hea-lth-portal' ); ?></p>
					<h1><?php echo esc_html( $title ); ?></h1>
					<p><?php esc_html_e( 'מקום אחד לפירושים בהירים של מונחים רפואיים, טכנולוגיות ושמות שירותים, עם קישור למקורות כאשר רשומה מאושרת לפרסום.', 'hea-lth-portal' ); ?></p>
				</div>
				<div class="hp-glossary-hero-letter" aria-hidden="true">א</div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--paper">
			<div class="hp-shell">
				<form class="hp-search-page-form hp-glossary-search" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search">
					<label class="screen-reader-text" for="glossary-search"><?php esc_html_e( 'חיפוש במילון', 'hea-lth-portal' ); ?></label>
					<input id="glossary-search" name="s" type="search" placeholder="<?php esc_attr_e( 'חפשו מונח או טכנולוגיה', 'hea-lth-portal' ); ?>">
					<button class="hp-button" type="submit"><?php esc_html_e( 'חיפוש במילון', 'hea-lth-portal' ); ?></button>
				</form>
				<div class="hp-glossary-letters" aria-label="<?php esc_attr_e( 'אינדקס אותיות', 'hea-lth-portal' ); ?>">
					<?php foreach ( $letters as $letter ) : ?>
						<span><?php echo esc_html( $letter ); ?></span>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--soft">
			<div class="hp-shell">
				<div class="hp-section-heading hp-section-heading--split">
					<div>
						<p class="hp-eyebrow"><?php esc_html_e( 'רשומות מאושרות', 'hea-lth-portal' ); ?></p>
						<h2><?php esc_html_e( 'כל מונח נבנה כרשומה עצמאית ומבוקרת', 'hea-lth-portal' ); ?></h2>
					</div>
					<p><?php esc_html_e( 'אין כאן הגדרות שנוצרו אוטומטית. הרשימה קוראת רק תוכן שפורסם בפועל בקטגוריית המילון.', 'hea-lth-portal' ); ?></p>
				</div>

				<?php if ( $glossary_query->have_posts() ) : ?>
					<div class="hp-glossary-grid">
						<?php while ( $glossary_query->have_posts() ) : $glossary_query->the_post(); ?>
							<?php
							$reviewed_date = (string) get_post_meta( get_the_ID(), 'hp_last_reviewed', true );
							$source_note   = (string) get_post_meta( get_the_ID(), 'hp_source_note', true );
							?>
							<a href="<?php the_permalink(); ?>">
								<span><?php esc_html_e( 'מונח שנבדק', 'hea-lth-portal' ); ?></span>
								<h3><?php the_title(); ?></h3>
								<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
								<small class="hp-glossary-card__evidence"><strong><?php esc_html_e( 'נבדק:', 'hea-lth-portal' ); ?></strong> <?php echo esc_html( $reviewed_date ); ?> <strong><?php esc_html_e( 'מקור:', 'hea-lth-portal' ); ?></strong> <?php echo esc_html( $source_note ); ?></small>
								<b aria-hidden="true">←</b>
							</a>
						<?php endwhile; ?>
					</div>
					<?php wp_reset_postdata(); ?>
				<?php else : ?>
					<div class="hp-empty-state">
						<h3><?php esc_html_e( 'עדיין אין מונחים מאושרים להצגה', 'hea-lth-portal' ); ?></h3>
						<p><?php esc_html_e( 'רשומות יתווספו לאחר מחקר, ניסוח, בדיקת מקורות ואישור פרסום.', 'hea-lth-portal' ); ?></p>
					</div>
				<?php endif; ?>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--paper">
			<div class="hp-reading-layout hp-reading-layout--glossary">
				<div><?php hea_lth_portal_render_current_content(); ?></div>
				<?php hea_lth_portal_render_information_boundary(); ?>
			</div>
		</section>
		<?php
	endwhile;
endif;

wp_reset_postdata();
get_footer();
