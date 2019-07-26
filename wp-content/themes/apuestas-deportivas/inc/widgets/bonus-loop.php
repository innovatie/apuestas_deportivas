<?php
/***************************************
*	Bonus V2 Loop Function
***************************************/

vc_map( array(
	"name" => __("Bonus List V2"),
	"base" => "bonus-list-v2",
	"category" => __('Content'),
	"params" => array(
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Nro of Bonus to show v2"),
			"param_name" => "nro_posts",
			"value" => __(""),
			"description" => __("Number of Bonus to show v2")
		)
   	)
));

add_shortcode('bonus-list-v2', 'goapostas_bonus_list_v2');
function goapostas_bonus_list_v2($atts,$content){
	extract(shortcode_atts(array(
      'nro_posts' => '',
      'pagination_link' => '',
   	), $atts));
	$out = '';
	$show_p = $nro_posts;

	$args = array(
 		'post_type' => 'bonus',
 		'posts_per_page' => intval($show_p)
 	);
 	//$out .= $show_p;
	$wp_query = new WP_Query( $args );
	if($wp_query->have_posts()):
		$out .= '<div class="bonus-list bl-v2">';
		$counter = 0;
 		while ( $wp_query->have_posts() ) : $wp_query->the_post();
 			$counter++;
 			$class_cat='';
 			if ($counter < 3) {
 				$thumb_id = get_post_thumbnail_id();
				$thumb_url = wp_get_attachment_image_src($thumb_id,'full', true);
				if (has_post_thumbnail()) {
					$thumb = $thumb_url[0];
				}else{
					$thumb = get_stylesheet_directory_uri().'/assets/images/goapostas-default.jpg';
				}
				$bonus_cats = get_the_terms( get_the_ID(), 'bonus_category' );
				$total_bcats = '';
				foreach ($bonus_cats as $cat) {
					// print_r($cat->slug);
					$class_cat = mb_strtolower(str_replace(' ', '-', $cat->slug));
					$cat_color = get_field('color_category', 'bonus_category_'.$cat->term_id) ? get_field('color_category', 'bonus_category_'.$cat->term_id) : '';
					$total_bcats .= '<span style="background:'.$cat_color.';" class="cat-'.$class_cat.'">'.$cat->name.'</span>';
				}
 				$out .= '<div class="item-gray-cont ">';
 					$out .= '<div class="details-bonus">';
 						$out .= '<div class="tags-l"><img src="'.get_field('bonus_logo').'" >'.$total_bcats.'</div>';
 						$out .= '<h5>'.get_the_title().'</h5>';
 						$out .= '<p>'.get_the_excerpt().'</p>';
 						$out .= '<div class="link-b"><a href="'.get_field('bonus_external_link').'" target="">'.__('Acessar o bônus', 'goapostas').'</a></div>';
 					$out .= '</div>';
	 			$out .= '</div>';
 			}elseif($counter > 2 && $counter < 7){
 				$bonus_cats = get_the_terms( get_the_ID(), 'bonus_category' );
 				$total_bcats = '';
				foreach ($bonus_cats as $cat) { 
					$class_cat = mb_strtolower(str_replace(' ', '-', $cat->slug));
					$cat_color = get_field('color_category', 'bonus_category_'.$cat->term_id) ? get_field('color_category', 'bonus_category_'.$cat->term_id) : '';
					$total_bcats .= '<span style="background:'.$cat_color.';" class="cat-'.$class_cat.'">'.$cat->name.'</span>';
				}
 				$out .= '<div class="item-regular ">';
 					$out .= '<div class="bonus-details">';
 						$out .= '<div class="tags-l"><img src="'.get_field('bonus_logo').'" >'.$total_bcats.'</div>';
 						$out .= '<h5>'.get_the_title().'</h5>';
 						$out .= '<p>'.get_the_excerpt().'</p>';
 					$out .= '</div>';
 					$out .= '<div class="bonus-link">';
 						$out .= '<a href="'.get_field('bonus_external_link').'" target="">'.__('Acessar o bônus', 'goapostas').'</a>';
 					$out .= '</div>';
 				$out .= '</div>';
 			}else{
 				$bonus_cats = get_the_terms( get_the_ID(), 'bonus_category' );
 				$total_bcats = '';
				foreach ($bonus_cats as $cat) { 
					$class_cat = mb_strtolower(str_replace(' ', '-', $cat->slug));
					$cat_color = get_field('color_category', 'bonus_category_'.$cat->term_id) ? get_field('color_category', 'bonus_category_'.$cat->term_id) : '';
					$total_bcats .= '<span style="background:'.$cat_color.';" class="cat-'.$class_cat.'">'.$cat->name.'</span>';
				}
 				$out .= '<div class="item-regular free-bet ">';
 					$out .= '<div class="bonus-details">';
 						$out .= '<div class="tags-l"><img src="'.get_field('bonus_logo').'" >'.$total_bcats.'</div>';
 						$out .= '<h5>'.get_the_title().'</h5>';
 						$out .= '<p>'.get_the_excerpt().'</p>';
 					$out .= '</div>';
 					$out .= '<div class="bonus-link">';
 						$out .= '<a href="'.get_field('bonus_external_link').'" target="">'.__('Acessar o bônus', 'goapostas').'</a>';
 					$out .= '</div>';
 				$out .= '</div>';
 			}
 		endwhile;
 		$out .= '</div>';
 		wp_reset_query();
 	endif;

	return $out;
}