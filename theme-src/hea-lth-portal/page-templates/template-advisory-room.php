<?php
/**
 * Template Name: חדר ייעוץ אישי
 * Template Post Type: page
 *
 * Code-gated private advisory room. Unlocks either through the native
 * WordPress post password or a matching ?code= query value, so the owner
 * can hand a client a one-click link. Room content comes from
 * Hea_Lth_Advisory_Rooms — never from post content. Buyer rooms render a
 * decision toolkit (comparison table, technology guide, decision
 * criteria, per-machine interest CTAs); supplier rooms render an
 * anonymized opportunity brief and material requests. Equipment cards
 * render only records whose editorial state is approved, and the surface
 * never carries invented prices — price fields are status-tracked.
 *
 * @package HeaLthPortal
 */

nocache_headers();

$room_page_id = get_the_ID();
$advisory     = class_exists( 'Hea_Lth_Advisory_Rooms' ) ? Hea_Lth_Advisory_Rooms::room_for_page( $room_page_id ) : null;
$given_code   = isset( $_GET['code'] ) ? sanitize_text_field( wp_unslash( $_GET['code'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Access code for a read-only gated brief; compared with hash_equals.
$code_valid   = $advisory && '' !== $given_code && Hea_Lth_Advisory_Rooms::code_matches( $advisory, $given_code );
$unlocked     = $advisory && ( $code_valid || ! post_password_required() );
$whatsapp     = '972525101555';

if ( $unlocked ) {
	Hea_Lth_Advisory_Rooms::notify_entry( $room_page_id, $advisory, $code_valid ? 'code' : 'password' );
}

/**
 * Static category badge (inline SVG, no dynamic content).
 *
 * @param string $badge Badge key.
 */
function hea_lth_advisory_badge( $badge ) {
	$svgs = array(
		'cryo'  => '<svg viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="22" fill="none" stroke="currentColor" stroke-width="2"/><path d="M24 10v28M14 15l20 18M34 15L14 33M24 17l-4-4M24 17l4-4M24 31l-4 4M24 31l4 4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
		'pulse' => '<svg viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="22" fill="none" stroke="currentColor" stroke-width="2"/><path d="M10 24h7l4-10 6 20 4-10h7" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		'wave'  => '<svg viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="22" fill="none" stroke="currentColor" stroke-width="2"/><path d="M10 28c3-8 6-8 9 0s6 8 9 0 6-8 9 0" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/><path d="M14 18c2.5-5 5-5 7.5 0s5 5 7.5 0 5-5 7.5 0" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity=".55"/></svg>',
		'focus' => '<svg viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="22" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="24" cy="24" r="13" fill="none" stroke="currentColor" stroke-width="1.6" opacity=".55"/><circle cx="24" cy="24" r="6" fill="none" stroke="currentColor" stroke-width="2.4"/><circle cx="24" cy="24" r="1.8" fill="currentColor"/></svg>',
		'beam'  => '<svg viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="22" fill="none" stroke="currentColor" stroke-width="2"/><path d="M14 34L30 14l4 4-16 20-6 2z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M31 13l4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M12 14l3 3M20 10l1.5 3.5M36 26l-3-1.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity=".55"/></svg>',
	);
	if ( isset( $svgs[ $badge ] ) ) {
		echo $svgs[ $badge ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static inline SVG map above, no variable content.
	}
}

/**
 * Resolve an approved equipment record for a room item.
 *
 * @param string $slug Equipment slug.
 * @return array|null title/tech/family/supplier/url or null when not approved.
 */
function hea_lth_advisory_machine( $slug ) {
	$machine = get_page_by_path( $slug, OBJECT, 'hp_equipment' );
	if ( ! $machine || 'publish' !== $machine->post_status || 'approved' !== get_post_meta( $machine->ID, 'hp_editorial_state', true ) ) {
		return null;
	}
	$supplier = (int) get_post_meta( $machine->ID, 'hp_supplier_id', true );
	return array(
		'title'    => get_the_title( $machine ),
		'tech'     => (string) get_post_meta( $machine->ID, 'hp_technology', true ),
		'family'   => (string) get_post_meta( $machine->ID, 'hp_product_family', true ),
		'supplier' => $supplier ? get_the_title( $supplier ) : '',
		'url'      => get_permalink( $machine ),
	);
}

get_header();

if ( ! $advisory ) :
	?>
	<section class="hp-template-section"><div class="hp-shell"><h1><?php esc_html_e( 'העמוד אינו זמין', 'hea-lth-portal' ); ?></h1><p><?php esc_html_e( 'חדר הייעוץ המבוקש אינו פעיל. דברו איתנו לקבלת קישור מעודכן.', 'hea-lth-portal' ); ?></p></div></section>
	<?php
elseif ( ! $unlocked ) :
	?>
	<section class="hp-advisory-hero"><div class="hp-shell">
		<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'אזור ייעוץ אישי', 'hea-lth-portal' ); ?></p>
		<h1><?php esc_html_e( 'חדר ייעוץ פרטי', 'hea-lth-portal' ); ?></h1>
		<p><?php esc_html_e( 'העמוד מיועד לשותף תהליך של Hea-lth ומוגן בקוד גישה אישי.', 'hea-lth-portal' ); ?></p>
	</div></section>
	<section class="hp-template-section hp-template-section--paper"><div class="hp-shell hp-advisory-gate">
		<h2><?php esc_html_e( 'הזינו קוד גישה', 'hea-lth-portal' ); ?></h2>
		<p><?php esc_html_e( 'הקוד האישי שלך: מספר הטלפון שלך, ספרות בלבד וללא מקפים.', 'hea-lth-portal' ); ?></p>
		<?php if ( '' !== $given_code && ! $code_valid ) : ?>
			<p class="hp-advisory-gate__error" role="alert"><?php esc_html_e( 'הקוד שהוזן אינו תואם. נסו שוב, ספרות בלבד.', 'hea-lth-portal' ); ?></p>
		<?php endif; ?>
		<form method="get" action="<?php echo esc_url( get_permalink() ); ?>" class="hp-advisory-gate__form">
			<label for="hp-advisory-code" class="screen-reader-text"><?php esc_html_e( 'קוד גישה', 'hea-lth-portal' ); ?></label>
			<input type="tel" id="hp-advisory-code" name="code" inputmode="numeric" autocomplete="off" placeholder="05XXXXXXXX" required>
			<button type="submit" class="hp-button"><?php esc_html_e( 'כניסה לחדר', 'hea-lth-portal' ); ?></button>
		</form>
	</div></section>
	<?php
elseif ( 'supplier' === $advisory['type'] ) :
	$reply_text = rawurlencode( 'שלום, כאן ' . $advisory['client'] . ' — מעדכנים לגבי החומרים לעסקה הפעילה בפורטל Hea-lth.' );
	?>
	<section class="hp-advisory-hero"><div class="hp-shell">
		<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'Hea-lth — אזור עסקאות לספקים', 'hea-lth-portal' ); ?></p>
		<h1><?php echo esc_html( $advisory['title'] ); ?></h1>
		<p class="hp-advisory-hero__client"><?php echo esc_html( sprintf( __( 'הוכן עבור %1$s — לידי %2$s', 'hea-lth-portal' ), $advisory['client'], $advisory['contact'] ) ); ?></p>
		<p><?php echo esc_html( $advisory['intro'] ); ?></p>
		<p class="hp-advisory-hero__updated"><?php echo esc_html( sprintf( __( 'עודכן: %s', 'hea-lth-portal' ), $advisory['updated'] ) ); ?></p>
	</div></section>

	<section class="hp-template-section hp-template-section--paper"><div class="hp-shell">
		<p class="hp-eyebrow"><?php esc_html_e( 'ההזדמנות', 'hea-lth-portal' ); ?></p>
		<h2><?php esc_html_e( 'תקציר העסקה', 'hea-lth-portal' ); ?></h2>
		<ul class="hp-advisory-brief">
			<?php foreach ( $advisory['brief'] as $line ) : ?>
				<li><?php echo esc_html( $line ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div></section>

	<section class="hp-template-section hp-template-section--soft"><div class="hp-shell">
		<p class="hp-eyebrow"><?php esc_html_e( 'נדרש מכם עכשיו', 'hea-lth-portal' ); ?></p>
		<h2><?php esc_html_e( 'החומרים שפותחים את ההשוואה', 'hea-lth-portal' ); ?></h2>
		<p class="hp-advisory-note"><?php esc_html_e( 'הרוכש בוחן את החלופות בעמוד השוואה חי. ספק שהחומרים שלו מגיעים ראשונים — מוצג ראשון.', 'hea-lth-portal' ); ?></p>
		<ol class="hp-advisory-steps">
			<?php foreach ( $advisory['asks'] as $ask ) : ?>
				<li><?php echo esc_html( $ask ); ?></li>
			<?php endforeach; ?>
		</ol>
		<div class="hp-advisory-cta-row">
			<a class="hp-button" href="<?php echo esc_url( 'https://wa.me/' . $whatsapp . '?text=' . $reply_text ); ?>" rel="noopener noreferrer"><?php esc_html_e( 'שליחת עדכון בוואטסאפ', 'hea-lth-portal' ); ?></a>
			<a class="hp-inline-link" href="mailto:mistabrajustice@gmail.com"><?php esc_html_e( 'או השיבו במייל עם הקבצים', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a>
		</div>
	</div></section>

	<?php if ( ! empty( $advisory['machines'] ) ) : ?>
	<section class="hp-template-section"><div class="hp-shell">
		<p class="hp-eyebrow"><?php esc_html_e( 'הקטלוג שלכם אצלנו', 'hea-lth-portal' ); ?></p>
		<h2><?php esc_html_e( 'המערכות שלכם ברשימה המוצגת לרוכש', 'hea-lth-portal' ); ?></h2>
		<div class="hp-advisory-chiprow">
			<?php
			foreach ( $advisory['machines'] as $slug ) :
				$machine = hea_lth_advisory_machine( $slug );
				if ( ! $machine ) {
					continue;
				}
				?>
				<a class="hp-advisory-chip" href="<?php echo esc_url( $machine['url'] ); ?>"><?php echo esc_html( $machine['title'] ); ?></a>
			<?php endforeach; ?>
		</div>
	</div></section>
	<?php endif; ?>

	<section class="hp-template-section hp-template-section--soft"><div class="hp-shell">
		<div class="hp-advisory-boundary"><p><?php echo esc_html( $advisory['terms'] ); ?></p></div>
	</div></section>
	<?php
else :
	$categories    = Hea_Lth_Advisory_Rooms::categories();
	$criteria      = Hea_Lth_Advisory_Rooms::decision_criteria();
	$used_cats     = array();
	$summary_text  = rawurlencode( 'שלום, כאן ' . $advisory['client'] . '. עברתי על חדר הייעוץ ואשמח לשיחת סיכום.' );
	$price_pending = __( 'בהשלמה מול הספק', 'hea-lth-portal' );
	?>
	<section class="hp-advisory-hero"><div class="hp-shell">
		<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'אזור ייעוץ אישי — Hea-lth', 'hea-lth-portal' ); ?></p>
		<h1><?php echo esc_html( $advisory['title'] ); ?></h1>
		<p class="hp-advisory-hero__client"><?php echo esc_html( sprintf( __( 'הוכן עבור %s', 'hea-lth-portal' ), $advisory['client'] ) ); ?></p>
		<p><?php echo esc_html( $advisory['intro'] ); ?></p>
		<p class="hp-advisory-hero__updated"><?php echo esc_html( sprintf( __( 'עודכן לאחרונה: %s · העמוד מתעדכן עם כל הצעה שנכנסת', 'hea-lth-portal' ), $advisory['updated'] ) ); ?></p>
	</div></section>

	<section class="hp-template-section hp-template-section--paper"><div class="hp-shell">
		<div class="hp-advisory-howto">
			<?php foreach ( $advisory['howto'] as $i => $step ) : ?>
				<div class="hp-advisory-howto__step"><span><?php echo esc_html( (string) ( $i + 1 ) ); ?></span><p><?php echo esc_html( $step ); ?></p></div>
			<?php endforeach; ?>
		</div>
	</div></section>

	<section class="hp-template-section"><div class="hp-shell">
		<p class="hp-eyebrow"><?php esc_html_e( 'הדרישות שהוגדרו', 'hea-lth-portal' ); ?></p>
		<h2><?php esc_html_e( 'מה חיפשנו עבורך', 'hea-lth-portal' ); ?></h2>
		<div class="hp-advisory-grid">
			<?php foreach ( $advisory['needs'] as $need ) : ?>
				<article class="hp-advisory-card"><h3><?php echo esc_html( $need['title'] ); ?></h3><p><?php echo esc_html( $need['copy'] ); ?></p></article>
			<?php endforeach; ?>
		</div>
	</div></section>

	<?php
	// Collect resolved machines once for both the table and the cards.
	$groups = array();
	foreach ( $advisory['equipment'] as $group ) {
		$resolved = array();
		foreach ( $group['items'] as $item ) {
			$machine = hea_lth_advisory_machine( $item['slug'] );
			if ( ! $machine ) {
				continue;
			}
			$machine['why']      = $item['why'];
			$machine['category'] = $item['category'];
			$resolved[]          = $machine;
			$used_cats[ $item['category'] ] = true;
		}
		if ( $resolved ) {
			$groups[] = array(
				'label' => $group['label'],
				'items' => $resolved,
			);
		}
	}
	?>

	<section class="hp-template-section hp-template-section--soft"><div class="hp-shell">
		<p class="hp-eyebrow"><?php esc_html_e( 'תמונת מצב', 'hea-lth-portal' ); ?></p>
		<h2><?php esc_html_e( 'טבלת ההשוואה המרוכזת', 'hea-lth-portal' ); ?></h2>
		<div class="hp-advisory-tablewrap">
			<table class="hp-advisory-table">
				<thead><tr>
					<th scope="col"><?php esc_html_e( 'מערכת', 'hea-lth-portal' ); ?></th>
					<th scope="col"><?php esc_html_e( 'קטגוריה', 'hea-lth-portal' ); ?></th>
					<th scope="col"><?php esc_html_e( 'טכנולוגיה', 'hea-lth-portal' ); ?></th>
					<th scope="col"><?php esc_html_e( 'ספק', 'hea-lth-portal' ); ?></th>
					<th scope="col"><?php esc_html_e( 'טווח מחיר', 'hea-lth-portal' ); ?></th>
					<th scope="col"><?php esc_html_e( 'סימון עניין', 'hea-lth-portal' ); ?></th>
				</tr></thead>
				<tbody>
				<?php foreach ( $groups as $group ) : ?>
					<?php foreach ( $group['items'] as $machine ) : ?>
					<tr>
						<th scope="row"><a href="<?php echo esc_url( $machine['url'] ); ?>"><?php echo esc_html( $machine['title'] ); ?></a></th>
						<td><?php echo esc_html( isset( $categories[ $machine['category'] ] ) ? $categories[ $machine['category'] ]['name'] : '' ); ?></td>
						<td><?php echo esc_html( $machine['tech'] ); ?></td>
						<td><?php echo esc_html( $machine['supplier'] ); ?></td>
						<td><span class="hp-advisory-pill"><?php echo esc_html( $price_pending ); ?></span></td>
						<td><a class="hp-advisory-mark" href="<?php echo esc_url( 'https://wa.me/' . $whatsapp . '?text=' . rawurlencode( 'שלום, כאן ' . $advisory['client'] . '. מעניין אותי לשמוע עוד על ' . $machine['title'] . ' (' . $machine['supplier'] . ').' ) ); ?>" rel="noopener noreferrer"><?php esc_html_e( 'מעניין אותי', 'hea-lth-portal' ); ?></a></td>
					</tr>
					<?php endforeach; ?>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<p class="hp-advisory-note hp-advisory-note--muted"><?php esc_html_e( 'טווחי המחירים מוצגים רק מתוך הצעות כתובות של הספקים — ללא הערכות. הסטטוס מתעדכן כאן עם כל הצעה שנכנסת.', 'hea-lth-portal' ); ?></p>
	</div></section>

	<?php foreach ( $groups as $group ) : ?>
	<section class="hp-template-section"><div class="hp-shell">
		<p class="hp-eyebrow"><?php esc_html_e( 'מהקטלוג המקצועי', 'hea-lth-portal' ); ?></p>
		<h2><?php echo esc_html( $group['label'] ); ?></h2>
		<div class="hp-advisory-grid hp-advisory-grid--three">
			<?php foreach ( $group['items'] as $machine ) : ?>
				<article class="hp-advisory-card hp-advisory-card--machine">
					<div class="hp-advisory-card__head">
						<span class="hp-advisory-badge"><?php hea_lth_advisory_badge( isset( $categories[ $machine['category'] ] ) ? $categories[ $machine['category'] ]['badge'] : '' ); ?></span>
						<div>
							<h3><?php echo esc_html( $machine['title'] ); ?></h3>
							<?php if ( '' !== $machine['family'] ) : ?><p class="hp-advisory-card__role"><?php echo esc_html( $machine['family'] ); ?></p><?php endif; ?>
						</div>
					</div>
					<?php if ( '' !== $machine['tech'] ) : ?><p class="hp-advisory-card__tech"><?php echo esc_html( $machine['tech'] ); ?></p><?php endif; ?>
					<p><?php echo esc_html( $machine['why'] ); ?></p>
					<p class="hp-advisory-card__supplier"><?php echo esc_html( sprintf( __( 'ספק: %s', 'hea-lth-portal' ), $machine['supplier'] ) ); ?></p>
					<p class="hp-advisory-pill"><?php echo esc_html( sprintf( __( 'טווח מחיר: %s', 'hea-lth-portal' ), $price_pending ) ); ?></p>
					<div class="hp-advisory-cta-row">
						<a class="hp-button hp-button--small" href="<?php echo esc_url( 'https://wa.me/' . $whatsapp . '?text=' . rawurlencode( 'שלום, כאן ' . $advisory['client'] . '. מעניין אותי לשמוע עוד על ' . $machine['title'] . ' (' . $machine['supplier'] . ').' ) ); ?>" rel="noopener noreferrer"><?php esc_html_e( 'מעניין אותי', 'hea-lth-portal' ); ?></a>
						<a class="hp-inline-link" href="<?php echo esc_url( $machine['url'] ); ?>"><?php esc_html_e( 'פרטי המערכת', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div></section>
	<?php endforeach; ?>

	<?php if ( $used_cats ) : ?>
	<section class="hp-template-section hp-template-section--paper"><div class="hp-shell">
		<p class="hp-eyebrow"><?php esc_html_e( 'רקע להחלטה', 'hea-lth-portal' ); ?></p>
		<h2><?php esc_html_e( 'מדריך הטכנולוגיות בקצרה', 'hea-lth-portal' ); ?></h2>
		<div class="hp-advisory-grid hp-advisory-grid--three">
			<?php foreach ( $used_cats as $cat_key => $unused ) : ?>
				<?php if ( isset( $categories[ $cat_key ] ) ) : ?>
				<article class="hp-advisory-card hp-advisory-card--cat">
					<div class="hp-advisory-card__head">
						<span class="hp-advisory-badge"><?php hea_lth_advisory_badge( $categories[ $cat_key ]['badge'] ); ?></span>
						<h3><?php echo esc_html( $categories[ $cat_key ]['name'] ); ?></h3>
					</div>
					<p><?php echo esc_html( $categories[ $cat_key ]['copy'] ); ?></p>
				</article>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div></section>
	<?php endif; ?>

	<section class="hp-template-section hp-template-section--soft"><div class="hp-shell">
		<p class="hp-eyebrow"><?php esc_html_e( 'לפני שסוגרים', 'hea-lth-portal' ); ?></p>
		<h2><?php esc_html_e( '7 הדברים שאנחנו בודקים בכל הצעה עבורך', 'hea-lth-portal' ); ?></h2>
		<ol class="hp-advisory-steps">
			<?php foreach ( $criteria as $criterion ) : ?>
				<li><?php echo esc_html( $criterion ); ?></li>
			<?php endforeach; ?>
		</ol>
	</div></section>

	<section class="hp-template-section"><div class="hp-shell">
		<p class="hp-eyebrow"><?php esc_html_e( 'מסלולי הספקים', 'hea-lth-portal' ); ?></p>
		<h2><?php esc_html_e( 'הספקים שגויסו לתהליך שלך', 'hea-lth-portal' ); ?></h2>
		<p class="hp-advisory-note"><?php esc_html_e( 'הצעות המחיר נאספות במרוכז דרכנו ויוצגו בהשוואה מסודרת — ללא שיחות מכירה ישירות אליך.', 'hea-lth-portal' ); ?></p>
		<div class="hp-advisory-grid hp-advisory-grid--three">
			<?php foreach ( $advisory['suppliers'] as $track ) : ?>
				<article class="hp-advisory-card"><h3><?php echo esc_html( $track['name'] ); ?></h3><p class="hp-advisory-card__role"><?php echo esc_html( $track['role'] ); ?></p><p><?php echo esc_html( $track['status'] ); ?></p><p class="hp-advisory-pill"><?php echo esc_html( $track['quotes'] ); ?></p></article>
			<?php endforeach; ?>
		</div>
	</div></section>

	<section class="hp-template-section hp-template-section--soft"><div class="hp-shell">
		<p class="hp-eyebrow"><?php esc_html_e( 'איך זה עובד', 'hea-lth-portal' ); ?></p>
		<h2><?php esc_html_e( 'תהליך העבודה שלנו עבורך', 'hea-lth-portal' ); ?></h2>
		<ol class="hp-advisory-steps">
			<?php foreach ( $advisory['process'] as $step ) : ?>
				<li><?php echo esc_html( $step ); ?></li>
			<?php endforeach; ?>
		</ol>
		<div class="hp-advisory-boundary">
			<p><?php esc_html_e( 'שקיפות: המידע העסקי בעמוד זה מבוסס על נתוני הספקים והאתרים הרשמיים שלהם; טווחי מחירים יוצגו אך ורק מתוך הצעות כתובות של הספקים. העמוד אינו מהווה ייעוץ רפואי, אבחון או המלצה טיפולית, ואינו תחליף לשיקול דעת מקצועי. הליווי לרוכש ניתן ללא עלות — Hea-lth מתוגמלת מהספקים במסגרת הסכמי תיווך גלויים.', 'hea-lth-portal' ); ?></p>
		</div>
		<div class="hp-advisory-cta-row">
			<a class="hp-button" href="<?php echo esc_url( 'https://wa.me/' . $whatsapp . '?text=' . $summary_text ); ?>" rel="noopener noreferrer"><?php esc_html_e( 'סיימתי לעבור — לשיחת סיכום', 'hea-lth-portal' ); ?></a>
		</div>
	</div></section>
	<?php
endif;

get_footer();
