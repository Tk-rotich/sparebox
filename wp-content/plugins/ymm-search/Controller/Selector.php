<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Pektsekye_Ymm_Controller_Selector {


  protected $_db;
        
        
	public function __construct() {
    include_once( Pektsekye_YMM()->getPluginPath() . 'Model/Db.php');		
		$this->_db = new Pektsekye_Ymm_Model_Db();	
	}

 
  public function fetch(){

    $categoryId = isset($_GET['cId']) ? (int) $_GET['cId'] : 0;
    $selectedValues = isset($_GET['values']) ? array_map('sanitize_text_field', array_map('stripslashes', (array) $_GET['values'])) : array();
    
    $nextColumnValues = (array) $this->_db->fetchColumnValues($selectedValues, $categoryId);
      
    echo json_encode($nextColumnValues);
    exit;     
  }	



  public function getCategories(){

    $values = isset($_GET['values']) ? array_map('sanitize_text_field', array_map('stripslashes', (array) $_GET['values'])) : array();
    
    if (count($values) == 0){
      echo '{}';
      exit;     
    }   
        
    $pIds = $this->_db->getProductIds($values);
    
    if (count($pIds) == 0){
      echo '{}';
      exit;     
    }
        
    $rootCategories = array();
    $categoryTree = array();       
    
    $taxonomy = 'product_cat';
    $args = array('orderby' => 'parent', 'order' => 'DESC');

    $defaults = array('fields' => 'all');
    $args = wp_parse_args($args, $defaults);

    $productCategories = wp_get_object_terms($pIds, $taxonomy, $args);
    
    $childIds = array();
       
    foreach($productCategories as $category){
        $catId = $category->term_id; 

        $parentIds = get_ancestors($catId, $taxonomy);
      
        if (count($parentIds) == 0){
          $rootCategories[$catId] = 1;
        } else {
          $parentIds = array_reverse($parentIds);
          $rootCategories[$parentIds[0]] = 1;
      
          foreach ($parentIds as $i => $id) {
      
            $parentCat = get_term($id, $taxonomy);

            if (!is_wp_error($parentCat) && $parentCat) {

              if (!isset($categoryTree[$id])){
                $categoryTree[$id] = array('title' => $parentCat->name, 'children' => array());  
              }
              
              $nextInd = $i + 1;
              $childId = isset($parentIds[$nextInd]) ? $parentIds[$nextInd] : $catId;
              if (!isset($childIds[$childId])){ //no duplicates
                $categoryTree[$id]['children'][] = $childId;
                $childIds[$childId] = 1;
              }  
            }
          }
        }
    
        $categoryTree[$catId]['title'] = $category->name;
        $categoryTree[$catId]['url'] = get_term_link($category);			
    }

    $rootCategories = array_keys($rootCategories);

    echo json_encode(array('rootCategoryIds'=>$rootCategories,'categories'=>$categoryTree));
    exit;     
  }

}
