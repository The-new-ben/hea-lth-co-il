<?php
/**
 * Search result template.
 *
 * @package HeaLthPortal
 */

get_header();
$query = get_search_query();
?>
<section class="hp-page-hero hp-page-hero--compact">
	<div class="hp-shell">
		<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'חיפוש באתר', 'hea-lth-portal' ); ?></p>
		<h1><?php printf( esc_html__( 'תוצאות עבור: %s', 'hea-lth-portal' ), esc_html( $query ) ); ?></h1>
	</div>
</section>

<section class="hp-template-section hp-template-section--paper">
	<div class="hp-shell">
		<form class="hp-search-page-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search">
			<label class="screen-reader-text" for="portal-results-search"><?php esc_html_e( 'חיפוש באתר', 'hea-lth-portal' ); ?></label>
			<input id="portal-results-search" name="s" type="search" value="<?php echo esc_attr( $query ); ?>" placeholder="<?php esc_attr_e( 'חפשו טיפול, בדיקה, תחום או מדריך', 'hea-lth-portal' ); ?>">
			<button class="hp-button" type="submit"><?php esc_html_e( 'חיפוש', 'hea-lth-portal' ); ?></button>
		</form>

		<?php if ( have_posts() ) : ?>
			<div class="hp-record-grid hp-record-grid--search">
				<?php while ( have_posts() ) : the_post(); ?>
					<article <?php post_class( 'hp-record-card' ); ?>>
						<a class="hp-record-card__link" href="<?php the_permalink(); ?>">
							<span><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?></span>
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
				<h2><?php esc_html_e( 'לא נמצאו תוצאות מתאימות', 'hea-lth-portal' ); ?></h2>
				<p><?php esc_html_e( 'נסו ניסוח אחר, התחילו במרכז טיפולים או עברו לאינדקס המקצוענים.', 'hea-lth-portal' ); ?></p>
				<a class="hp-button" href="<?php echo esc_url( hea_lth_portal_route( 'doctor_clinic_index' ) ); ?>"><?php esc_html_e( 'חיפוש אנשי מקצוע', 'hea-lth-portal' ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php get_footer(); ?>
