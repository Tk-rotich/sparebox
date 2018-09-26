<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Pektsekye_Ymm_Controller_Adminhtml_Ymm_Selector {


  protected $_db;
  protected $_dbImportHandler;  
  protected $_config;
    
    
	public function __construct() {

    include_once( Pektsekye_YMM()->getPluginPath() . 'Model/Db.php');		
		$this->_db =  new Pektsekye_Ymm_Model_Db();  
		
    include_once( Pektsekye_YMM()->getPluginPath() . 'Model/Db/CsvImportHandler.php');  
    $this->_dbImportHandler = new Pektsekye_Ymm_Model_Db_CsvImportHandler();
            
    include_once( Pektsekye_YMM()->getPluginPath() . 'etc/config.php');		
		$this->_config = new Pektsekye_Ymm_Config();			   			
	}
	
	
 
  public function execute(){
      
    if (isset($_GET['action'])){
      switch($_GET['action']){               
        case 'importData':       
          if (isset($_FILES['import_file'])){
            $mode = isset($_POST['delete_old']) && $_POST['delete_old'] == 1 ? 'delete_old' : 'add_new';                        
            try {                               
              $this->_dbImportHandler->importFromCsvFile($_FILES['import_file'], $mode);
              Pektsekye_YMM()->setMessage(__('Ymm CSV file has been imported.', 'ymm-search'));                 
            } catch (Exception $e){
              Pektsekye_YMM()->setMessage(__('Ymm CSV file has not been imported.', 'ymm-search') .' '. $e->getMessage(), 'error');                    
            }
          }
        break; 
        case 'exportData':
          if (isset($_POST['submit'])){           
                                                            
            $array = array();
            
            $array[] = $this->_config->getCsvColumnNames();
            
            $data = $this->_db->getVehicleData();                       

            if (count($data) == 0){
              $data = $this->_db->getSampleVehicleData();  
            }
            
            $array = array_merge($array, $data);
          
            $this->download_send_headers("ymm_restrictions.csv");              
            echo $this->array2csv($array);
            die();
          }         
        break;  
        case 'updateConfig':
          if (isset($_POST['submit'])){           
            $fitment = 'no';
            if (isset($_POST['ymm_display_vehicle_fitment']) && isset($_POST['ymm_display_vehicle_fitment']) == 1){
              $fitment = 'yes';
            }
            update_option('ymm_display_vehicle_fitment', $fitment);
            
            $categoryDropdowns = 'no';
            if (isset($_POST['ymm_enable_category_dropdowns']) && isset($_POST['ymm_enable_category_dropdowns']) == 1){
              $categoryDropdowns = 'yes';
            }
            update_option('ymm_enable_category_dropdowns', $categoryDropdowns);
            
            $categoryDropdowns = 'no';
            if (isset($_POST['ymm_enable_search_field']) && isset($_POST['ymm_enable_search_field']) == 1){
              $categoryDropdowns = 'yes';
            }
            update_option('ymm_enable_search_field', $categoryDropdowns);                        
          }         
        break;                                                                                      
      }
  
    }
      
  }		
	
 
  public function searchRestrictions(){

    if (!isset($_GET['search_query'])){
      return array();
    }  
    
    $searchWord = sanitize_text_field(stripslashes($_GET['search_query']));
    
    $restrictions = $this->_db->searchRestrictions($searchWord);
    
    echo json_encode($restrictions);
    exit;
  }
  
  	
  public function array2csv(array &$array)
  {
    if (count($array) == 0) {
     return null;
    }
    ob_start();
    $df = fopen("php://output", 'w');
    //fputcsv($df, array_keys(reset($array)));
    foreach ($array as $row) {
      fputcsv($df, $row);
    }
    fclose($df);
    return ob_get_clean();
  }


  public function download_send_headers($filename) {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
  }


}
