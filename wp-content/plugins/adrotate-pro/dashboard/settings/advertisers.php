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

<form name="settings" id="post" method="post" action="admin.php?page=adrotate-settings&tab=advertisers">
<?php wp_nonce_field('adrotate_settings','adrotate_nonce_settings'); ?>
<input type="hidden" name="adrotate_settings_tab" value="<?php echo $active_tab; ?>" />

<h2><?php _e('Advertisers', 'adrotate-pro'); ?></h2>
<span class="description"><?php _e('Enable advertisers so they can review and manage their own adverts.', 'adrotate-pro'); ?></span>
<table class="form-table">
	<tr>
		<th valign="top"><?php _e('Enable Advertisers', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_enable_advertisers"><input type="checkbox" name="adrotate_enable_advertisers" <?php if($adrotate_config['enable_advertisers'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Allow adverts to be coupled to users (Advertisers).', 'adrotate-pro'); ?></label>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Edit/update adverts', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_enable_editing"><input type="checkbox" name="adrotate_enable_editing" <?php if($adrotate_config['enable_editing'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Allow advertisers to add new or edit their adverts.', 'adrotate-pro'); ?></label>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Mobile adverts', 'adrotate-pro'); ?></th>
		<td>
			<input type="checkbox" name="adrotate_enable_mobile_advertisers" <?php if($adrotate_config['enable_mobile_advertisers'] == 1) { ?>checked="checked" <?php } ?> /> <?php _e('Allow advertisers to specify on which devices their ads will show.', 'adrotate-pro'); ?>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Geo Targeting', 'adrotate-pro'); ?></th>
		<td>
			<input type="checkbox" name="adrotate_enable_geo_advertisers" <?php if($adrotate_config['enable_geo_advertisers'] == 1) { ?>checked="checked" <?php } ?> /> <?php _e('Allow advertisers to specify where their ads will show. Geo Targeting has to be enabled, too.', 'adrotate-pro'); ?>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Advertiser role', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_role"><input type="checkbox" name="adrotate_role" <?php if(is_object(get_role('adrotate_advertiser'))) { ?>checked="checked" <?php } ?> /> <?php _e('Create a seperate user role for your advertisers.', 'adrotate-pro'); ?></label><br />
			<span class="description"><?php _e('Don\'t forget to give these users access to their advertiser dashboard via the Roles tab.', 'adrotate-pro'); ?></span>
		</td>
	</tr>
</table>

<p class="submit">
  	<input type="submit" name="adrotate_save_options" class="button-primary" value="<?php _e('Update Options', 'adrotate-pro'); ?>" />
</p>
</form>