<?php
/**
 * Plugin Name: Hea-lth Platform Core
 * Plugin URI: https://hea-lth.co.il
 * Description: Content model and safe public-directory foundation for the Hea-lth portal rebuild.
 * Version: 0.22.0
 * Requires at least: 6.5
 * Requires PHP: 7.4
 * Author: Hea-lth
 * Text Domain: hea-lth-platform-core
 *
 * Public data remains empty until its review and publication gates pass.
 *
 * @package HeaLthPlatformCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HEA_LTH_PLATFORM_CORE_VERSION', '0.22.0' );
define( 'HEA_LTH_PLATFORM_CORE_FILE', __FILE__ );
define( 'HEA_LTH_PLATFORM_CORE_DIR', plugin_dir_path( __FILE__ ) );

require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-platform-core.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-directory-controller.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-anatomy-model-registry.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-directory-map-registry.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-lead-route-resolver.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-knowledge-graph.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-page-provisioner.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-showroom-provisioner.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-clinic-provisioner.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-advisory-rooms.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-b2b-intake.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-supplier-portal.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-rfq-invitations.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-brokerage-ledger.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-brokerage-agreement.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-control-center.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-deal-desk.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-metrics.php';

Hea_Lth_Platform_Core::boot();
Hea_Lth_Page_Provisioner::boot();
Hea_Lth_Advisory_Rooms::boot();
Hea_Lth_Showroom_Provisioner::boot();
Hea_Lth_Clinic_Provisioner::boot();
Hea_Lth_B2B_Intake::boot();
Hea_Lth_Supplier_Portal::boot();
Hea_Lth_RFQ_Invitations::boot();
Hea_Lth_Brokerage_Ledger::boot();
Hea_Lth_Brokerage_Agreement::boot();
Hea_Lth_Deal_Desk::boot();

register_activation_hook( HEA_LTH_PLATFORM_CORE_FILE, array( 'Hea_Lth_Platform_Core', 'activate' ) );
register_deactivation_hook( HEA_LTH_PLATFORM_CORE_FILE, array( 'Hea_Lth_Platform_Core', 'deactivate' ) );
