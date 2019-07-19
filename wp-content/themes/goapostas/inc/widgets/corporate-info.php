<?php

vc_map( array(
    "name" => __("Corporate Info Block", 'goapostas'),
    "base" => "corporate-info-block",
    "category" => __('Content'),
    "params" => array(
        array(
            'group' => __('Left Column', 'goapostas'),
            "type" => "textarea_html",
            "holder" => "div",
            "heading" => __("Content", 'goapostas'),
            "param_name" => "content",
            'value' => '',
            "description" => ''
        ),

        array(
            'group' => __('Center Column', 'goapostas'),
            "type" => "textfield",
            "holder" => "div",
            "heading" => __("Title", 'goapostas'),
            "param_name" => "col2_title",
            'value' => '',
            "description" => ''
        ),
        array(
            'group' => __('Center Column', 'goapostas'),
            "type" => "textarea",
            "holder" => "div",
            "heading" => __("Content", 'goapostas'),
            "param_name" => "col2_text",
            'value' => '',
            "description" => ''
        ),

        array(
            'group' => __('Right Column', 'goapostas'),
            "type" => "textfield",
            "heading" => __("Title", 'goapostas'),
            "param_name" => "col3_title",
            'value' => '',
            "description" => ''
        ),
        array(
            'group' => __('Right Column', 'goapostas'),
            "type" => "textarea",
            "heading" => __("Phone", 'goapostas'),
            "param_name" => "col3_text",
            'value' => '',
            "description" => ''
        ),
        array(
            'group' => __('Right Column', 'goapostas'),
            "type" => "textfield",
            "heading" => __("Title", 'goapostas'),
            "param_name" => "col3_title2",
            'value' => '',
            "description" => ''
        ),
        array(
            'group' => __('Right Column', 'goapostas'),
            "type" => "textarea",
            "heading" => __("Email", 'goapostas'),
            "param_name" => "col3_text2",
            'value' => '',
            "description" => ''
        )
    )
) );

add_shortcode('corporate-info-block', 'goapostas_corporate_info_block');
function goapostas_corporate_info_block($atts, $content) {
    $atts = shortcode_atts( array(
        'col2_title' => '',
        'col2_text' => '',

        'col3_title' => '',
        'col3_text' => '',
        'col3_title2' => '',
        'col3_text2' => '',
    ), $atts );

    ob_start();
    ?>
    <div class="corporate-info-block">
        <div>
            <?php if($content): 
                echo $content;
            endif; ?>
        </div>

        <div>
            <h6><?php echo $atts['col2_title']; ?></h6>
            <p><?php echo $atts['col2_text'];  ?></p>
        </div>
        <div>
            <div class="box-bg">
                <h6><?php echo $atts['col3_title'];  ?></h6>
                <p><a href="tel:<?php echo $atts['col3_text'];  ?>"><?php echo $atts['col3_text'];  ?></a></p>
            </div>
            <?php if( $atts['col3_title2'] || $atts['col3_text2'] ): ?>
            <div class="box-bg">
                <h6><?php echo $atts['col3_title2']; ?></h6>
                <p><a href="mailto:<?php echo $atts['col3_text2']; ?>"><?php echo $atts['col3_text2']; ?></a></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    $out = ob_get_clean();
    return $out;
}