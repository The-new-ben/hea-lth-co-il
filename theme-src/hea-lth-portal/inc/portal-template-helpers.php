<?php
/**
 * Small presentation helpers shared by the portal templates.
 *
 * These helpers never create provider data, medical claims, or operational
 * records. They only render public presentation boundaries around WordPress
 * content that has already been approved for publication.
 *
 * @package HeaLthPortal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return a usable page title when an editor has not supplied one yet.
 *
 * @param string $fallback Fallback title.
 * @return string
 */
function hea_lth_portal_current_title( $fallback ) {
	$title = get_the_title();

	return $title ? $title : $fallback;
}

/**
 * Render page body content only if the editor supplied content.
 *
 * @return bool Whether a content body was rendered.
 */
function hea_lth_portal_render_current_content() {
	$content = trim( (string) get_the_content() );

	if ( '' === wp_strip_all_tags( $content ) ) {
		return false;
	}

	echo '<div class="hp-prose">';
	the_content();
	echo '</div>';

	return true;
}

/**
 * Render the reusable safe-information boundary.
 *
 * @param bool $show_emergency Whether to include emergency guidance.
 * @return void
 */
function hea_lth_portal_render_information_boundary( $show_emergency = false ) {
	?>
	<aside class="hp-information-boundary" aria-label="<?php esc_attr_e( 'גבולות המידע באתר', 'hea-lth-portal' ); ?>">
		<span class="hp-information-boundary__number" aria-hidden="true">i</span>
		<div>
			<strong><?php esc_html_e( 'המידע נועד להבנה ולהכנה לשיחה', 'hea-lth-portal' ); ?></strong>
			<p><?php esc_html_e( 'הוא אינו מחליף ייעוץ, אבחון או החלטה טיפולית אישית. כל החלטה רפואית מתקבלת עם איש או אשת מקצוע מוסמכים.', 'hea-lth-portal' ); ?></p>
			<?php if ( $show_emergency ) : ?>
				<p><?php esc_html_e( 'במקרה חירום יש לפנות למגן דוד אדום 101 או לחדר מיון.', 'hea-lth-portal' ); ?></p>
			<?php endif; ?>
		</div>
	</aside>
	<?php
}

/**
 * Render a simple numbered path that remains descriptive rather than clinical.
 *
 * @param array $steps Ordered step labels and descriptions.
 * @return void
 */
function hea_lth_portal_render_path_steps( $steps ) {
	?>
	<ol class="hp-path-steps">
		<?php foreach ( $steps as $index => $step ) : ?>
			<li>
				<span><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
				<h3><?php echo esc_html( $step['title'] ); ?></h3>
				<p><?php echo esc_html( $step['copy'] ); ?></p>
			</li>
		<?php endforeach; ?>
	</ol>
	<?php
}

/**
 * Read only the controlled directory filters from the current request.
 *
 * These values are discovery filters, not medical information. They share the
 * same names used by the verified-directory REST endpoint and are deliberately
 * restricted to taxonomy slugs.
 *
 * @return array<string, string>
 */
function hea_lth_portal_get_directory_context() {
	$filters = array();
	$keys    = array( 'specialty', 'region', 'service', 'body_region' );

	foreach ( $keys as $key ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public GET filters do not mutate state.
		if ( ! isset( $_GET[ $key ] ) || is_array( $_GET[ $key ] ) ) {
			continue;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public GET filters do not mutate state.
		$value = sanitize_title( wp_unslash( (string) $_GET[ $key ] ) );
		if ( '' !== $value ) {
			$filters[ $key ] = $value;
		}
	}

	return $filters;
}

/**
 * Return the explicit review gate shared by all medical and glossary feeds.
 *
 * A published WordPress status alone is not enough for a Hea-lth editorial
 * card. The record must have an approved state, an actual review date, and a
 * public source note. This keeps legacy URLs intact while preventing a broad
 * feed from making unreviewed medical content look endorsed.
 *
 * @return array<int|string, array<string, string>|string>
 */
function hea_lth_portal_reviewed_content_meta_query() {
	return array(
		'relation' => 'AND',
		array(
			'key'   => 'hp_editorial_state',
			'value' => 'approved',
		),
		array(
			'key'     => 'hp_last_reviewed',
			'value'   => '',
			'compare' => '!=',
		),
		array(
			'key'     => 'hp_source_note',
			'value'   => '',
			'compare' => '!=',
		),
	);
}

/**
 * Query published WordPress posts that have completed Hea-lth editorial
 * approval. The query intentionally retains existing post URLs rather than
 * inventing a parallel content route.
 *
 * @param int $limit Maximum number of cards to return.
 * @return WP_Query
 */
function hea_lth_portal_get_reviewed_guides( $limit = 3 ) {
	return new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => min( 12, max( 1, absint( $limit ) ) ),
			'orderby'                => 'modified',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => true,
			'meta_query'             => hea_lth_portal_reviewed_content_meta_query(),
		)
	);
}

