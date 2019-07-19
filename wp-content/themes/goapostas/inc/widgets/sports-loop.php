<?php
/***************************************
*	Sports Loop Function
***************************************/

vc_map( array(
	"name" => __("Sports News Loop"),
	"base" => "sports-loop",
	"category" => __('Content'),
	"params" => array(
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Title"),
			"param_name" => "title",
			"value" => __(""),
			"description" => __("Filter Title")
		)
   	)
));

add_shortcode('sports-loop', 'goapostas_sports_loop');
function goapostas_sports_loop($atts,$content){
	extract(shortcode_atts(array(
      'title' => '',
   	), $atts));
   	$out = '';
   	$out .= '<div class="sports-loop">';
   		$out .= '<div class="wrap">';
   			$out .= '<div class="attention-area">';
   				$sports = get_terms('sport');
			   	foreach ( $sports as $sport ):
			   		$out .= '<div class="sport-item">';
			   			$out .= '<div class="sport-bg" style="background:url('.get_field('thumbnail_sport', 'sport_'.$sport->term_id).');"></div>';
			   			$term_link = get_term_link( $sport );
			   			$out .= '<a class="item-content" href="'.esc_url( $term_link ).'">';
			   				$out .= '<img src="'.get_field('icon', 'sport_'.$sport->term_id).'" />';
			   				$out .= '<h6>'.$sport->name.'</h6>';
			   			$out .= '</a>';
			   		$out .= '</div>';
				endforeach;
   			$out .= '</div>';
   		$out .= '</div>';
   	$out .= '</div>';

	return $out;
}

