<?php
/***************************************
*	Blog Loop Function
***************************************/

vc_map( array(
	"name" => __("Blogs List"),
	"base" => "blogs-list",
	"category" => __('Content'),
	"params" => array(
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Nro of Posts"),
			"param_name" => "nro_posts",
			"value" => __(""),
			"description" => __("Number of Posts to show")
		)
   	)
));

add_shortcode('blogs-list', 'goapostas_blogs_list');
function goapostas_blogs_list($atts,$content){
	extract(shortcode_atts(array(
      'nro_posts' => '',
      'pagination_link' => '',
   	), $atts));
	$out = '';
	$show_p = $nro_posts;

	$args = array(
 		'post_type' => 'palpite',
 		'posts_per_page' => intval($show_p)
 	);
 	//$out .= $show_p;
	$wp_query = new WP_Query( $args );
	if($wp_query->have_posts()):
		$out .= '<div class="single-carousel">';
 		while ( $wp_query->have_posts() ) : $wp_query->the_post();
 			$out .= '<div class="item-post">';
 				$out .= '<h6><a href="'.get_the_permalink().'">'.get_the_title().'</a></h6>';
 				$out .= '<p>'.substr(get_the_excerpt(), 0, 40).'</p>';
 			$out .= '</div>';
 		endwhile;
 		$out .= '</div>';
 		$out .= '<script>
 		jQuery(document).ready(function(){
 			jQuery(".single-carousel").slick({
 			  dots: false,
			  slidesToShow: 3,
			  slidesToScroll: 1,
			  infinite: true,
			  responsive: [
			    {
			      breakpoint: 960,
			      settings: {
			      	dots: true,
			      	slidesToShow: 3,
			      	slidesToScroll: 1,
			        arrows: true
			      }
			    },
			    {
			      breakpoint: 600,
			      settings: {
			      	dots: true,
			      	infinite: true,
			      	slidesToShow: 1,
			      	slidesToScroll: 1,
					draggable: false
			      }
			    }
			  ]
			});
 		});
 		</script>';
 		wp_reset_query();
 	endif;

	return $out;
}