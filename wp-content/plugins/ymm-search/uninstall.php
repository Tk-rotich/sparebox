<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

delete_option('ymm_display_vehicle_fitment');
delete_option('ymm_enable_category_dropdowns');
delete_option('ymm_enable_search_field');

// Tables
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ymm" );


