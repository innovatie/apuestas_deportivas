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
<h2><?php _e('Manage Transactions', 'adrotate-pro'); ?></h2>
<p><em><strong><?php _e('Important:', 'adrotate-pro'); ?></strong> <?php _e('Transactions as a feature has been removed. For reference all current transactions are listed below. Please move your transaction data elsewhere. This menu will be removed in a future version of AdRotate Pro!', 'adrotate-pro'); ?></em></p>

<form name="banners" id="post" method="post" action="admin.php?page=adrotate-transactions">
	<?php wp_nonce_field('adrotate_bulk_transactions','adrotate_nonce'); ?>

	<div class="tablenav top">
		<div class="alignleft actions">
			<select name="adrotate_action" id="cat" class="postform">
		        <option value=""><?php _e('Bulk Actions', 'adrotate-pro'); ?></option>
		        <option value="transaction_paid"><?php _e('Mark as paid', 'adrotate-pro'); ?></option>
		        <option value="transaction_delete"><?php _e('Delete', 'adrotate-pro'); ?></option>
			</select> <input type="submit" id="post-action-submit" name="adrotate_action_submit" value="<?php _e('Go', 'adrotate-pro'); ?>" class="button-secondary" />
		</div>	
		<br class="clear" />
	</div>

	<table class="widefat tablesorter manage-transactions-main" style="margin-top: .5em">
		<thead>
		<tr>
			<td scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></td>
			<th width="20%"><?php _e('Reference', 'adrotate-pro'); ?></th>
			<th><?php _e('Advertiser / Advert', 'adrotate-pro'); ?></th>
	        <th width="10%"><center><?php _e('Amount', 'adrotate-pro'); ?></center></th>
	        <th width="15%"><center><?php _e('Billed', 'adrotate-pro'); ?> / <?php _e('Paid', 'adrotate-pro'); ?></center></th>
		</tr>
		</thead>
		<tbody>
	<?php
	$transactions = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}adrotate_transactions` WHERE `reference` != '' ORDER BY `id` ASC;");

	if($transactions) {
		$class = '';
		foreach($transactions as $transaction) {
			$class = ($class != 'alternate') ? 'alternate' : '';
			if($transaction->billed < $overdue AND $adrotate_config['payment_overdue'] > 0) $class = 'row_blue'; // Late payment
			
			$advertiser = $wpdb->get_row("SELECT `title`, `display_name` FROM `{$wpdb->prefix}adrotate`, `{$wpdb->prefix}adrotate_linkmeta`, `{$wpdb->users}` WHERE `{$wpdb->prefix}adrotate`.`id` = {$transaction->ad} AND `{$wpdb->prefix}adrotate`.`id` = `{$wpdb->prefix}adrotate_linkmeta`.`ad` AND `{$wpdb->prefix}adrotate_linkmeta`.`user` = `{$wpdb->users}`.`id` AND `group` = 0 AND `schedule` = 0;");

			$paid = date_i18n("F d, Y", $transaction->paid);
			$billed = date_i18n("F d, Y", $transaction->billed);
			$tick = '<img src="'.plugins_url('../../images/tick.png', __FILE__).'" width="10" height"10" title="'.$paid.'" />';
			$cross = '<img src="'.plugins_url('../../images/cross.png', __FILE__).'" width="10" height"10" title="'.__('Unpaid', 'adrotate-pro').'" />';
			?>
		    <tr id='adrotateindex' class='<?php echo $class; ?>'>
				<th class="check-column"><input type="checkbox" name="transactioncheck[]" value="<?php echo $transaction->id; ?>" /></th>
				<td><strong><?php echo $transaction->reference; ?></strong></td>
				<td><a href="<?php echo admin_url('/admin.php?page=adrotate-advertisers&view=profile&user='.$transaction->user);?>" title="<?php _e('Profile', 'adrotate-pro'); ?>"><?php echo $advertiser->display_name; ?></a> / <?php echo $advertiser->title; ?><br />
					<strong>Note:</strong> <?php echo stripslashes($transaction->note); ?><br />
					The paid amount should <?php echo (stripslashes($transaction->note) == "Y") ? "NOT" : ""; ?> be credited to the advert budget.</td>
				<td><center><?php echo $adrotate_config['payment_currency'].' '.number_format($transaction->amount, 2, '.', ''); ?></center></td>
				<td><center><?php echo $billed;?><br /><?php echo ($transaction->paid > 0) ? $tick.' '.$paid : $cross.' '.__('Unpaid', 'adrotate-pro'); ?></center></td>
			</tr>
			<?php } ?>
		<?php } else { ?>
		<tr id='no-schedules'>
			<th class="check-column">&nbsp;</th>
			<td colspan="5"><em><?php _e('Nothing here!', 'adrotate-pro'); ?></em></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<p><center>
	<span style="border: 1px solid #466f82; height: 12px; width: 12px; background-color: #8dcede">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e('Late payment.', 'adrotate-pro'); ?>
</center></p>
</form>
