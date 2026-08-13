<?php
/** Premium supplier mini-site. @package HeaLthPortal */
$supplier_id = get_queried_object_id();
if ( 'verified' !== get_post_meta( $supplier_id, 'hp_public_state', true ) ) {
	status_header( 404 );
	nocache_headers();
}
get_header();
$website     = get_post_meta( $supplier_id, 'hp_website_url', true );
$email       = get_post_meta( $supplier_id, 'hp_contact_email', true );
$phone       = get_post_meta( $supplier_id, 'hp_contact_phone', true );
$address     = get_post_meta( $supplier_id, 'hp_address', true );
$brands      = (array) get_post_meta( $supplier_id, 'hp_brands', true );
$capabilities= (array) get_post_meta( $supplier_id, 'hp_capabilities', true );
$verified    = get_post_meta( $supplier_id, 'hp_last_verified', true );
$equipment   = new WP_Query( array( 'post_type' => 'hp_equipment', 'post_status' => 'publish', 'posts_per_page' => 48, 'meta_key' => 'hp_supplier_id', 'meta_value' => $supplier_id, 'orderby' => 'title', 'order' => 'ASC' ) );
?>
<section class="hp-supplier-hero">
	<div class="hp-shell hp-supplier-hero__grid">
		<div>
			<a class="hp-supplier-back" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'suppliers' ) ); ?>">← <?php esc_html_e( 'כל הספקים', 'hea-lth-portal' ); ?></a>
			<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'אולם תצוגה מקצועי', 'hea-lth-portal' ); ?></p>
			<h1><?php the_title(); ?></h1>
			<p class="hp-supplier-hero__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<div class="hp-supplier-actions">
				<?php if ( $website ) : ?><a class="hp-button" href="<?php echo esc_url( $website ); ?>" rel="noopener" target="_blank"><?php esc_html_e( 'לאתר החברה', 'hea-lth-portal' ); ?></a><?php endif; ?>
				<?php if ( $email ) : ?><a class="hp-button hp-button--ghost" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php esc_html_e( 'פנייה מסחרית', 'hea-lth-portal' ); ?></a><?php endif; ?>
			</div>
		</div>
		<div class="hp-supplier-monogram" aria-hidden="true"><?php echo esc_html( substr( get_the_title(), 0, 1 ) ); ?></div>
	</div>
</section>
<section class="hp-template-section hp-template-section--paper">
	<div class="hp-shell hp-supplier-layout">
		<main>
			<div class="hp-prose hp-supplier-intro"><?php echo apply_filters( 'the_content', get_the_content() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<section class="hp-supplier-catalog">
				<div class="hp-section-heading"><div><p class="hp-eyebrow"><?php esc_html_e( 'קטלוג מקצועי', 'hea-lth-portal' ); ?></p><h2><?php esc_html_e( 'מערכות וטכנולוגיות', 'hea-lth-portal' ); ?></h2></div><strong><?php echo esc_html( (string) $equipment->post_count ); ?> <?php esc_html_e( 'מערכות', 'hea-lth-portal' ); ?></strong></div>
				<div class="hp-equipment-grid">
					<?php while ( $equipment->have_posts() ) : $equipment->the_post(); ?>
						<article class="hp-equipment-card">
							<span><?php echo esc_html( get_post_meta( get_the_ID(), 'hp_product_family', true ) ); ?></span>
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p><?php echo esc_html( get_post_meta( get_the_ID(), 'hp_technology', true ) ); ?></p>
							<a class="hp-text-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'פרטי המערכת', 'hea-lth-portal' ); ?> ←</a>
						</article>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			</section>
		</main>
		<aside class="hp-supplier-facts">
			<h2><?php esc_html_e( 'פרטי החברה', 'hea-lth-portal' ); ?></h2>
			<?php if ( $phone ) : ?><div><span><?php esc_html_e( 'טלפון', 'hea-lth-portal' ); ?></span><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></div><?php endif; ?>
			<?php if ( $address ) : ?><div><span><?php esc_html_e( 'כתובת', 'hea-lth-portal' ); ?></span><strong><?php echo esc_html( $address ); ?></strong></div><?php endif; ?>
			<?php if ( $brands ) : ?><div><span><?php esc_html_e( 'מותגים', 'hea-lth-portal' ); ?></span><strong><?php echo esc_html( implode( ' · ', $brands ) ); ?></strong></div><?php endif; ?>
			<?php if ( $capabilities ) : ?><div><span><?php esc_html_e( 'מעטפת למרפאה', 'hea-lth-portal' ); ?></span><ul><?php foreach ( $capabilities as $capability ) : ?><li><?php echo esc_html( $capability ); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
			<?php if ( $verified ) : ?><small><?php echo esc_html( sprintf( 'פרטים עודכנו: %s', $verified ) ); ?></small><?php endif; ?>
		</aside>
	</div>
</section>
<?php get_footer(); ?>
