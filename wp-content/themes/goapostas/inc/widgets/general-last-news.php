<?php
/***************************************
*	General News Function
***************************************/
add_shortcode('news-general-list', 'goapostas_new_general_list');
function goapostas_new_general_list($atts,$content){
	extract(shortcode_atts(array(
      'cats' => '',
   	), $atts));
   	$out = '';
   	$out .= '<div class="palpites-list pl-t-big new-plist">';
	   	$full = explode(",", $cats);
	   	$list_cats = array();
	   	$names = '';
	   	foreach ($full as $cat) {
	   		$list_cats[] = $cat;
	   		$term = '';
	   		$term = get_term( $cat );
	   		$names .= '<span>'.$term->name.'</span> <span>/</span> ';
	   	}
	   	$tax_args = array(
		    'hide_empty' => false
		);
		if ($cats) {
			$services = new WP_Query(
		        array(
		            'post_type' => 'news',
		            'showposts' => 4,
		            'tax_query' => array(
		                array(
		                    'taxonomy'  => 'sport',
		                    'terms'     => $list_cats,
		                    'field'     => 'term_id'
		                )
		            )
		        )
		    );
		}else{
			$services = new WP_Query(
		        array(
		            'post_type' => 'news',
		            'showposts' => 4
		        )
		    );
		}
	    $counter = 0;
	    $out .= '<div class="loop">';
	    while ($services->have_posts()) : $services->the_post();
	    	$counter++;
	    	$thumb_id = get_post_thumbnail_id();
			$thumb_url = wp_get_attachment_image_src($thumb_id,'medium', true);
			$opt_img = get_field('optional_image') ? get_field('optional_image') : '';
			if($opt_img) {
				$thumb = $opt_img;
			}else{
				if(has_post_thumbnail()){
					$thumb = $thumb_url[0];
				}else{
					$thumb = get_stylesheet_directory_uri().'/assets/images/goapostas-default.jpg';
				}
			}
			$out .= '<div class="four-content">';
				$out .= '<div class="content">';
					$out .= '<a class="new-thumb" href="'.get_the_permalink().'" style="background:url('.$thumb.');"></a>';
					$tit_l = strlen(get_the_title());
					if ($tit_l > 37) {
						$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.substr(get_the_title(),0,37).'...</a></h4></div>';
					}else{
						$out .= '<div class="title"><h4><a href="'.get_the_permalink().'">'.get_the_title().'</a></h4></div>';
					}
					$out .= '<hr>';
					$out .= '<div class="author">BY '.get_the_author().'</div>';
					$out .= '<div style="line-height:0;"><small>'.human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' '.__('atrás', 'goapostas').'</small></div>';
				$out .= '</div>';
			$out .= '</div>';
		endwhile;
		wp_reset_postdata();
		$out .= '</div>';

	$out .= '</div>';

	return $out;
}

add_action('init', 'cat_news_general');
function cat_news_general(){
	$tax_args = array(
	    'hide_empty' => true
	);
	$categories = get_terms('sport', $tax_args);
	$list_cats = [];   
   	foreach ( $categories as $category ):
		$list_cats[$category->name] = $category->term_id;
	endforeach;

	vc_map( array(
		"name" => __("General Last News"),
		"base" => "news-general-list",
		"category" => __('Content'),
		"params" => array(
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

