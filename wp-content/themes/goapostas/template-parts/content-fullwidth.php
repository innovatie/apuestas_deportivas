<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package GoApostas
 */

?>

<section class="main-content">

	<div class="entry-content">
		<?php

		if (is_singular('review')) {
			$rating = get_field('rating_stars') ? floatval(get_field('rating_stars')) : 0;

			if( $rating ):
				$rating = $rating % 100;
			endif;

			?>
			<section class="vc_section custom" data-vc-full-width="true" data-vc-full-width-init="true">
				<div class="vc_row wpb_row vc_row-fluid row-review-summary">
					<div class="wpb_column vc_column_container vc_col-sm-12">
						<div class="vc_column-inner">
							<div class="wpb_wrapper">
								<div class="review-analysis">
									<div class="rva-l">
										<picture class="rva-image"><img src="<?php echo get_field('review_logo'); ?>" /></picture>
										<div class="stars-rating-wrap">
											<div class="star-rating">
												<span style="width:<?php echo $rating; ?>%">
													<i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i><i class="icon-star-filled"></i>
												</span>
												<i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i>
											</div>
											<div class="stars-evaluation">
												<span><?php echo __('Confiança','goapostas');?></span>
												<span><?php echo $rating/10; ?></span>
											</div>
										</div>
									</div>
									<div class="rva-r">
										<div class="rva-rl">
											<div class="rva-item">
												<h3><?php echo get_field('years'); ?></h3>
												<p><?php echo __('Desde','goapostas');?></p>
											</div>
											<div class="rva-item">
												<h3><?php echo get_field('support'); ?></h3>
												<p><?php echo __('Idioma','goapostas');?></p>
											</div>
										</div>
										<?php
										$external_link = get_field('external_link') ? get_field('external_link') : '';
										if($external_link) {
											?>
											<a href="<?php echo get_field('external_link'); ?>" class="btn-solid"><?php echo __('Crie sua conta','goapostas');?></a>
											<?php
										}
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<?php
		}
		if (is_page()) {
			$floating_content = get_field('show_floating_header_white_content') ? get_field('show_floating_header_white_content') : '';
			$video_col = get_field('show_video_column') ? get_field('show_video_column') : '';
			if($floating_content && $video_col){
				?>
				<section data-vc-full-width="true" data-vc-full-width-init="true" data-vc-stretch-content="true" class="vc_section floating-section" style="overflow-x:hidden;">
					<div class="vc_row wpb_row vc_row-fluid vc_row-o-equal-height vc_row-flex" style="position:relative;">
						<div class="wpb_column vc_column_container vc_col-sm-3">
							<div class="vc_column-inner">
								<div class="wpb_wrapper">
									<div class="wpb_text_column wpb_content_element ">
										<div class="wpb_wrapper">
											<h4 style="text-align:center;"><?php echo get_field('title_floating_section'); ?></h4>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="wpb_column vc_column_container vc_col-sm-3">
							<div class="vc_column-inner">
								<div class="wpb_wrapper">
									<div class="wpb_text_column wpb_content_element ">
										<div class="wpb_wrapper">
											<?php echo get_field('column_1_floating_section'); ?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="wpb_column vc_column_container vc_col-sm-3">
							<div class="vc_column-inner">
								<div class="wpb_wrapper">
									<div class="wpb_text_column wpb_content_element ">
										<div class="wpb_wrapper">
											<?php echo get_field('column_2_floating_section'); ?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="lead-column wpb_column vc_column_container vc_col-sm-3 vc_col-has-fill">
							<div class="vc_column-inner vc_custom_video_bg" style="background:url(<?php echo get_field('video_image'); ?>);background-position:center center;background-size:cover;">
								<div class="wpb_wrapper"><!--href="#video-popup"-->
									<a class="cta-event-link"><span class="icon"><i class="fa fa-play"></i></span>Assistir<span class="sep">|</span>01:42</a>
								</div>
							</div>
						</div>
					</div>
				</section>
				<div class="vc_row-full-width vc_clearfix"></div>
				<?php
			}
			if($floating_content && !$video_col){
				?>
				<section class="vc_section float-white vc_section-has-fill section-float-bordered">
					<div class="vc_row wpb_row vc_row-fluid vc_custom_1557948257193 vc_row-has-fill vc_column-gap-10">
						<div class="wpb_column vc_column_container vc_col-sm-4">
							<div class="vc_column-inner">
								<div class="wpb_wrapper">
									<div class="wpb_text_column wpb_content_element ">
										<div class="wpb_wrapper">
											<h3><?php echo get_field('title_floating_section'); ?></h3>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="wpb_column vc_column_container vc_col-sm-4">
							<div class="vc_column-inner">
								<div class="wpb_wrapper">
									<div class="wpb_text_column wpb_content_element ">
										<div class="wpb_wrapper">
											<?php echo get_field('column_1_floating_section'); ?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="wpb_column vc_column_container vc_col-sm-4">
							<div class="vc_column-inner">
								<div class="wpb_wrapper">
									<div class="wpb_text_column wpb_content_element ">
										<div class="wpb_wrapper">
											<?php echo get_field('column_2_floating_section'); ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
				<?php
			}
		}

		the_content();

		?>
	</div><!-- .entry-content -->
</section><!-- .no-results -->
