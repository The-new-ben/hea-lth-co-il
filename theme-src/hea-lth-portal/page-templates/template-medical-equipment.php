<?php
/**
 * Template Name: Medical equipment marketplace
 *
 * Indexed equipment discovery, comparison and procurement intake.
 *
 * @package HeaLthPortal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$equipment = get_posts(
	array(
		'post_type'      => 'hp_equipment',
		'post_status'    => 'publish',
		'posts_per_page' => 120,
		'orderby'        => 'title',
		'order'          => 'ASC',
		'meta_key'       => 'hp_editorial_state', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Bounded reviewed marketplace catalog.
		'meta_value'     => 'approved', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Public records must pass the editorial gate.
		'no_found_rows'  => true,
	)
);
$families  = array();
$suppliers = array();

foreach ( $equipment as $machine ) {
	$family      = sanitize_text_field( (string) get_post_meta( (int) $machine->ID, 'hp_product_family', true ) );
	$supplier_id = absint( get_post_meta( (int) $machine->ID, 'hp_supplier_id', true ) );
	if ( '' !== $family ) {
		$families[ sanitize_title( $family ) ] = $family;
	}
	if ( $supplier_id > 0 && 'publish' === get_post_status( $supplier_id ) ) {
		$suppliers[ $supplier_id ] = get_the_title( $supplier_id );
	}
}

asort( $families, SORT_NATURAL | SORT_FLAG_CASE );
asort( $suppliers, SORT_NATURAL | SORT_FLAG_CASE );
?>
<section class="hp-equipment-market-hero">
	<div class="hp-shell hp-equipment-market-hero__grid">
		<div>
			<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'ציוד וטכנולוגיה למרפאות', 'hea-lth-portal' ); ?></p>
			<h1><?php esc_html_e( 'מגלים, משווים ומתכננים רכישת ציוד רפואי ואסתטי', 'hea-lth-portal' ); ?></h1>
			<p><?php esc_html_e( 'קטלוג מקצועי המחבר בין שימוש במרפאה, טכנולוגיה, מערכת וספק. בחרו מערכות להשוואה ושלחו בקשת רכש אחת מסודרת.', 'hea-lth-portal' ); ?></p>
		</div>
		<div class="hp-equipment-market-hero__stat" aria-label="היקף הקטלוג">
			<strong><?php echo esc_html( number_format_i18n( count( $equipment ) ) ); ?></strong>
			<span><?php esc_html_e( 'מערכות בקטלוג', 'hea-lth-portal' ); ?></span>
			<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'suppliers' ) ); ?>"><?php esc_html_e( 'לכל אולמות התצוגה', 'hea-lth-portal' ); ?> <span aria-hidden="true">←</span></a>
		</div>
	</div>
</section>

<section class="hp-template-section hp-template-section--paper hp-equipment-market" data-equipment-marketplace>
	<div class="hp-shell">
		<div class="hp-equipment-tools" aria-label="סינון קטלוג הציוד">
			<label><span><?php esc_html_e( 'חיפוש מערכת או טכנולוגיה', 'hea-lth-portal' ); ?></span><input type="search" data-equipment-search placeholder="לדוגמה HIFU, RF או לייזר"></label>
			<label><span><?php esc_html_e( 'תחום שימוש', 'hea-lth-portal' ); ?></span><select data-equipment-family><option value=""><?php esc_html_e( 'כל התחומים', 'hea-lth-portal' ); ?></option><?php foreach ( $families as $slug => $label ) : ?><option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></label>
			<label><span><?php esc_html_e( 'ספק', 'hea-lth-portal' ); ?></span><select data-equipment-supplier><option value=""><?php esc_html_e( 'כל הספקים', 'hea-lth-portal' ); ?></option><?php foreach ( $suppliers as $supplier_id => $label ) : ?><option value="<?php echo (int) $supplier_id; ?>"><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></label>
			<p class="hp-equipment-results" aria-live="polite"><strong data-equipment-count><?php echo esc_html( number_format_i18n( count( $equipment ) ) ); ?></strong> <?php esc_html_e( 'מערכות מוצגות', 'hea-lth-portal' ); ?><span data-comparison-status><?php esc_html_e( 'אפשר לבחור עד ארבע מערכות להשוואה', 'hea-lth-portal' ); ?></span></p>
		</div>

		<div class="hp-equipment-market-grid">
			<?php foreach ( $equipment as $machine ) : ?>
				<?php
				$machine_id    = (int) $machine->ID;
				$technology    = sanitize_text_field( (string) get_post_meta( $machine_id, 'hp_technology', true ) );
				$family        = sanitize_text_field( (string) get_post_meta( $machine_id, 'hp_product_family', true ) );
				$supplier_id   = absint( get_post_meta( $machine_id, 'hp_supplier_id', true ) );
				$supplier_name = $supplier_id ? get_the_title( $supplier_id ) : '';
				?>
				<article class="hp-equipment-market-card" data-equipment-card data-family="<?php echo esc_attr( sanitize_title( $family ) ); ?>" data-supplier="<?php echo (int) $supplier_id; ?>">
					<div class="hp-equipment-market-card__mark" aria-hidden="true"><?php echo esc_html( strtoupper( substr( $machine->post_title, 0, 1 ) ) ); ?></div>
					<div class="hp-equipment-market-card__body">
						<p class="hp-eyebrow"><?php echo esc_html( $family ); ?></p>
						<h2><a href="<?php echo esc_url( get_permalink( $machine_id ) ); ?>"><?php echo esc_html( $machine->post_title ); ?></a></h2>
						<dl><div><dt><?php esc_html_e( 'טכנולוגיה', 'hea-lth-portal' ); ?></dt><dd><?php echo esc_html( $technology ); ?></dd></div><div><dt><?php esc_html_e( 'ספק', 'hea-lth-portal' ); ?></dt><dd><?php echo esc_html( $supplier_name ); ?></dd></div></dl>
					</div>
					<div class="hp-equipment-market-card__actions">
						<label class="hp-compare-choice"><input type="checkbox" value="<?php echo esc_attr( $machine->post_name ); ?>" data-compare-equipment data-title="<?php echo esc_attr( $machine->post_title ); ?>" data-technology="<?php echo esc_attr( $technology ); ?>" data-family-label="<?php echo esc_attr( $family ); ?>" data-supplier-label="<?php echo esc_attr( $supplier_name ); ?>"><span><?php esc_html_e( 'הוספה להשוואה', 'hea-lth-portal' ); ?></span></label>
						<a class="hp-text-link" href="<?php echo esc_url( get_permalink( $machine_id ) ); ?>"><?php esc_html_e( 'פרטי המערכת', 'hea-lth-portal' ); ?> <span aria-hidden="true">←</span></a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
		<p class="hp-equipment-empty" data-equipment-empty hidden><?php esc_html_e( 'לא נמצאו מערכות בסינון שבחרתם. נסו תחום או מילת חיפוש אחרת.', 'hea-lth-portal' ); ?></p>

		<section class="hp-comparison-panel" data-comparison-panel hidden aria-labelledby="hp-comparison-title">
			<div class="hp-comparison-panel__heading"><div><p class="hp-eyebrow"><?php esc_html_e( 'השוואה מקצועית', 'hea-lth-portal' ); ?></p><h2 id="hp-comparison-title"><?php esc_html_e( 'המערכות שבחרתם', 'hea-lth-portal' ); ?></h2></div><button type="button" class="hp-text-button" data-comparison-clear><?php esc_html_e( 'ניקוי הבחירה', 'hea-lth-portal' ); ?></button></div>
			<div class="hp-comparison-table-wrap"><table class="hp-comparison-table"><thead data-comparison-head></thead><tbody data-comparison-body></tbody></table></div>
			<a class="hp-button" href="#equipment-quote" data-comparison-quote><?php esc_html_e( 'קבלת הצעות למערכות שנבחרו', 'hea-lth-portal' ); ?></a>
		</section>
	</div>
</section>

<section class="hp-template-section hp-equipment-quote" id="equipment-quote">
	<div class="hp-shell hp-equipment-quote__layout">
		<div><p class="hp-eyebrow"><?php esc_html_e( 'בקשת רכש אחת', 'hea-lth-portal' ); ?></p><h2><?php esc_html_e( 'ספרו לנו מה המרפאה צריכה', 'hea-lth-portal' ); ?></h2><p><?php esc_html_e( 'הבחירות שלכם יצורפו לבקשה. אפשר לכלול גם ציוד משלים, תשתיות, תוכנה, הדרכה ומימון.', 'hea-lth-portal' ); ?></p></div>
		<?php get_template_part( 'template-parts/b2b-intake-form', null, array( 'type' => 'clinic_quote', 'context' => 'equipment-marketplace', 'selected_equipment' => array(), 'return_url' => get_permalink() . '#equipment-quote' ) ); ?>
	</div>
</section>
<?php get_footer(); ?>
