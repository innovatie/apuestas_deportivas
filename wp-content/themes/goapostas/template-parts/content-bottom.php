<?php
/**
 * Template part for displaying content after page content and before pre-footer
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package GoApostas
 */

?>
<section class="top-betting-section">

	<div class="wrap">
	<h3 style="text-align: center;">Top casa de apostas</h3>
		<div class="betting-houses-block block-lg">
			<div class="row-grid">
				<?php
				$title_review = 'iBetting90';
				$evaluation_review = '9.8';

				$args = [
					'post_type' => 'review',
					'posts_per_page' => 4
				];
				$query = new WP_Query( $args );
            	if ( $query->have_posts() ):

					while ( $query->have_posts() ): $query->the_post();
						$rating = get_field('rating_stars');
						$rating = $rating? $rating : 0;

						$evaluation = round( $rating / 10.0, 1);
					?>

					<div class="col col-">
						<div class="review-block">
							<div class="image-container">
								<img src="<?php echo get_field('review_logo'); ?>"/>
							</div>
							<h5><?php echo get_the_title(); ?></h5>
							<?php echo do_shortcode('[stars-rating-block stars_rating="' .$rating. '" stars_eval="' .$evaluation. '" ]'); ?>
							<div>
								<a href="<?php echo get_field('external_link'); ?>" class="btn-solid" target="_blank"> <?php _e('Crie sua conta', 'goapostas'); ?></a>
							</div>
						</div>
					</div>

				<?php endwhile;
				endif; ?>
				<div class="col aside-col col-5 vc_hidden-sm vc_hidden-xs">
					<!-- fake block to maintain heights on column -->
	<!-- 				<div class="review-block">
						<div class="image-container">
							<img  src="<?php //echo get_stylesheet_directory_uri(); ?>/assets/images/bet365.png"/>
						</div>
						<h5> &nbsp; </h5>
						<div class="reviews-stars">
							<i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline"></i><i class="icon-star-outline-o"></i><i class="icon-star-outline-o"></i>
						</div>
						<div class="evaluation-sec">
							<label> &nbsp; </label> <strong> &nbsp; </strong>
						</div>
						<div>
							<a href="#" class="btn-solid"> &nbsp; </a>
						</div>
					</div> -->
					<!-- /. End fake block -->
					<!-- <img width="440" height="732" src="<?php //echo get_stylesheet_directory_uri(); ?>/assets/images/anuncio.png" class="vc_single_image-img attachment-full" alt="" > -->
					<?php echo do_shortcode( '[goapostas_adrotate location="top_casa_221_390"]' ); ?>
				</div>
			</div>

			<div class="navigation-apostas">
				<div class="arrows">
					<div id="prev-tcapostas" class="slick-arrow s-prev" style="display: inline-block;">‹</div>
					<div id="next-tcapostas" class="slick-arrow s-next" style="display: inline-block;">›</div>
				</div>
			</div>

			<div class="bottom-cta vc_hidden-lg vc_hidden-md">
				<a class="btn-solid secondary" href="#">Ver o ranking completo</a>
			</div>

		</div>
	</div>
</section>

<section class="top-betting-aside">
	<div class="wrap">
		<picture>
			<img width="440" height="732" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/anuncio.png" alt="" >
		</picture>
	</div>
</section>

