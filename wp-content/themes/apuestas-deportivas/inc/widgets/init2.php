<?php
/***************************************
*	My Widgets
***************************************/
add_action( 'vc_before_init', 'casa_de_acpostas_vc_before_init_actions' );
function casa_de_acpostas_vc_before_init_actions() {
    //require_once(get_stylesheet_directory_uri().'/inc/widgets/betting-block-v2.php');
    require_once( get_stylesheet_directory().'/inc/widgets/betting-block-v2.php' );
    require_once( get_stylesheet_directory().'/inc/widgets/bonus-loop.php' );
}