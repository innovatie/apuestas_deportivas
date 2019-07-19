<?php

vc_map( array(
    "name" => __("Gadget Icon"),
    "base" => "gadget-icon",
    "class"=> 'wpb_vc_single_image',
    "category" => __('Content'),
    "params" => array(
        array(
            "type" => "attach_image",
            "holder" => "img",
            "class" => "attachment-thumbnail",
            "heading" => __("Image Icon"),
            "param_name" => "gadget_image",
            "value" => '',
            "description" => ''
        ),
        array(
            "type" => "dropdown",
            "holder" => "span",
            "class" => "",
            "heading" => __("Icon size"),
            "param_name" => "gadget_height",
            "value" => array('Default'=>'', 'Small'=>'small'),
            "description" => 'Default is 30px'
        ),
        array(
            "type" => "checkbox",
            "holder" => "",
            "class" => "",
            "heading" => __("Marked"),
            "param_name" => "gadget_marked",
            "value" => '1',
            "description" => 'mark this gadget?'
        )
    )
) );

add_shortcode('gadget-icon', 'goapostas_gadget_icon');
function goapostas_gadget_icon($atts) {
    $atts = shortcode_atts( array(
        'gadget_image' => '',
        'gadget_height' => '',
        'gadget_marked' => false,
    ), $atts );

    $image = $atts['gadget_image']? wp_get_attachment_image_src( $atts['gadget_image'] ) : '';
    $sizesh = [
        'small'=>'21px'
    ];

    $size_h = ( $atts['gadget_height'] && isset( $sizesh[$atts['gadget_height']] ) )? $sizesh[$atts['gadget_height']] : '';
    $style = $size_h? 'max-height: '.$size_h.';' : '';

    if( $image ):

    $out = '<div class="gadget-icon">';
    $out .= '<img src="' .$image[0]. '" alt="gadget icon" style="' .$style. '">';
        if( $atts['gadget_marked'] ):
            $out .= '<span class="marked"></span>';
        endif;
    $out .= '</div>';

    endif;

    return $out;
}