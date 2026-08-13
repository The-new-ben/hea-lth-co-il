<?php
/**
 * Template Name: טכנולוגיות בריאות וציוד
 * Template Post Type: page
 *
 * @package HeaLthPortal
 */
get_header();
$paths = array(
	array( 'number' => '01', 'title' => 'קטלוג ציוד רפואי', 'copy' => 'מערכות מקצועיות לפי טכנולוגיה, שימוש ותחום קליני.', 'url' => hea_lth_portal_foundation_route( 'medical_equipment' ) ),
	array( 'number' => '02', 'title' => 'ספקים ואולמות תצוגה', 'copy' => 'מותגים, מוצרים ויכולות שירות של ספקים מקצועיים.', 'url' => hea_lth_portal_foundation_route( 'suppliers' ) ),
	array( 'number' => '03', 'title' => 'הקמת מרפאה', 'copy' => 'מפות רכש מלאות לפי סוג מרפאה ושלב עסקי.', 'url' => hea_lth_portal_foundation_route( 'clinic_build' ) ),
	array( 'number' => '04', 'title' => 'בדיקות ודימות', 'copy' => 'היכרות עם טכנולוגיות אבחון ושירותים מקצועיים.', 'url' => hea_lth_portal_foundation_route( 'diagnostics' ) ),
	array( 'number' => '05', 'title' => 'בינה מלאכותית ורובוטיקה רפואית', 'copy' => 'מערכות חכמות, אוטומציה, שקיפות ושימוש אחראי ברפואה.', 'url' => hea_lth_portal_foundation_route( 'health_technology_ai_robotics' ) ),
	array( 'number' => '06', 'title' => 'סמנים ביולוגיים ומעקב', 'copy' => 'מה מודדים, מה משמעות המדידה וכיצד בוחנים אותה בהקשר הנכון.', 'url' => hea_lth_portal_foundation_route( 'health_technology_biomarkers' ) ),
);
?>
<section class="hp-page-hero hp-page-hero--technology"><div class="hp-shell hp-template-hero-grid"><div><p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'טכנולוגיות בריאות', 'hea-lth-portal' ); ?></p><h1><?php esc_html_e( 'המקום שבו מדע, מרפאות וציוד מתחברים', 'hea-lth-portal' ); ?></h1><p><?php esc_html_e( 'גילוי טכנולוגיות, ספקים, מערכות ותכניות רכש, ממכשיר יחיד ועד הקמת מרפאה שלמה.', 'hea-lth-portal' ); ?></p></div><div class="hp-technology-hero-core" aria-hidden="true"><span>AI</span><i></i><i></i><i></i></div></div></section>
<section class="hp-template-section hp-template-section--paper"><div class="hp-shell"><div class="hp-section-heading"><p class="hp-eyebrow"><?php esc_html_e( 'מפת הטכנולוגיה', 'hea-lth-portal' ); ?></p><h2><?php esc_html_e( 'בחרו את נקודת הכניסה המתאימה', 'hea-lth-portal' ); ?></h2></div><div class="hp-technology-path-grid hp-technology-path-grid--market"><?php foreach ( $paths as $path ) : ?><a href="<?php echo esc_url( $path['url'] ); ?>"><span><?php echo esc_html( $path['number'] ); ?></span><h3><?php echo esc_html( $path['title'] ); ?></h3><p><?php echo esc_html( $path['copy'] ); ?></p><b aria-hidden="true">←</b></a><?php endforeach; ?></div></div></section>
<section class="hp-template-section hp-template-section--soft"><div class="hp-shell hp-technology-market"><div><p class="hp-eyebrow"><?php esc_html_e( 'שוק מקצועי מחובר', 'hea-lth-portal' ); ?></p><h2><?php esc_html_e( 'מכונה, ספק ותכנית עסקית באותה מערכת', 'hea-lth-portal' ); ?></h2><p><?php esc_html_e( 'לכל מערכת מוצגים מידע מקצועי, שימושים וספקים רלוונטיים. אולמות התצוגה ותכניות ההקמה מחברים את הפרטים לסל רכש שלם.', 'hea-lth-portal' ); ?></p></div><div class="hp-supplier-actions"><a class="hp-button" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'suppliers' ) ); ?>"><?php esc_html_e( 'לאולמות התצוגה', 'hea-lth-portal' ); ?></a><a class="hp-text-link" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'supplier_join' ) ); ?>"><?php esc_html_e( 'הצטרפות ספקים', 'hea-lth-portal' ); ?> ←</a></div></div></section>
<?php get_footer(); ?>
