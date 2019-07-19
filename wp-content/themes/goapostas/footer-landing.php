<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package GoApostas
 */

?>	
		</div><!--#wrap-->
 	</div><!-- #content -->

 	<!-- Pre Footer -->
 	<section class="pre-footer-landing">
 		<div class="wrap">
 			<h2><?php echo get_field('pre_footer_title'); ?></h2>
 			<?php
 			if( have_rows('pre_footer_books') ):
 				?>
 				<div class="container-books">
 				<?php
			    while ( have_rows('pre_footer_books') ) : the_row();
			    	?>
			        <div class="item-book">
			        	<a href="<?php echo get_sub_field('book_link'); ?>"><img src="<?php echo get_sub_field('book_image'); ?>" /></a>
			        </div>
			    	<?php
			    endwhile;
			    ?>
				</div>
			    <?php
			endif;
 			?>
 		</div>
 	</section>
 	<!-- End Pre Footer -->

	<!-- Footer Socials -->
	<section class="socials-footer">
		<div class="wrap">
			<div class="socials-container">
				<?php
				$footer_s = get_field('socials', 'option');
				if($footer_s) {
					?>
					<ul>
						<?php
						if($footer_s['google']){
							?>
							<li><a href="<?php echo $footer_s['google']; ?>" target="_blank"><i class="icon-google"></i></a></li>
							<?php
						}
						if($footer_s['facebook']){
							?>
							<li><a href="<?php echo $footer_s['facebook']; ?>" target="_blank"><i class="icon-facebook"></i></a></li>
							<?php
						}
						if($footer_s['twitter']){
							?>
							<li><a href="<?php echo $footer_s['twitter']; ?>" target="_blank"><i class="icon-twitter"></i></a></li>
							<?php
						}
						if($footer_s['instagram']){
							?>
							<li><a href="<?php echo $footer_s['instagram']; ?>" target="_blank"><i class="icon-instagram"></i></a></li>
							<?php
						}
						if($footer_s['behance']){
							?>
							<li><a href="<?php echo $footer_s['behance']; ?>" target="_blank"><i class="icon-behance"></i></a></li>
							<?php
						}
						if($footer_s['whatsapp']) {
							?>
							<li style="line-height:0;"><a href="<?php echo $footer_s['whatsapp']; ?>" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/whatsapp.png" style="width:18px;line-height:0;margin:0 5px;" /></a></li>
							<?php
						}
						?>
					</ul>
					<?php
				}
				?>
			</div>
			<div class="copyright"><p><?php echo get_field('copyright', 'option'); ?></p></div>
		</div>
	</section>
	<!-- End Footer Socials -->

	<footer id="colophon" class="site-footer">
		<div class="wrap">
			<a href="#" class="back-to-top"><?php echo __('Voltar ao topo', 'goapostas') ?></a>
			<div class="vc_row">
				<div class="vc_col-sm-2">
					<?php
					$logo_footer = get_field('logo_footer', 'option');
					if($logo_footer) {
						?>
						<a href="<?php echo get_site_url(); ?>"><img src="<?php echo $logo_footer; ?>" /></a>
						<?php
					}
					?>
				</div>
				<div class="vc_col-sm-10">
					<ul class="menu-footer">
					<?php dynamic_sidebar( 'footer-sidebar' ); ?>
					</ul>
				</div>
			</div>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
