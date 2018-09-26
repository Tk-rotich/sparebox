<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pektsekye_Ymm_Block_Selector {

  
  protected $_db;
  protected $_config;

  protected $_widgetId = '';  
  protected $_title = '';
  protected $_searchTitle = '';  
  protected $_filterCategoryPage = true;
  protected $_garageEnabled = true;
  protected $_removeFromGarageEnabled = false;      
  protected $_layoutType = 'default';   
  protected $_template;
  
  protected $_seletedCookie;
  protected $_garageVehicle;   
  protected $_garageVehicles;      
    
    
	public function __construct() {

    include_once( Pektsekye_YMM()->getPluginPath() . 'Model/Db.php');		
		$this->_db =  new Pektsekye_Ymm_Model_Db();  

    include_once( Pektsekye_YMM()->getPluginPath() . 'etc/config.php');		
		$this->_config = new Pektsekye_Ymm_Config();			   			
	}
  
     
  public function setWidgetId($id){
    $this->_widgetId = $id;
  } 
 
    
  public function getWidgetId(){
    return $this->_widgetId;
  } 
 
           
  public function setTitle($title){
    $this->_title = $title;
  } 
 
    
  public function getTitle(){
    return $this->_title;
  }
 
           
  public function setSearchTitle($title){
    $this->_searchTitle = $title;
  } 
 
    
  public function getSearchTitle(){
    return $this->_searchTitle;
  }  
    
    
  public function setFilterCategoryPage($mode){
    $this->_filterCategoryPage = $mode;
  } 
 
    
  public function getFilterCategoryPage(){
    return $this->_filterCategoryPage;
  }
  
  
  public function setGarageEnabled($enabled){
    $this->_garageEnabled = $enabled;
  } 
 
    
  public function getGarageEnabled(){
    return $this->_garageEnabled;
  }
  
  
  public function setRemoveFromGarageEnabled($enabled){
    $this->_removeFromGarageEnabled = $enabled;
  } 
 
    
  public function getRemoveFromGarageEnabled(){
    return $this->_removeFromGarageEnabled;
  }    
  
  
  public function setLayoutType($type)
  {         
    return $this->_layoutType = $type; 
  }
  
  
  public function isHorizontal()
  {         
    return $this->_layoutType == 'horizontal'; 
  }  
  
  
  public function setTemplate($template)
  {         
    return $this->_template = $template; 
  }  
  
  
  public function getLevels()
  {         
    return $this->_config->getLevels(); 
  } 
 
 
   public function getLevelSelectHtml($levelData)         
  {
    $class = 'ymm-select';
    $extra = '';
    
    $level = $levelData['level'];
    $value = $this->getSelectedValue($level);
 
    if ($level > 0){
      $prevDropValue = $this->getSelectedValue($level - 1);    
      if (empty($prevDropValue)){                    
        $class .= ' disabled';
        $extra .= ' disabled="disabled"';  
      }
    }       
       
    $html = '<select class="'.$class.'" name="'.$levelData['url_parameter'].'" '.$extra.'><option value="">'.esc_html( $levelData['option_title'] ).'</option>';			      
    foreach ( $this->getLevelOptions($level) as $val ){
      $html .= '<option value="'.esc_attr( $val ).'" '.($val == $value ? 'selected' : '').'>'.esc_html( $val ).'</option>';			
    }
    $html .= '</select>';

    return $html;    
  }
 

   public function getLevelOptions($level, $forCurrentCategory = true)     
  {    
    $categoryId = $this->getFilterCategoryPage() && $forCurrentCategory ? $this->getCategoryId() : null;

    $values = array();      
    if ($level == 0){
      $values = $this->_db->fetchColumnValues(array(), $categoryId);     
    } elseif ($this->getSelectedValue($level - 1)) {  
      $values = $this->_db->fetchColumnValues(array_slice($this->getSelectedValues(), 0, $level), $categoryId);
    }
    return $values;
  }    


   public function getHasSelectedValues()     
  {       
    return !is_null($this->getSelectedValues());        
  }    
   
    
   public function getIsSelected()     
  {   
    if (!$this->getHasSelectedValues()){
      return false;    
    }
    
    $level = 0;
    $value = $this->getSelectedValue($level);      
    $values = (array) $this->getLevelOptions($level);   			      
    $key = array_search($value, $values);
    
    return $key !== false;        
  }



   public function getFilterIsActive()     
  {
    $selectedParams = $this->getSelectedParameters();
    return !empty($selectedParams);
  }
  
  
   public function getCategoryId()     
  { 
    $categoryId = null;
    
    if (is_product_category()) {
      global $wp_query;
      $cat = $wp_query->get_queried_object();
      $categoryId = $cat->term_id;
    }
    
    return (int) $categoryId;     
  }
       
  
   public function getSelectedValues()     
  {      
    $selectedParams = $this->getSelectedParameters();
    if ($selectedParams){
      return $selectedParams;
    }
    
    $selectedCookie = $this->getSelectedCookie();       
    if (count($selectedCookie) > 0 && !empty($selectedCookie['vehicle'])){
      return explode(',', $selectedCookie['vehicle']);
    }
    
    return null;
  }


   public function getSelectedParameters()     
  {      
    return Pektsekye_YMM()->registry('selected_values');
  }

  
   public function getSelectedValue($level)     
  { 
    $values = $this->getSelectedValues();         
    return isset($values[$level]) ? $values[$level] : '';
  }     
  
  
   public function getAjaxUrl()     
  { 
    $protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
    return admin_url( 'admin-ajax.php', $protocol );        
  }      

  
   public function getAjaxShortUrl()     
  { 
    return Pektsekye_YMM()->getPluginUrl() . 'ymm_ajax.php';         
  } 
  
  
   public function getSubmitUrl()     
  {   
    return $this->getFilterCategoryPage() && is_product_category() ? '' : $this->getSubmitSearchUrl();
  }


   public function getSubmitSearchUrl()     
  {   
    return home_url( '/' );
  }
  

   public function getLevelCount()     
  { 
    $levels = $this->getLevels();
    return count($levels);        
  }


   public function getLevelParameterNames()     
  {
    $paramNames = array();
    foreach ($this->getLevels() as $level){
      $paramNames[] = $level['url_parameter']; 
    } 
    return json_encode($paramNames);        
  }


   public function getSelectedCookie()     
  {
    if (!isset($this->_seletedCookie)){
      $selected = array();        
      if (isset($_COOKIE['partfinder_selected'])){
        $data = json_decode(stripslashes($_COOKIE['partfinder_selected']), true);
        if (isset($data['vehicle']) && isset($data['vehicles'])){
          $selected['vehicle']  = $data['vehicle'];
          $selected['vehicles'] = (array) $data['vehicles'];          
        }    
      }
      $this->_seletedCookie = $selected;
    }          
    return $this->_seletedCookie;        
  }
  

   public function getGarageVehicles()     
  {
    if (!isset($this->_garageVehicles)){
      $garageVehicles = array();
      $selected = $this->getSelectedCookie();    
      if (count($selected) > 0){
        $vehicles = $selected['vehicles'];
        if (is_product_category() && $this->getFilterCategoryPage()){
          $vehicles = $this->_db->filterVehiclesForCategory($vehicles, (int)$this->getCategoryId());//vehicles that have products for current category
        }
        foreach($vehicles as $vehicle){
          $garageVehicles[] = array('title' => str_replace(',', ' ', $vehicle), 'value' => $vehicle);
        }      
      }
      $this->_garageVehicles = $garageVehicles;
    }         
    return $this->_garageVehicles;        
  }
  
  
   public function getGarageVehicle()     
  {
    if (!isset($this->_garageVehicle)){
      $vehicle = '';        
      $selected = $this->getSelectedCookie();       
      if (count($selected) > 0){
        $vehicle = $selected['vehicle'];
      }
      $this->_garageVehicle = $vehicle;
    }        
    return $this->_garageVehicle;        
  }
  
  
   public function getFirstLevelOptionsJson()     
  {
    $options = (array) $this->getLevelOptions(0, false);        
    return json_encode($options);        
  }    
    

   public function getCategorySearchEnabled()     
  { 
    return get_option('ymm_enable_category_dropdowns') == 'yes';       
  }
  
  
   public function getWordSearchEnabled()     
  { 
    return get_option('ymm_enable_search_field') == 'yes';       
  }     


   public function getCategoryDefaultOptionTitle()     
  {
    return $this->_config->getCategoryDefaultOptionTitle();        
  }
  
  
   public function canShowExtra()     
  {
    $showExtra = $this->getCategorySearchEnabled() || $this->getWordSearchEnabled();
    
    if (is_product_category() && $this->getFilterCategoryPage()){
      $showExtra = false;
    }
    
    return $showExtra;        
  }
  
  
  
   public function getGarageHasVehicles()     
  {
    $vehicles = $this->getGarageVehicles();
    return count($vehicles) > 0;        
  }


   public function getHasSelectableMakes()     
  {
    $categoryId = $this->getFilterCategoryPage() ? $this->getCategoryId() : null;

    $makes = $this->_db->fetchColumnValues(array(), $categoryId);
    
    return count($makes) > 0;        
  }
    
  
   public function isResultsPage()     
  {
    return is_search() && isset($_GET['ymm_search']);        
  }
     
   
   public function page_init()
  {    
    $templateFile = 'selector.php';
        
    if ($this->isHorizontal()){
      $templateFile = 'horizontal_selector.php'; 
    } elseif (isset($this->_template)) {
      $templatePath = Pektsekye_YMM()->getPluginPath() . 'view/frontend/templates/' . $this->_template;
      if (file_exists($templatePath)){
        $templateFile = $this->_template;
      }           
    }
    
    include(Pektsekye_YMM()->getPluginPath() . 'view/frontend/templates/' . $templateFile);
  }


}
