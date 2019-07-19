<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package GoApostas
 */

get_header();

$image = get_stylesheet_directory_uri() . '/assets/images/404.jpg';

?>
	<section class="hero hero-shadow" style="background-image: url(<?php echo $image; ?>)">
		<div class="wrap">
			<h1 class="page-title" style="margin-bottom:0;"><?php esc_html_e( 'Oops', 'goapostas' ); ?></h1>
			<p style="text-align:center;"><?php esc_html_e('404! Essa página não existe.', 'goapostas'); ?></p>
			<?php if( have_rows('btm_advert_blocks_404', 'option') ):
			?>
			<section class="sc-adverts">
				<div class="wrap" style="max-width:100%;">
					<div class="vc-row">
					<?php
					while ( have_rows('btm_advert_blocks_404', 'option') ) : the_row();
					?>
						<div class="wpb_column vc_col-sm-4">
							<a href="<?php echo get_sub_field('link_url'); ?>">
							<div class="vc_column-inner">
								<h4><?php echo get_sub_field('advert_title'); ?></h4>
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
			endif; ?>
		</div>
	</section>

	<div id="primary" class="content-area full-width">
		<div class="wrap">
		<main id="main" class="site-main">

		</main><!-- #main -->
		</div>
	</div><!-- #primary -->

<?php
get_footer();
