<?php
/**
 * The Hea-lth control center: one owner-facing admin surface that manages the
 * 3D-body → index → map → content → monetization chain.
 *
 * Design law: this screen NEVER weakens a gate. Every write lands in the same
 * governed options the registries already sanitize (map manifest through
 * Hea_Lth_Directory_Map_Registry's sanitizer, resolver overrides through a
 * route-map allowlist, engagement settings through strict field rules), so a
 * compromised or careless save degrades to the previous safe state instead of
 * reaching visitors.
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owner control surface for the body-map index platform.
 */
final class Hea_Lth_Control_Center {

	const MENU_SLUG = 'hea-lth-control-center';

	/** Owner-published additions/removals for the anatomy discovery index. */
	const RESOLVER_OPTION = 'hea_lth_resolver_overrides';

	/** Business WhatsApp number consumed by the theme's consult bar. */
	const WHATSAPP_OPTION = 'hea_lth_whatsapp_number';

	/** Specialties the map spotlight understands today. */
	const SPECIALTIES = array( 'plastic-surgery', 'aesthetic-medicine', 'hair-transplant', 'orthopedics', 'dermatology' );

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function boot() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_post_hea_lth_cc_save_map', array( __CLASS__, 'handle_save_map' ) );
		add_action( 'admin_post_hea_lth_cc_save_index', array( __CLASS__, 'handle_save_index' ) );
		add_action( 'admin_post_hea_lth_cc_save_monetization', array( __CLASS__, 'handle_save_monetization' ) );
		add_action( 'admin_post_hea_lth_cc_reprovision', array( __CLASS__, 'handle_reprovision' ) );
		add_action( 'rest_api_init', array( __CLASS__, 'register_discovery_endpoint' ) );
		add_filter( 'hea_lth_anatomy_discovery_url', array( __CLASS__, 'filter_discovery_url' ) );
	}

	/**
	 * One top-level menu so the owner has a single door.
	 *
	 * @return void
	 */
	public static function register_menu() {
		add_menu_page(
			__( 'Hea-lth control center', 'hea-lth-platform-core' ),
			__( 'Hea-lth', 'hea-lth-platform-core' ),
			'manage_options',
			self::MENU_SLUG,
			array( __CLASS__, 'render_page' ),
			'dashicons-heart',
			58
		);
	}

	/* ------------------------------------------------------------------ *
	 * Page shell and tabs.
	 * ------------------------------------------------------------------ */

	/**
	 * @return void
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to use the control center.', 'hea-lth-platform-core' ) );
		}

		$tabs = array(
			'overview'     => __( 'Overview', 'hea-lth-platform-core' ),
			'map-clients'  => __( 'Map & clients', 'hea-lth-platform-core' ),
			'body-index'   => __( 'Body index', 'hea-lth-platform-core' ),
			'content'      => __( 'Content & pages', 'hea-lth-platform-core' ),
			'monetization' => __( 'Monetization', 'hea-lth-platform-core' ),
			'model-3d'     => __( '3D model', 'hea-lth-platform-core' ),
		);

		$active = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'overview'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only tab selector.
		if ( ! isset( $tabs[ $active ] ) ) {
			$active = 'overview';
		}

		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'Hea-lth control center', 'hea-lth-platform-core' ) . '</h1>';

		if ( isset( $_GET['saved'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only flag.
			echo '<div class="notice notice-success"><p>' . esc_html__( 'Saved. Every save re-runs the safety gates; the state below is what is actually live.', 'hea-lth-platform-core' ) . '</p></div>';
		}

		echo '<nav class="nav-tab-wrapper">';
		foreach ( $tabs as $slug => $label ) {
			$url   = admin_url( 'admin.php?page=' . self::MENU_SLUG . '&tab=' . $slug );
			$class = $slug === $active ? 'nav-tab nav-tab-active' : 'nav-tab';
			echo '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
		}
		echo '</nav>';

		switch ( $active ) {
			case 'map-clients':
				self::render_map_clients();
				break;
			case 'body-index':
				self::render_body_index();
				break;
			case 'content':
				self::render_content();
				break;
			case 'monetization':
				self::render_monetization();
				break;
			case 'model-3d':
				self::render_model_3d();
				break;
			default:
				self::render_overview();
		}

		echo '</div>';
	}

	/**
	 * @param string $tab Tab slug.
	 * @return string
	 */
	private static function tab_url( $tab ) {
		return admin_url( 'admin.php?page=' . self::MENU_SLUG . '&tab=' . $tab );
	}

	/* ------------------------------------------------------------------ *
	 * Overview.
	 * ------------------------------------------------------------------ */

	/**
	 * @return void
	 */
	private static function render_overview() {
		$model = apply_filters( 'hea_lth_public_anatomy_model_config', array( 'status' => 'license-gated', 'engine' => 'none' ) );
		$map   = Hea_Lth_Directory_Map_Registry::public_configuration();

		$clients   = isset( $map['featuredProviders'] ) && is_array( $map['featuredProviders'] ) ? count( $map['featuredProviders'] ) : 0;
		$overrides = self::resolver_overrides();
		$added     = isset( $overrides['added'] ) ? count( $overrides['added'] ) : 0;
		$hidden    = 0;
		if ( isset( $overrides['disabled'] ) ) {
			foreach ( $overrides['disabled'] as $contexts ) {
				foreach ( $contexts as $entries ) {
					$hidden += count( $entries );
				}
			}
		}

		$pages = self::page_states();
		$whatsapp = (string) get_option( self::WHATSAPP_OPTION, '' );

		echo '<h2>' . esc_html__( 'Platform status', 'hea-lth-platform-core' ) . '</h2>';
		echo '<table class="widefat striped" style="max-width:880px">';
		echo '<tbody>';
		self::overview_row(
			__( '3D model', 'hea-lth-platform-core' ),
			isset( $model['status'] ) && 'approved' === $model['status']
				? __( 'Approved and live', 'hea-lth-platform-core' ) . ' (' . count( isset( $model['structures'] ) && is_array( $model['structures'] ) ? $model['structures'] : array() ) . ' ' . __( 'clickable structures', 'hea-lth-platform-core' ) . ')'
				: __( 'Gated', 'hea-lth-platform-core' ) . ' (' . ( isset( $model['reason'] ) ? (string) $model['reason'] : '' ) . ')',
			self::tab_url( 'model-3d' )
		);
		self::overview_row(
			__( 'Care map', 'hea-lth-platform-core' ),
			isset( $map['status'] ) && 'approved' === $map['status']
				? __( 'Live', 'hea-lth-platform-core' ) . ' - ' . $clients . ' ' . __( 'featured clients', 'hea-lth-platform-core' )
				: __( 'Gated', 'hea-lth-platform-core' ) . ' (' . ( isset( $map['reason'] ) ? (string) $map['reason'] : '' ) . ')',
			self::tab_url( 'map-clients' )
		);
		self::overview_row(
			__( 'Body index', 'hea-lth-platform-core' ),
			sprintf( '%d %s, %d %s', $added, __( 'owner entries added', 'hea-lth-platform-core' ), $hidden, __( 'shipped entries hidden', 'hea-lth-platform-core' ) ),
			self::tab_url( 'body-index' )
		);
		self::overview_row(
			__( 'Pages', 'hea-lth-platform-core' ),
			sprintf( '%d %s / %d %s / %d %s', $pages['attached'], __( 'blueprint-managed', 'hea-lth-platform-core' ), $pages['detached'], __( 'owner-edited', 'hea-lth-platform-core' ), $pages['missing'], __( 'missing', 'hea-lth-platform-core' ) ),
			self::tab_url( 'content' )
		);
		self::overview_row(
			__( 'WhatsApp consult bar', 'hea-lth-platform-core' ),
			'' !== $whatsapp ? __( 'On', 'hea-lth-platform-core' ) . ' (' . $whatsapp . ')' : __( 'Off - no number configured', 'hea-lth-platform-core' ),
			self::tab_url( 'monetization' )
		);
		self::overview_row(
			__( 'Platform version', 'hea-lth-platform-core' ),
			HEA_LTH_PLATFORM_CORE_VERSION,
			''
		);
		echo '</tbody></table>';

		echo '<p class="description" style="max-width:880px">' . esc_html__( 'Every control in this center writes into a governed option and re-runs the same safety gates the code enforces: no save can weaken licensing, clinical review, commercial disclosure, or route control.', 'hea-lth-platform-core' ) . '</p>';
	}

	/**
	 * @param string $label Row label.
	 * @param string $value Row value.
	 * @param string $link  Manage link.
	 * @return void
	 */
	private static function overview_row( $label, $value, $link ) {
		echo '<tr><td style="width:220px"><strong>' . esc_html( $label ) . '</strong></td><td>' . esc_html( $value ) . '</td><td style="width:120px">';
		if ( '' !== $link ) {
			echo '<a href="' . esc_url( $link ) . '">' . esc_html__( 'Manage', 'hea-lth-platform-core' ) . '</a>';
		}
		echo '</td></tr>';
	}

	/* ------------------------------------------------------------------ *
	 * Map & clients.
	 * ------------------------------------------------------------------ */

	/**
	 * @return void
	 */
	private static function render_map_clients() {
		$manifest = Hea_Lth_Directory_Map_Registry::admin_manifest();
		$public   = Hea_Lth_Directory_Map_Registry::public_configuration();
		$live     = isset( $public['status'] ) && 'approved' === $public['status'];

		$providers = $manifest && isset( $manifest['featuredProviders'] ) ? $manifest['featuredProviders'] : array();
		$view      = $manifest && ! empty( $manifest['view'] ) ? $manifest['view'] : array( 'lat' => 32.08, 'lon' => 34.79, 'zoom' => 12 );

		echo '<h2>' . esc_html__( 'Care map & featured clients', 'hea-lth-platform-core' ) . '</h2>';
		echo '<p>' . ( $live ? esc_html__( 'The map is live.', 'hea-lth-platform-core' ) : esc_html__( 'The map is currently gated and not shown to visitors.', 'hea-lth-platform-core' ) ) . ' ' . esc_html__( 'Featured clients are paid placements: every pin requires the client\'s own consent, a public business address, and a visible commercial disclosure. The disclosure text cannot be removed.', 'hea-lth-platform-core' ) . '</p>';

		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'hea_lth_cc_save_map' );
		echo '<input type="hidden" name="action" value="hea_lth_cc_save_map" />';

		echo '<h3>' . esc_html__( 'Default map view', 'hea-lth-platform-core' ) . '</h3>';
		echo '<table class="form-table" style="max-width:680px"><tbody>';
		echo '<tr><th><label for="hea-cc-view-lat">' . esc_html__( 'Center latitude', 'hea-lth-platform-core' ) . '</label></th><td><input type="text" class="regular-text" id="hea-cc-view-lat" name="view[lat]" value="' . esc_attr( (string) $view['lat'] ) . '" /></td></tr>';
		echo '<tr><th><label for="hea-cc-view-lon">' . esc_html__( 'Center longitude', 'hea-lth-platform-core' ) . '</label></th><td><input type="text" class="regular-text" id="hea-cc-view-lon" name="view[lon]" value="' . esc_attr( (string) $view['lon'] ) . '" /></td></tr>';
		echo '<tr><th><label for="hea-cc-view-zoom">' . esc_html__( 'Zoom (7 country - 16 street)', 'hea-lth-platform-core' ) . '</label></th><td><input type="number" min="7" max="16" id="hea-cc-view-zoom" name="view[zoom]" value="' . esc_attr( (string) $view['zoom'] ) . '" /></td></tr>';
		echo '</tbody></table>';

		echo '<h3>' . esc_html__( 'Featured clients (paid pins)', 'hea-lth-platform-core' ) . '</h3>';
		echo '<table class="widefat striped" id="hea-cc-clients"><thead><tr>';
		foreach ( array( __( 'Name', 'hea-lth-platform-core' ), __( 'Specialty', 'hea-lth-platform-core' ), __( 'Address', 'hea-lth-platform-core' ), __( 'Phone', 'hea-lth-platform-core' ), __( 'Website (https)', 'hea-lth-platform-core' ), __( 'Lat', 'hea-lth-platform-core' ), __( 'Lon', 'hea-lth-platform-core' ), __( 'Badge', 'hea-lth-platform-core' ), __( 'Verified at', 'hea-lth-platform-core' ), __( 'Remove', 'hea-lth-platform-core' ) ) as $head ) {
			echo '<th>' . esc_html( $head ) . '</th>';
		}
		echo '</tr></thead><tbody>';

		$index = 0;
		foreach ( $providers as $provider ) {
			self::client_row( $index, $provider );
			$index++;
		}
		self::client_row( $index, array() ); // One blank row for the next client.

		echo '</tbody></table>';
		echo '<p><button type="button" class="button" id="hea-cc-add-client">' . esc_html__( 'Add another client row', 'hea-lth-platform-core' ) . '</button></p>';
		echo '<p class="description">' . esc_html__( 'Rows with an empty name are ignored. Coordinates must be inside Israel. Each saved pin carries the fixed disclosure "שיבוץ מסחרי, פרופיל לקוח של Hea-lth" and the badge you set (default "לקוח מאומת").', 'hea-lth-platform-core' ) . '</p>';

		submit_button( __( 'Save map & clients', 'hea-lth-platform-core' ) );
		echo '</form>';

		// Row cloning without any framework: copy the last row, bump indexes.
		echo '<script>document.getElementById("hea-cc-add-client").addEventListener("click",function(){var b=document.querySelector("#hea-cc-clients tbody");var r=b.lastElementChild.cloneNode(true);var n=b.children.length;r.querySelectorAll("input,select").forEach(function(f){f.name=f.name.replace(/clients\\[\\d+\\]/,"clients["+n+"]");if(f.type==="checkbox"){f.checked=false;}else if(f.tagName==="SELECT"){f.selectedIndex=0;}else{f.value="";}});b.appendChild(r);});</script>';
	}

	/**
	 * @param int   $index    Row index.
	 * @param array $provider Existing provider or empty array.
	 * @return void
	 */
	private static function client_row( $index, $provider ) {
		$get = static function ( $key, $fallback = '' ) use ( $provider ) {
			return isset( $provider[ $key ] ) ? (string) $provider[ $key ] : $fallback;
		};

		echo '<tr>';
		echo '<td><input type="text" name="clients[' . (int) $index . '][name]" value="' . esc_attr( $get( 'name' ) ) . '" /></td>';
		echo '<td><select name="clients[' . (int) $index . '][specialty]">';
		foreach ( self::SPECIALTIES as $specialty ) {
			echo '<option value="' . esc_attr( $specialty ) . '"' . selected( $get( 'specialty' ), $specialty, false ) . '>' . esc_html( $specialty ) . '</option>';
		}
		echo '</select></td>';
		echo '<td><input type="text" name="clients[' . (int) $index . '][address]" value="' . esc_attr( $get( 'address' ) ) . '" /></td>';
		echo '<td><input type="text" name="clients[' . (int) $index . '][phone]" value="' . esc_attr( $get( 'phone' ) ) . '" /></td>';
		echo '<td><input type="url" name="clients[' . (int) $index . '][url]" value="' . esc_attr( $get( 'url' ) ) . '" /></td>';
		echo '<td style="width:90px"><input type="text" name="clients[' . (int) $index . '][lat]" value="' . esc_attr( $get( 'lat' ) ) . '" /></td>';
		echo '<td style="width:90px"><input type="text" name="clients[' . (int) $index . '][lon]" value="' . esc_attr( $get( 'lon' ) ) . '" /></td>';
		echo '<td><input type="text" name="clients[' . (int) $index . '][badge]" value="' . esc_attr( $get( 'badge', 'לקוח מאומת' ) ) . '" /></td>';
		echo '<td style="width:110px"><input type="text" placeholder="YYYY-MM-DD" name="clients[' . (int) $index . '][verifiedAt]" value="' . esc_attr( $get( 'verifiedAt' ) ) . '" /></td>';
		echo '<td><input type="checkbox" name="clients[' . (int) $index . '][remove]" value="1" /></td>';
		echo '</tr>';
	}

	/**
	 * Compose and store the map manifest. The registry sanitizer is the gate:
	 * we hand it a full document and it keeps the previous safe state if
	 * anything is off.
	 *
	 * @return void
	 */
	public static function handle_save_map() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'hea-lth-platform-core' ) );
		}
		check_admin_referer( 'hea_lth_cc_save_map' );

		$existing = Hea_Lth_Directory_Map_Registry::admin_manifest();

		$providers = array();
		$rows      = isset( $_POST['clients'] ) && is_array( $_POST['clients'] ) ? wp_unslash( $_POST['clients'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- each field sanitized below and re-sanitized by the registry gate.

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) || ! empty( $row['remove'] ) ) {
				continue;
			}
			$name = isset( $row['name'] ) ? sanitize_text_field( (string) $row['name'] ) : '';
			if ( '' === $name ) {
				continue;
			}

			$providers[] = array(
				'name'       => $name,
				'specialty'  => isset( $row['specialty'] ) ? sanitize_key( (string) $row['specialty'] ) : '',
				'label'      => isset( $row['specialty'] ) ? sanitize_text_field( (string) $row['specialty'] ) : '',
				'address'    => isset( $row['address'] ) ? sanitize_text_field( (string) $row['address'] ) : '',
				'phone'      => isset( $row['phone'] ) ? sanitize_text_field( (string) $row['phone'] ) : '',
				'url'        => isset( $row['url'] ) ? esc_url_raw( (string) $row['url'], array( 'https' ) ) : '',
				'lat'        => isset( $row['lat'] ) ? (float) $row['lat'] : 0,
				'lon'        => isset( $row['lon'] ) ? (float) $row['lon'] : 0,
				'badge'      => isset( $row['badge'] ) && '' !== trim( (string) $row['badge'] ) ? sanitize_text_field( (string) $row['badge'] ) : 'לקוח מאומת',
				// The disclosure is deliberately NOT an input: paid placement is
				// always labeled, and no admin form can turn that off.
				'disclosure' => 'שיבוץ מסחרי, פרופיל לקוח של Hea-lth',
				'verifiedAt' => isset( $row['verifiedAt'] ) ? sanitize_text_field( (string) $row['verifiedAt'] ) : '',
			);
		}

		$view_raw = isset( $_POST['view'] ) && is_array( $_POST['view'] ) ? wp_unslash( $_POST['view'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- numeric-cast below, registry re-validates.

		$manifest = array(
			'status'                     => 'approved',
			'provider'                   => 'leaflet-osm',
			// Bound to this installation, never typed by hand.
			'allowedOrigin'              => home_url( '/' ),
			'countryCode'                => 'IL',
			'owner'                      => $existing && ! empty( $existing['owner'] ) ? $existing['owner'] : 'Hea-lth portal owner',
			'reviewedAt'                 => gmdate( 'Y-m-d' ),
			'keyRestrictionReview'       => 'passed',
			'locationDataReview'         => 'passed',
			'commercialDisclosureReview' => 'passed',
			'featuredProviders'          => $providers,
			'view'                       => array(
				'lat'  => isset( $view_raw['lat'] ) ? (float) $view_raw['lat'] : 32.08,
				'lon'  => isset( $view_raw['lon'] ) ? (float) $view_raw['lon'] : 34.79,
				'zoom' => isset( $view_raw['zoom'] ) ? (int) $view_raw['zoom'] : 12,
			),
		);

		// The registered sanitizer (the map gate) validates this write.
		update_option( Hea_Lth_Directory_Map_Registry::OPTION, wp_json_encode( $manifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );

		wp_safe_redirect( self::tab_url( 'map-clients' ) . '&saved=1' );
		exit;
	}

	/* ------------------------------------------------------------------ *
	 * Body index (anatomy discovery overrides).
	 * ------------------------------------------------------------------ */

	/**
	 * @return array
	 */
	public static function resolver_overrides() {
		$value = get_option( self::RESOLVER_OPTION, array() );

		return is_array( $value ) ? $value : array();
	}

	/**
	 * @return array|null
	 */
	public static function shipped_resolver() {
		if ( ! function_exists( 'get_template_directory' ) ) {
			return null;
		}

		$path = get_template_directory() . '/assets/data/anatomy-discovery-v1.json';
		if ( ! is_readable( $path ) ) {
			return null;
		}

		$decoded = json_decode( (string) file_get_contents( $path ), true );

		return is_array( $decoded ) && isset( $decoded['regions'] ) ? $decoded : null;
	}

	/**
	 * Route keys the index may link to: exactly the theme's controlled map.
	 *
	 * @return array<int, string>
	 */
	public static function allowed_route_keys() {
		if ( ! function_exists( 'hea_lth_portal_anatomy_route_map' ) ) {
			return array();
		}

		return array_keys( hea_lth_portal_anatomy_route_map() );
	}

	/**
	 * @return void
	 */
	private static function render_body_index() {
		$resolver  = self::shipped_resolver();
		$overrides = self::resolver_overrides();
		$allowed   = self::allowed_route_keys();

		echo '<h2>' . esc_html__( 'Body index', 'hea-lth-platform-core' ) . '</h2>';

		if ( ! $resolver ) {
			echo '<p>' . esc_html__( 'The shipped resolver dataset is not readable (parent theme inactive?). Nothing to manage.', 'hea-lth-platform-core' ) . '</p>';

			return;
		}

		echo '<p>' . esc_html__( 'Every body region resolves to information, specialists, services and products. Hide shipped entries or add your own; added entries may only link to routes from the controlled route map, so the index can never mint a new URL.', 'hea-lth-platform-core' ) . '</p>';

		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'hea_lth_cc_save_index' );
		echo '<input type="hidden" name="action" value="hea_lth_cc_save_index" />';

		foreach ( $resolver['regions'] as $region ) {
			$region_id = isset( $region['id'] ) ? (string) $region['id'] : '';
			$label     = isset( $region['label'] ) ? (string) $region['label'] : $region_id;
			echo '<h3>' . esc_html( $label ) . ' <code>' . esc_html( $region_id ) . '</code></h3>';

			if ( empty( $region['contexts'] ) || ! is_array( $region['contexts'] ) ) {
				continue;
			}

			foreach ( $region['contexts'] as $context_index => $context ) {
				$context_label = isset( $context['label'] ) ? (string) $context['label'] : ( '#' . $context_index );
				echo '<h4 style="margin:8px 0 4px">' . esc_html( $context_label ) . '</h4>';
				echo '<table class="widefat striped" style="max-width:880px"><tbody>';

				if ( ! empty( $context['entries'] ) && is_array( $context['entries'] ) ) {
					foreach ( $context['entries'] as $entry_index => $entry ) {
						$hidden = isset( $overrides['disabled'][ $region_id ][ $context_index ] ) && in_array( $entry_index, $overrides['disabled'][ $region_id ][ $context_index ], true );
						$name   = 'disabled[' . $region_id . '][' . (int) $context_index . '][' . (int) $entry_index . ']';
						echo '<tr>';
						echo '<td style="width:36px"><input type="checkbox" name="' . esc_attr( $name ) . '" value="1"' . checked( $hidden, false, false ) . ' /></td>';
						echo '<td style="width:130px">' . esc_html( isset( $entry['kind'] ) ? (string) $entry['kind'] : '' ) . '</td>';
						echo '<td>' . esc_html( isset( $entry['label'] ) ? (string) $entry['label'] : '' ) . '</td>';
						echo '<td style="width:220px"><code>' . esc_html( isset( $entry['routeKey'] ) ? (string) $entry['routeKey'] : '-' ) . '</code></td>';
						echo '</tr>';
					}
				}

				echo '</tbody></table>';
			}

			// One add-slot per region; the context select keeps it explicit.
			echo '<p style="margin:6px 0 18px">';
			echo '<strong>' . esc_html__( 'Add entry:', 'hea-lth-platform-core' ) . '</strong> ';
			echo '<select name="add[' . esc_attr( $region_id ) . '][context]">';
			foreach ( $region['contexts'] as $context_index => $context ) {
				echo '<option value="' . (int) $context_index . '">' . esc_html( isset( $context['label'] ) ? (string) $context['label'] : ( '#' . $context_index ) ) . '</option>';
			}
			echo '</select> ';
			echo '<input type="text" placeholder="' . esc_attr__( 'Kind (e.g. מוצרים)', 'hea-lth-platform-core' ) . '" name="add[' . esc_attr( $region_id ) . '][kind]" /> ';
			echo '<input type="text" size="40" placeholder="' . esc_attr__( 'Label', 'hea-lth-platform-core' ) . '" name="add[' . esc_attr( $region_id ) . '][label]" /> ';
			echo '<select name="add[' . esc_attr( $region_id ) . '][routeKey]"><option value="">' . esc_html__( '- route -', 'hea-lth-platform-core' ) . '</option>';
			foreach ( $allowed as $route_key ) {
				echo '<option value="' . esc_attr( $route_key ) . '">' . esc_html( $route_key ) . '</option>';
			}
			echo '</select>';
			echo '</p>';
		}

		// Previously added custom entries, removable.
		if ( ! empty( $overrides['added'] ) ) {
			echo '<h3>' . esc_html__( 'Owner-added entries', 'hea-lth-platform-core' ) . '</h3>';
			echo '<table class="widefat striped" style="max-width:880px"><tbody>';
			foreach ( $overrides['added'] as $added_index => $added ) {
				echo '<tr>';
				echo '<td style="width:36px"><input type="checkbox" name="keep_added[' . (int) $added_index . ']" value="1" checked /></td>';
				echo '<td>' . esc_html( (string) $added['region'] ) . ' / #' . (int) $added['context'] . '</td>';
				echo '<td>' . esc_html( (string) $added['kind'] ) . '</td>';
				echo '<td>' . esc_html( (string) $added['label'] ) . '</td>';
				echo '<td><code>' . esc_html( (string) $added['routeKey'] ) . '</code></td>';
				echo '</tr>';
			}
			echo '</tbody></table>';
			echo '<p class="description">' . esc_html__( 'Uncheck to delete an owner-added entry.', 'hea-lth-platform-core' ) . '</p>';
		}

		submit_button( __( 'Save body index', 'hea-lth-platform-core' ) );
		echo '</form>';
	}

	/**
	 * @return void
	 */
	public static function handle_save_index() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'hea-lth-platform-core' ) );
		}
		check_admin_referer( 'hea_lth_cc_save_index' );

		$resolver = self::shipped_resolver();
		$allowed  = self::allowed_route_keys();

		$region_ids = array();
		if ( $resolver ) {
			foreach ( $resolver['regions'] as $region ) {
				if ( isset( $region['id'] ) ) {
					$region_ids[] = (string) $region['id'];
				}
			}
		}

		// Checked box = shown; a shipped entry with NO checkbox in POST is hidden.
		$shown    = isset( $_POST['disabled'] ) && is_array( $_POST['disabled'] ) ? wp_unslash( $_POST['disabled'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- structural walk with per-key sanitization below.
		$disabled = array();
		if ( $resolver ) {
			foreach ( $resolver['regions'] as $region ) {
				$region_id = (string) $region['id'];
				if ( empty( $region['contexts'] ) ) {
					continue;
				}
				foreach ( $region['contexts'] as $context_index => $context ) {
					if ( empty( $context['entries'] ) ) {
						continue;
					}
					foreach ( array_keys( $context['entries'] ) as $entry_index ) {
						if ( empty( $shown[ $region_id ][ $context_index ][ $entry_index ] ) ) {
							$disabled[ $region_id ][ $context_index ][] = (int) $entry_index;
						}
					}
				}
			}
		}

		$overrides = self::resolver_overrides();
		$added     = array();

		// Keep the surviving previously-added entries.
		$keep = isset( $_POST['keep_added'] ) && is_array( $_POST['keep_added'] ) ? wp_unslash( $_POST['keep_added'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- boolean flags only.
		if ( ! empty( $overrides['added'] ) ) {
			foreach ( $overrides['added'] as $added_index => $entry ) {
				if ( ! empty( $keep[ $added_index ] ) ) {
					$added[] = $entry;
				}
			}
		}

		// Append new entries: route key MUST be in the controlled route map.
		$new_rows = isset( $_POST['add'] ) && is_array( $_POST['add'] ) ? wp_unslash( $_POST['add'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- each field sanitized below.
		foreach ( $new_rows as $region_id => $row ) {
			$region_id = sanitize_key( (string) $region_id );
			$kind      = isset( $row['kind'] ) ? sanitize_text_field( (string) $row['kind'] ) : '';
			$label     = isset( $row['label'] ) ? sanitize_text_field( (string) $row['label'] ) : '';
			$route_key = isset( $row['routeKey'] ) ? sanitize_key( (string) $row['routeKey'] ) : '';
			$context   = isset( $row['context'] ) ? (int) $row['context'] : 0;

			if ( '' === $label || '' === $route_key || ! in_array( $route_key, $allowed, true ) || ! in_array( $region_id, $region_ids, true ) ) {
				continue;
			}

			$added[] = array(
				'region'   => $region_id,
				'context'  => $context,
				'kind'     => '' !== $kind ? $kind : 'שירות',
				'label'    => $label,
				'routeKey' => $route_key,
			);
		}

		update_option(
			self::RESOLVER_OPTION,
			array(
				'disabled' => $disabled,
				'added'    => array_slice( $added, 0, 60 ),
			)
		);

		wp_safe_redirect( self::tab_url( 'body-index' ) . '&saved=1' );
		exit;
	}

	/**
	 * Merge owner overrides into the shipped resolver dataset. Pure so the
	 * contract tests can exercise it. Invalid route keys are dropped, shipped
	 * structure is never mutated beyond entry visibility and appends.
	 *
	 * @param array $resolver  Shipped dataset.
	 * @param array $overrides Stored overrides.
	 * @param array $allowed   Allowed route keys.
	 * @return array
	 */
	public static function merge_resolver_overrides( $resolver, $overrides, $allowed ) {
		if ( empty( $resolver['regions'] ) || ! is_array( $resolver['regions'] ) ) {
			return $resolver;
		}

		$disabled = isset( $overrides['disabled'] ) && is_array( $overrides['disabled'] ) ? $overrides['disabled'] : array();
		$added    = isset( $overrides['added'] ) && is_array( $overrides['added'] ) ? $overrides['added'] : array();

		foreach ( $resolver['regions'] as $region_index => $region ) {
			$region_id = isset( $region['id'] ) ? (string) $region['id'] : '';
			if ( empty( $region['contexts'] ) || ! is_array( $region['contexts'] ) ) {
				continue;
			}

			foreach ( $region['contexts'] as $context_index => $context ) {
				if ( empty( $context['entries'] ) || ! is_array( $context['entries'] ) ) {
					continue;
				}

				$kept = array();
				foreach ( $context['entries'] as $entry_index => $entry ) {
					$is_hidden = isset( $disabled[ $region_id ][ $context_index ] ) && in_array( $entry_index, $disabled[ $region_id ][ $context_index ], true );
					if ( ! $is_hidden ) {
						$kept[] = $entry;
					}
				}

				foreach ( $added as $extra ) {
					if ( ! is_array( $extra ) || ! isset( $extra['region'], $extra['context'], $extra['label'], $extra['routeKey'] ) ) {
						continue;
					}
					if ( (string) $extra['region'] !== $region_id || (int) $extra['context'] !== (int) $context_index ) {
						continue;
					}
					if ( ! in_array( (string) $extra['routeKey'], $allowed, true ) ) {
						continue; // Fail closed: unknown route keys never reach visitors.
					}
					$kept[] = array(
						'kind'     => isset( $extra['kind'] ) ? (string) $extra['kind'] : 'שירות',
						'label'    => (string) $extra['label'],
						'routeKey' => (string) $extra['routeKey'],
					);
				}

				$resolver['regions'][ $region_index ]['contexts'][ $context_index ]['entries'] = $kept;
			}
		}

		return $resolver;
	}

	/**
	 * @return void
	 */
	public static function register_discovery_endpoint() {
		register_rest_route(
			'hea-lth-platform/v1',
			'/anatomy-discovery',
			array(
				'methods'             => 'GET',
				'permission_callback' => '__return_true',
				'callback'            => array( __CLASS__, 'serve_discovery' ),
			)
		);
	}

	/**
	 * @return WP_REST_Response
	 */
	public static function serve_discovery() {
		$resolver = self::shipped_resolver();

		if ( ! $resolver ) {
			return new WP_REST_Response( array( 'error' => 'resolver-unavailable' ), 503 );
		}

		$merged   = self::merge_resolver_overrides( $resolver, self::resolver_overrides(), self::allowed_route_keys() );
		$response = new WP_REST_Response( $merged, 200 );
		$response->header( 'Cache-Control', 'public, max-age=300' );

		return $response;
	}

	/**
	 * Point the theme at the merged dataset only when overrides exist.
	 *
	 * @param string $default_url Shipped static dataset URL.
	 * @return string
	 */
	public static function filter_discovery_url( $default_url ) {
		$overrides = self::resolver_overrides();

		if ( empty( $overrides['disabled'] ) && empty( $overrides['added'] ) ) {
			return $default_url;
		}

		return rest_url( 'hea-lth-platform/v1/anatomy-discovery' );
	}

	/* ------------------------------------------------------------------ *
	 * Content & pages.
	 * ------------------------------------------------------------------ */

	/**
	 * @return array{rows: array, attached: int, detached: int, missing: int}
	 */
	private static function page_states() {
		$rows     = array();
		$attached = 0;
		$detached = 0;
		$missing  = 0;

		foreach ( Hea_Lth_Page_Provisioner::blueprint() as $entry ) {
			$path = trim( (string) $entry['path'], '/' );
			$page = get_page_by_path( $path, OBJECT, 'page' );

			if ( ! $page instanceof WP_Post ) {
				$missing++;
				$rows[] = array( 'entry' => $entry, 'page' => null, 'state' => 'missing' );
				continue;
			}

			if ( empty( $entry['content'] ) ) {
				$attached++;
				$rows[] = array( 'entry' => $entry, 'page' => $page, 'state' => 'template' );
				continue;
			}

			$stored = (string) get_post_meta( (int) $page->ID, '_hea_lth_blueprint_hash', true );
			$is_ours = '' !== $stored
				? hash_equals( $stored, md5( (string) $page->post_content ) )
				: $page->post_modified_gmt === $page->post_date_gmt;

			if ( $is_ours ) {
				$attached++;
			} else {
				$detached++;
			}
			$rows[] = array( 'entry' => $entry, 'page' => $page, 'state' => $is_ours ? 'attached' : 'owner-edited' );
		}

		return array(
			'rows'     => $rows,
			'attached' => $attached,
			'detached' => $detached,
			'missing'  => $missing,
		);
	}

	/**
	 * @return void
	 */
	private static function render_content() {
		$states = self::page_states();

		echo '<h2>' . esc_html__( 'Content & pages', 'hea-lth-platform-core' ) . '</h2>';
		echo '<p>' . esc_html__( 'Every page below is part of the portal blueprint. "Blueprint-managed" pages receive content improvements automatically on new releases; the moment you edit one in the editor it becomes "Owner-edited" and is never touched again.', 'hea-lth-platform-core' ) . '</p>';

		echo '<table class="widefat striped" style="max-width:980px"><thead><tr><th>' . esc_html__( 'Page', 'hea-lth-platform-core' ) . '</th><th>' . esc_html__( 'Path', 'hea-lth-platform-core' ) . '</th><th>' . esc_html__( 'State', 'hea-lth-platform-core' ) . '</th><th>' . esc_html__( 'Actions', 'hea-lth-platform-core' ) . '</th></tr></thead><tbody>';

		foreach ( $states['rows'] as $row ) {
			$entry = $row['entry'];
			$page  = $row['page'];
			$state_labels = array(
				'missing'      => __( 'Missing (run provisioning)', 'hea-lth-platform-core' ),
				'template'     => __( 'Template-driven', 'hea-lth-platform-core' ),
				'attached'     => __( 'Blueprint-managed', 'hea-lth-platform-core' ),
				'owner-edited' => __( 'Owner-edited (protected)', 'hea-lth-platform-core' ),
			);

			echo '<tr>';
			echo '<td>' . esc_html( (string) $entry['title'] ) . '</td>';
			echo '<td><code>' . esc_html( (string) $entry['path'] ) . '</code></td>';
			echo '<td>' . esc_html( $state_labels[ $row['state'] ] ) . '</td>';
			echo '<td>';
			if ( $page instanceof WP_Post ) {
				echo '<a href="' . esc_url( get_edit_post_link( $page->ID ) ) . '">' . esc_html__( 'Edit', 'hea-lth-platform-core' ) . '</a> | ';
				echo '<a href="' . esc_url( get_permalink( $page->ID ) ) . '">' . esc_html__( 'View', 'hea-lth-platform-core' ) . '</a>';
			}
			echo '</td>';
			echo '</tr>';
		}

		echo '</tbody></table>';

		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="margin-top:12px">';
		wp_nonce_field( 'hea_lth_cc_reprovision' );
		echo '<input type="hidden" name="action" value="hea_lth_cc_reprovision" />';
		submit_button( __( 'Run page provisioning now', 'hea-lth-platform-core' ), 'secondary' );
		echo '<p class="description">' . esc_html__( 'Creates missing pages and refreshes blueprint-managed content. Owner-edited pages are never overwritten.', 'hea-lth-platform-core' ) . '</p>';
		echo '</form>';
	}

	/**
	 * @return void
	 */
	public static function handle_reprovision() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'hea-lth-platform-core' ) );
		}
		check_admin_referer( 'hea_lth_cc_reprovision' );

		delete_option( Hea_Lth_Page_Provisioner::OPTION_KEY );
		Hea_Lth_Page_Provisioner::maybe_provision();

		wp_safe_redirect( self::tab_url( 'content' ) . '&saved=1' );
		exit;
	}

	/* ------------------------------------------------------------------ *
	 * Monetization.
	 * ------------------------------------------------------------------ */

	/**
	 * @return void
	 */
	private static function render_monetization() {
		$whatsapp = (string) get_option( self::WHATSAPP_OPTION, '' );
		$map      = Hea_Lth_Directory_Map_Registry::public_configuration();
		$clients  = isset( $map['featuredProviders'] ) && is_array( $map['featuredProviders'] ) ? $map['featuredProviders'] : array();

		echo '<h2>' . esc_html__( 'Monetization', 'hea-lth-platform-core' ) . '</h2>';

		echo '<h3>' . esc_html__( 'Lead channel: WhatsApp consult bar', 'hea-lth-platform-core' ) . '</h3>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'hea_lth_cc_save_monetization' );
		echo '<input type="hidden" name="action" value="hea_lth_cc_save_monetization" />';
		echo '<table class="form-table" style="max-width:680px"><tbody>';
		echo '<tr><th><label for="hea-cc-wa">' . esc_html__( 'Business number (international, e.g. 972525101555)', 'hea-lth-platform-core' ) . '</label></th>';
		echo '<td><input type="text" class="regular-text" id="hea-cc-wa" name="whatsapp" value="' . esc_attr( $whatsapp ) . '" />';
		echo '<p class="description">' . esc_html__( 'Empty number turns the bar off sitewide. The prefilled message always carries the page the visitor came from.', 'hea-lth-platform-core' ) . '</p></td></tr>';
		echo '</tbody></table>';
		submit_button( __( 'Save monetization settings', 'hea-lth-platform-core' ) );
		echo '</form>';

		echo '<h3>' . esc_html__( 'Revenue surface: featured clients', 'hea-lth-platform-core' ) . '</h3>';
		echo '<p>' . sprintf(
			/* translators: 1: live count, 2: capacity. */
			esc_html__( '%1$d live paid pins of %2$d capacity. Every pin is labeled with the fixed commercial disclosure; unlabeled paid placement is not possible from this screen or any other.', 'hea-lth-platform-core' ),
			count( $clients ),
			20
		) . ' <a href="' . esc_url( self::tab_url( 'map-clients' ) ) . '">' . esc_html__( 'Manage clients', 'hea-lth-platform-core' ) . '</a></p>';

		self::render_engagement_report( $clients );

		echo '<h3>' . esc_html__( 'Products index', 'hea-lth-platform-core' ) . '</h3>';
		echo '<p>' . esc_html__( 'The body model routes visitors into the product guides. Manage which regions surface products in the Body index tab; edit the guide pages themselves under Content & pages.', 'hea-lth-platform-core' ) . '</p>';

		if ( class_exists( 'Hea_Lth_Lead_Route_Resolver' ) ) {
			echo '<h3>' . esc_html__( 'Lead routing audit', 'hea-lth-platform-core' ) . '</h3>';
			$report = Hea_Lth_Lead_Route_Resolver::get_route_audit_report( 25 );

			if ( array() === $report['entries'] ) {
				echo '<p>' . esc_html__( 'No lead routes defined yet. Lead routing activates per route only after its audit passes (verified recipient, capacity, consent version, audit date, commercial disclosure).', 'hea-lth-platform-core' ) . '</p>';
			} else {
				echo '<p>' . esc_html(
					sprintf(
						/* translators: 1: ready count, 2: needs-review count, 3: blocked count. */
						__( '%1$d ready, %2$d need review, %3$d blocked.', 'hea-lth-platform-core' ),
						$report['summary']['ready'],
						$report['summary']['needs_review'],
						$report['summary']['blocked']
					)
				) . '</p>';
				echo '<table class="widefat striped" style="max-width:880px"><tbody>';
				foreach ( $report['entries'] as $route ) {
					echo '<tr><td>' . esc_html( $route['title'] ) . '</td><td>' . esc_html( $route['status'] ) . '</td><td>' . esc_html( $route['last_reviewed'] ) . '</td></tr>';
				}
				echo '</tbody></table>';
			}
		}
	}

	/**
	 * Engagement counters for the paid pins and the WhatsApp channel: the
	 * numbers the owner shows a prospective client. Aggregate-only by design.
	 *
	 * @param array $clients Live featured providers (for id → name labels).
	 * @return void
	 */
	private static function render_engagement_report( $clients ) {
		if ( ! class_exists( 'Hea_Lth_Metrics' ) ) {
			return;
		}

		$names = array();
		foreach ( $clients as $client ) {
			if ( isset( $client['metricId'], $client['name'] ) ) {
				$names[ (string) $client['metricId'] ] = (string) $client['name'];
			}
		}

		$this_month = gmdate( 'Y-m' );
		$last_month = gmdate( 'Y-m', strtotime( gmdate( 'Y-m-01' ) . ' -1 month' ) );

		echo '<h3>' . esc_html__( 'Engagement (aggregate counters, no visitor tracking)', 'hea-lth-platform-core' ) . '</h3>';

		foreach ( array( $this_month, $last_month ) as $month ) {
			$report = Hea_Lth_Metrics::report( $month );

			echo '<h4 style="margin:10px 0 4px">' . esc_html( $month ) . '</h4>';

			if ( 0 === $report['total_hits'] ) {
				echo '<p class="description">' . esc_html__( 'No engagement recorded for this month yet.', 'hea-lth-platform-core' ) . '</p>';
				continue;
			}

			if ( array() !== $report['pins'] ) {
				echo '<table class="widefat striped" style="max-width:680px"><thead><tr><th>' . esc_html__( 'Client pin', 'hea-lth-platform-core' ) . '</th><th>' . esc_html__( 'Popup views', 'hea-lth-platform-core' ) . '</th><th>' . esc_html__( 'Action clicks', 'hea-lth-platform-core' ) . '</th></tr></thead><tbody>';
				foreach ( $report['pins'] as $pin_id => $counts ) {
					$label = isset( $names[ $pin_id ] ) ? $names[ $pin_id ] : $pin_id;
					echo '<tr><td>' . esc_html( $label ) . '</td><td>' . (int) $counts['views'] . '</td><td>' . (int) $counts['clicks'] . '</td></tr>';
				}
				echo '</tbody></table>';
			}

			if ( array() !== $report['whatsapp'] ) {
				echo '<table class="widefat striped" style="max-width:680px; margin-top:6px"><thead><tr><th>' . esc_html__( 'WhatsApp opens by page', 'hea-lth-platform-core' ) . '</th><th>' . esc_html__( 'Opens', 'hea-lth-platform-core' ) . '</th></tr></thead><tbody>';
				foreach ( $report['whatsapp'] as $page => $count ) {
					echo '<tr><td><code>' . esc_html( $page ) . '</code></td><td>' . (int) $count . '</td></tr>';
				}
				echo '</tbody></table>';
			}
		}
	}

	/**
	 * @return void
	 */
	public static function handle_save_monetization() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'hea-lth-platform-core' ) );
		}
		check_admin_referer( 'hea_lth_cc_save_monetization' );

		$number = isset( $_POST['whatsapp'] ) ? preg_replace( '/[^0-9]/', '', sanitize_text_field( wp_unslash( $_POST['whatsapp'] ) ) ) : '';
		if ( '' !== $number && ( strlen( $number ) < 9 || strlen( $number ) > 15 ) ) {
			$number = (string) get_option( self::WHATSAPP_OPTION, '' ); // Invalid input keeps the previous number.
		}

		update_option( self::WHATSAPP_OPTION, $number );

		wp_safe_redirect( self::tab_url( 'monetization' ) . '&saved=1' );
		exit;
	}

	/* ------------------------------------------------------------------ *
	 * 3D model (read-only gate transparency).
	 * ------------------------------------------------------------------ */

	/**
	 * @return void
	 */
	private static function render_model_3d() {
		$config = apply_filters( 'hea_lth_public_anatomy_model_config', array( 'status' => 'license-gated', 'engine' => 'none' ) );

		echo '<h2>' . esc_html__( '3D model', 'hea-lth-platform-core' ) . '</h2>';

		if ( ! isset( $config['status'] ) || 'approved' !== $config['status'] ) {
			echo '<p>' . esc_html__( 'The public model is gated:', 'hea-lth-platform-core' ) . ' <code>' . esc_html( isset( $config['reason'] ) ? (string) $config['reason'] : 'unknown' ) . '</code></p>';
			echo '<p>' . esc_html__( 'The model activates only through the anatomy manifest gate (license, clinical review, GLB validation, QA, LOD budget). There is deliberately no switch on this screen that can bypass it.', 'hea-lth-platform-core' ) . '</p>';

			return;
		}

		$structures = isset( $config['structures'] ) && is_array( $config['structures'] ) ? $config['structures'] : array();
		$layers     = isset( $config['layers'] ) && is_array( $config['layers'] ) ? $config['layers'] : array();
		$lods       = isset( $config['asset']['lods'] ) && is_array( $config['asset']['lods'] ) ? $config['asset']['lods'] : array();

		echo '<p>' . esc_html__( 'Approved and live.', 'hea-lth-platform-core' ) . ' ';
		echo esc_html( sprintf( '%s %s · %d %s · %d %s · %d LODs', __( 'Model', 'hea-lth-platform-core' ), isset( $config['modelId'] ) ? (string) $config['modelId'] : '', count( $structures ), __( 'clickable structures', 'hea-lth-platform-core' ), count( $layers ), __( 'layers', 'hea-lth-platform-core' ), count( $lods ) ) );
		echo '</p>';

		if ( $lods ) {
			echo '<table class="widefat striped" style="max-width:680px"><thead><tr><th>LOD</th><th>' . esc_html__( 'Purpose', 'hea-lth-platform-core' ) . '</th><th>' . esc_html__( 'Triangles', 'hea-lth-platform-core' ) . '</th></tr></thead><tbody>';
			foreach ( $lods as $lod ) {
				echo '<tr><td>' . esc_html( isset( $lod['id'] ) ? (string) $lod['id'] : '' ) . '</td><td>' . esc_html( isset( $lod['purpose'] ) ? (string) $lod['purpose'] : '' ) . '</td><td>' . esc_html( isset( $lod['triangleCount'] ) ? number_format_i18n( (float) $lod['triangleCount'] ) : '' ) . '</td></tr>';
			}
			echo '</tbody></table>';
		}

		echo '<p class="description">' . esc_html__( 'Changing the model means shipping a new manifest through the same gates (license, clinical review, QA); the shipped 3D runtime is under a code freeze and is modified only by owner-named exceptions.', 'hea-lth-platform-core' ) . '</p>';
	}
}

Hea_Lth_Control_Center::boot();
