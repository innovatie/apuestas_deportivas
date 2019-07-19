<?php

vc_map( array(
    "name" => __("Gadget Devices"),
    "base" => "gadget-devices",
    "class"=> 'wpb_vc_single_image',
    "category" => __('Content'),
    "params" => array(
        array(
            "type" => "checkbox",
            "heading" => __("Devices", 'goapostas'),
            "param_name" => "devices",
            'value' => array(
                __( 'Apple', 'goapostas') => 'apple',
                __( 'Android', 'goapostas') => 'android',
                __( 'Desktop', 'goapostas') => 'desktop',
                __( 'Mobile', 'goapostas') => 'mobile',
            ),
            "description" => __("Please chose marked gadgets")
        )
    )
) );

add_shortcode('gadget-devices', 'goapostas_gadget_devices');
function goapostas_gadget_devices($atts) {
    $atts = shortcode_atts( array(
        'devices' => '',
    ), $atts );

    $base_dir = get_stylesheet_directory_uri();
    $icons = [
        'apple' => $base_dir . '/assets/images/device-apple.svg', 
        'android' => $base_dir . '/assets/images/device-android.svg', 
        'desktop' => $base_dir . '/assets/images/device-desktop.svg', 
        'mobile' => $base_dir . '/assets/images/device-mobile.svg', 
    ];

    $style = 'max-height: 21px;';
    $out = '<div class="gadgets-devices" style="display:flex;">';

    foreach ($icons as $icon => $icon_url) {

        $out .= '<div class="gadget-icon">';
        $out .= '<img src="' .$icon_url. '" alt="' .$icon. '" style="' .$style. '">';
            if( strpos( $atts['devices'], $icon ) !== false  ):
                $out .= '<span class="marked"></span>';
            endif;
        $out .= '</div>';

    }
    $out .= '</div>';

    return $out;
}