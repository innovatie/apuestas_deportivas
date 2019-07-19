<?php
/***************************************
*	Palpite Single Function
***************************************/
add_shortcode('palpite-single', 'goapostas_palpite_single');
function goapostas_palpite_single($atts,$content){
	extract(shortcode_atts(array(
      'title' => '',
      'color' => '',
      'palpites' => '',
   	), $atts));
   	$out = '';
	$out .= '<div class="palpite-single">';
		$out .= '<div class="title-top vc_hidden-lg vc_hidden-md vc_hidden-sm" style="background:'.$color.';">'.$title.'</div>';   
   		$out .= '<div class="palpite-detail">';
   			if (has_post_thumbnail($palpites)) {
   				$thumb_id = get_post_thumbnail_id($palpites);
				$thumb_url = wp_get_attachment_image_src($thumb_id,'medium', true);
				$thumbnail = $thumb_url[0];
   			}else{
   				$thumbnail = get_stylesheet_directory_uri().'/assets/images/goapostas-default.jpg';
   			}
   			$out .= '<div class="title-top vc_hidden-xs" style="background:'.$color.';">'.$title.'</div>';
			$out .= '<h4><a href="'.get_the_permalink($palpites).'">'.get_the_title($palpites).'</a></h4>';
			if( $excerpt = get_the_excerpt($palpites) ):
			$out .= '<p>'.$excerpt.'</p>';
			endif;
			$out .= '<hr>';   
   			$out .= '<div class="author">BY '.get_the_author($palpites).'</div>';
   		$out .= '</div>';
   		$out .= '<a class="palpite-thumbnail" href="'.get_the_permalink($palpites).'" style="background:url('.$thumbnail.');"></a>';
	$out .= '</div>';

	return $out;
}

add_action('init', 'palpite_s');
function palpite_s(){
	$args = array(
 		'post_type' => 'palpite',
 		'posts_per_page' => -1
 	);
	$wp_query = new WP_Query( $args );
	if($wp_query->have_posts()):
 		while ( $wp_query->have_posts() ) : $wp_query->the_post();
 			$list_cats[get_the_title()] = get_the_ID();
 		endwhile;
 	endif;
	vc_map( array(
		"name" => __("Palpite Single"),
		"base" => "palpite-single",
		"category" => __('Content'),
		"params" => array(
			array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "",
				"heading" => __("Title"),
				"param_name" => "title",
				"value" => __(""),
				"description" => __("Head Title")
			),
			array(
				"type" => "colorpicker",
				"heading" => __("Color"),
				"param_name" => "color",
				"value" => __(""),
				"description" => __("Header Color")
			),
			array(
				"type" => "dropdown",
				"heading" => __("Palpites"),
				"param_name" => "palpites",
				'value' => $list_cats,
				"description" => __("Categories")
			)
	   	)
	));
}
