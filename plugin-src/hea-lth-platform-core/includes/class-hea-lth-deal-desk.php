<?php
/**
 * Deal desk: the owner's mail-independent window into the revenue funnel.
 *
 * One admin screen (submenu of the control center) that shows every B2B
 * lead with its contact details and notification-mail result, every
 * advisory-room entry, and the mail-failure counter — so a lost email can
 * never again mean an invisible lead (review finding C2).
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hea_Lth_Deal_Desk {

	const MENU_SLUG = 'hea-lth-deal-desk';

	public static function boot() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ), 20 );
	}

	public static function register_menu() {
		$parent = class_exists( 'Hea_Lth_Control_Center' ) ? Hea_Lth_Control_Center::MENU_SLUG : 'options-general.php';
		add_submenu_page(
			$parent,
			__( 'לידים וחדרי עסקה', 'hea-lth-platform-core' ),
			__( 'לידים וחדרים', 'hea-lth-platform-core' ),
			'manage_options',
			self::MENU_SLUG,
			array( __CLASS__, 'render_page' )
		);
	}

	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to use the deal desk.', 'hea-lth-platform-core' ) );
		}

		$failures = (int) get_option( 'hea_lth_b2b_mail_failures', 0 );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'לידים וחדרי עסקה', 'hea-lth-platform-core' ); ?></h1>
			<?php if ( $failures > 0 ) : ?>
				<div class="notice notice-error"><p><?php echo esc_html( sprintf( __( 'שים לב: %d הודעות מייל על לידים נכשלו בשליחה. הלידים עצמם שמורים כאן במלואם.', 'hea-lth-platform-core' ), $failures ) ); ?></p></div>
			<?php else : ?>
				<p><?php esc_html_e( 'כל ליד נשמר כאן במלואו, ללא תלות בהודעות המייל.', 'hea-lth-platform-core' ); ?></p>
			<?php endif; ?>

			<h2><?php esc_html_e( 'בקשות B2B', 'hea-lth-platform-core' ); ?></h2>
			<?php self::render_leads(); ?>

			<h2><?php esc_html_e( 'כניסות לחדרי ייעוץ', 'hea-lth-platform-core' ); ?></h2>
			<?php self::render_rooms(); ?>
		</div>
		<?php
	}

	private static function render_leads() {
		$leads = get_posts(
			array(
				'post_type'      => 'hp_b2b_request',
				'post_status'    => array( 'private', 'publish', 'draft' ),
				'posts_per_page' => 100,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		if ( ! $leads ) {
			echo '<p>' . esc_html__( 'אין עדיין בקשות.', 'hea-lth-platform-core' ) . '</p>';
			return;
		}
		?>
		<table class="widefat striped">
			<thead><tr>
				<th><?php esc_html_e( 'תאריך', 'hea-lth-platform-core' ); ?></th>
				<th><?php esc_html_e( 'סוג', 'hea-lth-platform-core' ); ?></th>
				<th><?php esc_html_e( 'חברה', 'hea-lth-platform-core' ); ?></th>
				<th><?php esc_html_e( 'איש קשר', 'hea-lth-platform-core' ); ?></th>
				<th><?php esc_html_e( 'טלפון', 'hea-lth-platform-core' ); ?></th>
				<th><?php esc_html_e( 'אימייל', 'hea-lth-platform-core' ); ?></th>
				<th><?php esc_html_e( 'מערכות', 'hea-lth-platform-core' ); ?></th>
				<th><?php esc_html_e( 'מייל התראה', 'hea-lth-platform-core' ); ?></th>
				<th><?php esc_html_e( 'ניהול', 'hea-lth-platform-core' ); ?></th>
			</tr></thead>
			<tbody>
			<?php foreach ( $leads as $lead ) : ?>
				<?php
				$type      = (string) get_post_meta( $lead->ID, 'hp_request_type', true );
				$names     = get_post_meta( $lead->ID, 'hp_equipment_names', true );
				$mail      = (string) get_post_meta( $lead->ID, 'hp_mail_result', true );
				$mail_text = ( 'sent' === $mail ) ? __( 'נשלח', 'hea-lth-platform-core' ) : ( ( 'failed' === $mail ) ? __( 'נכשל!', 'hea-lth-platform-core' ) : __( 'לא תועד', 'hea-lth-platform-core' ) );
				?>
				<tr>
					<td><?php echo esc_html( get_the_date( 'd.m.Y H:i', $lead ) ); ?></td>
					<td><?php echo esc_html( 'clinic_quote' === $type ? __( 'רכש למרפאה', 'hea-lth-platform-core' ) : __( 'הצטרפות ספק', 'hea-lth-platform-core' ) ); ?></td>
					<td><?php echo esc_html( (string) get_post_meta( $lead->ID, 'hp_company_name', true ) ); ?></td>
					<td><?php echo esc_html( (string) get_post_meta( $lead->ID, 'hp_contact_name', true ) ); ?></td>
					<td><?php echo esc_html( (string) get_post_meta( $lead->ID, 'hp_contact_phone', true ) ); ?></td>
					<td><?php echo esc_html( (string) get_post_meta( $lead->ID, 'hp_contact_email', true ) ); ?></td>
					<td><?php echo esc_html( is_array( $names ) ? implode( ', ', array_map( 'strval', $names ) ) : '' ); ?></td>
					<td><?php echo esc_html( $mail_text ); ?></td>
					<td><a href="<?php echo esc_url( get_edit_post_link( $lead->ID ) ); ?>"><?php esc_html_e( 'פתיחה', 'hea-lth-platform-core' ); ?></a></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	private static function render_rooms() {
		if ( ! class_exists( 'Hea_Lth_Advisory_Rooms' ) ) {
			return;
		}
		?>
		<table class="widefat striped">
			<thead><tr>
				<th><?php esc_html_e( 'חדר', 'hea-lth-platform-core' ); ?></th>
				<th><?php esc_html_e( 'עבור', 'hea-lth-platform-core' ); ?></th>
				<th><?php esc_html_e( 'סוג', 'hea-lth-platform-core' ); ?></th>
				<th><?php esc_html_e( 'כניסות', 'hea-lth-platform-core' ); ?></th>
				<th><?php esc_html_e( 'כניסה אחרונה (UTC)', 'hea-lth-platform-core' ); ?></th>
				<th><?php esc_html_e( 'ניהול', 'hea-lth-platform-core' ); ?></th>
			</tr></thead>
			<tbody>
			<?php foreach ( Hea_Lth_Advisory_Rooms::rooms() as $key => $room ) : ?>
				<?php
				$page    = get_page_by_path( 'advisory/' . $key );
				$entries = $page ? get_post_meta( $page->ID, '_hea_lth_advisory_entries', true ) : array();
				$entries = is_array( $entries ) ? $entries : array();
				$last    = $entries ? end( $entries ) : null;
				$last_at = ( $last && isset( $last['time'] ) ) ? $last['time'] . ( isset( $last['method'] ) && 'code' === $last['method'] ? ' (קישור)' : ' (סיסמה)' ) : __( 'טרם נכנסו', 'hea-lth-platform-core' );
				?>
				<tr>
					<td><?php echo esc_html( $key ); ?></td>
					<td><?php echo esc_html( $room['client'] ); ?></td>
					<td><?php echo esc_html( 'supplier' === $room['type'] ? __( 'ספק', 'hea-lth-platform-core' ) : __( 'רוכש', 'hea-lth-platform-core' ) ); ?></td>
					<td><?php echo esc_html( (string) count( $entries ) ); ?></td>
					<td><?php echo esc_html( $last_at ); ?></td>
					<td><?php echo $page ? '<a href="' . esc_url( get_edit_post_link( $page->ID ) ) . '">' . esc_html__( 'פתיחה', 'hea-lth-platform-core' ) . '</a>' : esc_html__( 'טרם הוקם', 'hea-lth-platform-core' ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}
}
