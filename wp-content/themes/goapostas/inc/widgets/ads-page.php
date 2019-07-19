<?php
vc_map( array(
    "name" => __("Ads Page", 'goapostas'),
    "base" => "ads-page",
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
            "type" => "attach_image",
            "holder" => "div",
            "class" => "",
            "heading" => __("Ad Image", 'goapostas'),
            "param_name" => "ad_image_url",
            "value" => '',
        ),
        array(
            "type" => "dropdown",
            "holder" => "div",
            "class" => "",
            "heading" => __("Ad Size", 'goapostas'),
            "param_name" => "ad_size",
            "value" => array('Default'=>'', 'Large'=>'size-l', 'Large Small'=>'size-l-sm', 'Extra Large'=>'size-xl', 'Square'=>'size-square'),
        ),
        array(
            "type" => "textarea_html",
            "holder" => "div",
            "class" => "",
            "heading" => __("Ad Code", 'goapostas'),
            "param_name" => "content",
            "value" => '',
        ),
    )
) );

add_shortcode('ads-page', 'goapostas_ads_page');
function goapostas_ads_page($atts,$content) {
    $atts = shortcode_atts( array(
        'ad_link' => '',
        'ad_image_url' => '',
        'ad_size' => ''
    ), $atts );

    $out = '';
    
    ob_start();

    $custom_link = $atts['ad_link'] ? $atts['ad_link'] : '';
    if ($custom_link) {
        ?>
        <div class="ads-block <?php echo $atts['ad_size']; ?>">
            <a href="<?php echo $atts['ad_link']; ?>">
                <img src="<?php echo wp_get_attachment_image_src($atts['ad_image_url'],'full')[0]; ?>">
            </a>
        </div>
        <?php
    }else{
        ?>
        <div class="ads-block <?php echo $atts['ad_size']; ?>">
            <?php echo do_shortcode(wpb_js_remove_wpautop($content, true)); ?>
        </div>
        <?php
    }

    $out = ob_get_clean();

    return $out;
}