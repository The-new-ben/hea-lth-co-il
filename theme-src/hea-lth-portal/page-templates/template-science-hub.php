<?php
/**
 * Template Name: מרכז ידע מדעי
 * Template Post Type: page
 *
 * @package HeaLthPortal
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		$domain  = sanitize_key( (string) get_post_meta( get_the_ID(), 'hp_science_domain', true ) );
		$node    = class_exists( 'Hea_Lth_Knowledge_Graph' ) ? Hea_Lth_Knowledge_Graph::node( $domain ) : array();
		$nodes   = class_exists( 'Hea_Lth_Knowledge_Graph' ) ? Hea_Lth_Knowledge_Graph::nodes() : array();
		$bridges = class_exists( 'Hea_Lth_Knowledge_Graph' ) ? Hea_Lth_Knowledge_Graph::bridges() : array();
		$reviewed = (string) get_post_meta( get_the_ID(), 'hp_last_reviewed', true );

		if ( empty( $node ) ) {
			continue;
		}

		$resolve_route = static function ( $bridge ) {
			if ( ! is_array( $bridge ) || empty( $bridge['route_key'] ) ) {
				return home_url( '/' );
			}

			return isset( $bridge['registry'] ) && 'canonical' === $bridge['registry']
				? hea_lth_portal_route( $bridge['route_key'] )
				: hea_lth_portal_foundation_route( $bridge['route_key'] );
		};
		?>
		<section class="hp-page-hero hp-page-hero--technology">
			<div class="hp-shell hp-template-hero-grid">
				<div>
					<p class="hp-eyebrow hp-eyebrow--light"><?php echo esc_html( $node['eyebrow'] ); ?></p>
					<h1><?php echo esc_html( $node['title'] ); ?></h1>
					<p><?php echo esc_html( $node['summary'] ); ?></p>
				</div>
				<div class="hp-technology-hero-core" aria-hidden="true"><span>SCI</span><i></i><i></i><i></i></div>
			</div>
		</section>

		<section class="hp-template-section hp-template-section--paper">
			<div class="hp-reading-layout hp-reading-layout--technology">
				<div>
					<p class="hp-eyebrow"><?php esc_html_e( 'מה המרכז מסביר', 'hea-lth-portal' ); ?></p>
					<h2><?php esc_html_e( 'מסגרת ידע לפני בחירת בדיקה, טיפול או טכנולוגיה', 'hea-lth-portal' ); ?></h2>
					<ul>
						<?php foreach ( $node['focus'] as $focus ) : ?>
							<li><?php echo esc_html( $focus ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<aside class="hp-reading-layout__rail" aria-label="<?php esc_attr_e( 'בקרת מקורות', 'hea-lth-portal' ); ?>">
					<div class="hp-catalog-gate">
						<div>
							<p class="hp-eyebrow"><?php esc_html_e( 'בקרת ראיות', 'hea-lth-portal' ); ?></p>
							<h2><?php esc_html_e( 'מקורות ותאריך בדיקה', 'hea-lth-portal' ); ?></h2>
							<?php if ( '' !== $reviewed ) : ?>
								<p><?php echo esc_html( sprintf( 'נבדק לאחרונה: %s', $reviewed ) ); ?></p>
							<?php endif; ?>
							<ul>
								<?php foreach ( $node['sources'] as $source ) : ?>
									<li><a href="<?php echo esc_url( $source['url'] ); ?>" rel="external noopener"><?php echo esc_html( $source['label'] ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				</aside>
			</div>
		</section>

		<?php if ( ! empty( $node['children'] ) ) : ?>
			<section class="hp-template-section hp-template-section--soft">
				<div class="hp-shell">
					<div class="hp-section-heading">
						<p class="hp-eyebrow"><?php esc_html_e( 'מפת המנגנונים', 'hea-lth-portal' ); ?></p>
						<h2><?php esc_html_e( 'שכבות הידע במרכז', 'hea-lth-portal' ); ?></h2>
					</div>
					<div class="hp-technology-path-grid">
						<?php foreach ( $node['children'] as $index => $child_id ) : ?>
							<?php $child = isset( $nodes[ $child_id ] ) ? $nodes[ $child_id ] : array(); ?>
							<?php if ( ! empty( $child ) ) : ?>
								<a href="<?php echo esc_url( hea_lth_portal_foundation_route( $child['route_key'] ) ); ?>">
									<span><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
									<h3><?php echo esc_html( $child['title'] ); ?></h3>
									<p><?php echo esc_html( $child['summary'] ); ?></p>
									<b aria-hidden="true">←</b>
								</a>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<section class="hp-template-section hp-template-section--paper">
			<div class="hp-shell">
				<div class="hp-section-heading">
					<p class="hp-eyebrow"><?php esc_html_e( 'מהידע לבחירה', 'hea-lth-portal' ); ?></p>
					<h2><?php esc_html_e( 'המשך לשירותים, מדידה וטכנולוגיה', 'hea-lth-portal' ); ?></h2>
					<p><?php esc_html_e( 'מכאן אפשר להמשיך למידע מעשי על בדיקות, שירותים, אנשי מקצוע וטכנולוגיות רלוונטיות.', 'hea-lth-portal' ); ?></p>
				</div>
				<div class="hp-technology-path-grid">
					<?php foreach ( $node['bridges'] as $index => $bridge_id ) : ?>
						<?php $bridge = isset( $bridges[ $bridge_id ] ) ? $bridges[ $bridge_id ] : array(); ?>
						<?php if ( ! empty( $bridge ) ) : ?>
							<a href="<?php echo esc_url( $resolve_route( $bridge ) ); ?>">
								<span><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
								<h3><?php echo esc_html( $bridge['label'] ); ?></h3>
								<p><?php echo esc_html( $bridge['copy'] ); ?></p>
								<b aria-hidden="true">←</b>
							</a>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	endwhile;
endif;

get_footer();
