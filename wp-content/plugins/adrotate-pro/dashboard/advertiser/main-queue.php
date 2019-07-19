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

<h3><?php _e('Queued Adverts', 'adrotate-pro'); ?></h3>
<p><em><?php _e('Adverts listed here are queued for review, awaiting payment, rejected by a reviewer or have a configuration error.', 'adrotate-pro'); ?></em></p>

<table class="widefat" style="margin-top: .5em">
	<thead>
	<tr>
		<th width="2%"><center><?php _e('ID', 'adrotate-pro'); ?></center></th>
		<th><?php _e('Title', 'adrotate-pro'); ?></th>
		<th width="5%"><center><?php _e('Device', 'adrotate-pro'); ?></center></th>
		<th width="20%"><?php _e('Contact publisher', 'adrotate-pro'); ?></th>
	</tr>
	</thead>
	
	<tbody>
<?php
	foreach($queuebanners as $banner) {
		$wpnonceaction = 'adrotate_email_advertiser_'.$banner['id'];
		$nonce = wp_create_nonce($wpnonceaction);
		
		$class = $errorclass = '';
		if('alternate' == $class) $class = 'alternate'; else $class = '';
		if($banner['type'] == 'error' OR $banner['type'] == 'a_error') $errorclass = ' row_yellow';
		if($banner['type'] == 'reject') $errorclass = ' row_red';
		if($banner['type'] == 'unpaid' AND $adrotate_config['payment_enable'] == "Y") $errorclass = ' row_blue';

		$mobile = '';
		if($banner['desktop'] == 'Y') {
			$mobile .= '<img src="'.plugins_url('../../images/desktop.png', __FILE__).'" width="12" height="12" title="Desktop" />';
		}
		if($banner['mobile'] == 'Y') {
			$mobile .= '<img src="'.plugins_url('../../images/mobile.png', __FILE__).'" width="12" height="12" title="Mobile" />';
		}
		if($banner['tablet'] == 'Y') {
			$mobile .= '<img src="'.plugins_url('../../images/tablet.png', __FILE__).'" width="12" height="12" title="Tablet" />';
		}

		if($adrotate_config['payment_enable'] == "Y") {
			$transaction = '';
			if($banner['type'] == 'unpaid') {
				$transaction = $wpdb->get_row("SELECT `reference`, `billed`, `amount` FROM `{$wpdb->prefix}adrotate_transactions` WHERE `ad` = {$banner['id']} AND `paid` = 0 ORDER BY `id` ASC LIMIT 1;");
			}
	
			// Prevent gaps
			$transaction_reference = (empty($transaction->reference)) ? 0 : $transaction->reference;
			$transaction_amount = (empty($transaction->amount)) ? 0 : $transaction->amount;

			$payment_arguments = array(
				'business' => $adrotate_config['payment_address'],
				'item_name' => html_entity_decode($banner['id'].' / '.$banner['title']),
				'item_number' => $transaction_reference,
				'amount' => number_format($transaction_amount, 2, '.', ''),
				'currency_code' => $adrotate_config['payment_currency'],
				'button_subtype' => 'services',
				'no_note' => 1,
				'no_shipping' => 2,
			);
			$payment_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&'.build_query($payment_arguments);
		}
		?>
	    <tr id='banner-<?php echo $banner['id']; ?>' class='<?php echo $class.$errorclass; ?>'>
			<td><center><?php echo $banner['id'];?></center></td>
			<td>
				<strong><a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-advertiser&view=edit&ad='.$banner['id']);?>" title="<?php _e('Edit', 'adrotate-pro'); ?>"><?php echo stripslashes(html_entity_decode($banner['title']));?></a></strong>
				<span style="color:#999;">
					<?php if($banner['crate'] > 0 OR $banner['irate'] > 0) {
						echo '<br /><span style="font-weight:bold;">'.__('Budget:', 'adrotate-pro').'</span> '.number_format($banner['budget'], 2, '.', '').' - '; 
						echo __('CPC:', 'adrotate-pro').' '.number_format($banner['crate'], 2, '.', '').' - ';
						echo __('CPM:', 'adrotate-pro').' '.number_format($banner['irate'], 2, '.', '');
					} ?>
				</span>
			</td>
			<td><center><?php echo $mobile;?></center></td>
			<td><a href="admin.php?page=adrotate-advertiser&view=message&request=renew&id=<?php echo $banner['id']; ?>&_wpnonce=<?php echo $nonce; ?>"><?php _e('Renew', 'adrotate-pro'); ?></a> - <a href="admin.php?page=adrotate-advertiser&view=message&request=remove&id=<?php echo $banner['id']; ?>&_wpnonce=<?php echo $nonce; ?>"><?php _e('Remove', 'adrotate-pro'); ?></a> - <a href="admin.php?page=adrotate-advertiser&view=message&request=other&id=<?php echo $banner['id']; ?>&_wpnonce=<?php echo $nonce; ?>"><?php _e('Other', 'adrotate-pro'); ?></a></td>
		</tr>
		<?php } ?>
	</tbody>

</table>
<p><center>
	<span style="border: 1px solid #e6db55; height: 12px; width: 12px; background-color: #ffffe0">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Configuration errors", "adrotate-pro"); ?>
	&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #c00; height: 12px; width: 12px; background-color: #ffebe8">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Rejected", "adrotate-pro"); ?>
	<?php if($adrotate_config['payment_enable'] == "Y") { ?>
	&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #466f82; height: 12px; width: 12px; background-color: #8dcede">&nbsp;&nbsp;&nbsp;&nbsp;</span> <?php _e("Unpaid", "adrotate-pro"); ?>
	<?php } ?>
</center></p>