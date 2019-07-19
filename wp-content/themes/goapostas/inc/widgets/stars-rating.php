<?php
vc_map( array(
	"name" => __("Stars Rating"),
	"base" => "stars-rating-block",
	"category" => __('Content'),
	"params" => array(
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Rating"),
			"param_name" => "stars_rating",
			"value" => __(""),
			"description" => __("number [0 to 100]")
		),
		array(
			"type" => "textfield",
			"holder" => "div",
			"class" => "",
			"heading" => __("Evaluation"),
			"param_name" => "stars_eval",
			"value" => __(""),
			"description" => __("(Optional) sample: 9.8")
		),
   	)
));

add_shortcode('stars-rating-block', 'goapostas_stars_rating_block');
function goapostas_stars_rating_block($atts) {
	$atts = shortcode_atts( array(
		'stars_rating' => '0',
		'stars_eval'  => ''
	), $atts );

	$rating = $atts['stars_rating']? floatval($atts['stars_rating']) : 0;
	$eval_value = $atts['stars_eval'];
	$out = '';

	if( $rating ):
		$rating = $rating % 100;
		ob_start();
	?>
	<div class="stars-rating-wrap">
		<div class="star-rating">
			<span style="width:<?php echo $rating; ?>%">
				<i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i>
			</span>
			<i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i>
		</div>

		<?php if( $eval_value ): ?>
		<div class="stars-evaluation">
			<span><?php echo __('Confiança','goapostas');?></span>
			<span><?php echo $eval_value?></span>
		</div>
		<?php endif; ?>

	</div>
	<?php
		$out = ob_get_clean();
	endif;

	return $out;
}
