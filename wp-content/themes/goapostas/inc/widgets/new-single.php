<?php
/***************************************
*	New Single Function
***************************************/
add_shortcode('new-single', 'goapostas_new_single');
function goapostas_new_single($atts,$content){
	extract(shortcode_atts(array(
      'news' => '',
   	), $atts));
   	$out = '';
   	$out .= '<div class="palpite-single">';
   		$out .= '<div class="palpite-detail">';
   			if (has_post_thumbnail($news)) {
   				$thumb_id = get_post_thumbnail_id($news);
				$thumb_url = wp_get_attachment_image_src($thumb_id,'medium', true);
				$thumbnail = $thumb_url[0];
   			}else{
   				$thumbnail = get_stylesheet_directory_uri().'/assets/images/goapostas-default.jpg';
   			}
   			$out .= '<h4><a href="'.get_the_permalink($news).'">'.get_the_title($news).'</a></h4>';
   			$out .= '<p>'.get_the_excerpt($news).'</p><hr>';
   			$out .= '<div class="author">BY '.get_the_author($news).'</div>';
   		$out .= '</div>';
   		$out .= '<a class="palpite-thumbnail" href="'.get_the_permalink($news).'" style="background:url('.$thumbnail.');"></a>';
		
	$out .= '</div>';

	return $out;
}


add_action('init', 'new_s');
function new_s(){
	$show_p = isset( $show_p ) ? $show_p : 0;
	$num_to_show = $show_p;
	$args = array(
 		'post_type' => 'news',
 		'posts_per_page' => intval($num_to_show)
	);
	
	$list_cats = [];
	$wp_query = new WP_Query( $args );
	if($wp_query->have_posts()):
 		while ( $wp_query->have_posts() ) : $wp_query->the_post();
 			$list_cats[get_the_title()] = get_the_ID();
 		endwhile;
 	endif;

	vc_map( array(
		"name" => __("New Single"),
		"base" => "new-single",
		"category" => __('Content'),
		"params" => array(
			array(
				"type" => "dropdown",
				"heading" => __("News"),
				"param_name" => "news",
				'value' => $list_cats,
				"description" => __("List of News available")
			)
	   	)
	));

}
