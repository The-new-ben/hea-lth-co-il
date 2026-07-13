<?php
/**
 * Idempotent provisioning of the portal's foundation pages.
 *
 * The theme ships page templates for approved foundation routes, but a
 * template renders only when a WordPress page exists at that path. Until now
 * the live navigation linked to routes whose pages were never created, so
 * every portal destination returned 404. This provisioner creates the missing
 * pages once per blueprint version. It never updates, overwrites, or deletes
 * an existing page: owner-managed content always wins.
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates missing foundation pages for shipped portal templates.
 */
class Hea_Lth_Page_Provisioner {

	const OPTION_KEY = 'hea_lth_provisioned_pages_blueprint';

	const BLUEPRINT_VERSION = '2026-07-12-01';

	/**
	 * Attach the provisioning check.
	 *
	 * @return void
	 */
	public static function boot() {
		add_action( 'init', array( __CLASS__, 'maybe_provision' ), 20 );
	}

	/**
	 * Foundation pages owned by the portal blueprint.
	 *
	 * Every slug must match a path in the theme route registry, and every
	 * template must ship in the parent theme. Routes without a dedicated
	 * template stay out of the blueprint until their template exists.
	 *
	 * @return array<string, array<string, string>>
	 */
	public static function blueprint() {
		return array(
			'anatomy'           => array(
				'title'    => 'הגוף האינטראקטיבי',
				'template' => 'page-templates/template-anatomy.php',
			),
			'guides'            => array(
				'title'    => 'מדריכים ומחקרים',
				'template' => 'page-templates/template-guides.php',
			),
			'glossary'          => array(
				'title'    => 'מילון בריאות',
				'template' => 'page-templates/template-glossary.php',
			),
			'find-care'         => array(
				'title'    => 'מסלול בחירה',
				'template' => 'page-templates/template-find-care.php',
			),
			'health-technology' => array(
				'title'    => 'טכנולוגיות בריאות וציוד',
				'template' => 'page-templates/template-health-technology.php',
			),
			'professionals'     => array(
				'title'    => 'אזור למקצוענים',
				'template' => 'page-templates/template-professionals.php',
			),
			'treatments'        => array(
				'title'    => 'מרכזי טיפול',
				'template' => 'page-templates/template-treatment-hub.php',
			),
		);
	}

	/**
	 * Create missing foundation pages once per blueprint version.
	 *
	 * @return void
	 */
	public static function maybe_provision() {
		if ( self::BLUEPRINT_VERSION === get_option( self::OPTION_KEY ) ) {
			return;
		}

		foreach ( self::blueprint() as $slug => $page ) {
			$existing = get_page_by_path( $slug, OBJECT, 'page' );

			if ( $existing instanceof WP_Post ) {
				continue;
			}

			$page_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_name'    => $slug,
					'post_title'   => $page['title'],
					'post_content' => '',
				),
				true
			);

			if ( ! is_wp_error( $page_id ) && '' !== $page['template'] ) {
				update_post_meta( (int) $page_id, '_wp_page_template', $page['template'] );
			}
		}

		update_option( self::OPTION_KEY, self::BLUEPRINT_VERSION, false );
	}
}
