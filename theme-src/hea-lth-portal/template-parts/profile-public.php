<?php
/**
 * Public profile presentation for a verified provider or clinic.
 *
 * The platform content model stays private until a release decision makes
 * routes public. If that later happens, this template exposes only the typed
 * public fields already governed by the platform core. It never reads lead,
 * commercial, consent, payment, contact-owner, or route configuration data.
 *
 * @package HeaLthPortal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$profile_id   = get_queried_object_id();
$profile_type = get_post_type( $profile_id );

if ( ! in_array( $profile_type, array( 'hp_provider', 'hp_clinic' ), true ) || 'verified' !== get_post_meta( $profile_id, 'hp_public_state', true ) ) {
	status_header( 404 );
	nocache_headers();
	?>
	<section class="hp-template-section hp-template-section--paper">
		<div class="hp-shell hp-profile-not-found">
			<p class="hp-eyebrow"><?php esc_html_e( 'פרופיל אינו זמין', 'hea-lth-portal' ); ?></p>
			<h1><?php esc_html_e( 'לא מצאנו פרופיל זמין להצגה', 'hea-lth-portal' ); ?></h1>
			<p><?php esc_html_e( 'אפשר להמשיך לאינדקס ולחפש בין רשומות מאומתות אחרות.', 'hea-lth-portal' ); ?></p>
			<a class="hp-button" href="<?php echo esc_url( hea_lth_portal_route( 'doctor_clinic_index' ) ); ?>"><?php esc_html_e( 'פתיחת האינדקס', 'hea-lth-portal' ); ?></a>
		</div>
	</section>
	<?php
	return;
}

$profile_title      = get_the_title( $profile_id );
$profile_kind       = 'hp_clinic' === $profile_type ? __( 'מרפאה או ארגון', 'hea-lth-portal' ) : __( 'מקצוען בריאות', 'hea-lth-portal' );
$city               = (string) get_post_meta( $profile_id, 'hp_city', true );
$languages          = get_post_meta( $profile_id, 'hp_languages', true );
$accessibility      = get_post_meta( $profile_id, 'hp_accessibility', true );
$last_verified      = (string) get_post_meta( $profile_id, 'hp_last_verified', true );
$public_disclosure  = (string) get_post_meta( $profile_id, 'hp_public_disclosure', true );
$specialties        = get_the_terms( $profile_id, 'hp_specialty' );
$regions            = get_the_terms( $profile_id, 'hp_region' );
$service_types      = get_the_terms( $profile_id, 'hp_service_type' );
$body_regions       = get_the_terms( $profile_id, 'hp_body_region' );
$content            = trim( (string) get_the_content( null, false, $profile_id ) );
$language_list      = is_array( $languages ) ? array_filter( array_map( 'sanitize_text_field', $languages ) ) : array();
$accessibility_list = is_array( $accessibility ) ? array_filter( array_map( 'sanitize_text_field', $accessibility ) ) : array();
$topic_groups       = array(
	array( 'label' => __( 'תחומי מומחיות', 'hea-lth-portal' ), 'terms' => $specialties ),
	array( 'label' => __( 'סוגי שירות', 'hea-lth-portal' ), 'terms' => $service_types ),
	array( 'label' => __( 'אזורי גוף', 'hea-lth-portal' ), 'terms' => $body_regions ),
	array( 'label' => __( 'אזורי שירות', 'hea-lth-portal' ), 'terms' => $regions ),
);

$facts = array_filter(
	array(
		__( 'עיר', 'hea-lth-portal' )        => $city,
		__( 'שפות', 'hea-lth-portal' )       => implode( ', ', $language_list ),
		__( 'נגישות', 'hea-lth-portal' )     => implode( ', ', $accessibility_list ),
	)
);
?>

<section class="hp-page-hero hp-page-hero--profile">
	<div class="hp-shell hp-profile-hero">
		<div class="hp-profile-hero__identity">
			<a class="hp-profile-hero__back" href="<?php echo esc_url( hea_lth_portal_route( 'doctor_clinic_index' ) ); ?>"><span aria-hidden="true">→</span><?php esc_html_e( 'חזרה לאינדקס', 'hea-lth-portal' ); ?></a>
			<div class="hp-profile-identity">
				<div class="hp-profile-identity__image">
					<?php if ( has_post_thumbnail( $profile_id ) ) : ?>
						<?php echo get_the_post_thumbnail( $profile_id, 'medium_large', array( 'class' => 'hp-profile-identity__photo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php else : ?>
						<span class="hp-profile-identity__mark" aria-hidden="true">H</span>
					<?php endif; ?>
				</div>
				<div>
					<p class="hp-eyebrow hp-eyebrow--light"><?php echo esc_html( $profile_kind ); ?></p>
					<h1><?php echo esc_html( $profile_title ); ?></h1>
					<p class="hp-profile-hero__verified"><span aria-hidden="true">✓</span><?php esc_html_e( 'פרופיל מאומת', 'hea-lth-portal' ); ?></p>
				</div>
			</div>
		</div>
		<div class="hp-profile-hero__proof" aria-label="<?php esc_attr_e( 'פרטי אימות הפרופיל', 'hea-lth-portal' ); ?>">
			<span><?php esc_html_e( 'אימות', 'hea-lth-portal' ); ?></span>
			<strong><?php esc_html_e( 'נתוני הפרופיל הוגדרו להצגה מבוקרת', 'hea-lth-portal' ); ?></strong>
			<?php if ( '' !== $last_verified ) : ?>
				<small><?php esc_html_e( 'עדכון אחרון:', 'hea-lth-portal' ); ?> <time datetime="<?php echo esc_attr( $last_verified ); ?>"><?php echo esc_html( $last_verified ); ?></time></small>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="hp-template-section hp-template-section--paper">
	<div class="hp-profile-layout">
		<article class="hp-profile-content">
			<?php if ( ! empty( $facts ) ) : ?>
				<dl class="hp-profile-facts">
					<?php foreach ( $facts as $label => $value ) : ?>
						<div><dt><?php echo esc_html( $label ); ?></dt><dd><?php echo esc_html( $value ); ?></dd></div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>

			<?php foreach ( $topic_groups as $group ) : ?>
				<?php if ( is_array( $group['terms'] ) && ! is_wp_error( $group['terms'] ) && ! empty( $group['terms'] ) ) : ?>
					<section class="hp-profile-topics">
						<h2><?php echo esc_html( $group['label'] ); ?></h2>
						<ul>
							<?php foreach ( $group['terms'] as $term ) : ?>
								<?php if ( $term instanceof WP_Term ) : ?>
									<li><?php echo esc_html( $term->name ); ?></li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>
			<?php endforeach; ?>

			<?php if ( '' !== wp_strip_all_tags( $content ) ) : ?>
				<section class="hp-profile-about">
					<p class="hp-eyebrow"><?php esc_html_e( 'על הפרופיל', 'hea-lth-portal' ); ?></p>
					<div class="hp-prose"><?php echo apply_filters( 'the_content', $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				</section>
			<?php endif; ?>

			<?php if ( '' !== $public_disclosure ) : ?>
				<section class="hp-profile-disclosure">
					<h2><?php esc_html_e( 'גילוי נאות', 'hea-lth-portal' ); ?></h2>
					<?php echo wpautop( esc_html( $public_disclosure ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</section>
			<?php endif; ?>
		</article>

		<aside class="hp-profile-rail">
			<section class="hp-profile-cta">
				<p class="hp-eyebrow"><?php esc_html_e( 'המשך הבירור', 'hea-lth-portal' ); ?></p>
				<h2><?php esc_html_e( 'אפשר להמשיך להשוות מסלולים ומקצוענים', 'hea-lth-portal' ); ?></h2>
				<p><?php esc_html_e( 'המעבר הבא מתחיל בבחירת תחום או שירות, בלי לשלוח מידע רפואי אישי דרך עמוד זה.', 'hea-lth-portal' ); ?></p>
				<a class="hp-button" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'find_care' ) ); ?>"><?php esc_html_e( 'בדיקת אפשרויות להמשך', 'hea-lth-portal' ); ?></a>
			</section>
			<?php hea_lth_portal_render_information_boundary( true ); ?>
		</aside>
	</div>
</section>
