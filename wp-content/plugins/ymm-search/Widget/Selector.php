<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pektsekye_Ymm_Widget_Selector extends WP_Widget {


	function __construct() {

		$widgetOptions = array(
			'description' => __( 'Year Make Model search box for WooCommerce products.', 'ymm-search')
		);

		parent::__construct(false, _x('YMM Search', 'Wgdet title in admin panel', 'ymm-search'), $widgetOptions);
	}


	// widget form creation
	function form($instance) {	

    // Check values
    if ( $instance && !empty($instance['title'])) {
         $title = esc_attr($instance['title']);
    } else {
         $title = __('Search by Vehicle', 'ymm-search');
    }
    if ( $instance && !empty($instance['filter_title'])) {
         $filterTitle = esc_attr($instance['filter_title']);
    } else {
         $filterTitle = __('Filter by Vehicle', 'ymm-search');
    }    
    if ( $instance && !empty($instance['as_filter_on_category_page'])) {
         $filterCategoryPage = esc_attr($instance['as_filter_on_category_page']);
    } else {
         $filterCategoryPage = 0;
    } 
    
    $garageEnabled = !isset($instance['garage_enabled']) || $instance['garage_enabled'] == 1 ? 1 : 0;  
    $removeFromGarageEnabled = !isset($instance['remove_from_garage_enabled']) || $instance['remove_from_garage_enabled'] == 1 ? 1 : 0;  
                     
    ?>
      <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Search Box Title', 'ymm-search'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
      </p>
      <p>
      <label for="<?php echo $this->get_field_id('filter_title'); ?>"><?php echo __('Filter Box Title', 'ymm-search'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('filter_title'); ?>" name="<?php echo $this->get_field_name('filter_title'); ?>" type="text" value="<?php echo $filterTitle; ?>" />
      </p>  
      <p>
      <input class="widefat" id="<?php echo $this->get_field_id('as_filter_on_category_page'); ?>" name="<?php echo $this->get_field_name('as_filter_on_category_page'); ?>" type="checkbox" value="1" <?php echo $filterCategoryPage == 1 ? 'checked="checked"' : ''; ?>/>      
      <label for="<?php echo $this->get_field_id('as_filter_on_category_page'); ?>"><?php echo __('Filter products on category page', 'ymm-search'); ?></label>      
      </p>   
      <p>
      <input class="widefat" id="<?php echo $this->get_field_id('garage_enabled'); ?>" name="<?php echo $this->get_field_name('garage_enabled'); ?>" type="checkbox" value="1" <?php echo $garageEnabled == 1 ? 'checked="checked"' : ''; ?>/>      
      <label for="<?php echo $this->get_field_id('garage_enabled'); ?>"><?php echo __('Enable Garage feature', 'ymm-search'); ?></label>      
      </p>
      <p>
      <input class="widefat" id="<?php echo $this->get_field_id('remove_from_garage_enabled'); ?>" name="<?php echo $this->get_field_name('remove_from_garage_enabled'); ?>" type="checkbox" value="1" <?php echo $removeFromGarageEnabled == 1 ? 'checked="checked"' : ''; ?>/>      
      <label for="<?php echo $this->get_field_id('remove_from_garage_enabled'); ?>"><?php echo __('Enable "Remove from Garage" link', 'ymm-search'); ?></label>      
      </p>                    
    <?php
	}


	// widget update
	function update($new_instance, $old_instance) {
    $instance = $old_instance;
    // Fields
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['filter_title'] = strip_tags($new_instance['filter_title']);
    $instance['as_filter_on_category_page'] = isset($new_instance['as_filter_on_category_page']) && $new_instance['as_filter_on_category_page'] == 1 ? 1 : 0; 
    $instance['garage_enabled'] = isset($new_instance['garage_enabled']) && $new_instance['garage_enabled'] == 1 ? 1 : 0;
    $instance['remove_from_garage_enabled'] = isset($new_instance['remove_from_garage_enabled']) && $new_instance['remove_from_garage_enabled'] == 1 ? 1 : 0;                
    return $instance;
	}


	// widget display
	function widget($args, $instance) {
    extract( $args );

    $searchTitle = isset($instance['title']) ? $instance['title'] : '';
    $filterTitle = isset($instance['filter_title']) ? $instance['filter_title'] : '';
    $filterCategory = isset($instance['as_filter_on_category_page']) && $instance['as_filter_on_category_page'] == 1; 
    $garageEnabled = isset($instance['garage_enabled']) && $instance['garage_enabled'] == 1; 
    $removeFromGarageEnabled = !isset($instance['remove_from_garage_enabled']) || $instance['remove_from_garage_enabled'] == 1;
              
    $title = $filterCategory && is_product_category() ? $filterTitle : $searchTitle;
    
    echo $before_widget;
    // Display the widget

    echo '<div class="widget-text wp_widget_plugin_box">';

    // Check if title is set
    if ( $title ) {
      echo $before_title . $title . $after_title;
    }

    include_once( Pektsekye_YMM()->getPluginPath() . 'Block/Selector.php'); 
         
    $block = new Pektsekye_Ymm_Block_Selector();
    if (isset($args['id'])){
      $block->setWidgetId($args['id']);
    }
    $block->setSearchTitle($searchTitle);     
    $block->setFilterCategoryPage($filterCategory);
    $block->setGarageEnabled($garageEnabled);
    $block->setRemoveFromGarageEnabled($removeFromGarageEnabled);         
    $block->page_init();

    echo '</div>';
    echo $after_widget;
	}
}