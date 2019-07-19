<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2017 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

/**
 * Override this template by copying it to yourtheme/adrotate-queued.php
 *
 * @version     1.0
 */

global $current_user;

$adverts = adrotate_load_adverts($current_user->ID);

if(count($adverts['queued']) > 0) { ?>

<table class="widefat" style="margin-top: .5em">
	<thead>
	<tr>
		<th width="5%"><center><?php _e('ID', 'adrotate-pro'); ?></center></th>
		<th><?php _e('Title', 'adrotate-pro'); ?></th>
	</tr>
	</thead>
	
	<tbody>
<?php
	foreach($adverts['queued'] as $ad) {
		$class = 'adrotate_active';
		if($ad['type'] == 'error' OR $ad['type'] == 'a_error') $class = ' adrotate_error';
		if($ad['type'] == 'reject') $class = ' adrotate_urgent';
		?>
	    <tr id='banner-<?php echo $ad['id']; ?>' class='<?php echo $class; ?> <?php echo $ad['type']; ?>'>
			<td><center><?php echo $ad['id'];?></center></td>
			<td><strong><?php echo $ad['title'];?></strong></td>
		</tr>
	<?php } ?>
	</tbody>

</table>
<p><center>
	<span style="border: 1px solid #e6db55; height: 12px; width: 12px; background-color: #ffffe0">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Has configuration errors.", "adrotate-pro"); ?>
	&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #c00; height: 12px; width: 12px; background-color: #ffebe8">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Has been rejected.", "adrotate-pro"); ?>
</center></p>

<?php } ?>