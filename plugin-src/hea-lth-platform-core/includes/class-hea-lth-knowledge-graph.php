<?php
/**
 * Evidence-governed science and technology knowledge graph.
 *
 * Nodes own informational intent. Bridges point to controlled route keys so
 * scientific authority can support commercial discovery without duplicating
 * treatment, provider, supplier, or product intent.
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stable registry for the biology-to-revenue information architecture.
 */
class Hea_Lth_Knowledge_Graph {

	/**
	 * Return all governed nodes.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function nodes() {
		$hallmarks = array(
			'label' => 'Hallmarks of Aging: An Expanding Universe',
			'url'   => 'https://pubmed.ncbi.nlm.nih.gov/36599349/',
		);
		$nia = array(
			'label' => 'National Institute on Aging, Biology of Aging',
			'url'   => 'https://www.nia.nih.gov/about/budget/biology-aging',
		);
		$who = array(
			'label' => 'World Health Organization, Healthy ageing and functional ability',
			'url'   => 'https://www.who.int/news-room/questions-and-answers/item/healthy-ageing-and-functional-ability',
		);
		$fda = array(
			'label' => 'FDA, Artificial Intelligence-Enabled Medical Devices',
			'url'   => 'https://www.fda.gov/medical-devices/software-medical-device-samd/artificial-intelligence-enabled-medical-devices',
		);
		$skin_barrier = array(
			'label' => 'Aging of the skin barrier, PubMed',
			'url'   => 'https://pubmed.ncbi.nlm.nih.gov/31345321/',
		);

		return array(
			'biology' => array(
				'route_key'   => 'biology',
				'eyebrow'     => 'מפת הידע המדעית',
				'title'       => 'ביולוגיה של האדם והזדקנות בריאה',
				'summary'     => 'מרכז יסוד להבנת התהליכים התאיים, המולקולריים והמערכתיים שמחברים בין גוף האדם, בריאות, הזדקנות וטכנולוגיה רפואית.',
				'focus'       => array( 'מנגנונים תאיים ומולקולריים', 'קשרים בין מערכות הגוף', 'תרגום מחקר למדידה, טיפול וטכנולוגיה' ),
				'children'    => array( 'cellular_aging', 'metabolism', 'inflammation', 'genetics_epigenetics' ),
				'bridges'     => array( 'longevity', 'skin_science', 'wellness', 'biomarkers' ),
				'sources'     => array( $hallmarks, $nia ),
				'review_level'=> 'maximum',
			),
			'cellular_aging' => array(
				'route_key'   => 'biology_cellular_aging',
				'eyebrow'     => 'ביולוגיה תאית',
				'title'       => 'הזדקנות תאית ומנגנוני יסוד',
				'summary'     => 'מפת מושגים לתהליכים שנחקרים בהזדקנות, ובהם יציבות הגנום, טלומרים, פרוטאוסטזיס, אוטופגיה, מיטוכונדריה וסנסנס תאי.',
				'focus'       => array( 'שנים עשר סימני ההיכר של ההזדקנות', 'יחסי גומלין בין נזק, תחזוקה ותגובה לעקה', 'הבדל בין מנגנון מחקרי לבין תועלת קלינית מוכחת' ),
				'children'    => array(),
				'bridges'     => array( 'longevity', 'skin_science', 'biomarkers' ),
				'sources'     => array( $hallmarks, $nia ),
				'review_level'=> 'maximum',
			),
			'metabolism' => array(
				'route_key'   => 'biology_metabolism',
				'eyebrow'     => 'מערכות אנרגיה',
				'title'       => 'מטבוליזם, חישה תזונתית ומיטוכונדריה',
				'summary'     => 'מרכז ידע על הפקת אנרגיה, איתות תזונתי, תפקוד מיטוכונדריאלי והקשרים שלהם לבריאות לאורך החיים.',
				'focus'       => array( 'מאזן אנרגיה והומאוסטזיס', 'מסלולי חישה תזונתית', 'מדדים, הקשר קליני ומגבלות פרשנות' ),
				'children'    => array(),
				'bridges'     => array( 'wellness', 'prevention', 'clinic_build', 'biomarkers' ),
				'sources'     => array( $hallmarks, $nia ),
				'review_level'=> 'maximum',
			),
			'inflammation' => array(
				'route_key'   => 'biology_inflammation',
				'eyebrow'     => 'מערכת החיסון',
				'title'       => 'דלקת כרונית, תקשורת בין תאים ובריאות',
				'summary'     => 'מסגרת מדעית להבנת דלקת, תקשורת בין תאים והקשרים למערכות גוף, בלי להפוך סמן יחיד לאבחנה או להבטחה טיפולית.',
				'focus'       => array( 'תגובה חיסונית והומאוסטזיס', 'דלקת כרונית בהקשר של הזדקנות', 'משמעות ומגבלות של סמנים ביולוגיים' ),
				'children'    => array(),
				'bridges'     => array( 'longevity', 'wellness', 'biomarkers' ),
				'sources'     => array( $hallmarks, $nia ),
				'review_level'=> 'maximum',
			),
			'genetics_epigenetics' => array(
				'route_key'   => 'biology_genetics_epigenetics',
				'eyebrow'     => 'מידע ביולוגי',
				'title'       => 'גנטיקה, אפיגנטיקה ויציבות הגנום',
				'summary'     => 'מרכז ידע על מידע תורשתי, בקרה על ביטוי גנים, נזק ותיקון DNA והשינויים הנחקרים לאורך החיים.',
				'focus'       => array( 'יציבות הגנום ותיקון DNA', 'שינויים אפיגנטיים', 'הבדל בין קשר מחקרי, סמן ויישום רפואי' ),
				'children'    => array(),
				'bridges'     => array( 'longevity', 'biomarkers', 'ai_robotics' ),
				'sources'     => array( $hallmarks, $nia ),
				'review_level'=> 'maximum',
			),
			'longevity' => array(
				'route_key'   => 'longevity_medicine',
				'eyebrow'     => 'רפואה ובריאות לאורך החיים',
				'title'       => 'רפואת אריכות ימים והזדקנות בריאה',
				'summary'     => 'מפה שמחברת בין ביולוגיית ההזדקנות, תפקוד לאורך החיים, מניעה, מדידה ושירותים קליניים, תוך הבחנה בין מחקר מתפתח לפרקטיקה מבוססת.',
				'focus'       => array( 'בריאות ותפקוד לאורך החיים', 'מניעה ומעקב מותאם הקשר', 'הערכת ראיות לפני אימוץ בדיקה או התערבות' ),
				'children'    => array(),
				'bridges'     => array( 'biology_root', 'prevention', 'biomarkers', 'doctor_index' ),
				'sources'     => array( $nia, $who, $hallmarks ),
				'review_level'=> 'maximum',
			),
			'skin' => array(
				'route_key'    => 'skin',
				'eyebrow'      => 'ביולוגיה של העור',
				'title'        => 'מדע העור, מחסום העור והזדקנות',
				'summary'      => 'מרכז ידע על שכבות העור, תפקוד המחסום, התחדשות, השפעות סביבתיות ושינויים מבניים ותפקודיים לאורך החיים.',
				'focus'        => array( 'מבנה ותפקוד מחסום העור', 'הזדקנות כרונולוגית והשפעות סביבתיות', 'הבדל בין מדע העור, טיפוח וטיפול רפואי' ),
				'children'     => array(),
				'bridges'      => array( 'longevity', 'skin_treatments', 'skin_products', 'aesthetic_medicine' ),
				'sources'      => array( $skin_barrier, $hallmarks ),
				'review_level' => 'maximum',
			),
			'biomarkers' => array(
				'route_key'   => 'health_technology_biomarkers',
				'eyebrow'     => 'מדידה רפואית',
				'title'       => 'סמנים ביולוגיים, מדידה ומעקב',
				'summary'     => 'מסגרת לבחינת מה נמדד, עד כמה המדידה תקפה, מה ההקשר הקליני שלה וכיצד היא מתחברת למעקב ולא לקבלת החלטה על בסיס נתון יחיד.',
				'focus'       => array( 'תוקף אנליטי ותוקף קליני', 'מגמות לאורך זמן והקשר אישי', 'חיבור בין מעבדה, מכשור ותוכנה' ),
				'children'    => array(),
				'bridges'     => array( 'laboratory', 'longevity', 'equipment', 'ai_robotics' ),
				'sources'     => array( $nia, $who ),
				'review_level'=> 'maximum',
			),
			'ai_robotics' => array(
				'route_key'   => 'health_technology_ai_robotics',
				'eyebrow'     => 'טכנולוגיה רפואית',
				'title'       => 'בינה מלאכותית ורובוטיקה בבריאות',
				'summary'     => 'מרכז ידע על תוכנה, אלגוריתמים, אוטומציה ורובוטיקה רפואית, עם הפרדה בין כלי בריאות כלליים, מערכות תומכות החלטה ומכשור רפואי מפוקח.',
				'focus'       => array( 'שקיפות וביצועי צוות אדם ומערכת', 'מחזור חיים, ניטור ושינויי תוכנה', 'הבדל בין חדשנות טכנולוגית לאישור רפואי' ),
				'children'    => array(),
				'bridges'     => array( 'health_technology', 'equipment', 'suppliers', 'clinic_build' ),
				'sources'     => array( $fda ),
				'review_level'=> 'maximum',
			),
		);
	}

	/**
	 * Return one node or an empty array.
	 *
	 * @param string $node_id Stable node identifier.
	 * @return array<string, mixed>
	 */
	public static function node( $node_id ) {
		$nodes = self::nodes();

		return isset( $nodes[ $node_id ] ) ? $nodes[ $node_id ] : array();
	}

