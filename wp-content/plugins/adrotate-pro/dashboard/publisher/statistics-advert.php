<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2019 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

$banner = $wpdb->get_row("SELECT `title`, `tracker`, `type` FROM `{$wpdb->prefix}adrotate` WHERE `id` = '{$id}';");

// Determine archived or not
$archive = ($banner->type == "archived") ? true : false;

$table = 'adrotate_stats';
if($archive) {
	$table = 'adrotate_stats_archive';
}

$stats = adrotate_stats($id, $archive);
$stats_today = adrotate_stats($id, $archive, adrotate_date_start('day'), null, 1);
$stats_last_month = adrotate_stats($id, $archive, mktime(0, 0, 0, date("m")-1, 1, date("Y")), mktime(0, 0, 0, date("m"), 0, date("Y")));
$stats_this_month = adrotate_stats($id, $archive, mktime(0, 0, 0, date("m"), 1, date("Y")), mktime(0, 0, 0, date("m"), date("t"), date("Y")));
$stats_graph_month = adrotate_stats($id, $archive, $monthstart, $monthend);

// Prevent gaps in display
if(empty($stats['impressions'])) $stats['impressions'] = 0;
if(empty($stats['clicks']))	$stats['clicks'] = 0;
if(empty($stats_today['impressions'])) $stats_today['impressions'] = 0;
if(empty($stats_today['clicks'])) $stats_today['clicks'] = 0;
if(empty($stats_last_month['impressions'])) $stats_last_month['impressions'] = 0;
if(empty($stats_last_month['clicks'])) $stats_last_month['clicks'] = 0;
if(empty($stats_this_month['impressions'])) $stats_this_month['impressions'] = 0;
if(empty($stats_this_month['clicks'])) $stats_this_month['clicks'] = 0;
if(empty($stats_graph_month['impressions'])) $stats_graph_month['impressions'] = 0;
if(empty($stats_graph_month['clicks'])) $stats_graph_month['clicks'] = 0;

// Get Click Through Rate
$ctr = adrotate_ctr($stats['clicks'], $stats['impressions']);
$ctr_today = adrotate_ctr($stats_today['clicks'], $stats_today['impressions']);
$ctr_last_month = adrotate_ctr($stats_last_month['clicks'], $stats_last_month['impressions']);
$ctr_this_month = adrotate_ctr($stats_this_month['clicks'], $stats_this_month['impressions']);
$ctr_graph_month = adrotate_ctr($stats_graph_month['clicks'], $stats_graph_month['impressions']);
?>
<h2><?php _e('Statistics for advert', 'adrotate-pro'); ?> '<?php echo stripslashes($banner->title); ?>'</h2>
<table class="widefat" style="margin-top: .5em">

	<thead>
  	<tr>
        <th colspan="3"><center><strong><?php _e('Today', 'adrotate-pro'); ?></strong></center></th>
        <th>&nbsp;</th>
		<th colspan="3"><center><strong><?php _e('All time', 'adrotate-pro'); ?></strong></center></th>
  	</tr>
	</thead>
	<tbody>
  	<tr>
        <td width="16%"><div class="stats_large"><?php _e('Impressions', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_today['impressions']; ?></div></div></td>
        <td width="16%"><div class="stats_large"><?php _e('Clicks', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_today['clicks']; ?></div></div></td>
        <td width="16%"><div class="stats_large"><?php _e('CTR', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $ctr_today.' %'; ?></div></div></td>

         <td>&nbsp;</td>
 
		 <td><div class="stats_large"><?php _e('Impressions', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats['impressions']; ?></div></div></td>
        <td width="16%"><div class="stats_large"><?php _e('Clicks', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats['clicks']; ?></div></div></td>
        <td width="16%"><div class="stats_large"><?php _e('CTR', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $ctr.' %'; ?></div></div></td>
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
        <td width="16%"><div class="stats_large"><?php _e('Impressions', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_last_month['impressions']; ?></div></div></td>
        <td width="16%"><div class="stats_large"><?php _e('Clicks', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_last_month['clicks']; ?></div></div></td>
        <td width="16%"><div class="stats_large"><?php _e('CTR', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $ctr_last_month.' %'; ?></div></div></td>

        <td>&nbsp;</td>
 
        <td width="16%"><div class="stats_large"><?php _e('Impressions', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_this_month['impressions']; ?></div></div></td>
        <td width="16%"><div class="stats_large"><?php _e('Clicks', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_this_month['clicks']; ?></div></div></td>
        <td width="16%"><div class="stats_large"><?php _e('CTR', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $ctr_this_month.' %'; ?></div></div></td>
  	</tr>
	<tbody>

</table>

<h2><?php _e('Monthly overview of clicks and impressions', 'adrotate-pro'); ?></h2>
<form method="post" action="admin.php?page=adrotate-statistics&view=advert&id=<?php echo $id; ?>">
<?php wp_nonce_field('adrotate_export_ads','adrotate_nonce'); ?>
<input type="hidden" name="adrotate_export_id" value="<?php echo $id; ?>" />
<input type="hidden" name="adrotate_export_type" value="single" />

<table class="widefat" style="margin-top: .5em">
	<tbody>
	<tr>
        <th colspan="3">
        	<div style="text-align:center;"><?php echo adrotate_stats_nav('ads', $id, $month, $year); ?></div>
        	<?php 
				echo adrotate_stats_graph('ads', $archive, $id, 1, $monthstart, $monthend); 
			?>
        </th>
	</tr>
	<tr>
        <td width="33%"><div class="stats_large"><?php _e('Impressions', 'adrotate-pro'); ?><br /><div class="number_large"><?php echo $stats_graph_month['impressions']; ?></div></div></td>
        <td width="33%"><div class="stats_large"><?php _e('Clicks', 'adrotate-pro'); ?><br /><div class="number_large"><?php if($banner->tracker == "Y") { echo $stats_graph_month['clicks']; } else { echo '--'; } ?></div></div></td>
        <td width="34%"><div class="stats_large"><?php _e('CTR', 'adrotate-pro'); ?><br /><div class="number_large"><?php if($banner->tracker == "Y") { echo $ctr_graph_month.' %'; } else { echo '--'; } ?></div></div></td>
	</tr>
	</tbody>

</table>	
</form>

<h2><?php _e('Export options', 'adrotate-pro'); ?></h2>
<form method="post" action="admin.php?page=adrotate-ads">
<?php wp_nonce_field('adrotate_export_ads','adrotate_nonce'); ?>
<input type="hidden" name="adrotate_export_id" value="<?php echo $id; ?>" />
<input type="hidden" name="adrotate_export_type" value="single" />

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