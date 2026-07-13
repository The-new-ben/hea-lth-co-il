<?php
/**
 * Local visual harness for the new Hea-lth homepage.
 *
 * It loads the actual theme templates with only the small set of WordPress
 * presentation functions used by the homepage. It is never part of the theme
 * package and cannot alter WordPress or the live site.
 */

declare( strict_types = 1 );

define( 'ABSPATH', __DIR__ . DIRECTORY_SEPARATOR );

$hea_lth_preview_theme = realpath( __DIR__ . '/../../theme-src/hea-lth-portal' );
if ( false === $hea_lth_preview_theme ) {
	http_response_code( 500 );
	exit( 'Theme source was not found.' );
}

$hea_lth_preview_page = isset( $_GET['page'] ) ? (string) $_GET['page'] : 'home';
$hea_lth_preview_pages = array(
	'home'          => array( 'file' => 'front-page.php', 'title' => 'Hea-lth' ),
	'treatments'    => array( 'file' => 'page-templates/template-treatment-hub.php', 'title' => 'טיפולים וניתוחים' ),
	'directory'     => array( 'file' => 'page-templates/template-directory.php', 'title' => 'רופאים, מרפאות ושירותים' ),
	'guides'        => array( 'file' => 'page-templates/template-guides.php', 'title' => 'מדריכים ומחקרים' ),
	'professionals' => array( 'file' => 'page-templates/template-professionals.php', 'title' => 'לרופאים, מרפאות ונותני שירות' ),
	'account'       => array( 'file' => 'page-templates/template-account.php', 'title' => 'האזור האישי' ),
	'anatomy'       => array( 'file' => 'page-templates/template-anatomy.php', 'title' => 'הגוף האינטראקטיבי' ),
	'glossary'      => array( 'file' => 'page-templates/template-glossary.php', 'title' => 'מילון בריאות' ),
	'technology'    => array( 'file' => 'page-templates/template-health-technology.php', 'title' => 'טכנולוגיות בריאות וציוד' ),
	'find-care'     => array( 'file' => 'page-templates/template-find-care.php', 'title' => 'מסלול בחירה' ),
	'profile-preview' => array( 'file' => 'single-hp_provider.php', 'title' => 'תצוגת מבנה פרופיל' ),
	'treatment-preview' => array( 'file' => 'single-hp_treatment.php', 'title' => 'תצוגת מבנה טיפול' ),
);

if ( ! isset( $hea_lth_preview_pages[ $hea_lth_preview_page ] ) ) {
	$hea_lth_preview_page = 'home';
}

$hea_lth_preview_title          = $hea_lth_preview_pages[ $hea_lth_preview_page ]['title'];
$hea_lth_preview_main_post_seen = false;
$hea_lth_preview_three_fixture  = 'anatomy' === $hea_lth_preview_page && isset( $_GET['threeFixture'] ) && '1' === (string) $_GET['threeFixture'];
$hea_lth_preview_front_three    = 'home' === $hea_lth_preview_page && isset( $_GET['threeFixture'] ) && '1' === (string) $_GET['threeFixture'];

/**
 * Isolated local fixtures used only to render dormant profile and treatment
 * template shells. They are never loaded by the WordPress theme or included
 * in a release package. The visible preview notice keeps them from being
 * mistaken for medical or provider content.
 *
 * @return array<string, mixed>
 */
function hea_lth_preview_fixture(): array {
	global $hea_lth_preview_page;

	$fixtures = array(
		'profile-preview' => array(
			'id'        => 901,
			'post_type' => 'hp_provider',
			'content'   => '<p>תצוגת פיתוח מקומית בלבד. פרטי המבנה כאן אינם מתייחסים לאיש, למרפאה או לשירות אמיתי.</p>',
			'meta'      => array(
				'hp_public_state' => 'verified',
				'hp_city'         => 'תצוגה מקומית',
				'hp_languages'    => array( 'עברית' ),
				'hp_accessibility' => array( 'תצוגת נגישות' ),
				'hp_last_verified' => '2026-07-11',
			),
			'terms'     => array(
				'hp_specialty'    => array( 'תחום תצוגה' ),
				'hp_region'       => array( 'אזור תצוגה' ),
				'hp_service_type' => array( 'סוג שירות לתצוגה' ),
				'hp_body_region'  => array( 'אזור גוף לתצוגה' ),
			),
		),
		'treatment-preview' => array(
			'id'        => 902,
			'post_type' => 'hp_treatment',
			'content'   => '<p>תצוגת פיתוח מקומית בלבד. עמוד זה נועד לבדיקת מבנה ורינדור ואינו תוכן רפואי לפרסום.</p>',
			'meta'      => array(
				'hp_editorial_state' => 'approved',
				'hp_last_reviewed'   => '2026-07-11',
				'hp_source_note'     => 'מקור תצוגה מקומי בלבד',
			),
			'terms'     => array(
				'hp_specialty'    => array( 'תחום תצוגה' ),
				'hp_service_type' => array( 'סוג שירות לתצוגה' ),
				'hp_body_region'  => array( 'אזור גוף לתצוגה' ),
			),
		),
	);

	return isset( $fixtures[ $hea_lth_preview_page ] ) ? $fixtures[ $hea_lth_preview_page ] : array();
}

