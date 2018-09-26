<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$message = $this->getMessage();
 
?>
<div><h3><?php echo __('Manage Year Make Model search', 'ymm-search'); ?></h3></div>
<?php if (isset($message['error'])): ?>
  <div id="woocommerce_errors" class="error"><p><?php echo $message['error']; ?></p></div>
<?php endif;?>
<?php if (isset($message['text'])): ?>    
  <div id="message" class="updated notice notice-success is-dismissible below-h2">
  <p><?php echo $message['text']; ?></p>
  <?php if (isset($message['error_lines'])): ?>
    <textarea rows="4" cols="100"><?php echo implode($message['error_lines'], "\r\n"); ?></textarea>
  <?php endif;?>     
  <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php echo __( 'Dismiss this notice.', 'woocommerce' );?></span></button></div>
<?php endif;?>
<div class="ymm-section">
  <div><h4><?php echo __('Import YMM Data', 'ymm-search'); ?>:</h4></div>    
  <form action="?page=ymm&action=importData" method="post" enctype="multipart/form-data">
      <fieldset class="ymm-fieldset">              
          <input type="file" name="import_file" class="input-file required-entry"/>
          <input type="checkbox" name="delete_old" id="ymm_delete_old" value="1"/>
          <label for="ymm_delete_old"><?php echo __('delete existing values', 'ymm-search'); ?></label>
          &nbsp;&nbsp;
          <input name="submit" id="submit" class="button button-primary" value="<?php echo __('Import CSV', 'ymm-search'); ?>" type="submit">                             
      </fieldset>
  </form>      
  <div><h4><?php echo __('Export YMM Data', 'ymm-search'); ?>:</h4></div>     
  <form id="export_form" action="?page=ymm&action=exportData" method="post" enctype="multipart/form-data">
      <fieldset class="ymm-fieldset">
          <input name="submit" id="submit" class="button button-primary" value="<?php echo $this->hasYmmData() ? __('Export CSV', 'ymm-search') :  __('Export sample CSV', 'ymm-search') ; ?>" type="submit">                                
      </fieldset>
  </form>
  <hr>  
  <div><h4><?php echo __('Configuration', 'ymm-search'); ?>:</h4></div>
  <form id="export_form" action="?page=ymm&action=updateConfig" method="post" enctype="multipart/form-data">
      <fieldset class="ymm-fieldset">
          <input type="checkbox" name="ymm_display_vehicle_fitment" id="ymm_fitment" value="1" <?php echo $this->getDisplayVehicleFitment() ? 'checked="checked"' : ''; ?>>
          <label for="ymm_fitment"><?php echo __('Display vehicle fitment on front-end product view page', 'ymm-search'); ?></label>
          <br/><br/>     
          <input type="checkbox" name="ymm_enable_category_dropdowns" id="ymm_category_dropdowns" value="1" <?php echo $this->getDisplayCategoryDropdowns() ? 'checked="checked"' : ''; ?>>
          <label for="ymm_category_dropdowns"><?php echo __('Display category drop-downs after selecting make, model', 'ymm-search'); ?></label>
          <br/><br/>    
          <input type="checkbox" name="ymm_enable_search_field" id="ymm_enable_search_field" value="1" <?php echo $this->getDisplaySearchField() ? 'checked="checked"' : ''; ?>>
          <label for="ymm_enable_search_field"><?php echo __('Display search text field after selecting make, model', 'ymm-search'); ?></label>
          <br/><br/>                                                       
          <input name="submit" id="submit" class="button button-primary" value="<?php echo __('Save Configuration', 'ymm-search') ; ?>" type="submit">      
      </fieldset>
  </form>     
</div>
     

