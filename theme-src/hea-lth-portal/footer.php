<?php
/**
 * Global footer.
 *
 * @package HeaLthPortal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main>

<footer class="hp-site-footer">
	<div class="hp-shell hp-footer__top">
		<div class="hp-footer__brand">
			<a class="hp-brand hp-brand--inverse" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<svg class="hp-brand__mark" viewBox="0 0 48 48" aria-hidden="true" focusable="false">
					<rect x="3" y="3" width="42" height="42" rx="14" fill="currentColor"/>
					<path d="M15.25 13.5v21M32.75 13.5v21M15.25 24h14" fill="none" stroke="#fffdf8" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/>
					<circle cx="33" cy="14" r="2.75" fill="#dfc17b"/>
				</svg>
				<span class="hp-brand__copy"><strong>hea-lth</strong><span><?php esc_html_e( 'בחירה במידע ובשירותי בריאות', 'hea-lth-portal' ); ?></span></span>
			</a>
			<p><?php esc_html_e( 'מקום אחד להבנת אפשרויות טיפול, בדיקות ושירותים פרטיים, לצד חיפוש אנשי מקצוע ומרפאות לפי מידע גלוי ומאומת.', 'hea-lth-portal' ); ?></p>
			<a class="hp-button hp-button--light" href="<?php echo esc_url( hea_lth_portal_foundation_route( 'find_care' ) ); ?>"><?php esc_html_e( 'התחילו חיפוש', 'hea-lth-portal' ); ?></a>
		</div>

		<div class="hp-footer__links">
			<section>
				<h2><?php esc_html_e( 'למצוא טיפול', 'hea-lth-portal' ); ?></h2>
				<a href="<?php echo esc_url( hea_lth_portal_route( 'aesthetic_medicine' ) ); ?>"><?php esc_html_e( 'רפואה אסתטית', 'hea-lth-portal' ); ?></a>
				<a href="<?php echo esc_url( hea_lth_portal_route( 'plastic_surgery_consultation' ) ); ?>"><?php esc_html_e( 'ניתוחים פלסטיים', 'hea-lth-portal' ); ?></a>
				<a href="<?php echo esc_url( hea_lth_portal_route( 'hair_transplant_consultation' ) ); ?>"><?php esc_html_e( 'השתלת שיער', 'hea-lth-portal' ); ?></a>
				<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'private_medicine' ) ); ?>"><?php esc_html_e( 'רפואה פרטית', 'hea-lth-portal' ); ?></a>
			</section>
			<section>
				<h2><?php esc_html_e( 'למצוא איש מקצוע', 'hea-lth-portal' ); ?></h2>
				<a href="<?php echo esc_url( hea_lth_portal_route( 'doctor_clinic_index' ) ); ?>"><?php esc_html_e( 'רופאים ומרפאות', 'hea-lth-portal' ); ?></a>
				<a href="<?php echo esc_url( hea_lth_portal_route( 'doctor_clinic_index' ) . '#directory-search' ); ?>"><?php esc_html_e( 'תחומי מומחיות', 'hea-lth-portal' ); ?></a>
				<a href="<?php echo esc_url( hea_lth_portal_route( 'doctor_clinic_index' ) . '#directory-search' ); ?>"><?php esc_html_e( 'חיפוש לפי אזור', 'hea-lth-portal' ); ?></a>
				<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'professionals' ) ); ?>"><?php esc_html_e( 'הצטרפות לאינדקס', 'hea-lth-portal' ); ?></a>
			</section>
			<section>
				<h2><?php esc_html_e( 'ידע וכלים', 'hea-lth-portal' ); ?></h2>
				<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'guides' ) ); ?>"><?php esc_html_e( 'מדריכים', 'hea-lth-portal' ); ?></a>
				<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'glossary' ) ); ?>"><?php esc_html_e( 'מילון בריאות', 'hea-lth-portal' ); ?></a>
				<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'health_technology' ) ); ?>"><?php esc_html_e( 'טכנולוגיות בריאות', 'hea-lth-portal' ); ?></a>
				<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'anatomy' ) ); ?>"><?php esc_html_e( 'הגוף האינטראקטיבי', 'hea-lth-portal' ); ?></a>
			</section>
		</div>
	</div>

	<div class="hp-shell hp-footer__trust">
		<div>
			<strong><?php esc_html_e( 'גבולות מידע רפואי', 'hea-lth-portal' ); ?></strong>
			<p><?php esc_html_e( 'המידע באתר נועד להבנה ולהכנה לשיחה עם איש מקצוע. במקרה חירום יש לפנות למגן דוד אדום 101 או לחדר מיון.', 'hea-lth-portal' ); ?></p>
		</div>
		<div>
			<strong><?php esc_html_e( 'אמינות ושקיפות', 'hea-lth-portal' ); ?></strong>
			<p><?php esc_html_e( 'פרופילים, מקורות ועדכוני תוכן מוצגים רק לאחר תהליך אימות ובקרה מתאים.', 'hea-lth-portal' ); ?></p>
		</div>
	</div>

	<div class="hp-shell hp-footer__bottom">
		<p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> hea-lth</p>
		<nav aria-label="<?php esc_attr_e( 'קישורי מדיניות', 'hea-lth-portal' ); ?>">
			<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'privacy' ) ); ?>"><?php esc_html_e( 'פרטיות', 'hea-lth-portal' ); ?></a>
			<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'terms' ) ); ?>"><?php esc_html_e( 'תנאי שימוש', 'hea-lth-portal' ); ?></a>
			<a href="<?php echo esc_url( hea_lth_portal_foundation_route( 'editorial_policy' ) ); ?>"><?php esc_html_e( 'מדיניות עריכה ובדיקה', 'hea-lth-portal' ); ?></a>
		</nav>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
