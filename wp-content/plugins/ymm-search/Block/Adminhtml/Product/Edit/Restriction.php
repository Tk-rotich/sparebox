<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Pektsekye_Ymm_Block_Adminhtml_Product_Edit_Restriction {


  protected $_db;  
     
   
	public function __construct() {

    include_once( Pektsekye_YMM()->getPluginPath() . 'Model/Db.php');		
		$this->_db =  new Pektsekye_Ymm_Model_Db();  	
  
    add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_product_tab') , 99 , 1 );	
    add_action( 'woocommerce_product_data_panels', array( $this, 'add_tab_fields') );   
    add_action( 'woocommerce_process_product_meta', array( $this, 'save_restriction') );
	}


  public function getFormatExplanationMessage() 
  {
    return __('Correct format is four columns in a row (three commas). Then a new line. Example:<br> Daihatsu, Altis, 1990, 2005<br>Toyota, Caldina, 1997, 2008 <br>Toyota, Camry, 1993, 2000<br><br>All models of one make:<br>Daihatsu, , 0, 0<br><br>All makes and models:<br> , , 0, 0', 'ymm-search');
  }
  

  public function hasYmmData() 
  {
    $limit = 1;
    $data = $this->_db->getVehicleData($limit);
    return count($data) > 0;
  }


   public function getAjaxUrl()     
  { 
    $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    return admin_url('admin-ajax.php', $protocol);        
  }


  public function add_product_tab($tabs) {
    $tabs['ymm'] = array(
        'label' => __( 'YMM', 'ymm-search' ),
        'target' => 'ymm_product_data',
        'class'  => array(),
        'priority' => 90            
    );
    return $tabs;
  }
  

  public function add_tab_fields() {
    global $post;
        
    $restriction = '';
    if (isset($post->ID)){
      $restriction = $this->_db->getProductRestrictionText((int)$post->ID);
    }
    
    ?>
    
    <div id="ymm_product_data" class="panel woocommerce_options_panel" style="display:none;">
      <?php
        // Purchase note
        woocommerce_wp_textarea_input(  array( 
          'id' => 'ymm_restriction', 
          'label' => __( 'Restriction', 'ymm-search' ),
          'style' => 'height: 17.5em;',
          'value' => $restriction,
          'desc_tip' => 'false', 
          'description' => $this->getFormatExplanationMessage()
        ) );
      ?>
      <?php if (!$this->hasYmmData()): ?> 
        <a href="#" id="ymm_sample"><?php echo __('Click here to fill the field with a sample restriction', 'ymm-search') ?></a>
      <?php endif; ?>
      <input type="hidden" id="ymm_restriction_changed" name="ymm_restriction_changed" value="0">     
      <div class="ymm-selector-container">
        <p class="form-field">
          <input type="text" name="partfinder_search_field" id="ymm_search_field" class="short ymm-search-field" value="" placeholder=" <?php echo __('... search here for makes in the restrictions of other products', 'ymm-search') ?>">
          <button type="button" class="button ymm-search-button"><?php echo __('Search', 'ymm-search') ?></button>
        </p>
        <div id="ymm_not_found" style="display:none"><?php echo __('No matches found.', 'ymm-search') ?></div>
        <p class="form-field">            
          <select class="ymm-result-select" size="10" multiple="multiple" disabled="disabled"></select>
          <br/>
          <button type="button" class="button ymm-add-button" disabled="disabled"><?php echo __('Add to restriction', 'ymm-search') ?></button>			     
        </p>
      </div>       
      <script type='text/javascript'>
        jQuery('#ymm_restriction').change(function(){jQuery('#ymm_restriction_changed').val(1)});
        jQuery('#ymm_sample').click(function(){
          var sample =
            "Daihatsu,Altis,2000,2008 \r\n"+
            "Lexus,ES300,1992,1997 \r\n"+
            "Lexus,GS300,1997,1999 \r\n"+
            "Lexus,RX300,1999,2003 \r\n"+
            "Toyota,Avalon,1999,2003 \r\n"+
            "Toyota,Caldina,1997,2008 \r\n"+
            "Toyota,Camry,1993,2000 \r\n"+
            "Toyota,Carina,1993,1998 \r\n"+
            "BMW,X5,2004,2008 \r\n";       
          jQuery('#ymm_restriction').val(sample).change();
          return false;
        });
        jQuery('#ymm_product_data').ymmRestriction({
          ajaxUrl : "<?php echo $this->getAjaxUrl(); ?>",
          toolTipMessage : "<?php echo str_replace('"', '&quot;', $this->getFormatExplanationMessage()); ?>"
        });        
      </script>
    </div>
  <?php
  }

 
  public function save_restriction( $post_id ){
         
    if (isset($_POST['ymm_restriction']) && isset($_POST['ymm_restriction_changed']) && $_POST['ymm_restriction_changed'] == 1){
      $restriction = sanitize_textarea_field(stripslashes($_POST['ymm_restriction']));
      try {                        
        $this->_db->saveProductRestrictionText($post_id, $restriction);                 
      } catch (Exception $e){
        WC_Admin_Meta_Boxes::add_error(__('YMM restriction was not saved.', 'ymm-search').' '.$this->getFormatExplanationMessage());                   
      }                    
    }
  }



}
