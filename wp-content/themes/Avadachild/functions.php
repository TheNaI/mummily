<?php
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles',11 );
function theme_enqueue_styles()
{
    wp_enqueue_style('parent-style', get_stylesheet_directory_uri() . '/css/index.css');

}