<?php
/***************************************
*	News Loop Function
***************************************/

vc_map( array(
	"name" => __("News Loop"),
	"base" => "news-loop",
	"category" => __('Content'),
	"params" => array(
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Quantity"),
			"param_name" => "quantity",
			"value" => __(""),
			"description" => __("Number of News to show")
		)
   	)
));

//$list_cats = array();
add_shortcode('news-loop', 'goapostas_news_loop');
function goapostas_news_loop($atts,$content){
	extract(shortcode_atts(array(
      'quantity' => '',
   	), $atts));
   	$out = '';
   	$out .= '<div class="palpites-list">';
		$services = new WP_Query(
	        array(
	            'post_type' => 'news',
	            'showposts' => $quantity
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
			$out .= '<div class="third-content">';
				$out .= '<div class="content">';
					$out .= '<a class="thumb" href="'.get_the_permalink().'" style="background:url('.$thumb.');"></a>';
					$out .= '<div class="detail">';
						$tit_l = strlen(get_the_title());
						if ($tit_l > 50) {
							$out .= '<h6><a href="'.get_the_permalink().'">'.substr(get_the_title(),0,50).'...</a></h6>';
						}else{
							$out .= '<h6><a href="'.get_the_permalink().'">'.get_the_title().'</a></h6>';
						}
						$out .= '<hr>';
						$out .= '<div class="author">BY '.get_the_author().'</div>';
					$out .= '</div>';
				$out .= '</div>';
			$out .= '</div>';
		endwhile;
		wp_reset_postdata();
		$out .= '</div>';

	$out .= '</div>';

	return $out;
}

