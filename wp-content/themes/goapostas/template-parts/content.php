<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package GoApostas
 */
$attr_s = '';
if ($_GET['search']) {
	$attr_s = 'search-class="true"';
}

?>

<article <?php echo $attr_s; ?> id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	if (has_post_thumbnail()) {
		?>
		<div class="thumb"><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_post_thumbnail(get_the_ID(), 'thumbnail'); ?></a></div>
		<?php
	}else{
		?>
		<div class="thumb"><a href="<?php echo get_the_permalink(); ?>"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/goapostas-default.jpg" width="150" height="150" /></a></div>
		<?php
	}
	?>
	<div class="content-res-f">
		<header class="entry-header">
			<?php
			if ( is_singular() ) :
				the_title( '<h1 class="entry-title">', '</h1>' );
			else :
				the_title( '<h4 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h4>' );
			endif;
			?>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<?php
			$limit = 25;
			$content = explode(' ', get_the_content(), $limit);
			if (count($content)>=$limit) {
			array_pop($content);
			$content = implode(" ",$content).'...';
			} else {
			$content = implode(" ",$content);
			}	
			$content = preg_replace('/\[.+\]/','', $content);
			$content = apply_filters('the_content', $content); 
			$content = str_replace(']]>', ']]&gt;', $content);
			$content = strip_tags($content, '<br>');
			echo '<p>'.$content.'</p>';
			echo '<a class="search-link" href="'.get_the_permalink().'">'.get_the_permalink().'</a>';
			?>
		</div><!-- .entry-content -->
	</div>

	<footer class="entry-footer">
		<?php //goapostas_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
