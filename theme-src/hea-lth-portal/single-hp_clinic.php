<?php
/**
 * Verified clinic and organisation profile shell.
 *
 * This template becomes reachable only when the platform deliberately enables
 * public clinic routes. It does not change that release gate.
 *
 * @package HeaLthPortal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( 'verified' !== get_post_meta( get_queried_object_id(), 'hp_public_state', true ) ) {
	status_header( 404 );
	nocache_headers();
}

get_header();
get_template_part( 'template-parts/profile', 'public' );
get_footer();
