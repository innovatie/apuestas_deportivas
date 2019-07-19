<?php

vc_map( array(
    "name" => __("Quote Block", 'goapostas'),
    "base" => "quote-block",
    "category" => 'Content',
    "params" => array(
        array(
            "type" => "textarea",
            "holder" => "div",
            "class" => "",
            "heading" => __("Text", 'goapostas'),
            "param_name" => "quote_text",
            "value" => '',
            "description" => ''
        )
    )
) );

add_shortcode('quote-block', 'goapostas_quote_block');
function goapostas_quote_block($atts) {
    $atts = shortcode_atts( array(
        'quote_text' => '',
    ), $atts );

    $out = '';

    if( $atts['quote_text'] ):

    $out .= '<div class="quote-block">';
        $out .= '<blockquote>' . $atts['quote_text'] . '</blockquote>';
    $out .= '</div>';

    endif;

    return $out;
}