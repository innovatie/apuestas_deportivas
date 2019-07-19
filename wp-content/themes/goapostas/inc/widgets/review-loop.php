<?php
/***************************************
*	Review Loop Function
***************************************/

vc_map( array(
	"name" => __("Reviews List"),
	"base" => "reviews-list",
	"category" => __('Content'),
	"params" => array(
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Nro of Reviews"),
			"param_name" => "nro_reviews",
			"value" => __(""),
			"description" => __("Number of Reviews to show")
		),
		array(
			"type" => "textfield",
			"heading" => __("Pagination Link Text"),
			"param_name" => "pagination_link",
			"description" => __("Bottom Pagination Link Text")
		)
   	)
));

add_shortcode('reviews-list', 'goapostas_reviews_list');
function goapostas_reviews_list($atts,$content){
	extract(shortcode_atts(array(
      'nro_reviews' => '',
      'pagination_link' => '',
   	), $atts));
	$out = '';
	$show_p = $nro_reviews;

	$args = array(
 		'post_type' => 'review',
 		'posts_per_page' => -1
 	);
 	//$out .= $show_p;
	$wp_query = new WP_Query( $args );
	if($wp_query->have_posts()):
		$out .= '<div class="dark-reviews-container">';
			$out .= '<div class="wrap">';
			$counter = 0;
	 		while ( $wp_query->have_posts() ) : $wp_query->the_post();
	 			$counter++;
	 			//$thumb_id = get_post_thumbnail_id();
				//$thumb_url = wp_get_attachment_image_src($thumb_id,'full', true);
				if ($counter == 1) {
					$out .= '<div class="top-three">';
				}
				if ($counter <= 3) {
					$out .= '<div class="review-block-dark">';

						$out .= '<div class="image-container">';
							$out .= '<a href="'.get_the_permalink().'"><img src="'.get_field('review_logo').'" /></a>';
						$out .= '</div>';
						$rating = get_field('rating_stars')? floatval(get_field('rating_stars')) : 0;
						if( $rating ):
							$rating = $rating % 100;
						endif;
						$out .= '<div class="rev-tot">';
							$out .= '<div class="reviews-stars">';
								if ($rating == 100) {
									$out .= '<i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i>';
								}elseif ($rating < 100 && $rating > 70) {
									$out .= '<i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-outline"></i>';
								}elseif ($rating < 70 && $rating > 60) {
									$out .= '<i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i>';
								}elseif ($rating < 60 && $rating > 50) {
									$out .= '<i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i>';
								}elseif ($rating < 50 && $rating > 30) {
									$out .= '<i class="icon-star-filled"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i>';
								}elseif ($rating < 30) {
									$out .= '<i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i>';
								}
							$out .= '</div>';
						$out .= '</div>';
						$out .= '<div class="number-review">#'.$counter.'</div>';
						$out .= '<div class="evaluation-sec">';
							$bonus = get_field('bonus') ? get_field('bonus') : '-';
							$out .= '<label>'.__('Bônus', 'goapostas').' '.$bonus.'</label> <strong>'.get_comments_number(get_the_ID()).'</strong>';
						$out .= '</div>';
						$out .= '<div><a href="'.get_the_permalink().'" class="btn-solid secondary" >'.__('Análise Completa', 'goapostas').'</a></div>';
						$out .= '<div><a href="'.get_field('external_link').'" class="btn-solid" target="_blank">'.__('Acessar', 'goapostas').'</a></div>';

					$out .= '</div>';
				}
				if ($counter == 3) {
					$out .= '</div>';
				}elseif($counter == 2 && $counter == $wp_query->post_count) {
					$out .= '</div>';
				}
				if ($counter > 3) {
					$rating = get_field('rating_stars')? floatval(get_field('rating_stars')) : 0;
					if( $rating ):
						$rating = $rating % 100;
					endif;
					$out .= '<div class="single-items">';
						$out .= '<div class="cont-items">';
							$out .= '<div class="number">'.$counter.'</div>';
							$out .= '<div class="icon-bet"><a href="'.get_the_permalink().'"><img src="'.get_field('review_logo').'" /></a></div>';
							$out .= '<div class="dsc-cnt-s">';
								$out .= '<div class="stars">';
								if ($rating == 100) {
									$out .= '<i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i>';
								}elseif ($rating < 100 && $rating > 70) {
									$out .= '<i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-outline"></i>';
								}elseif ($rating < 70 && $rating > 60) {
									$out .= '<i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i>';
								}elseif ($rating < 60 && $rating > 50) {
									$out .= '<i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i>';
								}elseif ($rating < 50 && $rating > 30) {
									$out .= '<i class="icon-star-filled"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i>';
								}elseif ($rating < 30) {
									$out .= '<i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i>';
								}
								$out .= '</div>';
								$bonus = get_field('bonus') ? get_field('bonus') : '-';
								$out .= '<div class="bonus"><label>'.__('Bônus', 'goapostas').' '.$bonus.'</label></div>';
								$out .= '<div class="quote"><i class="icon-quote"></i> '.get_comments_number(get_the_ID()).'</div>';
							$out .= '</div>';

							$out .= '<div class="item-cta">';
							$out .= '<div class="btn-first"><a href="'.get_the_permalink().'" class="btn-solid secondary" >'.__('Análise Completa', 'goapostas').'</a></div>';
							$out .= '<div class="btn-second"><a href="'.get_field('external_link').'" class="btn-solid" target="_blank">'.__('Acessar', 'goapostas').'</a></div>';
							$out .= '</div>';
						$out .= '</div>';
					$out .= '</div>';
				}
	 		endwhile;
 			$out .= '</div>';
 		$out .= '</div>';
 		wp_reset_query();

 		/* pagination */
 		$total_loop = $wp_query->post_count;
 		$total_per_p = intval($show_p) - 3;
 		if ($total_loop > $show_p) {
 			$out .= '<div class="navigation-reviews">';
	 			$out .= '<a href="#">'.$pagination_link.'</a>';
	 		$out .= '</div>';
 		}
 		$out .= '<script>
 		jQuery(document).ready(function(){
 			var counter = 0;
 			jQuery(".dark-reviews-container .single-items").each(function(){
 				counter++;
 				if(counter > '.$total_per_p.'){
 					jQuery(this).addClass("hide-single");
 				}
 			});
 			jQuery(".navigation-reviews a").on("click", function(e){
 				e.preventDefault();
 				jQuery(".navigation-reviews").hide();
 				jQuery(".dark-reviews-container .single-items.hide-single").each(function(){
 					jQuery(this).removeClass("hide-single");
 				});
 			});
 		});
 		</script>';

 	endif;

	return $out;
}