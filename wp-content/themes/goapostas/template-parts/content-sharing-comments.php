<?php

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

<section class="sc-comments">
	<div class="wrap">
		<?php comments_template(); ?>
	</div>
</section>

<?php if( is_singular('news') ): ?>
<section class="news-related dark">
	<div class="wrap">
		<?php echo do_shortcode('[news-loop quantity="3"]'); ?>
	</div>
</section>
<?php endif; ?>
