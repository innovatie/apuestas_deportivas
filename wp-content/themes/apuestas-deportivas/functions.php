<?php

function example_enqueue_styles() {
	wp_enqueue_style('parent-theme', get_template_directory_uri() .'/style.css');
}
add_action('wp_enqueue_scripts', 'example_enqueue_styles');


require get_stylesheet_directory() . '/inc/widgets/init2.php';
