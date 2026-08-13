<?php
/**
 * Template Name: הצטרפות ספקים ויבואנים
 * Template Post Type: page
 *
 * @package HeaLthPortal
 */
nocache_headers();
get_header();
$plans = array(
	array( 'name' => 'Verified', 'title' => 'נוכחות עסקית מאומתת', 'copy' => 'כרטיס חברה, פרטי קשר, תחומי פעילות וחיבור לאינדקס הספקים.' ),
	array( 'name' => 'אולם תצוגה', 'title' => 'מיני־אתר וקטלוג מקצועי', 'copy' => 'אולם תצוגה מלא עם עמודי ציוד, קטגוריות, מותגים וחיבור למידע רלוונטי באתר.' ),
	array( 'name' => 'Growth', 'title' => 'שותפות צמיחה ועסקאות', 'copy' => 'קדימות לטיפול בפניות מתאימות, קמפיינים, מדידה ומודל מסחרי מוסכם.' ),
);
?>
<section class="hp-supplier-join-hero"><div class="hp-shell"><p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'Hea-lth for Suppliers', 'hea-lth-portal' ); ?></p><h1><?php esc_html_e( 'הפכו את הקטלוג שלכם לאולם תצוגה שמייצר הזדמנויות', 'hea-lth-portal' ); ?></h1><p><?php esc_html_e( 'נוכחות מקצועית אחת שמחברת חברה, מוצרים, טכנולוגיות, תכניות הקמת מרפאה ופניות רכש.', 'hea-lth-portal' ); ?></p></div></section>
<section class="hp-template-section hp-template-section--paper"><div class="hp-shell"><div class="hp-supplier-plan-grid"><?php foreach ( $plans as $plan ) : ?><article><span><?php echo esc_html( $plan['name'] ); ?></span><h2><?php echo esc_html( $plan['title'] ); ?></h2><p><?php echo esc_html( $plan['copy'] ); ?></p></article><?php endforeach; ?></div></div></section>
<section class="hp-template-section hp-template-section--soft"><div class="hp-shell hp-supplier-join-layout"><div><p class="hp-eyebrow"><?php esc_html_e( 'הצטרפות', 'hea-lth-portal' ); ?></p><h2><?php esc_html_e( 'ספרו לנו על החברה והקטלוג', 'hea-lth-portal' ); ?></h2><p><?php esc_html_e( 'נמפה את המותגים, משפחות המוצרים, אזורי השירות והמסלול העסקי המתאים.', 'hea-lth-portal' ); ?></p></div><?php get_template_part( 'template-parts/b2b-intake-form', null, array( 'type' => 'supplier_join', 'context' => 'supplier-onboarding', 'return_url' => get_permalink() . '#quote' ) ); ?></div></section>
<?php get_footer(); ?>
