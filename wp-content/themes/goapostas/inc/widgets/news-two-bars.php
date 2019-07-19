<?php
/***************************************
*	News Two Bars Function
***************************************/
add_shortcode('news-two-bar-list', 'goapostas_new_two_bar_list');
function goapostas_new_two_bar_list($atts,$content){
	extract(shortcode_atts(array(
      'color' => '',
   	), $atts));
   	$out = '';
   	$out .= '<div class="palpites-list news-two">';
	   	$out .= '<div class="title-news" style="background:'.$color.';">&nbsp;</div>';
		$services = new WP_Query(
	        array(
	            'post_type' => 'news',
	            'showposts' => 2
	        )
	    );
	    $counter = 0;
	    $out .= '<div class="loop">';
	    while ($services->have_posts()) : $services->the_post();
	    	$counter++;
	    	$thumb_id = get_post_thumbnail_id();
			$thumb_url = wp_get_attachment_image_src($thumb_id,'medium', true);
			if(has_post_thumbnail()){
				$thumb = $thumb_url[0];
			}else{
				$thumb = get_stylesheet_directory_uri().'/assets/images/goapostas-default.jpg';
			}
			$out .= '<div class="four-content">';
				$out .= '<div class="content">';
					$out .= '<div class="new-thumb" style="background:url('.$thumb.');"></div>';
					$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.get_the_title().'</a></h4></div>';
					$out .= '<hr>';
					$out .= '<div class="author">BY '.get_the_author().'</div>';
				$out .= '</div>';
			$out .= '</div>';
		endwhile;
		$out .= '</div>';

	$out .= '</div>';

	return $out;
}

add_action('init', 'lifestyle_two_function');
function lifestyle_two_function(){
	vc_map( array(
		"name" => __("News Two Bar"),
		"base" => "news-two-bar-list",
		"category" => __('Content'),
		"params" => array(
			array(
				"type" => "colorpicker",
				"heading" => __("Color"),
				"param_name" => "color",
				"value" => __(""),
				"description" => __("Header Color")
			)
	   	)
	));

}

