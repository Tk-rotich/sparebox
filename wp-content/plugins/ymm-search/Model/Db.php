<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pektsekye_Ymm_Model_Db
{


    protected $_wpdb;
    protected $_mainTable;
    protected $_config;         

        
    public function __construct() {
			global $wpdb;
			
			$this->_wpdb = $wpdb;   
      $this->_mainTable = "{$wpdb->prefix}ymm"; 
            
      include_once( Pektsekye_YMM()->getPluginPath() . 'etc/config.php');		
      $this->_config = new Pektsekye_Ymm_Config();              
    }    



     public function fetchColumnValues($params = array(), $categoryId = 0)     
    {
      $values = array();

      $whereProducts = '';
      if ($categoryId > 0){
        $productIds = $this->getProductIdsOfCategory($categoryId);
        if (count($productIds) > 0){        
          $whereProducts = ' AND product_id IN ('.implode(',', $productIds).')';
        }
      }
      
      $nextlevel = count($params);
            
      if ($nextlevel == 0){
        $select = "SELECT DISTINCT make FROM {$this->_mainTable} WHERE make != '' {$whereProducts} ORDER BY make";
        $values = (array) $this->_wpdb->get_col($select);
      } else if ($nextlevel == 1){
        $select = "SELECT DISTINCT model FROM {$this->_mainTable} WHERE make = '".esc_sql($params[0])."' AND model != '' {$whereProducts} ORDER BY model";
        $values = (array) $this->_wpdb->get_col($select);      
      } else {
        $select = "SELECT DISTINCT year_from, year_to FROM {$this->_mainTable} WHERE make = '".esc_sql($params[0])."' AND model = '".esc_sql($params[1])."' AND (year_from != 0 or year_to != 0) {$whereProducts}";      
        $rows = (array) $this->_wpdb->get_results($select, ARRAY_A);

        $y = array();
        
        foreach ($rows as $r) {

          $from = (int) $r['year_from'];
          $to = (int) $r['year_to'];	

          if ($from == 0){
            $y[$to] = 1;          
          } elseif ($to == 0){
            $y[$from] = 1;
          } elseif ($from == $to){
            $y[$from] = 1;
          } elseif ($from < $to){          	
            while ($from <= $to){
              $y[$from] = 1;
              $from++;
            }            
          }
        } 

        krsort($y);
                  
        $values = array_keys($y);
      }
      
      return $values;             
    }
    
    
    
     public function filterVehiclesForCategory($vehicles, $categoryId)     
    {
      $filteredVehicles = array();
      
      $productIds = $this->getProductIdsOfCategory((int)$categoryId);
      
      if (count($productIds) == 0)
        return array();
            
      foreach((array)$vehicles as $vehicle){
        $values = explode(',', $vehicle);
        if (count($values) != 3){
          continue;
        }
        $year = (int) $values[2];
        $select = "SELECT make FROM {$this->_mainTable} WHERE make = '".esc_sql($values[0])."' AND model = '".esc_sql($values[1])."' AND (year_from <= {$year} and year_to >= {$year}) AND product_id IN (" . implode(',', $productIds) . ") LIMIT 1";
        $result = $this->_wpdb->get_var($select);

        if ($result){
          $filteredVehicles[] = $vehicle;    
        }   
      }
      return $filteredVehicles;             
    }
    
    
          
     public function getProductIds($values)     
    {    
      $level = count($values);
      
      if ($level == 1){
        $select = "SELECT DISTINCT product_id FROM {$this->_mainTable} WHERE make = '".esc_sql($values[0])."' OR make = ''";
      } else if ($level == 2){
        $select = "SELECT DISTINCT product_id FROM {$this->_mainTable} WHERE (make = '".esc_sql($values[0])."' or make = '') AND (model = '".esc_sql($values[1])."' or model = '')";     
      } else {
        $year = (int) $values[2];
        $select = "SELECT DISTINCT product_id FROM {$this->_mainTable} WHERE (make = '".esc_sql($values[0])."' or make = '') AND (model = '".esc_sql($values[1])."' or model = '') AND ((year_from <= {$year} and year_to >= {$year}) or (year_from=0  and year_to=0)) ";
      }

      return (array) $this->_wpdb->get_col($select);    
    }



     public function searchRestrictions($query)     
    {
      $where = '';
  
      $words = preg_split("/\s+/", $query);           
      foreach ($words as $word){
        $w = "make LIKE '%".esc_sql($word)."%' OR model LIKE '%".esc_sql($word)."%'" ;  
        $where .= ($where != '' ? ' AND ' : '') . "({$w})";          
      }
  
      $select = "SELECT DISTINCT CONCAT_WS(', ', make, model, year_from, year_to) as restriction  FROM {$this->_mainTable} WHERE {$where} ORDER BY make, model, year_from, year_to LIMIT 64;";

      return (array) $this->_wpdb->get_col($select);         
    }
    
    
        
     public function getSampleVehicleData()     
    {
      $data = array(
        array("H4184","Daihatsu","Altis","2000","2008"),
        array("PPF5471","Lexus","ES300","1992","1997"),
        array("PPF5471","Lexus","GS300","1997","1999"),
        array("PPF5471","Lexus","RX300","1999","2003"),
        array("PPF5497","Toyota","Avalon","1999","2003"),
        array("PPF5497","Toyota","Caldina","1997","2008"),
        array("PPF5493","Toyota","Camry","1993","2000"),
        array("PPF5077","Toyota","Carina","1993","1998"),
        array("H4061","BMW","X5","2004","2008")      
      );
      
      $numberOfProducts = 6;
      $productIdsBySku = $this->getProductIdsBySku($numberOfProducts);
      
      if (count($productIdsBySku) == $numberOfProducts){ // if there are enough products we can use existing product SKUs for sample data
        $productSkus = array_keys($productIdsBySku);
        $data = array(
          array($productSkus[0],"Daihatsu","Altis","2000","2008"),
          array($productSkus[1],"Lexus","ES300","1992","1997"),
          array($productSkus[1],"Lexus","GS300","1997","1999"),
          array($productSkus[1],"Lexus","RX300","1999","2003"),
          array($productSkus[2],"Toyota","Avalon","1999","2003"),
          array($productSkus[2],"Toyota","Caldina","1997","2008"),
          array($productSkus[3],"Toyota","Camry","1993","2000"),
          array($productSkus[4],"Toyota","Carina","1993","1998"),
          array($productSkus[5],"BMW","X5","2004","2008")      
        );
      }
      
      return $data;     
    }      

      
     public function getProductRestrictions($productId)     
    {
      $productId = (int) $productId;    
         
      $select = "
        SELECT make, model, year_from, year_to  
        FROM {$this->_mainTable} 
		    WHERE product_id = {$productId} 
		    ORDER BY make 	            
      ";
      
      return (array) $this->_wpdb->get_results($select, ARRAY_A);         
    }
    
    
          
     public function getProductRestrictionText($productId)     
    {          
      $text = '';
    
      $result = $this->getProductRestrictions($productId);     
      foreach ($result as $row){
        $text .= "{$row['make']}, {$row['model']}, {$row['year_from']}, {$row['year_to']}\n";
      }
      
      return $text;     
    }
    
    
    
     public function saveProductRestrictionText($productId, $restriction)     
    {
      $productId = (int) $productId;    
         


      $data = array();
      
      $fieldNames = $this->_config->getCsvColumnNames();
      
      array_shift($fieldNames);  //we don't need the first product_id field in the restriction
          
      $numberOfFields = count($fieldNames);
      
      $lines = explode("\n", $restriction);
      foreach ($lines as $line) {
        $line = trim($line);
        
        if (empty($line))
          continue;
        
        $values = explode(',', $line);
        
        if (count($values) != $numberOfFields){
          throw new Exception();          
          return;          
        }

        $make = trim($values[0]);
        $model = trim($values[1]);
        $yearFrom = (int) $values[2];
        $yearTo = (int) $values[3];
               
        if ($yearFrom > 0){
          if ($yearFrom < 1950){
            $yearFrom = 1950;
          } elseif ($yearFrom > 2030){
            $yearFrom = 2030;
          }                        
        }
        
        if ($yearTo > 0){
          if ($yearTo < 1950){
            $yearTo = 1950;
          } elseif ($yearTo > 2030){
            $yearTo = 2030;
          }                        
        }        
                                
        $data[] = array($productId, $make, $model, $yearFrom, $yearTo);        
      }
      
      $this->_wpdb->query("DELETE FROM {$this->_mainTable} WHERE product_id = {$productId}"); 
      
      if (count($data) > 0){
        $this->addValues($data);
      }                   
    }    



     public function getVehicleData($limit = 0)     
    {
      $queryLimit = $limit > 0 ? " LIMIT {$limit} " : '';    
    
      $select = "
        SELECT IF(LENGTH(postmeta.meta_value)>0, postmeta.meta_value, postmeta.post_id) as product_sku, ymm.make, ymm.model, ymm.year_from, ymm.year_to  
        FROM {$this->_wpdb->posts} AS posts 
		    JOIN {$this->_wpdb->postmeta} AS postmeta 
		      ON postmeta.post_id = posts.ID 
        JOIN {$this->_mainTable} ymm 
          ON ymm.product_id = posts.ID 
		    WHERE posts.post_type IN ('product') AND postmeta.meta_key = '_sku' 
		    ORDER BY posts.ID 
		    {$queryLimit}		            
      ";
      
      return (array) $this->_wpdb->get_results($select, ARRAY_N);     
    }              
      
      
      
    public function getProductIdsBySku($limit = 0)
    {
      $queryLimit = $limit > 0 ? " LIMIT {$limit} " : '';
      
      $select = "
        SELECT IF(LENGTH(postmeta.meta_value)>0, postmeta.meta_value, postmeta.post_id) as product_sku, postmeta.post_id as product_id 
        FROM {$this->_wpdb->posts} AS posts 
		    JOIN {$this->_wpdb->postmeta} AS postmeta 
		      ON postmeta.post_id = posts.ID 
		    WHERE posts.post_type IN ('product') AND postmeta.meta_key = '_sku'
		    {$queryLimit}          
      ";
      $result = (array) $this->_wpdb->get_results($select, ARRAY_A);

      $productIds = array();
      foreach ($result as $row){
        $productIds[$row['product_sku']] = $row['product_id'];
      }
       
      return $productIds;   
    }      
      
      
      
    public function addValues($data)
    {         
      $valuesStr = '';    
      foreach ($data as $values){
        $cell = '';
        foreach ($values as $value)
          $cell .= ",'" . esc_sql(trim($value)). "'";
                       
        $valuesStr .= ($valuesStr != '' ? ',' : '') . "(NULL{$cell})";     
      }
      
      $this->_wpdb->query("INSERT IGNORE INTO {$this->_mainTable} VALUES {$valuesStr}");                  
    }   



    function getProductIdsOfCategory($categoryId) 
    {    
      $query = new WP_Query( array(
          'post_type' => 'product',
          'post_status' => 'publish',
          'posts_per_page' => -1,          
          'fields' => 'ids', 
          'tax_query' => array(
              array(
                  'taxonomy' => 'product_cat',
                  'field' => 'term_id',
                  'terms' => (int) $categoryId,
                  'operator' => 'IN',
              )
          )
      ) );

      return (array) $query->posts;    
    }



    public function emptyTable()
    {      
      $this->_wpdb->query("TRUNCATE TABLE {$this->_mainTable}"); 
    }	
       
}
