<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pektsekye_Ymm_Setup_Install {


	public static function init() {

	}
	

	public static function install() {
	
		if ( !class_exists( 'WooCommerce' ) ) { 
		  deactivate_plugins('ymm');
		  wp_die( __( 'YMM requires WooCommerce to run. Please install WooCommerce and activate.', 'ymm-search' ) );
	  }

    if ( version_compare( WC()->version, '3.0', "<" ) ) {
      wp_die(sprintf(__( 'WooCommerce %s or higher is required (You are running %s)', 'ymm-search' ), '3.0', WC()->version));
    }
				   	  	
		self::create_tables();
		
	  add_option('ymm_display_vehicle_fitment', 'yes');		
	  add_option('ymm_enable_category_dropdowns', 'yes');
	  add_option('ymm_enable_search_field', 'yes');	  	  
	}


	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();
		 
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		dbDelta( self::get_schema() );
	}


	private static function get_schema() {
		global $wpdb;
		
		return "
CREATE TABLE {$wpdb->prefix}ymm (
  id int(11) unsigned NOT NULL auto_increment,
  product_id int(11) unsigned NOT NULL,  
  make varchar(100) NOT NULL, 
  model varchar(100) NOT NULL,
  year_from int(4) unsigned NOT NULL default 0,
  year_to int(4) unsigned NOT NULL default 0,
  PRIMARY KEY (id),
  UNIQUE KEY uk_ymm_product_id (product_id, make, model, year_from, year_to) 
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
		";
		 
	}


	public static function wpmu_drop_tables( $tables ) {
		global $wpdb;
		$tables[] = $wpdb->prefix . 'ymm';						
		return $tables;
	}
}
