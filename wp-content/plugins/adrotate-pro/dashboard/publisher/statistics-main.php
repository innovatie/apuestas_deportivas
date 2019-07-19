<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2019 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */
?>
<h2><?php _e('Statistics', 'adrotate-pro'); ?></h2>

<table class="widefat" style="margin-top: .5em">
	<thead>
 	<tr>
        <th colspan="3"><center><strong><?php _e('General', 'adrotate-pro'); ?></strong></center></th>
        <th>&nbsp;</th>
        <th colspan="3"><center><strong><?php _e('All time', 'adrotate-pro'); ?></strong></center></th>
  	</tr>
	</thead>
	<tbody>
	<tr>
        <td width="16%"><div class="stats_large"><?php _e('Adverts', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats['banners']; ?></div></div></td>
        <td width="16%">&nbsp;</td>
        <td width="16%"><div class="stats_large"><?php _e('Adverts counting stats', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats['tracker']; ?></div></div></td>
        <td>&nbsp;</td>
        <td width="16%"><div class="stats_large"><?php _e('Impressions', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats['overall_impressions']; ?></div></div></td>
        <td width="16%"><div class="stats_large"><?php _e('Clicks', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats['overall_clicks']; ?></div></div></td>
        <td width="16%"><div class="stats_large"><?php _e('CTR', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $ctr_alltime; ?> %</div></div></td>
	</tr>
 	</tbody>
	<thead>
 	<tr>
        <th colspan="3"><center><strong><?php _e('Last month', 'adrotate-pro'); ?></strong></center></th>
        <th>&nbsp;</th>
        <th colspan="3"><center><strong><?php _e('This month', 'adrotate-pro'); ?></strong></center></th>
  	</tr>
	</thead>
	<tbody>
  	<tr>
        <td><div class="stats_large"><?php _e('Impressions', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats['last_month_impressions']; ?></div></div></td>
        <td><div class="stats_large"><?php _e('Clicks', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats['last_month_clicks']; ?></div></div></td>
        <td><div class="stats_large"><?php _e('CTR', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $ctr_last_month.' %'; ?></div></div></td>
        <td>&nbsp;</td>
        <td><div class="stats_large"><?php _e('Impressions', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats['this_month_impressions']; ?></div></div></td>
        <td><div class="stats_large"><?php _e('Clicks', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats['this_month_clicks']; ?></div></div></td>
        <td><div class="stats_large"><?php _e('CTR', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $ctr_this_month.' %'; ?></div></div></td>
  	</tr>
	<?php if($stats['thebest']['id'] > 0) { ?>
	<tr>
        <td colspan="7">
	        <div class="stats_large">
		        <?php _e('Best performing advert', 'adrotate-pro');
				$advertiser = $wpdb->get_var("SELECT `user_login` FROM `{$wpdb->prefix}adrotate_linkmeta`, `$wpdb->users` WHERE `$wpdb->users`.`id` = `{$wpdb->prefix}adrotate_linkmeta`.`user` AND `ad` = '".$stats['thebest']['id']."' AND `group` = '0' AND `schedule` = '0' LIMIT 1;");

	        	echo ' \''.$stats['thebest']['title'].'\' ';
	        	if(!empty($advertiser)) echo __('from', 'adrotate-pro').' \''.$advertiser.'\' ';
	        	echo __('with', 'adrotate-pro').' '.$stats['thebest']['clicks'].' '.__('clicks.', 'adrotate-pro'); ?><br />
	        	<div style="margin: 10px;"><?php echo adrotate_preview($stats['thebest']['id']); ?></div>
	        	<?php unset($advertiser); ?>
			</div>
        </td>
	</tr>
	<?php } ?>
	<?php if($stats['theworst']['id'] > 0) { ?>
	<tr>
        <td colspan="7">
	        <div class="stats_large">
		        <?php _e('Least performing advert', 'adrotate-pro');
				$advertiser = $wpdb->get_var("SELECT `user_login` FROM `{$wpdb->prefix}adrotate_linkmeta`, `$wpdb->users` WHERE `$wpdb->users`.`id` = `{$wpdb->prefix}adrotate_linkmeta`.`user` AND `ad` = '".$stats['theworst']['id']."' AND `group` = '0' AND `schedule` = '0' LIMIT 1;");

	        	echo ' \''.$stats['theworst']['title'].'\' ';
	        	if(!empty($advertiser)) echo __('from', 'adrotate-pro').' \''.$advertiser.'\' ';
	        	echo __('with', 'adrotate-pro').' '.$stats['theworst']['clicks'].' '.__('clicks.', 'adrotate-pro'); ?><br />
	        	<div style="margin: 10px;"><?php echo adrotate_preview($stats['theworst']['id']); ?></div>
	        	<?php unset($advertiser); ?>
			</div>
		</td>
	</tr>
	<?php } ?>
	</tbody>
</table>

<h2><?php _e('Monthly overview of clicks and impressions', 'adrotate-pro'); ?></h2>
<table class="widefat" style="margin-top: .5em">

	<tbody>
	<tr>
        <th colspan="3">
        	<div style="text-align:center;"><?php echo adrotate_stats_nav('fullreport', 0, $month, $year); ?></div>
        	<?php echo adrotate_stats_graph('fullreport', false, 0, 1, $monthstart, $monthend); ?>
        </th>
	</tr>
	<tr>
        <td width="33%"><div class="stats_large"><?php _e('Impressions', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_graph_month['impressions']; ?></div></div></td>
        <td width="33%"><div class="stats_large"><?php _e('Clicks', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_graph_month['clicks']; ?></div></div></td>
        <td width="34%"><div class="stats_large"><?php _e('CTR', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $ctr_graph_month; ?> %</div></div></td>
	</tr>
	</tbody>

</table>

<h2><?php _e('Export options', 'adrotate-pro'); ?></h2>
<form method="post" action="admin.php?page=adrotate-ads">
<?php wp_nonce_field('adrotate_export_global','adrotate_nonce'); ?>
<input type="hidden" name="adrotate_export_id" value="0" />
<input type="hidden" name="adrotate_export_type" value="global" />

<?php 
$start_date	= adrotate_date_start('day');
$end_date = $start_date + (86400 * 7);
?>

<table class="widefat" style="margin-top: .5em">

	<tbody>
 	<tr>
		<th width="15%"><?php _e('Select period', 'adrotate-pro'); ?></th>
	    <td>
			<input tabindex="1" type="date" id="datepicker" name="adrotate_start_date" value="<?php echo gmdate("Y-m-d", $start_date); ?>" class="datepicker" /> / <input tabindex="2" type="date" id="datepicker" name="adrotate_end_date" value="<?php echo gmdate("Y-m-d", $end_date); ?>" class="datepicker" />
		</td>
	</tr>	
    <tr>
		<th><?php _e('Email options', 'adrotate-pro'); ?></th>
		<td>
  			<input tabindex="3" type="text" name="adrotate_export_addresses" size="45" class="search-input" value="" autocomplete="off" /> <em><?php _e('Maximum of 3 email addresses, comma seperated. Leave empty to download the CSV file instead.', 'adrotate-pro'); ?></em>
		</td>
	</tr>
    <tr>
		<th>&nbsp;</th>
		<td>
  			<input tabindex="4" type="submit" name="adrotate_export_submit" class="button-primary" value="<?php _e('Export', 'adrotate-pro'); ?>" /> <em><?php _e('Download or email your selected timeframe as a CSV file.', 'adrotate-pro'); ?></em>
		</td>
	</tr>
	</tbody>
</table>
</form>

<p><center>
	<em><small><strong><?php _e('Note:', 'adrotate-pro'); ?></strong> <?php _e('All statistics are indicative. They do not nessesarily reflect results counted by other parties.', 'adrotate-pro'); ?></small></em>
</center></p>