/**
 * Query existing glossary-category posts that completed the same review gate.
 *
 * Glossary records preserve the legacy post URLs during migration. The newer
 * internal hp_glossary type deliberately has no public URL until inventory,
 * content equivalence, and redirect evidence are approved.
 *
 * @param int $limit Maximum number of cards to return.
 * @return WP_Query
 */
function hea_lth_portal_get_reviewed_glossary_terms( $limit = 18 ) {
	return new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => min( 30, max( 1, absint( $limit ) ) ),
			'category_name'          => 'glossary',
			'orderby'                => 'modified',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => true,
			'meta_query'             => hea_lth_portal_reviewed_content_meta_query(),
		)
	);
}

/**
 * Return the public-facing review state for one editorial record.
 *
 * Existing legacy URLs remain reachable during migration. This helper lets a
 * single article be truthful about whether it has the three fields required
 * to appear in a reviewed feed, rather than implying a review that has not
 * occurred.
 *
 * @param int $post_id WordPress post ID, or current post when empty.
 * @return array{is_reviewed:bool,last_reviewed:string,source_note:string}
 */
function hea_lth_portal_get_editorial_status( $post_id = 0 ) {
	$post_id       = absint( $post_id ? $post_id : get_the_ID() );
	$editorial     = (string) get_post_meta( $post_id, 'hp_editorial_state', true );
	$last_reviewed = (string) get_post_meta( $post_id, 'hp_last_reviewed', true );
	$source_note   = (string) get_post_meta( $post_id, 'hp_source_note', true );

	return array(
		'is_reviewed'  => 'approved' === $editorial && '' !== $last_reviewed && '' !== $source_note,
		'last_reviewed' => $last_reviewed,
		'source_note'   => $source_note,
	);
}

/**
 * Render one reviewed guide card from the current WordPress loop.
 *
 * @return void
 */
function hea_lth_portal_render_reviewed_guide_card() {
	$permalink    = get_permalink();
	$title        = get_the_title();
	$reviewed_date = (string) get_post_meta( get_the_ID(), 'hp_last_reviewed', true );
	$source_note   = (string) get_post_meta( get_the_ID(), 'hp_source_note', true );
	$excerpt       = trim( (string) get_the_excerpt() );
	?>
	<article class="hp-journal-card hp-journal-card--reviewed">
		<p><?php esc_html_e( 'מדריך שנבדק', 'hea-lth-portal' ); ?></p>
		<h3><a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a></h3>
		<span class="hp-journal-card__line" aria-hidden="true"></span>
		<div>
			<?php if ( $excerpt ) : ?>
				<p><?php echo esc_html( wp_trim_words( $excerpt, 28 ) ); ?></p>
			<?php endif; ?>
			<dl class="hp-journal-card__evidence">
				<dt><?php esc_html_e( 'עודכן', 'hea-lth-portal' ); ?></dt>
				<dd><?php echo esc_html( $reviewed_date ); ?></dd>
				<dt><?php esc_html_e( 'מקור', 'hea-lth-portal' ); ?></dt>
				<dd><?php echo esc_html( $source_note ); ?></dd>
			</dl>
			<a href="<?php echo esc_url( $permalink ); ?>"><?php esc_html_e( 'פתיחת המדריך', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a>
		</div>
	</article>
	<?php
}
