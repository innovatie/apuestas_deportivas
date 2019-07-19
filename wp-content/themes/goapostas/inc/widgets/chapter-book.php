<?php
/* Chapters Book Widget */
vc_map( array(
    "name" => __("Chapter Book", 'goapostas'),
    "base" => "chapter-book",
    "category" => 'Content',
    "params" => array(
        array(
            'group' => 'Chapter Image',
            "type" => "attach_image",
            "holder" => "img",
            "heading" => __("Image", 'goapostas'),
            "param_name" => "chapter_image"
        ),
        array(
            'group' => 'Chapter Number',
            "type" => "textfield",
            "holder" => "div",
            "class" => "",
            "heading" => __("Top Chapter #", 'goapostas'),
            "param_name" => "chapter_number"
        ),
        array(
            'group' => 'Chapter Title',
            "type" => "textfield",
            "holder" => "div",
            "class" => "",
            "heading" => __("Title", 'goapostas'),
            "param_name" => "chapter_title"
        ),
        array(
            'group' => 'Chapter Link',
            'type' => 'vc_link',
            'holder' => 'div',
            'class' => '',
            'heading' => __( 'Link' ),
            'param_name' => 'chapter_link'
        )
    )
) );

add_shortcode('chapter-book', 'goapostas_chapter_book');
function goapostas_chapter_book($atts) {
    $atts = shortcode_atts( array(
        'chapter_image' => '',
        'chapter_number' => '',
        'chapter_title' => '',
        'chapter_link' => '',
    ), $atts );

    $out = '';
    $out .= '<a href="'.vc_build_link($atts['chapter_link'])['url'].'" class="chapter-book" style="background:url('.wp_get_attachment_image_src($atts['chapter_image'], 'full')[0].');">';
        $out .= '<div class="chapter-content">';
            $out .= '<h5>'.$atts['chapter_number'].'</h5>';
            $out .= '<h3>'.$atts['chapter_title'].'</h3>';
        $out .= '</div>';
    $out .= '</a>';

    return $out;
}