/**
 * Local-only approved skeletal configuration that mirrors the shape the platform
 * plugin's public gate returns. It is used purely to render and QA the homepage
 * WebGL viewer against the real, promoted Draco assets. It never ships in a
 * package and never touches WordPress or the live site.
 *
 * @return array<string, mixed>
 */
function hea_lth_preview_model_url( string $relative ): string {
	$disk = realpath( __DIR__ . '/../../theme-src/hea-lth-portal/' . $relative );
	$version = false !== $disk ? (string) filemtime( $disk ) : '0';

	return get_theme_file_uri( $relative ) . '?v=' . $version;
}

function hea_lth_preview_skeletal_config(): array {
	// Load the canonical shipped manifest so the local viewer reflects the real
	// gated structure/layer set. LOD paths are rewritten to local, cache-busted
	// theme URLs; everything else mirrors the manifest exactly.
	$manifest_path = realpath( __DIR__ . '/../../design-lab/3d-human-engine/examples/z-anatomy-skeletal-v1.manifest.json' );
	$manifest      = ( false !== $manifest_path ) ? json_decode( (string) file_get_contents( $manifest_path ), true ) : null;
	if ( ! is_array( $manifest ) ) {
		return array( 'status' => 'license-gated', 'engine' => 'none' );
	}

	$file_by_purpose = array(
		'mobile'  => 'assets/models/skeletal-preview.glb',
		'desktop' => 'assets/models/skeletal-detail.glb',
		'detail'  => 'assets/models/skeletal-detail.glb',
	);

	$lods = array();
	foreach ( $manifest['asset']['lods'] as $lod ) {
		if ( 'detail' === $lod['purpose'] ) {
			continue; // gate-proof LOD is not a runtime target
		}
		$relative = isset( $file_by_purpose[ $lod['purpose'] ] ) ? $file_by_purpose[ $lod['purpose'] ] : 'assets/models/skeletal-detail.glb';
		$lods[]   = array(
			'id'              => $lod['id'],
			'path'            => hea_lth_preview_model_url( $relative ),
			'purpose'         => $lod['purpose'],
			'triangleCount'   => $lod['triangleCount'],
			'compressedBytes' => $lod['compressedBytes'],
		);
	}

	$structures = array();
	foreach ( $manifest['structures'] as $structure ) {
		$structures[] = array(
			'id'       => $structure['id'],
			'regionId' => preg_replace( '/^anatomy:/', '', $structure['id'] ),
			'label'    => $structure['labels']['he'],
			'meshIds'  => $structure['meshIds'],
			'contexts' => $structure['contexts'],
		);
	}

	$layers = array();
	foreach ( $manifest['layers'] as $layer ) {
		$layers[] = array(
			'id'             => $layer['id'],
			'kind'           => $layer['kind'],
			'label'          => 'מערכת השלד',
			'defaultVisible' => ! empty( $layer['defaultVisible'] ),
			'meshIds'        => $layer['meshIds'],
		);
	}

	return array(
		'status'       => 'approved',
		'engine'       => 'three-webgl',
		'testOnly'     => true,
		'modelId'      => $manifest['modelId'],
		'version'      => $manifest['version'],
		'asset'        => array( 'lods' => $lods ),
		'layers'       => $layers,
		'structures'   => $structures,
		'capabilities' => array( 'rotate' => true, 'zoom' => true, 'meshSelection' => true, 'layerSelection' => count( $layers ) > 1 ),
	);
}

if ( $hea_lth_preview_front_three ) {
	$GLOBALS['hea_lth_preview_front_anatomy'] = hea_lth_preview_skeletal_config();
}

define( 'HEA_LTH_PORTAL_PREVIEW', true );

function hea_lth_preview_theme_directory(): string {
	global $hea_lth_preview_theme;

	return $hea_lth_preview_theme;
}

