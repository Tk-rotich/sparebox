<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="ymm-vehicle-fitment">
  <table id="ymm_applicable_list">
    <tr>
      <th> <?php echo __('Make', 'ymm-search') ?> </th>
      <th> <?php echo __('Model', 'ymm-search') ?> </th>
      <th> <?php echo __('Year', 'ymm-search') ?> </th>
    </tr>
  <?php foreach($this->getFormatedRestrictions() as $row): ?>
    <tr>
      <td> <?php echo $row['make'] ?> </td>
      <td> <?php echo $row['model'] ?> </td>
      <td> <?php echo $row['year'] ?> </td>
    </tr>
  <?php endforeach; ?>
  </table>       			          		      	          		      	      
</div>
<input type="button" id="seeMoreRecords" value="<?php echo __('SHOW MORE', 'ymm-search') ?>">
<input type="button" id="seeLessRecords" value="<?php echo __('SHOW LESS', 'ymm-search') ?>">
<script type="text/javascript">

var trs = jQuery("#ymm_applicable_list tr");
var btnMore = jQuery("#seeMoreRecords");
var btnLess = jQuery("#seeLessRecords");
var trsLength = trs.length;
var currentIndex = 10;

trs.hide();
trs.slice(0, 10).show(); 
checkButton();

btnMore.click(function (e) { 
    e.preventDefault();
    jQuery("#ymm_applicable_list tr").slice(currentIndex, currentIndex + 10).show();
    currentIndex += 10;
    checkButton();
});

btnLess.click(function (e) { 
    e.preventDefault();
    jQuery("#ymm_applicable_list tr").slice(currentIndex - 10, currentIndex).hide();          
    currentIndex -= 10;
    checkButton();
});

function checkButton() {
    var currentLength = jQuery("#ymm_applicable_list tr:visible").length;
    
    if (currentLength >= trsLength) {
        btnMore.hide();            
    } else {
        btnMore.show();   
    }
    
    if (trsLength > 10 && currentLength > 10) {
        btnLess.show();
    } else {
        btnLess.hide();
    }
    
}

</script>