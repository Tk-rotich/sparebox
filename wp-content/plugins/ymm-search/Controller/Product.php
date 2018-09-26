<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pektsekye_Ymm_Controller_Product {


  protected $_db;
  protected $_config;        
        
  protected $_selectedValues;
  protected $_foundProductIds;
    
  
	public function __construct() {
    include_once( Pektsekye_YMM()->getPluginPath() . 'Model/Db.php');		
		$this->_db = new Pektsekye_Ymm_Model_Db();
		
    include_once( Pektsekye_YMM()->getPluginPath() . 'etc/config.php');		
		$this->_config = new Pektsekye_Ymm_Config();
			 
    add_filter('single_term_title', array($this, 'add_selected_vehicle_to_category_title'));    		    	
    add_filter('woocommerce_layered_nav_link', array($this, 'add_selected_params_to_layered_nav_link'));
    add_filter('woocommerce_get_filtered_term_product_counts_query', array($this, 'add_found_product_ids_to_product_counts_query'));		  		  		
    add_filter('get_search_query', array($this, 'shop_order_search_label' ));   											
	}


  public function applyFilter(){ 
    global $wp_query;

    if (is_product_category() || isset($_GET['ymm_search'])){

      $values = $this->getSelectedValues();
      $pIds = $this->getFoundProductIds();
 
      if (!is_product_category() || count($values) > 0){//filter category page only when values selected
        $wp_query->query_vars['post__in'] = count($pIds) > 0 ? $pIds : array(-1); // -1 to display a "no products found" message
      }

      if (count($values) > 0){
        Pektsekye_YMM()->register('selected_values', $values);
      }                             
    }        	  	         
  }
  
  
  public function getSelectedValues(){    
    if (!isset($this->_selectedValues)){
      $values = array();
      foreach ((array) $this->_config->getLevels() as $level){
        $parameter = $level['url_parameter'];
        if (isset($_GET[$parameter])){
          $values[] = sanitize_text_field(stripslashes($_GET[$parameter]));
        }  
      }
      $this->_selectedValues = $values; 
    }    
    return $this->_selectedValues; 
  }
  
  
  public function getFoundProductIds(){    
    if (!isset($this->_foundProductIds)){
      $pIds = array();   
      $values = $this->getSelectedValues();          
      if (count($values) > 0){               
        $pIds = $this->_db->getProductIds($values);            
      }   
      $this->_foundProductIds = $pIds; 
    }    
    return $this->_foundProductIds; 
  }  
  

  public function product_query($query) {

    if ($query->is_main_query() && function_exists('is_product_category') && (is_product_category() || isset($_GET['ymm_search']))) {
      //call wc product_query before PartFinder filter
      WC()->query->product_query($query);

      remove_action( 'pre_get_posts', array( WC()->query, 'pre_get_posts' ) );
      
      if (function_exists('tr_sku_search_helper')){// WooCommerce Search by Product SKU 
        remove_filter('pre_get_posts', 'tr_sku_search_helper', 15);
      }
            
      $this->applyFilter();
    }    
  }


  public function shop_order_search_label($label) {
    global $pagenow;
  
    if ('index.php' == $pagenow && isset($_GET['s']) && isset($_GET['ymm_search'])) { 
      $trace = debug_backtrace(defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? DEBUG_BACKTRACE_IGNORE_ARGS : false, 6);
      if (
           (isset($trace[3]) && in_array(basename($trace[3]['file']), array('class-wc-breadcrumb.php','wc-template-functions.php','general-template.php')) && $trace[3]['function'] == 'get_search_query' && (!isset($trace[4]['function']) || $trace[4]['function'] != 'get_search_form')) //WP 4.3     
        || (isset($trace[4]) && in_array(basename($trace[4]['file']), array('class-wc-breadcrumb.php','wc-template-functions.php','general-template.php')) && $trace[4]['function'] == 'get_search_query' && (!isset($trace[5]['function']) || $trace[5]['function'] != 'get_search_form')) //WP 4.9
          ){ 
        $label = implode(' ', $this->getSelectedValues());
        
        if (!empty($_GET['s'])){
          $label .= ' ' . sanitize_text_field(stripslashes($_GET['s']));
        }
      }

    }
    
    return $label;		
  }  
	  	  
	  	  
  public function add_selected_vehicle_to_category_title($title) {
  	if (!is_product_category()){
  	  return $title;
  	}
  	
    $values = $this->getSelectedValues();
    if (count($values) > 0){
      $title .= ' '. __('for', 'ymm-search') .' '. implode(' ', $values);    
    }
    
    return $title;  
  }	  
	    

  public function add_selected_params_to_layered_nav_link($link) {
  	if (!is_product_category() && !is_search()){
  	  return $link;
  	}
  	  
    $params = array();  
    foreach ((array) $this->_config->getLevels() as $level){
      $pr = $level['url_parameter'];
      if (isset($_GET[$pr])){
        $params[$pr] = sanitize_text_field(stripslashes($_GET[$pr]));
      }  
    }
    
    if (isset($_GET['s']) && $_GET['s'] == ''){//we need the empty "s" GET parameter to display the search results page
      $params['s'] = '';
    }    
    
    if (count($params) > 0){
      $link .= '&' . http_build_query($params);
    }
    
    if (isset($_GET['ymm_search'])){
      $urlParts = explode('?', $link);
      $link = home_url( '/' ) . '?' . $urlParts[1] . '&ymm_search=1'; //return back to the main search results page
    }
    
    return $link;  
  }	
  
  
  public function add_found_product_ids_to_product_counts_query($query) {
  	if (!is_product_category() && !is_search()){
  	  return $query;
  	}
  	  
		global $wpdb;
		  
    $pIds = $this->getFoundProductIds();    
    if (count($pIds) > 0){
      $query['where'] .= " AND {$wpdb->posts}.ID IN (". implode(',', $pIds) .")";
    }    
    
    return $query;  
  }
  
  
}
