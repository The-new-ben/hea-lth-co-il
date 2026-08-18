<?php
/**
 * Template Name: חדר ייעוץ אישי
 * Template Post Type: page
 *
 * Code-gated private advisory room. Unlocks either through the native
 * WordPress post password or a matching ?code= query value, so the owner
 * can hand a client a one-click link. Room content comes from
 * Hea_Lth_Advisory_Rooms — never from post content — and equipment cards
 * render only records whose editorial state is approved.
 *
 * @package HeaLthPortal
 */

nocache_headers();

$room_page_id = get_the_ID();
$advisory     = class_exists( 'Hea_Lth_Advisory_Rooms' ) ? Hea_Lth_Advisory_Rooms::room_for_page( $room_page_id ) : null;
$given_code   = isset( $_GET['code'] ) ? sanitize_text_field( wp_unslash( $_GET['code'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Access code for a read-only gated brief; compared with hash_equals.
$code_valid   = $advisory && '' !== $given_code && Hea_Lth_Advisory_Rooms::code_matches( $advisory, $given_code );
$unlocked     = $advisory && ( $code_valid || ! post_password_required() );

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
		<p><?php esc_html_e( 'העמוד מיועד ללקוח מלווה של Hea-lth ומוגן בקוד גישה אישי.', 'hea-lth-portal' ); ?></p>
	</div></section>
	<section class="hp-template-section hp-template-section--paper"><div class="hp-shell hp-advisory-gate">
		<h2><?php esc_html_e( 'הזינו קוד גישה', 'hea-lth-portal' ); ?></h2>
		<p><?php esc_html_e( 'הקוד האישי שלך: מספר הנייד שלך, ספרות בלבד וללא מקפים.', 'hea-lth-portal' ); ?></p>
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
else :
	?>
	<section class="hp-advisory-hero"><div class="hp-shell">
		<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'אזור ייעוץ אישי — Hea-lth', 'hea-lth-portal' ); ?></p>
		<h1><?php echo esc_html( $advisory['title'] ); ?></h1>
		<p class="hp-advisory-hero__client"><?php echo esc_html( sprintf( __( 'הוכן עבור %s', 'hea-lth-portal' ), $advisory['client'] ) ); ?></p>
		<p><?php echo esc_html( $advisory['intro'] ); ?></p>
	</div></section>

	<section class="hp-template-section hp-template-section--paper"><div class="hp-shell">
		<p class="hp-eyebrow"><?php esc_html_e( 'הדרישות שהוגדרו', 'hea-lth-portal' ); ?></p>
		<h2><?php esc_html_e( 'מה חיפשנו עבורך', 'hea-lth-portal' ); ?></h2>
		<div class="hp-advisory-grid">
			<?php foreach ( $advisory['needs'] as $need ) : ?>
				<article class="hp-advisory-card"><h3><?php echo esc_html( $need['title'] ); ?></h3><p><?php echo esc_html( $need['copy'] ); ?></p></article>
			<?php endforeach; ?>
		</div>
	</div></section>

	<section class="hp-template-section hp-template-section--soft"><div class="hp-shell">
		<p class="hp-eyebrow"><?php esc_html_e( 'מסלולי הספקים', 'hea-lth-portal' ); ?></p>
		<h2><?php esc_html_e( 'הספקים שגויסו לתהליך שלך', 'hea-lth-portal' ); ?></h2>
		<p class="hp-advisory-note"><?php esc_html_e( 'הצעות המחיר נאספות במרוכז דרכנו ויוצגו בהשוואה מסודרת — ללא שיחות מכירה ישירות אליך.', 'hea-lth-portal' ); ?></p>
		<div class="hp-advisory-grid hp-advisory-grid--three">
			<?php foreach ( $advisory['suppliers'] as $track ) : ?>
				<article class="hp-advisory-card"><h3><?php echo esc_html( $track['name'] ); ?></h3><p class="hp-advisory-card__role"><?php echo esc_html( $track['role'] ); ?></p><p><?php echo esc_html( $track['status'] ); ?></p></article>
			<?php endforeach; ?>
		</div>
	</div></section>

	<?php foreach ( $advisory['equipment'] as $group ) : ?>
	<section class="hp-template-section"><div class="hp-shell">
		<p class="hp-eyebrow"><?php esc_html_e( 'מהקטלוג המקצועי', 'hea-lth-portal' ); ?></p>
		<h2><?php echo esc_html( $group['label'] ); ?></h2>
		<div class="hp-advisory-grid hp-advisory-grid--three">
			<?php
			foreach ( $group['slugs'] as $slug ) :
				$machine = get_page_by_path( $slug, OBJECT, 'hp_equipment' );
				if ( ! $machine || 'publish' !== $machine->post_status || 'approved' !== get_post_meta( $machine->ID, 'hp_editorial_state', true ) ) {
					continue;
				}
				$technology = (string) get_post_meta( $machine->ID, 'hp_technology', true );
				$family     = (string) get_post_meta( $machine->ID, 'hp_product_family', true );
				$supplier   = (int) get_post_meta( $machine->ID, 'hp_supplier_id', true );
				?>
				<article class="hp-advisory-card hp-advisory-card--machine">
					<h3><?php echo esc_html( get_the_title( $machine ) ); ?></h3>
					<?php if ( '' !== $family ) : ?><p class="hp-advisory-card__role"><?php echo esc_html( $family ); ?></p><?php endif; ?>
					<?php if ( '' !== $technology ) : ?><p><?php echo esc_html( $technology ); ?></p><?php endif; ?>
					<?php if ( $supplier ) : ?><p class="hp-advisory-card__supplier"><?php echo esc_html( sprintf( __( 'ספק: %s', 'hea-lth-portal' ), get_the_title( $supplier ) ) ); ?></p><?php endif; ?>
					<a class="hp-inline-link" href="<?php echo esc_url( get_permalink( $machine ) ); ?>"><?php esc_html_e( 'פרטי המערכת', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a>
				</article>
			<?php endforeach; ?>
		</div>
	</div></section>
	<?php endforeach; ?>

	<section class="hp-template-section hp-template-section--soft"><div class="hp-shell">
		<p class="hp-eyebrow"><?php esc_html_e( 'איך זה עובד', 'hea-lth-portal' ); ?></p>
		<h2><?php esc_html_e( 'תהליך העבודה שלנו עבורך', 'hea-lth-portal' ); ?></h2>
		<ol class="hp-advisory-steps">
			<?php foreach ( $advisory['process'] as $step ) : ?>
				<li><?php echo esc_html( $step ); ?></li>
			<?php endforeach; ?>
		</ol>
		<div class="hp-advisory-boundary">
			<p><?php esc_html_e( 'שקיפות: המידע העסקי בעמוד זה מבוסס על נתוני הספקים והאתרים הרשמיים שלהם, ומחירים סופיים ייקבעו אך ורק בהצעות מחיר רשמיות מהספקים. העמוד אינו מהווה ייעוץ רפואי, אבחון או המלצה טיפולית, ואינו תחליף לשיקול דעת מקצועי.', 'hea-lth-portal' ); ?></p>
		</div>
		<a class="hp-button" href="https://wa.me/972525101555" rel="noopener noreferrer"><?php esc_html_e( 'דברו איתנו בוואטסאפ', 'hea-lth-portal' ); ?></a>
	</div></section>
	<?php
endif;

get_footer();
