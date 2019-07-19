<?php

vc_map( array(
	"name" => __("Review Analysis Block"),
	"base" => "review-analysis",
	"class"=> 'wpb_vc_single_image',
	"category" => __('Content'),
	"params" => array(
		array(
			"type" => "vc_link",
			"heading" => __("Link"),
			"param_name" => "rva_link",
			"dependency" => array(
				"element" => "link",
				"value" => "",
			),
			"description" => __("Block Link")
		)
   	)
));

add_shortcode('review-analysis', 'goapostas_review_analysys_block');
function goapostas_review_analysys_block( $atts ){
	$image = get_field('review_logo');
	$rating = get_field('rating_stars')? get_field('rating_stars') : 0;
	$evaluation = get_field('evaluation')? get_field('evaluation') : '';
	$years = get_field('years')? get_field('years') : '';
	$support = get_field('support');

	extract(shortcode_atts(array(
		'rva_link'  => ''
	), $atts));
	$out = '';

	if( $image || $evaluation || $years || $support || $atts['rva_link'] ):

	$link = vc_build_link( $atts['rva_link'] );
	ob_start();
	?>
	<div class="review-analysis">

		<div class="rva-l">
			<picture class="rva-image">
				<?php if( $image ): ?>
				<img src="<?php echo $image; ?>" alt="betting image">
				<?php endif; ?>
			</picture>
			<?php echo do_shortcode('[stars-rating-block stars_rating="'.$rating.'" stars_eval="'.$evaluation.'" ]') ;
			?>
		</div>
		<div class="rva-r">
			<div class="rva-rl">
				<div class="rva-item">
					<h3><?php echo $years; ?></h3>
					<p><?php echo __('Desde','goaposta');?></p>
				</div>
				<div class="rva-item">
					<h3><?php echo $support; ?></h3>
					<p><?php echo __('Idioma','goapostas');?></p>
				</div>
			</div>

			<?php if( $link['url'] ): ?>
			<a href="<?php echo $link['url']; ?>" class="btn-solid" target="<?php echo $link['target']; ?>"><?php echo $link['title']; ?></a>
			<?php endif; ?>
		</div>
	</div>
	<?php
	$out = ob_get_clean();
	endif;
	return $out;
}