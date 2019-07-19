<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package GoApostas
 */

get_header();
$image = get_stylesheet_directory_uri() . '/assets/images/news-hero-bg.jpg';
?>
<section class="hero hero-shadow" style="background-image: url(<?php echo $image; ?>)">
	<div class="wrap">
		<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
		<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
	</div>
</section>

	<div id="primary" class="content-area full-width archive-loop">
		<div class="wrap">
		<main id="main" class="site-main archive-cnt">

		<?php if ( have_posts() ) : ?>

			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				/*
				 * Include the Post-Type-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
				 */
				get_template_part( 'template-parts/content', get_post_type() );

			endwhile;

			the_posts_navigation();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

		</main><!-- #main -->
		</div>
	</div><!-- #primary -->

<?php
get_footer();
