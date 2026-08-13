<?php
/**
 * Template Name: טכנולוגיות בריאות וציוד
 * Template Post Type: page
 *
 * @package HeaLthPortal
 */
get_header();
$paths = array(
	array( 'number' => '01', 'title' => 'ציוד ומערכות למרפאות', 'copy' => 'מערכות מקצועיות לפי טכנולוגיה, שימוש וספק.', 'url' => hea_lth_portal_foundation_route( 'suppliers' ) ),
	array( 'number' => '02', 'title' => 'הקמת מרפאה', 'copy' => 'מפות רכש מלאות לפי סוג מרפאה ושלב עסקי.', 'url' => hea_lth_portal_foundation_route( 'clinic_build' ) ),
	array( 'number' => '03', 'title' => 'בדיקות ודימות', 'copy' => 'היכרות עם טכנולוגיות אבחון ושירותים מקצועיים.', 'url' => hea_lth_portal_foundation_route( 'diagnostics' ) ),
	array( 'number' => '04', 'title' => 'AI ורובוטיקה רפואית', 'copy' => 'מערכות ניתוח, אוטומציה, מדידה ותמיכה בתהליכי טיפול.', 'url' => hea_lth_portal_foundation_route( 'health_technology_equipment' ) ),
);
?>
<section class="hp-page-hero hp-page-hero--technology"><div class="hp-shell hp-template-hero-grid"><div><p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'טכנולוגיות בריאות', 'hea-lth-portal' ); ?></p><h1><?php esc_html_e( 'המקום שבו מדע, מרפאות וציוד מתחברים', 'hea-lth-portal' ); ?></h1><p><?php esc_html_e( 'גילוי טכנולוגיות, ספקים, מערכות ותכניות רכש — ממכשיר יחיד ועד הקמת מרפאה שלמה.', 'hea-lth-portal' ); ?></p></div><div class="hp-technology-hero-core" aria-hidden="true"><span>AI</span><i></i><i></i><i></i></div></div></section>
<section class="hp-template-section hp-template-section--paper"><div class="hp-shell"><div class="hp-section-heading"><p class="hp-eyebrow"><?php esc_html_e( 'מפת הטכנולוגיה', 'hea-lth-portal' ); ?></p><h2><?php esc_html_e( 'בחרו את נקודת הכניסה המתאימה', 'hea-lth-portal' ); ?></h2></div><div class="hp-technology-path-grid"><?php foreach ( $paths as $path ) : ?><a href="<?php echo esc_url( $path['url'] ); ?>"><span><?php echo esc_html( $path['number'] ); ?></span><h3><?php echo esc_html( $path['title'] ); ?></h3><p><?php echo esc_html( $path['copy'] ); ?></p><b aria-hidden="true">←</b></a><?php endforeach; ?></div></div></section>
<section class="hp-template-section hp-template-section--soft"><div class="hp-shell hp-technology-market"><div><p class="hp-eyebrow"><?php esc_html_e( 'שוק מקצועי מחובר', 'hea-lth-portal' ); ?></p><h2><?php esc_html_e( 'מכונה, ספק ותכנית עסקית באותה מערכת', 'hea-lth-portal' ); ?></h2><p><?php esc_html_e( 'כל מערכת מקבלת עמוד קנוני אחד. כל ספק מקבל אולם תצוגה. תכניות ההקמה מחברות ביניהם לסל רכש שלם.', 'hea-lth-portal' ); ?></p></div><div class="hp-supplier-actions"><a class="hp-button" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'suppliers' ) ); ?>"><?php esc_html_e( 'לאולמות התצוגה', 'hea-lth-portal' ); ?></a><a class="hp-text-link" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'supplier_join' ) ); ?>"><?php esc_html_e( 'הצטרפות ספקים', 'hea-lth-portal' ); ?> ←</a></div></div></section>
<?php get_footer(); ?>
