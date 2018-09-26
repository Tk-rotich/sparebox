<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Pektsekye_Ymm_Block_Adminhtml_Ymm_Selector {


  protected $_db;  



	public function __construct() {
    include_once( Pektsekye_YMM()->getPluginPath() . 'Model/Db.php');		
		$this->_db = new Pektsekye_Ymm_Model_Db();  
	}
 
 
 
  public function page_init()
  {
    include_once( Pektsekye_YMM()->getPluginPath() . 'view/adminhtml/templates/ymm/selector.php');
  }



  public function hasYmmData() 
  {
    $limit = 1;
    $data = $this->_db->getVehicleData($limit);
    return count($data) > 0;
  }
  
  
  
  public function getMessage() 
  {
    return Pektsekye_YMM()->getMessage();
  }



  public function getDisplayVehicleFitment() 
  {
    return get_option('ymm_display_vehicle_fitment') == 'yes';
  }  
  
  
  
  public function getDisplayCategoryDropdowns() 
  {
    return get_option('ymm_enable_category_dropdowns') == 'yes';
  }
  
  
  
  public function getDisplaySearchField() 
  {
    return get_option('ymm_enable_search_field') == 'yes';
  }  
  
  
}
