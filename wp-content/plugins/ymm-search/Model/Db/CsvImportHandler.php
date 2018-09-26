<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pektsekye_Ymm_Model_Db_CsvImportHandler
{

    protected $_db;
    protected $_config; 
       
    protected $_delimiter = ',';
    
    
    public function __construct() 
    {
        include_once( Pektsekye_YMM()->getPluginPath() . 'Model/Db.php');		
        $this->_db =  new Pektsekye_Ymm_Model_Db(); 
            
        include_once( Pektsekye_YMM()->getPluginPath() . 'etc/config.php');		
        $this->_config = new Pektsekye_Ymm_Config();                                         
    }


    public function importFromCsvFile($file, $mode)
    {
      if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        throw new Exception(__('Please select a .csv file and then click the Import button', 'ymm-search'));
      }
      
      $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
      if (strtolower($fileExt) != 'csv') {
        throw new Exception(sprintf(__('Invalid file type "%s". Please upload a .csv file.', 'ymm-search'), $file['name']));
      }        
      
      $rows = array();
      
      ini_set("auto_detect_line_endings", true);
            
      if (($handle = fopen($file['tmp_name'], "r" )) !== false) {
        while (($row = fgetcsv($handle, 0, $this->_delimiter)) !== false) {
          $rows[] = $row;
        }
        fclose($handle);
      }

      if (count($rows) == 0) {
        throw new Exception(sprintf(__('The file "%s" is empty', 'ymm-search'), $file['name']));
      }

      $fieldNames = $this->_config->getCsvColumnNames(); 
                     
      foreach ($rows[0] as $k => $v) {
        $v = trim($v);
        if ($v != $fieldNames[$k]){
          throw new Exception(sprintf(__('The first row in the .csv file must contain correct column names. And the columns should have special order: "%s"', 'ymm-search'), implode($fieldNames,'","')));
          return;          
        } 
      }             
      
      $productIdsBySku = $this->_db->getProductIdsBySku();
      
      if ($mode == 'delete_old'){
        $this->_db->emptyTable($fieldNames);      
      }
      
      
      $data = array();
      
      $countRows = 0;    
      foreach ($rows as $rowIndex => $row) {
        
        if ($rowIndex == 0) // skip first row with column names
          continue;
    
        if (count($row) == 1 && $row[0] === null) // skip empty lines
          continue;
                             
        $d = array();       
        foreach ($fieldNames as $k => $v){
          $d[$v] = isset($row[$k]) ? trim($row[$k]) : '';
        }
        
        $productSku = $d['product_sku'];
        if (empty($productSku)){
          Pektsekye_YMM()->setMessage(sprintf(__('Row #%d was not imported. The "product_sku" field should not be empty.', 'ymm-search'), $rowIndex), 'error_lines');
          continue;                  
        }

        if (!isset($productIdsBySku[$productSku])){
          Pektsekye_YMM()->setMessage(sprintf(__('Row #%d was not imported. The product with SKU or ID "%s" does not exist.', 'ymm-search'), $rowIndex, $productSku), 'error_lines');
          continue;          
        }
      
        $d['product_sku'] = $productIdsBySku[$productSku]; //save product id instead of product SKU in the database 
                
        $d['year_from'] = (int) $d['year_from'];
        $d['year_to'] = (int) $d['year_to']; 
               
        if ($d['year_from'] > 0){
          if ($d['year_from'] < 1950){
            $d['year_from'] = 1950;
          } elseif ($d['year_from'] > 2030){
            $d['year_from'] = 2030;
          }                        
        }
        
        if ($d['year_to'] > 0){
          if ($d['year_to'] < 1950){
            $d['year_to'] = 1950;
          } elseif ($d['year_to'] > 2030){
            $d['year_to'] = 2030;
          }                        
        }   
        
        $data[] = $d;
        
        if ($countRows % 1000 == 0){
          $this->_db->addValues($data);
          $data = array();            
        }
        
        $countRows++;        
      }           
         
      if (count($data) > 0)
        $this->_db->addValues($data);
 
                       
    }

}
