<?php
/**
 * Template Name: הצטרפות ספקים ויבואנים
 * Template Post Type: page
 *
 * @package HeaLthPortal
 */
nocache_headers();
get_header();
$selected_plan = isset( $_GET['plan'] ) ? sanitize_key( wp_unslash( $_GET['plan'] ) ) : 'showroom'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display-only plan selector.
if ( ! in_array( $selected_plan, array( 'verified', 'showroom', 'growth' ), true ) ) {
	$selected_plan = 'showroom';
}
$plans = array(
	array( 'key' => 'verified', 'name' => 'Verified', 'title' => 'נוכחות עסקית מאומתת', 'price' => '₪990 לחודש', 'copy' => 'כרטיס חברה, פרטי קשר, תחומי פעילות וחיבור לאינדקס הספקים.' ),
	array( 'key' => 'showroom', 'name' => 'Showroom', 'title' => 'מיני־אתר וקטלוג מקצועי', 'price' => '₪7,500 הקמה + ₪2,490 לחודש', 'copy' => 'אולם תצוגה מלא עם עמודי ציוד, קטגוריות, מותגים וחיבור למידע רלוונטי באתר.' ),
	array( 'key' => 'growth', 'name' => 'Growth', 'title' => 'שותפות צמיחה ועסקאות', 'price' => '₪3,900 לחודש + עמלת הצלחה מוסכמת', 'copy' => 'אולם תצוגה מורחב, גישה להזדמנויות מתאימות, מדידה וליווי עסקאות.' ),
);
?>
<section class="hp-supplier-join-hero"><div class="hp-shell"><p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'Hea-lth for Suppliers', 'hea-lth-portal' ); ?></p><h1><?php esc_html_e( 'הפכו את הקטלוג שלכם לאולם תצוגה שמייצר הזדמנויות', 'hea-lth-portal' ); ?></h1><p><?php esc_html_e( 'נוכחות מקצועית אחת שמחברת חברה, מוצרים, טכנולוגיות, תכניות הקמת מרפאה ופניות רכש.', 'hea-lth-portal' ); ?></p></div></section>
<section class="hp-template-section hp-template-section--paper"><div class="hp-shell"><div class="hp-supplier-plan-grid"><?php foreach ( $plans as $plan ) : ?><article><span><?php echo esc_html( $plan['name'] ); ?></span><h2><?php echo esc_html( $plan['title'] ); ?></h2><strong><?php echo esc_html( $plan['price'] ); ?></strong><p><?php echo esc_html( $plan['copy'] ); ?></p><a class="hp-inline-link" href="<?php echo esc_url( add_query_arg( 'plan', $plan['key'], get_permalink() ) . '#quote' ); ?>"><?php esc_html_e( 'בחירת המסלול', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a></article><?php endforeach; ?></div></div></section>
<section class="hp-template-section hp-template-section--soft"><div class="hp-shell hp-supplier-join-layout"><div><p class="hp-eyebrow"><?php esc_html_e( 'הצטרפות', 'hea-lth-portal' ); ?></p><h2><?php esc_html_e( 'ספרו לנו על החברה והקטלוג', 'hea-lth-portal' ); ?></h2><p><?php esc_html_e( 'נמפה את המותגים, משפחות המוצרים, אזורי השירות והמסלול העסקי המתאים.', 'hea-lth-portal' ); ?></p><a class="hp-inline-link" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'supplier_portal' ) ); ?>"><?php esc_html_e( 'כבר הצטרפתם? כניסה לאזור הספקים', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a></div><?php get_template_part( 'template-parts/b2b-intake-form', null, array( 'type' => 'supplier_join', 'context' => 'supplier-onboarding', 'selected_plan' => $selected_plan, 'return_url' => get_permalink() . '#quote' ) ); ?></div></section>
<?php get_footer(); ?>
