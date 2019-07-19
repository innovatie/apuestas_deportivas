<?php
/***************************************
*	News Bars Function
***************************************/
add_shortcode('news-bar-list', 'goapostas_new_bar_list');
function goapostas_new_bar_list($atts,$content){
	extract(shortcode_atts(array(
      'title' => '',
      'color' => '',
   	), $atts));
   	$out = '';
   	$out .= '<div class="palpites-list news-barlist">';
	   	$out .= '<div class="title-news" style="background:'.$color.';">'.$title.'</div>';
		$services = new WP_Query(
	        array(
	            'post_type' => 'news',
	            'showposts' => 6
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
			if($counter<4){
				$out .= '<div class="four-content">';
					$out .= '<div class="content">';
						$out .= '<div class="new-thumb" style="background:url('.$thumb.');"></div>';
						$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.get_the_title().'</a></h4></div>';
						$out .= '<hr>';
						$out .= '<div class="author">BY '.get_the_author().'</div>';
					$out .= '</div>';
				$out .= '</div>';
			}else{
				$out .= '<div class="third-content">';
					$out .= '<div class="content">';
						$out .= '<div class="thumb" style="background:url('.$thumb.');"></div>';
						$out .= '<div class="detail">';
							$out .= '<h6><a href="'.get_the_permalink().'">'.get_the_title().'</a></h6>';
							$out .= '<hr>';
							$out .= '<div class="author">BY '.get_the_author().'</div>';
						$out .= '</div>';
					$out .= '</div>';
				$out .= '</div>';
			}
		endwhile;
		$out .= '</div>';

	$out .= '</div>';

	return $out;
}

add_action('init', 'lifestyle_function');
function lifestyle_function(){
	vc_map( array(
		"name" => __("News Lifestyle"),
		"base" => "news-bar-list",
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
			)
	   	)
	));

}

