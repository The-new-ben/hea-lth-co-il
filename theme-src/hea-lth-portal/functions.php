<?php
/**
 * Theme setup for the Hea-lth Portal rebuild.
 *
 * This theme deliberately owns presentation only. Provider records, inquiry
 * handling, consent storage, and operational services belong to plugins or
 * external systems, never to the public theme layer.
 *
 * @package HeaLthPortal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HEA_LTH_PORTAL_VERSION', '0.6.0' );

require_once get_template_directory() . '/inc/portal-route-registry.php';
require_once get_template_directory() . '/inc/portal-template-helpers.php';
require_once get_template_directory() . '/inc/portal-seo.php';

/**
 * Register visual foundations and navigation locations.
 */
function hea_lth_portal_setup() {
	load_theme_textdomain( 'hea-lth-portal', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_editor_style( 'assets/css/editor.css' );
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

	register_nav_menus(
		array(
			'primary'     => __( 'Primary portal navigation', 'hea-lth-portal' ),
			'utility'     => __( 'Utility navigation', 'hea-lth-portal' ),
			'footer_main' => __( 'Footer navigation', 'hea-lth-portal' ),
		)
	);
}
add_action( 'after_setup_theme', 'hea_lth_portal_setup' );

/**
 * Load the narrow font set and the token-driven portal assets.
 */
function hea_lth_portal_enqueue_assets() {
	$version = HEA_LTH_PORTAL_VERSION;

	wp_enqueue_style(
		'hea-lth-portal-fonts',
		get_theme_file_uri( 'assets/css/fonts.css' ),
		array(),
		$version
	);

	wp_enqueue_style(
		'hea-lth-portal',
		get_theme_file_uri( 'assets/css/portal.css' ),
		array( 'hea-lth-portal-fonts' ),
		$version
	);

	wp_enqueue_style(
		'hea-lth-portal-templates',
		get_theme_file_uri( 'assets/css/templates.css' ),
		array( 'hea-lth-portal' ),
		$version
	);

	wp_enqueue_style(
		'hea-lth-portal-a11y',
		get_theme_file_uri( 'assets/css/a11y.css' ),
		array( 'hea-lth-portal' ),
		$version
	);

	wp_enqueue_script(
		'hea-lth-portal-a11y',
		get_theme_file_uri( 'assets/js/a11y-panel.js' ),
		array(),
		$version,
		true
	);

	wp_enqueue_style(
		'hea-lth-portal-engagement',
		get_theme_file_uri( 'assets/css/engagement.css' ),
		array( 'hea-lth-portal' ),
		$version
	);

	wp_enqueue_script(
		'hea-lth-portal-engagement',
		get_theme_file_uri( 'assets/js/engagement.js' ),
		array(),
		$version,
		true
	);

	/**
	 * The WhatsApp consult bar activates only when the owner provides the
	 * business number (option or filter) — no number, no button.
	 */
	$whatsapp_number = apply_filters( 'hea_lth_whatsapp_number', get_option( 'hea_lth_whatsapp_number', '' ) );
	wp_add_inline_script(
		'hea-lth-portal-engagement',
		'window.heaLthEngage = ' . wp_json_encode( array( 'whatsapp' => (string) $whatsapp_number ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ';',
		'before'
	);

	wp_enqueue_script(
		'hea-lth-portal',
		get_theme_file_uri( 'assets/js/portal.js' ),
		array(),
		$version,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'hea_lth_portal_enqueue_assets' );

/**
 * Return the browser-safe anatomy configuration. The default has no model URL;
 * the platform plugin may replace it only after its license, clinical, visual,
 * GLB, and performance gates pass.
 *
 * @return array
 */
function hea_lth_portal_anatomy_viewer_config() {
	$fallback = array(
		'status' => 'license-gated',
		'engine' => 'none',
		'reason' => 'no-approved-model',
	);
	$config = apply_filters( 'hea_lth_public_anatomy_model_config', $fallback );

	return is_array( $config ) ? $config : $fallback;
}

/**
 * Return the browser-safe directory-map configuration. The default does not
 * load a third-party map, browser key, or provider location. The platform
 * plugin can replace it only after restricted-key, origin, data-quality, and
 * commercial-disclosure gates pass.
 *
 * @return array
 */
function hea_lth_portal_directory_map_config() {
	$fallback = array(
		'status'   => 'configuration-gated',
		'provider' => 'none',
		'reason'   => 'no-approved-map-config',
	);
	$config = apply_filters( 'hea_lth_public_directory_map_config', $fallback );

	return is_array( $config ) ? $config : $fallback;
}

/**
 * Expose only reviewed route keys needed by the static anatomy resolver. The
 * resolver data carries route keys and bounded query filters, never raw paths,
 * so it cannot quietly create a new anatomy or guide URL.
 *
 * @return array<string, string>
 */
function hea_lth_portal_anatomy_route_map() {
	return array(
		'aesthetic_medicine'           => hea_lth_portal_route( 'aesthetic_medicine' ),
		'plastic_surgery_consultation' => hea_lth_portal_route( 'plastic_surgery_consultation' ),
		'hair_transplant_consultation' => hea_lth_portal_route( 'hair_transplant_consultation' ),
		'doctor_clinic_index'          => hea_lth_portal_route( 'doctor_clinic_index' ),
		'guides'                       => hea_lth_portal_foundation_route( 'guides' ),
		'wellness'                     => hea_lth_portal_foundation_route( 'wellness' ),
	);
}

/**
 * Determine whether the self-hosted WebGL engine may be sent to a visitor.
 *
 * @param array $config Public model configuration.
 * @return bool
 */
function hea_lth_portal_has_approved_three_anatomy_model( $config ) {
	return isset( $config['status'], $config['engine'] ) && 'approved' === $config['status'] && 'three-webgl' === $config['engine'];
}

/**
 * Surfaces where the self-hosted WebGL viewer may render: the dedicated anatomy
 * template, the anatomy page, or the homepage hero. This decides only where the
 * runtime may appear; the platform approval gate is still enforced separately
 * before any import map, module, or asset is emitted.
 *
 * @return bool
 */
function hea_lth_portal_is_anatomy_viewer_surface() {
	return is_page_template( 'page-templates/template-anatomy.php' ) || is_page( 'anatomy' ) || is_front_page();
}

/**
 * Print the local import map before the 3D module. Three.js is deliberately
 * self-hosted in the theme package, not loaded from a third-party CDN at run
 * time. The module is emitted only after the platform gate has approved a
 * model for browser delivery.
 *
 * @return void
 */
function hea_lth_portal_print_anatomy_three_import_map() {
	if ( ! hea_lth_portal_is_anatomy_viewer_surface() ) {
		return;
	}

	if ( ! hea_lth_portal_has_approved_three_anatomy_model( hea_lth_portal_anatomy_viewer_config() ) ) {
		return;
	}

	$imports = array(
		'imports' => array(
			'three'          => get_theme_file_uri( 'assets/vendor/three/build/three.module.min.js' ),
			'three/addons/'  => get_theme_file_uri( 'assets/vendor/three/examples/jsm/' ),
		),
	);

	printf( "<script type=\"importmap\">%s</script>\n", wp_json_encode( $imports, JSON_UNESCAPED_SLASHES ) );
}
add_action( 'wp_head', 'hea_lth_portal_print_anatomy_three_import_map', 2 );

/**
 * Load the anatomy discovery controller only where its accessible viewer shell
 * exists. The renderer itself is only loaded after an approved model manifest
 * has supplied a local, browser-deliverable GLB.
 */
function hea_lth_portal_enqueue_anatomy_assets() {
	$is_anatomy_surface = is_page_template( 'page-templates/template-anatomy.php' ) || is_page( 'anatomy' );
	$is_front           = is_front_page();

	if ( ! $is_anatomy_surface && ! $is_front ) {
		return;
	}

	$model_config = hea_lth_portal_anatomy_viewer_config();

	// The accessible region/context resolver powers the "click a body part ->
	// connected services" experience. It ships on both the dedicated anatomy
	// page and the homepage hero (the only surfaces reaching this point), so
	// the interactive body is a first-fold feature, matching category leaders
	// (BioDigital, Zygote).
	wp_enqueue_script(
		'hea-lth-anatomy-discovery',
		get_theme_file_uri( 'assets/js/anatomy-discovery.js' ),
		array( 'hea-lth-portal' ),
		HEA_LTH_PORTAL_VERSION,
		true
	);

	wp_add_inline_script(
		'hea-lth-anatomy-discovery',
		'window.heaLthAnatomyViewer = ' . wp_json_encode( $model_config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '; window.heaLthAnatomyRoutes = ' . wp_json_encode( hea_lth_portal_anatomy_route_map(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ';',
		'before'
	);

	// The verified-directory map is heavier and REST-backed; keep it on the
	// dedicated anatomy page only.
	if ( $is_anatomy_surface ) {
		$map_config = hea_lth_portal_directory_map_config();
		wp_enqueue_script(
			'hea-lth-anatomy-directory-map',
			get_theme_file_uri( 'assets/js/anatomy-directory-map.js' ),
			array( 'hea-lth-anatomy-discovery' ),
			HEA_LTH_PORTAL_VERSION,
			true
		);
		wp_add_inline_script(
			'hea-lth-anatomy-directory-map',
			'window.heaLthDirectoryMap = ' . wp_json_encode( $map_config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ';',
			'before'
		);
	}

	if ( ! hea_lth_portal_has_approved_three_anatomy_model( $model_config ) ) {
		return;
	}

	hea_lth_portal_enqueue_anatomy_three_module();
}
add_action( 'wp_enqueue_scripts', 'hea_lth_portal_enqueue_anatomy_assets' );

/**
 * Enqueue the self-hosted WebGL viewer as an ES module, with a classic-script
 * fallback for WordPress versions without the script-module API.
 *
 * @return void
 */
function hea_lth_portal_enqueue_anatomy_three_module() {
	$three_script = get_theme_file_uri( 'assets/js/anatomy-three-viewer.js' );

	if ( function_exists( 'wp_enqueue_script_module' ) ) {
		wp_enqueue_script_module( 'hea-lth-anatomy-three-viewer', $three_script, array(), HEA_LTH_PORTAL_VERSION );
		return;
	}

	wp_enqueue_script(
		'hea-lth-anatomy-three-viewer',
		$three_script,
		array(),
		HEA_LTH_PORTAL_VERSION,
		true
	);
	wp_script_add_data( 'hea-lth-anatomy-three-viewer', 'type', 'module' );
}

/**
 * Load the public verified-directory browser only on its dedicated template.
 * The browser reads the endpoint but never accepts inquiries, account data,
 * payment data, or medical information.
 *
 * @return void
 */
function hea_lth_portal_enqueue_directory_browser_assets() {
	if ( ! is_page_template( 'page-templates/template-directory.php' ) ) {
		return;
	}

	wp_enqueue_script(
		'hea-lth-directory-browser',
		get_theme_file_uri( 'assets/js/directory-browser.js' ),
		array(),
		HEA_LTH_PORTAL_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'hea_lth_portal_enqueue_directory_browser_assets' );

/**
 * Keep directory-filter query states out of the search index.
 *
 * A selected body region, specialty, or city is a visitor-interface state,
 * not a new editorial or commercial landing page. The canonical directory
 * page owns the crawlable intent, while the filter may remain followable for
 * normal visitor navigation.
 *
 * @param array $robots Existing WordPress robots directives.
 * @return array
 */
function hea_lth_portal_directory_filter_robots( $robots ) {
	if ( ! is_page_template( 'page-templates/template-directory.php' ) || ! hea_lth_portal_get_directory_context() ) {
		return $robots;
	}

	$robots['noindex'] = true;
	$robots['follow']  = true;

	return $robots;
}
add_filter( 'wp_robots', 'hea_lth_portal_directory_filter_robots' );

/**
 * Preload the two Hebrew variable font files that drive the first paint. The
 * fonts are self-hosted under the theme (same-origin), so no third-party
 * font origin is contacted at runtime.
 */
function hea_lth_portal_preload_fonts() {
	$fonts = array(
		'assets/fonts/noto-sans-hebrew-var-hebrew.woff2',
		'assets/fonts/noto-serif-hebrew-var-hebrew.woff2',
	);

	foreach ( $fonts as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( get_theme_file_uri( $font ) )
		);
	}
}
add_action( 'wp_head', 'hea_lth_portal_preload_fonts', 2 );

/**
 * Apply persisted accessibility adjustments before first paint so returning
 * visitors never see a flash of unadjusted content, and expose the statement
 * URL to the panel script. Registered as a src-less head script so the inline
 * code travels through the WordPress script API rather than raw output.
 */
function hea_lth_portal_a11y_boot() {
	$statement_url = wp_json_encode( hea_lth_portal_foundation_route( 'accessibility' ) );

	wp_register_script( 'hea-lth-portal-a11y-boot', '', array(), HEA_LTH_PORTAL_VERSION, false );
	wp_enqueue_script( 'hea-lth-portal-a11y-boot' );
	wp_add_inline_script(
		'hea-lth-portal-a11y-boot',
		'(function(){try{var s=JSON.parse(localStorage.getItem("hea-lth-a11y")||"{}");var m={"font-110":"hp-a11y-font-110","font-125":"hp-a11y-font-125","contrast":"hp-a11y-contrast","underline":"hp-a11y-underline","no-motion":"hp-a11y-no-motion"};for(var k in m){if(s[k]){document.documentElement.classList.add(m[k]);}}}catch(e){}window.heaLthA11y={statementUrl:' . $statement_url . '};})();'
	);
}
add_action( 'wp_enqueue_scripts', 'hea_lth_portal_a11y_boot' );

/**
 * Serve the Hea-lth brand icon from the theme as the authoritative favicon.
 *
 * The site previously carried a legacy wp-admin Site Icon from the prior brand
 * ("health online"). Because the portal's identity is owned in code, the theme
 * brand mark takes precedence: the core Site Icon output is removed on the
 * front end and the brand SVG/PNG icons are emitted instead. To change the
 * favicon, replace the assets under assets/img/ (see the brand mark in
 * favicon.svg); a new wp-admin Site Icon will not override the brand.
 */
function hea_lth_portal_brand_site_icon() {
	remove_action( 'wp_head', 'wp_site_icon', 99 );

	printf( '<link rel="icon" type="image/svg+xml" href="%s">' . "\n", esc_url( get_theme_file_uri( 'assets/img/favicon.svg' ) ) );
	printf( '<link rel="icon" type="image/png" sizes="32x32" href="%s">' . "\n", esc_url( get_theme_file_uri( 'assets/img/favicon-32.png' ) ) );
	printf( '<link rel="apple-touch-icon" href="%s">' . "\n", esc_url( get_theme_file_uri( 'assets/img/favicon-180.png' ) ) );
}
add_action( 'wp_head', 'hea_lth_portal_brand_site_icon', 1 );

/**
 * Keep unused WooCommerce front-end weight off portal pages. The commerce
 * stack remains fully active on its own surfaces (shop, cart, checkout,
 * account); portal pages simply stop paying for commerce CSS/JS they never
 * render. Removing the plugin itself stays an owner decision in wp-admin.
 */
function hea_lth_portal_trim_commerce_assets() {
	if (
		! function_exists( 'is_woocommerce' )
		|| ! function_exists( 'is_cart' )
		|| ! function_exists( 'is_checkout' )
		|| ! function_exists( 'is_account_page' )
	) {
		return;
	}

	if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) {
		return;
	}

	wp_dequeue_style( 'woocommerce-layout' );
	wp_dequeue_style( 'woocommerce-smallscreen' );
	wp_dequeue_style( 'woocommerce-general' );
	wp_dequeue_style( 'wc-blocks-style' );
	wp_dequeue_script( 'wc-cart-fragments' );
}
add_action( 'wp_enqueue_scripts', 'hea_lth_portal_trim_commerce_assets', 99 );

/**
 * Provide a restrained description only when a dedicated SEO plugin is not
 * responsible for metadata. Individual editorial pages may supply the
 * _hea_lth_meta_description field; the public theme never fabricates medical
 * claims or provider data.
 */
function hea_lth_portal_fallback_meta_description() {
	if (
		is_admin()
		|| is_feed()
		|| defined( 'WPSEO_VERSION' )
		|| defined( 'RANK_MATH_VERSION' )
		|| defined( 'AIOSEO_VERSION' )
		|| defined( 'SEOPRESS_VERSION' )
	) {
		return;
	}

	$description = '';

	if ( is_front_page() ) {
		$description = __( 'Hea-lth: מידע, מדריכים, אינדקס מקצוענים ומסלולי בחירה ברפואה פרטית.', 'hea-lth-portal' );
	} elseif ( is_singular() ) {
		$post_id     = get_queried_object_id();
		$description = get_post_meta( $post_id, '_hea_lth_meta_description', true );

		if ( ! $description ) {
			$description = get_the_excerpt( $post_id );
		}
	}

	if ( ! $description ) {
		$description = __( 'Hea-lth מרכז מידע וכלי בחירה לרפואה פרטית, שירותי בריאות וטכנולוגיות רפואיות.', 'hea-lth-portal' );
	}

	printf(
		"<meta name=\"description\" content=\"%s\">\n",
		esc_attr( wp_trim_words( wp_strip_all_tags( $description ), 32, '' ) )
	);
}
add_action( 'wp_head', 'hea_lth_portal_fallback_meta_description', 1 );

/**
 * A deliberate navigation fallback keeps the first theme preview complete
 * before menu records are configured in WordPress.
 *
 * @param array $args wp_nav_menu arguments.
 * @return void
 */
function hea_lth_portal_primary_menu_fallback( $args = array() ) {
	$items = array(
		array(
			'label' => __( 'טיפולים וניתוחים', 'hea-lth-portal' ),
			'url'   => hea_lth_portal_foundation_route( 'treatments' ),
		),
		array(
			'label' => __( 'רופאים ומרפאות', 'hea-lth-portal' ),
			'url'   => hea_lth_portal_route( 'doctor_clinic_index' ),
		),
		array(
			'label' => __( 'בדיקות ואבחון', 'hea-lth-portal' ),
			'url'   => hea_lth_portal_foundation_route( 'diagnostics' ),
		),
		array(
			'label' => __( 'מדריכים ומחקרים', 'hea-lth-portal' ),
			'url'   => hea_lth_portal_foundation_route( 'guides' ),
		),
		array(
			'label' => __( 'למקצוענים', 'hea-lth-portal' ),
			'url'   => hea_lth_portal_foundation_route( 'professionals' ),
		),
	);

	printf( '<ul id="%1$s" class="%2$s">', esc_attr( isset( $args['menu_id'] ) ? $args['menu_id'] : 'portal-primary-menu' ), esc_attr( isset( $args['menu_class'] ) ? $args['menu_class'] : 'portal-menu' ) );
	foreach ( $items as $item ) {
		printf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( $item['url'] ),
			esc_html( $item['label'] )
		);
	}
	echo '</ul>';
}

/**
 * Add a sensible page excerpt where editorial cards need it.
 *
 * @param string $more Ellipsis markup.
 * @return string
 */
function hea_lth_portal_excerpt_more( $more ) {
	return '…';
}
add_filter( 'excerpt_more', 'hea_lth_portal_excerpt_more' );
