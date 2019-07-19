<?php
/**
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package GoApostas
 */

get_header();
?>
		<?php
		if ( have_posts() ) :


			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				/*
				 * Include the Post-Type-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
				 */
				get_template_part( 'template-parts/content','fullwidth' );

				?>
				<section class="sc-sharing">
					<div class="wrap wrap-md">
						<hr>
						<div class="sharing-wrap">
							<div class="sharethis-inline-share-buttons" data-share="<?php echo __('Shares', 'goapostas'); ?>" 
								data-more="<?php echo __('More options', 'goapostas'); ?>"></div>
						</div>
					</div>
				</section>
				<?php
				$out = '';
			   	$out .= '<div class="palpites-list simple-list">';
			   		$out .= '<div class="title-news" style="background:#0da375;">'.__('Mais palpites', 'goapostas').'</div>';
					$services = new WP_Query(
				        array(
				            'post_type' => 'palpite',
				            'showposts' => 6
				        )
				    );
				    $counter = 0;
				    $out .= '<div class="loop">';
				    while ($services->have_posts()) : $services->the_post();
				    	$counter++;
				    	$thumb_id = get_post_thumbnail_id();
						$thumb_url = wp_get_attachment_image_src($thumb_id,'medium', true);
						if(has_post_thumbnail()){
							$thumb = $thumb_url[0];
						}else{
							$thumb = get_stylesheet_directory_uri().'/assets/images/goapostas-default.jpg';
						}
						$out .= '<div class="third-content">';
							$out .= '<div class="content">';
								$out .= '<a class="thumb" href="'.get_the_permalink().'" style="background:url('.$thumb.');"></a>';
								$out .= '<div class="detail">';
									$tit_l = strlen(get_the_title());
									if ($tit_l > 50) {
										$out .= '<h6><a href="'.get_the_permalink().'">'.substr(get_the_title(),0,50).'...</a></h6>';
									}else{
										$out .= '<h6><a href="'.get_the_permalink().'">'.get_the_title().'</a></h6>';
									}
									$out .= '<hr>';
									$out .= '<div class="author">BY '.get_the_author().'</div>';
								$out .= '</div>';
							$out .= '</div>';
						$out .= '</div>';
					endwhile;
					wp_reset_postdata();
					$out .= '</div>';

				$out .= '</div>';
				echo $out;

			endwhile;

			the_posts_navigation();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

<?php
get_footer();
