<?php
/**
 * Global header and portal navigation.
 *
 * @package HeaLthPortal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'hp-body' ); ?>>
<?php wp_body_open(); ?>
<noscript>
	<nav class="hp-noscript-navigation" aria-label="<?php esc_attr_e( 'תפריט ראשי ללא סקריפטים', 'hea-lth-portal' ); ?>">
		<div class="hp-shell">
			<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'treatments' ) ); ?>"><?php esc_html_e( 'טיפולים וניתוחים', 'hea-lth-portal' ); ?></a>
			<a href="<?php echo esc_url( hea_lth_portal_route( 'doctor_clinic_index' ) ); ?>"><?php esc_html_e( 'רופאים ומרפאות', 'hea-lth-portal' ); ?></a>
			<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'diagnostics' ) ); ?>"><?php esc_html_e( 'בדיקות ואבחון', 'hea-lth-portal' ); ?></a>
			<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'wellness' ) ); ?>"><?php esc_html_e( 'בריאות ואיכות חיים', 'hea-lth-portal' ); ?></a>
			<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'guides' ) ); ?>"><?php esc_html_e( 'מדריכים ומחקרים', 'hea-lth-portal' ); ?></a>
			<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'professionals' ) ); ?>"><?php esc_html_e( 'למקצוענים', 'hea-lth-portal' ); ?></a>
		</div>
	</nav>
</noscript>
<a class="hp-skip-link" href="#main-content"><?php esc_html_e( 'דילוג לתוכן הראשי', 'hea-lth-portal' ); ?></a>

<header class="hp-site-header" data-site-header>
	<div class="hp-header__utility">
		<div class="hp-shell hp-header__utility-inner">
			<p><?php esc_html_e( 'מידע רפואי אינו תחליף לייעוץ, אבחון או טיפול אישי.', 'hea-lth-portal' ); ?></p>
			<nav aria-label="<?php esc_attr_e( 'קישורי שירות', 'hea-lth-portal' ); ?>">
				<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'about' ) ); ?>"><?php esc_html_e( 'אודות', 'hea-lth-portal' ); ?></a>
				<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'accessibility' ) ); ?>"><?php esc_html_e( 'נגישות', 'hea-lth-portal' ); ?></a>
				<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'contact' ) ); ?>"><?php esc_html_e( 'יצירת קשר', 'hea-lth-portal' ); ?></a>
			</nav>
		</div>
	</div>

	<div class="hp-shell hp-header__main">
		<a class="hp-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'hea-lth, בחירה מודעת בבריאות פרטית, דף הבית', 'hea-lth-portal' ); ?>">
			<svg class="hp-brand__mark" viewBox="0 0 48 48" aria-hidden="true" focusable="false">
				<rect x="3" y="3" width="42" height="42" rx="14" fill="currentColor"/>
				<path d="M15.25 13.5v21M32.75 13.5v21M15.25 24h14" fill="none" stroke="#fffdf8" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/>
				<circle cx="33" cy="14" r="2.75" fill="#dfc17b"/>
			</svg>
			<span class="hp-brand__copy">
				<strong>hea-lth</strong>
				<span><?php esc_html_e( 'בחירה במידע ובשירותי בריאות', 'hea-lth-portal' ); ?></span>
			</span>
		</a>

		<div class="hp-header__desktop-tools">
			<button class="hp-icon-button" type="button" data-search-toggle aria-controls="portal-search" aria-expanded="false">
				<span class="hp-icon-button__glyph" aria-hidden="true">⌕</span>
				<span class="screen-reader-text"><?php esc_html_e( 'פתיחת חיפוש', 'hea-lth-portal' ); ?></span>
			</button>
			<a class="hp-text-link" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'account' ) ); ?>"><?php esc_html_e( 'האזור האישי', 'hea-lth-portal' ); ?></a>
			<a class="hp-button hp-button--header" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'find_care' ) ); ?>"><?php esc_html_e( 'מצאו את הדרך שלכם', 'hea-lth-portal' ); ?></a>
		</div>

		<button class="hp-mobile-toggle" type="button" data-mobile-toggle aria-controls="portal-primary-navigation" aria-expanded="false">
			<span></span><span></span><span></span>
			<span class="screen-reader-text"><?php esc_html_e( 'פתיחת תפריט', 'hea-lth-portal' ); ?></span>
		</button>
	</div>

	<nav class="hp-primary-nav" id="portal-primary-navigation" aria-label="<?php esc_attr_e( 'תפריט ראשי', 'hea-lth-portal' ); ?>" data-primary-navigation>
		<div class="hp-shell hp-primary-nav__inner">
			<div class="hp-primary-nav__list">
				<button class="hp-nav-trigger" type="button" data-mega-trigger aria-expanded="false" aria-controls="mega-treatments">
					<?php esc_html_e( 'טיפולים וניתוחים', 'hea-lth-portal' ); ?><span aria-hidden="true">⌄</span>
				</button>
				<a href="<?php echo esc_url( hea_lth_portal_route( 'doctor_clinic_index' ) ); ?>"><?php esc_html_e( 'רופאים ומרפאות', 'hea-lth-portal' ); ?></a>
				<button class="hp-nav-trigger" type="button" data-mega-trigger aria-expanded="false" aria-controls="mega-diagnostics">
					<?php esc_html_e( 'בדיקות ואבחון', 'hea-lth-portal' ); ?><span aria-hidden="true">⌄</span>
				</button>
				<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'wellness' ) ); ?>"><?php esc_html_e( 'בריאות ואיכות חיים', 'hea-lth-portal' ); ?></a>
				<button class="hp-nav-trigger" type="button" data-mega-trigger aria-expanded="false" aria-controls="mega-knowledge">
					<?php esc_html_e( 'מדריכים וכלים', 'hea-lth-portal' ); ?><span aria-hidden="true">⌄</span>
				</button>
				<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'professionals' ) ); ?>"><?php esc_html_e( 'למקצוענים', 'hea-lth-portal' ); ?></a>
			</div>
		</div>

		<div class="hp-mega-panel" id="mega-treatments" hidden data-mega-panel>
			<div class="hp-shell hp-mega-panel__grid">
				<div class="hp-mega-panel__intro">
					<p class="hp-eyebrow"><?php esc_html_e( 'טיפולים וניתוחים', 'hea-lth-portal' ); ?></p>
					<h2><?php esc_html_e( 'השוואה שמתחילה בשאלות הנכונות', 'hea-lth-portal' ); ?></h2>
					<p><?php esc_html_e( 'מסלולי מידע, שאלות לפגישה וחיפוש לפי תחום, אזור וסוג שירות.', 'hea-lth-portal' ); ?></p>
					<a class="hp-inline-link" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'treatments' ) ); ?>"><?php esc_html_e( 'לכל הטיפולים והניתוחים', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a>
				</div>
				<div class="hp-mega-panel__links">
					<a href="<?php echo esc_url( hea_lth_portal_route( 'aesthetic_medicine' ) ); ?>"><span><?php esc_html_e( 'רפואה אסתטית', 'hea-lth-portal' ); ?></span><small><?php esc_html_e( 'עור, הזרקות, טיפולים לא ניתוחיים', 'hea-lth-portal' ); ?></small></a>
					<a href="<?php echo esc_url( hea_lth_portal_route( 'plastic_surgery_consultation' ) ); ?>"><span><?php esc_html_e( 'כירורגיה פלסטית', 'hea-lth-portal' ); ?></span><small><?php esc_html_e( 'פנים, גוף, שחזור והתייעצות', 'hea-lth-portal' ); ?></small></a>
					<a href="<?php echo esc_url( hea_lth_portal_route( 'hair_transplant_consultation' ) ); ?>"><span><?php esc_html_e( 'השתלת שיער', 'hea-lth-portal' ); ?></span><small><?php esc_html_e( 'בירור השתלה, מרפאות ושאלות לפגישה', 'hea-lth-portal' ); ?></small></a>
					<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'private_medicine' ) ); ?>"><span><?php esc_html_e( 'רפואה פרטית', 'hea-lth-portal' ); ?></span><small><?php esc_html_e( 'חוות דעת, מומחים ותיאום שירות', 'hea-lth-portal' ); ?></small></a>
				</div>
			</div>
		</div>

		<div class="hp-mega-panel" id="mega-diagnostics" hidden data-mega-panel>
			<div class="hp-shell hp-mega-panel__grid">
				<div class="hp-mega-panel__intro">
					<p class="hp-eyebrow"><?php esc_html_e( 'בדיקות ואבחון', 'hea-lth-portal' ); ?></p>
					<h2><?php esc_html_e( 'להבין את האפשרויות לפני שקובעים', 'hea-lth-portal' ); ?></h2>
					<p><?php esc_html_e( 'מידע על בדיקות, מרכזים ושאלות שכדאי לשאול את הצוות המטפל.', 'hea-lth-portal' ); ?></p>
					<a class="hp-inline-link" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'diagnostics' ) ); ?>"><?php esc_html_e( 'למרכז הבדיקות והאבחון', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a>
				</div>
				<div class="hp-mega-panel__links">
					<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'diagnostics_imaging' ) ); ?>"><span><?php esc_html_e( 'דימות רפואי', 'hea-lth-portal' ); ?></span><small><?php esc_html_e( 'MRI, CT, PET CT ואולטרסאונד', 'hea-lth-portal' ); ?></small></a>
					<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'diagnostics_laboratory' ) ); ?>"><span><?php esc_html_e( 'בדיקות מעבדה', 'hea-lth-portal' ); ?></span><small><?php esc_html_e( 'הכנה, פענוח והמשך בירור', 'hea-lth-portal' ); ?></small></a>
					<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'diagnostics_second_opinion' ) ); ?>"><span><?php esc_html_e( 'חוות דעת נוספת', 'hea-lth-portal' ); ?></span><small><?php esc_html_e( 'שאלות, מסמכים והכנה לשיחה', 'hea-lth-portal' ); ?></small></a>
					<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'health_technology' ) ); ?>"><span><?php esc_html_e( 'טכנולוגיות בריאות', 'hea-lth-portal' ); ?></span><small><?php esc_html_e( 'כלים, מכשירים ושירותים מתקדמים', 'hea-lth-portal' ); ?></small></a>
				</div>
			</div>
		</div>

		<div class="hp-mega-panel" id="mega-knowledge" hidden data-mega-panel>
			<div class="hp-shell hp-mega-panel__grid">
				<div class="hp-mega-panel__intro">
					<p class="hp-eyebrow"><?php esc_html_e( 'מדריכים וכלים', 'hea-lth-portal' ); ?></p>
					<h2><?php esc_html_e( 'מידע שנועד לעזור להבין, להשוות ולהתכונן', 'hea-lth-portal' ); ?></h2>
					<p><?php esc_html_e( 'ספריית ידע, מונחים, כלי גילוי ומידע על טכנולוגיות בריאות, עם מסלולים נגישים לחיפוש המשך.', 'hea-lth-portal' ); ?></p>
					<a class="hp-inline-link" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'guides' ) ); ?>"><?php esc_html_e( 'לכל המדריכים והמחקרים', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a>
				</div>
				<div class="hp-mega-panel__links">
					<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'guides' ) ); ?>"><span><?php esc_html_e( 'מדריכים ומחקרים', 'hea-lth-portal' ); ?></span><small><?php esc_html_e( 'שאלות, הכנה, חלופות ומקורות לקריאה', 'hea-lth-portal' ); ?></small></a>
					<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'glossary' ) ); ?>"><span><?php esc_html_e( 'מילון בריאות', 'hea-lth-portal' ); ?></span><small><?php esc_html_e( 'מונחים, הסברים וקישורים למידע שנבדק', 'hea-lth-portal' ); ?></small></a>
					<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'anatomy' ) ); ?>"><span><?php esc_html_e( 'הגוף האינטראקטיבי', 'hea-lth-portal' ); ?></span><small><?php esc_html_e( 'חיפוש לפי אזור גוף ומסלולי מידע', 'hea-lth-portal' ); ?></small></a>
					<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'health_technology' ) ); ?>"><span><?php esc_html_e( 'טכנולוגיות בריאות', 'hea-lth-portal' ); ?></span><small><?php esc_html_e( 'ציוד, דימות, כלים ושירותים מתקדמים', 'hea-lth-portal' ); ?></small></a>
				</div>
			</div>
		</div>
	</nav>

	<div class="hp-search-drawer" id="portal-search" hidden data-search-drawer>
		<div class="hp-shell hp-search-drawer__inner">
			<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search">
				<label class="screen-reader-text" for="portal-search-field"><?php esc_html_e( 'חיפוש באתר', 'hea-lth-portal' ); ?></label>
				<input id="portal-search-field" type="search" name="s" placeholder="<?php esc_attr_e( 'חפשו טיפול, בדיקה, מומחיות או מדריך', 'hea-lth-portal' ); ?>">
				<button class="hp-button" type="submit"><?php esc_html_e( 'חיפוש', 'hea-lth-portal' ); ?></button>
			</form>
			<button class="hp-search-drawer__close" type="button" data-search-close><?php esc_html_e( 'סגירה', 'hea-lth-portal' ); ?></button>
		</div>
	</div>
</header>

<main id="main-content" class="hp-main">
