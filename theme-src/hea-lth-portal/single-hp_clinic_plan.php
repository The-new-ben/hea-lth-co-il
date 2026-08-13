<?php
/** Complete clinic-build procurement plan. @package HeaLthPortal */
$plan_id = get_queried_object_id();
if ( 'verified' !== get_post_meta( $plan_id, 'hp_public_state', true ) ) {
	status_header( 404 );
	nocache_headers();
}
nocache_headers();
get_header();
$order       = (array) get_post_meta( $plan_id, 'hp_procurement_order', true );
$summary     = get_post_meta( $plan_id, 'hp_plan_summary', true );
$context     = isset( $_GET['equipment'] ) ? sanitize_key( wp_unslash( $_GET['equipment'] ) ) : get_post_field( 'post_name', $plan_id ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$supplier    = isset( $_GET['supplier'] ) ? sanitize_key( wp_unslash( $_GET['supplier'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$context     = $supplier ? 'supplier-' . $supplier : $context;
?>
<section class="hp-clinic-build-hero">
	<div class="hp-shell hp-clinic-build-hero__grid">
		<div>
			<a class="hp-supplier-back" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'clinic_build' ) ); ?>">← <?php esc_html_e( 'כל תכניות ההקמה', 'hea-lth-portal' ); ?></a>
			<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'תכנון מרפאה ורכש', 'hea-lth-portal' ); ?></p>
			<h1><?php the_title(); ?></h1>
			<p><?php echo esc_html( get_the_excerpt() ); ?></p>
			<a class="hp-button" href="#quote"><?php esc_html_e( 'בניית סל רכש למרפאה', 'hea-lth-portal' ); ?></a>
		</div>
		<div class="hp-clinic-build-proof"><span>360°</span><strong><?php echo esc_html( $summary ); ?></strong></div>
	</div>
</section>

<section class="hp-template-section hp-template-section--paper">
	<div class="hp-shell">
		<div class="hp-prose hp-clinic-build-intro"><?php echo apply_filters( 'the_content', get_the_content() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<div class="hp-procurement-sections">
			<?php foreach ( $order as $index => $term_slug ) : ?>
				<?php
				$term = get_term_by( 'slug', $term_slug, 'hp_procurement' );
				if ( ! $term instanceof WP_Term ) {
					continue;
				}
				$products = new WP_Query(
					array(
						'post_type'      => 'hp_equipment',
						'post_status'    => 'publish',
						'posts_per_page' => 8,
						'orderby'        => 'title',
						'order'          => 'ASC',
						'tax_query'      => array( array( 'taxonomy' => 'hp_procurement', 'field' => 'term_id', 'terms' => array( $term->term_id ) ) ),
					)
				);
				?>
				<section class="hp-procurement-section">
					<div class="hp-procurement-section__heading"><span><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><div><h2><?php echo esc_html( $term->name ); ?></h2><p><?php echo esc_html( $term->description ); ?></p></div></div>
					<?php if ( $products->have_posts() ) : ?>
						<div class="hp-procurement-products">
							<?php while ( $products->have_posts() ) : $products->the_post(); $product_supplier = (int) get_post_meta( get_the_ID(), 'hp_supplier_id', true ); ?>
								<article><span><?php echo esc_html( get_post_meta( get_the_ID(), 'hp_technology', true ) ); ?></span><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><?php if ( $product_supplier ) : ?><small><?php echo esc_html( get_the_title( $product_supplier ) ); ?></small><?php endif; ?></article>
							<?php endwhile; ?>
						</div>
					<?php else : ?>
						<div class="hp-procurement-scope"><strong><?php esc_html_e( 'חבילת דרישות להצעות מחיר', 'hea-lth-portal' ); ?></strong><p><?php esc_html_e( 'הקטגוריה תיכלל באפיון הספקים ובסל הרכש של הפרויקט.', 'hea-lth-portal' ); ?></p></div>
					<?php endif; wp_reset_postdata(); ?>
				</section>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="hp-template-section hp-template-section--soft">
	<div class="hp-shell hp-clinic-quote-layout">
		<div><p class="hp-eyebrow"><?php esc_html_e( 'בקשת רכש אחת', 'hea-lth-portal' ); ?></p><h2><?php esc_html_e( 'בנו סל מלא וקבלו התאמה מספקים רלוונטיים', 'hea-lth-portal' ); ?></h2><p><?php esc_html_e( 'סמנו את תחומי הרכש, את שלב הפרויקט ואת פרטי המרפאה. צוות Hea-lth ירכז את הצורך ויתאם את המשך השיחה העסקית.', 'hea-lth-portal' ); ?></p></div>
		<?php get_template_part( 'template-parts/b2b-intake-form', null, array( 'type' => 'clinic_quote', 'context' => $context, 'return_url' => get_permalink( $plan_id ) . '#quote' ) ); ?>
	</div>
</section>
<?php get_footer(); ?>
