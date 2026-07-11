<?php
/**
 * Editorially approved treatment profile shell.
 *
 * This source file does not enable public treatment routes. It prepares the
 * public presentation only for records that have passed the existing Hea-lth
 * editorial gate.
 *
 * @package HeaLthPortal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$editorial_status = hea_lth_portal_get_editorial_status( get_queried_object_id() );
if ( 'hp_treatment' !== get_post_type( get_queried_object_id() ) || ! $editorial_status['is_reviewed'] ) {
	status_header( 404 );
	nocache_headers();
}

get_header();
get_template_part( 'template-parts/treatment', 'public' );
get_footer();
