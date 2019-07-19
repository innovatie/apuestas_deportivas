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
 * Override this template by copying it to yourtheme/adrotate-active.php
 *
 * @version     1.0
 */

global $current_user;

$adverts = adrotate_load_adverts($current_user->ID);

if(count($adverts['active']) > 0) { ?>

<table style="margin-top: .5em">
	<thead>
	<tr>
		<th colspan="3"><?php _e('ID - Title', 'adrotate-pro'); ?></th>
		<th width="20%"><?php _e('Visible from', 'adrotate-pro'); ?></th>
		<th width="20%"><?php _e('Visible until', 'adrotate-pro'); ?></th>
	</tr>
	</thead>
	
	<tbody>
<?php
	foreach($adverts['active'] as $ad) {
		$stats = adrotate_stats($ad['id']);
		$stats_today = adrotate_stats($ad['id'], false, adrotate_date_start('day'));
		$grouplist = adrotate_ad_is_in_groups($ad['id']);

		$ctr = adrotate_ctr($stats['clicks'], $stats['impressions']);						

		$class = 'adrotate_active';
		if($ad['type'] == '2days') $class = ' adrotate_error'; 
		if($ad['type'] == '7days') $class = ' adrotate_error';
		if($ad['type'] == 'expired') $class = ' adrotate_urgent';
?>

	    <tr id='banner-<?php echo $ad['id']; ?>' class='<?php echo $class; ?> <?php echo $ad['type']; ?>'>
			<td colspan="3"><?php echo $ad['id']; ?> - <strong><?php echo $ad['title'];?></strong><?php if(strlen($grouplist) > 0) echo '<br /><span style="color:#999;"><span style="font-weight:bold;">'.__('Groups', 'adrotate-pro').':</span> '.$grouplist.'</span>'; ?></td>
			<td><?php echo date_i18n("F d, Y", $ad['firstactive']);?></td>
			<td><span style="color: <?php echo adrotate_prepare_color($ad['lastactive']);?>;"><?php echo date_i18n("F d, Y", $ad['lastactive']);?></span></td>
		</tr>
	  	<tr class='<?php echo $class; ?>'>
	        <td width="20%"><div class="adrotate_stats_large"><?php _e('Impressions', 'adrotate-pro'); ?><br /><div class="adrotate_large"><?php echo $stats['impressions']; ?></div></div></td>
	        <td width="20%"><div class="adrotate_stats_large"><?php _e('Clicks', 'adrotate-pro'); ?><br /><div class="adrotate_large"><?php if($ad['tracker'] == "Y") { echo $stats['clicks']; } else { echo '--'; } ?></div></div></td>
	        <td width="20%"><div class="adrotate_stats_large"><?php _e('Impressions today', 'adrotate-pro'); ?><br /><div class="adrotate_large"><?php echo $stats_today['impressions']; ?></div></div></td>
	        <td width="20%"><div class="adrotate_stats_large"><?php _e('Clicks today', 'adrotate-pro'); ?><br /><div class="adrotate_large"><?php if($ad['tracker'] == "Y") { echo $stats_today['clicks']; } else { echo '--'; } ?></div></div></td>
	        <td width="20%"><div class="adrotate_stats_large"><?php _e('CTR', 'adrotate-pro'); ?><br /><div class="adrotate_large"><?php if($ad['tracker'] == "Y") { echo $ctr.' %'; } else { echo '--'; } ?></div></div></td>
	  	</tr>
		<tr class='<?php echo $class; ?>'>
			<td colspan="5"><?php echo adrotate_stats_graph('advertiser', $ad['id'], $ad['id'], mktime(0, 0, 0, date("m")-2, 1, date("Y")), mktime(0, 0, 0, date("m")+1, 0, date("Y")), 100); ?></td>
		</tr>
	<?php } ?>
	</tbody>

</table>
<p><center>
	<span style="border: 1px solid #e6db55; height: 12px; width: 12px; background-color: #ffffe0">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Is almost expired.", "adrotate-pro"); ?>
	&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #c00; height: 12px; width: 12px; background-color: #ffebe8">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Has expired.", "adrotate-pro"); ?>
</center></p>

<?php } else { ?>

<p><center><?php _e("No adverts found, if you feel this to be in error contact your publisher.", "adrotate-pro"); ?></center></p>

<?php } ?>