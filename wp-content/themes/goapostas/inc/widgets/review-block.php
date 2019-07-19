<?php
/***************************************
*	Review Block Function
***************************************/

vc_map( array(
	"name" => __("Review Block"),
	"base" => "review-block",
	"category" => __('Content'),
	"params" => array(
		array(
			"type" => "attach_image",
			"holder" => "div",
			"class" => "image-container",
			"heading" => __("Image"),
			"param_name" => "image_top",
			"value" => __(""),
			"description" => __("Top Image Block Review.")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Title"),
			"param_name" => "title_review",
			"value" => __(""),
			"description" => __("Title Review")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Stars"),
			"param_name" => "stars_review",
			"value" => __(""),
			"description" => __("Stars Review")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Evaluation"),
			"param_name" => "evaluation_review",
			"value" => __(""),
			"description" => __("Evaluation Review")
		),
		array(
			"type" => "vc_link",
			"heading" => __("Link"),
			"param_name" => "block_link",
			"dependency" => array(
				"element" => "link",
				"value" => "",
			),
			"description" => __("Block Link")
		)
   	)
));

add_shortcode('review-block', 'goapostas_review_block');
function goapostas_review_block($atts,$content){
	extract(shortcode_atts(array(
      'image_top' => '',
      'title_review' => '',
      'stars_review' => '',
      'evaluation_review' => '',
      'block_link' => '',
   ), $atts));
	$out = '';
	$out .= '<div class="review-block">';
		$out .= '<div class="image-container">';
			$out .= '<img src="'.wp_get_attachment_image_src($image_top,'full')[0].'" />';
		$out .= '</div>';
		$out .= '<h5>'.$title_review.'</h5>';
		$reviews = $stars_review;
		$reviews = $reviews? $reviews : 0;
		$out .= '<div class="star-rating">';
   			$out .= '<span style="width:'. ($reviews / 6.0 * 100) .'%">';
				$out .= '<i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i>';
			$out .= '</span>';
			$out .= '<i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i>';
		$out .= '</div>';
		$out .= '<div class="evaluation-sec">';
			$out .= '<label>'.__('Avaliação', 'goapostas').'</label> <strong>'.$evaluation_review.'</strong>';
		$out .= '</div>';
		$out .= '<div><a href="'.vc_build_link($block_link)['url'].'" class="btn-solid" target="'.vc_build_link($block_link)['target'].'">'.vc_build_link($block_link)['title'].'</a></div>';
	$out .= '</div>';
	return $out;
}