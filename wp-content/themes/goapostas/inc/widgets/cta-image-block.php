<?php 
// feactured block
vc_map( array(
    "name" => __("CTA Image Block"),
    "base" => "cta-block",
    "class"=> 'wpb_vc_single_image',
    "category" => __('Content'),
    "params" => array(
        array(
            "type" => "attach_image",
            "holder" => "img",
            "class" => "attachment-thumbnail",
            "heading" => __("Image"),
            "param_name" => "cta_image",
            "value" => '',
            "description" => ''
        ),
        array(
            'type' => 'vc_link',
            'holder' => 'div',
            'class' => '',
            'heading' => __( 'CTA Link' ),
            'param_name' => 'cta_link',
            'value' => '',
            'description' =>'',
        ),
        array(
            "type" => "textfield",
            "holder" => "div",
            "class" => "",
            "heading" => __("CTA Time"),
            "param_name" => "cta_time",
            "value" => '',
            "description" => 'text format: 01:42'
        ),
        array(
            "type" => "dropdown",
            "holder" => "div",
            "class" => "attachment-thumbnail",
            "heading" => __("CTA location"),
            "param_name" => "cta_location",
            "value" => array('Center'=>'center', 'Bottom left'=>'btm-l'),
            "description" => ''
        ),
    )
) );

add_shortcode('cta-block', 'goapostas_cta_block');
function goapostas_cta_block($atts) {
    $atts = shortcode_atts( array(
        'cta_image' => '',
        'cta_link' => '',
        'cta_time' => '',
        'cta_location'  => 'centered'
    ), $atts );

    $link = vc_build_link( $atts['cta_link'] );
    $image = $atts['cta_image']? wp_get_attachment_image_src( $atts['cta_image'], 'medium_large' ) : '';

    $out = '';

    if( $atts['cta_image'] || $atts['cta_link'] ):

        $out .= '<div class="cta-image-block cta-'. $atts['cta_location'] .'">';

        $out .= goapostas_cta_event_link( $atts );

        $out .= '<picture>';
        if( $image ):
            $out .= '<img src="' .$image[0]. '">';
        endif;
        $out .= '</picture>';

        $out .= '</div>';

    endif;

    return $out;
}