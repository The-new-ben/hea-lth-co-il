<?php
/**
 * Idempotent provisioning for verified supplier showrooms and equipment.
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Hea_Lth_Showroom_Provisioner {
	const OPTION_KEY = 'hea_lth_showroom_blueprint';
	const VERSION    = '2026-08-13-01';

	public static function boot() {
		add_action( 'init', array( __CLASS__, 'maybe_provision' ), 30 );
	}

	public static function suppliers() {
		return array(
			'nubway' => array(
				'title'        => 'NUBWAY מבית Nicro',
				'excerpt'      => 'פתרונות טכנולוגיים למרפאות אסתטיקה, טיפולי גוף, עור ושיער.',
				'content'      => '<p>NUBWAY מציגה בישראל מערכות מקצועיות למרפאות אסתטיקה וטיפולי גוף. אולם התצוגה מרכז את משפחות המוצרים לפי טכנולוגיה ושימוש במרפאה, כדי לאפשר תכנון רכש והשוואה מסודרת.</p>',
				'website'      => 'https://nubway.co.il/',
				'email'        => 'sales@nubway.co.il',
				'phone'        => '077-7909936',
				'address'      => 'החרושת 18, רמת השרון',
				'brands'       => array( 'NUBWAY' ),
				'capabilities' => array( 'התאמת ציוד למרפאה', 'הדרכה מקצועית', 'שירות ותמיכה' ),
				'source'       => 'https://nubway.co.il/',
			),
			'galaxy' => array(
				'title'        => 'Galaxy',
				'excerpt'      => 'טכנולוגיות רפואיות ואסתטיות למרפאות, מרכזים רפואיים ואנשי מקצוע.',
				'content'      => '<p>Galaxy פועלת בישראל בתחום הטכנולוגיות הרפואיות והאסתטיות. אולם התצוגה מרכז את המותגים והמערכות המקצועיות לפי משפחת טיפול ותפקיד במרפאה.</p>',
				'website'      => 'https://glx.co.il/',
				'email'        => 'info@glx.co.il',
				'phone'        => '03-9601601',
				'address'      => 'שדרות נים 2, ראשון לציון',
				'brands'       => array( 'Cynosure', 'miraDry', 'Venus Concept', 'ARTAS' ),
				'capabilities' => array( 'התאמת טכנולוגיה למרפאה', 'הטמעה והדרכה', 'שירות מקצועי' ),
				'source'       => 'https://glx.co.il/',
			),
		);
	}

	public static function equipment() {
		return array(
			array( 'nubway', 'shefa-robot', 'Shefa Robot', 'RF 448 kHz', 'טיפולי גוף' ),
			array( 'nubway', 'depi-ai', 'Depi AI', 'רובוטיקה ובינה מלאכותית', 'הסרת שיער' ),
			array( 'nubway', 'torasculpt-360', 'ToraSculpt 360', 'Cryolipolysis', 'עיצוב הגוף' ),
			array( 'nubway', 'hakuvision', 'HakuVision', 'AI Skin Analysis', 'אבחון עור' ),
			array( 'nubway', 'kaipulse-ems', 'KaiPulse EMS', 'EMS', 'חיזוק שרירים ועיצוב הגוף' ),
			array( 'nubway', 'hikaripro-d8', 'HikariPro D8', 'Diode Laser', 'הסרת שיער' ),
			array( 'nubway', 'shinco2-elite', 'ShinCO2 Elite', 'CO2 Laser', 'טיפולי עור' ),
			array( 'nubway', 'shinpico-770', 'ShinPico 770', 'Pico Laser', 'טיפולי עור ופיגמנטציה' ),
			array( 'nubway', 'shinyag-pro', 'ShinYAG Pro', 'Nd:YAG Laser', 'טיפולי עור וכלי דם' ),
			array( 'nubway', 'zenlift-hifu', 'ZenLift HIFU', 'HIFU', 'מיצוק וטיפולי פנים' ),
			array( 'nubway', 'dermaneedle-rf6', 'DermaNeedle RF6', 'Microneedling RF', 'טיפולי עור' ),
			array( 'nubway', 'kaipelvi', 'KaiPelvi EMS Chair', 'EMS', 'רצפת אגן' ),
			array( 'galaxy', 'picosure', 'PicoSure', 'Pico Laser', 'טיפולי עור ופיגמנטציה' ),
			array( 'galaxy', 'icon', 'Icon', 'Laser and IPL', 'טיפולי עור' ),
			array( 'galaxy', 'sculpsure', 'SculpSure', 'Laser Body Contouring', 'עיצוב הגוף' ),
			array( 'galaxy', 'vectus', 'Vectus', 'Diode Laser', 'הסרת שיער' ),
			array( 'galaxy', 'artas', 'ARTAS', 'Robotic Hair Restoration', 'שיקום שיער' ),
			array( 'galaxy', 'miradry', 'miraDry', 'Microwave Energy', 'טיפול בהזעת יתר' ),
			array( 'galaxy', 'advatex', 'ADVATx', 'Laser Platform', 'טיפולי עור וכלי דם' ),
			array( 'galaxy', 'potenza', 'Potenza', 'Microneedling RF', 'טיפולי עור' ),
		);
	}

	public static function maybe_provision() {
		if ( self::VERSION === get_option( self::OPTION_KEY ) ) {
			return;
		}

		$supplier_ids = array();
		foreach ( self::suppliers() as $key => $supplier ) {
			$supplier_ids[ $key ] = self::upsert_supplier( $key, $supplier );
		}

		foreach ( self::equipment() as $equipment ) {
			if ( ! empty( $supplier_ids[ $equipment[0] ] ) ) {
				self::upsert_equipment( $equipment, $supplier_ids[ $equipment[0] ], self::suppliers()[ $equipment[0] ]['source'] );
			}
		}

		flush_rewrite_rules( false );
		update_option( self::OPTION_KEY, self::VERSION, false );
	}

	private static function find_seeded( $key, $post_type ) {
		$posts = get_posts(
			array(
				'post_type'      => $post_type,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => '_hea_lth_seed_key',
				'meta_value'     => $key,
			)
		);

		return empty( $posts ) ? 0 : (int) $posts[0];
	}

	private static function insert_seed( $key, $post_type, $record ) {
		$existing = self::find_seeded( $key, $post_type );
		if ( $existing ) {
			return $existing;
		}

		$post_id = wp_insert_post( $record, true );
		if ( is_wp_error( $post_id ) ) {
			return 0;
		}

		update_post_meta( (int) $post_id, '_hea_lth_seed_key', $key );
		return (int) $post_id;
	}

	private static function upsert_supplier( $key, $supplier ) {
		$post_id = self::insert_seed(
			'supplier:' . $key,
			'hp_supplier',
			array(
				'post_type'    => 'hp_supplier',
				'post_status'  => 'publish',
				'post_name'    => $key,
				'post_title'   => $supplier['title'],
				'post_excerpt' => $supplier['excerpt'],
				'post_content' => $supplier['content'],
			)
		);

		if ( $post_id ) {
			$meta = array(
				'hp_public_state'     => 'verified',
				'hp_last_verified'    => '2026-08-13',
				'hp_website_url'      => $supplier['website'],
				'hp_contact_email'    => $supplier['email'],
				'hp_contact_phone'    => $supplier['phone'],
				'hp_address'          => $supplier['address'],
				'hp_brands'           => $supplier['brands'],
				'hp_capabilities'     => $supplier['capabilities'],
				'hp_source_url'       => $supplier['source'],
				'hp_public_disclosure'=> 'פרטי החברה והקטלוג מבוססים על מידע עסקי פומבי של הספק.',
			);
			foreach ( $meta as $meta_key => $value ) {
				update_post_meta( $post_id, $meta_key, $value );
			}
		}

		return $post_id;
	}

	private static function upsert_equipment( $equipment, $supplier_id, $source_url ) {
		$post_id = self::insert_seed(
			'equipment:' . $equipment[0] . ':' . $equipment[1],
			'hp_equipment',
			array(
				'post_type'    => 'hp_equipment',
				'post_status'  => 'publish',
				'post_name'    => $equipment[0] . '-' . $equipment[1],
				'post_title'   => $equipment[2],
				'post_excerpt' => sprintf( '%s — מערכת מקצועית בקטגוריית %s.', $equipment[2], $equipment[4] ),
				'post_content' => sprintf( '<p>%s מוצג כחלק מאולם התצוגה המקצועי של הספק. העמוד מרכז את זהות המערכת, הטכנולוגיה ומשפחת השימוש במרפאה.</p>', esc_html( $equipment[2] ) ),
			)
		);

		if ( $post_id ) {
			update_post_meta( $post_id, 'hp_supplier_id', $supplier_id );
			update_post_meta( $post_id, 'hp_technology', $equipment[3] );
			update_post_meta( $post_id, 'hp_product_family', $equipment[4] );
			update_post_meta( $post_id, 'hp_clinic_roles', array( $equipment[4] ) );
			update_post_meta( $post_id, 'hp_source_url', $source_url );
			update_post_meta( $post_id, 'hp_editorial_state', 'reviewed' );
			update_post_meta( $post_id, 'hp_last_reviewed', '2026-08-13' );
			update_post_meta( $post_id, 'hp_source_note', 'האתר הרשמי של הספק' );
		}
	}
}
