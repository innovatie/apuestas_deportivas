<?php 
// feactured block
vc_map( array(
    "name" => __("CTA Event link"),
    "base" => "cta-event-link",
    "category" => __('Content'),
    "params" => array(
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
            "heading" => __("Time"),
            "param_name" => "cta_time",
            "value" => '',
            "description" => 'text format: 01:42'
        )
    )
) );

add_shortcode('cta-event-link', 'goapostas_cta_event_link');
function goapostas_cta_event_link($atts) {
    $atts = shortcode_atts( array(
        'cta_link' => '',
        'cta_time' => '',
    ), $atts );

    $out = '';

    if( $atts['cta_link'] ):

        $link = vc_build_link( $atts['cta_link'] );

        if( $link ):
            $out .= '<a class="cta-event-link" href="' .$link['url']. '" target="' .$link['target']. '" rel="' .$link['rel']. '">';
            $out .= '<span class="icon"><i class="fa fa-play"></i></span>';
            $out .= $link['title'];
            
            if( $atts['cta_time'] ):
                $out .= '<span class="sep">|</span>';
                $out .= $atts['cta_time'];
            endif;

            $out .= '</a>';
        endif;

    endif;

    return $out;
}