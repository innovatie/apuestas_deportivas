<?php
/***************************************
*	Navigation Apostas Function
***************************************/
vc_map( array(
	"name" => __("Navigation Casa de Apostas"),
	"base" => "casa-apostas-nav",
	"category" => __('Content'),
	"params" => array(
		array(
			"type" => "vc_link",
			"heading" => __("Ranking completo Link"),
			"param_name" => "ranking_link",
			"dependency" => array(
				"element" => "link",
				"value" => "",
			),
		),
	)
));
add_shortcode('casa-apostas-nav', 'goapostas_nav_apostas');
function goapostas_nav_apostas($atts,$content){
	extract(shortcode_atts(array(
      'ranking_link' => '',
   ), $atts));
   	$out = '';
   	$out .= '<div class="navigation-apostas">';
   		$out .= '<div class="arrows">';
   			$out .= '<div id="prev-tcapostas">&#8249;</div>';
   			$out .= '<div id="next-tcapostas">&#8250;</div>';
   		$out .= '</div>';
   		$out .= '<div class="link-ranking">';
   			$out .= '<a href="'.vc_build_link($ranking_link)['url'].'" target="'.vc_build_link($ranking_link)['target'].'">'.vc_build_link($ranking_link)['title'].'</a>';
   		$out .= '</div>';
	$out .= '</div>';

	return $out;
}
