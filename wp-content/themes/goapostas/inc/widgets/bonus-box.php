<?php

vc_map( array(
    "name" => __("Bonus Box", 'goapostas'),
    "base" => "bonus-box",
    "class"=> 'wpb_vc_single_image',
    "category" => 'Content',
    "params" => array(
        array(
            'group' => 'Box Top',
            "type" => "attach_image",
            "holder" => "img",
            "class" => "attachment-thumbnail",
            "heading" => __("Bonus Logo", 'goapostas'),
            "param_name" => "bonus_logo",
            "value" => '',
            "description" => ''
        ),
        array(
            'group' => 'Box Top',
            "type" => "textfield",
            "holder" => "div",
            "class" => "",
            "heading" => __("Tag 1", 'goapostas'),
            "param_name" => "bonus_tag1",
            "value" => '',
            "description" => ''
        ),
        array(
            'group' => 'Box Top',
            "type" => "textfield",
            "holder" => "div",
            "class" => "",
            "heading" => __("Tag 2", 'goapostas'),
            "param_name" => "bonus_tag2",
            "value" => '',
            "description" => ''
        ),

        array(
            'group' => 'Box Body',
            "type" => "textfield",
            "holder" => "div",
            "class" => "",
            "heading" => __("Title", 'goapostas'),
            "param_name" => "bonus_title",
            "value" => '',
            "description" => 'text format: 01:42'
        ),
        array(
            'group' => 'Box Body',
            "type" => "textarea",
            "holder" => "div",
            "class" => "",
            "heading" => __("Content", 'goapostas'),
            "param_name" => "bonus_content",
            "value" => '',
            "description" => 'text format: 01:42'
        ),
        array(
            'group' => 'Box Body',
            'type' => 'vc_link',
            'holder' => 'div',
            'class' => '',
            'heading' => __( 'Bonus Link' ),
            'param_name' => 'bonus_link',
            'value' => '',
            'description' =>'',
        )
    )
) );

add_shortcode('bonus-box', 'goapostas_bonus_box');
function goapostas_bonus_box($atts) {
    $atts = shortcode_atts( array(
        'bonus_logo' => '',
        'bonus_tag1' => '',
        'bonus_tag2' => '',
        'bonus_title' => '',
        'bonus_content' => '',
        'bonus_link' => '',
    ), $atts );

    ob_start();

    if( $atts['bonus_link'] || $atts['bonus_title'] || $atts['bonus_content'] ):

        $link = vc_build_link( $atts['bonus_link'] );
        $image = $atts['bonus_logo']? wp_get_attachment_image_src( $atts['bonus_logo'], 'thumbnail' ) : '';
    ?>
    <div class="bonus-box">
        <div class="header-box">
            <?php if( $image ): ?>
            <div>
                <img src="<?php echo $image[0] ?>" alt="">
            </div>
            <?php endif; ?>

            <div>
                <span class="tag"><?php echo $atts['bonus_tag1']; ?></span>
                <span class="tag"><?php echo $atts['bonus_tag2']; ?></span>
            </div>
        </div>
        <h4><?php echo $atts['bonus_title']; ?></h4>
        <p><?php echo $atts['bonus_content']; ?></p>
        <a href="<?php echo $link['url']; ?>" class="btn btn-green"><?php echo $link['title']; ?></a>        
    </div>
    <?php
    $out = ob_get_clean();
    endif;
    return $out;
}