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
 * Override this template by copying it to yourtheme/adrotate-disabled.php
 *
 * @version     1.0
 */

global $current_user;

$adverts = adrotate_load_adverts($current_user->ID);

if(count($adverts['disabled']) > 0) { ?>

<table style="margin-top: .5em">
	<thead>
	<tr>
		<th width="5%"><center><?php _e('ID', 'adrotate'); ?></center></th>
		<th><?php _e('Title', 'adrotate'); ?></th>
		<th width="8%"><center><?php _e('Impressions', 'adrotate'); ?></center></th>
		<th width="8%"><center><?php _e('Clicks', 'adrotate'); ?></center></th>
		<th width="8%"><center><?php _e('CTR', 'adrotate'); ?></center></th>
	</tr>
	</thead>
	
	<tbody>
<?php
	foreach($adverts['disabled'] as $ad) {
		$stat = adrotate_stats($ad['id']);
		$ctr = adrotate_ctr($stat['clicks'], $stat['impressions']);						
?>
	    <tr id='banner-<?php echo $ad['id']; ?>' class='adrotate_inactive'>
			<td><center><?php echo $ad['id'];?></center></td>
			<td><strong><?php echo $ad['title'];?></strong></td>
			<td><center><?php echo $stat['impressions']; ?></center></td>
			<td><center><?php echo $stat['clicks']; ?></center></td>
			<td><center><?php echo $ctr; ?> %</center></td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<?php } ?>