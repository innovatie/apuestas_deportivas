<?php

vc_map( array(
    "name" => __("Gadget Payments"),
    "base" => "gadgets-payments",
    "category" => __('Content'),
    "params" => array(
        array(
            "type" => "checkbox",
            "heading" => __("Gadgets Payment", 'goapostas'),
            "param_name" => "payments",
            'value' => array(
                __( 'Mastercard', 'goapostas') => 'mastercard',
                __( 'Visa', 'goapostas') => 'visa',
                __( 'American Express', 'goapostas') => 'express',
                __( 'Paypal', 'goapostas') => 'paypal',
                __( 'Scrill', 'goapostas') => 'scrill',
                __( 'Boleto', 'goapostas') => 'boleto',
                __( 'VCreditos', 'goapostas') => 'vcreditos',
                __( 'Bank transfer', 'goapostas') => 'transferbank',
                __( 'SafetyPay', 'goapostas') => 'safetypay',
                __( 'AstroPay', 'goapostas') => 'astropay',
                __( 'Ecopayz', 'goapostas') => 'ecopayz',
                __( 'EntroPay', 'goapostas') => 'entropay',
                __( 'Bitcoin', 'goapostas') => 'bitcoin',
                __( 'Transferencia', 'goapostas') => 'transferencia',
            ),
            "description" => __("Please chose marked gadgets")
        )
    )
) );

add_shortcode('gadgets-payments', 'goapostas_gadgets_payments');
function goapostas_gadgets_payments($atts) {
    $atts = shortcode_atts( array(
        'payments' => '',
    ), $atts );

    $base_dir = get_stylesheet_directory_uri();
    $icons = [
        'mastercard' => $base_dir . '/assets/images/pay-mastercard@2x.png', 
        'visa' => $base_dir . '/assets/images/pay-visa@2x.png', 
        'express' => $base_dir . '/assets/images/pay-american-express@2x.png', 
        'paypal' => $base_dir . '/assets/images/pay-paypal@2x.png', 
        'scrill' => $base_dir . '/assets/images/pay-scrill@2x.png',
        'boleto' => $base_dir . '/assets/images/pay-boleto@2x.png',
        'vcreditos' => $base_dir . '/assets/images/pay-vcreditos.png',
        'transferbank' => $base_dir . '/assets/images/pay-transferbank.png',
        'safetypay' => $base_dir . '/assets/images/pay-safetypay.png',
        'astropay' => $base_dir . '/assets/images/pay-astropay.png',
        'ecopayz' => $base_dir . '/assets/images/pay-ecopayz.png',
        'entropay' => $base_dir . '/assets/images/pay-entropay.png',
        'bitcoin' => $base_dir . '/assets/images/pay-bitcoin.png',
        'transferencia' => $base_dir . '/assets/images/pay-transferencia.png',
    ];

    $style = 'max-height: 32px;';
    $out = '<div class="gadgets-devices" style="display:flex;-webkit-flex-wrap:wrap;-ms-flex-wrap:wrap;flex-wrap:wrap;-webkit-box-pack:start;-webkit-justify-content:flex-start;-ms-flex-pack:start;justify-content:flex-start;">';

    foreach ($icons as $icon => $icon_url) {

        $out .= '<div class="gadget-icon">';
        $out .= '<img src="' .$icon_url. '" alt="' .$icon. '" style="' .$style. '">';
            if( strpos( $atts['payments'], $icon ) !== false  ):
                $out .= '<span class="marked"></span>';
            endif;
        $out .= '</div>';

    }
    $out .= '</div>';

    return $out;
}