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

<form name="settings" id="post" method="post" action="admin.php?page=adrotate-settings&tab=license">
<?php wp_nonce_field('adrotate_license','adrotate_nonce_license'); ?>
<input type="hidden" name="adrotate_settings_tab" value="<?php echo $active_tab; ?>" />

<h2><?php _e('AdRotate Pro License', 'adrotate-pro'); ?></h2>
<span class="description"><?php _e('Activate your AdRotate Pro License to receive automatic updates, use AdRotate Geo and be eligble for premium support.', 'adrotate-pro'); ?></span>
<table class="form-table">
	<tr>
		<th valign="top"><?php _e('License Type', 'adrotate-pro'); ?></th>
		<td>
			<?php echo ($adrotate_activate['type'] != '') ? $subscription.$adrotate_activate['type'].$legacy : __('Not activated - Not eligible for support and updates.', 'adrotate-pro'); ?>
		</td>
	</tr>
	<?php if($adrotate_hide_license == 0 AND !$adrotate_is_networked) { ?>
		<?php if($adrotate_activate['version'] == 101) { ?>
		<tr>
			<th valign="top"><?php _e('Important', 'adrotate-pro'); ?></th>
			<td>
				<?php _e('Your license has reached End-Of-Life with AdRotate Professional 4.13. In order to continue to receive updates, support and access to AdRotate Geo please renew your license. As a thank you for your continued use and support of AdRotate Pro you can get your renewal license at a special discounted price.', 'adrotate-pro'); ?> <a href="https://ajdg.solutions/manuals/adrotate-manuals/adrotate-pro-license-renewal/" target="_blank"><?php _e('More information', 'adrotate-pro'); ?> &raquo;</a>
			</td>
		</tr>
		<?php } ?>
	<tr>
		<th valign="top"><?php _e('License Email', 'adrotate-pro'); ?></th>
		<td>
			<input name="adrotate_license_email" type="text" class="search-input" size="50" value="<?php echo $adrotate_activate['email']; ?>" autocomplete="off" <?php echo ($adrotate_activate['status'] == 1) ? 'disabled' : ''; ?> /> <span class="description"><?php _e('The email address you used in your purchase of AdRotate Pro.', 'adrotate-pro'); ?></span>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('License Key', 'adrotate-pro'); ?></th>
		<td>
			<input name="adrotate_license_key" type="text" class="search-input" size="50" value="<?php echo $adrotate_activate['key']; ?>" autocomplete="off" <?php echo ($adrotate_activate['status'] == 1) ? 'disabled' : ''; ?> /> <span class="description"><?php _e('You can find the license key in your order email.', 'adrotate-pro'); ?></span>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Hide License Details', 'adrotate-pro'); ?></th>
		<td>
			<input type="checkbox" name="adrotate_license_hide" <?php echo ($adrotate_activate['status'] == 1) ? 'disabled' : ''; ?> /> <span class="description"><?php _e('If you have installed AdRotate Pro for a client or in a multisite setup and want to hide the License Key, Email and Mass-deactivation button (Duo, Multi and Developer License) from them.', 'adrotate-pro'); ?></span>
		</td>
	</tr>
	<?php if($adrotate_activate['status'] == 1) { ?>
	<tr>
		<th valign="top"><?php _e('Force de-activate', 'adrotate-pro'); ?></th>
		<td>
			<input type="checkbox" name="adrotate_license_force" /> <span class="description"><?php _e('If your yearly subscription has expired you may need to force de-activate the license before you can activate again after renewing your subscription.', 'adrotate-pro'); ?></span>
		</td>
	</tr>
	<?php } ?>
	<?php } ?>
</table>

<?php if(!$adrotate_is_networked) { ?>
	<p class="submit">
		<?php if($adrotate_activate['status'] == 0) { ?>
		<input type="submit" id="post-role-submit" name="adrotate_license_activate" value="<?php _e('Activate license', 'adrotate-pro'); ?>" class="button-primary" />
		<?php } else { ?>
		<input type="submit" id="post-role-submit" name="adrotate_license_deactivate" value="<?php _e('De-activate license', 'adrotate-pro'); ?>" class="button-primary" />
		<?php } ?>
		&nbsp;&nbsp;<em><?php _e('Click only once! this may take a few seconds.', 'adrotate-pro'); ?></em>
	</p>
<?php } ?>