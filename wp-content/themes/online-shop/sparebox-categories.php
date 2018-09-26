<?php /* Template Name: Search */ 
get_header();
?>
<div id="primary" class="content-area">
		<main id="main" class="site-main">

        <?php
            $make = $_GET['make'];
            $model = $_GET['model'];
            $engine = $_GET['engine'];

            $args = array(
                'post_type'             => 'product',
                'post_status'           => 'publish',
                'ignore_sticky_posts'   => 1,
                'posts_per_page'        => '12',
                'tax_query'             => array(
                    array(
                        'taxonomy'      => 'product_cat',
                        'field' => 'term_id',
                        'terms'         => array($make, $model, $engine),
                        'operator'      => 'IN' 
                    ),
                    array(
                        'taxonomy'      => 'product_visibility',
                        'field'         => 'slug',
                        'terms'         => 'exclude-from-catalog', 
                        'operator'      => 'NOT IN'
                    )
                )
            );
            $loop = new WP_Query($args);

            while ( $loop->have_posts() ) : $loop->the_post(); global $product; 
            get_template_part( 'template-parts/content', 'product' );
            endwhile;
                the_posts_navigation();
               ?>

           </main><!-- #main -->
	</div><!-- #primary -->
    <?php

get_sidebar( 'left' );
get_sidebar();
get_footer(); 
?>