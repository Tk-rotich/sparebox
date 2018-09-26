<?php

function online_shop_child_enqueue() {
    wp_enqueue_style( 'child_style', get_template_directory_uri() . '/style.css',array(),'1.0.0' );
    wp_enqueue_style( 'online-shop-parent',
        get_stylesheet_directory_uri() . '/../online-shop/style.css',array(),
        wp_get_theme()->get('Version')
    );
}

add_action( 'wp_enqueue_scripts', 'online_shop_child_enqueue' );