<?php
vc_map( array(
    "name" => __("Ads Block", 'goapostas'),
    "base" => "ads-block",
    "category" => __('Content'),
    "params" => array(
        array(
            "type" => "textfield",
            "holder" => "div",
            "class" => "",
            "heading" => __("Ad Link", 'goapostas'),
            "param_name" => "ad_link",
            "value" => '',
        ),
        array(
            "type" => "textfield",
            "holder" => "div",
            "class" => "",
            "heading" => __("Ad Image url", 'goapostas'),
            "param_name" => "ad_image_url",
            "value" => '',
        ),
        array(
            "type" => "dropdown",
            "holder" => "div",
            "class" => "",
            "heading" => __("Ad Size", 'goapostas'),
            "param_name" => "ad_size",
            "value" => array('Default'=>'', 'Large'=>'size-l', 'Large Small'=>'size-l-sm'),
        ),
        array(
            "type" => "dropdown",
            "holder" => "div",
            "class" => "",
            "heading" => __("Ad Position", 'goapostas'),
            "param_name" => "ad_position",
            "value" => array('Default'=>'', 'Floating to Right'=>'pos-float-r'),
        )
    )
) );

add_shortcode('ads-block', 'goapostas_ads_block');
function goapostas_ads_block($atts) {
    $atts = shortcode_atts( array(
        'ad_link' => '',
        'ad_image_url' => '',
        'ad_size' => '',
        'ad_position' => '',
    ), $atts );

    $out = '';

    // if( $atts['ad_link'] || $atts['ad_image_url'] ):

    ob_start();
    ?>
    <div class="ads-block <?php echo $atts['ad_size'] . ' ' . $atts['ad_position'];  ?>">
        <span>AD</span>
    </div>
    <?php
    $out = ob_get_clean();
    // endif;

    return $out;
}