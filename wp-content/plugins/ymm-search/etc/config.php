<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pektsekye_Ymm_Config
{
    
    protected $_csvColumnNames = array('product_sku', 'make', 'model', 'year_from', 'year_to');


    public function getLevels()
    {
      return array(
        array('level' => 0, 'url_parameter' => '_make', 'option_title' => __('-- Make --', 'ymm-search')),
        array('level' => 1, 'url_parameter' => '_model', 'option_title' => __('-- Model --', 'ymm-search')),
        array('level' => 2, 'url_parameter' => '_year', 'option_title' => __('-- Year --', 'ymm-search'))            
      );     
    }


    public function getCsvColumnNames()
    {
      return $this->_csvColumnNames;     
    }
    
    
     public function getCategoryDefaultOptionTitle()     
    {
      return __('-- select category --', 'ymm-search');        
    }    
    
}
