<?php
/***************************************
*	Palpites News Function
***************************************/
add_shortcode('news-palpites-list', 'goapostas_new_palpites_list');
function goapostas_new_palpites_list($atts,$content){
	extract(shortcode_atts(array(
      'title' => '',
      'color' => '',
      'cats' => '',
   	), $atts));
   	$out = '';
   	$out .= '<div class="palpites-list pl-t-big">';
	   	$full = explode(",", $cats);
	   	$list_cats = array();
	   	$names = '';
	   	foreach ($full as $cat) {
	   		$list_cats[] = $cat;
	   		$term = get_term( $cat );
	   		$names .= '<span>'.$term->name.'</span> <span>/</span> ';
	   	}
	   	$out .= '<div class="title-news" style="background:'.$color.';">'.$title.' '.substr($names, 0, -16).'</div>';
	   	$tax_args = array(
		    'hide_empty' => false
		);
		$services = new WP_Query(
	        array(
	            'post_type' => 'palpite',
	            'showposts' => 7,
	            'tax_query' => array(
	                array(
	                    'taxonomy'  => 'palpite_sport',
	                    'terms'     => $list_cats,
	                    'field'     => 'term_id'
	                )
	            )
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
			if($counter<5){
				$out .= '<div class="four-content">';
					$out .= '<div class="content">';
						$out .= '<a class="new-thumb" href="'.get_the_permalink().'" style="background:url('.$thumb.');"></a>';
						$tit_l = strlen(get_the_title());
						if ($tit_l > 35) {
							$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.substr(get_the_title(),0,35).'...</a></h4></div>';
						}else{
							$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.get_the_title().'</a></h4></div>';
						}
						$out .= '<hr>';
						$out .= '<div class="author">BY '.get_the_author().'</div>';
					$out .= '</div>';
				$out .= '</div>';
			}else{
				$out .= '<div class="third-content">';
					$out .= '<div class="content">';
						$out .= '<a class="thumb" href="'.get_the_permalink().'" style="background:url('.$thumb.');"></a>';
						$out .= '<div class="detail">';
							$tit_l = strlen(get_the_title());
							if ($tit_l > 43) {
								$out .= '<h6><a href="'.get_the_permalink().'">'.substr(get_the_title(),0,43).'...</a></h6>';
							}else{
								$out .= '<h6><a href="'.get_the_permalink().'">'.get_the_title().'</a></h6>';
							}
							$out .= '<hr>';
							$out .= '<div class="author">BY '.get_the_author().'</div>';
						$out .= '</div>';
					$out .= '</div>';
				$out .= '</div>';
			}
		endwhile;
		wp_reset_postdata();
		$out .= '</div>';

	$out .= '</div>';

	return $out;
}

add_action('init', 'curs_fucntion');
function curs_fucntion(){
	$tax_args = array(
	    'hide_empty' => true
	);
	$categories = get_terms('palpite_sport', $tax_args);
	$list_cats = [];   
   	foreach ( $categories as $category ):
		$list_cats[$category->name] = $category->term_id;
	endforeach;

	vc_map( array(
		"name" => __("Palpites News"),
		"base" => "news-palpites-list",
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
				"type" => "checkbox",
				"heading" => __("Sports"),
				"param_name" => "cats",
				'value' => $list_cats,
				"description" => __("Categories")
			)
	   	)
	));

}

