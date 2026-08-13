<?php
/** Canonical equipment detail. @package HeaLthPortal */
get_header();
$equipment_id = get_queried_object_id();
$supplier_id  = (int) get_post_meta( $equipment_id, 'hp_supplier_id', true );
$technology   = get_post_meta( $equipment_id, 'hp_technology', true );
$family       = get_post_meta( $equipment_id, 'hp_product_family', true );
$source       = get_post_meta( $equipment_id, 'hp_source_url', true );
?>
<section class="hp-equipment-hero">
	<div class="hp-shell">
		<?php if ( $supplier_id ) : ?><a class="hp-supplier-back" href="<?php echo esc_url( get_permalink( $supplier_id ) ); ?>">← <?php echo esc_html( get_the_title( $supplier_id ) ); ?></a><?php endif; ?>
		<p class="hp-eyebrow hp-eyebrow--light"><?php echo esc_html( $family ); ?></p>
		<h1><?php the_title(); ?></h1>
		<p><?php echo esc_html( $technology ); ?></p>
	</div>
</section>
<section class="hp-template-section hp-template-section--paper">
	<div class="hp-shell hp-equipment-detail">
		<article class="hp-prose"><?php echo apply_filters( 'the_content', get_the_content() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></article>
		<aside>
			<dl><div><dt><?php esc_html_e( 'טכנולוגיה', 'hea-lth-portal' ); ?></dt><dd><?php echo esc_html( $technology ); ?></dd></div><div><dt><?php esc_html_e( 'משפחת שימוש', 'hea-lth-portal' ); ?></dt><dd><?php echo esc_html( $family ); ?></dd></div></dl>
			<?php if ( $supplier_id ) : ?><a class="hp-button" href="<?php echo esc_url( get_permalink( $supplier_id ) ); ?>"><?php esc_html_e( 'לאולם התצוגה', 'hea-lth-portal' ); ?></a><?php endif; ?>
			<a class="hp-button hp-button--secondary" href="<?php echo esc_url( add_query_arg( 'equipment', get_post_field( 'post_name', $equipment_id ), hea_lth_portal_clinic_plan_url() ) ); ?>#quote"><?php esc_html_e( 'בקשת הצעת רכש', 'hea-lth-portal' ); ?></a>
			<?php if ( $source ) : ?><a class="hp-text-link" href="<?php echo esc_url( $source ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'מקור: אתר הספק', 'hea-lth-portal' ); ?></a><?php endif; ?>
		</aside>
	</div>
</section>
<?php get_footer(); ?>
