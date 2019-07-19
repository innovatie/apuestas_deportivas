<?php


vc_map( array(
    "name" => __("Quick Navigation", 'goapostas'),
    "base" => "quick-navigation",
    "category" => 'Content',
    "params" => array(
        array(
            "type" => "textarea_html",
            "holder" => "div",
            "class" => "",
            "heading" => __("Navigation Content", 'goapostas'),
            "param_name" => "content",
            "value" => '',
            "description" => __('Please use a list with linked text', 'goapostas')
        )
    )
) );

add_shortcode('quick-navigation', 'goapostas_quick_navigation');
function goapostas_quick_navigation($atts, $content) {
    $atts = shortcode_atts( array(
        'content' => '',
    ), $atts );

    $out = '';

    if( $content ):

    $out .= '<div class="quick-nav">';
        $out .= $content;
    $out .= '</div>';

    endif;

    return $out;
}