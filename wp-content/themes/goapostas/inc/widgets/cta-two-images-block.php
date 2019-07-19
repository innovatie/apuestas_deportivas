<?php 
// feactured block
vc_map( array(
    "name" => __("CTA Two Image Block"),
    "base" => "cta-two-image-block",
    "class"=> 'wpb_vc_single_image',
    "category" => __('Content'),
    "params" => array(
        array(
            'group' => 'CTA Image',
            "type" => "attach_image",
            "holder" => "img",
            "class" => "attachment-thumbnail",
            "heading" => __("CTA Image"),
            "param_name" => "cta_image",
            "value" => '',
            "description" => ''
        ),
        array(
            'group' => 'CTA Image',
            'type' => 'vc_link',
            'holder' => 'div',
            'class' => '',
            'heading' => __( 'CTA Link' ),
            'param_name' => 'cta_link',
            'value' => '',
            'description' =>'',
        ),
        array(
            'group' => 'CTA Image',
            "type" => "textfield",
            "holder" => "div",
            "class" => "",
            "heading" => __("CTA Time"),
            "param_name" => "cta_time",
            "value" => '',
            "description" => 'text format: 01:42'
        ),
        array(
            'group' => 'CTA Image',
            "type" => "dropdown",
            "holder" => "div",
            "class" => "attachment-thumbnail",
            "heading" => __("CTA location"),
            "param_name" => "cta_location",
            "value" => array('Center'=>'center', 'Bottom left'=>'btm-l'),
            "description" => ''
        ),

        array(
            'group' => 'Aside Image',
            "type" => "attach_image",
            "holder" => "img",
            "class" => "attachment-thumbnail",
            "heading" => __("Side Image"),
            "param_name" => "cta_aside_image",
            "value" => '',
            "description" => ''
        ),
    )
) );

add_shortcode('cta-two-image-block', 'goapostas_cta_two_image_block');
function goapostas_cta_two_image_block($atts) {
    $atts = shortcode_atts( array(
        'cta_image' => '',
        'cta_link' => '',
        'cta_time' => '',
        'cta_location'  => 'centered',
        'cta_aside_image' => ''
    ), $atts );

    $link = vc_build_link( $atts['cta_link'] );
    $image = $atts['cta_image']? wp_get_attachment_image_src( $atts['cta_image'], 'medium_large' ) : '';
    $aside_img = $atts['cta_aside_image']? wp_get_attachment_image_src( $atts['cta_aside_image'], 'large' ) : '';

    $out = '';

    if( ( $atts['cta_image'] || $atts['cta_link'] ) && $aside_img ):

        $out .= '<div class="cta-two-image-wrap">';

        $out .= '<div class="cta-image-block cta-'. $atts['cta_location'] .'">';
        $out .= goapostas_cta_event_link( $atts );

        $out .= '<picture>';
        if( $image ):
            $out .= '<img src="' .$image[0]. '">';
        endif;
        $out .= '</picture>';
        $out .= '</div>';

        $out .= '<picture class="aside-image">';
            $out .= '<img src="' .$aside_img[0]. '">';
        $out .= '</picture>';

    endif;

    return $out;
}