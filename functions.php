<?php
/**
 * Theme bootstrap for Health Revenue.
 *
 * @package HealthRevenue
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const HEALTH_REVENUE_VERSION = '0.1.0';

/**
 * Configure theme support and navigation surfaces.
 */
function health_revenue_setup(): void {
	load_theme_textdomain( 'health-revenue', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary'      => __( 'Primary Navigation', 'health-revenue' ),
			'footer'       => __( 'Footer Navigation', 'health-revenue' ),
			'professional' => __( 'Professional Navigation', 'health-revenue' ),
		)
	);
}
add_action( 'after_setup_theme', 'health_revenue_setup' );

/**
 * Enqueue theme assets.
 */
function health_revenue_assets(): void {
	$theme_uri = get_template_directory_uri();

	wp_enqueue_style(
		'health-revenue-theme',
		$theme_uri . '/assets/css/theme.css',
		array(),
		HEALTH_REVENUE_VERSION
	);

	wp_enqueue_script(
		'health-revenue-navigation',
		$theme_uri . '/assets/js/navigation.js',
		array(),
		HEALTH_REVENUE_VERSION,
		true
	);

	wp_enqueue_script(
		'health-revenue-lead-router',
		$theme_uri . '/assets/js/lead-router.js',
		array(),
		HEALTH_REVENUE_VERSION,
		true
	);

	wp_localize_script(
		'health-revenue-lead-router',
		'heaLthLeadRouter',
		array(
			'endpoint' => esc_url_raw( rest_url( 'hea-lth-ops/v1/leads' ) ),
			'siteUrl'  => esc_url_raw( home_url( '/' ) ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'health_revenue_assets' );

/**
 * Add a theme-specific body class for scoped styling.
 *
 * @param array<int,string> $classes Body classes.
 * @return array<int,string>
 */
function health_revenue_body_classes( array $classes ): array {
	$classes[] = 'health-revenue-theme';
	return $classes;
}
add_filter( 'body_class', 'health_revenue_body_classes' );

/**
 * Render a fallback primary menu before WordPress menus are configured.
 */
function health_revenue_primary_menu_fallback(): void {
	$items = array(
		array( __( 'רפואה אסתטית', 'health-revenue' ), '/aesthetic-medicine-treatments/' ),
		array( __( 'ניתוחים פלסטיים', 'health-revenue' ), '/plastic-surgery-consultation/' ),
		array( __( 'שיער ועור', 'health-revenue' ), '/hair-transplant-consultation/' ),
		array( __( 'רפואה פרטית', 'health-revenue' ), '/private-doctor-appointment/' ),
		array( __( 'בדיקות ו-Wellness', 'health-revenue' ), '/premium-health-services/' ),
		array( __( 'רופאים וקליניקות', 'health-revenue' ), '/doctor-clinic-index/' ),
	);
	echo '<ul class="hr-nav__list">';
	foreach ( $items as $item ) {
		printf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( home_url( $item[1] ) ),
			esc_html( $item[0] )
		);
	}
	echo '</ul>';
}
