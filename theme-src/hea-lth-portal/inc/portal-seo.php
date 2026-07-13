<?php
/**
 * Structured data and social metadata for the portal.
 *
 * Emits only facts that are true of the site itself (name, URL, language,
 * logo, search action). It never fabricates medical claims, ratings, authors,
 * or organisation details. Per-page editorial schema (MedicalWebPage, author,
 * reviewed date) is added by the editorial templates where that data exists.
 *
 * @package HeaLthPortal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether a dedicated SEO plugin owns social/meta output on this site.
 *
 * @return bool
 */
function hea_lth_portal_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' )
		|| defined( 'RANK_MATH_VERSION' )
		|| defined( 'AIOSEO_VERSION' )
		|| defined( 'SEOPRESS_VERSION' );
}

/**
 * Whether the active SEO plugin already publishes site-level Organization /
 * WebSite structured data. Yoast does so only once its "site representation"
 * is configured with an organisation name.
 *
 * @return bool
 */
function hea_lth_portal_seo_plugin_owns_site_schema() {
	if ( ! defined( 'WPSEO_VERSION' ) ) {
		return hea_lth_portal_seo_plugin_active();
	}

	$titles = get_option( 'wpseo_titles' );

	return is_array( $titles ) && ! empty( $titles['company_name'] );
}

/**
 * Absolute URL of the brand logo used in structured data and social cards.
 *
 * @return string
 */
function hea_lth_portal_brand_image_url() {
	return get_theme_file_uri( 'assets/img/favicon-180.png' );
}

/**
 * Emit Organization + WebSite JSON-LD once, in the document head.
 *
 * @return void
 */
function hea_lth_portal_json_ld() {
	if ( is_admin() || hea_lth_portal_seo_plugin_owns_site_schema() ) {
		return;
	}

	$home = home_url( '/' );

	$organization = array(
		'@type'  => 'Organization',
		'@id'    => $home . '#organization',
		'name'   => get_bloginfo( 'name' ),
		'url'    => $home,
		'logo'   => array(
			'@type' => 'ImageObject',
			'url'   => hea_lth_portal_brand_image_url(),
		),
	);

	$website = array(
		'@type'           => 'WebSite',
		'@id'             => $home . '#website',
		'url'             => $home,
		'name'            => get_bloginfo( 'name' ),
		'inLanguage'      => 'he-IL',
		'publisher'       => array( '@id' => $home . '#organization' ),
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => array(
				'@type'       => 'EntryPoint',
				'urlTemplate' => $home . '?s={search_term_string}',
			),
			'query-input' => 'required name=search_term_string',
		),
	);

	$graph = array(
		'@context' => 'https://schema.org',
		'@graph'   => array( $organization, $website ),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'hea_lth_portal_json_ld', 5 );

/**
 * Resolve a concise, non-fabricated description for the current view.
 *
 * @return string
 */
function hea_lth_portal_social_description() {
	$default = get_bloginfo( 'description' );

	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$custom  = get_post_meta( $post_id, '_hea_lth_meta_description', true );
		if ( is_string( $custom ) && '' !== trim( $custom ) ) {
			return trim( $custom );
		}

		$excerpt = has_excerpt( $post_id ) ? (string) get_the_excerpt( $post_id ) : '';
		if ( '' !== trim( $excerpt ) ) {
			return trim( wp_strip_all_tags( $excerpt ) );
		}
	}

	return (string) $default;
}

/**
 * Emit Open Graph and Twitter card metadata using only real, on-site values.
 *
 * @return void
 */
function hea_lth_portal_social_meta() {
	// A dedicated SEO plugin (Yoast is live on this site) owns Open Graph and
	// Twitter output; emitting a second set creates conflicting signals.
	if ( is_admin() || hea_lth_portal_seo_plugin_active() ) {
		return;
	}

	$site_name   = get_bloginfo( 'name' );
	$description  = hea_lth_portal_social_description();
	$url          = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );
	$is_article   = is_singular( array( 'post', 'hp_guide', 'hp_treatment' ) );

	if ( is_front_page() ) {
		$title = $site_name;
	} elseif ( is_singular() ) {
		$title = get_the_title();
	} else {
		$title = wp_get_document_title();
	}

	$tags = array(
		'og:site_name'   => $site_name,
		'og:type'        => $is_article ? 'article' : 'website',
		'og:locale'      => 'he_IL',
		'og:title'       => $title,
		'og:description' => $description,
		'og:url'         => $url,
		'og:image'       => hea_lth_portal_brand_image_url(),
	);

	foreach ( $tags as $property => $content ) {
		if ( '' === (string) $content ) {
			continue;
		}
		printf( '<meta property="%s" content="%s">' . "\n", esc_attr( $property ), esc_attr( $content ) );
	}

	printf( '<meta name="twitter:card" content="%s">' . "\n", 'summary_large_image' );
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	if ( '' !== (string) $description ) {
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $description ) );
	}
	printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( hea_lth_portal_brand_image_url() ) );
}
add_action( 'wp_head', 'hea_lth_portal_social_meta', 6 );