	/**
	 * Controlled commercial and navigational bridges.
	 *
	 * @return array<string, array<string, string>>
	 */
	public static function bridges() {
		return array(
			'longevity'        => array( 'label' => 'רפואת אריכות ימים', 'copy' => 'המסגרת הקלינית והראייתית לבריאות לאורך החיים.', 'registry' => 'foundation', 'route_key' => 'longevity_medicine' ),
			'biology_root'     => array( 'label' => 'ביולוגיה של האדם', 'copy' => 'המנגנונים שמאחורי המפה הקלינית.', 'registry' => 'foundation', 'route_key' => 'biology' ),
			'skin_science'     => array( 'label' => 'מדע העור', 'copy' => 'ביולוגיית העור, תפקוד המחסום ושינויים לאורך החיים.', 'registry' => 'foundation', 'route_key' => 'skin' ),
			'wellness'         => array( 'label' => 'בריאות ואיכות חיים', 'copy' => 'שינה, תנועה, תזונה והרגלים בהקשר מבוסס.', 'registry' => 'foundation', 'route_key' => 'wellness' ),
			'prevention'       => array( 'label' => 'מניעה ומעקב', 'copy' => 'נקודות לשיחה על בדיקות תקופתיות ומעקב.', 'registry' => 'foundation', 'route_key' => 'wellness_prevention' ),
			'biomarkers'       => array( 'label' => 'סמנים ביולוגיים', 'copy' => 'מדידה, תוקף, הקשר ומגבלות פרשנות.', 'registry' => 'foundation', 'route_key' => 'health_technology_biomarkers' ),
			'ai_robotics'      => array( 'label' => 'בינה מלאכותית ורובוטיקה', 'copy' => 'מערכות חכמות, מכשור ותהליכי אימוץ אחראי.', 'registry' => 'foundation', 'route_key' => 'health_technology_ai_robotics' ),
			'health_technology'=> array( 'label' => 'טכנולוגיות בריאות', 'copy' => 'מפת הטכנולוגיה, המכשור והציוד.', 'registry' => 'foundation', 'route_key' => 'health_technology' ),
			'laboratory'       => array( 'label' => 'בדיקות מעבדה', 'copy' => 'הכנה, מונחים ושאלות לפענוח מקצועי.', 'registry' => 'foundation', 'route_key' => 'diagnostics_laboratory' ),
			'equipment'        => array( 'label' => 'קטלוג ציוד רפואי', 'copy' => 'טכנולוגיות ומוצרים לפי שימוש מקצועי.', 'registry' => 'foundation', 'route_key' => 'medical_equipment' ),
			'suppliers'        => array( 'label' => 'אולמות תצוגה לספקים', 'copy' => 'ספקים, מותגים ומוצרים במבנה מקצועי.', 'registry' => 'foundation', 'route_key' => 'suppliers' ),
			'clinic_build'     => array( 'label' => 'הקמת מרפאה', 'copy' => 'מפת רכש, תשתיות ושירותים למרפאות.', 'registry' => 'foundation', 'route_key' => 'clinic_build' ),
			'doctor_index'     => array( 'label' => 'רופאים ומרפאות', 'copy' => 'איתור אנשי מקצוע לפי תחום והקשר.', 'registry' => 'canonical', 'route_key' => 'doctor_clinic_index' ),
			'skin_treatments'  => array( 'label' => 'טיפולי עור פרטיים', 'copy' => 'השוואת טיפולים, התאמה וספקי שירות.', 'registry' => 'canonical', 'route_key' => 'skin_treatments_private' ),
			'skin_products'    => array( 'label' => 'מוצרי טיפוח לעור', 'copy' => 'קטגוריות מוצרים ובחירה לפי צורך.', 'registry' => 'foundation', 'route_key' => 'products_skin' ),
			'aesthetic_medicine' => array( 'label' => 'רפואה אסתטית', 'copy' => 'מפת טיפולים ושירותים אסתטיים.', 'registry' => 'canonical', 'route_key' => 'aesthetic_medicine' ),
		);
	}
}
