<?php

if( have_rows('btm_advert_blocks', 'option') ):
?>
<section class="sc-adverts">
	<div class="wrap">
		<div class="vc-row">
	<?php

	while ( have_rows('btm_advert_blocks', 'option') ) : the_row();
	?>

			<div class="wpb_column vc_col-sm-4">
				<a href="<?php echo get_sub_field('link_url'); ?>">
				<div class="vc_column-inner">
					<h6><?php echo get_sub_field('advert_title'); ?></h6>
					<p><?php echo get_sub_field('advert_content'); ?></p>
				</div>
				</a>
			</div>
	<?php 
	endwhile;
	?>
		</div>
	</div>
</section>
<?php 
endif; 
?>