<?php
/*
Plugin Name: Sparebox Filter Plugin
Plugin URI: #
Description: Plugin to filter spares based on vehicle's specification.
Version: 1.0
Author: Titus
*/
defined( 'ABSPATH' ) or exit;

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	return;
}


class Sparebox_Filter extends WP_Widget {

	function __construct()
	{
		parent::__construct(
				'woocommerce_spareboxFilter',
				__( 'Sparebox Filter', '_spareboxFilter' ),
				array('description' => __( 'This plugin help display a list of filter spares based on vehicles specification.', '_spareboxFilter' )) 
				);
    }
    
    public function widget( $args, $instance ) {
		echo $args['before_widget'] ?>

		<div class="sparebox-wrapper">
		<label>Select Vehicle</label>
		<select id="sparebox_make_dropdown" onChange="populateModel()">
<option>Make</option>
		

<?php

$taxonomy     = 'product_cat';
$orderby      = 'name';  
$show_count   = 0;      // 1 for yes, 0 for no
$pad_counts   = 0;      // 1 for yes, 0 for no
$hierarchical = 1;      // 1 for yes, 0 for no  
$title        = '';  
$empty        = 0;

$args = array(
	   'taxonomy'     => $taxonomy,
	   'orderby'      => $orderby,
	   'show_count'   => $show_count,
	   'pad_counts'   => $pad_counts,
	   'hierarchical' => $hierarchical,
	   'title_li'     => $title,
	   'hide_empty'   => $empty
);
$all_categories = get_categories( $args );
foreach ($all_categories as $cat) {
  if($cat->category_parent == 0) {
	  $category_id = $cat->term_id;       
	  echo  '<option  value="'. $category_id .'">'. $cat->name . '</option>';
  }       
}
?>

</select>
<br/>
<select id="sparebox_model_dropdown" disabled>
<option>Model</option>
</select>
<select  id="sparebox_engine_dropdown" onChange="populateFilterURL()">
<?php   
		$engineFuel = get_terms( 'enginefuel', 'orderby=count&hide_empty=0' );
		echo '<option>Fuel Engine</option>';
		foreach($engineFuel as $fuelType){
			if($fuelType->parent == 0) {
				echo '<optgroup label='.$fuelType->name.'>';
				$terms = get_terms( 'enginefuel', array( 'parent' => $fuelType->term_id, 'orderby' => 'slug', 'hide_empty' => false ) );
				foreach ( $terms as $term ) {
					echo '<option value="'. $term->term_id .'">' . $term->name . '</option>';   
				}
				echo '</optgroup>';
			}
			
		} 
?>
</select>
<a id="filter_button" class="button product_type_simple add_to_cart_button ajax_add_to_cart">GO</a>

 </div>  <!-- End of div wrapper -->
   <?php
	echo $args['after_widget'];
	}

}
add_action( 'widgets_init', function(){
	register_widget( 'Sparebox_Filter' );
} );

add_action( 'wp_enqueue_scripts', 'ajax_sparebox_filter_scripts' );
function ajax_sparebox_filter_scripts() {
	wp_enqueue_script( 'sparebox', plugins_url( '/app.js', __FILE__ ), array('jquery'), '2.0');
	wp_enqueue_style( 'sparebox-style', plugins_url( '/app.css', __FILE__ ), null);
	
	wp_localize_script( 'sparebox', 'postModel', array(
		'ajax_url' => admin_url( 'admin-ajax.php' )
	));
}

add_action( 'wp_ajax_nopriv_post_sparebox_model', 'post_sparebox_model' );
add_action( 'wp_ajax_post_sparebox_model', 'post_sparebox_model' );
add_action( 'wp_ajax_populate_filter_url', 'populate_filter_url' );

function populate_filter_url(){
	echo esc_url_raw( add_query_arg(array(
		'make' => $_POST['sparebox_make'],
		'model' => $_POST['sparebox_model'],
		'engine' => $_POST['sparebox_engine']
	), site_url( '/filtered-categories' ) ) );
	die();
}
function post_sparebox_model() {
	$output = array();
	$catId = $_POST['sparebox_cat_id'];
	$taxonomy     = 'product_cat';
	$orderby      = 'name';  
	$show_count   = 0;      // 1 for yes, 0 for no
	$pad_counts   = 0;      // 1 for yes, 0 for no
	$hierarchical = 1;      // 1 for yes, 0 for no  
	$title        = '';  
	$empty        = 0;

	
	$args = array(
		'taxonomy'     => $taxonomy,
		'child_of'     => 0,
		'parent'       => $catId,
		'orderby'      => $orderby,
		'show_count'   => $show_count,
		'pad_counts'   => $pad_counts,
		'hierarchical' => $hierarchical,
		'title_li'     => $title,
		'hide_empty'   => $empty
);
$models = array();
$modelID = array();
	$categories = get_categories( $args );
	foreach($categories as $cat2) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {  
		$models[] = $category->name;
		$modelID[] = $category->term_id;
		}

		// if($cat2->category_parent == 2) {
			$category_id = $cat2->term_id;       
			
			$args2 = array(
					'taxonomy'     => $taxonomy,
					'child_of'     => 0,
					'parent'       => $category_id,
					'orderby'      => $orderby,
					'show_count'   => $show_count,
					'pad_counts'   => $pad_counts,
					'hierarchical' => $hierarchical,
					'title_li'     => $title,
					'hide_empty'   => $empty
			);
			$sub_cats = get_categories( $args2 );
			if($sub_cats) {
				foreach($sub_cats as $sub_category) {
					//echo  $sub_category->name ;
					// if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
						array_push($output, array("category"=>$cat2->name, "sub_category"=>$sub_category->name, "sub_cat_id"=>$sub_category->term_id));  
					 	$models2[] = $sub_category->name;
					// $modelID[] = $sub_category->term_id;
					// }
				}   
			}
		//} 
		
	}
	echo json_encode($output);
	die();
}


