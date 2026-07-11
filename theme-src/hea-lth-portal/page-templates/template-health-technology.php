<?php
/**
 * Template Name: טכנולוגיות בריאות וציוד
 * Template Post Type: page
 *
 * @package HeaLthPortal
 */

get_header();

$technology_paths = array(
	array( 'number' => '01', 'title' => __( 'דימות רפואי', 'hea-lth-portal' ), 'copy' => __( 'הבנת סוגי שירות, הכנה ושאלות להמשך בירור.', 'hea-lth-portal' ), 'route' => 'diagnostics_imaging' ),
	array( 'number' => '02', 'title' => __( 'פתרונות דיגיטליים', 'hea-lth-portal' ), 'copy' => __( 'כלים, שירותים ופרטי שימוש שמוצגים עם הקשר ברור.', 'hea-lth-portal' ), 'route' => 'health_technology' ),
	array( 'number' => '03', 'title' => __( 'ציוד ושירותים', 'hea-lth-portal' ), 'copy' => __( 'קטלוגים יופיעו רק אחרי סיווג, מקור ומידע ציבורי מלא.', 'hea-lth-portal' ), 'route' => 'health_technology_equipment' ),
	array( 'number' => '04', 'title' => __( 'מניעה ואורח חיים', 'hea-lth-portal' ), 'copy' => __( 'כלים להיכרות עם בדיקות, שינה, תנועה ותזונה.', 'hea-lth-portal' ), 'route' => 'wellness' ),
);

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		$title = hea_lth_portal_current_title( __( 'טכנולוגיות בריאות וציוד', 'hea-lth-portal' ) );
		?>
		<section class="hp-page-hero hp-page-hero--technology">
			<div class="hp-shell hp-template-hero-grid">
				<div>
					<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'טכנולוגיות בריאות', 'hea-lth-portal' ); ?></p>
					<h1><?php echo esc_html( $title ); ?></h1>
					<p><?php esc_html_e( 'מסלול ללמידה על טכנולוגיות, מכשור ושירותים רפואיים, לפני השוואה או פנייה לגורם מקצועי.', 'hea-lth-portal' ); ?></p>
				</div>
				<div class="hp-technology-hero-core" aria-hidden="true"><span>H</span><i></i><i></i><i></i></div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--paper">
			<div class="hp-shell">
				<div class="hp-section-heading">
					<p class="hp-eyebrow"><?php esc_html_e( 'תחומי חקר', 'hea-lth-portal' ); ?></p>
					<h2><?php esc_html_e( 'התחילו מהשירות או הטכנולוגיה שמעניינים אתכם', 'hea-lth-portal' ); ?></h2>
				</div>
				<div class="hp-technology-path-grid">
					<?php foreach ( $technology_paths as $path ) : ?>
						<a href="<?php echo esc_url( hea_lth_portal_foundation_route( $path['route'] ) ); ?>">
							<span><?php echo esc_html( $path['number'] ); ?></span>
							<h3><?php echo esc_html( $path['title'] ); ?></h3>
							<p><?php echo esc_html( $path['copy'] ); ?></p>
							<b aria-hidden="true">←</b>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--soft">
			<div class="hp-shell hp-catalog-gate">
				<div>
					<p class="hp-eyebrow"><?php esc_html_e( 'קטלוג עתידי', 'hea-lth-portal' ); ?></p>
					<h2><?php esc_html_e( 'מוצרים, ציוד ומוכרים יוצגו רק לאחר שמידע ציבורי מלא זמין', 'hea-lth-portal' ); ?></h2>
					<p><?php esc_html_e( 'הקטלוג אינו מציג כרגע מחירים, מלאי או טענות מוצר. לפני תצוגה נדרשים סיווג, מקור, פרטי שירות ומדיניות מתאימה.', 'hea-lth-portal' ); ?></p>
				</div>
				<div class="hp-catalog-gate__tokens" aria-hidden="true"><span>01</span><span>02</span><span>03</span><span>04</span></div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--paper">
			<div class="hp-reading-layout hp-reading-layout--technology">
				<div><?php hea_lth_portal_render_current_content(); ?></div>
				<?php hea_lth_portal_render_information_boundary(); ?>
			</div>
		</section>
		<?php
	endwhile;
endif;

get_footer();
