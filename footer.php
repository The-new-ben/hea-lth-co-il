<?php
/**
 * Site footer.
 *
 * @package HealthRevenue
 */

?>
</main>

<footer class="hr-footer">
	<div class="hr-footer__grid">
		<section>
			<h2>Hea-lth</h2>
			<p><?php esc_html_e( 'פלטפורמה חדשה לבריאות פרטית, רפואה אסתטית, רופאים, קליניקות ושירותי פרימיום בישראל.', 'health-revenue' ); ?></p>
			<p class="hr-footer__notice"><?php esc_html_e( 'המידע באתר אינו תחליף לייעוץ רפואי אישי. במצב חירום יש לפנות למד״א 101 או לחדר מיון.', 'health-revenue' ); ?></p>
		</section>

		<section>
			<h2><?php esc_html_e( 'תחומי ליבה', 'health-revenue' ); ?></h2>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/aesthetic-medicine-treatments/' ) ); ?>"><?php esc_html_e( 'רפואה אסתטית', 'health-revenue' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/plastic-surgery-consultation/' ) ); ?>"><?php esc_html_e( 'ניתוחים פלסטיים', 'health-revenue' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/hair-transplant-consultation/' ) ); ?>"><?php esc_html_e( 'השתלות שיער', 'health-revenue' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/premium-health-services/' ) ); ?>"><?php esc_html_e( 'בדיקות, אבחון ורפואה פרטית', 'health-revenue' ); ?></a></li>
			</ul>
		</section>

		<section>
			<h2><?php esc_html_e( 'מסלולים מסחריים', 'health-revenue' ); ?></h2>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/doctor-clinic-index/' ) ); ?>"><?php esc_html_e( 'איתור רופאים וקליניקות', 'health-revenue' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/botox-price/' ) ); ?>"><?php esc_html_e( 'מחירי בוטוקס', 'health-revenue' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/hyaluronic-acid-price/' ) ); ?>"><?php esc_html_e( 'מחירי חומצה היאלורונית', 'health-revenue' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/professionals/' ) ); ?>"><?php esc_html_e( 'הצטרפות מקצוענים', 'health-revenue' ); ?></a></li>
			</ul>
		</section>
	</div>

	<div class="hr-footer__bottom">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Hea-lth.</p>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'footer',
				'container'      => '',
				'menu_class'     => 'hr-footer__links',
				'fallback_cb'    => false,
			)
		);
		?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
