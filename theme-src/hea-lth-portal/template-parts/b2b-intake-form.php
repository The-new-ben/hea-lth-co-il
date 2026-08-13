<?php
/** Reusable clinic procurement / supplier onboarding form. @package HeaLthPortal */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$form_type   = isset( $args['type'] ) && 'supplier_join' === $args['type'] ? 'supplier_join' : 'clinic_quote';
$context     = isset( $args['context'] ) ? sanitize_key( $args['context'] ) : '';
$return_url  = isset( $args['return_url'] ) ? esc_url_raw( $args['return_url'] ) : get_permalink();
$selected_plan = isset( $args['selected_plan'] ) ? sanitize_key( $args['selected_plan'] ) : 'showroom';
$status      = isset( $_GET['request'] ) ? sanitize_key( wp_unslash( $_GET['request'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$categories  = array(
	'consultation-assessment' => __( 'הערכה ומדידה', 'hea-lth-portal' ),
	'body-contouring'         => __( 'עיצוב גוף וחיזוק שרירים', 'hea-lth-portal' ),
	'skin-analysis'           => __( 'אבחון עור', 'hea-lth-portal' ),
	'skin-treatment'          => __( 'טכנולוגיות עור ופנים', 'hea-lth-portal' ),
	'hair-removal'            => __( 'הסרת שיער', 'hea-lth-portal' ),
	'pelvic-floor'            => __( 'רצפת אגן', 'hea-lth-portal' ),
	'clinic-software'         => __( 'ניהול מרפאה ותוכנה', 'hea-lth-portal' ),
	'room-infrastructure'     => __( 'חדרי טיפול ותשתיות', 'hea-lth-portal' ),
	'consumables-service'     => __( 'מתכלים, הדרכה ושירות', 'hea-lth-portal' ),
	'finance-growth'          => __( 'מימון וצמיחה', 'hea-lth-portal' ),
);
?>
<div class="hp-b2b-form-wrap" id="quote">
	<?php if ( 'received' === $status ) : ?>
		<div class="hp-b2b-success" role="status">
			<strong><?php esc_html_e( 'הבקשה התקבלה.', 'hea-lth-portal' ); ?></strong>
			<p><?php esc_html_e( 'צוות Hea-lth יעבור על הפרטים ויחזור אליכם להמשך תיאום עסקי.', 'hea-lth-portal' ); ?></p>
		</div>
	<?php elseif ( in_array( $status, array( 'required', 'invalid', 'error' ), true ) ) : ?>
		<div class="hp-b2b-error" role="alert"><?php esc_html_e( 'לא הצלחנו לקלוט את הבקשה. בדקו את שדות החובה ושלחו שוב.', 'hea-lth-portal' ); ?></div>
	<?php endif; ?>

	<form class="hp-b2b-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="hea_lth_b2b_intake">
		<input type="hidden" name="request_type" value="<?php echo esc_attr( $form_type ); ?>">
		<input type="hidden" name="context_slug" value="<?php echo esc_attr( $context ); ?>">
		<input type="hidden" name="return_url" value="<?php echo esc_url( $return_url ); ?>">
		<input type="hidden" name="form_started" value="<?php echo esc_attr( (string) time() ); ?>">
		<?php wp_nonce_field( 'hea_lth_b2b_intake', 'hea_lth_b2b_nonce' ); ?>
		<label class="hp-b2b-honeypot" aria-hidden="true" hidden>Website<input type="text" name="company_website" tabindex="-1" autocomplete="off"></label>

		<div class="hp-b2b-form__grid">
			<label><span><?php esc_html_e( 'שם החברה או המרפאה', 'hea-lth-portal' ); ?> *</span><input type="text" name="company_name" required autocomplete="organization"></label>
			<label><span><?php esc_html_e( 'שם איש או אשת הקשר', 'hea-lth-portal' ); ?> *</span><input type="text" name="contact_name" required autocomplete="name"></label>
			<label><span><?php esc_html_e( 'טלפון', 'hea-lth-portal' ); ?> *</span><input type="tel" name="contact_phone" required autocomplete="tel" inputmode="tel"></label>
			<label><span><?php esc_html_e( 'אימייל עסקי', 'hea-lth-portal' ); ?> *</span><input type="email" name="contact_email" required autocomplete="email"></label>
			<label><span><?php esc_html_e( 'עיר', 'hea-lth-portal' ); ?></span><input type="text" name="city" autocomplete="address-level2"></label>
			<?php if ( 'supplier_join' === $form_type ) : ?>
				<label><span><?php esc_html_e( 'אתר החברה או הקטלוג', 'hea-lth-portal' ); ?></span><input type="url" name="company_url" autocomplete="url"></label>
				<label><span><?php esc_html_e( 'מסלול שמעניין אתכם', 'hea-lth-portal' ); ?></span><select name="plan_interest"><option value="verified" <?php selected( $selected_plan, 'verified' ); ?>><?php esc_html_e( 'נוכחות מאומתת', 'hea-lth-portal' ); ?></option><option value="showroom" <?php selected( $selected_plan, 'showroom' ); ?>><?php esc_html_e( 'אולם תצוגה מקצועי', 'hea-lth-portal' ); ?></option><option value="growth" <?php selected( $selected_plan, 'growth' ); ?>><?php esc_html_e( 'שותפות צמיחה ועסקאות', 'hea-lth-portal' ); ?></option></select></label>
			<?php else : ?>
				<label><span><?php esc_html_e( 'שלב הפרויקט', 'hea-lth-portal' ); ?></span><select name="project_stage"><option value="immediate"><?php esc_html_e( 'רכש מיידי', 'hea-lth-portal' ); ?></option><option value="planning"><?php esc_html_e( 'תכנון והקמה', 'hea-lth-portal' ); ?></option><option value="expansion"><?php esc_html_e( 'הרחבת מרפאה קיימת', 'hea-lth-portal' ); ?></option><option value="comparison"><?php esc_html_e( 'השוואת אפשרויות', 'hea-lth-portal' ); ?></option></select></label>
			<?php endif; ?>
		</div>

		<?php if ( 'clinic_quote' === $form_type ) : ?>
			<fieldset class="hp-b2b-categories"><legend><?php esc_html_e( 'מה תרצו לכלול בתכנון הרכש?', 'hea-lth-portal' ); ?></legend><div><?php foreach ( $categories as $slug => $label ) : ?><label><input type="checkbox" name="categories[]" value="<?php echo esc_attr( $slug ); ?>"><span><?php echo esc_html( $label ); ?></span></label><?php endforeach; ?></div></fieldset>
		<?php endif; ?>

		<label class="hp-b2b-consent"><input type="checkbox" name="contact_consent" value="1" required><span><?php esc_html_e( 'אני מאשר/ת ל-Hea-lth ליצור איתי קשר בנוגע לבקשה העסקית ולתאם המשך עם גורמים מתאימים.', 'hea-lth-portal' ); ?></span></label>
		<button class="hp-button" type="submit"><?php echo esc_html( 'supplier_join' === $form_type ? __( 'שליחת בקשת הצטרפות', 'hea-lth-portal' ) : __( 'שליחת בקשת רכש', 'hea-lth-portal' ) ); ?></button>
	</form>
</div>
