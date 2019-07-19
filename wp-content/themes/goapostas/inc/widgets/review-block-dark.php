<?php
/***************************************
*	Review Block Function
***************************************/

vc_map( array(
	"name" => __("Review Block Dark"),
	"base" => "review-block-dark",
	"category" => __('Content'),
	"params" => array(
		array(
			"type" => "attach_image",
			"holder" => "div",
			"class" => "",
			"heading" => __("Image"),
			"param_name" => "image_dark_top",
			"value" => __(""),
			"description" => __("Top Image")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Stars"),
			"param_name" => "stars_review_dark",
			"value" => __(""),
			"description" => __("Stars Review")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Number Big"),
			"param_name" => "number_review",
			"value" => __(""),
			"description" => __("#")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Bônus 100£"),
			"param_name" => "evaluation_review_dark",
			"value" => __(""),
			"description" => __("Evaluation Review")
		),
		array(
			"type" => "vc_link",
			"heading" => __("Link 1"),
			"param_name" => "block_blue_link",
			"dependency" => array(
				"element" => "link",
				"value" => "",
			),
			"description" => __("Blue Link")
		),
		array(
			"type" => "vc_link",
			"heading" => __("Link 2"),
			"param_name" => "block_green_link",
			"dependency" => array(
				"element" => "link",
				"value" => "",
			),
			"description" => __("Green Link")
		)
   	)
));

add_shortcode('review-block-dark', 'goapostas_r_block_dark');
function goapostas_r_block_dark($atts,$content){
	extract(shortcode_atts(array(
      'image_dark_top' => '',
      'stars_review_dark' => '',
      'number_review' => '',
      'evaluation_review_dark' => '',
      'block_blue_link' => '',
      'block_green_link' => '',
   ), $atts));
	$out = '';
	$out .= '<div class="review-block-dark">';
		$out .= '<div class="image-container">';
			$out .= '<img src="'.wp_get_attachment_image_src($image_dark_top,'full')[0].'" />';
		$out .= '</div>';
		$rating = $stars_review_dark;
		$out .= '<div class="reviews-stars">';
			if ($rating == 100) {
				$out .= '<i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i>';
			}elseif ($rating < 100 && $rating > 70) {
				$out .= '<i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-outline"></i>';
			}elseif ($rating < 70 && $rating > 60) {
				$out .= '<i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i>';
			}elseif ($rating < 60 && $rating > 50) {
				$out .= '<i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i>';
			}elseif ($rating < 50 && $rating > 30) {
				$out .= '<i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i>';
			}elseif ($rating < 30 && $rating > 20) {
				$out .= '<i class="icon-star-filled"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i>';
			}elseif ($rating < 20) {
				$out .= '<i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i>';
			}
		$out .= '</div>';
		$out .= '<div class="number-review">'.$number_review.'</div>';
		$out .= '<div class="evaluation-sec">';
			$out .= '<label>Bônus 100£</label> <strong>'.$evaluation_review_dark.'</strong>';
		$out .= '</div>';
		$out .= '<div><a href="'.vc_build_link($block_blue_link)['url'].'" class="btn-solid secondary" target="'.vc_build_link($block_blue_link)['target'].'">'.vc_build_link($block_blue_link)['title'].'</a></div>';
		$out .= '<div><a href="'.vc_build_link($block_green_link)['url'].'" class="btn-solid" target="'.vc_build_link($block_green_link)['target'].'">'.vc_build_link($block_green_link)['title'].'</a></div>';
	$out .= '</div>';
	return $out;
}