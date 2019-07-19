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

	 <?php 
	 do_action('gp_after_content');
	 ?>

 	<!-- Pre Footer Section -->
 	<section class="pre-footer">
 		<div class="wrap">
 			<div class="container-pre-footer">
 				<h3><?php echo get_field('pre_footer_contact_title', 'option'); ?></h3>
 				<div class="container-form"><?php echo do_shortcode(get_field('pre_footer_form', 'option')); ?></div>
 			</div>
 		</div>
 	</section>
 	<!-- End Pre Footer Section -->

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
							<li><a href="<?php echo $footer_s['google']; ?>" alt="Google" title="Google" target="_blank"><i class="icon-google"></i></a></li>
							<?php
						}
						if($footer_s['facebook']){
							?>
							<li><a href="<?php echo $footer_s['facebook']; ?>" alt="Facebook" title="Facebook" target="_blank"><i class="icon-facebook"></i></a></li>
							<?php
						}
						if($footer_s['twitter']){
							?>
							<li><a href="<?php echo $footer_s['twitter']; ?>" alt="Twitter" title="Twitter" target="_blank"><i class="icon-twitter"></i></a></li>
							<?php
						}
						if($footer_s['instagram']){
							?>
							<li><a href="<?php echo $footer_s['instagram']; ?>" alt="Instagram" title="Instagram" target="_blank"><i class="icon-instagram"></i></a></li>
							<?php
						}
						if($footer_s['behance']){
							?>
							<li><a href="<?php echo $footer_s['behance']; ?>" alt="Behance" title="Behance" target="_blank"><i class="icon-behance"></i></a></li>
							<?php
						}
						if($footer_s['whatsapp']) {
							?>
							<li style="line-height:0;"><a href="<?php echo $footer_s['whatsapp']; ?>" alt="Whatsapp" title="Whatsapp" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/whatsapp.png" style="width:18px;line-height:0;margin:0 5px;" /></a></li>
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
