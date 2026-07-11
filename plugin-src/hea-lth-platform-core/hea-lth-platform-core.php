<?php
/**
 * Plugin Name: Hea-lth Platform Core
 * Plugin URI: https://hea-lth.co.il
 * Description: Content model and safe public-directory foundation for the Hea-lth portal rebuild.
 * Version: 0.1.0
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

define( 'HEA_LTH_PLATFORM_CORE_VERSION', '0.1.0' );
define( 'HEA_LTH_PLATFORM_CORE_FILE', __FILE__ );
define( 'HEA_LTH_PLATFORM_CORE_DIR', plugin_dir_path( __FILE__ ) );

require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-platform-core.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-directory-controller.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-anatomy-model-registry.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-directory-map-registry.php';
require_once HEA_LTH_PLATFORM_CORE_DIR . 'includes/class-hea-lth-lead-route-resolver.php';

Hea_Lth_Platform_Core::boot();

register_activation_hook( HEA_LTH_PLATFORM_CORE_FILE, array( 'Hea_Lth_Platform_Core', 'activate' ) );
register_deactivation_hook( HEA_LTH_PLATFORM_CORE_FILE, array( 'Hea_Lth_Platform_Core', 'deactivate' ) );
