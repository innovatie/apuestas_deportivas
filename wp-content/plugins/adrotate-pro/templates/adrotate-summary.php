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
 * Override this template by copying it to yourtheme/adrotate-summary.php
 *
 * @version     1.0
 */

global $current_user;

$adverts = adrotate_load_adverts($current_user->ID);

// Summary stats
$summary = adrotate_prepare_advertiser_report($current_user->ID, $adverts['active']);
?>

<table class="widefat" style="margin-top: .5em">					
	<tbody>
    <tr>
		<td width="15%"><?php _e('General', 'adrotate'); ?></td>
		<td><?php echo $summary['ad_amount']; ?> <?php _e('ads, sharing a total of', 'adrotate'); ?> <?php echo $summary['total_impressions']; ?> <?php _e('impressions.', 'adrotate'); ?></td>
	</tr>
    <tr>
		<td><?php _e('Most clicks', 'adrotate'); ?></td>
		<td><?php if($summary['thebest']) {?>'<?php echo $summary['thebest']['title']; ?>' <?php _e('with', 'adrotate'); ?> <?php echo $summary['thebest']['clicks']; ?> <?php _e('clicks.', 'adrotate'); ?><?php } else { ?><?php _e('No ad stands out at this time.', 'adrotate'); ?><?php } ?></td>
	</tr>
    <tr>
		<td><?php _e('Least clicks', 'adrotate'); ?></td>
		<td><?php if($summary['theworst']) {?>'<?php echo $summary['theworst']['title']; ?>' <?php _e('with', 'adrotate'); ?> <?php echo $summary['theworst']['clicks']; ?> <?php _e('clicks.', 'adrotate'); ?><?php } else { ?><?php _e('No ad stands out at this time.', 'adrotate'); ?><?php } ?></td>
	</tr>
    <tr>
		<td><?php _e('Click-Through-Rate', 'adrotate'); ?></td>
		<td><?php echo adrotate_ctr($summary['total_clicks'], $summary['total_impressions']); ?>%, <?php _e('based on', 'adrotate'); ?> <?php echo $summary['total_impressions']; ?> <?php _e('impressions and', 'adrotate'); ?> <?php echo $summary['total_clicks']; ?> <?php _e('clicks.', 'adrotate'); ?></td>
	</tr>
	</tbody>
</table>

<h3><?php _e('Monthly overview of clicks and impressions', 'adrotate'); ?></h3>
<?php echo adrotate_stats_graph('advertiserfull', false, $current_user->ID, 100000, mktime(0, 0, 0, date("m"), 1, date("Y")), mktime(0, 0, 0, date("m")+1, 0, date("Y")), 200); ?>