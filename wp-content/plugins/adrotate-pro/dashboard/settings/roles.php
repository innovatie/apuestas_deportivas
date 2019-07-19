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

<form name="settings" id="post" method="post" action="admin.php?page=adrotate-settings&tab=roles">
<?php wp_nonce_field('adrotate_settings','adrotate_nonce_settings'); ?>
<input type="hidden" name="adrotate_settings_tab" value="<?php echo $active_tab; ?>" />

<h2><?php _e('Roles', 'adrotate-pro'); ?></h2>
<span class="description"><?php _e('Who has access to what? All but the "advertiser page" are usually for admins and moderators.', 'adrotate-pro'); ?></span>
<table class="form-table">
	<tr>
		<th valign="top"><?php _e('Advertiser page', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_advertiser"><select name="adrotate_advertiser">
				<?php wp_dropdown_roles($adrotate_config['advertiser']); ?>
			</select> <?php _e('Role to allow users/advertisers to see their advertisement page.', 'adrotate-pro'); ?></label>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<strong><?php _e('All permissions below do NOT apply to avertisers and they should NOT have access to it!', 'adrotate-pro'); ?></strong>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Full report page', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_global_report"><select name="adrotate_global_report">
				<?php wp_dropdown_roles($adrotate_config['global_report']); ?>
			</select> <?php _e('Role to review the full report.', 'adrotate-pro'); ?></label>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Manage/Add/Edit adverts', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_ad_manage"><select name="adrotate_ad_manage">
				<?php wp_dropdown_roles($adrotate_config['ad_manage']); ?>
			</select> <?php _e('Role to see and add/edit adverts.', 'adrotate-pro'); ?></label>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Delete/Reset adverts', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_ad_delete"><select name="adrotate_ad_delete">
				<?php wp_dropdown_roles($adrotate_config['ad_delete']); ?>
			</select> <?php _e('Role to delete adverts and reset stats.', 'adrotate-pro'); ?></label>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Manage/Add/Edit groups', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_group_manage"><select name="adrotate_group_manage">
				<?php wp_dropdown_roles($adrotate_config['group_manage']); ?>
			</select> <?php _e('Role to see and add/edit groups.', 'adrotate-pro'); ?></label>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Delete groups', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_group_delete"><select name="adrotate_group_delete">
				<?php wp_dropdown_roles($adrotate_config['group_delete']); ?>
			</select> <?php _e('Role to delete groups.', 'adrotate-pro'); ?></label>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Manage/Add/Edit schedules', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_schedule_manage"><select name="adrotate_schedule_manage">
				<?php wp_dropdown_roles($adrotate_config['schedule_manage']); ?>
			</select> <?php _e('Role to see and add/edit schedules.', 'adrotate-pro'); ?></label>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Delete schedules', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_schedule_delete"><select name="adrotate_schedule_delete">
				<?php wp_dropdown_roles($adrotate_config['schedule_delete']); ?>
			</select> <?php _e('Role to delete schedules.', 'adrotate-pro'); ?></label>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Manage advertisers', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_advertiser_manage"><select name="adrotate_advertiser_manage">
				<?php wp_dropdown_roles($adrotate_config['advertiser_manage']); ?>
			</select> <?php _e('Access to see and manage the advertisers.', 'adrotate-pro'); ?></label>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Moderate new adverts', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_moderate"><select name="adrotate_moderate">
				<?php wp_dropdown_roles($adrotate_config['moderate']); ?>
			</select> <?php _e('Role to approve adverts submitted by advertisers.', 'adrotate-pro'); ?></label>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Approve/Reject adverts in Moderation Queue', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_moderate_approve"><select name="adrotate_moderate_approve">
				<?php wp_dropdown_roles($adrotate_config['moderate_approve']); ?>
			</select> <?php _e('Role to approve or reject adverts submitted by advertisers.', 'adrotate-pro'); ?></label>
		</td>
	</tr>
</table>

<p class="submit">
  	<input type="submit" name="adrotate_save_options" class="button-primary" value="<?php _e('Update Options', 'adrotate-pro'); ?>" />
</p>
</form>