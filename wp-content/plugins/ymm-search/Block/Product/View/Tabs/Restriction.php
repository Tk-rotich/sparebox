<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pektsekye_Ymm_Block_Product_View_Tabs_Restriction {

  
  protected $_db;
  protected $_config;
  
  protected $_restrictions;  
  
      
    
	public function __construct() {

    include_once( Pektsekye_YMM()->getPluginPath() . 'Model/Db.php');		
		$this->_db =  new Pektsekye_Ymm_Model_Db();  

    include_once( Pektsekye_YMM()->getPluginPath() . 'etc/config.php');		
		$this->_config = new Pektsekye_Ymm_Config();			   			
	}



  public function getLevels()
  {         
    return $this->_config->getLevels(); 
  } 
 
 
 
   public function getFormatedRestrictions()     
  { 
    global $product;
    
    $result = $this->_db->getProductRestrictions((int) $product->get_id());
    
    foreach ($result as $k => $r) {
      $from = (int) $r['year_from'];
      $to = (int) $r['year_to'];
      
      $year = ''; 
           
      if ($from != 0 || $to != 0){   
        if ($from == 0){
          $year = $to;          
        } elseif ($to == 0){
          $year = $from;
        } elseif ($from == $to){
          $year = $from;
        } elseif ($from < $to){          	
          $year = "{$from} - {$to}";            
        }     
      }

      $result[$k]['year'] = $year;
    }
    
    return $result;      
  }



  public function setRestrictions($restrictions)
  {         
    return $this->_restrictions = $restrictions; 
  }



  public function getRestrictions()
  { 
    global $product;
              
    if (!isset($this->_restrictions)){
      $this->_restrictions = $this->_db->getProductRestrictions((int) $product->get_id());
    }
    
    return $this->_restrictions; 
  } 
  
  
    
  public function page_init(){
    include_once( Pektsekye_YMM()->getPluginPath() . 'view/frontend/templates/product/view/tabs/restriction.php');
  }


}
