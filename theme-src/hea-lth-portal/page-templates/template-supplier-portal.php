<?php
/**
 * Template Name: אזור הספקים
 * Template Post Type: page
 *
 * @package HeaLthPortal
 */

nocache_headers();
get_header();

$logged_in = is_user_logged_in();
$user      = $logged_in ? wp_get_current_user() : null;
$supplier  = $logged_in && class_exists( 'Hea_Lth_Supplier_Portal' ) ? Hea_Lth_Supplier_Portal::supplier_for_user() : null;
$status    = isset( $_GET['portal'] ) ? sanitize_key( wp_unslash( $_GET['portal'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display-only result flag.

$notices = array(
	'catalog-received' => array( 'success', 'בקשת העדכון התקבלה ותיכנס לבדיקה.' ),
	'catalog-required' => array( 'error', 'יש למלא את שם המוצר ואת תיאור הבקשה.' ),
	'catalog-error'    => array( 'error', 'לא הצלחנו לשמור את הבקשה. נסו שוב.' ),
	'pipeline-updated' => array( 'success', 'מצב הטיפול עודכן.' ),
	'terms-accepted'   => array( 'success', 'תנאי התיווך אושרו ונשמרו. צוות Hea-lth יכול להמשיך בהעברת פרטי ההתקשרות.' ),
);
?>
<div class="hp-supplier-portal">
	<section class="hp-supplier-portal__hero">
		<div class="hp-shell hp-supplier-portal__hero-grid">
			<div>
				<p class="hp-eyebrow hp-eyebrow--light"><?php esc_html_e( 'Hea-lth for Suppliers', 'hea-lth-portal' ); ?></p>
				<h1><?php esc_html_e( 'הבית העסקי של הספק שלכם', 'hea-lth-portal' ); ?></h1>
				<p><?php esc_html_e( 'ניהול אולם התצוגה, בקשות לעדכון הקטלוג והזדמנויות עסקיות שהוקצו לחברה — במקום אחד.', 'hea-lth-portal' ); ?></p>
			</div>
			<div class="hp-supplier-portal__seal">
				<span><?php echo $supplier instanceof WP_Post ? esc_html__( 'חשבון מקושר', 'hea-lth-portal' ) : esc_html__( 'גישה מאובטחת', 'hea-lth-portal' ); ?></span>
				<strong><?php echo $supplier instanceof WP_Post ? esc_html( get_the_title( $supplier ) ) : esc_html__( 'לספקים וליבואנים', 'hea-lth-portal' ); ?></strong>
			</div>
		</div>
	</section>

	<section class="hp-template-section hp-template-section--paper">
		<div class="hp-shell">
			<?php if ( isset( $notices[ $status ] ) ) : ?>
				<div class="hp-portal-notice hp-portal-notice--<?php echo esc_attr( $notices[ $status ][0] ); ?>" role="status"><?php echo esc_html( $notices[ $status ][1] ); ?></div>
			<?php endif; ?>

			<?php if ( ! $logged_in ) : ?>
				<div class="hp-account-entry hp-supplier-portal__entry">
					<div>
						<p class="hp-eyebrow"><?php esc_html_e( 'כניסת ספקים', 'hea-lth-portal' ); ?></p>
						<h2><?php esc_html_e( 'התחברו לחשבון החברה', 'hea-lth-portal' ); ?></h2>
						<p><?php esc_html_e( 'הגישה מיועדת לנציגים שקיבלו חיבור לכרטיס ספק פעיל ב-Hea-lth.', 'hea-lth-portal' ); ?></p>
					</div>
					<div class="hp-account-entry__actions">
						<a class="hp-button" href="<?php echo esc_url( wp_login_url( hea_lth_portal_foundation_route( 'supplier_portal' ) ) ); ?>"><?php esc_html_e( 'כניסה לחשבון', 'hea-lth-portal' ); ?></a>
						<a class="hp-inline-link" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'supplier_join' ) ); ?>"><?php esc_html_e( 'עדיין אין לכם כרטיס?', 'hea-lth-portal' ); ?><span aria-hidden="true">←</span></a>
					</div>
				</div>
			<?php elseif ( ! $supplier instanceof WP_Post ) : ?>
				<div class="hp-account-entry hp-supplier-portal__entry">
					<div>
						<p class="hp-eyebrow"><?php esc_html_e( 'חיבור החברה', 'hea-lth-portal' ); ?></p>
						<h2><?php esc_html_e( 'נחבר את החשבון לכרטיס הספק שלכם', 'hea-lth-portal' ); ?></h2>
						<p><?php esc_html_e( 'הגישו את פרטי החברה. לאחר ההתאמה, החשבון יקבל גישה לאולם התצוגה ולכלים העסקיים שלו.', 'hea-lth-portal' ); ?></p>
					</div>
					<a class="hp-button" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'supplier_join' ) ); ?>"><?php esc_html_e( 'הגשת פרטי החברה', 'hea-lth-portal' ); ?></a>
				</div>
			<?php else : ?>
				<?php
				$supplier_id  = (int) $supplier->ID;
				$plan         = Hea_Lth_Supplier_Portal::sanitize_plan( get_post_meta( $supplier_id, 'hp_membership_plan', true ) );
				$member_state = Hea_Lth_Supplier_Portal::sanitize_membership_state( get_post_meta( $supplier_id, 'hp_membership_state', true ) );
				$plans        = Hea_Lth_Supplier_Portal::plans();
				$states       = Hea_Lth_Supplier_Portal::membership_states();
				$requests     = Hea_Lth_Supplier_Portal::assigned_requests( $supplier_id );
				$submissions  = Hea_Lth_Supplier_Portal::catalog_submissions( $supplier_id );
				$equipment    = get_posts(
					array(
						'post_type'      => 'hp_equipment',
						'post_status'    => 'publish',
						'posts_per_page' => 100,
						'meta_key'       => 'hp_supplier_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Bounded supplier catalog count.
						'meta_value'     => (string) $supplier_id, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Bounded supplier catalog count.
						'fields'         => 'ids',
						'no_found_rows'  => true,
					)
				);
				?>
				<div class="hp-supplier-dashboard__welcome">
					<div>
						<p class="hp-eyebrow"><?php esc_html_e( 'שלום', 'hea-lth-portal' ); ?> <?php echo esc_html( $user->display_name ); ?></p>
						<h2><?php echo esc_html( get_the_title( $supplier ) ); ?></h2>
						<p><?php esc_html_e( 'מכאן מנהלים את הנוכחות המקצועית ואת הטיפול בהזדמנויות שהועברו לחברה.', 'hea-lth-portal' ); ?></p>
					</div>
					<a class="hp-button hp-button--secondary" href="<?php echo esc_url( get_permalink( $supplier ) ); ?>"><?php esc_html_e( 'צפייה באולם התצוגה', 'hea-lth-portal' ); ?></a>
				</div>

				<div class="hp-supplier-dashboard__stats" aria-label="מצב החשבון">
					<article><span><?php esc_html_e( 'מסלול', 'hea-lth-portal' ); ?></span><strong><?php echo esc_html( isset( $plans[ $plan ] ) ? $plans[ $plan ] : $plans['verified'] ); ?></strong></article>
					<article><span><?php esc_html_e( 'מצב', 'hea-lth-portal' ); ?></span><strong><?php echo esc_html( isset( $states[ $member_state ] ) ? $states[ $member_state ] : $states['pending'] ); ?></strong></article>
					<article><span><?php esc_html_e( 'מוצרים באולם', 'hea-lth-portal' ); ?></span><strong><?php echo esc_html( number_format_i18n( count( $equipment ) ) ); ?></strong></article>
					<article><span><?php esc_html_e( 'הזדמנויות', 'hea-lth-portal' ); ?></span><strong><?php echo esc_html( number_format_i18n( count( $requests ) ) ); ?></strong></article>
				</div>

				<div class="hp-supplier-dashboard__grid">
					<section class="hp-supplier-panel" aria-labelledby="hp-opportunities-title">
						<div class="hp-supplier-panel__heading">
							<div><p class="hp-eyebrow"><?php esc_html_e( 'עסקאות', 'hea-lth-portal' ); ?></p><h2 id="hp-opportunities-title"><?php esc_html_e( 'הזדמנויות שהוקצו לחברה', 'hea-lth-portal' ); ?></h2></div>
							<span><?php echo esc_html( number_format_i18n( count( $requests ) ) ); ?></span>
						</div>
						<?php if ( ! $requests ) : ?>
							<p class="hp-supplier-panel__empty"><?php esc_html_e( 'הזדמנויות מתאימות יופיעו כאן לאחר הקצאה לחברה.', 'hea-lth-portal' ); ?></p>
						<?php else : ?>
							<div class="hp-opportunity-list">
								<?php foreach ( $requests as $request ) : ?>
									<?php $lead = Hea_Lth_Supplier_Portal::request_view( $request, $supplier_id ); ?>
									<?php if ( ! $lead ) { continue; } ?>
									<article class="hp-opportunity-card">
										<div class="hp-opportunity-card__top"><strong><?php echo esc_html( $lead['company'] ? $lead['company'] : __( 'פנייה עסקית', 'hea-lth-portal' ) ); ?></strong><span><?php echo $lead['released'] ? esc_html__( 'פרטי קשר זמינים', 'hea-lth-portal' ) : esc_html__( 'בתיאום Hea-lth', 'hea-lth-portal' ); ?></span></div>
										<p><?php echo esc_html( $lead['city'] ); ?><?php echo $lead['stage'] ? ' · ' . esc_html( $lead['stage'] ) : ''; ?></p>
										<?php if ( class_exists( 'Hea_Lth_Brokerage_Ledger' ) && 'offered' === $lead['terms']['status'] ) : ?>
											<div class="hp-brokerage-terms">
												<p class="hp-eyebrow"><?php esc_html_e( 'תנאי התיווך להזדמנות זו', 'hea-lth-portal' ); ?></p>
												<strong><?php echo esc_html( Hea_Lth_Brokerage_Ledger::public_terms_summary( $lead['terms'] ) ); ?></strong>
												<p><?php echo esc_html( sprintf( 'חלון שיוך של %d ימים. העמלה חלה עם סגירת עסקה הנובעת מההיכרות, ובמהלך התקופה נשמרת התחייבות לאי־עקיפה ולעדכון Hea-lth בהתקדמות.', (int) $lead['terms']['attribution_days'] ) ); ?></p>
												<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
													<input type="hidden" name="action" value="hea_lth_accept_brokerage_terms"><input type="hidden" name="request_id" value="<?php echo (int) $lead['id']; ?>">
													<?php wp_nonce_field( 'hea_lth_accept_brokerage_terms', 'hea_lth_terms_nonce' ); ?>
													<label class="hp-b2b-consent"><input type="checkbox" name="terms_confirmed" value="1" required><span><?php esc_html_e( 'קראתי ואני מאשר/ת את תנאי התיווך והאי־עקיפה להזדמנות זו בשם החברה.', 'hea-lth-portal' ); ?></span></label>
													<button class="hp-button hp-button--small" type="submit"><?php esc_html_e( 'אישור התנאים', 'hea-lth-portal' ); ?></button>
												</form>
											</div>
										<?php elseif ( class_exists( 'Hea_Lth_Brokerage_Ledger' ) && 'accepted' === $lead['terms']['status'] ) : ?>
											<?php $agreement = class_exists( 'Hea_Lth_Brokerage_Agreement' ) ? Hea_Lth_Brokerage_Agreement::latest_document( (int) $lead['id'] ) : array(); ?>
											<div class="hp-brokerage-accepted"><strong><?php esc_html_e( 'תנאי התיווך אושרו ותועדו', 'hea-lth-portal' ); ?></strong><span><?php echo esc_html( Hea_Lth_Brokerage_Ledger::public_terms_summary( $lead['terms'] ) ); ?></span><?php if ( $agreement ) : ?><a href="<?php echo esc_url( Hea_Lth_Brokerage_Agreement::download_url( (int) $lead['id'], $agreement['document_id'] ) ); ?>"><?php esc_html_e( 'הורדת מסמך האישור', 'hea-lth-portal' ); ?></a><?php endif; ?></div>
										<?php else : ?>
											<p class="hp-opportunity-card__held"><?php esc_html_e( 'צוות Hea-lth מתאם את תנאי ההתקשרות להזדמנות זו.', 'hea-lth-portal' ); ?></p>
										<?php endif; ?>
										<?php if ( $lead['released'] ) : ?>
											<div class="hp-opportunity-card__contact"><strong><?php echo esc_html( $lead['contact_name'] ); ?></strong><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $lead['contact_phone'] ) ); ?>"><?php echo esc_html( $lead['contact_phone'] ); ?></a><a href="mailto:<?php echo esc_attr( $lead['contact_email'] ); ?>"><?php echo esc_html( $lead['contact_email'] ); ?></a></div>
										<?php else : ?>
											<p class="hp-opportunity-card__held"><?php esc_html_e( 'פרטי ההתקשרות יופיעו כאן לאחר השלמת התיאום העסקי.', 'hea-lth-portal' ); ?></p>
										<?php endif; ?>
										<form class="hp-pipeline-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
											<input type="hidden" name="action" value="hea_lth_supplier_pipeline"><input type="hidden" name="request_id" value="<?php echo (int) $lead['id']; ?>">
											<?php wp_nonce_field( 'hea_lth_supplier_pipeline', 'hea_lth_pipeline_nonce' ); ?>
											<label><span><?php esc_html_e( 'מצב טיפול', 'hea-lth-portal' ); ?></span><select name="pipeline_status">
												<?php foreach ( Hea_Lth_Supplier_Portal::pipeline_states() as $key => $label ) : ?><option value="<?php echo esc_attr( $key ); ?>" <?php selected( $lead['status'], $key ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?>
											</select></label><button class="hp-button hp-button--small" type="submit"><?php esc_html_e( 'שמירה', 'hea-lth-portal' ); ?></button>
										</form>
									</article>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</section>

					<section class="hp-supplier-panel" aria-labelledby="hp-catalog-title">
						<div class="hp-supplier-panel__heading"><div><p class="hp-eyebrow"><?php esc_html_e( 'אולם התצוגה', 'hea-lth-portal' ); ?></p><h2 id="hp-catalog-title"><?php esc_html_e( 'בקשת הוספה או עדכון', 'hea-lth-portal' ); ?></h2></div></div>
						<p><?php esc_html_e( 'שלחו מפרט מסודר. צוות Hea-lth יבדוק אותו ויעדכן את אולם התצוגה לאחר האישור.', 'hea-lth-portal' ); ?></p>
						<form class="hp-catalog-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<input type="hidden" name="action" value="hea_lth_catalog_submission">
							<?php wp_nonce_field( 'hea_lth_catalog_submission', 'hea_lth_supplier_nonce' ); ?>
							<label><span><?php esc_html_e( 'סוג הבקשה', 'hea-lth-portal' ); ?></span><select name="submission_kind"><option value="new_product"><?php esc_html_e( 'הוספת מוצר', 'hea-lth-portal' ); ?></option><option value="product_update"><?php esc_html_e( 'עדכון מוצר קיים', 'hea-lth-portal' ); ?></option><option value="company_update"><?php esc_html_e( 'עדכון פרטי החברה', 'hea-lth-portal' ); ?></option></select></label>
							<label><span><?php esc_html_e( 'שם המוצר או הנושא', 'hea-lth-portal' ); ?> *</span><input type="text" name="product_name" required maxlength="160"></label>
							<div class="hp-catalog-form__row"><label><span><?php esc_html_e( 'טכנולוגיה', 'hea-lth-portal' ); ?></span><input type="text" name="technology" maxlength="120"></label><label><span><?php esc_html_e( 'משפחת מוצרים', 'hea-lth-portal' ); ?></span><input type="text" name="product_family" maxlength="120"></label></div>
							<label><span><?php esc_html_e( 'קישור למפרט הרשמי', 'hea-lth-portal' ); ?></span><input type="url" name="source_url"></label>
							<label><span><?php esc_html_e( 'מה תרצו להציג או לשנות?', 'hea-lth-portal' ); ?> *</span><textarea name="submission_notes" required rows="5" maxlength="3000"></textarea></label>
							<button class="hp-button" type="submit"><?php esc_html_e( 'שליחה לבדיקה', 'hea-lth-portal' ); ?></button>
						</form>

						<?php if ( $submissions ) : ?>
							<div class="hp-submission-history"><h3><?php esc_html_e( 'בקשות אחרונות', 'hea-lth-portal' ); ?></h3><ul>
								<?php foreach ( array_slice( $submissions, 0, 8 ) as $submission ) : ?><li><span><?php echo esc_html( get_post_meta( (int) $submission->ID, 'hp_product_name', true ) ); ?></span><strong><?php echo esc_html( Hea_Lth_Supplier_Portal::submission_status_label( get_post_meta( (int) $submission->ID, 'hp_submission_status', true ) ) ); ?></strong></li><?php endforeach; ?>
							</ul></div>
						<?php endif; ?>
					</section>
				</div>
			<?php endif; ?>
		</div>
	</section>
</div>
<?php get_footer();
