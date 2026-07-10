<?php
/**
 * Site header.
 *
 * @package HealthRevenue
 */

?><!doctype html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="hr-skip-link" href="#main"><?php esc_html_e( 'דלגו לתוכן המרכזי', 'health-revenue' ); ?></a>

<header class="hr-header" data-hr-header>
	<div class="hr-header__bar">
		<a class="hr-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Hea-lth דף הבית', 'health-revenue' ); ?>">
			<span class="hr-brand__mark" aria-hidden="true">H</span>
			<span>
				<span class="hr-brand__name">Hea-lth</span>
				<span class="hr-brand__tagline"><?php esc_html_e( 'בריאות פרטית, אסתטיקה ורפואה פרימיום', 'health-revenue' ); ?></span>
			</span>
		</a>

		<button class="hr-menu-toggle" type="button" data-hr-menu-toggle aria-expanded="false" aria-controls="hr-primary-menu">
			<span><?php esc_html_e( 'תפריט', 'health-revenue' ); ?></span>
			<span class="hr-menu-toggle__lines" aria-hidden="true"></span>
		</button>

		<nav class="hr-nav" id="hr-primary-menu" data-hr-menu aria-label="<?php esc_attr_e( 'ניווט ראשי', 'health-revenue' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => '',
					'menu_class'     => 'hr-nav__list',
					'fallback_cb'    => 'health_revenue_primary_menu_fallback',
				)
			);
			?>
		</nav>

		<a class="hr-header__cta" href="#lead-intake"><?php esc_html_e( 'בדיקת התאמה', 'health-revenue' ); ?></a>
	</div>
</header>

<main id="main" class="hr-main">