function language_attributes(): void {
	echo 'lang="he" dir="rtl"';
}

function bloginfo( string $show ): void {
	if ( 'charset' === $show ) {
		echo 'UTF-8';
	}
}

function hea_lth_preview_base_url(): string {
	$host = isset( $_SERVER['HTTP_HOST'] ) && '' !== $_SERVER['HTTP_HOST'] ? (string) $_SERVER['HTTP_HOST'] : '127.0.0.1:8787';

	return 'http://' . $host;
}

function home_url( string $path = '/' ): string {
	return hea_lth_preview_base_url() . $path;
}

function rest_url( string $path = '' ): string {
	return hea_lth_preview_base_url() . '/wp-json/' . ltrim( $path, '/' );
}

function get_theme_file_uri( string $path = '' ): string {
	return hea_lth_preview_base_url() . '/theme-src/hea-lth-portal/' . ltrim( $path, '/' );
}

function esc_html( $value ): string {
	return htmlspecialchars( (string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
}

function esc_attr( $value ): string {
	return htmlspecialchars( (string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
}

function esc_url( $value ): string {
	return filter_var( (string) $value, FILTER_SANITIZE_URL );
}

function wp_json_encode( $value, int $options = 0 ): string {
	return (string) json_encode( $value, $options | JSON_UNESCAPED_UNICODE );
}

function absint( $value ): int {
	return abs( (int) $value );
}

function sanitize_title( string $value ): string {
	return trim( $value );
}

function wp_unslash( string $value ): string {
	return stripslashes( $value );
}

function __( string $text, string $domain = '' ): string {
	return $text;
}

function esc_html__( string $text, string $domain = '' ): string {
	return esc_html( $text );
}

function esc_html_e( string $text, string $domain = '' ): void {
	echo esc_html( $text );
}

function esc_attr_e( string $text, string $domain = '' ): void {
	echo esc_attr( $text );
}

function body_class( string $class = '' ): void {
	echo 'class="' . esc_attr( $class ) . '"';
}

function wp_body_open(): void {
	if ( ! empty( hea_lth_preview_fixture() ) ) {
		echo '<div data-preview-fixture="true" style="position:relative;z-index:20;padding:9px 18px;background:#e8c780;color:#09211e;text-align:center;font:700 12px/1.5 Arial,sans-serif">תצוגת פיתוח מקומית בלבד. אין כאן פרופיל, טיפול או מידע רפואי לפרסום.</div>';
	}
}

function wp_head(): void {
	global $hea_lth_preview_title;

	echo '<title>' . esc_html( $hea_lth_preview_title ) . " | Hea-lth</title>";
	echo '<meta name="description" content="Hea-lth: מידע, מדריכים, אינדקס מקצוענים ומסלולי בחירה ברפואה פרטית.">';
	echo '<link rel="icon" href="data:,">';
	echo '<link rel="preload" href="' . esc_attr( get_theme_file_uri( 'assets/fonts/noto-sans-hebrew-var-hebrew.woff2' ) ) . '" as="font" type="font/woff2" crossorigin>';
	echo '<link rel="preload" href="' . esc_attr( get_theme_file_uri( 'assets/fonts/noto-serif-hebrew-var-hebrew.woff2' ) ) . '" as="font" type="font/woff2" crossorigin>';
	echo '<link rel="stylesheet" href="' . esc_attr( get_theme_file_uri( 'assets/css/fonts.css' ) ) . '">';
	echo '<link rel="stylesheet" href="' . esc_attr( get_theme_file_uri( 'assets/css/portal.css' ) ) . '">';
	echo '<link rel="stylesheet" href="' . esc_attr( get_theme_file_uri( 'assets/css/templates.css' ) ) . '">';
}

function wp_footer(): void {
	global $hea_lth_preview_page, $hea_lth_preview_three_fixture;

	echo '<script src="' . esc_attr( get_theme_file_uri( 'assets/js/portal.js' ) ) . '"></script>';
	if ( 'anatomy' === $hea_lth_preview_page ) {
		if ( $hea_lth_preview_three_fixture ) {
			$fixture_config = array(
				'status'    => 'approved',
				'engine'     => 'three-webgl',
				'testOnly'   => true,
				'modelId'    => 'anatomy-engine-test-fixture-v1',
				'asset'      => array(
					'lods' => array(
						array(
							'id'      => 'lod-0',
							'path'    => 'http://127.0.0.1:8787/tooling/anatomy-fixture/anatomy-engine-test-fixture.glb',
							'purpose' => 'desktop',
						),
					),
				),
				'layers'     => array(
					array(
						'id'             => 'skin',
						'label'          => 'מעטפת מבחן',
						'kind'           => 'surface',
						'defaultVisible' => true,
						'meshIds'        => array( 'skin.outer', 'skin.head', 'face.nose.external', 'skin.left-arm', 'skin.right-arm', 'skin.left-leg', 'skin.right-leg' ),
					),
					array(
						'id'             => 'respiratory',
						'label'          => 'שכבת נשימה מבחן',
						'kind'           => 'system',
						'defaultVisible' => false,
						'meshIds'        => array( 'respiratory.left-lung', 'respiratory.right-lung', 'respiratory.nasal-cavity' ),
					),
					array(
						'id'             => 'skeletal',
						'label'          => 'שלד מבחן',
						'kind'           => 'system',
						'defaultVisible' => false,
						'meshIds'        => array( 'skeleton.spine', 'organ.heart' ),
					),
				),
				'structures' => array(
					array(
						'id'       => 'anatomy:nose',
						'regionId' => 'nose',
						'label'    => 'אף מבחן',
						'meshIds'  => array( 'face.nose.external', 'respiratory.nasal-cavity' ),
					),
					array(
						'id'       => 'anatomy:skin-face',
						'regionId' => 'skin-face',
						'label'    => 'פנים מבחן',
						'meshIds'  => array( 'skin.head' ),
					),
					array(
						'id'       => 'anatomy:movement',
						'regionId' => 'movement',
						'label'    => 'עמוד שדרה מבחן',
						'meshIds'  => array( 'skeleton.spine' ),
					),
				),
			);
			$import_map = array(
				'imports' => array(
					'three'         => get_theme_file_uri( 'assets/vendor/three/build/three.module.min.js' ),
					'three/addons/' => get_theme_file_uri( 'assets/vendor/three/examples/jsm/' ),
				),
			);

			echo '<script>window.heaLthAnatomyViewer = ' . wp_json_encode( $fixture_config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ';</script>';
		}
		// Mirror the production route map injected by functions.php so the
		// resolver's service links are testable locally.
		$preview_anatomy_routes = array(
			'aesthetic_medicine'           => '/aesthetic-medicine-treatments/',
			'plastic_surgery_consultation' => '/plastic-surgery-consultation/',
			'hair_transplant_consultation' => '/hair-transplant-consultation/',
			'doctor_clinic_index'          => '/doctor-clinic-index/',
			'guides'                       => '/guides/',
			'wellness'                     => '/wellness/',
		);
		echo '<script>window.heaLthAnatomyRoutes = ' . wp_json_encode( $preview_anatomy_routes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ';</script>';
		echo '<script src="' . esc_attr( get_theme_file_uri( 'assets/js/anatomy-discovery.js' ) ) . '"></script>';
		if ( $hea_lth_preview_three_fixture ) {
			echo '<script type="importmap">' . wp_json_encode( $import_map, JSON_UNESCAPED_SLASHES ) . '</script>';
			echo '<script type="module" src="' . esc_attr( get_theme_file_uri( 'assets/js/anatomy-three-viewer.js' ) ) . '"></script>';
		}
	}
	if ( 'directory' === $hea_lth_preview_page ) {
		echo '<script src="' . esc_attr( get_theme_file_uri( 'assets/js/directory-browser.js' ) ) . '"></script>';
	}
	if ( 'home' === $hea_lth_preview_page && ( $GLOBALS['hea_lth_preview_front_three'] ?? false ) ) {
		$front_config = hea_lth_preview_skeletal_config();
		$front_map    = array(
			'imports' => array(
				'three'         => get_theme_file_uri( 'assets/vendor/three/build/three.module.min.js' ),
				'three/addons/' => get_theme_file_uri( 'assets/vendor/three/examples/jsm/' ),
			),
		);
		$front_routes = array(
			'aesthetic_medicine'           => '/aesthetic-medicine-treatments/',
			'plastic_surgery_consultation' => '/plastic-surgery-consultation/',
			'hair_transplant_consultation' => '/hair-transplant-consultation/',
			'doctor_clinic_index'          => '/doctor-clinic-index/',
			'guides'                       => '/guides/',
			'wellness'                     => '/wellness/',
		);
		// Mirror production: the homepage hero now carries the full resolver, so
		// the discovery controller + route map must load alongside the module.
		echo '<script>window.heaLthAnatomyViewer = ' . wp_json_encode( $front_config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '; window.heaLthAnatomyRoutes = ' . wp_json_encode( $front_routes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ';</script>';
		echo '<script src="' . esc_attr( get_theme_file_uri( 'assets/js/anatomy-discovery.js' ) ) . '"></script>';
		echo '<script type="importmap">' . wp_json_encode( $front_map, JSON_UNESCAPED_SLASHES ) . '</script>';
		echo '<script type="module" src="' . esc_attr( get_theme_file_uri( 'assets/js/anatomy-three-viewer.js' ) ) . '"></script>';
	}
}

function get_header(): void {
	require hea_lth_preview_theme_directory() . '/header.php';
}

function get_footer(): void {
	require hea_lth_preview_theme_directory() . '/footer.php';
}

function have_posts(): bool {
	global $hea_lth_preview_main_post_seen;

	return ! $hea_lth_preview_main_post_seen;
}

function the_post(): void {
	global $hea_lth_preview_main_post_seen;

	$hea_lth_preview_main_post_seen = true;
}

function get_the_title( $post = 0 ): string {
	global $hea_lth_preview_title;

	return $hea_lth_preview_title;
}

function get_the_content( $more_link_text = null, bool $strip_teaser = false, $post = null ): string {
	$fixture = hea_lth_preview_fixture();

	return isset( $fixture['content'] ) ? (string) $fixture['content'] : '';
}

function wp_strip_all_tags( string $string ): string {
	return trim( strip_tags( $string ) );
}

function the_content(): void {
	echo get_the_content();
}

function post_class( string $class = '' ): void {
	echo 'class="' . esc_attr( $class ) . '"';
}

function has_post_thumbnail( $post = 0 ): bool {
	return false;
}

function the_post_thumbnail( string $size = 'large' ): void {
}

function get_the_post_thumbnail( $post = 0, string $size = 'post-thumbnail', $attr = '' ): string {
	return '';
}

function get_queried_object_id(): int {
	$fixture = hea_lth_preview_fixture();

	return isset( $fixture['id'] ) ? (int) $fixture['id'] : 0;
}

function get_post_type( $post = null ): string {
	$fixture = hea_lth_preview_fixture();

	return isset( $fixture['post_type'] ) ? (string) $fixture['post_type'] : 'page';
}

function get_post_meta( int $post_id, string $key, bool $single = false ) {
	$fixture = hea_lth_preview_fixture();
	$value   = isset( $fixture['meta'][ $key ] ) ? $fixture['meta'][ $key ] : '';

	return $single ? $value : array( $value );
}

final class WP_Term {
	public $name;

	public function __construct( string $name ) {
		$this->name = $name;
	}
}

function get_the_terms( int $post_id, string $taxonomy ) {
	$fixture = hea_lth_preview_fixture();
	$terms   = isset( $fixture['terms'][ $taxonomy ] ) && is_array( $fixture['terms'][ $taxonomy ] ) ? $fixture['terms'][ $taxonomy ] : array();

	return array_map(
		static function ( $name ) {
			return new WP_Term( (string) $name );
		},
		$terms
	);
}

function is_wp_error( $value ): bool {
	return false;
}

function sanitize_text_field( $value ): string {
	return trim( strip_tags( (string) $value ) );
}

function wpautop( string $text ): string {
	return '<p>' . $text . '</p>';
}

function status_header( int $code ): void {
}

function nocache_headers(): void {
}

function get_template_part( string $slug, ?string $name = null ): void {
	$path = hea_lth_preview_theme_directory() . '/' . $slug . ( null !== $name ? '-' . $name : '' ) . '.php';

	if ( is_file( $path ) ) {
		require $path;
	}
}

function is_user_logged_in(): bool {
	return false;
}

function wp_login_url( string $redirect = '' ): string {
	return 'http://127.0.0.1:8787/wp-login.php?redirect_to=' . rawurlencode( $redirect );
}

function wp_reset_postdata(): void {
}

function apply_filters( string $hook, $value ) {
	return $value;
}

class WP_Query {
	/**
	 * The local visual harness intentionally has no editorial records.
	 *
	 * @param array $args Query parameters.
	 */
	public function __construct( array $args = array() ) {
	}

	public function have_posts(): bool {
		return false;
	}

	public function the_post(): void {
	}
}

require_once hea_lth_preview_theme_directory() . '/inc/portal-route-registry.php';
require_once hea_lth_preview_theme_directory() . '/inc/portal-template-helpers.php';
require hea_lth_preview_theme_directory() . '/' . $hea_lth_preview_pages[ $hea_lth_preview_page ]['file'];
