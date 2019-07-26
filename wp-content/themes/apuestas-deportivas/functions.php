<?php

function example_enqueue_styles() {
	wp_enqueue_style('parent-theme', get_template_directory_uri() .'/style.css');
}
add_action('wp_enqueue_scripts', 'example_enqueue_styles');


require get_stylesheet_directory() . '/inc/widgets/init2.php';

add_filter( 'body_class', 'custom_class' );
function custom_class( $classes ) {
	$show_only_email = get_field( 'show_only_email' ) ? get_field( 'show_only_email' ) : '';
	if ($show_only_email && $show_only_email == 1 ) {
		$classes[] = 'show-only-email';
	}else{
		$classes[] = 'show-all-form';
	}

	$type_hero = get_field( 'hero_type' ) ? get_field( 'hero_type' ) : '';
	if ($type_hero && $type_hero == 3 ) {
		$classes[] = 'hero-3';
	}

    return $classes;
}